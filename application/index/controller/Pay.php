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
use app\index\model\Shop_user;

use app\common\api\wxpay\Wxpay;
/**
 * @Author: 小小
 * @Date:   2018-12-29 15:38:19
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-19 14:57:38
 */

class Pay extends Base
{
    public function _initialize(){

        // if (empty(Session::get('user_id'))) {
        //     dd('_empty');
        // }
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

    /**
     * [xianpay 订单在线支付]
     * @return [type] [description]
     * 
     */
    public function xianpay(Request $request){
        $pay = input('post.payPassword');
        if (empty($pay) || $pay == '' || !is_numeric($pay) || strlen($pay) != 6) {
            return ['code' => 0, 'message' => '参数错误'];
        }
        // 获取订单数据
        $order = json_decode(base64_decode(input('post.order')),true);
        // 查询数据
        $orderStatus = Db::table('shop_order')
                        ->where('order_ddh',$order['order_id'])
                        ->field('order_status,order_time,order_sid,order_uid')
                        ->find();
        if (empty($orderStatus)) {
            return ['code'=>0,'message'=>'订单有误！'];
        }
        /**
         * [$use 第一，先判断此订单是否属于此人的
         *        第二，判断此商品是否还在上架
         *        第三，判断订单是否在规定时间付款
         *        第四，判断此商品状态是否支付以及取消      
         * ]
         * @var [type]
         */
        // 第一
        if ((int)$orderStatus['order_uid'] != (int)Session::get('user_id')) {
            return ['code'=>0,'message'=>'参数有误'];
        }
        // 第二
        $goodsStatus = Goods::where('goods_id',$orderStatus['order_sid'])
                                ->where('is_on_sale',1)
                                ->find();
        if (empty($goodsStatus)) {
            return ['code'=>0,'message'=>'该商品已下架'];
        }
        // 第三
        if (time() > ($orderStatus['order_time']+3600*2)) {
            Db::table('shop_order')->where('order_ddh',$order['order_id'])->update(['order_status'=>'5']);
            return ['code'=>0,'message'=>'订单超时，请重新下单'];
        }
        // 第四
        if ($orderStatus['order_status'] != 1) {
            return ['code'=>0,'message'=>'订单已结束，请重新下单'];
        }

        $user = Db::table('shop_user')
                        ->alias('a')
                        ->join('shop_salt b','a.user_id = b.user_id')
                        ->where('a.user_id',Session::get('user_id'))
                        ->where('a.user_is_lock',1)
                        ->field('a.user_paypwd,a.user_money,b.salt_pay_pwd')
                        ->find();
        if (empty($user)) {
            return ['code'=>2,'message'=>'用户不存在，请重新登陆'];
        }
        if (empty($user['user_paypwd'])) {
            return ['code'=>3,'message'=>'请设置支付密码！'];
        }
        // 判断支付密码是否正确
        //  不正确添加记录  达到三次账号锁定
        if (!$this->webpay($user['user_paypwd'], $pay ,$user['salt_pay_pwd'])) {
            $userModel = Shop_user::get(Session::get('user_id'));
            Shop_user::where('user_id',Session::get('user_id'))
                                ->setInc('user_error_paypwd',1);
            if ($userModel['user_error_paypwd'] >= 3) {
                $userModel->user_is_lock = 0;
                $userModel->save();
                $this->userLog('用户：'.Session::get('user_id').'输入密码错误达到三次，账号被封！',$request,'index/pay/xianpay');
                return ['code'=>6,'message'=>'账号输入密码错误达到三次，账号被封！'];
            }
            $this->userLog('用户：'.Session::get('user_id').'支付输入密码错误',$request,'index/pay/xianpay');
            return ['code'=>5,'message'=>'密码输入错误，请重新输入！','data'=>$userModel['user_error_paypwd']];
        }
        /*   判断用户余额是否够支付 */
        if (((float)$user['user_money']-(float)abs($order['order_price'])) >= 0) {
            Db::startTrans();
            try{
                $userModel = Shop_user::get(Session::get('user_id'));
                $userModel->user_money = ((float)$user['user_money']-(float)abs($order['order_price']));
                $userModel->save();
                $this->userLog('用户：'.Session::get('user_id').'下单成功',$request,'');
                $i = Db::table('shop_order')
                        ->where('order_ddh',$order['order_id'])
                        ->update(['order_status' => 2,'order_pay'=>1]);
                // 写入资金日志
                $insert = [
                        'user_id' => Session::get('user_id'), 
                        'user_money' => '-'.$order['order_price'], 
                        'change_time' => time(), 
                        'desc' => "商城下单支付使用", 
                        'order_sn' => $order['order_id'], 
                    ];
                $b = Db('shop_account_log')->insertGetId($insert);
                if (!$i || !$b) {
                    throw new Exception("操作错误");
                }
                Db::commit();
                return ['code'=>1,'message'=>'支付成功'];

            } catch(\Excption $e){
                Db::rollback();
            return ['code'=>0,'message'=>$e->getMessage()];
            }
        }else{
            return ['code'=>0,'message'=>'账号余额不足'];
        }
    }
}
