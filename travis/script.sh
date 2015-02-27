#!/bin/bash
if [[ $TRAVIS_PHP_VERSION != "hhvm" \
  && $TRAVIS_PHP_VERSION != "hhvm-nightly" \
  && $TRAVIS_PHP_VERSION != "7.0" ]]; then
  vendor/bin/phpunit --coverage-text --coverage-clover ./build/logs/clover.xml
else
  vendor/bin/phpunit
fi
