<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* v3.1.0  */
class D_Member_Home extends M_Controller {

    /**
     * 管理
     */
    public function index() {

        if (IS_POST) {
            // 判断id是否为空
            $ids = $this->input->post('ids', TRUE);
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));

            if ($this->input->post('action') == 'update') {
                // 虚拟币检查
                $this->member_rule['update_score'] + $this->member['score'] < 0 && exit(dr_json(0, dr_lang('抱歉，'.SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE, abs($this->member_rule['update_score']), $this->member['score'])));
                // 积分检查
                $this->member_model->update_score(0, $this->uid, (int) $this->member_rule['update_experience'], '', "更新文档时间");
                // 虚拟币
                $this->member_model->update_score(1, $this->uid, (int) $this->member_rule['update_score'], '', "更新文档时间");
                // 更新文档时间
                $this->content_model->updatetime($ids);
                exit(dr_json(1, fc_lang('更新成功')));
            } else {
                $i = (int)$this->input->post('flag');
                !isset($this->flag[$i]) && exit(dr_json(0, fc_lang('推荐位不存在或者您无权操作')));
                $count = count($ids);
                $value = abs($this->flag[$i][$this->markrule] * $count);
                // 虚拟币检查
                $this->member['score'] - $value < 0 && exit(dr_json(0, fc_lang(SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE, $value, $this->member['score'])));
                $total = $this->content_model->flag($ids, $i);
                if ($total) {
                    // 虚拟币
                    $value = abs($this->flag[$i][$this->markrule] * $total);
                    $this->member_model->update_score(1, $this->uid, -$value, '', "推荐消费x" . $total);
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                }
                exit(dr_json(0, fc_lang('推荐失败')));
            }
        }

        $kw = dr_safe_replace($this->input->get('kw', TRUE));
        $catid = (int)$this->input->get('catid');
        $total = (int)$this->input->get('total');
        $order = dr_get_order_string(dr_safe_replace($this->input->get('order', TRUE)), 'updatetime desc');

        // 查询结果
        $list = array();
        if (!$total) {
            $this->db->select('count(*) as total');
            $this->db->where('uid', $this->uid)->where_in('catid', $this->catid)->where('status>=9')->where('link_id<1');
            // 搜索关键字
            $kw && $this->db->like('title', $kw);
            // 搜索栏目
            $catid && $this->db->where_in('catid', explode(',', $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid, 'childids')));
            $data = $this->db->get($this->content_model->prefix)->row_array();
            $total = (int)$data['total'];
        }

        if ($total) {
            $page = max((int)$this->input->get('page'), 1);
            $this->db->where('uid', $this->uid)->where_in('catid', $this->catid)->where('status>=9')->where('link_id<1');
            // 搜索关键字
            $kw && $this->db->like('title', $kw);
            // 搜索栏目
            $catid && $this->db->where_in('catid', explode(',', $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid, 'childids')));
            $this->db->order_by($order);
            $list = $this->db->limit($this->pagesize, $this->pagesize * ($page - 1))->get($this->content_model->prefix)->result_array();
        }

        // 模块表单嵌入
        $form = array();
        $data = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'form');
        if ($data) {
            foreach ($data as $t) {
                !$t['permission'][$this->markrule]['disabled'] && $form[] = array(
                    'url' => dr_member_url(APP_DIR.'/form_'.$t['table'].'/listc'),
                    'name' => $t['name'],
                    'field' => $t['table'].'_total',
                );
            }
        }

        $url = dr_member_url(APP_DIR.'/home/index').'&action=search&catid='.$catid.'&order='.$order.'&kw='.$kw;
        $this->template->assign(array(
            'kw' => $kw,
            'form' => $form,
            'list' => $list,
            'order' => $order,
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'extend' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend'),
            'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), $catid, 'class="form-control" id=\'dr_catid\' name=\'catid\'', ' -- ', 1, 1),
            'moreurl' => $url,
            'html_url' => 'index.php?'.(defined('IS_SHARE') && IS_SHARE ? '' : 's='.APP_DIR.'&'),
            'flagdata' => $this->flag,
        ));
        $this->template->display('module_index.html');
    }

    /**
     * 推荐位
     */
    public function flag() {

        $id = (int)$this->input->get('id');
        $flag = $this->flag;
        if ($flag && !$id) {
            foreach ($flag as $i => $t) {
                isset($t[$this->member['mark']]) && $t[$this->member['mark']] && $t['name'] && !$id && $id = $i;
            }
        }

        // 判断权限
        if (!isset($flag[$id])) {
            $this->template->assign(array(
                'id' => $id,
                'flag_error' => fc_lang('推荐位不存在或者您无权操作'),
            ));
            $this->template->display('module_flag.html');
            exit;
        }

       // $name = $flag[$id]['name'];
       // $score = $flag[$id][$this->member['mark']];

        if (IS_POST) {
            // 判断id是否为空
            $ids = $this->input->post('ids', TRUE);
            !$ids && exit(dr_json(0, fc_lang('你还没有选择呢')));
            if ($this->input->post('action') == 'update') {
                // 虚拟币检查
                $this->member_rule['update_score'] + $this->member['score'] < 0 && exit(dr_json(0, dr_lang(SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE, abs($this->member_rule['update_score']), $this->member['score'])));
                // 积分检查
                $this->member_model->update_score(0, $this->uid, (int) $this->member_rule['update_experience'], '', fc_lang('更新内容'));
                // 虚拟币
                $this->member_model->update_score(1, $this->uid, (int) $this->member_rule['update_score'], '', fc_lang('更新内容'));
                // 更新文档时间
                $this->content_model->updatetime($ids);
                exit(dr_json(1, fc_lang('更新成功')));
            } else {
                $this->db
                     ->where('flag', $id)
                     ->where_in('id', $ids)
                     ->where('uid', $this->uid)
                     ->delete($this->content_model->prefix.'_flag');
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                exit;
            }
        }

        $data = $this->db
                     ->from($this->content_model->prefix.'_flag')
                     ->join($this->content_model->prefix, $this->content_model->prefix.'.id='.$this->content_model->prefix.'_flag.id', 'left')
                     ->where($this->content_model->prefix.'_flag.uid', $this->uid)
                     ->where($this->content_model->prefix.'_flag.flag', $id)
                     ->get()
                     ->result_array();

        $this->template->assign(array(
            'id' => $id,
            'flag' => $flag,
            'list' => $data,
        ));
        $this->template->display('module_flag.html');
    }

    /**
     * 发布
     */
    public function add() {

        $did = (int)$this->input->get('did');
        $catid = (int)$this->input->get('catid');

        $module = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR);
        !$this->_module_post_catid($module) && $this->member_msg(fc_lang('无权限发布'));

        // 可用字段
        $field = $this->_get_member_field($catid);
		if ($this->module_rule[$catid]['verify']) {
			// 审核时不需要状态值
			unset($field['status']);
		}
        // 初始化参数
        $error = $data = array();

        // 提交操作
        if (IS_POST) {
            // 栏目参数
            $catid = (int)$this->input->post('catid');
            $catid = $catid ? $catid : (int)$this->input->get('catid');
            // 发布权限判断
            !$this->module_rule[$catid]['add'] && $this->member_msg(fc_lang('无权限发布'));
            // 日投稿上限检查
            if ($this->uid && $this->module_rule[$catid]['postnum']) {
                $total = $this->db->where('uid', $this->uid)->where('DATEDIFF(from_unixtime(inputtime),now())=0')->where('catid', $catid)->count_all_results($this->content_model->prefix.'_index');
                $total >= $this->module_rule[$catid]['postnum'] && $this->member_msg(fc_lang('每日发布数量不得超过%s个', $this->module_rule[$catid]['postnum']));
            }
            // 投稿总数检查
            if ($this->uid && $this->module_rule[$catid]['postcount']) {
                $total = $this->db->where('uid', $this->uid)->where('catid', $catid)->count_all_results($this->content_model->prefix.'_index');
                $total >= $this->module_rule[$catid]['postcount'] && $this->member_msg(fc_lang('发布总数不得超过%s个', $this->module_rule[$catid]['postcount']));
            }
            // 虚拟币检查
            $this->uid && $this->module_rule[$catid]['score'] + $this->member['score'] < 0 && $this->member_msg(fc_lang(SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE, abs($this->module_rule[$catid]['score']), $this->member['score']));
            // 字段验证与过滤
            $cat = $module['category'][$catid];
            $field = $this->_get_member_field($catid);
			if ($this->module_rule[$catid]['verify']) {
				// 审核时不需要状态值
				unset($field['status']);
			}
            // 设置uid便于校验处理
            $_POST['data']['id'] = 0;
            $_POST['data']['uid'] = $this->uid;
            $_POST['data']['author'] = $this->member['username'];
            $_POST['data']['inputtime'] = $_POST['data']['updatetime'] = SYS_TIME;
            $data = $this->validate_filter($field);

            // 验证出错信息
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data', TRUE);
            } elseif (!$catid) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('还没有选择栏目'));
            } elseif ($cat['child'] && !$cat['pcatpost']) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('该栏目不允许发布'));
            } else {
                // 设定文档默认值
                $data[1]['uid'] = $this->uid;
                $data[1]['hits'] = 0;
                $data[1]['catid'] = $catid;
                $data[1]['status'] = isset($field['status']) && isset($data[1]['status']) ? (int)$data[1]['status'] : 1;
                $data[1]['inputip'] = $this->input->ip_address();
                $data[1]['author'] = $this->member['username'] ? $this->member['username'] : 'guest';
                $data[1]['inputtime'] = $data[1]['updatetime'] = SYS_TIME;
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $this->clear_cache('save_'.APP_DIR.'_'.$this->uid);
                    $id = $this->content_model->save_draft($did, $data, 0);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $field);
                    (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, fc_lang('已保存到我的草稿箱中'), dr_member_url(APP_DIR.'/home/draft/'), $id));
                    $this->member_msg(fc_lang('已保存到我的草稿箱中'), dr_member_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                // 数据来至草稿时更新时间
                $did && $data[1]['updatetime'] = $data[1]['inputtime'] = SYS_TIME;
                // 发布文档
                if (($id = $this->content_model->add($data)) != FALSE) {
					// 执行提交后的脚本
					$this->validate_table($id, $field, $data);
                    // 发布草稿时删除草稿数据
                    $did && $this->content_model->delete_draft($did, 'cid=0 and eid=0') 
                        ? $this->attachment_replace_draft($did, $id, 0, $this->content_model->prefix, $data[1]['status'])
                        : $this->clear_cache('save_'.APP_DIR.'_'.$this->uid);
                    // 发布文档后执行
                    $this->_post($id, $data);
                    if ($data[1]['status'] >= 9) { // 审核通过
                        $mark = $this->content_model->prefix.'-'.$id;
                        // 积分处理
                        $experience = (int) $this->module_rule[$catid]['experience'];
                        $experience && $this->member_model->update_score(0, $this->uid, $experience, $mark, "发布文档", 1);
                        // 虚拟币处理
                        $score = (int) $this->module_rule[$catid]['score'];
                        $score && $this->member_model->update_score(1, $this->uid, $score, $mark, "发布文档", 1);
                        // 附件归档到文档
                        $this->attachment_handle($this->uid, $mark, $field);
                        $this->attachment_replace($this->uid, $id, $this->content_model->prefix);
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，马上返回列表'), dr_member_url(APP_DIR.'/home/index'), $id));
                        $this->template->assign(array(
                            'url' => SITE_URL.'index.php?s='.APP_DIR.'&c=show&id='.$id,
                            'add' => dr_member_url(APP_DIR.'/home/add', array('catid' => $catid)),
                            'edit' => 0,
                            'html' => $cat['setting']['html'] && $data[1]['status'] == 9 ? dr_module_create_show_file($id).dr_module_create_list_file($catid) : '',
                            'list' => $this->member['uid'] ? dr_member_url(APP_DIR . '/home/index') : SITE_URL.'index.php?s='.APP_DIR.'&c=category&id='.$catid,
                            'catid' => $catid,
                            'meta_name' => fc_lang('发布成功')
                        ));
                        $this->template->display('module_success_msg.html');
                    } else {
                        $this->member_model->admin_notice('content', fc_lang('%s 新内容审核', $module['name']), APP_DIR.'/admin/home/verifyedit/id/'.$id);
                        $this->attachment_handle($this->uid, $this->content_model->prefix.'_verify-'.$id, $field);
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，请等待管理员审核'), dr_member_url(APP_DIR.'/verify/index'), $id));
                        $this->template->assign(array(
                            'url' => dr_member_url(APP_DIR.'/verify/index'),
                            'add' => dr_member_url(APP_DIR.'/home/add', array('catid' => $catid)),
                            'edit' => 0,
                            'list' => $this->member['uid'] ? dr_member_url(APP_DIR.'/home/index') : SITE_URL.'index.php?s='.APP_DIR.'&c=category&id='.$catid,
                            'catid' => $catid,
                            'meta_name' => fc_lang('发布成功')
                        ));
                        $this->template->display('module_verify_msg.html');
                    }
                    exit;
                }
            }
            (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
            unset($data['id']);
        }
		
		if ($did) {
			$temp = $this->content_model->get_draft($did);
            $temp['draft']['cid'] == 0 && $temp['draft']['eid'] == 0 && $data = $temp;
		} else {
			$data = $this->get_cache_data('save_'.APP_DIR.'_'.$this->uid);
		}
		$catid = $data['catid'] ? $data['catid'] : $catid;
		// 栏目id不存在时就去第一个可用栏目为catid
		if (!$catid) {
			list($select, $catid) = $this->select_category($module['category'], 0, 'class="form-control" id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1, 1);
		} else {
			$select = $this->select_category($module['category'], $catid, 'class="form-control" id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1);
		}
		$field = $this->_get_member_field($catid);
		if ($this->module_rule[$catid]['verify']) {
			// 审核时不需要状态值
			unset($field['status']);
		}
        
		define('MODULE_CATID', $catid);
        // 接收参数中传递的标题
        isset($_GET['title']) && $_GET['title'] && $data['title'] = dr_safe_replace($_GET['title']);

        $backurl = str_replace(MEMBER_URL, '', $_SERVER['HTTP_REFERER']);
        $this->template->assign(array(
            'did' => $did,
            'purl' => dr_member_url(APP_DIR.'/home/add', array('catid' => $catid)),
            'catid' => $catid,
            'error' => $error,
            'verify' => 0,
            'select' => $select,
            'myfield' => $this->new_field_input($field, $data, TRUE),
            'listurl' => $backurl ? $backurl : dr_member_url(APP_DIR.'/home/index'),
            'isselect' => '',
            'meta_name' => fc_lang('发布'),
            'draft_url' => dr_member_url(APP_DIR.'/home/add', array('catid' => $catid)),
            'draft_list' => $this->content_model->get_draft_list('cid=0 and eid=0'),
            'result_error' => $error,
            'category_field_url' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category_field') ? dr_member_url(APP_DIR.'/home/add') : ''
        ));
        $this->template->display('module_add.html');
    }

    /**
     * 修改
     */
    public function edit() {

        // 初始化参数
        $id = (int)$_GET['id'];
        $did = (int)$this->input->get('did');
        $cid = (int)$this->input->get('catid');

        $data = $this->content_model->get($id);
        $error = array();
        $catid = $cid ? $cid : $data['catid'];

        // 数据是否存在
        !$data && $this->member_msg(fc_lang('对不起，数据被删除或者查询不存在').'('.$id.')');

        // 禁止修改他人文档
        $data['author'] != $this->member['username'] && $data['uid'] != $this->member['uid'] && $this->member_msg(fc_lang('无权限操作'));
        
        // 修改权限判断
        !$this->module_rule[$catid]['edit'] && $this->member_msg(fc_lang('无权限修改'));
        
        // 可用字段
        $field = $this->_get_member_field($catid);
		if (!$this->module_rule[$catid]['edit_verify']) {
			// 审核时不需要状态值
			unset($field['status']);
		}
        
        $isedit = (int)$this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid, 'setting', 'edit');

        // 保存修数据
        if (IS_POST) {
            $_data = $data;
            // 字段验证与过滤
            $catid = $isedit ? $catid : (int)$this->input->post('catid');
            // 修改权限判断
            !$this->module_rule[$catid]['edit'] && $this->member_msg(fc_lang('无权限修改'));
            $field = $this->_get_member_field($catid);
			if (!$this->module_rule[$catid]['edit_verify']) {
				// 审核时不需要状态值
				unset($field['status']);
			}
            // 设置uid便于校验处理
            $_POST['data']['id'] = $id;
            $_POST['data']['uid'] = $this->uid;
            $_POST['data']['author'] = $this->member['username'];
            $_POST['data']['inputtime'] = $data['inputtime'];
            $_POST['data']['updatetime'] = SYS_TIME;
            $data = $this->validate_filter($field, $_data);
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data', TRUE);
            } elseif (!$isedit && !$catid) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('还没有选择栏目'));
            } else {
                // 初始化数据
                $data[1]['uid'] = $this->uid;
                $data[1]['author'] = $this->member['username'];
                $data[1]['catid'] = $catid;
                $data[1]['status'] = isset($field['status']) && isset($data[1]['status']) ? (int)$data[1]['status'] : 1;
                $data[1]['updatetime'] = SYS_TIME;
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $data[1]['id'] = $data[0]['id'] = $id;
                    $id = $this->content_model->save_draft($did, $data, 0);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $field);
                    (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, fc_lang('已保存到我的草稿箱中'), dr_member_url(APP_DIR.'/home/draft/'), $id));
                    $this->admin_msg(fc_lang('已保存到我的草稿箱中'), dr_member_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                // 修改数据
                if ($this->content_model->edit($_data, $data)) {
					// 执行提交后的脚本
					$this->validate_table($id, $field, $data);
                    // 发布草稿时删除草稿数据
                    $did && $this->content_model->delete_draft($did, 'cid='.$id.' and eid=0') && $this->attachment_replace_draft($did, $id, 0, $this->content_model->prefix, $data[1]['status']);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'-'.$id, $field, $_data, $data[1]['status'] == 9 ? TRUE : FALSE);
                    if ($data[1]['status'] >= 9) { // 审核通过
						// 删除生成的文件
						if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid, 'setting', 'html')
                            && $data[1]['status'] == 10) {
							$html = $this->db->where('rid', $id)->where('type', 1)->get($this->content_model->prefix.'_html')->row_array();
							if ($html) {
								$files = dr_string2array($html['filepath']);
								if ($files) {
									foreach ($files as $file) {
										@unlink($file);
									}
								}
							}
						}
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，马上返回列表'), dr_member_url(APP_DIR.'/home/index'), $id));
                        $this->template->assign(array(
                            'url' => SITE_URL.'index.php?s='.APP_DIR.'&c=show&id='.$id,
                            'add' => dr_member_url(APP_DIR.'/home/add', array('catid' => $catid)),
                            'edit' => 1,
                            'list' => dr_member_url(APP_DIR.'/home/index'),
                            'html' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid, 'setting', 'html') && $data[1]['status'] == 9 ? dr_module_create_show_file($id).dr_module_create_list_file($catid) : '',
                            'catid' => $catid,
                            'meta_name' => fc_lang('修改成功')
                        ));
                        $this->template->display('module_success_msg.html');
                    } else {
                        $this->member_model->admin_notice('content', fc_lang('%s 修改内容审核', $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'name')), APP_DIR.'/admin/home/verifyedit/id/'.$id);
                        (IS_AJAX || IS_API_AUTH) && exit(dr_json(1, fc_lang('发布成功，请等待管理员审核'), dr_member_url(APP_DIR.'/verify/index'), $id));
                        $this->template->assign(array(
                            'url' => dr_member_url(APP_DIR.'/verify/index'),
                            'add' => dr_member_url(APP_DIR.'/home/add', array('catid' => $catid)),
                            'edit' => 1,
                            'list' => dr_member_url(APP_DIR.'/home/index'),
                            'catid' => $catid,
                            'meta_name' => fc_lang('修改成功')
                        ));
                        $this->template->display('module_verify_msg.html');
                    }
                } else {
                    $this->member_msg(fc_lang('修改失败'));
                }
                exit;
            }
            (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
        }
		
		if ($did) {
			$temp = $this->content_model->get_draft($did);
			if ($temp['draft']['cid'] == $data['id'] && $temp['draft']['eid'] == 0) {
				$temp['id'] = $id;
				$data = $temp;
				$catid = $temp['catid'] ? $temp['catid'] : $catid;
			}
		}
		define('MODULE_CATID', $catid);

        $backurl = str_replace(MEMBER_URL, '', $_SERVER['HTTP_REFERER']);
        $this->template->assign(array(
            'did' => $did,
            'purl' => dr_member_url(APP_DIR.'/home/add', array('id' => $id)),
            'data' => $data,
            'catid' => $catid,
            'error' => $error,
            'isedit' => $isedit,
            'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), $catid, 'class="form-control" id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1),
            'backurl' => $_SERVER['HTTP_REFERER'],
            'myfield' => $this->field_input($field, $data, TRUE),
            'listurl' => $backurl ? $backurl : dr_member_url(APP_DIR.'/home/index'),
            'meta_name' => fc_lang('修改'),
            'draft_url' => dr_member_url(APP_DIR.'/home/edit', array('id' => $id)),
            'draft_list' => $this->content_model->get_draft_list('cid='.$id.' and eid=0'),
            'result_error' => $error,
            'category_field_url' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category_field') ? dr_member_url(APP_DIR.'/home/edit', array('id' => $id, 'did' => $did)) : ''
        ));
        $this->template->display('module_add.html');
    }

    /**
     * 删除
     */
    public function del() {

        $id = (int) $this->input->post('id');
        $this->load->model('mform_model');
        $data = $this->db
                     ->where('id', $id)
                     ->where('uid', (int)$this->uid)
                     ->select('tableid,catid')
                     ->get($this->content_model->prefix)
                     ->row_array();
        // 删除权限判断
        (!$data || !$this->module_rule[$data['catid']]['del']) && exit(dr_json(0, fc_lang('无权限删除')));

        $this->content_model->delete_for_id($id, (int)$data['tableid']);

        exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
    }

    /**
     * 收藏的文档
     */
    public function favorite() {

        $table = $this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_favorite');

        if (IS_POST) {
            // 判断id是否为空
            $ids = $this->input->post('ids', TRUE);
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
            foreach ($ids as $id) {
                $id = intval($id);
                $row = $this->db->where('id', $id)->where('uid', $this->uid)->get($table)->row_array();
                if ($row) {
                    $this->db->where('id', $id)->delete($table);
                    // 更新收藏数量
                    if ($row['eid']) {
                        $c = $this->db->where('eid', (int)$row['eid'])->count_all_results($table);
                        $this->db->where('id', (int)$row['eid'])->set('favorites', $c)->update(SITE_ID.'_'.APP_DIR.'_extend');
                    } else {
                        $c = $this->db->where('cid', (int)$row['cid'])->count_all_results($table);
                        $this->db->where('id', (int)$row['cid'])->set('favorites', $c)->update(SITE_ID.'_'.APP_DIR);
                    }
                }
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

        $url = 'index.php?s=member&mod='.APP_DIR.'&c=home&m=favorite';
        $this->template->assign(array(
            'list' => $list,
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'moreurl' => $url
        ));
        $this->template->display('module_favorite_index.html');
    }

    /**
     * 购买的文档
     */
    public function buy() {

        $table = $this->db->dbprefix(SITE_ID.'_'.APP_DIR.'_buy');

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

        $url = 'index.php?s=member&mod='.APP_DIR.'&c=home&m=buy';
        $this->template->assign(array(
            'list' => $list,
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'moreurl' => $url
        ));
        $this->template->display('module_buy_index.html');
    }

    /**
     * 我的草稿箱
     */
    public function draft() {

        $table = $this->content_model->prefix.'_draft';

        if (IS_POST) {
            // 判断id是否为空
            $ids = $this->input->post('ids', TRUE);
            !$ids && exit(dr_json(0, fc_lang('对不起，数据被删除或者查询不存在')));
            $this->load->model('attachment_model');
            foreach ($ids as $id) {
                // 删除草稿记录
                if ($this->db->where('id', $id)->where('uid', $this->uid)->get($table)->row_array()) {
                    $this->db->where('id', $id)->delete($table);
                    // 删除表对应的附件
                    $this->attachment_model->delete_for_table($table.'-'.$id);
                }
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


        $url = 'index.php?s=member&mod='.APP_DIR.'&c=home&m=draft';
        $this->template->assign(array(
            'list' => $list,
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'moreurl' => $url
        ));
        $this->template->display('module_draft_index.html');
    }

    /**
     * Ajax调用栏目附加字段
     *
     * @return void
     */
    public function field() {
        $data = dr_string2array($this->input->post('data'));
        $field = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', (int)$this->input->post('catid'), 'field');
        !$field && exit('');
        echo $this->field_input($field, $data);
    }

    /**
     * 发布文档后执行的动作
     *
     * @return void
     */
    protected function _post($id, $data) {
        
    }
	
	// 文档状态设定
	public function status() {
		
		$id = (int)$this->input->get('id');
        $data = $this->content_model->get($id);
		if (!$data && $data['uid'] != $this->uid) {
			exit(dr_json(0, fc_lang('对不起，数据被删除或者查询不存在')));
		}
		
		// 删除缓存
        $this->clear_cache('show'.APP_DIR.SITE_ID.$id);
        $this->clear_cache('mshow'.APP_DIR.SITE_ID.$id);
		
		if ($data['status'] == 10) {
			$this->db->where('id', $id)->update($this->content_model->prefix, array('status' => 9));
			$this->db->where('id', $id)->update($this->content_model->prefix.'_index', array('status' => 9));
            // 调用方法状态更改方法
            $data['status'] = 9;
            $this->content_model->_update_status($data);
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $data['catid']));
		} else {
			// 删除生成的文件
			if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $data['catid'], 'setting', 'html') && strpos($data['url'], 'index.php') === FALSE) {
				$html = $this->db->where('rid', $id)->where('type', 1)->get($this->content_model->prefix.'_html')->row_array();
				if ($html) {
					$files = dr_string2array($html['filepath']);
					if ($files) {
						foreach ($files as $file) {
							@unlink($file);
						}
					}
				}
			}
			$this->db->where('id', $id)->update($this->content_model->prefix, array('status' => 10));
			$this->db->where('id', $id)->update($this->content_model->prefix.'_index', array('status' => 10));
            // 调用方法状态更改方法
            $data['status'] = 10;
            $this->content_model->_update_status($data);
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), 0));
		}
		
	}

}
