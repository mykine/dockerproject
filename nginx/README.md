##### 运用docker搭建1台nginx负载均衡反向代理服务器、3台web应用服务器

#### 设计docker部署方案
|容器名称| 容器IP |端口映射| nginx服务模式 |
|--|--|--|--|
|  nginx-loadbalance |  192.168.5.10|10080-80 |proxy|
|  nginx-web1 		 |  192.168.5.11|10081-80 |web  |
|  nginx-web2 		 |  192.168.5.12|10082-80 |web  |
|  nginx-web3 		 |  192.168.5.13|10083-80 |web  |

## ps:注意事项:
### 1.Docker容器后台运行要有一个前台进程，否则容器会自动退出,这里nginx启动要用前台方式 -g "daemon off;"
```
nginx -c /usr/local/nginx/conf/nginx.conf -g "daemon off;"
```
 