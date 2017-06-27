<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Space_model extends CI_Model{

	public $cache_file;
    
	/**
	 * 初始化
	 */
    public function __construct() {
        parent::__construct();
    }
	
	/**
	 * 会员空间信息
	 * 
	 * @param	intval	uid
	 * @return	array
	 */
	public function get($uid) {
		
		if (!$uid) {
            return NULL;
        }
		
		$space = $this->db->where('uid', $uid)->limit(1)->get('space')->row_array();
		if (!$space) {
            return NULL;
        }
		
		return $space;
	}

	/**
	 * 会员空间域名
	 *
	 * @param	intval	uid
	 * @return	array
	 */
	public function get_domain($uid) {

		if (!$uid) {
            return NULL;
        }

		$space = $this->db->where('uid', $uid)->limit(1)->get('space_domain')->row_array();
		if (!$space) {
            return NULL;
        }

		return $space['domain'];
	}
	
	
	/**
	 * 会员空间信息
	 * 
	 * @param	intval	$uid
	 * @param	intval	$groupid
	 * @param	array	$data
	 * @return	intval
	 */
	public function update($uid, $groupid, $data) {
		// 空间名称重复
		if (isset($data['name'])
            && $this->db->where('uid <>', $uid)->where('name', $data['name'])->count_all_results('space')) {
            return 0;
        }
		if ($this->db->where('uid', $uid)->count_all_results('space')) {
			// 修改资料
			$this->db->where('uid', $uid)->update('space', $data);
			return 1;
		} else {
			// 创建空间
            $verify = (int)$this->ci->get_cache('member', 'setting', 'space', 'verify');
            $template = $this->ci->get_cache('member', 'group', $this->member['groupid'], 'spacetemplate');
			$data['uid'] = $uid;
			$data['hits'] = 0;
			$data['style'] = $template && is_dir(WEBPATH.'statics/space/'.$template) ? $template : 'default';
			$data['status'] = $verify ? 0 : 1;
			$data['regtime'] = SYS_TIME;
			$this->db->replace('space', $data);
			$this->init($uid, $groupid);
			return $verify ? -1 : 1;
		}
	}
	
	/**
	 * 条件查询
	 *
	 * @param	object	$select	查询对象
	 * @param	array	$param	条件参数
	 * @return	array	
	 */
	private function _where(&$select, $param) {
	
		if (isset($param['keyword']) && $param['keyword']) {
			$select->like('space.name', urldecode($param['keyword']));
		}
		
		if (strlen($param['status']) > 0) {
			$select->where('space.status', (int)$param['status']);
		}
		
		if (isset($param['flag'])) {
			$_param['flag'] = $param['flag'];
			$select->where('space_flag.flag', $param['flag']);
		}
		
	}
	
	/**
	 * 数据分页显示
	 *
	 * @param	array	$param	条件参数
	 * @param	intval	$page	页数
	 * @param	intval	$total	总数据
	 * @return	array	
	 */
	public function limit_page($param, $page, $total) {
	
		if (!$total) {
			$select	= $this->db->select('count(*) as total');
			$this->_where($select, $param);
			$data = $select->get(isset($param['flag']) ? 'space_flag' : 'space')->row_array();
			unset($select);
			$total = (int)$data['total'];
			if (!$total) {
                return array(array(), array('total' => 0));
            }
		}
		
		$select	= $this->db;
		$this->_where($select, $param);
		if (isset($param['flag'])) {
			$flag = $this->db
						 ->where('flag', (int)$param['flag'])
						 ->get('space_flag')
						 ->result_array();
			if ($flag) {
				$in = array();
				foreach ($flag as $t) {
					$in[] = $t['uid'];
				}
				$select->where_in('space.uid', $in);
                unset($in);
			}
            unset($flag);
		}

        if (isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0) {
            $order = dr_get_order_string($this->input->get('order', TRUE), 'updatetime desc');
            list($field, $a) = explode(' ', $order);
            if (in_array($field, array('username', 'groupid'))) {
                $torder = 'member.'.$order;
            } else {
                $torder = 'space.'.$order;
            }
            unset($field, $a);
        } else {
            $order = 'regtime DESC';
            $torder = 'space.'.$order;
        }

		$data = $select->select('space.*,member.`username`,member.`groupid`')
					   ->from('space')
					   ->join('member', 'space.uid = member.uid', 'left')
					   ->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1))
					   ->order_by($torder)
					   ->get()
					   ->result_array();
		$param['total'] = $total;
		$param['order'] = $order;

		return array($data, $param);
	}
	
	// 初始化空间
	public function init($uid, $gid) {

        //查询初始化栏目
        $this->load->model('space_init_model');
        $pids = array();
        $category = $this->space_init_model->get_data(0, $gid, 1);
        if (!$category) {
            return NULL;
        }

        foreach ($category as $i => $t) {
            $this->db->insert('space_category', array(
                'uid' => $uid,
                'pid' => $t['pid'] ? (int)$pids[$t['pid']] : 0,
                'body' => '',
                'type' => (int)$t['type'],
                'name' => trim($t['name']),
                'link' => trim($t['link']),
                'title' => '',
                'showid' => (int)$t['showid'],
                'modelid' => (int)$t['modelid'],
                'keywords' => '',
                'description' => '',
                'displayorder' => 0
            ));
            $pids[$i] = $this->db->insert_id();
        }
	}
	
	// 删除空间
	public function delete($ids) {
	
		if (!$ids) {
            return NULL;
        }
		
		$this->db->where_in('uid', $ids)->delete('space');
		$model = $this->db->get('space_model')->result_array();
		if ($model) {
            $this->db->db_debug = FALSE;
			foreach ($model as $t) {
				$this->db->where_in('uid', $ids)->delete('space_'.$t['table']);
			}
		}
		$this->db->where_in('uid', $ids)->delete('space_flag');
		$this->db->where_in('uid', $ids)->delete('space_category');
	}
}