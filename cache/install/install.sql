DROP TABLE IF EXISTS `{dbprefix}comment`;
CREATE TABLE IF NOT EXISTS `{dbprefix}comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `value` text COMMENT '配置信息',
  `field` text COMMENT '自定义字段信息',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='评论配置表';

DROP TABLE IF EXISTS `{dbprefix}admin_notice`;
CREATE TABLE IF NOT EXISTS `{dbprefix}admin_notice` (
  `id` int(10) NOT NULL COMMENT 'id' AUTO_INCREMENT,
  `type` varchar(20) NOT NULL COMMENT '提醒类型：系统、内容、会员、应用',
  `msg` text NOT NULL COMMENT '提醒内容说明',
  `uri` varchar(100) NOT NULL COMMENT '对应的URI',
  `to_rid` smallint(5) NOT NULL COMMENT '指定角色组',
  `to_uid` int(10) NOT NULL COMMENT '指定管理员',
  `status` tinyint(1) NOT NULL COMMENT '未处理0，1已查看，2处理中，3处理完成',
  `uid` int(10) NOT NULL COMMENT '处理人',
  `username` varchar(100) NOT NULL COMMENT '处理人',
  `updatetime` int(10) NOT NULL COMMENT '处理时间',
  `inputtime` int(10) NOT NULL COMMENT '提醒时间',
  PRIMARY KEY (`id`),
  KEY `uri` (`uri`),
  KEY `status` (`status`),
  KEY `to_uid` (`to_uid`),
  KEY `to_rid` (`to_rid`),
  KEY `updatetime` (`updatetime`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台提醒表';

DROP TABLE IF EXISTS `{dbprefix}controller`;
CREATE TABLE IF NOT EXISTS `{dbprefix}controller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `app` varchar(100) NOT NULL COMMENT '网站,会员,后台',
  `type` tinyint(1) unsigned NOT NULL COMMENT '前台0会员1后台2',
  `cname` varchar(100) NOT NULL COMMENT '控制器名称',
  `file` varchar(100) NOT NULL COMMENT '文件路径',
  `url` varchar(255) NOT NULL COMMENT '访问地址',
  `meta_title` varchar(255) NOT NULL COMMENT '网页标题',
  `meta_keywords` varchar(255) NOT NULL COMMENT '网页关键字',
  `meta_description` varchar(255) NOT NULL COMMENT '网页描述',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='自定义控制器表';

DROP TABLE IF EXISTS `{dbprefix}var`;
CREATE TABLE IF NOT EXISTS `{dbprefix}var` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `name` varchar(100) NOT NULL COMMENT '变量描述名称',
  `cname` varchar(100) NOT NULL COMMENT '变量名称',
  `type` tinyint(2) NOT NULL COMMENT '变量类型',
  `value` text CHARACTER SET utf8 NOT NULL COMMENT '变量值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自定义变量表';



DROP TABLE IF EXISTS `{dbprefix}module_form`;
CREATE TABLE IF NOT EXISTS `{dbprefix}module_form` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT '模块目录',
  `name` varchar(50) NOT NULL COMMENT '表单名称',
  `table` varchar(50) NOT NULL COMMENT '表单表名称',
  `disabled` tinyint(1) unsigned NOT NULL COMMENT '是否禁用',
  `permission` text NOT NULL COMMENT '会员权限',
  `setting` text NOT NULL COMMENT '表单配置',
  PRIMARY KEY (`id`),
  KEY `table` (`table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块表单表';


DROP TABLE IF EXISTS `{dbprefix}admin_login`;
CREATE TABLE IF NOT EXISTS `{dbprefix}admin_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned DEFAULT NULL COMMENT '会员uid',
  `loginip` varchar(50) NOT NULL COMMENT '登录Ip',
  `logintime` int(10) unsigned NOT NULL COMMENT '登录时间',
  `useragent` varchar(255) NOT NULL COMMENT '客户端信息',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `loginip` (`loginip`),
  KEY `logintime` (`logintime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='登录日志记录';

DROP TABLE IF EXISTS `{dbprefix}member_login`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned DEFAULT NULL COMMENT '会员uid',
  `oauthid` varchar(30) NOT NULL COMMENT '快捷登录方式',
  `loginip` varchar(50) NOT NULL COMMENT '登录Ip',
  `logintime` int(10) unsigned NOT NULL COMMENT '登录时间',
  `useragent` varchar(255) NOT NULL COMMENT '客户端信息',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `loginip` (`loginip`),
  KEY `logintime` (`logintime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='登录日志记录';

DROP TABLE IF EXISTS `{dbprefix}downservers`;
CREATE TABLE IF NOT EXISTS `{dbprefix}downservers` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '服务器名',
  `server` varchar(255) NOT NULL COMMENT '服务器地址',
  `displayorder` tinyint(3) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='下载镜像服务器';

DROP TABLE IF EXISTS `{dbprefix}urlrule`;
CREATE TABLE IF NOT EXISTS `{dbprefix}urlrule` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '规则类型',
  `name` varchar(50) NOT NULL COMMENT '规则名称',
  `value` text NOT NULL COMMENT '详细规则',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='URL规则表' ;

DROP TABLE IF EXISTS `{dbprefix}member_notice_0`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_0` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_1`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_2`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';


DROP TABLE IF EXISTS `{dbprefix}member_notice_3`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_3` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_4`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_4` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_5`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_5` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_6`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_6` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_7`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_7` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_8`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_8` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_notice_9`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_notice_9` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}member_new_notice`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_new_notice` (
  `uid` smallint(8) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新通知提醒表';

DROP TABLE IF EXISTS `{dbprefix}cron_queue`;
CREATE TABLE IF NOT EXISTS `{dbprefix}cron_queue` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) unsigned NOT NULL COMMENT '类型',
  `value` text NOT NULL COMMENT '值',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态',
  `error` varchar(255) NOT NULL COMMENT '错误信息',
  `updatetime` int(10) unsigned NOT NULL COMMENT '执行时间',
  `inputtime` int(10) unsigned NOT NULL COMMENT '写入时间',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='任务队列表';


DROP TABLE IF EXISTS `{dbprefix}application`;
CREATE TABLE IF NOT EXISTS `{dbprefix}application` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `module` text COMMENT '模块划分',
  `dirname` varchar(50) NOT NULL COMMENT '目录名称',
  `setting` text COMMENT '配置信息',
  `disabled` tinyint(1) DEFAULT '0' COMMENT '是否禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dirname` (`dirname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用表';


DROP TABLE IF EXISTS `{dbprefix}member_menu`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) unsigned NOT NULL COMMENT '上级菜单id',
  `name` text NOT NULL COMMENT '菜单名称',
  `uri` varchar(255) DEFAULT NULL COMMENT 'uri字符串',
  `url` varchar(255) DEFAULT NULL COMMENT 'url',
  `mark` varchar(50) DEFAULT NULL COMMENT '菜单标识',
  `hidden` tinyint(1) unsigned DEFAULT NULL COMMENT '是否隐藏',
  `target` tinyint(3) unsigned DEFAULT NULL COMMENT '新窗口',
  `displayorder` tinyint(3) unsigned DEFAULT NULL COMMENT '排序值',
  `icon` VARCHAR(30) DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`),
  KEY `list` (`pid`),
  KEY `displayorder` (`displayorder`),
  KEY `mark` (`mark`),
  KEY `hidden` (`hidden`),
  KEY `uri` (`uri`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员菜单表';

DROP TABLE IF EXISTS `{dbprefix}admin`;
CREATE TABLE IF NOT EXISTS `{dbprefix}admin` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `realname` varchar(50) DEFAULT NULL COMMENT '管理员姓名',
  `usermenu` text COMMENT '自定义面板菜单，序列化数组格式',
  `color` text DEFAULT NULL COMMENT '定制权限',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='管理员表';

REPLACE INTO `{dbprefix}admin` VALUES(1, '网站创始人', '', '');

DROP TABLE IF EXISTS `{dbprefix}mail_smtp`;
CREATE TABLE IF NOT EXISTS `{dbprefix}mail_smtp` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `port` mediumint(8) unsigned NOT NULL,
  `displayorder` TINYINT(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='邮件账户表';

DROP TABLE IF EXISTS `{dbprefix}mail_queue`;
CREATE TABLE IF NOT EXISTS `{dbprefix}mail_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL COMMENT '邮件地址',
  `subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `message` text NOT NULL COMMENT '邮件内容',
  `status` tinyint(1) unsigned NOT NULL COMMENT '发送状态',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `updatetime` (`updatetime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='邮件队列表';

