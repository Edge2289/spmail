<?php
namespace app\index\model;

use think\Model;
use think\Db;
use think\Session;
use app\index\model\Goods_cate;
/**
 * @Author: 小小
 * @Date:   2018-12-20 14:21:21
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-23 17:45:58
 */

class Goods extends Model
{
	protected $table = 'shop_goods';

	// 商品详情数据
	public static function goodsList($id){
		header('Content-Type: text/html; charset=utf-8'); //网页编码
		$data = [];

		/////////////////////////////////////////// 属性///////////////////////////////////////////////////////////
		$data['attr'] = Db('shop_attr_goods')
				->alias('a')
				->join('shop_goods_attribute b','a.attr_id = b.attribute_id')
				->where('a.goods_id',$id)
				->field('b.attribute_name,a.sap_text')
				->select();
		/////////////////////////////////////////// 规格///////////////////////////////////////////////////////////
		$goodsSpecList = Db::table('shop_goods_speclist')->where('goods_id',$id)->field('gs_text,gs_name')->select();
		$goodsSpec = Db::table('shop_speclist')->field('speclist_name,speclist_id')->select();	

		foreach ($goodsSpec as $key => $value) {
			$specList[$value["speclist_id"]] = $value["speclist_name"];
		}
		$i = 0;
		foreach ($goodsSpecList as $value) {
			$b = explode("_",$value["gs_name"]);
			$spec[$i]['spec_id'] = $b[0];
			$spec[$i]['spec'] = $specList[$b[0]];
			$spec[$i]['gs_name'] = $value["gs_name"];
			$spec[$i]['gs_text'] = $value["gs_text"];
			$i++;
		}
		if (!empty($spec)) {
			
			foreach ($spec as $key => $value) {
				$specList1[$value["spec_id"]]["spec_id"] = $value["spec_id"];
				$specList1[$value["spec_id"]]["spec_name"] = $value["spec"];
				$bm["gs_name"] = $value["gs_name"];
				$bm["gs_text"] = $value["gs_text"];
				$specList1[$value["spec_id"]]["spec_list"][] = $bm;
			}
		}else{
			$specList1 = null;
		}
		$data['spec'] = $specList1;
		$data['price'] = Db::table('shop_goods_price')
							->where('goods_id',$id)
							->field('item_id,store_count,price,price_img')
							->select();
		//////////////////////////////////////////// 城市//////////////////////////////////////////////////////////
		$data['city'] = Db::table("shop_area")					
					->field("id,Name,ParentId")
					->where('id','<>',0)
					->where('ParentId','<>',0)
					->select();
		//////////////////////////////////////////// 图片//////////////////////////////////////////////////////////
		$data['img'] = Db::table("shop_goods_img")					
						->where('goods_id',$id)
						->field('img_src')
						->select();
		$goods = Goods::get(['is_on_sale'=>1,'goods_id'=>$id]);
		if (empty($goods)) {
			return [
				'code' => '0',
				'msg'  => '商品不存在或者已经下架！',
				'data' => null,
			];
		}
		//////////////////////////////////////////// 图片//////////////////////////////////////////////////////////
		$data['goods'] = $goods->toArray();
		//////////////////////////////////////////// 图片//////////////////////////////////////////////////////////
		return [
				'code' => '1',
				'msg'  => '',
				'data' => json_encode($data),
			];
	}

	// 前台index 的数据
	public static function goodsItem(){
		header('Content-Type: text/html; charset=utf-8'); //网页编码
		// 获取导航的列表
		$catItem = Goods_cate::where(['cate_static'=>0,'parent_id'=>0])
					->field("cate_name,parent_id_path,cate_is_hot")
					->order(['cate_order desc'])
					->limit("0,5")
					->select()
					->toArray();
		$i = 0;
		foreach ($catItem as $key => $value) {
			$dataItme[$i]['catItemNav'] = Goods_cate::where('parent_id_path','like',$value["parent_id_path"].'%')
											->where('parent_id_path','<>',$value["parent_id_path"])
											->where('cate_static','0')
											->field('cate_id,cate_name')
											->order('cate_order desc')
											->limit("0,5")
											->select()
											->toArray();
			$dataItme[$i]['cate_name'] = $value["cate_name"];
			$dataItme[$i]['goods'] = Goods::where('cat_id','like',$value["parent_id_path"].'%')
										->where('is_on_sale',1)
										->field('goods_id,goods_sn,goods_name,shop_price,is_new,is_hot,original_img')
										->order('sort desc')
										->limit("0,6")
										->select()
										->toArray();
			$i++;
		}
		return json_encode($dataItme);
	}

