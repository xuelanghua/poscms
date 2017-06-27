<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.7.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class Space extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        if (!$this->member['allowspace']) {
            $this->member_msg(fc_lang('该会员组不允许使用空间'));
        }
        $this->load->model('space_model');
        $this->space = $this->space_model->get($this->uid);
    }

    /**
     * 空间资料
     */
    public function index() {

        $error = NULL;
        $field = array();
        $MEMBER = $this->get_cache('member');
        $field[] = $MEMBER['spacefield']['name'];
        if ($MEMBER['spacefield']
            && $MEMBER['group'][$this->member['groupid']]['spacefield']) {
            foreach ($MEMBER['spacefield'] as $t) {
                if (in_array($t['fieldname'], $MEMBER['group'][$this->member['groupid']]['spacefield'])) {
                    $field[] = $t;
                }
            }
        }

        define('IS_SPACE_THEME', $this->space['style'] ? $this->space['style'] : 'default');

        if (IS_POST) {
            $post = $this->validate_filter($field, $this->space);
            if (isset($post['error'])) {
                $data = $this->input->post('data', TRUE);
                $error = $post['msg'];
            } else {
                $error = $this->space_model->update($this->uid, $this->member['groupid'], $post[1]);
                if ($error) {
                    $this->attachment_handle($this->uid, $this->db->dbprefix('space').'-'.$this->uid, $field, $this->space);
                }
                if ($error == 0) {
                    // 名称重复
                    $error = fc_lang('空间名称已经存在');
                } elseif ($error == 1) {
                    // 操作成功
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('space/space/index'), 1);
                } else {
                    // 操作成功,等待审核
                    $this->member_msg(fc_lang('操作成功，等待管理员的审核'), dr_member_url('space/space/index'), 2);
                }
            }
            if (IS_AJAX || IS_API_AUTH) {
                exit(dr_json(0, $error));
            }
        } else {
            $data = $this->space;
        }

        $this->template->assign(array(
            'data' => $data,
            'field' => $field,
            'myfield' => $this->field_input($field, $data, FALSE, 'uid'),
            'newspace' => $this->space ? 0 : 1,
            'result_error' => $error,
        ));
        $this->template->display('space_index.html');
    }


    /**
     * 空间模板
     */
    public function template() {

        $style = $this->input->get('style');
        if ($style && $this->space['style'] != $style) {
            $rule = dr_string2array(@file_get_contents(WEBPATH.'statics/space/'.$style.'/rule.php'));
            if ($style == 'default'
                || (isset($rule[$this->markrule]['use']) && $rule[$this->markrule]['use'])) {
                // 判断是否购买过
                $price = isset($rule[$this->markrule]['price']) ? intval($rule[$this->markrule]['price']) : 0;
                if ($price
                    && $style != 'default'
                    && !$this->db
                             ->where('type', 1)
                             ->where('uid', $this->uid)
                             ->where('mark', 'space-tpl-'.$style)
                             ->count_all_results('member_scorelog')) {
                    // 未购买时，进行购买操作
                    if ($this->member['score'] - $price < 0) {
                        $this->member_msg(fc_lang('抱歉，'.SITE_SCORE.'不足！本次操作需要<font color=red><b>%s</b></font>'.SITE_SCORE.'，当前余额<font color=blue><b>%s</b></font>'.SITE_SCORE.'', $price, $this->member['score']));
                    }
                    $this->member_model->update_score(1, $this->uid, -$price, 'space-tpl-'.$style, fc_lang('购买空间消费'));
                }
                $this->db->where('uid', (int)$this->uid)->update('space', array('style' => $style));
                $this->member_msg(fc_lang('模板选择成功'), dr_member_url('space/space/template'), 1);
            } else {
                $this->member_msg(fc_lang('无权限选择此模板'));
            }
        }

        $my = $list = array();
        $data = dr_dir_map(WEBPATH.'statics/space/', 1);
        if ($data) {
            foreach ($data as $dir) {
                $tpl = array(
                    'name' => $dir,
                    'price' => 0,
                    'preview' => THEME_PATH.'space/'.$dir.'/preview.jpg'
                );
                $rule = dr_string2array(@file_get_contents(WEBPATH.'statics/space/'.$dir.'/rule.php'));
                if ($dir == 'default') {
                    $list[$dir] = $tpl;
                } elseif ($rule
                    && isset($rule[$this->markrule]['use'])
                    && $rule[$this->markrule]['use']) {
                    $tpl['price'] = intval($rule[$this->markrule]['price']);
                    $list[$dir] = $tpl;
                }
            }
            $mytpl = $this->db
                          ->where('type', 1)
                          ->where('uid', $this->uid)
                          ->like('mark', 'space-tpl-')
                          ->get('member_scorelog')
                          ->result_array();
            if ($mytpl) {
                foreach ($mytpl as $t) {
                    list($a, $b, $dir) = explode('-', $t['mark']);
                    if (isset($list[$dir]) && $list[$dir]['price']) {
                        $my[$dir] = $list[$dir];
                    }
                }
            }
        }

        $this->template->assign(array(
            'my' => $my,
            'list' => $list,
            'style' => $this->space['style'] ? $this->space['style'] : 'default',
        ));
        $this->template->display('space_template.html');
    }

    // 绑定域名
    public function domain() {

        if (!$this->member['spacedomain']) {
            $this->member_msg(fc_lang('当前会员组无权限使用空间域名'));
        }

        $domain = $this->get_cache('member', 'setting', 'space', 'spacedomain');
        if (!$domain) {
            $this->member_msg(fc_lang('系统尚未设置空间主域名：<br>后台-会员-空间-空间配置-设置主域名'));
        }

        if (IS_POST) {
            $value = $this->input->post('domain');
            if (!$value) {
                $this->db->where('uid', $this->uid)->delete('space_domain');
                $this->set_cache_data('member-space-domain-'.$this->uid, '', -1);
                $this->member_msg(fc_lang('您已经取消了域名'), dr_member_url('space/space/domain'), 1);
            }
            $not_in = $this->get_cache('member', 'setting', 'space', 'notindomain');
            if ($not_in && @in_array($value, @explode(PHP_EOL, $not_in))) {
                $error = fc_lang('此域名是系统保留域名，不允许设置');
            } elseif (!preg_match('/^[a-z0-9_\-]+$/iU', $value)) {
                $error = fc_lang('二级域名只能由“英文、数字、_、-”组成');
            } elseif (is_dir(FCPATH.$value.'/')) {
                $error = fc_lang('此域名是系统保留域名，不允许设置');
            } elseif ($this->db->where('uid<>'.$this->uid)->where('domain', $value)->count_all_results('space_domain')) {
                $error = fc_lang('此二级域名已经被注册');
            } else {
                $this->db->replace('space_domain', array(
                    'uid' => $this->uid,
                    'domain' => $value,
                ));
                $this->set_cache_data('member-space-domain-'.$this->uid, '', -1);
                $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('space/space/domain'), 1);
            }
            if (IS_AJAX || IS_API_AUTH) {
                exit(dr_json(0, $error));
            }
            $my_domain = $value;
        } else {
            $error = 0;
            $my_domain = $this->space_model->get_domain($this->uid);
        }

        $this->template->assign(array(
            'domain' => $domain,
            'my_domain' => $my_domain,
            'result_error' => $error,
        ));
        $this->template->display('space_domain.html');

    }
}