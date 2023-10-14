# Desenvolvimento da REST API do Open Food Facts

Neste projeto, estaremos criando uma REST API para utilizar os dados do projeto Open Food Facts, um banco de dados aberto que contém informações nutricionais de uma ampla variedade de produtos alimentícios. O principal objetivo desta API é fornecer suporte à equipe de nutricionistas da Fitness Foods LC, permitindo que eles revisem rapidamente as informações nutricionais dos alimentos publicadas.

## Visão Geral

O Open Food Facts é uma iniciativa de código aberto que coleta e compartilha dados detalhados sobre produtos alimentícios, incluindo informações sobre ingredientes, valores nutricionais, origem e muito mais. A equipe da Fitness Foods LC utiliza esses dados para fornecer aos usuários uma maneira conveniente de acessar informações nutricionais precisas e atualizadas sobre os alimentos que consomem.

## Funcionalidades Principais

A REST API que estamos desenvolvendo oferecerá as seguintes funcionalidades principais:

1. **Acesso a Dados Nutricionais:** A API permitirá aos nutricionistas da Fitness Foods LC acessar facilmente as informações nutricionais de produtos alimentícios armazenadas no Open Food Facts.

2. **Pesquisa Avançada:** Os nutricionistas poderão realizar pesquisas avançadas para encontrar produtos com base em critérios específicos, como valor calórico, teor de gordura, presença de alérgenos, entre outros.

3. **Atualização de Dados:** A API permitirá que a equipe da Fitness Foods LC atualize e aprimore as informações nutricionais dos produtos alimentícios no banco de dados do Open Food Facts, garantindo que as informações estejam sempre precisas.

## Tecnologias utilizadas:

<p align="center" width="100%">
 <br>
  <img align="center" alt="React" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/react/react-original.svg">
  <img align="center" alt="NodeJS" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/nodejs/nodejs-original.svg">
  <img align="center" alt="Docker" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/docker/docker-original.svg">
  <img align="center" alt="HTML" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/html5/html5-original.svg">
  <img align="center" alt="CSS" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/css3/css3-original.svg">
  <img align="center" alt="PHP" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-plain.svg">
  <img align="center" alt="Laravel" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/laravel/laravel-plain-wordmark.svg">
  <img align="center" alt="PostgreSQL" height="30" width="40" src="https://raw.githubusercontent.com/devicons/devicon/master/icons/postgresql/postgresql-original.svg">
  <br>
</p>

## Pré-Requisitos

### Com Docker
-   Docker Engine ou Docker Desktop

### Sem Docker
-   Node.js (v16.0.0 ou maior)
-   PHP 8.1
-   Postgresql

## Instalação

A configuração do .env é necessária nesta etapa. Não altere as configurações do banco de dados, apenas se for realizar a instalação sem utilizar o docker. Existe algumas várivas novas dentro do .env, elas são:

-   PRODUCTS_SYNC # Link para buscar os nomes do arquivos para sincronização. Padrão: https://challenges.coode.sh/food/data/json/index.txt
-   PRODUCTS_ENDPOINT # Link para baixar os arquivos para a sincronização. Padrão: https://challenges.coode.sh/food/data/json/
-   LIMIT_LINES # Quantidade de linhas que será importada por arquivo. Padrão: 100
-   LIMIT_QUERY # Quantidade máxima de linhas que será retornada por offset. Padrão: 100

### Docker

1. Na pasta raiz do projeto, execute o comando abaixo:
```bash
docker-compose up -d --build
```

2. Após concluir a construção dos containers, executar o seguinte comando:
```bash
docker exec -it app bash   
```

3. Dentro do terminal do container, execute este comando:
```bash
composer install
```

4. Executar as migrations:
```bash
php artisan migrate
```

5. Depois da instalação, você precisará gerar uma key:
```bash
php artisan key:generate
```

Seu servidor estará disponível em: http://localhost:8989

### Sem Docker

1. Na pasta raiz do projeto, execute o comando abaixo:
```bash
cd apps/app
```

2. Depois execute o seguinte comando:
```bash
composer install  
```

3. Depois da instalação do projeto, execute:
```bash
php artisan key:generate
```

4. Executar as migrations:
```bash
php artisan migrate
```

5. E depois execute:
```bash
php artisan serve
```

Seu servidor estará disponível em: http://localhost:8000


## Comandos
Alguns comandos personalizados

-   php artisan app:syncproducts    #Força a sincronização dos produtos.


## Documentação API

### Rotas da API

A API oferece as seguintes rotas:

-    `GET /api/products`: Retorna todos os produtos.
-    `GET /api/products/{code}`: Retorna um produto específico pelo código.
-    `POST /api/products`: Cria um novo produto.
-    `PUT /api/products/{code}`: Atualiza um produto existente.
-    `DELETE /api/products/{code}`: Exclui um produto pelo código.

### Descrição dos Campos

Aqui está a descrição de todos os campos usados nas rotas da API:

#### `code`

- **Tipo**: int
- **Condições Permitidas**: =, <, >, <=, >=, like

#### `status`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like
- **Valores permitidos**: 'draft', 'published', 'trash'

#### `url`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `creator`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `product_name`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `quantity`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `brands`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `categories`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `labels`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `cities`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `purchase_places`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `stores`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `ingredients_text`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `traces`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `serving_size`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `serving_quantity`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `nutriscore_score`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `nutriscore_grade`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `main_category`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `image_url`

