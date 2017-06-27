<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* v3.1.0  */
class D_Admin_Comment extends M_Controller {

    public $cid;
    public $uri; //
    public $back; // 返回uri
    public $rname; // 评论缓存名称
    public $cname; // 评论名称
    public $cconfig; // 评论配置
    public $mycfg_file; // 自定义扩展配置
    public $cache_file; // 分页数据缓存

    /**
     * 构造函数
     */

    public function __construct() {
        parent::__construct();
        $this->load->model('comment_model');
        $this->cache_file = md5($this->duri->uri(1).$this->uid.$this->sid.$this->input->ip_address().$this->input->user_agent()); // 缓存文件名称
    }

    // 设置空间操作评论
    public function space() {
        $this->uri = 'space/admin/comment/';
        $this->back = $this->_get_back_url('space/space/index');
        $this->rname = 'comment-space';
        $this->cname = fc_lang('空间评论');
        $this->comment_model->space();
        $this->cconfig = $this->get_cache('comment', $this->rname);
        $this->mycfg_file = FCPATH.'module/space/templates/admin/my_comment.html';
    }

    // 设置空间模型操作评论
    public function model($mid) {
    }

    // 设置模块操作评论
    public function module($dir) {
        $this->uri = $dir.'/admin/comment/';
        $this->back = $this->_get_back_url($dir.'/home/index');
        $this->rname = 'comment-module-'.$dir;
        $this->cname = fc_lang('内容评论');
        $this->comment_model->module($dir);
        $this->cconfig = $this->get_cache('comment', $this->rname);
        $this->mycfg_file = FCPATH.'module/'.$dir.'/templates/admin/my_comment.html';
    }

    // 设置模块扩展操作评论
    public function extend($dir) {
        $this->uri = $dir.'/admin/ecomment/';
        $this->rname = 'comment-extend-'.$dir;
        $this->cname = fc_lang('子内容评论');
        $this->comment_model->extend($dir);
        $this->cconfig = $this->get_cache('comment', $this->rname);
        $data = $this->comment_model->get_cdata((int)$this->input->get('cid'));
        $this->back = $this->_get_back_url($dir.'/extend/index', array('cid' => $data['cid']));
        $this->mycfg_file = FCPATH.'module/'.$dir.'/admin/my_comment_extend.html';
    }

    // 评论配置
    public function config() {

        $page = intval($_GET['page']);

        if (IS_POST) {
            $data = $this->input->post('data');
            if ($data['buy']) {
                // 初始化订单配置
                $data['verify'] = 0;
                $data['num'] = 1;
                $data['my'] = 0;
                $data['reply'] = 1;
            }
            $this->db->where('name', $this->rname)->update('comment', array(
                'value' => dr_array2string($data),
            ));
            $page = intval($_POST['page']);
            $this->system_model->comment();
        } else {
            $data = $this->db->where('name', $this->rname)->get('comment')->row_array();
            if (!$data) {
                $this->db->insert('comment', array(
                    'name' => $this->rname,
                    'value' => '',
                    'field' => '',
                ));
            }
            $data = dr_string2array($data['value']);
        }

        if (empty($data['review'])) {
            // 默认点评
            $data['review']['use'] = 0;
            $data['review']['score'] = 10;
            $data['review']['option'] = array();
            // 点评选项
            for ($i = 1; $i <= 9; $i++) {
                $data['review']['option'][$i] = array(
                    'field' => 'sort'.$i,
                    'name' => '选项'.$i,
                    'use' => 0,
                );
            }
            // 点评值
            for ($i = 1; $i <= 5; $i++) {
                $data['review']['value'][$i] = array(
                    'name' => $i.'星评价',
                    'use' => 0,
                );
            }
        }

        $this->template->assign(array(
            'data' => $data,
            'page' => $page,
            'menu' => $this->get_menu_v3(array(
                fc_lang('%s配置', $this->cname) => array($this->uri.'config', 'cog'),
                fc_lang('%s字段', $this->cname) => array('admin/field/index/rname/'.$this->rname.'/rid/0', 'plus'),
            )),
            'mycfg' => is_file($this->mycfg_file) ? $this->mycfg_file : 0,
            'myfield2' => $this->db->where('disabled', 0)->where('relatedid', 0)->where('relatedname', $this->rname)->order_by('displayorder ASC, id ASC')->get('field')->result_array(),
        ));
        $this->template->display('comment_config.html');
    }

