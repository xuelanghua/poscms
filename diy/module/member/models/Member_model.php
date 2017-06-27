<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* v3.1.0  */
class Member_model extends CI_Model {

    /**
     * 初始化
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 会员修改信息
     *
     * @param	array	$main	主表字段
     * @param	array	$data	附加表字段
     * @return	void
     */
    public function edit($main, $data) {

        if (isset($main['check']) && $main['check']) {
            $main['ismobile'] = 1;
            $main['randcode'] = '';
            unset($main['check']);
        }

        if (isset($main['check'])) {
            unset($main['check']);
        }

        $this->db->where('uid', $this->uid)->update('member', $main);

        $data['uid'] = $this->uid;
        $data['complete'] = 1;

        // 屏蔽数据库错误
        $this->db->db_debug = false;
        $data['is_auth'] = dr_is_app('auth') && $this->db->where('uid', $this->uid)->where('status', 3)->count_all_results('member_auth') ? 1 : 0;
        $this->db->db_debug = true;

        $this->db->replace('member_data', $data);

        return TRUE;
    }

    /**
     * 会员基本信息
     *
     * @param	intval|string	$key
     * @param	intval			$type 0按id，1按会员名
     * @return	array
     */
    public function get_base_member($key, $type = 0) {

        if (!$key) {
            return NULL;
        }

        $type ? $this->db->where('username', $key) : $this->db->where('uid', (int)$key);

        $data = $this->db
                     ->limit(1)
                     ->select('uid,username,email,levelid,groupid,score,experience')
                     ->get('member')
                     ->row_array();
        if (!$data) {
            return NULL;
        }

        $data['markrule'] = $data['groupid'] < 3 ? $data['groupid'] : ($data['groupid'].'_'.$data['levelid']);

        return $data;
    }

    /**
     * 会员权限标识
     *
     * @param	intval	uid
     * @return	string
     */
    public function get_markrule($uid) {

        if (!$uid) {
            return 0;
        }

        $data = $this->db->select('groupid,levelid')->where('uid', (int)$uid)->limit(1)->get('member')->row_array();
        if (!$data) {
            return 0;
        }

        return $data['groupid'] < 3 ? $data['groupid'] : ($data['groupid'].'_'.$data['levelid']);
    }

    /**
     * 会员SNS信息
     *
     * @param	intval	uid
     * @return	array
     */
    public function get_sns($uid) {

        if (!$uid) {
            return NULL;
        }

        $wb = $this->db->where('uid', $uid)->count_all_results('sns_feed');

        return array(
            'feed' => $wb,
            'fans' => $this->db->where('uid', $uid)->count_all_results('sns_follow'),
            'weibo' => $wb,
            'follow' => $this->db->where('fid', $uid)->count_all_results('sns_follow'),
        );
    }

    /**
     * 会员信息
     *
     * @param	intval	uid
     * @return	array
     */
    public function get_member($uid) {

        $uid = intval($uid);
        if (!$uid) {
            return NULL;
        }

        // 查询会员信息
        $data = $this->db
                     ->from($this->db->dbprefix('member').' AS m2')
                     ->join($this->db->dbprefix('member_data').' AS a', 'a.uid=m2.uid', 'left')
                     ->where('m2.uid', $uid)
                     ->limit(1)
                     ->get()
                     ->row_array();
        if (!$data) {
            return NULL;
        }

        $group = $this->ci->get_cache('member', 'group');
        $data['uid'] = $uid;
        $data['tableid'] = (int) substr((string) $uid, -1, 1);
        $data['groupname'] = $group[$data['groupid']]['name'];
        $data['levelname'] = $group[$data['groupid']]['level'][$data['levelid']]['name'];
        $data['avatar_url'] = '';
        if (defined('UCSSO_API')) {
            $data['avatar_url'] =  ucsso_get_avatar($uid);
        } elseif (defined('UC_API')) {
            list($ucenter) = uc_get_user($data['username']);
            $data['avatar_url'] = UC_API.'/avatar.php?uid='.$ucenter.'&size=small';
        } else {
            foreach (array('png', 'jpg', 'gif', 'jpeg') as $ext) {
                if (is_file(SYS_UPLOAD_PATH.'/member/'.$uid.'/45x45.'.$ext)) {
                    $data['avatar_url'] = SYS_ATTACHMENT_URL.'member/'.$uid.'/45x45.'.$ext;
                    break;
                }
            }
            $data['avatar_url'] = $data['avatar_url'] ? $data['avatar_url'] : THEME_PATH.'admin/images/avatar_45.png';
        }

        $data['levelstars'] = $group[$data['groupid']]['level'][$data['levelid']]['stars'];
        $data['allowspace'] = $group[$data['groupid']]['allowspace'];
        $data['spacedomain'] = $group[$data['groupid']]['spacedomain'];

        // 快捷登陆用户信息提取
        $data['bang'] = 0;
        $oauth = require WEBPATH.'config/oauth.php';
        if ($oauth) {
            $bang = 0;
            // 判断是否有可用的快捷登陆配置
            foreach ($oauth as $n => $t) {
                if ($t['use']) {
                    $bang = 1;
                    break;
                }
            }
            // 当存在快捷登陆时才查询绑定表，减少一次查询次数
            if ($bang) {
                $oauth2 = $this->db->where('uid', $uid)->order_by('expire_at desc')->get('member_oauth')->result_array();
                if ($oauth2) {
                    foreach ($oauth2 as $i => $t) {
                        $t['nickname'] = dr_weixin_emoji($t['nickname']);
                        if (!$data['username']) {
                            $data['bang'] = 1;
                            $data['username'] = $t['nickname'];
                        }
                        $data['oauth'][$t['oauth']] = $t;
                    }
                }
            }
        }

        $data['weixin'] = array();
        if ($this->db->table_exists($this->db->dbprefix(SITE_ID.'_weixin_user'))) {
            $data['weixin'] = $this->db->where('uid', $uid)->get(SITE_ID.'_weixin_user')->row_array();
        }


        // 会员组过期判断
        if (!$data['groupid']
            || ($data['overdue'] && $group[$data['groupid']]['price'] && $data['overdue'] < SYS_TIME)) {
            if ($group[$data['groupid']]['unit'] == 1
                && $data['score'] - abs(intval($group[$data['groupid']]['price'])) > 0) {
                // 虚拟币自动扣费
                $this->update_score(1, $uid, -abs(intval($group[$data['groupid']]['price'])), '', fc_lang('会员组到期自动扣费'));
                $time = $this->upgrade($uid, $data['groupid'], $group[$data['groupid']]['limit'], $data['overdue']);
                $time = $time > 2000000000 ? fc_lang('永久') : dr_date($time);
                // 邮件提醒
                $this->ci->sendmail_queue(
                    $this->member['email'],
                    fc_lang('会员组续费成功'),
                    fc_lang(@file_get_contents(WEBPATH.'cache/email/xufei.html'), $data['name'] ? $data['name'] : $data['username'], $group[$data['groupid']]['name'], $time)
                );
                $this->add_notice($uid, 1, fc_lang('会员组续费成功'));
            } else {
                // 转为过期的后的会员组
                $data['groupid'] = intval($data['group']['overdue'] ? $data['group']['overdue'] : 3);
                $this->db->where('uid', $uid)->update('member', array(
                    'levelid' => 0,
                    'overdue' => 0,
                    'groupid' => $data['groupid'],
                ));
                $data['groupname'] = $group[$data['groupid']]['name'];
                $data['allowspace'] = $group[$data['groupid']]['allowspace'];
                $this->add_notice($uid, 1, fc_lang('很遗憾，您的会员组已经过期，被自动初始化'));
            }
        }

        // 会员组等级升级
        if ($group[$data['groupid']]['level']) {
            $level = array_reverse($group[$data['groupid']]['level']); // 倒序判断
            foreach ($level as $t) {
                if ($data['experience'] >= $t['experience']) {
                    if ($data['levelid'] != $t['id']) {
                        $data['levelid'] = $t['id'];
                        $data['levelname'] = $group[$data['groupid']]['level'][$data['levelid']]['name'];
                        $data['levelstars'] = $group[$data['groupid']]['level'][$data['levelid']]['stars'];
                        $this->db->where('uid', $uid)->update('member', array('levelid' => $t['id']));
                        /* 挂钩点：会员组等级升级 */
                        $this->add_notice($uid, 1, fc_lang('您的会员组等级升级成功'));
                    }
                    break;
                }
            }
        }

        $data['mark'] = $data['groupid'] < 3 ? $data['groupid'] : ($data['groupid'].'_'.$data['levelid']);

        return $data;
    }

