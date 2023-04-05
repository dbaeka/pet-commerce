export INNODB_USE_NATIVE_AIO=1

ifneq ("$(wildcard .env)","")
	include .env
endif

build:
	@INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) docker-compose -f docker-compose-dev.yml build

start: ## Start dev environment
	@INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) docker-compose -f docker-compose-dev.yml up -d

composer-install: ## Installs composer dependencies
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader"

env-dev: ## Creates config for dev environment
	cp ./.env.dev.docker ./.env

exec:
	@INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) docker-compose -f docker-compose-dev.yml exec app $$cmd

exec-bash:
	@INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) docker-compose -f docker-compose-dev.yml exec app bash -c "$(cmd)"

drop-migrate: ## Drops databases and runs all migrations for the database
	@make exec cmd="php artisan migrate:fresh"

migrate: ## Runs all migrations for main/test databases
	@make exec cmd="php artisan migrate --force"

seed: ## Runs all seeds for test database
	@make exec cmd="php artisan db:seed --force"

keys: ## Generates Laravel application keys
	@make exec cmd="php artisan key:generate --force"

jwt-keys: ## Generates JWT keys
	@make exec cmd="php artisan jwt:keys --force"

db-wait: ## Wait for db
	@make exec cmd="wait-for-it mysql:3306"

fix-permissions:
	@make exec cmd="chmod 777 -R /var/www/storage"

test: ## Runs PhpUnit tests
	@make exec cmd="./vendor/bin/phpunit -c phpunit.xml"

stop:
	@INNODB_USE_NATIVE_AIO=$(INNODB_USE_NATIVE_AIO) docker-compose -f docker-compose-dev.yml down

bootstrap: env-dev build start composer-install keys fix-permissions db-wait drop-migrate seed jwt-keys
