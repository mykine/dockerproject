##### 运用docker搭建1台MySQL主节点、2台从节点

#### 设计docker部署方案
|容器名称| 容器IP |端口映射| nginx服务模式 |
|--|--|--|--|
|  mysql-1 |  192.168.5.10|10080-80 |master|
|  mysql-2 		 |  192.168.5.11|13306-3306 |slave  |
|  mysql-3 		 |  192.168.5.12|13307-3306 |salve  |

## ps:注意事项:
### 1.
```
-DCMAKE_INSTALL_PREFIX=/usr/local/mysql          //安装目录
-DINSTALL_DATADIR=/usr/local/mysql/data          //数据库存放目录
-DDEFAULT_CHARSET=utf8                    　　　　//使用utf8字符
-DDEFAULT_COLLATION=utf8_general_ci              //校验字符
-DWITH_EXTRA_CHARSETS=all                        //安装所有扩展字符集
-DENABLED_LOCAL_INFILE=1                    　　  //允许从本地导入数据

```