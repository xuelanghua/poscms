<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.3.7
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class Api extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }


    // 空间统计
    public function space_count() {

        $uid = (int)$this->input->get('uid');
        if ($this->uid && $uid && $this->uid != $uid) {
            // 记录访客信息
            $name = 'space-count-'.$this->uid.'-'.$uid;
            if (get_cookie($name)) {
                // 缓存期
            } else {
                // 查询今天是否访问过
                if ($this->db->where('uid', $this->uid)->where('spaceid', $uid)->where('DATEDIFF(from_unixtime(inputtime),now())=0')->count_all_results('space_access')) {
                    $this->db->where('uid', $this->uid)->where('spaceid', $uid)->update('space_access', array(
                        'inputtime' => SYS_TIME
                    ));
                } else {
                    $this->db->insert('space_access', array(
                        'uid' => $this->uid,
                        'spaceid' => $uid,
                        'content' => '',
                        'username' => $this->member['username'],
                        'inputtime' => SYS_TIME,
                    ));
                }
                set_cookie($name, SYS_TIME, 300); // 5分钟统计一次
            }
        }

        // 验证空间权限
        if ($this->_space_show($uid)) {
            $callback = isset($_GET['callback']) ? $this->input->get('callback', TRUE) : 'callback';
            exit($callback.'('.json_encode(array('url' => dr_url('space/api/access', array('uid' => $uid)))).')');
        }

        exit('');

    }

    // 空间访问受限时的提示
    public function access() {

        $uid = (int)$this->input->get('uid');
        if ($this->_space_show($uid)) {
            $this->member_msg(fc_lang('对方设置不允许查看Ta的空间'));
        } else {
            redirect(dr_space_url($uid), 'refresh');
        }

    }

    // 空间的关注按钮接口
    public function guanzhu() {

        $uid = (int)$this->input->get('uid');
        $style = dr_safe_replace($this->input->get('style'));

        if ($style) {
            // 显示好友关系
            $follow = dr_sns_follow($this->uid, $uid); // 好友关系
            ob_start();
            $this->template->assign(array(
                'uid2' => $uid,
                'follow' => $follow == -1 ? 0 : 1,
            ));
            $this->template->space($style);
            $this->template->display('api_guanzhu.html');
            $html = ob_get_contents();
            ob_clean();
            $html = addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html));
            echo 'document.write("'.$html.'");';
        } else {
            // 执行关注操作
            $callback = isset($_GET['callback']) ? $this->input->get('callback', TRUE) : 'callback';
            if (!$this->uid || !$this->member) {
                exit($callback.'('.json_encode(array('status' => 0, 'msg' => fc_lang('会话超时，请重新登录'))).')');
            }
            $this->load->model('sns_model');
            exit($callback.'('.json_encode(array('status' => 1, 'msg' => $this->sns_model->following($uid))).')');
        }
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
}