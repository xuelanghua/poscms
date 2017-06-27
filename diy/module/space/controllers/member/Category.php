<?php

/**
 * Dayrui Website Management System
 *
 * @since		version 2.6.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class Category extends M_Controller {

	private $thumb;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->_is_space();
		$this->thumb = array(
			array(
				'ismain' => 1,
				'fieldtype' => 'File',
				'fieldname' => 'thumb',
				'setting' => array(
					'option' => array(
						'size' => 10,
						'ext' => 'jpg,gif,png',
					)
				)
			)
		);
		$this->load->model('space_category_model');
    }
	
    /**
     * 首页
     */
    public function index() {

        $is_edit = $this->get_cache('member', 'setting', 'space', 'category') ? 0 : 1;

		if (IS_POST) {
			$ids = $this->input->post('ids', TRUE);
			if ($this->input->post('action') == 'order') {
				if (!$ids) {
                    exit(dr_json(0, fc_lang('您还没有选择呢')));
                }
				$data = $this->input->post('data');
				foreach ($ids as $id) {
					$this->db->where('id', (int)$id)->where('uid', (int)$this->uid)->update($this->space_category_model->tablename, $data[$id]);
				}
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			} elseif ($this->input->post('action') == 'del' && $is_edit) {
				if (!$ids) {
                    exit(dr_json(0, fc_lang('您还没有选择呢')));
                }
				$this->space_category_model->del($ids);
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			}
		}
	
		$this->load->library('dtree');
		$this->dtree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$this->dtree->nbsp = '&nbsp;&nbsp;&nbsp;';
		
		$tree = array();
		$this->space_category_model->repair($this->uid);
		$data = $this->space_category_model->get_data(0, 0, 1);
		
		if ($data) {
			foreach($data as $t) {
				
				switch ($t['showid']) {
					case 0:
						$t['show'] = fc_lang('<font color=red>不显示</font>');
						break;
					case 1:
						$t['show'] = fc_lang('顶部');
						break;
					case 2:
						$t['show'] = fc_lang('底部');
						break;
					case 3:
						$t['show'] = fc_lang('<font color=green>都显示</font>');
						break;
				}
				
				switch ($t['type']) {
					case 0:
						$t['model'] = fc_lang('<font color=red>外链</font>');
						break;
					case 1:
						$t['model'] = $this->get_cache('space-model', $t['modelid'], 'name');
						break;
					case 2:
						$t['model'] = fc_lang('<font color=blue>单页</font>');
						break;
				}
				
				$t['option'] = '<a href="'.dr_space_list_url($this->uid, $t['id']).'" target="_blank">'.fc_lang('访问').'</a>';
				$t['add'] = $t['type'] ? "&nbsp;&nbsp;<a href='".dr_member_url('space/category/add')."&pid=".$t['id']."&type=".$t['type']."&mid=".$t['modelid']."'>[ ".fc_lang('添加子栏目')." ]</a>" : '';
				$t['add'] = $is_edit ? $t['add'] : '';
                $tree[$t['id']] = $t;
			}
		}
		
		$str = "<tr>";
		$str.= "<td align='right' style=''><input name='ids[]' type='checkbox' class='dr_select toggle md-check' value='\$id' /></td>";
		$str.= "<td align='center'><input class='input-text displayorder' type='text' name='data[\$id][displayorder]' value='\$displayorder' /></td>";
		$str.= "<td class='ajax'>\$spacer<a href='".dr_member_url('space/category/edit')."&id=\$id'>\$name</a>  \$add</td>";
		$str.= "<td align='center'>\$model</td>";
		$str.= "<td align='center'>\$show</td>";
		$str.= "<td align='center'>\$option</td>";
		$str.= "</tr>";
		
		$this->dtree->init($tree);
		
		$this->template->assign(array(
			'list' => $this->dtree->get_tree(0, $str),
            'page' => (int)$this->input->get('page'),
            'is_edit' => $is_edit
		));
		$this->template->display('space_category_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {

        if ($this->get_cache('member', 'setting', 'space', 'category')) {
            $this->member_msg(fc_lang('系统不允许操作栏目'));
        }

		$data = array(
			'pid' => (int)$this->input->get('pid'),
			'type' => (int)$this->input->get('type'),
			'showid' => 3,
			'modelid' => (int)$this->input->get('mid'),
		);
		
		if (IS_POST) {
			$post = $this->input->post('data', TRUE);
            $post['modelid'] = $post['modelid'] ? $post['modelid'] : $data['modelid'];
			$result = $this->space_category_model->add($post);
			if ($result === TRUE) {
				$this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('space/category/index'), 1);
			}
            if (IS_AJAX || IS_API_AUTH) {
                exit(dr_json(0, $result));
            }
            $data = $post;
		} else {
            $result	= '';
		}
		
		$this->template->assign(array(
			'data' => $data,
			'result_error' => $result,
			'method' => $this->router->method,
			'meta_name' => fc_lang('新增栏目'),
            'is_edit' => 1
		));
		$this->template->display('space_category_add.html');
	}
	
	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->space_category_model->get($id);
		if (!$data)	{
            $this->member_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

		$is_edit = $this->get_cache('member', 'setting', 'space', 'category') ? 0 : 1;

		if (IS_POST) {
			$post = $this->input->post('data', TRUE);
            $post['pid'] = $is_edit ? $post['pid'] : $data['pid'];
			$post['type'] = $data['type'];
            $post['modelid'] = $data['modelid'];
			$result	= $this->space_category_model->edit($id, $post);
			if ($result === TRUE) {
                $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('space/category/index'), 1);
            }
            if (IS_AJAX || IS_API_AUTH) {
                exit(dr_json(0, $result));
            }
			$post['id'] = $id;
			$data = $post;
		} else {
			$result	= '';
		}
		
		$this->template->assign(array(
			'data' => $data,
			'result_error' => $result,
			'method' => $this->router->method,
            'is_edit' => $is_edit,
		));
		$this->template->display('space_category_add.html');
	}
	
	/**
     * 栏目分类
     */
    public function select() {
	
		$pid = (int)$this->input->get('pid');
		$mid = (int)$this->input->get('mid');
		$type = (int)$this->input->get('type');
		
		$this->db->where('uid', (int)$this->uid);
		
		switch ($type) {
			
			case 0: // 外链
				$this->db->where('type>', 0);
				break;
			
			case 1: // 模型
                $this->db->where('((type=1 and modelid='.$mid.') or type=2)');
				break;
				
			case 2: // 单页
				$this->db->where('type>', 0);
				break;
		}
		
		$data = $this->db->get('space_category')->result_array();
		
		echo $this->select_space_category($data, $pid, ' name=\'data[pid]\' style=\'margin-top:7px;\'', fc_lang('顶级栏目'));
    }
	
}