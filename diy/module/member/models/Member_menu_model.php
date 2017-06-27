<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Member_menu_model extends CI_Model{

    private $ids;
    
	/**
	 * 初始化
	 */
    public function __construct() {
        parent::__construct();
	}
	
	/**
	 * 添加菜单
	 *
	 * @param	array	$data	添加数据
	 * @return	void
	 */
	public function add($data) {
	
		if (!$data) {
            return NULL;
        }
		
		$insert	= array(
			'pid' => $data['pid'],
			'url' => $data['url'],
			'uri' => trim($data['dir'].'/'.$data['class'].'/'.$data['method'], '/'),
            'mark' => '',
			'name' => $data['name'],
			'icon' => $data['icon'],
			'target' => (int)$data['target'],
			'hidden' => 0,
			'displayorder' => 0,
		);
		$this->db->insert('member_menu', $insert);
		
		$this->cache();
		
		return TRUE;
	}
	
	/**
	 * 修改菜单
	 *
	 * @param	intval	$id		
	 * @param	array	$data	数据
	 * @return	void
	 */
	public function edit($id, $data) {
	
		if (!$data || !$id) {
            return NULL;
        }
		
		$this->db->where('id', $id)->update('member_menu', array(
            'pid' => $data['pid'],
            'url' => $data['url'],
            'uri' => trim($data['dir'].'/'.$data['class'].'/'.$data['method'], '/'),
            'name' => $data['name'],
            'icon' => $data['icon'],
            'target' => (int)$data['target'],
        ));
		
		$this->cache();
		
		return $id;
	}
	
	/**
	 * 顶级菜单id
	 *
	 * @return	array
	 */
	public function get_top_id() {
	
		$_data = $this->db->select('id')->where('pid=0')->order_by('id ASC')->get('member_menu')->result_array();
		if (!$_data) {
            return NULL;
        }
		
		$data = array();
		foreach ($_data as $t) {
			$data[] = $t['id'];
		}
		
		return $data;
	}
	
	/**
	 * 分组菜单id
	 *
	 * @return	array
	 */
	public function get_left_id() {
	
		$_data = $this->db->select('id')->where_in('pid', $this->get_top_id())->order_by('id ASC')->get('member_menu')->result_array();
		if (!$_data) {
            return NULL;
        }
		
		$data = array();
		foreach ($_data as $t) {
			$data[] = $t['id'];
		}
		
		return $data;
	}
	
	/**
	 * 父级菜单选择
	 *
	 * @param	intval	$level	级别
	 * @param	intval	$id		选中项id
	 * @param	intval	$name	select部分
	 * @return	string
	 */
	public function parent_select($level, $id = NULL, $name = NULL) {
	
		$select = $name ? $name : '<select name="data[pid]">';
		
		switch ($level) {
			case 0: // 顶级菜单
				$select.= '<option value="0">'.fc_lang('顶级菜单').'</option>';
				break;
			case 1: // 分组菜单
				$topdata = $this->db->select('id,name')->where('pid=0')->get('member_menu')->result_array();
				foreach ($topdata as $t) {
					$select.= '<option value="'.$t['id'].'"'.($id == $t['id'] ? ' selected' : '').'>'.$t['name'].'</option>';
				}
				break;
			case 2: // 链接菜单
				$topdata = $this->db->select('id,name')->where('pid=0')->get('member_menu')->result_array();
				foreach ($topdata as $t) {
					$select.= '<optgroup label="'.$t['name'].'">';
					$linkdata = $this->db->select('id,name')->where('pid='.$t['id'])->get('member_menu')->result_array();
					foreach ($linkdata as $c) {
						$select.= '<option value="'.$c['id'].'"'.($id == $c['id'] ? ' selected' : '').'>'.$c['name'].'</option>';
					}
					$select.= '</optgroup>';
				}
				break;
		}
		
		$select.= '</select>';
		
		return $select;
	}
	
	/**
	 * 更新缓存
	 *
	 * @return	array
	 */
	public function cache() {
	
		$data = $this->db->where('hidden', 0)->order_by('displayorder ASC,id ASC')->get('member_menu')->result_array();
		$cache = array();

		if ($data) {
			foreach ($data as $t) {
				if ($t['pid'] == 0) {
					$cache['data'][$t['id']] = $t;
					foreach ($data as $m) {
						if ($m['pid'] == $t['id']) {
							$cache['data'][$t['id']]['left'][$m['id']] = $m;
							foreach ($data as $n) {
								if ($n['pid'] == $m['id']) {
									$cache['data'][$t['id']]['left'][$m['id']]['link'][$n['id']] = $n;
									if ($n['uri']) {
										$n['tid'] = $t['id'];
										$cache['uri'][$n['uri']] = $n;
									}
								}
							}
						}
					}
				}
			}
			$this->dcache->set('member-menu', $cache);
		} else {
			$this->dcache->delete('member-menu');
		}

		
		return $cache;
	}
	
	public function init() {
	
		// 清空菜单
		$this->db->query('TRUNCATE `'.$this->db->dbprefix('member_menu').'`');
		
		// 导入初始化菜单数据
		$this->ci->sql_query(str_replace(
			'{dbprefix}',
			$this->db->dbprefix,
			file_get_contents(WEBPATH.'cache/install/member_menu.sql')
		));
		// 按模块安装菜单
		$module = $this->db->get('module')->result_array();
		if (MEMBER_OPEN_SPACE) {
			$module[] = array(
				'id' => 0,
				'dirname' => 'space',
				'share' => 0,
			);
		}
		if ($module) {
			foreach ($module as $m) {
				$this->init_module($m);
			}
		}
		// 按应用安装菜单
		$app = $this->db->get('application')->result_array();
		if ($app) {
			foreach ($app as $a) {
				$dir = $a['dirname'];
				if (is_file(FCPATH.'app/'.$dir.'/config/menu.php')) {
					$menu = require FCPATH.'app/'.$dir.'/config/menu.php';
					$this->system_model->add_app_menu($menu, $dir, $a['id']);
				}
			}
		}
		// 安装空间模型
		if (MEMBER_OPEN_SPACE) {
			$space = $this->db->get('space_model')->result_array();
			if ($space) {
				foreach ($space as $t) {
					$id = $t['id'];
					$uri = 'space/space'.$id.'/index';
					if (!$this->db->where('uri', $uri)->count_all_results('member_menu')) {
						$this->db->insert('member_menu', array(
							'pid' => 26,
							'uri' => $uri,
							'url' => '',
							'mark' => 'space-'.$id,
							'name' => $t['name'].'管理',
							'icon' => 'fa fa-th-large',
							'target' => 0,
							'hidden' => 0,
							'displayorder' => $id + 5,
						));
					}
				}
			}
		}
		// 按分支系统安装
		if ($this->ci->branch) {
			foreach ($this->ci->branch as $dir) {
				$path = FCPATH.'branch/'.$dir.'/';
				// 安装菜单
				$menu = require $path.'menu.php';
				if ($menu['member'] && $menu['member']['menu']) {
					// 会员顶级菜单
					$this->system_model->add_member_menu(array(
						'uri' => '',
						'url' => '',
						'pid' => 0,
						'mark' => 'branch-'.$dir,
						'name' => $menu['member']['name'],
						'icon' => $menu['member']['icon'] ? $menu['member']['icon'] : dr_get_icon_m($dir),
						'hidden' => 0,
						'displayorder' => 0,
					));
					$topid = $this->db->insert_id();
					foreach ($menu['member']['menu'] as $left) {
						if ($left['menu']) {
							// 分组菜单名称
							$this->system_model->add_member_menu(array(
								'uri' => '',
								'url' => '',
								'pid' => $topid,
								'mark' => 'branch-'.$dir,
								'name' => $left['name'],
								'icon' => $left['icon'] ? $left['icon'] : 'fa fa-th-large',
								'target' => 0,
								'hidden' => 0,
								'displayorder' => 0,
							));
							$leftid = $this->db->insert_id();
							foreach ($left['menu'] as $link) {
								$this->system_model->add_member_menu(array(
									'pid' => $leftid,
									'url' => '',
									'uri' => 'member/'.$link['uri'],
									'mark' => 'branch-'.$dir,
									'name' => $link['name'],
									'icon' => $link['icon'] ? $link['icon'] : 'fa fa-th-large',
									'target' => 0,
									'hidden' => 0,
									'displayorder' => 0,
								));
							}
						}
					}
				}
			}
		}
	}

    // 获取自己id和子id
    private function _get_id($id) {

        if (!$id) {
            return NULL;
        }

        $this->ids[$id] = $id;

        $data = $this->db->select('id')->where('pid', $id)->get('member_menu')->result_array();
        if (!$data) {
            return NULL;
        }

        foreach ($data as $t) {
            $this->ids[$t['id']] = $t['id'];
            $this->_get_id($t['id']);
        }
    }

    public function delete($ids) {

        $this->ids = array();

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $this->_get_id($id);
            }
        } else {
            $this->_get_id($ids);
        }

        if ($this->ids) {
            $this->db->where_in('id', $this->ids)->delete('member_menu');
        }
    }
	
	public function init_module($m) {

		$id = $m['id'];
		$dir = $m['dirname'];
		
		// 菜单
		if (is_file(FCPATH.'module/'.$dir.'/config/menu.php')) {
			$config = require FCPATH.'module/'.$dir.'/config/module.php';
			$name = $config['name']; // 顶部菜单名称
			$menu = require FCPATH.'module/'.$dir.'/config/menu.php';
			if ($menu['member']) {
				// 查询内容的顶级菜单
				$top = $this->db->where('mark', 'm_mod')->get('member_menu')->row_array();
				if (!$top) {
					$this->db->insert('member_menu', array(
						'uri' => '',
						'url' => '',
						'pid' => 0,
						'mark' => 'm_mod',
						'name' => '内容',
						'icon' => $m['icon'] ? $m['icon'] : 'fa fa-th-large',
						'target' => 0,
						'hidden' => 0,
						'displayorder' => 0,
					));
					$top['id'] = $this->db->insert_id();
				}
				$topid = $top['id'];
				// 链接菜单
				foreach ($menu['member'] as $left) {
					if ($left['menu']) {
						// 分组菜单名称
						$this->db->insert('member_menu', array(
							'uri' => '',
							'url' => '',
							'pid' => $topid,
							'mark' => 'left-'.$dir,
							'name' => $left['name'],
							'icon' => $left['icon'] ? $left['icon'] : 'fa fa-th-large',
							'target' => 0,
							'hidden' => 0,
							'displayorder' => 0,
						));
						$leftid = $this->db->insert_id();
						foreach ($left['menu'] as $link) {
							$this->db->insert('member_menu', array(
								'pid' => $leftid,
								'url' => '',
								'uri' => strpos($link['uri'], '{id}') === FALSE ? trim($dir.'/'.$link['uri'], '/') : str_replace('{id}', $id, $link['uri']),
								'mark' => 'module-'.$dir,
								'name' => $link['name'],
								'icon' => $link['icon'] ? $link['icon'] : 'fa fa-th-large',
								'target' => 0,
								'hidden' => 0,
								'displayorder' => 0,
							));
						}
					}
				}
				// 查询表单
				$form = $this->db->where('module', $dir)->get('module_form')->result_array();
				if ($form && $leftid) {
					// 将此表单放在模块菜单中
					foreach ($form as $f) {
						$f['setting'] = dr_string2array($f['setting']);
						$this->db->insert('member_menu', array(
							'pid' => $leftid,
							'url' => '',
							'uri' => $dir.'/form_'.$f['table'].'/index',
							'mark' => 'module-'.$dir,
							'name' => fc_lang('我的%s', $f['name']),
							'icon' => $f['setting']['icon'] ? $f['setting']['icon'] : 'fa fa-th-large',
							'target' => 0,
							'hidden' => 0,
							'displayorder' => 0,
						));
					}
				}
			}
		}
	}
}