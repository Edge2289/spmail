<?php
namespace app\admin\controller;

use think\Session;
use think\Db;
use app\admin\model\Admin;
use app\admin\common\Base;

/**
 *  管理员表
 */
class Manager extends Base
{
	/**
	 * [index 显示管理员列表]
	 * @return [type] [description]
	 */
	public function index(){
		$data = Admin::field('admin_id,admin_name,admin_email,admin_role,status,admin_time')->select();
		$this->assign('data',$data);
		return $this->fetch();
	}

	public function manaedit(){
		$id = input('get.id');
		$data = null;
		if(!empty($id)){
			$data = Admin::get($id);
		}
		$this->assign('data',$data);
		return $this->fetch('manaedit');
	}

	public function log(){
		$pindex = empty(input('get.pindex'))?0:input('get.pindex');
		$psizeInt = empty(input('get.psizeInt'))?10:input('get.psizeInt');
		$data = Db::table('shop_admin_log')
		             ->page($pindex,$psizeInt)
		             ->order('log_id desc')
		             ->select();
		$this->assign([
				'data'=>$data,
				'dataSum'=>Db::table('shop_admin_log')->select(),
				'pindex'=>$pindex,
				'psizeInt'=>$psizeInt,
			]);
		return $this->fetch();
	}
}