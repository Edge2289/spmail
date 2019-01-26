<?php
namespace app\user\validate;

use think\Validate;
/**
 * @Author: 小小
 * @Date:   2019-01-26 17:39:02
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-26 17:55:17
 */

/**
 * name
 * mobile
 * parse_one
 * parse_two
 * parse_thr
 * address
 */
class AddressValidate extends Validate
{
	protected $rule = [
			'name' => 'require|max:50',
			'mobile' => 'require|mobile',
			'parse_one' => 'require|integer',
			'parse_two' => 'require|integer',
			'parse_thr' => 'require|integer',
			'address' => 'require|max:100',
		];

	protected $msg = [
			'name.require' => '名称必填',
			'name.max' => '名称过长',
			'mobile.require' => '手机号码必填',
			'mobile.mobile' => '手机号码格式不对',
			'parse_one.require' => '请选择省份',
			'parse_one.integer' => '参数错误',
			'parse_two.require' => '请选择城市',
			'parse_two.integer' => '参数错误',
			'parse_thr.require' => '请选择县',
			'parse_thr.integer' => '参数错误',
			'address.require' => '详细地址必填',
			'address.max' => '详细地址过长',
		];
	
}