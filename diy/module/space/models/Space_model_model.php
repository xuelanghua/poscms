<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Space_model_model extends CI_Model{

	/**
	 * 初始化
	 */
    public function __construct() {
        parent::__construct();
    }
	
	/**
	 * 修改模型
	 * 
	 * @param	intval	$id
	 * @param	array	$data
	 * @return	void
	 */
	public function edit($id, $data) {

		$this->db->where('id', (int)$id)->update('space_model', array(
			'name' => $data['name'],
			'setting' => dr_array2string($data['setting']),
		));

        $this->db->where('mark', 'space-'.$id)->update('admin_menu', array(
            'uri' => 'space/admin/content/index/mid/'.$id,
            'name' => $data['name'].'管理',
        ));

        $this->db->where('mark', 'space-'.$id)->update('member_menu', array(
            'uri' => 'space/space'.$id.'/index',
            'name' => $data['name'].'管理',
        ));
	}
	
	/**
	 * 添加模型
	 * 
	 * @param	array	$data
	 * @return	string|TRUE
	 */
	public function add($data) {
	
		if (!$data['name'] || !$data['table']) {
            return fc_lang('名称或者表名称不能为空');
        } elseif (in_array($data['table'], array('category', 'model'))
		|| !preg_match('/^[a-z]+[a-z0-9_\-]+$/i', $data['table'])
		|| $this->db->where('table', $data['table'])->count_all_results('space_model')) {
            return fc_lang('表名称重复或者格式不正确');
        }
		
		$data['setting'] = dr_array2string($data['setting']);
		
		if ($this->db->insert('space_model', $data)) {
			$id = $this->db->insert_id();
			$file = FCPATH.'module/space/controllers/member/Space'.$id.'.php';
			if (!file_put_contents($file, '<?php
			class Space'.$id.' extends M_Controller {

				public function __construct() {
					parent::__construct();
				}
				
				public function add() {
					$this->space_content_add();
				}
				
				public function edit() {
					$this->space_content_edit();
				}
				
				public function index() {
					$this->space_content_index();
				}
			}')) {
				$this->db->where('id', $id)->delete($this->db->dbprefix('space_model'));
				return fc_lang('目录(%s)没有写入权限', 'module/space/controllers/member/');
			}
			$sql = "
			CREATE TABLE IF NOT EXISTS `{tablename}` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `catid` mediumint(8) unsigned NOT NULL COMMENT '栏目id',
			  `title` varchar(255) NOT NULL COMMENT '标题',
			  `uid` mediumint(8) unsigned NOT NULL COMMENT '作者uid',
			  `author` varchar(50) NOT NULL COMMENT '作者',
			  `hits` int(10) unsigned NOT NULL COMMENT '点击量',
			  `status` tinyint(1) unsigned NOT NULL COMMENT '审核状态',
			  `inputtime` int(10) unsigned NOT NULL COMMENT '录入时间',
			  `updatetime` int(10) unsigned NOT NULL COMMENT '更新时间',
			  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY `id` (`id`),
			  KEY `uid` (`uid`),
			  KEY `hits` (`hits`),
			  KEY `catid` (`catid`),
			  KEY `status` (`status`),
			  KEY `inputtime` (`inputtime`),
			  KEY `updatetime` (`updatetime`),
			  KEY `displayorder` (`displayorder`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员空间".$data['name']."模型表';";
			
			$this->db->query(str_replace('{tablename}', $this->db->dbprefix('space_'.$data['table']), $sql));

            /*
            $menu = $this->db->where('mark', 'space-content')->get('admin_menu')->row_array();
            $pid = isset($menu['id']) ? intval($menu['id']) : 17;
			$this->system_model->add_admin_menu(array(
				'pid' => $pid,
				'uri' => 'space/admin/content/index/mid/'.$id,
				'url' => '',
				'mark' => 'space-'.$id,
				'name' => $data['name'].'管理',
                'hidden' => 0,
				'displayorder' => $id + 5,
			));
            更新缓存时增加菜单
            (*/

			$this->system_model->add_member_menu(array(
				'pid' => 26,
				'uri' => 'space/space'.$id.'/index',
				'url' => '',
				'mark' => 'space-'.$id,
				'name' => $data['name'].'管理',
                'hidden' => 0,
				'displayorder' => $id + 5,
			));
			
			$this->db->insert('field', array(
				'name' => '主题',
				'fieldname' => 'title',
				'fieldtype' => 'Text',
				'relatedid' => $id,
				'relatedname' => 'space',
				'isedit' => 1,
				'ismain' => 1,
				'ismember' => 1,
				'issystem' => 1,
				'issearch' => 1,
				'disabled' => 0,
				'setting' => dr_array2string(array(
					'option' => array(
						'width' => 400, // 表单宽度
						'fieldtype' => 'VARCHAR', // 字段类型
						'fieldlength' => '255' // 字段长度
					),
					'validate' => array(
						'xss' => 1, // xss过滤
						'required' => 1, // 表示必填
					)
				)),
				'displayorder' => 0,
			));
		}
		
		return TRUE;
	}
	
	/**
	 * 删除
	 * 
	 * @param	intval	id
	 */
	public function del($id) {
		
		if (!$id) {
            return NULL;
        }
		
		$data = $this->db->where('id', (int)$id)->select('table')->limit(1)->get('space_model')->row_array();
		if (!$data) {
            return NULL;
        }
		
		$this->db->where('mark', 'space-'.$id)->delete('admin_menu');
			 
		$this->db->where('mark', 'space-'.$id)->delete('member_menu');
			 
		$this->db->where('relatedid', (int)$id)->where('relatedname', 'space')->delete('field');
			
		$this->load->model('attachment_model');
		$table = $this->db->dbprefix('space_'.$data['table']);
		
		$this->db->query('DROP TABLE IF EXISTS `'.$table.'`');
		$this->attachment_model->delete_for_table($table, TRUE);
		
		$this->db->where('id', (int)$id)->delete('space_model');
			 
		$this->db->where('modelid', (int)$id)->delete('space_category');
			 
		@unlink(FCPATH.'module/space/controllers/member/Space_'.$data['table'].'.php');
		
		return NULL;
	}
	
	/**
	 * 生成缓存
	 * 
	 * @return	void
	 */
	public function cache() {
		
		$this->dcache->delete('space-model');
		
		$space = $this->db->get('space_model')->result_array();
		if (!$space) {
            return NULL;
        }
		
		$cache = array();
		
		foreach ($space as $t) {
			$data = $this->db->where('relatedid', $t['id'])->where('relatedname', 'space')->order_by('displayorder ASC,id ASC')->get('field')->result_array();
			if ($data) {
				foreach ($data as $field) {
					$field['setting'] = dr_string2array($field['setting']);
					$t['field'][$field['fieldname']] = $field;
				}
			}
			
			$t['setting'] = dr_string2array($t['setting']);
			$cache[$t['id']] = $t;
		}
		
		$this->dcache->set('space-model', $cache);

        return $cache;
	}
	
}