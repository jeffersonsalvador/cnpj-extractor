🇺🇸 English version [here](README.en.md)

---
# Projecto em desenvolvimento, lançamento em janeiro de 2024

[//]: # (# CNPJ - Dados públicos da Receita Federal - em desenvolvimento)

---

![License](https://img.shields.io/badge/license-MIT-blue.svg)

## Descrição

Este repositório contém uma aplicação web projetada para processamento de dados do CNPJ (o equivalente no Brasil a um número de identificação fiscal de empresas). É construído usando o framework Laravel para PHP e utiliza Docker para facilitar a configuração e a implantação. A aplicação lida com arquivos CSV de grande porte, processa-os e armazena os dados em um banco de dados PostgreSQL para análises posteriores.

O download dos arquivos de dados da Receita Federal pode ser feito [aqui](https://dados.gov.br/dados/conjuntos-dados/cadastro-nacional-da-pessoa-juridica---cnpj) - última atualização em 24/11/2023.

## Funcionalidades
- Processamento de arquivos CSV de grande porte com dados CNPJ.
- Armazenamento de dados processados em banco de dados PostgreSQL.
- Integração com Redis para otimização de desempenho.
- Nginx como proxy reverso para o servidor web.
- Configuração conteinerizada com Docker e Docker Compose.

## Estrutura do projeto

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

- `/docker` - Arquivos de configuração do Docker.
- `/src` - Código fonte da aplicação em Laravel.
- `/data` - Arquivos de dados da Receita Federal.

## Configuração Inicial

- Entre na pasta do projeto `/src`, renomeie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente.

- Ainda na pasta do projeto, wxecute o comando `composer install` para instalar as dependências do projeto.

## Docker

Para construir e executar a aplicação, você usará os comandos do Makefile:

`make up` para iniciar os containers e a aplicação web.

ou

`make cnpj-terminal` para iniciar os serviços necessários para rodar o script de importação de dados via terminal.

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

## Licença
Distribuído sob a Licença MIT. Veja LICENSE para mais informações.

## Contato
Seu Nome – contato@jeffersoncosta.dev

Link do Projeto: https://github.com/jeffersonsalvador/cnpj-dados-publicos-receita-federal