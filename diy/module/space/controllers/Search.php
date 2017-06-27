<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.0.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource	svn://www.dayrui.net/v2/news/controllers/search.php
 */

class Search extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 搜索
     */
    public function index() {

        // 搜索参数
        $get = $this->input->get(NULL, TRUE);
        $get = isset($get['rewrite']) ? dr_rewrite_decode($get['rewrite']) : $get;
        $_GET['page'] = max(1, (int)$get['page']);
        $get['keyword'] = str_replace(array('%', ' '), array('', '%'), $get['keyword']);
        unset($get['s'], $get['c'], $get['m'], $get['page']);

        // URL定向
        $url = dr_space_search_url($get);
        if (IS_PC && $url != dr_now_url()) {
            redirect($url, 'location', '301');exit;
        }

        $where = '';
        if ($get) {
            $field = $this->get_cache('member', 'field');
            $space = $this->get_cache('member', 'spacefield');
            foreach ($get as $name => $v) {
                if (isset($space[$name]) && $space[$name]['fieldtype'] == 'Linkage') {
                    // 组合空间字段的联动菜单
                    $link = dr_linkage($space[$name]['setting']['option']['linkage'], $v);
                    if ($link) {
                        if ($link['child']) {
                            $where.= 'IN_'.$name.'='.$link['childids'].' ';
                        } else {
                            $where.= $name.'='.$link['ii'].' ';
                        }
                    }
                } elseif (isset($field[$name]) && $field[$name]['fieldtype'] == 'Linkage') {
                    // 组合会员字段的联动菜单
                    $link = dr_linkage($field[$name]['setting']['option']['linkage'], $v);
                    if ($link) {
                        if ($link['child']) {
                            $where.= 'IN_'.$name.'='.$link['childids'].' ';
                        } else {
                            $where.= $name.'='.$link['ii'].' ';
                        }
                    }
                } else {
                    $where.= $name.'='.$v.' ';
                }
            }
        }

        $this->template->assign(array(
            'get' => $get,
            'where' => $where,
            'params' => $get,
            'keyword' => $get['keyword'],
            'urlrule' => dr_space_search_url($get, 'page', '[page]'),
            'meta_title' => $this->space['title'],
            'meta_keywords' => $this->space['keywords'],
            'meta_description' => $this->space['description'],
        ));
        $this->template->display('search.html');
    }
}