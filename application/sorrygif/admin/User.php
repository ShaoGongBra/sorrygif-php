<?php
namespace app\sorrygif\admin;

use app\sorrygif\admin\Base;
use app\common\builder\ZBuilder; // 引入ZBuilder
use think\Db;

/**
 * 用户模块
 */
class User extends Base
{
  public function index(){
    $list = Db::name('sorrygif_user')->order('id desc')->select();
    
    // 使用ZBuilder快速创建数据表格
    return ZBuilder::make('table')
      ->setPageTitle('用户管理')
      ->hideCheckbox()
      ->addColumns([ // 批量添加列
          ['id', 'ID'],
          ['avatar_url', '头像','img_url'],
          ['nickname', '昵称'],
          ['gender', '性别','','', ['0' =>'未知','1' => '男','2' => '女']],
          ['create_time', '注册时间','datetime'],
          ['update_time', '更新时间','datetime']
      ])
      ->setRowList($list) // 设置表格数据
      ->fetch();
  }
}