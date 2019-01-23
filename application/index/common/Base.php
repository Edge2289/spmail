<?php

namespace app\index\common;

use think\Controller;
use PHPMailer\PHPMailer;
use app\common\redis\RedisClice;
use think\Db;
use think\Session;

/**
* 
*/
class Base extends Controller
{

// redis 示例话
    public function redisConfig()
    {
        $conf = [
            'host' =>  '120.79.65.240', // 127.0.0.1  120.79.65.240
            'port' =>  '6379',
            'auth' =>  '3600',
            'index' =>  '11'
        ];
        return new RedisClice($conf);
    }
    
//密码盐
    function make_password($length = 6) 
    { 
  
    // 密码字符集，可任意添加你需要的字符 
        $chars = array('a','b','c','d','e','f','g','h',
        'i','j','k','l','m','n','o','p','q','r','s',
        't','u','v','w','x','y','z','A','B','C','D',
        'E','F','G','H','I','J','K','L','M','N','O',
        'P','Q','R','S','T','U','V','W','X','Y','Z',
        '0','1','2','3','4','5','6','7','8','9','!',
        '@','#','$','%','^','&','*','(',')','-','_',
        '[',']','{','}','<','>','~','`','+','=',',',
        '.',';',':','/','?','|');
        $password = '';
        for($i = 0;$i < $length;$i++) 
        { 
            $password .= $chars[rand(0,count($chars)-1)];
        } 
            return $password;
    }
    function make_shuzi($length = 6){
        $chars = array(1,2,3,4,5,6,7,8,9,0);
        $password = '';
        for($i = 0;$i < $length;$i++) 
        { 
            $password .= $chars[rand(0,count($chars)-1)];
        } 
            return $password;
    }

/**
 * 中文分词
 */
    function decorateSearch_pre($words)
    {
    header('Content-Type: text/html; charset=utf-8');
         //引入 extend/qrcode.php
        import('phpanalysis/phpanalysis', EXTEND_PATH);
        $obj = new \phpanalysis('utf-8', 'utf-8', true);

        $memory_info = '';


        if($words != '')
        {
            //岐义处理
            $do_fork = true;// empty($_POST['do_fork']) ? false : 
            //新词识别
            $do_unit = true;// empty($_POST['do_unit']) ? false : 
            //多元切分
            $do_multi = true;// empty($_POST['do_multi']) ? false : 
            //词性标注
            $do_prop =  false;// empty($_POST['do_prop']) ? false :
            //是否预载全部词条
            $pri_dict =  true;// empty($_POST['pri_dict']) ? false :
            
            //初始化类
            $obj::$loadInit = true;
            $pa = new \phpanalysis('utf-8', 'utf-8', $pri_dict);
            
            //载入词典
            $pa->LoadDict();
            //执行分词
            $memory_info = $pa->SetSource($words);
            $pa->differMax = $do_multi;
            $pa->unitWord = $do_unit;
            
           $pa->StartAnalysis($do_fork);
            
            $memory_info = $okresult = $pa->GetFinallyResult('_MM_', $do_prop);
            return explode("_MM_",substr($memory_info, 4));
            
        }
    }

    public function linkbutton(){
        //去缓存 
         $link = Db::table('shop_link')->select();

        $linka = Db::view('shop_artcleclass','ac_id,ac_title')
                    ->view('shop_articlelist','al_title','shop_artcleclass.ac_id=shop_articlelist.ac_id')
                    ->where('shop_artcleclass.is_sys','=',1)
                    ->where('shop_artcleclass.index_static','=',1)
                    ->order('ac_order')
                    ->select();
                    
        foreach ($linka as $key => $value) {
           $i[$key] = $value['ac_title'];
        }
        $link_one = explode("_m_", implode(array_unique($i), "_m_"));
        // var_dump();die;
         $this->assign([
            'linka' => $linka,
            'link_one' => $link_one,
            'link' => $link,
        ]);
    }

    /**
     * [webpay description]
     * @param  [type] $paypwd  [后台密码]
     * @param  [type] $qianpwd [前台输入的密码]
     * @param  [type] $salt_pw [密钥]
     * @return [type]          [description]
     */
    public function webpay($paypwd, $qianpwd, $salt_pw){
        $qianpaypwd = MD5(MD5($qianpwd).$salt_pw);
        if ($paypwd == $qianpaypwd) {
            return true;
        }
        return false;
    }

    /**
     * [userLog 用户操作错误日志]
     * @param  string $error      [错误信息]
     * @param  [type] $request    [请求]
     * @param  string $controller [控制器]
     * @return [type]             [description]
     */
    public function userLog($error = '错误操作', $request, $controller = 'index'){
        Db::table('shop_user_log')
            ->insert([
                'user_id' => Session::get('user_id'),
                'user_log_time' => time(),
                'user_log_ip' => $request->ip(),
                'user_log_list_address' => $request->url(true),
                'user_log_text' => $error,
            ]);
    }
}