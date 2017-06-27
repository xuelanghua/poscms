<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Account extends M_Controller {

    /**
     * 基本资料
     */
    public function index() {

        $MEMBER = $this->get_cache('member');
        $error = NULL;
        $field = array(
            'name' => array(
                'name' => fc_lang('姓名'),
                'ismain' => 0,
                'ismember' => 1,
                'fieldname' => 'name',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                    ),
                    'validate' => array(
                        'xss' => 1,
                        'required' => 1,
                    )
                )
            ),
            'phone' => array(
                'name' => fc_lang('手机号码'),
                'ismain' => 0,
                'ismember' => 1,
                'fieldname' => 'phone',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                    ),
                    'validate' => array(
                        'xss' => 1,
                        'check' => '_check_phone',
                        'required' => 1,
                        'isedit' => @in_array('phone', $MEMBER['setting']['regfield']) ? 1 : 0,
                    )
                )
            ),
        );


        // 开启手机认证
        if ($MEMBER['setting']['ismobile']) {
            if ($this->member['ismobile'] && $this->member['phone']) {
                $field['phone']['setting']['validate']['isedit'] = 1;
                $field['phone']['setting']['validate']['formattr'] = '';
            } else {
                $field['check'] = array(
                    'name' => fc_lang('短信验证码'),
                    'ismain' => 0,
                    'ismember' => 1,
                    'fieldname' => 'check',
                    'fieldtype' => 'Text',
                    'setting' => array(
                        'option' => array(
                            'width' => 117,
                        ),
                        'validate' => array(
                            'xss' => 1,
                        )
                    )
                );
                $field['phone']['setting']['append'] = '<label style="padding-left:10px"><a class="btn btn-xs blue" onclick="dr_send_sms()"> <i class="fa fa-send"></i> '.fc_lang('短信验证码').'</a></label>';
            }
        }

        // 可用字段
        if ($MEMBER['field'] && $MEMBER['group'][$this->member['groupid']]['allowfield']) {
            foreach ($MEMBER['field'] as $t) {
                in_array($t['fieldname'], $MEMBER['group'][$this->member['groupid']]['allowfield']) && $field[] = $t;
            }
        }

        if (IS_POST) {

            // 快捷登录组完善资料
            if (!isset($data['error']) && $this->member['groupid'] == 2) {
                if (!$this->member['bang']) {
                    // 直接注册模式
                    $post = $this->input->post('member');
                    $data['email'] = $post['email'];
                    $id = $this->member_model->edit_email_password($this->member['username'], $post);
                } else {
                    // 绑定账号模式，需要重新注册一个账号
                    $post = $this->input->post('member');
                    $data['email'] = $post['email'];
                    $data['username'] = $post['username'];
                    $id = $this->member_model->register($post, NULL, $this->uid);
                }
                if ($id == -1) {
                    $data = array('error' => 'username', 'msg' => fc_lang('该会员【%s】已经被注册', $data['username']));
                } elseif ($id == -2) {
                    $data = array('error' => 'email', 'msg' => fc_lang('邮箱格式不正确'));
                } elseif ($id == -3) {
                    $data = array('error' => 'email', 'msg' => fc_lang('该邮箱【%s】已经被注册', $data['email']));
                } elseif ($id == -4) {
                    $data = array('error' => 'username', 'msg' => fc_lang('同一IP在限制时间内注册过多'));
                } elseif ($id == -5) {
                    $data = array('error' => 'username', 'msg' => fc_lang('Ucenter：会员名称不合法'));
                } elseif ($id == -6) {
                    $data = array('error' => 'username', 'msg' => fc_lang('Ucenter：包含不允许注册的词语'));
                } elseif ($id == -7) {
                    $data = array('error' => 'username', 'msg' => fc_lang('Ucenter：Email格式有误'));
                } elseif ($id == -8) {
                    $data = array('error' => 'username', 'msg' => fc_lang('Ucenter：Email不允许注册'));
                } elseif ($id == -9) {
                    $data = array('error' => 'username', 'msg' => fc_lang('Ucenter：Email已经被注册'));
                } elseif ($id == -10) {
                    $error = array('name' => 'phone', 'msg' => fc_lang('手机号码必须是11位的整数'));
                } elseif ($id == -11) {
                    $error = array('name' => 'phone', 'msg' => fc_lang('该手机号码已经注册'));
                }
                if (isset($data['error'])) {
                    $error = $data;
                    (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
                    $this->member_msg($error['msg'], dr_member_url('account/index'));
                } else {
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('account/index'), 1);
                }
            } else {
                $data = $this->validate_filter($field, $this->member);
                // 开启手机认证时
                $v = !isset($data['error']) && isset($field['check']);
                if ($MEMBER['setting']['ismobile'] && $v) {
                    // 号码是否重复
                    if ($this->db->where('uid<>', $this->uid)->where('phone', $data[0]['phone'])->count_all_results('member')) {
                        $data = array('error' => 'phone', 'msg' => fc_lang('该手机号码已经注册'));
                    } elseif ($data[0]['check'] && $data[0]['check'] != $this->member['randcode']) {
                        $data = array('error' => 'check', 'msg' => fc_lang('短信验证码不正确'));
                    } elseif (!$data[0]['check']) {
                        $data = array('error' => 'check', 'msg' => fc_lang('短信验证码未填写'));
                    }
                }
                // 邮箱验证
                $email = dr_safe_replace($this->input->post('email', TRUE));
                if ($email) {
                    if (!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $email)) {
                        $data = array('error' => 'email', 'msg' => fc_lang('邮箱格式不正确'));
                    } elseif ($this->db->where('email', $email)->count_all_results('member')) {
                        $data = array('error' => 'email', 'msg' => fc_lang('该邮箱【%s】已经被注册', $email));
                    } else {
                        $data[0]['email'] = $email;
                    }
                }
                if (isset($data['error'])) {
                    $error = $data;
                    (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error['msg'], $error['error']));
                    $data = $this->input->post('data', TRUE);
                    unset($data['uid']);
                    $input = $this->input->post('member', TRUE);
                } else {
                    $result = $this->member_model->edit($data[0], $data[1]);
                    $this->attachment_handle($this->uid, $this->db->dbprefix('member').'-'.$this->uid, $field, $this->member);
                    if ($result) {
                        // 完善资料积分处理
                        !$this->db
                            ->where('uid', $this->uid)
                            ->where('type', 0)
                            ->where('mark', 'complete')
                            ->count_all_results('member_scorelog') && $this->member_model->update_score(0, $this->uid, (int)$this->member_rule['complete_experience'], 'complete', fc_lang('完善资料'));
                        // 完善资料虚拟币处理
                        !$this->db
                            ->where('uid', $this->uid)
                            ->where('type', 1)
                            ->where('mark', 'complete')
                            ->count_all_results('member_scorelog') && $this->member_model->update_score(1, $this->uid, (int)$this->member_rule['complete_score'], 'complete', fc_lang('完善资料'));
                        }
                    $this->member_msg(fc_lang('操作成功，正在刷新...'), dr_member_url('account/index'), 1);
                }
            }
            $data['phone'] = $this->member['phone'];
        } else {
            $data = $this->member;
        }

        $this->template->assign(array(
            'data' => $data,
            'field' => $field,
            'input' => $input,
            'myfield' => $this->field_input($field, $data, FALSE, 'uid'),
            'regfield' => $MEMBER['setting']['regfield'],
            'result_error' => $error
        ));
        $this->template->display('account_index.html');
    }


    /**
     * 短信认证验证码发送
     */
    public function sendsms() {

        // 重复发送
        get_cookie('send_sms') && exit(dr_json(0, fc_lang('验证码已发送，请在2分钟之后再试')));

        // 是否已经认证过
        $this->member['ismobile'] && $this->member['phone'] && exit(dr_json(0, fc_lang('您已经认证过了')));

        // 安全字符替换
        $mobile = dr_safe_replace($this->input->get('phone'));
        (strlen($mobile) != 11 || !is_numeric($mobile)) && exit(dr_json(0, fc_lang('手机号码必须是11位的整数')));

        // 号码是否重复
        $this->db->where('uid<>', $this->uid)->where('phone', $mobile)->count_all_results('member') && exit(dr_json(0, fc_lang('该手机号码已经注册')));
        
        $code = dr_randcode();
        $result = $this->member_model->sendsms($mobile, fc_lang('尊敬的用户，您的本次验证码是：%s', $code));
        if ($result['status']) {
            // 发送成功
            $this->db->where('uid', $this->uid)->update('member', array('randcode' => $code));
            set_cookie('send_sms', 1, 120);
            exit(dr_json(1, fc_lang('验证码发送成功，请注意查收')));
        } else {
            // 发送失败
            exit(dr_json(0, $result['msg']));
        }
    }

    /**
     * OAuth解绑
     */
    public function jie() {

        $id = dr_safe_replace($this->input->get('id'));
        if ($this->get_cache('member', 'setting', 'regoauth')) {
            $this->msg(fc_lang('您的账号不支持解除绑定'));
        } elseif (!$this->member['username'] && !$this->member['password']) {
            $this->msg(fc_lang('操作成功，正在刷新...'));
        }

        $this->db->where('uid', $this->uid)->where('oauth', $id)->delete('member_oauth');

        // 解绑积分处理
        !$this->db
            ->where('uid', $this->uid)
            ->where('type', 0)
            ->where('mark', 'jie_'.$id)
            ->count_all_results('member_scorelog') && $this->member_model->update_score(0, $this->uid, (int)$this->member_rule['jie_experience'], 'jie_'.$id, 'OAuth账号解绑');
        
        // 解绑虚拟币处理
        !$this->db
            ->where('uid', $this->uid)
            ->where('type', 1)
            ->where('mark', 'jie_'.id)
            ->count_all_results('member_scorelog') && $this->member_model->update_score(1, $this->uid, (int)$this->member_rule['jie_score'], 'jie_'.$id, 'OAuth账号解绑');
        
        $this->msg(fc_lang('操作成功，正在刷新...'), dr_member_url('account/oauth'), 1, 3);
    }

    /**
     * OAuth绑定
     */
    public function bang() {

        $appid = dr_safe_replace($this->input->get('id'));
        $oauth = require WEBPATH.'config/oauth.php';
        $config	= $oauth[$appid];
        !$config && $this->msg(fc_lang('OAuth错误: 缺少OAuth参数'));

        $config['url'] = SITE_URL.'index.php?s=member&c=account&m=bang&id='.$appid; // 回调地址设置
        $this->load->library('OAuth2');

        // OAuth
        $code = $this->input->get('code', TRUE);
        $oauth = $this->oauth2->provider($appid, $config);

        if (!$code) { // 登录授权页
            try {
                $oauth->authorize();
            } catch (OAuth2_Exception $e) {
                $this->msg(fc_lang('OAuth授权错误').' - '.$e);
            }
        } else { // 回调返回数据
            try {
                $user = $oauth->get_user_info($oauth->access($code));
                if (is_array($user) && $user['oid']) {
                    if ($uid = $this->member_model->OAuth_bang($appid, $user)) {
                        $this->msg(fc_lang('抱歉！该授权已经被绑定过了，<a href="%s" target="_blank">看看Ta是谁？</a>', dr_space_url($uid)));
                    } else {
                        // 绑定积分处理
                        !$this->db
                            ->where('uid', $this->uid)
                            ->where('type', 0)
                            ->where('mark', 'bang_'.$appid)
                            ->count_all_results('member_scorelog') && $this->member_model->update_score(0, $this->uid, (int)$this->member_rule['bang_experience'], 'bang_'.$appid, 'OAuth账号绑定');
                        
                        // 绑定虚拟币处理
                        !$this->db
                            ->where('uid', $this->uid)
                            ->where('type', 1)
                            ->where('mark', 'bang_'.$appid)
                            ->count_all_results('member_scorelog') && $this->member_model->update_score(1, $this->uid, (int)$this->member_rule['bang_score'], 'bang_'.$appid, 'OAuth账号绑定');
                        $this->msg(fc_lang('绑定成功'), dr_member_url('account/oauth'), 1, 3);
                    }
                } else {
                    $this->msg(fc_lang('OAuth回调错误: 获取用户信息失败'));
                }
            } catch (OAuth2_Exception $e) {
                $this->msg(fc_lang('OAuth回调错误: 获取用户信息失败').' - '.$e);
            }
        }
    }

    /**
     * 登录记录
     */
    public function login() {
        $this->load->library('dip');
        $this->template->display('account_login.html');
    }

    /**
     * OAuth
     */
    public function oauth() {
        $this->template->assign(array(
            'list' => $this->member['oauth'],
        ));
        $this->template->display('account_oauth.html');
    }

    /**
     * 修改密码
     */
    public function password() {

        $error = 0;

        if (IS_POST) {

            $password = dr_safe_replace($this->input->post('password'));
            $password1 = dr_safe_replace($this->input->post('password1'));
            $password2 = dr_safe_replace($this->input->post('password2'));

            if (!$password1 || $password1 != $password2) {
                $error = fc_lang('两次密码输入不一致');
            } elseif ($password == $password2) {
                $error = fc_lang('不能与原密码相同');
            } elseif (md5(md5($password).$this->member['salt'].md5($password)) != $this->member['password']) {
                $error = fc_lang('当前密码不正确');
            } else {
                if (defined('UCSSO_API')) {
                    $rt = ucsso_edit_password($this->uid, $password1);
                    // 修改失败
                    if (!$rt['code']) {
                        $this->admin_msg(fc_lang($rt['msg']));
                    }
                } elseif ($this->get_cache('MEMBER', 'setting', 'ucenter')) {
                    $ucresult = uc_user_edit($this->member['username'], $password, $password1, $this->member['email']);
                    $ucresult == -1 && $error = fc_lang('旧密码不正确');
                }
            }

            if ($error === 0) {
                $this->db->where('uid', $this->uid)->update('member', array(
                    'password' => md5(md5($password1).$this->member['salt'].md5($password1))
                ));
                $this->hooks->call_hook('member_edit_password', array('member' => $this->member, 'password' => $password1));
                $this->member_msg(fc_lang('密码修改成功'), dr_member_url('account/password'), 1);
            }

            (IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error));
        }

        $this->template->assign(array(
            'result_error' => $error
        ));
        $this->template->display('account_password.html');
    }

    /**
     * 密码校验
     */
    public function cpassword() {
        $password = dr_safe_replace($this->input->post('password'));
        echo md5(md5($password).$this->member['salt'].md5($password)) == $this->member['password'] ? '' : fc_lang('旧密码不正确');
    }

    /**
     * 上传头像
     */
    public function avatar() {

        $ucenter = '';

        defined('UC_KEY')
        && $data = uc_get_user($this->member['username']) && list($ucenter, $username, $email) = $data;

        $this->template->assign(array(
            'ucenter' => $ucenter,
        ));
        $this->template->display('account_avatar.html');
    }

    /**
     *  上传头像处理
     *  传入头像压缩包，解压到指定文件夹后删除非图片文件
     */
    public function upload() {

        $post = file_get_contents('php://input');
        !$post && exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '环境php://input不支持') : 'php://input NULL');
        
        // 创建图片存储文件夹
        $dir = SYS_UPLOAD_PATH.'/member/'.$this->uid.'/';
        @dr_dir_delete($dir);
        !is_dir($dir) && dr_mkdirs($dir);

        // 移动端头像提交
        if (isset($_GET['iajax']) && $_GET['iajax'] && IS_MOBILE) {
            if ($_POST['tx']) {
                $file = str_replace(' ', '+', $_POST['tx']);
                if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $file, $result)){
                    $new_file = $dir.'0x0.'.$result[2];
                    if (!@file_put_contents($new_file, base64_decode(str_replace($result[1], '', $file)))) {
                        exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '目录权限不足或磁盘已满') : 'error3');
                    } else {
                        $this->load->library('image_lib');
                        $config['create_thumb'] = TRUE;
                        $config['thumb_marker'] = '';
                        $config['maintain_ratio'] = FALSE;
                        $config['source_image'] = $new_file;
                        foreach (array(30, 45, 90, 180) as $a) {
                            $config['width'] = $config['height'] = $a;
                            $config['new_image'] = $dir.$a.'x'.$a.'.'.$result[2];
                            $this->image_lib->initialize($config);
                            if (!$this->image_lib->resize()) {
                                exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', $this->image_lib->display_errors()) : $this->image_lib->display_errors());
                                break;
                            }
                        }
                        list($width, $height, $type, $attr) = getimagesize($dir.'45x45.'.$result[2]);
                        !$type && exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '图片字符串不规范') : 'error3');
                    }
                } else {
                    exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '图片字符串不规范') : 'error3');
                }
            } else {
                exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '图片不存在') : 'error1');
            }
        } else {

            $filename = $dir.'avatar.zip'; // 存储flashpost图片
            file_put_contents($filename, $post);

            // 解压缩文件
            $this->load->library('Pclzip');
            $this->pclzip->PclFile($filename);
            $content = $this->pclzip->listContent();
            if (!$content) {
                @unlink($filename);
                exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '文件已损坏') : 'The file has damaged');
            }
            // 验证文件
            foreach ($content as $t) {
                if (strpos($t['filename'], '..') !== FALSE ||
                    strpos($t['filename'], '/') !== FALSE ||
                    strpos($t['filename'], '.php') !== FALSE ||
                    strpos($t['stored_filename'], '..') !== FALSE ||
                    strpos($t['stored_filename'], '/') !== FALSE ||
                    strpos($t['stored_filename'], '.php') !== FALSE) {
                    @unlink($filename);
                    exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '非法名称的文件') : 'llegal name file');
                }
                if (substr(strrchr($t['stored_filename'], '.'), 1) != 'jpg') {
                    @unlink($filename);
                    exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '文件格式校验不正确') : 'The document format verification is not correct');
                }
            }

            // 解压文件
            if ($this->pclzip->extract(PCLZIP_OPT_PATH, $dir, PCLZIP_OPT_REPLACE_NEWER) == 0) {
                @dr_dir_delete($dir);
                exit($this->pclzip->zip(true));
            }
            @unlink($filename);

            (!is_file($dir.'45x45.jpg') || !is_file($dir.'90x90.jpg')) && exit(function_exists('iconv') ? iconv('UTF-8', 'GBK', '文件创建失败') : 'File creation failure');
        }
