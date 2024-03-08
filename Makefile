# Makefile

init-project:
	docker-compose up -d --build
	docker-compose exec php-apache /usr/local/bin/composer install
	docker-compose exec php-apache /usr/local/bin/composer dump-env prod
update-database-schema:
	docker-compose exec php-apache bin/console doctrine:schema:update --force
	docker-compose exec php-apache bin/console doctrine:database:create --env=test
	docker-compose exec php-apache bin/console doctrine:schema:update --force --env=test
load-fixtures-data:
	docker-compose exec php-apache bin/console doctrine:fixtures:load --env=test
run-test:
	docker-compose exec php-apache vendor/bin/phpunit tests/Unit
