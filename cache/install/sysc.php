<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * {name}
 */
 
class {icname} extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	
    /**
     * 程序执行部分
     */
    public function index() {

        /**这里写你的程序结构**/

        echo '这是自定义控制器，你可以在文件（'.__FILE__.'）按照你自己的需求来修改程序结构';exit;

        /**这里写你的程序结构**/


        $data = $this->get_sysc({id}); // 获取自定义控制器的seo信息
        // 变量传值到模板中
        $this->template->assign(array(
            'meta_title' => $data['meta_title'],
            'meta_keywords' => $data['meta_keywords'],
            'meta_description' => $data['meta_description'],
        ));
        $this->template->display('{cname}.html'); // 这里是加载指定模板
	}
}