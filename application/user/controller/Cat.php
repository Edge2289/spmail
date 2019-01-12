<?php
namespace app\user\controller;

use think\Session;
use think\Cache;
use think\Db;
use think\Request;
use think\Cookie;
use app\user\common\Base;

/**
 * @Author: 小小
 * @Date:   2018-12-19 11:28:06
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-12 09:45:54
 */

class Cat extends Base
{
	public function _initialize(){
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
        $this->assign([
            'linka' => $linka,
            'link_one' => $link_one,
            'link' => $link,
            'dataCat' => json_decode(cookie::get('dataCat'),true),
        ]);
	}


	public function index(){
		// dd(json_decode(cookie::get('dataCat'),true));
		 $this->assign([
            'dataCat' => json_decode(cookie::get('dataCat'),true),
        ]);
		return $this->fetch();
	}

	/**
	 * 添加购物车
	 */
	public function cookieCat(Request $request){
		$dataCat = json_decode(input('post.dataCat'),true);
		// Cookie::delete('dataCat');
		/**
		 * [$dataCookieCat 比较cookie里面得购物车]
		 * @var [type]
		 */
		$dataCookieCat = json_decode(cookie::get('dataCat'),true);
		if (empty($dataCookieCat)) {
			$data = array($dataCat);
			cookie::set('dataCat',json_encode($data));
		}else{
		// dd($dataCookieCat);
			$catGoodsNum = 0;
			for ($i=0; $i < count($dataCookieCat); $i++) { 
				$catSpec = 0;
				if($dataCookieCat[$i]['goods_id'] == $dataCat['goods_id']){
					if (count($dataCookieCat[$i]['catSpeclist']) == count($dataCat['catSpeclist'])) {
							for ($y=0; $y < count($dataCookieCat[$i]['catSpeclist']); $y++) { 
								if ($dataCookieCat[$i]['catSpeclist'][$y] == $dataCat['catSpeclist'][$y]) {
									$catSpec++;
								}
							}
							if ($catSpec == count($dataCookieCat[$i]['catSpeclist'])) {
								$dataCookieCat[$i]["num"] += $dataCat['num'];
								$catGoodsNum++;
							}
					}
					cookie::set('dataCat',json_encode($dataCookieCat));
				}
			}
			if (empty($catGoodsNum)) {
				$dataCookieCat[count($dataCookieCat)] = $dataCat;
				cookie::set('dataCat',json_encode($dataCookieCat));
			}
		}
		return json_encode([
				'core' => '1',
				'msg'  => '添加成功'
			]);
	}
}