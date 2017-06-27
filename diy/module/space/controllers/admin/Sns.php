<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.5.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */
	
class Sns extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('动态管理') => array('space/admin/sns/index', 'weibo'),
		    fc_lang('话题管理') => array('space/admin/sns/topic', 'header'),
		)));
		$this->load->model('sns_model');
    }
	
	/**
     * 动态管理
     */
    public function index() {

        if (IS_POST && $this->input->post('action')) {
            // ID格式判断
            $ids = $this->input->post('ids');
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            // 删除
            if (!$this->is_auth('space/admin/sns/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
            foreach ($ids as $id) {
                $this->sns_model->delete($id);
            }
            $this->system_log('删除会员动态【#'.@implode(',', $ids).'】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        // 重置页数和统计
        if (IS_POST) {
            $_GET['page'] = $_GET['total'] = 0;
        }

        // 根据参数筛选结果
        $param = array();
        if ($this->input->get('search')) {
            $param['search'] = 1;
        }

        // 数据库中分页查询
        list($data, $_param, $_search) = $this->sns_model->feed_limit_page(
            $param,
            max((int)$_GET['page'], 1),
            (int)$_GET['total']
        );
        $param = $_param ? $param + $_param : $param;
        $field = array(
            'username' => array('fieldname' => 'username','name' => fc_lang('会员名称')),
            'content' => array('fieldname' => 'content','name' => fc_lang('主题'))
        );
        $search = $_search ? $param + $_search : $param;
        $this->template->assign(array(
            'list' => $data,
            'field' => $field,
            'param' => $search,
            'pages'	=> $this->get_pagination(dr_url('space/sns/index', $param), $param['total']),
        ));
        $this->template->display('sns_index.html');
    }

	/**
     * 话题管理
     */
    public function topic() {

        if (IS_POST && $this->input->post('action')) {
            // ID格式判断
            $ids = $this->input->post('ids');
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            // 删除
            if (!$this->is_auth('space/admin/sns/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
            foreach ($ids as $id) {
                $this->sns_model->delete_topic($id);
            }
            $this->system_log('删除会员话题【#'.@implode(',', $ids).'】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        // 重置页数和统计
        if (IS_POST) {
            $_GET['page'] = $_GET['total'] = 0;
        }

        // 根据参数筛选结果
        $param = array();
        if ($this->input->get('search')) {
            $param['search'] = 1;
        }

        // 数据库中分页查询
        list($data, $_param, $_search) = $this->sns_model->topic_limit_page(
            $param,
            max((int)$_GET['page'], 1),
            (int)$_GET['total']
        );
        $param = $_param ? $param + $_param : $param;
        $field = array(
            'username' => array('fieldname' => 'username','name' => fc_lang('发起人')),
            'name' => array('fieldname' => 'name','name' => fc_lang('话题'))
        );
        $search = $_search ? $param + $_search : $param;
        $this->template->assign(array(
            'list' => $data,
            'field' => $field,
            'param' => $search,
            'pages'	=> $this->get_pagination(dr_url('space/topic/index', $param), $param['total']),
        ));
        $this->template->display('sns_topic.html');
    }

}