    /**
     * 通过会员id取会员名称
     *
     * @param	intval	$uid
     * @return  string
     */
    function get_username($uid) {

        if (!$uid) {
            return NULL;
        }

        $data = $this->db->select('username')->where('uid', (int)$uid)->limit(1)->get('member')->row_array();

        return $data['username'];
    }

    /**
     * 会员组续费/升级
     *
     * @param	intval	$uid		会员uid
     * @param	intval	$groupid	组id
     * @param	intval	$limit		limit值
     * @param	intval	$time		当前过期时间，为0时表示新开
     * @return	intval
     */
    public function upgrade($uid, $groupid, $limit, $time = 0) {

        if (!$uid || !$groupid || !$limit) {
            return FALSE;
        }

        $time = max($time, SYS_TIME);

        // 得到增加的时间戳
        switch ($limit) {

            case 1: // 月
                $time = strtotime('+1 month', $time);
                break;

            case 2: // 半年
                $time = strtotime('+6 month', $time);
                break;

            case 3: // 年
                $time = strtotime('+1 year', $time);
                break;

            case 4: // 永久
                $time = 4294967295;
                break;
        }

        // 更新至数据库
        $this->db->where('uid', $uid)->update('member', array(
            'groupid' => $groupid,
            'overdue' => $time,
        ));

        // 发送通知
        $this->add_notice($uid, 1, fc_lang('恭喜亲，您的会员组续费成功'));

        // 会员组升级挂钩点
        $this->hooks->call_hook('member_group_upgrade', array('uid' => $uid, 'groupid' => $groupid));

        return $time;
    }

    /**
     * 后台管理员验证登录
     *
     * @param	string	$username	会员名称
     * @param	string	$password	明文密码
     * @return	int
     * int	id	登录成功
     * int	-1	用户不存在
     * int	-2	密码不正确
     * int	-3	您无权限登录管理平台
     * int	-4	您无权限登录该站点
     */
    public function admin_login($username, $password) {

        $password = trim($password);
        // 查询用户信息
        $data = $this->db
                     ->select('`password`, `salt`, `adminid`,`uid`')
                     ->where('username', $username)
                     ->limit(1)
                     ->get('member')
                     ->row_array();
        // 判断用户状态
        if (!$data) {
            return -1;
        } elseif (md5(md5($password).$data['salt'].md5($password)) != $data['password']) {
            return -2;
        } elseif ($data['adminid'] == 0) {
            return -3;
        } elseif (!$this->is_admin_auth($data['adminid'])) {
            return -4; // 站点权限判断
        }

        // 管理员登录日志记录
        $this->_login_log($data['uid'], '', 1);

        // 保存会话
        $this->session->set_userdata('uid', $data['uid']);
        $this->session->set_userdata('admin', $data['uid']);
        $this->input->set_cookie('member_uid', $data['uid'], 86400);
        $this->input->set_cookie('member_cookie', substr(md5(SYS_KEY . $data['password']), 5, 20), 86400);

        return $data['uid'];
    }

    /**
     * 管理员用户信息
     *
     * @param	int	$uid	用户id
     * @param	int	$verify	是否验证该管理员权限
     * @return	array|int
     * int	-3	您无权限登录管理平台
     * int	-4	您无权限登录该站点
     * array	管理员用户信息数组
     */
    public function get_admin_member($uid, $verify = 0) {

        // 查询用户信息
        $data = $this->db
                     ->select('m.uid,m.email,m.username,m.adminid,m.groupid,a.realname,a.usermenu,a.color')
                     ->from($this->db->dbprefix('member').' AS m')
                     ->join($this->db->dbprefix('admin').' AS a', 'a.uid=m.uid', 'left')
                     ->where('m.uid', $uid)
                     ->limit(1)
                     ->get()
                     ->row_array();
        if (!$data) {
            return 0;
        } elseif ($verify) {
            // 判断用户状态
            if ($data['adminid'] == 0) {
                return -3;
            } elseif (!$this->is_admin_auth($data['adminid'])) {
                return -4;
            }
        }

        $role = $this->dcache->get('role');
        $data['role'] = $role[$data['adminid']];
        $data['usermenu'] = dr_string2array($data['usermenu']);
        $data['color'] = dr_string2array($data['color']);

        return $data;
    }

    /**
     * 管理员权限验证
     *
     * @param	int	$adminid	管理员id
     * @return	bool
     */
    public function is_admin_auth($adminid) {

        $role = $this->dcache->get('role');
        $role = $role ? $role : $this->auth_model->role_cache();

        if ($adminid == 1) {
            return TRUE;
        }

        return @in_array(SITE_ID, $role[$adminid]['site']) ? TRUE : FALSE;
    }

    /**
     * 管理人员
     *
     * @param	int		$roleid		角色组id
     * @param	string	$keyword	匹配关键词
     * @return	array
     */
    public function get_admin_all($roleid = 0, $keyword = NULL) {

        $select = $this->db
                       ->from($this->db->dbprefix('admin').' AS a')
                       ->join($this->db->dbprefix('member').' AS b', 'a.uid=b.uid', 'left');
        $select->join($this->db->dbprefix('admin_role').' AS c', 'b.adminid=c.id', 'left');

        $roleid && $select->where('b.adminid', $roleid);
        $keyword && $select->like('b.username', $keyword);

        return $select->get()->result_array();
    }

    /**
     * 添加管理人员
     *
     * @param	array	$insert	入库管理表内容
     * @param	array	$update	更新会员表内容
     * @param	int		$uid	uid
     * @return	void
     */
    public function insert_admin($insert, $update, $uid) {
        $this->db->where('uid', $uid)->update('member', $update);
        $this->db->replace('admin', $insert);
    }

    /**
     * 修改管理人员
     *
     * @param	array	$insert	入库管理表内容
     * @param	array	$update	更新会员表内容
     * @param	int		$uid	uid
     * @return	void
     */
    public function update_admin($insert, $update, $uid) {
        $this->db->where('uid', $uid)->update('member', $update);
        $this->db->where('uid', $uid)->update('admin', $insert);
    }

    /**
     * 移除管理人员
     *
     * @param	int		$uid	uid
     * @return	void
     */
    public function del_admin($uid) {

        if ($uid == 1) {
            return NULL;
        }

        $this->db->where('uid', $uid)->delete('admin');
        $this->db->where('uid', $uid)->delete('admin_login');
        $this->db->where('uid', $uid)->update('member', array('adminid' => 0));
    }

    /**
     * 通过OAuth登录
     *
     * @param	string	$appid	OAuth服务商名称
     * @param	array	$data	授权返回数据
     * @return	sting
     */
    public function OAuth_login($appid, $data) {

        // 判断OAuth是否已经注册到oauth表
        $oauth = $this->db
                      ->select('id,uid')
                      ->where('oid', $data['oid'])
                      ->where('oauth', $appid)
                      ->limit(1)
                      ->get('member_oauth')
                      ->row_array();
        if ($oauth) {
            // 已经注册就直接保存登录会话，更新表中的记录
            $uid = $oauth['uid'];
            $this->db->where('id', $oauth['id'])->update('member_oauth', $data);
            // 快捷登陆时挂钩点
            $this->hooks->call_hook('member_oauth_login', array('uid' => $uid, 'oauth' => $appid));
        } else {
            // 没有注册时，就直接注册会员账号
			 if ($this->ci->get_cache('member', 'setting', 'regoauth')) {
                // 直接注册
            	 $uid = $data['uid'] = $this->_register($data, $appid);
            } else {
                // 绑定账号
				  return 'bang';
            }
        }

        // 查询会员表
        $member = $this->db
                       ->where('uid', $uid)
                       ->select('uid,username,salt')
                       ->limit(1)
                       ->get('member')
                       ->row_array();
        $MEMBER = $this->ci->get_cache('member');
        $synlogin = '';

        // 同步登录
        if (defined('UCSSO_API')) {
            $synlogin.= ucsso_synlogin($member['uid']);
        } elseif ($MEMBER['setting']['ucenter']
            && $member['username']
            && $ucdata = uc_get_user($member['username'])) {
            list($uid) = $ucdata;
            $synlogin.= uc_user_synlogin($uid);
        }

        foreach ($MEMBER['synurl'] as $url) {
            $code = dr_authcode($member['uid'].'-'.$member['salt'], 'ENCODE');
            $synlogin.= '<script type="text/javascript" src="'.$url.'/index.php?s=member&c=api&m=synlogin&expire=36000&code='.$code.'"></script>';
        }

        $this->_login_log($member['uid'], $appid);

        return $synlogin;
    }

