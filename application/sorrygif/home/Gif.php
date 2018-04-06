<?php
namespace app\sorrygif\home;

use app\sorrygif\home\Base;
use think\Db;

/**
 * sorryGif gif
 */
class Gif extends Base
{
    public function make(){
        return $this->fetch('/info',['info'=>['msg'=>'网页版已经停用 即将上线小程序']]);
        $id = input('id');
        $thtmes = Db::name('sorrygif_gif_themes')->field('id,name,title,icon,total,config')->where(['status'=>1,'id'=>$id])->find();
        if(empty($thtmes)){
            return $this->fetch('/info',['info'=>['msg'=>'模板不存在']]);
        }
        $thtmes['icon'] = get_file_path($thtmes['icon']);
        $thtmes['config'] = json_decode($thtmes['config'],true);
        return $this->fetch('/gif_themes',['info'=>$thtmes]);
    }
}