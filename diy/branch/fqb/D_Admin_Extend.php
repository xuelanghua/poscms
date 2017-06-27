<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class D_Admin_Extend extends M_Controller {

	public $catid; // 栏目参数id
	public $catrule; // 栏目权限规则
	public $content; // 内容数据
    public $field; // 自定义字段+含系统字段
	protected $sysfield; // 系统字段
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$cid = (int)$this->input->get('cid');
		$catid = (int)$this->input->get('catid');
		$this->content = $this->content_model->get($cid);
		// 判断管理组是否具有此栏目的管理权限
		$this->catrule = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR,'category',$this->content['catid'],'setting','admin',$this->admin['adminid']);
		if ($this->admin['adminid'] > 1 && $this->catrule && !$this->catrule['show']) {
			IS_AJAX && exit(dr_json(0, fc_lang('当前管理组角色您无权限操作此模块内容')));
			$this->admin_msg(fc_lang('当前管理组角色您无权限操作此模块内容'));
		} else {
			$this->catrule['show'] = $this->catrule['add'] = $this->catrule['edit'] = $this->catrule['del'] = 1;
		}
		$this->load->library('Dfield', array(APP_DIR));
		$this->sysfield = array(
            'hits' => array(
                'name' => fc_lang('阅读数'),
                'ismain' => 1,
                'fieldname' => 'hits',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'value' => 0,
                        'width' => 200,
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
            'updatetime' => array(
                'name' => fc_lang('更新时间'),
                'ismain' => 1,
                'fieldtype' => 'Date',
                'fieldname' => 'updatetime',
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
            'status' => array(
                'name' => fc_lang('状态'),
                'ismain' => 1,
                'fieldname' => 'status',
                'fieldtype' => 'Radio',
                'setting' => array(
                    'option' => array(
						'value' => 9,
                        'options' => fc_lang('正常').'|9'.chr(13).fc_lang('关闭').'|10'
                    ),
                    'validate' => array(
                        'tips' => fc_lang('关闭状态起内容暂存作用，除自己和管理员以外的人均无法访问'),
                    )
                )
            ),
		);
		$this->field = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend');
		// 筛选出右边显示的字段
		foreach ($this->field as $i => $t) {
			if ($t['setting']['is_right']) {
				$next[$i] = $t;
				$this->sysfield = array_merge($next, $this->sysfield);
				unset($this->field[$i]);
			}
		}

		$this->template->assign(array(
			'cid' => $cid,
			'catid' => $catid,
			'catrule' => $this->catrule,
			'content' => $this->content,
		));
        $this->catid = $catid;
	}

    /**
     * 管理
     */
    public function index() {
		
		if (IS_POST && !$this->input->post('search')) {
		
			$ids = $this->input->post('ids');
			!$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));

			switch($this->input->post('action')) {
		        // 删除
				case 'del':
					$this->catrule['del'] ? $this->content_model->delete_extend_for_ids($ids) : exit(dr_json(0, fc_lang('您无权限操作')));
					$this->system_log('删除站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.@implode(',', $ids).'】'); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
					break;
				// 排序
				case 'order':
					!$this->catrule['edit'] && exit(dr_json(0, fc_lang('您无权限操作')));
					$_data = $this->input->post('data');
					foreach ($ids as $id) {
						$this->db->where('id', $id)->update($this->content_model->prefix.'_extend', $_data[$id]);
					}
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.@implode(',', $ids).'】排序'); // 记录日志
					exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
					break;
				// 移动
				case 'move':
					$type = $this->input->post('type');
					!$type && exit(dr_json(0, fc_lang('您无权限操作')));
					!$this->catrule['edit'] && exit(dr_json(0, fc_lang('您无权限操作')));
					$this->content_model->extend_move($ids, $type);
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.@implode(',', $ids).'】修改'); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
					break;
				// 未定义
				default :
					exit(dr_json(0, fc_lang('操作成功，正在刷新...')));
					break;
			}
		}
		
		// 根据参数筛选结果
		$param = $this->input->get(NULL, TRUE);
		unset($param['s'], $param['c'], $param['m'], $param['d'], $param['page']);

        $this->field = $this->field ? array_merge($this->field, $this->sysfield) : $this->sysfield;
		// 数据库中分页查询
		list($list, $param)	= $this->content_model->extend_limit_page($this->content['id'], $param);
		
		// 搜索参数
		$param['cid'] = $this->content['id'];
		$param['catid'] = $this->content['catid'];
		// 存储当前页URL
		$this->_set_back_url(APP_DIR.'/extend/index', $param);
		if ($this->content) {
			$menu = array(
				fc_lang('章节管理') => array($this->_get_back_url(APP_DIR.'/extend/index'), 'table'),
				fc_lang('发布') => array(APP_DIR.'/admin/extend/add/cid/'.$this->content['id'].'/catid/'.$this->content['catid'], 'plus'),
				fc_lang('返回内容管理') => array($this->_get_back_url(APP_DIR.'/home/index'), 'reply')
			);
		} else {
			$menu = array(
				fc_lang('章节管理') => array($this->_get_back_url(APP_DIR.'/extend/index'), 'table'),
			);
		}
		$this->template->assign(array(
			'list' => $list,
			'menu' => $this->get_menu_v3($menu),
            'field' => $this->field,
			'param'	=> $param,
			'pages'	=> $this->get_pagination(dr_url(APP_DIR.'/extend/index', $param), $param['total']),
		));
		$this->template->display('content_extend_index.html');
    }
    
	/**
     * 添加
     */
    public function add() {

		!$this->catrule['add'] && $this->admin_msg(fc_lang('您无权限操作'));

        $did = (int)$this->input->get('did');
		$error = $data = array();
		
		if (IS_POST) {
			$_POST['data']['cid'] = $this->content['id'];
			$_POST['data']['uid'] = $this->content['uid'];
            $this->field = $this->field ? array_merge($this->field, $this->sysfield) : $this->sysfield;
			$data = $this->validate_filter($this->field);
			if (isset($data['error'])) {
				$error = $data;
				$data = $this->input->post('data');
			} else {
				$data[1]['cid'] = $this->content['id'];
				$data[1]['uid'] = $this->content['uid'];
                $data[1]['catid'] = $this->content['catid'];
                $data[1]['author'] = $this->content['author'];
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $this->clear_cache('save_'.APP_DIR.'_extend_'.$this->uid);
                    $id = $this->content_model->save_draft($did, $data, 1);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $this->field);
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.$id.'】保存草稿'); // 记录日志
                    $this->admin_msg(fc_lang('已保存到我的草稿箱中'), dr_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                // 数据来至草稿时更新时间
				$did && $data[1]['inputtime'] = $data[1]['updatetime'] = SYS_TIME;
				if ($id = $this->content_model->add_extend($data)) {
                    // 发布草稿时删除草稿数据
					$did && $this->content_model->delete_draft($did, 'cid='.$this->content['id'].' and eid=-1') 
						? $this->attachment_replace_draft($did, $this->content['id'], $id, $this->content_model->prefix)
                    	: $this->clear_cache('save_'.APP_DIR.'_extend_'.$this->uid);
					// mark
					$mark = $this->content_model->prefix.'-'.$this->content['id'].'-'.$id;
					$member = $this->member_model->get_base_member($this->content['uid']);
					$markrule = $member['markrule'];
					$category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $this->content['catid']);
					$rule = $category['permission'][$markrule];
					// 积分处理
					$rule['extend_experience'] + $member['experience'] >= 0 && $this->member_model->update_score(0, $this->content['uid'], $rule['extend_experience'], $mark, fc_lang('发布内容'), 1);
					// 虚拟币处理
					$rule['extend_score'] + $member['score'] >= 0 && $this->member_model->update_score(1, $this->content['uid'], $rule['extend_score'], $mark, fc_lang('发布内容'), 1);
					// 操作成功处理附件
					$this->attachment_handle($this->content['uid'], $mark, $this->field);
					$create = $category['setting']['html'] && $data[1]['status'] == 9 ? dr_module_create_show_file($this->content['id'], 1) : '';
                    $this->system_log('添加 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.$id.'】'); // 记录日志
					if ($this->input->post('action') == 'back') {
						$this->admin_msg(
                            fc_lang('操作成功，正在刷新...').
                            ($create ? "<script src='".$create."'></script>".dr_module_create_list_file($this->content['catid'])  : ''),
							$this->_get_back_url(APP_DIR.'/extend/index'),
                            1
                        );
					} else {
						unset($data);
						$error = array('error' => 'error');
					}
				} else {
					$error = array('error' => 'error');
				}
			}
		} else {
            if ($did) {
                $temp = $this->content_model->get_draft($did);
				$temp['draft']['cid'] == $this->content['id'] && $temp['draft']['eid'] == -1 && $data = $temp;
            } else {
                $data = $this->get_cache_data('save_'.APP_DIR.'_extend_'.$this->uid);
            }
        }

		if ($this->content) {
			$menu = array(
				fc_lang('章节管理') => array($this->_get_back_url(APP_DIR.'/extend/index'), 'table'),
				fc_lang('发布') => array(APP_DIR.'/admin/extend/add/cid/'.$this->content['id'].'/catid/'.$this->content['catid'], 'plus'),
				fc_lang('返回内容管理') => array($this->_get_back_url(APP_DIR.'/home/index'), 'reply')
			);
		} else {
			$menu = array(
				fc_lang('章节管理') => array($this->_get_back_url(APP_DIR.'/extend/index'), 'table'),
			);
		}

		$this->template->assign(array(
            'did' => $did,
			'data' => $data,
			'menu' => $this->get_menu_v3($menu),
			'error' => $error,
			'create' => $create,
            'myfield' => $this->field_input($this->field, $data, TRUE),
            'sysfield' => $this->new_field_input($this->sysfield, $data, TRUE, '', '<div class="form-group" id="dr_row_{name}"><label class="col-sm-12">{text}</label><div class="col-sm-12">{value}</div></div>'),
            'draft_url' => dr_url(APP_DIR.'/extend/add', array('cid' => $this->content['id'], 'catid' => $this->catid)),
            'draft_list' => $this->content_model->get_draft_list('cid='.$this->content['id'].' and eid=-1'),
		));
		$this->template->display('content_extend_add.html');
	}
	
	/**
     * 修改
     */
    public function edit() {

		!$this->catrule['edit'] && $this->admin_msg(fc_lang('您无权限操作'));
		
		$id = (int)$this->input->get('id');
        $did = (int)$this->input->get('did');

		$data = $this->content_model->get_extend($id);
		!$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

		$error = array();
		
		if (IS_POST) {
			$_data = $data;
			$_POST['data']['cid'] = $this->content['id'];
			$_POST['data']['uid'] = $this->content['uid'];
            $this->field = $this->field ? array_merge($this->field, $this->sysfield) : $this->sysfield;
			$data = $this->validate_filter($this->field, $_data);
			if (isset($data['error'])) {
				$error = $data;
				$data = $this->input->post('data', TRUE);
			} else {
				$data[1]['cid'] = $this->content['id'];
				$data[1]['uid'] = $this->content['uid'];
				$data[1]['catid'] = $this->content['catid'];
                $data[1]['author'] = $this->content['author'];
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $data[1]['id'] = $data[0]['id'] = $id;
                    $id = $this->content_model->save_draft($did, $data, 1);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $this->field);
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.$id.'】保存草稿'); // 记录日志
                    $this->admin_msg(fc_lang('已保存到我的草稿箱中'), dr_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                // 正常保存
				if ($id = $this->content_model->edit_extend($_data, $data)) {
                    // 发布草稿时删除草稿数据
					$did && $this->content_model->delete_draft($did, 'cid='.$this->content['id'].' and eid='.$id) && $this->attachment_replace_draft($did, $this->content['id'], $id, $this->content_model->prefix);
                    $mark = $this->content_model->prefix.'-'.$this->content['id'].'-'.$id;
					// 操作成功处理附件
					$this->attachment_handle($this->content['uid'], $mark, $this->field, $_data);
                    $this->system_log('修改 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.$id.'】'); // 记录日志
					// 删除生成的文件
					if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $this->content['catid'], 'setting', 'html')
						&& $data[1]['status'] == 10) {
						$html = $this->db->where('rid', $id)->where('type', 2)->get($this->content_model->prefix.'_html')->row_array();
						if ($html) {
							$files = dr_string2array($html['filepath']);
							if ($files) {
								foreach ($files as $file) {
									@unlink($file);
								}
							}
						}
					}
                    $this->admin_msg(
                        fc_lang('操作成功，正在刷新...').
                        ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $this->content['catid'], 'setting', 'html') && $data[1]['status'] == 9 ? dr_module_create_show_file($this->content['id']).dr_module_create_list_file($this->content['catid']) : ''),
						$this->_get_back_url(APP_DIR.'/extend/index'),
                        1
                    );
				} else {
					$error = array('error' => 'error');
				}
			}
		} else {
            if ($did) {
                $temp = $this->content_model->get_draft($did);
				$temp['draft']['cid'] == $this->content['id'] && $temp['draft']['eid'] == $id && $data = $temp;
            }
        }
        $data['updatetime'] = SYS_TIME;
		if ($this->content) {
			$menu = array(
				fc_lang('章节管理') => array($this->_get_back_url(APP_DIR.'/extend/index'), 'table'),
				fc_lang('发布') => array(APP_DIR.'/admin/extend/add/cid/'.$this->content['id'].'/catid/'.$this->content['catid'], 'plus'),
				fc_lang('返回内容管理') => array($this->_get_back_url(APP_DIR.'/home/index'), 'reply')
			);
		} else {
			$menu = array(
				fc_lang('章节管理') => array($this->_get_back_url(APP_DIR.'/extend/index'), 'table'),
			);
		}
		$this->template->assign(array(
            'did' => $did,
			'data' => $data,
			'menu' => $this->get_menu_v3($menu),
			'error' => $error,
            'myfield' => $this->field_input($this->field, $data, TRUE),
            'sysfield' => $this->new_field_input($this->sysfield, $data, TRUE, '', '<div class="form-group" id="dr_row_{name}"><label class="col-sm-12">{text}</label><div class="col-sm-12">{value}</div></div>'),
            'draft_url' => dr_url(APP_DIR.'/extend/edit', array('cid' => $this->content['id'], 'catid' => $this->catid, 'id' => $id)),
            'draft_list' => $this->content_model->get_draft_list('cid='.$this->content['id'].' and eid='.$id),
		));
		$this->template->display('content_extend_add.html');
    }
	
	// 文档状态设定
	public function status() {
		
		$id = (int)$this->input->get('id');
        $data = $this->content_model->get_extend($id);
		!$data && exit(dr_json(0, fc_lang('对不起，数据被删除或者查询不存在')));
		
		// 删除缓存
        $this->clear_cache('show'.APP_DIR.SITE_ID.$data['cid']);
        $this->clear_cache('mshow'.APP_DIR.SITE_ID.$data['cid']);
        $this->clear_cache('extend'.APP_DIR.SITE_ID.$id);
        $this->clear_cache('mextend'.APP_DIR.SITE_ID.$id);
		
		if ($data['status'] == 10) {
			$this->db->where('id', $id)->update($this->content_model->prefix.'_extend', array('status' => 9));
			$this->db->where('id', $id)->update($this->content_model->prefix.'_extend_index', array('status' => 9));
			// 调用方法状态更改方法
			$data['status'] = 9;
			$this->content_model->_update_status_extend($data);
            $this->system_log('修改 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$data['cid'].'】扩展【#'.$id.'】状态为【正常】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $data['catid']));
		} else {
			// 删除生成的文件
			if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $data['catid'], 'setting', 'html')
				&& strpos($data['url'], 'index.php') === FALSE) {
				$html = $this->db->where('rid', $id)->where('type', 2)->get($this->content_model->prefix.'_html')->row_array();
				if ($html) {
					$files = dr_string2array($html['filepath']);
					if ($files) {
						foreach ($files as $file) {
							@unlink($file);
						}
					}
				}
			}
			$this->db->where('id', $id)->update($this->content_model->prefix.'_extend', array('status' => 10));
			$this->db->where('id', $id)->update($this->content_model->prefix.'_extend_index', array('status' => 10));
			// 调用方法状态更改方法
			$data['status'] = 10;
			$this->content_model->_update_status_extend($data);
            $this->system_log('修改 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$data['cid'].'】扩展【#'.$id.'】状态为【关闭】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), 0));
		}
		
	}


}