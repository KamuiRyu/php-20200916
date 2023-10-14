<?php

namespace App\Http\Controllers;

use App\Models\Logcron;
use App\Models\Product;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function healthCheck()
    {
        try {
            try {
                DB::connection()->getPdo();
                $dbConnection = true;
            } catch (\Exception $e) {
                $dbConnection = false;
            }

            $lastCronRun = Logcron::getLastCron();
            if (!empty($lastCronRun)) {
                $lastSync = new DateTime($lastCronRun['lastSync']);
                $lastSync = $lastSync->format('d-m-Y H:i:s');
            }

            $memoryUsage = round(memory_get_usage() / 1024 / 1024, 2) . "MB";

            return response()->json([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Dados retornados com sucesso',
                ],
                'retorno' => [
                    'db_conexao' => $dbConnection ? 'OK' : 'NOK',
                    'ultima_sincronizacao' => $lastSync ?? "",
                    'resposta_ultima_sincronizacao' => isset($lastCronRun['response']) ? json_decode($lastCronRun['response']) : "",
                    'uso_memoria' => $memoryUsage
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'cabecalho' => [
                    'status' => 500,
                    'mensagem' => $th->getMessage(),
                ],
            ]);
        }
    }

    public static function syncProducts()
    {
        $logCron = [
            'startTime' => "",
            'endTime' => "",
            'response' => ""
        ];
        $logcronModel = new Logcron();
        try {
            // URL para buscar os arquivos dos json que devem ser importados. OBS: a variavel do .env deve estar configurada para funcionar
            $url = env('PRODUCTS_SYNC');
            if (empty($url)) {
                throw new \Exception('A variável de ambiente "PRODUCTS_SYNC" não foi configurada', 500);
            }

            // Procurar arquivo através da URL
            $content = file_get_contents($url);

            if ($content === false) {
                throw new \Exception('Não foi possível buscar os arquivos para sincronização', 502);
            }
            //Separar o array para cada linha encontrada no arquivo
            $content = explode("\n", $content);
            $content = array_filter($content);

            //Adicionando o data de inicio do cron
            $logCron['startTime'] = date('Y-m-d H:i:s');
            $records = 0;

            if (empty($content)) {
                throw new \Exception('Não foi possível encontrar os arquivos para realizar a sincronização', 502);
            }

            foreach ($content as $value) {
                //Buscar a URL do .env para baixar os arquivos
                $endpoint = env('PRODUCTS_ENDPOINT') . $value;
                $jsonZip = file_get_contents($endpoint);
                if ($jsonZip === false) {

                    throw new \Exception('O arquivo para a sincronização não foi encontrado!', 502);
                }

                //Guardar o arquivo no storage/app/public
                Storage::put("public/temp.gz", $jsonZip);

                //Transformar os dados em array para realizar a inserção
                $items = self::uncompress(storage_path("app/public/temp.gz"));

                //Inserindo todos os registros retornados
                if (!empty($items)) {
                    $result = Product::massInsert($items);
                }

                //Deletar o arquivo temporario
                Storage::delete("public/temp.gz");

                if ($result['success'] === false) {
                    throw new \Exception($result['message'], 502);
                }

                $records += $result['records'] ?? 0;
            }

            //Adicionar o data final do cron e a response
            $logCron['endTime'] = date('Y-m-d H:i:s');
            $logCron['response'] = json_encode([
                'records' => $records,
                'status' => 201,
                'message' => 'A sincronização foi realizada com sucesso',
            ]);

            //Salvar o log
            $logcronModel->saveLog($logCron);
        } catch (\Throwable $th) {
            //Caso dê erro, obtem o codigo do erro e a message. Apos isso, salva essas informações na logcron
            $code = $th->getCode() ?: 500;
            $message =  $th->getMessage();
            $logCron['startTime'] = !empty($logCron['startTime']) ? $logCron['startTime'] : date('Y-m-d H:i:s');
            $logCron['endTime'] = !empty($logCron['endTime']) ? $logCron['endTime'] : date('Y-m-d H:i:s');
            $logCron['response'] = json_encode([
                'status' => $code,
                'message' => $message,
            ]);

            $logcronModel->saveLog($logCron);
        }
    }

    public function getProduct($code)
    {
        try {
            $product = Product::getProduct($code);
            if ($product['success'] === false) {
                $code = $product['code'] ?? 404;
                throw new \Exception($product['message'], $code);
            }
            return response()->json([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Dados retornados com sucesso',
                ],
                'retorno' => json_decode($product['data']),
            ]);
        } catch (\Throwable $th) {
            $code = $th->getCode() ?: 500;
            $message =  $th->getMessage();
            return response()->json([
                'cabecalho' => [
                    'status' => $code,
                    'mensagem' => $message,
                ],
            ]);
        }
    }

    public function getAllProducts(Request $request)
    {
        try {
            $jsonValid = self::checkJson($request->getContent());
            if($jsonValid === false){
                throw new \Exception("O formato do corpo enviado é inválido. Por favor, utilize um JSON correto.", 404);
            }
            $data = $request->all() ?? "";
            $offset = isset($data['offset']) ? intval($data['offset']) : 0;
            $filtros = isset($data['filtros']) ? $data['filtros'] : '';
            if (!empty($filtros)) {
                $filtros = $this->validParameters($filtros);
                if ($filtros['success'] === false) {
                    throw new \Exception(json_encode($filtros['errors']), 400);
                }
            }
            $result = Product::getAllProducts($filtros, $offset);
            if ($result['success'] === false) {
                throw new \Exception($result['message'], 500);
            }
            return response()->json([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Dados retornados com sucesso',
                ],
                'retorno' => [
                    'contador' => isset($result['data']) ? count($result['data']) : 0,
                    'offset' => $offset,
                    'items' => $result['data'] ?? $result['message'] ?? null,
                ]
            ], 200);
        } catch (\Throwable $th) {
            $code = $th->getCode() ?: 500;
            $message =  json_decode($th->getMessage(), true) ?? $th->getMessage();
            return response()->json([
                'cabecalho' => [
                    'status' => $code,
                    'mensagem' => $message,
                ],
            ]);
        }
    }

    public function deleteProduct($code)
    {
        try {
            if (empty($code)) {
                throw new \Exception('Não foi encontrado o codigo do produto', 400);
            }
            $result = Product::deleteProduct($code);
            if ($result['success'] === false) {
                throw new \Exception($result['message'], 200);
            }
            return response()->json([
                'cabecalho' => [
                    'status' => 204,
                    'mensagem' => 'Produto excluído com sucesso',
                ]
            ], 204);
        } catch (\Throwable $th) {
            $code = $th->getCode() ?: 500;
            $message =  json_decode($th->getMessage(), true) ?? $th->getMessage();
            return response()->json([
                'cabecalho' => [
                    'status' => $code,
                    'mensagem' => $message,
                ],
            ]);
        }
    }

    public function updateProduct($code, Request $request)
    {
        try {
            $jsonValid = self::checkJson($request->getContent());
            if($jsonValid === false){
                throw new \Exception("O formato do corpo enviado é inválido. Por favor, utilize um JSON correto.", 404);
            }
            $data = $request->all();
            $validData = $this->validUpdate($data);
            if ($validData['success'] === false) {
                throw new \Exception(json_encode($validData['errors']), 400);
            }

            $result = Product::updateProduct($code, $validData['data']);

            if ($result['success'] === false) {
                $code = $result['code'] ?? 500;
                throw new \Exception($result['message'], $code);
            }

            return response()->json([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Dados atualizados com sucesso',
                ],
                'retorno' => $result['data'] ?? $result['message'],
            ]);
        } catch (\Throwable $th) {
            $code = $th->getCode() ?: 500;
            $message =  json_decode($th->getMessage(), true) ?? $th->getMessage();
            return response()->json([
                'cabecalho' => [
                    'status' => $code,
                    'mensagem' => $message,
                ],
            ]);
        }
    }

    public function validUpdate($data)
    {
        $allowedFields = [
            "url" => 'string',
            "creator" => 'string',
            "product_name" => 'string',
            "quantity" => 'string',
            "brands" => 'string',
            "categories" => 'string',
            "labels" => 'string',
            "cities" => 'string',
            "purchase_places" => 'string',
            "stores" => 'string',
            "ingredients_text" => 'string',
            "traces" => 'string',
            "serving_size" => 'string',
            "serving_quantity" => 'string',
            "nutriscore_score" => 'string',
            "nutriscore_grade" => 'string',
            "main_category" => 'string',
            "image_url" => 'string',
        ];

        $filteredData = [];
        $errors = [];

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $allowedFields)) {
                if (gettype($value) === $allowedFields[$key]) {
                    $filteredData[$key] = $value;
                } else {
                    $errors[$key] = "Campo '$key' deve ser do tipo '{$allowedFields[$key]}'";
                }
            } else {
                $errors[$key] = "Campo '$key' não é permitido";
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'data' => $filteredData];
    }

    public function validParameters($data)
    {
        $filters = [
            "code" => 'int',
            "status" => ['draft', 'published', 'trash'],
            "url" => 'string',
            "creator" => 'string',
            "product_name" => 'string',
            "quantity" => 'string',
            "brands" => 'string',
            "categories" => 'string',
            "labels" => 'string',
            "cities" => 'string',
            "purchase_places" => 'string',
            "stores" => 'string',
            "ingredients_text" => 'string',
            "traces" => 'string',
            "serving_size" => 'string',
            "serving_quantity" => 'string',
            "nutriscore_score" => 'string',
            "nutriscore_grade" => 'string',
            "main_category" => 'string',
            "image_url" => 'string',
            "imported_t" => 'date',
            "created_t" => 'date',
            "last_modified_t" => 'date',
        ];
        $operators = [
            'between',
            'like',
            'ilike',
            '>',
            '<',
            '=',
            '>=',
            '<=',
            '<>',
        ];
        $validData = [];
        $errors = [];
        if (!empty($data)) {
            foreach ($data as $value) {
                $key = $value["campo"] ?? null;
                $condicao = isset($value['condicao']) && !empty($value['condicao']) ? strtolower($value['condicao']) : "=";
                $valor = $value["valor"] ?? null;
                $valor2 = $value["valor2"] ?? null;
                if (!empty($key)) {
                    if (array_key_exists($key, $filters)) {

                        $filterType = $filters[$key];
                        if ($filterType === 'int') {
                            if (is_numeric($valor) && is_int($valor + 0)) {
                                if ($condicao === 'between') {
                                    $errors[] = "A condição between não pode ser utilizada no campo '$key'";
                                } else if ($condicao === 'ilike') {
                                    $errors[] = "A condição ilike não pode ser utilizada no campo '$key'";
                                } else {

                                    $validData[$key]['valor'] = (int) $valor;
                                }
                            } else {
                                $errors[] = "O valor para '$key' deve ser um número inteiro.";
                            }
                        } elseif (is_array($filterType)) {
                            if (in_array($valor, $filterType)) {
                                if ($condicao === 'between') {
                                    $errors[] = "A condição between não pode ser utilizada no campo '$key'";
                                } else if ($condicao === '>=' || $condicao === '<=' || $condicao === '>' || $condicao === '>') {
                                    $errors[] = "A condição '$condicao' não pode ser utilizada no campo '$key'";
                                } else {
                                    $validData[$key]['valor'] = $valor;
                                }
                            } else {
                                $errors[] = "O valor para '$key' não está em uma lista parametros aceitos";
                            }
                        } elseif ($filterType === 'string') {
                            if ($condicao === 'between') {
                                $errors[] = "A condição between não pode ser utilizada no campo '$key'";
                            } else if ($condicao === '>=' || $condicao === '<=' || $condicao === '>' || $condicao === '>') {
                                $errors[] = "A condição '$condicao' não pode ser utilizada no campo '$key'";
                            } else {
                                $validData[$key]['valor'] = $valor;
                            }
                        } elseif ($filterType === 'date') {
                            $regex = '/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/(?:\d{2}|\d{4})$/';
                            if (preg_match($regex, $valor)) {
                                $formatsToTry = ['d-m-y', 'd-m-Y'];
                                $dateValue = str_replace('/', '-', $valor);
                                $date = null;
                                foreach ($formatsToTry as $format) {
                                    $date = DateTime::createFromFormat($format, $dateValue);
                                    if ($date != false) {
                                        break;
                                    }
                                }
                                if ($date instanceof DateTime) {
                                    $validData[$key]['valor'] = $date->format('Y-m-d');
                                } else {
                                    $errors[] = "O valor para '$key' não é uma data válida.";
                                }
                            } else {
                                $errors[] = "O valor para '$key' não é uma data válida.";
                            }
                            if ($condicao === 'between') {
                                if (isset($value['valor2']) && preg_match($regex, $valor2)) {
                                    $formatsToTry = ['d-m-y', 'd-m-Y'];
                                    $dateValue = str_replace('/', '-', $valor2);
                                    $date = null;
                                    foreach ($formatsToTry as $format) {
                                        $date = DateTime::createFromFormat($format, $dateValue);
                                        if ($date != false) {
                                            break;
                                        }
                                    }
                                    if ($date instanceof DateTime) {
                                        $validData[$key]['valor2'] = $date->format('Y-m-d');
                                    } else {
                                        $errors[] = "O valor para '$key' valor 2 não é uma data válida.";
                                    }
                                } else {
                                    $errors[] = "O valor 2 para '$key' é obrigatório na condição between.";
                                }
                            }
                        }


                        if (in_array($condicao, $operators)) {
                            $validData[$key]['condicao'] = $condicao;
                        } else {
                            $errors[] = "A condição '$condicao' não é válida";
                        }
                    } else {
                        $errors[] = "O filtro '$key' não é permitido na consulta.";
                    }
                } else {
                    $errors[] = "O campo não foi informado no filtro";
                }
            }
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
        }

        return ['success' => true, 'data' => $validData];
    }

    public static function uncompress($file)
    {
        //Buscar a quantidade de linha permitidas no .env
        $linesLimit = env('LIMIT_LINES') ?? 100;

        //Lê o arquivo .gz
        $fileGz = gzopen($file, 'r');
        $i = 0;
        $returnedItems = [];

        //Array com os campos permitidos para inserção
        $allowedFields = [
            "code",
            "status",
            "imported_t",
            "url",
            "creator",
            "created_datetime",
            "last_modified_datetime",
            "product_name",
            "quantity",
            "brands",
            "categories",
            "labels",
            "cities",
            "purchase_places",
            "stores",
            "ingredients_text",
            "traces",
            "serving_size",
            "serving_quantity",
            "nutriscore_score",
            "nutriscore_grade",
            "main_category",
            "image_url",
        ];
        while ($i < $linesLimit) {

            //Buscar a linha 
            $line = gzgets($fileGz);

            if ($line) {
                //Decodificar o json
                $data = json_decode($line, true);

                //Transformar o code em inteiro
                $code = !empty($data['code']) ? (int)(str_replace(['"', "'"], '', $data['code'])) : null;

                //Verificar se existe o code dentro do sistema
                $codeExistent = Product::checkCode($code);
                if ($codeExistent['success'] === false) {

                    //Filtra os campos do json
                    $returnedItems[] = array_intersect_key($data, array_flip($allowedFields));
                    $returnedItems[$i]['code'] = $code;
                    $returnedItems[$i]['created_t'] = $returnedItems[$i]['created_datetime'];
                    $returnedItems[$i]['last_modified_t'] = $returnedItems[$i]['last_modified_datetime'];
                    $returnedItems[$i]['serving_quantity'] = !empty($returnedItems[$i]['serving_quantity']) ? (int)(str_replace(['"', "'"], '', $returnedItems[$i]['serving_quantity'])) : null;
                    $returnedItems[$i]['nutriscore_score'] = !empty($returnedItems[$i]['nutriscore_score']) ? (int)(str_replace(['"', "'"], '', $returnedItems[$i]['nutriscore_score'])) : null;
                    unset($returnedItems[$i]['last_modified_datetime']);
                    unset($returnedItems[$i]['created_datetime']);
                    $returnedItems[$i]['status'] = 'published';
                    $returnedItems[$i]['imported_t'] = date('Y-m-d H:i:s');
                    $i++;
                } else {
                    unset($returnedItems[$i]);
                }
            }
        }
        $returnedItems = array_values($returnedItems);
        gzclose($fileGz);
        return $returnedItems;
    }

    public static function checkJson($data)
    {
        $jsonData = json_decode($data);
        if ($jsonData !== null) {
            return true;
        } else {
            return false;
        }
    }
}
