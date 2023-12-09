up:
	docker-compose up -d

down:
	docker-compose down

restart: down up

up-terminal:
	docker-compose stop
	docker-compose up -d redis db app

cnpj-app:
	docker-compose exec app bash