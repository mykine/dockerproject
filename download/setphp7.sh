#!/bin/bash
yum -y  install  gcc  gcc-c++ libxml2-devel m4 autoconf pcre-devel make cmake bison openssl openssl-devel  libxml2-devel
mkdir /usr/local/php
mkdir ~/download
cd ~/download
wget http://cn2.php.net/distributions/php-7.2.15.tar.gz
tar -zxvf php-7.2.15.tar.gz
cd php-7.2.15 
echo "开始安装..."
echo "configure..."
./configure --prefix=/usr/local/php --with-config-file-path=/usr/local/php/etc-with-libxml-dir=/usr   --with-mhash --with-openssl --with-mysqli=shared,mysqlnd --with-pdo-mysql=shared,mysqlnd --with-zlib --enable-zip --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-xml --enable-bcmath --enable-shmop --enable-sysvsem --enable-mbregex--enable-mbstring --enable-pcntl --enable-sockets --without-pear --with-gettext --enable-session
echo "make..."
make
echo "make install..."
make install 
ln -s  /usr/local/php/bin/php /usr/local/bin/php
echo "安装结束"
php -v

