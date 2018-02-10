.PHONY: test deps build56

vendor/composer/installed.json:
	composer install

deps: vendor/composer/installed.json

test: deps
	php vendor/bin/phpunit

test-all: test-72 test-71 test-70 test-56

test-all-7: test-72 test-71 test-70

test-72: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.2-cli php vendor/bin/phpunit

test-71: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.1-cli php vendor/bin/phpunit

test-70: deps
	docker run -it --rm -v "$$PWD":/opt/mockery -w /opt/mockery php:7.0-cli php vendor/bin/phpunit

test-56: build56
	docker run -it --rm \
		-v "$$PWD/library":/opt/mockery/library \
		-v "$$PWD/tests":/opt/mockery/tests \
		-v "$$PWD/phpunit.xml.dist":/opt/mockery/phpunit.xml \
		-w /opt/mockery \
		mockery_php56 \
		php vendor/bin/phpunit

build56:
	docker build -t mockery_php56 -f "$$PWD/docker/php56/Dockerfile" .