DROP TABLE IF EXISTS `{dbprefix}admin_menu`;
CREATE TABLE IF NOT EXISTS `{dbprefix}admin_menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) unsigned NOT NULL COMMENT '上级菜单id',
  `name` text NOT NULL COMMENT '菜单语言名称',
  `uri` varchar(255) DEFAULT NULL COMMENT 'uri字符串',
  `url` varchar(255) DEFAULT NULL COMMENT '外链地址',
  `mark` varchar(100) DEFAULT NULL COMMENT '菜单标识',
  `hidden` tinyint(1) unsigned DEFAULT NULL COMMENT '是否隐藏',
  `displayorder` tinyint(3) unsigned DEFAULT NULL COMMENT '排序值',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标标示',
  PRIMARY KEY (`id`),
  KEY `list` (`pid`),
  KEY `displayorder` (`displayorder`),
  KEY `mark` (`mark`),
  KEY `hidden` (`hidden`),
  KEY `uri` (`uri`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

DROP TABLE IF EXISTS `{dbprefix}admin_role`;
CREATE TABLE IF NOT EXISTS `{dbprefix}admin_role` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `site` text NOT NULL COMMENT '允许管理的站点，序列化数组格式',
  `name` text NOT NULL COMMENT '角色组语言名称',
  `system` text NOT NULL COMMENT '系统权限',
  `module` text NOT NULL COMMENT '模块权限',
  `application` text NOT NULL COMMENT '应用权限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台角色权限表';

REPLACE INTO `{dbprefix}admin_role` VALUES(1, '', '超级管理员', '', '', '');
REPLACE INTO `{dbprefix}admin_role` VALUES(2, '', '网站编辑员', '', '', '');

DROP TABLE IF EXISTS `{dbprefix}admin_verify`;
CREATE TABLE IF NOT EXISTS `{dbprefix}admin_verify` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT '名称',
  `verify` text NOT NULL COMMENT '审核部署',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='审核管理表';

REPLACE INTO `{dbprefix}admin_verify` VALUES(1, '审核一次', 'a:1:{i:1;a:2:{i:0;s:1:\\"2\\";i:1;s:1:\\"3\\";}}');

DROP TABLE IF EXISTS `{dbprefix}attachment`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `siteid` tinyint(3) unsigned NOT NULL COMMENT '站点id',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `tableid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '附件副表id',
  `download` mediumint(8) NOT NULL DEFAULT '0' COMMENT '下载次数',
  `filesize` int(10) unsigned NOT NULL COMMENT '文件大小',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filemd5` varchar(50) NOT NULL COMMENT '文件md5值',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `author` (`author`),
  KEY `relatedtid` (`related`),
  KEY `fileext` (`fileext`),
  KEY `filemd5` (`filemd5`),
  KEY `siteid` (`siteid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='附件表';

DROP TABLE IF EXISTS `{dbprefix}attachment_0`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_0` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表0';

DROP TABLE IF EXISTS `{dbprefix}attachment_1`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_1` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表1';

DROP TABLE IF EXISTS `{dbprefix}attachment_2`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_2` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表2';

DROP TABLE IF EXISTS `{dbprefix}attachment_3`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_3` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表3';

DROP TABLE IF EXISTS `{dbprefix}attachment_4`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_4` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表4';

DROP TABLE IF EXISTS `{dbprefix}attachment_5`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_5` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表5';

DROP TABLE IF EXISTS `{dbprefix}attachment_6`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_6` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表6';

DROP TABLE IF EXISTS `{dbprefix}attachment_7`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_7` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表7';

DROP TABLE IF EXISTS `{dbprefix}attachment_8`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_8` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表8';

DROP TABLE IF EXISTS `{dbprefix}attachment_9`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_9` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `related` varchar(50) NOT NULL COMMENT '相关表标识',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表9';

