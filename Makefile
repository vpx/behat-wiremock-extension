default: help

help:
	@echo " - test"
	@echo " - phpcs"
	@echo " - phpunit"
	@echo " - behat"

test: phpcs phpunit behat

phpcs:
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 --extensions=php src/ -n -p

phpunit:
	vendor/bin/phpunit --coverage-html build/code-coverage/

behat:
	vendor/bin/behat