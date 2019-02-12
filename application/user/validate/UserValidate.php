<?php
namespace app\user\validate;
/**
 * @Author: 小小
 * @Date:   2019-02-12 11:36:59
 * @Last Modified by:   小小
 * @Last Modified time: 2019-02-12 12:45:36
 */

use think\Validate;

class UserValidate extends Validate
{
	protected $rule = [
		'user_nick' => 'require',
		'user_sex' => 'require',
		'user_birthday' => 'require',
	];

	protected $message = [
		'user_nick.require' => '用户昵称必填',
		'user_sex.require' => '性别必填',
		'user_birthday.require' => '生日必填',
	];
}