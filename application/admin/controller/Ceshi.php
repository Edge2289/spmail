<?php
namespace app\admin\Controller;
/**
 * @Author: 小小
 * @Date:   2018-12-17 13:34:35
 * @Last Modified by:   小小
 * @Last Modified time: 2018-12-18 14:05:33
 */
use think\Controller;
use app\admin\model\Admin;
use think\Request;
use app\admin\common\Base;

class Ceshi extends Base
{
	
	public function index(){
		$admin = new admin;
		$admin->admin_name = 'jack';
		$admin->admin_email = '1131191695@qq.com';
		$admin->admin_password = 'e56419914765c6d22dc5018114d73a28';
		$admin->admin_salt = '123456';
		$admin->admin_time = time();
		$admin->admin_role = 0;
		$admin->admin_juris = 0;
		$admin->admin_login_time = time();
		$admin->admin_login_ip = '127.0.0.1';
		$admin->save();
		// 获取自增ID
		echo $admin->admin_id;
		print_r($admin::select());
	}

	public function update(){
		// 取出主键为1的数据 
		$admin = admin::get(2);
		$admin->admin_name = 'thinkphp1';
		$admin->admin_email = 'thinkphp@qq.com';
		$admin->save();
		print_r($admin);
	}

	public function delete(){
		Admin::destroy('3,4');
		print_r(Admin::select());
	}

	public function select(){
		header('content-type:text/html;charset=utf-8');
		$admin = new Admin;
		$list = $admin::get(2)->topics()->select();
		print_r($list);
		die;
		print_r($admin::get(['admin_name'=>'jack']));
	}

	public function ovg(){
		$admin = new Admin();
		print_r($admin->count());
		die;
		$admin->where();
	}

	public function dataAll(){
		$list = admin::all();
		$list_da = Admin::get(1);
		print_r($list_da->hidden(['admin_juris','admin_id','admin_email'])->toJson());
		die;
		print_r(collection($list)->toArray());
	}

	public function guanlian(){

		$aget = Request::instance()->header('user-agent');
		print_r($aget);
		$request = Request::instance();
echo '路由信息：';
dump($request->route());
echo '调度信息：';
dump($request->dispatch());
	}
}