<?php
namespace app\sorrygif\home;

use app\sorrygif\home\Base;
use think\Db;

/**
 * sorryGif gif生成接口
 */
class ApiGif extends Base
{
    //首页
    public function index(){
        
    }

    //gif列表
    public function get_gif_list(){
        $old_count = intval(input('count'));
        $offset = intval(input('offset'));
        $count = Db::name('sorrygif_gif')->count();
        $old_count = $old_count == 0?$count:$old_count;
        $list = Db::name('sorrygif_gif')->field('id,icon,laud,total,create_time')->limit($offset+($count-$old_count),10)->order('id desc')->select();
        foreach($list as $k => $v){
            $list[$k]['icon'] = $this->get_file_path($v['icon']);
            $list[$k]['create_time'] = date('m-d',$v['create_time']);
        }
        return $this->dataReturn(200,'获取列表成功',$list,$count);
    }

    //gif内容
    public function get_gif(){
        $id = intval(input('id'));
        $info = Db::name('sorrygif_gif')->where('id',$id)->find();
        if(empty($info)){
            return $this->dataReturn(403,'表情不存在');
        }
        $info['icon'] = $this->get_file_path($info['icon']);
        $info['content'] = json_decode($info['content'],true);
        $info['create_time'] = date('m-d H:i',$info['create_time']);
        return $this->dataReturn(200,'内容获取成功',$info);
    }
}