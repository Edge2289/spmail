<?php
namespace app\admin\model;

use think\Model;
use app\admin\model\User;
use app\admin\model\Goods;
use app\admin\model\Address;
use app\admin\model\Area;

/**
 * @Author: 小小
 * @Date:   2019-01-19 14:01:06
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-19 17:50:51
 */
class Order extends Model
{
	protected  $table = "shop_order";

	public function getOrderStatusAttr($value)
	{
		// 1=未付款,2=待发货,3=待确认收货,4=已完成,5=已取消
		$status = [1=>"未付款",2=>"待发货",13=>"待确认收货",4=>"已完成",5=>"已取消"];
		return $status[$value];
	} 

	public function getOrderPayAttr($value)
	{
		// 1=未付款,2=待发货,3=待确认收货,4=已完成,5=已取消
		$pay = [0=>"在线付款",1=>"在线付款",2=>"货到付款",3=>"微信付款",4=>"支付宝付款"];
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

	/**
	 * @param  [type] $page      [页码]
	 * @param  [type] $pageSum   [页数]
	 * @param  [type] $paytype   [支付方式]
	 * @param  [type] $ordertype [订单方式]
	 * @param  [type] $orderddh  [订单号]
	 * @param  [type] $starttime [开启时间]
	 * @param  [type] $outtime   [结束时间]
	 * @return [type]            [description]
	 */
	public function orderList($page, $pageSum, $paytype = 0, $ordertype = 0, $orderddh = 0, $starttime = 0, $outtime = 0)
	{
		$where = [];
		if (!empty($paytype)) {
			$where['order_pay'] = $paytype;
		}
		if (!empty($ordertype)) {
			$where['order_status'] = $ordertype;
		}
		if (!empty($orderddh)) {
			$where['order_ddh'] = $orderddh;
		}
		print_r($paytype);
		if (empty($starttime) && empty($outtime)){
			return self::with('UserList')
							->with('AddressList')
							->with('GoodsList')
							->page($page,$pageSum)
							->where($where)
							->select();
		}
		// strtotime
		if (!empty($starttime) && !empty($outtime)) {
			return self::with('UserList')
							->with('AddressList')
							->with('GoodsList')
							->page($page,$pageSum)
							->where($where)
							->where('order_time','<',strtotime($starttime))
							->where('order_time','>',strtotime($outtime))
							->select();
		}
		if (!empty($starttime)) {
			return self::with('UserList')
							->with('AddressList')
							->with('GoodsList')
							->page($page,$pageSum)
							->where($where)
							->where('order_time','<',strtotime($starttime))
							->select();
		}
		if (!empty($outtime)) {
			return self::with('UserList')
							->with('AddressList')
							->with('GoodsList')
							->page($page,$pageSum)
							->where($where)
							->where('order_time','>',strtotime($outtime))
							->select();
		}
	}
}