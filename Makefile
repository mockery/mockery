.PHONY: test help

MOCKERY_PHP_VER?=mockery_php72

help:
	@echo "Run Mockery tests locally from PHP 5.6 up to 7.2 with docker"
	@echo "You need to have docker-ce and docker-compose installed."
	@echo "Run 'export MOCKERY_PHP_VER=mockery_phpXY' to set the PHP version"
	@echo "And then run 'make test'"
	@echo "Available values for MOCKERY_PHP_VER: mockery_php72, mockery_php71, mockery_php70, mockery_php56"

test:
	docker-compose run --rm ${MOCKERY_PHP_VER} vendor/bin/phpunit
