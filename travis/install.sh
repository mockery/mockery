#!/bin/bash
export PATH=$PWD/phar:$PATH
if [[ "$TRAVIS_PHP_VERSION" == "5.3.3" ]]; then
    echo PATH is $PATH
fi
composer install -n