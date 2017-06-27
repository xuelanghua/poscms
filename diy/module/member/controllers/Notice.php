<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Notice extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->template->assign('notice', $this->_notice());
    }

    /**
     * 提醒跳转
     */
    public function go() {

        $data = $this->db->select('type')->where('uid', (int)$this->uid)->where('isnew', 1)->get('member_notice_'.$this->member['tableid'])->row_array();
        if (!$data) {
            redirect(dr_member_url('notice/index'), 'refresh');
        } elseif ($data['type'] == 1) {
            redirect(dr_member_url('notice/index'), 'refresh');
        } elseif ($data['type'] == 2) {
            redirect(dr_member_url('notice/member'), 'refresh');
        } elseif ($data['type'] == 3) {
            redirect(dr_member_url('notice/module'), 'refresh');
        } elseif ($data['type'] == 4) {
            redirect(dr_member_url('notice/app'), 'refresh');
        } else {
            redirect(dr_member_url('notice/index'), 'refresh');
        }

    }

    /**
     * 系统提醒
     */
    public function index() {
		$this->m_notice(1);
    }
	
	/**
     * 会员提醒
     */
    public function member() {
		$this->m_notice(2);
    }
	
	/**
     * 模块提醒
     */
    public function module() {
		$this->m_notice(3);
    }
	
	/**
     * 应用提醒
     */
    public function app() {
		$this->m_notice(4);
    }
	
	/**
     * 提醒查看
     */
    private function m_notice($type) {

        $name = array(
            1 => fc_lang('系统提醒'),
            2 => fc_lang('互动提醒'),
            3 => fc_lang('模块提醒'),
            4 => fc_lang('应用提醒'),
        );

        $total = (int)$this->input->get('total');

        // 查询结果
        $list = array();
        if (!$total) {
            $this->db->select('count(*) as total');
            $this->db->where('uid', (int)$this->uid)->where('type', (int)$type);
            $data = $this->db->get('member_notice_'.$this->member['tableid'])->row_array();
            $total = (int)$data['total'];
        }

        if ($total) {
            $page = max((int)$this->input->get('page'), 1);
            $this->db->where('uid', (int)$this->uid)->where('type', (int)$type)->order_by('inputtime DESC');
            $this->db->limit($this->pagesize, $this->pagesize * ($page - 1));
            $list = $this->db->get('member_notice_'.$this->member['tableid'])->result_array();
        }

        // 更新新提醒
        $this->db->where('uid', (int)$this->uid)->where('type', (int)$type)->update('member_notice_'.$this->member['tableid'],
            array('isnew' => 0)
        );

        // 删除新提醒
        $this->db->where('uid', (int)$this->uid)->delete('member_new_notice');

        $this->template->assign(array(
            'list' => $list,
            'pages'	=> $this->get_member_pagination(dr_member_url($this->router->class.'/'.$this->router->method).'&total='.$total, $total),
            'page_total' => $total,
            'meta_name' => $name[$type],
        ));
        
        $this->template->display('notice_index.html');
	}
}