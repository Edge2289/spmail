<?php
namespace app\admin\model;

use think\Model;
/**
 * @Author: 小小
 * @Date:   2019-01-22 15:17:00
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-22 16:41:48
 */

class GoodsPrice extends Model
{
	protected $table = "shop_goods_price";

	/**
	 * [addCK 添加库存]
	 * @param [type] $item [规格项]
	 */
	public static function addKC($gid ,$item ,$kcSum){
		$data = self::where('goods_id',$gid)->select();
		$items = explode('_', $item); 
		$priceId = 0;
		foreach ($data->toArray() as $key => $value) {
			if (!array_diff(explode('_', $value['item_id']),$items)) {
				$priceId = $value['price_id'];
				break;
			}
		}
		if ($priceId == 0 || empty($priceId)) {
			return false;
		}
		try{
			$priceSelf = self::get($priceId);
			$priceSelf->store_count = (int)($priceSelf->store_count+(int)$kcSum);
			$priceSelf->save();
			return true;
		}catch(\Excption $e){
			return false;
		}
	}
}