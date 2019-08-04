<?php
/**
 * Created by PhpStorm.
 * User: 7
 * Date: 2019/7/26
 * Time:
 * composer require Predis/Predis
 */

namespace App\Common;
use mysql_xdevapi\Exception;
use Predis;

class RedisClusterHelper
{
    //（主节点）配置信息
    const hostsConfig = [
        ['publicIP'=>'38.112.201.233','privateIP'=>'192.168.1.10','port'=>6110],
        ['publicIP'=>'38.112.201.233','privateIP'=>'192.168.2.20','port'=>6220],
        ['publicIP'=>'38.112.201.233','privateIP'=>'192.168.3.30','port'=>6330],
    ];

    //节点数组
    private static $_hostsArr = null;

    //连接对象
    private static $_client = null;

    //CRC16
    private static $_crc16 = null;

/***
 * 连接节点
 * @param string $forNodeHost 指定节点IP:Port 不传就使用随机节点
 * return 连接对象
*/
    private static function connectNode( $forNodeHost = null )
    {
        $retryLimit = 5;//最多重试连接次数
        $retry = 0 ;//重连变量
        RETRY:{
            try{
                if(!self::$_client){
                    self::$_hostsArr = [];
                    foreach (self::hostsConfig as $configVal){
                        self::$_hostsArr[] = $configVal['publicIP'].':'.$configVal['port'];
                        $privateIPHostArr[$configVal['privateIP']] = $configVal;
                    }
                    self::$_client = new Predis\Client(self::$_hostsArr,['cluster'=>'redis']);
                    self::$_crc16 = new Predis\Cluster\Hash\CRC16();
                }
                if(!$forNodeHost){
                    $forNodeHost = self::$_hostsArr[array_rand(self::$_hostsArr)];
                }
                $nodeClient = self::$_client->getClientFor($forNodeHost);
                return $nodeClient;
            }catch (\Exception $e){
                echo $e->getMessage();
                if( $retryLimit <= $retry ){
                    echo $e->getMessage();
//                    throw new Exception('重试次数过多，连接失败~|'.$forNodeHost);
                }else{
                    ++$retry;
                    sleep(1);
                    goto RETRY;
                }
            }
        }

    }

/**
 * 获取对应公网IP
 */
    private static function getPublicIP($ip)
    {
        foreach (self::hostsConfig as $configVal){
            if($ip==$configVal['publicIP']){
                return $ip;
            }elseif($ip==$configVal['privateIP']){
                return $configVal['publicIP'];
            }
        }
        throw new Exception('找不到对应公网IP|$ip|'.$ip);
    }


/**
 * 根据key连接节点
  $slotInfo   Array
(
    [0] => Array
        (
            [0] => 5462 //槽begin
            [1] => 10922 //槽end
            [2] => Array //Master节点信息
                (
                    [0] => 47.112.201.233//可能会返回内网IP
                    [1] => 6220 //端口
                    [2] => 1176214ff9defaed191d04099c6aa187db2584b5 //nodeId
                )

            [3] => Array
                (
                    [0] => 47.112.201.233 //Slave节点信息
                    [1] => 6221 //端口
                    [2] => db99e17cd62d9a1163b4aefc5adfa611818a8ef3 //nodeId
                )

        )

)
 */
    public static function getNodeClientByKey($key)
    {
        $nodeClient = self::connectNode();
        $slotInfo = $nodeClient->executeRaw(['cluster','slots']);
        $slotNodes = [];
        foreach ($slotInfo as $value){
            $keyArr = $value[0].','.$value[1];
            $slotNodes[$keyArr] = self::getPublicIP($value[2][0]).':'.$value[2][1];
        }
        $slotNum = self::getSlotByHashKey($key);
        $Node = null;
        array_walk($slotNodes,function ($node,$slotRange) use ($slotNum,&$Node){
            $slotRangeArr = explode(',',$slotRange);
            if($slotRangeArr[0]<=$slotNum && $slotRangeArr[1]>=$slotNum){
                $Node = $node;
            }
        });
        $nodeClient = self::connectNode($Node);
        return $nodeClient;
    }

/***
 * setKeyValue
 *
 */
    public static function set($key,$value)
    {
        $nodeClient = self::getNodeClientByKey($key);
        $nodeClient->set($key,$value);
    }

    /***
     * 带过期时间(剩余多少秒失效)set
     *
     */
    public static function setex($key,$ttlSeconds,$value)
    {
        $nodeClient = self::getNodeClientByKey($key);
        $nodeClient->setex($key,$ttlSeconds,$value);
    }


/***
 * getKeyValue
 *
 */
    public static function get($key)
    {
        $nodeClient = self::getNodeClientByKey($key);
        return $nodeClient->get($key);
    }

/***
 * getKeyValue
 *
 */
    public static function del($key)
    {
        $nodeClient = self::getNodeClientByKey($key);
        return $nodeClient->del($key);
    }

/***
 * 计算key的哈希值
 *
 */
    private static function getSlotByHashKey($key)
    {
        return self::$_crc16->hash($key) % 16384;
    }


}
