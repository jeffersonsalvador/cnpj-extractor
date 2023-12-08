build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker-compose down

restart: down up

logs:
	docker-compose logs -f

cnpj-app:
	docker exec -it cnpj-app bash

cnpj-redis:
	docker exec -it cnpj-redis redis-cli