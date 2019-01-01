<?php
namespace app\admin\controller;

use app\admin\common\Base;
use app\common\redis\RedisClice;
use app\admin\model\JxMiun;
use think\Db;
use think\Config;
use think\Request;

/**
* 
*/
class User extends Base
{
	// 删除user
	public function deleteRedis(){
		$redis = $this->redisConfig();
		$redis->delete('hsah','userlist');
		$redis->delete('userlistAdd');
		$info = "清除失败 :(";
		if ($redis->hLan('hsah','userlist') == 0 && $redis->hLan('userlistAdd') == 0) {
		$info = "清除成功 :)";
		}
		return $info;
	}

	// 用户管理
	public function index(){
		$curr = empty(input('get.curr'))?1:input('get.curr');
		$limit = empty(input('get.limit'))?10:input('get.limit');
		$search = empty(input('get.search'))?'':input('get.search');
		$searchSelect = empty(input('get.searchSelect'))?'weikong':input('get.searchSelect');

		$redis = $this->redisConfig();

		$user = Db('shop_user')->select();

		if ($redis->hGetJson('hsah','userlist')) {
			$userlist = json_decode($redis->hGetJson('hsah','userlist'),true);
		}else{
			$userlist = Db::view('shop_user','user_id,user_nick,user_mobile,user_email,user_money,user_pay_points,user_total_amount,user_reg_time,user_is_lock')
					->view('shop_user_lever','lever_name','shop_user.user_lever = shop_user_lever.lever_grade')
					->order('user_id','desc')
					->select();
			$redis->hSetJson('hsah','userlist',json_encode($userlist),'86400');
			foreach ($userlist as $value) {
					$redis->hSetJson('userlistAdd', $value['user_id'],json_encode($value),'86400');
			}
	}

	$bin = 0;
	$ban = 0;
	$min = array();
	$max = array();
			if($searchSelect != "weikong") {
				for ($i= 0; $i < count($userlist); $i++) { 
						if ($userlist[$i][$searchSelect] == $search) {
						$max[$ban] = $userlist[$i];
						$ban++; 
					}
				}
				$userlist = $max;
			}
			for ($i= ($limit * ($curr-1)); $i < $limit * $curr; $i++) { 
				if (!empty($userlist[$i])) {
					$min[$bin] = $userlist[$i];
					$bin++; 
				}
			}
		$this->assign([
					'userlist' => $min,
					'datanum' => count($userlist),
					'limit' => $limit,
					'curr' => $curr,
					'search' => $search,
					'searchSelect' => $searchSelect,
			]);
		return view();
	}

	public function ajaxUserAUD(){
		$jm = new JxMiun();
		$data = $jm->userADU(json_decode(input('post.data'),true),input('post.info'));
		$this->ajaxReturn('',$data['info'],$data['static']);
	}

	public function SendEmail(){
		$arr = array(input('get.e'),input('get.t'),input('get.c'));
		$data = JxMiun::SendEmailM($arr);
		$this->ajaxReturn('1',$data['message'],$data['state']);
	}

