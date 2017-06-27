<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
require FCPATH.'branch/fqb/D_Common.php';

class M_Controller extends D_Common {

    public $space;

    /**
     * 构造函数继承公共Module类
     */
    public function __construct() {
        parent::__construct();
        $this->space = $this->get_cache('member', 'setting', 'space');
        if (!$this->space['open']) {
            $this->msg(fc_lang('模块未安装'));
        }
        define('MOD_DIR', 'space'); // 定义模块目录
        // 模块风格目录
        define('SPACE_THEME_PATH', HOME_THEME_PATH);
        define('MODULE_THEME_PATH', SPACE_THEME_PATH);
        // 设置模块模板
        $this->template->module('space');
    }


    /**
     * 空间模型管理
     */
    protected function space_content_index() {

        $this->_is_space();

        $mid = (int)str_replace('space', '', $this->router->class);
        $model = $this->get_cache('space-model', $mid);
        if (!$model) {
            $this->member_msg(fc_lang('会员空间模型不存在'));
        } elseif (!$model['setting'][$this->markrule]['use']) {
            $this->member_msg(fc_lang('无权限使用'));
        }

        $table = $this->db->dbprefix('space_'.$model['table']);

        if (IS_POST && $this->input->post('action') == 'delete') {
            // 删除操作
            $id = (int)$this->input->post('id');
            $row = $this->db->where('id', $id)->get($table)->row_array();
            if ($row['uid'] == $this->uid) {
                //
                $this->db->where('id', $id)->delete($table);
                // 删除附件
                $this->load->model('attachment_model');
                $this->attachment_model->delete_for_table($table.'-'.$id);
                // 积分处理
                $experience = (int)$model['setting'][$this->markrule]['experience'];
                if ($experience > 0) {
                    $this->member_model->update_score(0, $this->uid, -$experience, '', "delete");
                }
                // 虚拟币处理
                $score = (int)$model['setting'][$this->markrule]['score'];
                if ($score > 0) {
                    $this->member_model->update_score(1, $this->uid, -$score, '', "delete");
                }
                exit(dr_json(1, fc_lang('操作成功，正在刷新...'), $id));
            } else {
                exit(dr_json(0, fc_lang('无权限使用')));
            }
        } elseif (IS_POST && $this->input->post('action') == 'remove') {
            // 移动栏目
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('对不起，数据被删除或者查询不存在')));
            }
            $catid = (int)$this->input->post('catid');
            if ($catid) {
                $this->db->where('uid', $this->uid)->where_in('id', $ids)->update($table, array(
                    'catid' => $catid
                ));
            } else {
                exit(dr_json(0, fc_lang('请选择一个栏目')));
            }

            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $this->load->model('space_category_model');
        $category = $this->space_category_model->get_data($mid);

        $kw = dr_safe_replace($this->input->get('kw', TRUE));
        $total = (int)$this->input->get('total');
        $order = dr_get_order_string(dr_safe_replace($this->input->get('order', TRUE)), 'updatetime desc');

        // 查询结果
        $list = array();
        if (!$total) {
            $this->db->select('count(*) as total');
            $this->db->where('uid', (int)$this->uid);
            if ($kw) {
                $this->db->like('title', $kw);
            }
            $data = $this->db->get($table)->row_array();
            $total = (int)$data['total'];
        }

        if ($total) {
            $page = max((int)$this->input->get('page'), 1);
            $this->db->where('uid', (int)$this->uid);
            if ($kw) {
                $this->db->like('title', $kw);
            }
            $this->db->order_by($order);
            $list = $this->db->limit($this->pagesize, $this->pagesize * ($page - 1))->get($table)->result_array();
        }

        $this->template->assign(array(
            'mid' => $mid,
            'dclass' => $this->router->class,
            'category' => $category,
        ));

        $url = dr_member_url('space/'.$this->router->class.'/index');
        $this->template->assign(array(
            'kw' => $kw,
            'list' => $list,
            'isdl' => $model['setting']['dl'], // 是否独立模型
            'pages'	=> $this->get_member_pagination($url.'&total='.$total, $total),
            'page_total' => $total,
            'select' => $this->select_space_category($category, 0, ' name=\'catid\'', '  --  ', 1),
            'moreurl' => $url
        ));
        $this->template->display(is_file(FCPATH.'module/space/templates/member/'.MEMBER_TEMPLATE.'/space_'.$model['table'].'_index.html') ? 'space_'.$model['table'].'_index.html' : 'space_content_index.html');
    }

    /**
     * 添加空间模型内容
     */
    protected function space_content_add() {

        $this->_is_space();

        $mid = (int)str_replace('space', '', $this->router->class);
        $model = $this->get_cache('space-model', $mid);
        if (!$model) {
            $this->member_msg(fc_lang('会员空间模型不存在'));
        } elseif (!$model['setting'][$this->markrule]['use']) {
            $this->member_msg(fc_lang('无权限使用'));
        }

        $this->load->model('space_content_model');
        $this->load->model('space_category_model');
        $category = $this->space_category_model->get_data($mid);
        $this->space_content_model->tablename = $this->db->dbprefix('space_'.$model['table']);

        // 虚拟币检查
        $score = (int)$model['setting'][$this->markrule]['score'];
        if ($score && $score + $this->member['score'] < 0) {
            $this->member_msg(fc_lang('每次发布需要%s'.SITE_SCORE.'，当前余额%'.SITE_SCORE.'', abs($score), $this->member['score']));
        }
        // 日投稿上限检查
        if ($model['setting'][$this->markrule]['postnum']) {
            $total = $this->db
                        ->where('uid', $this->uid)
                        ->where('DATEDIFF(from_unixtime(inputtime),now())=0')
                        ->count_all_results($this->space_content_model->tablename);
                    if ($total >= $model['setting'][$this->markrule]['postnum']) {
                $this->member_msg(fc_lang('每日发布数量不得超过%s条', $model['setting'][$this->markrule]['postnum']));
            }
        }
        // 投稿总数检查
        if ($model['setting'][$this->markrule]['postcount']) {
            $total = $this->db->where('uid', $this->uid)->count_all_results($this->space_content_model->tablename);
            if ($total >= $model['setting'][$this->markrule]['postcount']) {
                $this->member_msg(fc_lang('发布总数不得超过%s条', $model['setting'][$this->markrule]['postcount']));
            }
        }
        if (IS_POST) {

            // 栏目参数
            $catid = (int)$this->input->post('catid');

            // 设置uid便于校验处理
            $_POST['data']['uid'] = $this->uid;
            $_POST['data']['author'] = $this->member['username'];
            $_POST['data']['inputtime'] = $_POST['data']['updatetime'] = SYS_TIME;
            $data = $this->validate_filter($model['field']);

            // 验证出错信息
            if (isset($data['error'])) {
                $error = $data;
                $data = $this->input->post('data', TRUE);
            } elseif (!$model['setting']['dl'] && !$catid) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('请选择一个栏目'));
            } elseif (!$model['setting']['dl']
                && ($category[$catid]['child'] || $category[$catid]['modelid'] != $mid)) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('该栏目不允许发布信息'));
            } else {

                // 设定文档默认值
                $data[1]['uid'] = $this->uid;
                $data[1]['catid'] = $catid;
                $data[1]['status'] = (int)$model['setting'][$this->markrule]['verify'] ? 0 : 1;
                $data[1]['author'] = $this->member['username'];
                $data[1]['inputtime'] = $data[1]['updatetime'] = SYS_TIME;
                $data[1]['displayorder'] = $data[1]['hits'] = 0;

                // 发布文档
                if (($id = $this->space_content_model->add($data[1])) != FALSE) {
                    $mark = $this->space_content_model->tablename.'-'.$id;
                    if ($data[1]['status']) {
                        // 积分处理
                        $experience = (int)$model['setting'][$this->markrule]['experience'];
                        if ($experience) {
                            $this->member_model->update_score(0, $this->uid, $experience, $mark, "发布内容", 1);
                        }
                        // 虚拟币处理
                        $score = (int)$model['setting'][$this->markrule]['score'];
                        if ($score) {
                            $this->member_model->update_score(1, $this->uid, $score, $mark, "发布内容", 1);
                        }
                    }
                    // 附件归档到文档
                    $this->attachment_handle($this->uid, $mark, $model['field']);
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('space/'.$this->router->class.'/index'), 1);
                }
            }

            if (IS_AJAX || IS_API_AUTH) {
                exit(dr_json(0, $error['msg'], $error['error']));
            }

            $data = $data[1];
            unset($data['id']);
        }

        $this->template->assign(array(
            'isdl' => $model['setting']['dl'], // 是否独立模型
            'purl' => dr_member_url('space/'.$this->router->class.'/add'),
            'error' => $error,
            'verify' => 0,
            'select' => $this->select_space_category($category, (int)$data['catid'], 'class="form-control" name=\'catid\'', NULL, 1),
            'listurl' => dr_member_url('space/'.$this->router->class.'/index'),
            'myfield' => $this->field_input($model['field'], $data, TRUE),
            'meta_name' => fc_lang('发布'),
            'model_name' => $model['name'],
            'result_error' => $error,
        ));
        $this->template->display(is_file(FCPATH.'module/space/templates/member/'.MEMBER_TEMPLATE.'/space_'.$model['table'].'_add.html') ? 'space_'.$model['table'].'_add.html' : 'space_content_add.html');
    }

    /**
     * 修改空间模型内容
     */
    protected function space_content_edit() {

        $this->_is_space();

        $id = (int)$this->input->get('id');
        $mid = (int)str_replace('space', '', $this->router->class);
        $model = $this->get_cache('space-model', $mid);
        if (!$model) {
            $this->member_msg(fc_lang('会员空间模型不存在'));
        } elseif (!$model['setting'][$this->markrule]['use']) {
            $this->member_msg(fc_lang('无权限使用'));
        }

        $this->load->model('space_category_model');
        $this->load->model('space_content_model');
        $category = $this->space_category_model->get_data($mid);
        $this->space_content_model->tablename = $this->db->dbprefix('space_'.$model['table']);
        $data = $this->space_content_model->get($this->uid, $id);
        if (!$data) {
            $this->member_msg(fc_lang('数据不存在'));
        }

        if (IS_POST) {

            // 栏目参数
            $catid = (int)$this->input->post('catid');

            // 设置uid便于校验处理
            $_POST['data']['updatetime'] = SYS_TIME;
            $post = $this->validate_filter($model['field']);

            // 验证出错信息
            if (isset($post['error'])) {
                $error = $post;
                $data = $this->input->post('data', TRUE);
            } elseif (!$model['setting']['dl'] && !$catid) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('请选择一个栏目'));
            } elseif (!$model['setting']['dl']
                && ($category[$catid]['child'] || $category[$catid]['modelid'] != $mid)) {
                $data = $this->input->post('data', TRUE);
                $error = array('error' => 'catid', 'msg' => fc_lang('该栏目不允许发布信息'));
            } else {

                // 设定文档默认值
                $post[1]['catid'] = $catid;
                $post[1]['status'] = (int)$model['setting'][$this->markrule]['verify'] ? 0 : 1;
                $post[1]['updatetime'] = SYS_TIME;

                // 修改文档
                if (($id = $this->space_content_model->edit($id, $data['uid'], $post[1])) != FALSE) {
                    $this->attachment_handle($this->uid, $this->space_content_model->tablename.'-'.$id, $model['field'], $data, $post[1]['status'] ? TRUE : FALSE);
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('space/'.$this->router->class.'/index'), 1);
                }
            }

            if (IS_AJAX || IS_API_AUTH) {
                exit(dr_json(0, $error['msg'], $error['error']));
            }

            $data = $data[1];
            unset($data['id']);
        }

        $this->template->assign(array(
            'isdl' => $model['setting']['dl'], // 是否独立模型
            'purl' => dr_member_url('space/'.$this->router->class.'/edit', array('id'=>$id)),
            'error' => $error,
            'verify' => 0,
            'select' => $this->select_space_category($category, (int)$data['catid'], 'class="form-control" name=\'catid\'', NULL, 1),
            'listurl' => dr_member_url('space/'.$this->router->class.'/index'),
            'myfield' => $this->field_input($model['field'], $data, TRUE),
            'meta_name' => fc_lang('发布'),
            'model_name' => $model['name'],
            'result_error' => $error,
        ));
        $this->template->display(is_file(FCPATH.'module/space/templates/member/'.MEMBER_TEMPLATE.'/space_'.$model['table'].'_add.html') ? 'space_'.$model['table'].'_add.html' : 'space_content_add.html');
    }

    /**
     * 判断当前空间是否可以使用
     */
    protected function _is_space($return = FALSE) {

        if (!MEMBER_OPEN_SPACE) {
            $this->member_msg(fc_lang('系统已经关闭了空间功能'));
        }

        // 判断会员组是否可以使用
        if (!$this->member['allowspace']) {
            if ($return) {
                return FALSE;
            } else {
                $this->member_msg(fc_lang('该会员组不允许使用空间'));
            }
        }

        // 空间信息
        $data = $this->db->where('uid', (int)$this->uid)->limit(1)->get('space')->row_array();
        if (!$data) {
            if ($return) {
                return FALSE;
            } else {
                $this->member_msg(fc_lang('该会员的空间还没有创建'));
            }
        }

        // 空间状态判断
        if (!$data['status']) {
            if ($return) {
                return FALSE;
            } else {
                $this->member_msg(fc_lang('空间正在审核中'));
            }
        }

        define('IS_SPACE_THEME', $data['style'] ? $data['style'] : 'default'); // 空间模板页面

    }

    /**
     * 栏目选择
     *
     * @param array			$data		栏目数据
     * @param intval/array	$id			被选中的ID，多选是可以是数组
     * @param string		$str		属性
     * @param string		$default	默认选项
     * @param intval		$onlysub	只可选择子栏目
     * @param intval		$is_push	是否验证权限
     * @return string
     */
    public function select_space_category($data, $id = 0, $str = '', $default = ' -- ', $onlysub = 0, $is_push = 0) {

        $cache = md5(dr_array2string($data).$id.$str.$default.$onlysub.$is_push);
        if ($cache_data = $this->get_cache_data($cache)) {
            return $cache_data;
        }

        $tree = array();
        $string = '<select class="form-control" '.$str.'>';

        if ($default) {
            $string.= "<option value='0'>$default</option>";
        }

        if (is_array($data)) {

            foreach($data as $t) {

                // 选中操作
                $t['selected'] = '';
                if (is_array($id)) {
                    $t['selected'] = in_array($t['id'], $id) ? 'selected' : '';
                } elseif(is_numeric($id)) {
                    $t['selected'] = $id == $t['id'] ? 'selected' : '';
                }

                // 是否可选子栏目
                $t['html_disabled'] = !empty($onlysub) && $t['child'] != 0 ? 1 : 0;

                $tree[$t['id']] = $t;
            }
        }

        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $str2 = "<optgroup label='\$spacer \$name'></optgroup>";

        $this->load->library('dtree');
        $this->dtree->init($tree);

        $string.= $this->dtree->get_tree_category(0, $str, $str2);
        $string.= '</select>';

        $this->set_cache_data($cache, $string, 7200);

        return $string;
    }

    // 空间的访问权限验证
    protected function _space_show($uid) {

        if (!$uid || $this->member['adminid']) {
            return;
        }

        $this->load->model('sns_model');
        $config = $this->sns_model->config($uid);
        if (!$config) {
            return;
        }

        // 全部不允许访问
        if ($config['show_all']) {
            return;
        }

        // 允许ta粉丝访问
        if ($config['show_fans']
            && $this->db->where('uid', $uid)->where('fid', $this->uid)->count_all_results('sns_follow')) {
            return;
        }

        // 允许ta关注的人访问
        if ($config['show_follow']
            && $this->db->where('uid', $this->uid)->where('fid', $uid)->count_all_results('sns_follow')) {
            return;
        }

        return 1;
    }

    /**
     * 本地会员空间
     *
     * @return	array
     */
    protected function get_local_space() {

        $this->load->helper('directory');
        $file = directory_map(FCPATH.'statics/space/', 1);
        $data = array();
        if ($file) {
            foreach ($file as $t) {
                $t = basename($t);
                $config = FCPATH.'statics/space/'.$t.'/config.php';
                if (!in_array($t, array('admin', 'member')) && is_file($config)) {
                    $data[$t] = require $config;
                }
            }
        }
        return $data;
    }

    // 获取空间的uid
    protected function _get_space_uid($host, $domain) {

        $name = str_replace('.'.$domain, '', $host); // 删除主域名
        $data = $this->db->where('domain', $name)->get('space_domain')->row_array();
        return $data['uid'] ? $data['uid'] : 0;

    }
}