<?php

/**
 * @Author: 小小
 * @Date:   2018-12-17 13:34:35
 * @Last Modified by:   小小
 * @Last Modified time: 2018-12-18 13:27:33
 */
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

	// 设置状态的中文
	// public function getStatusAttr($value)
 //    {
 //        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
 //        return $status[$value];
 //    }
}