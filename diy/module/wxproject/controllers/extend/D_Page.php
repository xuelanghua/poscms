<?php

/* v3.1.0  */

class D_Page extends M_Controller {

    private $_id;
	private $field;
	private $nocache;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		if (IS_ADMIN) {
            $menu = array(
                fc_lang('页面管理') => array(APP_DIR.'/admin/page/index', 'adn'),
                fc_lang('添加') => array(APP_DIR.'/admin/page/add', 'plus'),
                fc_lang('自定义字段') => array('admin/field/index/rname/page/rid/'.SITE_ID, 'plus-square'),
            );
            if (APP_DIR) {
                unset($menu[fc_lang('自定义字段')]);
            }
			$this->template->assign('menu', $this->get_menu_v3($menu));
        }
        $this->field = array(
            'name' => array(
                'name' => IS_ADMIN ? fc_lang('页面名称') : '',
                'ismain' => 1,
                'fieldname' => 'name',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 150,
                    ),
                    'validate' => array(
                        'required' => 1,
                        'formattr' => 'onblur="d_topinyin(\'dirname\',\'name\');"',
                    )
                )
            ),
            'dirname' => array(
                'name' => IS_ADMIN ? fc_lang('页面目录') : '',
                'ismain' => 1,
                'fieldname' => 'dirname',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 150,
                    ),
                    'validate' => array(
                        'required' => 1,
                    )
                )
            ),
            'thumb' => array(
                'name' => IS_ADMIN ? fc_lang('缩略图') : '',
                'ismain' => 1,
                'fieldname' => 'thumb',
                'fieldtype' => 'File',
                'setting' => array(
                    'option' => array(
                        'ext' => 'jpg,gif,png',
                        'size' => 10,
                    )
                )
            ),
            'keywords' => array(
                'name' => IS_ADMIN ? fc_lang('SEO关键字') : '',
                'ismain' => 1,
                'fieldname' => 'keywords',
                'fieldtype'	=> 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => '80%'
                    )
                )
            ),
            'title' => array(
                'name' => IS_ADMIN ? fc_lang('SEO标题') : '',
                'ismain' => 1,
                'fieldname' => 'title',
                'fieldtype'	=> 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => '80%'
                    )
                )
            ),
            'description' => array(
                'name' => IS_ADMIN ? fc_lang('SEO描述信息') : '',
                'ismain' => 1,
                'fieldname' => 'description',
                'fieldtype'	=> 'Textarea',
                'setting' => array(
                    'option' => array(
                        'width' => '80%',
                        'height' => 60
                    )
                )
            ),
            'template' => array(
                'name' => IS_ADMIN ? fc_lang('模板文件') : '',
                'ismain' => 1,
                'fieldname' => 'template',
                'fieldtype'	=> 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                        'value' => 'page.html'
                    )
                )
            ),
            'urllink' => array(
                'name' => IS_ADMIN ? fc_lang('转向链接') : '',
                'ismain' => 1,
                'fieldname' => 'urllink',
                'fieldtype'	=> 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 400,
                        'value' => ''
                    )
                )
            ),
            'urlrule' => array(
                'name' => IS_ADMIN ? fc_lang('URL规则') : '',
                'ismain' => 1,
                'fieldname' => 'urlrule',
                'fieldtype'	=> 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 300
                    )
                )
            ),
            'show' => array(
                'name' => IS_ADMIN ? fc_lang('是否显示') : '',
                'ismain' => 1,
                'fieldname' => 'show',
                'fieldtype'	=> 'Radio',
                'setting' => array(
                    'option' => array(
                        'value' => '1',
                        'options' => (IS_ADMIN ? fc_lang('是') : 'Yes').'|1'.PHP_EOL.(IS_ADMIN ? fc_lang('否') : 'No').'|0',
                    )
                )
            ),
            'getchild' => array(
                'name' => IS_ADMIN ? fc_lang('排序') : '',
                'ismain' => 1,
                'fieldtype'	=> 'Radio',
                'fieldname' => 'getchild',
                'setting' => array(
                    'option' => array(
                        'value' => '1',
                        'options' => (IS_ADMIN ? fc_lang('是') : 'Yes').'|1'.PHP_EOL.(IS_ADMIN ? fc_lang('否') : 'No').'|0',
                    )
                )
            ),
        );
		$this->load->model('page_model');
    }

    //
    protected function _get_page($id, $dir, $pid) {

        !$id && !$dir && $this->goto_404_page(fc_lang('参数不完整，id与dir必须有一个'));

        // 页面缓存
        $PAGE = $this->get_cache('page-'.SITE_ID);
        $page = APP_DIR ? $PAGE['data'][APP_DIR] : $PAGE['data']['index'];

        // 获取页面ID
        $id = !$id && $dir ? $PAGE['dir'][$dir] : $id;

        // 无法通过目录找到栏目时，尝试多及目录
        if (!$id && $dir && $page) {
            foreach ($page as $t) {
                if ($t['urlrule']) {
                    $rule = $this->get_cache('urlrule', $t['urlrule']);
                    if ($rule['value']['catjoin'] && strpos($dir, $rule['value']['catjoin'])) {
                        $dir = trim(strchr($dir, $rule['value']['catjoin']), $rule['value']['catjoin']);
                        if (isset($PAGE['dir'][$dir])) {
                            $id = $PAGE['dir'][$dir];
                            break;
                        }
                    }
                }
            }
        }
        unset($PAGE);

        // 当前页面的数据
        $data = $page[$id];
        !$data && $this->goto_404_page(fc_lang('页面（%s）不存在', $id));
        
        // 页面验证是否存在子栏目，是否将下级第一个页面作为当前页
        if ($data['child'] && $data['getchild']) {
            $temp = explode(',', $data['childids']);
            if ($temp) {
                foreach ($temp as $i) {
                    if ($page[$i]['id'] != $id && $page[$i]['show'] && !$page[$i]['child']) {
                        $id = $i;
                        $data = $page[$i];
                        break;
                    }
                }
            }
        }

        $my = $this->get_cache('page-field-'.SITE_ID);
        $my = $my ? array_merge($this->field, $my) : $this->field;
        $data = $this->field_format_value($my, $data, $pid); // 格式化输出自定义字段

        // 定向URL
        $data['url'] && dr_is_redirect(1, dr_url_prefix($data['url']));

        $join = SITE_SEOJOIN ? SITE_SEOJOIN : '_';
        $title = $data['title'] ? $data['title'] : dr_get_page_pname($id, $join);
        isset($data['content_title']) && $data['content_title'] && $title = $data['content_title'].$join.$title;

        // 栏目下级或者同级栏目
        $related = $parent = array();
        if ($data['pid']) {
            foreach ($page as $t) {
                if (!$t['show']) {
                    continue;
                }
                if ($t['pid'] == $data['pid']) {
                    $related[] = $t;
                    $parent = $data['child'] ? $data : $page[$t['pid']];
                }
            }
        } elseif ($data['child']) {
            $parent = $data;
            foreach ($page as $t) {
                if (!$t['show']) {
                    continue;
                }
                $t['pid'] == $data['id'] && $related[] = $t;
            }
        } else {
            $parent = $data;
            if ($page) {
                foreach ($page as $t) {
                    if (!$t['show']) {
                        continue;
                    }
                    $related[] = $t;
                }
            }
        }

        // 格式化配置
        $data['setting'] = dr_string2array($data['setting']);

        // 存储id和缓存参数
        $this->_id = $data['id'];
        $this->nocache = (int)$data['setting']['nocache'];

        $this->template->assign($data);
        $this->template->assign(array(
            'pageid' => $id,
            'parent' => $parent,
            'related' => $related,
            'urlrule' => $this->mobile ? dr_mobile_page_url($data['module'], $data['id'], '{page}') : dr_page_url($data, '{page}'),
            'meta_title' => $title,
            'meta_keywords' => trim($data['keywords'].','.SITE_KEYWORDS, ','),
            'meta_description' => $data['description']
        ));
        $this->template->display($data['template'] ? $data['template'] : 'page.html');
    }
	
	/**
	 * 页面输出
	 */
	protected function _page() {

        ob_start();
        $this->_get_page(
            (int)$this->input->get('id'),
            $this->input->get('dir'),
            max(1, (int)$this->input->get('page'))
        );
        $html = ob_get_clean();

        // 不被缓存
        $this->nocache && exit($html);

        // 生成缓存
        defined('SYS_AUTO_CACHE') && SYS_AUTO_CACHE && file_put_contents(WEBPATH.'cache/page/'.md5(PAGE_CACHE_URL.max(intval($_GET['page']), 1)).'.html', $html, LOCK_EX);

        echo $html;exit;
	}

	/*
	 * 删除
	 */
	protected function admin_delete($ids) {
	
		if (!$ids) {
            return NULL;
        }
		
		// 筛选栏目id
		$catid = '';
		foreach ($ids as $id) {
			$data = $this->page_model->link->select('childids')->where('id', $id)->get($this->page_model->tablename)->row_array();
			$catid.= ','.$data['childids'];
		}
		$catid = explode(',', $catid);
		$catid = array_flip(array_flip($catid));
		$this->load->model('attachment_model');
		
		// 逐一删除
		foreach ($catid as $id) {
			// 删除主表
			$this->page_model->link->where('id', $id)->delete($this->page_model->tablename);
			// 删除附件
			$this->attachment_model->delete_for_table($this->page_model->tablename.'-'.$id);
            // 删除导航数据
            $this->page_model->link->where('mark', 'page-'.$id)->delete(SITE_ID.'_navigator');
		}
		
		$this->page_model->cache(SITE_ID);

        $this->system_log('删除页面【#'.@implode(',', $ids).'】'); // 记录日志
	}
	
    /**
     * 首页
     */
    protected function admin_index() {
		
		if (IS_POST) {
			$ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
			if ($this->input->post('action') == 'order') {
				$data = $this->input->post('data');
				foreach ($ids as $id) {
					$this->page_model->link->where('id', $id)->update($this->page_model->tablename, $data[$id]);
				}
				$this->page_model->cache(SITE_ID);
                $this->system_log('排序页面【'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			} else {
                !$this->is_auth(APP_DIR.'/admin/page/index') && exit(dr_json(0, fc_lang('您无权限操作')));
				$this->admin_delete($ids);
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			}
		}
		
		$this->page_model->repair();
		$this->load->library('dtree');
		$this->dtree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$this->dtree->nbsp = '&nbsp;&nbsp;&nbsp;';
		
		$tree = array();
		$data = $this->page_model->get_data();
		
		if ($data) {
			foreach($data as $t) {
				$t['option'] = '<a class="ago" href="'.dr_url_prefix($t['url']).'" target="_blank"> <i class="fa fa-send"></i> '.fc_lang('访问').'</a>';
                $this->is_auth(APP_DIR.'/admin/page/add') && $t['option'].= '<a class="aadd" href='.dr_url(APP_DIR.'/page/add', array('id' => $t['id'])).'> <i class="fa fa-plus"></i> '.fc_lang('添加子页').'</a>';
                $this->is_auth(APP_DIR.'/admin/page/edit') && $t['option'].= '<a class="aedit" href='.dr_url(APP_DIR.'/page/edit', array('id' => $t['id'])).'> <i class="fa fa-edit"></i> '.fc_lang('修改/内容').'</a>';
				$t['cache'] = $t['setting']['nocache'] ? '<img src="'.THEME_PATH.'admin/images/0.gif">' : '<img src="'.THEME_PATH.'admin/images/1.gif">';
                $t['show'] = $t['show'] ? '<img src="'.THEME_PATH.'admin/images/1.gif">' : '<img src="'.THEME_PATH.'admin/images/0.gif">';
				$t['cache'] = '<a href="'.dr_url(APP_DIR.'/page/option', array('op' => 'cache', 'id' => $t['id'])).'">'.$t['cache'].'</a>';
				$t['show'] = '<a href="'.dr_url(APP_DIR.'/page/option', array('op' => 'show', 'id' => $t['id'])).'">'.$t['show'].'</a>';
                $tree[$t['id']] = $t;
			}
		}
		
		$str = "<tr class='\$class'>";
		$str.= "<td><input name='ids[]' type='checkbox' class='toggle md-check dr_select' value='\$id' /></td>";
		$str.= "<td><input class='input-text displayorder' type='text' name='data[\$id][displayorder]' value='\$displayorder' /></td>";
		$str.= "<td>\$id</td>";
		$str.= $this->is_auth(APP_DIR.'/admin/page/edit') ? "<td>\$spacer<a href='".dr_url(APP_DIR.'/page/edit')."&id=\$id'>\$name</a>  \$parent</td>" : "<td>\$spacer\$name  \$parent</td>";
		$str.= "<td>\$dirname</td>";
        $str.= "<td style='text-align: center'>\$cache</td>";
        $str.= "<td style='text-align: center'>\$show</td>";
		$str.= "<td class='dr_option'>\$option</td>";
		$str.= "</tr>";
		$this->dtree->init($tree);
		
		$this->template->assign(array(
			'list' => $this->dtree->get_tree(0, $str),
            'page' => (int)$this->input->get('page')
		));
		$this->template->display('page_index.html');
    }
	
	/**
     * 添加
     */
    protected function admin_add() {
		
		$pid = (int)$this->input->get('id');
		$data = array(
            'show' => 1,
            'getchild' => 1,
        );
        $error = $result = NULL;
        $field = $this->get_cache('page-field-'.SITE_ID);

		if (IS_POST) {
            $my = $field ? array_merge($this->field, $field) : $this->field;
			$data = $this->validate_filter($my);
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data');
            } else {
                $data[1]['pid'] = $this->input->post('pid');
                $data[1]['show'] = intval($data[1]['show']);
                $data[1]['urlrule'] = $this->input->post('urlrule');
                $data[1]['getchild'] = intval($data[1]['getchild']);
                $page = $this->page_model->add($data[1]);
                if (is_numeric($page)) {
                    // 更新至网站导航
                    $this->load->model('navigator_model');
                    $this->navigator_model->syn_value($data[1], $page);
                    $this->page_model->cache(SITE_ID);
                    $this->system_log('添加页面【#'.$page.'】'.$data[1]['name']); // 记录日志
                    $this->attachment_handle($this->uid, $this->page_model->tablename.'-'.$page, $my);
                    if ($this->input->post('action') == 'back') {
                        $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/page/index'), 1);
                    } else {
                        $pid = $data[1]['pid'];
                        unset($data);
                        $result = fc_lang('操作成功，正在刷新...');
                    }
                } else {
                    $data = $this->input->post('data');
                    $error = array('msg' => $page);
                }
            }
		} else {
            // 调用父属性
            if ($pid && ($row = $this->db->where('id', $pid)->get(SITE_ID.'_page')->row_array())) {
                $data['urlrule'] = $row['urlrule'];
                $data['setting'] = dr_string2array($row['setting']);
                $data['template'] = $row['template'];
                // 过滤自定义字段
                if ($field && $data['setting']['nofield']) {
                    foreach ($field as $i => $t) {
                        if (@in_array($t['fieldname'], $data['setting']['nofield'])) {
                            unset($field[$i]);
                        }
                    }
                }
            }
        }
		
		$this->template->assign(array(
			'page' => 0,
			'data' => $data,
			'error' => $error,
			'field' => $this->field,
			'result' => $result,
            'select' => $this->_select($this->page_model->get_data(), $pid, 'name=\'pid\'', fc_lang('作为顶级')),
            'myfield' => $this->field_input($field, $data, FALSE),
            'myfield2' => $this->get_cache('page-field-'.SITE_ID),
		));
		$this->template->display('page_add.html');
	}
	
	/**
     * 修改
     */
    protected function admin_edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->page_model->get($id);
        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

        $error = $result = NULL;
        $field = $this->get_cache('page-field-'.SITE_ID);
        $data['setting'] = dr_string2array($data['setting']);

        // 过滤自定义字段
        if ($field && $data['setting']['nofield']) {
            foreach ($field as $i => $t) {
                if (@in_array($t['fieldname'], $data['setting']['nofield'])) {
                    unset($field[$i]);
                }
            }
        }

		if (IS_POST) {
            $my = $field ? array_merge($this->field, $field) : $this->field;
            $post = $this->validate_filter($my);
            if (isset($post['error'])) {
                $error = $post;
            } else {
                $post[1]['pid'] = $this->input->post('pid');
                $post[1]['pid'] = $post[1]['pid'] == $id ? $data['pid'] : $post[1]['pid'];
                $post[1]['show'] = intval($post[1]['show']);
                $post[1]['urlrule'] = $this->input->post('urlrule');
                $post[1]['getchild'] = intval($post[1]['getchild']);
                $post[1]['displayorder'] = $data['displayorder'];
                $page = $this->page_model->edit($id, $post[1]);
                if (is_numeric($page)) {
                    // 更新至网站导航
                    $this->load->model('navigator_model');
                    $this->navigator_model->syn_value($post[1], $page);
                    $this->page_model->syn($this->input->post('synid'), $post[1]['urlrule']);
                    $this->attachment_handle($this->uid, $this->page_model->tablename.'-'.$page, $my);
                    $this->page_model->cache(SITE_ID);
                    $this->system_log('修改页面【#'.$page.'】'.$post[1]['name']); // 记录日志
                    $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/page/index'), 1);
                } else {
                    $error = array('msg' => $page);
                }
            }
		}

		$page = $this->page_model->get_data();
		$this->template->assign(array(
			'id' => $id,
			'data' => $data,
			'page' => (int)$this->input->post('page'),
            'error' => $error,
			'field' => $this->field,
			'result' => $result,
            'select' => $this->_select($page, $data['pid'], 'name=\'pid\'', fc_lang('作为顶级')),
            'myfile' => is_file(APPPATH.'templates/admin/page_'.SITE_ID.'_'.$id.'.html') ? 'page_'.SITE_ID.'_'.$id.'.html' : '',
            'myfield' => $this->field_input($field, $data, FALSE),
            'myfield2' => $this->get_cache('page-field-'.SITE_ID),
			'select_syn' => $this->_select($page, 0, 'id="dr_synid" name=\'synid[]\' multiple style="height:200px;"', '')
		));
		$this->template->display('page_add.html');
	}

	
	/**
     * 缓存
     */
    protected function admin_cache() {

        $this->page_model->cache(isset($_GET['site']) ? (int)$_GET['site'] : SITE_ID);
        $this->load->helper('file');
        delete_files(WEBPATH.'cache/page/');

        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
	
	/**
	 * 上级选择
	 *
	 * @param array			$data		数据
	 * @param intval/array	$id			被选中的ID
	 * @param string		$str		属性
	 * @param string		$default	默认选项
	 * @return string
	 */
	private function _select($data, $id = 0, $str = '', $default = ' -- ') {
	
		$tree = array();
		$string = '<select class="form-control" '.$str.'>';

        $default && $string.= "<option value='0'>$default</option>";
		
		if (is_array($data)) {
			foreach($data as $t) {
				$t['selected'] = ''; // 选中操作
				if (is_array($id)) {
					$t['selected'] = in_array($t['id'], $id) ? 'selected' : '';
				} elseif(is_numeric($id)) {
					$t['selected'] = $id == $t['id'] ? 'selected' : '';
				}
				$tree[$t['id']] = $t;
			}
		}
		
		$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
		$str2 = "<optgroup label='\$spacer \$name'></optgroup>";
		
		$this->load->library('dtree');
		$this->dtree->init($tree);
		
		$string.= $this->dtree->get_tree_category(0, $str, $str2);
		$string.= '</select>';
		
		return $string;
	}

    /**
     * 操作
     */
    public function option() {
        if ($this->is_auth('admin/page/edit') && IS_ADMIN) {
            $id = (int)$this->input->get('id');
            $data = $this->page_model->get($id);
            if ($this->input->get('op') == 'show') {
                $value = $data['show'] ? 0 : 1;
                $this->page_model->link->where('id', $id)->update(
                    $this->page_model->tablename,
                    array('show' => $value)
                );
                $this->system_log('修改网站【#'.SITE_ID.'】页面【'.$data['name'].'#'.$id.'】显示状态为：'.($value ? '可见' : '隐藏')); // 记录日志
            } elseif ($this->input->get('op') == 'cache') {
                $data['setting'] = dr_string2array($data['setting']);
                $data['setting']['nocache'] = $value = $data['setting']['nocache'] ? 0 : 1;
                $this->page_model->link->where('id', $id)->update(
                    $this->page_model->tablename,
                    array('setting' => dr_array2string($data['setting']))
                );
                $this->system_log('修改网站【#'.SITE_ID.'】页面【'.$data['name'].'#'.$id.'】状态为：'.($value ? '关闭静态缓存' : '开启静态缓存')); // 记录日志
            }

            $this->page_model->cache(SITE_ID);
            $this->load->helper('file');
            delete_files(WEBPATH.'cache/page/');
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/page/index'), 1);
        } else {
            $this->admin_msg(fc_lang('您无权限操作'));
        }
    }
}