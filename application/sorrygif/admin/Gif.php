<?php
namespace app\sorrygif\admin;

use app\sorrygif\admin\Base;
use app\common\builder\ZBuilder; // 引入ZBuilder
use think\Db;

/**
 * gif管理
 */
class Gif extends Base
{
  public function index(){
    $list = Db::name('sorrygif_gif')
    ->alias('g')
    ->join('__SORRYGIF_GIF_THEMES__ gt','g.gif_thtmes_id = gt.id')
    ->field('g.id,g.icon,laud,g.total,create_time,title')
    ->order('id desc')
    ->select();
    
    // 使用ZBuilder快速创建数据表格
    return ZBuilder::make('table')
      ->setPageTitle('GIF管理')
      ->hideCheckbox()
      ->addColumns([ // 批量添加列
          ['id', 'ID'],
          ['icon', 'GIF','picture'],
          ['title', '模板'],
          ['laud', '点赞数'],
          ['total', '使用次数'],
          ['create_time', '生成时间','datetime']
      ])
      ->setRowList($list) // 设置表格数据
      ->fetch();
  }
}