<?php
namespace app\admin\model;

use think\Model;
use think\Db;

use app\admin\model\User;

/**
 * @Author: 小小
 * @Date:   2019-01-19 14:01:06
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-21 17:54:22
 */
class Order extends Model
{
	protected  $table = "shop_order";

	public function getOrderStatusAttr($value)
	{
		// 1=未付款,2=待发货,3=待确认收货,4=已完成,5=已取消
		$status = [1=>"未付款",2=>"待发货",3=>"待确认收货",4=>"已完成",5=>"已取消"];
		return $status[$value];
	} 

	public function getOrderPayAttr($value)
	{
		// 1=未付款,2=待发货,3=待确认收货,4=已完成,5=已取消
		$pay = [0=>"未支付",1=>"在线付款",2=>"货到付款",3=>"微信付款",4=>"支付宝付款"];
		return $pay[$value];
	} 

	public function UserList(){
		return $this->hasOne("User",'user_id','order_uid')
							->field('user_id,user_nick');
	}

	public function AddressList(){
		return $this->hasOne("Address",'address_id','order_address');
	}

	public function GoodsList(){
		return $this->hasOne("Goods",'goods_id','order_sid')
							->field('goods_id,goods_name,original_img');
	}

	public function KuaidiList(){
		return $this->hasOne("Kuaidi",'id','order_fahuo_wuliu')
							->field('id,name');
	}


	/**
	 *  订单列表
	 * @param  [type] $page      [页码]
	 * @param  [type] $pageSum   [页数]
	 * @param  [type] $paytype   [支付方式]
	 * @param  [type] $ordertype [订单方式]
	 * @param  [type] $orderddh  [订单号]
	 * @param  [type] $starttime [开启时间]
	 * @param  [type] $outtime   [结束时间]
	 * @return [type]            [description]
	 */
	public function orderList($data)
	{
		$where = [];
		if (!empty($data['paytype'])) {
			$where['order_pay'] = $data['paytype'];
		}
		if (!empty($data['paytype'])) {
			$where['order_pay'] = $data['paytype'];
		}
		if (!empty($data['ordertype'])) {
			$where['order_status'] = $data['ordertype'];
		}
		if (!empty($data['orderddh'])) {
			$where['order_ddh'] = $data['orderddh'];
		}
		if ($data['paystatus'] == 2) {
			$where['order_status'] = 1;
		}
		if ($data['paystatus'] == 1) {
				unset($where['order_status']);
				return self::with('UserList')
								->with(['AddressList','GoodsList','KuaidiList'])
								->page($data['page'],$data['pageSum'])
								->where($where)
								->where('order_status','<>',1)
								->select();
			}
		return self::with('UserList')
							->with(['AddressList','GoodsList','KuaidiList'])
							->page($data['page'],$data['pageSum'])
							->where($where)
							->where('order_time','>',strtotime($data['starttime']))
							->where('order_time','<',strtotime($data['outtime']))
							->select();
	}

	/**
	 * [orderCount 订单总数]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function orderCount($data)
	{
		$where = [];
		if (!empty($data['paytype'])) {
			$where['order_pay'] = $data['paytype'];
		}
		if (!empty($data['paytype'])) {
			$where['order_pay'] = $data['paytype'];
		}
		if (!empty($data['ordertype'])) {
			$where['order_status'] = $data['ordertype'];
		}
		if (!empty($data['orderddh'])) {
			$where['order_ddh'] = $data['orderddh'];
		}
		if ($data['paystatus'] == 2) {
			$where['order_status'] = 1;
		}
		if ($data['paystatus'] == 1) {
				unset($where['order_status']);
				return self::with('UserList')
								->with(['AddressList','GoodsList','KuaidiList'])
								->page($data['page'],$data['pageSum'])
								->where($where)
								->where('order_status','<>',1)
								->field('count(*) as count')
								->select()
								->toArray();
			}
		return self::with('UserList')
							->with(['AddressList','GoodsList','KuaidiList'])
							->page($data['page'],$data['pageSum'])
							->where($where)
							->where('order_time','>',strtotime($data['starttime']))
							->where('order_time','<',strtotime($data['outtime']))
							->field('count(*) as count')
							->select()
							->toArray();
	}

	/**
	 * [orderCancel 取消订单]
	 * @return [type] [description]
	 */
	public static function orderCancel($gid){
		$order = self::get($gid);
		$orderlist = $order->toArray();
		$data =  [];
		if ($orderlist['order_status'] == 1 || $orderlist['order_status'] == 0) { 
			/**
			 *  1 当用户没有付款的时候
			 */
			$order->order_status = 5;
			$order->save();
			$data = [
					'core' => 1,
					'msg'  => '取消订单成功',
				];
		}else{
			/**
			 *  2 用户付款了，返现
			 *
			 *  在线支付
			 *  微信支付
			 *  支付宝支付
			 */
			Db::startTrans();

			// 在线支付
			if ($order->order_pay == 1) {
				try{
					$order->order_status = 5;
					$order->save();
					$user = User::get($order['order_uid']);
					$user->user_money = ($user->user_money+$order->order_price);
					$user->save();

					$data = [
							'core' => 1,
							'msg'  => '取消订单成功',
						];
					Db::commit();
				}catch (\Excption $e){

					Db::rollback();
					$data = [
					'core' => 0,
					'msg'  => '取消订单失败',
				];
				}
			}else{
				$data = [
					'core' => 0,
					'msg'  => '暂不支持其他支付取消订单',
				];
			}
		}
		
		return json_encode($data);
	}

	/**
	 * [order 订单详情]
	 * @return [type] [description]
	 */
	public static function orderDetails($id){
		return self::with('UserList')
							->with(['AddressList','GoodsList','KuaidiList'])
							->where(['order_gid'=>$id])
							->find();
	}
}