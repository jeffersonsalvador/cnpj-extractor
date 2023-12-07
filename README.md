[//]: # ( English version [here]&#40;README.en.md&#41;.)

# CNPJ - Dados públicos da Receita Federal - em desenvolvimento

[![License](https://img.shields.io/badge/license-MIT-blue.svg)

___
Script em PHP para carregar os dados públicos da Receita Federal do Brasil (RFB) no banco de dados Postgres.

## Pré-requisitos
- Docker
- Composer

## Configuração Inicial
- Renomeie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente.
- Execute o comando `composer install` para instalar as dependências do projeto.

## Executando a Aplicação
Para construir e executar a aplicação, você usará os comandos do Makefile:

1. `make build` para construir o ambiente.
2. `make up` para iniciar os containers.

Outros comando úteis:

- `make down` para parar e remover os containers.
- `make restart` para reiniciar os containers.
- `make logs` para acompanhar os logs.

## Database
Run the migrations to create the tables with `php artisan migrate` command.

