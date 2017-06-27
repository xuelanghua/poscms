<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 扩展表结构（由开发者定义）
 *
 * sql: 初始化SQL语句，用{tablename}表示表名称
 * filed：初始化的自定义字段，可以用来由用户修改的字段
 */

return array(

	'sql' => '
	CREATE TABLE IF NOT EXISTS `{tablename}` (
	  `id` int(10) unsigned NOT NULL,
	  `cid` mediumint(8) unsigned NOT NULL COMMENT "内容id",
	  `uid` mediumint(8) unsigned NOT NULL COMMENT "作者uid",
	  `catid` smallint(5) unsigned NOT NULL COMMENT "栏目id",
	  `author` varchar(50) NOT NULL COMMENT "作者名称",
	  `name` varchar(255) DEFAULT NULL COMMENT "名称",
	  `url` varchar(255) DEFAULT NULL COMMENT "地址",
	  `status` tinyint(2) NOT NULL COMMENT "审核状态",
	  `tableid` smallint(5) unsigned NOT NULL COMMENT "副表id",
	  `inputtime` int(10) unsigned NOT NULL COMMENT "录入时间",
	  `updatetime` int(10) unsigned NOT NULL COMMENT "更新时间",
  	  `comments` int(10) unsigned  DEFAULT 0 COMMENT "评论数量",
  	  `favorites` int(10) unsigned  DEFAULT 0 COMMENT "收藏数量",
	  `hits` mediumint(8) unsigned DEFAULT NULL COMMENT "浏览数",
	  `displayorder` tinyint(3) NOT NULL DEFAULT "0",
	  UNIQUE KEY `id` (`id`),
	  KEY `uid` (`uid`),
	  KEY `catid` (`catid`),
	  KEY `status` (`status`),
	  KEY `hits` (`hits`),
	  KEY `comments` (`comments`),
	  KEY `favorites` (`favorites`),
	  KEY `displayorder` (`displayorder`,`updatetime`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="扩展表";
	',
	
	'field' => array(
		array(
			'textname' => '章节名',	// 字段显示名称
			'fieldname' => 'name',	// 字段名称
			'fieldtype'	=> 'Text',	// 字段类别
			'setting' => array(
				'option' => array(
					'width' => 400, // 表单宽度
					'fieldtype' => 'VARCHAR', // 字段类型
					'fieldlength' => '255' // 字段长度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
					'required' => 1, // 表示必填
				)
			)
		)
	)

);