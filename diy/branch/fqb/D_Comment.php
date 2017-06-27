<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class D_Comment extends M_Controller {

    public $uri; //
    public $curl;
    public $cdata;
    public $rname;
    public $cconfig;
    public $permission;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $auth = $this->input->get('auth');
        if ($auth) {
            if ($auth != md5(SYS_KEY)) {
                // 授权认证码不正确
                echo $this->callback_json(array(
                    'msg' => '授权认证码不正确',
                    'code' => 0
                ));exit;
            }
            define('SELECT_API_AUTH', 1);
        }
        $this->load->model('comment_model');
    }

    // 设置空间操作评论
    public function space() {

        $this->rname = 'comment-space';
        $this->comment_model->space();

        $id = (int)$this->input->get('id');
        $data = $this->comment_model->get_cdata($id);
        !$data && $id = 0;

        $this->uri = 'space/admin/comment/';
        $this->curl = '/index.php?s=space&c=comment&id='.$id;
        $this->cdata = array(
            'cid' => $id,
            'url' => $data['url'],
            'uid' => $data['uid'],
            'catid' => $data['catid'],
            'title' => $data['title'],
        );
        $this->cconfig = $this->get_cache('comment', $this->rname);
        $this->permission = $this->cconfig['value']['permission'][$this->markrule];
        unset($this->cconfig['value']['permission'][$this->markrule]);
    }

    // 设置空间模型操作评论
    public function model($mid) {
    }

    // 设置模块操作评论
    public function module($dir) {

        $this->rname = 'comment-module-'.$dir;
        $this->comment_model->module($dir);

        $id = (int)$this->input->get('id');
        $data = $this->comment_model->get_cdata($id);
        !$data && $id = 0;

        $this->uri = $dir.'/admin/comment/';
        $this->curl = '/index.php?s='.$dir.'&c=comment&id='.$id;
        $this->cdata = array(
            'cid' => $id,
            'dir' => $dir,
            'url' => $data['url'],
            'uid' => $data['uid'],
            'catid' => $data['catid'],
            'title' => $data['title'],
        );
        $this->cconfig = $this->get_cache('comment', $this->rname);
        $this->permission = $this->cconfig['value']['permission'][$this->markrule];
        unset($this->cconfig['value']['permission'][$this->markrule]);
    }

    // 设置模块扩展操作评论
    public function extend($dir) {

        $this->rname = 'comment-module-'.$dir;
        $this->comment_model->extend($dir);

        $id = (int)$this->input->get('id');
        $data = $this->comment_model->get_cdata($id);
        !$data && $id = 0;

        $this->uri = $dir.'/admin/ecomment/';
        $this->curl = '/index.php?s='.$dir.'&c=ecomment&id='.$id;
        $this->cdata = array(
            'cid' => $id,
            'dir' => $dir,
            'url' => $data['url'],
            'uid' => $data['uid'],
            'catid' => $data['catid'],
            'title' => $data['title'],
        );
        $this->cconfig = $this->get_cache('comment', $this->rname);
        $this->permission = $this->cconfig['value']['permission'][$this->markrule];
        unset($this->cconfig['value']['permission'][$this->markrule]);
    }

    // 评论列表
    public function index() {

        $emotion = array();
        if ($fp = @opendir(WEBPATH.'api/emotions/')) {
            while (FALSE !== ($file = readdir($fp))) {
                $info = pathinfo($file);
                @in_array($info['extension'], array('gif', 'png', 'jpg')) && $emotion[$info['filename']] = SITE_URL.'api/emotions/'.$file;
            }
        }

        $type = (int)$this->input->get('type');
        $order = 'inputtime desc';
        switch ($type) {
            case 1:
                $order = 'inputtime asc';
                break;
            case 2:
                $order = 'support asc';
                break;
            case 3:
                $order = 'avgsort desc';
                break;
            default:
                $_GET['order'] && $order = strtolower(dr_get_order_string($_GET['order'], $order));
                break;
        }

        $page = max(1, (int)$this->input->get('page'));
        list($table, $comment) = $this->comment_model->get_table($this->cdata['cid'], 1);

        // 判断字段是否可用
        $temp = trim(str_replace(array(' asc', ' desc'), '', $order));
        $field = $this->get_table_field($table);
        $order = isset($field[$temp]) ? $order : 'inputtime desc';

        $this->cconfig['value']['pagesize'] = max(2, (int)$this->cconfig['value']['pagesize']);
        $data = $this->comment_model
                    ->mydb
                    ->where('cid', $this->cdata['cid'])
                    ->where('reply', 0)
                    ->where('status', 1)
                    ->limit($this->cconfig['value']['pagesize'], $this->cconfig['value']['pagesize'] * ($page - 1))
                    ->order_by($order)
                    ->get($table)->result_array();
        if ($data) {
            foreach ($data as $i => $t) {
                $data[$i]['rlist'] = $t['in_reply'] ? $this->comment_model->mydb->where('cid', $this->cdata['cid'])->where('reply', $t['id'])->where('status', 1)->order_by('inputtime desc')->get($table)->result_array() : array();
            }
        }

        if (defined('SELECT_API_AUTH')) {
            $function = $this->input->get('function');
            if ($function) {
                if (!function_exists($function)) {
                    $data = array('msg' => fc_lang('自定义函数'.$function.'不存在'), 'code' => 0);
                } else {
                    $data = $function($data);
                }
            }
            echo $this->callback_json($data);exit;
        }

        // 评论数量，非回复
        $total = $this->comment_model->mydb->where('cid', $this->cdata['cid'])->where('reply', 0)->where('status', 1)->count_all_results($table);

        $this->template->assign(array(
            'js' => $this->input->get('js') ? $this->input->get('js') : 'dr_null_call',
            'use' => $this->cconfig['value']['use'],
            'type' => $type,
            'page' => $page,
            'list' => $data,
            'html' => empty($_GET['r']) ? 1 : 0,
            'curl' => $this->curl.'&oid='.(int)$this->input->get('oid').'&gid='.(int)$this->input->get('gid'),
            'code' => isset($this->permission['code']) && $this->permission['code'],
            'cdata' => $this->cdata,
            'catid' => $this->cdata['catid'],
            'pages' => $this->get_pages('javascript:'.$this->input->get('js').'('.$type.', {page})', $total),
            'review' => $this->cconfig['value']['review'],
            'myfield' => $this->new_field_input($this->cconfig['field'], NULL, 0, '', $this->cconfig['value']['format']),
            'emotion' => $emotion,
            'comment' => $comment,
            'commnets' => $comment['comments'],
            'is_reply' => $this->cconfig['value']['reply'],
            'is_review' => $this->cconfig['value']['review']['use'],
            'meta_title' => $this->cdata['title'],
        ));

        if (empty($_GET['r'])) {
            !$this->cdata['cid'] && $this->msg(fc_lang('评论主题不存在'));
            $this->template->display('comment.html');
            exit;
        }

        ob_start();
        !$this->cdata['cid'] && exit(fc_lang('评论主题不存在'));
        $this->template->display('comment.html');
        $html = ob_get_contents();
        ob_clean();
        $data = $this->callback_json(array('html' => $html));
        echo $this->input->get('callback', TRUE).'('.$data.')';
    }

    // 发布评论
    public function add() {

        $buy = array();
        $rid =(int)$this->input->get('rid');
        $oid =(int)$this->input->get('oid');
        $name = md5($this->uid.$this->curl.'sb');
        $table = $this->comment_model->get_table($this->cdata['cid']); // 评论附表

        if (!$this->cconfig['value']['use']) {
            // 判断发布权限
            exit(dr_json(0, fc_lang('系统关闭了评论功能')));
        } else if ($this->cconfig['value']['my'] && $this->cdata['uid'] == $this->uid) {
            // 判断不能对自己评论
            exit(dr_json(0, fc_lang('系统禁止对自己评论')));
        } else if ($rid) {
            // 判断是否回复权限
            $row = $this->comment_model->mydb->where('cid', $this->cdata['cid'])->where('id', $rid)->get($table)->row_array();
            if (!$row) {
                exit(dr_json(0, fc_lang('您回复的评论主体不存在')));
            } elseif (!$this->cconfig['value']['reply']) {
                exit(dr_json(0, fc_lang('系统禁止回复功能')));
            } elseif ($this->cconfig['value']['reply'] == 2) {
                // # 仅自己
                if ($this->member['uid'] == $row['uid'] && $row['uid'] == $this->cdata['uid']) {
                    // 自己的评论,或者回复文章的作者
                } elseif ($this->member['adminid']) {
                    // 管理员可以回复
                } else {
                    exit(dr_json(0, fc_lang('您无权限回复')));
                }
            }
        } else if (isset($this->permission['disabled']) && $this->permission['disabled']) {
            // 角色发布权限
            exit(dr_json(0, fc_lang('您无权限评论')));
        } else if (isset($this->permission['time']) && $this->permission['time'] && $this->session->userdata($name)) {
            // 发布间隔判断
            exit(dr_json(0, fc_lang('您动作太快了！')));
        } else if ($this->cconfig['value']['buy'] && is_dir(FCPATH.'module/order/')) {
            if (!$oid) {
                exit(dr_json(0, fc_lang('缺少订单参数oid')));
            }
            // 如果来自商铺store的评论，首先要判断该订单的对应模块
            if (APP_DIR == 'space') {
                // 针对商铺的评论
                $buy = $this->db
                            ->select('id,buy_uid,order_comment,sell_uid,sn')
                            ->where('id', $oid)
                            ->get(SITE_ID.'_order')
                            ->row_array();
                if ($buy['buy_uid'] == $this->uid) {
                    // 买家
                    if (!$buy['order_comment']) {
                        exit(dr_json(0, fc_lang('请先给商品进行评论')));
                    } else if ($buy['order_comment'] > 1) {
                        exit(dr_json(0, fc_lang('您已经评论过了，不允许重复评论')));
                    }
                } else {
                    exit(dr_json(0, fc_lang('您无权限操作')));
                }
            } else {
                $gid = (int)$this->input->get('gid');
                if (!$gid) {
                    exit(dr_json(0, fc_lang('缺少订单商品参数gid')));
                }
                // 查询到我购买的商品
                $buy = $this->db
                            ->where('uid', $this->uid)
                            ->where('oid', $oid)
                            ->where('gid', $gid)
                            ->where('cid', $this->cdata['cid'])
                            ->get(SITE_ID.'_order_buy')
                            ->row_array();
                if (!$buy) {
                    // 购买之后才允许评论
                    exit(dr_json(0, fc_lang('您还没有购买商品，不能评论')));
                } elseif ($buy['comment']) {
                    exit(dr_json(0, fc_lang('您已经评论过了，不允许重复评论')));
                }
            }
        } else if ($this->cconfig['value']['num']) {
            // 只允许评论一次
            if ($this->comment_model->mydb->where('cid', $this->cdata['cid'])->where('uid', $this->uid)->count_all_results($this->comment_model->prefix.'_comment_my')) {
                exit(dr_json(0, fc_lang('您已经评论过了，请勿再次评论')));
            } elseif ($this->comment_model->mydb->where('cid', $this->cdata['cid'])->where('uid', $this->uid)->count_all_results($table)) {
                exit(dr_json(0, fc_lang('您已经评论过了，请勿再次评论')));
            }
        }

        // 评论发布前的钩子
        $this->hooks->call_hook('commnet_add_before', array());

        if (IS_POST) {
            // 验证码
            isset($this->permission['code']) && $this->permission['code'] && !$this->check_captcha('code') && exit(dr_json(0, fc_lang('验证码不正确')));
            // 自定义字段
            $my = array();
            if ($this->cconfig['field']) {
                $my = $this->validate_filter($this->cconfig['field']);
                isset($my['error']) && exit(dr_json(0, $my['msg']));
            }
            // 开启点评功能时，判断各项点评数，回复不做点评
            $review = array();
            if (!$rid && $this->cconfig['value']['review']['use'] && $this->cconfig['value']['review']['option']) {
                foreach ($this->cconfig['value']['review']['option'] as $i => $t) {
                    if ($t['use']) {
                        $review[$i] = (int)$_POST['review'][$i];
                        !$review[$i] && exit(dr_json(0, fc_lang('点评选项%s未评分', $t['name'])));
                    }
                }
            }
            $this->cdata['rid'] = $rid;
            $this->cdata['review'] = $review;
            $this->cdata['content'] = $this->_safe_replace($this->input->post('content'));
            $this->cdata['score'] = (int)$this->permission['experience'];
            $this->cdata['experience'] = (int)$this->permission['experience'];
            empty($this->cdata['content']) && exit(dr_json(0, fc_lang('请填写评论内容')));
            // 需要审核评论
            $this->cdata['verify'] = $this->member['adminid'] ? 0 : $this->cconfig['value']['verify'];
            // 提交入库
            $id = $this->comment_model->post($this->uid, $this->cdata, $my);
            !$id && exit(dr_json(0, '评论失败，数据异常'));
            // 评论间隔
            isset($this->permission['time']) && $this->permission['time'] && $this->session->set_tempdata($name, 1, $this->permission['time']);
            // 操作成功处理附件
            if ($this->uid) {
                $this->cconfig['field']['content'] = array(
                    'ismain' => 1,
                    'fieldtype' => 'Ueditor',
                    'fieldname' => 'content',
                    'setting' => array(
                        'option' => array(
                            'mode' => 1,
                            'height' => 300,
                            'width' => '100%'
                        )
                    )
                ); // 将content当做ueditor附件处理
                $this->data[1]['content'] = $this->cdata['content']; // 伪装content字段
                $this->attachment_handle(
                    $this->uid,
                    $this->comment_model->get_table($this->cdata['cid']).'-'.$id,
                    $this->cconfig['field'],
                    $my
                );
            }
            // 评论发布后的钩子
            $this->hooks->call_hook('commnet_add_after', array(
                'id' => $id,
                'my' => $my,
                'cdata' => $this->cdata,
            ));
            // 购买后的评论
            if ($buy) {
                if (APP_DIR == 'space') {
                    if ($buy['buy_uid'] == $this->uid) {
                        // 买家
                        $this->db->where('id', (int)$buy['id'])->update(SITE_ID.'_order', array(
                            'order_comment' => $id,
                        ));
                        // 通知卖家
                        $this->member_model->add_notice($buy['sell_uid'], 3, fc_lang('买家已评论，订单号：%s', '<a href="'.dr_member_url('order/sell/comment', array('id' => $buy['id'])).'" target="_blank">'.$buy['sn'].'</a>'));
                    }
                    //
                } else {
                    $this->db->where('id', (int)$buy['id'])->update(SITE_ID.'_order_buy', array(
                        'comment' => $id,
                    ));
                    if ($this->db->where('oid', $oid)->where('comment>0')->count_all_results(SITE_ID.'_order_buy') == $this->db->where('oid', $oid)->count_all_results(SITE_ID.'_order_goods')) {
                        // 表示所有商品都评论完了
                        $this->db->where('id', $oid)->update(SITE_ID.'_order', array(
                            'order_comment' => -1,
                        ));
                    }

                }

            }

            // 提交评论之后的动作
            $this->cdata['id'] = $id;
            isset($my[1]) && count($my[1]) && $this->cdata = array_merge($this->cdata['id'], $my[1]);
            $this->_post_commnet($this->cdata);

            if ($this->cdata['verify']) {
                // 需要审核
                exit(dr_json(1, fc_lang('评论成功，需要管理员审核之后才能显示')));
            } else {
                // 成功
                // 增加经验值
                $this->permission['experience'] && $this->member_model->update_score(0, $this->uid, abs($this->permission['experience']), '', '评论');
                // 增加虚拟币
                $this->permission['score'] && $this->member_model->update_score(1, $this->uid, $this->permission['score'], '', '评论');
                exit(dr_json(1, fc_lang('评论成功')));
            }
        } else {
            exit(dr_json(0, 'POST数据异常'));
        }
    }

    // 基本操作
    public function op() {

        !$this->cconfig['value']['use'] && exit(dr_json(0, fc_lang('系统关闭了评论功能')));

        $op = $this->input->get('t');
        $id = (int)$this->input->get('rid');
        $name = $op.$id.$this->uid; // 验证识别
        $index = $this->comment_model
                        ->mydb
                        ->where('cid', $this->cdata['cid'])
                        ->get($this->comment_model->prefix.'_comment_index')
                        ->row_array();
        !$index && exit(dr_json(0, '数据异常'));

        $table = $this->comment_model->prefix.'_comment_data_'.intval($index['tableid']);
        if ($op == 'delete') {
            !$this->member['adminid'] && exit(dr_json(0, '无权限'));
            $this->comment_model->delete($id, $this->cdata['cid'], $index);
            exit(dr_json(1, 'ok'));

        }

        // 验证操作间隔
        $this->session->userdata($name) && exit(dr_json(0, fc_lang('亲，您动作太快了！')));
        $data = $this->comment_model->mydb->where('id', $id)->get($table)->row_array();
        !$data && exit(dr_json(0, '数据异常'));

        switch ($op) {
            case 'zc':
                $num = (int)$data['support'] + 1;
                $this->comment_model->mydb->where('id', $id)->set('support', $num)->update($table);
                $this->comment_model->mydb->where('id', $index['id'])->set('support', 'support+1', false)->update($this->comment_model->prefix.'_comment_index');
                $this->session->set_tempdata($name, 1, 3600);
                exit(dr_json(1, $num));
                break;
            case 'fd':
                $num = (int)$data['oppose'] + 1;
                $this->comment_model->mydb->where('id', $id)->set('oppose', $num)->update($table);
                $this->comment_model->mydb->where('id', $index['id'])->set('oppose', 'oppose+1', false)->update($this->comment_model->prefix.'_comment_index');
                $this->session->set_tempdata($name, 1, 3600);
                exit(dr_json(1, $num));
                break;
            case 'delete':
                break;
        }

        exit(dr_json(0, '未定义'));
    }

    /**
     * 分页
     *
     * @param
     * @return
     */
    private function get_pages($url, $total) {
        $this->load->library('pagination');
        $config['base_url'] = $url;
        $config['per_page'] = $this->cconfig['value']['pagesize'];
        $config['next_link'] = '>';
        $config['prev_link'] = '<';
        $config['last_link'] = '>|';
        $config['first_link'] = '|<';
        $config['total_rows'] = $total;
        $config['cur_tag_open'] = '<a class="ds-current">';
        $config['cur_tag_close'] = '</a>';
        $config['use_page_numbers'] = TRUE;
        $config['query_string_segment'] = 'page';
        $this->pagination->initialize($config);
        return $this->pagination->dr_links();
    }

    // 提交评论之后，方便二次开发和重写
    public function _post_commnet($data) {

    }

    // 格式化评论内容，方便二次开发和重写
    public function _safe_replace($data) {
        return dr_safe_replace($data);
    }

}
