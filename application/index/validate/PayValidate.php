<?php
namespace app\index\validate;

use think\Validate;
/**
 * @Author: 小小
 * @Date:   2019-01-04 11:01:50
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-23 17:19:41
 */

class PayValidate extends Validate
{
	// 验证的数据
	protected $rule = [
			'__token__'=>'require|token', //这里__token__不能去改.
			// 'type' => 'require', // alphaDash  token
			'num' => 'require|number|integer',
			'id' => 'require|number|integer',
			'store_count' => 'require|number|integer',
	];

	// 验证的返回信息
	protected $message = [
			// 'type.require' => '参数类型错误，请重新提交0',
			// 'type.token' => '请不要重复提交',
			'num.require' => '参数类型错误，请重新提交1',
			'num.number' => '参数类型错误，请重新提交2',
			'num.integer' => '参数类型错误，请重新提交3',
			'id.require' => '参数类型错误，请重新提交4',
			'id.number' => '参数类型错误，请重新提交5',
			'id.integer' => '参数类型错误，请重新提交6',
			'store_count.require' => '请输入数字(数量)',
			'store_count.number' => '格式有误(数量)',
			'store_count.integer' => '格式有误(数量)',
	];

	protected $scene = [
		'count' => ['type','num','id','store_count'],
	];
}