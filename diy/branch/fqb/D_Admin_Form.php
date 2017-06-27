<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class D_Admin_Form extends M_Controller {

	public $ids; // 可操作的所有内容id
	public $fid; // 表单表名称
	public $cid; // 内容id
	public $form; // 表单信息
	protected $cdata; // 内容数据
	protected $field; // 全部字段
	protected $sysfield; // 系统字段
	protected $cache_file; // 缓存文件名
	
    /**
     * 构造函数（模块表单后台）
     */
    public function __construct() {
        parent::__construct();
		// 表单验证
        $this->fid = trim(strchr($this->router->class, '_'), '_');
		$this->form = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'form', $this->fid);
		!$this->form && $this->admin_msg(fc_lang('表单不存在，请更新表单缓存'));
		// 内容验证
		$this->cid = (int)$this->input->get('cid');
		$this->cdata = $this->db->where('id', $this->cid)->get(SITE_ID.'_'.APP_DIR)->row_array();
		$this->cid && !$this->cdata && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        // 判断角色权限
		/*
		if ($this->admin['adminid'] > 1) {

		}*/
		// 系统字段
		$this->load->library('Dfield', array(APP_DIR));
		$this->sysfield = array(
			'author' => array(
				'name' => fc_lang('录入作者'),
				'ismain' => 1,
				'fieldtype' => 'Text',
				'fieldname' => 'author',
				'setting' => array(
					'option' => array(
						'width' => 200,
						'value'	=> $this->admin['username']
					),
					'validate' => array(
						'tips' => fc_lang('填写录入者的会员名称'),
						'check' => '_check_member',
						'required' => 1,
						'formattr' => '',
					)
				)
			),
			'inputtime' => array(
				'name' => fc_lang('录入时间'),
				'ismain' => 1,
				'fieldtype' => 'Date',
				'fieldname' => 'inputtime',
				'setting' => array(
					'option' => array(
						'width' => 200
					),
					'validate' => array(
						'required' => 1,
						'formattr' => '',
					)
				)
			),
			'inputip' => array(
				'name' => fc_lang('客户端IP'),
				'ismain' => 1,
				'fieldtype' => 'Text',
				'fieldname' => 'inputip',
				'setting' => array(
					'option' => array(
						'width' => 200,
						'value' => $this->input->ip_address()
					),
					'validate' => array(
						'formattr' => '',
					)
				)
			)
		);
		$this->field = $this->form['field'] ? array_merge($this->form['field'], $this->sysfield) : $this->sysfield;
		$this->load->model('mform_model');
        if ($this->cid) {
			$my = array(
				fc_lang('返回') => array($this->_get_back_url(APP_DIR.'/home/index'), 'mail-reply'),
				$this->form['name'] => array(APP_DIR.'/admin/'.$this->router->class.'/index/cid/'.$this->cid, $this->form['setting']['icon'] ? str_replace('fa fa-', '', $this->form['setting']['icon']) : 'table'),
                fc_lang('发布')  => array(APP_DIR.'/admin/'.$this->router->class.'/add/cid/'.$this->cid, 'plus'),
				fc_lang('发布预览') => array(SITE_URL.'index.php?s='.APP_DIR.'&c='.$this->router->class.'&cid='.$this->cid.'" target="_blank', 'send'),
			);
			$this->router->method == 'show' && $my[fc_lang('查看')] = array(APP_DIR.'/admin/'.$this->router->class.'/show/cid/'.$this->cid.'/id/'.intval($_GET['id']), 'search');
		} else {
			$my = array(
				$this->form['name'] => array(APP_DIR.'/admin/'.$this->router->class.'/index', $this->form['setting']['icon'] ? str_replace('fa fa-', '', $this->form['setting']['icon']) : 'table'),
			);
			$this->router->method == 'show' && $my[fc_lang('查看')] = array(APP_DIR.'/admin/'.$this->router->class.'/show/id/'.intval($_GET['id']), 'search');
		}
		$menu = $this->get_menu_v3($my);
        // 判断栏目权限，如果数据量大时可以注释此判断
        if (IS_ADMIN && $this->admin['adminid'] > 1) {
            $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category');
            if ($category) {
                $catid = array();
                foreach ($category as $c) {
                    // 具有管理权限的栏目id集合
					!$c['child'] && $c['setting']['admin'][$this->admin['adminid']]['show'] == 1 && $catid[] = $c['id'];
                }
                unset($category);
                if ($catid) {
                    $data = $this->db->select('id')->where_in('catid', $catid)->get(SITE_ID.'_'.APP_DIR.'_index')->result_array();
                    if ($data) {
                        foreach ($data as $t) {
                            $this->ids[] = (int)$t['id'];
                        }
                    }
                }

            }
        }
		$this->template->assign(array(
            'cid' => $this->cid,
			'menu' => $menu,
			'field' => array_merge(
				array(
					array(
						'name' => fc_lang('名称'),
						'ismain' => 1,
						'fieldname' => 'title',
					)
				), $this->form['field']),
			'_class' => $this->router->class,
		));
		$this->cache_file = md5($this->duri->uri(1).$this->uid.$this->sid.$this->input->ip_address().$this->input->user_agent()); // 缓存文件名称
	}
	
	/**
	 * 条件查询
	 *
	 * @param	object	$select	查询对象
	 * @param	intval	$where	是否搜索
	 * @return	intval	
	 */
	protected function _where(&$select, $param) {
	
		// 存在POST提交时
		if (IS_POST) {
            $search = $this->input->post('data');
            $param['keyword'] = $search['keyword'];
            $param['start'] = $search['start'];
            $param['end'] = $search['end'];
            $param['field'] = $search['field'];
		}

        // 相对于内容
		$this->cid && $select->where('cid', $this->cid);

        // 权限筛选
		$this->ids && $select->where_in('cid', $this->ids);
		
		// 存在search参数时，读取缓存文件
		if ($param) {
            if (isset($param['keyword']) && $param['keyword'] != '') {
				$field = $this->field;
				$param['field'] = $param['field'] ? $param['field'] : 'subject';
				if ($param['field'] == 'id') {
					// 按id查询
					$id = array();
					$ids = explode(',', $param['keyword']);
					foreach ($ids as $i) {
						$id[] = (int)$i;
					}
					$select->where_in('id', $id);
				} elseif ($param['field'] == 'title') {
					// 按主题
					$select->where('cid IN (select id from '.$this->db->dbprefix(SITE_ID.'_'.APP_DIR).' where title Like "%'.urldecode($param['keyword']).'%")');
				} elseif ($field[$param['field']]['fieldtype'] == 'Linkage'
                    && $field[$param['field']]['setting']['option']['linkage']) {
                    // 联动菜单搜索
                    if (is_numeric($param['keyword'])) {
                        // 联动菜单id查询
                        $link = dr_linkage($field[$param['field']]['setting']['option']['linkage'], (int)$param['keyword'], 0, 'childids');
						$link && $select->where($param['field'].' IN ('.$link.')');
                    } else {
                        // 联动菜单名称查询
                        $id = (int)$this->get_cache('linkid-'.SITE_ID, $field[$param['field']]['setting']['option']['linkage']);
						$id && $select->where($param['field'].' IN (select id from `'.$select->dbprefix('linkage_data_'.$id).'` where `name` like "%'.$param['keyword'].'%")');
                    }
                } else {
                    $select->like($param['field'], urldecode($param['keyword']));
                }
            }
            // 时间搜索
            if (isset($param['start']) && $param['start']) {
                $param['end'] = strtotime(date('Y-m-d 23:59:59', $param['end'] ? $param['end'] : SYS_TIME));
                $param['start'] = strtotime(date('Y-m-d 00:00:00', $param['start']));
                $select->where('inputtime BETWEEN ' . $param['start'] . ' AND ' . $param['end']);
            } elseif (isset($param['end']) && $param['end']) {
                $param['end'] = strtotime(date('Y-m-d 23:59:59', $param['end']));
                $param['start'] = 0;
                $select->where('inputtime BETWEEN ' . $param['start'] . ' AND ' . $param['end']);
            }
		}
		
		return $param;
	}
	
	/**
	 * 数据分页显示
	 *
	 * @return	array	
	 */
	protected function limit_page() {

        if (IS_POST) {
            $page = $_GET['page'] = 1;
            $total = 0;
        } else {
            $page = max(1, (int)$this->input->get('page'));
            $total = (int)$this->input->get('total');
        }

        $param = $this->input->get(NULL);
        unset($param['s'],$param['c'],$param['m'],$param['d'],$param['page']);
		$table = SITE_ID.'_'.APP_DIR.'_form_'.$this->fid;
		
		if (!$total) {
			$select	= $this->db->select('count(*) as total');
            $param = $this->_where($select, $param);
			$data = $select->get($table)->row_array();
			unset($select);
			$total = (int)$data['total'];
			if (!$total) {
                return array(array(), $total, $param);
            }
		}

		$select	= $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $param = $this->_where($select, $param);
        $_order = isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0 ? $this->input->get('order') : 'inputtime DESC';
        $data = $select->order_by($_order)->get($table)->result_array();
					   
		return array($data, $total, $param);
	}

    /**
     * 管理
     */
    public function index() {

		if ($this->input->post('action') == 'del') {
			$ids = $this->input->post('ids');
			!$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
			// 删除表对应的附件
			$table = SITE_ID.'_'.APP_DIR.'_form_'.$this->fid;
			$this->load->model('attachment_model');
			foreach ($ids as $id) {
				$row = $this->db->where('id', $id)->get($table)->row_array();
                if ($row) {
                    $this->db->where('id', $row['cid'])->set($this->fid.'_total', $this->fid.'_total - 1', FALSE)->update(SITE_ID.'_'.APP_DIR);
                    $this->db->where('id', $id)->delete($table);
                    $this->db->where('id', $id)->delete($table.'_data_'.(int)$row['tableid']);
                    $this->attachment_model->delete_for_table($table.'-'.$id);
                }
			}
            $this->system_log('删除站点【#'.SITE_ID.'】模块【'.APP_DIR.'】表单【'.$this->fid.'】内容【#'.@implode(',', $ids).'】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		
		// 数据库中分页查询
		list($data, $total, $param)	= $this->limit_page();
        $param['cid'] = $this->cid;
        $param['total'] = $total;
		$tpl = APPPATH.'templates/admin/mform_listc_'.$this->fid.'.html';
		$this->template->assign(array(
			'tpl' => str_replace(FCPATH, '/', $tpl),
			'list' => $data,
			'total' => $total,
			'pages'	=> $this->get_pagination(dr_url(APP_DIR.'/'.$this->router->class.'/index', $param), $total),
			'param' => $param,
		));
		$this->template->display(is_file($tpl) ? basename($tpl) : 'mform_listc.html');
    }
    
	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->mform_model->get($id, $this->fid);
		$error = array();
		$result = '';
		!$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        // 无权限操作
		$this->ids && !in_array($data['cid'], $this->ids) && $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', $data['id']));
        
		if (IS_POST) {
			// 设置uid便于校验处理
			$_POST['data']['id'] = $id;
			$_POST['data']['uid'] = $data['uid'];
			$_POST['data']['author'] = $data['author'];
			$post = $this->validate_filter($this->field, $data);
			if (isset($data['error'])) {
				$error = $data;
				$data = $this->input->post('data', TRUE);
			} else {
				$post[1]['uid'] = $post[0]['uid'] = $data['uid'];
				$post[1]['author'] = $data['author'];
				$table = $this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_form_'.$this->fid);
				$this->db->where('id', $id)->update($table, $post[1]);
                if ($post[0]) {
                    $this->db->where('id', (int)$id)->update($table.'_data_'.intval($data['tableid']), $post[0]);
                } else {
                    $post[0]['id'] = (int)$id;
                    $this->db->replace($table.'_data_'.intval($data['tableid']), $post[0]);
                }
				// 操作成功处理附件
				$this->attachment_handle($data['uid'], $table.'-'.$id, $this->field, $post);
                $this->system_log('修改站点【#'.SITE_ID.'】模块【'.APP_DIR.'】表单【'.$this->fid.'】内容【#'.$id.'】'); // 记录日志
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/index', array('cid' => $this->cid)), 1);
			}
		}
		
		$tpl = APPPATH.'templates/admin/mform_editc_'.$this->fid.'.html';
		$this->template->assign(array(
			'tpl' => str_replace(FCPATH, '/', $tpl),
			'data' => $data,
			'error' => $error,
			'result' => $result,
			'myfield' => $this->field_input($this->field, $data, TRUE)
		));
		$this->template->display(is_file($tpl) ? basename($tpl) : 'mform_editc.html');
    }

    /**
     * 发布
     */
    public function add() {

        $data = $error = array();
        $result = '';

        if (IS_POST) {
            // 设置uid便于校验处理
            $post = $this->validate_filter($this->field, $data);
            if (isset($post['error'])) {
                $error = $post;
                $data = $this->input->post('data', TRUE);
            } else {
                $post[1]['cid'] = $post[0]['cid'] = $this->cid;
                $post[1]['uid'] = $post[0]['uid'] = $this->uid;
                $post[1]['url'] = $this->cdata['url'];
                $post[1]['title'] = $this->cdata['title'];
                $post[1]['inputip'] = $this->input->ip_address();
                $post[1]['inputtime'] = SYS_TIME;
                $table = SITE_ID.'_'.APP_DIR.'_form_'.$this->fid;
                if ($id = $this->_add($table, $post)) {
                    // 操作成功处理附件
                    $this->attachment_handle($this->uid, $this->db->dbprefix($this->table).'-'.$id, $this->form['field']);
                    $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/index', array('cid' => $this->cid)), 1);
                } else {
                    $this->admin_msg(fc_lang('操作失败'));
                }

            }
        }

        $tpl = APPPATH.'templates/admin/mform_editc_'.$this->fid.'.html';
        $this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
            'data' => $data,
            'error' => $error,
            'result' => $result,
            'myfield' => $this->field_input($this->field, $data, TRUE)
        ));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'mform_editc.html');
    }

	/**
     * 查看页面
     */
    public function show() {

		$id = (int)$this->input->get('id');
		$data = $this->mform_model->get($id, $this->fid);
		!$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        // 无权限操作
		$this->ids && !in_array($data['cid'], $this->ids) && $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', $data['id']));
        
		$tpl = APPPATH.'templates/admin/mform_show_'.$this->fid.'.html';
		$this->template->assign(array(
			'tpl' => str_replace(FCPATH, '/', $tpl),
			'data' => $data,
		));
		$this->template->display(is_file($tpl) ? basename($tpl) : 'mform_show.html');
    }


    // 添加入库
    protected function _add($table, $data) {
        // 入库
        $data[1]['tableid'] = 0;
        $table = $this->db->dbprefix($table);
        $this->db->insert($table, $data[1]);
        //
        if ($id = $this->db->insert_id()) {
            // 无限分表
            $tableid = floor($id / 50000);
            $this->db->where('id', $id)->update($table, array('tableid' => $tableid));
            if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$tableid."'")->row_array()) {
                // 附表不存在时创建附表
                $sql = $this->db->query("SHOW CREATE TABLE `".$table."_data_0`")->row_array();
                $this->db->query(str_replace(
                    array($sql['Table'], 'CREATE TABLE '),
                    array($table.'_data_'.$tableid, 'CREATE TABLE IF NOT EXISTS '),
                    $sql['Create Table']
                ));
            }
            $data[0]['id'] = $id;
            $this->db->replace($table.'_data_'.$tableid, $data[0]);
            // 更新模块表的统计值
            $this->db->where('id', $this->cid)->set($this->fid.'_total', $this->fid.'_total + 1', FALSE)->update(SITE_ID.'_'.APP_DIR);
        }

        return $id;
    }
    
}