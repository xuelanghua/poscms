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
	  `body` text DEFAULT NULL COMMENT "分集简介",
	  `video` text DEFAULT NULL COMMENT "视频信息",
	  UNIQUE KEY `id` (`id`),
	  KEY `uid` (`uid`),
	  KEY `catid` (`catid`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT="扩展表";
	',
	
	'field' => array(
		array(
			'textname' => '视频信息', // 字段显示名称
			'fieldname' => 'video', // 字段名称
			'fieldtype'	=> 'Video', // 字段类别
			'setting' => array(
				'option' => array(
					'ext' => 'mp4',
					'size' => 10, 
					'width' => '90%', // 表单宽度
				),
				'validate' => array(
					'tips' => '', // 提示信息
					'required' => 1, // 表示必填
				)
			)
		),
		array(
			'textname' => '分集简介', // 字段显示名称
			'fieldname' => 'body', // 字段名称
			'fieldtype'	=> 'Ueditor', // 字段类别
			'setting' => array(
				'option' => array(
					'mode' => 2, // 工具栏模式
					'width' => '90%', // 表单宽度
					'height' => 300, // 表单高度
				),
				'validate' => array(
					'xss' => 1, // xss过滤
				)
			)
		),
	)

);