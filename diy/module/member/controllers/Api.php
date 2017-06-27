<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


class Api extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    public function icon() {

        $this->template->admin();
        $this->template->display('icon.html');
    }

    // ajax 删除草稿
    public function ajax_delete_draft() {

        $sid = (int)$this->input->get('sid');
        $did = (int)$this->input->get('did');
        $table = $this->db->dbprefix($sid.'_'.$this->input->get('dir').'_draft');

        $this->load->model('attachment_model');
        if ($this->site[$sid]->where('id', $did)->where('uid', $this->uid)->get($table)->row_array()) {
            $this->site[$sid]->where('id', $did)->delete($table);
            // 删除表对应的附件
            $this->attachment_model->delete_for_table($table.'-'.$did);
            exit(''.$did.'');
        } else {
            exit('删除失败：草稿不存在！');
        }
    }

    // ajax 实时保存草稿
    public function ajax_save_draft() {

        $sid = (int)$this->input->get('sid');
        $did = (int)$this->input->get('did');
        $catid = (int)$this->input->get('catid');

        $data1 = $this->input->post('data');
        if (isset($data1['title']) && !strlen($data1['title'])) {
            exit;
        } elseif(isset($data1['name']) && !strlen($data1['name'])) {
            exit;
        } elseif(!isset($data1['name']) && !isset($data1['title'])) {
            exit;
        }

        $update = array(
            'content' => dr_array2string($data1),
            'inputtime' => SYS_TIME,
        );

        $catid && $update['catid'] = $catid;

        $this->site[$sid]
             ->where('id', $did)
             ->where('uid', $this->uid)
             ->update($sid.'_'.$this->input->get('dir').'_draft', $update);
    }

    // ajax 实时保存添加内容
    public function ajax_save_add() {
        $this->set_cache_data('save_'.$this->input->get('dir').'_'.$this->uid, $this->input->post('data'), 3600);
    }

    // 注销授权，进入会员中心
    public function member() {
        $this->session->set_userdata('member_auth_uid', 0);
        redirect(dr_member_url(), 'refresh');
    }

    // 登录授权
    public function ologin() {

        $uid = (int)$this->input->get('uid');

        // 注销上一个会员
        if ($this->session->userdata('member_auth_uid')) {
            $this->session->set_userdata('member_auth_uid', 0);
            redirect(SITE_URL.'index.php?s=member&c=api&m=ologin&uid='.$uid, 'refresh');
        }

        // 未登录的情况下
        !$this->member && $this->admin_msg(fc_lang('务必要在会员中心登录一次,才能进行授权登录'), MEMBER_URL);

        // 非管理员无权操作
        $this->member['adminid'] != 1 && $this->admin_msg($this->member['username'].'：'.fc_lang('您无权限操作'));

        $this->uid != $uid && $this->session->set_userdata('member_auth_uid', $uid);

        $go = $this->input->get('go');
        $go = $go ? $go : MEMBER_URL;
        $this->template->assign('meta_name', fc_lang('登录成功'));

        $this->admin_msg(fc_lang('授权登录成功，正在跳转到会员中心，请稍后...'), $go, 2);
    }

    /**
     * 内容关联字段数据读取
     */
    public function related() {

        // 强制将模板设置为后台
        $this->template->admin();

        // 登陆判断
        !$this->uid && $this->admin_msg(fc_lang('会话超时，请重新登录'));

        // 参数判断
        $dirname = $this->input->get('module');
        !$dirname && $this->admin_msg(fc_lang('模块module参数不存在'));

        // 站点选择
        $site = (int)$_GET['site'];
        $site = $site ? $site : SITE_ID;

        // 模块缓存判断
        $module = $this->get_cache('module-'.$site.'-'.$dirname);
        !$module && $this->admin_msg(fc_lang('此模块（%s）缓存不存在', $dirname));

        // 加载后台用到的语言包
        $this->lang->load('admin');
        $this->lang->load('template');

        $db = $this->site[$site];
        $field = $module['field'];
        $category = $module['category'];

        $field['id'] = array(
            'name' => 'Id',
            'ismain' => 1,
            'fieldtype' => 'Text',
            'fieldname' => 'id',
        );

        if ($this->member['adminid']) {
            $field['author'] = array(
                'name' => fc_lang('录入作者'),
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'author',
            );
        } else {
            $db->where('uid', $this->uid);
        }

        if (IS_POST) {
            $data = $this->input->post('data');
            $catid = (int)$this->input->post('catid');
            $catid && $db->where_in('catid', $category[$catid]['catids']);
            if (isset($data['keyword']) && $data['keyword']
                && $data['field'] && isset($field[$data['field']])) {
                if ($data['field'] == 'id') {
                    // id搜索
                    $id = array();
                    $ids = explode(',', $data['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int)$i;
                    }
                    $db->where_in('id', $id);
                } elseif ($field[$data['field']]['fieldtype'] == 'Linkage'
                    && $field[$data['field']]['setting']['option']['linkage']) {
                    // 联动菜单搜索
                    if (is_numeric($data['keyword'])) {
                        // 联动菜单id查询
                        $link = dr_linkage($field[$data['field']]['setting']['option']['linkage'], (int)$data['keyword'], 0, 'childids');
                        $link && $db->where($data['field'].' IN ('.$link.')');
                    } else {
                        // 联动菜单名称查询
                        $id = (int)$this->get_cache('linkid-'.SITE_ID, $field[$data['field']]['setting']['option']['linkage']);
                        $id && $db->where($data['field'].' IN (select id from `'.$db->dbprefix('linkage_data_'.$id).'` where `name` like "%'.$data['keyword'].'%")');
                    }
                } else {
                    // 其他模糊搜索
                    $db->like($data['field'], urldecode($data['keyword']));
                }
            }
        }

        // 搜索结果显示条数
        $limit = (int)$_GET['limit'];
        $limit = $limit ? $limit : 50;

        sort($field);
        $list = $db->limit($limit) ->order_by('updatetime DESC')->select('id,title,updatetime,url')->get($site.'_'.$dirname)->result_array();

        // 栏目选择
        $tree = array();
        $select = '<select name="catid">';
        $select.= "<option value='0'> -- </option>";
        if (is_array($category)) {
            foreach($category as $t) {
                $t['selected'] = $catid == $t['id'] ? 'selected' : '';
                $t['html_disabled'] = 0;
                unset($t['permission'], $t['setting'], $t['catids'], $t['url']);
                $tree[$t['id']] = $t;
            }
        }
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $str2 = "<optgroup label='\$spacer \$name'></optgroup>";
        $this->load->library('dtree');
        $this->dtree->init($tree);
        $select.= $this->dtree->get_tree_category(0, $str, $str2);
        $select.= '</select>';

        $this->template->assign(array(
            'list' => $list,
            'param' => $data,
            'field' => $field,
            'select' => $select,
        ));
        $this->template->display('related.html', 'admin');
    }

    /**
     * 检查新提醒
     */
    public function notice() {

        $value = $this->uid ? $this->db->where('uid', (int)$this->uid)->count_all_results('member_new_notice') : 0;
        $callback = isset($_GET['callback']) ? $this->input->get('callback', TRUE) : 'callback';
        exit($callback . '(' . json_encode(array('status' => $value)) . ')');
    }

    /**
     * 检测会员在线情况
     */
    public function online() {

        $uid = (int)$this->input->get('uid');
        $type = (int)$this->input->get('type');
        $icon = MEMBER_THEME_PATH.'images/';

        if ($this->db->where('uid', $uid)->count_all_results('member_online')) {
            $icon.= 'web'.$type.'.gif';
            $online = 1;
        } else {
            $icon.= 'web'.$type.'-off.gif';
            $online = 0;
        }

        $member = $this->db->select('username')->where('uid', $uid)->get('member')->row_array();

        $string = '<img src="'
            .$icon.'" align="absmiddle" style="cursor:pointer" onclick="dr_chat(this)" username="'
            .$member['username'].'" uid='.$uid.' online='.$online.'>';

        exit("document.write('$string');");
    }

    /**
     * 站点间的同步登录
     */
    public function synlogin() {
        $this->api_synlogin();
    }

    /**
     * 站点间的同步退出
     */
    public function synlogout() {
        $this->api_synlogout();
    }

    /**
     * 自定义信息JS调用
     */
    public function template() {
        $this->api_template();
    }

    /**
     * 自定义空间信息JS调用
     */
    public function space_template() {

        $name = dr_safe_replace($this->input->get('name', TRUE));
        $style = dr_safe_replace($this->input->get('style', TRUE));
        
        ob_start();
        $this->template->cron = 0;
        $this->template->display('statics/space/'.$style.'/'.$name);
        $html = ob_get_contents();
        ob_clean();
        
        // 格式输出
        if (isset($_GET['return']) && $_GET['return'] == 'js') {
            $html = addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html));
            echo 'document.write("'.$html.'");';exit;
        } else {
            exit($html);
        }
    }

    /**
     * 伪静态测试
     */
    public function test() {
        header('Content-Type: text/html; charset=utf-8');
        echo '服务器支持伪静态';
    }

    /**
     * 联动栏目分类调用
     */
    public function category() {

        $dir = $this->input->get('module');
        $pid = (int)$this->input->get('parent_id');
        $json = array();
        $category = $this->get_cache('module-'.SITE_ID.'-'.$dir, 'category');

        foreach ($category as $k => $v) {
            if ($v['pid'] == $pid) {
                if (!$v['child'] && !$v['permission'][$this->markrule]['add']) {
                    continue;
                }
                $json[] = array(
                    'region_id' => $v['id'],
                    'region_name' => $v['name'],
                    'region_child' => $v['child']
                );
            }
        }

        echo json_encode($json);
    }

    /**
     * 联动菜单调用
     */
    public function linkage() {

        $pid = (int)$this->input->get('parent_id');
        $json = array();
        $code = $this->input->get('code');
        $linkage = $this->get_cache('linkage-'.SITE_ID.'-'.$code);

        foreach ($linkage as $v) {
            $v['pid'] == $pid && $json[] = array('region_id' => $v['ii'], 'region_name' => $v['name']);
        }

        echo json_encode($json);
    }

    /**
     * 会员登录信息JS调用
     */
    public function userinfo() {
        
        ob_start();
        $this->template->display('api.html');
        $html = ob_get_contents();
        ob_clean();
        
        $callback = $this->input->get('callback', TRUE);
        if ($callback) {
            echo $callback.'('.json_encode(array('html' => $html)).')';
        } else {
            $html = addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html));
            echo 'document.write("'.$html.'");';
        }
        
        exit;
    }

    /**
     * Ajax调用字段属性表单
     *
     * @return void
     */
    public function field() {

        $id = (int)$this->input->post('id');
        $type = $this->input->post('type');

        $this->load->model('field_model');
        $this->relatedid = $this->input->post('relatedid');
        $this->relatedname = $this->input->post('relatedname');

        $data = $this->field_model->get($id);
        $fields = $this->field_model->get_data();
        $related = $this->input->post('relatedname');
        if ($data) {
            $value = dr_string2array($data['setting']);
            $value = $value['option'];
        } else {
            $value = array();
        }

        $this->lang->load('admin');
        $this->lang->load('template');
        $this->load->library('Dfield', array($this->input->post('module')));

        define('TEXT_UNIQUE', $related == 'module' ? 1 : 0);
        $return	= $this->dfield->option($type, $value, $fields);

        if ($return !== 0) {
            echo $return;
        }
    }

    /**
     * 百度地图调用
     *
     * @return void
     */
    public function baidumap() {

        $list = $this->input->get('city') ? explode(',', urldecode($this->input->get('city'))) : NULL;
        $city = isset($list[0]) ? $list[0] : '';
        $value = $this->input->get('value');
        $value = strlen($value) > 10 ? $value : '';

        $this->template->assign(array(
            'city' => $city,
            'value' => $value,
            'list' => $list,
            'name' => $this->input->get('name'),
            'level' => (int)$this->input->get('level'),
            'width' => $this->input->get('width'),
            'height' => $this->input->get('height') - 30,
        ));
        $this->template->display('baidumap.html', 'admin');

    }

    /**
     * 文件上传
     *
     * @return void
     */
    public function upload() {

        $site = (int)$this->input->get('siteid');
        $site = $site ? $site : SITE_ID;
        $code = str_replace(' ', '+', $this->input->get('code'));
        list($size, $ext, $path) = explode('|', dr_authcode($code, 'DECODE'));

        $uid = $this->uid;
        // 附件上传时采用后台登陆会员
        $this->session->userdata('member_auth_uid') && $uid = $this->member_model->member_uid(1);

        $this->load->model('attachment_model');
        $notused = $this->attachment_model->get_unused($uid, $ext);

        $this->template->assign(array(
            'ext' => str_replace(',', '|', $ext),
            'code' => $code,
            'page' => $notused ? 3 : 0,
            'size' => (int)$size * 1024,
            'name' => $this->input->get('name'),
            'types' => '*.'.str_replace(',', ';*.', $ext),
            'siteid' => $site,
            'fileid' => $this->input->get('filename'),
            'fcount' => (int)$this->input->get('count'),
            'notused' => $notused,
            'session' => dr_authcode($uid, 'ENCODE'),
            'is_admin' => 0,
        ));
        $this->template->display('upload.html', 'admin');
    }

    /**
     * fex文件上传
     *
     * @return void
     */
    public function fex_upload() {

        $site = (int)$this->input->get('siteid');
        $site = $site ? $site : SITE_ID;
        $code = str_replace(' ', '+', $this->input->get('code'));
        list($size, $ext, $path) = explode('|', dr_authcode($code, 'DECODE'));

        $uid = $this->uid;
        // 附件上传时采用后台登陆会员
        $this->session->userdata('member_auth_uid') && $uid = $this->member_model->member_uid(1);

        $this->load->model('attachment_model');
        $notused = $this->attachment_model->get_unused($uid, $ext);

        $this->template->assign(array(
            'ext' => str_replace('|', ',', $ext),
            'code' => $code,
            'name' => $this->input->get('name'),
            'types' => '*.'.str_replace(',', ';*.', $ext),
            'notused' => $notused,
            'size' => (int)$size * 1024,
            'name' => $this->input->get('name'),
            'siteid' => $site,
            'fileid' => $this->input->get('filename'),
            'fcount' => (int)$this->input->get('count'),
            'post_url' => '/index.php?s=member&c=api&m=fex_ajax_upload&code='.$code,
        ));
        $this->template->display('upload_fex.html', 'admin');
    }

    /**
     * fex文件上传处理
     *
     * @return void
     */
    public function fex_ajax_upload() {


        $uid = $this->uid;
        // 游客不允许上传，未获取到会员信息时提示游客无法上传
        !$this->member && exit('0,'.fc_lang('抱歉！游客不允许上传附件'));

        // 会员组权限
        $member_rule = $this->get_cache('member', 'setting', 'permission', $this->member['mark']);

        // 是否允许上传附件
        !$this->member['adminid'] && !$member_rule['is_upload'] && exit('0,'.fc_lang('抱歉！您的会员组无权上传附件'));

        // 附件总大小判断
        if (!$this->member['adminid'] && $member_rule['attachsize']) {
            $data = $this->db->select_sum('filesize')->where('uid', $uid)->get('attachment')->row_array();
            $filesize = (int)$data['filesize'];
            $filesize > $member_rule['attachsize'] * 1024 * 1024 && exit('0,'.fc_lang('附件空间不足！您的附件总空间%s，现有附件%s。', $member_rule['attachsize'].'MB', dr_format_file_size($filesize)));
        }

        if (IS_POST) {
            $code = str_replace(' ', '+', $this->input->get('code'));
            list($size, $ext, $path) = explode('|', dr_authcode($code, 'DECODE'));
            $path = $path ? SYS_UPLOAD_PATH.'/'.$path.'/' : SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
            !is_dir($path) && dr_mkdirs($path);
            $this->load->library('upload', array(
                'max_size' => (int)$size * 1024,
                'overwrite' => FALSE,
                'file_name' => substr(md5(time()), rand(0, 20), 10),
                'upload_path' => $path,
                'allowed_types' => str_replace(',', '|', $ext),
                'file_ext_tolower' => TRUE,
            ));
            if ($this->upload->do_upload('file')) {
                $info = $this->upload->data();
                $site = (int)$this->input->post('siteid');
                $this->load->model('attachment_model');
                $this->attachment_model->siteid = $site ? $site : SITE_ID;
                $result = $this->attachment_model->upload($uid, $info);
                !is_array($result) && exit('0,'.$result);
                list($id, $file, $_ext) = $result;
                $icon = is_file(WEBPATH.'statics/admin/images/ext/'.$_ext.'.gif') ? THEME_PATH.'admin/images/ext/'.$_ext.'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
                //唯一ID,文件全路径,图标,文件名称,文件大小,扩展名
                exit($id.','.dr_file($file).','.$icon.','.str_replace(array('|', '.'.$_ext), '', $info['client_name']).','.dr_format_file_size($info['file_size'] * 1024).','.$_ext);
            } else {
                exit('0,'.$this->upload->display_errors('', ''));
            }
        }
    }


    // sns上传图片
    public function sns_upload() {

        $uid = (int)dr_authcode(str_replace(' ', '+', $this->input->post('PHPSESSID')), 'DECODE');
        !$uid && exit(json_encode(array('status' => 0, 'data' => fc_lang('抱歉！游客不允许上传附件'))));

        // 根据页面传入的session来获取当前登录uid，未获取到uid时提示游客无法上传
        $this->member = $this->member_model->get_member($uid); // 获取会员信息

        // 游客不允许上传，未获取到会员信息时提示游客无法上传
        !$this->member && exit(json_encode(array('status' => 0, 'data' => fc_lang('抱歉！游客不允许上传附件'))));

        // 会员组权限
        $member_rule = $this->get_cache('member', 'setting', 'permission', $this->member['mark']);

        // 是否允许上传附件
        !$this->member['adminid'] && !$member_rule['is_upload'] && exit(json_encode(array('status' => 0, 'data' => fc_lang('抱歉！您的会员组无权上传附件'))));

        // 附件总大小判断
        if (!$this->member['adminid'] && $member_rule['attachsize']) {
            $data = $this->db->select_sum('filesize')->where('uid', $uid)->get('attachment')->row_array();
            $filesize = (int)$data['filesize'];
            $filesize > $member_rule['attachsize'] * 1024 * 1024 && exit(json_encode(array('status' => 0, 'data' => fc_lang('附件空间不足！您的附件总空间%s，现有附件%s。', $member_rule['attachsize'].'MB', dr_format_file_size($filesize)))));
        }

        $path = SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
        !is_dir($path) && dr_mkdirs($path);

        $this->load->library('upload', array(
            'max_size' => 10240,
            'overwrite' => FALSE,
            'file_name' => substr(md5(time()), rand(0, 20), 10),
            'upload_path' => $path,
            'allowed_types' => 'gif|jpg|png',
            'file_ext_tolower' => TRUE,
        ));
        if ($this->upload->do_upload('Filedata')) {
            $info = $this->upload->data();
            $this->load->model('attachment_model');
            $result = $this->attachment_model->upload($uid, $info);
            !is_array($result) && exit(json_encode(array('status' => 0, 'data' => $result)));
            list($id, $file, $_ext) = $result;
            echo json_encode(array(
                'status' => 1,
                'data' => array(
                    'src' => dr_file($file),
                    'extension' => $_ext,
                    'attach_id' => $id
                )
            ));
            exit;
        } else {
            exit(json_encode(array('status' => 0, 'data' => $this->upload->display_errors('', ''))));
        }
    }

    // ajax 图片上传
    public function ajax_upload() {


        // 游客不允许上传
        !$this->member && exit(json_encode(array('code'=>0, 'msg'=>fc_lang('抱歉！游客不允许上传附件'), 'url'=> '', 'id'=>'')));

        // 会员组权限
        $member_rule = $this->get_cache('member', 'setting', 'permission', $this->member['mark']);

        // 是否允许上传附件
        !$this->member['adminid'] && !$member_rule['is_upload'] && exit(json_encode(array('code'=>0, 'msg'=>fc_lang('抱歉！您的会员组无权上传附件'), 'url'=> '', 'id'=>'')));

        // 附件总大小判断
        if (!$this->member['adminid'] && $member_rule['attachsize']) {
            $data = $this->db->select_sum('filesize')->where('uid', $this->uid)->get('attachment')->row_array();
            $filesize = (int)$data['filesize'];
            $filesize > $member_rule['attachsize'] * 1024 * 1024 && exit(json_encode(array('code'=>0, 'msg'=>fc_lang('附件空间不足！您的附件总空间%s，现有附件%s。', $member_rule['attachsize'].'MB', dr_format_file_size($filesize)), 'url'=> '', 'id'=>'')));
        }

        $ext = 'jpg,jpeg,gif,png';
        $site = (int)$this->input->get('siteid');
        $site = $site ? $site : SITE_ID;
        $code = str_replace(' ', '+', $this->input->get('code'));
        list($size, $path) = explode('|', dr_authcode($code, 'DECODE'));

        $path = $path ? SYS_UPLOAD_PATH.'/'.$path.'/' : SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
        !is_dir($path) && dr_mkdirs($path);

        $this->load->library('upload', array(
            'max_size' => (int)$size * 1024,
            'overwrite' => FALSE,
            'file_name' => substr(md5(time()), rand(0, 20), 10),
            'upload_path' => $path,
            'allowed_types' => str_replace(',', '|', $ext),
            'file_ext_tolower' => TRUE,
        ));
        
        if ($this->upload->do_upload(isset($_GET['fname']) ? $_GET['fname'] : 'Filedata')) {
            $info = $this->upload->data();
            $this->load->model('attachment_model');
            $this->attachment_model->siteid = $site;
            $result = $this->attachment_model->upload($this->uid, $info);
            !is_array($result) && exit('0,'.$result);
            list($id, $file, $_ext) = $result;
            echo json_encode(array('code'=>1, 'msg'=>'', 'name' => dr_strcut($info['filename'], 15).'.'.$_ext, 'id'=>$id, 'url' => dr_get_file($id)));exit;
        } else {
            echo json_encode(array('code'=>0, 'msg'=>$this->upload->display_errors('', ''), 'url'=> '', 'id'=>''));exit;
        }

    }

    // 文件下载并上传
    public function down_file() {

        $p = array();
        $url = explode('&', $this->input->post('url'));

        foreach ($url as $t) {
            $item = explode('=', $t);
            $p[$item[0]] = $item[1];
        }

        !$this->uid && exit(dr_json(0, fc_lang('抱歉！游客不允许上传附件')));

        // 会员组权限
        $member_rule = $this->get_cache('member', 'setting', 'permission', $this->member['mark']);

        // 是否允许上传附件
        !$this->member['adminid'] && !$member_rule['is_upload'] && exit(dr_json(0, fc_lang('抱歉！您的会员组无权上传附件')));
        
        // 附件总大小判断
        if (!$this->member['adminid'] && $member_rule['attachsize']) {
            $data = $this->db->select_sum('filesize')->where('uid', $this->uid)->get('attachment')->row_array();
            $filesize = (int)$data['filesize'];
            $filesize > $member_rule['attachsize'] * 1024 * 1024 && exit(dr_json(0, fc_lang('附件空间不足！您的附件总空间%s，现有附件%s。', $member_rule['attachsize'].'MB', dr_format_file_size($filesize))));
        }

        list($size, $ext, $path) = explode('|', dr_authcode($p['code'], 'DECODE'));
        $path = $path ? SYS_UPLOAD_PATH.'/'.$path.'/' : SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
        !is_dir($path) && dr_mkdirs($path);

        $furl = $this->input->post('file');
        $file = dr_catcher_data($furl);
        !$file && exit(dr_json(0, '获取远程文件失败'));

        $fileext = strtolower(trim(substr(strrchr($furl, '.'), 1, 10))); //扩展名
        !@in_array($fileext, @explode(',', $ext)) && exit(dr_json(0, '远程文件扩展名（'.$fileext.'）不允许'));
        
        $filename = substr(md5(time()), 0, 7).rand(100, 999);
        if (@file_put_contents($path.$filename.'.'.$fileext, $file)) {
            $info = array(
                'file_ext' => '.'.$fileext,
                'full_path' => $path.$filename.'.'.$fileext,
                'file_size' => filesize($path.$filename.'.'.$fileext)/1024,
                'client_name' => '',
            );
            $this->load->model('attachment_model');
            $this->attachment_model->siteid = $p['siteid'] ? $p['siteid'] : SITE_ID;
            $result = $this->attachment_model->upload($this->uid, $info);
            if (is_array($result)) {
                list($id, $file, $_ext) = $result;
                //$icon = is_file(WEBPATH.'statics/admin/images/ext/'.$_ext.'.gif') ? THEME_PATH.'admin/images/ext/'.$_ext.'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
                //echo json_encode(array('status'=>1, 'icon'=> $icon, 'size' => dr_format_file_size($info['file_size'] * 1024), 'id'=>$id));exit;
                echo json_encode(array('status'=>1, 'id'=>$id, 'name' => dr_strcut($filename, 10).'.'.$fileext));exit;
            } else {
                @unlink($info['full_path']);
                exit(dr_json(0, $result));
            }
        } else {
            exit(dr_json(0, '文件移动失败，目录无权限（'.$path.'）'));
        }
    }

    /**
     * 文件上传处理
     *
     * @return void
     */
    public function swfupload() {

        $uid = (int)dr_authcode(str_replace(' ', '+', $this->input->post('session')), 'DECODE');
        !$uid && exit('0,'.fc_lang('抱歉！游客不允许上传附件'));
        
        // 根据页面传入的session来获取当前登录uid，未获取到uid时提示游客无法上传
        $this->member = $this->member_model->get_member($uid); // 获取会员信息

        // 游客不允许上传，未获取到会员信息时提示游客无法上传
        !$this->member && exit('0,'.fc_lang('抱歉！游客不允许上传附件'));
        
        // 会员组权限
        $member_rule = $this->get_cache('member', 'setting', 'permission', $this->member['mark']);

        // 是否允许上传附件
        !$this->member['adminid'] && !$member_rule['is_upload'] && exit('0,'.fc_lang('抱歉！您的会员组无权上传附件'));
        
        // 附件总大小判断
        if (!$this->member['adminid'] && $member_rule['attachsize']) {
            $data = $this->db->select_sum('filesize')->where('uid', $uid)->get('attachment')->row_array();
            $filesize = (int)$data['filesize'];
            $filesize > $member_rule['attachsize'] * 1024 * 1024 && exit('0,'.fc_lang('附件空间不足！您的附件总空间%s，现有附件%s。', $member_rule['attachsize'].'MB', dr_format_file_size($filesize)));
        }

        if (IS_POST) {
            $code = str_replace(' ', '+', $this->input->post('code'));
            list($size, $ext, $path) = explode('|', dr_authcode($code, 'DECODE'));
            $path = $path ? SYS_UPLOAD_PATH.'/'.$path.'/' : SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
            !is_dir($path) && dr_mkdirs($path);
            $this->load->library('upload', array(
                'max_size' => (int)$size * 1024,
                'overwrite' => FALSE,
                'file_name' => substr(md5(time()), rand(0, 20), 10),
                'upload_path' => $path,
                'allowed_types' => str_replace(',', '|', $ext),
                'file_ext_tolower' => TRUE,
            ));
            if ($this->upload->do_upload('Filedata')) {
                $info = $this->upload->data();
                $site = (int)$this->input->post('siteid');
                $this->load->model('attachment_model');
                $this->attachment_model->siteid = $site ? $site : SITE_ID;
                $result = $this->attachment_model->upload($uid, $info);
                !is_array($result) && exit('0,'.$result);
                list($id, $file, $_ext) = $result;
                $icon = is_file(WEBPATH.'statics/admin/images/ext/'.$_ext.'.gif') ? THEME_PATH.'admin/images/ext/'.$_ext.'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
                //唯一ID,文件全路径,图标,文件名称,文件大小,扩展名
                exit($id.','.dr_file($file).','.$icon.','.str_replace(array('|', '.'.$_ext), '', $info['client_name']).','.dr_format_file_size($info['file_size'] * 1024).','.$_ext);
            } else {
                exit('0,'.$this->upload->display_errors('', ''));
            }
        }
    }

    /**
     * 新ajax文件上传处理
     *
     * @return void
     */
    public function new_ajax_upload() {

        // 游客不允许上传，未获取到会员信息时提示游客无法上传
        !$this->member && exit(json_encode(array('code'=>0, 'msg'=>fc_lang('游客不允许上传附件'))));
        
        // 会员组权限
        $member_rule = $this->get_cache('member', 'setting', 'permission', $this->member['mark']);

        // 是否允许上传附件
        !$this->member['adminid'] && !$member_rule['is_upload'] && exit(json_encode(array('code'=>0, 'msg'=>fc_lang('抱歉！您的会员组无权上传附件'))));
        
        // 附件总大小判断
        if (!$this->member['adminid'] && $member_rule['attachsize']) {
            $data = $this->db->select_sum('filesize')->where('uid', $this->uid)->get('attachment')->row_array();
            $filesize = (int)$data['filesize'];
            $filesize > $member_rule['attachsize'] * 1024 * 1024 && exit(json_encode(array('code'=>0, 'msg'=>fc_lang('附件空间不足！您的附件总空间%s，现有附件%s。', $member_rule['attachsize'].'MB', dr_format_file_size($filesize)))));
        }

        $code = str_replace(' ', '+', $this->input->get('code'));
        list($size, $ext, $path) = explode('|', dr_authcode($code, 'DECODE'));

        $path = $path ? SYS_UPLOAD_PATH.'/'.$path.'/' : SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
        !is_dir($path) && dr_mkdirs($path);


        $this->load->library('upload', array(
            'max_size' => (int)$size * 1024,
            'overwrite' => FALSE,
            'file_name' => substr(md5(time()), rand(0, 20), 10),
            'upload_path' => $path,
            'allowed_types' => str_replace(',', '|', $ext),
            'file_ext_tolower' => TRUE,
        ));

        if ($this->upload->do_upload('file')) {
            $info = $this->upload->data();
            $site = (int)$this->input->get('siteid');
            $site = $site ? $site : SITE_ID;
            $this->load->model('attachment_model');
            $this->attachment_model->siteid = $site;
            $result = $this->attachment_model->upload($this->uid, $info);
            !is_array($result) && exit('0,'.$result);
            list($id, $file, $_ext) = $result;
            $icon = is_file(WEBPATH.'statics/admin/images/ext/'.$_ext.'.gif') ? THEME_PATH.'admin/images/ext/'.$_ext.'.gif' : THEME_PATH.'admin/images/ext/blank.gif';
            echo json_encode(array('code'=>1, 'icon'=>$icon, 'id'=>$id, 'size'=>dr_format_file_size($info['file_size'] * 1024), 'name'=>str_replace(array('|', '.'.$_ext), '', $info['client_name'])));exit;
        } else {
            echo json_encode(array('code'=>0, 'msg'=> $this->upload->client_name.' : '.$this->upload->display_errors('', '')));exit;
        }
    }

    /**
     * 删除附件
     */
    public function swfdelete() {

        if (!$this->uid) {
            return NULL;
        }

        $id = (int)$this->input->post('id');
        $site = (int)$this->input->get('siteid');
        $this->load->model('attachment_model');
        $this->attachment_model->siteid = $site ? $site : SITE_ID;

        // 删除未使用
        $data = $this->db->where('id', $id)->where('uid', $this->uid)->get('attachment_unused')->row_array();
        if ($data) {
            // 删除附件
            $this->db->delete('attachment', 'id='.$id);
            $this->db->delete('attachment_unused', 'id='.$id);
            $this->attachment_model->_delete_attachment($data);
        }
    }

    /**
     * 网站附件浏览
     */
    public function myattach() {

        !$this->member['adminid'] && exit(fc_lang('只有管理组才能附件浏览'));

        $this->load->helper('directory');
        $this->input->get('dir').PHP_EOL;
        $dir = trim(trim(str_replace('.', '', $this->input->get('dir')), '/'), DIRECTORY_SEPARATOR);
        $root = SYS_ATTACHMENT_DIR ? (WEBPATH.trim(SYS_ATTACHMENT_DIR, '/').'/') : WEBPATH;
        $root = SYS_ATTACHMENT_DIR == '/' ? WEBPATH : $root;
        $path = $dir ? $root.$dir.'/' : $root;
        $list = array();
        $data = directory_map($path, 1);
        $fext = $this->input->get('ext');
        $exts = explode('|', $fext);
        $fcount = max(1, (int)$this->input->get('fcount'));
        $furl = '/index.php?s=member&c=api&m=myattach&ext='.$fext.'&fcount='.$fcount;

        if ($data) {
            foreach ($data as $t) {
                if (is_dir($path.'/'.$t)) {
                    $name = trim($t, DIRECTORY_SEPARATOR);
                    $list[] = array(
                        'type' => 'dir',
                        'name' => $name,
                        'icon' => THEME_PATH.'admin/images/ext/dir.gif',
                        'file' => $furl.'&dir='.str_replace($root, '', $path.$name),
                    );
                } else {
                    $ext = trim(strrchr($t, '.'), '.');
                    if ($ext != 'php' && in_array($ext, $exts)) {
                        $list[] = array(
                            'type' => 'file',
                            'name' => $t,
                            'size' => dr_format_file_size(@filesize($path.$t)),
                            'file' => SITE_URL.str_replace(WEBPATH, '', $path).$t,
                            'icon' => is_file(WEBPATH.'statics/admin/images/ext/'.$ext.'.gif') ? THEME_PATH.'admin/images/ext/'.$ext.'.gif' : THEME_PATH.'admin/images/ext/blank.gif',
                        );
                    }
                }
            }
        }

        $this->template->assign(array(
            'list' => $list,
            'path' => str_replace(WEBPATH, '/', $path),
            'purl' => $furl.'&dir='.dirname(str_replace($root, '/', $path)),
            'parent' => $dir,
            'fcount' => $fcount,
        ));
        $this->template->display('myattach.html', 'admin');
    }
    
    // 输入上传信息
    public function upload_input() {
        $this->template->assign(array(
            'file' => dr_safe_replace($_GET['file']),
            'title' => dr_safe_replace($_GET['title']),
        ));
        $this->template->display('upload_input.html', 'admin');
    }
    

    /**
     * Ueditor上传(图片)
     * 向浏览器返回数据json数据
     * {
     *   'url'      :'a.jpg',   //保存后的文件路径
     *   'title'    :'hello',   //文件描述，对图片来说在前端会添加到title属性上
     *   'original' :'b.jpg',   //原始文件名
     *   'state'    :'SUCCESS'  //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
     * }
     * @return void
     */
    public function ueupload() {

        !$this->uid && exit("{'url':'','title':'','original':'','state':'".fc_lang('会话超时，请重新登录')."'}");
        
        // 是否允许上传附件
        !$this->member['adminid'] && !$this->member_rule['is_upload'] && exit("{'url':'','title':'','original':'','state':'".fc_lang('抱歉！您的会员组无权上传附件')."'}");
        

        if (!$this->member['adminid'] && $this->member_rule['attachsize']) { // 附件总大小判断
            $data = $this->db->select_sum('filesize')->where('uid', $this->uid)->get('attachment')->row_array();
            $filesize = (int)$data['filesize'];
            $filesize > $this->member_rule['attachsize'] * 1024 * 1024 && exit("{'url':'','title':'','original':'','state':'".fc_lang('附件空间不足！您的附件总空间%s，现有附件%s。', $this->member_rule['attachsize'].'MB', dr_format_file_size($filesize))."'}");
        }
        
        $path = SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
        !is_dir($path) && dr_mkdirs($path);

        $type = $this->input->get('type');
        $_ext = $type == 'img' ? 'gif|jpg|png|jpeg' :
            'gz|7z|tar|ppt|pptx|xls|xlsx|rar|doc|docx|zip|pdf|txt|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|mov|wmv|mp4|webm';

        $this->load->library('upload', array(
            'max_size' => '999999',
            'overwrite' => FALSE, // 是否覆盖
            'file_name' => substr(md5(time()), 0, 10), // 文件名称
            'upload_path' => $path, // 上传目录
            'allowed_types' => $_ext,
        ));
        if ($this->upload->do_upload('upfile')) {
            $info = $this->upload->data();
            $this->load->model('attachment_model');
            $result = $this->attachment_model->upload($this->uid, $info);
            !is_array($result) && exit('0,'.$result);
            list($id, $file, $_ext) = $result;
            $url = $type == 'file' ? dr_down_file($id) : dr_file($file);
            $title = htmlspecialchars($this->input->post('pictitle', TRUE), ENT_QUOTES);
            exit("{'id':'".$id."','fileType':'.".$_ext."', 'url':'".$url."','title':'".$title."','original':'" . str_replace('|', '_', $info['client_name']) . "','state':'SUCCESS'}");
        } else {
            exit("{'url':'','title':'','original':'','state':'".$this->upload->display_errors('', '')."'}");
        }
    }

    /**
     * Ueditor附件上传
     * 向浏览器返回数据json数据
     * {
     *   'url'      :'a.rar',        //保存后的文件路径
     *   'fileType' :'.rar',         //文件描述，对图片来说在前端会添加到title属性上
     *   'original' :'编辑器.jpg',   //原始文件名
     *   'state'    :'SUCCESS'       //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
     * }
     */
    public function uefile() {

    }

    /**
     * Ueditor下载远程图片
     * 返回数据格式
     * {
     *   'id'   : '新图片id一ue_separate_ue新地址二ue_separate_ue新地址三',
     *   'url'   : '新地址一ue_separate_ue新地址二ue_separate_ue新地址三',
     *   'srcUrl': '原始地址一ue_separate_ue原始地址二ue_separate_ue原始地址三'，
     *   'tip'   : '状态提示'
     * }
     * @return void
     */
    public function uecatcher() {


    }

    /**
     * Ueditor未使用的图片
     * 图片id|地址一ue_separate_ue图片id|地址二ue_separate_ue图片id|地址三
     * @return void
     */
    public function uemanager() {

        if (!$this->uid) {
            return NULL;
        }

        $this->load->model('attachment_model');
        $data = $this->attachment_model->get_unused($this->uid, 'jpg,png,gif|jpeg');
        if (!$data) {
            return NULL;
        }

        $result = array();
        foreach ($data as $t) {
            $result[] = dr_file($t['attachment']).'?dr_image_id='.$t['id'];
        }
        echo implode('ue_separate_ue', $result);
    }

    /**
     * 汉字转换拼音
     */
    public function pinyin() {

        $name = $this->input->get('name', TRUE);
        !$name && exit('');

        $this->load->library('pinyin');
        $py = $this->pinyin->result($name);
        if (strlen($py) > 12) {
            exit($this->pinyin->result($name, 0));
        }
        exit($py);
    }

    /**
     * 标题检查
     */
    public function checktitle() {

        $id = (int)$this->input->get('id');
        $title = $this->input->get('title', TRUE);
        $module = $this->input->get('module');
        
        (!$title || !$module) && exit('');

        $num = $this->db->where('id<>', $id)->where('title', $title)->count_all_results(SITE_ID.'_'.$module);
        $num ? exit(fc_lang('<font color=red>'.fc_lang('重复').'</font>')) : exit('');
    }

    /**
     * 提取关键字
     */
    public function getkeywords() {

        $kw = $this->input->get('kw', TRUE);
        $kw = dr_safe_replace($kw ? $kw : $this->input->get('title'));
        // 返回数据


        $rt = '';

        //tag数据
        $tags = $this->dcache->get('tags-'.SITE_ID);
        if ($tags) {
            foreach ($tags as $t) {
                // 找到了
                if (strpos($kw, $t['name']) !== false) {
                    $rt.= ','.$t['tags'];
                }
            }
        }

        if ($rt) {
            exit(trim($rt, ',')) ;
        }

        $return = array();
        //tag数据
        $tags = $this->dcache->get('tag-'.SITE_ID);
        if ($tags) {
            foreach ($tags as $t) {
                strpos($kw, $t) !== false && $return[] = $t;
            }
        }
        $data = @file_get_contents('http://keyword.discuz.com/related_kw.html?ics=utf-8&ocs=utf-8&title='.rawurlencode($kw).'&content='.rawurlencode($kw));

        if ($data) {
            $xml = xml_parser_create();
            xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($xml, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($xml, $data, $values, $index);
            xml_parser_free($xml);
            foreach ($values as $v) {
                $kw = trim($v['value']);
                strlen($kw) > 5 && ($v['tag'] == 'kw' || $v['tag'] == 'ekw') && $return[] = $kw;
            }
        }

        echo @implode(',', $return);exit;
    }

    /**
     * 文件信息
     */
    public function fileinfo() {

        $this->load->helper('system');
        $key = dr_safe_replace($this->input->get('name'));
        $info = dr_file_info($key);
        $file = $info['attachment'] ? dr_get_file($info['attachment']) : $key;
        if (in_array($info['fileext'], array('jpg', 'jpeg', 'gif', 'png'))) {
            echo '<p style="text-align: center"><img src="'.$file.'" width="'.min($info['width'], 400).'" ></p>';
            echo '<p style="text-align: center"><a target="_blank" href="'.$file.'">'.fc_lang('查看原图').'</a></p>';
        } else {
            echo '<p style="text-align: center"><a href="'.$file.'" target=_blank>'.($info['filename'] ? $info['filename'] : $file).'</a></p>';
        }
    }

    /**
     * 下载文件
     */
    public function file() {

        $id = (int)$this->input->get('id');
        $info = get_attachment($id);
        $this->template->admin();

        !$info && $this->admin_msg(fc_lang('附件不存在或者已经被删除'));

        // 是否允许下载附件
        if (!$this->uid && !$this->member_rule['is_download']) {
            $this->msg(fc_lang('游客不允许下载附件，请登录'), dr_member_url('login/index'), 0, 3);
        } elseif (!$this->member['adminid'] && !$this->member_rule['is_download']) {
            $this->msg(fc_lang('您所在的会员组【%s】无权限下载附件', $this->member['groupname']), dr_member_url('login/index'), 0, 3);
        }

        // 虚拟币与经验值检查
        $mark = 'attachment-'.$id;
        $table = $this->db->dbprefix('member_scorelog');
        if ($this->member_rule['download_score']
            && !$this->db->where('type', 1)->where('mark', $mark)->count_all_results($table)) {
            // 虚拟币不足时，提示错误
            $this->member_rule['download_score'] + $this->member['score'] < 0 && $this->admin_msg(fc_lang('下载附件需要%s%s', SITE_SCORE, abs($this->member_rule['download_score'])));
            // 虚拟币扣减
            $this->member_model->update_score(1, $this->uid, (int)$this->member_rule['download_score'], $mark, fc_lang('附件下载'));
        }
        // 经验值扣减
        $this->member_rule['download_experience']
        && !$this->db->where('type', 0)->where('mark', $mark)->count_all_results($table) 
        && $this->member_model->update_score(0, $this->uid, (int)$this->member_rule['download_experience'], $mark, fc_lang('附件下载'));
    

        $file = $info['attachment'];
        $this->db->where('id', $id)->set('download', 'download+1', FALSE)->update('attachment');

        if (strpos($file, ':/')) {
            //远程文件
            header("Location: $file");
        } else {
            //本地文件
            $file = SYS_UPLOAD_PATH.'/'.str_replace('..', '', $file);
            $file = str_replace('member/uploadfile/member/uploadfile', 'member/uploadfile', $file);
            $name = urlencode(($info['filename'] ? $info['filename'] : $info['filemd5']).'.'.$info['fileext']);
            $this->load->helper('download');
            force_download($name, file_get_contents($file));
        }
    }


    /**
     * OAuth2授权登录
     */
    public function oauth() {

        $this->uid && $this->member_msg(fc_lang('请退出后再登录'), $_SERVER['HTTP_REFERER']);

        $appid = $this->input->get('id');
        $oauth = require WEBPATH.'config/oauth.php';
        $config	= $oauth[$appid];
        !$config && $this->member_msg(fc_lang('OAuth错误: 缺少OAuth参数'));

        $code = $this->input->get('code', TRUE);
        $MEMBER = $this->get_cache('member');
		
		if (IS_POST) {
			$user = dr_string2array(dr_authcode($this->input->post('code'), 'DECODE'));
            !$user && $this->member_msg(fc_lang('数据已过期，请重新登录'));
			//
			$data = $this->input->post('data');
			$type = $this->input->post('type');
			$error1 = $error2 = '';
			
			if ($type == 1) {
				// 登录
				$rt = $this->member_model->login($data['username'], $data['password'], 36000);
				if (strlen($rt) > 3) {
				    // 登录成功
                    !$this->uid && $this->member_msg(fc_lang('绑定失败，请重新登录'));
					// 绑定到此账号
					$user['uid'] = $this->uid;
					$this->db->insert('member_oauth', $user);
                    $this->hooks->call_hook('member_login', $data); // 登录成功挂钩点
					$this->member_msg(dr_weixin_emoji($user['nickname']).'，'.fc_lang('登录成功').$rt, dr_member_url('home/index'), 1, 1);
				} elseif ($rt == -1) {
					$error1 = fc_lang('会员不存在');
				} elseif ($rt == -2) {
					$error1 = fc_lang('密码不正确');
				} elseif ($rt == -3) {
					$error1 = fc_lang('Ucenter注册失败');
				} elseif ($rt == -4) {
					$error1 = fc_lang('Ucenter：会员名称不合法');
				}
			} else {
				// 注册
				$id = $this->member_model->register($data);
				if ($id > 0) {
				    // 注册成功
                    $data['uid'] = $this->uid;
                    $this->hooks->call_hook('member_register_after', $data); // 注册之后挂钩点
                    // 注册后的登录
                    $rt = $this->member_model->login($id, $data['password'], 86400, 0, 1);
                    strlen($rt) > 3 && $this->hooks->call_hook('member_login', $data); // 登录成功挂钩点
					// 绑定到此账号
					$user['uid'] = $id;
					$this->db->insert('member_oauth', $user);
					$this->member_msg(dr_weixin_emoji($user['nickname']).'，'.fc_lang('登录成功').$rt, dr_member_url('home/index'), 1, 1);
                } elseif ($id == -1) {
					$error = array('name' => 'username', 'msg' => fc_lang('该会员【%s】已经被注册', $data['username']));
				} elseif ($id == -2) {
					$error = array('name' => 'email', 'msg' => fc_lang('邮箱格式不正确'));
				} elseif ($id == -3) {
					$error = array('name' => 'email', 'msg' => fc_lang('该邮箱【%s】已经被注册', $data['email']));
				} elseif ($id == -4) {
					$error = array('name' => 'username', 'msg' => fc_lang('同一IP在限制时间内注册过多'));
				} elseif ($id == -5) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：会员名称不合法'));
				} elseif ($id == -6) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：包含不允许注册的词语'));
				} elseif ($id == -7) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：Email格式有误'));
				} elseif ($id == -8) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：Email不允许注册'));
				} elseif ($id == -9) {
					$error = array('name' => 'username', 'msg' => fc_lang('Ucenter：Email已经被注册'));
				} elseif ($id == -10) {
					$error = array('name' => 'phone', 'msg' => fc_lang('手机号码必须是11位的整数'));
				} elseif ($id == -11) {
					$error = array('name' => 'phone', 'msg' => fc_lang('该手机号码已经注册'));
				}
				$error2 = $error['msg'];
			}
			
			$this->template->assign(array(
				'type' => $type,
				'code' => dr_authcode(dr_array2string($user), 'ENCODE'),
				'oauth' => $user,
				'error_1' => $error1,
				'error_2' => $error2,
            	'regfield' => $MEMBER['setting']['regfield'],
			));
			$this->template->display('oauth.html');
			exit;
		}
		
        $config['url'] = SITE_URL.'index.php?s=member&c=api&m=oauth&id='.$appid; // 回调地址设置
        $this->load->library('OAuth2');

        // OAuth
        $oauth = $this->oauth2->provider($appid, $config);

        if (!$code) {
            // 登录授权页
            try {
                $oauth->authorize();
            } catch (OAuth2_Exception $e) {
                $this->member_msg(fc_lang('OAuth授权错误').' _ '.$e);
            }
        } else {
            // 回调返回数据
            try {
                $user = $oauth->get_user_info($oauth->access($code));
                if (is_array($user) && $user['oid']) {
                    !$user['nickname'] && $user['nickname'] = substr($user['oid'], 0, 10);
                    $code = $this->member_model->OAuth_login($appid, $user);
					  if ($code == 'bang') {
							// 绑定账号
							$this->template->assign(array(
								'type' => 1,
								'code' => dr_authcode(dr_array2string($user), 'ENCODE'),
								'oauth' => $user,
            					'regfield' => $MEMBER['setting']['regfield'],
							));
							$this->template->display('oauth.html');
						} else {
							// 直接注册
                    		$this->member_msg(dr_weixin_emoji($user['nickname']).'，'.fc_lang('登录成功').$code, dr_member_url('home/index'), 1, 3);
						}
                } else {
                    $this->member_msg(fc_lang('OAuth回调错误: 获取用户信息失败'));
                }
            } catch (OAuth2_Exception $e) {
                $this->member_msg(fc_lang('OAuth回调错误: 获取用户信息失败').' - '.$e);
            }
        }
    }

    /**
     * 更新模型浏览数
     */
    public function hits() {

        $id = (int)$this->input->get('id');
        $mid = (int)$this->input->get('mid');
        $mod = $this->get_cache('space-model', $mid);
        if (!$mod) {
            $data = $this->callback_json(array('html' => 0));
            echo $this->input->get('callback', TRUE).'('.$data.')';exit;
        }

        $table = $this->db->dbprefix('space_'.$mod['table']);
        $name = $table.'-space-hits-'.$id;
        $hits = (int)$this->get_cache_data($name);
        if (!$hits) {
            $data = $this->db->where('id', $id)->select('hits')->get($table)->row_array();
            $hits = (int)$data['hits'];
        }

        $hits++;
        $this->set_cache_data($name, $hits, SYS_CACHE_SPACE);
        $this->db->where('id', $id)->update($table, array('hits' => $hits));

        $data = $this->callback_json(array('html' => $hits));
        echo $this->input->get('callback', TRUE).'('.$data.')';exit;
    }

    /**
     * 会员验证登录
     *
     * @param	string	$username	用户名
     * @param	string	$password	明文密码
     * @param	intval	$expire	    会话生命周期
     * @param	intval	$back	    返回uid
     * @return	string|intval
     * string	EMAIL
     */
    public function login() {
        $data = $this->member_model->login($this->input->get('username'), $this->input->get('password'), NULL, $this->input->get('back'));
        echo $data['email'];
    }

    /**
     * 在线聊天部分
     */
    public function webchat() {

        $uid = (int)$this->input->get('uid');
        $username = $this->input->get('username');
        $callback = isset($_GET['callback']) ? $this->input->get('callback', TRUE) : 'callback';
        
        !dr_is_app('pms') && exit;
        
        $this->load->add_package_path(FCPATH.'app/pms/');
        $this->load->model('pm_model');

        if ($this->input->get('action') == 'more') {
            ob_start();
            list($touid, $list) = $this->pm_model->read_limit_page($uid, 1);
            $this->template->assign(array(
                'list' => $list,
                'touid' => $uid,
                'action' => 'more',
            ));
            $this->template->display('pm_webchat.html');
            $html = ob_get_contents();
            ob_clean();
        } elseif ($this->input->get('action') == 'send') {
            $data['message'] = $this->input->get('msg', TRUE);
            $data['username'] = $username;
            
            (!$this->uid || !$this->member) && exit($callback . '(' . json_encode(array('status' => 0, 'msg' => fc_lang('会话超时，请重新登录'))) . ')');
            
            $error = $this->pm_model->send($this->uid, $this->member['username'], $data);
            $error && exit($callback . '(' . json_encode(array('status' => 0, 'msg' => $error)) . ')');
            exit($callback . '(' . json_encode(array('status' => 1)) . ')');
        } else {
            ob_start();
            $this->template->assign(array(
                'touid' => $uid,
                'action' => 0,
                'syntime' => 10 * 1000,
                'username' => $username,
            ));
            $this->template->display('pm_webchat.html');
            $html = ob_get_contents();
            ob_clean();
        }
        exit($callback . '(' . json_encode(array('html' => $html)) . ')');
    }

    // 增加极验验证
    public function geetest() {

        require FCPATH.'dayrui/libraries/Geetestlib.php';

        $GtSdk = new GeetestLib();
        $return = $GtSdk->register();

        if ($return) {
            $this->session->set_userdata('gtserver', 1);
            $result = array(
                'success' => 1,
                'gt' => SYS_GEE_CAPTCHA_ID,
                'challenge' => $GtSdk->challenge
            );
            echo json_encode($result);
        }else{
            $this->session->set_userdata('gtserver', 0);
            $rnd1 = md5(rand(0,100));
            $rnd2 = md5(rand(0,100));
            $challenge = $rnd1 . substr($rnd2,0,2);
            $result = array(
                'success' => 0,
                'gt' => CAPTCHA_ID,
                'challenge' => $challenge
            );
            $this->session->set_userdata('challenge', $result['challenge']);
            echo json_encode($result);
        }
        exit;
    }

}