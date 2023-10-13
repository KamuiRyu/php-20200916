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
docker-compose exec app bash   
```

3. Dentro do terminal do container, execute este comando:
```bash
composer install
```

4. Depois da instalação, você precisará gerar uma key:
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

4. E depois execute:
```bash
php artisan serve
```

Seu servidor estará disponível em: http://localhost:8000



