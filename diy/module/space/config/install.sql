
CREATE TABLE IF NOT EXISTS `{dbprefix}space_access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `uid` int(10) unsigned NOT NULL COMMENT '访客uid',
  `username` varchar(100) NOT NULL COMMENT '访客名',
  `spaceid` int(10) unsigned NOT NULL COMMENT '被访空间',
  `inputtime` int(10) unsigned NOT NULL COMMENT '访问时间',
  `content` text COMMENT '访问信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `my` (`uid`,`spaceid`,`inputtime`),
  KEY `uid` (`uid`,`username`,`spaceid`,`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='空间访问统计表';


CREATE TABLE IF NOT EXISTS `{dbprefix}space_domain` (
  `uid` int(10) NOT NULL,
  `domain` varchar(50) NOT NULL,
  UNIQUE KEY `uid` (`uid`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='空间二级域名';



CREATE TABLE IF NOT EXISTS `{dbprefix}space_category_init` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `gid` mediumint(8) unsigned NOT NULL COMMENT '会员组id',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `pids` varchar(255) DEFAULT NULL COMMENT '所有上级id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '0外链，1模型，2单页',
  `name` varchar(30) NOT NULL COMMENT '栏目名称',
  `link` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `showid` tinyint(1) unsigned NOT NULL COMMENT '0不显示,1顶部,2底部,3都显示',
  `modelid` smallint(5) unsigned NOT NULL COMMENT '模型id',
  `child` tinyint(1) unsigned DEFAULT '0' COMMENT '是否有下级',
  `childids` text DEFAULT NULL COMMENT '下级所有id',
  PRIMARY KEY (`id`),
  KEY `uid` (`gid`),
  KEY `pid` (`pid`),
  KEY `showid` (`showid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='空间栏目表初始化表';


CREATE TABLE IF NOT EXISTS `{dbprefix}space_flag` (
  `flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '标记id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  KEY `flag` (`flag`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标记表';



CREATE TABLE IF NOT EXISTS `{dbprefix}space_category` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '会员uid',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `pids` varchar(255) DEFAULT NULL COMMENT '所有上级id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '0外链，1模型，2单页',
  `name` varchar(30) NOT NULL COMMENT '栏目名称',
  `link` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `body` text DEFAULT NULL COMMENT '单页内容',
  `showid` tinyint(1) unsigned NOT NULL COMMENT '0不显示,1顶部,2底部,3都显示',
  `modelid` smallint(5) unsigned NOT NULL COMMENT '模型id',
  `child` tinyint(1) unsigned DEFAULT NULL DEFAULT '0' COMMENT '是否有下级',
  `childids` text DEFAULT NULL COMMENT '下级所有id',
  `title` varchar(255) NOT NULL COMMENT 'SEO标题',
  `keywords` varchar(255) NOT NULL COMMENT '关键字',
  `description` text NOT NULL COMMENT '描述信息',
  `displayorder` tinyint(3) DEFAULT NULL DEFAULT '0' COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`),
  KEY `showid` (`showid`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目表';


CREATE TABLE IF NOT EXISTS `{dbprefix}space_model` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `table` varchar(50) NOT NULL COMMENT '表名',
  `setting` text NOT NULL COMMENT '配置信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `table` (`table`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员模型表';



CREATE TABLE IF NOT EXISTS `{dbprefix}space` (
  `uid` mediumint(8) unsigned NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '空间名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '空间logo',
  `style` varchar(30) DEFAULT NULL COMMENT '空间风格',
  `title` varchar(255) DEFAULT NULL COMMENT 'SEO标题',
  `keywords` varchar(255) DEFAULT NULL COMMENT 'SEO关键字',
  `description` text DEFAULT NULL COMMENT 'SEO描述',
  `introduction` text DEFAULT NULL COMMENT '空间简介',
  `code` text DEFAULT NULL COMMENT '第三方代码',
  `footer` text DEFAULT NULL COMMENT '底部信息',
  `hits` int(10) unsigned NOT NULL COMMENT '点击量',
  `status` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
  `regtime` int(10) unsigned NOT NULL COMMENT '注册时间',
  `displayorder` tinyint(3) DEFAULT NULL DEFAULT '0' COMMENT '排序值',
  PRIMARY KEY (`uid`),
  KEY `hits` (`hits`),
  KEY `status` (`status`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员空间表';



CREATE TABLE IF NOT EXISTS `{dbprefix}sns_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(10) unsigned NOT NULL COMMENT '动态表id',
  `uid` int(10) unsigned NOT NULL COMMENT '评论人id',
  `username` varchar(50) NOT NULL COMMENT '评论人',
  `comment` text NOT NULL COMMENT '评论内容',
  `inputip` varchar(50) NOT NULL COMMENT '录入ip',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入评论时间',
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`,`uid`,`inputtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='动态评论';


CREATE TABLE IF NOT EXISTS `{dbprefix}sns_config` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员uid',
  `show_all` tinyint(1) unsigned DEFAULT NULL COMMENT '是否全部人可查看我的动态',
  `show_follow` tinyint(1) unsigned DEFAULT NULL COMMENT '是否允许关注我的人访问',
  `show_fans` tinyint(1) unsigned DEFAULT NULL COMMENT '是否允许我的粉丝访问',
  UNIQUE KEY `uid` (`uid`),
  KEY `show_all` (`show_all`),
  KEY `show_fans` (`show_fans`),
  KEY `show_follow` (`show_follow`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员互动配置';


CREATE TABLE IF NOT EXISTS `{dbprefix}sns_feed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '动态ID',
  `uid` int(10) unsigned NOT NULL COMMENT '产生动态的用户UID',
  `username` varchar(100) NOT NULL COMMENT '会员名称',
  `comment` int(10) unsigned DEFAULT '0' COMMENT '评论数',
  `repost` int(10) unsigned DEFAULT '0' COMMENT '分享数',
  `digg` int(10) unsigned DEFAULT '0' COMMENT '赞数量',
  `content` text NOT NULL COMMENT '内容',
  `repost_id` int(10) unsigned NOT NULL COMMENT '转发id',
  `source` varchar(100) NOT NULL COMMENT '来源名称',
  `images` varchar(255) NOT NULL COMMENT '图片',
  `inputip` varchar(50) NOT NULL COMMENT '录入者ip',
  `inputtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ctime` (`inputtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='会员动态表';


CREATE TABLE IF NOT EXISTS `{dbprefix}sns_feed_digg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `fid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='点赞表';

CREATE TABLE IF NOT EXISTS `{dbprefix}sns_feed_favorite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `fid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='收藏表';

CREATE TABLE IF NOT EXISTS `{dbprefix}sns_follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) NOT NULL COMMENT '被关注者ID',
  `username` varchar(50) NOT NULL COMMENT '被关注者名称',
  `gid` int(11) unsigned NOT NULL COMMENT '分组表',
  `fid` int(11) NOT NULL COMMENT '关注者ID',
  `fusername` VARCHAR(50) NOT NULL COMMENT '关注者用户名',
  `isdouble` tinyint(1) unsigned NOT NULL COMMENT '是否互粉',
  `remark` varchar(50) NOT NULL COMMENT '备注',
  `ctime` int(11) NOT NULL COMMENT '关注时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid-fid` (`uid`,`fid`),
  UNIQUE KEY `fid-uid` (`fid`,`uid`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='关注表';

CREATE TABLE IF NOT EXISTS `{dbprefix}sns_follow_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关注组ID',
  `uid` int(11) NOT NULL COMMENT '用户UID',
  `title` varchar(255) NOT NULL COMMENT '组名称',
  `ctime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='关注组表';

CREATE TABLE IF NOT EXISTS `{dbprefix}sns_topic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '话题名称',
  `uid` int(10) NOT NULL COMMENT '发起人',
  `username` varchar(100) NOT NULL COMMENT '发起人名称',
  `count` int(10) unsigned NOT NULL COMMENT '关联动态数量',
  `inputtime` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `uid` (`uid`,`inputtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='话题表';


CREATE TABLE IF NOT EXISTS `{dbprefix}sns_topic_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL COMMENT '话题id',
  `fid` int(10) unsigned NOT NULL COMMENT '动态id',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`,`fid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='话题关联表';


REPLACE INTO `{dbprefix}space_category` (`id`, `uid`, `pid`, `pids`, `type`, `name`, `link`, `body`, `showid`, `modelid`, `child`, `childids`, `title`, `keywords`, `description`, `displayorder`) VALUES(1, 1, 0, '0', 2, '关于我们', '', '', 3, 0, 1, '1,2,3', '', '', '', 0),(2, 1, 1, '0,1', 2, '空间简介', '', '<p>FineCMS v2（简称v2）是一款开源的跨平台网站内容管理系统，以“实用+好用”为基本产品理念，提供从内容发布、组织、传播、互动和数据挖掘的网站一体化解决方案。系统基于CodeIgniter框架，具有良好扩展性和管理性，可以帮助您在各种操作系统与运行环境中搭建各种网站模型而不需要对复杂繁琐的编程语言有太多的专业知识，系统采用UTF-8编码，采取(语言-代码-程序)两两分离的技术模式，全面使用了模板包与语言包结构，为用户的修改提供方便，网站内容的每一个角落都可以在后台予以管理，是一套非常适合用做系统建站或者进行二次开发的程序核心。<br /></p>', 3, 0, 0, '2', '', '', '', 0),(3, 1, 1, '0,1', 2, '联系我们', '', '<p><img src="http://api.map.baidu.com/staticimage?center=104.077889,30.551305&zoom=18&width=530&height=340&markers=104.076658,30.551693" height="340" width="530" /></p><p>扣扣咨询：135977378<br />电子邮箱：finecms@qq.com</p>', 3, 0, 0, '3', '', '', '', 0),(4, 1, 0, '0', 1, '新闻资讯', '', '', 3, 1, 0, '4', '', '', '', 0),(5, 1, 0, '0', 1, '我的日志', '', '', 3, 3, 0, '5', '', '', '', 0),(6, 1, 0, '0', 1, '精彩图片', '', '', 3, 4, 0, '6', '', '', '', 0),(7, 1, 0, '0', 1, '首页幻灯', '', '', 0, 5, 0, '7', '', '', '', 0),(8, 1, 0, '0', 1, '友情链接', '', '', 3, 2, 0, '8', '', '', '', 0),(9, 1, 0, '0', 0, '技术支持', 'http://www.dayrui.com', '', 3, 0, 0, '9', '', '', '0', 0);

INSERT INTO `{dbprefix}field` VALUES(NULL, '名称', 'title', 'Text', 1, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"1\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:34:\\"onblur=\\"get_keywords(\\''keywords\\'');\\"\\";}}', 1);
INSERT INTO `{dbprefix}field` VALUES(NULL, '名称', 'title', 'Text', 2, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"1\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '主题', 'title', 'Text', 3, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"1\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:34:\\"onblur=\\"get_keywords(\\''keywords\\'');\\"\\";}}', 1);
INSERT INTO `{dbprefix}field` VALUES(NULL, '名称', 'title', 'Text', 4, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"1\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:34:\\"onblur=\\"get_keywords(\\''keywords\\'');\\"\\";}}', 1);
INSERT INTO `{dbprefix}field` VALUES(NULL, '内容', 'content', 'Ueditor', 1, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:6:{s:5:\\"width\\";s:3:\\"90%\\";s:6:\\"height\\";s:3:\\"100\\";s:3:\\"key\\";s:0:\\"\\";s:4:\\"mode\\";s:1:\\"2\\";s:4:\\"tool\\";s:29:\\"\\''bold\\'', \\''italic\\'', \\''underline\\''\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"1\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 4);
INSERT INTO `{dbprefix}field` VALUES(NULL, '链接地址', 'link', 'Redirect', 2, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:2:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '内容', 'content', 'Ueditor', 3, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:6:{s:5:\\"width\\";s:3:\\"90%\\";s:6:\\"height\\";s:3:\\"100\\";s:3:\\"key\\";s:0:\\"\\";s:4:\\"mode\\";s:1:\\"2\\";s:4:\\"tool\\";s:29:\\"\\''bold\\'', \\''italic\\'', \\''underline\\''\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 4);
INSERT INTO `{dbprefix}field` VALUES(NULL, '图片集', 'image', 'Files', 4, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:6:{s:5:\\"width\\";s:3:\\"80%\\";s:4:\\"size\\";s:2:\\"10\\";s:5:\\"count\\";s:2:\\"50\\";s:3:\\"ext\\";s:11:\\"gif,png,jpg\\";s:10:\\"uploadpath\\";s:25:\\"{siteid}/photo/{y}{m}{d}/\\";s:3:\\"pan\\";s:1:\\"0\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 3);
INSERT INTO `{dbprefix}field` VALUES(NULL, '简介', 'content', 'Ueditor', 4, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:6:{s:5:\\"width\\";s:3:\\"90%\\";s:6:\\"height\\";s:3:\\"100\\";s:3:\\"key\\";s:0:\\"\\";s:4:\\"mode\\";s:1:\\"2\\";s:4:\\"tool\\";s:29:\\"\\''bold\\'', \\''italic\\'', \\''underline\\''\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 5);
INSERT INTO `{dbprefix}field` VALUES(NULL, '主题', 'title', 'Text', 5, 'space', 1, 1, 1, 1, 1, 0, 'a:2:{s:6:\\"option\\";a:3:{s:5:\\"width\\";i:400;s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:4:{s:3:\\"xss\\";i:1;s:8:\\"required\\";i:1;s:4:\\"tips\\";N;s:9:\\"errortips\\";N;}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '图片', 'image', 'File', 5, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:4:\\"size\\";s:2:\\"10\\";s:3:\\"ext\\";s:11:\\"gif,png,jpg\\";s:10:\\"uploadpath\\";s:0:\\"\\";s:3:\\"pan\\";s:1:\\"0\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '链接地址', 'link', 'Redirect', 5, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:2:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '关键字', 'keywords', 'Text', 1, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:43:\\"多个关键字以小写逗号“,”分隔\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 2);
INSERT INTO `{dbprefix}field` VALUES(NULL, '描述', 'description', 'Textarea', 1, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:3:{s:5:\\"width\\";s:3:\\"500\\";s:6:\\"height\\";s:2:\\"60\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:12:\\"dr_clearhtml\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 3);
INSERT INTO `{dbprefix}field` VALUES(NULL, '关键字', 'keywords', 'Text', 3, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:43:\\"多个关键字以小写逗号“,”分隔\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 2);
INSERT INTO `{dbprefix}field` VALUES(NULL, '描述', 'description', 'Textarea', 3, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:3:{s:5:\\"width\\";s:3:\\"400\\";s:6:\\"height\\";s:2:\\"60\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:12:\\"dr_clearhtml\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 3);
INSERT INTO `{dbprefix}field` VALUES(NULL, '关键字', 'keywords', 'Text', 4, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:43:\\"多个关键字以小写逗号“,”分隔\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 2);
INSERT INTO `{dbprefix}field` VALUES(NULL, '描述', 'description', 'Textarea', 4, 'space', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:3:{s:5:\\"width\\";s:3:\\"400\\";s:6:\\"height\\";s:2:\\"60\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"1\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";N;s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:12:\\"dr_clearhtml\\";s:4:\\"tips\\";N;s:8:\\"formattr\\";s:0:\\"\\";}}', 4);

INSERT INTO `{dbprefix}field` VALUES(NULL, '名称', 'name', 'Text', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:3:{s:5:\\"width\\";i:300;s:9:\\"fieldtype\\";s:7:\\"VARCHAR\\";s:11:\\"fieldlength\\";s:3:\\"255\\";}s:8:\\"validate\\";a:4:{s:3:\\"xss\\";i:1;s:8:\\"required\\";i:1;s:4:\\"tips\\";N;s:9:\\"errortips\\";N;}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, 'Logo', 'logo', 'File', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:3:{s:4:\\"size\\";s:1:\\"2\\";s:3:\\"ext\\";s:11:\\"jpg,gif,png\\";s:10:\\"uploadpath\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";s:0:\\"\\";s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:0:\\"\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '空间简介', 'introduction', 'Ueditor', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:5:{s:5:\\"width\\";s:3:\\"90%\\";s:6:\\"height\\";s:3:\\"200\\";s:4:\\"mode\\";s:1:\\"2\\";s:4:\\"tool\\";s:29:\\"\\''bold\\'', \\''italic\\'', \\''underline\\''\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";s:0:\\"\\";s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:0:\\"\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, 'SEO标题', 'title', 'Text', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:0:\\"\\";s:11:\\"fieldlength\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";s:0:\\"\\";s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:0:\\"\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, 'SEO关键字', 'keywords', 'Text', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:4:{s:5:\\"width\\";s:3:\\"400\\";s:5:\\"value\\";s:0:\\"\\";s:9:\\"fieldtype\\";s:0:\\"\\";s:11:\\"fieldlength\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";s:0:\\"\\";s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:0:\\"\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, 'SEO描述信息', 'description', 'Textarea', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:3:{s:5:\\"width\\";s:3:\\"500\\";s:6:\\"height\\";s:3:\\"100\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";s:0:\\"\\";s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:0:\\"\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '第三方代码', 'code', 'Textarea', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:3:{s:5:\\"width\\";s:3:\\"500\\";s:6:\\"height\\";s:3:\\"100\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";s:0:\\"\\";s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:0:\\"\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 0);
INSERT INTO `{dbprefix}field` VALUES(NULL, '底部信息', 'footer', 'Ueditor', 0, 'spacetable', 1, 1, 1, 1, 0, 0, 'a:2:{s:6:\\"option\\";a:5:{s:5:\\"width\\";s:3:\\"90%\\";s:6:\\"height\\";s:3:\\"200\\";s:4:\\"mode\\";s:1:\\"2\\";s:4:\\"tool\\";s:29:\\"\\''bold\\'', \\''italic\\'', \\''underline\\''\\";s:5:\\"value\\";s:0:\\"\\";}s:8:\\"validate\\";a:9:{s:3:\\"xss\\";s:1:\\"0\\";s:8:\\"required\\";s:1:\\"0\\";s:7:\\"pattern\\";s:0:\\"\\";s:9:\\"errortips\\";s:0:\\"\\";s:6:\\"isedit\\";s:1:\\"0\\";s:5:\\"check\\";s:0:\\"\\";s:6:\\"filter\\";s:0:\\"\\";s:4:\\"tips\\";s:0:\\"\\";s:8:\\"formattr\\";s:0:\\"\\";}}', 0);

REPLACE INTO `{dbprefix}space_model` VALUES(1, '文章', 'news', 'a:8:{s:3:\\"3_1\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"1\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_2\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"2\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_3\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"3\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_4\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"4\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_5\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"5\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_6\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"6\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_7\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"7\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_8\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"8\\";s:5:\\"score\\";s:0:\\"\\";}}');
REPLACE INTO `{dbprefix}space_model` VALUES(2, '外链', 'link', 'a:8:{s:3:\\"3_1\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"1\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_2\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"2\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_3\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"3\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_4\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"4\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_5\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"5\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_6\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"6\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_7\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"7\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_8\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"8\\";s:5:\\"score\\";s:0:\\"\\";}}');
REPLACE INTO `{dbprefix}space_model` VALUES(3, '日志', 'log', 'a:8:{s:3:\\"3_1\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"1\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_2\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"2\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_3\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"3\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_4\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"4\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_5\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"5\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_6\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"6\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_7\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"7\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_8\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"8\\";s:5:\\"score\\";s:0:\\"\\";}}');
REPLACE INTO `{dbprefix}space_model` VALUES(4, '相册', 'photo', 'a:8:{s:3:\\"3_1\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"1\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_2\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"2\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_3\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"3\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_4\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"4\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_5\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"1\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_6\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"2\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_7\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"3\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_8\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:1:\\"4\\";s:5:\\"score\\";s:0:\\"\\";}}');
REPLACE INTO `{dbprefix}space_model` VALUES(5, '幻灯', 'slides', 'a:8:{s:3:\\"3_1\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_2\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_3\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"3_4\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_5\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_6\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_7\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}s:3:\\"4_8\\";a:3:{s:3:\\"use\\";s:1:\\"1\\";s:10:\\"experience\\";s:0:\\"\\";s:5:\\"score\\";s:0:\\"\\";}}');

DROP TABLE IF EXISTS `{dbprefix}space_link`;
CREATE TABLE IF NOT EXISTS `{dbprefix}space_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `hits` int(10) unsigned NOT NULL COMMENT '点击量',
  `status` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `hits` (`hits`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `updatetime` (`updatetime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员空间外链模型表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{dbprefix}space_log`;
CREATE TABLE IF NOT EXISTS `{dbprefix}space_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键词',
  `description` text DEFAULT NULL COMMENT '描述',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `hits` int(10) unsigned NOT NULL COMMENT '点击量',
  `status` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `content` mediumtext,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `hits` (`hits`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `updatetime` (`updatetime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员空间日志模型表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{dbprefix}space_news`;
CREATE TABLE IF NOT EXISTS `{dbprefix}space_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键词',
  `description` text DEFAULT NULL COMMENT '描述',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `hits` int(10) unsigned NOT NULL COMMENT '点击量',
  `status` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `content` mediumtext,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `hits` (`hits`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `updatetime` (`updatetime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员空间单页模型表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{dbprefix}space_photo`;
CREATE TABLE IF NOT EXISTS `{dbprefix}space_photo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键词',
  `description` text DEFAULT NULL COMMENT '描述',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `hits` int(10) unsigned NOT NULL COMMENT '点击量',
  `status` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `image` text,
  `content` mediumtext,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `hits` (`hits`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `updatetime` (`updatetime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员空间相册模型表' AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{dbprefix}space_slides`;
CREATE TABLE IF NOT EXISTS `{dbprefix}space_slides` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `hits` int(10) unsigned NOT NULL COMMENT '点击量',
  `status` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `hits` (`hits`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `updatetime` (`updatetime`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员空间幻灯模型表' AUTO_INCREMENT=1;

REPLACE INTO `{dbprefix}space_slides` (`id`, `catid`, `title`, `uid`, `author`, `hits`, `status`, `inputtime`, `updatetime`, `displayorder`, `image`, `link`) VALUES (1, 7, '应用开放平台', 1, 'admin', 0, 1, 1377949237, 1377949237, 0, 'http://www.dayrui.com/statics/admin/images/index_banner1.jpg', 'http://store.dayrui.com'),(2, 7, '群站多语言管理', 1, 'admin', 0, 1, 1377949258, 1377949258, 0, 'http://www.dayrui.com/dayrui/statics/dayrui/images/index_banner2.jpg', 'http://www.dayrui.com/cms/'),(3, 7, 'FineCMS 一套神奇的系统', 1, 'admin', 0, 1, 1377949306, 1377949306, 0, 'http://www.dayrui.com/dayrui/statics/dayrui/images/index_banner3.jpg', 'http://www.dayrui.com/');

REPLACE INTO `{dbprefix}space` (`uid`, `name`, `logo`, `style`, `title`, `keywords`, `description`, `introduction`, `code`, `footer`, `hits`, `status`, `regtime`) VALUES
(1, 'FineCMS设计室', 'http://www.dayrui.com/assets/logo.png', 'default', 'FineCMS设计室-专业技术团队', 'FineCMS,网站建设,内容管理系统', 'FineCMS v2（简称v2）是一款开源的跨平台网站内容管理系统，以“实用+好用”为基本产品理念，提供从内容发布、组织、传播、互动和数据挖掘的网站一体化解决方案', '<p>FineCMS v2（简称v2）是一款开源的跨平台网站内容管理系统，以“实用+好用”为基本产品理念，提供从内容发布、组织、传播、互动和数据挖掘的网站一体化解决方案。系统基于CodeIgniter框架，具有良好扩展性和管理性，可以帮助您在各种操作系统与运行环境中搭建各种网站模型而不需要对复杂繁琐的编程语言有太多的专业知识，系统采用UTF-8编码，采取(语言-代码-程序)两两分离的技术模式，全面使用了模板包与语言包结构，为用户的修改提供方便，网站内容的每一个角落都可以在后台予以管理，是一套非常适合用做系统建站或者进行二次开发的程序核心。</p>', '<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cdiv id=''cnzz_stat_icon_5629330''%3E%3C/div%3E%3Cscript src=''" + cnzz_protocol + "s9.cnzz.com/stat.php%3Fid%3D5629330%26show%3Dpic2'' type=''text/javascript''%3E%3C/script%3E"));</script>', '<p>扣扣咨询：135977378 电子邮箱：finecms@qq.com</p>某某某公司版权所有，ICP备案0000001<p><br /></p>', 888888, 1, 1377949585);


REPLACE INTO `{dbprefix}space_flag` (`flag`, `uid`) VALUES (1, 1), (2, 1);

REPLACE INTO `{dbprefix}space_category_init` VALUES(1, 1, 0, '0', 2, '关于我们', '', 3, 0, 1, '1,2,3');
REPLACE INTO `{dbprefix}space_category_init` VALUES(2, 1, 1, '0,1', 2, '空间简介', '', 3, 0, 0, '2');
REPLACE INTO `{dbprefix}space_category_init` VALUES(3, 1, 1, '0,1', 2, '联系我们', '', 3, 0, 0, '3');
REPLACE INTO `{dbprefix}space_category_init` VALUES(4, 1, 0, '0', 1, '新闻资讯', '', 3, 1, 0, '4');
REPLACE INTO `{dbprefix}space_category_init` VALUES(5, 1, 0, '0', 1, '我的日志', '', 3, 3, 0, '5');
REPLACE INTO `{dbprefix}space_category_init` VALUES(6, 1, 0, '0', 1, '精彩图片', '', 3, 4, 0, '6');
REPLACE INTO `{dbprefix}space_category_init` VALUES(7, 1, 0, '0', 1, '首页幻灯', '', 0, 5, 0, '7');
REPLACE INTO `{dbprefix}space_category_init` VALUES(8, 1, 0, '0', 1, '友情链接', '', 3, 2, 0, '8');
REPLACE INTO `{dbprefix}space_category_init` VALUES(9, 1, 0, '0', 0, '技术支持', 'http://www.dayrui.com', 3, 0, 0, '9');
REPLACE INTO `{dbprefix}space_category_init` VALUES(10, 2, 0, '0', 2, '关于我们', '', 3, 0, 1, '10,11,12');
REPLACE INTO `{dbprefix}space_category_init` VALUES(11, 2, 10, '0,10', 2, '空间简介', '', 3, 0, 0, '11');
REPLACE INTO `{dbprefix}space_category_init` VALUES(12, 2, 10, '0,10', 2, '联系我们', '', 3, 0, 0, '12');
REPLACE INTO `{dbprefix}space_category_init` VALUES(13, 2, 0, '0', 1, '新闻资讯', '', 3, 1, 0, '13');
REPLACE INTO `{dbprefix}space_category_init` VALUES(14, 2, 0, '0', 1, '我的日志', '', 3, 3, 0, '14');
REPLACE INTO `{dbprefix}space_category_init` VALUES(15, 2, 0, '0', 1, '精彩图片', '', 3, 4, 0, '15');
REPLACE INTO `{dbprefix}space_category_init` VALUES(16, 2, 0, '0', 1, '首页幻灯', '', 0, 5, 0, '16');
REPLACE INTO `{dbprefix}space_category_init` VALUES(17, 2, 0, '0', 1, '友情链接', '', 3, 2, 0, '17');
REPLACE INTO `{dbprefix}space_category_init` VALUES(18, 2, 0, '0', 0, '技术支持', 'http://www.dayrui.com', 3, 0, 0, '18');
REPLACE INTO `{dbprefix}space_category_init` VALUES(19, 3, 0, '0', 2, '关于我们', '', 3, 0, 1, '19,20,21');
REPLACE INTO `{dbprefix}space_category_init` VALUES(20, 3, 19, '0,19', 2, '空间简介', '', 3, 0, 0, '20');
REPLACE INTO `{dbprefix}space_category_init` VALUES(21, 3, 19, '0,19', 2, '联系我们', '', 3, 0, 0, '21');
REPLACE INTO `{dbprefix}space_category_init` VALUES(22, 3, 0, '0', 1, '新闻资讯', '', 3, 1, 0, '22');
REPLACE INTO `{dbprefix}space_category_init` VALUES(23, 3, 0, '0', 1, '我的日志', '', 3, 3, 0, '23');
REPLACE INTO `{dbprefix}space_category_init` VALUES(24, 3, 0, '0', 1, '精彩图片', '', 3, 4, 0, '24');
REPLACE INTO `{dbprefix}space_category_init` VALUES(25, 3, 0, '0', 1, '首页幻灯', '', 0, 5, 0, '25');
REPLACE INTO `{dbprefix}space_category_init` VALUES(26, 3, 0, '0', 1, '友情链接', '', 3, 2, 0, '26');
REPLACE INTO `{dbprefix}space_category_init` VALUES(27, 3, 0, '0', 0, '技术支持', 'http://www.dayrui.com', 3, 0, 0, '27');
REPLACE INTO `{dbprefix}space_category_init` VALUES(28, 4, 0, '0', 2, '关于我们', '', 3, 0, 1, '28,29,30');
REPLACE INTO `{dbprefix}space_category_init` VALUES(29, 4, 28, '0,28', 2, '空间简介', '', 3, 0, 0, '29');
REPLACE INTO `{dbprefix}space_category_init` VALUES(30, 4, 28, '0,28', 2, '联系我们', '', 3, 0, 0, '30');
REPLACE INTO `{dbprefix}space_category_init` VALUES(31, 4, 0, '0', 1, '新闻资讯', '', 3, 1, 0, '31');
REPLACE INTO `{dbprefix}space_category_init` VALUES(32, 4, 0, '0', 1, '我的日志', '', 3, 3, 0, '32');
REPLACE INTO `{dbprefix}space_category_init` VALUES(33, 4, 0, '0', 1, '精彩图片', '', 3, 4, 0, '33');
REPLACE INTO `{dbprefix}space_category_init` VALUES(34, 4, 0, '0', 1, '首页幻灯', '', 0, 5, 0, '34');
REPLACE INTO `{dbprefix}space_category_init` VALUES(35, 4, 0, '0', 1, '友情链接', '', 3, 2, 0, '35');
REPLACE INTO `{dbprefix}space_category_init` VALUES(36, 4, 0, '0', 0, '技术支持', 'http://www.dayrui.com', 3, 0, 0, '36');


REPLACE INTO `{dbprefix}member_menu` VALUES(NULL , 26, '文章管理', 'space/space1/index', '', 'space-1',0, 0, 0,'fa fa-navicon');
REPLACE INTO `{dbprefix}member_menu` VALUES(NULL, 26, '外链管理', 'space/space2/index', '', 'space-2',0, 0, 0,'fa fa-navicon');
REPLACE INTO `{dbprefix}member_menu` VALUES(NULL, 26, '日志管理', 'space/space3/index', '', 'space-3',0, 0, 0,'fa fa-navicon');
REPLACE INTO `{dbprefix}member_menu` VALUES(NULL, 26, '相册管理', 'space/space4/index', '', 'space-4',0, 0, 0,'fa fa-navicon');
REPLACE INTO `{dbprefix}member_menu` VALUES(NULL, 26, '幻灯管理', 'space/space5/index', '', 'space-5',0, 0, 0,'fa fa-navicon');


REPLACE INTO `{dbprefix}admin_menu` VALUES(NULL, 17, '文章管理', 'space/admin/content/index/mid/1', '', 'space-1',0, 6,'icon-reorder');
REPLACE INTO `{dbprefix}admin_menu` VALUES(NULL, 17, '外链管理', 'space/admin/content/index/mid/2', '', 'space-2',0, 7,'icon-reorder');
REPLACE INTO `{dbprefix}admin_menu` VALUES(NULL, 17, '日志管理', 'space/admin/content/index/mid/3', '', 'space-3',0, 8,'icon-reorder');
REPLACE INTO `{dbprefix}admin_menu` VALUES(NULL, 17, '相册管理', 'space/admin/content/index/mid/4', '', 'space-4',0, 9,'icon-reorder');
REPLACE INTO `{dbprefix}admin_menu` VALUES(NULL, 17, '幻灯管理', 'space/admin/content/index/mid/5', '', 'space-5',0, 10,'icon-reorder');
