<?php
namespace app\user\controller;

use think\Cache;
use think\Session;
use think\Cookie;
use think\Db;
use app\user\common\Base;
use app\user\model\User;
use app\user\model\Order;

use app\common\redis\RedisLock;

class Index extends Base
{
	public function _initialize()
	{
		if (empty(Session::get('user_id'))) {
			$this->redirect('index/index/login');
		}
        parent::_initialize();
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
        
    	$userData = User::get(Session::get('user_id'))
    					->toArray();
        $this->assign([
            'linka' => $linka,
            'link_one' => $link_one,
            'link' => $link,
            'userData' => $userData,
            'dataCat' => json_decode(cookie::get('dataCat'),true),
        ]);
	}

    public function index()
    {
    	/**
    	 * [$userData 用户信息]
    	 * @var [type]
    	 */
    	$userData = User::get(Session::get('user_id'))
    					->toArray();
    	/**
    	 *  订单信息
    	 *  知道最近两张
    	 */
    	$userOrder = Order::getGoodsByID(Session::get('user_id'));
    	$OrderList = [];
    	foreach ($userOrder as $key => $value) {
    		$OrderList[$key] = $value->toArray();
    		$b = $value['items']->toArray();
    		$OrderList[$key]['items'] = $b[0];
    	}

    	/**
    	 *  足迹表
    	 */
    	
    	$footprint = Db('shop_footprint')
    					->alias('f')
    					->join('shop_goods g','g.goods_id = f.shop_id')
    					->field('g.goods_id,g.original_img,shop_price')
    					->where('f.user_id',Session::get('user_id'))
    					->order('f.footprint_id desc')
    					->select();

    	/**
    	 *  收藏表
    	 */
    	
    	$collection = Db('shop_collection')
    					->alias('c')
    					->join('shop_goods g','g.goods_id = c.shop_id')
    					->field('g.goods_id,g.original_img,shop_price')
    					->where('c.user_id',Session::get('user_id'))
    					->order('c.collection_id desc')
    					->select();
    	/**
    	 *  收藏表
    	 */


    	$this->assign([
    			'userData' => $userData,
    			'orderList' => $OrderList,
    			'footprint' => $footprint,
    			'collection' => $collection,
    		]);
    	// dd($userData);
        return $this->fetch();
    }

    /**
     * [information 个人资料]
     * @return [type] [description]
     */
    public function information(){
    	return $this->fetch();
    }

    /**
     * [information 个人消息]
     * @return [type] [description]
     */
    public function safety(){
    	return $this->fetch();
    }

    /**
     * [information 地址管理]
     * @return [type] [description]
     */
    public function address(){
    	return $this->fetch();
    }

    /**
     * [information 快捷支付]
     * @return [type] [description]
     */
    public function cardlist(){
    	return $this->fetch();
    }

    /**
     * [information 登录密码]
     * @return [type] [description]
     */
    public function password(){
    	return $this->fetch();
    }


    /**
     * [information 登录密码]
     * @return [type] [description]
     */
    public function setpay(){
    	return $this->fetch();
    }

    /**
     * [information 手机验证]
     * @return [type] [description]
     */
    public function bindphone(){
    	return $this->fetch();
    }

    /**
     * [information 邮箱验证]
     * @return [type] [description]
     */
    public function email(){
    	return $this->fetch();
    }
    /**
     * [information 实名认证]
     * @return [type] [description]
     */
    public function idcard(){
    	return $this->fetch();
    }

    public function lock(){
        $redislock = new RedisLock();
        $is_lock = $redislock->lock('goodslock',30,0);
        if ($is_lock) {
            echo "获取锁成功";
            sleep(1);
            // 删除锁
            $redislock->unlock('goodslock');
        }
        else{
            echo "获取锁失败";
            // $redislock->unlock('goodslock');
        }
    }

}
