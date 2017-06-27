<?php



class D_Tag extends M_Controller {
	
	public $module;
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->module = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR);
		!$this->module && $this->admin_msg(fc_lang('模块不存在，请尝试更新缓存'));
		$this->load->model('tag_model');
    }
	
	/**
     * tag
     */
	protected function _tag() {
		
		$code = $this->input->get('name', TRUE);
		$this->load->model('tag_model');
		$data = $this->tag_model->tag($code);
		!$data && $this->msg(fc_lang('Tag(%s)不存在', $code));

		$sql = 'SELECT * FROM '.$this->content_model->prefix.' WHERE ';
		$tag = $where = array();
		foreach ($data as $t) {
			$tag[] = $t['name'];
			$where[] = '`title` LIKE "%'.$t['name'].'%" OR `keywords` LIKE "%'.$t['name'].'%"';
		}
		//$urlrule = $this->module['setting']['tag']['url_page'] ? $this->module['setting']['tag']['url_page'] : 'index.php?c=tag&name={tag}&page={page}';
		$tag = implode(',', $tag);
		$sql.= '`status`=9 AND ('.implode(' OR ', $where).')';
		// 分站模式
		if (SITE_FID && $linkage = dr_linkage(SITE_LID, SITE_FID)) {
			$sql.= ' AND `fid` IN ('.$linkage['childids'].')';
			/*
			$site_url = dr_fenzhan_url(SITE_FID); // 分站url
			if (strpos($urlrule, 'index.php') !== FALSE) {
				// 此地址相对于网站根目录时
				if (strpos($urlrule, '/') === 0) {
					$url = ltrim(str_replace('{tag}', $code, $urlrule), '/');
					if (!$this->module['site'][SITE_ID]['domain']) {
						// 此模块未绑定域名才有效
						$urlrule = (SITE_BRANCH_DOMAIN ? $site_url.'/' : SITE_URL).$url;
					} else {
						$urlrule = (SITE_BRANCH_DOMAIN ? $site_url.'/'.$this->module['dirname'].'/' : $this->module['url']).str_replace('{tag}', $code, $urlrule);
					}
				} elseif (strpos($urlrule, 'index.php') === 0) {
					$urlrule = (SITE_BRANCH_DOMAIN ? $site_url.'/'.$this->module['dirname'].'/' : $this->module['url']).str_replace('{tag}', $code, $urlrule).'&fid='.SITE_FID;
				} else {
					$urlrule = (SITE_BRANCH_DOMAIN ? $site_url.'/'.$this->module['dirname'].'/' : $this->module['url']).str_replace('{tag}', $code, $urlrule);
				}
			} else {
				$urlrule = (SITE_BRANCH_DOMAIN ? $site_url.'/'.$this->module['dirname'].'/' : $this->module['url']).str_replace('{tag}', $code, $urlrule).'&fid='.SITE_FID;
			}
			*/
		} else {
			/*
			// 此地址相对于网站根目录时
			if (strpos($urlrule, '/') === 0) {
				$url = ltrim(str_replace('{tag}', $code, $urlrule), '/');
				if (!$this->module['site'][SITE_ID]['domain']) {
					// 此模块未绑定域名才有效
					$urlrule = SITE_URL.$url;
				} else {
					$urlrule = $this->module['url'].str_replace('{tag}', $code, $urlrule);
				}
			} else {
				$urlrule = $this->module['url'].str_replace('{tag}', $code, $urlrule);
			}*/
		}

		$sql.= ' ORDER BY `updatetime` DESC';

		$this->template->assign(array(
			'tag' => $tag,
			'list' => $data,
			'tagsql' => $sql,
			'urlrule' => dr_tag_url($this->module, $code, '{page}'),
			'meta_title' => $tag.(SITE_SEOJOIN ? SITE_SEOJOIN : '_').$this->module['name'],
			'meta_keywords' => $this->module['setting']['seo']['meta_keywords'],
			'meta_description' => $this->module['setting']['seo']['meta_description']
		));
		$this->template->display('tag.html');
	}
	
	/**
     * 后台菜单
     */
	private function _menu() {
		$this->template->assign('menu', $this->get_menu_v3(array(
			fc_lang('标签管理') => array(APP_DIR.'/admin/tag/index', 'tag'),
			fc_lang('添加') => array(APP_DIR.'/admin/tag/add_js', 'plus'),
		)));
	}

    /**
     * 管理
     */
    protected function admin_index() {
		
		if ($this->input->post('action') == 'del') {
			!$this->is_auth(APP_DIR.'/admin/tag/del') &&  exit(dr_json(0, fc_lang('您无权限操作')));
			$id = $this->input->post('ids');
			$id && $this->db->where_in('id', $id)->delete($this->tag_model->tablename);
            $this->system_log('删除站点【#'.SITE_ID.'】模块【'.APP_DIR.'】Tag内容【#'.@implode(',', $id).'】'); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		
		// 数据库中分页查询
		$kw = $this->input->get('kw') ? $this->input->get('kw') : '';
		list($data, $param)	= $this->tag_model->limit_page($kw, max((int)$this->input->get('page'), 1), (int)$this->input->get('total'));

        // 菜单选择
        if (isset($_GET['kw'])) {
            $this->template->assign('menu', $this->get_menu_v3(array(
                fc_lang('标签管理') => array(APP_DIR.'/admin/tag/index/kw/', 'tag'),
                fc_lang('添加') => array(APP_DIR.'/admin/tag/add_js', 'plus'),
            )));
        } else {
            $this->_menu();
        }

		$this->template->assign(array(
			'mod' => $this->module,
			'list' => $data,
			'param'	=> $param,
			'pages'	=> $this->get_pagination(dr_url(APP_DIR.'/tag/index', $param), $param['total'])
		));
		$this->template->display('tag_index.html');
    }
	
	/**
     * 添加
     */
    protected function admin_add() {
	
		if (IS_POST) {
			$data = $this->input->post('data', TRUE);
			$result	= $this->tag_model->add($data);
			switch ($result) {
				
				case -1:
					exit(dr_json(0, fc_lang('数据错误'), 'name'));
					break;
					
				case -2:
					exit(dr_json(0, fc_lang('Tag名称重复了，请更换名称'), 'name'));
					break;
				
				default:
                    $this->system_log('添加【#'.SITE_ID.'】模块【'.APP_DIR.'】Tag内容【'.$data['name'].'】'); // 记录日志
					exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
					break;
			}
		}
		
		$this->template->assign(array(
			'data' => array()
		));
		$this->template->display('tag_add.html');
	}
	
	/**
     * 修改
     */
    protected function admin_edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->tag_model->get($id);
		!$data && exit(fc_lang('对不起，数据被删除或者查询不存在'));
		
		if (IS_POST) {
			
			$data = $this->input->post('data', TRUE);
			$result	= $this->tag_model->edit($id, $data);
			switch ($result) {
				
				case -1:
					exit(dr_json(0, fc_lang('数据错误')));
					break;
					
				case -2:
					exit(dr_json(0, fc_lang('Tag名称重复了，请更换名称')));
					break;
				
				default:
                    $this->system_log('修改【#'.SITE_ID.'】模块【'.APP_DIR.'】Tag内容【#'.$id.'】'); // 记录日志
					exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
					break;
			}
		}
		
		$this->template->assign(array(
			'id' => $id,
			'data' => $data
		));
		$this->template->display('tag_add.html');
	}
	
	/**
     * 删除
     */
    protected function admin_del() {
		$this->db->where('id', (int)$this->input->get('id'))->delete($this->tag_model->tablename);
        $this->system_log('删除【#'.SITE_ID.'】模块【'.APP_DIR.'】Tag内容【#'.$this->input->get('id').'】'); // 记录日志
		exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
	}
	
}