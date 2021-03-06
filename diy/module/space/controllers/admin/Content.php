<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.7.1
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */
	
class Content extends M_Controller {

	public $mid; // 模型id
	public $model; // 模型

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->mid = (int)$this->input->get('mid');
		$this->model = $this->get_cache('space-model', $this->mid);
		if (!$this->model) {
            $this->admin_msg(fc_lang('空间模型不存在，请更新模型缓存'));
        }
		$this->template->assign(array(
			'mid' => $this->mid,
			'menu' => $this->get_menu_v3(array(
				$this->model['name'] => array('space/admin/content/index/mid/'.$this->mid, 'table'),
				fc_lang('自定义字段') => array('admin/field/index/rname/space/rid/'.$this->mid, 'plus-square'),
			))
		));
		$this->load->model('space_content_model');
		$this->space_content_model->tablename = $this->db->dbprefix('space_'.$this->model['table']);
    }
	
	/**
     * 管理
     */
    public function index() {
	
		if (IS_AJAX && $this->input->post('action')) {
			$ids = $this->input->post('ids');
			if ($this->input->post('action') == 'delete') {
				if ($ids) {
					foreach ($ids as $id) {
						$data = $this->db
									 ->where('id', (int)$id)
									 ->select('uid')
									 ->limit(1)
									 ->get($this->space_content_model->tablename)
									 ->row_array();
						if ($data) {
							$this->db->where('id', (int)$id)->delete($this->space_content_model->tablename);
							$this->load->model('attachment_model');
							$this->attachment_model->delete_for_table($this->space_content_model->tablename.'-'.$id); // 删除附件
							$member = $this->member_model->get_member($data['uid']);
							$markrule = $member ? $member['mark'] : 0;
							$experience = (int)$this->model['setting'][$markrule]['experience'];
							$score = (int)$this->model['setting'][$markrule]['score'];
							// 积分处理
							if ($experience > 0) {
								$this->member_model->update_score(0, $data['uid'], -$experience, '', "delete");
							}
							// 虚拟币处理
							if ($score > 0) {
								$this->member_model->update_score(1, $data['uid'], -$score, '', "delete");
							}
						}
					}
                    $this->system_log('删除空间模型【'.$this->model['table'].'】数据【#'.@implode(',', $ids).'】'); // 记录日志
				}
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			} else {
				if ($ids) {
					$status = (int)$this->input->post('status');
					foreach ($ids as $id) {
						$data = $this->db
									 ->where('id', (int)$id)
									 ->select('uid')
									 ->limit(1)
									 ->get($this->space_content_model->tablename)
									 ->row_array();
						$this->db->where('id', (int)$id)->update($this->space_content_model->tablename, array('status' => $status));
						if ($status) {
							$member = $this->member_model->get_base_member($data['uid']);
							$markrule = $member ? $member['mark'] : 0;
							$experience = (int)$this->model['setting'][$markrule]['experience'];
							$score = (int)$this->model['setting'][$markrule]['score'];
							$mark = $this->space_content_model->tablename.'-'.$id;
							// 积分处理
							if ($experience) {
								$this->member_model->update_score(0, $data['uid'], $experience, $mark, "add", 1);
							}
							// 虚拟币处理
							if ($score) {
								$this->member_model->update_score(1, $data['uid'], $score, $mark, "add", 1);
							}
						}
					}
                    $this->system_log('空间模型【'.$this->model['table'].'】数据【#'.@implode(',', $ids).'】状态更改'); // 记录日志
				}
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			}
		}
        // 重置页数和统计
        if (IS_POST) {
            $_GET['page'] = $_GET['total'] = 0;
        }
		// 根据参数筛选结果
		$param = array();
		if ($this->input->get('search')) $param['search'] = 1;
		// 数据库中分页查询
		list($data, $param)	= $this->space_content_model->limit_page($param, max((int)$_GET['page'], 1), (int)$_GET['total']);
		// 搜索参数
		if ($this->input->get('search')) {
			$_param = $this->cache->file->get($this->space_content_model->cache_file);
		} else {
			$_param = $this->input->post('data');
		}
		$_param = $_param ? $param + $_param : $param;
        $param['mid'] = $this->mid;
		$this->template->assign(array(
			'list' => $data,
			'param'	=> $_param,
			'field' => $this->model['field'],
			'pages'	=> $this->get_pagination(dr_url('space/content/index', $param), $param['total']),
		));
		$this->template->display(is_file(FCPATH.'module/space/templates/admin/content_'.$this->mid.'.html') ? 'content_'.$this->mid.'.html' : 'content_index.html');
    }
	
	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get($this->space_content_model->tablename)->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

        $space = dr_space_info($data['uid']);
        define('IS_SPACE_THEME', $space['style'] ? $space['style'] : 'default'); // 空间模板页面

		$this->load->model('space_category_model');
		$this->load->model('space_content_model');
		$category = $this->space_category_model->get_data($this->mid, $data['uid']);
		
		if (IS_POST) {
		
			$post = $this->validate_filter($this->model['field']);
			$catid = (int)$this->input->post('catid'); // 栏目参数
			
			// 验证出错信息
			if (isset($post['error'])) {
				$error = $post;
				$data = $this->input->post('data', TRUE);
			} elseif (!$catid) {
				$data = $this->input->post('data', TRUE);
				$error = array('error' => 'catid', 'msg' => fc_lang('请选择一个栏目'));
			} elseif ($category[$catid]['child'] || $category[$catid]['modelid'] != $this->mid) {
				$data = $this->input->post('data', TRUE);
				$error = array('error' => 'catid', 'msg' => fc_lang('该栏目不允许发布信息'));
			} else {
			
				// 设定文档默认值
				$post[1]['catid'] = $catid;
				$post[1]['status'] = (int)$this->input->post('status');
				
				// 修改文档
				if (($id = $this->space_content_model->edit($id, $data['uid'], $post[1])) != FALSE) {
					$mark = $this->space_content_model->tablename.'-'.$id;
					if ($post[1]['status']) {
					
						$member = $this->member_model->get_base_member($data['uid']);
						$markrule = $member ? $member['mark'] : 0;
						$experience = (int)$this->model['setting'][$markrule]['experience'];
						$score = (int)$this->model['setting'][$markrule]['score'];
						
						// 积分处理
						if ($experience) {
							$this->member_model->update_score(0, $data['uid'], $experience, $mark, "发布文档", 1);
						}
						// 虚拟币处理
						if ($score) {
							$this->member_model->update_score(1, $data['uid'], $score, $mark, "发布文档", 1);
						}
					}

                    $this->system_log('修改空间模型【'.$this->model['table'].'】数据【#'.$id.'】'); // 记录日志
					$this->attachment_handle($data['uid'], $mark, $this->model['field'], $data, $post[1]['status'] ? TRUE : FALSE);
					$this->member_msg(fc_lang('操作成功，正在刷新...'), dr_url('space/content/index', array('mid' => $this->mid, 'tid' => $this->tid)), 1);
				}
			}
			$data = $data[1];
			unset($data['id']);
		}
		
		$this->template->assign(array(
			'data' => $data,
			'error' => $error,
			'select' => $this->select_space_category($category, (int)$data['catid'], 'class="form-control" name=\'catid\'', NULL, 1),
			'myfield' => $this->field_input($this->model['field'], $data),
		));
		$this->template->display('content_edit.html');
    }
	
}