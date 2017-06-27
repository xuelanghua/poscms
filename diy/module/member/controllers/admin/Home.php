<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class Home extends M_Controller {

    private $userinfo;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
			fc_lang('会员管理') => array('member/admin/home/index', 'user'),
			fc_lang('添加') => array('member/admin/home/add_js', 'plus')
		)));
    }

    /**
     * 首页
     */
    public function index() {

		if (IS_POST && $this->input->post('action')) {

            // ID格式判断
			$ids = $this->input->post('ids');
            !$ids && exit(dr_json(0, fc_lang('您还没有选择呢')));
			
			if ($this->input->post('action') == 'del') {
                // 删除
                !$this->is_auth('member/admin/home/del') && exit(dr_json(0, fc_lang('您无权限操作')));
                foreach ($ids as $i) {
                    // 角色权限验证
                    $data = $this->member_model->get_admin_member($i);
                    !$this->auth_model->role_level($this->member['adminid'], $data['adminid']) && exit(dr_json(0, fc_lang('您无权操作（ta的权限高于你）')));
                }
				$this->member_model->delete($ids);
                defined('UCSSO_API') && ucsso_delete($ids);
                $this->system_log('删除会员【#'.@implode(',', $ids).'】'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			} else {
                // 修改会员组
                !$this->is_auth('member/admin/home/edit') && exit(dr_json(0, fc_lang('您无权限操作')));
				$gid = (int)$this->input->post('groupid');
				$note = fc_lang('您的会员组由管理员%s改变成：%s', $this->member['username'], $this->get_cache('member', 'group', $gid, 'name'));
				$this->db->where_in('uid', $ids)->update('member', array('groupid' => $gid));
				$this->member_model->add_notice($ids, 1, $note);
                foreach ($ids as $uid) {
                    // 会员组升级挂钩点
                    $this->hooks->call_hook('member_group_upgrade', array('uid' => $uid, 'groupid' => $gid));
                    // 表示审核会员
                    $this->member_model->update_admin_notice('member/admin/home/index/field/uid/keyword/'.$uid, 3);
                }
                $this->system_log('修改会员【#'.@implode(',', $ids).'】的会员组'); // 记录日志
				exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
			}
		}

        // 重置页数和统计
        IS_POST && $_GET['page'] = $_GET['total'] = 0;
	
		// 根据参数筛选结果
        $param = $this->input->get(NULL, TRUE);
        unset($param['s'], $param['c'], $param['m'], $param['d'], $param['page']);
		
		// 数据库中分页查询
		list($data, $param) = $this->member_model->limit_page($param, max((int)$_GET['page'], 1), (int)$_GET['total']);


        $field = $this->get_cache('member', 'field');
        $field = array(
            'username' => array('fieldname' => 'username','name' => fc_lang('会员名称')),
            'name' => array('fieldname' => 'name','name' => fc_lang('姓名')),
            'email' => array('fieldname' => 'email','name' => fc_lang('会员邮箱')),
            'phone' => array('fieldname' => 'phone','name' => fc_lang('手机号码')),
            'ismobile' => array('fieldname' => 'ismobile','name' => fc_lang('是否手机认证')),
            'complete' => array('fieldname' => 'complete','name' => fc_lang('是否完善资料')),
            'is_auth' => array('fieldname' => 'is_auth','name' => fc_lang('是否实名认证')),
        ) + ($field ? $field : array());

        // 存储当前页URL
        $this->_set_back_url('member/home/index', $param);

		$this->template->assign(array(
			'list' => $data,
            'field' => $field,
			'param'	=> $param,
			'pages'	=> $this->get_pagination(dr_url('member/home/index', $param), $param['total']),
		));
		$this->template->display('member_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {


        $MEMBER = $this->get_cache('member');
        if ($MEMBER['setting']['ucenter'] && is_file(WEBPATH.'api/ucenter/config.inc.php')) {
            include WEBPATH.'api/ucenter/config.inc.php';
            include WEBPATH.'api/ucenter/uc_client/client.php';
        }
	
		if (IS_POST) {
		
			$all = $this->input->post('all');
			$info = $this->input->post('info');
			$data = $this->input->post('data');

            !$data['groupid'] && exit(dr_json(0, fc_lang('先选择一个会员组吧'), 'groupid'));
			
			if ($all) {
				// 批量添加
                !$info && exit(dr_json(0, fc_lang('批量注册信息填写不完整'), 'info'));
				$data = explode(PHP_EOL, $info);
				$success = $error = 0;
				foreach ($data as $t) {
					list($username, $password, $email, $phone) = explode('|', $t);
					if ($username || $password || $email || $phone) {
						$uid = $this->member_model->register(array(
                            'phone' => $phone,
                            'email' => $email,
							'username' => $username,
							'password' => trim($password),
						), $data['groupid']);
						if ($uid > 0) {
							$success ++;
                            $this->system_log('添加会员【#'.$uid.'】'.$username); // 记录日志
						} else {
							$error ++;
						}
					}
				}
				exit(dr_json(1, fc_lang('批量注册成功%s，失败%s', $success, $error)));
			} else {
				// 单个添加
                $uid = $this->member_model->register(array(
                    'email' => $data['email'],
                    'phone' => $data['phone'] ? $data['phone'] : '',
                    'username' => $data['username'],
                    'password' => trim($data['password']),
                ), $data['groupid']);
                if ($uid == -1) {
                    exit(dr_json(0, fc_lang('该会员【%s】已经被注册', $data['username']), 'username'));
                } elseif ($uid == -2) {
                    exit(dr_json(0, fc_lang('邮箱格式不正确'), 'email'));
                } elseif ($uid == -3) {
                    exit(dr_json(0, fc_lang('该邮箱【%s】已经被注册', $data['email']), 'email'));
                } elseif ($uid == -4) {
                    exit(dr_json(0, fc_lang('同一IP在限制时间内注册过多'), 'username'));
                } elseif ($uid == -5) {
                    exit(dr_json(0, fc_lang('Ucenter：会员名称不合法'), 'username'));
                } elseif ($uid == -6) {
                    exit(dr_json(0, fc_lang('Ucenter：包含不允许注册的词语'), 'username'));
                } elseif ($uid == -7) {
                    exit(dr_json(0, fc_lang('Ucenter：Email格式有误'), 'username'));
                } elseif ($uid == -8) {
                    exit(dr_json(0, fc_lang('Ucenter：Email不允许注册'), 'username'));
                } elseif ($uid == -9) {
                    exit(dr_json(0, fc_lang('Ucenter：Email已经被注册'), 'username'));
                } elseif ($uid == -10) {
                    exit(dr_json(0, fc_lang('手机号码必须是11位的整数'), 'phone'));
                } elseif ($uid == -11) {
                    exit(dr_json(0, fc_lang('该手机号码已经注册'), 'phone'));
                } else {
                    $this->system_log('添加会员【#'.$uid.'】'.$data['username']); // 记录日志
                    exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
                }
			}
		}
		
		$this->template->display('member_add.html');
    }
	
	/**
     * 修改
     */
    public function edit() {
	
		$uid = (int)$this->input->get('uid');
		$page = (int)$this->input->get('page');
		$data = $this->member_model->get_member($uid);

        !$data && $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        // 角色权限验证
        !$this->auth_model->role_level($this->member['adminid'], $data['adminid']) && $this->admin_msg(fc_lang('您无权操作（ta的权限高于你）'));

		$field = array();
		$MEMBER = $this->get_cache('member');
        if ($MEMBER['setting']['ucenter'] && is_file(WEBPATH.'api/ucenter/config.inc.php')) {
            include WEBPATH.'api/ucenter/config.inc.php';
            include WEBPATH.'api/ucenter/uc_client/client.php';
        }

		if ($MEMBER['field'] && $MEMBER['group'][$data['groupid']]['allowfield']) {
			foreach ($MEMBER['field'] as $t) {
                in_array($t['fieldname'], $MEMBER['group'][$data['groupid']]['allowfield']) && $field[] = $t;
			}
		}

		$is_uc = function_exists('uc_user_edit') && $MEMBER['setting']['ucenter'];
		
		if (IS_POST) {
			$edit = $this->input->post('member');
			$page = (int)$this->input->post('page');
			$post = $this->validate_filter($field, $data);
			if (!$edit['groupid']) {
				$error = fc_lang('先选择一个会员组吧');
			} elseif (isset($post['error'])) {
				$error = $post['msg'];
			} else {
				$post[1]['uid'] = $uid;
				$post[1]['is_auth'] = (int)$data['is_auth'];
				$post[1]['complete'] = (int)$data['complete'];
				$this->db->replace('member_data', $post[1]);
				$this->attachment_handle($uid, $this->db->dbprefix('member').'-'.$uid, $field, $data);
				$update = array(
					'name' => $edit['name'],
					'phone' => $edit['phone'],
					'groupid' => $edit['groupid'],
				);
                // 修改密码
                $edit['password'] = trim($edit['password']);
				if ($edit['password']) {
                    if (defined('UCSSO_API')) {
                        $rt = ucsso_edit_password($uid, $edit['password']);
                        // 修改失败
                        if (!$rt['code']) {
                            $this->admin_msg(fc_lang($rt['msg']));
                        }
                    }
                    $is_uc && uc_user_edit($data['username'], '', $edit['password'], '', 1);
					$update['password'] = md5(md5($edit['password']).$data['salt'].md5($edit['password']));
                    $this->hooks->call_hook('member_edit_password', array('member' => $data, 'password' => $edit['password']));
					$this->member_model->add_notice($uid, 1, fc_lang('您的密码被管理员%s修改了', $this->member['username']));
                    $this->system_log('修改会员【'.$data['username'].'】密码'); // 记录日志
				}
                // 修改邮箱
                if ($edit['email'] != $data['email']) {
                    !preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $edit['email']) && $this->admin_msg(fc_lang('邮箱格式不正确'));
                    $this->db->where('email', $edit['email'])->where('uid<>', $uid)->count_all_results('member') && $this->admin_msg(fc_lang('该邮箱【%s】已经被注册', $edit['email']));
                    if ($is_uc) {
                        $ucid = uc_user_edit($data['username'], '', '', $edit['email'], 1);
                        if ($ucid == -4) {
                            $this->admin_msg(fc_lang('Ucenter：Email格式有误'));
                        } elseif ($ucid == -5) {
                            $this->admin_msg(fc_lang('Ucenter：Email不允许注册'));
                        } elseif ($ucid == -6) {
                            $this->admin_msg(fc_lang('Ucenter：Email已经被注册'));
                        }
                    }
                    if (defined('UCSSO_API')) {
                        $rt = ucsso_edit_email($uid, $edit['email']);
                        // 修改失败
                        if (!$rt['code']) {
                            $this->admin_msg(fc_lang($rt['msg']));
                        }
                    }
                    $update['email'] = $edit['email'];
                    $this->member_model->add_notice($uid, 1, fc_lang('您的注册邮箱被管理员%s修改了', $this->member['username']));
                    $this->system_log('修改会员【'.$data['username'].'】邮箱'); // 记录日志
                }
                // 修改手机
                if  ($edit['phone'] != $data['phone']) {
                    if (defined('UCSSO_API')) {
                        $rt = ucsso_edit_phone($uid, $edit['phone']);
                        // 修改失败
                        if (!$rt['code']) {
                            $this->admin_msg(fc_lang($rt['msg']));
                        }
                    }
                }
				$this->db->where('uid', $uid)->update('member', $update);
                // 会员组升级挂钩点
                if ($data['groupid'] != $edit['groupid']) {
                    // 表示审核会员
                    $data['groupid'] == 1 && $this->member_model->update_admin_notice('member/admin/home/index/field/uid/keyword/'.$uid, 3);
                    $this->hooks->call_hook('member_group_upgrade', array('uid' => $uid, 'groupid' => $edit['groupid']));
                    $this->system_log('修改会员【'.$data['username'].'】会员组'); // 记录日志
                }
                $this->system_log('修改会员【'.$data['username'].'】资料'); // 记录日志
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('member/home/edit', array('uid' => $uid, 'page' => $page)), 1);
			}
			$this->admin_msg($error, dr_url('member/home/edit', array('uid' => $uid, 'page' => $page)));
		}
		
		$this->template->assign(array(
			'data' => $data,
			'page' => $page,
			'myfield' => $this->field_input($field, $data, TRUE),
		));
		$this->template->display('member_edit.html');
    }

    public function ajax_email() {

        $uid = (int)$this->input->get('uid');
        $email = $this->input->get('email');

        if (!$email || !preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $email)) {
            exit(fc_lang('邮箱格式不正确'));
        } elseif ($this->db->where('email', $email)->where('uid<>', $uid)->count_all_results('member')) {
            exit(fc_lang('该邮箱【%s】已经被注册', $email));
        }

        exit(0);
    }

    /**
     * 经验值管理
     */
    public function _experience() {

        $uid = (int)$this->input->get('uid');
        $this->userinfo = $this->member_model->get_member($uid);
        !$this->userinfo && $this->admin_msg(fc_lang('会员不存在'));

        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') =>  array($this->_get_back_url('member/home/index'), 'reply'),
                SITE_EXPERIENCE =>  array('member/admin/home/experience/uid/'.$uid, 'star'),
                fc_lang('添加') =>  array('member/admin/home/add_experience/uid/'.$uid.'_js', 'plus')
            )),
            'userinfo' => $this->userinfo
        ));
        $this->load->model('score_model');
    }

    /**
     * 经验值
     */
    public function experience() {

        $this->_experience();

        $uid = (int)$this->input->get('uid');

        // 根据参数筛选结果
        $param = array('uid' => $uid, 'type' => 0);
        $this->input->get('search') && $param['search'] = 1;

        // 数据库中分页查询
        list($data, $param)	= $this->score_model->limit_page($param, max((int)$this->input->get('page'), 1), (int)$this->input->get('total'));
        $param['uid'] = $uid;

        $_param = $this->input->get('search') ? $this->cache->file->get($this->score_model->cache_file) : $this->input->post('data');
        $_param = $_param ? $param + $_param : $param;

        $this->template->assign(array(
            'list' => $data,
            'name' => SITE_EXPERIENCE,
            'param'	=> $_param,
            'pages'	=> $this->get_pagination(dr_url('member/home/experience', $param), $param['total'])
        ));
        $this->template->display('score_index.html');
    }

    /**
     * 充值经验值
     */
    public function add_experience() {

        $this->_experience();

        if (IS_POST) {
            $data = $this->input->post('data');
            $value = intval($data['value']);
            !$value && exit(dr_json(0, fc_lang('请填写变动数量值'), 'value'));
            $this->member_model->update_score(0, $this->userinfo['uid'], $value, '', $data['note']);
            $this->member_model->add_notice($this->userinfo['uid'], 1, fc_lang('%s变动：%s；本次操作人：%s', SITE_EXPERIENCE, $value, $this->member['username']));
            $this->system_log('会员【'.$this->userinfo['username'].'】充值'.SITE_EXPERIENCE); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $this->template->display('score_add.html');
    }

    /**
     * 虚拟币
     */
    public function _score() {

        $uid = (int)$this->input->get('uid');
        $this->userinfo = $this->member_model->get_member($uid);
        !$this->userinfo && $this->admin_msg(fc_lang('会员不存在'));

        $this->template->assign(array(
            'menu' => $this->get_menu_v3(array(
                fc_lang('返回') => array($this->_get_back_url('member/home/index'), 'reply'),
                SITE_SCORE => array('member/admin/home/score/uid/'.$uid, 'star'),
                fc_lang('添加') => array('member/admin/home/add_score/uid/'.$uid.'_js', 'plus')
            )),
            'userinfo' => $this->userinfo
        ));
        $this->load->model('score_model');
    }

    /**
     * 虚拟币
     */
    public function score() {

        $this->_score();

        $uid = (int)$this->input->get('uid');

        // 根据参数筛选结果
        $param = array('uid' => $uid, 'type' => 1);
        $this->input->get('search') && $param['search'] = 1;

        // 数据库中分页查询
        list($data, $param)	= $this->score_model->limit_page($param, max((int)$this->input->get('page'), 1), (int)$this->input->get('total'));
        $param['uid'] = $uid;

        $_param = $this->input->get('search') ? $this->cache->file->get($this->score_model->cache_file) : $this->input->post('data');
        $_param = $_param ? $param + $_param : $param;

        $this->template->assign(array(
            'list' => $data,
            'name' => SITE_SCORE,
            'param'	=> $_param,
            'pages'	=> $this->get_pagination(dr_url('member/home/score', $param), $param['total'])
        ));
        $this->template->display('score_index.html');
    }

    /**
     * 充值
     */
    public function add_score() {

        $this->_score();

        if (IS_POST) {
            $data = $this->input->post('data');
            $value = intval($data['value']);
            !$value && exit(dr_json(0, fc_lang('请填写变动数量值'), 'value'));
            $this->member_model->update_score(1, $this->userinfo['uid'], $value, '', $data['note']);
            $this->member_model->add_notice($this->userinfo['uid'], 1, fc_lang('%s变动：%s；本次操作人：%s', SITE_SCORE, $value, $this->member['username']));
            $this->system_log('会员【'.$this->userinfo['username'].'】充值'.SITE_SCORE); // 记录日志
            exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
        }

        $this->template->display('score_add.html');
    }

   

}