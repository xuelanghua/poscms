<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Level extends M_Controller {

	public $groupid;
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->groupid = (int)$this->input->get('gid');
		!$this->groupid && $this->admin_msg(fc_lang('会员组不存在'));
		$this->groupid < 2 && $this->admin_msg(fc_lang('该会员组无等级功能'));
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('会员组模型') => array('member/admin/group/index', 'users'),
				fc_lang('等级管理') => array('member/admin/level/index/gid/'.$this->groupid, 'signal'),
				fc_lang('添加') => array('member/admin/level/add/gid/'.$this->groupid, 'plus'),
			)),
			'groupid' => $this->groupid
		));
		$this->load->model('level_model');
    }

    /**
     * 管理
     */
    public function index() {
		if (IS_POST) {
			if ($this->input->post('action') == 'del') { // 删除
                $ids = $this->input->post('ids');
				$this->level_model->del($ids);
                $this->clear_cache('member');
                $this->system_log('删除会员等级【#'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功')));
			} elseif ($this->input->post('action') == 'edit') { // 修改

			}
		}
		$this->template->assign(array(
			'list' => $this->level_model->get_data(),
		));
		$this->template->display('level_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
		$page = (int)$this->input->get('page');
		$error = 0;
		if (IS_POST) {
			$data = $this->input->post('data', TRUE);
			$page = (int)$this->input->post('page');
			if (!$data['name']) {
				$error = fc_lang('名称必须填写');
			} else {
				$this->level_model->add($data);
                $this->clear_cache('member');
                $this->system_log('添加会员等级【'.$data['name'].'】'); // 记录日志
				$this->admin_msg(fc_lang('操作成功'), dr_url('member/level/index', array('gid' => $this->groupid)), 1);
			}
		}
		$this->template->assign(array(
			'page' => $page,
			'error' => $error,
		));
		$this->template->display('level_add.html');
    }
	
	/**
     * 修改
     */
    public function edit() {
		
		$id = (int)$this->input->get('id');
		$data = $this->level_model->get($id);
		!$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
		
		$page = (int)$this->input->get('page');
		$error = 0;
		if (IS_POST) {
			$_data = $data;
			$data = $this->input->post('data', TRUE);
			$page = (int)$this->input->post('page');
			if (!$data['name']) {
				$error = fc_lang('名称必须填写');
			} else {
				$this->level_model->edit($_data, $data);
                $this->clear_cache('member');
                $this->system_log('修改会员等级【'.$data['name'].'】'); // 记录日志
				$this->admin_msg(fc_lang('操作成功'), dr_url('member/level/index', array('gid' => $this->groupid)), 1);
			}
		}
		
		$this->template->assign(array(
			'page' => $page,
			'data' => $data,
			'error' => $error
		));
		$this->template->display('level_add.html');
    }
	
	/**
     * 删除
     */
    public function del() {
        $id = (int)$this->input->get('id');
		$this->level_model->del($id);
        $this->system_log('删除会员等级【#'.$id.'】'); // 记录日志
        $this->clear_cache('member');
		exit(dr_json(1, fc_lang('操作成功')));
	}
}