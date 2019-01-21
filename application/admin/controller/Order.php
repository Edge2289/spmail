<?php
namespace app\admin\controller;

use think\Db;
use Exception;
use think\Session;
use think\Loader;
use app\admin\common\Base;
use app\common\redis\RedisClice;
use app\admin\model\Order as OrderModel;
/**
 * @Author: 小小
 * @Date:   2019-01-19 14:13:40
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-21 17:52:54
 */
class Order extends Base
{
	public function index()
	{
		// starttime=&outtime=&paytype=0&paytype=0&ordertype=0&orderddh=&paystatus=
		$data['page'] = empty(input('get.page'))?0:input('get.page');  // 多少页
		$data['pageSum'] = empty(input('get.pageSum'))?10:input('get.pageSum'); // 一页显示多少条
		$data['paytype'] = empty(input('get.paytype'))?0:input('get.paytype'); // 支付方式
		$data['paystatus'] = empty(input('get.paystatus'))?0:input('get.paystatus'); // 支付状态
		$data['ordertype'] = empty(input('get.ordertype'))?0:input('get.ordertype'); // 订单方式
		$data['orderddh'] = empty(input('get.orderddh'))?'':input('get.orderddh'); // 订单号
		$data['starttime'] = empty(input('get.starttime'))?'':input('get.starttime'); // 开启时间
		$data['outtime'] = empty(input('get.outtime'))?date("Y-m-d",time()):input('get.outtime'); // 结束时间
		$order = Loader::Validate('OrderValidate');
		if (!$order->check($data)) {
			dd($order->getError());
		}
		// 交互区
		$order = new OrderModel();
		$orderList = $order->orderList($data);
		$orderCount = $order->orderCount($data);
		$this->assign([
				'orderList' => $orderList->toArray(),
				'page' => $data['page'],
				'pageSum' => $data['pageSum'],
				'paytype' => $data['paytype'],
				'ordertype' => $data['ordertype'],
				'orderddh' => $data['orderddh'],
				'paystatus' => $data['paystatus'], 
				'orderCount' => $orderCount[0]['count'], 
				'starttime' => $data['starttime'],
				'outtime' => $data['outtime'],
			]);
		return $this->fetch();
	}

	/**
	 * [orderDetails 订单详情]
	 * @return [type] [description]
	 */
	public function orderDetails(){
		$id = input('get.id');
		$orderList = OrderModel::orderDetails($id);
		// dd($orderList->toArray());
		$this->assign('orderList',$orderList->toArray());
		return $this->fetch();
	}

	/**
	 * [orderfahuo 发货显示]
	 * @return [type] [description]
	 */
	public function orderfahuo(){
		$kuaidi = Db('shop_kuaidi')
						->where('static',0)
						->field('id,name')
						->select();
		$kdxx = Db('shop_order')
						->where('order_gid',input('get.id'))
						->field('order_fahuo_wuliu,order_fahuo_wuliudh')
						->find();
		$this->assign([
				'gid' => input('get.id'),
				'kuaidi' => $kuaidi,
				'kdxx' => $kdxx,
			]);
		return $this->fetch();
	}

	/**
	 * [orderAjaxFahuo description]
	 * @return [type] [description]
	 */
	public function orderAjaxFahuo(){
		$wuliu = input('post.wuliu');
		$wuliudh = input('post.wuliudh');
		$gid = input('post.gid');
		if (!is_numeric($wuliu) || !is_numeric($wuliu)) {
			return [
					'core' => 0,
					'msg' => "参数错误",
				];
		}

		$status = OrderModel::get(['gid',$gid]);
		if (empty($status) || !$status->getData('order_status') == 2) {
			return [
					'core' => 0,
					'msg' => "订单有误",
				];
		}
		$status->order_status = 3;
		$status->order_fahuo_wuliu = $wuliu;
		$status->order_fahuo_wuliudh = $wuliudh;
		$status->order_fahuo_time = time();
		$status->save();
		return [
					'core' => 1,
					'msg' => "成功",
				];
	}

	/**
	 * [orderCancel 取消订单]
	 * @return [type] [description]
	 */
	public function orderCancel(){
		$gid = input('post.gid');
		$type =  OrderModel::orderCancel($gid);
		return json_decode($type,true);
	}
	
}