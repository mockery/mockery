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
	wget https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.0.0/phpDocumentor.phar
	wget https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.0.0/phpDocumentor.phar.asc

library_files=$(shell find library -name '*.php')
docs/api/index.html: vendor/composer/installed.json $(library_files) phpDocumentor.phar
	php phpDocumentor.phar run -d library -t docs/api

.PHONY: test-all
test-all: test-74 test-73

.PHONY: test-74
test-74: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.4-cli php vendor/bin/phpunit

.PHONY: test-73
test-73: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.3-cli php vendor/bin/phpunit
