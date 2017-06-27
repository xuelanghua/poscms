<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// 不自动初始化模块
define('DR_IS_SO', 1);
require_once FCPATH.'branch/fqb/D_Module.php';


class Extend extends D_Module {

    /**
     * 内容
     */
    public function index() {
        $this->dir = 'share';
        return $this->_extend((int)$this->input->get('id'));
    }
}