<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Update extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->db->db_debug = TRUE;
    }

    /**
     * 更新程序
     */
    public function index() {



    }

    private function set_lang($file, $version, $code) {


    }
}