<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Menu extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('会员菜单') => array('member/admin/menu/index', 'table'),
		)));
		$this->load->model('member_menu_model');
    }
	
	/**
     * 菜单管理
     */
    public function index() {
	
		if (IS_POST) {
		
			$ids = $this->input->post('ids');
			!$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
			
			if ($this->input->post('action') == 'order') {
				$_data = $this->input->post('data');
				foreach ($ids as $id) {
					$this->db->where('id', $id)->update('member_menu',  array('displayorder' => (int)$_data[$id]['displayorder']));
				}
				$this->member_menu_model->cache();
                $this->system_log('排序会员中心菜单【#'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功，请按F5刷新整个页面')));
			} else {
				$this->db->where_in('id', $ids)->delete('member_menu');
				$this->member_menu_model->cache();
                $this->system_log('删除会员中心菜单【#'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功，请按F5刷新整个页面')));
			}
		}
		
		$this->load->library('dtree');
		$this->dtree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$this->dtree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$left = $this->member_menu_model->get_left_id();
		$data = $this->db->order_by('displayorder ASC,id ASC')->get('member_menu')->result_array();
		$tree = array();
		
		if ($data) {
			foreach($data as $t) {
				$t['name'] = '<i class=" '.$t['icon'].'"></i> '.$t['name'];
				$t['option'] = '';
				if ($this->is_auth('member/admin/menu/add') && !@in_array($t['pid'], $left)) {
					$t['option'].= '<a class="aadd" title="'.fc_lang('添加').'" href="'.dr_dialog_url(dr_url('member/menu/add', array('pid' => $t['id'])), 'add').'"> <i class="fa fa-plus"></i> '.fc_lang('添加').'</a>';
					$t['target'] = '';
				} else {
					$t['option'].= '';
					$t['target'] = '<img src="'.THEME_PATH.'admin/images/'.$t['target'].'.gif" />';
				}
                if ($this->is_auth('member/admin/menu/edit')) {
                    $t['hidden'] = '<a href="javascript:;" onClick="return dr_dialog_set(\''.($t['hidden'] ? fc_lang('<font color=blue><b>你确定要启用它？启用后将正常使用</b></font>') : fc_lang('<font color=red><b>你确定要禁用它？禁用后将无法使用</b></font>')).'\',\''.dr_url('member/menu/hidden',array('id'=>$t['id'])).'\');"><img src="'.THEME_PATH.'admin/images/'.($t['hidden'] ? 0 : 1).'.gif"></a>';
                } else {
                    $t['hidden'] = '<img src="'.THEME_PATH.'admin/images/'.($t['hidden'] ? 0 : 1).'.gif">';
                }
				if ($this->is_auth('member/admin/menu/edit')) {
					$t['option'].= '<a class="aedit" title="'.fc_lang('修改').'" href="'.dr_dialog_url(dr_url('member/menu/edit', array('id' => $t['id'])), 'edit').'"> <i class="fa fa-edit"></i> '.fc_lang('修改').'</a>';
                    $t['name'] = '<a title="'.fc_lang('修改').'" href="'.dr_dialog_url(dr_url('member/menu/edit', array('id' => $t['id'])), 'edit').'">'.$t['name'].'</a>&nbsp;&nbsp;';
                }
				$this->is_auth('member/admin/menu/del') && $t['option'].= '<a class="adel" title="'.fc_lang('删除').'" href="javascript:;" onClick="return dr_dialog_del(\''.fc_lang('您确定要这样操作吗？').'\',\''.dr_url('member/menu/del',array('id' => $t['id'])).'\');"> <i class="fa fa-trash"></i> '.fc_lang('删除').'</a>';
				$tree[$t['id']] = $t;
			}
		}
		
		$str = "<tr>
					<td align='right'><input name='ids[]' type='checkbox' class='toggle md-check dr_select' value='\$id' /></td>
					<td align='center'><input class='input-text displayorder' type='text' name='data[\$id][displayorder]' value='\$displayorder' /></td>
					<td align='center'>\$hidden</td>
					<td>\$spacer\$name</td>
					<td align='center'>\$target</td>
					<td align='left' class='dr_option'>\$option</td>
				</tr>";
		$this->dtree->init($tree);
		
		$this->template->assign(array(
			'list' => $this->dtree->get_tree(0, $str),
		));
		$this->template->display('menu_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST) {
            $data = $this->input->post('data');
            $this->system_log('添加会员中心菜单【'.$data['name'].'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功'), $this->member_menu_model->add($data)));
		}
		
		$top = $this->member_menu_model->get_top_id();
		$menu_name = $menu_type	= '';
		$data['pid'] = (int)$this->input->get('pid');
		if ($data['pid']) {
			if (in_array($data['pid'], $top)) {
				$menu_type = 0;
				$menu_name = fc_lang('分组菜单');
			} else {
				$menu_type = 1;
				$menu_name = fc_lang('链接菜单');
			}
		} else {
			$menu_type = 0;
			$menu_name = fc_lang('顶级菜单');
		}
		
		$this->template->assign(array(
			'data' => $data,
			'menu_url' => 2,
			'menu_type' => $menu_type,
			'menu_name'	=> $menu_name
		));
		$this->template->display('menu_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db
					 ->where('id', $id)
					 ->limit(1)
					 ->get('member_menu')
					 ->row_array();
		!$data && exit(fc_lang('对不起，数据被删除或者查询不存在'));
		
		if (IS_POST) {
            $data = $this->input->post('data');
            $this->system_log('修改会员中心菜单【'.$data['name'].'】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功'), $this->member_menu_model->edit($id, $data)));
		}
		
		$top = $this->member_menu_model->get_top_id();
		$uri = $this->duri->uri2ci($data['uri']);
		$uri['dir']	= $uri['app'] ? $uri['app'] : ($uri['path'] ? $uri['path'] : '');
		$menu_name = $menu_type = '';
		$select = '<select name="data[pid]">';
		if ($data['pid']) {
			if (in_array($data['pid'], $top)) { // 分组菜单
				$menu_type = 0;
				$menu_name = fc_lang('分组菜单');
				$select = $this->member_menu_model->parent_select(1, $data['pid']);
			} else { // 链接菜单
				$menu_type = 1;
				$menu_name = fc_lang('链接菜单');
				$select = $this->member_menu_model->parent_select(2, $data['pid']);
			}
		} else { // 顶级菜单
			$menu_type = 0;
			$menu_name = fc_lang('顶级菜单');
			$select = $this->member_menu_model->parent_select(0, $data['pid']);
		}
		$this->template->assign(array(
			'uri' => $uri,
			'data' => $data,
			'select' => $select,
			'menu_url' => $data['uri'] ? 2 : 1,
			'menu_name'	=> $menu_name,
			'menu_type'	=> $menu_type
		));
		$this->template->display('menu_add.html');
    }

    /**
     * 隐藏/显示
     */
    public function hidden() {

        $id = (int)$this->input->get('id');
        $data = $this->db->where('id', $id)->get('member_menu')->row_array();
        if ($data) {
            $update = array();
            $update[] = $id;
            $data2 = $this->db->where('pid', $id)->get('member_menu')->result_array();
            if ($data2) {
                // 查询二级菜单
                foreach ($data2 as $t2) {
                    $update[] = $t2['id'];
                    $data3 = $this->db->where('pid', $t2['id'])->get('member_menu')->result_array();
                    if ($data3) {
                        // 查询3及菜单
                        foreach ($data3 as $t3) {
                            $update[] = $t3['id'];
                        }
                    }
                }
            }
            // 更新状态
            $this->db->where_in('id', $update)->update('member_menu', array('hidden' => $data['hidden'] ? 0 : 1));
            $this->system_log('修改会员中心菜单【'.$data['name'].'】'); // 记录日志
        }

        $this->member_menu_model->cache();
        exit(dr_json(1, fc_lang('操作成功，请按F5刷新整个页面')));
    }
	
	/**
     * 删除
     */
    public function del() {
        $id = (int)$this->input->get('id');
        $this->member_menu_model->delete($id);
        $this->member_menu_model->cache();
        $this->system_log('删除会员中心菜单【#'.$id.'】'); // 记录日志
        exit(dr_json(1, fc_lang('操作成功，请按F5刷新整个页面')));
	}
	
	/**
     * 初始化菜单
     */
    public function init() {
		$this->member_menu_model->init();
		$this->member_menu_model->cache();
        $this->system_log('初始化会员中心菜单'); // 记录日志
		$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('member/menu/index'), 1);
	}
	
	
	/**
     * 缓存
     */
    public function cache() {
		$admin = $this->input->get('admin') ? $this->input->get('admin') : $this->input->get('admin');
		$this->member_menu_model->cache();
		$admin or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}