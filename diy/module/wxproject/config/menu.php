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
			'name' => '商品管理',
			'menu' => array(
				array(
					'name' => '商品分类列表',
					'uri' => 'admin/category/index'
				),
				array(
					'name' => '产品列表',
					'uri' => 'admin/goods/index'
				),
				array(
                    'name' => '自定义字段',
                    'uri' => 'admin/field/index/rname/comment-module-{dir}/rid/0'
                ),
            )
		),

        array(
            'name' => '门店管理',
            'menu' => array(
                array(
                    'name' => '门店列表',
                    'uri' => 'admin/store/index'
                ),
            ),
        ),
		
		array(
			'name' => '幻灯片管理',
			'menu' => array(
				array(
					'name' => '幻灯片列表',
					'uri' => 'admin/slide/index'
				),
			),
		),
	
	),
	
	//  会员菜单部分
	
	'member' => array(
		array(
			'name' => '商品管理',
			'menu' => array(
				array(
					'name' => '商品分类列表',
					'uri' => 'admin/category/index'
				),
				array(
					'name' => '产品列表',
					'uri' => 'admin/goods/index'
				),
				array(
                    'name' => '自定义字段',
                    'uri' => 'admin/field/index/rname/comment-module-{dir}/rid/0'
            )   ),
		),

        array(
            'name' => '门店管理',
            'menu' => array(
                array(
                    'name' => '门店列表',
                    'uri' => 'admin/store/index'
                ),
            ),
        ),
		
		array(
			'name' => '幻灯片管理',
			'menu' => array(
				array(
					'name' => '幻灯片列表',
					'uri' => 'admin/slide/index'
				),
			),
		),
	),
);