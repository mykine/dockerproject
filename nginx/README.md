##### 运用docker搭建1台nginx负载均衡反向代理服务器、3台web应用服务器

#### 设计docker部署方案
|容器名称| 容器IP |端口映射| nginx服务模式 |
|--|--|--|--|
|  nginx-loadbalance |  192.168.5.10|10080-80 |proxy|
|  nginx-web1 		 |  192.168.5.11|10081-80 |web  |
|  nginx-web2 		 |  192.168.5.12|10082-80 |web  |
|  nginx-web3 		 |  192.168.5.13|10083-80 |web  |

## ps:注意事项:
### 1.Docker容器后台运行要有一个前台进程，否则容器会自动退出,这里nginx启动要用前台方式 -g "daemon off;"或者挂起其他程序同时后台运行nginx
```
touch ~/play.log.txt &&  /usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf && tail -f ~/play.log.txt
```
或
```
/usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf -g "daemon off;"
```
### 2.一般地，应当总是使用绝对路径来启动 nginx，以避免不必要的麻烦，即类似 /usr/local/nginx/sbin/nginx -c /usr/local/nginx/conf/nginx.conf
如果是查找环境变量中的nginx来启动的，以后进行平滑升级时给nginx传递信号执行操作时会报错:make: *** [upgrade] Error 1

参考：https://groups.google.com/forum/#!topic/openresty/HiV3c-JwTZ4
https://jpuyy.com/2016/05/nginx-upgrade-failed.html