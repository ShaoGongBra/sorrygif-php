<?php
namespace app\sorrygif\home;

use app\index\controller\Home;
use app\sorrygif\func\File;
use think\Request;
use think\exception\HttpResponseException;
use think\Db;

/**
 * sorryGif 后台模块
 */
class Base extends Home
{
    protected function dataReturn($code = 200, $msg = "列表获取成功",$data = array(), $count = 0){
        $datas = [
            'code'=>$code,
            'msg'=>$msg,
            'count'=>$count,
            'data'=>$data
        ];
        return json($datas);
    }

    protected function auth(){
        $token = input('token');
        if(empty($token)){
            $token = Request::instance()->header("token");
        }
        $result = null;
        if (empty($token) || strlen($token) != 32) {
            $result = $this->dataReturn(401, '抱歉，您没有操作权限');
        } else {
            $user = Db::name('sorrygif_user')->where('token', $token)->find();
            if (empty($user)) {
                $result = $this->dataReturn(401, '抱歉，登陆已过期，请重新登陆');
            } else {
                return $user;
            }
        }

        throw new HttpResponseException($result);
    }

    //将一个文件添加到附件返回附件id
    protected function file_to_att($path){
        if(!is_file($path)){
            return false;
        }
        $file = new File($path);
        $file->isTest(true);
        $info = $file->copy(ROOT_PATH . 'public' . DS . 'uploads'.DS.'images');
        if(!$info){
            return false;
        }
        //验证对否存在相同的文件，存在就返回这个文件id
        $md5 = $file->hash('md5');
        $sha1 = $file->hash('sha1');
        $att = Db::name('admin_attachment')->field('id')->where(['md5'=>$md5,'sha1'=>$sha1])->find();
        if($att){
            unlink($path);//删除重复文件
            return $att['id'];
        }

        $file_info = [
            'uid'    => is_signin(),
            'name'   => $info->getFilename(),
            'mime'   => $file->getMime(),
            'path'   => 'uploads'.DS.'images'.DS.$info->getSaveName(),
            'ext'    => $info->getExtension(),
            'size'   => $file->getSize(),
            'md5'    => $file->hash('md5'),
            'sha1'   => $file->hash('sha1'),
            'thumb'  => '',
            'driver' => 'local',
            'module' => 'sorrygif',
            'create_time'=>time(),
            'update_time'=>time()
        ];
        return Db::name('admin_attachment')->insertGetId($file_info);
    }

    protected function get_file_path($id){
        return "http://gif.dhoyun.com".get_file_path($id);
    }
}