    /**
     * OAuth绑定当前账户
     *
     * @param	string	$appid	OAuth服务商名称
     * @param	array	$data	授权返回数据
     * @return	sting
     */
    public function OAuth_bang($appid, $data) {

        // 判断OAuth是否已经注册到oauth表
        $oauth = $this->db
                      ->select('id,uid')
                      ->where('oid', $data['oid'])
                      ->where('oauth', $appid)
                      ->limit(1)
                      ->get('member_oauth')
                      ->row_array();
        // 已经存在就直接更新表中的记录
        if ($oauth) {
            // 其他账户绑定了时返回其他账户uid
            if ($oauth['uid'] !== $this->uid) {
                return $oauth['uid'];
            }
            $this->db->where('id', $oauth['id'])->update('member_oauth', $data);
        } else {
            // 不存在时就保存OAuth数据
            $data['uid'] = $this->uid;
            $this->db->insert('member_oauth', $data);
        }

        return NULL;
    }

    /**
     * 前端会员验证登录
     *
     * @param	string	$username	用户名
     * @param	string	$password	明文密码
     * @param	intval	$expire	    会话生命周期
     * @param	intval	$back	    是否返回字段
     * @return	string|intval|array
     * string	登录js同步代码
     * int	-1	会员不存在
     * int	-2	密码不正确
     * int  -3	Ucenter注册失败
     * int  -4	Ucenter：会员名称不合法
     */
    public function login($username, $password, $expire, $back = 0, $is_uid = 0) {

        // 查询会员信息
        if ($is_uid) {
            $data = $this->db->where('uid', (int)$username)->get('member')->row_array();
            $username = $data['username'];
        } else {
            $data = $this->db->where('username', $username)->get('member')->row_array();
        }

        $MEMBER = $this->ci->get_cache('member');
        $synlogin = '';

        // 同步登录
        if (defined('UCSSO_API')) {
            /*
                    1:表示用户登录成功
                   -1:用户名不合法
                   -2:密码不合法
                   -3:用户名不存在
                   -4:密码不正确
               */
            $rt = ucsso_login($username, $password);
            if ($rt['code'] < 0) {
                if ($rt['code'] == -3) {
                    // 当ucsso用户不存在时，在验证本地库
                    !$data && $data = dr_vip_login($this->db, $username);
                    if ($data) {
                        //如果本地库有，我们就同步到服务器去
                        $rt = ucsso_register($username, $password, $data['email'], $data['phone']);
                        if (!$rt) {
                            return -404; # 网络异常
                        }
                        //var_dump($rt);exit;
                        if ($rt['code']) {
                            // 注册成功了
                            // 上报uid
                            $rt2 = ucsso_syncuid($rt['code'], $data['uid']);
                            if (!$rt2['code']) {
                                return -5; #同步uid失败
                            }
                            $synlogin.= ucsso_synlogin($data['uid']);
                        } else {
                            return 0;
                        }
                    }
                } elseif ($rt['code'] == -1) {
                    return -1;
                } elseif ($rt['code'] == -2) {
                    return -2;
                } elseif ($rt['code'] == -3) {
                    return -1;
                } elseif ($rt['code'] == -4) {
                    return -2;
                } elseif ($rt['code'] == -404) {
                    return -404;
                }
            } elseif (!$rt['data']['uid']) {
                // 表示ucsso存在这个账号，但没有注册uid
                // 进行高级验证
                !$data && $data = dr_vip_login($this->db, $username);
                $ucsso_id = $rt['data']['ucsso_id'];
                if (!$data) {
                    // 本地有会员不存在时就重新注册
                    $data['uid'] = $this->_register(array(
                        'username' => $username,
                        'password' => $password,
                        'email' => $rt['data']['email'],
                        'phone' => $rt['data']['phone'],
                    ));
                    if (!$data['uid']) {
                        return -3;
                    }
                }
                // 上报uid
                $rt = ucsso_syncuid($ucsso_id, $data['uid']);
                if (!$rt['code']) {
                    return -55;
                }
            }
            $synlogin.= ucsso_synlogin($data['uid']);
        } elseif ($MEMBER['setting']['ucenter'] && function_exists('uc_user_login')) {
            $ousername = $username;
            // Ucenter 验证
            list($uid, $username, $password, $email) = uc_user_login($username, $password);
            if ($uid <= 0) {
                // 进行高级验证
                $data = dr_vip_login($this->db, $ousername);
                $data && list($uid, $username, $password, $email) = uc_user_login($data['username'], $password);
            }
            if ($uid > 0) {
                // 当前会员不存在时就重新注册
                if (!$data) {
                    $data['uid'] = $this->_register(array('username' => $username, 'password' => $password, 'email' => $email));
                    if (!$data['uid']) {
                        return -3;
                    }
                }
                $synlogin = uc_user_synlogin($uid);
            } elseif ($uid == -1) {
                // Ucenter会员不存在
                if (!$data) {
                    return -1;
                }
                // 注册Ucenter会员
                $uid = uc_user_register($data['username'], $password, $data['email']);
                if ($uid > 0) {
                    $synlogin = uc_user_synlogin($uid);
                } elseif ($uid == -1) {
                    return -4;
                } else {
                    return -3;
                }
            } else {
                return -2;
            }
        } else {
            // 高级验证
            if (!$data) {
                $data = dr_vip_login($this->db, $username);
            }
            // 会员不存在
            if (!$data) {
                return -1;
            }
            // 密码验证
            $password = trim($password);
            if (md5(md5($password).$data['salt'].md5($password)) != $data['password']) {
                return -2;
            }
        }

		$this->ci->uid = $data['uid'];
        $this->_login_log($data['uid']);

        // 返字段值，默认返回email
        if ($back) {
            return $data;
        }

        $expire = $expire ? $expire : 36000;
        foreach ($MEMBER['synurl'] as $url) {
            $code = dr_authcode($data['uid'].'-'.$data['salt'], 'ENCODE');
            $synlogin.= '<script type="text/javascript" src="'.$url.'/index.php?c=api&m=synlogin&expire='.$expire.'&code='.$code.'"></script>';
        }


        $this->input->set_cookie('member_uid', $data['uid'], 86400);
        $this->input->set_cookie('member_cookie', substr(md5(SYS_KEY . $data['password']), 5, 20), 86400);

        return $synlogin;
    }

    /**
     * 登录记录
     *
     * @param	intval	$uid		会员uid
     * @param	string	$OAuth		快捷登录
     * @param	intval	$is_admin	是否管理员
     */
    private function _login_log($uid, $OAuth = '', $is_admin = 0) {

        $ip = $this->input->ip_address();
        if (!$ip || !$uid) {
            return;
        }

        $agent = ($this->agent->is_mobile() ? $this->agent->mobile() : $this->agent->platform()).' '.$this->agent->browser().' '.$this->agent->version();
        if (strlen($agent) <= 5) {
            return;
        }

        $data = array(
            'uid' => $uid,
            'loginip' => $ip,
            'logintime' => SYS_TIME,
            'useragent' => substr($agent, 0, 255),
        );

        if (!$is_admin) {
            $data['oauthid'] = $OAuth;
        }

        $table = $is_admin ? 'admin_login' : 'member_login';

        // 同一天Ip一致时只更新一次更新时间
        if ($row = $this->db
                        ->select('id')
                        ->where('uid', $uid)
                        ->where('loginip', $ip)
                        ->where('DATEDIFF(from_unixtime(logintime),now())=0')
                        ->get($table)
                        ->row_array()) {
            $this->db->where('id', $row['id'])->update($table, $data);
        } else {
            $this->db->insert($table, $data);
        }

        // 会员部分只保留10条登录记录
        if (!$is_admin) {
            $row = $this->db->where('uid', $uid)->order_by('logintime desc')->get($table)->result_array();
            if (count($row) > 10) {
                $del = array();
                foreach ($row as $i => $t) {
                    $del[] = (int) $t['id'];
                    if ($i >= 9) {
                        break;
                    }
                }
                $this->db->where('uid', $uid)->where_not_in('id', $del)->delete($table);
            }
        }
    }

