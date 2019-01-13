<?php
namespace app\index\controller;

use app\index\common\Base;
use app\index\validate\Register;
use app\index\sms\SendSMS;
use app\index\sms\SMS_dd;
use app\index\model\Shop_user;
use app\index\model\Goods_cate;
use app\index\model\Goods;
use think\viewport;
use think\Request;
use think\Session;
use think\Cookie;
use think\Cache;
use think\Db;


//前台商品显示 .class.php

class Index extends Base
{
    public function _initialize(){
        $this->assign([
            'dataCat' => json_decode(Cookie::get('dataCat'),true),
        ]);
    }

	//显示index
    public function index()
    {
        $redis = $this->redisConfig();
        $i = array();
        $buttom_order = array();

        $user_id = Session::get('user_id');
        $user = Session::get('user');
        $bannerimg = Db::table('shop_bannerimg')->where("status",0)->field("url,link,color,target")->order("weight desc")->select();
        /************************* 数据测试区 *******************************************/  

         $redis->delete('goodsItem');

        /************************* 数据测试区 *******************************************/  


        /************************* 缓存区 *******************************************/  
        //  商品类别表
        if (json_decode($redis->get("goods_cate"),true)) {
            $goods_cate = json_decode($redis->get("goods_cate"),true);
        }else{
             $goods_cate = json_decode(Goods_cate::goos_list__handel(Goods_cate::select()),true);
            $redis->set("goods_cate",json_encode($goods_cate),rand(3000, 3600));
         }
         //  首页商品数据
        if (json_decode($redis->get("goodsItem"),true)) {
            $goodsItem = json_decode($redis->get("goodsItem"),true);
        }else{
            $goodsItem = json_decode(Goods::goodsItem(),true);
            $redis->set("goodsItem",Goods::goodsItem(),rand(3000, 3600));
         }

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
         // $redis->delete('spmailgg');
         //  商城公告数据表
        if (json_decode($redis->get("spmailgg"),true)) {
            $spmailgg = json_decode($redis->get("spmailgg"),true);
        }else{
            $spmailgg = Db('shop_articlelist')
                    ->where('shop_articlelist.ac_id','=',1)
                    ->where('shop_articlelist.al_static','=',1)
                    ->field('Id,ac_id,al_title')
                    ->select();
            $redis->set("spmailgg",json_encode($spmailgg),rand(3000, 3600));
         }
        /************************* 结束 缓存区 *******************************************/


        /********************* * 交互区 * ***********************************/

     foreach ($linka as $key => $value) {
           $i[$key] = $value['ac_title'];
        }
        $link_one = explode("_m_", implode(array_unique($i), "_m_"));

        if(isset($user)){
            $data = Db::table('shop_user')->where('user_id',$user_id)->select();
            $lever = Db::table('shop_user_lever')->where('lever_id',$data[0]['user_lever'])->select();
            $this->assign([
                'data' => $data[0],
                'lever' => $lever[0]["lever_name"],
                ]);
        }

        /********************* * 交互区 * ***********************************/
          


        /**
         * user 用户信息
         * linka 链接
         * link_one 导航
         * bannerimg 前台banner
         * spmailgg 商品公告
         * goods_cate 商品类别表
         * goodsItem  前台数据区
         */
        $this->assign([
            'user'=> $user,
            'linka' => $linka,
            'link_one' => $link_one,
            'spmailgg' => $spmailgg,
            'link' => $link,
            'goodsItem' => $goodsItem,
            'bannerimg' => $bannerimg,
            'goods_cate' => $goods_cate
        ]);
        return $this->fetch('index');
    }


    // 显示 login 
    public function login()
    {
        return $this->fetch();
    }

    // 显示 register 
    public function register()
    {
        $sign = Db::table('shop_sms_scene')->where('id',1)->field('static')->find();
        $this->assign('sign',$sign["static"]);
        return $this->fetch();
    }
    

    //验证登录
    public function login_register(Request $request)
    {
        $obj = $request->post('obj/a');
        if ( $obj['login_yzm'] != '123456') { // !captcha_check($obj['login_yzm']) &&
        	$data['static'] = 0;
        	$data['message'] = "验证码错误";
       		return json_encode($data);
       		break;
        }else{
        	if ($obj['setter'] == 0) {   //  会员名登录
      			$map = ['user_nick'=>$obj['login_user']];
        		$datt = Shop_user::get($map);
	       	}else if ($obj['setter'] == 1) {   // 手机号登录
	       		$map = ['user_mobile'=>$obj['login_user']];
        		$datt = Shop_user::get($map);
	       	}else if ($obj['setter'] == 2) {    //邮箱登录
	       		$map = ['user_email'=>$obj['login_user']];
        		$datt = Shop_user::get($map);
	       	}
	        if (!is_null($datt)) {
        	   $pw = Db::table('shop_salt')->where('user_id',$datt->user_id)->field('salt_pw')->find();
	       	}
	       	if (is_null($datt)) {
	       			$data['static'] = 0;
        			$data['message'] = "账号或密码错误！！";
	       		}else if($datt->user_is_lock == 0){
	       			$data['static'] = 0;
        			$data['message'] = "账号被锁定，请联系管理员解封";
	       		}else if($datt->user_password == MD5(MD5($obj['login_password']).$pw['salt_pw'])){
	       			$data['static'] = 1;
        			$data['message'] = "登录成功。。";
                    $user_log_insert = ['user_id' => $datt->user_id,'user_log_time' => time(), 'user_log_ip' => $request->ip(), 'user_log_list_address' => $_SERVER["HTTP_REFERER"]];
                    Db::table('shop_user_log')->insert($user_log_insert);
        			/* - - 登录成功  保存用户session - -   */
        			Session::set('user_id',$datt->user_id);
                    Session::set('user',$obj['login_user']);
        			Session::set('user_last_time',$datt->user_last_time);
        			Session::set('user_last_ip',$datt->user_last_ip);
        			/* - - 登录成功  保存用户session - -   */
        			Shop_user::where('user_nick',$obj['login_user'])->setInc('user_is_num');    //自增登录次数
	       		}else{
	       			$data['static'] = 0;
        			$data['message'] = "账号或密码错误！！";
	       		}
        }
        return json_encode($data);
    }

}
