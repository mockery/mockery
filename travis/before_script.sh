#!/bin/bash
if [[ $TRAVIS_PHP_VERSION != "hhvm" \
  && $TRAVIS_PHP_VERSION != "hhvm-nightly" \
  && $TRAVIS_PHP_VERSION != "7.0" ]]; then
  phpenv config-add ./travis/extra.ini
  phpenv rehash
fi

if [[ $TRAVIS_PHP_VERSION == "5.6" ]]; then
  sed '/MockeryPHPUnitIntegration/d' -i ./phpunit.xml.dist
fi
