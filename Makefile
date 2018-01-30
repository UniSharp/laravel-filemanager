test:
	vendor/bin/phpunit --coverage-text
	vendor/bin/phpcs --version && echo &&	vendor/bin/phpcs -p --standard=PSR2 --ignore=src/lang/,src/views/ src tests
