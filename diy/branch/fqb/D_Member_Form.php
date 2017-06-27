<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class D_Member_Form extends M_Controller {

	protected $form; // 表单信息
	protected $table; // 表单表名称
	
    /**
     * 构造函数（模块表单会员中心）
     */
    public function __construct() {
        parent::__construct();
		// 表单验证
        $fid = trim(strchr($this->router->class, '_'), '_');
		$this->form = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'form', $fid);
		if (!$this->form) {
            $this->member_msg(fc_lang('表单不存在'));
        } elseif ($this->form['permission'][$this->markrule]['disabled']) {
            $this->member_msg(fc_lang('当前会员组无权限操作'));
        }
		$this->table = SITE_ID.'_'.APP_DIR.'_form_'.$fid;
	}
	
    /**
     * 管理
     */
    public function index() {

		// 接收参数
		$kw = dr_safe_replace($this->input->get('kw', TRUE));
		$cid = (int)$this->input->get('cid');
		$total = (int)$this->input->get('total');
		$order = dr_get_order_string(dr_safe_replace($this->input->get('order', TRUE)), 'inputtime desc');

		// 相关文档
		if ($cid) {
			$cdata = $this->_get_data($cid);
			if (!$cdata) {
				$this->member_msg(fc_lang('内容(id#%s)不存在', $cid));
			} elseif ($cdata['uid'] != $this->uid) {
				$this->member_msg(fc_lang('无权限操作'));
			}
		}
		
		
		// 查询结果
		$list = array();
		if (!$total) {
			$this->db->select('count(*) as total');
			$this->db->where('uid', $this->uid);
			$kw && $this->db->like('subject', $kw);
			$cid && $this->db->where('cid', $cid);
			$data = $this->db->get($this->table)->row_array();
			$total = (int)$data['total'];
		}

		if ($total) {
			$page = max((int)$this->input->get('page'), 1);
			$this->db->where('uid', $this->uid);
			$kw && $this->db->like('subject', $kw);
			$cid && $this->db->where('cid', $cid);
			$this->db->order_by($order);
			$list = $this->db->limit($this->pagesize, $this->pagesize * ($page - 1))->get($this->table)->result_array();
		}

		$url = dr_member_url(APP_DIR.'/'.$this->router->class.'/'.$this->router->method).'&kw='.$kw.'&order='.$order.'&cid='.$cid;
		$this->template->assign(array(
			'list' => $list,
			'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
			'page_total' => $total,
			'isedit' => $this->form['permission'][$this->markrule]['notedit'] ? 0 : 1,
			'moreurl' => $url,
		));
		$this->template->display(is_file(dr_tpl_path('mform_index_'.$this->form['table'].'.html')) ? 'mform_index_'.$this->form['table'].'.html' : 'mform_index.html');
    }
    
	/**
     * 列表
     */
    public function listc() {
		$this->index();
    }
	
	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->where('uid', $this->uid)->get($this->table)->row_array();
		!$data && $this->admin_msg(fc_lang('表单内容(id#%s)不存在', $id));

        $data2 = $this->db->where('id', $id)->get($this->table.'_data_'.intval($data['tableid']))->row_array();
		$data2 && $data = array_merge($data, $data2);
		
		if (IS_POST) {
			// 设置uid便于校验处理
			$_POST['data']['id'] = $id;
			$_POST['data']['uid'] = $data['uid'];
			$_POST['data']['author'] = $data['author'];
			$post = $this->validate_filter($this->form['field'], $data);
			if (isset($data['error'])) {
				$error = $data;
				(IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
				$data = $this->input->post('data', TRUE);
			} else {
                $post[0]['uid'] = $post[1]['uid'] = $data['uid'];
				$post[1]['author'] = $data['author'];
				$table = $this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_form_'.$this->form['table']);
				$this->db->where('id', $id)->update($table, $post[1]);
                $this->db->where('id', $id)->update($table.'_data_'.intval($data['tableid']), $post[0]);
				// 操作成功处理附件
				$this->attachment_handle($data['uid'], $table.'-'.$id, $this->form['field'], $post);
				(IS_AJAX || IS_API_AUTH) && exit(dr_json(0, 'ok', '', $id));
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_member_url(APP_DIR.'/'.$this->router->class.'/index'), 1);
			}
		}
		
		$tpl = dr_tpl_path('mform_edit_'.$this->form['table'].'.html');
		$this->template->assign(array(
			'data' => $data,
			'error' => $error,
			'myfield' => $this->field_input($this->form['field'], $data, TRUE),
            'result_error' => $error,
		));
		$this->template->display(is_file($tpl) ? basename($tpl) : 'mform_edit.html');
    }
    
	/**
     * 删除
     */
    public function del() {
	
		$id = (int)$this->input->post('id');
		$table = SITE_ID.'_'.APP_DIR.'_form_'.$this->form['table'];
        $result = $this->db->where('id', $id)->where('uid', $this->uid)->get($table)->row_array();
		if ($result) {
            // 更新模块表的统计值
            $this->db->where('id', $result['cid'])->set($this->form['table'].'_total', $this->form['table'].'_total - 1', FALSE)->update(SITE_ID.'_'.APP_DIR);
            // 删除
            $this->db->where('id', $id)->delete($table);
            $this->db->where('id', $id)->delete($table.'_data_'.$result['tableid']);
            $this->load->model('attachment_model');
            $this->attachment_model->delete_for_table($table.'-'.$id);
		}
		
		exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
	}
	
	/**
     * 查看
     */
    public function show() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->get($this->table)->row_array();
		!$data && exit('<div style="padding:10px 20px 20px;">'.fc_lang('表单内容(id#%s)不存在', $id).'</div>');

        $data2 = $this->db->where('id', $id)->get($this->table.'_data_'.intval($data['tableid']))->row_array();
		$data2 && $data = array_merge($data, $data2);

		// 格式化输出自定义字段
		$fields = $this->form['field'];
		$fields['inputtime'] = array('fieldtype' => 'Date');
		$data = $this->field_format_value($fields, $data, 1);

		$tpl = dr_tpl_path('mform_show_'.$this->form['table'].'.html');
		$this->template->assign(array(
			'tpl' => str_replace(FCPATH, '/', $tpl),
			'data' => $data,
		));
		$this->template->display(is_file($tpl) ? basename($tpl) : 'mform_show.html');
	}
	
	
	// 内容表内容
	private function _get_data($cid) {
	
		$data = $this->get_cache_data('show'.APP_DIR.SITE_ID.$cid);
		if (!$data) {
			$this->load->model('content_model');
			$data = $this->content_model->get($cid);
		}
		
		return $data;
	}
}