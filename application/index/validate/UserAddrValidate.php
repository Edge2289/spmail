<?php
namespace app\index\validate;

use think\Validate;

/**
 * @Author: 小小
 * @Date:   2019-01-09 09:07:24
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-09 15:28:02
 */
/**
 * 	"consignee": "aa",   // 用户名
	"mobile": "11",		 // 手机号码
	"zipcode": "1",		// 国家区号
	"country": 100000,	// 省
	"province": "120000",	// 市
	"city": "120100",		// 区
	"district": "120101",	//县
	"twon": 0,				// 村
	"address": "123"		地址
 */
class UserAddrValidate extends Validate
{
	protected $rule = [
			"consignee" => "require|max:50",
			"mobile" => "require",
			"zipcode" => "require|number",
			"country" => "require|number",
			"province" => "require|number",
			"city" => "require|number",
			"district" => "require|number",
			"address" => "require|max:100"
	];

	protected $msg = [
			"consignee.require" => "收货人必填",
			"consignee.max" => "收货人长度不可以超多50个",
			"mobile.require" => "手机号必填",
			// "mobile.mobile" => "手机号格式不对",
			"zipcode.require" => "参数必填",
			"zipcode.number" => "参数格式不正确",
			"country.require" => "参数必填",
			"country.number" => "参数格式不正确",
			"province.require" => "参数必填",
			"province.number" => "参数格式不正确",
			"city.require" => "参数必填",
			"city.number" => "参数格式不正确",
			"district.require" => "参数必填",
			"district.number" => "参数格式不正确",
			"address.require" => "参数必填",
			"address.max" => "地址大小不可以超多100个",
	];
}