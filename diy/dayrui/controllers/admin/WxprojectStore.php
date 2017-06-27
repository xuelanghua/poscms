<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * v3.2
 */


class WxprojectStore extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('门店列表') => array('admin/wxprojectStore/store_list', 'sitemap'),
                fc_lang('添加') => array('admin/wxprojectStore/store_add', 'plus'),
                fc_lang('更新缓存') => array('admin/module/cache/dir/share', 'refresh'),
            )),
            'module' => $this->get_module(SITE_ID),
        ));
        $this->thumb = array(
            'thumb' => array(
                'name' => fc_lang('缩略图'),
                'ismain' => 1,
                'fieldtype' => 'File',
                'fieldname' => 'thumb',
                'setting' => array(
                    'option' => array(
                        'ext' => 'jpg,gif,png',
                        'size' => 10,
                    )
                )
            )
        );
        $this->content = array(
            'content' => array(
                'name' => fc_lang('单网页内容'),
                'ismain' => 1,
                'fieldtype' => 'Ueditor',
                'fieldname' => 'content',
                'setting' => array(
                    'option' => array(
                        'mode' => 1,
                        'height' => 300,
                        'width' => '100%'
                    )
                )
            )
        );
        $this->field = array();
        $field = $this->db
                       ->where('disabled', 0)
                       ->where('relatedid', SITE_ID)
                       ->where('relatedname', 'category-share')
                        ->order_by('displayorder ASC, id ASC')
                        ->get('field')
                        ->result_array();
        if ($field) {
            foreach ($field as $t) {
                $t['setting'] = dr_string2array($t['setting']);
                $this->field[$t['fieldname']] = $t;
            }
            unset($field);
        }
        $this->load->model('category_share_model');
    }

    /**
     * 产品分类
     */
    public function store_list() {


        // $page = (int)$this->input->get('page');
        // $field = array(
        //     'qrcode' => array(
        //         'ismain' => 1,
        //         'fieldtype' => 'File',
        //         'fieldname' => 'qrcode',
        //         'setting' => array(
        //             'option' => array(
        //                 'ext' => 'jpg',
        //                 'size' => 99999,
        //             )
        //         )
        //     ),
        // );
        // $data = $this->weixin_model->config('config');

        // if (IS_POST) {
        //     $update = $this->input->post('data');
        //     $this->validate_filter($field);
        //     $this->weixin_model->config('config', $update);
        //     $this->attachment_handle($this->uid, $this->weixin_model->prefix.'-0', $field, $data);
        //     $this->cache(1);
        //     $this->system_log('微信:修改公众号配置'); // 记录日志
        //     $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url($this->router->class.'/'.$this->router->method, array('page' => (int)$this->input->post('page'))), 1);
        // }

        // $this->template->assign(array(
        //     'page' => $page,
        //     'data' => $data,
        //     'field' => $field,
        // ));
        $this->template->display('wxproject/wxcategory_list.html');
    }

    public function store_add() {
        $this->template->display('wxproject/wxcategory_add.html');
    }

    // 产品列表
    public function store_edit() {

        // $page = (int)$this->input->get('page');
        // $data = $this->weixin_model->config('reply');

        // if (IS_POST) {
        //     $update = $this->input->post('data');
        //     if ($update['hello'] && !$this->db->where('id', $update['hello'])->count_all_results($this->weixin_model->prefix.'_keyword')) {
        //         $this->admin_msg('关键字ID('.$update['hello'].')不存在,请确保它存在于关键字库中');
        //     }
        //     if ($update['reply'] && !$this->db->where('id', $update['reply'])->count_all_results($this->weixin_model->prefix.'_keyword')) {
        //         $this->admin_msg('关键字ID('.$update['reply'].')不存在,请确保它存在于关键字库中');
        //     }
        //     $this->weixin_model->config('reply', $update);
        //     $this->cache(1);
        //     $this->system_log('微信:修改公众号配置'); // 记录日志
        //     $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url($this->router->class.'/'.$this->router->method, array('page' => (int)$this->input->post('page'))), 1);
        // }

        // $this->template->assign(array(
        //     'page' => $page,
        //     'data' => $data,
        //     'menu' => $this->get_menu_v3(array(
        //         fc_lang('系统回复设置') => array('admin/weixin/reply', 'cog'),
        //         fc_lang('更新缓存') => array('admin/weixin/cache', 'refresh'),
        //     )),
        // ));
        // $this->template->display('weixin_reply.html');

        echo "这里是goods_list";
    }

    // 门店列表
    public function store_del() {

        // $this->template->assign(array(
        //     'callback' => $_GET['callback']
        // ));
        // $this->template->display('weixin_emoji.html');

        echo '这里是store_list';
    }

       // 幻灯片列表
    public function slide_list() {

        // $this->template->assign(array(
        //     'callback' => $_GET['callback']
        // ));
        // $this->template->display('weixin_emoji.html');

        echo 'slide';
    }

    // 上传文件upload
    public function ajaxload() {

        $path = SYS_UPLOAD_PATH.'/weixin/'.date('Ym', SYS_TIME).'/';
        if (!is_dir($path)) {
            dr_mkdirs($path);
        }

        $this->load->library('upload', array(
            'max_size' => 102400,
            'overwrite' => FALSE,
            'file_name' => substr(md5(time()), rand(0, 20), 10),
            'upload_path' => $path,
            'allowed_types' => $this->input->get('ext'),
            'file_ext_tolower' => TRUE,
        ));
        if ($this->upload->do_upload('download')) {
            $info = $this->upload->data();
            $this->load->model('attachment_model');
            $this->attachment_model->siteid = SITE_ID;
            $result = $this->attachment_model->upload($this->uid, $info);
            if (!is_array($result)) {
                exit('0,'.$result);
            }
            list($id, $file, $_ext) = $result;
            echo json_encode(array(
                'status'=>1,
                'info'=>'上传成功',
                'data'=> '',
                'id'=> $id,
                'path'=> $file,
                'file'=> $file,
                'url'=> $file,
            ));exit;
        } else {
            echo json_encode(array('status'=>0, 'info'=>$this->upload->display_errors('', ''), 'data'=> ''));exit;
        }

    }

	// 推送新闻
	public function ts() {
		
		$mid = $this->input->get('mid');
		$ids = $this->input->get('ids');
		$ok = (int)$this->input->get('ok');
		$sb = (int)$this->input->get('sb');
		$value = (int)$this->input->get('value');
		if (!$ids) {
			$this->admin_msg(fc_lang('您还没有选择呢'));
		}
		
		$url = dr_url('weixin/ts', array('mid'=>$mid, 'value'=>$value, 'ids' => $ids));
		
		$page = (int)$this->input->get('page');
		if (!$page) {
			$this->admin_msg(fc_lang('正在开始推送到微信端...'), $url.'&page=1', 2);
		}
		
		$data = $this->site[SITE_ID]->where_in('id', $ids)->get(SITE_ID.'_'.$mid)->result_array();
		if (!$data) {
			$this->admin_msg(fc_lang('没有可用内容'));
		}
		
		// 全部粉丝
		$psize = 10; // 每次发送n个粉丝
		$allUser = $this->db->limit($psize, $psize * ($page - 1))->get($this->weixin_model->prefix.'_follow')->result_array();
		if (!$allUser) {
			// 发送完毕
			$this->admin_msg(fc_lang('发送完毕，成功('.$ok.')，失败('.$sb.')'), '', 1);
		}
		
		$articles = array();
        foreach ( $data as $vo ) {
            // 文章内容
            $art ['title'] = $vo ['title'];
            $art ['description'] = $vo ['description'];
            $art ['url'] = $vo ['url'];
            // 获取封面图片URL
            $art ['picurl'] = dr_get_file( $vo ['thumb'] );
            $articles [] = $art;
        }
		
		// 群发
		foreach ( $allUser as $k => $v ) {
			$k = $v['openid'];
			$param = array();
			$param ['news'] ['articles'] = $articles;
			$result = $this->weixin_model->_replyData($k, $param, 'news');
			if ($result ['status'] == 1) {
				$ok ++;
			} else {
				$sb ++;
			}
		}
		
		$this->admin_msg(fc_lang('正在开始推送到微信端('.$page.')...'), $url.'&page='.($page+1).'&ok='.$ok.'&sb='.$sb, 2);

	}

    /**
     * 缓存
     */
    public function cache($update = 0) {

        $this->weixin_model->cache(isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);

        ((int)$_GET['admin'] || $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
    }
}