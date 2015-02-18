#!/bin/bash
if [[ "$TRAVIS_PHP_VERSION" != "hhvm" && "$TRAVIS_PHP_VERSION" != "hhvm-nightly" ]]; then
  vendor/bin/phpunit --coverage-text --coverage-clover ./build/logs/clover.xml
fi

if [[ "$TRAVIS_PHP_VERSION" == "hhvm" || "$TRAVIS_PHP_VERSION" == "hhvm-nightly" ]]; then
  vendor/bin/phpunit
fi