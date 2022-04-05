phpstan:
	./vendor/bin/phpstan analyse

phpinsights:
	./vendor/bin/phpinsights

analyse:
	make phpstan
	make phpinsights

test:
	php bin/console doctrine:fixtures:load --no-interaction --env=test
	php bin/phpunit

fix:
	./vendor/bin/phpinsights --fix

