<?php
namespace app\user\model;

use think\Model;

/**
 * @Author: 小小
 * @Date:   2019-01-24 09:55:25
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-24 10:00:22
 */

class Address extends Model
{
	
	protected $table = "shop_user_address";

	public function getCountryAttr($value){
		$date = Area::get(['Id' => $value]);
		return $date['Name'];
	}

	public function getProvinceAttr($value){
		$date = Area::get(['Id' => $value]);
		return $date['Name'];
	}

	public function getCityAttr($value){
		$date = Area::get(['Id' => $value]);
		return $date['Name'];
	}

	public function getDistrictAttr($value){
		$date = Area::get(['Id' => $value]);
		return $date['Name'];
	}
}