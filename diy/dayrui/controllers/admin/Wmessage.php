<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * v3.2
 */

require FCPATH.'branch/fqb/D_Admin_Table.php';

class Wmessage extends D_Admin_Table {

    private $user;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('weixin_model');
        $this->mydb = $this->db; // 数据库
        $this->tfield = 'inputtime';
        $this->mytable = $this->weixin_model->prefix.'_message';
        $this->myfield = $field = array(
            'username' => array(
                'name' => '会员账号',
                'ismain' => 1,
                'fieldname' => 'username',
            ),
        );
        $this->template->assign(array(
            'field' => $field,
            'menu' => $this->get_menu_v3(array(
                '消息管理' => array('admin/wmessage/index', 'envelope'),
            )),
        ));
    }

    public function index() {

        if (IS_POST && $this->input->post('action') == 'del') {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            $this->db->where_in('id', $ids)->delete($this->mytable);
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $data = $this->_index();
        if ($data) {
            foreach ($data as $i => $t) {
                $user = isset($this->user[$t['openid']]) ? $this->user[$t['openid']] : $this->weixin_model->get_user_info($t['openid']);
               if ($user) {
			     $data[$i] = @array_merge($t, $user);
                $data[$i]['id'] = $t['id'];
			   } else {
				   unset($data[$i]);
				  }
            }
        }
        $this->template->assign(array(
            'list' => $data,
        ));
        $this->template->display('wmessage_index.html');
    }
}