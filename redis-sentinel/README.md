##### 运用docker-compose编排包含6个容器的项目:3哨兵监控1主2从读写分离redis高可用架构

#### 设计docker部署方案
|容器名称| 容器IP |端口映射| Redis服务模式 |
|--|--|--|--|
|  redis-sentinel1 |  172.16.0.101|22531-26379  |sentinel  |
|  redis-sentinel2 |  172.16.0.102|22532-26379  |sentinel  |
|  redis-sentinel3 |  172.16.0.103|22533-26379  |sentinel  |
|  redis-master    |  172.16.0.111|22631-6379     | master  |
|  redis-slave1     |  172.16.0.112|22632-6379    | slave     |
|  redis-slave2     |  172.16.0.113|22633-6379    | slave     |

已手动创建了容器自定义网段 sentinelnetwork 172.16.0.0/16
基于Dockerfile构建好了redis镜像 redis-base-image
6个节点的redis配置文件提前规划好了，使用容器挂载共享宿主机config目录下使用配置文件
关于手动搭建环境的blog地址 https://blog.csdn.net/Jo_Andy/article/details/96839254
