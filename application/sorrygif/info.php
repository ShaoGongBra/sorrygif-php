<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

/**
 * 模块信息
 */
return [
  'name' => 'sorrygif',
  'title' => '脑洞GIF',
  'identifier' => 'sorrygif.shaogong.module',
  'author' => 'shaogong',
  'version' => '1.0.0',
  'description' => 'gif生成管理模块',
  'tables' => [
    'sorrygif_gif_themes',
    'sorrygif_gif',
    'sorrygif_gif_user',
    'sorrygif_user',
    'sorrygif_laud',
  ],
  'database_prefix' => 'dh_',
  'config' => [
    [
      'text',
      'appid',
      '小程序APPID',
      'wx6ff70f21edf45848',
    ],
    [
      'text',
      'app_secret',
      '小程序AppSecret',
      '556e9de44419f107bcb8ea5dc167ee4b',
    ],
  ],
];
