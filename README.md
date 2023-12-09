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

## Pré-requisitos
Antes de começar, certifique-se de que os seguintes requisitos foram atendidos:

- Docker e Docker Compose instalados em sua máquina.
- Conhecimento básico de Laravel, Docker e PostgreSQL.
  
## Instalação
Para configurar o projeto para desenvolvimento, siga estes passos:

1. Clone o repositório.
```
git clone https://github.com/jeffersonsalvador/cnpj-dados-publicos-receita-federal.git
cd cnpj-dados-publicos-receita-federal
```

2. Navegue até o diretório docker e inicie os serviços:
```
cd docker
make up
```

Isso irá construir e executar os seguintes serviços:

- `app`: A aplicação Laravel.
- `nginx`: O servidor web da aplicação.
- `postgres`: O banco de dados PostgreSQL.
- `redis`: O servidor Redis.

## Uso

Uma vez que os contêineres estejam em execução, você pode:

- Acessar a aplicação via http://localhost:8080.
- Conectar ao banco de dados usando as credenciais fornecidas no arquivo .env.
- Monitorar a instância do Redis na porta 6379.

Para processar dados CNPJ:

1. Coloque seus arquivos CSV no diretório designado (conforme mencionado na documentação da aplicação).
2. Use a interface web da aplicação (em desenvolvimento) ou comandos CLI para iniciar o processamento.

## Docker

Para construir e executar a aplicação, você usará os comandos do Makefile:

`make up` para iniciar os containers e a aplicação web (em desenvolvimento).

ou

`make up-terminal` para iniciar os serviços necessários para rodar o script de importação de dados via terminal.

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