#!/bin/bash 
touch ~/play.log.txt
/usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf && tail -f ~/play.log.txt