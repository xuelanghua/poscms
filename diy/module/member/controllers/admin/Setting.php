<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class Setting extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 配置
     */
    public function index() {
	
		$page = (int)$this->input->get('page');
		$result = 0;
		
		if (IS_POST) {
			$post = $this->input->post('data');
			$page = (int)$this->input->post('page');
			// 规则判断
			if (empty($post['regfield'])) {
				$this->admin_msg('至少需要选择一个注册字段，否则注册系统会崩溃', dr_url('member/setting/index', array('page'=> $page)), 0, 9);
			} elseif (!in_array('email', $post['regfield']) && $post['regverify'] == 1) {
				$this->admin_msg('开启邮件审核后，注册字段必须选择【邮箱】，否则注册系统会崩溃', dr_url('member/setting/index', array('page'=> $page)), 0, 9);
			} elseif (!in_array('email', $post['regfield']) && $post['ucenter'] == 1) {
				$this->admin_msg('开启Ucenter后，注册字段必须选择【邮箱】，否则注册系统会崩溃', dr_url('member/setting/index', array('page'=> $page)), 0, 9);
			} elseif (!in_array('phone', $post['regfield']) && $post['regverify'] == 3) {
				$this->admin_msg('开启手机验证码审核后，注册字段必须选择【手机】，否则注册系统会崩溃', dr_url('member/setting/index', array('page'=> $page)), 0, 9);
			} elseif ($post['ucsso'] && $post['ucenter']) {
                $this->admin_msg('Ucenter和UCSSO不能同时开启', dr_url('member/setting/index', array('page'=> $page)), 0, 9);
            }
			$this->member_model->member($post);
			$data = $post;
			$cache = $this->member_model->cache();
            $result = 1;
            $this->system_log('会员配置'); // 记录日志
		} else {
			$cache = $this->member_model->cache();
			$data = $cache['setting'];
        }
		
		$html = '

					</div>
				</div>
<div class="portlet light bordered" id="dr_{name}">
					<div class="portlet-title mytitle">
						{text}
					</div>
<div class="portlet-body">
{value}';
		!$data['mergefield'] && $data['mergefield'] = $html;
		!$data['mbmergefield'] && $data['mbmergefield'] = $html;

		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('功能配置') => array('member/admin/setting/index', 'cog')
			)),
			'data' => $data,
			'page' => $page,
			'result' => $result,
            'synurl' => $cache['synurl'],
		));
		$this->template->display('setting_index.html');
    }
	
	/**
     * 导入进Ucenter用户表
     */
	public function importuc() {
		
	}
	
	/**
     * 会员权限划分
     */
	public function permission() {
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('会员权限') => array('member/admin/setting/permission', 'users'),
			))
		));
		$this->template->display('setting_permission.html');
	}
	
    /**
     * 网银配置
     */
    public function pay() {

        $pay = $this->get_pay_api();
		if (IS_POST) {
            $this->load->library('dconfig');
            $data = $this->input->post('data');
            foreach ($pay as $dir => $t) {
                if (isset($data[$dir])) {
                    $data[$dir]['name'] = $t['name'];
                    $file = WEBPATH.'api/pay/'.$dir.'/config.php';
                    $size = $this->dconfig->file($file) ->note($dir.' 支付接口配置文件')->space(12)->to_require_one($data[$dir], $data[$dir]);
					!$size && $this->admin_msg(fc_lang('文件【%s】修改失败，请检查权限', 'api/pay/'.$dir.'/config.php'));
                }
            }
            $setting = $this->input->post('setting');
            asort($setting['order']);
			$this->member_model->pay($setting);
			$this->member_model->cache();
            $this->system_log('网银配置'); // 记录日志
            $pay = $this->get_pay_api();
		}

		$this->template->assign(array(
            'pay' => $pay,
			'menu' => $this->get_menu_v3(array(
				fc_lang('网银配置') => array('member/admin/setting/pay', 'rmb'),
			)),
			'setting' => $this->member_model->pay(),
		));
		$this->template->display('setting_pay.html');
    }
	
	/**
     * 会员设置规则
     */
    public function rule() {
		$id = $this->input->get('id');
		if (IS_POST) {
			$this->member_model->permission($id, $this->input->post('data'));
			$this->member_model->cache();
            $this->system_log('会员权限设置'); // 记录日志
			exit;
		}
		$this->template->assign(array(
			'data' => $this->member_model->permission($id),
		));
		$this->template->display('setting_rule.html');
    }
	
	/**
     * OAuth2授权登录
     */
	public function oauth() {
		
		$oauth = array('qq' => 'QQ', 'sina' => '微博', 'weixin' => '微信'); //
		$this->load->library('dconfig');
		$config = require WEBPATH.'config/oauth.php';
		
		if (IS_POST) {
			$cfg = array();
			$data = $this->input->post('data');
			foreach ($oauth as $i => $name) {
				$cfg[$i] = array(
					'key' => trim($data['key'][$i]),
					'use' => isset($data['use'][$i]) ? 1 : 0,
					'name' => $config[$i]['name'] ? $config[$i]['name'] : $name,
					'icon' => $config[$i]['icon'] ? $config[$i]['icon'] : $i,
					'secret' => trim($data['secret'][$i])
				);
			}
			$this->dconfig->file(WEBPATH.'config/oauth.php')->note('OAuth2授权登录')->to_require($cfg);
			$config = $cfg;
            $this->system_log('快捷登录配置'); // 记录日志
			$this->template->assign('result', fc_lang('配置文件更新成功'));
		}
		
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				'OAuth' => array('member/admin/setting/oauth', 'weibo'),
			)),
			'data' => $config,
			'oauth' => $oauth
		));
		$this->template->display('setting_oauth.html');
	}
	
	/**
     * 空间配置
     */
    public function space() {
	
		$data = $this->member_model->space();
		$page = (int)$this->input->get('page');

		if (IS_POST) {
			$post = $this->input->post('data');
			$post['open'] = $data['open'];
			$page = (int)$this->input->post('page');
			$this->member_model->space($post);
			$this->member_model->cache();
            $this->system_log('会员空间配置'); // 记录日志
			if ($post['open'] != $data['open']) {
                $this->admin_msg(fc_lang('操作成功，请按F5刷新整个页面'), '', 1);
            } else {
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('member/setting/space', array('page' => $page)), 1);
            }
		}
		
		$this->template->assign(array(
			'page' => $page,
			'data' => $data,
			'menu' => $this->get_menu_v3(array(
				fc_lang('空间设置') => array('member/admin/setting/space', 'cog')
			)),
			'theme' => dr_get_theme(),
			'is_theme' => strpos($data['theme'], 'http://') === 0 ? 1 : 0,
			'template_path' => @array_diff(dr_dir_map(FCPATH.'module/space/templates/', 1), array('admin', 'member')),
		));
		$this->template->display('setting_space.html');
    }
	
	/**
     * 缓存
     */
    public function cache() {
		$site = $this->input->get('site') ? $this->input->get('site') : SITE_ID;
		$admin = (int)$this->input->get('admin');
		$this->member_model->cache($site);
		$admin or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
    }
}