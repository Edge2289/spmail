<?php
namespace app\common\redis;

use app\common\redis\RedisClice;
/**
 * @Author: 小小
 * @Date:   2019-01-24 10:33:01
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-24 11:34:03
 */

/**
 *  redis分布式锁
 */
class RedisLock 
{
    private $_redis;

	public function __construct()
	{
		$conf = [
            'host' =>  '120.79.65.240', // 127.0.0.1
            'port' =>  '6379',
            'auth' =>  '3600',
            'index' =>  '11'
        ];
        $this->_redis = new RedisClice($conf);
	}

	/**
	 * [lock 获取锁 获取成功返回true]
	 * @param  [type]  $key    [键名]
	 * @param  integer $expire [过期时间]
	 * @param  integer $num    [获取失败重试次数]
	 * @return [type]          [description]
	 * hSetNx($redis_key,$name,$data,$timeOut=0)
	 */
	public function lock($key, $expire = 5, $num = 0){
		$is_lock = $this->_redis->setnx($key, time()+$expire);
		if (!$is_lock) {
			// 获取锁失败重试
			for ($i=0; $i < $num; $i++) { 
				$is_lock = $this->_redis->setnx($key, time()+$expire);
				if ($is_lock) {
					break;
				}
				sleep(1);
			}
		}

		// 不能获取锁
		if (!$is_lock) {
			// 判断锁是否过期
			$lock_time = $this->_redis->get($key);
			// 锁过期，删除锁，重新获取锁
			if (time() > $lock_time) {
				$this->unlock($key);
				$is_lock = $this->_redis->setnx($key, time()+$expire);
			}
		}
		return $is_lock ? true : false;
	}

	/**
	 * [unlock 释放锁]
	 * @param  [type] $key [锁表识]
	 * @return [type]      [description]
	 */
	public function unlock($key){
		return $this->_redis->del($key);
	}
}