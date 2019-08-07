#!/bin/bash 
touch ~/play.log.txt
service mysql start && tail -f ~/play.log.txt