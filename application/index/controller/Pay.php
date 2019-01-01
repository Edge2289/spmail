<?php
namespace app\index\controller;

use think\Session;
use think\Cache;
use think\Db;
use app\common\redis\RedisClice;
use app\index\common\Base;

/**
 * @Author: 小小
 * @Date:   2018-12-29 15:38:19
 * @Last Modified by:   小小
 * @Last Modified time: 2018-12-29 16:40:49
 */

class Pay extends Base
{
	public function index(){

        $redis = $this->redisConfig();
		$list = input('post.list');
        /*****************************************************************/
        $userAdd = Db::table('shop_user_address')
        				->alias('a')
        				->join('shop_area b','a.country = b.Id')
        				->join('shop_area c','a.province = c.Id')
        				->join('shop_area d','a.city = d.Id')
        				->join('shop_area e','a.district = e.Id')
        				->where('a.user_id',Session::get('user_id'))
        				->field('a.consignee as name,a.address,a.mobile,a.is_default,b.Name as country,c.Name as province,d.Name as city,e.Name as district')
        				->select();
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
        /*****************************************************************/

		$this->assign([
				'data' => $list,
            	'link_one' => $link_one,
				'link' => $link,
				'linka' => $linka,
				'userAdd' => $userAdd,
			]);
		return $this->fetch();
	}

}
