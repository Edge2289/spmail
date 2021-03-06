<?php
namespace app\index\controller;

use think\Db;
use think\Loader;
use think\Session;
use think\Cookie;
use think\Cache;
use think\Request;
use app\index\model\Goods;
use app\index\common\Base;

class Order extends Base
{
	public function _initialize(){
        $redis = $this->redisConfig();
		//去友情链接
        if (Cache::get('link')) {
            $link = Cache::get('link');
        }else{
            $link = Db::table('shop_link')->select();
            Cache::set('link',$link,rand(3000, 3600));
        }
        //  链接表
        if (json_decode($redis->get("linka"),true)) {
            $linka = json_decode($redis->get("linka"),true);
        }else{
            $linka = Db::view('shop_artcleclass','ac_id,ac_title')
                    ->view('shop_articlelist','al_title','shop_artcleclass.ac_id=shop_articlelist.ac_id')
                    ->where('shop_artcleclass.is_sys','=',1)
                    ->where('shop_artcleclass.index_static','=',1)
                    ->order('ac_order')
                    ->select();
            $redis->set("linka",json_encode($linka),rand(3000, 3600));
         }
         foreach ($linka as $key => $value) {
           $i[$key] = $value['ac_title'];
        }
        $link_one = explode("_m_", implode(array_unique($i), "_m_"));
        $this->assign([
            'linka' => $linka,
            'link_one' => $link_one,
            'link' => $link,
            'dataCat' => json_decode(Cookie::get('dataCat'),true),
        ]);
    }

	public function sureOrder(){
		$data = json_decode(base64_decode(input('post.data')),true);
		$orderLisr = [];
		foreach ($data as $key => $value) {
			if ($key == 'goodslist') {
				continue;
			}
			$orderLisr[$key] = $value;
		}
		// 判断其他信息
		$goodsOrQt = Loader::validate('OrderValidate');
			if (!$goodsOrQt->scene('qita')->check($orderLisr)) {
				$data['status'] = 0;
	            $data['msg'] = $goodsOrQt->getError();
	            $data['data'] = '';
	            return json_encode($data);
			}
		// 判断商品是否可用之类
		foreach ($data["goodslist"] as $k => $v) {
			/** @var [type] [先判断商品是否可用] */
			$goodsStatus = json_decode(Goods::OrderStatus($v),true);
			if (empty($goodsStatus['status'])) {
				return json_encode($goodsStatus);
				die;
			}
			$orderLisr['goodslist'][$k] = $goodsStatus;
			$goodsOrVa = Loader::validate('OrderValidate');
			if (!$goodsOrVa->scene('goods')->check($v)) {
				$data['status'] = 0;
	            $data['msg'] = $goodsOrVa->getError();
	            $data['data'] = '';
	            return json_encode($data);
			}
		}
		/***************   下订单  *****************/

		foreach ($orderLisr["goodslist"] as $ok => $ov) {
			$orderSql = [
					'order_ddh' => 'xxsc'.date("Ymd").$this->make_shuzi(), // 订单号
					'order_uid' => Session::get('user_id'), // 用户id
					'order_sid' => $ov['data']['goodsid'], // 商品id
					'order_num' => $ov['data']['goodsnum'], // 购买数量
					'order_price' => (int)$ov['data']['goodsnum']*(int)$ov['data']['price'], // 付款金额
					'order_address' => $orderLisr["address"], // 收货人
					'order_time' => time(), // 下单时间
					'order_status' => 1, // 状态
					'order_pay' => 0, // 支付方式
					'order_liuyan' => $orderLisr["liuyan"], // 留言
					'order_item' => $ov['data']['item_id'], //
					'order_itemname' => $ov['data']['item_name'], //
				];
			Db::startTrans();
			try {
				$i = Db::table('shop_order')
						->insertGetid($orderSql);
				$priceId = Goods::reitemId($orderSql['order_sid'],$orderSql["order_item"]);
				$b = Db::table('shop_goods_price')
						->where('price_id',$priceId)
						->setDec('store_count',$orderSql['order_num']);
				$c = Db::table('shop_goods_price')
						->where('price_id',$priceId)
						->field('store_count')
						->find();
				if (!$i && $b && $c >= 0) {
					throw new Exception('提交订单失败');
				}
				// 提交事务
				Db::commit();
			} catch (Exception $e) {
				Db::rollback();
				return json_encode([
							'status' => 0,
							'msg'	 => $e->getMessage(),
							'data'   => ''
						]);
				die;
			}
		}
		return json_encode([
			'status' => 1,
			'msg'	 => "success",
			'data'   => [
						'order_id' => $orderSql['order_ddh'],
						'order_price' => $orderSql['order_price'],
						'order_pay' => $orderSql['order_pay'],
						'order_time' => date("Y-m-d H:i:s",($orderSql['order_time']+7200)),
						'address' => $orderSql['order_address'],
					],
			]);

	}

	public function orderPay(Request $request){
		$order = json_decode(base64_decode(input('get.order')),true);
		if (empty($order)) {
			dd("_empty");
		}
        $pay = Db::table('shop_pay')->where('static',0)->field('id,pay_name,pay_img')->select();
		$order['address'] = Db::table('shop_user_address')
				                        ->alias('a')
				                        ->join('shop_area b','a.country = b.Id')
				                        ->join('shop_area c','a.province = c.Id')
				                        ->join('shop_area d','a.city = d.Id')
				                        ->join('shop_area e','a.district = e.Id')
				                        ->where('a.user_id',Session::get('user_id'))
				                        ->where('address_id',$order['address'])
				                        ->field('a.address_id,a.consignee as name,a.address,a.mobile,a.is_default,b.Name as country,c.Name as province,d.Name as city,e.Name as district')
				                        ->find();
		// 
		$this->assign([
				'data' => $order,
				'pay' => $pay,
				'order' => input('get.order')
			]);
		// dd($order);
		return $this->fetch();
	}

	public function _empty(){
		dd('_empty');
	}
}