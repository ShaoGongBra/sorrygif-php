<?php
namespace app\sorrygif\home;

use app\sorrygif\home\Base;
use think\Db;  //使用数据库操作
use think\Request;
use app\sorrygif\func\WXBizDataCrypt; ;

/**
 * sorryGif API接口
 */
class ApiUser extends Base
{
    
    //用户登陆
    public  function login(){
        $APPID = module_config('sorrygif.appid');
        $AppSecret = module_config('sorrygif.app_secret');
        $wx_request_url = "https://api.weixin.qq.com/sns/jscode2session"; 
        $code = input("code");
        $param = array( 
          'appid' => $APPID, 
          'secret' => $AppSecret, 
          'js_code' => $code, 
          'grant_type' => 'authorization_code'
        ); 
        // 一个使用curl实现的get方法请求
        $arr = $this->http_send($wx_request_url, $param, 'post'); 
        $arr = json_decode($arr,true);
        if(isset($arr['errcode']) && !empty($arr['errcode'])){
            return $this->dataReturn(403,$arr['errmsg']);
        }
        $openid = $arr['openid'];
        $session_key = $arr['session_key'];
  
        // 数据签名校验
        $signature = input("signature");
        $signature2 = sha1($_POST['rawData'].$session_key);  //别用框架自带的input,会过滤掉必要的数据
        if ($signature != $signature2) {
            $msg = "shibai 1";
            return $this->dataReturn(403,$arr['errmsg']);
        }
  
        //开发者如需要获取敏感数据，需要对接口返回的加密数据( encryptedData )进行对称解密
        $encryptedData = $_POST['encryptedData'];
        $iv = $_POST['iv'];
        $pc = new WXBizDataCrypt($APPID, $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);  //其中$data包含用户的所有数据
        if ($errCode != 0) {
            return $this->dataReturn(403,'获取失败');
        }
        //写自己的逻辑： 操作数据库等操作
        $data = json_decode($data,true);
        $token = $this->uuid('',false);
        //print_r($data);
        $savedata = [
            'openid'=>$data['openId'],
            'nickname'=>$data['nickName'],
            'gender'=>$data['gender'],
            'avatar_url'=>$this->wx_avatar($data['avatarUrl'],96),
            'token'=>$token,
            'create_time'=>time(),
            'update_time'=>time()
        ];
        $user = Db::name('sorrygif_user')->where(['openid'=>$data['openId']])->find();
        if($user){
            Db::name('sorrygif_user')->field('nickname,gender,avatar_url,token,update_time')->where(['id'=>$user['id']])->update($savedata);
        }else{
            Db::name('sorrygif_user')->insert($savedata);
        }

        //生成第三方3rd_session
        $session3rd  = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<16;$i++){
            $session3rd .=$strPol[rand(0,$max)];
        }
        return $this->dataReturn(200,'登录成功',['session3rd'=>$session3rd,'token'=>$token]);
    }  
    
    /** 
     * 发送HTTP请求方法 
     * @param string $url  请求URL 
     * @param array $params 请求参数 
     * @param string $method 请求方法GET/POST 
     * @return array $data  响应数据 
     */
    protected function http_send($url, $params, $method = 'GET', $header = array(), $multi = false){ 
        $opts = array( 
        CURLOPT_TIMEOUT    => 30, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_SSL_VERIFYPEER => false, 
        CURLOPT_SSL_VERIFYHOST => false, 
        CURLOPT_HTTPHEADER   => $header 
        ); 
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){ 
        case 'GET': 
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params); 
            break; 
        case 'POST': 
            //判断是否传输文件 
            $params = $multi ? $params : http_build_query($params); 
            $opts[CURLOPT_URL] = $url; 
            $opts[CURLOPT_POST] = 1; 
            $opts[CURLOPT_POSTFIELDS] = $params; 
            break; 
        default: 
            throw new Exception('不支持的请求方式！'); 
        } 
        /* 初始化并执行curl请求 */
        $ch = curl_init(); 
        curl_setopt_array($ch, $opts); 
        $data = curl_exec($ch); 
        $error = curl_error($ch); 
        curl_close($ch); 
        if($error) throw new Exception('请求发生错误：' . $error); 
        return $data; 
    }

    /**
     * 生成除特殊符号外32位UUID序列
     * $prefix: uuid前缀
     * $minus: 是否带有常见的-符号
     */
    protected function uuid($prefix = '', $minus = true){
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = '';
        if ($minus) {
            $uuid .= substr($chars,0,8) . '-';
            $uuid .= substr($chars,8,4) . '-';
            $uuid .= substr($chars,12,4) . '-';
            $uuid .= substr($chars,16,4) . '-';
            $uuid .= substr($chars,20,12);
        } else {
            $uuid .= substr($chars,0,8);
            $uuid .= substr($chars,8,4);
            $uuid .= substr($chars,12,4);
            $uuid .= substr($chars,16,4);
            $uuid .= substr($chars,20,12);
        }
        return $prefix . $uuid;
    }

    /**
     * 处理微信用户头像
     * $path: 头像地址
     * $size: 需要的尺寸
     */
    protected function wx_avatar($path,$size = 0){
        return substr($path,0,strripos($path,'/')+1).$size;
    }
}