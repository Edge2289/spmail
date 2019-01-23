<?php
namespace app\user\model;

use think\Model;
use think\Session;
/**
 * @Author: 小小
 * @Date:   2019-01-23 10:54:11
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-23 16:36:54
 */

class Cat extends Model
{
	protected $table = "shop_cat";

	public static function addData($data){
		$Cat = self::get(['goods_id'=>$data['goods_id'],'user_id'=>Session::get('user_id')]);
		// 检测是否数据库有这个规格的表
		if (!empty($Cat)) {
			$CatArray = $Cat->toArray();
			// 检测是否数据库有这个规格的表
			if (!array_diff(json_decode($CatArray["catSpeclist"],true), $data['catSpeclist'])) {
				// 自增库存
				$num = (int)$Cat->num+(int)$data['num'];
				if ($num > $Cat->store_count) {
					return [
						'core' => '0',
						'msg'  => '库存不足'
					];
				}
				$Cat->num = $num;
				$Cat->save();
				return [
					'core' => '1',
					'msg'  => '添加成功'
				];
			}
		}
			// $list['user_id'] = Session::get('user_id');
			// $list['cart_time'] = time();
			// $list['cart_type'] = json_encode($data["catSpeclist"]);
			// $list['catSpeclist'] = $data["catSpeclist"];
			// $list['num'] = $data["num"];
			// $list['goods_id'] = $data["goods_id"];
			// $list['goods_name'] = $data["goods_name"];
			// $list['goods_price'] = $data["goods_price"];
			// $list['itemSpeclist'] = json_encode($data["itemSpeclist"]);
			// $list['specListText'] = json_encode($data["specListText"]);
			// $list['store_count'] = $data["store_count"];
			// $list['original_img'] = $data["original_img"];
			

			$catList = new Cat();
			$catList->data([
					'user_id' => Session::get('user_id'),
					'cat_time' => time(),
					'cat_type' => 0,
					'catSpeclist' => json_encode($data["catSpeclist"]),
					'num' => $data["num"],
					'goods_id' => $data["goods_id"],
					'goods_name' => $data["goods_name"],
					'goods_price' => $data["goods_price"],
					'itemSpeclist' => json_encode($data["itemSpeclist"]),
					'specListText' => json_encode($data["specListText"]),
					'store_count' => $data["store_count"],
					'original_img' => $data["original_img"]
				]);
		// dd($catList);
			$catList->save();
			if (empty($catList->cat_id)) {
				return [
					'core' => '0',
					'msg'  => '添加失败'
				];
			}
		return [
			'core' => '1',
			'msg'  => '添加成功'
		];
	}
}