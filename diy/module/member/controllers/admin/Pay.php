<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Pay extends M_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('pay_model');
	}

	/**
	 * 首页
	 */
	public function index() {

		// 重置页数和统计
		IS_POST && $_GET['page'] = $_GET['total'] = 0;

		// 根据参数筛选结果
		$param = $this->input->get(NULL, TRUE);
		unset($param['s'], $param['c'], $param['m'], $param['d'], $param['page']);

		// 数据库中分页查询
		$this->load->model('pay_model');
		list($data, $param) = $this->pay_model->limit_page(
			$param,
			max((int)$this->input->get('page'), 1),
			(int)$this->input->get('total')
		);

		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('财务流水') => array('member/admin/pay/'.$this->router->method, 'calculator'),
				fc_lang('添加') => array('member/admin/pay/add_js', 'plus')
			)),
			'pay' => $this->get_pay_api(1),
			'list' => $data,
			'param'	=> $param,
			'pages'	=> $this->get_pagination(dr_url('member/pay/'.$this->router->method, $param), $param['total']),
		));
		$this->template->display('pay_index.html');
	}

	/**
	 * 充值
	 */
	public function add() {


		if (IS_POST) {
			
			$data = $this->input->post('data');
			$value = intval($data['value']);
			!$value && exit(dr_json(0, fc_lang('请填写变动数量值'), 'value'));
			
			$userinfo = $this->db->where('username', $data['username'])->get('member')->row_array();
			!$userinfo && exit(dr_json(0, fc_lang('会员不存在')));
			
			$uid = intval($userinfo['uid']);
			$data['value'] < 0 && $userinfo['money'] + $data['value'] < 0 && exit(dr_json(0, fc_lang('%s值超出了账户余额', $data['value'])));
			
			$this->pay_model->add($uid, $data['value'], $data['note']);
			$this->member_model->add_notice($userinfo['uid'], 1, fc_lang('%s变动：%s；本次操作人：%s', SITE_MONEY, $value, $this->member['username']));
			$this->system_log('会员【'.$userinfo['username'].'】充值金额【'.$value.'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}

		$this->template->display('pay_add.html');
	}
}