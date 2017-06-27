<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* v3.1.0  */
class D_Member_Extend_Back extends M_Controller {

    public $content; // 内容数据
    public $field; // 自定义字段+含系统字段

    /**
     * 构造函数
     */

    public function __construct() {
        parent::__construct();
        $this->load->library('Dfield', array(APP_DIR));
    }

    /**
     * 审核
     */
    public function index() {

        if (IS_POST) {

            $ids = $this->input->post('ids', TRUE);
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));

            $this->load->model('attachment_model');
            foreach ($ids as $id) {
                $data = $this->db // 主表状态
                             ->where('id', (int)$id)
                             ->where('uid', (int)$this->uid)
                             ->select('cid')
                             ->limit(1)
                             ->get($this->content_model->prefix.'_extend_index')
                             ->row_array();
                if ($data) {
                    // 删除数据
                    $this->content_model->del_extend_verify($id);
                    // 删除表对应的附件
                    $this->attachment_model->delete_for_table($this->content_model->prefix.'_verify-'.$data['cid'].'-'.$id);
                }
            }

            exit(dr_json(1, fc_lang('删除成功')));
        }

        $total = (int)$this->input->get('total');

        // 查询结果
        $list = array();
        $table = $this->content_model->prefix.'_extend_verify';
        if (!$total) {
            $this->db->select('count(*) as total');
            $this->db->where('uid', $this->uid)->where('status', 0);
            $data = $this->db->get($table)->row_array();
            $total = (int)$data['total'];
        }

        if ($total) {
            $page = max((int)$this->input->get('page'), 1);
            $this->db
                ->select('id,inputtime,catid,content')
                ->where('uid', $this->uid)
                ->where('status', 0)
                ->order_by('inputtime DESC');
            $list = $this->db->limit($this->pagesize, $this->pagesize * ($page - 1))->get($table)->result_array();
        }

        $url = dr_member_url(APP_DIR.'/eback/index');
        $this->template->assign(array(
            'list' => $list,
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'moreurl' => $url,
        ));
        $this->template->display('module_extend_back_index.html');
    }

    /**
     * 修改审核
     */
    public function edit() {

        $id = (int) $this->input->get('id');
        $data = $this->content_model->get_extend_verify($id);
        $error = array();
        
        !$data && $this->member_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        // 禁止修改他人文档
        $data['author'] != $this->member['username'] && $data['uid'] != $this->member['uid'] && $this->member_msg(fc_lang('无权限操作'));
        
        $field = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend');

        if (IS_POST) {
            $_data = $data;
            // 设置uid便于校验处理
            $_POST['data']['id'] = $id;
            $_POST['data']['uid'] = $this->uid;
            $_POST['data']['author'] = $this->member['username'];
            $data = $this->validate_filter($field, $_data['content']);
            if (isset($data['error'])) {
                $error = $data;
                (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
                $data['content'] = $this->input->post('data', TRUE);
                $data['backinfo'] = $_data['backinfo'];
            } else {
                $this->content = $this->content_model->get($_data['cid']);
                $data[1]['cid'] = (int)$this->content['id'];
                $data[1]['uid'] = $this->member['uid'];
                $data[1]['catid'] = (int)$this->content['catid'];
                $data[1]['status'] = 1;
                $data[1]['author'] = $this->member['username'];
                isset($data[1]['mytype']) && $data[1]['mytype'] = $_data['mytype'];
                // 修改数据
                if ($this->content_model->edit_extend($_data['content'], $data)) {
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_verify-'.$_data['cid'].'-'.$id, $field);
                    (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，请等待管理员审核'), dr_member_url(APP_DIR.'/everify/index'), $id));
                    $this->template->assign(array(
                        'url' => dr_member_url(APP_DIR.'/everify/index'),
                        'add' => dr_member_url(APP_DIR.'/extend/add', array('cid' => $_data['cid'])),
                        'edit' => 1,
                        'list' => dr_member_url(APP_DIR.'/extend/index', array('cid' => $_data['cid'])),
                        'meta_name' => fc_lang('修改成功')
                    ));
                    $this->template->display('module_verify_msg.html');
                } else {
                    $this->member_msg(fc_lang('修改失败'));
                }
                exit;
            }
        }

        $this->template->assign(array(
            'purl' => dr_member_url(APP_DIR.'/everify/edit', array('id' => $id)),
            'data' => $data,
            'myfield' => $this->field_input($field, $data['content'], TRUE),
            'meta_name' => fc_lang('审核中'),
            'result_error' => $error,
        ));
        $this->template->display('module_extend_back_edit.html');
    }

}
