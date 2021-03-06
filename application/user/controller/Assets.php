<?php
namespace app\user\controller;

use think\Cache;
use think\Session;
use think\Cookie;
use think\Db;
use app\user\common\Base;
use app\user\model\User;
use app\user\model\Order;
/**
 * @Author: 小小
 * @Date:   2019-01-18 15:50:26
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-18 17:47:00
 */
class Assets extends Base
{
		public function _initialize()
	{
		if (empty(Session::get('user_id'))) {
			$this->redirect('index/index/login');
		}
        parent::_initialize();
        $redis = $this->redisConfig();
         //去友情链接
        if (Cache::get('link')) {
            $link = Cache::get('link');
        }else{
            $link = Db::table('shop_link')->select();
            Cache::set('link',$link,rand(3000, 3600));
        }

        //  链接表
        if (json_decode($redis->get("linka"),true)) {
            $linka = json_decode($redis->get("linka"),true);
        }else{
            $linka = Db::view('shop_artcleclass','ac_id,ac_title')
                    ->view('shop_articlelist','al_title','shop_artcleclass.ac_id=shop_articlelist.ac_id')
                    ->where('shop_artcleclass.is_sys','=',1)
                    ->where('shop_artcleclass.index_static','=',1)
                    ->order('ac_order')
                    ->select();
            $redis->set("linka",json_encode($linka),rand(3000, 3600));
         }
           foreach ($linka as $key => $value) {
           $i[$key] = $value['ac_title'];
        }
        $link_one = explode("_m_", implode(array_unique($i), "_m_"));
        $userData = User::get(Session::get('user_id'))
    					->toArray();
        $this->assign([
            'linka' => $linka,
            'link_one' => $link_one,
            'link' => $link,
            'userData' => $userData,
            'dataCat' => json_decode(cookie::get('dataCat'),true),
        ]);
	}
	/**
	 * [order 账户余额]
	 * @return [type] [description]
	 */
	public function wallet(){
    	return $this->fetch();
	}

	/**
	 * [order 账单明细]
	 * @return [type] [description]
	 */
	public function walletlist(){
    	return $this->fetch();
	}

	/**
	 * [order 积分商城]
	 * @return [type] [description]
	 */
	public function points(){
    	return $this->fetch();
	}

}