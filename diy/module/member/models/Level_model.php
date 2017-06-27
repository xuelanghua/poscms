<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Level_model extends CI_Model{

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
		return $this->db
					->where('groupid', $this->groupid)
					->order_by('experience ASC')
					->get('member_level')
					->result_array();
	}
	
	/** 
	 * 数据
	 *
	 * @param	int		$id
	 * @return	array
	 */
	public function get($id) {
		return $this->db
					->limit(1)
					->where('groupid', $this->groupid)
					->where('id', (int)$id)
					->get('member_level')
					->row_array();
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

		$this->db->insert('member_level', array(
			'name' => $data['name'],
			'stars' => $data['stars'],
			'groupid' => $this->groupid,
			'experience' => $data['experience'],
            'allowupgrade' => 0
		));

		return $this->db->insert_id();
    }
	
	/**
	 * 添加
	 *
	 * @param	array	$data
	 * @return	int		存储表id
	 */
	public function edit($_data, $data) {

		if (!$data || !$_data) {
            return NULL;
        }

		$this->db->where('id', $_data['id'])->update('member_level', array(
			'name' => $data['name'],
			'stars' => $data['stars'],
			'groupid' => $this->groupid,
			'experience' => $data['experience'],
		));

		return $_data['id'];
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

		$this->db
			 ->where_in('id', $id)
			 ->where('groupid', $this->groupid)
			 ->delete('member_level');
		$this->db
			 ->where_in('levelid', $id)
			 ->where('groupid', $this->groupid)
			 ->update('member', array('levelid' => 0));
    }
}