	public static function goodsPay($data){
		// 单个操作
				$item_id = array();
				$item_id2 = '';
			for ($y=0; $y < count($data['type']); $y++) { 
				$name = explode("_", $data['type'][$y]);
				$item_id[$y] = $name[1];
				$item_id2 .= $data['type'][$y].',';
			}
			$itemList = Db::table('shop_goods_price')
									->where('goods_id',$data["id"])
									->field('item_name,item_id,price')
									->select();
			foreach ($itemList as $key => $value) {
				if (empty(array_diff(explode('_',$value['item_id']),$item_id))) {
					$list['item']['price'] = $value['price'];
					$list['item']['item_name'] = $value['item_name'];
				}
			}
			$list['item']['itemSpeclist'] = substr($item_id2,0,-1);
			$list['goods'] = self::get($data['id'])->visible(['goods_id','is_free_shipping','goods_sn','original_img','goods_name','market_price','shop_price'])->toArray();
			$list['goods']['num'] = $data['num'];
		return json_encode($list);
	}

	public static function goodsListPay($data){
		// 遍历操作
		for ($i=0; $i < count($data); $i++) { 
				$item_id = array();
			for ($y=0; $y < count($data[$i]['type']); $y++) { 
				$name = explode("_", $data[$i]['type'][$y]);
				$item_id[$y] = $name[1];
			}
			$itemList = Db::table('shop_goods_price')
									->where('goods_id',$data[$i]["id"])
									->field('item_name,item_id,price')
									->select();
			foreach ($itemList as $key => $value) {
				if (empty(array_diff(explode('_',$value['item_id']),$item_id))) {
					$list[$i]['item']['price'] = $value['price'];
					$list[$i]['item']['item_name'] = $value['item_name'];
				}
			}
			$list[$i]['item']['itemSpeclist'] = $data[$i]['type1'];
			$list[$i]['goods'] = self::get($data[$i]['id'])
									->visible(['goods_id','is_free_shipping','goods_sn','goods_name','original_img','market_price','shop_price'])
									->toArray();
			$list[$i]['goods']['num'] = $data[$i]['num'];
		}
		return json_encode($list);
	}

	/**
	 * [OrderStatus 订单验证商品是否可用之类]
	 * @param [type] $goodsid   [商品id]
	 * @param [type] $goodsnum  [商品数量]
	 * @param [type] $goodsitem [商品规格]
	 */
	public static function OrderStatus($data){
		$reData = [];
		$reList = [];
		$selfGoods = self::get($data['goodsid']);
		if(!empty($selfGoods) && !empty($selfGoods['is_on_sale'])){
			// 获取规格属性，遍历判断
			$dataArr = Db::table('shop_goods_price')
									->where('goods_id',$data["goodsid"])
									->field('item_name,item_id,price,store_count,price')
									->select();
			// 解析前端过来的数据做数组对比
			$dataItem = explode(',', $data['itemId']);
			$item = array();
			if (empty($dataItem)) {
					foreach ($dataItem as $key => $value) {
						$v1 = explode('_', $value);
						$item[$key] = $v1[1];
					}
					// 对比数组
					foreach ($dataArr as $key => $value) {
						if (empty(array_diff(explode('_',$value['item_id']),$item))) {
							if ($value['store_count'] >= $data['goodsNum']) {
								$reList['price'] = $value['price'];
								$reList['item_name'] = $value['item_name'];
								$reList['item_id'] = $value['item_id'];
								$reList['goodsid'] = $data['goodsid'];
								$reList['goodsnum'] = $data['goodsNum'];
								$reData = [
									'status' => 1,
									'msg'    => "",
									'data'   => $reList,
								];
							}else{
								// 库存不足
								$reData = [
									'status' => 0,
									'msg'    => "商品库存不足",
									'data'   => $reList,
								];
							}
						}
					}
			}else{
				$goodsList = self::get(['goods_id'=>$data['goodsid']]);
				$goodsList = $goodsList->toArray();
				$reList['price'] = $goodsList['shop_price'];
				$reList['item_name'] = '';
				$reList['item_id'] = '';
				$reList['goodsid'] = $data['goodsid'];
				$reList['goodsnum'] = $data['goodsNum'];
				$reData = [
						'status' => 1,
						'msg'    => "",
						'data'   => $reList,
					];
			}
		}else{
			$reData = [
					'status' => 0,
					'msg'    => "商品不存在,或以下架",
					'data'   => $reList,
				];
		}
		return json_encode($reData);	
	}

	public static function reitemId($goodsid,$item_id){
		$item_arr = explode('_', $item_id);
		$itemList = Db::table('shop_goods_price')
									->where('goods_id',$goodsid)
									->field('price_id,item_name,item_id')
									->select();
			foreach ($itemList as $key => $value) {
				if (empty(array_diff(explode('_',$value['item_id']),$item_arr))) {
					return $value['price_id'];
				}
			}
	}


}
