<?php
namespace app\sorrygif\home;

use app\sorrygif\home\Base;
use think\Db;

/**
 * sorryGif gif生成接口
 */
class ApiMakeGif extends Base
{
    //模板列表
    public function get_themes_list(){
        $offset = intval(input('offset'));
        $list = Db::name('sorrygif_gif_themes')->field('id,name,title,icon,total')->where(['status'=>1])->limit($offset,20)->select();
        foreach($list as $k => $v){
            $list[$k]['icon'] = $this->get_file_path($v['icon']);
            
        }
        return $this->dataReturn(200,'列表获取成功',$list);
    }

    //模板模板详情
    public function get_themes(){
        $id = intval(input('id'));
        if(empty($id)){
            return $this->dataReturn(403,'参数错误');
        }
        $thtmes = Db::name('sorrygif_gif_themes')->field('id,name,title,icon,total,config')->where(['status'=>1,'id'=>$id])->find();
        if($thtmes){
            $thtmes['config'] = json_decode($thtmes['config'],true);
            $thtmes['icon'] = $this->get_file_path($thtmes['icon']);
            return $this->dataReturn(200,'获取成功',$thtmes);
        }else{
            return $this->dataReturn(403,'模板不存在');
        }
    }

    //生成GIF图片
    public function make_gif(){
        $user = $this->auth();
        $id = intval(input('id'));
        $sentences = $_POST['sentences'];
        if(empty($id)){
            return $this->dataReturn(403,'参数错误');
        }
        $thtmes = Db::name('sorrygif_gif_themes')->field('name,config')->where(['status'=>1,'id'=>$id])->find();
        if($thtmes){
            $thtmes['config'] = json_decode($thtmes['config'],true);
            //检测是否完整
            $empty_count = 0;//有多少个空数据
            for ($x=0; $x<count($thtmes['config']['subtitle']); $x++) {
                if(!isset($sentences[$x]) || empty($sentences[$x])){
                    $empty_count++;
                }
            }
            if($empty_count > 0 && $empty_count<count($thtmes['config']['subtitle'])){
                return $this->dataReturn(403,'内容不完整，你可以尝试什么都不输入');
            }
            if($empty_count == count($thtmes['config']['subtitle'])){
                foreach($thtmes['config']['subtitle'] as $k => $v){
                    $sentences[$k] = $v['default'];
                }
            }
            $video = APP_PATH.'sorrygif'.DS.'gifthemes'.DS.$thtmes['name'].DS.'template.mp4';
            $assfile = APP_PATH.'sorrygif'.DS.'gifthemes'.DS.$thtmes['name'].DS.'template.ass';
            if(is_file($video) && is_file($assfile)){
                $newassfile = $this->make_subtitle($assfile,$sentences);
                $gifFile = DS.'uploads'.DS.'images'.DS.'gif'.DS.md5(json_encode($sentences).$id).'.gif';
                $gifPath = ROOT_PATH.'public'.$gifFile;
                if(!is_file($gifPath)){
                    $cmd = "ffmpeg -i " . $video . " -r 8 -vf ass=" . $newassfile . ",scale=220:-1 -y " . $gifPath;
                    exec($cmd);
                }
                $imgid = $this->file_to_att($gifPath);
                $gifid = Db::name('sorrygif_gif')->where('icon',$imgid)->value('id');
                if(!$gifid){
                    $gifdata = [
                        'gif_thtmes_id'=>$id,
                        'icon'=>$imgid,
                        'content'=>json_encode($sentences),
                        'create_time'=>time()
                    ];
                    $gifid = Db::name('sorrygif_gif')->insertGetId($gifdata);
                }else{
                    Db::name('sorrygif_gif')->where('id',$gifid)->setInc('total');
                }
                $usergif = [
                    'user_id'=>$user['id'],
                    'gif_id'=>$gifid,
                    'create_time'=>time()
                ];
                Db::name('sorrygif_gif_user')->insert($usergif);
                
                $gifFile = $this->get_file_path($imgid);
                return $this->dataReturn(200,'生成成功',['path'=>$gifFile]);
            }else{
                return $this->dataReturn(403,'模板错误生成失败');
            }
        }else{
            return $this->dataReturn(403,'内容不存在，生成失败');
        }
    }

    protected function make_subtitle($assfile,$sentences){
        $savefile = ROOT_PATH.'runtime'.DS.'ffmpeg'.DS.md5(json_encode($sentences).$assfile).'.ass';
        //检测是否存在相同的字幕
        if(is_file($savefile)){
            return $savefile;
        }
        //读取字幕模板
        $file = fopen($assfile, "r");
        $subtitle = fread($file,filesize($assfile));
        fclose($file);
        //修改字幕模板
        for ($x=0; $x<count($sentences); $x++) {
            $zengze = '/<%= sentences\['.$x.'\] %>/';
            $subtitle = preg_replace($zengze,$sentences[$x],$subtitle);
        }
        //写入新的字幕模板
        $file = fopen($savefile, "w+");
        fwrite($file, $subtitle);
        fclose($file);
        return $savefile;
    }
}