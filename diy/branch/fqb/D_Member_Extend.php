<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* v3.1.0  */
class D_Member_Extend extends M_Controller {

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
        $this->content = $this->content_model->get($cid);
        $this->router->method != 'buy' && !$this->content && $this->member_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        // 判断具有此栏目的管理权限
        $this->catrule = $this->module_rule[$this->content['catid']];
        $this->content['type'] = dr_string2array($this->content['type']);
        $this->template->assign(array(
            'cid' => $cid,
            'catrule' => $this->catrule,
            'content' => $this->content,
        ));
        $this->load->library('Dfield', array(APP_DIR));
        $this->field = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend');
		$this->field['status'] = array(
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
		);
    }

    /**
     * 管理
     */
    public function index() {

        // 作者判断
        $this->content['uid'] != $this->uid && $this->member_msg(fc_lang('无权限操作'));

        if (IS_POST) {
            // 判断id是否为空
            $ids = $this->input->post('ids', TRUE);
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
            if ($this->input->post('action') == 'update') {
                $_data = $this->input->post('data');
                foreach ($ids as $id) {
                    $this->db->where('id', $id)->update(SITE_ID.'_'.APP_DIR.'_extend', $_data[$id]);
                }
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            }
        }

        $kw = dr_safe_replace($this->input->get('kw', TRUE));
        $total = (int)$this->input->get('total');
        $order = dr_get_order_string(dr_safe_replace($this->input->get('order', TRUE)), 'displayorder desc');

        // 查询结果
        $list = array();
        if (!$total) {
            $this->db->select('count(*) as total');
            $kw && $this->db->like('name', $kw);
            $this->db->where('uid', $this->uid)->where('cid', (int)$this->content['id'])->where('status>=9');
            $data = $this->db->get(SITE_ID.'_'.APP_DIR.'_extend')->row_array();
            $total = (int)$data['total'];
        }

        if ($total) {
            $page = max((int)$this->input->get('page'), 1);
            $kw && $this->db->like('name', $kw);
            $this->db->where('uid', $this->uid)->where('cid', (int)$this->content['id'])->where('status>=9');
            $this->db->order_by($order.',id desc');
            $list = $this->db->limit($this->pagesize, $this->pagesize * ($page - 1))->get(SITE_ID.'_'.APP_DIR.'_extend')->result_array();
        }

        $url = dr_member_url(APP_DIR.'/extend/index').'&cid='.$this->content['id']."&kw=$kw&order=$order";
        $this->template->assign(array(
            'kw' => $kw,
            'list' => $list,
            'order' => $order,
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'extend' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend'),
            'moreurl' => $url,
            'meta_name' => fc_lang('已通过的章节'),
        ));
        $this->template->display('module_extend_index.html');
    }

    /**
     * 添加
     */
    public function add() {

        // 作者判断
        $this->content['uid'] != $this->uid && $this->member_msg(fc_lang('无权限操作'));

        // 添加权限
        !$this->catrule['add'] && $this->member_msg(fc_lang('您无权限操作'));

        // 虚拟币检查
        $this->catrule['extend_score'] + $this->member['score'] < 0 && $this->member_msg(fc_lang(SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE, abs($this->catrule['extend_score']), $this->member['score']));

        $did = (int)$this->input->get('did');
        $error = $data = array();
        $result = '';
		
		// 需要审核时，不显示状态字段
		if (!$this->uid || $this->module_rule[$this->content['catid']]['verify']) {
			unset($this->field['status']);
		}

        // 保存操作
        if (IS_POST) {
            $_POST['data']['cid'] = $this->content['id'];
            $_POST['data']['uid'] = $this->member['uid'];
            $data = $this->validate_filter($this->field);
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data', TRUE);
                (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
            } else {
                $data[1]['cid'] = $this->content['id'];
                $data[1]['uid'] = $this->member['uid'];
                $data[1]['catid'] = $this->content['catid'];
                $data[1]['author'] = $this->member['username'];
                $data[1]['status'] = isset($this->field['status']) && isset($data[1]['status']) ? (int)$data[1]['status'] : 1;
                $data[1]['updatetime'] = $data[1]['inputtime'] = SYS_TIME;
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $this->clear_cache('save_'.APP_DIR.'_extend_'.$this->uid);
                    $id = $this->content_model->save_draft($did, $data, 1);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $this->field);
                    $this->member_msg(fc_lang('已保存到我的草稿箱中'), dr_member_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                if ($id = $this->content_model->add_extend($data)) {
                    // 发布草稿时删除草稿数据
                    $did && $this->content_model->delete_draft($did, 'cid='.$this->content['id'].' and eid=-1')
                        ? $this->attachment_replace_draft($did, $this->content['id'], $id, $this->content_model->prefix, $data[1]['status'])
                        : $this->clear_cache('save_'.APP_DIR.'_extend_'.$this->uid);
                    if ($data[1]['status'] >= 9) {
                        $mark = $this->content_model->prefix.'-'.$this->content['id'].'-'.$id;
                        $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $this->content['catid']);
                        // 积分处理
                        $this->catrule['extend_experience'] && $this->member_model->update_score(0, $this->content['uid'], $this->catrule['extend_experience'], $mark, fc_lang('发布内容'), 1);
                        // 虚拟币处理
                        $this->catrule['extend_score'] && $this->member_model->update_score(1, $this->content['uid'], $this->catrule['extend_score'], $mark, fc_lang('发布内容'), 1);
                        // 操作成功处理附件
                        $this->attachment_handle($this->content['uid'], $mark, $this->field);
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，马上返回列表'), dr_member_url(APP_DIR.'/extend/index', array('cid' => $this->content['id'])), $id));
                        $this->template->assign(array(
                            'url' => SITE_URL.'index.php?s='.APP_DIR.'&c=extend&id='.$id,
                            'add' => dr_member_url(APP_DIR.'/extend/add', array('cid' => $this->content['id'], 'type' => $data[1]['mytype'])),
                            'edit' => 0,
                            'html' => $category['setting']['html'] && $data[1]['status'] == 9 ? dr_module_create_show_file($this->content['id']).dr_module_create_list_file($this->content['catid']) : '',
                            'list' => dr_member_url(APP_DIR.'/extend/index', array('cid' => $this->content['id'])),
                            'meta_name' => fc_lang('发布成功')
                        ));
                        $this->template->display('module_success_msg.html');
                    } else {
                        $this->member_model->admin_notice('content', fc_lang('%s 修改内容扩展审核', $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'name')), APP_DIR.'/admin/extend/edit/id/'.$id);
                        $this->attachment_handle($this->uid, $this->content_model->prefix.'_verify-'.$this->content['id'].'-'.$id, $this->field);
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，请等待管理员审核'), dr_member_url(APP_DIR . '/everify/index'), $id));
                        $this->template->assign(array(
                            'url' => dr_member_url(APP_DIR.'/everify/index'),
                            'add' => dr_member_url(APP_DIR.'/extend/add', array('cid' => $this->content['id'], 'type' => $data[1]['mytype'])),
                            'edit' => 0,
                            'list' => dr_member_url(APP_DIR.'/extend/index', array('cid' => $this->content['id'])),
                            'meta_name' => fc_lang('发布成功')
                        ));
                        $this->template->display('module_verify_msg.html');
                    }
                    exit;
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

        $this->template->assign(array(
            'did' => $did,
            'data' => $data,
            'error' => $error,
            'result' => $result,
            'myfield' => $this->field_input($this->field, $data, TRUE),
            'draft_url' => dr_member_url(APP_DIR.'/extend/add', array('cid' => $this->content['id'], 'catid' => $this->catid)),
            'draft_list' => $this->content_model->get_draft_list('cid='.$this->content['id'].' and eid=-1'),
            'result_error' => $error,
        ));
        $this->template->display('module_extend_add.html');
    }

    /**
     * 修改
     */
    public function edit() {

        // 作者判断
        $this->content['uid'] != $this->uid && $this->member_msg(fc_lang('无权限操作'));

        // 修改权限
        !$this->catrule['edit'] && $this->member_msg(fc_lang('您无权限操作'));

		// 需要审核时，不显示状态字段
		if (!$this->uid || !$this->module_rule[$this->content['catid']]['edit_verify']) {
			unset($this->field['status']);
		}

        $id = (int) $this->input->get('id');
        $did = (int)$this->input->get('did');
        $data = $this->content_model->get_extend($id);
        !$data && $this->member_msg(fc_lang('对不起，数据被删除或者查询不存在'));

        $error = array();
        $result = '';

        if (IS_POST) {
            $_data = $data;
            $_POST['data']['cid'] = $this->content['id'];
            $_POST['data']['uid'] = $this->content['uid'];
            $data = $this->validate_filter($this->field, $_data);
            if (isset($data['error'])) {
                $error = $data;
                (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
                $data = $this->input->post('data', TRUE);
            } else {
                $data[1]['cid'] = $this->content['id'];
                $data[1]['uid'] = $_data['uid'];
                $data[1]['catid'] = $this->content['catid'];
                $data[1]['status'] = isset($this->field['status']) && isset($data[1]['status']) ? (int)$data[1]['status'] : 1;
                $data[1]['author'] = $_data['author'];
                $data[1]['updatetime'] = SYS_TIME;
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $data[1]['id'] = $data[0]['id'] = $id;
                    $id = $this->content_model->save_draft($did, $data, 1);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $this->field);
                    $this->member_msg(fc_lang('已保存到我的草稿箱中'), dr_member_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                // 正常保存
                if ($id = $this->content_model->edit_extend($_data, $data)) {
                    // 发布草稿时删除草稿数据
                    $did && $this->content_model->delete_draft($did, 'cid='.$this->content['id'].' and eid='.$id) && $this->attachment_replace_draft($did, $this->content['id'], $id, $this->content_model->prefix, $data[1]['status']);
                    if ($data[1]['status'] >= 9) {
                        $mark = $this->content_model->prefix.'-'.$this->content['id'].'-'.$id;
                        // 操作成功处理附件
                        $this->attachment_handle($this->content['uid'], $mark, $this->field, $_data);
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
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，马上返回列表'), dr_member_url(APP_DIR.'/extend/index', array('cid' => $this->content['id'])), $id));
                        $this->template->assign(array(
                            'url' => SITE_URL.'index.php?s='.APP_DIR.'&c=extend&id='.$id,
                            'add' => dr_member_url(APP_DIR.'/extend/add', array('cid' => $this->content['id'], 'type' => $data[1]['mytype'])),
                            'edit' => 1,
                            'html' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $this->content['catid'], 'setting', 'html') && $data[1]['status'] == 9 ? dr_module_create_show_file($this->content['id']).dr_module_create_list_file($this->content['catid']) : '',
                            'list' => dr_member_url(APP_DIR.'/extend/index', array('cid' => $this->content['id'])),
                            'meta_name' => fc_lang('发布成功')
                        ));
                        $this->template->display('module_success_msg.html');
                    } else {
                        $this->attachment_handle($this->uid, $this->content_model->prefix.'_verify-'.$this->content['id'].'-'.$id, $this->field);
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，请等待管理员审核'), dr_member_url(APP_DIR.'/everify/index'), $id));
                        $this->template->assign(array(
                            'url' => dr_member_url(APP_DIR.'/everify/index'),
                            'add' => dr_member_url(APP_DIR.'/extend/add', array('cid' => $this->content['id'], 'type' => $data[1]['mytype'])),
                            'edit' => 0,
                            'list' => dr_member_url(APP_DIR.'/extend/index', array('cid' => $this->content['id'])),
                            'meta_name' => fc_lang('发布成功')
                        ));
                        $this->template->display('module_verify_msg.html');
                    }
                    exit;
                } else {
                    $error = array('error' => $id);
                }
            }
        } else {
            if ($did) {
                $temp = $this->content_model->get_draft($did);
                $temp['draft']['cid'] == $this->content['id'] && $temp['draft']['eid'] == $id && $data = $temp;
            }
        }

        $this->template->assign(array(
            'did' => $did,
            'data' => $data,
            'error' => $error,
            'result' => $result,
            'myfield' => $this->field_input($this->field, $data, TRUE),
            'draft_url' => dr_member_url(APP_DIR.'/extend/edit', array('cid' => $this->content['id'], 'catid' => $this->catid, 'id' => $id)),
            'draft_list' => $this->content_model->get_draft_list('cid='.$this->content['id'].' and eid='.$id),
            'result_error' => $error,
        ));
        $this->template->display('module_extend_add.html');
    }

    /**
     * 删除
     */
    public function del() {

        // 作者判断
        $this->content['uid'] != $this->uid && $this->member_msg(fc_lang('无权限操作'));
        
        // 删除权限
        !$this->catrule['del'] && $this->member_msg(fc_lang('您无权限操作'));
        
        $id = (int) $this->input->post('id');
        if ($id) {
            $data = $this->db
                         ->select('tableid,id')
                         ->where('id', $id)
                         ->get($this->content_model->prefix.'_extend')
                         ->row_array();
            $data && $this->content_model->delete_extend_for_id($id, $this->content['id'], $data['tableid']);
        }
        exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
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
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $data['catid']));
		} else {
			// 删除生成的文件
			if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $data['catid'], 'setting', 'html')) {
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
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), 0));
		}
		
	}


    /**
     * 购买的文档
     */
    public function buy() {

        $table = $this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_extend_buy');

        if (IS_POST) {
            // 判断id是否为空
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            $ids = $this->input->post('ids', TRUE);
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
            foreach ($ids as $id) {
                $this->db->where('id', intval($id))->where('uid', $this->uid)->delete($table);
            }
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $total = (int)$this->input->get('total');

        // 查询结果
        $list = array();
        if (!$total) {
            $this->db->select('count(*) as total');
            $this->db->where('uid', $this->uid);
            $data = $this->db->get($table)->row_array();
            $total = (int)$data['total'];
        }

        if ($total) {
            $page = max((int)$this->input->get('page'), 1);
            $this->db->where('uid', $this->uid)->order_by('inputtime desc');
            $list = $this->db->limit($this->pagesize, $this->pagesize * ($page - 1))->get($table)->result_array();
        }

        $url = 'index.php?s=member&mod='.APP_DIR.'&c=extend&m=buy';
        $this->template->assign(array(
            'list' => $list,
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'moreurl' => $url
        ));
        $this->template->display('module_extend_buy_index.html');
    }


}
