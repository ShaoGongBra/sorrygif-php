-- -----------------------------
-- 导出时间 `2018-04-06 22:39:36`
-- -----------------------------

-- -----------------------------
-- 表结构 `dh_sorrygif_gif_themes`
-- -----------------------------
DROP TABLE IF EXISTS `dh_sorrygif_gif_themes`;
CREATE TABLE `dh_sorrygif_gif_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL COMMENT '主题模板标识',
  `title` varchar(250) NOT NULL COMMENT '主题模板标题',
  `icon` int(11) NOT NULL COMMENT '封面',
  `version` int(4) NOT NULL COMMENT '版本',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `config` varchar(1000) NOT NULL COMMENT '主题配置信息JSON',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '使用次数',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0禁用1启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dh_sorrygif_gif`
-- -----------------------------
DROP TABLE IF EXISTS `dh_sorrygif_gif`;
CREATE TABLE `dh_sorrygif_gif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gif_thtmes_id` int(11) NOT NULL COMMENT '模板id',
  `icon` int(11) NOT NULL COMMENT '图片',
  `laud` int(11) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `total` int(11) NOT NULL DEFAULT '1' COMMENT '使用次数',
  `content` text NOT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dh_sorrygif_gif_user`
-- -----------------------------
DROP TABLE IF EXISTS `dh_sorrygif_gif_user`;
CREATE TABLE `dh_sorrygif_gif_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gif_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `dh_sorrygif_user`
-- -----------------------------
DROP TABLE IF EXISTS `dh_sorrygif_user`;
CREATE TABLE `dh_sorrygif_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL COMMENT 'openid',
  `nickname` varchar(50) NOT NULL COMMENT '昵称',
  `gender` tinyint(1) NOT NULL COMMENT '性别',
  `avatar_url` varchar(250) NOT NULL COMMENT '头像',
  `token` varchar(50) NOT NULL COMMENT 'token',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

