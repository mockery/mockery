vendor/composer/installed.json: composer.json
	composer install

.PHONY: deps
deps: vendor/composer/installed.json

.PHONY: test
test: deps
	php vendor/bin/phpunit

.PHONY: apidocs
apidocs: docs/api/index.html

library_files=$(shell find library -name '*.php')
docs/api/index.html: vendor/composer/installed.json $(library_files) 
	vendor/bin/phpdoc -d library -t docs/api

.PHONY: test-all
test-all: test-72 test-71 test-70 test-56

.PHONY: test-all-7
test-all-7: test-72 test-71 test-70

.PHONY: test-72
test-72: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.2-cli php vendor/bin/phpunit

.PHONY: test-71
test-71: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.1-cli php vendor/bin/phpunit

.PHONY: test-70
test-70: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.0-cli php vendor/bin/phpunit

.PHONY: test-56
test-56: build56
	docker run -it --rm \
		-v "$$PWD/library":/opt/mockery/library \
		-v "$$PWD/tests":/opt/mockery/tests \
		-v "$$PWD/phpunit.xml.dist":/opt/mockery/phpunit.xml \
		-w /opt/mockery \
		mockery_php56 \
		php vendor/bin/phpunit

.PHONY: build56
build56:
	docker build -t mockery_php56 -f "$$PWD/docker/php56/Dockerfile" .
