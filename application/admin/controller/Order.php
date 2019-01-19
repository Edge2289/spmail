<?php
namespace app\admin\controller;

use think\Db;
use Exception;
use think\Session;
use app\admin\common\Base;
use app\common\redis\RedisClice;
use app\admin\model\Order as OrderModel;
/**
 * @Author: 小小
 * @Date:   2019-01-19 14:13:40
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-19 17:49:31
 */
class Order extends Base
{
	public function index()
	{
		$page = empty(input('get.page'))?0:input('get.page');  // 多少页
		$pageSum = empty(input('get.pageSum'))?10:input('get.pageSum'); // 一页显示多少条
		$paytype = empty(input('get.paytype'))?0:input('get.paytype'); // 支付方式
		$ordertype = empty(input('get.ordertype'))?0:input('get.ordertype'); // 订单方式
		$orderddh = empty(input('get.orderddh'))?0:input('get.orderddh'); // 订单号
		$starttime = empty(input('get.starttime'))?0:input('get.starttime'); // 开启时间
		$outtime = empty(input('get.outtime'))?0:input('get.outtime'); // 结束时间
		$order = new OrderModel();
		$orderList = $order->orderList($page, $pageSum, $paytype, $ordertype, $orderddh, $starttime, $outtime);
		// dd($orderList->toArray());
		$this->assign([
				'orderList' => $orderList->toArray(),
			]);
		return $this->fetch();
	}
}