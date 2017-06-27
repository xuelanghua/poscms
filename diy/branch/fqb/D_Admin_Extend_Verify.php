<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class D_Admin_Extend_Verify extends M_Controller {

    public $field; // 自定义字段+含系统字段
    public $content;
    protected $verify; // 审核流程
	protected $table; // 审核表
	protected $sysfield; // 系统字段
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->load->library('Dfield', array(APP_DIR));
        $this->table = $this->content_model->prefix.'_extend_verify';
		$this->sysfield = array(
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
						'formattr' => '',
					)
				)
			)
		);
		$field = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend');
		$this->field = $field ? array_merge($field, $this->sysfield) : $this->sysfield;
        $this->admin['adminid'] > 1 && $this->verify = $this->_get_verify();
	}

    /**
     * 审核
     */
    public function index() {

        $this->admin['adminid'] > 1 && !$this->verify && $this->admin_msg(fc_lang('此角色组还没有分配审核流程'));

        if (IS_POST && $this->input->post('action') != 'search') {
            $ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));

            if ($this->admin['adminid'] > 1) {
                // 非管理员角色只能操作自己审核的
                $status = array();
                foreach ($this->verify as $t) {
                    $status+=$t['status'];
                }
                $where = '`status` IN (' . implode(',', $status) . ')';
            } else {
                $where = '';
            }

            switch ($this->input->post('action')) {
                case 'del': // 删除
                    $this->load->model('attachment_model');
                    foreach ($ids as $id) {
                        $data = $this->db // 主表状态
                                     ->where($where ? $where.' AND `id`='.(int)$id : '`id`='.(int)$id)
                                     ->select('uid,catid')
                                     ->limit(1)
                                     ->get($this->content_model->prefix . '_extend_index')
                                     ->row_array();
                        if ($data) {
                            // 删除数据
                            $this->content_model->del_extend_verify($id);
                            // 删除表对应的附件
                            $this->attachment_model->delete_for_table($this->table.'-' . $id);
                        }
                    }
                    $this->system_log('删除站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.@implode(',', $ids).'】'); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                    break;
                case 'flag': // 标记
                    $js = $error = array();
                    !$this->input->post('flagid') && exit(dr_json(0, fc_lang('您还没有选择呢')));
                    foreach ($ids as $id) {
                        $result = $this->_verify($id, NULL, $where ? $where.' AND `id`='.(int)$id : '`id`='.(int)$id);
                        if (is_array($result)) {
                            if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $result['catid'], 'setting', 'html')) {
                                $js[] = dr_module_create_show_file($result['id'], 1);
                                $js[] = dr_module_create_list_file($result['catid'], 1);
                            }
                        } elseif ($result) {
                            $error[] = str_replace('<br>', '', $result);
                        }
                    }
                    $this->system_log('处理站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.@implode(',', $ids).'】审核'); // 记录日志
                    $error ? exit(dr_json(1, $error, $js)) : exit(dr_json(2, fc_lang('操作成功，正在刷新...'), $js));
                    break;
                default:
                    exit(dr_json(0, fc_lang('未定义的操作')));
                    break;
            }
        }

        $_menu = $param = array();
        $meta_name = fc_lang('被退回');
        $param['status'] = (int) $this->input->get('status');

        if ($this->admin['adminid'] == 1) {
            // 管理员角色列出所有审核流程
            $goto = isset($_GET['status']) ? 1 : '';
            $where = '`status`='.$param['status'];
            for ($i = 0; $i < 9; $i++) {
                $total = (int)$this->db->where('status', $i)->count_all_results($this->table);
                $key_name = ($i ? fc_lang('第%s次审核', $i) : fc_lang('被退回'));
                $total && !$goto && $i > 0 && $goto = $param['status'] == $i ? 1 : $key_name;
                $_menu[$key_name] = array(
                    'url' => $this->duri->uri2url(APP_DIR.'/admin/verify/index'.(isset($_GET['status']) || $i ? '/status/'.$i : '')),
                    'count' => $total,
                );
                $param['status'] == $i && $meta_name = $key_name;
            }
			// 跳转到对应的状态
            if (strlen($goto) > 1 && isset($_menu[$goto])) {
                redirect(ADMIN_URL.$_menu[$goto]['url'], 'refresh');
                exit;
            }
        } else {
            // 非管理员角色列出自己审核的
            $status = array();
            foreach ($this->verify as $t) {
                $status+=$t['status'];
            }
            $where = $param['status'] ? '`status` IN ('.implode(',', $status).')' : '`status`=0 AND `backuid`='.$this->uid;
            // 被退回
            $_menu[fc_lang('被退回')] = array(
                'url' => $this->duri->uri2url(APP_DIR.'/admin/verify/index'),
                'count' => $this->db->where('`status`=0 AND `backuid`='.$this->uid)->count_all_results($this->table)
            );
            // 我的审核
            $_menu[fc_lang('我的审核')] = array(
                'url' => $this->duri->uri2url(APP_DIR.'/admin/verify/index/status/1'),
                'count' => $this->db->where_in('status', $status)->count_all_results($this->table)
            );
            $param['status'] == 1 && $meta_name = fc_lang('我的审核');
        }
        // 栏目筛选
        if ($this->input->get('cid')) {
            $param['cid'] = (int) $this->input->get('cid');
            $where.= ' AND `cid` = '.$param['cid'];
        }
        // 获取总数量
        $param['total'] = $total = $this->input->get('total') ? $this->input->get('total') : $this->db->where($where)->count_all_results($this->table);
        $page = max(1, (int) $this->input->get('page'));
        $data = $this->db
                     ->select('id,cid,author,content,inputtime,status')
                     ->where($where)
                     ->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1))
                     ->order_by('inputtime DESC, id DESC')
                     ->get($this->table)
                     ->result_array();

        $this->template->assign(array(
            'list' => $data,
            'menu' => $_menu,
            'param' => $param,
            'pages' => $this->get_pagination(dr_url(APP_DIR.'/verify/index', $param), $param['total']),
            'meta_name' => $meta_name,
        ));
        $this->template->display('content_extend_verify.html');
    }

    /**
     * 修改审核文档
     */
    public function edit() {

        $id = (int) $this->input->get('id');
        $data = $this->content_model->get_extend_verify($id);
        $error = array();
        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

        if (IS_POST) {

            $_data = $data;
            $this->content = $this->content_model->get($data['cid']);
            $_POST['data']['cid'] = $this->content['id'];
            $_POST['data']['uid'] = $this->content['uid'];
            $data = $this->validate_filter($this->field, $_data['content']);

            if (isset($data['error'])) {
                $error = $data;
                $data['content'] = $this->input->post('data', TRUE);
                $data['backinfo'] = $_data['backinfo'];
            } else {
                $data[1]['cid'] = $this->content['id'];
                $data[1]['uid'] = $this->content['uid'];
                $data[1]['catid'] = $this->content['catid'];
                $data[1]['status'] = $_data['status'];
                $data[1]['author'] = $this->content['author'];
                isset($data[1]['mytype']) && $data[1]['mytype'] = $_data['mytype'];
                $result = $this->_verify($id, $data, '`id`='.$id);
                $this->system_log('处理站点【#'.SITE_ID.'】模块【'.APP_DIR.'】扩展内容【#'.$id.'】审核'); // 记录日志
                if (is_array($result)) {
                    $this->admin_msg(
                        fc_lang('操作成功，正在刷新...').
                        ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $this->content['catid'], 'setting', 'html') ? dr_module_create_show_file($this->content['id']).dr_module_create_list_file($this->content['catid']) : ''),
                        $this->duri->uri2url($this->input->post('backurl')),
                        1
                    );
                } elseif ($result) {
                    $this->admin_msg($result);
                }
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), $this->input->post('backurl'), 1);
            }
        }

        if ($data['status'] == 0) { // 退回
            $backuri = APP_DIR.'/admin/verify/index/status/0';
        } elseif ($data['status'] > 0 && $data['status'] < 9) {
            $backuri = APP_DIR.'/admin/verify/index/status/'.$data['status'];
        } else {
            $backuri = APP_DIR.'/admin/verify/index/';
        }

        $data['content']['status'] = 9;
        $this->template->assign(array(
            'page' => max((int) $this->input->post('page'), 0),
            'data' => $data['content'],
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') => array($backuri, 'reply')
            )),
            'error' => $error,
            'backurl' => $backuri,
            'myfield' => $this->field_input($this->field, $data['content'], TRUE),
        ));
        $this->template->display('content_extend_edit.html');
    }


    // 审核内容
    public function _verify($id, $data, $_where) {

        // 获得审核数据
        $verify = $this->content_model->get_extend_verify($id);
        if (!$verify) {
            return;
        }

        // 通过审核
        if ($this->input->post('flagid') > 0) {
            // 查询当前的审核状态id
            $status = $this->_get_verify_status($verify['uid'], $verify['catid'], $verify['status']);
            // 权限验证
            if ($status == 9) {
                $member = $this->member_model->get_base_member($verify['uid']);
                $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $verify['catid']);
                // 标示
                $rule = $category['permission'][$member['markrule']];
                $mark = $this->content_model->prefix.'-'.$verify['cid'].'-'.$id;
                // 积分处理
                $rule['experience'] && $this->member_model->update_score(0, $verify['uid'], $rule['experience'], $mark, "发布文档", 1);
                // 虚拟币处理
                if ($rule['score']) {
                    if (!$this->db->where('type', 1)->where('mark', $mark)->count_all_results('member_scorelog')) {
                        if ($rule['score'] + $member['score'] < 0) {
                            // 数量不足提示
                            return fc_lang('【%s】审核失败！<br>会员（%s）%s不足，此次需要：%s', $verify['content']['name'],  $member['username'], SITE_SCORE, abs($rule['score']));
                        }
                        $this->member_model->update_score(1, $verify['uid'], $rule['score'], $mark, "发布文档", 1);
                    }
                }
            }
            // 筛选字段
            if (!$data) {
                $data = array();
                foreach ($this->field as $field) {
                    if ($field['fieldtype'] == 'Group' || $field['fieldtype'] == 'Merge') {
                        continue;
                    }
                    if ($field['fieldtype'] == 'Baidumap') {
                        $data[$field['ismain']][$field['fieldname'].'_lng'] = (double)$verify['content'][$field['fieldname'].'_lng'];
                        $data[$field['ismain']][$field['fieldname'].'_lat'] = (double)$verify['content'][$field['fieldname'].'_lat'];
                    } else {
                        $value = $verify['content'][$field['fieldname']];
                        if (strpos($field['setting']['option']['fieldtype'], 'INT') !== FALSE) {
                            $value = (int)$value;
                        } elseif ($field['setting']['option']['fieldtype'] == 'DECIMAL'
                            || $field['setting']['option']['fieldtype'] == 'FLOAT') {
                            $value = (double)$value;
                        }
                        $data[$field['ismain']][$field['fieldname']] = $value;
                    }
                }
                $data[1]['author'] = $verify['content']['author'];
                isset($data[1]['mytype']) && $data[1]['mytype'] = $verify['content']['mytype'];
            }
            $data[1]['id'] = $data[0]['id'] = $id;
            $data[1]['cid'] = $data[0]['cid'] = (int)$verify['cid'];
            $data[1]['uid'] = $data[0]['uid'] = (int)$verify['uid'];
            $data[1]['catid'] = $data[0]['catid'] = (int)$verify['catid'];
            $data[1]['updatetime'] = $verify['content']['updatetime'] ? $verify['content']['updatetime'] : $verify['content']['inputtime'];
            $data[1]['updatetime'] = $data[1]['updatetime'] ? $data[1]['updatetime'] : SYS_TIME;
            $data[1]['inputtime'] = $data[1]['inputtime'] ? $data[1]['inputtime'] : $data[1]['updatetime'];
            $data[1]['status'] = $status;
            // 保存内容
            $this->content_model->edit_extend($verify['content'], $data);
            // 审核通过
            if ($status == 9) {
                // 审核通过的挂钩点
                $this->hooks->call_hook('content_extend_verify', $data);
                // 操作成功处理附件
                $this->attachment_handle($data[1]['uid'], $this->content_model->prefix.'-'.$data[1]['cid'].'-'.$id, $this->field, $data);
                $this->attachment_replace($data[1]['uid'], $data[1]['cid'].'-'.$id, $this->content_model->prefix);
                $this->member_model->add_notice(
                    $data[1]['uid'],
                    3,
                    fc_lang('【%s】审核通过', $verify['content']['title'].$data[1]['name'])
                );
                $this->member_model->update_admin_notice(APP_DIR.'/admin/verify/edit/id/'.$id, 3);
                return array('id' => $id, 'catid' => $data[1]['catid']);
            }
        } else {
            // 拒绝审核
            // 更改主表状态
            $this->db->where($_where)->update($this->content_model->prefix.'_extend', array('status' => 0));
            // 更改索引表状态
            $this->db->where($_where)->update($this->content_model->prefix.'_extend_index', array('status' => 0));
            // 更改审核表状态
            $this->db->where($_where)->update($this->content_model->prefix.'_extend_verify', array(
                'status' => 0,
                'backuid' => (int)$this->uid,
                'backinfo' => dr_array2string(array(
                    'uid' => $this->uid,
                    'author' => $this->admin['username'],
                    'rolename' => $this->admin['role']['name'],
                    'optiontime' => SYS_TIME,
                    'backcontent' => $this->input->post('backcontent')
                ))
            ));
            $this->member_model->update_admin_notice(APP_DIR.'/admin/verify/edit/id/'.$id, 2);
            $this->member_model->add_notice(
                $verify['uid'],
                3,
                fc_lang('【%s】审核被拒绝，<a href="%s">查看原因</a>', $verify['content']['name'], SITE_URL.'index.php?s=member&mod='.APP_DIR.'&c=eback&m=edit&id='.$id)
            );
        }
    }

}