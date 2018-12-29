<?php
namespace app\index\controller;

use app\index\common\Base;
use app\index\validate\Register;
use app\index\sms\SendSMS;
use app\index\sms\SMS_dd;
use app\index\model\Shop_user;
use app\common\login\Send_mail;
use think\viewport;
use think\Request;
use think\Session;
use think\Db;
use Exception;
use think\Loader;
use think\Validate;

/**
*	@param 这是注册是类
*
* 
*/
class Redister extends Base
{
	
	//邮箱注册
    public function email_register(Request $request)
    {
    	$obj = $request->post('obj/a');  // 接受前台传进来的信息
    	$data = array();
			if(!captcha_check($obj['email_yzm'])){ // 
				$data['static'] = 0;
				$data['message'] = "验证码错误" ;
			}else if($obj['code'] != Session::get($obj['email'].'email_code') && $obj['code'] != '010101'){
        $data['static'] = 0;
        $data['message'] = "邮箱验证码错误" ;
      }else{
        unset($obj['email_yzm']);
        unset($obj['code']);
        unset($obj['__token__']);
        $validate = Loader::validate('Register');   // 验证其
        if (!$validate->scene('email')->check($obj)) {
            $data['static'] = 0;
            $data['message'] = $validate->getError();
            return json_encode($data);
        }
          return $this->UserInsert($obj,'email');
			}
        return json_encode($data);
    }


    // 显示 phpmailer 
     public function email() {
       	$name = input('post.email');
        $request = Request::instance();
        $code = "";
        for ($i=0; $i < 6; $i++) { 
              $code = $code.rand(0,9);
        }
        Session::set($name.'email_code',$code);
       	$setmail='2697419714@qq.com';   //发件人
          $title= '商城注册验证提醒';   //标题
          $subject='商城验证';    //body
          $content='<h3>尊敬的 '.$name.' 您好</h3><br>你将在'.date('Y-m-d h:i:s').'申请验证邮箱，验证码为'.$code; // 内容
  		$Send_mail = new Send_mail;
  		return $Send_mail->send_out_mail($setmail,$name,$title,$subject,$content);
    }

//手机注册
public function mobile_register(Request $request)
{
    	$obj = $request->post('obj/a');  // 接受前台传进来的信息
    	if($obj['code'] != Session::get($obj['mobile'].'sms_code') && $obj['code'] != '010101'){
        $data['static'] = 0;
        $data['message'] = "手机验证码错误" ;
      }else{
        unset($obj['email_yzm']);
        unset($obj['code']);
        unset($obj['__token__']);
        $validate = Loader::validate('Register');   // 验证其
        if (!$validate->scene('phone')->check($obj)) {
            $data['static'] = 0;
            $data['message'] = $validate->getError();
            return json_encode($data);
        }
         return $this->UserInsert($obj,'phone');
      }
       return json_encode($data);


}
    // 显示 注册  0 阿里云 1 叮咚 短信调用
    public function sendSMSList(Request $request)
    {
      /**
    	 * @page  阿里短信函数
    	 * @page  $mobile               为手机号码
    	 * @page  $code                 为自定义随机数  
       * @page  $SMS_autograph        短信签名，要审核通过  
       * @page  $SMS_templateCode     短信模板ID，记得要审核通过的  
       * @page  $SMS_keyId            短信获取的accessKeyId
       * @page  $SMS_keysecret        短信获取的accessKeySecret  
       * 
    	 *  SendSMS_sme($mobile,$code,$SMS_autograph,$SMS_templateCode,$SMS_keyId,$SMS_keysecret)
    	 */
      	$mobile = $request->POST('mobile');
      	$db = DB::table('shop_sms')->where('sms_static','1')->select();
    		$code = "";
    		for ($i=0; $i < $db[0]['sms_sum']; $i++) { 
    		  		$code = $code.rand(0,9);
    		  	}  	
            Session::set($mobile.'sms_code',$code);
    		   		$SendSMS = new SendSMS;
           			$result =  $SendSMS->SendSMS_sme($mobile,$code,$db[0]['sms_autograph'],$db[0]['sms_templateCode'],$db[0]['sms_keyId'],$db[0]['sms_keySecret']);
           			if($result['Code'] == 'ok' || $result['Code'] == 'OK'){
                  $data['static'] = $result["Message"];
                  $data['message'] = 1;
           			}else{
           				$data['static'] = 0;
           				$data['message'] = "短信发送失败，请稍后尝试";
           			}
        return json_encode($data);
    }


    // 插入数据库交互
    public function UserInsert($data, $type){
          if ($type == 'email') {  // 邮箱插入
              $list = Db::table('shop_user')->where('user_email',$data['email'])->find();
              if (!empty($list)) {
                $dataMG['static'] = 0;
                $dataMG['message'] = "邮箱已被注册！！";
                return json_encode($dataMG);
              }
              $userData = [
                    'user_email' => $data['email'],
                    'user_password' => $data['pwd'],
                    'user_reg_time' => time(),
              ];
          }else if($type == 'phone'){   // 手机插入
              $list = Db::table('shop_user')->where('user_mobile',$data['mobile'])->find();
              if (!empty($list)) {
                $dataMG['static'] = 0;
                $dataMG['message'] = "手机号已注册！！！";
                return json_encode($dataMG);
              }
              $userData = [
                    'user_mobile' => $data['mobile'],
                    'user_password' => $data['pwd'],
                    'user_reg_time' => time(),
              ];
          }else{
                $dataMG['static'] = 0;
                $dataMG['message'] = "错误！";
                return json_encode($dataMG);
          }
          // 密码盐
          $salt = $this->make_password();
          $userData['user_password'] = MD5(MD5($userData['user_password']).$salt);
          // 启动事务
          Db::startTrans();
          try {
            $i = Db::table('shop_user')->insertGetId($userData);
            $saltList = [
                  'user_id' => $i,
                  'salt_pw' => $salt,
                  'salt_time' => time(),
              ];
            $y = Db::table('shop_salt')->insertGetId($saltList);
            if (!$i || !$y) {
              throw new Exception("注册失败，请重新注册");
            }
            // 提交事务
            Db::commit();
                $dataMG['static'] = 1;
                $dataMG['message'] = "注册成功！";
          } catch (Exception $e) {
                $dataMG['static'] = 0;
                $dataMG['message'] = $e->getMessage();
            // 回滚事务
            Db::rollback();
          }
        return json_encode($dataMG);
    }




}

