<?php
namespace app\sorrygif\admin;

use app\admin\controller\Admin;
use think\Db;
use app\sorrygif\func\File;

/**
 * sorryGif 后台模块
 */
class Base extends Admin
{
    //将一个文件添加到附件
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
}