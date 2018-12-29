<?php
namespace app\user\controller;

use think\Session;
use app\user\common\Base;

/**
 * @Author: 小小
 * @Date:   2018-12-19 11:28:06
 * @Last Modified by:   小小
 * @Last Modified time: 2018-12-19 11:29:43
 */

class Cat extends Base
{
	
	public function index(){
		return "hello ，我是购物车";
	}
}