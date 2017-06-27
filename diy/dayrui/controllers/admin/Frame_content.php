<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Frame_content extends M_Controller {

    public function index() {

        $cache = $this->get_cache('module', SITE_ID);
        $module = array();
        $dirname = '';
        if ($cache) {
            foreach ($cache as $dir) {
                !$dirname && $dirname = $dir;
                $module[$dir] = $this->get_cache('module-'.SITE_ID.'-'.$dir);
                $module[$dir]['url'] = dr_url($dir.'/content/index');
            }
        } else {
            $this->admin_msg(fc_lang('系统尚未安装模块'));
        }


		$this->template->assign(array(
		    'url' => $module[$dirname]['url'],
		    'dirname' => $dirname,
			'module' => $module,
		));
		$this->template->display('iframe.html');
    }
	

}