// 上传图片到服务器
        if (defined('UCSSO_API')) {
            $rt = ucsso_avatar($this->uid, file_get_contents($dir.'90x90.jpg'));
            !$rt['code'] && $this->_json(0, fc_lang('通信失败：%s', $rt['msg']));
        }

        // 上传头像积分处理
        !$this->db
            ->where('uid', $this->uid)
            ->where('type', 0)
            ->where('mark', 'avatar')
            ->count_all_results('member_scorelog') && $this->member_model->update_score(0, $this->uid, (int)$this->member_rule['avatar_experience'], 'avatar', fc_lang('上传头像'));
        

        // 上传头像虚拟币处理
        !$this->db
            ->where('uid', $this->uid)
            ->where('type', 1)
            ->where('mark', 'avatar')
            ->count_all_results('member_scorelog') && $this->member_model->update_score(1, $this->uid, (int)$this->member_rule['avatar_score'], 'avatar', fc_lang('上传头像'));
        
        // 更新头像
        $this->db->where('uid', $this->uid)->update('member', array('avatar' => $this->uid));

        exit('1');
    }

    /**
     * 会员组升级
     */
    public function upgrade() {

        $id = (int)$this->input->get('id');

        if ($id) {

            $group = $this->get_cache('member', 'group', $id);
            if (!$group) {
                $this->member_msg(fc_lang('会员组不存在'));
            } elseif (!$group['allowapply']) {
                $this->member_msg(fc_lang('该会员组不允许自助升级'));
            }

            if ($id == $this->member['groupid']) {
                // 表示续费
                $time = $this->member['overdue'];
                $renew = TRUE;
                $time > 2000000000 && $this->member_msg(fc_lang('当前会员组有效期为永久，因此无法重复升级'));
            } else {
                // 表示申请其他组
                $time = 0;
                $renew = FALSE;
                !$this->get_cache('member', 'group', $this->member['groupid'], 'allowapply_orther') && $this->member_msg(fc_lang('该会员组不允许申请其他组'));
            }

            if ($group['unit'] == 1) {
                // 虚拟币扣减
                $value = intval($group['price']);
                $this->member['score'] - $value < 0 && $this->member_msg(fc_lang(SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE.'', $value, $this->member['score']));
                $this->member_model->update_score(1, $this->uid, -$value, '', '会员组升级消费：%s'.$group['name']);
            } else {
                // 人民币扣减
                $this->member['money'] - $group['price'] < 0 && $this->member_msg(fc_lang(SITE_MONEY.'不足！本次需要%s'.SITE_MONEY.'，当前余额%s'.SITE_MONEY.'', $group['price'], $this->member['money']));
                $this->load->model('pay_model');
                $this->pay_model->add($this->uid, -$group['price'], '会员组升级消费：%s'.$group['name']);
            }

            $time = $this->member_model->upgrade($this->uid, $id, $group['limit'], $time);
            $time = $time > 2000000000 ? fc_lang('永久') : dr_date($time);
            $subject = $renew ? fc_lang('会员组续费成功') : fc_lang('会员组升级成功');
            $message = dr_lang($renew ? @file_get_contents(WEBPATH.'cache/email/xufei.html') : @file_get_contents(WEBPATH.'cache/email/group.html'), $this->member['name'] ? $this->member['name'] : $this->member['username'], $group['name'], $time);

            // 邮件提醒
            $this->sendmail_queue($this->member['email'], $subject, $message);
            $this->member_msg(fc_lang('操作成功，有效期至：%s', $time), dr_member_url('account/permission'), 1, 3);

        } else {

            $data = array();
            $group = $this->get_cache('member', 'group');
            if ($group) {
                if (!$group[$this->member['groupid']]['allowapply_orther']) {
                    // 不能申请其他组
                    $data = array(
                        $this->member['groupid'] => $group[$this->member['groupid']]
                    );
                } else {
                    foreach ($group as $t) {
                        $t['allowapply'] && $data[$t['id']] = $t;
                    }
                }
            }

            $this->template->assign(array(
                'group' => $data,
            ));
            $this->template->display('account_upgrade.html');
        }
    }

    /**
     * 会员组权限
     */
    public function permission() {

        $page = (int)$this->input->get('page');
        $groupid = (int)$this->input->get('groupid');
        $levelid = (int)$this->input->get('levelid');
        $groupid = $groupid ? $groupid : $this->member['groupid'];

        $group = $this->get_cache('member', 'group', $groupid);
        !$group && $this->member_msg(fc_lang('会员组不存在'));

        $levelid = $groupid != $this->member['groupid'] ? ($levelid ? $levelid : array_rand($group['level'])) : ($levelid ? $levelid : $this->member['levelid']);
        
        $content = NULL;
        $category = array(0 => fc_lang('会员'), 1 => fc_lang('空间'));
        if (!MEMBER_OPEN_SPACE) {
            unset($category[1]);
        }
        $markrule = $groupid < 3 ? $groupid : ($groupid.'_'.$levelid);

        if ($page == 0) {
            // 会员的基本权限表格
            $rule = $this->get_cache('member', 'setting', 'permission', $markrule);
            $content = '<table class="dr_table table" width="100%" border="0">';
            $content.= '  <tr><td width="200" align="right">'.fc_lang('每日登录增加%s', SITE_EXPERIENCE).'：&nbsp;</td><td width="100">&nbsp;'.intval($rule['login_experience']).'</td>';
            $content.= '  <td width="200" align="right">'.fc_lang('每日登录增加%s', SITE_SCORE).'：&nbsp;</td><td>&nbsp;'.intval($rule['login_score']).'</td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('上传头像增加%s', SITE_EXPERIENCE).'：&nbsp;</td><td>&nbsp;'.intval($rule['avatar_experience']).'</td>';
            $content.= '  <td align="right">'.fc_lang('上传头像增加%s', SITE_SCORE).'：&nbsp;</td><td>&nbsp;'.intval($rule['avatar_score']).'</td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('完善资料增加%s', SITE_EXPERIENCE).'：&nbsp;</td><td>&nbsp;'.intval($rule['complete_experience']).'</td>';
            $content.= '  <td align="right">'.fc_lang('完善资料增加%s', SITE_SCORE).'：&nbsp;</td><td>&nbsp;'.intval($rule['complete_score']).'</td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('绑定OAuth增减%s', SITE_EXPERIENCE).'：&nbsp;</td><td>&nbsp;'.intval($rule['bang_experience']).'</td>';
            $content.= '  <td align="right">'.fc_lang('绑定OAuth增减%s', SITE_SCORE).'：&nbsp;</td><td>&nbsp;'.intval($rule['bang_score']).'</td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('解绑OAuth增减%s', SITE_EXPERIENCE).'：&nbsp;</td><td>&nbsp;'.intval($rule['jie_experience']).'</td>';
            $content.= '  <td align="right">'.fc_lang('解绑OAuth增减%s', SITE_SCORE).'：&nbsp;</td><td>&nbsp;'.intval($rule['jie_score']).'</td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('更新文档时间增减%s', SITE_EXPERIENCE).'：&nbsp;</td><td>&nbsp;'.intval($rule['update_experience']).'</td>';
            $content.= '  <td align="right">'.fc_lang('更新文档时间增减%s', SITE_SCORE).'：&nbsp;</td><td>&nbsp;'.intval($rule['update_score']).'</td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('下载附件增减%s', SITE_EXPERIENCE).'：&nbsp;</td><td>&nbsp;'.intval($rule['download_experience']).'</td>';
            $content.= '  <td align="right">'.fc_lang('下载附件增减%s', SITE_SCORE).'：&nbsp;</td><td>&nbsp;'.intval($rule['download_score']).'</td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('是否允许上传附件').'：&nbsp;</td><td>&nbsp;<img src="'.THEME_PATH.'admin/images/'.(int)$rule['is_upload'].'.gif"></td>';
            $content.= '  <td align="right">'.fc_lang('是否允许下载附件').'：&nbsp;</td><td>&nbsp;<img src="'.THEME_PATH.'admin/images/'.(int)$rule['is_download'].'.gif"></td></tr>';
            $content.= '  <tr><td align="right">'.fc_lang('附件总空间').'：&nbsp;</td><td>&nbsp;'.($rule['attachsize'] ? $rule['attachsize'].'MB' : fc_lang('不限')).'</td></tr>';
            $content.= '</table>';
        } elseif ($page == 1 && MEMBER_OPEN_SPACE) {
            $setting = $this->get_cache('member', 'setting');
            // 会员空间表格
            $content = '
			<table class="dr_table table" width="100%" border="0">
			<tr>
				<td width="120" align="right">'.fc_lang('是否审核空间').'：&nbsp;</td>
				<td><img src="'.THEME_PATH.'admin/images/'.(int)$setting['verifyspace'].'.gif"></td>
				<td width="120" align="right">'.fc_lang('允许空间使用').'：&nbsp;</td>
				<td><img src="'.THEME_PATH.'admin/images/'.(int)$this->get_cache('member', 'group', $this->member['groupid'], 'allowspace').'.gif"></td>
				<td width="120" align="right">'.fc_lang('使用二级域名').'：&nbsp;</td>
				<td><img src="'.THEME_PATH.'admin/images/'.(int)$this->get_cache('member', 'group', $this->member['groupid'], 'spacedomain').'.gif"></td>
			</tr>
			</table>
			<table class="dr_table table" width="100%" border="0">
			<tr>
				<td>'.fc_lang('模型').'</td>
				<td align="center">'.fc_lang('使用').'</td>
				<td align="center">'.fc_lang('审核').'</td>
				<td align="center">'.fc_lang('每日').'</td>
				<td align="center">'.fc_lang('总数').'</td>
				<td align="center">'.fc_lang('发布增减%s', SITE_EXPERIENCE).'</td>
				<td align="center">'.fc_lang('发布增减%s', SITE_SCORE).'</td>
			</tr>';
            $model = $this->get_cache('space-model');
            foreach ($model as $t) {
                $rule = $t['setting'][$markrule];
                $content.= '<tr>';
                $content.= '<td>'.$t['name'].'</td>';
                $content.= '<td align="center"><img src="'.THEME_PATH.'admin/images/'.(int)$rule['use'].'.gif"></td>';
                $content.= '<td align="center"><img src="'.THEME_PATH.'admin/images/'.($rule['verify'] ? 1 : 0).'.gif"></td>';
                $content.= '<td align="center">'.($rule['postnum'] ? $rule['postnum'] : fc_lang('不限')).'</td>';
                $content.= '<td align="center">'.($rule['postcount'] ? $rule['postcount'] : fc_lang('不限')).'</td>';
                $content.= '<td align="center">'.(int)$rule['experience'].'</td>';
                $content.= '<td align="center">'.(int)$rule['score'].'</td>';
                $content.= '</tr>';

            }
            $content.= '</table>';
        }

        // 检测可管理的模块
        $module = $this->get_cache('module', SITE_ID);
        if ($module) {
            foreach ($module as $dir) {
                $mod = $this->get_cache('module-'.SITE_ID.'-'.$dir);
                $key = count($category);
                $cat = $mod['category'];
                if (!$cat || !$mod['name']) {
                    continue;
                }
                $category[$key] = $mod['name'];
                // 权限表格
                if ($key == $page) {
                    $content = '<table class="dr_table table" width="100%" border="0">';
                    $content.= '  <tr>
						<td>'.fc_lang('栏目').'</td>
						<td align="center">'.fc_lang('访问').'</td>
						<td align="center">'.fc_lang('添加').'</td>
						<td align="center">'.fc_lang('修改').'</td>
						<td align="center">'.fc_lang('删除').'</td>
						<td align="center">'.fc_lang('审核').'</td>
						<td align="center">'.fc_lang('每日').'</td>
						<td align="center">'.fc_lang('总数').'</td>
						<td align="center">'.SITE_EXPERIENCE.'</td>
						<td align="center">'.SITE_SCORE.'</td>
					  </tr>';
                    foreach ($cat as $c) {
                        if (!$c['child']) {
                            $rule = $c['permission'][$markrule];
                            $content.= '<tr>';
                            $content.= '<td>'.$c['name'].'('.$c['id'].')</td>';
                            $content.= '<td align="center"><img src="'.THEME_PATH.'admin/images/'.($rule['show'] ? 0 : 1).'.gif"></td>';
                            $content.= '<td align="center"><img src="'.THEME_PATH.'admin/images/'.(int)$rule['add'].'.gif"></td>';
                            $content.= '<td align="center"><img src="'.THEME_PATH.'admin/images/'.(int)$rule['edit'].'.gif"></td>';
                            $content.= '<td align="center"><img src="'.THEME_PATH.'admin/images/'.(int)$rule['del'].'.gif"></td>';
                            $content.= '<td align="center"><img src="'.THEME_PATH.'admin/images/'.($rule['verify'] ? 1 : 0).'.gif"></td>';
                            $content.= '<td align="center">'.($rule['postnum'] ? $rule['postnum'] : fc_lang('不限')).'</td>';
                            $content.= '<td align="center">'.($rule['postcount'] ? $rule['postcount'] : fc_lang('不限')).'</td>';

                            $content.= '<td  align="center">'.intval($rule['experience']).'</td>';
                            $content.= '<td align="center">'.intval($rule['score']).'</td>';
                            $content.= '</tr>';
                        }

                    }
                    $content.= '</table>';
                }
            }
        }

        // 检测可管理的应用

        $this->template->assign(array(
            'page' => $page,
            'group' => $group,
            'levelid' => $levelid,
            'groupid' => $groupid,
            'content' => $content,
            'category' => $category,
        ));
        $this->template->display('account_permission.html');
    }

    /**
     * 附件管理
     */
    public function attachment() {

        $ext = dr_safe_replace($this->input->get('ext'));
        $table = $this->input->get('module');
        $this->load->model('attachment_model');

        $page = max((int)$this->input->get('page'), 1);
        
        // 检测可管理的模块
        $module = array();
        $modules = $this->get_cache('module', SITE_ID);
        if ($modules) {
            foreach ($modules as $dir) {
                $mod = $this->get_cache('module-'.SITE_ID.'-'.$dir);
                $this->_module_post_catid($mod, $this->markrule) && $module[$dir] = $mod['name'];
            }
        }
        
        // 查询结果
        list($total, $data) = $this->attachment_model->limit($this->uid, $page, $this->pagesize, $ext, $table);
        
        $acount = $this->get_cache('member', 'setting', 'permission', $this->markrule, 'attachsize');
        $acount = $acount ? $acount : 1024000;
        $ucount = $this->db->select('sum(`filesize`) as total')->where('uid', (int)$this->uid)->limit(1)->get('attachment')->row_array();
        $ucount = (int)$ucount['total'];
        $acount = $acount * 1024 * 1024;
        $scount = max($acount - $ucount, 0);
        
        $this->template->assign(array(
            'ext' => $ext,
            'list' => $data,
            'table' => $table,
            'module' => $module,
            'acount' => $acount,
            'ucount' => $ucount,
            'scount' => $scount,
            'pages'	=> $this->get_member_pagination(dr_member_url($this->router->class.'/'.$this->router->method, array('ext' => $ext)), $total),
            'page_total' => $total,
        ));
        $this->template->display('account_attachment_list.html');
    }

    // 删除附件
    public function del_attach() {
        $id = (int)$this->input->post('id');
        $this->load->model('attachment_model');
        $this->attachment_model->delete($this->uid, '', $id) ? exit(dr_json(1, fc_lang('操作成功，正在刷新...'))) :  exit(dr_json(0, 'Error'));
    }
}