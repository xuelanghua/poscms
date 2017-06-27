<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * v3.2
 */

require FCPATH.'branch/fqb/D_Admin_Table.php';

class Wkeyword extends D_Admin_Table {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('weixin_model');
        $this->mydb = $this->db; // 数据库
        $this->tfield = 'inputtime'; // 时间字段用于搜索和排序
        $this->mytable = $this->weixin_model->prefix.'_keyword';
        $this->myfield = $field = array(
            'keywords' => array(
                'name' => '关键词',
                'ismain' => 1,
                'fieldname' => 'keywords',
                'fieldtype' => 'Text',
                'setting' => array (
                    'option' =>
                        array (
                            'width' => 200,
                        ),
                    'validate' =>
                        array (
                            'xss' => 1,
                            'formattr' => ' data-role="tagsinput"', // tag属性
                        ),
                ),
            ),
        );
        $this->myfield['mtype'] = array(
            'ismain' => 1,
            'fieldname' => 'mtype',
            'fieldtype' => 'Text',
        );
        $this->myfield['content'] = array(
            'ismain' => 1,
            'fieldname' => 'content',
            'fieldtype' => 'Text',
        );
        $this->myfield['mid'] = array(
            'ismain' => 1,
            'fieldname' => 'mid',
            'fieldtype' => 'Text',
        );
        $this->myfield['plug'] = array(
            'ismain' => 1,
            'fieldname' => 'plug',
            'fieldtype' => 'Text',
        );
        $this->myfield['count'] = array(
            'ismain' => 1,
            'fieldname' => 'count',
            'fieldtype' => 'Text',
        );
        $this->myfield['inputtime'] = array(
            'ismain' => 1,
            'fieldname' => 'inputtime',
            'fieldtype' => 'Date',
        );
        $this->template->assign(array(
            'field' => $field,
            'menu' => $this->get_menu_v3(array(
                '关键词管理' => array('admin/wkeyword/index', 'tag'),
                '添加' => array('admin/wkeyword/add', 'plus'),
            )),
            'type' => array(
                '文本' => 'wz',
                '图片' => 'tp',
                '图文' => 'tw',
                '视频' => 'sp',
                '语音' => 'yy',
            ),
        ));
    }

    /**
     * 管理
     */
    public function index() {

        if (IS_POST && $this->input->post('action') == 'del') {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            $this->db->where_in('id', $ids)->delete($this->mytable);
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $this->_index();
        $this->template->display('wkeyword_index.html');
    }


    public function add() {

        if (IS_POST) {
            $post = $_POST['data'];
            $scid = intval($_POST['sc']);
            if ($post['keywords']) {
                $kws = explode(',', $post['keywords']);
                $_POST['data']['keywords'] = '';
                foreach ($kws as $t) {
                    // 查询重复
                    if ($this->db->where('`keywords` LIKE "%,'.$t.',%"')->count_all_results($this->mytable)) {
                        $this->admin_msg('关键字['.$t.']已经存在了');
                    }
                    $_POST['data']['keywords'].= ','.$t;
                }
                $_POST['data']['keywords'].= ',';
            }
            if ($post['mtype'] == 'wz') {
                // 文字
                if ($_POST['content']) {
                    $_POST['data']['content'] = $_POST['content'];
                } else {
                    $this->admin_msg('文字内容不能为空');
                }
            } else {
                // 其他类型的素材
                $_POST['data']['mid'] = $scid;
            }
        }

        $this->_add();
        $this->template->display('wkeyword_add.html');
    }

    public function edit() {

        $id = (int)$this->input->get($this->myid);
        $data = $this->_get_data($id);
        if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }

        if (IS_POST) {
            $post = $_POST['data'];
            $scid = intval($_POST['sc']);
            if ($post['keywords']) {
                $kws = explode(',', $post['keywords']);
                $_POST['data']['keywords'] = '';
                foreach ($kws as $t) {
                    // 查询重复
                    if ($this->db->where('`id`<> '.$id.' and `keywords` LIKE "%,'.$t.',%"')->count_all_results($this->mytable)) {
                        $this->admin_msg('关键字['.$t.']已经存在了');
                    }
                    $_POST['data']['keywords'].= ','.$t;
                }
                $_POST['data']['keywords'].= ',';
            }
            if ($post['mtype'] == 'wz') {
                // 文字
                if ($_POST['content']) {
                    $_POST['data']['content'] = $_POST['content'];
                } else {
                    $this->admin_msg('文字内容不能为空');
                }
            } else {
                // 其他类型的素材
                $_POST['data']['mid'] = $scid;
            }

            $this->_update_data($id, $_POST['data'], $data);
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url(APP_DIR.'/'.$this->router->class.'/index'), 1);
        }

        if ($data['mtype'] == 'wz') {
            $sc = $this->db->where('id', $data['mid'])->get($this->weixin_model->prefix.'_material_text')->row_array();
        } elseif ($data['mtype'] == 'tp') {
            $sc = $this->db->where('id', $data['mid'])->get($this->weixin_model->prefix.'_material_image')->row_array();
        } elseif ($data['mtype'] == 'sp' || $data['mtype'] == 'yy') {
            $sc = $this->db->where('id', $data['mid'])->get($this->weixin_model->prefix.'_material_file')->row_array();
        } elseif ($data['mtype'] == 'tw') {
            $sc = $this->db->where('id', $data['mid'])->get($this->weixin_model->prefix.'_material_news')->row_array();
        }

        $this->template->assign(array(
            'sc' => $sc,
            'data' => $data,
            'myfield' => $this->field_input($this->myfield, $data, TRUE)
        ));
        $this->template->display('wkeyword_add.html');
    }

    // 插入数据
    public function _insert_data($data) {
        $data['mid'] = intval($data['mid']);
        $data['count'] = 0;
        $data['inputtime'] = SYS_TIME;
        $this->mydb->insert($this->mytable, $data);
        return $this->mydb->insert_id();
    }

}