<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 管理后台菜单分布
 *
 * array(
 *		'name' => '分组菜单名称',
 *		'menu' => array(
 *			array(
 *				'name' => '链接菜单名称',
 *				'uri' => '链接菜单的uri'
 *			)
 *			......
 *		)
 * )
 * .......
 */

return array(

	// 后台菜单部分
	
	'admin' => array(
		array(
			'name' => '视频管理',
			'menu' => array(
				array(
					'name' => '已通过视频',
					'uri' => 'admin/home/index'
				),
				array(
					'name' => '待审核视频',
					'uri' => 'admin/home/verify'
				),
				array(
					'name' => '已通过分集',
					'uri' => 'admin/extend/index'
				),
                array(
                    'name' => '待审核分集',
                    'uri' => 'admin/verify/index'
                ),
                array(
                    'name' => '我的草稿箱',
                    'uri' => 'admin/home/draft'
                ),
				array(
					'name' => '栏目分类',
					'uri' => 'admin/category/index'
				),
				array(
					'name' => 'Tag标签',
					'uri' => 'admin/tag/index'
				),
				array(
					'name' => '单页管理',
					'uri' => 'admin/page/index'
				),
			),
		),
		
		array(
			'name' => '相关功能',
			'menu' => array(
                array(
                    'name' => '内容维护',
                    'uri' => 'admin/home/content'
                ),
				array(
					'name' => '生成静态',
					'uri' => 'admin/home/html'
				),
				array(
					'name' => '自定义字段',
					'uri' => 'admin/field/index/rname/module/rid/{id}'
				),
				array(
					'name' => '自定义视频字段',
					'uri' => 'admin/field/index/rname/extend/rid/{id}'
				),
                array(
                    'name' => '模块属性配置',
                    'uri' => 'admin/module/config/id/{id}'
                ),
			),
		),

        array(
            'name' => '视频评论',
            'menu' => array(
                array(
                    'name' => '评论设置',
                    'uri' => 'admin/comment/config'
                ),
                array(
                    'name' => '评论管理',
                    'icon' => 'icon-comments',
                    'uri' => 'admin/comment/index'
                ),
                array(
                    'name' => '自定义字段',
                    'uri' => 'admin/field/index/rname/comment-module-{dir}/rid/0'
                ),
            ),
        ),

        array(
            'name' => '分集评论',
            'menu' => array(
                array(
                    'name' => '评论设置',
                    'uri' => 'admin/ecomment/config'
                ),
                array(
                    'name' => '评论管理',
                    'icon' => 'icon-comments',
                    'uri' => 'admin/ecomment/index'
                ),
                array(
                    'name' => '自定义字段',
                    'uri' => 'admin/field/index/rname/comment-extend-{dir}/rid/0'
                ),
            ),
        ),
		
		array(
			'name' => '模板风格',
			'menu' => array(
				array(
					'name' => '模板管理',
					'uri' => 'admin/tpl/index'
				),
				array(
					'name' => '风格管理',
					'uri' => 'admin/theme/index'
				),
				array(
					'name' => '标签向导',
					'uri' => 'admin/tpl/tag'
				),
			),
		)
	),
	
	//  会员菜单部分
	
	'member' => array(
		array(
			'name' => '视频管理',
			'menu' => array(
				array(
					'name' => '已通过的视频',
					'uri' => 'home/index',
				),
				array(
					'name' => '待审核的视频',
					'uri' => 'verify/index',
				),
				array(
					'name' => '被退回的视频',
					'uri' => 'back/index',
				),
                array(
                    'name' => '待审核的分集',
                    'uri' => 'everify/index',
                ),
                array(
                    'name' => '被退回的分集',
                    'uri' => 'eback/index',
                ),
				array(
					'name' => '已推荐的视频',
					'uri' => 'home/flag',
				),
				array(
					'name' => '我收藏的视频',
					'uri' => 'home/favorite',
				),
				array(
					'name' => '我购买的视频',
					'uri' => 'home/buy',
				),
                array(
                    'name' => '我购买的分集',
                    'uri' => 'extend/buy',
                ),
                array(
                    'name' => '我评论的视频',
                    'uri' => 'comment/index',
                ),
                array(
                    'name' => '我评论的分集',
                    'uri' => 'ecomment/index',
                ),
			)
		)
	),
	
);