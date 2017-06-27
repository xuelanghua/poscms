<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class D_Home_Form extends M_Controller {

    public $fid; // 表单id
    public $cid; // 内容id
    protected $form; // 表单
    protected $table; // 表单表
    protected $cdata; // 内容数据
    protected $field; // 全部字段

    /**
     * 构造函数（模块表单前台）
     */

    public function __construct() {
        parent::__construct();
        // 表单验证
        $this->fid = trim(strchr($this->router->class, '_'), '_');
        $this->form = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'form', $this->fid);
        !$this->form && $this->msg(fc_lang('表单不存在'));
        // 内容验证
        $this->cid = (int)$this->input->get('cid');
        $this->cdata = $this->get_cache_data('show'.APP_DIR.SITE_ID.$this->cid);
        if (!$this->cdata) {
            $this->load->model('content_model');
            $this->cdata = $this->content_model->get($this->cid);
        }
        !$this->cdata && $this->msg(fc_lang('内容(id#%s)不存在', $this->cid));
        $this->table = SITE_ID.'_'.APP_DIR.'_form_'.$this->fid;
        // 投稿权限验证
        $rule = $this->form['permission'][$this->markrule];
        // 禁用权限
        $rule['disabled'] && $this->msg(fc_lang('当前会员组无权限操作'));
        // 每日发布数量检查
        if ($rule['postnum']) {
            $total = $this->db->where('uid', $this->uid)->where('DATEDIFF(from_unixtime(inputtime),now())=0')->count_all_results($this->table);
            $total >= $rule['postnum'] && $this->msg(fc_lang('每日发布数量不得超过%s个', $rule['postnum']));
        }
        // 投稿总数检查
        if ($rule['postcount']) {
            $total = $this->db->where('uid', $this->uid)->count_all_results($this->table);
            $total >= $rule['postcount'] && $this->msg(fc_lang('发布总数不得超过%s个', $rule['postcount']));
        }
        // 虚拟币检查
        $rule['score'] + $this->member['score'] < 0 && $this->msg(fc_lang(SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE, abs($rule['score']), $this->member['score']));
        $this->load->model('mform_model');
    }

    /**
     * 添加
     */
    public function index() {

        $mod = $this->get_cache('module-'.SITE_ID.'-'.APP_DIR);
        $cat = $mod['category'][$this->cdata['catid']];
        $post = $error = null;

        if (IS_POST) {
            // 验证码
            if ($this->form['setting']['code'] && !$this->check_captcha('code')) {
                $post = $this->input->post('data', TRUE);
                $error = array('error' => 'code', 'msg' => fc_lang('验证码不正确'));
            } else {
                // 设置uid便于校验处理
                $_POST['data']['uid'] = $this->uid;
                $data = $this->validate_filter($this->form['field']);
                if (isset($data['error'])) {
                    $error = $data;
                    (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
                    $post = $this->input->post('data', TRUE);
                } else {
                    $data[1]['cid'] = $this->cid;
                    $data[1]['uid'] = $this->uid;
                    $data[1]['url'] = $this->cdata['url'];
                    $data[1]['title'] = $this->cdata['title'];
                    $data[1]['author'] = $this->member['username'] ? $this->member['username'] : 'guest';
                    $data[1]['inputip'] = $this->input->ip_address();
                    $data[1]['inputtime'] = SYS_TIME;
                    if ($id = $this->_add($data) && $this->uid) {
                        $rule = $this->form['permission'][$this->markrule];
                        // 积分处理
                        $rule['experience'] && $this->member_model->update_score(0, $this->uid, $rule['experience'], '', $this->form['name']);
                        // 虚拟币处理
                        $rule['score'] && $this->member_model->update_score(1, $this->uid, $rule['score'], '', $this->form['name']);
                        // 操作成功处理附件
                        $this->attachment_handle($this->uid, $this->db->dbprefix($this->table).'-'.$id, $this->form['field']);
                    }
                    $this->msg(fc_lang('提交成功'), $this->form['setting']['rt_url'] ? str_replace(array('{catid}', '{cid}'), array($this->cdata['catid'], $this->cdata['id']), $this->form['setting']['rt_url']) : $this->cdata['url'], 1);
                }
            }
        }

        // 格式化输出自定义字段
        $fields = $mod['field'];
        $fields = $cat['field'] ? array_merge($fields, $cat['field']) : $fields;
        $fields['inputtime'] = array('fieldtype' => 'Date');
        $fields['updatetime'] = array('fieldtype' => 'Date');
        $data = $this->field_format_value($fields, $this->cdata, 1);

        $tpl = dr_tpl_path('form_'.$this->fid.'.html');
        $this->template->assign($data);
        $this->template->assign(array(
            'tpl' => str_replace(FCPATH, '/', $tpl),
            'code' => $this->form['setting']['code'],
            'form' => $this->form,
            'result' => $error,
            'myfield' => $this->field_input($this->form['field'], $post, FALSE),
            'meta_title' => $this->form['name'].SITE_SEOJOIN.$data['title'].SITE_SEOJOIN.$cat['name'].SITE_SEOJOIN.MODULE_NAME.SITE_SEOJOIN.SITE_NAME
        ));
        $this->template->display(is_file($tpl) ? basename($tpl) : 'form.html');
    }

    // 添加入库
    protected function _add($data) {
        // 入库
        $table = $this->db->dbprefix($this->table);
        $data[1]['tableid'] = 0;
        $this->db->insert($table, $data[1]);
        //
        if (($id = $this->db->insert_id())) {
            // 无限分表
            $tableid = floor($id / 50000);
            $this->db->where('id', $id)->update($table, array('tableid' => $tableid));
            if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$tableid."'")->row_array()) {
                // 附表不存在时创建附表
                $sql = $this->db->query("SHOW CREATE TABLE `".$table."_data_0`")->row_array();
                $this->db->query(str_replace(
                    array($sql['Table'], 'CREATE TABLE '),
                    array($table.'_data_'.$tableid, 'CREATE TABLE IF NOT EXISTS '),
                    $sql['Create Table']
                ));
            }
            $data[0]['id'] = $id;
            $data[0]['cid'] = $this->cid;
            $data[0]['uid'] = $this->uid;
            $this->db->replace($table.'_data_'.$tableid, $data[0]);
            $user = dr_member_info($this->cdata['uid']);
            // 通知功能
            if ($user) {
                $murl = dr_member_url(APP_DIR.'/'.$this->router->class.'/listc', array('cid' => $this->cdata['id']));
                $title = fc_lang('《%s》有新的%s', $this->cdata['title'], $this->form['name']);
                // 邮件提醒
                $this->form['setting']['email'] && $this->sendmail_queue($user['email'], $title, dr_lang('《%s》有新的%s', $this->cdata['title'], $this->form['name'], $murl, $murl));
                // 短信提醒
                $this->form['setting']['sms'] && $user['phone'] && $this->member_model->sendsms($user['phone'], $title);
                // 添加提醒
                $this->member_model->add_notice($this->cdata['uid'], 3, '<a href="'.$murl.'">'.$title.'</a>');
            }
            // 更新模块表的统计值
            $this->db->where('id', $this->cid)->set($this->fid.'_total', $this->fid.'_total + 1', FALSE)->update(SITE_ID.'_'.APP_DIR);
        }
        return $id;
    }

}
