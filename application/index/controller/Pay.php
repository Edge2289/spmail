<?php
namespace app\index\controller;

use think\Session;
use think\Cache;
use think\Db;
use think\Loader;
use app\common\redis\RedisClice;
use app\index\common\Base;
use app\index\model\Goods;

/**
 * @Author: 小小
 * @Date:   2018-12-29 15:38:19
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-04 17:22:06
 */

class Pay extends Base
{

	public function index(){
        
        $listAdd['type'] = explode(',', input('type'));
        $listAdd['num'] = input('num');
        $listAdd['id'] = input('id');
        $listAdd['store_count'] = input('store_count');
        $validate = Loader::validate('IntroValidate');
        if (!$validate->scene('count')->check($listAdd)) {
            dd($validate->getError());
        }
        if (empty($listAdd)) {
            dd("请选择商品！");
        }
        $goodsData = json_decode(Goods::goodsPay($listAdd),true);
        $redis = $this->redisConfig();
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
       
        /*****************************************************************/

        $this->linkbutton();
		$this->assign([
                'listAdd' => $listAdd,
                'userAdd' => $userAdd,
                'goodsList' => $goodsData,
			]);
		return $this->fetch();
	}

}
