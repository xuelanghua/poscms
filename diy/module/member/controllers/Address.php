<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Address extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->load->model('address_model');
    }
	
    /**
     * 收货地址
     */
    public function index() {
		$this->template->assign(array(
			'list' => $this->db
						   ->where('uid', $this->uid)
						   ->order_by('id asc')
						   ->get('member_address')
						   ->result_array()
		));
		$this->template->display('address_index.html');
    }
	
	/**
     * 添加地址
     */
    public function add() {
	
		if (IS_POST) {
			$data = $this->validate_filter($this->address_model->get_address_field());
			if (isset($data['error'])) {
				(IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $data['msg'], $data['error']));
				$error = $data['error'].$data['msg'];
			} else {
				$this->address_model->add_address($data[1]);
				$this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('address/index'), 1);
			}
		}
		
		$this->template->assign(array(
			'data' => $data,
			'result_error' => $error,
		));
		$this->template->display('address_add.html');
    }
	
	/**
	 * 修改收货地址
	 */
	public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->address_model->get_address($id);
		
		if (IS_POST) {
			$data = $this->validate_filter($this->address_model->get_address_field(), $data);
			if (isset($data['error'])) {
				$error = $data['error'];
				(IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
			} else {
				$this->address_model->edit_address($id, $data[1]);
				$this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('address/index'), 1);
			}
		}
		
		$this->template->assign(array(
			'data' => $data,
			'result_error' => $error,
		));
		$this->template->display('address_add.html');
	}
	
	/**
	 * 删除收货地址
	 */
	public function del() {
		$id = (int)$this->input->get('id');
		$this->db
			 ->where('id', $id)
			 ->where('uid', $this->uid)
			 ->delete('member_address');
		$this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('address/index'), 1);
	}
}