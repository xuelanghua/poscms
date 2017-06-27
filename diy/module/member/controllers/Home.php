<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Home extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index() {

		// 登录验证
		$url = dr_member_url('login/index', array('backurl' => urlencode(dr_now_url())));
		!$this->uid && $this->member_msg(fc_lang('会话超时，请重新登录').$this->member_model->logout(), $url);

		$this->load->library('dip');
		$this->template->assign(array(
			'indexu' => 1,
			'invite' => dr_get_invite($this->uid, 'username'),
			'invite_url' => dr_member_url('register/index', array('uid' => $this->uid, 'invite' => $this->member['username'])),

		));
		$this->template->display(IS_AJAX ? 'main.html' : 'index.html');
    }
	



}