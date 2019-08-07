##### 运用docker搭建1台MySQL主节点、2台从节点

#### 设计docker部署方案
|容器名称| 容器IP |端口映射| nginx服务模式 |
|--|--|--|--|
|  mysql-1 |  192.168.5.10|10080-80 |master|
|  mysql-2 		 |  192.168.5.11|13306-3306 |slave  |
|  mysql-3 		 |  192.168.5.12|13307-3306 |salve  |

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

### 5.有个问题:想做到挂载mysql的文件到宿主机，但是一般的-v挂载会造成宿主机的空文件夹覆盖mysql的有内容的文件夹丢失文件，mysql启动不了
volumes:
       - /usr/docker/mysql/share/mysql1/data:/usr/local/mysql/data