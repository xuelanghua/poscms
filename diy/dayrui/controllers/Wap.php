<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
 
class Wap extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->output->enable_profiler(FALSE);
    }
	
    /**
     * 移动端展示部分
     */
    public function index() {


	}
}