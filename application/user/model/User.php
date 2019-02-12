<?php
namespace app\user\model;

use think\Model;
use think\Session;
/**
 * @Author: 小小
 * @Date:   2019-01-18 13:48:43
 * @Last Modified by:   小小
 * @Last Modified time: 2019-02-12 12:44:24
 */

class User extends Model
{
	protected $table = "shop_user";

	public static function UserEdit($data){
		$infoData['core'] = 0;
		$info = self::where(['user_nick'=>$data['user_nick']])
						->where('user_id','NEQ',Session::get('user_id'))
						->find();
		if (!empty($info)) {
			$infoData['msg'] = '昵称已存在!';
			return $infoData;
		}
			$i = self::where('user_id',Session::get('user_id'))->update($data);
		if ($i) {
			$infoData['core'] = 1;
			$infoData['msg'] = '修改成功!';
		}else{
			$infoData['msg'] = '修改失败!';
		}
			return $infoData;
	}
}