    /**
     * 前端会员退出登录
     *
     * @return	string
     */
    public function logout() {

        // 注销授权登陆的会员
        if ($this->session->userdata('member_auth_uid')) {
            $this->session->set_userdata('member_auth_uid', 0);
            return;
        }

        $synlogin = '';
        $MEMBER = $this->ci->get_cache('member');
        $MEMBER['setting']['ucenter'] && $synlogin.= uc_user_synlogout();
        defined('UCSSO_API') && $synlogin.= ucsso_synlogout();

        foreach ($MEMBER['synurl'] as $url) {
            $synlogin.= '<script type="text/javascript" src="'.$url.'/index.php?c=api&m=synlogout"></script>';
        }

        return $synlogin;
    }

    /**
     * 注册会员 验证
     *
     * @param	array	$data	会员数据
     * @return	int
     * int	uid	注册成功
     * int	-1	会员名称已经存在
     * int	-2	Email格式有误
     * int	-3	Email已经被注册
     * int	-4	同一IP注册限制
     * int	-5	Ucenter 会员名不合法
     * int	-6	Ucenter 包含不允许注册的词语
     * int	-7	Ucenter Email 格式有误
     * int	-8	Ucenter Email 不允许注册
     * int	-9	Ucenter Email 已经被注册
     * int	-10	手机号码不正确
     * int	-11	手机号码已经被注册
     */
    public function register($data, $groupid = NULL, $uid = NULL) {

        $setting = $this->ci->get_cache('member', 'setting');
        $this->ucsynlogin = $this->synlogin = '';

        if (!IS_ADMIN && !$uid
            && $setting['regiptime']
            && $this->db->where('regip', $this->input->ip_address())->where('regtime>', SYS_TIME - 3600 * $setting['regiptime'])->count_all_results('member')) {
            return -4;
        }

        // 模式认证
        if (!IS_ADMIN) {
            if (count($setting['regfield']) == 1 && in_array('phone', $setting['regfield'])) {
                // 当只有手机号码时
                $data['email'] = '';
                $data['username'] = $data['phone'];
            } elseif (count($setting['regfield']) == 1 && in_array('username', $setting['regfield'])) {
                $data['phone'] = '';
                $data['email'] = '';
            } elseif (count($setting['regfield']) == 1 && in_array('email', $setting['regfield'])) {
                $data['phone'] = '';
                $data['username'] = $data['email'];
            }
        }


        !$data['username'] && $data['phone'] && $data['username'] = $data['phone'];
        !$data['username'] && $data['email'] && $data['username'] = $data['email'];

        // 验证邮箱
        if (@in_array('email', $setting['regfield'])) {
            if (!$data['email'] || !preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $data['email'])) {
                return -2;
            } elseif ($this->db->where('email', $data['email'])->count_all_results('member')) {
                return -3;
            }
        }

        // 验证手机
        if (@in_array('phone', $setting['regfield'])) {
            if (strlen($data['phone']) != 11 || !is_numeric($data['phone'])) {
                return -10;
            } elseif ($this->db->where('phone', $data['phone'])->count_all_results('member')) {
                return -11;
            }
        }

        // 验证账号
        if ($this->db->where('username', $data['username'])->count_all_results('member')) {
            return -1;
        }

