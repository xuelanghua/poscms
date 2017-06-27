<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* v3.1.0  */
class D_Admin_Home extends M_Controller {

    public $field; // 自定义字段+含系统字段
    protected $verify; // 审核流程
    protected $sysfield; // 系统字段

    /**
     * 构造函数
     */

    public function __construct() {
        parent::__construct();
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
                        'value' => $this->admin['username']
                    ),
                    'validate' => array(
                        'tips' => fc_lang('填写录入者的会员名称'),
                        'check' => '_check_member',
                        'required' => 1,
                        'formattr' => ' ondblclick="dr_dialog_member(\'author\')" ',
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
                        'formattr' => ' ondblclick="dr_dialog_ip(\'inputip\')" ',
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
        $this->admin['adminid'] > 1 && $this->verify = $this->_get_verify();
    }

    // 获取可用字段
    public function _get_field($catid = 0) {

        // 主字段
        $field = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'field');

        // 指定栏目字段
        $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid, 'field');
        if ($category) {
            $tmp = $field;
            $field = array();
            if (isset($tmp['title'])) {
                $field['title'] = $tmp['title'];
                unset($tmp['title']);
                $field = array_merge($field, $category, $tmp);
            } else {
                $field = array_merge($category, $tmp);
            }
        }
        // 筛选出右边显示的字段
        foreach ($field as $i => $t) {
            if ($t['setting']['is_right']) {
                $next[$i] = $field[$i];
                $this->sysfield = array_merge($next, $this->sysfield);
                unset($field[$i]);
            }
        }

        return $field;
    }

    /**
     * 管理
     */
    public function index() {

        if (IS_POST && !$this->input->post('search')) {
            $ids = $this->input->post('ids');
            $action = $this->input->post('action');
            !$ids && ($action == 'html' ? $this->admin_msg(fc_lang('您还没有选择呢')) : exit(dr_json(0, fc_lang('您还没有选择呢'))));
            switch ($action) {
                case 'del':
                    $ok = $no = 0;
                    foreach ($ids as $id) {
                        $data = $this->db->where('id', (int)$id)->select('id,catid,tableid')->get($this->content_model->prefix)->row_array();
                        if ($data) {
                            if (!$this->is_category_auth($data['catid'], 'del')) {
                                $no++;
                            } else {
                                $ok++;
                                $this->content_model->delete_for_id((int)$data['id'], (int)$data['tableid']);
                            }
                        }
                    }
                    $this->system_log('删除站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】'); // 记录日志
                    exit(dr_json($no ? 0 : 1, $no ? fc_lang('管理员：%s', $ok, $no) : fc_lang('操作成功，正在刷新...')));
                    break;
                case 'order':
                    $_data = $this->input->post('data');
                    foreach ($ids as $id) {
                        $this->db->where('id', $id)->update($this->content_model->prefix, $_data[$id]);
                    }
                    $this->system_log('排序站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】'); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                    break;
                case 'move':
                    $catid = $this->input->post('catid');
                    if (!$catid) {
                        exit(dr_json(0, fc_lang('目标栏目id不存在')));
                    } elseif (!$this->is_auth(APP_DIR.'/admin/home/edit')
                        || !$this->is_category_auth($catid, 'edit')) {
                        exit(dr_json(0, fc_lang('您无权限操作')));
                    }
                    $this->content_model->move($ids, $catid);
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】更改栏目#'.$catid); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                    break;
                case 'flag':
                    !$this->is_auth(APP_DIR.'/admin/home/edit') &&  exit(dr_json(0, fc_lang('您无权限操作')));
                    $flag = $this->input->get('flag');
                    $this->content_model->flag($ids, -$flag);
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】移出推荐位#'.$flag); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                    break;
                case 'html':
                    $url = ADMIN_URL.'index.php?s='.APP_DIR.'&c=show&m=html&page=1&type=html&value='.implode(',', $ids).'&total='.count($ids);
                    redirect($url, 'refresh');
                    break;
                default :
                    exit(dr_json(0, fc_lang('操作成功，正在刷新...')));
                    break;
            }
        }

        // 重置页数和统计
        IS_POST && $_GET['page'] = $_GET['total'] = 0;

        // 筛选结果
        $param = $this->input->get(NULL, TRUE);
        $catid = isset($param['catid']) ? (int)$param['catid'] : 0;
        unset($param['s'], $param['c'], $param['m'], $param['d'], $param['page']);

        // 按字段的搜索
        $this->field = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'field');
        $this->field['author'] = array('name' => fc_lang('录入作者'), 'ismain' => 1, 'fieldname' => 'author');

        // 数据库中分页查询
        list($list, $param) = $this->content_model->limit_page($param, max((int)$_GET['page'], 1), (int)$_GET['total']);

        $meta_name = fc_lang('已通过的内容');

        // 统计推荐位
        $flag = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'flag');
        if ($flag) {
            foreach ($flag as $id => $t) {
                if ($t['name'] && $id) {
                    $flag[$id]['url'] =  $this->duri->uri2url($catid ?
                        APP_DIR.'/admin/home/index/flag/'.$id.'/catid/'.$catid :
                        APP_DIR.'/admin/home/index/flag/'.$id);
                    isset($param['flag']) && $param['flag'] && $param['flag'] == $id && $meta_name = fc_lang($t['name']);
                } else {
					unset($flag[$id]);
				}
            }
        }

        // 模块应用嵌入
        $app = array();
        $data = $this->get_cache('app');
        if ($data) {
            foreach ($data as $dir) {
                $a = $this->get_cache('app-'.$dir);
                if (isset($a['module'][APP_DIR]) && isset($a['related']) && $a['related']) {
                    $app[] = array(
                        'url' => dr_url($dir.'/content/index'),
                        'name' => $a['name'],
                        'field' => $a['related'],
                    );
                }
            }
        }

        // 模块表单嵌入
        $form = array();
        $data = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'form');
        if ($data) {
            foreach ($data as $t) {
                $form[] = array(
                    'url' => dr_url(APP_DIR.'/form_'.$t['table'].'/index'),
                    'name' => $t['name'],
                    'field' => $t['table'].'_total',
                );
            }
        }

        // 存储当前页URL
        $this->_set_back_url(APP_DIR.'/home/index', $param);

        $this->template->assign(array(
            'app' => $app,
            'form' => $form,
            'list' => $list,
            'flag' => isset($param['flag']) ? $param['flag'] : '',
            'flags' => $flag,
            'param' => $param,
            'meta_name' => $meta_name,
            'field' => $this->field,
            'pages' => $this->get_pagination(dr_url(APP_DIR.'/home/index', $param), $param['total']),
            'extend' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'extend'),
            'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), 0, 'id=\'move_id\' name=\'catid\'', ' --- ', 1, 1),
            'select2' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), $catid, ' name=\'data[catid]\'', ' --- ', 0, 1),
            'html_url' => 'index.php?s='.APP_DIR.'&',
            'post_url' => $this->duri->uri2url($catid ? APP_DIR.'/admin/home/add/catid/'.$catid : APP_DIR.'/admin/home/add'),
            'list_url' =>  $this->duri->uri2url($catid ? APP_DIR.'/admin/home/index/catid/'.$catid : APP_DIR.'/admin/home/index'),
            'list_data_tpl' => is_file(FCPATH.'module/'.APP_DIR.'/templates/admin/content_data.html') ? FCPATH.'module/'.APP_DIR.'/templates/admin/content_data.html' : FCPATH.'dayrui/templates/admin/content_data.html',
        ));
        $this->template->display('content_index.html');
    }

    /**
     * 添加
     */
    public function add() {

        $did = (int)$this->input->get('did');
        $catid = (int)$this->input->get('catid');

        $error = $data = array();
        $select = '';
        $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category');

        // 提交保存操作------
        if (IS_POST) {
            $cid = (int)$this->input->post('catid');
            $syncatid = $this->input->post('syncatid');
            // 判断栏目权限
            $cid != $catid && !$this->is_category_auth($catid, 'add') && $this->admin_msg(fc_lang('您无权限操作'));
            $catid = $cid;
            $cat = $cid != $catid ? $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid) : $category[$catid];
            unset($cid);
            // 设置uid便于校验处理
            $uid = $this->input->post('data[author]') ? get_member_id($this->input->post('data[author]')) : 0;
            $_POST['data']['id'] = 0;
            $_POST['data']['uid'] = $uid;
            // 获取字段
            $myfield = array_merge($this->_get_field($catid), $this->sysfield);
            $data = $this->validate_filter($myfield);
            // 返回错误
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data', TRUE);
            } elseif (!$catid) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('还没有选择栏目'));
            } else {
                $data[1]['uid'] = $uid;
                $data[1]['catid'] = $catid;
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $this->clear_cache('save_'.APP_DIR.'_'.$this->uid);
                    $id = $this->content_model->save_draft($did, $data, 0);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $myfield);
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$id.'】保存草稿'); // 记录日志
                    $this->admin_msg(fc_lang('已保存到我的草稿箱中'), dr_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                // 数据来至草稿时更新时间
                $did && $data[1]['updatetime'] = $data[1]['inputtime'] = SYS_TIME;
                // 正常发布
                if (($id = $this->content_model->add($data, $syncatid)) != FALSE) {
					// 执行提交后的脚本
					$this->validate_table($id, $myfield, $data);
                    // 发布草稿时删除草稿数据
                    $did && $this->content_model->delete_draft($did, 'cid=0 and eid=0')
                        ? $this->attachment_replace_draft($did, $id, 0, $this->content_model->prefix)
                        : $this->clear_cache('save_'.APP_DIR.'_'.$this->uid);
                    $mark = $this->content_model->prefix.'-'.$id;
                    $member = $this->member_model->get_base_member($uid);
                    $rule = $cat['permission'][$member['markrule']];
                    // 积分处理
                    $rule['experience'] + $member['experience'] >= 0 && $this->member_model->update_score(0, $uid, $rule['experience'], $mark, "发布文档", 1);
                    // 虚拟币处理
                    $rule['score'] + $member['score'] >= 0 && $this->member_model->update_score(1, $uid, $rule['score'], $mark, "发布文档", 1);
                    // 操作成功处理附件

                    $this->attachment_handle($data[1]['uid'], $mark, $myfield);
                    // 处理推荐位
                    $update = $this->input->post('flag');
                    if ($update) {
                        foreach ($update as $i) {
                            $this->db->insert(SITE_ID.'_'.APP_DIR.'_flag', array(
                                'id' => $id,
                                'uid' => $uid,
                                'flag' => $i,
                                'catid' => $catid
                            ));
                        }
                    }
                    $this->system_log('添加 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$id.'】'); // 记录日志
                    // 是否创建静态页面链接
                    $create = $cat['setting']['html'] && $data[1]['status'] == 9 ? dr_module_create_show_file($id, 1) : '';
                    if ($this->input->post('action') == 'back') {
                        $this->admin_msg(
                            fc_lang('操作成功，正在刷新...').
                            ($create ? "<script src='".$create."'></script>".dr_module_create_list_file($catid) : ''),
                            $this->_get_back_url(APP_DIR.'/home/index'),
                            1,
                            1
                        );
                    } else {
                        unset($data);
                        $error = array('msg' => dr_lang('发布成功'), 'status'=>1);
                    }
                }
            }
            $data['syncatid'] = $syncatid;
            $select = $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), $catid, 'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1);
        } else {
            if ($did) {
                $temp = $this->content_model->get_draft($did);
                $temp['draft']['cid'] == 0 && $temp['draft']['eid'] == 0 && $data = $temp;
            } else {
                $data = $this->get_cache_data('save_'.APP_DIR.'_'.$this->uid);
            }
            $catid = $data['catid'] ? $data['catid'] : $catid;
            // 栏目id不存在时就去第一个可用栏目为catid
            if (!$catid) {
                list($select, $catid) = $this->select_category($category, 0, 'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1, 1);
            } else {
                $select = $this->select_category($category, $catid, 'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1);
            }


        }

        // 判断栏目权限
        !$this->is_category_auth($catid, 'add') && $this->admin_msg(fc_lang('您无权限操作'));

        $myfield = $this->_get_field($catid);
        define('MODULE_CATID', $catid);

        $this->template->assign(array(
            'did' => $did,
            'data' => $data,
            'flag' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'flag'),
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') => array($this->_get_back_url(APP_DIR.'/home/index'), 'reply'),
                fc_lang('发布') => array(APP_DIR.'/admin/home/add', 'plus')
            )),
            'catid' => $catid,
            'error' => $error,
            'create' => $create,
            'myflag' => $this->input->post('flag'),
            'select' => $select,
            'myfield' => $this->new_field_input($myfield, $data, TRUE),
            'sysfield' => $this->new_field_input($this->sysfield, $data, TRUE, '', '<div class="form-group" id="dr_row_{name}"><label class="col-sm-12">{text}</label><div class="col-sm-12">{value}</div></div>'),
            'draft_url' => dr_url(APP_DIR.'/home/add'),
            'draft_list' => $this->content_model->get_draft_list('cid=0 and eid=0'),
            'is_category_field' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category_field'),
        ));
        $this->template->display('content_add.html');
    }

    /**
     * 修改
     */
    public function edit() {

        $id = (int)$this->input->get('id');
        $did = (int)$this->input->get('did');
        $cid = (int)$this->input->get('catid');
        $data = $this->content_model->get($id);
        $catid = $cid ? $cid : $data['catid'];
        $error = $myflag = array();
        unset($cid);

        // 数据判断
        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

        if ($data['link_id'] > 0) {
            $data = $this->content_model->get($data['link_id']);
            !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

        $lid = $data['link_id'] > 0 ? $data['id'] : $id;

        // 栏目缓存
        $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category');

        if ($flag = $this->db->where('id', $id)->get(SITE_ID.'_'.APP_DIR.'_flag')->result_array()) {
            foreach ($flag as $t) {
                $myflag[] = $t['flag'];
            }
        }
        unset($flag);

        if (IS_POST) {
            $cid = (int)$this->input->post('catid');
            // 判断栏目权限
            $cid != $catid && !$this->is_category_auth($catid, 'add') && $this->admin_msg(fc_lang('您无权限操作'));
            $catid = $cid;
            $cat = $cid != $catid ? $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid) : $category[$catid];
            unset($cid);
            // 设置uid便于校验处理
            $uid = $this->input->post('data[author]') ? get_member_id($this->input->post('data[author]')) : 0;
            $_POST['data']['id'] = $id;
            $_POST['data']['uid'] = $uid;
            // 获取字段
            $myfield = array_merge($this->_get_field($catid), $this->sysfield);
            $post = $this->validate_filter($myfield, $data);
            if (isset($post['error'])) {
                $error = $post;
            } elseif (!$catid) {
                $error = array('error' => 'catid', 'msg' => fc_lang('还没有选择栏目'));
            } else {
                $post[1]['uid'] = $uid;
                $post[1]['catid'] = $catid;
                $post[1]['updatetime'] = $this->input->post('no_time') ? $data['updatetime'] : $post[1]['updatetime'];
                // 保存为草稿
                if ($this->input->post('action') == 'draft') {
                    $post[1]['id'] = $post[0]['id'] = $lid;
                    $id = $this->content_model->save_draft($did, $post, 0);
                    $this->attachment_handle($this->uid, $this->content_model->prefix.'_draft-'.$id, $myfield);
                    $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$lid.'】保存草稿'); // 记录日志
                    $this->admin_msg(fc_lang('已保存到我的草稿箱中'), dr_url(APP_DIR.'/home/draft/'), 1);
                    exit;
                }
                // 正常保存
                $this->content_model->edit($data, $post, $lid);
				// 执行提交后的脚本
				$this->validate_table($id, $myfield, $post);
                // 发布草稿时删除草稿数据
                $did && $this->content_model->delete_draft($did, 'cid='.$lid.' and eid=0') && $this->attachment_replace_draft($did, $lid, 0, $this->content_model->prefix);
                // 操作成功处理附件
                $this->attachment_handle($post[1]['uid'], $this->content_model->prefix.'-'.$lid, $myfield, $data);
                // 处理推荐位
                $update = $this->input->post('flag');
                if ($update !== $myflag) {
                    // 删除旧的
                    $myflag && $this->db->where('id', $id)->where_in('flag', $myflag)->delete(SITE_ID.'_'.APP_DIR.'_flag');
                    // 增加新的
                    if ($update) {
                        foreach ($update as $i) {
                            $this->db->insert(SITE_ID.'_'.APP_DIR.'_flag', array(
                                'id' => $id,
                                'uid' => $uid,
                                'flag' => $i,
                                'catid' => $catid
                            ));
                        }
                    }
                }
                $this->system_log('修改 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$lid.'】'); // 记录日志
				if ($cat['setting']['html']
                    && $data['link_id'] != 0 && $post[1]['status'] == 10) {
                    // 删除生成的静态文件
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
                //exit;
                $this->admin_msg(
                    fc_lang('操作成功，正在刷新...') .
                    ($cat['setting']['html'] && $post[1]['status'] == 9 ? dr_module_create_show_file($lid).dr_module_create_list_file($catid) : ''),
                    $this->_get_back_url(APP_DIR.'/home/index'),
                    1,
                    1
                );
            }
			$data = $this->input->post('data', TRUE);
            $myflag = $this->input->post('flag');
        } else {
            if ($did) {
                $temp = $this->content_model->get_draft($did);
                if ($temp['draft']['cid'] == $data['id'] && $temp['draft']['eid'] == 0) {
                    $temp['id'] = $id;
                    $data = $temp;
                    $catid = $temp['catid'] ? $temp['catid'] : $catid;
                }
            }
        }

        // 判断栏目权限
        !$this->is_category_auth($catid, 'add') && $this->admin_msg(fc_lang('您无权限操作'));

        // 可用字段
        $myfield = $this->_get_field($catid);
        define('MODULE_CATID', $catid);

        $data['updatetime'] = SYS_TIME;
        $this->template->assign(array(
            'did' => $did,
            'data' => $data,
            'flag' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'flag'),
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') => array($this->_get_back_url(APP_DIR.'/home/index'), 'reply'),
                fc_lang('发布') => array(APP_DIR.'/admin/home/add/catid/'.$catid, 'plus')
            )),
            'catid' => $catid,
            'error' => $error,
            'myflag' => $myflag,
            'select' => $this->select_category($category, $catid, 'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1),
            'myfield' => $this->new_field_input($myfield, $data, TRUE),
            'sysfield' => $this->new_field_input($this->sysfield, $data, TRUE, '', '<div class="form-group" id="dr_row_{name}"><label class="col-sm-12">{text}</label><div class="col-sm-12">{value}</div></div>'),
            'draft_url' => dr_url(APP_DIR.'/home/edit', array('id' => $id)),
            'draft_list' => $this->content_model->get_draft_list('cid='.$id.' and eid=0'),
            'is_category_field' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category_field'),
        ));
        $this->template->display('content_add.html');
    }
	
	/*===========草稿部分===========*/

	/**
     * 草稿箱管理
     */
    public function draft() {

        $table = $this->content_model->prefix.'_draft';

        if (IS_POST) {
            $ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
            $this->load->model('attachment_model');
            foreach ($ids as $id) {
                // 删除草稿记录
                if ($this->db->where('id', $id)->where('uid', $this->uid)->get($table)->row_array()) {
                    $this->db->where('id', $id)->delete($table);
                    // 删除表对应的附件
                    $this->attachment_model->delete_for_table($table.'-'.$id);
                }
            }
            $this->system_log('删除站点【#'.SITE_ID.'】模块【'.APP_DIR.'】草稿内容【#'.@implode(',', $ids).'】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $page = max(1, (int) $this->input->get('page'));
        $total = $_GET['total'] ? intval($_GET['total']) : $this->db->where('uid', $this->uid)->count_all_results($table);
        $result = $total ? $this->db
                                ->where('uid', $this->uid)
                                ->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1))
                                ->order_by('inputtime DESC, id DESC')
                                ->get($table)
                                ->result_array() : array();

        // 存储当前页URL
        $this->_set_back_url(APP_DIR.'/home/index', '', APP_DIR.'/home/draft');
        
        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('草稿箱') =>  array(APP_DIR.'/admin/home/draft', 'edit'),
                fc_lang('发布') => array(APP_DIR.'/admin/home/add', 'plus')
            )),
            'list' => $result,
            'total' => $total,
            'pages' => $this->get_pagination(dr_url(APP_DIR.'/home/draft'), $total)
        ));
        $this->template->display('content_draft.html');
    }
	
	/*===========审核部分===========*/
	
	/**
     * 审核
     */
    public function verify() {

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
                $where = '`status` IN ('.implode(',', $status).')';
            } else {
                $where = '';
            }
            switch ($this->input->post('action')) {
                case 'del': // 删除
                    $this->load->model('attachment_model');
                    foreach ($ids as $id) {
                        // 主表状态
                        $data = $this->db
                                     ->where($where ? $where.' AND `id`='.(int)$id : '`id`='.(int)$id)
                                     ->select('uid,catid')
                                     ->limit(1)
                                     ->get($this->content_model->prefix.'_index')
                                     ->row_array();
                        if ($data) {
                            // 删除数据
                            $this->content_model->del_verify($id);
                            // 删除表对应的附件
                            $this->attachment_model->delete_for_table($this->content_model->prefix.'_verify-'.$id);
                        }
                    }
                    $this->system_log('删除站点【#'.SITE_ID.'】模块【'.APP_DIR.'】审核内容【#'.@implode(',', $ids).'】'); // 记录日志
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
                    $error ? exit(dr_json(1, $error, $js)) : exit(dr_json(2, fc_lang('操作成功，正在刷新...'), $js));
                    $this->system_log('处理站点【#'.SITE_ID.'】模块【'.APP_DIR.'】审核内容【#'.@implode(',', $ids).'】'); // 记录日志
                    break;
                default:
                    exit(dr_json(0, fc_lang('未定义的操作')));
                    break;
            }
        }

        $_menu = $param = array();
        $meta_name = fc_lang('被退回');
        $param['status'] = (int)$this->input->get('status');

        if ($this->admin['adminid'] == 1) {
            // 管理员角色列出所有审核流程
            $goto = isset($_GET['status']) ? 1 : '';
            $where = '`status`='.$param['status'];
            for ($i = 0; $i < 9; $i++) {
                $total = (int)$this->db->where('status', $i)->count_all_results($this->content_model->prefix.'_verify');
                $key_name = $i ? fc_lang('%s审', $i) : fc_lang('被退回');
                $total && !$goto && $i > 0 && $goto = $param['status'] == $i ? 1 : $key_name;
                $_menu[$key_name] = array(
                    'url' => $this->duri->uri2url(APP_DIR.'/admin/home/verify'.(isset($_GET['status']) || $i ? '/status/'.$i : '')),
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
                'url' => $this->duri->uri2url(APP_DIR.'/admin/home/verify'),
                'count' => $this->db->where('`status`=0 AND `backuid`='.$this->uid)->count_all_results($this->content_model->prefix.'_verify')
            );
            // 我的审核
            $_menu[fc_lang('我的审核')] = array(
                'url' => $this->duri->uri2url(APP_DIR.'/admin/home/verify/status/1'),
                'count' => $this->db->where_in('status', $status)->count_all_results($this->content_model->prefix.'_verify')
            );
            $param['status'] == 1 && $meta_name = fc_lang('我的审核');
        }
        // 栏目筛选
        if ($this->input->get('catid')) {
            $param['catid'] = (int) $this->input->get('catid');
            $where.= ' AND `catid` = '.$param['catid'];
        }
        // 获取总数量
        $param['total'] = $total = $this->input->get('total') ? $this->input->get('total') : $this->db->where($where)->count_all_results($this->content_model->prefix.'_verify');
        $page = max(1, (int) $this->input->get('page'));
        $data = $this->db
                     ->select('id,catid,author,content,inputtime,status')
                     ->where($where)
                     ->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1))
                     ->order_by('inputtime DESC, id DESC')
                     ->get($this->content_model->prefix . '_verify')
                     ->result_array();

        $this->template->assign(array(
            'list' => $data,
            'menu' => $_menu,
            'param' => $param,
            'meta_name' => $meta_name,
            'pages' => $this->get_pagination(dr_url(APP_DIR.'/home/verify', $param), $param['total'])
        ));
        $this->template->display('content_verify.html');
    }


    /**
     * 修改审核文档
     */
    public function verifyedit() {

        $id = (int)$this->input->get('id');
        $cid = (int)$this->input->get('catid');
        $data = $this->content_model->get_verify($id);
        $catid = $cid ? $cid : $data['catid'];
        $error = array();

        // 数据验证
        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

        $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category');

        $this->_check_admin_verify($data, $category[$catid]) && $this->admin_msg(fc_lang('对不起，无权限审核该内容'));

        if (IS_POST) {
            $cid = (int)$this->input->post('catid');
            define('MODULE_CATID', $cid);
            // 判断栏目权限
            $cid != $catid && !$this->is_category_auth($catid, 'add') && $this->admin_msg(fc_lang('您无权限操作'));
            $catid = $cid;
            $category = $cid != $catid ? $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $catid) : $category[$catid];
            unset($cid);
			$myfield = $this->_get_field($catid);
			unset($myfield['status']);
            // 设置uid便于校验处理
            $uid = $this->input->post('data[author]') ? get_member_id($this->input->post('data[author]')) : 0;
            $_POST['data']['id'] = $id;
            $_POST['data']['uid'] = $uid;
            // 获取字段
            $myfield = array_merge($this->_get_field($catid), $this->sysfield);
            unset($myfield['status']);
            $post = $this->validate_filter($myfield, $data['content']);
            if (isset($post['error'])) {
                $error = $post;
                $data['content'] = $this->input->post('data', TRUE);
            } elseif (!$catid) {
                $data['content'] = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('还没有选择栏目'));
            } elseif (!$this->input->post('flagid')) {
                $data['content'] = $this->input->post('data', TRUE);
                $error = array('error' => 'flagid', 'msg' => fc_lang('请选择审核状态'));
            } else {
                $post[1]['uid'] = $uid;
                $post[1]['catid'] = $catid;
                $result = $this->_verify($id, $post, '`id`='.$id);
                $this->system_log('处理站点【#'.SITE_ID.'】模块【'.APP_DIR.'】审核内容【#'.$id.'】'); // 记录日志
                if (is_array($result)) {
                    $this->admin_msg(
                        fc_lang('操作成功，正在刷新...').
                        ($category['setting']['url'] ? dr_module_create_show_file($id).dr_module_create_list_file($catid) : ''),
                        $this->duri->uri2url($this->input->post('backurl')),
                        1,
                        1
                    );
                } elseif ($result) {
                    $this->admin_msg($result);
                }
                $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/home/verify'), 1);
            }
        } else {
            define('MODULE_CATID', $catid);
        }

        // 可用字段
        $myfield = $this->_get_field($catid);
        unset($myfield['status']);

        if ($data['status'] == 0) { // 退回
            $backuri = APP_DIR.'/admin/home/verify/status/0';
        } elseif ($data['status'] > 0 && $data['status'] < 9) {
            $backuri = APP_DIR.'/admin/home/verify/status/'.$data['status'];
        } else {
            $backuri = APP_DIR.'/admin/home/verify/';
        }

        $data['content']['status'] = 9;
        $this->template->assign(array(
            'data' => $data['content'],
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') => array($backuri, 'reply'),
                fc_lang('修改') => array(APP_DIR.'/admin/home/verifyedit/id/'.$data['id'], 'edit')
            )),
            'catid' => $catid,
            'error' => $error,
            'select' => $this->select_category($category, $catid, 'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"', '', 1, 1),
            'backurl' => $backuri,
            'myfield' => $this->new_field_input($myfield, $data['content'], TRUE),
            'sysfield' => $this->new_field_input($this->sysfield, $data['content'], TRUE, '', '<div class="form-group" id="dr_row_{name}"><label class="col-sm-12">{text}</label><div class="col-sm-12">{value}</div></div>'),
            'is_category_field' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category_field'),
        ));
        $this->template->display('content_edit.html');
    }

    // 审核内容
    public function _verify($id, $data, $_where) {

        // 获得审核数据
        $verify = $this->content_model->get_verify($id);
        if (!$verify) {
            return;
        }
        $category = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $verify['catid']);
        if ($this->_check_admin_verify($verify, $category)) {
            return;
        }
        // 通过审核
        if ($this->input->post('flagid') > 0) {
            // 查询当前的审核状态id
            $status = $this->_get_verify_status($verify['uid'], $verify['catid'], $verify['status']);
            // 权限验证
            if ($status == 9) {
                $member = $this->member_model->get_base_member($verify['uid']);
                // 标示
                $rule = $category['permission'][$member['markrule']];
                $mark = $this->content_model->prefix.'-'.$id;
                // 积分处理
                $rule['experience'] && $this->member_model->update_score(0, $verify['uid'], $rule['experience'], $mark, "发布文档", 1);
                // 虚拟币处理
                if ($rule['score']) {
                    // 虚拟币判断重复
                    if (!$this->db->where('type', 1)->where('mark', $mark)->count_all_results('member_scorelog')) {
                        if ($rule['score'] + $member['score'] < 0) {
                            // 数量不足提示
                            return fc_lang('【%s】审核失败！<br>会员（%s）%s不足，此次需要：%s', $verify['content']['title'],  $member['username'], SITE_SCORE, abs($rule['score']));
                        }
                        $this->member_model->update_score(1, $verify['uid'], $rule['score'], $mark, "发布文档", 1);
                    }
                }
            }
            $catid = $data[1]['catid'] ? $data[1]['catid'] : (int)$verify['catid'];
            // 筛选字段
            if (!$data) {
                $data = array();
                $myfield = $this->_get_field($catid);
                foreach ($myfield as $field) {
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
            } else {
                $myfield = $this->_get_field($verify['catid']);
            }

            $data[1]['id'] = $data[0]['id'] = $id;
            $data[1]['uid'] = $data[0]['uid'] = (int)$verify['uid'];
            $data[1]['catid'] = $data[0]['catid'] = $catid;
            $data[1]['updatetime'] = $verify['content']['updatetime'] ? $verify['content']['updatetime'] : $verify['content']['inputtime'];
            $data[1]['updatetime'] = $data[1]['updatetime'] ? $data[1]['updatetime'] : SYS_TIME;
            $data[1]['inputtime'] = $data[1]['inputtime'] ? $data[1]['inputtime'] : $data[1]['updatetime'];
            $data[1]['status'] = $status;

            // 保存内容
            $this->content_model->edit($verify['content'], $data);
            // 审核通过
            if ($status == 9) {
                // 审核通过的挂钩点
                $this->hooks->call_hook('content_verify', $data);
				// 执行提交后的脚本
				$this->validate_table($id, $myfield, $data);
                // 操作成功处理附件
                $this->attachment_handle($data[1]['uid'], $this->content_model->prefix.'-'.$id, $myfield, $data);
                $this->attachment_replace($data[1]['uid'], $id, $this->content_model->prefix);
                $this->member_model->add_notice(
                    $data[1]['uid'],
                    3,
                    fc_lang('【%s】审核通过', $verify['content']['title'])
                );
                $this->member_model->update_admin_notice(APP_DIR.'/admin/home/verifyedit/id/'.$id, 3);
                return array('id' => $id, 'catid' => $data[1]['catid']);
            }
        } else {
            // 拒绝审核
            // 更改主表状态
            $this->db->where($_where)->update($this->content_model->prefix, array('status' => 0));
            // 更改索引表状态
            $this->db->where($_where)->update($this->content_model->prefix.'_index', array('status' => 0));
            // 更改审核表状态
            $this->db->where($_where)->update($this->content_model->prefix.'_verify', array(
                    'status' => 0,
                    'backuid' => (int)$this->uid,
                    'backinfo' => dr_array2string(array(
                        'uid' => $this->uid,
                        'author' => $this->admin['username'],
                        'rolename' => $this->admin['role']['name'],
                        'optiontime' => SYS_TIME,
                        'backcontent' => $this->input->post('backcontent')
                    ))
                )
            );
            $this->member_model->update_admin_notice(APP_DIR.'/admin/home/verifyedit/id/'.$id, 2);
            $this->member_model->add_notice(
                $verify['uid'],
                3,
                fc_lang('【%s】审核被拒绝，<a href="%s">查看原因</a>', $verify['content']['title'], SITE_URL.'index.php?s=member&mod='.APP_DIR.'&c=back&m=edit&id='.$id)
            );
        }
    }
	
	/*===========相关功能===========*/

    /**
     * 生成静态
     */
    public function html() {
        redirect(ADMIN_URL.dr_url('html/index', array('dir' => APP_DIR)), 'refresh');
        exit;

    }

    /**
     * 清除静态文件
     */
    public function clear() {
        redirect(ADMIN_URL.dr_url('html/index', array('dir' => APP_DIR)), 'refresh');
        exit;

    }

    // 复制文章
    public function copy() {

        $this->admin_msg('功能已取消，请安装模块内容复制的应用');

    }

    // 推送执行界面
    public function ts_ajax() {

        if ($this->input->get('ispost')) {
            $ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
            !$this->is_auth(APP_DIR.'/admin/home/edit') && exit(dr_json(0, fc_lang('您无权限操作')));
            if ($this->input->get('tab') == 1) {
                // 推荐位推送
                $flag = array();
                $value = @explode(',', $this->input->get('value'));
                !$value && exit(dr_json(0, fc_lang('您还没有选择呢')));
                // 执行推荐位
                foreach ($value as $t) {
                    if ($t) {
                        $flag[] = $t;
                        $this->content_model->flag($ids, $t);
                        $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】设置推荐位#'.$t); // 记录日志
                    }
                }
                // 再次验证
                !$flag && exit(dr_json(0, fc_lang('您还没有选择呢')));
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            } elseif ($this->input->get('tab') == 0) {
				// 推送栏目
				$value = @explode(',', $this->input->get('value'));
                !$value && exit(dr_json(0, fc_lang('您还没有选择呢')));
				 // 执行同步指定栏目
				foreach ($ids as $id) {
					$this->db->where('id', (int)$id)->update($this->content_model->prefix, array('link_id' => -1)); // 更改状态
					$data = $this->db->where('id', (int)$id)->get($this->content_model->prefix)->row_array(); // 获取数据
					if (!$data) {
						continue;
					}
					foreach ($value as $catid) {
						if ($catid && $catid != $data['catid']) {
							// 插入到同步栏目中
							$new[1] = $data;
							$new[1]['catid'] = $catid;
							$new[1]['link_id'] = $id;
							$new[1]['tableid'] = 0;
							$new[1]['id'] = $this->content_model->index($new);
							$this->db->replace($this->content_model->prefix, $new[1]); // 创建主表
							$this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.@implode(',', $ids).'】同步到栏目#'.$catid); // 记录日志
						}
					}
				}
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			}	
        } else {
            $this->template->assign(array(
                'flag' => $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'flag'),
                'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), 0, 'id="dr_synid" name=\'catid[]\' multiple style="width:200px;height:250px;"', '', 1, 1),
            ));
            $this->template->display('content_ts.html');exit;
        }
    }
	
	// 文档状态设定
	public function status() {
		
		$id = (int)$this->input->get('id');
        $data = $this->content_model->get($id);
        !$data && exit(dr_json(0, fc_lang('对不起，数据被删除或者查询不存在')));
		
		// 删除缓存
        $this->clear_cache('show'.APP_DIR.SITE_ID.$id);
        $this->clear_cache('mshow'.APP_DIR.SITE_ID.$id);
		
		if ($data['status'] == 10) {
			$this->db->where('id', $id)->update($this->content_model->prefix, array('status' => 9));
			$this->db->where('id', $id)->update($this->content_model->prefix.'_index', array('status' => 9));
            // 调用方法状态更改方法
            $data['status'] = 9;
            $this->content_model->_update_status($data);
            $this->system_log('修改 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$id.'】状态为【正常】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $data['catid']));
		} else {
			// 删除生成的文件
			if ($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category', $data['catid'], 'setting', 'html')
                && strpos($data['url'], 'index.php') === FALSE) {
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
            $this->system_log('修改 站点【#'.SITE_ID.'】模块【'.APP_DIR.'】内容【#'.$id.'】状态为【关闭】'); // 记录日志
			exit(dr_json(1, fc_lang('操作成功，正在刷新...'), 0));
		}
		
	}

    // 跳转
    public function content() {
        redirect(ADMIN_URL.dr_url(APP_DIR.'/content/index'), 'refresh');
        exit;
    }

    // 同步栏目
    public function syncat_ajax() {

        $cat = array();
        $ids = @explode('|', $this->input->get('ids'));
        $cache = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category');
        foreach ($cache as $t) {
            if (!$t['child']) {
                $cat[$t['id']] = array(
                    'id' => $t['id'],
                    'name' => $t['name'],
                    'cname' => dr_catpos($t['id'], ' > ', FALSE),
                );
            }
        }

        $this->template->assign(array(
            'cat' => $cat,
            'ids' => $ids,
        ));
        $this->template->display('content_syncat.html');exit;
    }

    /**
     * 更新URL 兼容处理
     */
    public function url() {

        $cfile = SITE_ID.APP_DIR.$this->uid.$this->input->ip_address().'_content_url';

        if (IS_POST) {
            $catid = $this->input->post('catid');
            $query = $this->db;
            if (count($catid) > 1 || $catid[0]) {
                $query->where_in('catid', $catid);
                count($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category')) == count($catid) && $catid = 0;
            } else {
                $catid = 0;
            }
            // 统计数量
            $total = $query->count_all_results($this->content_model->prefix.'_index');
            $this->cache->file->save($cfile, array('catid' => $catid, 'total' => $total), 10000);
            if ($total) {
                $this->system_log('站点【#'.SITE_ID.'】模块【'.APP_DIR.'】更新URL地址#'.$total); // 记录日志
                $this->mini_msg(fc_lang('可更新内容%s条，正在准备执行...', $total), dr_url(APP_DIR.'/content/url', array('todo' => 1)), 2);
            } else {
                $this->mini_msg(fc_lang('抱歉，没有找到可更新的内容'));
            }
        }

        // 处理url
        if ($this->input->get('todo')) {
            $page = max(1, (int)$this->input->get('page'));
            $psize = 100; // 每页处理的数量
            $cache = $this->cache->file->get($cfile);
            if ($cache) {
                $total = $cache['total'];
                $catid = $cache['catid'];
            } else {
                $catid = 0;
                $total = $this->db->count_all_results($this->content_model->prefix);
            }
            $tpage = ceil($total / $psize); // 总页数
            if ($page > $tpage) {
                // 更新完成删除缓存
                $this->cache->file->delete($cfile);
                $this->mini_msg(fc_lang('更新成功'), NULL, 1);
            }
            $module = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR);
            $table = $this->content_model->prefix;
            $catid && $this->db->where_in('catid', $catid);
            $data = $this->db->limit($psize, $psize * ($page - 1))->order_by('id DESC')->get($table)->result_array();
            foreach ($data as $t) {
                if ($t['link_id'] && $t['link_id'] >= 0) {
                    // 同步栏目的数据
                    $i = $t['id'];
                    $t = $this->db->where('id', (int)$t['link_id'])->get($table)->row_array();
                    if (!$t) {
                        continue;
                    }
                    $url = dr_show_url($module, $t);
                    $t['id'] = $i; // 替换成当前id
                } else {
                    $url = dr_show_url($module, $t);
                }
                $this->db->update($table, array('url' => $url), 'id='.$t['id']);
                if ($module['extend']) {
                    $extend = $this->db->where('cid', (int)$t['id'])->order_by('id DESC')->get($table.'_extend')->result_array();
                    if ($extend) {
                        foreach ($extend as $e) {
                            $e['fid'] = intval($data['fid']);
                            $this->db->where('id=',(int)$e['id'])->update($table.'_extend', array(
                                'url' => dr_extend_url($module, $e)
                            ));
                        }
                    }
                }
            }
            $this->mini_msg(fc_lang('正在执行中(%s) ... ', "$tpage/$page"), dr_url(APP_DIR.'/content/url', array('todo' => 1, 'page' => $page + 1)), 2, 0);
        } else {
            $this->template->assign(array(
                'select' => $this->select_category($this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'category'), 0, 'id="dr_synid" name=\'catid[]\' multiple style="width:200px;height:250px;"', ''),
            ));
            $this->template->display('content_url.html');
        }
    }

}
