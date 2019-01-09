<?php
namespace app\index\controller;

use app\index\common\Base;
use think\Session;
use think\Cache;
use think\Request;

/**
*
*	@param   前台  购物车
* 
*/
class Shopcart extends Base
{

	public function index()
	{
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
		 $this->assign([
            'linka' => $linka,
            'link_one' => $link_one,
            'link' => $link,
        ]);
		return $this->fetch('shopcart');
	}
}