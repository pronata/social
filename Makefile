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
