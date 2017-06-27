<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * v3.2
 */


require FCPATH.'branch/fqb/D_Admin_Table.php';

class Wmaterial extends D_Admin_Table {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('weixin_model');
        $this->mydb = $this->db; // 数据库
    }

    /**
     * 文本素材管理
     */
    public function index() {

        $this->myid = 'id'; // 主键
        $this->tfield = 'inputtime'; // 时间字段用于搜索和排序
        $this->mytable = $this->weixin_model->prefix.'_material_text'; // 表名
        $field = array(
            'content' => array(
                'name' => '内容',
                'ismain' => 1,
                'fieldname' => 'content',
                'fieldtype' => 'Text',
            ),
        ); // 搜索字段
        $this->myfield = array(
            'uid' => array(
                'ismain' => 1,
                'fieldname' => 'uid',
                'fieldtype' => 'Text',
            ),
            'username' => array(
                'ismain' => 1,
                'fieldname' => 'username',
                'fieldtype' => 'Text',
            ),
            'inputtime' => array(
                'ismain' => 1,
                'fieldname' => 'inputtime',
                'fieldtype' => 'Date',
            ),
        ) + $field;

        $action = $this->input->get('action');
        if ($action == 'add') {

            $_POST['data']['uid'] = $this->uid;
            $_POST['data']['username'] = $this->member['username'];
            $_POST['data']['inputtime'] = SYS_TIME;

            $this->_add();

            $this->template->assign(array(
                'field' => $this->myfield,
                'menu' => $this->get_menu_v3(array(
                    '文本素材' => array('admin/wmaterial/index', 'file-text'),
                    '添加' => array('admin/wmaterial/index/action/add', 'plus'),
                )),
            ));
            $this->template->display($this->router->class.'_'.$this->router->method.'_add.html');
        } elseif ($action == 'edit') {

            unset($this->myfield['uid'], $this->myfield['username'], $this->myfield['inputtime']);

            $this->_edit();

            $this->template->assign(array(
                'field' => $this->myfield,
                'menu' => $this->get_menu_v3(array(
                    '文本素材' => array('admin/wmaterial/index', 'file-text'),
                    '添加' => array('admin/wmaterial/index/action/add', 'plus'),
                    '修改' => array('admin/wmaterial/index/action/edit/id/'.$_GET['id'], 'edit'),
                )),
            ));
            $this->template->display($this->router->class.'_'.$this->router->method.'_add.html');
        } else {

            if (IS_POST && $this->input->post('action') == 'del') {
                // 执行的动作 删除
                $this->load->model('attachment_model');
                $_ids = $this->input->post('ids');
                foreach ($_ids as $id) {
                    $row = $this->mydb->where('id', (int)$id)->get($this->mytable)->row_array();
                    if ($row) {
                        $this->mydb->where('id', (int)$id)->delete($this->mytable);
                        $this->attachment_model->delete_for_table($this->mytable.'-'.$id);
                        // 删除关联
                        $this->system_log('删除站点【#'.SITE_ID.'】微信文本素材【#'.$id.'】'); // 记录日志
                    }
                }
                exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
            }

            $this->_index();

            $this->template->assign(array(
                'field' => $field,
                'menu' => $this->get_menu_v3(array(
                    '文本素材' => array('admin/wmaterial/index', 'file-text'),
                    '添加' => array('admin/wmaterial/index/action/add', 'plus'),
                )),
            ));
            $this->template->display($this->router->class.'_'.$this->router->method.'.html');
        }
    }

    /**
     * 图片素材管理
     */
    public function tp() {

        $this->myid = 'id'; // 主键
        $this->tfield = 'inputtime'; // 时间字段用于搜索和排序
        $this->mytable = $this->weixin_model->prefix.'_material_image'; // 表名
        $field = array(
            'file' => array(
                'ismain' => 1,
                'fieldname' => 'file',
                'fieldtype' => 'File',
            ),
        ); // 搜索字段
        $this->myfield = array(
            'uid' => array(
                'ismain' => 1,
                'fieldname' => 'uid',
                'fieldtype' => 'Text',
            ),
            'username' => array(
                'ismain' => 1,
                'fieldname' => 'username',
                'fieldtype' => 'Text',
            ),
            'media_id' => array(
                'ismain' => 1,
                'fieldname' => 'media_id',
                'fieldtype' => 'Text',
            ),
            'wechat_url' => array(
                'ismain' => 1,
                'fieldname' => 'wechat_url',
                'fieldtype' => 'Text',
            ),
            'inputtime' => array(
                'ismain' => 1,
                'fieldname' => 'inputtime',
                'fieldtype' => 'Date',
            ),
        ) + $field;

        $action = $this->input->get('action');
        if ($action == 'add') {

            $id = (int)$this->input->post('id');
            if (!$id) {
                echo json_encode(array('status'=>0, 'code'=>'参数不对', 'data'=> ''));exit;
            }

            $_POST['data']['uid'] = $this->uid;
            $_POST['data']['username'] = $this->member['username'];
            $_POST['data']['inputtime'] = SYS_TIME;
            $_POST['data']['file'] = $id;
            $_POST['data']['media_id'] = '0';
            $_POST['data']['wechat_url'] = '';

            $this->_add();
            exit;
        } elseif ($action == 'download') {
            // 一键下载到服务器
            if (!$_GET['todo']) {
                $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
            }
            $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . dr_get_access_token ();
            $param ['type'] = 'image';
            $param ['offset'] = intval($_GET['offset']);
            $param ['count'] = 10;
            $list = dr_post_data ($url, $param);
            if (isset($list['errcode']) && $list['errcode']!= 0) {
                $this->admin_msg ( dr_error_msg ( $list ) );
            }
            if (empty($list['item'])) {
                $this->admin_msg('操作成功', dr_url($this->router->class.'/'.$this->router->method), 1);
                $this->system_log('站点【#'.SITE_ID.'】微信图片素材从公众平台下载到本地'); // 记录日志
            }
            foreach ( $list ['item'] as $item ) {
                $media_id = $item ['media_id'];
                // 判断是否存在
                if ($this->mydb->where('media_id', $media_id)->count_all_results($this->mytable)) {
                    continue;
                }
                if ($item ['url']) {
                    $file = $this->_down_save_file($media_id, $item ['url'], 'jpg');
                    if ($file) {
                        // 保存成功
                        $this->mydb->insert($this->mytable, array(
                            'uid' => $this->uid,
                            'username' => $this->member['username'],
                            'file' => (int)$file,
                            'inputtime' => SYS_TIME,
                            'media_id' => $item ['media_id'],
                            'wechat_url' => $item ['url'],
                        ));
                        $id = $this->mydb->insert_id();
                        // 替换附件
                        $this->attachment_model->replace_attach($this->uid, $this->db->dbprefix.$this->mytable.'-'.$id, array($file));
                    }
                }
            }

            $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'offset' => $param ['offset'] + $list ['item_count'], 'todo' => 1)), 1, 0);

        } elseif ($action == 'upload') {
            // 一键上传到服务器
            if (!$_GET['todo']) {
                $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
            }
            $list = $this->mydb->where('media_id', '0')->limit(5)->get($this->mytable)->result_array();
            if (!$list) {
                $this->admin_msg('操作成功', dr_url($this->router->class.'/'.$this->router->method), 1);
                $this->system_log('站点【#'.SITE_ID.'】微信图片素材同步到公众平台'); // 记录日志
            }
            foreach ($list as $t) {
                $token = dr_get_access_token();
                $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $token;
                $info = get_attachment($t['file']);
                if (!$info) {
                    // 附件不存在删除
                    $this->mydb->where('id', (int)$t['id'])->delete($this->mytable);
                    $this->system_log('由于图片不存在, 删除站点【#'.SITE_ID.'】微信图片素材【#'.$t['id'].'】'); // 记录日志
                }
                if (isset($info['remote']) && $info['remote']) {
                    $file = WEBPATH.'cache/attach/'.time().'_'.basename($info['attachment']);
                    file_put_contents($file, dr_catcher_data($info['attachment']));
                } else {
                    $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
                }
                $res = dr_post_data($url, array(
                    'type' => 'image',
                    'media' => '@' . $file,
                ), true);
                if (isset ($res['errcode']) && $res['errcode'] != 0) {
                    $this->admin_msg(dr_error_msg($res, '图片(#'.$t['id'].')上传'));
                    exit();
                }
                // 更新内容
                $this->mydb->where('id', intval($t['id']))->update($this->mytable, array(
                    'media_id' => $res['media_id'],
                    'wechat_url' => $res['url'],
                ));
            }
            $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
        } elseif ($action == 'del') {
            $id = (int)$this->input->post('id');
            $row = $this->mydb->where('id', (int)$id)->get($this->mytable)->row_array();
            if ($row) {
                $this->mydb->where('id', (int)$id)->delete($this->mytable);
                $this->load->model('attachment_model');
                $this->attachment_model->delete_for_table($this->mytable.'-'.$id);
                // 删除关联
                $this->system_log('删除站点【#'.SITE_ID.'】微信图片素材【#'.$id.'】'); // 记录日志
            }
            exit;
        } else {

            $this->_index();

            $this->template->assign(array(
                'menu' => $this->get_menu_v3(array(
                    '图片素材' => array('admin/wmaterial/'.$this->router->method, 'file-picture-o'),
                )),
            ));
            $this->template->display($this->router->class.'_'.$this->router->method.'.html');
        }
    }


    /**
     * 图文素材管理
     */
    public function tw()
    {

        $this->myid = 'id'; // 主键
        $this->tfield = 'inputtime'; // 时间字段用于搜索和排序
        $this->mytable = $this->weixin_model->prefix . '_material_news'; // 表名
        $field = array(
            'title' => array(
                'name' => '标题',
                'ismain' => 1,
                'fieldname' => 'title',
                'fieldtype' => 'Text',
            ),
            'author' => array(
                'name' => '作者',
                'ismain' => 1,
                'fieldname' => 'author',
                'fieldtype' => 'Text',
            ),
            'description' => array(
                'name' => '摘要',
                'ismain' => 1,
                'fieldname' => 'description',
                'fieldtype' => 'Text',
            ),
            'content' => array(
                'name' => '内容',
                'ismain' => 1,
                'fieldname' => 'content',
                'fieldtype' => 'Ueditor',
            ),
        ); // 搜索字段
        $this->myfield = array(
                'uid' => array(
                    'ismain' => 1,
                    'fieldname' => 'uid',
                    'fieldtype' => 'Text',
                ),
                'thumb' => array(
                    'ismain' => 1,
                    'fieldname' => 'thumb',
                    'fieldtype' => 'File',
                ),
                'username' => array(
                    'ismain' => 1,
                    'fieldname' => 'username',
                    'fieldtype' => 'Text',
                ),
                'linkurl' => array(
                    'ismain' => 1,
                    'fieldname' => 'linkurl',
                    'fieldtype' => 'Text',
                ),
                'group_id' => array(
                    'ismain' => 1,
                    'fieldname' => 'group_id',
                    'fieldtype' => 'Text',
                ),
                'thumb_media_id' => array(
                    'ismain' => 1,
                    'fieldname' => 'thumb_media_id',
                    'fieldtype' => 'Text',
                ),
                'media_id' => array(
                    'ismain' => 1,
                    'fieldname' => 'media_id',
                    'fieldtype' => 'Text',
                ),
                'inputtime' => array(
                    'ismain' => 1,
                    'fieldname' => 'inputtime',
                    'fieldtype' => 'Date',
                ),
            ) + $field;

        $action = $this->input->get('action');
        if ($action == 'add') {

            if (IS_POST) {

                $post = json_decode($_POST['dataStr'], true);
                if (!$post) {
                    exit(dr_json(0, '请填写完整的内容信息'));
                }
                $group_id = 0;
                foreach ($post as $row) {
                    if ($row) {
                        $save = $_POST = array(); // 初始化form
                        foreach ($row as $t) {
                            $save[$t['name']] = $t['value'];
                        }
                        $save['thumb'] = $save['cover_id'];
                        unset($save['cover_id']);
                        $save['linkurl'] = $save['link'];
                        unset($save['link']);
                        $save['description'] = $save['intro'];
                        unset($save['intro']);
                        $save['group_id'] = $group_id;
                        $save['uid'] = $this->uid;
                        $save['username'] = $this->member['username'];
                        $save['thumb_media_id'] = '0';
                        $save['media_id'] = '0';
                        $save['url'] = '';
                        $save['inputtime'] = SYS_TIME;
                        // 保存数据库
                        $this->mydb->insert($this->mytable, $save);
                        $id = $this->mydb->insert_id();
                        if ($group_id == 0) {
                            $group_id = $id;
                            $this->mydb->where('id', $id)->update($this->mytable, array('group_id' => $id)); // 更新组id
                        }
                        // 存储附件和归档
                        $_POST['data']['thumb'] = $save['thumb'];
                        $_POST['data']['content'] = $save['content'];
                        $this->validate_filter($this->myfield);
                        $this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable) . '-' . $id, $this->myfield);
                    }
                }
                exit(dr_json(1, '操作成功'));
            }

            $this->template->assign(array(
                'field' => $this->myfield,
                'menu' => $this->get_menu_v3(array(
                    '图文素材' => array('admin/wmaterial/' . $this->router->method . '', 'file-picture-o'),
                    '添加' => array('admin/wmaterial/' . $this->router->method . '/action/add', 'plus'),
                )),
            ));
            $this->template->display($this->router->class . '_' . $this->router->method . '_add.html');
        } elseif ($action == 'edit') {

            $gid = intval($this->input->get('gid'));
            $data = $this->mydb->where('group_id', $gid)->order_by('id asc')->get($this->mytable)->result_array();

            if (IS_POST) {

                $post = json_decode($_POST['dataStr'], true);
                if (!$post) {
                    exit(dr_json(0, '请填写完整的内容信息'));
                }
                $row = array();
                foreach ($data as $t) {
                    $row[$t['id']] = $t;
                }
                foreach ($post as $row) {
                    if ($row) {
                        $save = $_POST = array(); // 初始化form
                        foreach ($row as $t) {
                            $save[$t['name']] = $t['value'];
                        }
                        $id = $save['id'];
                        unset($save['id']);
                        $save['thumb'] = $save['cover_id'];
                        unset($save['cover_id']);
                        $save['linkurl'] = $save['link'];
                        unset($save['link']);
                        $save['description'] = $save['intro'];
                        unset($save['intro']);
                        // 保存数据库
                        if (!$id) {
                            $save['group_id'] = $gid;
                            $save['uid'] = $this->uid;
                            $save['username'] = $this->member['username'];
                            $save['thumb_media_id'] = '0';
                            $save['media_id'] = '0';
                            $save['url'] = '';
                            $save['inputtime'] = SYS_TIME;
                            // 保存数据库
                            $this->mydb->insert($this->mytable, $save);
                            $id = $this->mydb->insert_id();
                        } else {
                            $this->mydb->where('id', $id)->update($this->mytable, $save);
                        }
                        // 存储附件和归档
                        $_POST['data']['id'] = $id;
                        $_POST['data']['thumb'] = $save['thumb'];
                        $_POST['data']['content'] = $save['content'];
                        $this->validate_filter($this->myfield);
                        $this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable) . '-' . $id, $this->myfield, $row[$id]);
                    }
                }
                exit(dr_json(1, '操作成功'));
            }

            $this->template->assign(array(
                'data' => $data,
                'menu' => $this->get_menu_v3(array(
                    '图文素材' => array('admin/wmaterial/' . $this->router->method . '', 'file-picture-o'),
                    '添加' => array('admin/wmaterial/' . $this->router->method . '/action/add', 'plus'),
                    '修改' => array('admin/wmaterial/' . $this->router->method . '/action/edit/gid/' . $gid, 'edit'),
                )),
            ));
            $this->template->display($this->router->class . '_' . $this->router->method . '_add.html');
        } elseif ($action == 'del') {

            if ($_POST) {
                $data = $this->mydb->where('id', (int)$_POST['id'])->get($this->mytable)->result_array();
            } else {
                $data = $this->mydb->where('group_id', (int)$_GET['gid'])->get($this->mytable)->result_array();
            }

            if ($data) {
                $this->load->model('attachment_model');
                foreach ($data as $row) {
                    $id = (int)$row['id'];
                    $this->mydb->where('id', $id)->delete($this->mytable);
                    $this->attachment_model->delete_for_table($this->mytable . '-' . $id);
                    // 删除关联
                    $this->system_log('删除站点【#' . SITE_ID . '】微信图文素材【#' . $id . '】'); // 记录日志
                }
            }

            if ($_POST) {
                exit;
            } else {
                $this->admin_msg('操作成功', dr_url('wmaterial/tw'), 1);
            }
            exit;
        } elseif ($action == 'download') {
            // 一键下载到服务器
            if (!$_GET['todo']) {
                $this->admin_msg('正在同步中...', dr_url($this->router->class . '/' . $this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
            }
            $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . dr_get_access_token();
            $param ['type'] = 'news';
            $param ['offset'] = intval($_GET['offset']);
            $param ['count'] = 10;
            $list = dr_post_data($url, $param);
            if (isset($list['errcode']) && $list['errcode'] != 0) {
                $this->admin_msg(dr_error_msg($list));
            }
            if (empty($list['item'])) {
                $this->admin_msg('操作成功', dr_url($this->router->class . '/' . $this->router->method), 1);
                $this->system_log('站点【#' . SITE_ID . '】微信图文素材从公众平台下载到本地'); // 记录日志
            }
            foreach ($list ['item'] as $item) {
                $media_id = $item ['media_id'];
                // 判断是否存在
                if ($this->mydb->where('media_id', $media_id)->count_all_results($this->mytable)) {
                    continue;
                }
                $ids = array();
                //echo '<pre>';print_r($item);exit;
                foreach ($item ['content'] ['news_item'] as $vo) {
                    $data = array();
                    $data['title'] = $vo ['title'];
                    $data['author'] = $vo ['author'];
                    $data['description'] = $vo ['digest'];
                    $data['content'] = $vo ['content'];
                    $data['thumb_media_id'] = $vo ['thumb_media_id'];
                    $data['url'] = $vo ['url'];
                    $data['media_id'] = $media_id;
                    $data['thumb'] = $this->_download_imgage($data ['thumb_media_id']);
                    $data['group_id'] = 0;
                    $data['uid'] = $this->uid;
                    $data['linkurl'] = '';
                    $data['username'] = $this->member['username'];
                    $data['inputtime'] = SYS_TIME;
                    // 存储附件和归档
                    $this->mydb->insert($this->mytable, $data);
                    $id = $this->mydb->insert_id();
                    $_POST = array();
                    $_POST['data']['id'] = $id;
                    $_POST['data']['thumb'] = $data['thumb'];
                    $_POST['data']['content'] = $data['content'];
                    $this->validate_filter($this->myfield);
                    $this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable) . '-' . $id, $this->myfield, $data);
                    $ids[] = $id;
                }
                // 更新groupid
                $this->mydb->where_in('id', $ids)->update($this->mytable, array(
                    'group_id' => $ids [0]
                ));
            }

            $this->admin_msg('正在同步中...', dr_url($this->router->class . '/' . $this->router->method, array('action' => $action, 'offset' => $param ['offset'] + $list ['item_count'], 'todo' => 1)), 1, 0);

        } elseif ($action == 'upload') {
            // 一键上传到服务器
            if (!$_GET['todo']) {
                $this->admin_msg('正在同步中...', dr_url($this->router->class . '/' . $this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
            }
            $list = $this->mydb->select('*,count(id) as count')->where('media_id', '0')->group_by('group_id')->order_by('id desc')->limit(5)->get($this->mytable)->result_array();
            if (!$list) {
                $this->admin_msg('操作成功', dr_url($this->router->class . '/' . $this->router->method), 1);
                $this->system_log('站点【#' . SITE_ID . '】微信图文素材同步到公众平台'); // 记录日志
            }


            $ids = $gids = array();
            foreach ($list as $vo) {
                $ids [] = $vo ['id'];
                $gids [] = $vo ['group_id'];
            }
            $map2 ['id'] = array(
                'not in',
                $ids
            );
            $map2 ['group_id'] = array(
                'in',
                $gids
            );
            $child = $this->mydb->where('id NOT IN (' . implode(',', $ids) . ')')->where_in('group_id', $gids)->order_by('id asc')->get($this->mytable)->result_array();
            empty ($child) || $list = array_merge($list, $child);

            foreach ($list as $vo) {
                $data ['title'] = $vo ['title'];
                $data ['thumb_media_id'] = empty ($vo ['thumb_media_id']) ? $this->_thumb_media_id($vo) : $vo ['thumb_media_id'];
                $data ['author'] = $vo ['author'];
                $data ['digest'] = $vo ['description'];
                $data ['show_cover_pic'] = 1;
                $data ['content'] = str_replace('"', '\'', $vo ['content']);
                $data ['content_source_url'] = dr_weixin_show_url($vo['id']);
                $articles [$vo ['group_id']] [] = $data;
            }

            $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=' . dr_get_access_token();

            foreach ($articles as $group_id => $art) {
                $param ['articles'] = $art;
                $res = dr_post_data($url, $param);
                if ($res ['errcode'] != 0) {
                    $this->admin_msg(dr_error_msg($res));
                } else {
                    $this->mydb->where('group_id', $group_id)->update($this->mytable, array(
                        'media_id' => $res ['media_id']
                    ));
                    $newsUrl = $this->_news_url($res ['media_id']);
                    foreach ($art as $a) {
                        $this->mydb->where('group_id', $group_id)->where('title', $a ['title'])->update($this->mytable, array(
                            'url' => $newsUrl [$a ['title']]
                        ));
                    }
                }
            }
            $this->admin_msg('正在同步中...', dr_url($this->router->class . '/' . $this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
        } else {

            $this->mygroup = 'group_id';
            $data = $this->_index();
            if ($data) {
                foreach ($data as $i => $t) {
                    if ($t['count'] > 1) {
                        $data[$i]['child'] = $this->mydb->where('id<>' . $t['id'])->where('group_id', $t['id'])->order_by('id asc')->get($this->mytable)->result_array();
                    }
                }
            }

            $this->template->assign(array(
                'list' => $data,
                'field' => $field,
                'menu' => $this->get_menu_v3(array(
                    '图文素材' => array('admin/wmaterial/' . $this->router->method . '', 'file-picture-o'),
                    '添加' => array('admin/wmaterial/' . $this->router->method . '/action/add', 'plus'),
                )),
            ));
            $this->template->display($this->router->class . '_' . $this->router->method . '.html');
        }
    }

        /**
         * 语言素材管理
         */
        public function yy() {

            $this->myid = 'id'; // 主键
            $this->tfield = 'inputtime'; // 时间字段用于搜索和排序
            $this->mytable = $this->weixin_model->prefix.'_material_file'; // 表名
            $field = array(
                'title' => array(
                    'name' => '标题',
                    'ismain' => 1,
                    'fieldname' => 'title',
                    'fieldtype' => 'Text',
                ),

            ); // 搜索字段
            $this->myfield = array(
                    'is_video' => array(
                        'ismain' => 1,
                        'fieldname' => 'is_video',
                        'fieldtype' => 'Text',
                    ),
                    'description' => array(
                        'name' => '摘要',
                        'ismain' => 1,
                        'fieldname' => 'description',
                        'fieldtype' => 'Text',
                    ),

                    'uid' => array(
                        'ismain' => 1,
                        'fieldname' => 'uid',
                        'fieldtype' => 'Text',
                    ),
                    'file' => array(
                        'ismain' => 1,
                        'fieldname' => 'file',
                        'fieldtype' => 'File',
                        'setting' => array(
                            'option' => array(
                                'ext' => 'mp3,wma,wav,amr',
                                'size' => 30,
                            )
                        )
                    ),
                    'username' => array(
                        'ismain' => 1,
                        'fieldname' => 'username',
                        'fieldtype' => 'Text',
                    ),
                    'wechat_url' => array(
                        'ismain' => 1,
                        'fieldname' => 'wechat_url',
                        'fieldtype' => 'Text',
                    ),
                    'media_id' => array(
                        'ismain' => 1,
                        'fieldname' => 'media_id',
                        'fieldtype' => 'Text',
                    ),
                    'inputtime' => array(
                        'ismain' => 1,
                        'fieldname' => 'inputtime',
                        'fieldtype' => 'Date',
                    ),
                ) + $field;

            $action = $this->input->get('action');
            if ($action == 'add') {

                if (IS_POST) {
                    $_POST['data']['is_video'] = 0;
                    $_POST['data']['uid'] = $this->uid;
                    $_POST['data']['username'] = $this->member['username'];
                    $_POST['data']['inputtime'] = SYS_TIME;
                    $_POST['data']['wechat_url'] = '';
                    $_POST['data']['description'] = '';
                    $_POST['data']['media_id'] = '0';
                    $post = $this->validate_filter($this->myfield);
                    if (isset($post['error'])) {
                        $this->admin_msg($post['msg']);
                    } else {
                        $id = $this->_insert_data($post[1]);
                        // 操作成功处理附件
                        $this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable).'-'.$id, $this->myfield, $post);
                        $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/'.$this->router->method), 1);
                    }
                }

                $this->template->assign(array(
                    'field' => $this->myfield,
                    'menu' => $this->get_menu_v3(array(
                        '语音素材' => array('admin/wmaterial/'.$this->router->method.'', 'file-sound-o'),
                        '添加' => array('admin/wmaterial/'.$this->router->method.'/action/add', 'plus'),
                    )),
                ));
                $this->template->display($this->router->class.'_'.$this->router->method.'_add.html');
            } elseif ($action == 'edit') {

                unset($this->myfield['uid'], $this->myfield['username'], $this->myfield['inputtime']);
                unset($this->myfield['is_video'], $this->myfield['description'], $this->myfield['media_id']);
                unset($this->myfield['wechat_url']);

                $id = (int)$this->input->get($this->myid);
                $data = $this->_get_data($id);
                if (!$data) {
                    $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
                }

                if (IS_POST) {
                    $post = $this->validate_filter($this->myfield);
                    if (isset($post['error'])) {
                        $this->admin_msg($post['msg']);
                    } else {
                        $this->_update_data($data['id'], $post[1], $data);
                        // 操作成功处理附件
                        $this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable).'-'.$data['id'], $this->myfield, $post);
                        $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/'.$this->router->method), 1);
                    }
                }

                $this->template->assign(array(
                    'data' => $data,
                    'field' => $this->myfield,
                    'menu' => $this->get_menu_v3(array(
                        '语音素材' => array('admin/wmaterial/'.$this->router->method.'', 'file-sound-o'),
                        '添加' => array('admin/wmaterial/'.$this->router->method.'/action/add', 'plus'),
                        '修改' => array('admin/wmaterial/'.$this->router->method.'/action/edit/id/'.$data['id'], 'edit'),
                    )),
                ));
                $this->template->display($this->router->class.'_'.$this->router->method.'_add.html');
            } elseif ($action == 'download') {
                // 一键下载到服务器
                if (!$_GET['todo']) {
                    $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
                }
                $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . dr_get_access_token ();
                $param ['type'] = 'voice';
                $param ['offset'] = intval($_GET['offset']);
                $param ['count'] = 10;
                $list = dr_post_data ($url, $param);
                if (isset($list['errcode']) && $list['errcode']!= 0) {
                    $this->admin_msg ( dr_error_msg ( $list ) );
                }
                if (empty($list['item'])) {
                    $this->admin_msg('操作成功', dr_url($this->router->class.'/'.$this->router->method), 1);
                    $this->system_log('站点【#'.SITE_ID.'】微信语音素材从公众平台下载到本地'); // 记录日志
                }
                foreach ( $list ['item'] as $item ) {
                    $media_id = $item ['media_id'];
                    // 判断是否存在
                    if ($this->mydb->where('media_id', $media_id)->count_all_results($this->mytable)) {
                        continue;
                    }
                    $file = $this->_down_save_file($media_id, $item ['url'], 'mp3');
                    if ($file) {
                        // 保存成功
                        $this->mydb->insert($this->mytable, array(
                            'uid' => $this->uid,
                            'username' => $this->member['username'],
                            'file' => (int)$file,
                            'inputtime' => SYS_TIME,
                            'media_id' => $item ['media_id'],
                            'wechat_url' => '',
                            'is_video' => 0,
                            'title' => $item['name'],
                            'description' => '',
                        ));
                        $id = $this->mydb->insert_id();
                        // 替换附件
                        $this->attachment_model->replace_attach($this->uid, $this->db->dbprefix.$this->mytable.'-'.$id, array($file));
                    }
                }


                $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'offset' => $param ['offset'] + $list ['item_count'], 'todo' => 1)), 1, 0);

            } elseif ($action == 'upload') {
                // 一键上传到服务器
                if (!$_GET['todo']) {
                    $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
                }
                $list = $this->mydb->where('media_id', '0')->where('is_video', 0)->limit(5)->get($this->mytable)->result_array();
                if (!$list) {
                    $this->admin_msg('操作成功', dr_url($this->router->class.'/'.$this->router->method), 1);
                    $this->system_log('站点【#'.SITE_ID.'】微信语音素材同步到公众平台'); // 记录日志
                }

                foreach ($list as $t) {
                    $token = dr_get_access_token();
                    $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $token;
                    $info = get_attachment($t['file']);
                    if (!$info) {
                        // 附件不存在删除
                        $this->mydb->where('id', (int)$t['id'])->delete($this->mytable);
                        $this->system_log('由于文件不存在, 删除站点【#'.SITE_ID.'】微信语音素材【#'.$t['id'].'】'); // 记录日志
                    }
                    if (isset($info['remote']) && $info['remote']) {
                        $file = WEBPATH.'cache/attach/'.time().'_'.basename($info['attachment']);
                        file_put_contents($file, dr_catcher_data($info['attachment']));
                    } else {
                        $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
                    }
                    $res = dr_post_data($url, array(
                        'type' => 'voice',
                        'media' => '@' . $file,
                    ), true);
                    if (isset ($res['errcode']) && $res['errcode'] != 0) {
                        $this->admin_msg(dr_error_msg($res, '素材(#'.$t['id'].')上传'));
                        exit();
                    }
                    // 更新内容
                    $this->mydb->where('id', intval($t['id']))->update($this->mytable, array(
                        'media_id' => $res['media_id'],
                        // 无返回'wechat_url' => $res['url'],
                    ));
                }

                $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
            } elseif ($action == 'del') {
                $id = (int)$this->input->post('id');
                $row = $this->mydb->where('id', (int)$id)->get($this->mytable)->row_array();
                if ($row) {
                    $this->mydb->where('id', (int)$id)->delete($this->mytable);
                    $this->load->model('attachment_model');
                    $this->attachment_model->delete_for_table($this->mytable.'-'.$id);
                    // 删除关联
                    $this->system_log('删除站点【#'.SITE_ID.'】微信语音素材【#'.$id.'】'); // 记录日志
                }
                exit;
            } else {

                $this->mywhere = 'is_video=0';
                $this->_index();

                $this->template->assign(array(
                    'field' => $field,
                    'menu' => $this->get_menu_v3(array(
                        '语音素材' => array('admin/wmaterial/'.$this->router->method.'', 'file-sound-o'),
                        '添加' => array('admin/wmaterial/'.$this->router->method.'/action/add', 'plus'),
                    )),
                ));
                $this->template->display($this->router->class.'_'.$this->router->method.'.html');
            }

    }


        /**
         * 视频素材管理
         */
        public function sp() {

            $this->myid = 'id'; // 主键
            $this->tfield = 'inputtime'; // 时间字段用于搜索和排序
            $this->mytable = $this->weixin_model->prefix.'_material_file'; // 表名
            $field = array(
                'title' => array(
                    'name' => '标题',
                    'ismain' => 1,
                    'fieldname' => 'title',
                    'fieldtype' => 'Text',
                ),

            ); // 搜索字段
            $this->myfield = array(
                    'is_video' => array(
                        'ismain' => 1,
                        'fieldname' => 'is_video',
                        'fieldtype' => 'Text',
                    ),
                    'description' => array(
                        'name' => '摘要',
                        'ismain' => 1,
                        'fieldname' => 'description',
                        'fieldtype' => 'Text',
                    ),

                    'uid' => array(
                        'ismain' => 1,
                        'fieldname' => 'uid',
                        'fieldtype' => 'Text',
                    ),
                    'file' => array(
                        'ismain' => 1,
                        'fieldname' => 'file',
                        'fieldtype' => 'File',
                        'setting' => array(
                            'option' => array(
                                'ext' => '*',
                                'size' => 30,
                            )
                        )
                    ),
                    'username' => array(
                        'ismain' => 1,
                        'fieldname' => 'username',
                        'fieldtype' => 'Text',
                    ),
                    'wechat_url' => array(
                        'ismain' => 1,
                        'fieldname' => 'wechat_url',
                        'fieldtype' => 'Text',
                    ),
                    'media_id' => array(
                        'ismain' => 1,
                        'fieldname' => 'media_id',
                        'fieldtype' => 'Text',
                    ),
                    'inputtime' => array(
                        'ismain' => 1,
                        'fieldname' => 'inputtime',
                        'fieldtype' => 'Date',
                    ),
                ) + $field;

            $action = $this->input->get('action');
            if ($action == 'add') {

                if (IS_POST) {
                    $_POST['data']['is_video'] = 1;
                    $_POST['data']['uid'] = $this->uid;
                    $_POST['data']['username'] = $this->member['username'];
                    $_POST['data']['inputtime'] = SYS_TIME;
                    $_POST['data']['wechat_url'] = '';
                    $_POST['data']['media_id'] = '0';
                    $post = $this->validate_filter($this->myfield);
                    if (isset($post['error'])) {
                        $this->admin_msg($post['msg']);
                    } else {
                        $id = $this->_insert_data($post[1]);
                        // 操作成功处理附件
                        $this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable).'-'.$id, $this->myfield, $post);
                        $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/'.$this->router->method), 1);
                    }
                }

                $this->template->assign(array(
                    'field' => $this->myfield,
                    'menu' => $this->get_menu_v3(array(
                        '视频素材' => array('admin/wmaterial/'.$this->router->method.'', 'file-video-o'),
                        '添加' => array('admin/wmaterial/'.$this->router->method.'/action/add', 'plus'),
                    )),
                ));
                $this->template->display($this->router->class.'_'.$this->router->method.'_add.html');
            } elseif ($action == 'edit') {

                unset($this->myfield['uid'], $this->myfield['username'], $this->myfield['inputtime']);
                unset($this->myfield['is_video'],$this->myfield['media_id']);
                unset($this->myfield['wechat_url']);

                $id = (int)$this->input->get($this->myid);
                $data = $this->_get_data($id);
                if (!$data) {
                    $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
                }

                if (IS_POST) {
                    $post = $this->validate_filter($this->myfield);
                    if (isset($post['error'])) {
                        $this->admin_msg($post['msg']);
                    } else {
                        $this->_update_data($data['id'], $post[1], $data);
                        // 操作成功处理附件
                        $this->attachment_handle($this->uid, $this->db->dbprefix($this->mytable).'-'.$data['id'], $this->myfield, $post);
                        $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/'.$this->router->method), 1);
                    }
                }

                $this->template->assign(array(
                    'data' => $data,
                    'field' => $this->myfield,
                    'menu' => $this->get_menu_v3(array(
                        '视频素材' => array('admin/wmaterial/'.$this->router->method.'', 'file-video-o'),
                        '添加' => array('admin/wmaterial/'.$this->router->method.'/action/add', 'plus'),
                        '修改' => array('admin/wmaterial/'.$this->router->method.'/action/edit/id/'.$data['id'], 'edit'),
                    )),
                ));
                $this->template->display($this->router->class.'_'.$this->router->method.'_add.html');
            } elseif ($action == 'download') {
                // 一键下载到服务器
                if (!$_GET['todo']) {
                    $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
                }
                $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . dr_get_access_token ();
                $param ['type'] = 'video';
                $param ['offset'] = intval($_GET['offset']);
                $param ['count'] = 10;
                $list = dr_post_data ($url, $param);
                if (isset($list['errcode']) && $list['errcode']!= 0) {
                    $this->admin_msg ( dr_error_msg ( $list ) );
                }
                if (empty($list['item'])) {
                    $this->admin_msg('操作成功', dr_url($this->router->class.'/'.$this->router->method), 1);
                    $this->system_log('站点【#'.SITE_ID.'】微信视频素材从公众平台下载到本地'); // 记录日志
                }
                foreach ( $list ['item'] as $item ) {
                    $media_id = $item ['media_id'];
                    // 判断是否存在
                    if ($this->mydb->where('media_id', $media_id)->count_all_results($this->mytable)) {
                        continue;
                    }
                    //视频
                    $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . dr_get_access_token ();
                    $param ['media_id'] = $media_id;
                    $video = dr_post_data ( $url, $param);
                    if (isset ( $video ['errcode'] ) && $video ['errcode'] != 0) {
                        $this->admin_msg(dr_error_msg ( $video, '下载视频文件素材失败' ) );
                        exit ();
                    }
                    print_r($video);
                    exit;

                    $file = $this->_down_save_file(0, $video['down_url'], 'mp4');
                    if ($file) {
                        // 保存成功
                        $this->mydb->insert($this->mytable, array(
                            'uid' => $this->uid,
                            'username' => $this->member['username'],
                            'file' => (int)$file,
                            'inputtime' => SYS_TIME,
                            'media_id' => $media_id,
                            'wechat_url' => $video['down_url'],
                            'is_video' => 1,
                            'title' => $video['title'],
                            'description' => $video['description'],
                        ));
                        $id = $this->mydb->insert_id();
                        // 替换附件
                        $this->attachment_model->replace_attach($this->uid, $this->db->dbprefix.$this->mytable.'-'.$id, array($file));
                    }
                }


                $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'offset' => $param ['offset'] + $list ['item_count'], 'todo' => 1)), 1, 0);

            } elseif ($action == 'upload') {
                // 一键上传到服务器
                if (!$_GET['todo']) {
                    $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
                }
                $list = $this->mydb->where('media_id', '0')->where('is_video', 1)->limit(5)->get($this->mytable)->result_array();
                if (!$list) {
                    $this->admin_msg('操作成功', dr_url($this->router->class.'/'.$this->router->method), 1);
                    $this->system_log('站点【#'.SITE_ID.'】微信视频素材同步到公众平台'); // 记录日志
                }

                foreach ($list as $t) {
                    $token = dr_get_access_token();
                    $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $token;
                    $info = get_attachment($t['file']);
                    if (!$info) {
                        // 附件不存在删除
                        $this->mydb->where('id', (int)$t['id'])->delete($this->mytable);
                        $this->system_log('由于文件不存在, 删除站点【#'.SITE_ID.'】微信视频素材【#'.$t['id'].'】'); // 记录日志
                    }
                    if (isset($info['remote']) && $info['remote']) {
                        $file = WEBPATH.'cache/attach/'.time().'_'.basename($info['attachment']);
                        file_put_contents($file, dr_catcher_data($info['attachment']));
                    } else {
                        $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
                    }
                    $res = dr_post_data($url, array(
                        'type' => 'video',
                        'media' => '@' . $file,
                    ), true);
                    if (isset ($res['errcode']) && $res['errcode'] != 0) {
                        $this->admin_msg(dr_error_msg($res, '素材(#'.$t['id'].')上传'));
                        exit();
                    }
                    // 更新内容
                    $this->mydb->where('id', intval($t['id']))->update($this->mytable, array(
                        'media_id' => $res['media_id'],
                        // 无返回'wechat_url' => $res['url'],
                    ));
                }

                $this->admin_msg('正在同步中...', dr_url($this->router->class.'/'.$this->router->method, array('action' => $action, 'todo' => 1)), 1, 0);
            } elseif ($action == 'del') {
                $id = (int)$this->input->post('id');
                $row = $this->mydb->where('id', (int)$id)->get($this->mytable)->row_array();
                if ($row) {
                    $this->mydb->where('id', (int)$id)->delete($this->mytable);
                    $this->load->model('attachment_model');
                    $this->attachment_model->delete_for_table($this->mytable.'-'.$id);
                    // 删除关联
                    $this->system_log('删除站点【#'.SITE_ID.'】微信语音素材【#'.$id.'】'); // 记录日志
                }
                exit;
            } else {

                $this->mywhere = 'is_video=1';
                $this->_index();

                $this->template->assign(array(
                    'field' => $field,
                    'menu' => $this->get_menu_v3(array(
                        '视频素材' => array('admin/wmaterial/'.$this->router->method.'', 'file-video-o'),
                        '添加' => array('admin/wmaterial/'.$this->router->method.'/action/add', 'plus'),
                    )),
                ));
                $this->template->display($this->router->class.'_'.$this->router->method.'.html');
            }

    }


    ######################

    public function _down_save_file($media_id, $url, $fileext) {

        if (!$url) {
            // 获取图片URL
            $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . dr_get_access_token ();
            $param ['media_id'] = $media_id;
            $picContent = dr_post_data ( $url, $param, false, false );
            $picjson = json_decode ( $picContent, true );
            if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
                $this->admin_msg(dr_error_msg ( $picjson, '下载文件素材失败' ) );
                exit ();
            }
            $file = $picContent;
        } else {
            $file = dr_catcher_data($url);
        }
        if (!$file) {
            $this->admin_msg('获取远程数据失败('.$url.')');
        } else {
            $path = SYS_UPLOAD_PATH.'/weixin/'.date('Ym', SYS_TIME).'/';
            if (!is_dir($path)) {
                dr_mkdirs($path);
            }
            $filename = substr(md5(time()), 0, 7).rand(100, 999);
            if (@file_put_contents($path.$filename.'.'.$fileext, $file)) {
                $info = array(
                    'file_ext' => '.'.$fileext,
                    'full_path' => $path.$filename.'.'.$fileext,
                    'file_size' => filesize($path.$filename.'.'.$fileext)/1024,
                    'client_name' => $url,
                );
                $this->load->model('attachment_model');
                $result = $this->attachment_model->upload($this->uid, $info);
                if (is_array($result)) {
                    // 保存成功
                    return $result[0];
                } else {
                    @unlink($path.$filename.'.'.$fileext);
                    $this->admin_msg('下载远程图片失败('.$result.')');
                }
            } else {
                $this->admin_msg('下载远程图片失败：文件写入失败');
            }
        }
    }

    public function _download_imgage($media_id) {

        $path = SYS_UPLOAD_PATH.'/weixin/'.date('Ym', SYS_TIME).'/';
        if (!is_dir($path)) {
            dr_mkdirs($path);
        }
        // 获取图片URL
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . dr_get_access_token ();
        $param ['media_id'] = $media_id;
        $picContent = dr_post_data ( $url, $param, false, false );
        $picjson = json_decode ( $picContent, true );
        if (isset ( $picjson ['errcode'] ) && $picjson ['errcode'] != 0) {
            return '';
            $this->admin_msg(dr_error_msg ( $picjson, '下载图片' ) );
            exit ();
        }
        $fileext = 'jpg'; //扩展名
        $filename = substr(md5(time()), 0, 7).rand(100, 999);
        if (@file_put_contents($path.$filename.'.'.$fileext, $picContent)) {
            $info = array(
                'file_ext' => '.'.$fileext,
                'full_path' => $path.$filename.'.'.$fileext,
                'file_size' => filesize($path.$filename.'.'.$fileext)/1024,
                'client_name' => $url,
            );
            $this->load->model('attachment_model');
            $result = $this->attachment_model->upload($this->uid, $info);
            if (is_array($result)) {
                // 保存成功
                return $result[0];
            } else {
                @unlink($path.$filename.'.'.$fileext);
                $this->admin_msg('下载远程图片失败('.$result.')');
            }
        } else {
            $this->admin_msg('下载远程图片失败：文件写入失败');
        }
    }

    function _thumb_media_id($data) {

        $info = get_attachment($data['thumb']);
        if (!$info) {
            // 附件不存在
            $this->admin_msg( '获取文章封面失败，请确认是否增加封面' );
        }
        if (isset($info['remote']) && $info['remote']) {
            $file = WEBPATH.'cache/attach/'.time().'_'.basename($info['attachment']);
            file_put_contents($file, dr_catcher_data($info['attachment']));
        } else {
            $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
        }

        $param ['type'] = 'thumb';
        $param ['media'] = '@' . $file;
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . dr_get_access_token ();
        $res = dr_post_data ( $url, $param, true );

        if (isset ( $res ['errcode'] ) && $res ['errcode'] != 0) {
            $this->admin_msg(dr_error_msg ( $res, '封面图上传' ) );
        }

        $this->mydb->where('id', $data['id'])->update($this->mytable, array(
            'thumb_media_id' => $res ['media_id']
        ));

        return $res ['media_id'];
    }
    // 获取图文素材url
    function _news_url($media_id) {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . dr_get_access_token ();
        $param ['media_id'] = $media_id;
        $news = dr_post_data ( $url, $param );
        if (isset ( $news ['errcode'] ) && $news ['errcode'] != 0) {
            $this->admin_msg(dr_error_msg ( $news) );
        }
        foreach ( $news ['news_item'] as $vo ) {
            $newsUrl [$vo ['title']] = $vo ['url'];
        }
        return $newsUrl;
    }
}