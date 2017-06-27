<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
require FCPATH.'branch/fqb/D_Common.php';

class M_Controller extends D_Common {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();

		if (defined('DR_PAY_ID') && DR_PAY_ID) {
            $this->load->model('pay_model');
			require WEBPATH.'api/pay/'.DR_PAY_ID.'/call.php';
			exit;
		} elseif (defined('DR_UEDITOR') && is_dir(WEBPATH.'api/'.DR_UEDITOR)) {
            require WEBPATH.'api/'.DR_UEDITOR.'/php/controller.php';
            exit;
        } else {
			$notice = array();
			// 统计未读短消息
			$notice['app/pms/index'] = dr_is_app('pms') ? $this->db->where('uid', $this->uid)->where('isnew', 1)->count_all_results('pm_members') : 0;
			$new_notice = $this->db->where('uid', $this->uid)->count_all_results('member_new_notice');
			if ($new_notice) {
				// 统计未读系统提醒
				$notice['notice/index'] = $this->db->where('uid', $this->uid)->where('type', 1)->where('isnew', 1)->count_all_results('member_notice_'.(int)$this->member['tableid']);
				// 统计未读会员提醒
				$notice['notice/member'] = $this->db->where('uid', $this->uid)->where('type', 2)->where('isnew', 1)->count_all_results('member_notice_'.(int)$this->member['tableid']);
				// 统计未读模块提醒
				$notice['notice/module'] = $this->db->where('uid', $this->uid)->where('type', 3)->where('isnew', 1)->count_all_results('member_notice_'.(int)$this->member['tableid']);
				// 统计未读应用提醒
				$notice['notice/app'] = $this->db->where('uid', $this->uid)->where('type', 4)->where('isnew', 1)->count_all_results('member_notice_'.(int)$this->member['tableid']);
			}

			$this->template->assign('newpm', $notice['app/pms/index']);
			$this->template->assign('notices', $notice);
		}
    }

	
	/**
	 * 验证会员名称
	 *
	 * @param	string	$username
	 * @return	NULL
	 */
	protected function is_username($username) {
		
		if (!$username) {
            return fc_lang('会员名称格式不正确');
        }
		
		$setting = $this->get_cache('member', 'setting');
		if ($setting['regnamerule']
            && !preg_match($setting['regnamerule'], $username)) {
            return fc_lang('会员名称格式不正确');
        }

		if ($setting['regnotallow']
            && @in_array($username, explode(',', $setting['regnotallow']))) {
            return fc_lang('该会员名称系统禁止注册');
        }
		
		return NULL;
	}
	
	/**
	 * 验证Email
	 *
	 * @param	string	$email
	 * @return	NULL
	 */
	protected function is_email($email) {
		
		if (!$email) {
            return fc_lang('邮箱格式不正确');
        }
		
		if (!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $email)) {
            return fc_lang('邮箱格式不正确');
        }
		
		return NULL;
	}

    // 消息提醒
    protected function _notice() {


    }

}