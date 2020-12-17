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
test-all: test-74 test-73 test-72 test-71 test-70 test-56

.PHONY: test-all-7
test-all-7: test-74 test-73 test test-71 test-70

.PHONY: test-74
test-74: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.4-cli php vendor/bin/phpunit

.PHONY: test-73
test-73: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.3-cli php vendor/bin/phpunit

.PHONY: test-72
test-72: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.2-cli php vendor/bin/phpunit

.PHONY: test-71
test-71: build71
	docker run -it --rm \
		-v "$$PWD/library":/opt/mockery/library \
		-v "$$PWD/tests":/opt/mockery/tests \
		-v "$$PWD/phpunit.xml.dist":/opt/mockery/phpunit.xml \
		-w /opt/mockery \
		mockery_php71 \
		php vendor/bin/phpunit

.PHONY: build71
build71:
	docker build -t mockery_php71 -f "$$PWD/docker/php71/Dockerfile" .

.PHONY: test-70
test-70: build70
	docker run -it --rm \
		-v "$$PWD/library":/opt/mockery/library \
		-v "$$PWD/tests":/opt/mockery/tests \
		-v "$$PWD/phpunit.xml.dist":/opt/mockery/phpunit.xml \
		-w /opt/mockery \
		mockery_php70 \
		php vendor/bin/phpunit

.PHONY: build70
build70:
	docker build -t mockery_php70 -f "$$PWD/docker/php70/Dockerfile" .

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
