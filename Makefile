.PHONY: tests
tests: test-73 test-83

vendor/composer/installed.json: composer.json
	composer install

.PHONY: deps
deps: vendor/composer/installed.json

.PHONY: test
test: deps
	php vendor/bin/phpunit

.PHONY: apidocs
apidocs: docs/api/index.html

phpDocumentor.phar:
	wget https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.3.1/phpDocumentor.phar
	wget https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.3.1/phpDocumentor.phar.asc

library_files=$(shell find library -name '*.php')
docs/api/index.html: vendor/composer/installed.json $(library_files) phpDocumentor.phar
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.1-pcov php phpDocumentor.phar run -d library -t docs/api

.PHONY: test-all
test-all: test-83 test-82 test-81 test-80 test-74 test-73

.PHONY: test-73
test-73: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:7.3-pcov php vendor/bin/phpunit

.PHONY: test-74
test-74: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:7.4-pcov php vendor/bin/phpunit

.PHONY: test-80
test-80: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.0-pcov php vendor/bin/phpunit

.PHONY: test-81
test-81: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.1-pcov php vendor/bin/phpunit

.PHONY: test-82
test-82: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.2-pcov php vendor/bin/phpunit

.PHONY: test-83
test-83: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.3-pcov php vendor/bin/phpunit
