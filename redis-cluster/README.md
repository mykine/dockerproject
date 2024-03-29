## 运用docker-compose编排redis-cluster缓存集群，实现分布式存储缓存:分布在不同机器上的3台master节点分别负责存储无交集的一部分数据实现数据分布，同步到各自的从节点实现数据备份

# 节点规划
|容器名称| 容器IP |端口映射| Redis服务模式 |
|--|--|--|--|
|  redis-master10    |  192.168.1.10 |6110-6110 16110-16110     | master  |
|  redis-slave11     |  192.168.1.11 |6111-6111 16111-16111     | slave   |
|  redis-master20    |  192.168.2.20 |6220-6220 16220-16220     | master  |
|  redis-slave21     |  192.168.2.21 |6221-6221 16221-16221     | slave   |
|  redis-master30    |  192.168.3.30 |6330-6330 16330-16330     | master  |
|  redis-slave31     |  192.168.3.31 |6331-6331 16331-16331     | slave   |

## 搭建集群
#### 步骤一:使用compose创建6个redis节点
#### 步骤二:使用cluster meet ip地址 端口号 ，利用Gossip流言协议握手通信,例如先进入节点redis-cli使用下面命令
36.112.201.233是容器宿主机公网IP，6111是宿主机端口号，映射了容器端口号6111
```
cluster meet 36.112.201.233 6111
cluster meet 36.112.201.233 6220
cluster meet 36.112.201.233 6221
cluster meet 36.112.201.233 6330
cluster meet 36.112.201.233 6331

```
#### 步骤三:设置主从关系，通过redis-cli连接从节点redis命令终端，使用命令cluster replicate 主节点nodeId
### nodeId是指 cluster nodes 命令显示中的id，不是info中的run_id
```
redis-cli -h 36.112.201.233 -p 6111
cluster replicate 8864eb1b98a44df0b2081ee1b0c5613c00268492

redis-cli -h 36.112.201.233 -p 6221
cluster replicate 1176214ff9defaed191d04099c6aa187db2584b5

redis-cli -h 36.112.201.233 -p 6331
cluster replicate 6a161a04a5bf4bb967f08de5eaaa361f7e179caf

```
#### 步骤四:为主节点分配数据槽，瓜分0~16383编号，在宿主机正常的bash命令行中（而不是节点的redis-cli客户端中）进行批量操作
```
redis-cli -h 36.112.201.233 -p 6110 cluster addslots {0..5461}
redis-cli -h 36.112.201.233 -p 6220 cluster addslots {5462..10922}
redis-cli -h 36.112.201.233 -p 6330 cluster addslots {10923..16383}

```
#### 步骤五:集群模式读取数据，通过-c配置项，会帮客户端重定向到key对应的节点进行存取数据
```
redis-cli -h 36.112.201.233 -p 6331 -c  set name1 jo 
redis-cli -h 36.112.201.233 -p 6331 -c get name1

```

## ps:注意事项:
### 1.使用redis-cli客户端连接redis-server时 要指定端口号,不指定就会用默认的6379
 ```
 redis-cli -p 6110
 ```
 
### 2.物理部署cluster时，一般每一台机器下面都有master和slave，但同一台机器下的slave逻辑上并非是属于该物理机器下的master，而是交叉地服务与另外一台物理机器上的master，作为其备份。这样能避免当一台物理机器失效了，就整个master和其对应的slave同时失效，进而导致整个redis-cluster则无法继续提供服务。错开交叉方式的master-slave配置，具备更高的可用性。
 
### 3.客户端读写集群redis数据逻辑原理:
#### 首先，通过crc16(key)%16383算法算出这个key对应的数据槽编号n
#### 然后，根据槽编号n查找这个槽所在的redis节点nodeM
#### 最后，连接节点nodeM读取数据
 
### 4.Redis集群中的从节点，默认是不分担读请求的，从节点只作为主节点的备份，仅负责故障转移,当有请求是在向从节点发起时，会直接重定向到对应key所在的主节点来处理
#### 如果要实现读写分离还需额外配置从节点，在redis-cli中运行 readonly 命令即可，将slave临时设置成可读
 ```
 readonly
 ```
 
### 5.redis性能和水平扩展
#### 一般的，对于redis单个实例的读吞吐是4w/s~5W/s，写吞吐为2w/s。
#### 在原redis-cluster架构中的每台物理机上增加redis进程实例,在同一台机器上合理开启redis多个实例情况下（一般实例或线程数最好为CPU核数的倍数），总吞吐量会有所上升，但每个实例线程的平均处理能力会有所下降。例如一个2核CPU，开启2个实例的时候，总读吞吐能上升是6W/s~7W/s，即每个实例线程平均约3W/s再多一些。但同一台物理机器中过多的redis实例反而会限制了总吞吐量，而且一旦一台物理机器失效，会导致多个实例同时失效，从而导致redis-cluster全体失效的风险增加。
#### 新增机器，部署新redis-master实例，即物理上的水平扩展：例如，我们可以再原来只有3台master的基础上，连入新机器继续新实例的部署，最终水平扩展为6台master（建议每新增一个master，也新增一个对应的slave）。例如之前每台master的处理能力假设是读吞吐5W/s,写吞吐2W/s,扩展前一共的处理能力是：15W/s读，6W/s写。如果我们水平扩展到6台master，读吞吐可以达到总量30W/s，写可以达到12w/s，性能能够成倍增加
##### 部分参考: https://my.oschina.net/u/2600078/blog/1923696
 
### 6.CAP理论为：在分布式系统中，多个节点之间只能满足CP或AP，即强一致性和高可用是不能同时满足的。Redis的主从同步是AP的，具体对高可用的强度要求，可用通过在redis.conf配置，即有至少有多少个slaves存在和至多多少秒内没响应，则才执行写请求，否则报错
##### 部分参考: https://blog.csdn.net/u010013573/article/details/83965510 
