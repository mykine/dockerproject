FROM centos:latest
RUN yum -y install gcc gcc-c++ wget  pcre pcre-devel zlib zlib-devel openssl  openssl-devel \
&& wget http://nginx.org/download/nginx-1.14.1.tar.gz \
&& tar -zxvf nginx-1.14.1.tar.gz \
&& cd nginx-1.14.1 \
&&  ./configure --prefix=/usr/local/nginx \
 --with-http_stub_status_module \
--with-http_gzip_static_module \
--with-http_realip_module \
--with-http_sub_module \
--with-http_ssl_module \
--with-http_realip_module \
--with-http_sub_module \
--with-http_gunzip_module \
--with-http_gzip_static_module \
--with-http_auth_request_module \
--with-http_random_index_module \
--with-http_slice_module \
--with-http_stub_status_module \
&& make \
&& make install \
&& ln -s /usr/local/nginx/sbin/nginx /usr/local/bin/nginx 
