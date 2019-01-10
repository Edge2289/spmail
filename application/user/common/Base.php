<?php

namespace app\user\common;

use think\Controller;
use app\common\redis\RedisClice;
/**
* 
*/
class Base extends Controller
{
	// redis 示例话
    public function redisConfig()
    {
        $conf = [
            'host' =>  '120.79.65.240', // 127.0.0.1
            'port' =>  '6379',
            'auth' =>  '3600',
            'index' =>  '11'
        ];
        return new RedisClice($conf);
    }
	
}