- **Tipo**: string
- **Condições permitidas**: =, ilike, like, <>

#### `imported_t`

- **Tipo**: date
- **Condições permitidas**: =, <, >, <=, >=, between, <>

#### `created_t`

- **Tipo**: date
- **Condições permitidas**: =, <, >, <=, >=, between, <>

#### `last_modified_t`

- **Tipo**: date
- **Condições permitidas**: =, <, >, <=, >=, between, <>

#### `offset`

- **Tipo**: int
- **Descrição**: A API só retornar 100 linhas por vez, utilizando campo offset é possível retornar mais 100 linhas sem repetir as anteriores.

### Exemplos de Solicitação e Resposta

#### `GET /api/products`

Solicitação:
```json
{
   "filtros":[
      {
         "campo":"imported_t",
         "condição":"=",
         "valor":"13/10/23"
      }
   ],
   "offset": 0
}
```

Resposta:

```json
{
   "cabecalho":{
      "status":200,
      "mensagem":"Dados retornados com sucesso"
   },
   "retorno":{
      "contador":100,
      "offset":0,
      "items":[
         {
            "code":17,
            "status":"published",
            "imported_t":"2023-10-13 17:51:34",
            "url":"http:\/\/world-en.openfoodfacts.org\/product\/0000000000017\/vitoria-crackers",
            "creator":"kiliweb",
            "product_name":"Vitória crackers",
            "quantity":"",
            "brands":"",
            "categories":"",
            "labels":"",
            "cities":"",
            "purchase_places":"",
            "stores":"",
            "ingredients_text":"",
            "traces":"",
            "serving_size":"",
            "serving_quantity":null,
            "nutriscore_score":null,
            "nutriscore_grade":"",
            "main_category":"",
            "image_url":"https:\/\/static.openfoodfacts.org\/images\/products\/000\/000\/000\/0017\/front_fr.4.400.jpg",
            "created_t":"2018-06-15T13:38:00.000000Z",
            "last_modified_t":"2019-06-25T14:55:18.000000Z"
         },
         {
            "code":31,
            "status":"published",
            "imported_t":"2023-10-13 17:51:34",
            "url":"http:\/\/world-en.openfoodfacts.org\/product\/0000000000031\/cacao",
            "creator":"isagoofy",
            "product_name":"Cacao",
            "quantity":"130 g",
            "brands":"",
            "categories":"",
            "labels":"",
            "cities":"",
            "purchase_places":"",
            "stores":"",
            "ingredients_text":"",
            "traces":"",
            "serving_size":"",
            "serving_quantity":null,
            "nutriscore_score":null,
            "nutriscore_grade":"",
            "main_category":"",
            "image_url":"https:\/\/static.openfoodfacts.org\/images\/products\/000\/000\/000\/0031\/front_fr.3.400.jpg",
            "created_t":"2018-10-14T00:06:14.000000Z",
            "last_modified_t":"2018-10-14T00:06:57.000000Z"
         }
      ]
   }
}
```

#### `GET /api/products/{code}`

Resposta:

```json
{
   "cabecalho":{
      "status":200,
      "mensagem":"Dados retornados com sucesso"
   },
   "retorno":{
      "code":17,
      "status":"published",
      "imported_t":"2023-10-13 17:51:34",
      "url":"http:\/\/world-en.openfoodfacts.org\/product\/0000000000017\/vitoria-crackers",
      "creator":"kiliweb",
      "product_name":"Vitória crackers",
      "quantity":"",
      "brands":"",
      "categories":"",
      "labels":"",
      "cities":"",
      "purchase_places":"",
      "stores":"",
      "ingredients_text":"",
      "traces":"",
      "serving_size":"",
      "serving_quantity":null,
      "nutriscore_score":null,
      "nutriscore_grade":"",
      "main_category":"",
      "image_url":"https:\/\/static.openfoodfacts.org\/images\/products\/000\/000\/000\/0017\/front_fr.4.400.jpg",
      "created_t":"2018-06-15T13:38:00.000000Z",
      "last_modified_t":"2019-06-25T14:55:18.000000Z"
   }
}
```

#### `PUT /api/products/{code}: 

Solicitação:
```json
{
   "product_name":"Teste",
   "categories":"fruta"
}
```

Resposta:

```json
{
   "cabecalho":{
      "status":200,
      "mensagem":"Dados atualizados com sucesso"
   },
   "retorno":{
      "id":14,
      "code":31,
      "status":"published",
      "imported_t":"2023-10-13 19:20:11",
      "url":"http:\/\/world-en.openfoodfacts.org\/product\/0000000000031\/cacao",
      "creator":"teste",
      "product_name":"Cacao",
      "quantity":"130 g",
      "brands":"",
      "categories":"",
      "labels":"",
      "cities":"",
      "purchase_places":"",
      "stores":"",
      "ingredients_text":"",
      "traces":"",
      "serving_size":"",
      "serving_quantity":null,
      "nutriscore_score":null,
      "nutriscore_grade":"",
      "main_category":"",
      "image_url":"https:\/\/static.openfoodfacts.org\/images\/products\/000\/000\/000\/0031\/front_fr.3.400.jpg",
      "created_t":"2018-10-14T00:06:14.000000Z",
      "last_modified_t":"2023-10-14T00:15:48.000000Z"
   }
}
```

#### `DELETE /api/products/{code}`

Resposta:

```json
{
   "cabecalho":{
      "status":204,
      "mensagem":"Produto excluído com sucesso"
   }
}
```
