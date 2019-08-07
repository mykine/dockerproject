FROM centos:latest
RUN groupadd -r mysql && useradd  -r -g mysql mysql \
&& mkdir /usr/local/mysql \
&& mkdir /usr/local/mysql/data \
&& chown -R mysql:mysql /usr/local/mysql \
&& yum -y install gcc gcc-c++ wget  pcre pcre-devel zlib zlib-devel openssl openssl-devel cmake ncurses-devel bison perl perl-devel perl-Data-Dumper initscripts \
&& wget https://dev.mysql.com/get/Downloads/MySQL-5.6/mysql-5.6.45.tar.gz \
&& tar -zxvf mysql-5.6.45.tar.gz \
&& rm -rf mysql-5.6.45.tar.gz \
&& cd mysql-5.6.45 \
&&  cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql \
-DMYSQL_UNIX_ADDR=/usr/local/mysql/mysql.sock \
-DDEFAULT_CHARSET=utf8 \
-DDEFAULT_COLLATION=utf8_general_ci \
-DWITH_EXTRA_CHARSETS:STRING=utf8,gbk,gb2312 \
-DWITH_MYISAM_STORAGE_ENGINE=1 \
-DENABLED_LOCAL_INFILE=ON \
-DWITH_INNOBASE_STORAGE_ENGINE=1 \
-DWITH_MEMORY_STORAGE_ENGINE=1 \
-DWITH_READLINE=1 \
-DENABLED_LOCAL_INFILE=1 \
-DMYSQL_DATADIR=/usr/local/mysql/data \
-DMYSQL_USER=mysql \
&& make \
&& make install \
&& cd /usr/local/mysql \
&& scripts/mysql_install_db --basedir=/usr/local/mysql --datadir=/usr/local/mysql/data --user=mysql \
&& cp support-files/mysql.server /etc/init.d/mysql \
&& ln -s /usr/local/mysql/bin/mysql /usr/bin/mysql \
&& chkconfig mysql on 
CMD touch ~/play.log.txt && service mysql start && tail -f ~/play.log.txt