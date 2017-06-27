<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.3.6
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 * @filesource	svn://www.dayrui.net/v2/member/controllers/admin/spacetpl.php
 */

require FCPATH.'branch/fqb/D_File.php';

class Spacetpl extends D_File {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->path = WEBPATH.'statics/space/';
        $this->_dir = array('member', 'admin');
		$this->template->assign(array(
			'path' => $this->path,
			'furi' => 'space/spacetpl/',
			'auth' => 'space/admin/spacetpl/',
			'menu' => $this->get_menu(array(
				fc_lang('空间模板') => 'space/admin/spacetpl/index',
			)),
			'space' => 1
		));
    }
    
	/**
     * 会员权限划分
     */
	public function permission() {
		
		$dir = trim(str_replace('.', '', $this->input->get('dir')), '/');
		$file = $this->path.$dir.'/rule.php';
		
		if (IS_POST) {
			file_put_contents($file, dr_array2string($this->input->post('data')));
            $this->system_log('会员空间模板的权限划分'); // 记录日志
			echo dr_json(1, fc_lang('操作成功，正在刷新...'));exit;
		}

        $data = is_file($file) ? dr_string2array(file_get_contents($file)) : array();
        if ($data && !isset($data[1]['price'])) {
            $temp = array();
            foreach ($data as $i => $t) {
                $temp[$i]['use'] = 0;
                $temp[$i]['price'] = $t;
            }
            $data = $temp;
        }

		$this->template->assign('data', $data);
		$this->template->assign('space', $dir);
		$this->template->display('space_permission.html');
	}
}