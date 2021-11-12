phpstan:
	./vendor/bin/phpstan analyse

phpinsights:
	./vendor/bin/phpinsights

analyse:
	make phpstan
	make phpinsights

fix:
	./vendor/bin/phpinsights --fix