    // 评论管理
    public function index() {

        $tid = (int)$this->input->get('tid');
        $cid = $this->cid = (int)$this->input->get('cid');
        $index = array();
        $is_review = (int)$this->cconfig['value']['review']['use'];

        if ($cid) {
            // 从内容处进来时
            list($table, $index) = $this->comment_model->get_table($cid, 1);
            $menu = array(
                fc_lang('返回') => array($this->back, 'mail-reply'),
                fc_lang('%s管理', $this->cname) => array($this->uri.'index/cid/'.$cid, 'comments'),
            );
            $show_url = $this->duri->uri2url($this->uri.'show/cid/'.$cid);
        } else {
            // 全部数据时
            $table = $this->comment_model->prefix.'_comment_data_';
            $menu = array(
                fc_lang('%s - 默认存储表', $this->cname) => array($this->uri.'index/tid/0', 'database'),
            );
            for ($i = 1; $i < 100; $i ++) {
                if (!$this->comment_model->mydb->query("SHOW TABLES LIKE '".$table.$i."'")->row_array()) {
                    break;
                }
                $menu[fc_lang('归档【%s】表', $i)] = array($this->uri.'index/tid/'.$i, 'database');
            }
            $table.= $tid;
            $show_url = $this->duri->uri2url($this->uri.'show/tid/'.$tid);
            $is_review = 0;
        }

        if ($this->input->post('action') == 'verify') {
            // 审核操作
            $ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
            foreach ($ids as $i) {
                $this->comment_model->verify($table, intval($i));
            }
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        } elseif ($this->input->post('action') == 'del') {
            // 删除操作
            $ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
            foreach ($ids as $i) {
                $row = $this->comment_model->mydb->where('id', intval($i))->get($table)->row_array();
                $row && $this->comment_model->delete($i, $row['cid']);
            }
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $field = array(
            'content' => array(
                'name' => fc_lang('关键字'),
                'fieldname' => 'content',
            ),
            'title' => array(
                'name' => fc_lang('主题名称'),
                'fieldname' => 'title',
            ),
            'cid' => array(
                'name' => fc_lang('内容Id'),
                'fieldname' => 'cid',
            )
        );
        $field = $this->cconfig['field'] ? array_merge($field, $this->cconfig['field']) : $field;

        // 数据库中分页查询
        list($data, $total, $param)	= $this->limit_page($table);
        $param['cid'] = $cid;
        $param['tid'] = $tid;
        $param['total'] = $total;

        $this->load->library('dip');
        $tpl = FCPATH.'dayrui/templates/admin/'.str_replace('-', '_', $this->rname).'.html';

        $this->template->assign(array(
            'uri' => $this->uri,
            'tpl' => str_replace(FCPATH, '/', $tpl),
            'menu' => $this->get_menu_v3($menu),
            'list' => $data,
            'total' => $total,
            'pages'	=> $this->get_pagination(dr_url(str_replace('/admin', '', $this->uri).'index', $param), $total),
            'param' => $param,
            'index' => $index,
            'field' => $field,
            'review' => $this->cconfig['value']['review'],
            'show_url' => $show_url,
            'is_review' => $is_review,
        ));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'comment_index.html');
    }

