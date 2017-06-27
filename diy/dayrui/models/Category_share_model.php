<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Category_share_model extends CI_Model {

	public $link;
	public $prefix;
	public $tablename;
	private	$categorys;
	
	/*
	 * 模块栏目
	 */
    public function __construct() {
        parent::__construct();
		$this->prefix = $this->db->dbprefix(SITE_ID.'_share');
		$this->tablename = $this->db->dbprefix(SITE_ID.'_share_category');
    }
	
	/**
	 * 获取权限
	 *
	 * @param	intval	$id
	 * @return	array
	 */
	public function get_permission($id) {
		$data = $this->db->where('id', $id)->select('permission')->get($this->tablename)->row_array();
		return dr_string2array($data['permission']);
	}
	
	/**
	 * 单跳栏目
	 *
	 * @param	intval	$id
	 * @return	array
	 */
	public function get($id) {
	
		$data = $this->db->where('id', $id)->limit(1)->get($this->tablename)->row_array();
		if (isset($data['setting'])) {
            $data['setting'] = dr_string2array($data['setting']);
        }
		if (isset($data['permission'])) {
            $data['permission'] = dr_string2array($data['permission']);
        }
		
		return $data;
	}
	
	/**
	 * 所有数据
	 *
	 * @return	array
	 */
	public function get_data() {
	
		$data = array();
		$_data = $this->db->order_by('displayorder ASC,id ASC')->get($this->tablename)->result_array();
		if (!$_data) {
            return $data;
        }
		
		foreach ($_data as $t) {
            $t['setting'] = dr_string2array($t['setting']);
            $t['permission'] = dr_string2array($t['permission']);
			$data[$t['id']]	= $t;
		}
		
		return $data;
	}
	
	/**
	 * 批量添加
	 *
	 * @param	array	$names
	 * @param	array	$data
	 * @return	int
	 */
	public function add_all($names, $data, $field = array()) {
	
		if (!$names) {
            return 0;
        }
		
		$count = 0;
		$_data = explode(PHP_EOL, $names);
		
		foreach ($_data as $t) {
		
			list($name, $dir) = explode('|', $t);
			$data['name'] = trim($name);
			if (!$data['name']) {
                continue;
            } elseif ($data['tid'] == 1 && !$data['mid']) {
                continue;
            }
			
			!$dir && $dir = dr_word2pinyin($data['name']);
			$this->dirname_exitsts($dir) && $dir.= rand(0,99);

            $insert = array(
                'pid' => (int)$data['pid'],
                'pids' => '',
                'name' => $data['name'],
                'show' => $data['show'],
                'letter' => $dir{0},
                'setting' => dr_array2string($data['setting']),
                'dirname' => str_replace(array('/', '-', '_'), '', $dir),
                'pdirname' => '',
                'childids' => '',
                'displayorder' => 0
            );
            $field = $this->ci->get_table_field($this->tablename);
            foreach ($data as $i => $t) {
				isset($field[$i]) && !isset($insert[$i]) && $insert[$i] = $t;
            }
			$this->db->insert($this->tablename, $insert);
            $id = $this->db->insert_id();

			// 附件归档
			$this->ci->attachment_handle($this->uid, $this->tablename.'-'.$id, $field);
            // 更新至网站导航
            $this->load->model('navigator_model');
            $this->navigator_model->syn_value($data, $id, APP_DIR);

			$count ++;
		}

		$this->repair();
		
		return $count;
	}

    /**
     * 获取全部父级的mid值, 或者更新
     */
    public function get_parent_mid($category, $id) {

        if (!isset($category[$id])) {
            return array();
        }

        $mid = '';
        $ids = @array_merge(explode(',',  $category[$id]['childids']), explode(',',  $category[$id]['pids']));
        foreach ($ids as $id) {
            if ($id && $category[$id] && $category[$id]['mid']) {
                $mid = $category[$id]['mid'];
                break;
            }
        }

        return array($mid, $ids);
    }
	
	/**
	 * 单个添加
	 *
	 * @param	array	$data
	 * @return	intval
	 */
	public function add($data, $field = array()) {

        if (!$data) {
            return fc_lang('栏目数据不存在');
        } elseif (!$data['dirname']) {
            return fc_lang('栏目目录不存在');
        } elseif ($this->dirname_exitsts($data['dirname'])) {
            return fc_lang('目录已经存在了');
        } elseif ($data['tid'] == 1 && !$data['mid']) {
            return fc_lang('共享模块必须选择');
        }


		$mid = $data['tid'] == 1 ? $data['mid'] : '';
        $insert = array(
            'pid' => (int)$data['pid'],
            'tid' => (int)$data['tid'],
            'mid' => $mid,
            'pids' => '',
            'name' => trim($data['name']),
            'show' => $data['show'],
            'domain' => $data['domain'] ? $data['domain'] : '',
            'letter' => $data['letter'] ? $data['letter'] : $data['dirname']{0},
			'content' => $data['content'],
            'setting' => dr_array2string($data['setting']),
            'dirname' => str_replace(array('/', '-', '_'), '', $data['dirname']),
            'pdirname' => '',
            'childids' => '',
			'pcatpost' => (int)$data['pcatpost'],
            'displayorder' => 0
        );

        foreach ($data as $i => $t) {
			isset($field[$i]) && !isset($insert[$i]) && $insert[$i] = $t;
        }
		$this->db->insert($this->tablename, $insert);
		
		$id = $this->db->insert_id();
		$this->repair();

        // 更新至网站导航
		$mid && $this->load->model('navigator_model');
		$mid && $this->navigator_model->syn_value($data, $id, $mid);

		return $id;
	}
	
	/**
	 * 修改
	 *
	 * @param	intval	$id
	 * @param	array	$data
	 * @return	string
	 */
	public function edit($id, $data, $_data, $field = array()) {
	
		if (!$data) {
            return fc_lang('栏目数据不存在');
        } elseif (!$data['dirname']) {
            return fc_lang('栏目目录不存在');
        } elseif ($this->dirname_exitsts($data['dirname'], $id)) {
            return fc_lang('目录已经存在了');
        }

		!isset($data['setting']['admin']) && $data['setting']['admin'] = array();
		!isset($data['setting']['member']) && $data['setting']['member'] = array();

		$permission = $data['rule'];
		if ($_data['permission']) {
			foreach ($_data['permission'] as $i => $t) {
				unset($t['show'], $t['forbidden'], $t['add'], $t['edit'], $t['del']);
				$permission[$i] = $permission[$i] ? $permission[$i] + $t : $t;
			}
		}
		$data['setting']['html'] = intval($data['setting']['html']);
        $data['setting']['getchild'] = intval($data['setting']['getchild']);
		$update = array(
            'pid' => (int)$data['pid'],
            'name' => $data['name'],
            'show' => $data['show'],
			'domain' => $data['domain'] ? $data['domain'] : '',
            'letter' => $data['letter'] ? $data['letter'] : $data['dirname']{0},
			'content' => $data['content'],
            'dirname' => str_replace(array('/', '-', '_'), '', $data['dirname']),
            'setting' => dr_array2string(array_merge($_data['setting'], $data['setting'])),
			'pcatpost' => (int)$data['pcatpost'],
            'permission' => dr_array2string($permission)
        );

        foreach ($data as $i => $t) {
			isset($field[$i]) && !isset($update[$i]) && $update[$i] = $t;
        }

		$this->db->where('id', $id)->update($this->tablename, $update);
		$this->repair();

        // 更新至网站导航
        $this->load->model('navigator_model');
        $this->navigator_model->syn_value($data, $id, APP_DIR);

		return fc_lang('操作成功');
	}
	
	/**
	 * 同步
	 *
	 * @param	array	$data
	 * @param	array	$_data
	 * @return	NULL
	 */
	public function syn($data, $_data) {

		if (!$data) {
            return NULL;
        }

        $data['child'] = $data['pcatpost'] ? 0 : $data['child'];

		$option = $this->input->post('syn');
		if (!$option) {
            return NULL;
        }

		$syn = $this->input->post('synid');
		if (!$syn) {
            return NULL;
        }

		$permission = $data['rule'];
		if ($_data['permission']) {
			foreach ($_data['permission'] as $i => $t) {
				unset($t['show'], $t['forbidden'], $t['add'], $t['edit'], $t['del']);
				$permission[$i] = $permission[$i] ? $permission[$i] + $t : $t;
			}
		}

		foreach ($syn as $id) {
			$cat = $this->get($id);
			if ($cat['tid'] == 2) {
				continue; // 排除外链
			}
			$update = array();
			$_setting = $cat['setting'];
			in_array(1, $option) && $_setting['seo'] = $data['setting']['seo'];
			in_array(2, $option) && $_setting['template'] = $data['setting']['template'];
			in_array(3, $option) && $_setting['admin'] = $data['setting']['admin'];
			in_array(4, $option) && $update['permission'] = dr_array2string($permission);
			in_array(5, $option) && $_setting['urlrule'] = $data['setting']['urlrule'];
			in_array(6, $option) && $_setting['html'] = $data['setting']['html'];
			$data['child'] && $_setting['admin'] = '';
			$data['child'] && $update['permission'] = '';
			$update['setting'] = dr_array2string($_setting);
			$this->db->where('id', $id)->update($this->tablename, $update);
		}
		
		return NULL;
	}
	
	/**
	 * 目录是否存在
	 *
	 * @param	array	$data
	 * @return	bool
	 */
	private function dirname_exitsts($dir, $id = 0) {
		return $dir ? $this->db->where('dirname', $dir)->where('id<>', $id)->count_all_results($this->tablename) : 1;
	}
	
	/**
	 * 获取全部栏目
	 *
	 * @param	array	$data
	 * @return	bool
	 */
	private function get_categorys($data = array()) {
	
		if (is_array($data) && !empty($data)) {
			foreach ($data as $catid => $c) {
				$this->categorys[$catid] = $c;
				$result = array();
				foreach ($this->categorys as $_k => $_v) {
					$_v['pid'] && $result[] = $_v;
				}
			}
		} 
		
		return true;
	}
	
	
	/**
	 * 获取父级
	 * 
	 * @param	integer	$catid	��ĿID
	 * @param	array	$pids	��Ŀ¼ID
	 * @param	integer	$n		���ҵĲ��
	 * @return	string
	 */
	private function get_pids($catid, $pids = '', $n = 1) {
	
		if ($n > 5
            || !is_array($this->categorys)
            || !isset($this->categorys[$catid])) {
            return FALSE;
        }
		
		$pid = $this->categorys[$catid]['pid'];
		$pids = $pids ? $pid.','.$pids : $pid;
		
		if ($pid) {
			$pids = $this->get_pids($pid, $pids, ++$n);
		} else {
			$this->categorys[$catid]['pids'] = $pids;
		}
		
		return $pids;
	}
	
	/**
	 * 获取全部子栏目
	 * 
	 * @param	$catid	��ĿID
	 * @return	string
	 */
	private function get_childids($catid) {
	
		$childids = $catid;
		
		if (is_array($this->categorys)) {
			foreach ($this->categorys as $id => $cat) {
				if ($cat['pid']
                    && $id != $catid
                    && $cat['pid'] == $catid) {
					$childids.= ','.$this->get_childids($id);
				}
			}
		}
		
		return $childids;
	}
	
	/**
	 * 上级栏目目录
	 * 
	 * @param	$catid	��ĿID
	 * @return	string
	 */
	public function get_pdirname($catid) {
	
		if ($this->categorys[$catid]['pid']==0) {
            return '';
        }

		$t = $this->categorys[$catid];
		$pids = $t['pids'];
		$pids = explode(',', $pids);
		$catdirs = array();
		krsort($pids);
		
		foreach ($pids as $id) {
			if ($id == 0) {
                continue;
            }
			$catdirs[] = $this->categorys[$id]['dirname'];
			if ($this->categorys[$id]['pdirname'] == '') {
                break;
            }
		}
		krsort($catdirs);
		
		return implode('/', $catdirs).'/';
	}
	
	/**
     * 修复栏目完整数据
	 */
	public function repair() {
	
		$this->categorys = $categorys = array();
		$this->categorys = $categorys = $this->get_data();
		$this->get_categorys($categorys);
		
		if (is_array($this->categorys)) {
		
			foreach ($this->categorys as $catid => $cat) {
				$pids = $this->get_pids($catid);
				$childids = $this->get_childids($catid);
				$child = is_numeric($childids) ? 0 : 1;
				$pdirname = $this->get_pdirname($catid);

                if ($cat['mid'] && $cat['pid']) {
                    list($pmid, $ids) = $this->get_parent_mid($this->categorys, $cat['pid']);
                    $pmid && $this->db->where_in('id', $ids)->update($this->tablename, array('mid' => $pmid));
                }

				if ($categorys[$catid]['pdirname'] != $pdirname 
				|| $categorys[$catid]['pids'] != $pids 
				|| $categorys[$catid]['childids'] != $childids 
				|| $categorys[$catid]['child'] != $child) {
					$this->db->where('id', $cat['id'])->update($this->tablename, array(
						'pids' => $pids,
						'child' => $child,
						'childids' => $childids,
						'pdirname' => $pdirname
					));
				}
			}
		}
	}
}