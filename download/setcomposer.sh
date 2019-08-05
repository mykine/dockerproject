#!/bin/bash
echo "composer安装开始..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
echo "composer安装结束"
composer --version
