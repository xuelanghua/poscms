<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



require FCPATH.'branch/fqb/D_File.php';

class Theme extends D_File {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->path = WEBPATH.'statics/';
		$this->template->assign(array(
			'path' => $this->path,
			'furi' => 'member/theme/',
			'auth' => 'member/admin/theme/',
			'menu' => $this->get_menu(array(
				fc_lang('风格管理') => 'member/admin/theme/index'
			)),
		));
    }
	
}