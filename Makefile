.PHONY: confirmation_prompt
confirmation_prompt:
	@echo -n "Уверен? [y/N] " && read ans && [ $${ans:-N} = y ]

up:
	cd docker && docker-compose build \
	&& docker-compose up -d

# make composer-req cmd="phpunit/phpunit --dev"
composer-req:
	cd docker && docker-compose run --rm social_php composer req $(cmd)

composer-install:
	cd docker && docker-compose run --rm social_php composer install

db-recreate-test:
	@echo "Дропаю тестовую БД"
	cd docker && docker-compose exec social_php php bin/console --env=test doctrine:database:drop --force --if-exists --no-debug
	@echo "Создаю тестовую БД"
	cd docker && docker-compose exec social_php php bin/console --env=test doctrine:database:create --quiet --no-debug
	@echo "Применяю схему тестовой БД"
	cd docker && docker-compose exec social_php php bin/console --env=test doctrine:schema:create --quiet --no-debug

db-recreate:
	cd docker && docker-compose exec social_php php bin/console doctrine:database:drop --force --if-exists --no-debug \
	&& docker-compose exec social_php php bin/console doctrine:database:create --quiet --no-debug
	$(MAKE) db-migrate
	cd docker && docker-compose exec social_php php bin/console doctrine:schema:validate --no-debug

db-generate:
	@echo "Создаю миграцию"
	cd docker && docker-compose run --rm social_php php bin/console doctrine:migrations:generate --no-interaction --no-debug

db-diff:
	@echo "Делаю diff БД c миграциями"
	cd docker && docker-compose run --rm social_php php bin/console doctrine:migrations:diff --no-interaction --no-debug

db-migrate:
	@echo "Делаю diff БД c миграциями"
	cd docker && docker-compose run --rm social_php php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --no-debug

down-volumes:
	cd docker && docker-compose down --volumes

pg_basebackup:
	docker exec -it social_db /bin/bash -c 'pg_basebackup -h social_db -D /postgres-wal-dir -U replicator -v -P --wal-method=stream'

psql:
	#cd docker && docker compose exec pgmaster bash
	docker exec -it pgasyncslave psql
