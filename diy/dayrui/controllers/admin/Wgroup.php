<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * v3.2
 */

require FCPATH.'branch/fqb/D_Admin_Table.php';

class Wgroup extends D_Admin_Table {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('weixin_model');
        $this->mydb = $this->db; // 数据库
        $this->mytable = $this->weixin_model->prefix.'_group';
        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                '粉丝分组' => array('admin/wgroup/index', 'users'),
                '添加分组' => array('admin/wgroup/add_js', 'plus'),
                '与微信公众平台同步' => array('admin/wgroup/syc', 'refresh'),
            )),
        ));
    }

    /**
     * 微信分组管理
     */
    public function index() {

        if (IS_POST) {
            $ids = $this->input->post('ids', TRUE);
            if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            } elseif (!$this->is_auth('admin/wgroup/del')) {
                exit(dr_json(0, fc_lang('您无权限操作')));
            }
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $_GET['order'] = 'id asc';
        $this->_index();
        $this->template->display('wgroup_index.html');
    }

    public function add() {

        if (IS_POST) {
            $name = $this->input->post('name');
            $this->db->insert($this->weixin_model->prefix.'_group', array(
                'name' => $name ? $name : '未命名',
                'count' => 0,
                'wechat_id' => -1,
            ));
            exit(dr_json(1, fc_lang('操作成功')));
        }

        $this->template->assign('data', array());
        $this->template->display('wgroup_add.html');
    }

    public function edit() {

        $id = (int)$_GET['id'];
        $data = $this->db->where('id', $id)->get($this->mytable)->row_array();

        if (IS_POST) {
            $name = $this->input->post('name');
            $this->db->where('id', $id)->update($this->weixin_model->prefix.'_group', array(
                'name' => $name ? $name : '未命名',
            ));
            exit(dr_json(1, fc_lang('操作成功')));
        }

        $this->template->assign('data', $data);
        $this->template->display('wgroup_add.html');
    }

    public function del() {

        $id = (int)$_GET['id'];
        $data = $this->db->where('id', $id)->get($this->mytable)->row_array();
        if ($data) {
            if ($data['wechat_id']) {
                $url = "https://api.weixin.qq.com/cgi-bin/groups/delete?access_token=".dr_get_access_token();
                $data = array(
                    'group' => array('id' => $data['wechat_id'])
                );
                $data = json_encode($data);
                $rs = dr_post_data ($url, $data);
                if (isset($rs['errcode']) && $rs['errcode']!= 0) {
                    $this->admin_msg ( dr_error_msg ( $rs ) );
                }
            }
            $this->db->where('id', $id)->delete($this->mytable);
            $this->system_log('删除微信粉丝分组【#'.$id.'】'); // 记录日志
        }
        $this->admin_msg('操作成功', dr_url('wgroup/index'), 1);
    }


    public function syc() {

        $url = 'https://api.weixin.qq.com/cgi-bin/groups/get?access_token='.dr_get_access_token();
        $data = @json_decode(@wx_get_https_json_data ($url), true);
        if (!isset($data['errcode'])){

            // 查询本地数据
            $tmp = $this->db->get($this->weixin_model->prefix.'_group')->result_array();
            $groups = array();
            if ($tmp) {
                foreach ($tmp as $t) {
                    $groups[$t['wechat_id']] = $t;
                }
            }

            foreach ($data['groups'] as $d) {
                if (isset($groups[$d['id']])) {
                    $save = array();
                    // 更新本地数据
                    $old = $groups [$d ['id']];
                    if ($old['name'] != $d['name']) {
                        // 当本地数据和服务器数据不一致时 修改微信端的数据
                        $updateUrl = "https://api.weixin.qq.com/cgi-bin/groups/update?access_token=".dr_get_access_token();
                        $newGroup['group']['id'] = $d['id'];
                        $newGroup['group']['name'] = $old['name'];
                        $res = dr_post_data($updateUrl, $newGroup);
                    }
                    if ($old['name'] != $d['name'] || $old ['count'] != $d['count']) {
                        // 更新本地
                        $this->db->where('id', $old['id'])->update($this->weixin_model->prefix.'_group', array(
                            'name' => $d['name'],
                            'count' => $d['count'],
                        ));
                    }
                    unset ( $groups[$d['id']]);
                } else {
                    // 增加本地数据
                    $this->db->insert($this->weixin_model->prefix.'_group', array(
                        'name' => $d['name'],
                        'count' => $d['count'],
                        'wechat_id' => $d['id'],
                    ));
                }
            }

            if ($groups) {
                // 剩余的本地库数据就新增到服务器中
                foreach ( $groups as $v ) {
                    // 增加微信端的数据
                    $url = 'https://api.weixin.qq.com/cgi-bin/groups/create?access_token=' . dr_get_access_token ();
                    $param ['group'] ['name'] = $v ['name'];
                    $param = JSON ( $param );
                    $res = dr_post_data ( $url, $param );
                    if (! empty ( $res ['group'] ['id'] )) {
                        $this->db->where('id', $v['id'])->update($this->weixin_model->prefix.'_group', array(
                            'wechat_id' => $res ['group'] ['id'],
                            'count' => (int)$res ['group'] ['count'],
                        ));
                    }
                }
            }
            $this->admin_msg('同步成功...', dr_url('wgroup/index'), 1);
        } else {
            $this->admin_msg('同步失败...');
        }

    }
}