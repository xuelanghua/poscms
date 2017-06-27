<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Pm extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		if (!dr_is_app('pms')) {
			$this->member_msg(fc_lang('尚未安装【站内短消息】应用'));
		}
    }

    /**
     * 消息管理
     */
    public function index() {

        $this->member_msg(fc_lang('跳转中...'), dr_member_url('pms/home/index'), 2);
    }
	
	/**
     * 发送消息
     */
    public function send() {


    }
	
	/**
     * 阅读消息页
     */
    public function read() {


    }
	

}