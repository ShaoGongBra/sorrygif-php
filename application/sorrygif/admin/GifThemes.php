<?php
namespace app\sorrygif\admin;

use app\sorrygif\admin\Base;
use app\common\builder\ZBuilder; // 引入ZBuilder
use think\Db;

/**
 * gif主题
 */
class GifThemes extends Base
{
  public function index(){
    $themes = Db::name('sorrygif_gif_themes')->field('id,name,title,icon,author,version,total,status')->order('id desc')->select();
    
    // 未安装
    $btn_install = [
      'title' => '未安装模板',
      'href'  => url('install_list')
    ];
    // 使用ZBuilder快速创建数据表格
    return ZBuilder::make('table')
      ->setPageTitle('模板列表')
      ->hideCheckbox()
      ->addColumns([ // 批量添加列
          ['id', 'ID'],
          ['icon', '封面','picture'],
          ['name', '标识'],
          ['title', '标题'],
          ['author', '作者'],
          ['version', '版本'],
          ['total', '使用次数'],
          ['status','状态','status', '', ['禁用', '正常']]
      ])
      ->setRowList($themes) // 设置表格数据
      ->addTopButton('custom', $btn_install) // 添加授权按钮
      ->fetch();
  }

  //未安装的主题列表
  public function install_list(){
    $themesDir = APP_PATH.'sorrygif'.DS.'gifthemes'.DS;
    if(!is_dir($themesDir)){
      $this->error("模板目录不存在：".$themesDir);
    }

    $install_themes = Db::name('sorrygif_gif_themes')->field('name')->select();

    $themes = $this->getDir($themesDir);
    $themesList = [];
    foreach($themes as $k => $v){
      $configDir = $themesDir.$v.DS.'config.php';
      $is_install = false;
      foreach($install_themes as $k1 => $v1){
        if($v1['name'] == $v){
          $is_install = true;
          break;
        }
      }
      if(is_file($configDir) && !$is_install){
        $themesInfo = require($themesDir.$v.DS.'config.php');
        $themesList[] = [
          'name'=>$v,
          'title'=>$themesInfo['title'],
          'author'=>$themesInfo['author'],
          'version'=>$themesInfo['version'],
        ];
      }
    }

    $btn_install = [
        'title' => '安装',
        'icon'  => 'si si-login',
        'class' => 'btn btn-xs btn-default ajax-get',
        'href'  => url('install', ['name' => '__id__'])
    ];
  
    return ZBuilder::make('table')
      ->hideCheckbox()  
      ->setPageTitle('未安装的模板')
      ->addColumns([ // 批量添加列
          ['name', '标识'],
          ['title', '标题'],
          ['author', '作者'],
          ['version', '版本'],
          ['right_button', '操作','btn']
      ])
      ->addRightButton('custom', $btn_install) // 添加授权按钮
      ->setPrimaryKey('name')
      ->setRowList($themesList) // 设置表格数据
      ->fetch();
  }

  public function install(){
    $name = input('name');
    $iconfileDir = APP_PATH.'sorrygif'.DS.'gifthemes'.DS.$name.DS.'icon.jpg';
    $configfileDir = APP_PATH.'sorrygif'.DS.'gifthemes'.DS.$name.DS.'config.php';
    if(is_file($iconfileDir) && is_file($configfileDir)){
      $config = require($configfileDir);
      $data = [
        "name"=>$name,
        "title"=>$config['title'],
        "icon" => $this->file_to_att($iconfileDir),
        "version"=>$config['version'],
        "author"=>$config['author'],
        "config"=>json_encode($config)
      ];
      Db::name('sorrygif_gif_themes')->insert($data);
      $this->success("安装成功");
    }else{
      $this->error("封面不存在");
    }
  }



  //获取文件目录列表,该方法返回数组
  private function getDir($dir) {
    $dirArray[]=NULL;
    if (false != ($handle = opendir ( $dir ))) {
        $i=0;
        while ( false !== ($file = readdir ( $handle )) ) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".."&&!strpos($file,".")) {
                $dirArray[$i]=$file;
                $i++;
            }
        }
        //关闭句柄
        closedir ( $handle );
    }
    return $dirArray;
  }
}