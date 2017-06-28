/*
Navicat MySQL Data Transfer

Source Server         : PHPstudy
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : poscms

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-06-28 17:50:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dr_1_block
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_block`;
CREATE TABLE `dr_1_block` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '名称',
  `code` varchar(100) NOT NULL COMMENT '别名',
  `content` text NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='资料块表';

-- ----------------------------
-- Records of dr_1_block
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_form
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_form`;
CREATE TABLE `dr_1_form` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `table` varchar(50) NOT NULL COMMENT '表名',
  `setting` text COMMENT '配置信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `table` (`table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='表单模型表';

-- ----------------------------
-- Records of dr_1_form
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_navigator
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_navigator`;
CREATE TABLE `dr_1_navigator` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) unsigned NOT NULL COMMENT '上级id',
  `pids` text COMMENT '所有上级id数据项',
  `type` tinyint(1) unsigned NOT NULL COMMENT '导航类型',
  `name` varchar(255) NOT NULL COMMENT '导航名称',
  `title` varchar(255) NOT NULL COMMENT 'seo标题',
  `url` varchar(255) NOT NULL COMMENT '导航地址',
  `thumb` varchar(255) NOT NULL COMMENT '图片标示',
  `show` tinyint(1) unsigned NOT NULL COMMENT '显示',
  `mark` varchar(255) DEFAULT NULL COMMENT '类型标示',
  `extend` tinyint(1) unsigned DEFAULT NULL COMMENT '是否继承下级',
  `child` tinyint(1) unsigned NOT NULL COMMENT '是否有下级',
  `childids` text COMMENT '所有下级数据项',
  `target` tinyint(1) unsigned NOT NULL COMMENT '是否站外链接',
  `displayorder` tinyint(3) NOT NULL COMMENT '显示顺序',
  PRIMARY KEY (`id`),
  KEY `list` (`id`,`type`,`show`,`displayorder`),
  KEY `mark` (`mark`),
  KEY `extend` (`extend`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='网站导航表';

-- ----------------------------
-- Records of dr_1_navigator
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news`;
CREATE TABLE `dr_1_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `title` varchar(255) DEFAULT NULL COMMENT '主题',
  `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `keywords` varchar(255) DEFAULT NULL COMMENT '关键字',
  `description` text COMMENT '描述',
  `hits` mediumint(8) unsigned DEFAULT NULL COMMENT '浏览数',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者id',
  `author` varchar(50) NOT NULL COMMENT '作者名称',
  `status` tinyint(2) NOT NULL COMMENT '状态',
  `url` varchar(255) DEFAULT NULL COMMENT '地址',
  `link_id` int(10) NOT NULL DEFAULT '0' COMMENT '同步id',
  `tableid` smallint(5) unsigned NOT NULL COMMENT '附表id',
  `inputip` varchar(15) DEFAULT NULL COMMENT '录入者ip',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  `comments` int(10) unsigned NOT NULL COMMENT '评论数量',
  `favorites` int(10) unsigned NOT NULL COMMENT '收藏数量',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`,`updatetime`),
  KEY `link_id` (`link_id`),
  KEY `comments` (`comments`),
  KEY `favorites` (`favorites`),
  KEY `status` (`status`),
  KEY `hits` (`hits`),
  KEY `displayorder` (`displayorder`,`updatetime`)
) ENGINE=MyISAM AUTO_INCREMENT=207 DEFAULT CHARSET=utf8 COMMENT='主表';

-- ----------------------------
-- Records of dr_1_news
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_buy
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_buy`;
CREATE TABLE `dr_1_news_buy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cid` int(10) unsigned NOT NULL COMMENT '文档id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `thumb` varchar(255) NOT NULL COMMENT '缩略图',
  `url` varchar(255) NOT NULL COMMENT 'URL地址',
  `score` int(10) unsigned NOT NULL COMMENT '使用虚拟币',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`uid`,`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='主题购买记录表';

-- ----------------------------
-- Records of dr_1_news_buy
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_category
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_category`;
CREATE TABLE `dr_1_news_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `pids` varchar(255) NOT NULL COMMENT '所有上级id',
  `name` varchar(30) NOT NULL COMMENT '栏目名称',
  `letter` char(1) NOT NULL COMMENT '首字母',
  `dirname` varchar(30) NOT NULL COMMENT '栏目目录',
  `pdirname` varchar(100) NOT NULL COMMENT '上级目录',
  `child` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有下级',
  `childids` text NOT NULL COMMENT '下级所有id',
  `thumb` varchar(255) NOT NULL COMMENT '栏目图片',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `permission` text COMMENT '会员权限',
  `setting` text NOT NULL COMMENT '属性配置',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `show` (`show`),
  KEY `module` (`pid`,`displayorder`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目表';

-- ----------------------------
-- Records of dr_1_news_category
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_category_data
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_category_data`;
CREATE TABLE `dr_1_news_category_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目附加表';

-- ----------------------------
-- Records of dr_1_news_category_data
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_category_data_0
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_category_data_0`;
CREATE TABLE `dr_1_news_category_data_0` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目附加表';

-- ----------------------------
-- Records of dr_1_news_category_data_0
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_comment_data_0
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_comment_data_0`;
CREATE TABLE `dr_1_news_comment_data_0` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论ID',
  `cid` int(10) unsigned NOT NULL COMMENT '关联id',
  `uid` mediumint(8) unsigned DEFAULT '0' COMMENT '会员ID',
  `url` varchar(250) DEFAULT NULL COMMENT '主题地址',
  `title` varchar(250) DEFAULT NULL COMMENT '主题名称',
  `author` varchar(250) DEFAULT NULL COMMENT '评论者',
  `content` text COMMENT '评论内容',
  `support` int(10) unsigned DEFAULT '0' COMMENT '支持数',
  `oppose` int(10) unsigned DEFAULT '0' COMMENT '反对数',
  `avgsort` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '平均分',
  `sort1` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort2` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort3` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort4` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort5` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort6` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort7` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort8` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `sort9` tinyint(1) unsigned DEFAULT '0' COMMENT '评分值',
  `reply` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复id',
  `in_reply` tinyint(1) unsigned DEFAULT '0' COMMENT '是否存在回复',
  `status` smallint(1) unsigned DEFAULT '0' COMMENT '审核状态',
  `inputip` varchar(50) DEFAULT NULL COMMENT '录入者ip',
  `inputtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `cid` (`cid`),
  KEY `reply` (`reply`),
  KEY `support` (`support`),
  KEY `oppose` (`oppose`),
  KEY `avgsort` (`avgsort`),
  KEY `status` (`status`),
  KEY `aa` (`cid`,`status`,`inputtime`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论内容表';

-- ----------------------------
-- Records of dr_1_news_comment_data_0
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_comment_index
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_comment_index`;
CREATE TABLE `dr_1_news_comment_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
  `support` int(10) unsigned DEFAULT '0' COMMENT '支持数',
  `oppose` int(10) unsigned DEFAULT '0' COMMENT '反对数',
  `comments` int(10) unsigned DEFAULT '0' COMMENT '评论数',
  `avgsort` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '平均分',
  `sort1` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort2` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort3` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort4` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort5` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort6` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort7` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort8` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `sort9` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '选项分数',
  `tableid` smallint(5) unsigned DEFAULT '0' COMMENT '附表id',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `support` (`support`),
  KEY `oppose` (`oppose`),
  KEY `comments` (`comments`),
  KEY `avgsort` (`avgsort`),
  KEY `tableid` (`tableid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论索引表';

-- ----------------------------
-- Records of dr_1_news_comment_index
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_comment_my
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_comment_my`;
CREATE TABLE `dr_1_news_comment_my` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid',
  `title` varchar(250) DEFAULT NULL COMMENT '内容标题',
  `url` varchar(250) DEFAULT NULL COMMENT 'URL地址',
  `comments` int(10) unsigned DEFAULT '0' COMMENT '评论数量',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `comments` (`comments`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='我的评论表';

-- ----------------------------
-- Records of dr_1_news_comment_my
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_data_0
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_data_0`;
CREATE TABLE `dr_1_news_data_0` (
  `id` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `content` mediumtext COMMENT '内容',
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附表';

-- ----------------------------
-- Records of dr_1_news_data_0
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_draft
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_draft`;
CREATE TABLE `dr_1_news_draft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
  `eid` int(10) DEFAULT NULL COMMENT '扩展id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `content` mediumtext NOT NULL COMMENT '具体内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `eid` (`eid`),
  KEY `uid` (`uid`),
  KEY `cid` (`cid`),
  KEY `catid` (`catid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容草稿表';

-- ----------------------------
-- Records of dr_1_news_draft
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_favorite
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_favorite`;
CREATE TABLE `dr_1_news_favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cid` int(10) unsigned NOT NULL COMMENT '文档id',
  `eid` int(10) unsigned DEFAULT NULL COMMENT '扩展id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid',
  `url` varchar(255) NOT NULL COMMENT 'URL地址',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `cid` (`cid`),
  KEY `eid` (`eid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收藏夹表';

-- ----------------------------
-- Records of dr_1_news_favorite
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_flag
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_flag`;
CREATE TABLE `dr_1_news_flag` (
  `flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '文档标记id',
  `id` int(10) unsigned NOT NULL COMMENT '文档内容id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
  KEY `flag` (`flag`,`id`,`uid`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标记表';

-- ----------------------------
-- Records of dr_1_news_flag
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_hits
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_hits`;
CREATE TABLE `dr_1_news_hits` (
  `id` int(10) unsigned NOT NULL COMMENT '文章id',
  `hits` int(10) unsigned NOT NULL COMMENT '总点击数',
  `day_hits` int(10) unsigned NOT NULL COMMENT '本日点击',
  `week_hits` int(10) unsigned NOT NULL COMMENT '本周点击',
  `month_hits` int(10) unsigned NOT NULL COMMENT '本月点击',
  `year_hits` int(10) unsigned NOT NULL COMMENT '年点击量',
  UNIQUE KEY `id` (`id`),
  KEY `day_hits` (`day_hits`),
  KEY `week_hits` (`week_hits`),
  KEY `month_hits` (`month_hits`),
  KEY `year_hits` (`year_hits`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='时段点击量统计';

-- ----------------------------
-- Records of dr_1_news_hits
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_html
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_html`;
CREATE TABLE `dr_1_news_html` (
  `id` bigint(18) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL COMMENT '相关id',
  `cid` int(10) unsigned NOT NULL COMMENT '内容id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `type` tinyint(1) unsigned NOT NULL COMMENT '文件类型',
  `catid` tinyint(3) unsigned NOT NULL COMMENT '栏目id',
  `filepath` text NOT NULL COMMENT '文件地址',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `rid` (`rid`),
  KEY `cid` (`cid`),
  KEY `type` (`type`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='html文件存储表';

-- ----------------------------
-- Records of dr_1_news_html
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_index
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_index`;
CREATE TABLE `dr_1_news_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `status` tinyint(2) NOT NULL COMMENT '审核状态',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容索引表';

-- ----------------------------
-- Records of dr_1_news_index
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_search
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_search`;
CREATE TABLE `dr_1_news_search` (
  `id` varchar(32) NOT NULL,
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `params` text NOT NULL COMMENT '参数数组',
  `keyword` varchar(255) NOT NULL COMMENT '关键字',
  `contentid` mediumtext NOT NULL COMMENT 'id集合',
  `inputtime` int(10) unsigned NOT NULL COMMENT '搜索时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `catid` (`catid`),
  KEY `keyword` (`keyword`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='搜索表';

-- ----------------------------
-- Records of dr_1_news_search
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_search_index
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_search_index`;
CREATE TABLE `dr_1_news_search_index` (
  `id` varchar(32) NOT NULL,
  `cid` int(10) unsigned NOT NULL COMMENT '文档Id',
  `inputtime` int(10) unsigned NOT NULL COMMENT '搜索时间',
  KEY `id` (`id`),
  KEY `cid` (`cid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='搜索索引表';

-- ----------------------------
-- Records of dr_1_news_search_index
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_tag
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_tag`;
CREATE TABLE `dr_1_news_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL COMMENT 'tag名称',
  `code` varchar(200) NOT NULL COMMENT 'tag代码（拼音）',
  `hits` mediumint(8) unsigned NOT NULL COMMENT '点击量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `letter` (`code`,`hits`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tag标签表';

-- ----------------------------
-- Records of dr_1_news_tag
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_news_verify
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_news_verify`;
CREATE TABLE `dr_1_news_verify` (
  `id` int(10) unsigned NOT NULL,
  `catid` smallint(5) unsigned NOT NULL COMMENT '栏目id',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
  `author` varchar(50) NOT NULL COMMENT '作者',
  `status` tinyint(2) NOT NULL COMMENT '审核状态',
  `content` mediumtext NOT NULL COMMENT '具体内容',
  `backuid` mediumint(8) unsigned NOT NULL COMMENT '操作人uid',
  `backinfo` text NOT NULL COMMENT '操作退回信息',
  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
  UNIQUE KEY `id` (`id`),
  KEY `uid` (`uid`),
  KEY `catid` (`catid`),
  KEY `status` (`status`),
  KEY `inputtime` (`inputtime`),
  KEY `backuid` (`backuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='内容审核表';

-- ----------------------------
-- Records of dr_1_news_verify
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_page
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_page`;
CREATE TABLE `dr_1_page` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(20) NOT NULL COMMENT '模块dir',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `pids` varchar(255) NOT NULL COMMENT '所有上级id',
  `name` varchar(255) NOT NULL COMMENT '单页名称',
  `dirname` varchar(30) NOT NULL COMMENT '栏目目录',
  `pdirname` varchar(100) NOT NULL COMMENT '上级目录',
  `child` tinyint(1) unsigned NOT NULL COMMENT '是否有子类',
  `childids` varchar(255) NOT NULL COMMENT '下级所有id',
  `thumb` varchar(255) NOT NULL COMMENT '缩略图',
  `title` varchar(255) NOT NULL COMMENT 'seo标题',
  `keywords` varchar(255) NOT NULL COMMENT 'seo关键字',
  `description` varchar(255) NOT NULL COMMENT 'seo描述',
  `content` mediumtext COMMENT '单页内容',
  `attachment` text COMMENT '附件信息',
  `template` varchar(30) NOT NULL COMMENT '模板文件',
  `urlrule` smallint(5) unsigned DEFAULT NULL COMMENT 'url规则id',
  `urllink` varchar(255) NOT NULL COMMENT 'url外链',
  `getchild` tinyint(1) unsigned NOT NULL COMMENT '将下级第一个菜单作为当前菜单',
  `show` tinyint(1) unsigned NOT NULL COMMENT '是否显示在菜单',
  `url` varchar(255) NOT NULL COMMENT 'url地址',
  `setting` mediumtext NOT NULL COMMENT '自定义内容',
  `displayorder` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`module`),
  KEY `pid` (`pid`),
  KEY `show` (`show`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='单页表';

-- ----------------------------
-- Records of dr_1_page
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_remote
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_remote`;
CREATE TABLE `dr_1_remote` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `type` tinyint(2) NOT NULL COMMENT '远程附件类型',
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `exts` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='远程附件表';

-- ----------------------------
-- Records of dr_1_remote
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_share_category
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_share_category`;
CREATE TABLE `dr_1_share_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `tid` tinyint(1) NOT NULL COMMENT '栏目类型，0单页，1模块，2外链',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `mid` varchar(20) NOT NULL COMMENT '模块目录',
  `pids` varchar(255) NOT NULL COMMENT '所有上级id',
  `name` varchar(30) NOT NULL COMMENT '栏目名称',
  `domain` varchar(50) NOT NULL COMMENT '绑定域名',
  `letter` char(1) NOT NULL COMMENT '首字母',
  `dirname` varchar(30) NOT NULL COMMENT '栏目目录',
  `pdirname` varchar(100) NOT NULL COMMENT '上级目录',
  `child` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有下级',
  `childids` text NOT NULL COMMENT '下级所有id',
  `pcatpost` tinyint(1) NOT NULL COMMENT '是否父栏目发布',
  `thumb` varchar(255) NOT NULL COMMENT '栏目图片',
  `show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `content` mediumtext NOT NULL COMMENT '单页内容',
  `permission` text COMMENT '会员权限',
  `setting` text NOT NULL COMMENT '属性配置',
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`),
  KEY `tid` (`tid`),
  KEY `show` (`show`),
  KEY `dirname` (`dirname`),
  KEY `module` (`pid`,`displayorder`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='共享模块栏目表';

-- ----------------------------
-- Records of dr_1_share_category
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_share_extend_index
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_share_extend_index`;
CREATE TABLE `dr_1_share_extend_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` varchar(22) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='共享模块扩展索引表';

-- ----------------------------
-- Records of dr_1_share_extend_index
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_share_index
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_share_index`;
CREATE TABLE `dr_1_share_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mid` varchar(20) NOT NULL COMMENT '模块目录',
  PRIMARY KEY (`id`),
  KEY `mid` (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='共享模块内容索引表';

-- ----------------------------
-- Records of dr_1_share_index
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_tag
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_tag`;
CREATE TABLE `dr_1_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT '0' COMMENT '父级id',
  `name` varchar(200) NOT NULL COMMENT '关键词名称',
  `code` varchar(200) NOT NULL COMMENT '关键词代码（拼音）',
  `pcode` varchar(255) DEFAULT NULL,
  `hits` mediumint(8) unsigned NOT NULL COMMENT '点击量',
  `url` varchar(255) DEFAULT NULL COMMENT '关键词url',
  `childids` varchar(255) NOT NULL COMMENT '子类集合',
  `content` text NOT NULL COMMENT '关键词描述',
  `total` int(10) NOT NULL COMMENT '点击数量',
  `displayorder` int(10) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `letter` (`code`,`hits`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关键词库表';

-- ----------------------------
-- Records of dr_1_tag
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin`;
CREATE TABLE `dr_1_weixin` (
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信属性参数表';

-- ----------------------------
-- Records of dr_1_weixin
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_follow
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_follow`;
CREATE TABLE `dr_1_weixin_follow` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `status` (`status`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝同步表';

-- ----------------------------
-- Records of dr_1_weixin_follow
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_group
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_group`;
CREATE TABLE `dr_1_weixin_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `count` int(10) NOT NULL,
  `wechat_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝分组表';

-- ----------------------------
-- Records of dr_1_weixin_group
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_keyword
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_keyword`;
CREATE TABLE `dr_1_weixin_keyword` (
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

-- ----------------------------
-- Records of dr_1_weixin_keyword
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_material_file
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_material_file`;
CREATE TABLE `dr_1_weixin_material_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_video` tinyint(1) NOT NULL,
  `file` int(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `wechat_url` varchar(255) NOT NULL,
  `media_id` varchar(100) NOT NULL,
  `inputtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片素材';

-- ----------------------------
-- Records of dr_1_weixin_material_file
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_material_image
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_material_image`;
CREATE TABLE `dr_1_weixin_material_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `file` varchar(100) NOT NULL,
  `media_id` varchar(100) NOT NULL,
  `wechat_url` varchar(255) NOT NULL,
  `inputtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片素材';

-- ----------------------------
-- Records of dr_1_weixin_material_image
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_material_news
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_material_news`;
CREATE TABLE `dr_1_weixin_material_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `thumb` int(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `content` text NOT NULL,
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

-- ----------------------------
-- Records of dr_1_weixin_material_news
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_material_text
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_material_text`;
CREATE TABLE `dr_1_weixin_material_text` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `inputtime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文本素材';

-- ----------------------------
-- Records of dr_1_weixin_material_text
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_menu
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_menu`;
CREATE TABLE `dr_1_weixin_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `displayorder` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信菜单表';

-- ----------------------------
-- Records of dr_1_weixin_menu
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_message
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_message`;
CREATE TABLE `dr_1_weixin_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `send` tinyint(1) NOT NULL COMMENT '是否用户提交',
  `openid` varchar(100) NOT NULL,
  `content` text NOT NULL COMMENT '消息内容',
  `inputtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信消息记录表';

-- ----------------------------
-- Records of dr_1_weixin_message
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_weixin_user
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_weixin_user`;
CREATE TABLE `dr_1_weixin_user` (
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

-- ----------------------------
-- Records of dr_1_weixin_user
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_wxproject_category
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_wxproject_category`;
CREATE TABLE `dr_1_wxproject_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '产品分类排序',
  `category_title` varchar(50) NOT NULL COMMENT '产品分类名称',
  `category_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '产品分类简介',
  `thumb` varchar(255) NOT NULL COMMENT '产品分类缩略图',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示,0为不显示,1为显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dr_1_wxproject_category
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_wxproject_goods
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_wxproject_goods`;
CREATE TABLE `dr_1_wxproject_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '产品名称',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '产品标题',
  `thumb` varchar(255) NOT NULL COMMENT '产品缩略图',
  `store` int(10) unsigned NOT NULL COMMENT '产品库存',
  `sales` int(10) unsigned NOT NULL COMMENT '产品销量',
  `origin_price` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '产品原价',
  `sale_price` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '商品售价',
  `detail` text NOT NULL COMMENT '产品详情',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品更新时间',
  `status` tinyint(3) unsigned NOT NULL COMMENT '商品显示状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dr_1_wxproject_goods
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_wxproject_store
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_wxproject_store`;
CREATE TABLE `dr_1_wxproject_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '门店名称',
  `desc` varchar(255) NOT NULL COMMENT '门店简介',
  `thumb` varchar(255) NOT NULL COMMENT '门店缩略图',
  `linkman` char(10) NOT NULL COMMENT '门店联系人',
  `phone` char(15) NOT NULL COMMENT '门店联系电话',
  `address` varchar(255) NOT NULL COMMENT '门店地址',
  `lon` varchar(50) NOT NULL COMMENT '门店经度',
  `lat` varchar(50) NOT NULL COMMENT '门店纬度',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '门店状态,1为开启.0为关闭',
  `create_time` int(10) unsigned NOT NULL COMMENT '门店创建时间',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dr_1_wxproject_store
-- ----------------------------

-- ----------------------------
-- Table structure for dr_1_wxprojec_slide
-- ----------------------------
DROP TABLE IF EXISTS `dr_1_wxprojec_slide`;
CREATE TABLE `dr_1_wxprojec_slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `displayorder` smallint(5) unsigned NOT NULL COMMENT '幻灯片排序',
  `name` varchar(255) NOT NULL COMMENT '幻灯片名称',
  `picture` varchar(255) NOT NULL COMMENT '图片',
  `status` tinyint(1) unsigned NOT NULL COMMENT '幻灯片显示状态,1为显示,0为不显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dr_1_wxprojec_slide
-- ----------------------------

-- ----------------------------
-- Table structure for dr_admin
-- ----------------------------
DROP TABLE IF EXISTS `dr_admin`;
CREATE TABLE `dr_admin` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `realname` varchar(50) DEFAULT NULL COMMENT '管理员姓名',
  `usermenu` text COMMENT '自定义面板菜单，序列化数组格式',
  `color` text COMMENT '定制权限',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of dr_admin
-- ----------------------------
INSERT INTO `dr_admin` VALUES ('1', '网站创始人', '', '');

-- ----------------------------
-- Table structure for dr_admin_login
-- ----------------------------
DROP TABLE IF EXISTS `dr_admin_login`;
CREATE TABLE `dr_admin_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned DEFAULT NULL COMMENT '会员uid',
  `loginip` varchar(50) NOT NULL COMMENT '登录Ip',
  `logintime` int(10) unsigned NOT NULL COMMENT '登录时间',
  `useragent` varchar(255) NOT NULL COMMENT '客户端信息',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `loginip` (`loginip`),
  KEY `logintime` (`logintime`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='登录日志记录';

-- ----------------------------
-- Records of dr_admin_login
-- ----------------------------
INSERT INTO `dr_admin_login` VALUES ('1', '1', '::1', '1498377763', 'Windows 10 Firefox 54.0');
INSERT INTO `dr_admin_login` VALUES ('2', '1', '::1', '1498523772', 'Windows 10 Firefox 54.0');
INSERT INTO `dr_admin_login` VALUES ('3', '1', '::1', '1498642155', 'Windows 10 Chrome 58.0.3029.110');
INSERT INTO `dr_admin_login` VALUES ('4', '1', '192.168.0.6', '1498643180', 'Windows 10 Internet Explorer 7.0');

-- ----------------------------
-- Table structure for dr_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `dr_admin_menu`;
CREATE TABLE `dr_admin_menu` (
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
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
-- Records of dr_admin_menu
-- ----------------------------
INSERT INTO `dr_admin_menu` VALUES ('1', '0', '首页', '', null, 'home', '0', '0', 'fa fa-home');
INSERT INTO `dr_admin_menu` VALUES ('2', '1', '控制台', '', null, 'home-home', '0', '0', 'fa fa-home');
INSERT INTO `dr_admin_menu` VALUES ('3', '2', '后台首页', 'admin/home/main', '', '', '0', '0', 'fa fa-home');
INSERT INTO `dr_admin_menu` VALUES ('4', '2', '资料修改', 'admin/root/my', '', '', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('5', '2', '登录日志', 'admin/root/log', '', '', '0', '0', 'fa fa-calendar-check-o');
INSERT INTO `dr_admin_menu` VALUES ('6', '2', '错误日志', 'admin/system/debug', '', '', '0', '0', 'fa fa-bug');
INSERT INTO `dr_admin_menu` VALUES ('7', '2', '操作日志', 'admin/system/oplog', '', '', '0', '0', 'fa fa-calendar');
INSERT INTO `dr_admin_menu` VALUES ('8', '0', '设置', '', null, 'cog', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('9', '8', '系统设置', '', null, 'cog-sys', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('10', '9', '系统设置', 'admin/system/index', '', '', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('11', '9', '分离配置', 'admin/system/file', '', '', '0', '0', 'fa fa-cubes');
INSERT INTO `dr_admin_menu` VALUES ('12', '9', '邮件设置', 'admin/mail/index', '', '', '0', '0', 'fa fa-envelope');
INSERT INTO `dr_admin_menu` VALUES ('13', '9', '短信设置', 'admin/sms/index', '', '', '0', '0', 'fa fa-envelope');
INSERT INTO `dr_admin_menu` VALUES ('14', '9', '会员设置', 'member/admin/setting/index', '', '', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('15', '9', '网银接口', 'member/admin/setting/pay', '', '', '0', '0', 'fa fa-rmb');
INSERT INTO `dr_admin_menu` VALUES ('16', '9', '多语言设置', 'admin/language/index', '', '', '0', '0', 'fa fa-users');
INSERT INTO `dr_admin_menu` VALUES ('17', '8', '网站设置', '', null, '', '0', '0', 'fa fa-globe');
INSERT INTO `dr_admin_menu` VALUES ('18', '17', '网站设置', 'admin/site/config', '', '', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('19', '17', '网站管理', 'admin/site/index', '', '', '0', '0', 'fa fa-globe');
INSERT INTO `dr_admin_menu` VALUES ('20', '17', '内容模块', 'admin/module/index', '', '', '0', '0', 'fa fa-cogs');
INSERT INTO `dr_admin_menu` VALUES ('21', '17', '网站表单', 'admin/form/index', '', '', '0', '0', 'fa fa-tasks');
INSERT INTO `dr_admin_menu` VALUES ('22', '17', '模块评论', 'admin/frame_comment/index', '', '', '0', '0', 'fa fa-comments');
INSERT INTO `dr_admin_menu` VALUES ('23', '8', '权限设置', '', null, '', '0', '0', 'fa fa-users');
INSERT INTO `dr_admin_menu` VALUES ('24', '23', '后台菜单', 'admin/menu/index', '', '', '0', '0', 'fa fa-list');
INSERT INTO `dr_admin_menu` VALUES ('25', '23', '审核流程', 'admin/verify/index', '', '', '0', '0', 'fa fa-square');
INSERT INTO `dr_admin_menu` VALUES ('26', '23', '角色管理', 'admin/role/index', '', '', '0', '0', 'fa fa-users');
INSERT INTO `dr_admin_menu` VALUES ('27', '23', '会员权限', 'member/admin/setting/permission', '', '', '0', '0', 'fa fa-users');
INSERT INTO `dr_admin_menu` VALUES ('28', '23', '管理员管理', 'admin/root/index', '', '', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('29', '0', '内容', '', null, 'content', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_admin_menu` VALUES ('30', '29', '内容管理', '', null, 'content-content', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_admin_menu` VALUES ('31', '30', '共享栏目', 'admin/category_share/index', '', '', '0', '0', 'fa fa-list');
INSERT INTO `dr_admin_menu` VALUES ('32', '30', '生成静态', 'admin/html/index', '', '', '0', '0', 'fa fa-file');
INSERT INTO `dr_admin_menu` VALUES ('33', '30', '关键词库', 'admin/tag/index', '', '', '0', '0', 'fa fa-tag');
INSERT INTO `dr_admin_menu` VALUES ('34', '30', '附件管理', 'admin/attachment/index', '', '', '0', '0', 'fa fa-folder');
INSERT INTO `dr_admin_menu` VALUES ('35', '30', '自定义链接', 'admin/navigator/index', '', '', '0', '0', 'fa fa-map-marker');
INSERT INTO `dr_admin_menu` VALUES ('36', '30', '自定义页面', 'admin/page/index', '', '', '0', '0', 'fa fa-adn');
INSERT INTO `dr_admin_menu` VALUES ('37', '30', '自定义内容', 'admin/block/index', '', '', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_admin_menu` VALUES ('38', '30', '模块内容维护', 'admin/frame_content/index', '', '', '0', '0', 'fa fa-wrench');
INSERT INTO `dr_admin_menu` VALUES ('39', '29', '网站表单', '', null, 'content-form', '0', '0', 'fa fa-table');
INSERT INTO `dr_admin_menu` VALUES ('40', '0', '会员', '', null, 'member', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('41', '40', '会员管理', '', null, '', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('42', '41', '会员管理', 'member/admin/home/index', '', '', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('43', '41', '会员模型', 'member/admin/group/index', '', '', '0', '0', 'fa fa-users');
INSERT INTO `dr_admin_menu` VALUES ('44', '41', '财务流水', 'member/admin/pay/index', '', '', '0', '0', 'fa fa-calculator');
INSERT INTO `dr_admin_menu` VALUES ('45', '41', '会员菜单', 'member/admin/menu/index', '', '', '0', '0', 'fa fa-list');
INSERT INTO `dr_admin_menu` VALUES ('46', '41', '快捷登录', 'member/admin/setting/oauth', '', '', '0', '0', 'fa fa-weibo');
INSERT INTO `dr_admin_menu` VALUES ('47', '41', '自定义字段', 'admin/field/index/rname/member/rid/0', '', '', '0', '0', 'fa fa-code');
INSERT INTO `dr_admin_menu` VALUES ('48', '0', '空间', '', null, 'myspace', '0', '0', 'fa fa-trello');
INSERT INTO `dr_admin_menu` VALUES ('49', '48', '空间黄页', '', null, '', '0', '0', 'fa fa-trello');
INSERT INTO `dr_admin_menu` VALUES ('50', '49', '空间管理', 'space/admin/space/index', '', '', '0', '0', 'fa fa-trello');
INSERT INTO `dr_admin_menu` VALUES ('51', '49', '空间模型', 'space/admin/model/index', '', '', '0', '0', 'fa fa-cogs');
INSERT INTO `dr_admin_menu` VALUES ('52', '49', '动态管理', 'space/admin/sns/index', '', '', '0', '0', 'fa fa-weibo');
INSERT INTO `dr_admin_menu` VALUES ('53', '49', '默认栏目', 'space/admin/space/init', '', '', '0', '0', 'fa fa-th');
INSERT INTO `dr_admin_menu` VALUES ('54', '49', '空间设置', 'space/admin/setting/space', '', '', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('55', '49', '自定义字段', 'admin/field/index/rname/spacetable/rid/0', '', '', '0', '0', 'fa fa-code');
INSERT INTO `dr_admin_menu` VALUES ('56', '48', '空间内容', '', null, 'space-content', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_admin_menu` VALUES ('57', '0', '界面', '', null, '', '0', '0', 'fa fa-html5');
INSERT INTO `dr_admin_menu` VALUES ('58', '57', '网站模板', '', null, '', '0', '0', 'fa fa-folder');
INSERT INTO `dr_admin_menu` VALUES ('59', '58', '电脑模板', 'admin/tpl/index', '', '', '0', '0', 'fa fa-desktop');
INSERT INTO `dr_admin_menu` VALUES ('60', '58', '手机模板', 'admin/tpl/mobile', '', '', '0', '0', 'fa fa-mobile');
INSERT INTO `dr_admin_menu` VALUES ('61', '58', '风格样式', 'admin/theme/index', '', '', '0', '0', 'fa fa-css3');
INSERT INTO `dr_admin_menu` VALUES ('62', '58', '标签向导', 'admin/tpl/tag', '', '', '0', '0', 'fa fa-tag');
INSERT INTO `dr_admin_menu` VALUES ('63', '57', '会员模板', '', null, '', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('64', '63', '电脑模板', 'member/admin/tpl/index', '', '', '0', '0', 'fa fa-desktop');
INSERT INTO `dr_admin_menu` VALUES ('65', '63', '手机模板', 'member/admin/tpl/mobile', '', '', '0', '0', 'fa fa-mobile');
INSERT INTO `dr_admin_menu` VALUES ('66', '63', '标签向导', 'member/admin/tpl/tag', '', '', '0', '0', 'fa fa-tag');
INSERT INTO `dr_admin_menu` VALUES ('67', '57', '空间模板', '', null, 'template-space', '0', '0', 'fa fa-trello');
INSERT INTO `dr_admin_menu` VALUES ('68', '67', '个人空间模板', 'space/admin/spacetpl/index', '', '', '0', '0', 'fa fa-desktop');
INSERT INTO `dr_admin_menu` VALUES ('69', '0', '微信Beta', '', null, '', '0', '0', 'fa fa-weixin');
INSERT INTO `dr_admin_menu` VALUES ('70', '69', '公众号', '', null, '', '0', '0', 'fa fa-wechat');
INSERT INTO `dr_admin_menu` VALUES ('71', '70', '参数设置', 'admin/weixin/index', '', '', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('72', '70', '自定义菜单', 'admin/wmenu/index', '', '', '0', '0', 'fa fa-table');
INSERT INTO `dr_admin_menu` VALUES ('73', '69', '素材管理', '', null, '', '0', '0', 'fa fa-navicon');
INSERT INTO `dr_admin_menu` VALUES ('74', '73', '文字素材', 'admin/wmaterial/index', '', '', '0', '0', 'fa fa-file-text');
INSERT INTO `dr_admin_menu` VALUES ('75', '73', '图文素材', 'admin/wmaterial/tw', '', '', '0', '0', 'fa fa-file-image-o');
INSERT INTO `dr_admin_menu` VALUES ('76', '73', '图片素材', 'admin/wmaterial/tp', '', '', '0', '0', 'fa fa-file-picture-o');
INSERT INTO `dr_admin_menu` VALUES ('77', '73', '语音素材', 'admin/wmaterial/yy', '', '', '0', '0', 'fa fa-file-sound-o');
INSERT INTO `dr_admin_menu` VALUES ('78', '73', '视频素材', 'admin/wmaterial/sp', '', '', '0', '0', 'fa fa-file-video-o');
INSERT INTO `dr_admin_menu` VALUES ('79', '69', '粉丝管理', '', null, '', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('80', '79', '微信分组', 'admin/wgroup/index', '', '', '0', '0', 'fa fa-users');
INSERT INTO `dr_admin_menu` VALUES ('81', '79', '微信粉丝', 'admin/wuser/index', '', '', '0', '0', 'fa fa-user');
INSERT INTO `dr_admin_menu` VALUES ('82', '69', '回复设置', '', null, '', '0', '0', 'fa fa-commenting');
INSERT INTO `dr_admin_menu` VALUES ('83', '82', '关键字管理', 'admin/wkeyword/index', '', '', '0', '0', 'fa fa-tag');
INSERT INTO `dr_admin_menu` VALUES ('84', '82', '系统回复设置', 'admin/weixin/reply', '', '', '0', '0', 'fa fa-cog');
INSERT INTO `dr_admin_menu` VALUES ('85', '69', '消息管理', '', null, '', '0', '0', 'fa fa-envelope');
INSERT INTO `dr_admin_menu` VALUES ('86', '85', '群发消息', 'admin/wsms/index', '', '', '0', '0', 'fa fa-send');
INSERT INTO `dr_admin_menu` VALUES ('87', '85', '消息记录', 'admin/wmessage/index', '', '', '0', '0', 'fa fa-envelope');
INSERT INTO `dr_admin_menu` VALUES ('88', '0', '插件', '', null, 'myapp', '0', '0', 'fa fa-puzzle-piece');
INSERT INTO `dr_admin_menu` VALUES ('89', '88', '系统插件', '', null, 'cog-sys', '0', '0', 'fa fa-puzzle-piece');
INSERT INTO `dr_admin_menu` VALUES ('90', '89', '系统提醒', 'admin/notice/index', '', '', '0', '0', 'fa fa-volume-down');
INSERT INTO `dr_admin_menu` VALUES ('91', '89', '任务队列', 'admin/cron/index', '', '', '0', '0', 'fa fa-forward');
INSERT INTO `dr_admin_menu` VALUES ('92', '89', 'URL规则', 'admin/urlrule/index', '', '', '0', '0', 'fa fa-magnet');
INSERT INTO `dr_admin_menu` VALUES ('93', '89', '下载镜像', 'admin/downservers/index', '', '', '0', '0', 'fa fa-arrow-circle-down');
INSERT INTO `dr_admin_menu` VALUES ('94', '89', '远程附件', 'admin/attachment2/index', '', '', '0', '0', 'fa fa-upload');
INSERT INTO `dr_admin_menu` VALUES ('95', '89', '联动菜单', 'admin/linkage/index', '', '', '0', '0', 'fa fa-windows');
INSERT INTO `dr_admin_menu` VALUES ('96', '89', '全局变量', 'admin/sysvar/index', '', '', '0', '0', 'fa fa-tumblr');
INSERT INTO `dr_admin_menu` VALUES ('97', '89', '数据结构', 'admin/db/index', '', '', '0', '0', 'fa fa-database');
INSERT INTO `dr_admin_menu` VALUES ('98', '89', '自定义控制器', 'admin/syscontroller/index', '', '', '0', '0', 'fa fa-code');
INSERT INTO `dr_admin_menu` VALUES ('99', '88', '应用插件', '', null, 'cloud-cloud', '0', '0', 'fa fa-cloud');
INSERT INTO `dr_admin_menu` VALUES ('100', '99', '应用管理', 'admin/application/index', '', '', '0', '0', 'fa fa-cloud');
INSERT INTO `dr_admin_menu` VALUES ('101', '99', '插件商城', 'admin/application/yun', '', '', '0', '0', 'fa fa-shopping-cart');
INSERT INTO `dr_admin_menu` VALUES ('102', '29', '新闻管理', '', null, 'module-news', '0', '0', 'fa fa-tasks');
INSERT INTO `dr_admin_menu` VALUES ('103', '102', '已通过文档', 'news/admin/home/index', null, 'module-news', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_admin_menu` VALUES ('104', '102', '待审核文档', 'news/admin/home/verify', null, 'module-news', '0', '0', 'fa fa-retweet');
INSERT INTO `dr_admin_menu` VALUES ('105', '102', '我的草稿箱', 'news/admin/home/draft', null, 'module-news', '0', '0', 'fa fa-edit');
INSERT INTO `dr_admin_menu` VALUES ('106', '102', 'Tag标签', 'news/admin/tag/index', null, 'module-news', '0', '0', 'fa fa-tags');
INSERT INTO `dr_admin_menu` VALUES ('107', '102', '评论管理', 'news/admin/comment/index', null, 'module-news', '0', '0', 'icon-comments');
INSERT INTO `dr_admin_menu` VALUES ('134', '133', '幻灯片列表', 'admin/wxprojectSlide/slide_list', '', null, '0', '0', 'fa fa-file-movie-o');
INSERT INTO `dr_admin_menu` VALUES ('132', '131', '门店列表', 'admin/wxprojectStore/store_list', '', null, '0', '0', 'fa fa-sitemap');
INSERT INTO `dr_admin_menu` VALUES ('133', '127', '幻灯片管理', '', null, null, '0', '0', 'fa fa-video-camera');
INSERT INTO `dr_admin_menu` VALUES ('131', '127', '门店管理', '', null, null, '0', '0', 'fa fa-university');
INSERT INTO `dr_admin_menu` VALUES ('130', '128', '产品列表', 'admin/wxprojectGoods/goods_list', '', null, '0', '0', 'fa fa-list');
INSERT INTO `dr_admin_menu` VALUES ('129', '128', '产品分类', 'admin/wxprojeCtcategory/category_list', '', null, '0', '0', 'fa fa-cubes');
INSERT INTO `dr_admin_menu` VALUES ('128', '127', '商品管理', '', null, null, '0', '0', 'fa fa-th-large');
INSERT INTO `dr_admin_menu` VALUES ('127', '0', '小程序', '', null, null, '0', '0', 'fa fa-odnoklassniki');
INSERT INTO `dr_admin_menu` VALUES ('124', '108', '商品管理', '', null, null, '0', '0', 'fa fa-list');
INSERT INTO `dr_admin_menu` VALUES ('125', '124', '商品分类', 'goods_category/admin/goodsCategory/index', '', null, '0', '0', '');
INSERT INTO `dr_admin_menu` VALUES ('126', '124', '自定义字段', 'admin/field/index/rname/module/rid/2', '', null, '0', '0', 'fa fa-plus');

-- ----------------------------
-- Table structure for dr_admin_notice
-- ----------------------------
DROP TABLE IF EXISTS `dr_admin_notice`;
CREATE TABLE `dr_admin_notice` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
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

-- ----------------------------
-- Records of dr_admin_notice
-- ----------------------------

-- ----------------------------
-- Table structure for dr_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `dr_admin_role`;
CREATE TABLE `dr_admin_role` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `site` text NOT NULL COMMENT '允许管理的站点，序列化数组格式',
  `name` text NOT NULL COMMENT '角色组语言名称',
  `system` text NOT NULL COMMENT '系统权限',
  `module` text NOT NULL COMMENT '模块权限',
  `application` text NOT NULL COMMENT '应用权限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='后台角色权限表';

-- ----------------------------
-- Records of dr_admin_role
-- ----------------------------
INSERT INTO `dr_admin_role` VALUES ('1', '', '超级管理员', '', '', '');
INSERT INTO `dr_admin_role` VALUES ('2', '', '网站编辑员', '', '', '');

-- ----------------------------
-- Table structure for dr_admin_verify
-- ----------------------------
DROP TABLE IF EXISTS `dr_admin_verify`;
CREATE TABLE `dr_admin_verify` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT '名称',
  `verify` text NOT NULL COMMENT '审核部署',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='审核管理表';

-- ----------------------------
-- Records of dr_admin_verify
-- ----------------------------
INSERT INTO `dr_admin_verify` VALUES ('1', '审核一次', 'a:1:{i:1;a:2:{i:0;s:1:\\\"2\\\";i:1;s:1:\\\"3\\\";}}');

-- ----------------------------
-- Table structure for dr_application
-- ----------------------------
DROP TABLE IF EXISTS `dr_application`;
CREATE TABLE `dr_application` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `module` text COMMENT '模块划分',
  `dirname` varchar(50) NOT NULL COMMENT '目录名称',
  `setting` text COMMENT '配置信息',
  `disabled` tinyint(1) DEFAULT '0' COMMENT '是否禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dirname` (`dirname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应用表';

-- ----------------------------
-- Records of dr_application
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment`;
CREATE TABLE `dr_attachment` (
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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Records of dr_attachment
-- ----------------------------
INSERT INTO `dr_attachment` VALUES ('1', '1', 'admin', '1', '', '1', '0', '59197', 'jpg', '2ce7af571abfbbefa902812ddcda5eb2');
INSERT INTO `dr_attachment` VALUES ('2', '1', 'admin', '1', '', '1', '0', '77087', 'jpg', '345b7f10603fe6ee5ffb361a75dd8046');
INSERT INTO `dr_attachment` VALUES ('3', '1', 'admin', '1', '', '1', '0', '126351', 'jpg', 'b8fd0adeaf6c172ec56aa554cce2e661');
INSERT INTO `dr_attachment` VALUES ('4', '1', 'admin', '1', '', '1', '0', '468460', 'jpg', 'c3fdd5c0bd0414d9cc99352b2a53ddc2');
INSERT INTO `dr_attachment` VALUES ('5', '1', 'admin', '1', '', '1', '0', '85555', 'jpg', 'c7e96d746d687246bf1b61a6ee6984a5');
INSERT INTO `dr_attachment` VALUES ('6', '1', 'admin', '1', '', '1', '0', '77087', 'jpg', '345b7f10603fe6ee5ffb361a75dd8046');
INSERT INTO `dr_attachment` VALUES ('7', '1', 'admin', '1', '', '1', '0', '126351', 'jpg', 'b8fd0adeaf6c172ec56aa554cce2e661');
INSERT INTO `dr_attachment` VALUES ('8', '1', 'admin', '1', '', '1', '0', '65188', 'jpg', '66b6d1088fe23cd4f613490ae1db6695');
INSERT INTO `dr_attachment` VALUES ('9', '1', 'admin', '1', '', '1', '0', '66642', 'jpg', '1599e5819d5372414744aeedd229e5bc');
INSERT INTO `dr_attachment` VALUES ('10', '1', 'admin', '1', '', '1', '0', '14049', 'jpg', 'cce2a25bfc20f695d3a00b3da4c5a7b6');
INSERT INTO `dr_attachment` VALUES ('11', '1', 'admin', '1', '', '1', '0', '77087', 'jpg', '345b7f10603fe6ee5ffb361a75dd8046');
INSERT INTO `dr_attachment` VALUES ('12', '1', 'admin', '1', '', '1', '0', '468460', 'jpg', 'c3fdd5c0bd0414d9cc99352b2a53ddc2');
INSERT INTO `dr_attachment` VALUES ('13', '1', 'admin', '1', '', '1', '0', '85555', 'jpg', 'c7e96d746d687246bf1b61a6ee6984a5');
INSERT INTO `dr_attachment` VALUES ('14', '1', 'admin', '1', '', '1', '0', '77087', 'jpg', '345b7f10603fe6ee5ffb361a75dd8046');
INSERT INTO `dr_attachment` VALUES ('15', '1', 'admin', '1', '', '1', '0', '85555', 'jpg', 'c7e96d746d687246bf1b61a6ee6984a5');
INSERT INTO `dr_attachment` VALUES ('16', '1', 'admin', '1', '', '1', '0', '77087', 'jpg', '345b7f10603fe6ee5ffb361a75dd8046');
INSERT INTO `dr_attachment` VALUES ('17', '1', 'admin', '1', '', '1', '0', '468460', 'jpg', 'c3fdd5c0bd0414d9cc99352b2a53ddc2');
INSERT INTO `dr_attachment` VALUES ('18', '1', 'admin', '1', '', '1', '0', '85555', 'jpg', 'c7e96d746d687246bf1b61a6ee6984a5');
INSERT INTO `dr_attachment` VALUES ('19', '1', 'admin', '1', '', '1', '0', '77087', 'jpg', '345b7f10603fe6ee5ffb361a75dd8046');
INSERT INTO `dr_attachment` VALUES ('20', '1', 'admin', '1', '', '1', '0', '77087', 'jpg', '345b7f10603fe6ee5ffb361a75dd8046');

-- ----------------------------
-- Table structure for dr_attachment_0
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_0`;
CREATE TABLE `dr_attachment_0` (
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

-- ----------------------------
-- Records of dr_attachment_0
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_1
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_1`;
CREATE TABLE `dr_attachment_1` (
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

-- ----------------------------
-- Records of dr_attachment_1
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_2
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_2`;
CREATE TABLE `dr_attachment_2` (
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

-- ----------------------------
-- Records of dr_attachment_2
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_3
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_3`;
CREATE TABLE `dr_attachment_3` (
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

-- ----------------------------
-- Records of dr_attachment_3
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_4
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_4`;
CREATE TABLE `dr_attachment_4` (
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

-- ----------------------------
-- Records of dr_attachment_4
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_5
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_5`;
CREATE TABLE `dr_attachment_5` (
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

-- ----------------------------
-- Records of dr_attachment_5
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_6
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_6`;
CREATE TABLE `dr_attachment_6` (
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

-- ----------------------------
-- Records of dr_attachment_6
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_7
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_7`;
CREATE TABLE `dr_attachment_7` (
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

-- ----------------------------
-- Records of dr_attachment_7
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_8
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_8`;
CREATE TABLE `dr_attachment_8` (
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

-- ----------------------------
-- Records of dr_attachment_8
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_9
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_9`;
CREATE TABLE `dr_attachment_9` (
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

-- ----------------------------
-- Records of dr_attachment_9
-- ----------------------------

-- ----------------------------
-- Table structure for dr_attachment_unused
-- ----------------------------
DROP TABLE IF EXISTS `dr_attachment_unused`;
CREATE TABLE `dr_attachment_unused` (
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

-- ----------------------------
-- Records of dr_attachment_unused
-- ----------------------------
INSERT INTO `dr_attachment_unused` VALUES ('1', '1', 'admin', '1', 'chuizi', 'jpg', '59197', '201706/ffa4319e7f.jpg', '0', '{\"height\":412,\"width\":550}', '1498622607');
INSERT INTO `dr_attachment_unused` VALUES ('2', '1', 'admin', '1', 'swipe2', 'jpg', '77087', '201706/e77c83761a.jpg', '0', '{\"height\":330,\"width\":946}', '1498624803');
INSERT INTO `dr_attachment_unused` VALUES ('3', '1', 'admin', '1', 'swipe3', 'jpg', '126351', '201706/c11f2c9e6c.jpg', '0', '{\"height\":300,\"width\":1190}', '1498629137');
INSERT INTO `dr_attachment_unused` VALUES ('4', '1', 'admin', '1', 'vivo', 'jpg', '468460', '201706/e7a0ebfa8d.jpg', '0', '{\"height\":1824,\"width\":2736}', '1498629186');
INSERT INTO `dr_attachment_unused` VALUES ('5', '1', 'admin', '1', 'swipe1', 'jpg', '85555', '201706/fe0b001e49.jpg', '0', '{\"height\":330,\"width\":946}', '1498629376');
INSERT INTO `dr_attachment_unused` VALUES ('6', '1', 'admin', '1', 'swipe2', 'jpg', '77087', '201706/62d9793f54.jpg', '0', '{\"height\":330,\"width\":946}', '1498629377');
INSERT INTO `dr_attachment_unused` VALUES ('7', '1', 'admin', '1', 'swipe3', 'jpg', '126351', '201706/80e67c203e.jpg', '0', '{\"height\":300,\"width\":1190}', '1498629379');
INSERT INTO `dr_attachment_unused` VALUES ('8', '1', 'admin', '1', 'swipe4', 'jpg', '65188', '201706/fc4fe147ed.jpg', '0', '{\"height\":300,\"width\":1190}', '1498629380');
INSERT INTO `dr_attachment_unused` VALUES ('9', '1', 'admin', '1', 'swipe5', 'jpg', '66642', '201706/95fc3523e0.jpg', '0', '{\"height\":300,\"width\":1190}', '1498629381');
INSERT INTO `dr_attachment_unused` VALUES ('10', '1', 'admin', '1', 'xiaomi4x', 'jpg', '14049', '201706/963c157cb0.jpg', '0', '{\"height\":200,\"width\":200}', '1498629649');
INSERT INTO `dr_attachment_unused` VALUES ('11', '1', 'admin', '1', 'swipe2', 'jpg', '77087', '201706/e70c6bb193.jpg', '0', '{\"height\":330,\"width\":946}', '1498629950');
INSERT INTO `dr_attachment_unused` VALUES ('12', '1', 'admin', '1', 'vivo', 'jpg', '468460', '201706/98b8838628.jpg', '0', '{\"height\":1824,\"width\":2736}', '1498630186');
INSERT INTO `dr_attachment_unused` VALUES ('13', '1', 'admin', '1', 'swipe1', 'jpg', '85555', '201706/2c1b697fdd.jpg', '0', '{\"height\":330,\"width\":946}', '1498630233');
INSERT INTO `dr_attachment_unused` VALUES ('14', '1', 'admin', '1', 'swipe2', 'jpg', '77087', '201706/4f46733065.jpg', '0', '{\"height\":330,\"width\":946}', '1498630412');
INSERT INTO `dr_attachment_unused` VALUES ('15', '1', 'admin', '1', 'swipe1', 'jpg', '85555', '201706/f5e3f802f9.jpg', '0', '{\"height\":330,\"width\":946}', '1498630454');
INSERT INTO `dr_attachment_unused` VALUES ('16', '1', 'admin', '1', 'swipe2', 'jpg', '77087', '201706/672f125d1f.jpg', '0', '{\"height\":330,\"width\":946}', '1498630752');
INSERT INTO `dr_attachment_unused` VALUES ('17', '1', 'admin', '1', 'vivo', 'jpg', '468460', '201706/0fe49ff8bd.jpg', '0', '{\"height\":1824,\"width\":2736}', '1498630816');
INSERT INTO `dr_attachment_unused` VALUES ('18', '1', 'admin', '1', 'swipe1', 'jpg', '85555', '201706/b8b71d1c45.jpg', '0', '{\"height\":330,\"width\":946}', '1498630849');
INSERT INTO `dr_attachment_unused` VALUES ('19', '1', 'admin', '1', 'swipe2', 'jpg', '77087', '201706/d13c0f8cbc.jpg', '0', '{\"height\":330,\"width\":946}', '1498631629');
INSERT INTO `dr_attachment_unused` VALUES ('20', '1', 'admin', '1', 'swipe2', 'jpg', '77087', '201706/ae7b03f0e3.jpg', '0', '{\"height\":330,\"width\":946}', '1498631805');

-- ----------------------------
-- Table structure for dr_comment
-- ----------------------------
DROP TABLE IF EXISTS `dr_comment`;
CREATE TABLE `dr_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '名称',
  `value` text COMMENT '配置信息',
  `field` text COMMENT '自定义字段信息',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='评论配置表';

-- ----------------------------
-- Records of dr_comment
-- ----------------------------
INSERT INTO `dr_comment` VALUES ('1', 'comment-module-wxproject', '', '');
INSERT INTO `dr_comment` VALUES ('2', 'comment-module-news', '', '');

-- ----------------------------
-- Table structure for dr_controller
-- ----------------------------
DROP TABLE IF EXISTS `dr_controller`;
CREATE TABLE `dr_controller` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自定义控制器表';

-- ----------------------------
-- Records of dr_controller
-- ----------------------------

-- ----------------------------
-- Table structure for dr_cron_queue
-- ----------------------------
DROP TABLE IF EXISTS `dr_cron_queue`;
CREATE TABLE `dr_cron_queue` (
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

-- ----------------------------
-- Records of dr_cron_queue
-- ----------------------------

-- ----------------------------
-- Table structure for dr_downservers
-- ----------------------------
DROP TABLE IF EXISTS `dr_downservers`;
CREATE TABLE `dr_downservers` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '服务器名',
  `server` varchar(255) NOT NULL COMMENT '服务器地址',
  `displayorder` tinyint(3) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='下载镜像服务器';

-- ----------------------------
-- Records of dr_downservers
-- ----------------------------

-- ----------------------------
-- Table structure for dr_field
-- ----------------------------
DROP TABLE IF EXISTS `dr_field`;
CREATE TABLE `dr_field` (
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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='字段表';

-- ----------------------------
-- Records of dr_field
-- ----------------------------
INSERT INTO `dr_field` VALUES ('1', '相关附件', 'attachment', 'Files', '1', 'page', '1', '1', '1', '1', '0', '0', 'a:2:{s:6:\"option\";a:5:{s:5:\"width\";s:3:\"80%\";s:4:\"size\";s:1:\"2\";s:5:\"count\";s:2:\"10\";s:3:\"ext\";s:31:\"jpg,gif,png,ppt,doc,xls,rar,zip\";s:10:\"uploadpath\";s:0:\"\";}s:8:\"validate\";a:9:{s:8:\"required\";s:1:\"0\";s:7:\"pattern\";s:0:\"\";s:9:\"errortips\";s:0:\"\";s:6:\"isedit\";s:1:\"0\";s:3:\"xss\";s:1:\"0\";s:5:\"check\";s:0:\"\";s:6:\"filter\";s:0:\"\";s:4:\"tips\";s:0:\"\";s:8:\"formattr\";s:0:\"\";}}', '0');
INSERT INTO `dr_field` VALUES ('2', '单页内容', 'content', 'Ueditor', '1', 'page', '1', '1', '1', '1', '0', '0', 'a:2:{s:6:\"option\";a:7:{s:5:\"width\";s:3:\"90%\";s:6:\"height\";s:3:\"400\";s:4:\"mode\";s:1:\"1\";s:4:\"tool\";s:0:\"\";s:5:\"mode2\";s:1:\"1\";s:5:\"tool2\";s:0:\"\";s:5:\"value\";s:0:\"\";}s:8:\"validate\";a:9:{s:8:\"required\";s:1:\"1\";s:7:\"pattern\";s:0:\"\";s:9:\"errortips\";s:0:\"\";s:6:\"isedit\";s:1:\"0\";s:3:\"xss\";s:1:\"1\";s:5:\"check\";s:0:\"\";s:6:\"filter\";s:0:\"\";s:4:\"tips\";s:0:\"\";s:8:\"formattr\";s:0:\"\";}}', '0');
INSERT INTO `dr_field` VALUES ('3', '主题', 'title', 'Text', '1', 'module', '1', '1', '1', '1', '1', '0', '{\"option\":{\"width\":400,\"fieldtype\":\"VARCHAR\",\"fieldlength\":\"255\"},\"validate\":{\"xss\":1,\"required\":1,\"formattr\":\"onblur=\\\"check_title();get_keywords(\'keywords\');\\\"\"}}', '0');
INSERT INTO `dr_field` VALUES ('4', '缩略图', 'thumb', 'File', '1', 'module', '1', '1', '1', '1', '1', '0', '{\"option\":{\"ext\":\"jpg,gif,png\",\"size\":10,\"width\":400,\"fieldtype\":\"VARCHAR\",\"fieldlength\":\"255\"}}', '0');
INSERT INTO `dr_field` VALUES ('5', '关键字', 'keywords', 'Text', '1', 'module', '1', '1', '1', '1', '1', '0', '{\"option\":{\"width\":400,\"fieldtype\":\"VARCHAR\",\"fieldlength\":\"255\"},\"validate\":{\"xss\":1,\"formattr\":\" data-role=\\\"tagsinput\\\"\"}}', '0');
INSERT INTO `dr_field` VALUES ('6', '描述', 'description', 'Textarea', '1', 'module', '1', '1', '1', '1', '1', '0', '{\"option\":{\"width\":500,\"height\":60,\"fieldtype\":\"VARCHAR\",\"fieldlength\":\"255\"},\"validate\":{\"xss\":1,\"filter\":\"dr_clearhtml\"}}', '0');
INSERT INTO `dr_field` VALUES ('7', '内容', 'content', 'Ueditor', '1', 'module', '1', '0', '1', '1', '1', '0', '{\"option\":{\"mode\":1,\"width\":\"90%\",\"height\":400},\"validate\":{\"xss\":1,\"required\":1}}', '0');

-- ----------------------------
-- Table structure for dr_linkage
-- ----------------------------
DROP TABLE IF EXISTS `dr_linkage`;
CREATE TABLE `dr_linkage` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '菜单名称',
  `type` tinyint(1) unsigned NOT NULL,
  `code` char(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `module` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='联动菜单表';

-- ----------------------------
-- Records of dr_linkage
-- ----------------------------
INSERT INTO `dr_linkage` VALUES ('1', '中国地区', '0', 'address');

-- ----------------------------
-- Table structure for dr_linkage_data_1
-- ----------------------------
DROP TABLE IF EXISTS `dr_linkage_data_1`;
CREATE TABLE `dr_linkage_data_1` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `site` mediumint(5) unsigned NOT NULL COMMENT '站点id',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `pids` varchar(255) DEFAULT NULL COMMENT '所有上级id',
  `name` varchar(30) NOT NULL COMMENT '栏目名称',
  `cname` varchar(30) NOT NULL COMMENT '别名',
  `child` tinyint(1) unsigned DEFAULT '0' COMMENT '是否有下级',
  `hidden` tinyint(1) unsigned DEFAULT '0' COMMENT '前端隐藏',
  `childids` text COMMENT '下级所有id',
  `displayorder` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cname` (`cname`),
  KEY `hidden` (`hidden`),
  KEY `list` (`site`,`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='联动菜单数据表';

-- ----------------------------
-- Records of dr_linkage_data_1
-- ----------------------------
INSERT INTO `dr_linkage_data_1` VALUES ('1', '1', '0', '0', '地区1', 'diqu1', '0', '0', '1', '0');
INSERT INTO `dr_linkage_data_1` VALUES ('2', '1', '0', '0', '城市', 'chengshi', '0', '0', '2', '0');
INSERT INTO `dr_linkage_data_1` VALUES ('3', '1', '0', '0', '北京', 'beijing', '0', '0', '3', '0');
INSERT INTO `dr_linkage_data_1` VALUES ('4', '1', '0', '0', '洞子口', 'dongzikou', '0', '0', '4', '0');
INSERT INTO `dr_linkage_data_1` VALUES ('5', '1', '0', '0', '细河区', 'xihequ', '0', '0', '5', '0');

-- ----------------------------
-- Table structure for dr_mail_queue
-- ----------------------------
DROP TABLE IF EXISTS `dr_mail_queue`;
CREATE TABLE `dr_mail_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL COMMENT '邮件地址',
  `subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `message` text NOT NULL COMMENT '邮件内容',
  `status` tinyint(1) unsigned NOT NULL COMMENT '发送状态',
  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `updatetime` (`updatetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邮件队列表';

-- ----------------------------
-- Records of dr_mail_queue
-- ----------------------------

-- ----------------------------
-- Table structure for dr_mail_smtp
-- ----------------------------
DROP TABLE IF EXISTS `dr_mail_smtp`;
CREATE TABLE `dr_mail_smtp` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `port` mediumint(8) unsigned NOT NULL,
  `displayorder` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邮件账户表';

-- ----------------------------
-- Records of dr_mail_smtp
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member
-- ----------------------------
DROP TABLE IF EXISTS `dr_member`;
CREATE TABLE `dr_member` (
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of dr_member
-- ----------------------------
INSERT INTO `dr_member` VALUES ('1', '1045578710@qq.com', 'admin', '1440423d9f1dbc508c94227626c83329', '05f971b5ec', '', '', '', '9999.00', '0.00', '0.00', '10000', '10000', '1', '3', '4', '0', '', '0', '0', '0');
INSERT INTO `dr_member` VALUES ('2', '', 'wanxian', 'cce6e4040e433e152d87740db1ccb41b', 'df6d2338b2', '', '', '', '0.00', '0.00', '0.00', '0', '0', '0', '3', '0', '0', '::1', '1498379638', '0', '0');

-- ----------------------------
-- Table structure for dr_member_address
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_address`;
CREATE TABLE `dr_member_address` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员收货地址表';

-- ----------------------------
-- Records of dr_member_address
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_data
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_data`;
CREATE TABLE `dr_member_data` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `complete` tinyint(1) unsigned NOT NULL COMMENT '完善资料标识',
  `is_auth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '实名认证标识',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
-- Records of dr_member_data
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_group
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_group`;
CREATE TABLE `dr_member_group` (
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
  `spacedomain` tinyint(1) unsigned DEFAULT NULL COMMENT '是否启用空间域名',
  `spacetemplate` varchar(50) DEFAULT NULL COMMENT '空间默认模板',
  `displayorder` tinyint(3) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='会员组表';

-- ----------------------------
-- Records of dr_member_group
-- ----------------------------
INSERT INTO `dr_member_group` VALUES ('1', '待审核会员', 'default', 'default', '0.00', '1', '1', '0', '0', '0', '0', '0', '', '', '1', 'default', '0');
INSERT INTO `dr_member_group` VALUES ('2', '快捷登录', 'default', 'default', '0.00', '0', '0', '0', '0', '0', '0', '0', '', '', '1', 'default', '0');
INSERT INTO `dr_member_group` VALUES ('3', '普通会员', 'default', 'default', '0.00', '1', '1', '3', '0', '1', '0', '1', '', '', '1', 'default', '0');
INSERT INTO `dr_member_group` VALUES ('4', '商业会员', 'default', 'default', '10.00', '2', '1', '3', '1', '0', '0', '1', '', '', '1', 'default', '0');

-- ----------------------------
-- Table structure for dr_member_level
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_level`;
CREATE TABLE `dr_member_level` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '会员级别名称',
  `stars` tinyint(2) NOT NULL COMMENT '星星数量',
  `experience` int(10) unsigned NOT NULL COMMENT '经验值要求',
  `allowupgrade` tinyint(1) NOT NULL COMMENT '允许自动升级',
  PRIMARY KEY (`id`),
  KEY `experience` (`experience`),
  KEY `groupid` (`groupid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='会员级别表';

-- ----------------------------
-- Records of dr_member_level
-- ----------------------------
INSERT INTO `dr_member_level` VALUES ('1', '3', '初级', '1', '0', '0');
INSERT INTO `dr_member_level` VALUES ('2', '3', '中级', '5', '200', '0');
INSERT INTO `dr_member_level` VALUES ('3', '3', '高级', '10', '500', '0');
INSERT INTO `dr_member_level` VALUES ('4', '3', '元老', '15', '1000', '0');
INSERT INTO `dr_member_level` VALUES ('5', '4', '普通', '16', '0', '0');
INSERT INTO `dr_member_level` VALUES ('6', '4', '银牌', '23', '500', '0');
INSERT INTO `dr_member_level` VALUES ('7', '4', '金牌', '35', '1000', '0');
INSERT INTO `dr_member_level` VALUES ('8', '4', '钻石', '55', '2000', '0');

-- ----------------------------
-- Table structure for dr_member_login
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_login`;
CREATE TABLE `dr_member_login` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='登录日志记录';

-- ----------------------------
-- Records of dr_member_login
-- ----------------------------
INSERT INTO `dr_member_login` VALUES ('1', '1', '', '::1', '1498610007', 'Windows 10 Firefox 54.0');

-- ----------------------------
-- Table structure for dr_member_menu
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_menu`;
CREATE TABLE `dr_member_menu` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) unsigned NOT NULL COMMENT '上级菜单id',
  `name` text NOT NULL COMMENT '菜单名称',
  `uri` varchar(255) DEFAULT NULL COMMENT 'uri字符串',
  `url` varchar(255) DEFAULT NULL COMMENT 'url',
  `mark` varchar(50) DEFAULT NULL COMMENT '菜单标识',
  `hidden` tinyint(1) unsigned DEFAULT NULL COMMENT '是否隐藏',
  `target` tinyint(3) unsigned DEFAULT NULL COMMENT '新窗口',
  `displayorder` tinyint(3) unsigned DEFAULT NULL COMMENT '排序值',
  `icon` varchar(30) DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`),
  KEY `list` (`pid`),
  KEY `displayorder` (`displayorder`),
  KEY `mark` (`mark`),
  KEY `hidden` (`hidden`),
  KEY `uri` (`uri`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COMMENT='会员菜单表';

-- ----------------------------
-- Records of dr_member_menu
-- ----------------------------
INSERT INTO `dr_member_menu` VALUES ('1', '0', '账号', '', '', '', '0', '0', '0', 'icon-user');
INSERT INTO `dr_member_menu` VALUES ('2', '0', '财务', '', '', '', '0', '0', '0', 'fa fa-rmb');
INSERT INTO `dr_member_menu` VALUES ('3', '0', '空间', '', '', 'm_space', '0', '0', '0', '');
INSERT INTO `dr_member_menu` VALUES ('4', '0', '应用', '', '', 'm_app', '0', '0', '0', 'fa fa-cloud');
INSERT INTO `dr_member_menu` VALUES ('43', '16', '转账服务', 'pay/transfer', '', '', '0', '0', '0', 'fa fa-rmb');
INSERT INTO `dr_member_menu` VALUES ('5', '1', '基本管理', '', '', '', '0', '0', '0', 'fa fa-cogs');
INSERT INTO `dr_member_menu` VALUES ('6', '5', '基本资料', 'account/index', '', '', '0', '0', '0', 'fa fa-cog');
INSERT INTO `dr_member_menu` VALUES ('10', '1', '会员相关', '', '', '', '0', '0', '0', 'fa fa-user');
INSERT INTO `dr_member_menu` VALUES ('11', '5', '修改密码', 'account/password', '', '', '0', '0', '0', 'icon-lock');
INSERT INTO `dr_member_menu` VALUES ('12', '10', '快捷登录', 'account/oauth', '', '', '0', '0', '0', 'fa fa-weibo');
INSERT INTO `dr_member_menu` VALUES ('13', '10', '登录记录', 'account/login', '', '', '0', '0', '0', 'fa fa-calendar-o');
INSERT INTO `dr_member_menu` VALUES ('14', '10', '会员升级', 'account/upgrade', '', '', '0', '0', '0', 'fa fa-user-plus');
INSERT INTO `dr_member_menu` VALUES ('15', '10', '会员权限', 'account/permission', '', '', '0', '0', '0', 'fa fa-users');
INSERT INTO `dr_member_menu` VALUES ('16', '2', '充值付款', '', '', '', '0', '0', '0', 'fa fa-rmb');
INSERT INTO `dr_member_menu` VALUES ('17', '2', '财务记录', '', '', '', '0', '0', '0', 'fa fa-calendar-o');
INSERT INTO `dr_member_menu` VALUES ('18', '16', '在线充值', 'pay/add', '', '', '0', '0', '0', 'fa fa-rmb');
INSERT INTO `dr_member_menu` VALUES ('20', '16', '兑换服务', 'pay/convert', '', '', '0', '0', '0', 'fa fa-exchange');
INSERT INTO `dr_member_menu` VALUES ('21', '17', '收入记录', 'pay/index', '', '', '0', '0', '0', 'fa fa-calendar-plus-o');
INSERT INTO `dr_member_menu` VALUES ('22', '17', '消费记录', 'pay/spend', '', '', '0', '0', '0', 'fa fa-calendar-minus-o');
INSERT INTO `dr_member_menu` VALUES ('23', '17', '经验值记录', 'pay/experience', '', '', '0', '0', '0', 'fa fa-compass');
INSERT INTO `dr_member_menu` VALUES ('24', '17', '虚拟币记录', 'pay/score', '', '', '0', '0', '0', 'fa fa-diamond');
INSERT INTO `dr_member_menu` VALUES ('25', '3', '基本设置', '', '', '', '0', '0', '0', 'fa fa-cog');
INSERT INTO `dr_member_menu` VALUES ('26', '3', '内容管理', '', '', 'm_space_content', '0', '0', '99', 'fa fa-database');
INSERT INTO `dr_member_menu` VALUES ('27', '25', '空间设置', 'space/space/index', '', '', '0', '0', '0', 'fa fa-cog');
INSERT INTO `dr_member_menu` VALUES ('28', '25', '模板设置', 'space/space/template', '', '', '0', '0', '0', 'fa fa-html5');
INSERT INTO `dr_member_menu` VALUES ('29', '26', '栏目分类', 'space/category/index', '', '', '0', '0', '0', 'fa fa-database');
INSERT INTO `dr_member_menu` VALUES ('30', '4', '我的应用', '', '', '', '0', '0', '99', 'fa fa-cloud');
INSERT INTO `dr_member_menu` VALUES ('31', '26', '文章管理', 'space/space1/index', '', 'space-1', '0', '0', '0', 'fa fa-navicon');
INSERT INTO `dr_member_menu` VALUES ('32', '26', '外链管理', 'space/space2/index', '', 'space-2', '0', '0', '0', 'fa fa-navicon');
INSERT INTO `dr_member_menu` VALUES ('33', '26', '日志管理', 'space/space3/index', '', 'space-3', '0', '0', '0', 'fa fa-navicon');
INSERT INTO `dr_member_menu` VALUES ('34', '26', '相册管理', 'space/space4/index', '', 'space-4', '0', '0', '0', 'fa fa-navicon');
INSERT INTO `dr_member_menu` VALUES ('35', '26', '幻灯管理', 'space/space5/index', '', 'space-5', '0', '0', '0', 'fa fa-navicon');
INSERT INTO `dr_member_menu` VALUES ('37', '3', '空间互动', '', '', '', '0', '0', '0', 'fa fa-weibo');
INSERT INTO `dr_member_menu` VALUES ('38', '37', '我的动态', 'space/sns/index', '', '', '0', '0', '0', 'fa fa-weibo');
INSERT INTO `dr_member_menu` VALUES ('39', '37', '我关注的', 'space/sns/follow', '', '', '0', '0', '0', 'fa fa-share-alt-square');
INSERT INTO `dr_member_menu` VALUES ('40', '37', '我的粉丝', 'space/sns/fans', '', '', '0', '0', '0', 'fa fa-user');
INSERT INTO `dr_member_menu` VALUES ('41', '25', '空间权限', 'space/sns/config', '', '', '0', '0', '99', 'fa fa-cog');
INSERT INTO `dr_member_menu` VALUES ('42', '25', '二级域名', 'space/space/domain', '', '', '0', '0', '95', 'fa fa-wifi');
INSERT INTO `dr_member_menu` VALUES ('45', '5', '上传头像', 'account/avatar', '', '', '0', '0', '0', 'icon-picture');
INSERT INTO `dr_member_menu` VALUES ('46', '5', '收货地址', 'address/index', '', '', '0', '0', '0', 'icon-home');
INSERT INTO `dr_member_menu` VALUES ('47', '5', '附件管理', 'account/attachment', '', '', '0', '0', '0', 'fa fa-folder');
INSERT INTO `dr_member_menu` VALUES ('48', '1', '通知提醒', '', '', '', '0', '0', '0', 'fa fa-bell-o');
INSERT INTO `dr_member_menu` VALUES ('50', '48', '系统提醒', 'notice/index', '', '', '0', '0', '0', 'fa fa-bell');
INSERT INTO `dr_member_menu` VALUES ('51', '48', '互动提醒', 'notice/member', '', '', '0', '0', '0', 'fa fa-at');
INSERT INTO `dr_member_menu` VALUES ('52', '48', '模块提醒', 'notice/module', '', '', '0', '0', '0', 'fa fa-whatsapp');
INSERT INTO `dr_member_menu` VALUES ('53', '48', '应用提醒', 'notice/app', '', '', '0', '0', '0', 'fa fa-volume-up');
INSERT INTO `dr_member_menu` VALUES ('54', '0', '内容', '', '', 'm_mod', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('55', '54', '文档管理', '', '', 'left-news', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('56', '55', '已通过的文档', 'news/home/index', '', 'module-news', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('57', '55', '待审核的文档', 'news/verify/index', '', 'module-news', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('58', '55', '被退回的文档', 'news/back/index', '', 'module-news', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('59', '55', '我推荐的文档', 'news/home/flag', '', 'module-news', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('60', '55', '我收藏的文档', 'news/home/favorite', '', 'module-news', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('61', '55', '我购买的文档', 'news/home/buy', '', 'module-news', '0', '0', '0', 'fa fa-th-large');
INSERT INTO `dr_member_menu` VALUES ('62', '55', '我评论的文档', 'news/comment/index', '', 'module-news', '0', '0', '0', 'fa fa-th-large');

-- ----------------------------
-- Table structure for dr_member_new_notice
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_new_notice`;
CREATE TABLE `dr_member_new_notice` (
  `uid` smallint(8) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新通知提醒表';

-- ----------------------------
-- Records of dr_member_new_notice
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_0
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_0`;
CREATE TABLE `dr_member_notice_0` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_0
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_1
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_1`;
CREATE TABLE `dr_member_notice_1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_1
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_2
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_2`;
CREATE TABLE `dr_member_notice_2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_2
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_3
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_3`;
CREATE TABLE `dr_member_notice_3` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_3
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_4
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_4`;
CREATE TABLE `dr_member_notice_4` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_4
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_5
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_5`;
CREATE TABLE `dr_member_notice_5` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_5
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_6
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_6`;
CREATE TABLE `dr_member_notice_6` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_6
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_7
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_7`;
CREATE TABLE `dr_member_notice_7` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_7
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_8
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_8`;
CREATE TABLE `dr_member_notice_8` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_8
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_notice_9
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_notice_9`;
CREATE TABLE `dr_member_notice_9` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `uid` mediumint(8) unsigned NOT NULL COMMENT '通知者uid',
  `isnew` tinyint(1) unsigned NOT NULL COMMENT '新提醒',
  `content` text NOT NULL COMMENT '通知内容',
  `inputtime` int(10) unsigned NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `isnew` (`isnew`),
  KEY `type` (`type`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员通知提醒表';

-- ----------------------------
-- Records of dr_member_notice_9
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_oauth
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_oauth`;
CREATE TABLE `dr_member_oauth` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员OAuth2授权表';

-- ----------------------------
-- Records of dr_member_oauth
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_online
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_online`;
CREATE TABLE `dr_member_online` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(10) unsigned NOT NULL COMMENT '在线时间',
  PRIMARY KEY (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员在线情况表';

-- ----------------------------
-- Records of dr_member_online
-- ----------------------------
INSERT INTO `dr_member_online` VALUES ('1', '1498643418');

-- ----------------------------
-- Table structure for dr_member_paylog
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_paylog`;
CREATE TABLE `dr_member_paylog` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `value` decimal(10,2) NOT NULL COMMENT '价格',
  `type` varchar(20) NOT NULL COMMENT '类型',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态',
  `order` varchar(255) DEFAULT NULL COMMENT '下单详情',
  `module` varchar(30) NOT NULL COMMENT '应用或模块目录',
  `note` varchar(255) NOT NULL COMMENT '备注',
  `inputtime` int(10) unsigned NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `order` (`order`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付记录表';

-- ----------------------------
-- Records of dr_member_paylog
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_scorelog
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_scorelog`;
CREATE TABLE `dr_member_scorelog` (
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

-- ----------------------------
-- Records of dr_member_scorelog
-- ----------------------------

-- ----------------------------
-- Table structure for dr_member_setting
-- ----------------------------
DROP TABLE IF EXISTS `dr_member_setting`;
CREATE TABLE `dr_member_setting` (
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员属性参数表';

-- ----------------------------
-- Records of dr_member_setting
-- ----------------------------
INSERT INTO `dr_member_setting` VALUES ('member', '');
INSERT INTO `dr_member_setting` VALUES ('permission', 'a:10:{i:1;a:13:{s:16:\\\"login_experience\\\";s:1:\\\"1\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"1\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:10:\\\"attachsize\\\";s:1:\\\"0\\\";}i:2;a:14:{s:16:\\\"login_experience\\\";s:1:\\\"5\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"1\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:1:\\\"5\\\";}s:3:\\\"3_1\\\";a:15:{s:16:\\\"login_experience\\\";s:1:\\\"5\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"2\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:2:\\\"10\\\";}s:3:\\\"3_2\\\";a:15:{s:16:\\\"login_experience\\\";s:1:\\\"5\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"2\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:2:\\\"10\\\";}s:3:\\\"3_3\\\";a:15:{s:16:\\\"login_experience\\\";s:1:\\\"5\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:2:\\\"10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"2\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:2:\\\"20\\\";}s:3:\\\"3_4\\\";a:15:{s:16:\\\"login_experience\\\";s:1:\\\"5\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"3\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:2:\\\"30\\\";}s:3:\\\"4_5\\\";a:15:{s:16:\\\"login_experience\\\";s:2:\\\"10\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:2:\\\"10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"5\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:2:\\\"50\\\";}s:3:\\\"4_6\\\";a:15:{s:16:\\\"login_experience\\\";s:2:\\\"10\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"5\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:2:\\\"70\\\";}s:3:\\\"4_7\\\";a:15:{s:16:\\\"login_experience\\\";s:2:\\\"10\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"5\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:3:\\\"100\\\";}s:3:\\\"4_8\\\";a:15:{s:16:\\\"login_experience\\\";s:2:\\\"10\\\";s:11:\\\"login_score\\\";s:1:\\\"0\\\";s:17:\\\"avatar_experience\\\";s:2:\\\"10\\\";s:12:\\\"avatar_score\\\";s:1:\\\"0\\\";s:19:\\\"complete_experience\\\";s:2:\\\"10\\\";s:14:\\\"complete_score\\\";s:1:\\\"0\\\";s:15:\\\"bang_experience\\\";s:2:\\\"10\\\";s:10:\\\"bang_score\\\";s:1:\\\"0\\\";s:14:\\\"jie_experience\\\";s:3:\\\"-10\\\";s:9:\\\"jie_score\\\";s:1:\\\"0\\\";s:17:\\\"update_experience\\\";s:1:\\\"5\\\";s:12:\\\"update_score\\\";s:1:\\\"0\\\";s:9:\\\"is_upload\\\";s:1:\\\"1\\\";s:11:\\\"is_download\\\";s:1:\\\"1\\\";s:10:\\\"attachsize\\\";s:1:\\\"0\\\";}}');
INSERT INTO `dr_member_setting` VALUES ('pay', 'a:2:{s:6:\\\"tenpay\\\";a:3:{s:4:\\\"name\\\";s:9:\\\"财付通\\\";s:2:\\\"id\\\";s:0:\\\"\\\";s:3:\\\"key\\\";s:0:\\\"\\\";}s:6:\\\"alipay\\\";a:4:{s:4:\\\"name\\\";s:9:\\\"支付宝\\\";s:8:\\\"username\\\";s:0:\\\"\\\";s:2:\\\"id\\\";s:0:\\\"\\\";s:3:\\\"key\\\";s:0:\\\"\\\";}}');
INSERT INTO `dr_member_setting` VALUES ('space', 'a:9:{s:6:\\\"domain\\\";s:0:\\\"\\\";s:4:\\\"edit\\\";s:1:\\\"1\\\";s:6:\\\"verify\\\";s:1:\\\"0\\\";s:7:\\\"rewrite\\\";s:1:\\\"0\\\";s:7:\\\"seojoin\\\";s:1:\\\"_\\\";s:5:\\\"title\\\";s:41:\\\"会员空间_FineCMS自助建站平台！\\\";s:8:\\\"keywords\\\";s:0:\\\"\\\";s:11:\\\"description\\\";s:0:\\\"\\\";s:4:\\\"flag\\\";a:9:{i:1;a:1:{s:4:\\\"name\\\";s:12:\\\"达人空间\\\";}i:2;a:1:{s:4:\\\"name\\\";s:12:\\\"推荐空间\\\";}i:3;a:1:{s:4:\\\"name\\\";s:0:\\\"\\\";}i:4;a:1:{s:4:\\\"name\\\";s:0:\\\"\\\";}i:5;a:1:{s:4:\\\"name\\\";s:0:\\\"\\\";}i:6;a:1:{s:4:\\\"name\\\";s:0:\\\"\\\";}i:7;a:1:{s:4:\\\"name\\\";s:0:\\\"\\\";}i:8;a:1:{s:4:\\\"name\\\";s:0:\\\"\\\";}i:9;a:1:{s:4:\\\"name\\\";s:0:\\\"\\\";}}}');

-- ----------------------------
-- Table structure for dr_module
-- ----------------------------
DROP TABLE IF EXISTS `dr_module`;
CREATE TABLE `dr_module` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site` text COMMENT '站点划分',
  `dirname` varchar(50) NOT NULL COMMENT '目录名称',
  `share` tinyint(1) unsigned DEFAULT NULL COMMENT '是否共享模块',
  `extend` tinyint(1) unsigned DEFAULT NULL COMMENT '是否是扩展模块',
  `sitemap` tinyint(1) unsigned DEFAULT NULL COMMENT '是否生成地图',
  `setting` text COMMENT '配置信息',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '禁用？',
  `displayorder` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dirname` (`dirname`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='模块表';

-- ----------------------------
-- Records of dr_module
-- ----------------------------
INSERT INTO `dr_module` VALUES ('1', '{\"1\":{\"use\":1,\"html\":0,\"theme\":\"default\",\"domain\":\"\",\"template\":\"default\"}}', 'news', '1', '0', '1', '', '0', '0');

-- ----------------------------
-- Table structure for dr_module_form
-- ----------------------------
DROP TABLE IF EXISTS `dr_module_form`;
CREATE TABLE `dr_module_form` (
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

-- ----------------------------
-- Records of dr_module_form
-- ----------------------------

-- ----------------------------
-- Table structure for dr_site
-- ----------------------------
DROP TABLE IF EXISTS `dr_site`;
CREATE TABLE `dr_site` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '站点名称',
  `domain` varchar(50) NOT NULL COMMENT '站点域名',
  `setting` text NOT NULL COMMENT '站点配置',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='站点表';

-- ----------------------------
-- Records of dr_site
-- ----------------------------
INSERT INTO `dr_site` VALUES ('1', 'POSCMS', 'localhost', '{\"SITE_CLOSE\":0,\"SITE_CLOSE_MSG\":\"\\u7f51\\u7ad9\\u5347\\u7ea7\\u4e2d....\",\"SITE_NAME\":\"POSCMS\",\"SITE_TIME_FORMAT\":\"Y-m-d H:i\",\"SITE_LANGUAGE\":\"zh-cn\",\"SITE_THEME\":\"default\",\"SITE_TEMPLATE\":\"default\",\"SITE_TIMEZONE\":8,\"SITE_DOMAINS\":\"\",\"SITE_REWRITE\":0,\"SITE_MOBILE_OPEN\":1,\"SITE_MOBILE\":\"\",\"SITE_SEOJOIN\":\"_\",\"SITE_TITLE\":\"\",\"SITE_KEYWORDS\":\"\",\"SITE_DESCRIPTION\":\"\",\"SITE_IMAGE_RATIO\":1,\"SITE_IMAGE_WATERMARK\":0,\"SITE_IMAGE_REMOTE\":1,\"SITE_IMAGE_VRTALIGN\":\"top\",\"SITE_IMAGE_HORALIGN\":\"left\",\"SITE_IMAGE_VRTOFFSET\":\"\",\"SITE_IMAGE_HOROFFSET\":\"\",\"SITE_IMAGE_TYPE\":1,\"SITE_IMAGE_OVERLAY\":\"default.png\",\"SITE_IMAGE_OPACITY\":77,\"SITE_IMAGE_FONT\":\"default.ttf\",\"SITE_IMAGE_COLOR\":\"\",\"SITE_IMAGE_SIZE\":\"\",\"SITE_IMAGE_TEXT\":\"\",\"SITE_DOMAIN\":\"localhost\",\"SITE_NAVIGATOR\":\"\\u4e3b\\u5bfc\\u822a,\\u9996\\u9875\\u5e7b\\u706f,\\u9996\\u9875\\u5934\\u6761,\\u5e95\\u90e8\\u5bfc\\u822a,\\u53cb\\u60c5\\u94fe\\u63a5\",\"SITE_IMAGE_CONTENT\":0,\"SITE_ATTACH_URL\":\"localhost\",\"SITE_ATTACH_HOST\":\"localhost\"}');

-- ----------------------------
-- Table structure for dr_urlrule
-- ----------------------------
DROP TABLE IF EXISTS `dr_urlrule`;
CREATE TABLE `dr_urlrule` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '规则类型',
  `name` varchar(50) NOT NULL COMMENT '规则名称',
  `value` text NOT NULL COMMENT '详细规则',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='URL规则表';

-- ----------------------------
-- Records of dr_urlrule
-- ----------------------------
INSERT INTO `dr_urlrule` VALUES ('1', '0', '单页测试规则', 'a:52:{s:4:\\\"page\\\";s:14:\\\"page-{id}.html\\\";s:9:\\\"page_page\\\";s:21:\\\"page-{id}-{page}.html\\\";s:6:\\\"module\\\";s:0:\\\"\\\";s:4:\\\"list\\\";s:0:\\\"\\\";s:9:\\\"list_page\\\";s:0:\\\"\\\";s:4:\\\"show\\\";s:0:\\\"\\\";s:9:\\\"show_page\\\";s:0:\\\"\\\";s:6:\\\"extend\\\";s:0:\\\"\\\";s:11:\\\"extend_page\\\";s:0:\\\"\\\";s:3:\\\"tag\\\";s:0:\\\"\\\";s:8:\\\"tag_page\\\";s:0:\\\"\\\";s:6:\\\"search\\\";s:0:\\\"\\\";s:11:\\\"search_page\\\";s:0:\\\"\\\";s:9:\\\"share_tag\\\";s:0:\\\"\\\";s:14:\\\"share_tag_page\\\";s:0:\\\"\\\";s:12:\\\"share_search\\\";s:0:\\\"\\\";s:17:\\\"share_search_page\\\";s:0:\\\"\\\";s:10:\\\"share_list\\\";s:0:\\\"\\\";s:15:\\\"share_list_page\\\";s:0:\\\"\\\";s:10:\\\"share_show\\\";s:0:\\\"\\\";s:15:\\\"share_show_page\\\";s:0:\\\"\\\";s:12:\\\"share_extend\\\";s:0:\\\"\\\";s:17:\\\"share_extend_page\\\";s:0:\\\"\\\";s:9:\\\"so_search\\\";s:0:\\\"\\\";s:14:\\\"so_search_page\\\";s:0:\\\"\\\";s:7:\\\"sitemap\\\";s:0:\\\"\\\";s:5:\\\"space\\\";s:0:\\\"\\\";s:12:\\\"space_search\\\";s:0:\\\"\\\";s:17:\\\"space_search_page\\\";s:0:\\\"\\\";s:5:\\\"uhome\\\";s:0:\\\"\\\";s:5:\\\"ulist\\\";s:0:\\\"\\\";s:10:\\\"ulist_page\\\";s:0:\\\"\\\";s:5:\\\"ushow\\\";s:0:\\\"\\\";s:10:\\\"ushow_page\\\";s:0:\\\"\\\";s:8:\\\"sns_show\\\";s:0:\\\"\\\";s:9:\\\"sns_topic\\\";s:0:\\\"\\\";s:14:\\\"sns_topic_page\\\";s:0:\\\"\\\";s:3:\\\"sns\\\";s:0:\\\"\\\";s:8:\\\"sns_page\\\";s:0:\\\"\\\";s:12:\\\"ulist_domain\\\";s:0:\\\"\\\";s:17:\\\"ulist_domain_page\\\";s:0:\\\"\\\";s:12:\\\"ushow_domain\\\";s:0:\\\"\\\";s:17:\\\"ushow_domain_page\\\";s:0:\\\"\\\";s:15:\\\"sns_show_domain\\\";s:0:\\\"\\\";s:16:\\\"sns_topic_domain\\\";s:0:\\\"\\\";s:21:\\\"sns_topic_domain_page\\\";s:0:\\\"\\\";s:10:\\\"sns_domain\\\";s:0:\\\"\\\";s:15:\\\"sns_domain_page\\\";s:0:\\\"\\\";s:6:\\\"member\\\";s:0:\\\"\\\";s:10:\\\"member_reg\\\";s:0:\\\"\\\";s:12:\\\"member_login\\\";s:0:\\\"\\\";s:7:\\\"catjoin\\\";s:1:\\\"/\\\";}');
INSERT INTO `dr_urlrule` VALUES ('2', '0', '单页测试规则（用于模块）', 'a:52:{s:4:\\\"page\\\";s:21:\\\"module-page-{id}.html\\\";s:9:\\\"page_page\\\";s:28:\\\"module-page-{id}-{page}.html\\\";s:6:\\\"module\\\";s:0:\\\"\\\";s:4:\\\"list\\\";s:0:\\\"\\\";s:9:\\\"list_page\\\";s:0:\\\"\\\";s:4:\\\"show\\\";s:0:\\\"\\\";s:9:\\\"show_page\\\";s:0:\\\"\\\";s:6:\\\"extend\\\";s:0:\\\"\\\";s:11:\\\"extend_page\\\";s:0:\\\"\\\";s:3:\\\"tag\\\";s:0:\\\"\\\";s:8:\\\"tag_page\\\";s:0:\\\"\\\";s:6:\\\"search\\\";s:0:\\\"\\\";s:11:\\\"search_page\\\";s:0:\\\"\\\";s:9:\\\"share_tag\\\";s:0:\\\"\\\";s:14:\\\"share_tag_page\\\";s:0:\\\"\\\";s:12:\\\"share_search\\\";s:0:\\\"\\\";s:17:\\\"share_search_page\\\";s:0:\\\"\\\";s:10:\\\"share_list\\\";s:0:\\\"\\\";s:15:\\\"share_list_page\\\";s:0:\\\"\\\";s:10:\\\"share_show\\\";s:0:\\\"\\\";s:15:\\\"share_show_page\\\";s:0:\\\"\\\";s:12:\\\"share_extend\\\";s:0:\\\"\\\";s:17:\\\"share_extend_page\\\";s:0:\\\"\\\";s:9:\\\"so_search\\\";s:0:\\\"\\\";s:14:\\\"so_search_page\\\";s:0:\\\"\\\";s:7:\\\"sitemap\\\";s:0:\\\"\\\";s:5:\\\"space\\\";s:0:\\\"\\\";s:12:\\\"space_search\\\";s:0:\\\"\\\";s:17:\\\"space_search_page\\\";s:0:\\\"\\\";s:5:\\\"uhome\\\";s:0:\\\"\\\";s:5:\\\"ulist\\\";s:0:\\\"\\\";s:10:\\\"ulist_page\\\";s:0:\\\"\\\";s:5:\\\"ushow\\\";s:0:\\\"\\\";s:10:\\\"ushow_page\\\";s:0:\\\"\\\";s:8:\\\"sns_show\\\";s:0:\\\"\\\";s:9:\\\"sns_topic\\\";s:0:\\\"\\\";s:14:\\\"sns_topic_page\\\";s:0:\\\"\\\";s:3:\\\"sns\\\";s:0:\\\"\\\";s:8:\\\"sns_page\\\";s:0:\\\"\\\";s:12:\\\"ulist_domain\\\";s:0:\\\"\\\";s:17:\\\"ulist_domain_page\\\";s:0:\\\"\\\";s:12:\\\"ushow_domain\\\";s:0:\\\"\\\";s:17:\\\"ushow_domain_page\\\";s:0:\\\"\\\";s:15:\\\"sns_show_domain\\\";s:0:\\\"\\\";s:16:\\\"sns_topic_domain\\\";s:0:\\\"\\\";s:21:\\\"sns_topic_domain_page\\\";s:0:\\\"\\\";s:10:\\\"sns_domain\\\";s:0:\\\"\\\";s:15:\\\"sns_domain_page\\\";s:0:\\\"\\\";s:6:\\\"member\\\";s:0:\\\"\\\";s:10:\\\"member_reg\\\";s:0:\\\"\\\";s:12:\\\"member_login\\\";s:0:\\\"\\\";s:7:\\\"catjoin\\\";s:1:\\\"/\\\";}');
INSERT INTO `dr_urlrule` VALUES ('3', '1', '独立模块默认规则', 'a:52:{s:4:\\\"page\\\";s:0:\\\"\\\";s:9:\\\"page_page\\\";s:0:\\\"\\\";s:6:\\\"module\\\";s:14:\\\"{modname}.html\\\";s:4:\\\"list\\\";s:29:\\\"{modname}-list-{dirname}.html\\\";s:9:\\\"list_page\\\";s:36:\\\"{modname}-list-{dirname}-{page}.html\\\";s:4:\\\"show\\\";s:24:\\\"{modname}-show-{id}.html\\\";s:9:\\\"show_page\\\";s:31:\\\"{modname}-show-{id}-{page}.html\\\";s:6:\\\"extend\\\";s:24:\\\"{modname}-read-{id}.html\\\";s:11:\\\"extend_page\\\";s:31:\\\"{modname}-read-{id}-{page}.html\\\";s:3:\\\"tag\\\";s:24:\\\"{modname}-tag-{tag}.html\\\";s:8:\\\"tag_page\\\";s:31:\\\"{modname}-tag-{tag}-{page}.html\\\";s:6:\\\"search\\\";s:21:\\\"{modname}/search.html\\\";s:11:\\\"search_page\\\";s:29:\\\"{modname}/search-{param}.html\\\";s:9:\\\"share_tag\\\";s:0:\\\"\\\";s:14:\\\"share_tag_page\\\";s:0:\\\"\\\";s:12:\\\"share_search\\\";s:0:\\\"\\\";s:17:\\\"share_search_page\\\";s:0:\\\"\\\";s:10:\\\"share_list\\\";s:0:\\\"\\\";s:15:\\\"share_list_page\\\";s:0:\\\"\\\";s:10:\\\"share_show\\\";s:0:\\\"\\\";s:15:\\\"share_show_page\\\";s:0:\\\"\\\";s:12:\\\"share_extend\\\";s:0:\\\"\\\";s:17:\\\"share_extend_page\\\";s:0:\\\"\\\";s:9:\\\"so_search\\\";s:0:\\\"\\\";s:14:\\\"so_search_page\\\";s:0:\\\"\\\";s:7:\\\"sitemap\\\";s:0:\\\"\\\";s:5:\\\"space\\\";s:0:\\\"\\\";s:12:\\\"space_search\\\";s:0:\\\"\\\";s:17:\\\"space_search_page\\\";s:0:\\\"\\\";s:5:\\\"uhome\\\";s:0:\\\"\\\";s:5:\\\"ulist\\\";s:0:\\\"\\\";s:10:\\\"ulist_page\\\";s:0:\\\"\\\";s:5:\\\"ushow\\\";s:0:\\\"\\\";s:10:\\\"ushow_page\\\";s:0:\\\"\\\";s:8:\\\"sns_show\\\";s:0:\\\"\\\";s:9:\\\"sns_topic\\\";s:0:\\\"\\\";s:14:\\\"sns_topic_page\\\";s:0:\\\"\\\";s:3:\\\"sns\\\";s:0:\\\"\\\";s:8:\\\"sns_page\\\";s:0:\\\"\\\";s:12:\\\"ulist_domain\\\";s:0:\\\"\\\";s:17:\\\"ulist_domain_page\\\";s:0:\\\"\\\";s:12:\\\"ushow_domain\\\";s:0:\\\"\\\";s:17:\\\"ushow_domain_page\\\";s:0:\\\"\\\";s:15:\\\"sns_show_domain\\\";s:0:\\\"\\\";s:16:\\\"sns_topic_domain\\\";s:0:\\\"\\\";s:21:\\\"sns_topic_domain_page\\\";s:0:\\\"\\\";s:10:\\\"sns_domain\\\";s:0:\\\"\\\";s:15:\\\"sns_domain_page\\\";s:0:\\\"\\\";s:6:\\\"member\\\";s:0:\\\"\\\";s:10:\\\"member_reg\\\";s:0:\\\"\\\";s:12:\\\"member_login\\\";s:0:\\\"\\\";s:7:\\\"catjoin\\\";s:1:\\\"/\\\";}');
INSERT INTO `dr_urlrule` VALUES ('4', '2', '共享模块测试', 'a:52:{s:4:\\\"page\\\";s:0:\\\"\\\";s:9:\\\"page_page\\\";s:0:\\\"\\\";s:6:\\\"module\\\";s:0:\\\"\\\";s:4:\\\"list\\\";s:0:\\\"\\\";s:9:\\\"list_page\\\";s:0:\\\"\\\";s:4:\\\"show\\\";s:0:\\\"\\\";s:9:\\\"show_page\\\";s:0:\\\"\\\";s:6:\\\"extend\\\";s:0:\\\"\\\";s:11:\\\"extend_page\\\";s:0:\\\"\\\";s:3:\\\"tag\\\";s:0:\\\"\\\";s:8:\\\"tag_page\\\";s:0:\\\"\\\";s:6:\\\"search\\\";s:0:\\\"\\\";s:11:\\\"search_page\\\";s:0:\\\"\\\";s:9:\\\"share_tag\\\";s:24:\\\"{modname}-tag-{tag}.html\\\";s:14:\\\"share_tag_page\\\";s:31:\\\"{modname}-tag-{tag}-{page}.html\\\";s:12:\\\"share_search\\\";s:21:\\\"{modname}/search.html\\\";s:17:\\\"share_search_page\\\";s:29:\\\"{modname}/search/{param}.html\\\";s:10:\\\"share_list\\\";s:10:\\\"{dirname}/\\\";s:15:\\\"share_list_page\\\";s:26:\\\"{dirname}/page/{page}.html\\\";s:10:\\\"share_show\\\";s:24:\\\"{dirname}/show/{id}.html\\\";s:15:\\\"share_show_page\\\";s:36:\\\"{dirname}/show/{id}/page/{page}.html\\\";s:12:\\\"share_extend\\\";s:24:\\\"{dirname}/read/{id}.html\\\";s:17:\\\"share_extend_page\\\";s:36:\\\"{dirname}/read/{id}/page/{page}.html\\\";s:9:\\\"so_search\\\";s:0:\\\"\\\";s:14:\\\"so_search_page\\\";s:0:\\\"\\\";s:7:\\\"sitemap\\\";s:0:\\\"\\\";s:5:\\\"space\\\";s:0:\\\"\\\";s:12:\\\"space_search\\\";s:0:\\\"\\\";s:17:\\\"space_search_page\\\";s:0:\\\"\\\";s:5:\\\"uhome\\\";s:0:\\\"\\\";s:5:\\\"ulist\\\";s:0:\\\"\\\";s:10:\\\"ulist_page\\\";s:0:\\\"\\\";s:5:\\\"ushow\\\";s:0:\\\"\\\";s:10:\\\"ushow_page\\\";s:0:\\\"\\\";s:8:\\\"sns_show\\\";s:0:\\\"\\\";s:9:\\\"sns_topic\\\";s:0:\\\"\\\";s:14:\\\"sns_topic_page\\\";s:0:\\\"\\\";s:3:\\\"sns\\\";s:0:\\\"\\\";s:8:\\\"sns_page\\\";s:0:\\\"\\\";s:12:\\\"ulist_domain\\\";s:0:\\\"\\\";s:17:\\\"ulist_domain_page\\\";s:0:\\\"\\\";s:12:\\\"ushow_domain\\\";s:0:\\\"\\\";s:17:\\\"ushow_domain_page\\\";s:0:\\\"\\\";s:15:\\\"sns_show_domain\\\";s:0:\\\"\\\";s:16:\\\"sns_topic_domain\\\";s:0:\\\"\\\";s:21:\\\"sns_topic_domain_page\\\";s:0:\\\"\\\";s:10:\\\"sns_domain\\\";s:0:\\\"\\\";s:15:\\\"sns_domain_page\\\";s:0:\\\"\\\";s:6:\\\"member\\\";s:0:\\\"\\\";s:10:\\\"member_reg\\\";s:0:\\\"\\\";s:12:\\\"member_login\\\";s:0:\\\"\\\";s:7:\\\"catjoin\\\";s:1:\\\"/\\\";}');
INSERT INTO `dr_urlrule` VALUES ('5', '3', '共享栏目规则测试', 'a:52:{s:4:\\\"page\\\";s:0:\\\"\\\";s:9:\\\"page_page\\\";s:0:\\\"\\\";s:6:\\\"module\\\";s:0:\\\"\\\";s:4:\\\"list\\\";s:0:\\\"\\\";s:9:\\\"list_page\\\";s:0:\\\"\\\";s:4:\\\"show\\\";s:0:\\\"\\\";s:9:\\\"show_page\\\";s:0:\\\"\\\";s:6:\\\"extend\\\";s:0:\\\"\\\";s:11:\\\"extend_page\\\";s:0:\\\"\\\";s:3:\\\"tag\\\";s:0:\\\"\\\";s:8:\\\"tag_page\\\";s:0:\\\"\\\";s:6:\\\"search\\\";s:0:\\\"\\\";s:11:\\\"search_page\\\";s:0:\\\"\\\";s:9:\\\"share_tag\\\";s:0:\\\"\\\";s:14:\\\"share_tag_page\\\";s:0:\\\"\\\";s:12:\\\"share_search\\\";s:0:\\\"\\\";s:17:\\\"share_search_page\\\";s:0:\\\"\\\";s:10:\\\"share_list\\\";s:24:\\\"html/{dirname}-list.html\\\";s:15:\\\"share_list_page\\\";s:31:\\\"html/{dirname}-list-{page}.html\\\";s:10:\\\"share_show\\\";s:29:\\\"html/{dirname}-show-{id}.html\\\";s:15:\\\"share_show_page\\\";s:36:\\\"html/{dirname}-show-{id}-{page}.html\\\";s:12:\\\"share_extend\\\";s:31:\\\"html/{dirname}-extend-{id}.html\\\";s:17:\\\"share_extend_page\\\";s:38:\\\"html/{dirname}-extend-{id}-{page}.html\\\";s:9:\\\"so_search\\\";s:0:\\\"\\\";s:14:\\\"so_search_page\\\";s:0:\\\"\\\";s:7:\\\"sitemap\\\";s:0:\\\"\\\";s:5:\\\"space\\\";s:0:\\\"\\\";s:12:\\\"space_search\\\";s:0:\\\"\\\";s:17:\\\"space_search_page\\\";s:0:\\\"\\\";s:5:\\\"uhome\\\";s:0:\\\"\\\";s:5:\\\"ulist\\\";s:0:\\\"\\\";s:10:\\\"ulist_page\\\";s:0:\\\"\\\";s:5:\\\"ushow\\\";s:0:\\\"\\\";s:10:\\\"ushow_page\\\";s:0:\\\"\\\";s:8:\\\"sns_show\\\";s:0:\\\"\\\";s:9:\\\"sns_topic\\\";s:0:\\\"\\\";s:14:\\\"sns_topic_page\\\";s:0:\\\"\\\";s:3:\\\"sns\\\";s:0:\\\"\\\";s:8:\\\"sns_page\\\";s:0:\\\"\\\";s:12:\\\"ulist_domain\\\";s:0:\\\"\\\";s:17:\\\"ulist_domain_page\\\";s:0:\\\"\\\";s:12:\\\"ushow_domain\\\";s:0:\\\"\\\";s:17:\\\"ushow_domain_page\\\";s:0:\\\"\\\";s:15:\\\"sns_show_domain\\\";s:0:\\\"\\\";s:16:\\\"sns_topic_domain\\\";s:0:\\\"\\\";s:21:\\\"sns_topic_domain_page\\\";s:0:\\\"\\\";s:10:\\\"sns_domain\\\";s:0:\\\"\\\";s:15:\\\"sns_domain_page\\\";s:0:\\\"\\\";s:6:\\\"member\\\";s:0:\\\"\\\";s:10:\\\"member_reg\\\";s:0:\\\"\\\";s:12:\\\"member_login\\\";s:0:\\\"\\\";s:7:\\\"catjoin\\\";s:1:\\\"/\\\";}');
INSERT INTO `dr_urlrule` VALUES ('6', '4', '站点URL测试', 'a:52:{s:4:\\\"page\\\";s:0:\\\"\\\";s:9:\\\"page_page\\\";s:0:\\\"\\\";s:6:\\\"module\\\";s:0:\\\"\\\";s:4:\\\"list\\\";s:0:\\\"\\\";s:9:\\\"list_page\\\";s:0:\\\"\\\";s:4:\\\"show\\\";s:0:\\\"\\\";s:9:\\\"show_page\\\";s:0:\\\"\\\";s:6:\\\"extend\\\";s:0:\\\"\\\";s:11:\\\"extend_page\\\";s:0:\\\"\\\";s:3:\\\"tag\\\";s:0:\\\"\\\";s:8:\\\"tag_page\\\";s:0:\\\"\\\";s:6:\\\"search\\\";s:0:\\\"\\\";s:11:\\\"search_page\\\";s:0:\\\"\\\";s:9:\\\"share_tag\\\";s:0:\\\"\\\";s:14:\\\"share_tag_page\\\";s:0:\\\"\\\";s:12:\\\"share_search\\\";s:11:\\\"search.html\\\";s:17:\\\"share_search_page\\\";s:19:\\\"search/{param}.html\\\";s:10:\\\"share_list\\\";s:0:\\\"\\\";s:15:\\\"share_list_page\\\";s:0:\\\"\\\";s:10:\\\"share_show\\\";s:0:\\\"\\\";s:15:\\\"share_show_page\\\";s:0:\\\"\\\";s:12:\\\"share_extend\\\";s:0:\\\"\\\";s:17:\\\"share_extend_page\\\";s:0:\\\"\\\";s:9:\\\"so_search\\\";s:7:\\\"so.html\\\";s:14:\\\"so_search_page\\\";s:15:\\\"so-{param}.html\\\";s:7:\\\"sitemap\\\";s:12:\\\"sitemap.html\\\";s:5:\\\"space\\\";s:0:\\\"\\\";s:12:\\\"space_search\\\";s:0:\\\"\\\";s:17:\\\"space_search_page\\\";s:0:\\\"\\\";s:5:\\\"uhome\\\";s:0:\\\"\\\";s:5:\\\"ulist\\\";s:0:\\\"\\\";s:10:\\\"ulist_page\\\";s:0:\\\"\\\";s:5:\\\"ushow\\\";s:0:\\\"\\\";s:10:\\\"ushow_page\\\";s:0:\\\"\\\";s:8:\\\"sns_show\\\";s:0:\\\"\\\";s:9:\\\"sns_topic\\\";s:0:\\\"\\\";s:14:\\\"sns_topic_page\\\";s:0:\\\"\\\";s:3:\\\"sns\\\";s:0:\\\"\\\";s:8:\\\"sns_page\\\";s:0:\\\"\\\";s:12:\\\"ulist_domain\\\";s:0:\\\"\\\";s:17:\\\"ulist_domain_page\\\";s:0:\\\"\\\";s:12:\\\"ushow_domain\\\";s:0:\\\"\\\";s:17:\\\"ushow_domain_page\\\";s:0:\\\"\\\";s:15:\\\"sns_show_domain\\\";s:0:\\\"\\\";s:16:\\\"sns_topic_domain\\\";s:0:\\\"\\\";s:21:\\\"sns_topic_domain_page\\\";s:0:\\\"\\\";s:10:\\\"sns_domain\\\";s:0:\\\"\\\";s:15:\\\"sns_domain_page\\\";s:0:\\\"\\\";s:6:\\\"member\\\";s:0:\\\"\\\";s:10:\\\"member_reg\\\";s:0:\\\"\\\";s:12:\\\"member_login\\\";s:0:\\\"\\\";s:7:\\\"catjoin\\\";s:1:\\\"/\\\";}');
INSERT INTO `dr_urlrule` VALUES ('7', '5', '空间黄页测试地址', 'a:50:{s:4:\\\"page\\\";s:0:\\\"\\\";s:9:\\\"page_page\\\";s:0:\\\"\\\";s:6:\\\"module\\\";s:0:\\\"\\\";s:4:\\\"list\\\";s:0:\\\"\\\";s:9:\\\"list_page\\\";s:0:\\\"\\\";s:4:\\\"show\\\";s:0:\\\"\\\";s:9:\\\"show_page\\\";s:0:\\\"\\\";s:6:\\\"extend\\\";s:0:\\\"\\\";s:11:\\\"extend_page\\\";s:0:\\\"\\\";s:3:\\\"tag\\\";s:0:\\\"\\\";s:8:\\\"tag_page\\\";s:0:\\\"\\\";s:6:\\\"search\\\";s:0:\\\"\\\";s:11:\\\"search_page\\\";s:0:\\\"\\\";s:9:\\\"share_tag\\\";s:0:\\\"\\\";s:14:\\\"share_tag_page\\\";s:0:\\\"\\\";s:12:\\\"share_search\\\";s:0:\\\"\\\";s:17:\\\"share_search_page\\\";s:0:\\\"\\\";s:10:\\\"share_list\\\";s:0:\\\"\\\";s:15:\\\"share_list_page\\\";s:0:\\\"\\\";s:10:\\\"share_show\\\";s:0:\\\"\\\";s:15:\\\"share_show_page\\\";s:0:\\\"\\\";s:12:\\\"share_extend\\\";s:0:\\\"\\\";s:17:\\\"share_extend_page\\\";s:0:\\\"\\\";s:9:\\\"so_search\\\";s:0:\\\"\\\";s:14:\\\"so_search_page\\\";s:0:\\\"\\\";s:7:\\\"sitemap\\\";s:0:\\\"\\\";s:5:\\\"space\\\";s:7:\\\"hy.html\\\";s:12:\\\"space_search\\\";s:14:\\\"hy-search.html\\\";s:17:\\\"space_search_page\\\";s:22:\\\"hy-search-{param}.html\\\";s:5:\\\"uhome\\\";s:12:\\\"u-{uid}.html\\\";s:5:\\\"ulist\\\";s:22:\\\"u-{uid}-list-{id}.html\\\";s:10:\\\"ulist_page\\\";s:29:\\\"u-{uid}-list-{id}-{page}.html\\\";s:5:\\\"ushow\\\";s:28:\\\"u-{uid}-show-{mid}-{id}.html\\\";s:10:\\\"ushow_page\\\";s:35:\\\"u-{uid}-show-{mid}-{id}-{page}.html\\\";s:8:\\\"sns_show\\\";s:26:\\\"u-{uid}-sns-show-{id}.html\\\";s:9:\\\"sns_topic\\\";s:27:\\\"u-{uid}-sns-topic-{id}.html\\\";s:14:\\\"sns_topic_page\\\";s:34:\\\"u-{uid}-sns-topic-{id}-{page}.html\\\";s:3:\\\"sns\\\";s:23:\\\"u-{uid}-sns-{name}.html\\\";s:8:\\\"sns_page\\\";s:30:\\\"u-{uid}-sns-{name}-{page}.html\\\";s:12:\\\"ulist_domain\\\";s:16:\\\"u-list-{id}.html\\\";s:17:\\\"ulist_domain_page\\\";s:23:\\\"u-list-{id}-{page}.html\\\";s:12:\\\"ushow_domain\\\";s:22:\\\"u-show-{mid}-{id}.html\\\";s:17:\\\"ushow_domain_page\\\";s:29:\\\"u-show-{mid}-{id}-{page}.html\\\";s:15:\\\"sns_show_domain\\\";s:20:\\\"u-sns-show-{id}.html\\\";s:16:\\\"sns_topic_domain\\\";s:21:\\\"u-sns-topic-{id}.html\\\";s:21:\\\"sns_topic_domain_page\\\";s:28:\\\"u-sns-topic-{id}-{page}.html\\\";s:10:\\\"sns_domain\\\";s:22:\\\"u-sns-{name}-{id}.html\\\";s:15:\\\"sns_domain_page\\\";s:29:\\\"u-sns-{name}-{id}-{page}.html\\\";s:6:\\\"member\\\";s:0:\\\"\\\";s:7:\\\"catjoin\\\";s:1:\\\"/\\\";}');
INSERT INTO `dr_urlrule` VALUES ('8', '6', '会员部分测试', 'a:52:{s:4:\\\"page\\\";s:0:\\\"\\\";s:9:\\\"page_page\\\";s:0:\\\"\\\";s:6:\\\"module\\\";s:0:\\\"\\\";s:4:\\\"list\\\";s:0:\\\"\\\";s:9:\\\"list_page\\\";s:0:\\\"\\\";s:4:\\\"show\\\";s:0:\\\"\\\";s:9:\\\"show_page\\\";s:0:\\\"\\\";s:6:\\\"extend\\\";s:0:\\\"\\\";s:11:\\\"extend_page\\\";s:0:\\\"\\\";s:3:\\\"tag\\\";s:0:\\\"\\\";s:8:\\\"tag_page\\\";s:0:\\\"\\\";s:6:\\\"search\\\";s:0:\\\"\\\";s:11:\\\"search_page\\\";s:0:\\\"\\\";s:9:\\\"share_tag\\\";s:0:\\\"\\\";s:14:\\\"share_tag_page\\\";s:0:\\\"\\\";s:12:\\\"share_search\\\";s:0:\\\"\\\";s:17:\\\"share_search_page\\\";s:0:\\\"\\\";s:10:\\\"share_list\\\";s:0:\\\"\\\";s:15:\\\"share_list_page\\\";s:0:\\\"\\\";s:10:\\\"share_show\\\";s:0:\\\"\\\";s:15:\\\"share_show_page\\\";s:0:\\\"\\\";s:12:\\\"share_extend\\\";s:0:\\\"\\\";s:17:\\\"share_extend_page\\\";s:0:\\\"\\\";s:9:\\\"so_search\\\";s:0:\\\"\\\";s:14:\\\"so_search_page\\\";s:0:\\\"\\\";s:7:\\\"sitemap\\\";s:0:\\\"\\\";s:5:\\\"space\\\";s:0:\\\"\\\";s:12:\\\"space_search\\\";s:0:\\\"\\\";s:17:\\\"space_search_page\\\";s:0:\\\"\\\";s:5:\\\"uhome\\\";s:0:\\\"\\\";s:5:\\\"ulist\\\";s:0:\\\"\\\";s:10:\\\"ulist_page\\\";s:0:\\\"\\\";s:5:\\\"ushow\\\";s:0:\\\"\\\";s:10:\\\"ushow_page\\\";s:0:\\\"\\\";s:8:\\\"sns_show\\\";s:0:\\\"\\\";s:9:\\\"sns_topic\\\";s:0:\\\"\\\";s:14:\\\"sns_topic_page\\\";s:0:\\\"\\\";s:3:\\\"sns\\\";s:0:\\\"\\\";s:8:\\\"sns_page\\\";s:0:\\\"\\\";s:12:\\\"ulist_domain\\\";s:0:\\\"\\\";s:17:\\\"ulist_domain_page\\\";s:0:\\\"\\\";s:12:\\\"ushow_domain\\\";s:0:\\\"\\\";s:17:\\\"ushow_domain_page\\\";s:0:\\\"\\\";s:15:\\\"sns_show_domain\\\";s:0:\\\"\\\";s:16:\\\"sns_topic_domain\\\";s:0:\\\"\\\";s:21:\\\"sns_topic_domain_page\\\";s:0:\\\"\\\";s:10:\\\"sns_domain\\\";s:0:\\\"\\\";s:15:\\\"sns_domain_page\\\";s:0:\\\"\\\";s:6:\\\"member\\\";s:11:\\\"member.html\\\";s:10:\\\"member_reg\\\";s:13:\\\"register.html\\\";s:12:\\\"member_login\\\";s:10:\\\"login.html\\\";s:7:\\\"catjoin\\\";s:0:\\\"\\\";}');

-- ----------------------------
-- Table structure for dr_var
-- ----------------------------
DROP TABLE IF EXISTS `dr_var`;
CREATE TABLE `dr_var` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `name` varchar(100) NOT NULL COMMENT '变量描述名称',
  `cname` varchar(100) NOT NULL COMMENT '变量名称',
  `type` tinyint(2) NOT NULL COMMENT '变量类型',
  `value` text NOT NULL COMMENT '变量值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自定义变量表';

-- ----------------------------
-- Records of dr_var
-- ----------------------------
