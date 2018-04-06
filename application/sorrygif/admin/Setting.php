<?php
namespace app\sorrygif\admin;

use app\sorrygif\admin\Base;

/**
 * 设置
 */
class Setting extends Base
{
  public function index(){
    // 调用moduleConfig()方法即可，或者使用函数module_config()
    return $this->moduleConfig();
  }
}