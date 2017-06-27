<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.7.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class Setting extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
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
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('space/setting/space', array('page' => $page)), 1);
            }
		}


		$template = array_diff(dr_dir_map(FCPATH.'module/space/templates/', 1), array('admin'));
		$template2 = dr_dir_map(TPLPATH.'pc/web/', 1);
		
		$this->template->assign(array(
			'page' => $page,
			'data' => $data,
			'menu' => $this->get_menu_v3(array(
				fc_lang('空间设置') => array('space/admin/setting/space', 'cog')
			)),
			'theme' => dr_get_theme(),
			'is_theme' => strpos($data['theme'], 'http://') === 0 ? 1 : 0,
			'template_path' => $template && $template2 ? array_merge($template, $template2) : ($template2 ? $template2 : $template)
		));
		$this->template->display('setting_space.html');
    }
}