    // 查看
    public function show() {

        $id = (int)$this->input->get('id');
        $tid = (int)$this->input->get('tid');
        $cid = $this->cid = (int)$this->input->get('cid');

        if ($cid) {
            // 从内容处进来时
            $table = $this->comment_model->get_table($cid);
            $menu = array(
                fc_lang('返回') => array($this->back, 'reply'),
                fc_lang('评论管理') => array($this->uri.'index/cid/'.$cid, 'comments'),
            );
            $show_url = $this->uri.'show/cid/'.$cid;
        } else {
            // 全部数据时
            $table = $this->comment_model->prefix.'_comment_data_';
            $menu = array(
                fc_lang('默认存储表') => array($this->uri.'index/tid/0', 'database'),
            );
            for ($i = 1; $i < 100; $i ++) {
                if (!$this->comment_model->mydb->query("SHOW TABLES LIKE '".$table.$i."'")->row_array()) {
                    break;
                }
                $menu[fc_lang('归档【%s】表', $i)] = array($this->uri.'index/tid/'.$i, 'database');
            }
            $table.= $tid;
            $show_url = $this->uri.'show/tid/'.$tid;
        }

        $data = $this->comment_model->mydb->where('id', $id)->get($table)->row_array();
        // 数据验证
        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));

        if (IS_POST) {
            $update = $this->input->post('post');
            if ($this->cconfig['field']) {
                $my = $this->validate_filter($this->cconfig['field']);
                isset($my['error']) && $this->admin_msg($my['msg']);
                $update = array_merge($update, $my[1]);
            }
            $this->comment_model->mydb->where('id', $id)->update($table, $update);
            // 操作成功处理附件
            if ($data['uid'] && $my) {
                $this->attachment_handle(
                    $data['uid'],
                    $table.'-'.$id,
                    $this->cconfig['field'],
                    $my
                );
            }
            // 审核操作
            if (isset($update['status']) && $update['status']) {
                $this->comment_model->verify($table, $id);
                // 任务状态
                $this->member_model->update_admin_notice($this->uri.'show/tid/'.$tid.'/id/'.$id, 3);
            }

            $this->admin_msg(
                fc_lang('操作成功，正在刷新...'),
                $this->duri->uri2url($show_url.'/id/'.$id),
                1,
                1
            );
        }

        $menu[fc_lang('查看/修改')] = array($show_url.'/id/'.$id, 'edit');
        $this->load->library('dip');
        $tpl = APPPATH.'templates/admin/'.str_replace('-', '_', $this->rname).'_show.html';

        $this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
            'data' => $data,
            'menu' => $this->get_menu_v3($menu),
            'review' => $this->cconfig['value']['review'],
            'myfield' => $this->new_field_input($this->cconfig['field'], $data),
            'is_review' => (int)$this->cconfig['value']['review']['use'],
        ));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'comment_show.html');
    }

    /**
     * 条件查询
     *
     * @param	object	$select	查询对象
     * @param	intval	$where	是否搜索
     * @return	intval
     */
    protected function _where(&$select, $param) {

        // 存在POST提交时
        if (IS_POST) {
            $search = $this->input->post('data');
            $param['keyword'] = $search['keyword'];
            $param['start'] = $search['start'];
            $param['end'] = $search['end'];
            $param['field'] = $search['field'];
        }

        // 相对于内容
        $this->cid && $select->where('cid', $this->cid);

        // 存在search参数时，读取缓存文件
        if ($param) {
            if (isset($param['keyword']) && $param['keyword'] != '') {
                $field = $this->field;
                $param['field'] = $param['field'] ? $param['field'] : 'content';
                if ($param['field'] == 'id' || $param['field'] == 'cid') {
                    // 按id查询
                    $id = array();
                    $ids = explode(',', $param['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int)$i;
                    }
                    $select->where_in($param['field'], $id);
                } elseif ($field[$param['field']]['fieldtype'] == 'Linkage'
                    && $field[$param['field']]['setting']['option']['linkage']) {
                    // 联动菜单搜索
                    if (is_numeric($param['keyword'])) {
                        // 联动菜单id查询
                        $link = dr_linkage($field[$param['field']]['setting']['option']['linkage'], (int)$param['keyword'], 0, 'childids');
                        $link && $select->where($param['field'].' IN ('.$link.')');
                    } else {
                        // 联动菜单名称查询
                        $id = (int)$this->ci->get_cache('linkid-'.SITE_ID, $field[$param['field']]['setting']['option']['linkage']);
                        $id && $select->where($param['field'].' IN (select id from `'.$select->dbprefix('linkage_data_'.$id).'` where `name` like "%'.$param['keyword'].'%")');
                    }
                } else {
                    $select->like($param['field'], urldecode($param['keyword']));
                }
            }
            // 时间搜索
            if (isset($param['start']) && $param['start']) {
                $param['end'] = strtotime(date('Y-m-d 23:59:59', $param['end'] ? $param['end'] : SYS_TIME));
                $param['start'] = strtotime(date('Y-m-d 00:00:00', $param['start']));
                $select->where('inputtime BETWEEN ' . $param['start'] . ' AND ' . $param['end']);
            } elseif (isset($param['end']) && $param['end']) {
                $param['end'] = strtotime(date('Y-m-d 23:59:59', $param['end']));
                $param['start'] = 0;
                $select->where('inputtime BETWEEN ' . $param['start'] . ' AND ' . $param['end']);
            }
        }

        return $param;
    }

    /**
     * 数据分页显示
     *
     * @return	array
     */
    protected function limit_page($table) {

        if (IS_POST) {
            $page = $_GET['page'] = 1;
            $total = 0;
        } else {
            $page = max(1, (int)$this->input->get('page'));
            $total = (int)$this->input->get('total');
        }

        $param = $this->input->get(NULL);
        unset($param['s'],$param['c'],$param['m'],$param['d'],$param['page']);

        if (!$total) {
            $select	= $this->db->select('count(*) as total');
            $param = $this->_where($select, $param);
            $data = $select->get($table)->row_array();
            unset($select);
            $total = (int)$data['total'];
            if (!$total) {
                return array(array(), $total, $param);
            }
        }

        $select	= $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $param = $this->_where($select, $param);
        $_order = isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0 ? $this->input->get('order') : 'inputtime DESC';
        $data = $select->order_by('status asc,'.$_order)->get($table)->result_array();

        return array($data, $total, $param);
    }
}
