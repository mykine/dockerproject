#!/bin/bash
yum -y  install  gcc  gcc-c++ libxml2-devel m4 autoconf pcre-devel make cmake bison openssl openssl-devel  libxml2-devel bzip2 bzip2-devel readline-devel curl-devel libjpeg-devel libpng libpng-devel freetype-devel
mkdir /usr/local/php
groupadd -r www-data && useradd -r -g www-data www-data 
chown -R www-data:www-data /usr/local/php
mkdir /webroot
chmod -R 755 /webroot
chown -R www-data:www-data /webroot
mkdir ~/download
cd ~/download
wget http://cn2.php.net/distributions/php-7.2.15.tar.gz
tar -zxvf php-7.2.15.tar.gz
cd php-7.2.15 
echo "开始安装..."
echo "configure..."
./configure \
--prefix=/usr/local/php \
--with-fpm-user=www-data \
--with-fpm-group=www-data \
--disable-fileinfo \
--with-config-file-path=/usr/local/php/etc \
--with-mhash \
--with-mysqli=shared,mysqlnd \
--disable-debug \
--with-gettext \
--with-pdo-mysql=shared,mysqlnd \
--with-bz2 \
--with-gd \
--with-freetype-dir \
--with-jpeg-dir \
--with-png-dir \
--with-zlib-dir \
--with-libxml-dir \
--with-readline \
--with-curl \
--with-pear \
--with-openssl \
--enable-fpm \
--enable-xml \
--enable-bcmath \
--enable-shmop \
--enable-sysvsem \
--enable-inline-optimization \
--enable-mbregex \
--enable-mbstring \
--enable-pcntl \
--enable-sockets \
--enable-soap \
--enable-session \
--enable-zip
echo "make..."
make
echo "make install..."
make install 
ln -s  /usr/local/php/bin/php /usr/local/bin/php
ln -s  /usr/local/php/bin/phpize /usr/local/bin/phpize
ln -s  /usr/local/php/bin/pecl /usr/local/bin/pecl
ln -s  /usr/local/php/bin/php-config /usr/local/bin/php-config
ln -s  /usr/local/php/sbin/php-fpm /usr/local/bin/php-fpm
cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
cp /usr/local/php/etc/php-fpm.d/www.conf.default /usr/local/php/etc/php-fpm.d/www.conf
cp php.ini-production /usr/local/php/etc/php.ini
cp sapi/fpm/init.d.php-fpm /etc/init.d/php-fpm
chmod 755 /etc/init.d/php-fpm
chkconfig --add php-fpm
service php-fpm start
echo "安装结束"
echo "请手动更改/usr/local/php/etc/php-fpm.conf的pid参数为为pid = /usr/local/php/var/run/php-fpm.pid "
php -v