        // Ucenter 注册判断
        if (defined('UCSSO_API')) {
            /*
                    大于 0:返回用户 ID，表示用户注册成功
                     0:失败
                    -1:用户名不合法
                    -2:用户名已经存在
                    -3:Email 格式有误
                    -4:该 Email 已经被注册
                    -5:该 手机号码 格式有误
                    -6:该 手机号码 已经被注册
                */
            $rt = ucsso_register($data['username'], $data['password'], $data['email'], $data['phone']);
            if ($rt['code'] == -1) {
                return -5;
            } elseif ($rt['code'] == -2) {
                return -1;
            } elseif ($rt['code'] == -3) {
                return -2;
            } elseif ($rt['code'] == -4) {
                return -3;
            } elseif ($rt['code'] == -5) {
                return -10;
            } elseif ($rt['code'] == -6) {
                return -11;
            } elseif ($rt['code'] == 0) {
                return 0;
            }
            $this->ucsso_id = (int)$rt['code'];
        } elseif ($setting['ucenter']) {
            // 验证邮箱
            if (!$data['email'] || !preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $data['email'])) {
                return -2;
            } elseif ($this->db->where('email', $data['email'])->count_all_results('member')) {
                return -3;
            } elseif (uc_get_user($data['username'])) {
                return -1;
            }
            $ucid = uc_user_register($data['username'], $data['password'], $data['email']);
            if ($ucid == -1) {
                return -5;
            } elseif ($ucid == -2) {
                return -6;
            } elseif ($ucid == -4) {
                return -7;
            } elseif ($ucid == -5) {
                return -8;
            } elseif ($ucid == -6) {
                return -9;
            }
        }

        return $this->_register($data, NULL, $groupid, $uid);
    }

    /**
     * 注册会员 入库
     *
     * @param	array	$data		会员数据
     * @param	string	$OAuth		OAuth名称
     * @param	intval	$groupid	组id
     * @return	int
     */
    public function _register($data, $OAuth = NULL, $groupid = NULL, $uid = NULL) {

        $salt = substr(md5(rand(0, 999)), 0, 10); // 随机10位密码加密码
        $regverify = $this->ci->get_cache('member', 'setting', 'regverify');

        if ($OAuth) {
            // OAuth注册时，会员初始化信息
            $groupid = 2;
            if ($this->ci->get_cache('member', 'setting', 'regoauth')) {
                // 直接注册
                $data['nickname'] = dr_clear_emoji(dr_weixin_emoji($data['nickname']));
                !$data['nickname'] && $data['nickname'] = rand(1,99).SYS_TIME;
                $data['username'] = $this->db->where('username', $data['nickname'])->count_all_results('member') ? $data['nickname'].'2' : $data['nickname'];
            } else {
                // 绑定账号
                $data['username'] = '';
            }
            $username = $data['username'];
            $this->db->insert('member', array(
                'salt' => $salt,
                'name' => $data['nickname'] ? $data['nickname'] : '',
                'phone' => '',
                'regip' => $this->input->ip_address(),
                'email' => '',
                'spend' => 0,
                'money' => 0,
                'score' => 0,
                'avatar' => $data['avatar'] ? $data['avatar'] : '',
                'freeze' => 0,
                'regtime' => SYS_TIME,
                'groupid' => $groupid,
                'levelid' => 0,
                'overdue' => 0,
                'username' => $username,
                'password' => '',
                'randcode' => 0,
                'ismobile' => 0,
                'experience' => 0,
            ));
            $uid = $data['uid'] = $this->db->insert_id();
            unset($data['username']);
            // 保存OAuth数据
            $this->db->insert('member_oauth', $data);
            // 快捷登陆注册时挂钩点
            $data['username'] = $username;
            $this->hooks->call_hook('member_oauth_register', array('uid' => $uid, 'oauth' => $OAuth));
        } elseif ($uid) {
            // OAuth组转换为普通组
            $data['email'] = strtolower($data['email']);
            $data['phone'] = trim($data['phone']);
            $data['password'] = trim($data['password']);
            $groupid = 3;
            $this->db->where('uid', (int) $uid)->update('member', array(
                'salt' => $salt,
                'email' => $data['email'],
                'groupid' => $groupid,
                'username' => $data['username'],
                'password' => md5(md5($data['password']).$salt.md5($data['password']))
            ));
        } else {
            // 正常注册时，会员初始化信息
            $data['email'] = strtolower($data['email']);
            $data['password'] = trim($data['password']);
            $groupid = $groupid ? $groupid : ($regverify ? 1 : 3);
            $randcode = $regverify == 3 ? rand(100000, 999999) : 0;
            $this->db->insert('member', array(
                'salt' => $salt,
                'name' => '',
                'phone' => $data['phone'] ? $data['phone'] : '',
                'regip' => $this->input->ip_address(),
                'email' => $data['email'],
                'money' => 0,
                'score' => 0,
                'spend' => 0,
                'avatar' => '',
                'freeze' => 0,
                'regtime' => SYS_TIME,
                'groupid' => $groupid,
                'levelid' => 0,
                'overdue' => 0,
                'username' => $data['username'],
                'password' => md5(md5($data['password']).$salt.md5($data['password'])),
                'randcode' => $randcode,
                'ismobile' => 0,
                'experience' => 0,
            ));
            $uid = $this->db->insert_id();
            if ($regverify == 1) {
                // 邮件审核
                $url = dr_member_url('login/verify').'&code='.$this->get_encode($uid);
                $this->sendmail($data['email'], fc_lang('会员注册-邮件验证'), fc_lang(@file_get_contents(WEBPATH.'cache/email/verify.html'), $data['username'], $url, $url, $this->input->ip_address()));
            } elseif ($regverify == 3) {
                // 手机审核
                $this->sendsms($data['phone'], fc_lang('尊敬的用户，您的本次验证码是：%s', $randcode));
            } elseif ($regverify == 2) {
                // 人工审核
                $this->admin_notice('member', fc_lang('新会员【%s】注册审核', $data['username']), 'member/admin/home/index/field/uid/keyword/'.$uid);
            }
        }


        // uid 同步
        if ($this->ucsso_id && defined('UCSSO_API')) {
            $rt = ucsso_syncuid($this->ucsso_id, $uid);
            if (!$rt['code']) {
                // 同步失败
                log_message('error', 'UCSSO同步uid失败：'.$rt['msg']);
            }
        }

        // 邀请注册
        $invite = (int)$this->input->get('uid');
        if (dr_is_app('invite') && $invite && ($member = $this->get_base_member($invite))) {
            $idata = array(
                'uid' => $invite,
                'rid' => $uid,
                'rname' => $data['username'] ? $data['username'] : $username,
                'regtime' => SYS_TIME,
                'username' => $member['username']
            );
            $this->db->insert('member_invite', $idata);
            // 关注ta
            if (MEMBER_OPEN_SPACE) {
                $this->load->add_package_path(FCPATH.'module/space/');
                $this->load->model('sns_model');
                $this->sns_model->following($invite, $uid);
            }
            // 分数奖励
            $value = $this->ci->get_cache('member', 'setting', 'permission', $member['markrule']);
            $value['invite_experience'] && $this->update_score(0, $invite, (int)$value['invite_experience'], 'invite-'.$uid, "邀请好友注册奖励");
            $value['invite_score'] && $this->update_score(1, $invite, (int)$value['invite_score'], 'invite-'.$uid, "邀请好友注册奖励");
            // 邀请注册后的挂钩点
            $this->hooks->call_hook('member_invite', $idata);
            unset($value, $member, $idata);
        }
        // 注册时创建空间
        if ($this->ci->get_cache('member', 'setting', 'regspace') && MEMBER_OPEN_SPACE) {
            $this->load->add_package_path(FCPATH.'module/space/');
            $this->load->model('space_model');
            $this->space_model->update($uid, $groupid, array(
                'name' => $data['username'].'的空间',
            ));
        }

        // 会员组升级挂钩点
        $this->hooks->call_hook('member_group_upgrade', array('uid' => $uid, 'groupid' => $groupid));

        return $uid;
    }

    // 修改邮箱和密码
    public function edit_email_password($username, $data) {

        // 验证本站会员
        if (!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $data['email'])) {
            return -2;
        } elseif ($this->db->where('email', $data['email'])->count_all_results('member')) {
            return -3;
        }
        // 验证UCenter
        if (defined('UC_KEY')) {
            $ucid = uc_user_edit($username, NULL, $data['password'], $data['email'], 1);
            if ($ucid == -1) {
                return -5;
            } elseif ($ucid == -2) {
                return -6;
            } elseif ($ucid == -4) {
                return -7;
            } elseif ($ucid == -5) {
                return -8;
            } elseif ($ucid == -6) {
                return -9;
            }
        }
        // 修改资料
        $salt = substr(md5(rand(0, 999)), 0, 10); // 随机10位密码加密码
        $data['password'] = trim($data['password']);
        $this->db->where('username', $username)->update('member', array(
            'salt' => $salt,
            'email' => $data['email'],
            'groupid' => 3,
            'password' => md5(md5($data['password']).$salt.md5($data['password']))
        ));
    }

    /**
     * 取会员COOKIE
     *
     * @return	int	$uid	会员uid
     */
    public function member_uid($login = 0) {

        if (!$login && IS_MEMBER && $uid = $this->session->userdata('member_auth_uid')) {
            // 更新online表
            $this->db->replace('member_online', array('uid' => $uid, 'time' => SYS_TIME));
            return $uid;
        } else {
            $uid = (int)get_cookie('member_uid');
            if (!$uid) {
                return NULL;
            }
            if (!$this->session->userdata('uid')) {
                $this->_login_log($uid); // 更新登录时间
                $this->session->set_userdata('uid', $uid); // 更新会员活动时间
            }
            // 更新online表
            $this->db->replace('member_online', array('uid' => $uid, 'time' => SYS_TIME));
            return $uid;
        }
    }

    // 验证会员有效性
    public function check_member_login() {

        // 授权登陆时不验证
        if ($this->uid && $this->session->userdata('member_auth_uid') == $this->uid) {
            return 1;
        }

        $cookie = get_cookie('member_cookie');
        if (!$cookie) {
            return 0;
        }

        if (substr(md5(SYS_KEY.$this->member['password']), 5, 20) !== $cookie) {
            if (defined('UCSSO_API')) {
                $rt = ucsso_get_password($this->uid);
                if ($rt['code']) {
                    // 变更本地库
                    $this->db->where('uid', $this->uid)->update('member', array(
                        'salt' => $rt['data']['salt'],
                        'password' => $rt['data']['password'],
                    ));
                }
            }
            return 0;
        }

        return 1;
    }

    /**
     * 会员配置信息
     *
     * @return	array
     */
    public function setting($isdomain = FALSE) {

        $domain = $member = $data = array();

        // 查询出配置信息
        $setting = $this->db->get('member_setting')->result_array();
        foreach ($setting as $t) {
            $t['name'] == 'member' ? $member = dr_string2array($t['value']) : $data[$t['name']] = dr_string2array($t['value']);
        }
        $data = $data + $member;
        // 返回域名信息
        if ($isdomain && $data['domain']) {
            foreach($data['domain'] as $c) {
                $c && $domain[] = dr_http_prefix($c);
            }
        }

        return $isdomain ? array($data, $domain) : $data;
    }

    /**
     * 会员配置
     *
     * @return	array
     */
    public function member($set = NULL) {

        $data = $this->db->where('name', 'member')->get('member_setting')->row_array();
        $data = dr_string2array($data['value']);

        // 修改数据
        if ($set) {
            $this->db->where('name', 'member')->update('member_setting', array('value' => dr_array2string($set)));
            $data = $set;
        }

        return $data;
    }

    /**
     * 会员权限
     *
     * @param	intval	$id		权限组标识
     * @param	string	$set	权限组值
     * @return	array
     */
    public function permission($id, $set = NULL) {

        $data = $this->db->where('name', 'permission')->get('member_setting')->row_array();
        $data = dr_string2array($data['value']);

        // 修改数据
        if ($set) {
            $data[$id] = $set;
            $this->db->where('name', 'permission')->update('member_setting', array('value' => dr_array2string($data)));
        }

        return isset($data[$id]) ? $data[$id] : NULL;
    }

    /**
     * 支付配置
     *
     * @param	array	$set	修改数据
     * @return	array
     */
    public function pay($set = NULL) {

        $data = $this->db->where('name', 'pay')->get('member_setting')->row_array();
        $data = dr_string2array($data['value']);

        // 修改数据
        if ($set) {
            $this->db->where('name', 'pay')->update('member_setting', array('value' => dr_array2string($set)));
            $data = $set;
        }

        return $data;
    }

    /**
     * 提现配置
     *
     * @param	array	$set	修改数据
     * @return	array
     */
    public function cash($set = NULL) {

        $data = $this->db->where('name', 'cash')->get('member_setting')->row_array();
        $data = dr_string2array($data['value']);

        // 修改数据
        if ($set) {
            $this->db->replace('member_setting', array(
                'name' => 'cash',
                'value' => dr_array2string($set)
            ));
            return $set;
        }

        return $data;
    }

    /**
     * 游客配置
     *
     * @param	array	$set	修改数据
     * @return	array
     */
    public function guest($set = NULL) {

        $data = $this->db->where('name', 'guest')->get('member_setting')->row_array();
        !$data && $this->db->insert('member_setting', array('name' => 'guest', 'value' => ''));
        $data = dr_string2array($data['value']);
        // 修改数据
        if ($set) {
            $this->db->where('name', 'guest')->update('member_setting', array('value' => dr_array2string($set)));
            $data = $set;
        }

        return $data;
    }

    /**
     * 会员空间配置
     *
     * @param	array	$set	修改数据
     * @return	array
     */
    public function space($set = NULL) {

        $data = $this->db->where('name', 'space')->get('member_setting')->row_array();
        $data = dr_string2array($data['value']);

        // 修改数据
        if ($set) {
            $this->db->where('name', 'space')->update('member_setting', array('value' => dr_array2string($set)));
            $data = $set;
        }

        return $data;
    }

    /**
     * 会员缓存
     *
     * @param	int		$id
     * @return	NULL
     */
    public function cache() {

        $cache = array();
        $this->dcache->delete('member');

        // 会员自定义字段
        $field = $this->db
                      ->where('disabled', 0)
                      ->where('relatedid', 0)
                      ->where('relatedname', 'member')
                      ->order_by('displayorder ASC,id ASC')
                      ->get('field')
                      ->result_array();
        if ($field) {
            foreach ($field as $t) {
                $t['setting'] = dr_string2array($t['setting']);
                $cache['field'][$t['fieldname']] = $t;
            }
        }

        // 会员空间自定义字段
        $field = $this->db
                      ->where('disabled', 0)
                      ->where('relatedid', 0)
                      ->where('relatedname', 'spacetable')
                      ->order_by('displayorder ASC,id ASC')
                      ->get('field')
                      ->result_array();
        if ($field) {
            foreach ($field as $t) {
                $t['setting'] = dr_string2array($t['setting']);
                $cache['spacefield'][$t['fieldname']] = $t;
            }
        }

        // 会员组
        $group = $this->db->order_by('displayorder ASC, id ASC')->get('member_group')->result_array();
        if ($group) {
            foreach ($group as $t) {
                $t['allowfield'] = dr_string2array($t['allowfield']);
                $t['spacefield'] = dr_string2array($t['spacefield']);
                // 会员等级
                $level = $this->db->where('groupid', $t['id'])->order_by('experience ASC')->get('member_level')->result_array();
                if ($level) {
                    foreach ($level as $l) {
                        $t['level'][$l['id']] = $l;
                    }
                    $cache['group'][$t['id']] = $t;
                } elseif ($t['id'] < 3) {
                    $cache['group'][$t['id']] = $t;
                }
            }
        }

        $cache['synurl'] = array();
        list($cache['setting'], $cache['synurl']) = $this->setting(TRUE);
        $cache['rule'] = $this->ci->get_cache('urlrule', (int)$cache['setting']['urlrule'], 'value'); // 会员规则
        $cache['setting']['space']['rule'] = $this->ci->get_cache('urlrule', (int)$cache['setting']['space']['urlrule'], 'value'); // 空间规则
        $domain = require WEBPATH.'config/domain.php'; // 加载站点域名配置文件
        // 加载分站域名配置文件
        $fenzhan_domain = SITE_FID ? require WEBPATH.'config/fenzhan.php' : array();

        // 增加到登录同步列表中
        foreach ($this->site_info as $sid => $t) {
            // 主站点域名
            $cache['synurl'][] = dr_http_prefix($t['SITE_DOMAIN']);
            // 移动端域名
            $t['SITE_MOBILE'] && $cache['synurl'][] = dr_http_prefix($t['SITE_MOBILE']);
            // 将站点的域名配置文件加入同步列表中
            foreach ($domain as $url => $site_id) {
                if ($url && $site_id == $sid) {
                    if (isset($fenzhan_domain[$url]) && $fenzhan_domain[$url]) {
                        // 分站域名
                        $cache['synurl'][] = dr_http_prefix($url);
                    } elseif ($t['SITE_DOMAIN'] != $url && $t['SITE_MOBILE'] != $url) {
                        // 筛选出站点域名和移动端域名
                        $cache['synurl'][] = dr_http_prefix($url);
                    }
                }
            }
        }
        // 空间域名
        $cache['setting']['space']['domain'] && $cache['synurl'][] = prep_url($cache['setting']['space']['domain']);
        $cache['synurl'] = array_unique($cache['synurl']);

        // 更新Ucenter配置
        if ($cache['setting']['ucenter']) {
            $s = '<?php ' . PHP_EOL . '/* UCenter配置 */' . PHP_EOL
                . stripslashes($cache['setting']['ucentercfg'])
                . PHP_EOL . '/* FineCMS配置 */' . PHP_EOL
                . '$dbhost    = \'' . $this->db->hostname . '\';' . PHP_EOL
                . '$dbuser    = \'' . $this->db->username . '\';' . PHP_EOL
                . '$dbpw      = \'' . $this->db->password . '\';' . PHP_EOL
                . '$dbname    = \'' . $this->db->database . '\';' . PHP_EOL
                . '$pconnect  = 0;' . PHP_EOL
                . '$tablepre  = \'' . $this->db->dbprefix . '\';' . PHP_EOL
                . '$dbcharset = \'utf8\';' . PHP_EOL
                . '/* 同步登录Cookie */' . PHP_EOL
                . 'define(\'SITE_KEY\', \'' . SYS_KEY . '\');' . PHP_EOL
                . 'define(\'SITE_PREFIX\', \'' . config_item('cookie_prefix') . '\');' . PHP_EOL
                . '?>';
            file_put_contents(WEBPATH.'api/ucenter/config.inc.php', $s);
        }

        // 更新UCSSO配置
        if ($cache['setting']['ucsso']) {
            file_put_contents(WEBPATH.'api/ucsso/config.php', stripslashes($cache['setting']['ucssocfg']));
        }

        $this->ci->clear_cache('member');
        $this->dcache->set('member', $cache);

        return $cache;
    }

    /**
     * 条件查询
     *
     * @param	object	$select	查询对象
     * @param	array	$param	条件参数
     * @return	array
     */
    private function _where(&$select, $data) {


        // 存在POST提交时，重新生成缓存文件
        if (IS_POST) {
            $data = $this->input->post('data');
            foreach ($data as $i => $t) {
                if ($t == '') {
                    unset($data[$i]);
                }
            }
        }

        // 存在search参数时，读取缓存文件
        if ($data) {
            if (isset($data['keyword']) && $data['keyword'] != '' && $data['field']) {
                if ($data['field'] == 'uid') {
                    // 按id查询
                    $id = array();
                    $ids = explode(',', $data['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int)$i;
                    }
                    $select->where_in('uid', $id);
                } elseif ($data['field'] == 'ismobile') {
                    $select->where($data['field'], intval($data['keyword']));
                } elseif (in_array($data['field'], array('complete', 'is_auth'))) {
                    $select->where('uid IN (select uid from `'.$this->db->dbprefix('member_data').'` where `'.$data['field'].'` = '.intval($data['keyword']).')');
                } elseif (in_array($data['field'], array('phone', 'name', 'email', 'username'))) {
                    $select->like($data['field'], urldecode($data['keyword']));
                } else {
                    // 查询附表字段
                    $select->where('uid IN (select uid from `'.$this->db->dbprefix('member_data').'` where `'.$data['field'].'` LIKE "%'.urldecode($data['keyword']).'%")');
                }
            }
            // 查询会员组
            isset($data['groupid']) && $data['groupid'] && $select->where('groupid', (int)$data['groupid']);
        }

        // 判断groupid
        !isset($data['groupid']) && $_GET['groupid'] && $select->where('groupid', (int)$_GET['groupid']);

        return $data;
    }

    /**
     * 数据分页显示
     *
     * @param	array	$param	条件参数
     * @param	intval	$page	页数
     * @param	intval	$total	总数据
     * @return	array
     */
    public function limit_page($param, $page, $total) {

        if (!$total || IS_POST) {
            $select = $this->db->select('count(*) as total');
            $_param = $this->_where($select, $param);
            $data = $select->get('member')->row_array();
            unset($select);
            $total = (int) $data['total'];
            if (!$total) {
                $_param['total'] = 0;
                return array(array(), $_param);
            }
            $page = 1;
        }

        $select = $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $_param = $this->_where($select, $param);
        $order = dr_get_order_string(isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0 ? $this->input->get('order', TRUE) : 'uid desc', 'uid desc');
        $data = $select->order_by($order)->get('member')->result_array();
        $_param['total'] = $total;
        $_param['order'] = $order;

        return array($data, $_param);
    }

    /**
     * 更新分数
     *
     * @param	intval	$type	0积分;1虚拟币
     * @param	intval	$uid	会员id
     * @param	intval	$value	分数变动值
     * @param	string	$mark	标记
     * @param	string	$note	备注
     * @param	intval	$count	统计次数
     * @return	intval
     */
    public function update_score($type, $uid, $val, $mark, $note = '', $count = 0) {

        if (!$uid || !$val) {
            return NULL;
        }

        $table = $this->db->dbprefix('member_scorelog');
        if ($count && $this->db->where('type', (int)$type)->where('mark', $mark)->count_all_results($table) >= $count) {
            return NULL;
        }

        $data = $this->db->select('score,experience')->where('uid', $uid)->get('member')->row_array();
        $score = $type ? (int)$data['score'] : (int)$data['experience'];
        $value = $score + $val;
        $value = $value > 0 ? $value : 0; // 不允许积分或虚拟币小于0
        unset($data);

        // 更新
        $type ? $this->db->where('uid', (int)$uid)->update('member', array('score' => $value)) : $this->db->where('uid', (int)$uid)->update('member', array('experience' => $value));

        unset($value);

        $this->db->insert($table, array(
            'uid' => $uid,
            'type' => $type,
            'mark' => $mark,
            'note' => $note,
            'value' => $val,
            'inputtime' => SYS_TIME,
        ));

        return $this->db->insert_id();
    }

    /**
     * 会员初始化处理
     */
    public function init_member() {

        // 明天凌晨时间戳
        $time = strtotime(date('Y-m-d', strtotime('+1 day')));

        // 每日登录积分处理
        if (!get_cookie('login_experience_'.$this->uid)
            && !$this->db
                     ->where('uid', $this->uid)
                     ->where('type', 0)
                     ->where('mark', 'login')
                     ->where('DATEDIFF(from_unixtime(inputtime),now())=0')
                     ->count_all_results('member_scorelog')) {
            set_cookie('login_experience_'.$this->uid, TRUE, $time - SYS_TIME);
            $this->update_score(0, $this->uid, (int)$this->member_rule['login_experience'], 'login', fc_lang('每日登陆'));
        }

        // 每日登录虚拟币处理
        if (!get_cookie('login_score_'.$this->uid)
            && !$this->db
                     ->where('uid', (int) $this->uid)
                     ->where('type', 1)
                     ->where('mark', 'login')
                     ->where('DATEDIFF(from_unixtime(inputtime),now())=0')
                     ->count_all_results('member_scorelog')) {
            set_cookie('login_score_'.$this->uid, TRUE, $time - SYS_TIME);
            $this->update_score(1, $this->uid, (int) $this->member_rule['login_score'], 'login', fc_lang('每日登陆'));
        }
    }

    /**
     * 邮件发送
     *
     * @param	string	$tomail
     * @param	string	$subject
     * @param	string	$message
     * @return  bool
     */
    public function sendmail($tomail, $subject, $message) {

        if (!$tomail || !$subject || !$message) {
            return FALSE;
        }

        $cache = $this->ci->get_cache('email');
        if (!$cache) {
            return NULL;
        }

        $this->load->library('Dmail');
        foreach ($cache as $data) {
            $this->dmail->set(array(
                'host' => $data['host'],
                'user' => $data['user'],
                'pass' => $data['pass'],
                'port' => $data['port'],
                'from' => $data['user'],
            ));
            if ($this->dmail->send($tomail, $subject, $message)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * 短信发送
     *
     * @param	string	$mobile
     * @param	string	$content
     * @return  bool
     */
    public function sendsms($mobile, $content) {

        if (!$mobile || !$content) {
            return FALSE;
        }

        $file = WEBPATH.'config/sms.php';
        if (!is_file($file)) {
            return FALSE;
        }

        $config = require_once $file;
        if ($config['third']) {
            $this->load->helper('sms');
            if (function_exists('my_sms_send')) {
                $result = my_sms_send($mobile, $content, $config);
            } else {
                return FALSE;
            }
        } else {
            $result = dr_catcher_data('http://sms.dayrui.com/index.php?uid='.$config['uid'].'&key='.$config['key'].'&mobile='.$mobile.'&content='.$content.'【'.$config['note'].'】&domain='.trim(str_replace('http://', '', SITE_URL), '/').'&sitename='.SITE_NAME);
            if (!$result) {
                return FALSE;
            }
            $result = dr_object2array(json_decode($result));
        }

        @file_put_contents(WEBPATH.'cache/sms_error.log', date('Y-m-d H:i:s').' ['.$mobile.'] ['.$result['msg'].'] （'.str_replace(array(chr(13), chr(10)), '', $content).'）'.PHP_EOL, FILE_APPEND);

        return $result;
    }

    /**
     * 验证码加密
     *
     * @param	intval	$uid
     * @return  string
     */
    public function get_encode($uid) {
        $randcode = rand(1000, 999999);
        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);
        $this->db->where('uid', $uid)->update('member', array('randcode' => $randcode));
        return $this->encrypt->encode(SYS_TIME.','.$uid.','.$randcode);
    }

    /**
     * 验证码解码
     *
     * @param	string	$code
     * @return  string
     */
    public function get_decode($code) {
        $code = str_replace(' ', '+', $code);
        $this->encrypt->set_cipher(MCRYPT_BLOWFISH);
        return $this->encrypt->decode($code);
    }

    /**
     * 会员删除
     *
     * @param	intval	$uid
     * @return  bool
     */
    public function delete($uids) {

        if (!$uids || !is_array($uids)) {
            return NULL;
        }

        $this->load->model('attachment_model');
        $app = $this->db->get('application')->result_array();

        foreach ($uids as $uid) {
            if ($uid == 1) {
                continue;
            }
            $tableid = (int)substr((string)$uid, -1, 1);
            // 删除会员表
            $this->db->where('uid', $uid)->delete('member');
            // 删除会员附表
            $this->db->where('uid', $uid)->delete('member_data');
            // 删除会员地址表
            $this->db->where('uid', $uid)->delete('member_address');
            // 删除快捷登陆表
            $this->db->where('uid', $uid)->delete('member_oauth');
            // 删除会员登录日志表
            $this->db->where('uid', $uid)->delete('member_login');
            // 删除管理员表
            $this->db->where('uid', $uid)->delete('admin');
            // 删除支付记录
            $this->db->where('uid', $uid)->delete('member_paylog');
            // 删除积分记录
            $this->db->where('uid', $uid)->delete('member_scorelog');
            // 删除附件
            $this->attachment_model->delete_for_uid($uid);
            // 按站点删除模块数据
            foreach ($this->site_info as $siteid => $v) {
                $cache = $this->dcache->get('module-'.$siteid);
                if ($cache) {
                    foreach ($cache as $dir => $mod) {
                        $table = $this->site[$siteid]->dbprefix($siteid.'_'.$dir);
                        if (!$this->site[$siteid]->where('uid', $uid)->count_all_results($table.'_index')) {
                            continue;
                        }
                        // 删除主表
                        $this->site[$siteid]->where('uid', $uid)->delete($table);
                        // 删除索引表
                        $this->site[$siteid]->where('uid', $uid)->delete($table.'_index');
                        // 删除审核表
                        $this->site[$siteid]->where('uid', $uid)->delete($table.'_verify');
                        // 删除标记表
                        $this->site[$siteid]->where('uid', $uid)->delete($table.'_flag');
                        // 删除栏目表
                        $this->site[$siteid]->where('uid', $uid)->delete($table.'_category_data');
                        // 删除附表
                        for ($i = 0; $i < 125; $i ++) {
                            if (!$this->site[$siteid]->query("SHOW TABLES LIKE '%".$table.'_data_'.$i."%'")->row_array()) {
                                break;
                            }
                            $this->site[$siteid]->where('uid', $uid)->delete($table.'_data_'.$i);
                        }
                        // 删除栏目附表
                        for ($i = 0; $i < 125; $i ++) {
                            if (!$this->site[$siteid]->query("SHOW TABLES LIKE '%".$table.'_category_data_'.$i."%'")->row_array()) {
                                break;
                            }
                            $this->site[$siteid]->where('uid', $uid)->delete($table.'_category_data_'.$i);
                        }
                    }
                }
            }
            // 按应用删除
            if ($app) {
                foreach ($app as $a) {
                    $dir = $a['dirname'];
                    if (is_file(FCPATH.'app/'.$dir.'/models/'.$dir.'_model.php')) {
                        $this->load->add_package_path(FCPATH.'app/'.$dir.'/');
                        $this->load->model($dir.'_model', 'app_model');
                        $this->app_model->delete_for_uid($uid);
                        $this->load->remove_package_path(FCPATH.'app/'.$dir.'/');
                    }
                }
            }
            // 删除会员附件
            $this->load->helper('file');
            delete_files(SYS_UPLOAD_PATH.'/member/'.$uid.'/');
            // 删除通知
            $this->db->where('uid', $uid)->delete('member_notice_'.$tableid);
        }
        // 删除空间
        if (MEMBER_OPEN_SPACE) {
            $this->load->add_package_path(FCPATH.'module/space/');
            $this->load->model('space_model');
            $this->space_model->delete($uids);
        }
    }

    /**
     * 添加一条通知
     *
     * @param	string	$uid
     * @param	intval	$type 1系统，2互动，3模块，4应用
     * @param	string	$note
     * @return	null
     */
    public function add_notice($uid, $type, $note) {

        if (!$uid || !$note) {
            return NULL;
        }

        $uids = is_array($uid) ? $uid : explode(',', $uid);
        foreach ($uids as $uid) {
            $tableid = (int)substr((string)$uid, -1, 1);
            $this->db->insert('member_notice_'.$tableid, array(
                'uid' => $uid,
                'type' => $type,
                'isnew' => 1,
                'content' => $note,
                'inputtime' => SYS_TIME,
            ));
            $this->db->replace('member_new_notice', array('uid' => $uid));
        }

        return NULL;
    }

    /**
     * 添加微博
     *
     * @param	uid 发布者
     * @param	content 内容
     * @param	attach 附件
     * @param	source 来源
     * @param	repost 转发id
     */
    public function add_sns($uid, $content, $attach = 0, $source = 0, $repost = 0) {

        // 判断uid是否存在
        if (!$uid) {
            return FALSE;
        }

        // 查询用户名
        if ($uid == $this->uid) {
            $username = $this->member['username'];
        } else {
            $m = dr_member_info($uid);
            $username = $m['username'];
            unset($m);
        }

        // 来源
        !$source && $source = $this->agent->is_mobile() ? fc_lang('来自移动端') : fc_lang('来自网站');
        
        // 过滤非法内容
        $content = dr_preg_html($content).' ';

        // 提取URL链接
        $content = preg_replace_callback('/((?:https?|mailto|ftp):\/\/([^\x{2e80}-\x{9fff}\s<\'\"“”‘’，。}]*)?)/u', '_format_feed_content_url_length', $content);

        // 提取@
        $user = array();
        if (preg_match_all('/@(.+) /U', $content, $match)) {
            $data = array_unique($match[1]);
            foreach ($data as $t) {
                $m = $this->db->select('uid')->where('username', $t)->get('member')->row_array();
                if ($m) {
                    $user[$t] = $m['uid'];
                    $content = str_replace('@'.$t.' ', ' <a href="javascript:;" uid="'.$m['uid'].'" event-node="face_card" target="_blank">@'.$t.'</a> ', $content);
                }
            }
            unset($data, $m);
        }

        // 提取话题
        $topic = array();
        if (preg_match_all('/#(.+)#/U', $content, $match)) {
            $data = array_unique($match[1]);
            foreach ($data as $t) {
                // 查询话题是否存在，不存在就创建
                $row = $this->db->where('name', $t)->get('sns_topic')->row_array();
                if ($row) {
                    $tid = $row['id'];
                } else {
                    $this->db->insert('sns_topic', array(
                        'name' => $t,
                        'uid' => $uid,
                        'username' => $username,
                        'count' => 0,
                        'inputtime' => SYS_TIME,
                    ));
                    $tid = $this->db->insert_id();
                }
                $topic[] = $tid;
                $content = str_replace('#'.$t.'#', '<a href="[TOPIC-URL-'.$tid.']" target="_blank">#'.$t.'#</a> ', $content);
            }
            unset($data);
        }

        $content = trim($content);
        if (!$content) {
            return FALSE;
        }

        // 是转发文章
        if ($repost) {
            $row = $this->db->where('id', $repost)->get('sns_feed')->row_array();
            if ($row) {
                $repost = $row['repost_id'] ? $row['repost_id'] : $row['id'];
                // 统计原文转发数量
                $this->db->where('id', $repost)->set('repost', 'repost+1', FALSE)->update('sns_feed');
                // 清除缓存数据
                $this->ci->set_cache_data('sns-feed-'.$repost, '', 1);
            } else {
                $repost = 0;
            }
        }

        $images = $attach ? trim($attach, '|') : '';

        // 插入的数据
        $this->db->insert('sns_feed', array(
            'uid' => $uid,
            'username' => $username,
            'comment' => 0,
            'repost' => 0,
            'digg' => 0,
            'content' => $content,
            'repost_id' => $repost,
            'source' => $source,
            'images' => $images,
            'inputip' => $this->input->ip_address(),
            'inputtime' => SYS_TIME,
        ));
        $id = $this->db->insert_id();

        // 保存附件
        if ($images) {
            $this->load->model('attachment_model');
            $this->attachment_model->replace_attach($uid, $this->db->dbprefix('sns_feed').'-'.$id, explode('|', $images));
        }

        // 更新话题关系表
        if ($topic) {
            foreach ($topic as $tid) {
                $this->db->insert('sns_topic_index', array('fid' => $id, 'tid' => $tid));
                $this->db->where('id', $tid)->set('count', 'count+1', FALSE)->update('sns_topic');
            }
        }

        // 给@的人发送提醒
        $user && $this->add_notice($user, 2, fc_lang('【%s】在动态中@提到了我，<a href="%s" target="_blank">查看动态</a>。', $username, dr_sns_feed_url($uid, $id)));
        
        // 给作者发送转发的提醒
        $repost && $this->add_notice($row['uid'], 2, fc_lang('【%s】转发了我的动态，<a href="%s" target="_blank">查看动态</a>。', $username, dr_sns_feed_url($uid, $id)));
        
        // 分数奖励
        if ($uid == $this->uid) {
            $this->member_rule['feed_experience'] && $this->update_score(0, $uid, (int)$this->member_rule['feed_experience'], '', "发布一条动态奖励");
            $this->member_rule['feed_score'] && $this->update_score(1, $uid, (int)$this->member_rule['feed_score'], '', "发布一条动态奖励");
        }

        return TRUE;
    }

    /**
     * 系统提醒
     *
     * @param	type    system系统  content内容相关  member会员相关 app应用相关
     * @param	msg     提醒内容
     * @param	uri     后台对应的链接
     * @param	to      通知对象 留空表示全部对象
     * array(
     *      to_uid 指定人
     *      to_rid 指定角色组
     * )
     */
    public function admin_notice($type, $msg, $uri, $to = array()) {

        $this->db->insert('admin_notice', array(
            'type' => $type,
            'msg' => $msg,
            'uri' => $uri,
            'to_rid' => intval($to['to_rid']),
            'to_uid' => intval($to['to_uid']),
            'status' => 0,
            'uid' => 0,
            'username' => '',
            'updatetime' => 0,
            'inputtime' => SYS_TIME,
        ));
    }

    /**
     * 处理系统提醒
     *
     * @param	uri     后台对应的链接
     * @param	status  状态值 3完成
     */
    public function update_admin_notice($uri, $status = 3) {

        $data = $this->db->where('uri', $uri)->where('status<>3')->get('admin_notice')->result_array();
        if ($data) {
            foreach ($data as $t) {
                $this->db->where('id', $t['id'])->update('admin_notice', array(
                    'status' => $status,
                    'uid' => $this->uid,
                    'username' => $this->member['username'],
                    'updatetime' => $status == 3 ? SYS_TIME : 0,
                ));
            }
        }
    }

}
