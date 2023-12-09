🇺🇸 English version [here](README.en.md)

---

# CNPJ - Dados públicos da Receita Federal - em desenvolvimento

![License](https://img.shields.io/badge/license-MIT-blue.svg)

___

Script em PHP para carregar os dados públicos da Receita Federal do Brasil (RFB) no banco de dados MySQL ou Postgres.

Para rodar toda a aplicação localmente, é necessário ter o Docker instalado.

## Pré-requisitos

- Docker

[//]: # ()
[//]: # (## Configuração Inicial)

[//]: # (- Renomeie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente.)

[//]: # (- Execute o comando `composer install` para instalar as dependências do projeto.)

[//]: # ()
[//]: # (## Executando a Aplicação)

[//]: # (Para construir e executar a aplicação, você usará os comandos do Makefile:)

[//]: # ()
[//]: # (1. `make build` para construir o ambiente.)

[//]: # (2. `make up` para iniciar os containers.)

[//]: # ()
[//]: # (Outros comando úteis:)

[//]: # ()
[//]: # (- `make down` para parar e remover os containers.)

[//]: # (- `make restart` para reiniciar os containers.)

[//]: # (- `make logs` para acompanhar os logs.)

[//]: # ()
[//]: # (## Database)

[//]: # (Run the migrations to create the tables with `php artisan migrate` command.)

[//]: # ()
[//]: # (## Redis)

[//]: # ()
[//]: # (Neste projeto, o Redis é utilizado como um armazenamento temporário de dados durante o processamento de arquivos CSV. O Redis oferece um armazenamento rápido em memória, o que melhora a performance ao lidar com grandes volumes de dados.)

[//]: # ()
[//]: # (### Processamento de CSV)

[//]: # ()
[//]: # (Durante o processamento de arquivos CSV:)

[//]: # ()
[//]: # (- Cada registro é normalizado e serializado como JSON.)

[//]: # (- Os registros são armazenados temporariamente no Redis em uma lista chamada `processed_records`.)

[//]: # ()
[//]: # (### Inserção de Dados)

[//]: # ()
[//]: # (Após o processamento:)

[//]: # ()
[//]: # (- Os dados são lidos do Redis.)

[//]: # (- Eles são desserializados e inseridos em lote no banco de dados PostgreSQL.)

[//]: # ()
[//]: # (Este método assegura eficiência no processamento de dados e minimiza a carga sobre o banco de dados durante a inserção de grandes volumes de registros.)