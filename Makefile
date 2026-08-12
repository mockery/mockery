SHELL := /bin/sh
PROJECT := /opt/mockery
PHP_IMAGE := ghcr.io/ghostwriter/php
PHP_VERSIONS := 7.3 7.4 8.0 8.1 8.2 8.3 8.4 8.5 8.6

.PHONY: tests
tests: test-7.3 test-8.5

.PHONY: test-all
test-all: $(PHP_VERSIONS:%=test-%)

.PHONY: test
test: deps
	php vendor/bin/phpunit

.PHONY: psalm
psalm:
	docker run -it --rm -v "$$PWD:$(PROJECT)" -w "$(PROJECT)" "$(PHP_IMAGE):8.5" composer psalm

.PHONY: deps
deps: vendor/composer/installed.json

.PHONY: apidocs
apidocs: docs/api/index.html

.PHONY: $(PHP_VERSIONS:%=test-%)
$(PHP_VERSIONS:%=test-%):
	docker run -it --rm -v "$$PWD:$(PROJECT)" -w "$(PROJECT)" "$(PHP_IMAGE):$(@:test-%=%)" make test

vendor/composer/installed.json: composer.json composer.lock
	composer install --no-interaction --prefer-dist

library_files=$(shell find library -name '*.php')
docs/api/index.html: vendor/composer/installed.json $(library_files)
	docker run -it --rm -v "$$PWD:$(PROJECT)" -w "$(PROJECT)" "$(PHP_IMAGE):8.5" php tools/phpdocumentor run -d library -t docs/api
