<?php
namespace app\index\model;
use think\Model;

/**
* 用户表
*/
class Shop_user extends Model
{
	protected $table = 'shop_user';

	/**
	 * [$hidden 测试]
	 * @var [type]
	 */
	protected $hidden = ['user_id','user_sex'];

	protected $type = [
			'user_reg_time' => 'timestamp',
			'user_last_time' => 'timestamp',
	];

	public function getUserIsVipAttr($value){
		$vip = [
				0 => '青铜会员',
				1 => '白银会员',
				2 => '黄金会员',
				3 => '白金会员',
				4 => '荣耀会员',
				5 => '王者会员',
				6 => '至尊会员',
			];
			return $vip[$value];
	}

	public function userAddress(){
		return $this->hasOne('ShopAddress', 'user_id', 'user_id');
	}

	public static function getUserAddress($id){
		return self::with('userAddress')->find($id)->toArray();
	}
}