	// 添加编辑用户
	public function UserAdd(){


		switch (input('get.name')) {
			case 'add':   //新增
				# code...
				return view('userAdd');
			
			case 'update':  // 更新
				# code...
				$lever = Db('shop_user_lever')->field('lever_grade,lever_name')->select();
				$data = Db('shop_user')->where('user_id',input('get.id'))->find();
				// var_dump($data);
				$this->assign([
							'lever' => $lever,
							'data' => $data,
						]);
				return view('userUpdate');


			case 'getznx':  // 站内信
				# code...
				$GetId = '';
				$text = '';
				$index = '';
				$userArr = explode(',', input('get.id'));
				$redis = $this->redisConfig();
				$user = $redis->hGet('userlistAdd'); // userlistAdd
				foreach ($userArr as $key) {
					$index = json_decode(json_decode($user[$key],true),true);
					$text = $text.'  '.$index['user_id'].'   '.$index['user_nick'].'&#10;';
					$GetId = $GetId.$index['user_id'].'-';
				}

				$this->assign([
							'text' => $text,
							'GetId' => $GetId,
					]);
				return view('userZNX');


			case 'getemail':   //邮箱
				# code...
				return view('useremaile');

			case 'capital':
				// # code...
				$capital = Db('shop_account_log')->where('user_id',input('get.id'))->field('user_money,frozen_money,pay_points,desc,change_time,log_id')->order('log_id desc')->select();
				$this->assign([
						'id' => input('get.id'),
						'capital' => $capital,
					]);
				return view('capital');
				break;

			case 'address':
				# code...
				$data = Db::query('SELECT a.consignee,a.address,a.mobile,a.zipcode,a.consignee,a.address,a.address_id,
			b.`Name` as country_1,
			c.`Name` as province_1,
			d.`Name` as city_1,
			e.`Name` as district_1,
			f.`Name` as twon_1,
			g.`user_nick` as user_name
			from shop_user_address a 
						inner join shop_area b on a.country = b.Id 
						inner join shop_area c on a.province = c.Id
						inner join shop_area d on a.city = d.Id
						inner join shop_area e on a.district = e.Id
						inner join shop_area f on a.twon = f.Id
						inner join shop_user g on a.user_id = g.user_id
						where a.user_id ='.input('get.id'));
		$this->assign('data',$data);
				return view('address');
				break;

			case 'zjtj':
				# code...
				$capital = Db('shop_user')->where('user_id',input('get.id'))->field('user_money,user_frozen_money,user_pay_points')->find();
				$this->assign([
						'id' => input('get.id'),
						'capital' => $capital,
					]);
				return view('capital_edit');
				break;
		}
	}
	
	// 导出列表数据 
	 public function export_user(){

	 	$text = input('get.text');
	 	$type = input('get.type');
	 	if ($type != "weikong") {
	 		$condition[$type] = $text;
	 	}
	 	$condition['user_id'] = ['>','0'];
    	$strTable ='<table width="500" border="1">';
    	$strTable .= '<tr>';
    	$strTable .= '<td style="text-align:center;font-size:12px;width:120px;">会员ID</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="100">会员昵称</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员等级</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">手机号</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">邮箱</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">注册时间</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">最后登陆</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">余额</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">积分</td>';
    	$strTable .= '<td style="text-align:center;font-size:12px;" width="*">累计消费</td>';
    	$strTable .= '</tr>';
        $count = DB::name('shop_user')->where($condition)->count();
    	$p = ceil($count/5000);
    	for($i=0;$i<$p;$i++){
    		$start = $i*5000;
    		$end = ($i+1)*5000;
    		$userList = Db('shop_user')->where($condition)->order('user_id')->limit($start.','.$end)->select();
    		if(is_array($userList)){
    			foreach($userList as $k=>$val){
    				$strTable .= '<tr>';
    				$strTable .= '<td style="text-align:center;font-size:12px;">'.$val['user_id'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_nick'].' </td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_lever'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_mobile'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_email'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i',$val['user_reg_time']).'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i',$val['user_last_time']).'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_money'].'</td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_pay_points'].' </td>';
    				$strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_total_amount'].' </td>';
    				$strTable .= '</tr>';
    			}
    			unset($userList);
    		}
    	}

    	$strTable .='</table>';
    	JxMiun::downloadExcel($strTable,'users_'.$i);
    	exit();
    }


	// 等级列表
	public function levelList(){
		$lelist = Db('shop_user_lever')->order('lever_grade asc')->select();
		$this->assign('list',$lelist);
		return view('levelList');
	}

	public function levelList_edit()
	{
		$lever = null; // 定义一个
		if(input('get.name') == "edit"){
			$lever = Db('shop_user_lever')->where('lever_grade',input('get.id'))->find();
		}
		$this->assign('lever',$lever);
		$this->assign('name',input('get.name'));
		return view('levelList_edit');
	}

	public function AjaxLeverLi(){
		$i = new JxMiun();
		$d = $i->UserLeverEdit(json_decode(input('post.data'),true),input('post.info'));
		$this->ajaxReturn('',$d['info'],$d['static']);
	}

	public function AjaxLeverLiDe(){
		$i = Db::query('select count(*) from shop_user join shop_user_lever on shop_user.user_lever = shop_user_lever.lever_grade where shop_user_lever.lever_grade = '.input('post.id'));
		if ($i[0]['count(*)'] != 0) {
			$this->ajaxReturn('',"删除失败，用户表有此等级",'0');
		}else{
		$b = Db('shop_user_lever')->where('lever_grade',input('post.id'))->delete();
			if ($b != 0) {
				$this->ajaxReturn('','删除成功','1');
			}else{
				$this->ajaxReturn('','删除失败','0');
			}
		}

	}
	// 收货地址管理
	public function address(){
		$data = Db::query('SELECT a.consignee,a.address,a.mobile,a.zipcode,a.consignee,a.address,a.address_id,
			b.`Name` as country_1,
			c.`Name` as province_1,
			d.`Name` as city_1,
			e.`Name` as district_1,
			f.`Name` as twon_1,
			g.`user_nick` as user_name
			from shop_user_address a 
						inner join shop_area b on a.country = b.Id 
						inner join shop_area c on a.province = c.Id
						inner join shop_area d on a.city = d.Id
						inner join shop_area e on a.district = e.Id
						inner join shop_area f on a.twon = f.Id
						inner join shop_user g on a.user_id = g.user_id');
		$this->assign('data',$data);
		return view('address');
	}

	// 充值记录   只有查看，没有操作
	public function recharge(){
		$nick = empty(input('get.nick'))?'':input('get.nick');
		$limit = empty(input('get.limit'))?'10':input('get.limit'); // 页面大小
		$curr = empty(input('get.curr'))?'1':input('get.curr');  // 当前页
		$data = Db::table('shop_user_recharge')
				->alias('a')
				->join('shop_user b','a.user_id = b.user_id')
				->where('b.user_nick','like','%'.$nick.'%')
				->field('a.*,b.user_nick')
				->select();
			$this->assign([
					'data'=>$data,
					'limit'=>$limit,
					'curr'=>$curr,
					'nick'=>$nick,
					'datanum'=>count($data),
			]);
		return view('recharge');
	}

	// 提现申请
	public function withdrawals(){
		$nick = empty(input('get.nick'))?'':input('get.nick');
		$start = empty(input('get.start'))?mktime(0,0,0,date('m'),date('d'),date('Y')):strtotime(input('get.start').' 00:00:00');
		$end = empty(input('get.end'))?mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1000:strtotime(input('get.end').' 23:59:59');
		$limit = empty(input('get.limit'))?'10':input('get.limit'); // 页面大小
		$curr = empty(input('get.curr'))?'1':input('get.curr');  // 当前页
		// dump();
		$data = Db::table('shop_user_withdrawals')
				->alias('a')
				->join('shop_user b','a.user_id = b.user_id')
				->where('b.user_nick','like','%'.$nick.'%')
				->where('a.is_time','>',$start)
				->where('a.is_time','<',$end)
				->field('a.*,b.user_nick')
				->page($curr,$limit)
				->select();
			$this->assign([
					'data'=>$data,
					'limit'=>$limit,
					'curr'=>$curr,
					'nick'=>$nick,
					'datanum'=>count($data),
					'start'=>$start,
					'end'=>$end,
			]);
		return view('withdrawals');
	}

	// 提现详情
	public function withdList(){
		$id = input('get.id');
		$data = Db::table('shop_user_withdrawals')
				->alias('a')
				->join('shop_user b','a.user_id = b.user_id')
				->where('a.user_id',$id)
				->field('a.*,b.user_nick')
				->find();
			$this->assign('data',$data);
		return view('withdrawals_edit');
	}

	// 提现的修改函数
	public function withdupdate(Request $request){
		if (input('post.type') == 'shenpiyz' && empty(input('post.type'))) {
			$id = input('post.id');
			$is_shenpin = input('post.shenpi');
			$shenpin_czsm = input('post.bname_text');
			if (is_numeric($id) && is_numeric($is_shenpin)) {
				$update = ['is_shenpin'=>$is_shenpin,
							'shenpin_czsm'=>$shenpin_czsm,
							'shenpin_time'=>time(),
							'is_withdrawals'=>$is_shenpin == 1?2:3];

				$i = Db('shop_user_withdrawals')
						->where('id',$id)
						->where('is_shenpin','NEQ','1')
						->update($update);
				if ($i) {
					return ['status'=>1,'msg'=>'审批成功'];
				}else{
					return ['status'=>0,'msg'=>'提交失败'];
				}
			}else{
				return ['status'=>0,'msg'=>'参数错误'];
			}

		}else if (!empty(input('post.id'))){
			$file = request()->file('file');
			if (!is_numeric(input('post.id'))) {
	            // 上传失败获取错误信息
	            $reubfo['code']= 0;
	            $reubfo['err'] = "参数错误";
			}
			$info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'image' . DS . 'user' . DS . 'withdup/');
			$savename = "/static/image/user/withdup/".substr($info->getSaveName(), 0,8).'/'.substr($info->getSaveName(),9);
	        $reubfo = array();  //定义一个返回的数组
	        if($info){
				$i = Db('shop_user_withdrawals')
					->where('id',input('post.id'))
					->where('is_withdrawals',2)
					->update([
						'is_withdrawals'=>1,
						'zhuanzhang_img'=>$savename,
						'is_withdrawals_time'=>time(),
						]);
				if (!$i) {
		            $reubfo['code']= 0;
		            $reubfo['err'] = "提交出错！！";
				}else{
		            $reubfo['code']= 1;
		            $reubfo['err'] = "操作成功";
				}
	        }else{
	            // 上传失败获取错误信息
	            $reubfo['code']= 0;
	            $reubfo['err'] = $file->getError();
	        }
	        return $reubfo;
		}
	}

	// 汇款记录
	public function remittance(){
		$nick = empty(input('get.nick'))?'':input('get.nick');
		$limit = empty(input('get.limit'))?'10':input('get.limit'); // 页面大小
		$curr = empty(input('get.curr'))?'1':input('get.curr');  // 当前页
		$data = Db::table('shop_user_remittance')
				->alias('a')
				->join('shop_user b','a.user_id = b.user_id')
				->where('b.user_nick','like','%'.$nick.'%')
				->field('a.*,b.user_nick')
				->select();
			$this->assign([
					'data'=>$data,
					'limit'=>$limit,
					'curr'=>$curr,
					'nick'=>$nick,
					'datanum'=>count($data),
			]);
		return view('remittance');
	}

	// 会员签到
	public function signList(){
		$sign = empty(input('get.nick'))?'':input('get.nick');
		$data = Db('shop_user_sign')->where('user_nick','like','%'.$sign.'%')->select();
				$this->assign('data',$data);
				$this->assign('searchSelect',$data);
		return view('signList');
	}

	// 控制器不存在是调用
	public function _empty(){
		$html = '<div style="text-align: center;margin: 0px auto;">
		<br><br><br>
        <div style="font-size: 48px;
    line-height: 50px;
    margin-bottom: 50px;">
            o(╥﹏╥)o
        </div>
        <div style="    font-size: 64px;
    line-height: 80px;">
            404
        </div>
        <div style="    font-size: 36px;
    line-height: 72px;
    font-weight: 700;">
            页面找不到了
        </div>
        <div class="return">
            <a href="https://www.csdn.net">返回首页</a>
        </div>
    </div>';
		return $html;
	}

}