[//]: # ( English version [here]&#40;README.en.md&#41;.)

# CNPJ - Dados públicos da Receita Federal
___
Script em PHP para carregar os dados públicos da Receita Federal do Brasil (RFB) no banco de dados MySQL.

## Pré-requisitos
- Docker

## Executando a Aplicação
Para construir e executar a aplicação, você usará os comandos do Makefile:

1. `make build` para construir o ambiente.
2. `make up` para iniciar os containers.

Outros comando úteis:

- `make down` para parar e remover os containers.
- `make restart` para reiniciar os containers.
- `make logs` para acompanhar os logs.