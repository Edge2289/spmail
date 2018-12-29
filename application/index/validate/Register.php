<?php 
namespace app\index\validate;

use think\Validate;
/**
* 声明register 验证器
*/
class Register extends Validate
{
	//受保护的验证规则
	protected $rule = [
		'__token__' => 'token',
	    'email'=>'require|email',
	    'mobile'=>'require|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
        'code'  => 'require|captcha',
	    'pwd'=>'require|max:20|min:6',
	];

	//受保护的验证信息
	protected $msg = [
	    'email.require' => '名称必须',
	    'email.email'        => '邮箱格式错误',
	    'mobile.require' => '请输入手机号码',
	    'mobile.max' => '手机号码最多不能超过11个字符',
	    'mobile.regex' => '手机号码格式不正确',
	    'pwd.require' => '密码不存在',
        'code.require'  => '验证码不能为空！',
        'code.captcha'  => '验证码错误！',
	    'pwd.max'  => '密码不可以超过20个字符',
	    'pwd.min'  => '密码不可以低于6个字符',
		'__token__'  => '非法操作！',
	];

	// 验证场景
	protected $scene = [
			'email' => 'email,pwd,__token__',
			'phone' => 'mobile,pwd,__token__',
	];
}