DROP TABLE IF EXISTS `{dbprefix}attachment_unused`;
CREATE TABLE IF NOT EXISTS `{dbprefix}attachment_unused` (
  `id` mediumint(8) unsigned NOT NULL COMMENT '附件id',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `author` varchar(50) NOT NULL COMMENT '会员',
  `siteid` tinyint(3) unsigned NOT NULL COMMENT '站点id',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '原文件名',
  `fileext` varchar(20) NOT NULL COMMENT '文件扩展名',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `attachment` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器路径',
  `remote` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '远程附件id',
  `attachinfo` text NOT NULL COMMENT '附件信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '入库时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `author` (`author`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='未使用的附件表';

DROP TABLE IF EXISTS `{dbprefix}field`;
CREATE TABLE IF NOT EXISTS `{dbprefix}field` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT '字段别名语言',
  `fieldname` varchar(50) NOT NULL COMMENT '字段名称',
  `fieldtype` varchar(50) NOT NULL COMMENT '字段类型',
  `relatedid` smallint(5) unsigned NOT NULL COMMENT '相关id',
  `relatedname` varchar(50) NOT NULL COMMENT '相关表',
  `isedit` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否可修改',
  `ismain` tinyint(1) unsigned NOT NULL COMMENT '是否主表',
  `issystem` tinyint(1) unsigned NOT NULL COMMENT '是否系统表',
  `ismember` tinyint(1) unsigned NOT NULL COMMENT '是否会员可见',
  `issearch` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否可搜索',
  `disabled` tinyint(1) unsigned NOT NULL COMMENT '禁用？',
  `setting` text NOT NULL COMMENT '配置信息',
  `displayorder` tinyint(3) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `list` (`relatedid`,`disabled`,`issystem`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='字段表';

DROP TABLE IF EXISTS `{dbprefix}linkage`;
CREATE TABLE IF NOT EXISTS `{dbprefix}linkage` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '菜单名称',
  `type` tinyint(1) unsigned NOT NULL,
  `code` char(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `module` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='联动菜单表';

DROP TABLE IF EXISTS `{dbprefix}linkage_data_1`;
CREATE TABLE IF NOT EXISTS `{dbprefix}linkage_data_1` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `site` mediumint(5) unsigned NOT NULL COMMENT '站点id',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `pids` varchar(255) DEFAULT NULL COMMENT '所有上级id',
  `name` varchar(30) NOT NULL COMMENT '栏目名称',
  `cname` varchar(30) NOT NULL COMMENT '别名',
  `child` tinyint(1) unsigned DEFAULT NULL DEFAULT '0' COMMENT '是否有下级',
  `hidden` tinyint(1) unsigned DEFAULT NULL DEFAULT '0' COMMENT '前端隐藏',
  `childids` text DEFAULT NULL COMMENT '下级所有id',
  `displayorder` tinyint(3) DEFAULT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cname` (`cname`),
  KEY `hidden` (`hidden`),
  KEY `list` (`site`,`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='联动菜单数据表';

DROP TABLE IF EXISTS `{dbprefix}member`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(40) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '加密密码',
  `salt` char(10) NOT NULL COMMENT '随机加密码',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `phone` char(20) NOT NULL COMMENT '手机号码',
  `avatar` varchar(255) NOT NULL COMMENT '头像地址',
  `money` decimal(10,2) unsigned NOT NULL COMMENT 'RMB',
  `freeze` decimal(10,2) unsigned NOT NULL COMMENT '冻结RMB',
  `spend` decimal(10,2) unsigned NOT NULL COMMENT '消费RMB总额',
  `score` int(10) unsigned NOT NULL COMMENT '虚拟币',
  `experience` int(10) unsigned NOT NULL COMMENT '经验值',
  `adminid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '管理组id',
  `groupid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '用户组id',
  `levelid` smallint(5) unsigned NOT NULL COMMENT '会员级别',
  `overdue` int(10) unsigned NOT NULL COMMENT '到期时间',
  `regip` varchar(15) NOT NULL COMMENT '注册ip',
  `regtime` int(10) unsigned NOT NULL COMMENT '注册时间',
  `randcode` mediumint(6) unsigned NOT NULL COMMENT '随机验证码',
  `ismobile` tinyint(1) unsigned DEFAULT NULL COMMENT '手机认证标识',
  PRIMARY KEY (`uid`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `groupid` (`groupid`),
  KEY `adminid` (`adminid`),
  KEY `phone` (`phone`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员表';

REPLACE INTO `{dbprefix}member` VALUES(1, '{email}', '{username}', '{password}', '{salt}', '', '', '', 9999.00, 0.00, 0.00, 10000, 10000, 1, 3, 4, 0, '', 0, 0, 0);

DROP TABLE IF EXISTS `{dbprefix}member_address`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_address` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `city` mediumint(8) unsigned NOT NULL COMMENT '城市id',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `phone` varchar(20) NOT NULL COMMENT '电话',
  `zipcode` varchar(10) NOT NULL COMMENT '邮编',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `default` tinyint(1) unsigned NOT NULL COMMENT '是否默认',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员收货地址表';

DROP TABLE IF EXISTS `{dbprefix}member_data`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_data` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `complete` tinyint(1) unsigned NOT NULL COMMENT '完善资料标识',
  `is_auth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '实名认证标识',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员表';

DROP TABLE IF EXISTS `{dbprefix}member_online`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_online` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(10) unsigned NOT NULL COMMENT '在线时间',
  PRIMARY KEY (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员在线情况表';

DROP TABLE IF EXISTS `{dbprefix}member_group`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_group` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT '会员组名称',
  `theme` varchar(255) NOT NULL COMMENT '风格目录',
  `template` varchar(50) NOT NULL COMMENT '模板目录',
  `price` decimal(10,2) NOT NULL COMMENT '售价',
  `unit` tinyint(1) unsigned NOT NULL COMMENT '价格单位:1虚拟卡，2金钱',
  `limit` tinyint(1) unsigned NOT NULL COMMENT '售价限制：1月，2半年，3年',
  `overdue` smallint(5) unsigned NOT NULL COMMENT '过期后变成的组',
  `allowregister` tinyint(1) unsigned NOT NULL COMMENT '是否允许会员注册',
  `allowapply` tinyint(1) unsigned NOT NULL COMMENT '是否允许会员申请',
  `allowapply_orther` tinyint(1) unsigned NOT NULL COMMENT '是否允许会员申请其他组',
  `allowspace` tinyint(1) unsigned NOT NULL COMMENT '是否允许会员空间',
  `allowfield` text NOT NULL COMMENT '可用字段，序列化数组格式',
  `spacefield` text NOT NULL COMMENT '空间字段，序列化数组格式',
  `spacedomain` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT '是否启用空间域名',
  `spacetemplate` varchar(50) DEFAULT NULL COMMENT '空间默认模板',
  `displayorder` tinyint(3) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员组表';

REPLACE INTO `{dbprefix}member_group` VALUES(1, '待审核会员', 'default', 'default', 0.00, 1, 1, 0, 0, 0, 0, 0, '', '', 1, 'default', 0);
REPLACE INTO `{dbprefix}member_group` VALUES(2, '快捷登录', 'default', 'default', 0.00, 0, 0, 0, 0, 0, 0, 0, '', '', 1, 'default', 0);
REPLACE INTO `{dbprefix}member_group` VALUES(3, '普通会员', 'default', 'default', 0.00, 1, 1, 3, 0, 1, 0, 1, '', '', 1, 'default', 0);
REPLACE INTO `{dbprefix}member_group` VALUES(4, '商业会员', 'default', 'default', 10.00, 2, 1, 3, 1, 0, 0, 1, '', '', 1, 'default', 0);

DROP TABLE IF EXISTS `{dbprefix}member_level`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_level` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '会员级别名称',
  `stars` tinyint(2) NOT NULL COMMENT '星星数量',
  `experience` int(10) unsigned NOT NULL COMMENT '经验值要求',
  `allowupgrade` tinyint(1) NOT NULL COMMENT '允许自动升级',
  PRIMARY KEY (`id`),
  KEY `experience` (`experience`),
  KEY `groupid` (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员级别表';

REPLACE INTO `{dbprefix}member_level` VALUES(1, 3, '初级', 1, 0, 0);
REPLACE INTO `{dbprefix}member_level` VALUES(2, 3, '中级', 5, 200, 0);
REPLACE INTO `{dbprefix}member_level` VALUES(3, 3, '高级', 10, 500, 0);
REPLACE INTO `{dbprefix}member_level` VALUES(4, 3, '元老', 15, 1000, 0);
REPLACE INTO `{dbprefix}member_level` VALUES(5, 4, '普通', 16, 0, 0);
REPLACE INTO `{dbprefix}member_level` VALUES(6, 4, '银牌', 23, 500, 0);
REPLACE INTO `{dbprefix}member_level` VALUES(7, 4, '金牌', 35, 1000, 0);
REPLACE INTO `{dbprefix}member_level` VALUES(8, 4, '钻石', 55, 2000, 0);

DROP TABLE IF EXISTS `{dbprefix}member_oauth`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_oauth` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '会员uid',
  `oid` varchar(255) NOT NULL COMMENT 'OAuth返回id',
  `oauth` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `expire_at` int(10) unsigned NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员OAuth2授权表';

DROP TABLE IF EXISTS `{dbprefix}member_paylog`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_paylog` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `value` decimal(10,2) NOT NULL COMMENT '价格',
  `type` varchar(20) NOT NULL COMMENT '类型',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态',
  `order` varchar(255) NULL COMMENT '下单详情',
  `module` varchar(30) NOT NULL COMMENT '应用或模块目录',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `inputtime` int(10) unsigned NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `order` (`order`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付记录表';

DROP TABLE IF EXISTS `{dbprefix}member_scorelog`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_scorelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) unsigned NOT NULL COMMENT '积分0,虚拟币1',
  `value` int(10) NOT NULL COMMENT '分数变化值',
  `mark` varchar(50) NOT NULL COMMENT '标记',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `inputtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`),
  KEY `mark` (`mark`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='得分日志';

DROP TABLE IF EXISTS `{dbprefix}member_setting`;
CREATE TABLE IF NOT EXISTS `{dbprefix}member_setting` (
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员属性参数表';

DROP TABLE IF EXISTS `{dbprefix}module`;
CREATE TABLE IF NOT EXISTS `{dbprefix}module` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` text NULL COMMENT '站点划分',
  `dirname` varchar(50) NOT NULL COMMENT '目录名称',
  `share` tinyint(1) unsigned DEFAULT NULL COMMENT '是否共享模块',
  `extend` tinyint(1) unsigned DEFAULT NULL COMMENT '是否是扩展模块',
  `sitemap` tinyint(1) unsigned DEFAULT NULL COMMENT '是否生成地图',
  `setting` text NULL COMMENT '配置信息',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '禁用？',
  `displayorder` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dirname` (`dirname`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模块表';

DROP TABLE IF EXISTS `{dbprefix}site`;
CREATE TABLE IF NOT EXISTS `{dbprefix}site` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '站点名称',
  `domain` varchar(50) NOT NULL COMMENT '站点域名',
  `setting` text NOT NULL COMMENT '站点配置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='站点表';

REPLACE INTO `{dbprefix}linkage` VALUES(1, '中国地区', 0, 'address');
REPLACE INTO `{dbprefix}linkage_data_1` VALUES(1, 1, 0, '0', '地区1', 'diqu1', 0, 0, '1', 0);
REPLACE INTO `{dbprefix}linkage_data_1` VALUES(2, 1, 0, '0', '城市', 'chengshi', 0, 0, '2', 0);
REPLACE INTO `{dbprefix}linkage_data_1` VALUES(3, 1, 0, '0', '北京', 'beijing', 0, 0, '3', 0);
REPLACE INTO `{dbprefix}linkage_data_1` VALUES(4, 1, 0, '0', '洞子口', 'dongzikou', 0, 0, '4', 0);
REPLACE INTO `{dbprefix}linkage_data_1` VALUES(5, 1, 0, '0', '细河区', 'xihequ', 0, 0, '5', 0);

REPLACE INTO `{dbprefix}member_setting` VALUES('member', '');
REPLACE INTO `{dbprefix}member_setting` VALUES('permission', 'a:10:{i:1;a:13:{s:16:\\"login_experience\\";s:1:\\"1\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"1\\";s:12:\\"update_score\\";s:1:\\"0\\";s:10:\\"attachsize\\";s:1:\\"0\\";}i:2;a:14:{s:16:\\"login_experience\\";s:1:\\"5\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"1\\";s:12:\\"update_score\\";s:1:\\"0\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:1:\\"5\\";}s:3:\\"3_1\\";a:15:{s:16:\\"login_experience\\";s:1:\\"5\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"2\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:2:\\"10\\";}s:3:\\"3_2\\";a:15:{s:16:\\"login_experience\\";s:1:\\"5\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"2\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:2:\\"10\\";}s:3:\\"3_3\\";a:15:{s:16:\\"login_experience\\";s:1:\\"5\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:2:\\"10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"2\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:2:\\"20\\";}s:3:\\"3_4\\";a:15:{s:16:\\"login_experience\\";s:1:\\"5\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"3\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:2:\\"30\\";}s:3:\\"4_5\\";a:15:{s:16:\\"login_experience\\";s:2:\\"10\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:2:\\"10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"5\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:2:\\"50\\";}s:3:\\"4_6\\";a:15:{s:16:\\"login_experience\\";s:2:\\"10\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"5\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:2:\\"70\\";}s:3:\\"4_7\\";a:15:{s:16:\\"login_experience\\";s:2:\\"10\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"5\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:3:\\"100\\";}s:3:\\"4_8\\";a:15:{s:16:\\"login_experience\\";s:2:\\"10\\";s:11:\\"login_score\\";s:1:\\"0\\";s:17:\\"avatar_experience\\";s:2:\\"10\\";s:12:\\"avatar_score\\";s:1:\\"0\\";s:19:\\"complete_experience\\";s:2:\\"10\\";s:14:\\"complete_score\\";s:1:\\"0\\";s:15:\\"bang_experience\\";s:2:\\"10\\";s:10:\\"bang_score\\";s:1:\\"0\\";s:14:\\"jie_experience\\";s:3:\\"-10\\";s:9:\\"jie_score\\";s:1:\\"0\\";s:17:\\"update_experience\\";s:1:\\"5\\";s:12:\\"update_score\\";s:1:\\"0\\";s:9:\\"is_upload\\";s:1:\\"1\\";s:11:\\"is_download\\";s:1:\\"1\\";s:10:\\"attachsize\\";s:1:\\"0\\";}}');
REPLACE INTO `{dbprefix}member_setting` VALUES('pay', 'a:2:{s:6:\\"tenpay\\";a:3:{s:4:\\"name\\";s:9:\\"财付通\\";s:2:\\"id\\";s:0:\\"\\";s:3:\\"key\\";s:0:\\"\\";}s:6:\\"alipay\\";a:4:{s:4:\\"name\\";s:9:\\"支付宝\\";s:8:\\"username\\";s:0:\\"\\";s:2:\\"id\\";s:0:\\"\\";s:3:\\"key\\";s:0:\\"\\";}}');
REPLACE INTO `{dbprefix}member_setting` VALUES('space', 'a:9:{s:6:\\"domain\\";s:0:\\"\\";s:4:\\"edit\\";s:1:\\"1\\";s:6:\\"verify\\";s:1:\\"0\\";s:7:\\"rewrite\\";s:1:\\"0\\";s:7:\\"seojoin\\";s:1:\\"_\\";s:5:\\"title\\";s:41:\\"会员空间_FineCMS自助建站平台！\\";s:8:\\"keywords\\";s:0:\\"\\";s:11:\\"description\\";s:0:\\"\\";s:4:\\"flag\\";a:9:{i:1;a:1:{s:4:\\"name\\";s:12:\\"达人空间\\";}i:2;a:1:{s:4:\\"name\\";s:12:\\"推荐空间\\";}i:3;a:1:{s:4:\\"name\\";s:0:\\"\\";}i:4;a:1:{s:4:\\"name\\";s:0:\\"\\";}i:5;a:1:{s:4:\\"name\\";s:0:\\"\\";}i:6;a:1:{s:4:\\"name\\";s:0:\\"\\";}i:7;a:1:{s:4:\\"name\\";s:0:\\"\\";}i:8;a:1:{s:4:\\"name\\";s:0:\\"\\";}i:9;a:1:{s:4:\\"name\\";s:0:\\"\\";}}}');

INSERT INTO `{dbprefix}urlrule` VALUES(1, 0, '单页测试规则', 'a:52:{s:4:\\"page\\";s:14:\\"page-{id}.html\\";s:9:\\"page_page\\";s:21:\\"page-{id}-{page}.html\\";s:6:\\"module\\";s:0:\\"\\";s:4:\\"list\\";s:0:\\"\\";s:9:\\"list_page\\";s:0:\\"\\";s:4:\\"show\\";s:0:\\"\\";s:9:\\"show_page\\";s:0:\\"\\";s:6:\\"extend\\";s:0:\\"\\";s:11:\\"extend_page\\";s:0:\\"\\";s:3:\\"tag\\";s:0:\\"\\";s:8:\\"tag_page\\";s:0:\\"\\";s:6:\\"search\\";s:0:\\"\\";s:11:\\"search_page\\";s:0:\\"\\";s:9:\\"share_tag\\";s:0:\\"\\";s:14:\\"share_tag_page\\";s:0:\\"\\";s:12:\\"share_search\\";s:0:\\"\\";s:17:\\"share_search_page\\";s:0:\\"\\";s:10:\\"share_list\\";s:0:\\"\\";s:15:\\"share_list_page\\";s:0:\\"\\";s:10:\\"share_show\\";s:0:\\"\\";s:15:\\"share_show_page\\";s:0:\\"\\";s:12:\\"share_extend\\";s:0:\\"\\";s:17:\\"share_extend_page\\";s:0:\\"\\";s:9:\\"so_search\\";s:0:\\"\\";s:14:\\"so_search_page\\";s:0:\\"\\";s:7:\\"sitemap\\";s:0:\\"\\";s:5:\\"space\\";s:0:\\"\\";s:12:\\"space_search\\";s:0:\\"\\";s:17:\\"space_search_page\\";s:0:\\"\\";s:5:\\"uhome\\";s:0:\\"\\";s:5:\\"ulist\\";s:0:\\"\\";s:10:\\"ulist_page\\";s:0:\\"\\";s:5:\\"ushow\\";s:0:\\"\\";s:10:\\"ushow_page\\";s:0:\\"\\";s:8:\\"sns_show\\";s:0:\\"\\";s:9:\\"sns_topic\\";s:0:\\"\\";s:14:\\"sns_topic_page\\";s:0:\\"\\";s:3:\\"sns\\";s:0:\\"\\";s:8:\\"sns_page\\";s:0:\\"\\";s:12:\\"ulist_domain\\";s:0:\\"\\";s:17:\\"ulist_domain_page\\";s:0:\\"\\";s:12:\\"ushow_domain\\";s:0:\\"\\";s:17:\\"ushow_domain_page\\";s:0:\\"\\";s:15:\\"sns_show_domain\\";s:0:\\"\\";s:16:\\"sns_topic_domain\\";s:0:\\"\\";s:21:\\"sns_topic_domain_page\\";s:0:\\"\\";s:10:\\"sns_domain\\";s:0:\\"\\";s:15:\\"sns_domain_page\\";s:0:\\"\\";s:6:\\"member\\";s:0:\\"\\";s:10:\\"member_reg\\";s:0:\\"\\";s:12:\\"member_login\\";s:0:\\"\\";s:7:\\"catjoin\\";s:1:\\"/\\";}');
INSERT INTO `{dbprefix}urlrule` VALUES(2, 0, '单页测试规则（用于模块）', 'a:52:{s:4:\\"page\\";s:21:\\"module-page-{id}.html\\";s:9:\\"page_page\\";s:28:\\"module-page-{id}-{page}.html\\";s:6:\\"module\\";s:0:\\"\\";s:4:\\"list\\";s:0:\\"\\";s:9:\\"list_page\\";s:0:\\"\\";s:4:\\"show\\";s:0:\\"\\";s:9:\\"show_page\\";s:0:\\"\\";s:6:\\"extend\\";s:0:\\"\\";s:11:\\"extend_page\\";s:0:\\"\\";s:3:\\"tag\\";s:0:\\"\\";s:8:\\"tag_page\\";s:0:\\"\\";s:6:\\"search\\";s:0:\\"\\";s:11:\\"search_page\\";s:0:\\"\\";s:9:\\"share_tag\\";s:0:\\"\\";s:14:\\"share_tag_page\\";s:0:\\"\\";s:12:\\"share_search\\";s:0:\\"\\";s:17:\\"share_search_page\\";s:0:\\"\\";s:10:\\"share_list\\";s:0:\\"\\";s:15:\\"share_list_page\\";s:0:\\"\\";s:10:\\"share_show\\";s:0:\\"\\";s:15:\\"share_show_page\\";s:0:\\"\\";s:12:\\"share_extend\\";s:0:\\"\\";s:17:\\"share_extend_page\\";s:0:\\"\\";s:9:\\"so_search\\";s:0:\\"\\";s:14:\\"so_search_page\\";s:0:\\"\\";s:7:\\"sitemap\\";s:0:\\"\\";s:5:\\"space\\";s:0:\\"\\";s:12:\\"space_search\\";s:0:\\"\\";s:17:\\"space_search_page\\";s:0:\\"\\";s:5:\\"uhome\\";s:0:\\"\\";s:5:\\"ulist\\";s:0:\\"\\";s:10:\\"ulist_page\\";s:0:\\"\\";s:5:\\"ushow\\";s:0:\\"\\";s:10:\\"ushow_page\\";s:0:\\"\\";s:8:\\"sns_show\\";s:0:\\"\\";s:9:\\"sns_topic\\";s:0:\\"\\";s:14:\\"sns_topic_page\\";s:0:\\"\\";s:3:\\"sns\\";s:0:\\"\\";s:8:\\"sns_page\\";s:0:\\"\\";s:12:\\"ulist_domain\\";s:0:\\"\\";s:17:\\"ulist_domain_page\\";s:0:\\"\\";s:12:\\"ushow_domain\\";s:0:\\"\\";s:17:\\"ushow_domain_page\\";s:0:\\"\\";s:15:\\"sns_show_domain\\";s:0:\\"\\";s:16:\\"sns_topic_domain\\";s:0:\\"\\";s:21:\\"sns_topic_domain_page\\";s:0:\\"\\";s:10:\\"sns_domain\\";s:0:\\"\\";s:15:\\"sns_domain_page\\";s:0:\\"\\";s:6:\\"member\\";s:0:\\"\\";s:10:\\"member_reg\\";s:0:\\"\\";s:12:\\"member_login\\";s:0:\\"\\";s:7:\\"catjoin\\";s:1:\\"/\\";}');
INSERT INTO `{dbprefix}urlrule` VALUES(3, 1, '独立模块默认规则', 'a:52:{s:4:\\"page\\";s:0:\\"\\";s:9:\\"page_page\\";s:0:\\"\\";s:6:\\"module\\";s:14:\\"{modname}.html\\";s:4:\\"list\\";s:29:\\"{modname}-list-{dirname}.html\\";s:9:\\"list_page\\";s:36:\\"{modname}-list-{dirname}-{page}.html\\";s:4:\\"show\\";s:24:\\"{modname}-show-{id}.html\\";s:9:\\"show_page\\";s:31:\\"{modname}-show-{id}-{page}.html\\";s:6:\\"extend\\";s:24:\\"{modname}-read-{id}.html\\";s:11:\\"extend_page\\";s:31:\\"{modname}-read-{id}-{page}.html\\";s:3:\\"tag\\";s:24:\\"{modname}-tag-{tag}.html\\";s:8:\\"tag_page\\";s:31:\\"{modname}-tag-{tag}-{page}.html\\";s:6:\\"search\\";s:21:\\"{modname}/search.html\\";s:11:\\"search_page\\";s:29:\\"{modname}/search-{param}.html\\";s:9:\\"share_tag\\";s:0:\\"\\";s:14:\\"share_tag_page\\";s:0:\\"\\";s:12:\\"share_search\\";s:0:\\"\\";s:17:\\"share_search_page\\";s:0:\\"\\";s:10:\\"share_list\\";s:0:\\"\\";s:15:\\"share_list_page\\";s:0:\\"\\";s:10:\\"share_show\\";s:0:\\"\\";s:15:\\"share_show_page\\";s:0:\\"\\";s:12:\\"share_extend\\";s:0:\\"\\";s:17:\\"share_extend_page\\";s:0:\\"\\";s:9:\\"so_search\\";s:0:\\"\\";s:14:\\"so_search_page\\";s:0:\\"\\";s:7:\\"sitemap\\";s:0:\\"\\";s:5:\\"space\\";s:0:\\"\\";s:12:\\"space_search\\";s:0:\\"\\";s:17:\\"space_search_page\\";s:0:\\"\\";s:5:\\"uhome\\";s:0:\\"\\";s:5:\\"ulist\\";s:0:\\"\\";s:10:\\"ulist_page\\";s:0:\\"\\";s:5:\\"ushow\\";s:0:\\"\\";s:10:\\"ushow_page\\";s:0:\\"\\";s:8:\\"sns_show\\";s:0:\\"\\";s:9:\\"sns_topic\\";s:0:\\"\\";s:14:\\"sns_topic_page\\";s:0:\\"\\";s:3:\\"sns\\";s:0:\\"\\";s:8:\\"sns_page\\";s:0:\\"\\";s:12:\\"ulist_domain\\";s:0:\\"\\";s:17:\\"ulist_domain_page\\";s:0:\\"\\";s:12:\\"ushow_domain\\";s:0:\\"\\";s:17:\\"ushow_domain_page\\";s:0:\\"\\";s:15:\\"sns_show_domain\\";s:0:\\"\\";s:16:\\"sns_topic_domain\\";s:0:\\"\\";s:21:\\"sns_topic_domain_page\\";s:0:\\"\\";s:10:\\"sns_domain\\";s:0:\\"\\";s:15:\\"sns_domain_page\\";s:0:\\"\\";s:6:\\"member\\";s:0:\\"\\";s:10:\\"member_reg\\";s:0:\\"\\";s:12:\\"member_login\\";s:0:\\"\\";s:7:\\"catjoin\\";s:1:\\"/\\";}');
INSERT INTO `{dbprefix}urlrule` VALUES(4, 2, '共享模块测试', 'a:52:{s:4:\\"page\\";s:0:\\"\\";s:9:\\"page_page\\";s:0:\\"\\";s:6:\\"module\\";s:0:\\"\\";s:4:\\"list\\";s:0:\\"\\";s:9:\\"list_page\\";s:0:\\"\\";s:4:\\"show\\";s:0:\\"\\";s:9:\\"show_page\\";s:0:\\"\\";s:6:\\"extend\\";s:0:\\"\\";s:11:\\"extend_page\\";s:0:\\"\\";s:3:\\"tag\\";s:0:\\"\\";s:8:\\"tag_page\\";s:0:\\"\\";s:6:\\"search\\";s:0:\\"\\";s:11:\\"search_page\\";s:0:\\"\\";s:9:\\"share_tag\\";s:24:\\"{modname}-tag-{tag}.html\\";s:14:\\"share_tag_page\\";s:31:\\"{modname}-tag-{tag}-{page}.html\\";s:12:\\"share_search\\";s:21:\\"{modname}/search.html\\";s:17:\\"share_search_page\\";s:29:\\"{modname}/search/{param}.html\\";s:10:\\"share_list\\";s:10:\\"{dirname}/\\";s:15:\\"share_list_page\\";s:26:\\"{dirname}/page/{page}.html\\";s:10:\\"share_show\\";s:24:\\"{dirname}/show/{id}.html\\";s:15:\\"share_show_page\\";s:36:\\"{dirname}/show/{id}/page/{page}.html\\";s:12:\\"share_extend\\";s:24:\\"{dirname}/read/{id}.html\\";s:17:\\"share_extend_page\\";s:36:\\"{dirname}/read/{id}/page/{page}.html\\";s:9:\\"so_search\\";s:0:\\"\\";s:14:\\"so_search_page\\";s:0:\\"\\";s:7:\\"sitemap\\";s:0:\\"\\";s:5:\\"space\\";s:0:\\"\\";s:12:\\"space_search\\";s:0:\\"\\";s:17:\\"space_search_page\\";s:0:\\"\\";s:5:\\"uhome\\";s:0:\\"\\";s:5:\\"ulist\\";s:0:\\"\\";s:10:\\"ulist_page\\";s:0:\\"\\";s:5:\\"ushow\\";s:0:\\"\\";s:10:\\"ushow_page\\";s:0:\\"\\";s:8:\\"sns_show\\";s:0:\\"\\";s:9:\\"sns_topic\\";s:0:\\"\\";s:14:\\"sns_topic_page\\";s:0:\\"\\";s:3:\\"sns\\";s:0:\\"\\";s:8:\\"sns_page\\";s:0:\\"\\";s:12:\\"ulist_domain\\";s:0:\\"\\";s:17:\\"ulist_domain_page\\";s:0:\\"\\";s:12:\\"ushow_domain\\";s:0:\\"\\";s:17:\\"ushow_domain_page\\";s:0:\\"\\";s:15:\\"sns_show_domain\\";s:0:\\"\\";s:16:\\"sns_topic_domain\\";s:0:\\"\\";s:21:\\"sns_topic_domain_page\\";s:0:\\"\\";s:10:\\"sns_domain\\";s:0:\\"\\";s:15:\\"sns_domain_page\\";s:0:\\"\\";s:6:\\"member\\";s:0:\\"\\";s:10:\\"member_reg\\";s:0:\\"\\";s:12:\\"member_login\\";s:0:\\"\\";s:7:\\"catjoin\\";s:1:\\"/\\";}');
INSERT INTO `{dbprefix}urlrule` VALUES(5, 3, '共享栏目规则测试', 'a:52:{s:4:\\"page\\";s:0:\\"\\";s:9:\\"page_page\\";s:0:\\"\\";s:6:\\"module\\";s:0:\\"\\";s:4:\\"list\\";s:0:\\"\\";s:9:\\"list_page\\";s:0:\\"\\";s:4:\\"show\\";s:0:\\"\\";s:9:\\"show_page\\";s:0:\\"\\";s:6:\\"extend\\";s:0:\\"\\";s:11:\\"extend_page\\";s:0:\\"\\";s:3:\\"tag\\";s:0:\\"\\";s:8:\\"tag_page\\";s:0:\\"\\";s:6:\\"search\\";s:0:\\"\\";s:11:\\"search_page\\";s:0:\\"\\";s:9:\\"share_tag\\";s:0:\\"\\";s:14:\\"share_tag_page\\";s:0:\\"\\";s:12:\\"share_search\\";s:0:\\"\\";s:17:\\"share_search_page\\";s:0:\\"\\";s:10:\\"share_list\\";s:24:\\"html/{dirname}-list.html\\";s:15:\\"share_list_page\\";s:31:\\"html/{dirname}-list-{page}.html\\";s:10:\\"share_show\\";s:29:\\"html/{dirname}-show-{id}.html\\";s:15:\\"share_show_page\\";s:36:\\"html/{dirname}-show-{id}-{page}.html\\";s:12:\\"share_extend\\";s:31:\\"html/{dirname}-extend-{id}.html\\";s:17:\\"share_extend_page\\";s:38:\\"html/{dirname}-extend-{id}-{page}.html\\";s:9:\\"so_search\\";s:0:\\"\\";s:14:\\"so_search_page\\";s:0:\\"\\";s:7:\\"sitemap\\";s:0:\\"\\";s:5:\\"space\\";s:0:\\"\\";s:12:\\"space_search\\";s:0:\\"\\";s:17:\\"space_search_page\\";s:0:\\"\\";s:5:\\"uhome\\";s:0:\\"\\";s:5:\\"ulist\\";s:0:\\"\\";s:10:\\"ulist_page\\";s:0:\\"\\";s:5:\\"ushow\\";s:0:\\"\\";s:10:\\"ushow_page\\";s:0:\\"\\";s:8:\\"sns_show\\";s:0:\\"\\";s:9:\\"sns_topic\\";s:0:\\"\\";s:14:\\"sns_topic_page\\";s:0:\\"\\";s:3:\\"sns\\";s:0:\\"\\";s:8:\\"sns_page\\";s:0:\\"\\";s:12:\\"ulist_domain\\";s:0:\\"\\";s:17:\\"ulist_domain_page\\";s:0:\\"\\";s:12:\\"ushow_domain\\";s:0:\\"\\";s:17:\\"ushow_domain_page\\";s:0:\\"\\";s:15:\\"sns_show_domain\\";s:0:\\"\\";s:16:\\"sns_topic_domain\\";s:0:\\"\\";s:21:\\"sns_topic_domain_page\\";s:0:\\"\\";s:10:\\"sns_domain\\";s:0:\\"\\";s:15:\\"sns_domain_page\\";s:0:\\"\\";s:6:\\"member\\";s:0:\\"\\";s:10:\\"member_reg\\";s:0:\\"\\";s:12:\\"member_login\\";s:0:\\"\\";s:7:\\"catjoin\\";s:1:\\"/\\";}');
INSERT INTO `{dbprefix}urlrule` VALUES(6, 4, '站点URL测试', 'a:52:{s:4:\\"page\\";s:0:\\"\\";s:9:\\"page_page\\";s:0:\\"\\";s:6:\\"module\\";s:0:\\"\\";s:4:\\"list\\";s:0:\\"\\";s:9:\\"list_page\\";s:0:\\"\\";s:4:\\"show\\";s:0:\\"\\";s:9:\\"show_page\\";s:0:\\"\\";s:6:\\"extend\\";s:0:\\"\\";s:11:\\"extend_page\\";s:0:\\"\\";s:3:\\"tag\\";s:0:\\"\\";s:8:\\"tag_page\\";s:0:\\"\\";s:6:\\"search\\";s:0:\\"\\";s:11:\\"search_page\\";s:0:\\"\\";s:9:\\"share_tag\\";s:0:\\"\\";s:14:\\"share_tag_page\\";s:0:\\"\\";s:12:\\"share_search\\";s:11:\\"search.html\\";s:17:\\"share_search_page\\";s:19:\\"search/{param}.html\\";s:10:\\"share_list\\";s:0:\\"\\";s:15:\\"share_list_page\\";s:0:\\"\\";s:10:\\"share_show\\";s:0:\\"\\";s:15:\\"share_show_page\\";s:0:\\"\\";s:12:\\"share_extend\\";s:0:\\"\\";s:17:\\"share_extend_page\\";s:0:\\"\\";s:9:\\"so_search\\";s:7:\\"so.html\\";s:14:\\"so_search_page\\";s:15:\\"so-{param}.html\\";s:7:\\"sitemap\\";s:12:\\"sitemap.html\\";s:5:\\"space\\";s:0:\\"\\";s:12:\\"space_search\\";s:0:\\"\\";s:17:\\"space_search_page\\";s:0:\\"\\";s:5:\\"uhome\\";s:0:\\"\\";s:5:\\"ulist\\";s:0:\\"\\";s:10:\\"ulist_page\\";s:0:\\"\\";s:5:\\"ushow\\";s:0:\\"\\";s:10:\\"ushow_page\\";s:0:\\"\\";s:8:\\"sns_show\\";s:0:\\"\\";s:9:\\"sns_topic\\";s:0:\\"\\";s:14:\\"sns_topic_page\\";s:0:\\"\\";s:3:\\"sns\\";s:0:\\"\\";s:8:\\"sns_page\\";s:0:\\"\\";s:12:\\"ulist_domain\\";s:0:\\"\\";s:17:\\"ulist_domain_page\\";s:0:\\"\\";s:12:\\"ushow_domain\\";s:0:\\"\\";s:17:\\"ushow_domain_page\\";s:0:\\"\\";s:15:\\"sns_show_domain\\";s:0:\\"\\";s:16:\\"sns_topic_domain\\";s:0:\\"\\";s:21:\\"sns_topic_domain_page\\";s:0:\\"\\";s:10:\\"sns_domain\\";s:0:\\"\\";s:15:\\"sns_domain_page\\";s:0:\\"\\";s:6:\\"member\\";s:0:\\"\\";s:10:\\"member_reg\\";s:0:\\"\\";s:12:\\"member_login\\";s:0:\\"\\";s:7:\\"catjoin\\";s:1:\\"/\\";}');
INSERT INTO `{dbprefix}urlrule` VALUES(7, 5, '空间黄页测试地址', 'a:50:{s:4:\\"page\\";s:0:\\"\\";s:9:\\"page_page\\";s:0:\\"\\";s:6:\\"module\\";s:0:\\"\\";s:4:\\"list\\";s:0:\\"\\";s:9:\\"list_page\\";s:0:\\"\\";s:4:\\"show\\";s:0:\\"\\";s:9:\\"show_page\\";s:0:\\"\\";s:6:\\"extend\\";s:0:\\"\\";s:11:\\"extend_page\\";s:0:\\"\\";s:3:\\"tag\\";s:0:\\"\\";s:8:\\"tag_page\\";s:0:\\"\\";s:6:\\"search\\";s:0:\\"\\";s:11:\\"search_page\\";s:0:\\"\\";s:9:\\"share_tag\\";s:0:\\"\\";s:14:\\"share_tag_page\\";s:0:\\"\\";s:12:\\"share_search\\";s:0:\\"\\";s:17:\\"share_search_page\\";s:0:\\"\\";s:10:\\"share_list\\";s:0:\\"\\";s:15:\\"share_list_page\\";s:0:\\"\\";s:10:\\"share_show\\";s:0:\\"\\";s:15:\\"share_show_page\\";s:0:\\"\\";s:12:\\"share_extend\\";s:0:\\"\\";s:17:\\"share_extend_page\\";s:0:\\"\\";s:9:\\"so_search\\";s:0:\\"\\";s:14:\\"so_search_page\\";s:0:\\"\\";s:7:\\"sitemap\\";s:0:\\"\\";s:5:\\"space\\";s:7:\\"hy.html\\";s:12:\\"space_search\\";s:14:\\"hy-search.html\\";s:17:\\"space_search_page\\";s:22:\\"hy-search-{param}.html\\";s:5:\\"uhome\\";s:12:\\"u-{uid}.html\\";s:5:\\"ulist\\";s:22:\\"u-{uid}-list-{id}.html\\";s:10:\\"ulist_page\\";s:29:\\"u-{uid}-list-{id}-{page}.html\\";s:5:\\"ushow\\";s:28:\\"u-{uid}-show-{mid}-{id}.html\\";s:10:\\"ushow_page\\";s:35:\\"u-{uid}-show-{mid}-{id}-{page}.html\\";s:8:\\"sns_show\\";s:26:\\"u-{uid}-sns-show-{id}.html\\";s:9:\\"sns_topic\\";s:27:\\"u-{uid}-sns-topic-{id}.html\\";s:14:\\"sns_topic_page\\";s:34:\\"u-{uid}-sns-topic-{id}-{page}.html\\";s:3:\\"sns\\";s:23:\\"u-{uid}-sns-{name}.html\\";s:8:\\"sns_page\\";s:30:\\"u-{uid}-sns-{name}-{page}.html\\";s:12:\\"ulist_domain\\";s:16:\\"u-list-{id}.html\\";s:17:\\"ulist_domain_page\\";s:23:\\"u-list-{id}-{page}.html\\";s:12:\\"ushow_domain\\";s:22:\\"u-show-{mid}-{id}.html\\";s:17:\\"ushow_domain_page\\";s:29:\\"u-show-{mid}-{id}-{page}.html\\";s:15:\\"sns_show_domain\\";s:20:\\"u-sns-show-{id}.html\\";s:16:\\"sns_topic_domain\\";s:21:\\"u-sns-topic-{id}.html\\";s:21:\\"sns_topic_domain_page\\";s:28:\\"u-sns-topic-{id}-{page}.html\\";s:10:\\"sns_domain\\";s:22:\\"u-sns-{name}-{id}.html\\";s:15:\\"sns_domain_page\\";s:29:\\"u-sns-{name}-{id}-{page}.html\\";s:6:\\"member\\";s:0:\\"\\";s:7:\\"catjoin\\";s:1:\\"/\\";}');
INSERT INTO `{dbprefix}urlrule` VALUES(8, 6, '会员部分测试', 'a:52:{s:4:\\"page\\";s:0:\\"\\";s:9:\\"page_page\\";s:0:\\"\\";s:6:\\"module\\";s:0:\\"\\";s:4:\\"list\\";s:0:\\"\\";s:9:\\"list_page\\";s:0:\\"\\";s:4:\\"show\\";s:0:\\"\\";s:9:\\"show_page\\";s:0:\\"\\";s:6:\\"extend\\";s:0:\\"\\";s:11:\\"extend_page\\";s:0:\\"\\";s:3:\\"tag\\";s:0:\\"\\";s:8:\\"tag_page\\";s:0:\\"\\";s:6:\\"search\\";s:0:\\"\\";s:11:\\"search_page\\";s:0:\\"\\";s:9:\\"share_tag\\";s:0:\\"\\";s:14:\\"share_tag_page\\";s:0:\\"\\";s:12:\\"share_search\\";s:0:\\"\\";s:17:\\"share_search_page\\";s:0:\\"\\";s:10:\\"share_list\\";s:0:\\"\\";s:15:\\"share_list_page\\";s:0:\\"\\";s:10:\\"share_show\\";s:0:\\"\\";s:15:\\"share_show_page\\";s:0:\\"\\";s:12:\\"share_extend\\";s:0:\\"\\";s:17:\\"share_extend_page\\";s:0:\\"\\";s:9:\\"so_search\\";s:0:\\"\\";s:14:\\"so_search_page\\";s:0:\\"\\";s:7:\\"sitemap\\";s:0:\\"\\";s:5:\\"space\\";s:0:\\"\\";s:12:\\"space_search\\";s:0:\\"\\";s:17:\\"space_search_page\\";s:0:\\"\\";s:5:\\"uhome\\";s:0:\\"\\";s:5:\\"ulist\\";s:0:\\"\\";s:10:\\"ulist_page\\";s:0:\\"\\";s:5:\\"ushow\\";s:0:\\"\\";s:10:\\"ushow_page\\";s:0:\\"\\";s:8:\\"sns_show\\";s:0:\\"\\";s:9:\\"sns_topic\\";s:0:\\"\\";s:14:\\"sns_topic_page\\";s:0:\\"\\";s:3:\\"sns\\";s:0:\\"\\";s:8:\\"sns_page\\";s:0:\\"\\";s:12:\\"ulist_domain\\";s:0:\\"\\";s:17:\\"ulist_domain_page\\";s:0:\\"\\";s:12:\\"ushow_domain\\";s:0:\\"\\";s:17:\\"ushow_domain_page\\";s:0:\\"\\";s:15:\\"sns_show_domain\\";s:0:\\"\\";s:16:\\"sns_topic_domain\\";s:0:\\"\\";s:21:\\"sns_topic_domain_page\\";s:0:\\"\\";s:10:\\"sns_domain\\";s:0:\\"\\";s:15:\\"sns_domain_page\\";s:0:\\"\\";s:6:\\"member\\";s:11:\\"member.html\\";s:10:\\"member_reg\\";s:13:\\"register.html\\";s:12:\\"member_login\\";s:10:\\"login.html\\";s:7:\\"catjoin\\";s:0:\\"\\";}');
