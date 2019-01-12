<?php
namespace app\index\controller;

use think\Session;
use think\Cache;
use think\Cookie;
use think\Db;
use think\Loader;
use think\vendor;
use think\Request;
use app\common\redis\RedisClice;
use app\index\common\Base;
use app\index\model\Goods;

use app\common\api\wxpay\Wxpay;
/**
 * @Author: 小小
 * @Date:   2018-12-29 15:38:19
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-12 17:28:44
 */

class Pay extends Base
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
    /**
     * [index 下单]
     * type 规格选项  用,分来  例如   5_22,3_26
     * num  数量
     * id   商品id
     * store_count   
     * @return [type] [description]
     */
	public function index(){
        $listAdd['type'] = explode(',', input('type'));
        $listAdd['num'] = input('num');
        $listAdd['id'] = input('id');
        $listAdd['store_count'] = input('store_count');
        $validate = Loader::validate('PayValidate');
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
     * [catPay 购物车过来，可能存在多个值]
     * @return [type] [description]
     */
    public function catPay($commit){
        $commit = json_decode(base64_decode($commit),true);
        if (!is_array($commit)) {
            return "参数有误！";
        }
        for ($i=0; $i < count($commit); $i++) { 
            $validate = Loader::validate('PayValidate');
            if (!$validate->scene('count')->check($commit[$i])) {
                dd($validate->getError());
            }
        }
        $goodsData = json_decode(Goods::goodsListPay($commit),true);
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
        // dd($goodsData);
        $this->assign([
                'userAdd' => $userAdd,
                'kuaidi' => $kuaidi,
                'pay' => $pay,
                'city' => $city,
                'userAddrDef' => $userAddrDef,
                'goodsItemList' => $goodsData,
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


    public function wxpay(){
        header("Content-type:text/html;charset=utf-8");
        $tools = new Wxpay();
        // vendor('phpqrcode/phpqrcode');
        // $bb = new \QRcode();
        // $url = urldecode($tools->native());
        // dd($url);
        // $url1 = "weixin://wxpay/bizpayurl?appid=wx2421b1c4370ec43b&mch_id=10000100&nonce_str=f6808210402125e30663234f94c87a8c&product_id=1&time_stamp=1415949957&sign=512F68131DD251DA4A45DA79CC7EFE9D";
        // echo $bb::png($url);
        // die;
        dd($tools->wxpay('JSAPI支付测试','14156599902019','1415659990',100,3));
    }
}
