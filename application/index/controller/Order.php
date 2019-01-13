<?php
namespace app\index\controller;

use think\Db;
use think\Loader;
use think\Session;
use think\Cookie;
use app\index\common\Base;

/**
* 
*/
class Order extends Base
{
	public function sureOrder(){
		$data = json_decode(base64_decode(input('post.data')),true);
		
	}
}