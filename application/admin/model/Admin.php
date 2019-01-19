<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{
	protected $table = 'shop_admin';

	// 程序加载时加载
	protected static function init(){

		// 更新前验证  false 回去不做更改
		Admin::event('before_update', function($admin){
				if($admin->status != 1){
					echo '--- 123 ---';
					return false;
				}
		});

		// 更新后验证， flase回去不输出
		Admin::event('after_update', function($admin){
				if($admin->status != 0){
					echo '--- 789 ---';
					return false;
				}
		});
	}

	public function profile(){
		return $this->hasOne('Profile','admin_id')->bind([
				'admin_email',
				'admin_name'
			]);
	}

}