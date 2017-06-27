<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.7.1
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */
	
class Model extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('空间模型') => array('space/admin/model/index', 'table'),
		    fc_lang('添加') => array('space/admin/model/add', 'plus')
		)));
		$this->load->model('space_model_model');
    }
	
	/**
     * 管理
     */
    public function index() {
		$this->template->assign(array(
			'list' => $this->db->get($this->db->dbprefix('space_model'))->result_array(),
		));
		$this->template->display('model_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST) {
			
			$data = $this->input->post('data');
			$result = $this->space_model_model->add($data);
			if ($result === TRUE) {
				/* 更新相关缓存 */
				$this->space_model_model->cache();
				$this->load->model('menu_model');
				$this->menu_model->cache();
				$this->load->model('member_model');
				$this->member_model->cache();
				/* 更新相关缓存 */
                $this->system_log('添加会员空间模型【#'.$data['table'].'】'); // 记录日志
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('space/model/index'), 1);
			}
			
		}
		
		$this->template->assign(array(
			'data' => $data,
			'result' => $result,
		));
		$this->template->display('model_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db
					 ->where('id', $id)
					 ->limit(1)
					 ->get($this->db->dbprefix('space_model'))
					 ->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }
		
		if (IS_POST) {
			$data = $this->input->post('data', TRUE);
			$this->space_model_model->edit($id, $data);
            /* 更新相关缓存 */
            $this->space_model_model->cache();
            $this->load->model('menu_model');
            $this->menu_model->cache();
            $this->load->model('member_model');
            $this->member_model->cache();
            /* 更新相关缓存 */
            $this->system_log('修改会员空间模型【#'.$data['table'].'】'); // 记录日志
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('space/model/index'), 1);
		}
		
		$data['setting'] = dr_string2array($data['setting']);
		
		$this->template->assign(array(
			'data' => $data,
		));
		$this->template->display('model_add.html');
    }
	
	/**
     * 删除
     */
    public function del() {
        $id = (int)$this->input->get('id');
		$this->space_model_model->del($id);
        /* 更新相关缓存 */
        $this->space_model_model->cache();
        $this->load->model('menu_model');
        $this->menu_model->cache();
        $this->load->model('member_model');
        $this->member_model->cache();
        /* 更新相关缓存 */
        $this->system_log('添加会员空间模型【#'.$id.'】'); // 记录日志
		$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('space/model/index'), 1);
	}
	
	/**
     * 缓存
     */
    public function cache() {

		if (MEMBER_OPEN_SPACE) {
			$this->space_model_model->cache();
		}
		$this->input->get('admin') or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}