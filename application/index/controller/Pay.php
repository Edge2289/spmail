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
 * @Last Modified time: 2019-01-08 17:44:38
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
        /********************  地址 **********************************/
        $userAdd = Db::table('shop_user_address')
        				->alias('a')
        				->join('shop_area b','a.country = b.Id')
        				->join('shop_area c','a.province = c.Id')
        				->join('shop_area d','a.city = d.Id')
        				->join('shop_area e','a.district = e.Id')
        				->where('a.user_id',Session::get('user_id'))
        				->field('a.address_id,a.consignee as name,a.address,a.mobile,a.is_default,b.Name as country,c.Name as province,d.Name as city,e.Name as district')
        				->select();
        $userAddrDef =  Db::table('shop_user_address')
                        ->alias('a')
                        ->join('shop_area b','a.country = b.Id')
                        ->join('shop_area c','a.province = c.Id')
                        ->join('shop_area d','a.city = d.Id')
                        ->join('shop_area e','a.district = e.Id')
                        ->where('a.user_id',Session::get('user_id'))
                        ->where('a.is_default',0)
                        ->field('a.address_id,a.consignee as name,a.address,a.mobile,a.is_default,b.Name as country,c.Name as province,d.Name as city,e.Name as district')
                        ->find();
        /********************  城市json **********************************/
        $city = Db::table("shop_area")                 
                        ->field("id,Name,ParentId")
                        ->where('id','<>',0)
                        ->where('ParentId','<>',0)
                        ->select();   
        /********************  城市json **********************************/
       $kuaidi = Db::table('shop_kuaidi')->where('static',0)->field('id,name,logo')->select();
       $pay = Db::table('shop_pay')->where('static',0)->field('id,pay_name,pay_img')->select();
        /*****************************************************************/
        $this->linkbutton();
		$this->assign([
                'listAdd' => $listAdd,
                'userAdd' => $userAdd,
                'kuaidi' => $kuaidi,
                'pay' => $pay,
                'city' => $city,
                'userAddrDef' => $userAddrDef,
                'goodsList' => $goodsData,
			]);
		return $this->fetch();
	}


    /**
     * [userAdd 用户地址添加]
     * @return [type] [description]
     */
    public function userAdd(){
        $data = input('post.data');
        dd($data);
    }
}
