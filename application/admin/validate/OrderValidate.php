<?php
namespace app\admin\validate;

use think\Validate;
/**
 * @Author: 小小
 * @Date:   2019-01-21 11:36:28
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-21 13:08:14
 * starttime	开始时间
 * outtime		结束时间
 * paystatus	支付状态
 * paytype		支付类型
 * ordertype	订单状态
 * orderddh		订单号
 * page 		页数
 * pageSum		一页显示多少条
 */

class OrderValidate extends Validate
{
	
	protected $rule = [
				'starttime' => 'date',
				'outtime' => 'require|date',
				'paystatus' => 'require|integer',
				'paytype' => 'require|integer',
				'ordertype' => 'require|integer',
				'page' => 'require|integer',
				'pageSum' => 'require|integer',
	];

	protected $msg = [
				'starttime.require' => '参数错误',
				'starttime.date' => '参数错误',

				'outtime.require' => '参数错误',
				'outtime.date' => '参数错误',


				'paystatus.date' => '参数错误',
				'paystatus.integer' => '参数错误',

				'paytype.date' => '参数错误',
				'paytype.integer' => '参数错误',

				'ordertype.date' => '参数错误',
				'ordertype.integer' => '参数错误',

				'orderddh.date' => '参数错误',

				'page.date' => '参数错误',
				'page.integer' => '参数错误',

				'pageSum.date' => '参数错误',
				'pageSum.integer' => '参数错误',
	];
}