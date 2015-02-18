#!/bin/bash
if [[ "$TRAVIS_PHP_VERSION" == "5.3.3" ]]; then
  pushd $HOME
  git clone --depth 1 https://github.com/php/php-src.git
  pushd php-src
  ./buildconf -f
  ./configure --with-curl --prefix=$HOME/.phpenv/versions/7 --quiet
  make --quiet
  make install
  phpenv global 7
  popd
  popd
  echo Travis PHP Version is $TRAVIS_PHP_VERSION
  wget http://getcomposer.org/composer.phar
  mkdir $PWD/phar
  mv composer.phar $PWD/phar/composer
  chmod +x $PWD/phar/composer
else
  composer self-update
fi