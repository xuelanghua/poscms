<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Language extends M_Controller {


    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();


		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('多语言设置') => array('admin/language/index', 'users'),
			)),
		));
    }
	
	/**
     * 管理
     */
    public function index() {

        $file = WEBPATH.'config/fanyi.php';

        if (IS_POST) {

            $data = $this->input->post('data');

            if ($_POST['aa'] == 0) {
                @unlink($file);
            } else {
                $this->load->library('dconfig');
                $size = $this->dconfig
                    ->file($file)
                    ->note('多语言翻译接口配置文件')
                    ->space(8)
                    ->to_require_one($data);
                if (!$size) {
                    $this->admin_msg(fc_lang('文件创建失败，请检查config目录权限'));
                }
            }

            $this->system_log('配置多语言翻译接口'); // 记录日志
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('language/index'), 1);
        }

        $data = is_file($file) ? require $file : array();
        $this->template->assign(array(
            'data' => $data,
        ));

		$this->template->display('language_index.html');
    }

    /**
     * 缓存
     */
    public function cache($update = 0) {
		$this->system_model->block(isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);
		((int)$_GET['admin'] || $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}