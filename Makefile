.PHONY: confirmation_prompt
confirmation_prompt:
	@echo -n "Уверен? [y/N] " && read ans && [ $${ans:-N} = y ]

up:
	cd docker && docker-compose build \
	&& docker-compose up -d \
	&& docker-compose run --rm social_php composer install \
	&& docker-compose run --rm social_php bin/console doctrine:migrations:migrate --no-interaction

# make composer-req cmd="phpunit/phpunit --dev"
composer-req:
	cd docker && docker-compose run --rm social_php composer req $(cmd)

db-reset-test:
	@echo "Дропаю тестовую БД"
	cd docker && docker-compose exec social_php php bin/console --env=test doctrine:database:drop --force --if-exists --no-debug
	@echo "Создаю тестовую БД"
	cd docker && docker-compose exec social_php php bin/console --env=test doctrine:database:create --quiet --no-debug
	@echo "Применяю схему тестовой БД"
	cd docker && docker-compose exec social_php php bin/console --env=test doctrine:schema:create --quiet --no-debug

pg_basebackup:
#	docker-compose run --rm social_pgmaster
#	docker exec -it synapse-db-1 pg_basebackup -h /sockets -U synapse -D /tmp/pgreplica
#	docker exec -it pgmaster pg_basebackup -h pgmaster -D /pgslave -U replicator -v -P --wal-method=stream
#	docker exec -it pgmaster pg_basebackup -h pgmaster -D /pgslave -U replicator -v -P --wal-method=stream
	#cd docker && docker compose run --rm pgmaster bash -c 'create role replicator with login replication password \'pass\''
# create role replicator with login replication password 'pass';
	docker exec -it pgmaster /bin/bash -c 'pg_basebackup -h pgmaster -D /postgres-wal-dir -U replicator -v -P --wal-method=stream'

psql:
	#cd docker && docker compose exec pgmaster bash
	docker exec -it pgmaster psql -U social

down-volumes:
	cd docker && docker-compose down --volumes
