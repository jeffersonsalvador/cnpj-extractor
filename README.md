🇺🇸 English version [here](README.en.md)

---

# CNPJ - Dados públicos da Receita Federal - em desenvolvimento

![License](https://img.shields.io/badge/license-MIT-blue.svg)

___

Script em PHP para carregar os dados públicos da Receita Federal do Brasil (RFB) no banco de dados MySQL ou Postgres.

Para rodar toda a aplicação localmente, é necessário ter o Docker instalado.

## Pré-requisitos

- Docker

## Estrutura do project

```
/cnoj-dados-publicos-receita-federal
│
├── /docker
│   ├── docker-compose.yml
│   ├── Dockerfile.app
│   └── /nginx
│       └── default.conf
│
│── /src
│   ├── /app
│   ├── .env.example
│   ├── ...
│
│── /data
```

`/docker` - Arquivos de configuração do Docker.

`/src` - Código fonte da aplicação em Laravel.

`/data` - Arquivos de dados da Receita Federal.

## Configuração Inicial

- Entre na pasta do projeto `/src`, renomeie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente.

- Ainda na pasta do projeto, wxecute o comando `composer install` para instalar as dependências do projeto.

## Docker

Para construir e executar a aplicação, você usará os comandos do Makefile:

1. `make build` para construir o ambiente.

2. `make up` para iniciar os containers e a aplicação web.

ou

2. `make cnpj-terminal` para iniciar os serviços necessários para rodar o script de importação de dados via terminal.

Outros comando úteis:


- `make down` para parar e remover os containers.
- `make restart` para reiniciar os containers.

## Database

Na pasta da aplicação `/src`, execute o comando `php artisan migrate` para criar as tabelas no banco de dados.

## Redis

Neste projeto, o Redis é utilizado como um armazenamento temporário de dados durante o processamento de arquivos CSV. O Redis oferece um armazenamento rápido em memória, o que melhora a performance ao lidar com grandes volumes de dados.

### Processamento de CSV

Durante o processamento de arquivos CSV:

- Cada registro é normalizado e serializado como JSON.

- Os registros são armazenados temporariamente no Redis em uma lista chamada `processed_records_{$type}`.

### Inserção de Dados

Após o processamento:

- Os dados são lidos do Redis.

- Eles são desserializados e inseridos em lote no banco de dados PostgreSQL.

Este método assegura eficiência no processamento de dados e minimiza a carga sobre o banco de dados durante a inserção de grandes volumes de registros.