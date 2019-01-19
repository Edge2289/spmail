<?php
namespace app\admin\model;

use think\Model;
use app\admin\model\Area;

/**
 * @Author: 小小
 * @Date:   2019-01-19 14:05:58
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-19 16:55:36
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