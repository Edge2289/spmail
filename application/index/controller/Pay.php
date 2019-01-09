<?php
namespace app\index\controller;

use think\Session;
use think\Cache;
use think\Db;
use think\Loader;
use think\Request;
use app\common\redis\RedisClice;
use app\index\common\Base;
use app\index\model\Goods;

/**
 * @Author: 小小
 * @Date:   2018-12-29 15:38:19
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-09 16:55:23
 */

class Pay extends Base
{

	public function index(){
        // if ($this->request->isPost()) {
        //     echo "非法操作";die;
        // }

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
    public function userAdd(Request $request){

        $ajax = [
            'code' => '0',
            'msg' => "操作失败！",
        ];

        $user = Session::get('user_id');
        if (empty($user)) {
            $ajax['msg'] = "请登录！";
            return json_encode($ajax);
        }
        if($this->request->isGet()){
            return json_encode($ajax);
        }

        $data = json_decode(input('post.data'),true);
        $valida = Loader::Validate('UserAddrValidate');
        if (!$valida->check($data)) {
           $ajax = [
                'code' => '0',
                'msg' => $valida->getError(),
            ];
        }else{
            $data['user_id'] = $user;
            $i = Db::table('shop_user_address')->insertGetId($data);
            if ($i) {
                $dataList = Db::table('shop_user_address')->where('address_id',$id)->find();
                $ajax = [
                    'code' => '1',
                    'msg' => "添加成功！",
                    'data' => $dataList,
                ];  
            }
        }
        return json_encode($ajax);
    }
}
