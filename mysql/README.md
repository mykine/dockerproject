##### 运用docker容器搭建环境演练Mysql架构演变：
##### 1.初期：单机
##### 2.中期：一主多从、读写分离
##### 3.后期：多主多从的集群模式：使用MyCat对Mysql1进行分库分表,数据写入到mysql2、mysql3两个Master节点,然后数据复制到Mysql4、Mysql5两个Slave节点上

#### 设计docker部署方案
|容器名称| 容器IP |端口映射| nginx服务模式 |
|--|--|--|--|
|  mysql1 		 |  192.168.16.11|13307-3306 |master   |
|  mycat 		 |  宿主机        |8066  | 中间件      |
|  mysql2 		 |  192.168.16.12|13308-3306 |slave/master  |
|  mysql3 		 |  192.168.16.13|13309-3306 |salve/master  |
|  mysql4 		 |  192.168.16.14|13310-3306 |salve |
|  mysql5 		 |  192.168.16.15|13311-3306 |salve |


## ps:注意事项:

### 1.编译安装时Cmake的部分参数说明
```
-DCMAKE_INSTALL_PREFIX=/usr/local/mysql          //安装目录
-DINSTALL_DATADIR=/usr/local/mysql/data          //数据库存放目录
-DDEFAULT_CHARSET=utf8                    　　　　//使用utf8字符
-DDEFAULT_COLLATION=utf8_general_ci              //校验字符
-DWITH_EXTRA_CHARSETS=all                        //安装所有扩展字符集
-DENABLED_LOCAL_INFILE=1                    　　  //允许从本地导入数据

```



### 2.mysql5.6默认没有密码直接登录，登录进入后更改密码步骤如下:
```
  use mysql;
  update user set password = PASSWORD('123456') where user = 'root';
  flush privileges;
```
### 3.开放IP访问用于测试
```
   GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '123456' WITH GRANT OPTION; 
   flush privileges;
```
### 4.远程登录测试
```
  mysql -h 192.168.1.111 -P 3306 -u root -p123456
```

### 5.有个问题:想做到挂载mysql的data文件到宿主机，但是一般的-v挂载会造成宿主机的空文件夹覆盖mysql的有内容的文件夹丢失文件，mysql启动不了
volumes:
       - /usr/docker/mysql/share/mysql1/data:/usr/local/mysql/data