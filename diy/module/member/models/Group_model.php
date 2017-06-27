<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Group_model extends CI_Model{
    
	/**
	 * 初始化
	 */
    public function __construct() {
        parent::__construct();
    }
	
	/**
	 * 所有数据
	 *
	 * @return	void
	 */
	public function get_data() {

		$_data = $this->db->order_by('displayorder ASC, id ASC')->get('member_group')->result_array();
		if (!$_data) {
            return NULL;
        }

		$data = array();
		foreach ($_data as $t) {
			$t['level'] = $this->db->where('groupid', $t['id'])->count_all_results('member_level');
			$t['allowfield'] = dr_string2array($t['allowfield']);
			$t['spacefield'] = dr_string2array($t['spacefield']);
			$data[] = $t;
		}

		return $data;
	}
	
	/**
	 * 数据
	 *
	 * @param	int		$id
	 * @return	array
	 */
	public function get($id) {

		$_data = $this->db->where('id', (int)$id)->get('member_group')->row_array();
		if (!$_data) {
            return NULL;
        }

		$_data['allowfield'] = dr_string2array($_data['allowfield']);
		$_data['spacefield'] = dr_string2array($_data['spacefield']);

		return $_data;
	}
	
	/**
	 * 添加
	 *
	 * @param	array	$data
	 * @return	int		存储表id
	 */
	public function add($data) {

		if (!$data) {
            return NULL;
        }

		$this->db->insert('member_group', array(
			'name' => $data['name'],
			'theme' => $data['theme'],
			'template' => $data['template'],
			'price' => $data['price'],
			'unit' => (int)$data['unit'],
			'limit' => (int)$data['limit'],
			'overdue' => (int)$data['overdue'],
			'allowfield' => dr_array2string($data['allowfield']),
			'spacefield' => dr_array2string($data['spacefield']),
			'allowregister' => (int)$data['allowregister'],
			'allowapply' => (int)$data['allowapply'],
			'allowapply_orther' => (int)$data['allowapply_orther'],
			'allowspace' => (int)$data['allowspace'],
            'spacedomain' => (int)$data['spacedomain'],
            'displayorder' => 0,
            'spacetemplate' => $data['spacetemplate'],
		));

		return $this->db->insert_id();
    }
	
	/**
	 * 修改
	 *
	 * @param	array	$data
	 * @return	int		存储表id
	 */
	public function edit($id, $data) {

		if (!$data || !$id) {
            return NULL;
        }

		$this->db->where('id', $id)->update('member_group', array(
			'name' => $data['name'],
			'unit' => (int)$data['unit'],
			'theme' => $data['theme'],
			'price' => $data['price'],
			'limit' => (int)$data['limit'],
			'overdue' => (int)$data['overdue'],
			'template' => $data['template'],
			'allowfield' => dr_array2string($data['allowfield']),
			'spacefield' => dr_array2string($data['spacefield']),
			'allowregister' => (int)$data['allowregister'],
			'allowapply' => (int)$data['allowapply'],
			'allowapply_orther' => (int)$data['allowapply_orther'],
			'allowspace' => (int)$data['allowspace'],
            'spacedomain' => (int)$data['spacedomain'],
            'spacetemplate' => $data['spacetemplate'],
		));

        $syn = $this->input->post('syn');
        $synid = $this->input->post('synid');

        if ($syn && $synid) {
            $update = array();
			// 主题风格
			in_array(1, $syn) && $update['theme'] = $data['theme'];
			// 模板目录
			in_array(2, $syn) && $update['template'] = $data['template'];
			// 会员字段
			in_array(3, $syn) && $update['allowfield'] = dr_array2string($data['allowfield']);
			// 空间字段
			in_array(4, $syn) && $update['spacefield'] = dr_array2string($data['spacefield']);
			// 允许使用空间
			in_array(5, $syn) && $update['allowspace'] = $data['allowspace'];

            $this->db->where_in('id', $synid)->update('member_group', $update);
        }
    }
	
	/**
	 * 删除
	 *
	 * @param	array	$id
	 */
	public function del($id) {

		if (!$id) {
            return NULL;
        }

		!is_array($id) && $id = array($id);

		foreach ($id as $i => $ii) {
			if ($ii <= 3) {
                unset($id[$i]);
            }
		}

		if (!$id) {
            return NULL;
        }

		$this->db->where_in('id', $id)->delete('member_group');
		$this->db->where_in('groupid', $id)->update('member', array('groupid' => 3));
    }
}