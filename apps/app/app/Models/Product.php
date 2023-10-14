<?php

namespace App\Models;

use App\Models\Product as ModelsProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        "code",
        "status",
        "url",
        "creator",
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
        'imported_t',
        'created_t',
        'last_modified_t',
    ];

    const CREATED_AT = 'created_t';
    const UPDATED_AT = 'last_modified_t';

    public static function massInsert($data)
    {
        try {
            self::insert($data);
            return ['success' => true, 'message' => 'Inserção em massa bem-sucedida', 'records' => count($data)];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro na inserção em massa: ' . $e->getMessage()];
        }
    }

    public static function checkCode($code)
    {
        try {
            $data = self::where('code', $code)->first();
            if ($data) {
                return ['success' => true, 'message' => 'Encontrado'];
            } else {
                return ['success' => false, 'message' => 'Registro não encontrado'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro na consulta: ' . $e->getMessage()];
        }
    }

    public static function getProduct($code)
    {
        try {
            $data = self::where('code', $code)->where('status', '!=', 'trash')->first();
            if (!empty($data)) {
                $data->last_modified_t = Carbon::parse($data->last_modified_t)->format('d-m-Y H:i:s');
                $data->created_t = Carbon::parse($data->created_t)->format('d-m-Y H:i:s');
                $data->imported_t = Carbon::parse($data->imported_t)->format('d-m-Y H:i:s');;
                return ['success' => true, 'data' => $data];
            } else {
                return ['success' => false, 'message' => 'Registro não encontrado'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro na consulta: ' . $e->getMessage(), 'status' => 500];
        }
    }

    public static function updateProduct($code, $updateData)
    {
        try {
            $updated = self::where('code', $code)->where('status', '!=', 'trash')->update($updateData);
            if ($updated > 0) {
                $data = self::where('code', $code)->first();
                return ['success' => true, 'message' => 'Produto atualizado com sucesso', 'data' => $data];
            } else {
                return ['success' => false, 'message' => 'Produto não encontrado', 'code' => 200];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro na atualização: ' . $e->getMessage()];
        }
    }


    public static function getAllProducts($data, $offset)
    {
        try {
            $query = self::filterProducts($data, $offset);
            if (!empty($query)) {
                return ['success' => true, 'data' => $query];
            } else {
                return ['success' => true, 'message' => 'Nenhum registro encontrado'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro na consulta: ' . $e->getMessage()];
        }
    }

    public static function deleteProduct($code)
    {
        try {
            $updated = self::where('code', $code)->update(['status' => 'trash']);
            if ($updated > 0) {
                return ['success' => true, 'message' => 'Produto excluído com sucesso'];
            } else {
                return ['success' => false, 'message' => 'Produto não encontrado'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro na exclusão: ' . $e->getMessage()];
        }
    }


    public static function filterProducts($data, $offset)
    {
        $allowedParameters = [
            "code",
            "status",
            "url",
            "creator",
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
            "imported_t",
            "created_t",
            "last_modified_t",
        ];

        $limit = env("LIMIT_QUERY") ?? 100;
        $query = Product::query();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $condicao = $value['condicao'] ?? '=';
                $valor = $value['valor'] ?? null;
                $valor2 = $value['valor2'] ?? null;
                if (!empty($valor)) {
                    if (in_array($key, $allowedParameters)) {
                        if ($key === 'imported_t' || $key === 'created_t' || $key === 'last_modified_t') {
                            if ($condicao == 'between') {
                                $query->whereBetween($key, [$valor, $valor2]);
                            } else {
                                $query->whereDate($key, $condicao, $valor);
                            }
                        } else if (is_numeric($valor)) {
                            if ($condicao === 'like') {
                                $query->where($key, 'like', '%' . $valor . '%');
                            } else {
                                $query->where($key, $condicao, $valor);
                            }
                        } else {
                            if ($condicao === 'like') {
                                $query->where($key, 'like', '%' . $valor . '%');
                            } elseif ($condicao === 'ilike') {
                                $query->whereRaw("LOWER($key) LIKE ?", ["%" . strtolower($valor) . "%"]);
                            } else {
                                $query->where($key, $condicao, $valor);
                            }
                        }
                    }
                }
            }
        }

        $result = $query->where('status', '!=', 'trash')->skip($offset * $limit)->take($limit)->get()->toArray();
        return $result;
    }
}
