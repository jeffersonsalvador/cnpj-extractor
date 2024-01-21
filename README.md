🇺🇸 English version [here](README.en.md)

# CNPJ - Dados públicos da Receita Federal

![License](https://img.shields.io/badge/license-MIT-blue.svg)

## Descrição

Este repositório contém uma aplicação para processamento de dados do CNPJ. É construído usando o framework Laravel para PHP e utiliza Docker para facilitar a configuração e a implantação. A aplicação lida com arquivos CSV de grande porte, processa-os e armazena os dados em um banco de dados MySQL ou PostgreSQL para análises posteriores.

O download dos arquivos de dados da Receita Federal pode ser feito [aqui](https://dados.gov.br/dados/conjuntos-dados/cadastro-nacional-da-pessoa-juridica---cnpj) - 
última atualização em 19/01/2024.

## Funcionalidades
- Processamento de arquivos CSV de grande porte com dados CNPJ.
- Armazenamento de dados processados em banco de dados MySQL ou PostgreSQL.
- Integração com Redis para otimização de desempenho.
- Nginx como proxy reverso para o servidor web.
- Configuração conteinerizada com Docker e Docker Compose.

## Estrutura do projeto

```
/cnoj-extractor
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
- Conhecimento básico de Laravel, Docker e MySQL/PostgreSQL.
  
## Instalação
Para configurar o projeto para desenvolvimento, siga estes passos:

1. Clone o repositório.
```
git clone https://github.com/jeffersonsalvador/cnpj-extractor.git
cd cnpj-extractor
```

2. Navegue até o diretório docker e inicie os serviços:
```
cd docker
make up
```

Isso irá construir e executar os seguintes serviços:

- `app`: A aplicação Laravel.
- `postgres`: O banco de dados PostgreSQL.
- `redis`: O servidor Redis.

## Uso
Uma vez que os contêineres estejam em execução, você pode:

- <strike>Acessar a aplicação via http://localhost:8080.</strike>
- Conectar ao banco de dados usando as credenciais fornecidas no arquivo .env.
- Monitorar a instância do Redis na porta 6379.

Para processar dados CNPJ:

1. Coloque seus arquivos CSV no diretório designado (conforme mencionado na documentação da aplicação).
2. Use <strike>a interface web da aplicação (em desenvolvimento) ou</strike> comandos CLI para iniciar o processamento.

### Processar arquivos zipados

Na pasta /docker, execute o comando:

- `make cnpj-app`
- `php artisan process:cnpj`

Os arquivos zip serão processados e armazenados no redis. 
Para processar a fila no redis execute o comando:

- `php artisan queue:work`

## Docker

Para construir e executar a aplicação, você usará os comandos do Makefile:

`make up-terminal` para iniciar os serviços necessários para rodar o script de importação de dados via terminal.

<strike>`make up` para iniciar os containers e a aplicação web (em desenvolvimento).</strike>

Outros comando úteis:

- `make down` para parar e remover os containers.
- `make restart` para reiniciar os containers.

## Database

Na pasta de configuração `/docker`, execute os comandos `make cnpj-app` para entrar em modo bash e `php artisan migrate` para criar as tabelas no banco de dados.

## Redis

Neste projeto, o Redis é utilizado como um armazenamento temporário de dados durante o processamento de arquivos CSV. O Redis oferece um armazenamento rápido em memória, o que melhora a performance ao lidar com grandes volumes de dados.

### Processamento de CSV

Durante o processamento de arquivos CSV:

- Cada registro é normalizado e serializado como JSON.

- Os registros são armazenados temporariamente no Redis em uma lista chamada `processed_records_{$type}`.

### Inserção de Dados

Após o processamento:

- Os dados são lidos do Redis.

- Eles são desserializados e inseridos em lote no banco de dados configurado no arquivo .env.

Este método assegura eficiência no processamento de dados e minimiza a carga sobre o banco de dados durante a inserção de grandes volumes de registros.

## Resultados após processamento

| Tabela         |  Registros |  Tamanho |
|----------------|-----------:|---------:|
| cities         |      5.571 | 266.7 Kb |
| cnaes          |      1.359 | 135.6 Kb |
| companies      | 22.036.299 |   2.0 Gb |
| countries      |        255 |  15.9 Kb |
| establishments | 12.289.370 |   2.4 Gb |
| legal_natures  |         90 |  13.2 Kb |
| partners       |  7.526.181 | 774.3 Mb |
| partners_qualifications |         68 |  11.1 Kb |
| simples | 18.863.731 | 837.0 Mb |

## Licença
Distribuído sob a Licença MIT. Veja LICENSE para mais informações.

## Contato
Jefferson Costa – contato@jeffersoncosta.dev

Link do Projeto: https://github.com/jeffersonsalvador/cnpj-extractor