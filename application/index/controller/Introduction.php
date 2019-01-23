<?php
namespace app\index\controller;
/**
 * @Author: 小小
 * @Date:   2018-12-21 14:20:16
 * @Last Modified by:   小小
 * @Last Modified time: 2019-01-23 10:35:12
 * @PDF_set_info_title()   这个是商品详情类
 */
use think\Session;
use think\Db;
use think\Cookie;
use think\Loader;
use app\index\common\Base;
use app\index\model\Goods;


//商城详情页.class.php
class Introduction extends Base
{
    public function _initialize(){
            $user_id = Session::get('user_id');
            if(isset($user_id)){
                $data = Db::table('shop_user')->where('user_id',$user_id)->find();
                $lever = Db::table('shop_user_lever')->where('lever_id',$data['user_lever'])->find();
                $this->assign([
                    'data' => $data,
                    'user' => empty($data['user_nick'])?$data['user_moblie']:$data['user_nick'],
                    'lever' => $lever["lever_name"],
                    ]);
            }
            $this->assign([
                'dataCat' => json_decode(Cookie::get('dataCat'),true),
            ]);
    }


    public function index($id = null)
    {
    	header("Content-type: text/html; charset=utf-8");
    	$data = Goods::goodsList($id);
    	if ($data['code'] == 0) {
    		dump($data['msg']);die;
    	}
        $this->linkbutton();
		$this->assign([
				'item' => json_decode($data['data'],true),
		]);
		return $this->fetch();
    }

    //demo
    public function demo()
    {
        return $this->fetch();
    }

    public function _empty(){
    	return "该控制器不存在！";
    }

}
