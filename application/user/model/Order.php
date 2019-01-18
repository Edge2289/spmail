<?php
namespace app\user\model;

use think\Model;
use think\Session;
use app\user\model\Goods;
/**
 * @Author: 小小
 * @Date:   2019-01-18 14:29:59
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-18 16:05:34
 */
class Order extends Model
{
	protected $table = "shop_order";

	public function items(){
		// 一对一关联
		return $this->hasMany('Goods','goods_id','order_sid')
					->field('goods_id,goods_sn,goods_name,original_img');
	} 

	public static function getGoodsByID($order_sid)	
    {
    	// dd($order_sid);
        $goods = self::with('items')
        				->where("order_uid",$order_sid)
						->field('order_price,order_time,order_status,order_num')
        				->order('order_gid desc')
        				->limit(2)->select(); // 通过 with 使用关联模型，参数为关联关系的方法名
        return $goods;
    }
}