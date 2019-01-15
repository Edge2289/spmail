<?php
namespace app\index\validate;
use think\Validate;
/**
 * @Author: 小小
 * @Date:   2019-01-15 11:37:29
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-15 14:34:07
 */
class OrderValidate extends Validate
{
	
	protected $rule = [
			'__token__' => '__token__',
			'liuyan' => 'max:50',
			'pay' => 'require|number',
			'address' => 'require|number',
			'time' => 'require|number|max:13',
			/******************************/
			'goodsNum' => 'require|number',
			'goodsid' => 'require|number',
			'itemId' => 'require',
		];

	protected $msg = [
			'liuyan.max' => '留言字数不可以超多50个字符',
			'pay.require' => '支付方式必填',
			'pay.number' => '参数错误',
			'address.require' => '地址必选',
			'address.number' => '参数错误',
			'time.require' => '参数错误',
			'time.number' => '参数错误',
			'time.max' => '参数错误',
			'goodsNum.max' => '参数错误',
			'goodsNum.max' => '参数错误',
			'goodsid.max' => '参数错误',
			'goodsid.max' => '参数错误',
			'itemId.max' => '参数错误',
		];
	protected $scene = [
			'qita' => ['liuyan','pay','address','time'], // __token__
			'goods'=> ['goodsNum','goodsid','itemId'],
		];
}