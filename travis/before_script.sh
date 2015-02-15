#!/bin/bash
if [[ "$TRAVIS_PHP_VERSION" != "hhvm" \
  && "$TRAVIS_PHP_VERSION" != "hhvm-nightly" \
  && "$TRAVIS_PHP_VERSION" != "5.3.3" ]]; then
  phpenv config-add ./travis/extra.ini
fi
phpenv rehash