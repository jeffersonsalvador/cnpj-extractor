🇧🇷 Versão em Português [aqui](README.md).

# CNPJ Brazil extractor: Data Processing Application

![License](https://img.shields.io/badge/license-MIT-blue.svg)

## Description

This repository contains an application designed for processing CNPJ data (the Brazilian equivalent of a business tax identification number). It's built using the Laravel framework for PHP and utilizes Docker for easy setup and deployment. The application handles large CSV files, processes them, and stores the data in a MySQL/PostgreSQL database for further analysis.

The download of the Receita Federal data files can be done [here](https://dados.gov.br/dados/conjuntos-dados/cadastro-nacional-da-pessoa-juridica---cnpj) -
last updated in 2024-01-19.

## Features
- Process large CSV files with CNPJ data.
- Store processed data in a MySQL/PostgreSQL database.
- Redis integration for performance optimization.
- Nginx as a reverse proxy for the web server.
- Containerized setup with Docker and Docker Compose.

## Project structure

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

- `/docker` - Docker configuration files.
- `/src` - Laravel application source code.
- `/data` - Receita Federal data zip files.
  
## Prerequisites

Before you begin, ensure you have met the following requirements:

- Docker and Docker Compose installed on your machine.
- Basic knowledge of Laravel, Docker, and PostgreSQL.

## Installation
To set up the project for development, follow these steps:

1. Clone the repository:
```
git clone https://github.com/jeffersonsalvador/cnpj-extractor.git
cd cnpj-extractor
```
2. Navigate to the docker directory and start the services:
```
cd docker
make up
```

This will build and run the following services:

- `app`: The Laravel application.
- `postgres`: The PostgreSQL database.
- `redis`: The Redis server.

## Usage
Once the containers are up and running, you can:

- <strike>Access the application via http://localhost:8080.</strike>
- Connect to the database using the credentials provided in the .env file.
- Monitor the Redis instance on port 6379.

To process CNPJ data:

1. Place your CSV files in the designated directory (as mentioned in the application documentation).
2. Use the application's web interface (not finished yet) or CLI commands to start the processing.

### Zip Files Processing

In the /docker folder, run the command:

- `make cnpj-app`
- `php artisan process:cnpj`

The zip files will be processed and stored in redis. 
To process the queue in redis, run the command:

- `php artisan queue:work`

## Docker

To build and run the application, you will use the Makefile commands:

`make up-terminal` to start the necessary services to run the data import script via terminal.

<strike>`make up` to start the containers and the web application (in development).</strike>

Other useful commands:

- `make down` to stop and remove the containers.
- `make restart` to restart the containers.

## Database

In the `/docker` configuration folder, run the commands `make cnpj-app` to enter bash mode and `php artisan migrate` to create the tables in the database.

## Redis

In this project, Redis is used as a temporary data store during the processing of CSV files. Redis offers fast in-memory storage, which improves performance when dealing with large volumes of data.

### CSV Processing

During the processing of CSV files:

- Each record is normalized and serialized as JSON.
- The records are temporarily stored in Redis in a list called `processed_records_{$type}`.

### Data Insertion

After processing:

- Data is read from Redis.
- They are deserialized and batch inserted into the database configured in the .env file.

This method ensures efficiency in data processing and minimizes the load on the database during the insertion of large volumes of records.

## Results

| Table                   |    Records |     Size |
|-------------------------|-----------:|---------:|
| cities         |      5.571 | 266.7 Kb |
| cnaes          |      1.359 | 135.6 Kb |
| companies      | 22.036.299 |   2.0 Gb |
| countries      |        255 |  15.9 Kb |
| establishments | 12.289.370 |   2.4 Gb |
| legal_natures  |         90 |  13.2 Kb |
| partners       |  7.526.181 | 774.3 Mb |
| partners_qualifications |         68 |  11.1 Kb |
| simples | 18.863.731 | 837.0 Mb |

## License
Distributed under the MIT License. See LICENSE for more information.

## Contact
Jefferson Costa – contact@jeffersoncota.dev

Project Link: https://github.com/jeffersonsalvador/cnpj-extractor