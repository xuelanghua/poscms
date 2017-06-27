<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Register extends M_Controller {

	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 注册
	 */
	public function index() {

		// 会员配置
		$MEMBER = $this->get_cache('MEMBER');

		// 来路认证
		$json = 0;
		$auth = $this->input->get('auth');
		if ($auth) {
			$auth != md5(SYS_KEY) && exit($this->callback_json(array(
				'msg' => '授权认证码不正确',
				'code' => 0
			)));
			!IS_POST && exit($this->callback_json(array(
				'msg' => '请用POST方式提交',
				'code' => 0,
			)));
			$json = 1;
			$MEMBER['setting']['regcode'] = 0;
		}

		$groupid = (int)$this->input->get('groupid');

		// 判断是否开启注册
		if (!$MEMBER['setting']['register']) {
			$json && exit($this->callback_json(array(
				'msg' => fc_lang('站点已经关闭了会员注册'),
				'code' => 0
			)));
			$this->member_msg(fc_lang('站点已经关闭了会员注册'));
		} elseif ($groupid && !$MEMBER['group'][$groupid]['allowregister']) {
			// 指定模型注册验证
			$json && exit($this->callback_json(array(
				'msg' => fc_lang('此会员组模型系统不允许注册'),
				'code' => 0
			)));
			$this->member_msg(fc_lang('此会员组模型系统不允许注册'));
		} elseif ($this->member && !$json) {
			// 已经登录不允许注册
			$this->member_msg(fc_lang('您已经登录了，不能注册'));
		}

		if (IS_POST) {
			$data = $this->input->post('data', TRUE);
			$back_url = $_POST['back'] ? urldecode($this->input->post('back')) : '';
			$back_url = $back_url && strpos($back_url, 'register') === FALSE ? $back_url : dr_member_url('home/index');
			if (!$json && $MEMBER['setting']['regcode'] && !$this->check_captcha('code')) {
				$error = array('name' => 'code', 'msg' => fc_lang('验证码不正确'));
			} elseif (@in_array('username', $MEMBER['setting']['regfield'])
				&& $result = $this->is_username($data['username'])) {
				$error = array('name' => 'username', 'msg' => $result);
			} elseif (!$data['password']) {
				$error = array('name' => 'password', 'msg' => fc_lang('密码不能为空'));
			} elseif ($data['password'] !== $data['password2']) {
				$error = array('name' => 'password2', 'msg' => fc_lang('两次密码输入不一致'));
			} elseif (@in_array('email', $MEMBER['setting']['regfield'])
				&& $result = $this->is_email($data['email'])) {
				$error = array('name' => 'email', 'msg' => $result);
			} else {
				$this->hooks->call_hook('member_register_before', $data); // 注册之前挂钩点
				$id = $this->member_model->register($data, $groupid);
				if ($id > 0) {
					// 注册成功
					$data['uid'] = $id;
					$this->hooks->call_hook('member_register_after', $data); // 注册之后挂钩点
					// 注册后的登录
					$code = $this->member_model->login($id, $data['password'], $data['auto'] ? 8640000 : $MEMBER['setting']['loginexpire'], 0, 1);
					strlen($code) > 3 && $this->hooks->call_hook('member_login', $data); // 登录成功挂钩点
					$json && exit($this->callback_json(array(
						'msg' => 'ok',
						'code' => 1,
						'uid' => $id,
						'return' => dr_member_info($id)
					)));
				} elseif ($id == -1) {
					$error = array('name' => 'username', 'msg' => fc_lang('该会员【%s】已经被注册', $data['username']));
				} elseif ($id == -2) {
					$error = array('name' => 'email', 'msg' => fc_lang('邮箱格式不正确'));
				} elseif ($id == -3) {
					$error = array('name' => 'email', 'msg' => fc_lang('该邮箱【%s】已经被注册', $data['email']));
				} elseif ($id == -4) {
					$error = array('name' => 'username', 'msg' => fc_lang('同一IP在限制时间内注册过多'));
				} elseif ($id == -5) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：会员名称不合法'));
				} elseif ($id == -6) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：包含不允许注册的词语'));
				} elseif ($id == -7) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：Email格式有误'));
				} elseif ($id == -8) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：Email不允许注册'));
				} elseif ($id == -9) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：Email已经被注册'));
				} elseif ($id == -10) {
					$error = array('name' => 'phone', 'msg' => fc_lang('手机号码必须是11位的整数'));
				} elseif ($id == -11) {
                    $error = array('name' => 'phone', 'msg' => fc_lang('该手机号码已经注册'));
                } else {
					$error = array('name' => 'username', 'msg' => fc_lang('注册失败'));
				}
			}
			$json && exit($this->callback_json(array(
				'msg' => $error['msg'],
				'code' => 0
			)));
			if (IS_AJAX) {
				$error && exit(dr_json(0, $error['msg']));
				$id > 0 && exit(json_encode(array(
					'status' => 1,
					'backurl' => $back_url,
					'syncurl' => dr_member_sync_url($code))));
			}
			$code && $this->member_msg(fc_lang('注册成功').$code, $back_url, 1, 3);
            exit;
		} else {
			$data = array();
			$back_url = $this->input->get('back') ? $this->input->get('back') : (isset($_SERVER['HTTP_REFERER']) ? (strpos($_SERVER['HTTP_REFERER'], 'login') !== false ? '' : $_SERVER['HTTP_REFERER']) : '');
		}

		$this->template->assign(array(
			'data' => $data,
			'code' => $MEMBER['setting']['regcode'],
			'back_url' => $back_url,
			'regfield' => $MEMBER['setting']['regfield'],
			'meta_title' => fc_lang('会员注册'),
		));
		$tpl = 'register'.($groupid ? '_'.$groupid : '').'.html';
		$this->template->display(is_file(TPLPATH.'pc/member/'.MEMBER_TEMPLATE.'/common/'.$tpl) ? $tpl : 'register.html');
	}
}