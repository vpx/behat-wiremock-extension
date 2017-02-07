default: help

help:
	@echo " - phpcs"
	@echo " - phpunit"

test: phpcs phpunit

phpcs:
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 --extensions=php src/ -n -p

phpunit:
	vendor/bin/phpunit --coverage-html build/code-coverage/
