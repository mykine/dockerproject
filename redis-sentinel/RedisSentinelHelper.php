<?php
/**
 * Created by PhpStorm.
 * User: 7
 * Date: 2019/7/26
 * Time:
 * composer require Predis/Predis
 */

namespace App\Common;

use Illuminate\Queue\Console\RetryCommand;


class RedisSentinelHelper
{
    //哨兵配置信息
    const sentinelConfig = [
        ['ip'=>'36.112.201.233','port'=>22531],
        ['ip'=>'36.112.201.233','port'=>22532],
        ['ip'=>'36.112.201.233','port'=>22533],
    ];
    //连接哨兵对象
    private static $_sentinelClient = null;

    //（主从节点）配置信息
    const hostsConfig = [
        ['publicIP'=>'36.112.201.233','privateIP'=>'172.16.0.111','port'=>22631],
        ['publicIP'=>'36.112.201.233','privateIP'=>'172.16.0.112','port'=>22632],
        ['publicIP'=>'36.112.201.233','privateIP'=>'172.16.0.113','port'=>22633],
    ];

/***
 * 连接节点
 * @param int $type 访问方式:1写数据 2读数据
 * @param  ['ip'=>'','port'=>''] 需要连接的节点信息,默认null表示随机连接
 * return 连接的实例对象
*/
    private static function connectNode($type,$nodeHost=null)
    {
        $retryLimit = 2;//最多重试连接次数
        $retry = 0;//重连计数器
        RETRY:{
           try{
               if(!self::$_sentinelClient){
                   $sentinelHost = self::sentinelConfig[array_rand(self::sentinelConfig)];
                   $sentinelClient = new \Redis();
//                   $sentinelClient->auth('');
                   $sentinelClient->pconnect($sentinelHost['ip'],$sentinelHost['port'],3);
                   self::$_sentinelClient = $sentinelClient;
               }
               if(!$nodeHost){
                   //采用随机节点
                   if( 1 == $type ){
                       //写操作要连接主节点
                       $nodeInfo = self::$_sentinelClient->rawCommand('sentinel','master','mymaster');
                   }else{
                       //读操作要连接从节点
                       $nodeInfoArr = self::$_sentinelClient->rawCommand('sentinel','slaves','mymaster');
                       $nodeInfo = $nodeInfoArr[array_rand($nodeInfoArr)];
                   }
                   $nodeHostInfo = self::getPublicInfo($nodeInfo[3]);
                   $nodeHost['ip'] = $nodeHostInfo['ip'];
                   $nodeHost['port'] = $nodeHostInfo['port'];
               }
               $client = new \Redis();
               $res = $client->connect($nodeHost['ip'],$nodeHost['port'],3);
//               $client->auth('');
               return $client;
           } catch (\Exception $e){
                echo $e->getMessage();
                if( $retryLimit<=$retry ){
                    throw new Exception('连接次数过多，连接失败~');
                }
                $retry++;
               goto RETRY;
           }
        }

    }

    /**
     * 获取对应公网IP和port信息
     * @return  array('ip'=>'','port'=>'')
     */
    private static function getPublicInfo($ip)
    {
        foreach (self::hostsConfig as $configVal){
            if($ip==$configVal['publicIP']){
                return ['ip'=>$ip,'port'=>$configVal['port']];
            }elseif($ip==$configVal['privateIP']){
                return ['ip'=>$configVal['publicIP'],'port'=>$configVal['port']];
            }
        }
        throw new Exception('找不到对应公网IP|$ip|'.$ip);
    }

/**
 * set数据
 *
 * @param   string  $key
 * @param   string  $value
 * @param   int|array  设置过期时间,0表示不设置，>0表示N秒后过期，或者传数组形式具体参数
 *                      $timeout [optional] Calling setex() is preferred if you want a timeout.<br>
 *                      Since 2.6.12 it also supports different flags inside an array. Example ['NX', 'EX' => 60]<br>
 *                      EX seconds -- Set the specified expire time, in seconds.<br>
 *                      PX milliseconds -- Set the specified expire time, in milliseconds.<br>
 *                      PX milliseconds -- Set the specified expire time, in milliseconds.<br>
 *                      NX -- Only set the key if it does not already exist.<br>
 *                      XX -- Only set the key if it already exist.<br>
 * @return  bool    TRUE if the command is successful.
 * @link    https://redis.io/commands/set
 * @example $redis->set('key', 'value');
 */
    public static function set($key,$value,$timeout=0)
    {
        $nodeClient = self::connectNode(1);
        if($timeout<=0){
            $res = $nodeClient->set($key,$value);
        }else{
            $res = $nodeClient->set($key,$value,$timeout);
        }
        if(!$res){
            throw new \Exception('set操作失败|'.json_encode(func_get_args()));
        }
    }

/**
 * get数据
 */
    public static function get($key)
    {
        $nodeClient = self::connectNode(2);
        return $nodeClient->get($key);
    }


}