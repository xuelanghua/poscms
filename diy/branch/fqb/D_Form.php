<?php



class D_Form extends M_Controller {

	public $form;
    protected $field;
    protected $uriprefix;
	
    /**
     * 构造函数（网站表单）
     */
    public function __construct() {
        parent::__construct();
		$name = str_replace('form_', '', $this->router->class);
        // 表单参数为数字时按id读取
        $this->form = is_numeric($name) ? $this->get_cache('form-'.SITE_ID, $name) : $this->get_cache('form-name-'.SITE_ID, $name);
        if (!$this->form) {
			if (!IS_ADMIN) {
				exit($this->call_msg(fc_lang('表单缓存不存在')));
			} elseif (IS_AJAX) {
				exit(dr_json(0, fc_lang('表单不存在，请更新表单缓存')));
			} else {
				$this->admin_msg(fc_lang('表单不存在，请更新表单缓存'));
			}
		}

        $this->load->model('form_model');
        if (IS_ADMIN) {
            $this->field = array(
                'author' => array(
                    'name' => fc_lang('录入作者'),
                    'ismain' => 1,
                    'fieldtype' => 'Text',
                    'fieldname' => 'author',
                    'setting' => array(
                        'option' => array(
                            'width' => 157,
                            'value' => $this->admin['username']
                        ),
                        'validate' => array(
                            'tips' => fc_lang('填写录入者的会员名称'),
                            'check' => '_check_member',
                            'required' => 1,
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
                    'fieldname' => 'inputip',
                    'fieldtype' => 'Text',
                    'setting' => array(
                        'option' => array(
                            'width' => 200,
                            'value' => $this->input->ip_address()
                        ),
                        'validate' => array(
                        )
                    )
                )
            );
            $this->uriprefix = 'admin/form_'.$this->form['table'].'/';
        }
    }
	
	/**
     * 内容维护
     */
	protected function _listc() {

		//!$this->is_auth('admin/form/listc') && (IS_AJAX ? exit(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')) : $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')));

		if (IS_POST && $this->input->post('action')) {
            $table = $this->form_model->prefix.'_'.$this->form['table'];
			if ($this->input->post('action') == 'del') {
				// 删除
				$this->load->model('attachment_model');
				$_ids = $this->input->post('ids');
				foreach ($_ids as $id) {
                    $row = $this->form_model->link->where('id', (int)$id)->get($table)->row_array();
                    if ($row) {
                        $this->form_model->link->where('id', (int)$id)->delete($table);
                        $this->form_model->link->where('id', (int)$id)->delete($table.'_data_'.(int)$row['tableid']);
                        $this->attachment_model->delete_for_table($table.'-'.$id);
                        $this->system_log('删除站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.$id.'】'.$row['title']); // 记录日志
                    }
				}
            } elseif ($this->input->post('action') == 'order') {
				// 修改
				$_ids = $this->input->post('ids');
				$_data = $this->input->post('data');
				foreach ($_ids as $id) {
					$this->form_model->link->where('id', (int)$id)->update($table, $_data[$id]);
				}
                $this->system_log('排序站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.@implode(',', $_ids).'】'); // 记录日志
				unset($_ids, $_data);
			}
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}

        // 重置页数和统计
		IS_POST && $_GET['page'] = $_GET['total'] = 0;

		// 根据参数筛选结果
		$param = $this->input->get(NULL);
		unset($param['s'],$param['c'],$param['m'],$param['d'],$param['page']);
		if ($this->input->post('search')) {
			$search = $this->input->post('data');
			$param['keyword'] = $search['keyword'];
			$param['start'] = $search['start'];
			$param['end'] = $search['end'];
			$param['field'] = $search['field'];
		}

		// 数据库中分页查询
		list($data, $total)	= $this->form_model->limit_page(
            $this->form['table'],
            $param,
            max((int)$_GET['page'], 1),
            (int)$_GET['total']
        );
		$param['total'] = $total;

        $tpl = APPPATH.'templates/admin/form_listc_'.$this->form['table'].'.html';
		$this->template->assign(array(
			'mid' => $this->mid,
            'tpl' => str_replace(FCPATH, '/', $tpl),
			'menu' => $this->get_menu_v3(array(
				fc_lang($this->form['name']) => array($this->uriprefix.'index', $this->form['setting']['icon'] ? str_replace('fa fa-', '', $this->form['setting']['icon']) : 'table'),
				fc_lang('添加') => array($this->uriprefix.'add', 'plus'),
				fc_lang('发布预览') => array(SITE_URL.'index.php?c=form_'.$this->form['table'].'" target="_blank', 'send'),
			)),
			'list' => $data,
			'form' => 'form_'.$this->form['table'],
			'param'	=> $param,
			'total' => $total,
            'field' => $this->form['field'] + $this->field,
			'pages'	=> $this->get_pagination(dr_url($this->router->class.'/index', $param), $param['total']),
		));

        $this->template->display(is_file($tpl) ? basename($tpl) : 'form_listc.html');
	}
	
	/**
     * 添加内容
     */
	protected function _addc() {

		//!$this->is_auth('admin/form/listc') && (IS_AJAX ? exit(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')) : $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')));

		if (IS_POST) {
			
			$data = $this->validate_filter($this->form['field'] + $this->field);
			
			// 验证出错信息
			if (isset($data['error'])) {
				$error = $data;
				$data = $this->input->post('data', TRUE);
			} else {
				// 设定文档默认值
                $data[1]['displayorder'] = 0;
                $data[1]['uid'] = $data[0]['uid'] = get_member_id($data[1]['author']);
				// 发布文档
				if (($id = $this->form_model->new_addc($this->form['table'], $data)) != FALSE) {
					// 附件归档到文档
					$this->attachment_handle($this->uid, $this->form_model->prefix.'_'.$this->form['table'].'-'.$id, $this->form['field']);
                    $this->system_log('添加站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.$id.'】'); // 记录日志
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_url($this->router->class.'/index'), 1);
				}
			}
            $data = $data[0] ? array_merge($data[1], $data[0]) : $data[1];
			unset($data['id']);
		}

        $tpl = APPPATH.'templates/admin/form_addc_'.$this->form['table'].'.html';
		$this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
			'menu' => $this->get_menu_v3(array(
				fc_lang($this->form['name']) => array($this->uriprefix.'index', $this->form['setting']['icon'] ? str_replace('fa fa-', '', $this->form['setting']['icon']) : 'table'),
				fc_lang('添加') => array($this->uriprefix.'add', 'plus'),
			)),
            'data' => $data,
			'error' => $error,
			'myfield' => $this->field_input($this->form['field'] + $this->field, $data)
		));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'form_addc.html');
	}
	
	/**
     * 修改内容
     */
	protected function _editc() {

		//!$this->is_auth('admin/form/listc') && (IS_AJAX ? exit(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')) : $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')));

		$id = (int)$this->input->get('id');
		$table = $this->form_model->prefix.'_'.$this->form['table'];

        // 获取表单数据
		$data = $this->form_model->get_data($id, $table);
		!$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
			
		if (IS_POST) {
			$post = $this->validate_filter($this->form['field'] + $this->field);
			// 验证出错信息
			if (isset($post['error'])) {
				$error = $post;
				$data = $this->input->post('data', TRUE);
			} else {
				// 发布文档
                $post[1]['uid'] = $post[0]['uid'] = get_member_id($post[1]['author']);
                if ($this->form_model->new_editc($id, $this->form['table'], $data['tableid'], $post)) {
					// 附件归档到文档
					$this->attachment_handle($this->uid, $table.'-'.$id, $this->form['field']);
                    $this->system_log('修改站点【#'.SITE_ID.'】表单【'.$this->form['table'].'】内容【#'.$id.'】'); // 记录日志
					$this->member_msg(fc_lang('操作成功，正在刷新...'), dr_url($this->router->class.'/index'), 1);
				}
			}
			$data = $post[0] ? array_merge($post[1], $post[0]) : $post[1];
			unset($data['id']);
		}

        $tpl = APPPATH.'templates/admin/form_addc_'.$this->form['table'].'.html';
		$this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
			'menu' => $this->get_menu_v3(array(
				fc_lang($this->form['name']) => array($this->uriprefix.'index', $this->form['setting']['icon'] ? str_replace('fa fa-', '', $this->form['setting']['icon']) : 'table'),
				fc_lang('添加') => array($this->uriprefix.'add', 'plus'),
				fc_lang('修改') => array($this->uriprefix.'edit/id/'.$id, 'edit'),
			)),
            'data' => $data,
			'error' => $error,
			'myfield' => $this->field_input($this->form['field'] + $this->field, $data)
		));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'form_addc.html');
	}


	/**
     * 查看内容
     */
	public function show() {

        $id = (int)$this->input->get('id');
        $table = $this->form_model->prefix.'_'.$this->form['table'];

        // 获取表单数据
        $data = $this->form_model->get_data($id, $table);

        if (IS_ADMIN) {

            //!$this->is_auth('admin/form/listc') && (IS_AJAX ? exit(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')) : $this->admin_msg(fc_lang('抱歉！您无权限操作(%s)', 'admin/form/listc')));

            !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

            $tpl = APPPATH.'templates/admin/form_show_'.$this->form['table'].'.html';
            $this->template->assign(array(
                'tpl' => str_replace(FCPATH, '/', $tpl),
                'menu' => $this->get_menu_v3(array(
                    fc_lang($this->form['name']) => array($this->uriprefix.'index', $this->form['setting']['icon'] ? str_replace('fa fa-', '', $this->form['setting']['icon']) : 'table'),
                    fc_lang('添加') => array($this->uriprefix.'add', 'plus'),
                    fc_lang('查看') => array($this->uriprefix.'show/id/'.$id, 'search'),
                )),
                'data' => $data,
            ));
            $this->template->display(is_file($tpl) ? basename($tpl) : 'form_show.html');
        } else {
            !$data && $this->msg(fc_lang('表单内容不存在'));
            $tpl = dr_tpl_path('form_'.$this->form['table'].'_show.html');
            $this->template->assign($data);
            $this->template->assign(array(
                'form' => $this->form,
                'meta_title' => $this->form['name'].SITE_SEOJOIN.SITE_NAME
            ));
            $this->template->display(is_file($tpl) ? basename($tpl) : 'form_show.html');
        }
	}

	/**
     * 查看内容
     */
	public function Page() {

        $tpl = dr_tpl_path('form_'.$this->form['table'].'_page.html');
        $page = max(1, intval($_GET['page']));

        $this->template->assign(array(
            'page' => $page,
            'form' => $this->form,
            'table' => $this->form_model->prefix.'_'.$this->form['table'],
            'form_table' => $this->form['table'],
            'form_url' => 'index.php?c=form_'.$this->form['table'].'&m=show&id=',
            'urlrule' => 'index.php?c=form_'.$this->form['table'].'&m=page&page=[page]',
            'meta_title' => ($page > 1 ? fc_lang('第%s页', $page).SITE_SEOJOIN : '').$this->form['name'].SITE_SEOJOIN.SITE_NAME
        ));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'form_page.html');
	}
	
	/**
     * 提交内容
     */
    protected function _post() {

		!$this->form['setting']['post'] && (IS_POST ? exit($this->call_msg(fc_lang('此表单没有开启前端提交功能'))) : $this->msg(fc_lang('此表单没有开启前端提交功能')));

		if (IS_POST) {

			$this->form['setting']['code'] && !$this->check_captcha('code') && exit($this->call_msg(fc_lang('验证码不正确')));
            
			$data = $this->validate_filter($this->form['field']);
				
			// 验证出错信息
			isset($data['error']) && exit($this->call_msg($data['msg']));

            $data[1]['uid'] =$data[0]['uid'] = $this->uid;
			$data[1]['author'] = $this->uid ? $this->member['username'] : 'guest';
			$data[1]['inputip'] = $this->input->ip_address();
			$data[1]['inputtime'] = SYS_TIME;
			$data[1]['displayorder'] = 0;
			
			$this->load->model('form_model');
			$data[1]['id'] = $id = $this->form_model->new_addc($this->form['table'], $data);
			
			if ($this->form['setting']['send'] && $this->form['setting']['template']) {
				// 兼容php5.5
				if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
                    $rep = new php5replace($data[1]+$data[0]);
					$content = preg_replace_callback('#{(.*)}#U', array($rep, 'php55_replace_data'), $this->form['setting']['template']);
                    $content = preg_replace_callback('#{([a-z_0-9]+)\((.*)\)}#Ui', array($rep, 'php55_replace_function'), $content);
                    unset($rep);
                } else {
                    @extract($data[1]+$data[0]);
					$content = preg_replace("/{(.*)}/Ue", "\$\\1", $this->form['setting']['template']);
                    $content = preg_replace('#{([a-z_0-9]+)\((.*)\)}#Uie', "\\1(dr_safe_replace('\\2'))", $content);
				}
				$this->sendmail_queue($this->form['setting']['send'], fc_lang('【%s】通知 [来自：'.SITE_NAME.']', $this->form['name']), nl2br($content));
			}
			$this->call_msg(fc_lang('操作成功'), 1, $data);
		} else {
            $tpl = dr_tpl_path('form_'.$this->form['table'].'.html');
			$this->template->assign(array(
				'form' => $this->form,
				'code' => $this->form['setting']['code'],
				'myfield' => $this->field_input($this->form['field']),
				'meta_title' => $this->form['name'].SITE_SEOJOIN.SITE_NAME
			));
			$this->template->display(is_file($tpl) ? basename($tpl) : 'form.html');
		}
    }
	
	/**
     * 回调方法 有问题
     */
	public function call_msg($msg, $code = 0, $data = array()) {

		IS_API_AUTH && exit($this->callback_json(array(
			'error' => $msg,
			'code' => $code,
		)));

        $url = $this->form['setting']['rt_url'];
		!$url && $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		
		if (IS_AJAX) {
			exit(dr_json($code, $msg, $url)); // AJAX请求时返回json格式
		} else {
			if ($code) {
				$this->msg($msg, $url, 1); // 成功
			} else {
				$this->msg($msg); // 错误
			}
		}
	}
	
}