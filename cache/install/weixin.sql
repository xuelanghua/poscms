
DROP TABLE IF EXISTS `{dbprefix}weixin`;

CREATE TABLE `{dbprefix}weixin` (
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信属性参数表';

DROP TABLE IF EXISTS `{dbprefix}weixin_follow`;

CREATE TABLE `{dbprefix}weixin_follow` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`status`),
  KEY (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝同步表';

DROP TABLE IF EXISTS `{dbprefix}weixin_menu`;
CREATE TABLE `{dbprefix}weixin_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `displayorder` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信菜单表';


DROP TABLE IF EXISTS `{dbprefix}weixin_group`;

CREATE TABLE `{dbprefix}weixin_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `count` int(10) NOT NULL,
  `wechat_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝分组表';



DROP TABLE IF EXISTS `{dbprefix}weixin_keyword`;

CREATE TABLE `{dbprefix}weixin_keyword` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `keywords` varchar(255) NOT NULL COMMENT '关键词',
  `mtype` varchar(50) NOT NULL COMMENT '素材类型',
  `content` text NOT NULL,
  `mid` int(10) NOT NULL COMMENT '素材Id',
  `plug` varchar(100) NOT NULL COMMENT '插件名称',
  `count` int(10) NOT NULL,
  `inputtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keywords` (`keywords`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信关键字回复表';



DROP TABLE IF EXISTS `{dbprefix}weixin_material_file`;

CREATE TABLE `{dbprefix}weixin_material_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `is_video` tinyint(1) NOT NULL,
  `file` int(10) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `wechat_url` varchar(255) NOT NULL,
  `media_id` varchar(100) NOT NULL,
  `inputtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片素材';



DROP TABLE IF EXISTS `{dbprefix}weixin_material_image`;

CREATE TABLE `{dbprefix}weixin_material_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `file` varchar(100) CHARACTER SET utf8 NOT NULL,
  `media_id` varchar(100) NOT NULL,
  `wechat_url` varchar(255) NOT NULL,
  `inputtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片素材';



DROP TABLE IF EXISTS `{dbprefix}weixin_material_news`;

CREATE TABLE `{dbprefix}weixin_material_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `author` varchar(255) CHARACTER SET utf8 NOT NULL,
  `thumb` int(10) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `linkurl` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `group_id` int(10) NOT NULL,
  `thumb_media_id` varchar(255) NOT NULL,
  `media_id` varchar(100) NOT NULL,
  `inputtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片素材';

DROP TABLE IF EXISTS `{dbprefix}weixin_material_text`;
CREATE TABLE `{dbprefix}weixin_material_text` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `inputtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文本素材';





DROP TABLE IF EXISTS `{dbprefix}weixin_message`;
CREATE TABLE `{dbprefix}weixin_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `send` tinyint(1) NOT NULL COMMENT '是否用户提交',
  `openid` varchar(100) NOT NULL,
  `content` text NOT NULL COMMENT '消息内容',
  `inputtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信消息记录表';

DROP TABLE IF EXISTS `{dbprefix}weixin_user`;
CREATE TABLE `{dbprefix}weixin_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '会员id',
  `username` varchar(100) NOT NULL,
  `groupid` int(10) NOT NULL,
  `openid` varchar(50) NOT NULL COMMENT '唯一id',
  `nickname` text NOT NULL COMMENT '微信昵称',
  `sex` tinyint(1) unsigned DEFAULT NULL COMMENT '性别',
  `city` varchar(30) DEFAULT NULL COMMENT '城市',
  `country` varchar(30) DEFAULT NULL COMMENT '国家',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `language` varchar(30) DEFAULT NULL COMMENT '语言',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `subscribe_time` int(10) unsigned NOT NULL COMMENT '关注时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `subscribe_time` (`subscribe_time`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝表';