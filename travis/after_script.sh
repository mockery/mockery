#!/bin/bash
if [[ "$TRAVIS_PHP_VERSION" != "hhvm" && "$TRAVIS_PHP_VERSION" != "hhvm-nightly" ]]; then
  vendor/bin/coveralls -v
fi

if [[ "$TRAVIS_PHP_VERSION" == "5.6" ]]; then
  wget https://scrutinizer-ci.com/ocular.phar
fi

if [[ "$TRAVIS_PHP_VERSION" == "5.6" ]]; then
  php ocular.phar code-coverage:upload --format=php-clover ./build/logs/clover.xml
fi