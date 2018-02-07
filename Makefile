.PHONY: test le

MOCKERY_PHP_VER?=mockery_php71

le:
	@echo "MOCKERY_PHP_VER: mockery_php72, mockery_php71, mockery_php70"

test:
	docker-compose run --rm ${MOCKERY_PHP_VER} vendor/bin/phpunit
