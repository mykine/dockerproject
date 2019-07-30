##### 运用docker搭建1台nginx负载均衡反向代理服务器、3台web应用服务器

#### 设计docker部署方案
|容器名称| 容器IP |端口映射| nginx服务模式 |
|--|--|--|--|
|  nginx-loadbalance |  172.16.1.10|10080-80 |proxy|
|  nginx-web1 		 |  172.16.1.11|10081-80 |web  |
|  nginx-web2 		 |  172.16.1.12|10082-80 |web  |
|  nginx-web3 		 |  172.16.1.13|10083-80 |web  |

##### 已手动创建了容器自定义网段 sentinelnetwork 172.16.0.0/16
##### 基于Dockerfile构建好了redis镜像 redis-base-image
##### 6个节点的redis配置文件提前规划好了，使用容器挂载共享宿主机config目录下使用配置文件
##### 关于手动搭建环境的blog地址[https://blog.csdn.net/Jo_Andy/article/details/96839254](https://blog.csdn.net/Jo_Andy/article/details/96839254)
 
##### ps:注意事项:
 ###### 1.编写yml文件时，遵循类似树状层级型配置参数，除了类似python语法缩进对齐格式外，叶子型参数冒号:后面要多一个空格，否则会报错"ERROR: yaml.scanner.ScannerError: mapping values are not allowed here"
 ###### 2.创建项目环境时运行命令 docker-compose up –d ，其中-d表示后台运行,也可以通过-f指定使用哪个yml文件、-p指定项目名称,如下：
 ```
 docker-compose -f /usr/docker/mygitprojects/redis-sentinel/file/docker-compose.yml -p redisSentinelProject up -d
 ```
 