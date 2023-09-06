vendor/composer/installed.json: composer.json
	composer install

.PHONY: bump
bump: composer.json
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:7.3-pcov composer update --prefer-stable --no-interaction

.PHONY: deps
deps: vendor/composer/installed.json

.PHONY: test
test: deps
	php tools/phpunit

.PHONY: psalm-baseline
psalm-baseline: tools/psalm
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:7.4-pcov tools/psalm --php-version=7.4 --no-diff --no-cache --set-baseline=psalm-baseline.xml --stats

.PHONY: psalm
psalm: tools/psalm
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:7.4-pcov tools/psalm --php-version=7.4 --no-diff --no-cache --shepherd --stats

.PHONY: phive
phive: composer.json
	phive update --force-accept-unsigned
	phive purge

.PHONY: fix
fix: tools/php-cs-fixer
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.2-pcov tools/php-cs-fixer fix --diff --using-cache=no --verbose

.PHONY: apidocs
apidocs: docs/api/index.html

tools/psalm: .phive/phars.xml
	phive install --copy --force-accept-unsigned psalm

tools/phpdocumentor: .phive/phars.xml
	phive install --copy --force-accept-unsigned phpdocumentor

tools/php-cs-fixer: .phive/phars.xml
	phive install --copy --force-accept-unsigned php-cs-fixer

library_files=$(shell find library -name '*.php')
docs/api/index.html: vendor/composer/installed.json $(library_files) tools/phpdocumentor
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.1-pcov php tools/phpdocumentor run -d src -t docs/api

.PHONY: test-all
test-all: test-83 test-82 test-81 test-80 test-74 test-73

.PHONY: test-73
test-73: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:7.3-pcov php tools/phpunit

.PHONY: test-74
test-74: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:7.4-pcov php tools/phpunit

.PHONY: test-80
test-80: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.0-pcov php tools/phpunit

.PHONY: test-81
test-81: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.1-pcov php tools/phpunit

.PHONY: test-82
test-82: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.2-pcov php tools/phpunit

.PHONY: test-83
test-83: deps
	docker run -it --rm -v $$PWD:/opt/mockery -w /opt/mockery ghcr.io/ghostwriter/php:8.3-rc-pcov php tools/phpunit
