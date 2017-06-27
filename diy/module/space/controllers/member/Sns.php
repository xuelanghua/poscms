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
	
class Sns extends M_Controller {

    public $callback;
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('sns_model');
        $this->callback = isset($_GET['callback']) ? $this->input->get('callback', TRUE) : '';

        if (!$this->uid) {
            // 会员未登录验证
            if (!$this->callback) {
                if (IS_AJAX || IS_API_AUTH) {
                    exit(fc_lang('会话超时，请重新登录'));
                }
                $this->member_msg(
                    fc_lang('会话超时，请重新登录').$this->member_model->logout(),
                    dr_member_url('login/index', array('backurl' => urlencode(dr_now_url())))
                );
            } else {
                exit($this->callback.'('.json_encode(array('html' => fc_lang('会话超时，请重新登录'))).')');
            }
        }
    }

    // 话题
    public function topic() {
        $this->index((int)$this->input->get('id'));
    }

    // 配置
    public function config() {

        if (IS_POST) {
            $save = 1;
            $post = $this->input->post('data');
            $post['show_all'] = (int)$post['show_all'];
            $post['show_fans'] = (int)$post['show_fans'];
            $post['show_follow'] = (int)$post['show_follow'];
            $data = $this->sns_model->config($this->uid, $post);
        } else {
            $save = 0;
            $data = $this->sns_model->config($this->uid);
        }

        $this->template->assign(array(
            'save' => $save,
            'data' => $data,
        ));
        $this->template->display('sns_config.html');
    }

	/**
     * 我的动态
     */
    public function index($topic = 0) {

        $uid = (int)$this->input->get('uid');
        $more = (int)$this->input->get('more');
        $page = max((int)$this->input->get('page'), 1);
        $type = $topic ? 5 : (int)$this->input->get('type');

        // 表情符号
        $this->load->helper('directory');
        $this->template->assign('emotion', directory_map(WEBPATH.'api/emotions/', 1));

        // 我收藏的
        $temp = $this->db->where('uid', $this->uid)->get('sns_feed_favorite')->result_array();
        $favorite = array();
        if ($temp) {
            foreach ($temp as $t) {
                $favorite[] = $t['fid'];
            }
            unset($temp);
        }
        $this->template->assign('favorite', $favorite);

        // 数据查询
        if ($type == 0) {
            // 我关注的
            $this->db->where('uid IN (select uid from '.$this->db->dbprefix('sns_follow').' where fid='.$this->uid.')');
            $this->db->where('uid NOT IN (select uid from '.$this->db->dbprefix('sns_config').' where `show_fans`=0)');
            $this->db->or_where('uid', $this->uid);
        } elseif ($type == 1) {
            // 我赞过的
            $this->db->where('id IN (select fid from '.$this->db->dbprefix('sns_feed_digg').' where uid='.$this->uid.')');
        } elseif ($type == 2) {
            // 我收藏的
            $this->db->where('id IN (select fid from '.$this->db->dbprefix('sns_feed_favorite').' where uid='.$this->uid.')');
        } elseif ($topic) {
            // 话题
            $topic = $this->db->where('id', $topic)->get('sns_topic')->row_array();
            if (!$topic) {
                $this->member_msg(fc_lang('此话题不存在'));
            }
            $this->template->assign('topic', $topic);
            $this->template->assign('meta_name', fc_lang('话题#%s#', $topic['name']));
            $this->db->where('id IN (select fid from '.$this->db->dbprefix('sns_topic_index').' where tid='.$topic['id'].')');
        } else {
            // 全站
            if (!$this->member['adminid']) {
                $this->db->where('uid NOT IN (select uid from '.$this->db->dbprefix('sns_config').' where `show_all`=0)');
            }
        }

        $this->db->order_by('inputtime desc');


        // 加载模板文件
        if ($more == 0) {
            $this->load->model('attachment_model');
            $data = $this->db->limit($page ? $page * $this->pagesize : $this->pagesize)->get('sns_feed')->result_array();
            $this->template->assign(array(
                'type' => $type,
                'list' => $data,
                'page' => max(2, $page + 1),
                'group' => $this->db
                                 ->where('uid', $this->uid)
                                 ->order_by('ctime desc')
                                 ->get('sns_follow_group')
                                 ->result_array(),
                'notused' => $this->attachment_model->get_unused($this->uid, 'jpg,png,gif', 9),
                'moreurl' => $topic ? dr_member_url('space/sns/topic', array('id' => $topic['id'], 'more' => 1)) : dr_member_url('space/sns/index', array('type'=>$type, 'uid'=>$uid, 'more' => 1)),
            ));
            $this->template->display('sns_index.html');
        } else {
            $data = $this->db->limit($this->pagesize, $this->pagesize * ($page - 1))->get('sns_feed')->result_array();
            if (!$data) {
                exit('null');
            }
            $this->template->assign(array(
                'list' => $data,
            ));
            $this->template->display('sns_data.html');
        }
	}

    // 动态详情
    public function feed() {

        header("Location: ".dr_space_sns_url($data['uid'], 'show', $id));exit;
    }

    /**
     * 我的关注的好友
     */
    public function follow() {

        $kw = $this->input->get('kw');
        $uid = (int)$this->input->get('uid');
        $gid = (int)$this->input->get('gid');
        $page = max((int)$this->input->get('page'), 1);

        // ta的好友
        if ($uid) {
            // 访问权限
            $this->_show($uid);
            $ta = dr_member_info($uid);
            $this->db->where('f.fid', $uid);
            $this->template->assign('ta', $ta);
        } else {
            $this->db->where('f.fid', $this->uid);
        }

        // 查询数据
        $this->db->from($this->db->dbprefix('sns_follow').' AS f');
        $this->db->join($this->db->dbprefix('member').' AS m', 'm.uid=f.uid', 'left');
        if ($gid) {
            $this->db->where('f.gid', $gid);
        }
        if ($kw) {
            $this->db->like('f.username', $kw);
        }

        $this->db->order_by('f.ctime desc');
        $this->db->limit($this->pagesize, $this->pagesize * ($page - 1));
        $data = $this->db->get()->result_array();

        // 我的分组
        $g = array();
        $group = $this->db
                      ->where('uid', $this->uid)
                      ->order_by('ctime desc')
                      ->get('sns_follow_group')
                      ->result_array();
        if ($group) {
            foreach ($group as $t) {
                $g[$t['id']] = $t;
            }
            unset($group);
        }

        // 模板选择
        if ($page == 1) {
            $this->template->assign(array(
                'list' => $data,
                'group' => $g,
                'moreurl' => dr_member_url('space/sns/follow', array('gid' => $gid, 'uid' => $uid, 'kw' => $kw))
            ));
            $this->template->display('sns_follow.html');
        } else {
            if (!$data) {
                exit('null');
            }
            $this->template->assign(array(
                'list' => $data,
            ));
            $this->template->display('sns_follow_data.html');
        }
    }

    /**
     * 我的fans
     */
    public function fans() {

        $uid = (int)$this->input->get('uid');
        $page = max((int)$this->input->get('page'), 1);

        // ta的粉丝
        if ($uid) {
            // 访问权限
            $this->_show($uid);
            $ta = dr_member_info($uid);
            $this->db->where('uid IN(select fid from '.$this->db->dbprefix('sns_follow').' where uid='.$uid.')');
            $this->template->assign('ta', $ta);
        } else {
            $this->db->where('uid IN(select fid from '.$this->db->dbprefix('sns_follow').' where uid='.$this->uid.')');
        }

        // 查询数据
        $this->db->from($this->db->dbprefix('member'));
        $this->db->order_by('uid desc');
        $this->db->limit($this->pagesize, $this->pagesize * ($page - 1));
        $data = $this->db->get()->result_array();

        // 模板选择
        if ($page == 1) {
            $this->template->assign(array(
                'list' => $data,
                'moreurl' => dr_member_url('space/sns/fans', array('uid' => $uid))
            ));
            $this->template->display('sns_fans.html');
        } else {
            if (!$data) {
                exit('null');
            }
            $this->template->assign(array(
                'list' => $data,
            ));
            $this->template->display('sns_fans_data.html');
        }
    }



    // 我的邀请用户
    public function invite() {

        $page = max((int)$this->input->get('page'), 1);
        $data = $this->db
                     ->where('uid', $this->uid)
                     ->order_by('regtime desc')
                     ->limit($this->pagesize, $this->pagesize * ($page - 1))
                     ->get('member_invite')
                     ->result_array();
        if ($page == 1) {
            $this->template->assign(array(
                'list' => $data,
                'moreurl' => dr_member_url('space/sns/invite')
            ));
            $this->template->display('sns_invite.html');
        } else {
            if (!$data) {
                exit('null');
            }
            $this->template->assign(array(
                'list' => $data,
            ));
            $this->template->display('sns_invite_data.html');
        }

    }


    // 会员好友状态选择
    public function follow_member() {

        $uid = (int)$this->input->get('uid');
        $row = $this->db->where('fid', $this->uid)->where('uid', $uid)->get('sns_follow')->row_array();
        if (!$row) {
            // 未关注
            echo '<b class="ico-add-blue"></b>关注';
        } elseif ($row['isdouble']) {
            // 相互关注
            echo '<b class="ico-minus-gray"></b>取消关注';
        } else {
            // 已经关注
            echo '<b class="ico-minus-gray"></b>取消关注';
        }
        exit;
    }



    // 删除动态
    public function delete() {

        $id = (int)$this->input->get('id');
        if ($this->member['adminid']
            || $this->db->where('uid', $this->uid)->where('id', $id)->count_all_results('sns_feed')) {
            $this->sns_model->delete($id);
            exit(dr_json(1, fc_lang('操作成功')));
        } else {
            exit(dr_json(0, fc_lang('操作失败')));
        }
    }

    // 删除动态评论
    public function delete_comment() {

        $id = (int)$this->input->get('id');
        $data = $this->db->where('id', $id)->get('sns_comment')->row_array();
        if ($this->member['adminid'] || $data['uid'] == $this->uid) {
            $this->sns_model->delete_comment($id, $data['fid']);
            if ($this->callback) {
                exit($this->callback.'('.dr_json(1, fc_lang('操作成功')).')');
            } else {
                exit(dr_json(1, fc_lang('操作成功')));
            }
        } else {
            if ($this->callback) {
                exit($this->callback.'('.dr_json(0, fc_lang('操作失败')).')');
            } else {
                exit(dr_json(0, fc_lang('操作失败')));
            }
        }

    }




    // 评论
    public function comment() {

        $id = (int)$this->input->get('id');
        $content = trim(dr_safe_replace($this->callback ? $this->input->get('content') : $this->input->post('content')));

        // 验证字数
        if (($num = (int)$this->get_cache('member', 'setting', 'sns_post_num'))
            && strlen($content) > $num) {
            if ($this->callback) {
                exit($this->callback.'('.dr_json(0, fc_lang('您的字数超过了系统上限')).')');
            } else {
                exit(dr_json(0, fc_lang('您的字数超过了系统上限')));
            }
        }

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

        $content = trim($content);
        if (!$content) {
            if ($this->callback) {
                exit($this->callback.'('.dr_json(0, fc_lang('内容不能为空')).')');
            } else {
                exit(dr_json(0, fc_lang('内容不能为空')));
            }
        }

        $data = dr_sns_feed($id);
        if (!$data) {
            if ($this->callback) {
                exit($this->callback.'('.dr_json(0, fc_lang('此动态不存在')).')');
            } else {
                exit(dr_json(0, fc_lang('此动态不存在')));
            }
        }

        // 写入评论
        $this->db->insert('sns_comment', array(
            'fid' => $id,
            'uid' => $this->uid,
            'comment' => $content,
            'inputip' => $this->input->ip_address(),
            'username' => $this->member['username'],
            'inputtime' => SYS_TIME,
        ));

        // @给作者
        $this->member_model->add_notice($data['uid'], 2, fc_lang('【%s】评论了您的动态，<a href="%s" target="_blank">查看详情</a>。', $this->member['username'], dr_sns_feed_url($data['uid'], $id)));

        // 给@的人发送提醒
        if ($user) {
            $this->member_model->add_notice($user, 2, fc_lang('【%s】在评论中回复@了你，<a href="%s" target="_blank">查看详情</a>。', $this->member['username'], dr_sns_feed_url($data['uid'], $id)));
        }

        // 更新动态表
        $this->db->where('id', $id)->set('comment', 'comment+1', FALSE)->update('sns_feed');

        if ($this->callback) {
            exit($this->callback.'('.dr_json(1, fc_lang('评论成功')).')');
        } else {
            exit(dr_json(1, fc_lang('评论成功')));
        }
    }



    // 发表动态
    public function post() {

        // 验证间隔
        if (($time = (int)$this->get_cache('member', 'setting', 'space', 'sns_post_time'))
            && get_cookie('sns_post_'.$this->uid)) {
            exit(dr_json(0, fc_lang('亲，您动作太快了！')));
        }

        $content = trim(dr_safe_replace($this->input->post('content')));

        // 验证字数
        if (($num = (int)$this->get_cache('member', 'setting', 'space', 'sns_post_num'))
            && strlen($content) > $num) {
            exit(dr_json(0, fc_lang('您的字数超过了系统上限')));
        }


        // 发布
        $this->member_model->add_sns(
            $this->uid,
            $content,
            $this->input->post('attach'),
            dr_safe_replace($this->input->post('source')),
            0
        );

        // 保存cookie
        if ($time) {
            set_cookie('sns_post_'.$this->uid, SYSTIME, $time);
        }

        exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
    }



    // 取消关注
    public function follow_delete() {
        $uid = (int)$this->input->get('uid');
        if ($this->sns_model->following($uid)) {
            exit('1');
        } else {
            exit('0');
        }
    }

    // 设置备注
    public function remark() {
        $id = (int)$this->input->get('id');
        if (IS_POST) {
            $name = trim(dr_safe_replace($this->input->post('name')));
            $this->db->where('id', $id)->where('uid', $this->uid)->update('sns_follow', array('remark'=>$name));
            echo $name;exit;
        } else {
            $this->template->display('sns_remark.html');
        }
    }

    // 分组选择
    public function group_select() {
        $id = (int)$this->input->get('id');
        $gid = (int)$this->input->get('gid');
        $group = $this->db->where('id', $gid)->get('sns_follow_group')->row_array();
        if ($group) {
            $this->db->where('id', $id)->update('sns_follow', array('gid'=>$gid));
            exit(dr_strcut($group['title'],15));
        }
        exit;
    }

    /**
     * 添加分组
     */
    public function group_add() {
        $title = trim(dr_safe_replace($this->input->post('title')));
        if (!$title) {
            exit(dr_json(0, fc_lang('分组名称不能为空')));
        }
        $this->db->insert('sns_follow_group', array(
            'uid' => $this->uid,
            'title' => $title,
            'ctime' => SYS_TIME,
        ));
        exit(dr_json(1, '', $this->db->insert_id()));
    }

    /**
     * 保存分组
     */
    public function group_save() {
        $gid = intval($this->input->post('gid'));
        $title = trim(dr_safe_replace($this->input->post('title')));
        if (!$title) {
            exit(dr_json(0, fc_lang('分组名称不能为空')));
        }
        $this->db->where('id', $gid)->where('uid', $this->uid)->update('sns_follow_group', array(
            'title' => $title
        ));
        exit(dr_json(1, ''));
    }

    /**
     * 删除分组
     */
    public function group_del() {
        $gid = intval($this->input->post('gid'));
        // 删除此分组
        $this->db->where('id', $gid)->delete('sns_follow_group');
        // 清零关注表的分组值
        $this->db->where('uid', $this->uid)->where('gid', $gid)->update('sns_follow', array('gid'=>0));
        exit(dr_json(1, ''));
    }

    /**
     * 分组管理
     */
    public function group() {
        $this->template->assign(array(
            'list' => $this->db
                           ->where('uid', $this->uid)
                           ->order_by('ctime desc')
                           ->get('sns_follow_group')
                           ->result_array()
        ));
        $this->template->display('sns_group.html');
    }


    // 关注
    public function set_follow() {

        $rt = $this->sns_model->following((int)$this->input->get('uid'));
        if ($this->callback) {
            exit($this->callback.'('.json_encode(array('html' => $rt)).')');
        } else {
            echo $rt;exit;
        }
    }

    // 会员信息
    public function member() {

        $uid = (int)$this->input->get('uid');
        $row = $this->db->where('fid', $this->uid)->where('uid', $uid)->get('sns_follow')->row_array();

        $this->template->assign(array(
            'uid' => $uid,
            'follow' => $row ? $row['isdouble'] : -1,
            'membersns' => dr_sns_info($uid),
            'memberinfo' => dr_member_info($uid),
        ));

        if ($this->callback) {
            ob_start();
            $this->template->display('sns_member.html');
            $html = ob_get_contents();
            ob_clean();
            exit($this->callback.'('.json_encode(array('html' => $html)).')');
        } else {
            $this->template->display('sns_member.html');
        }
    }

    // 转发
    public function repost() {

        $id = (int)$this->input->get('id');

        if (IS_POST || ($this->callback && isset($_GET['submit']))) {
            // 执行转发操作
            $content = trim(dr_safe_replace($this->callback ? $this->input->get('content') : $this->input->post('content')));
            if (!$content) {
                if ($this->callback) {
                    exit($this->callback.'('.dr_json(0, fc_lang('内容不能为空')).')');
                } else {
                    exit(dr_json(0, fc_lang('内容不能为空')));
                }
            }
            // 验证字数
            if (($num = (int)$this->get_cache('member', 'setting', 'sns_post_num'))
                && strlen($content) > $num) {
                if ($this->callback) {
                    exit($this->callback.'('.dr_json(0, fc_lang('您的字数超过了系统上限')).')');
                } else {
                    exit(dr_json(0, fc_lang('您的字数超过了系统上限')));
                }
            }
            $this->member_model->add_sns($this->uid, $content, '', 0, $id);
            if ($this->callback) {
                exit($this->callback.'('.dr_json(1, fc_lang('动态转发成功')).')');
            } else {
                exit(dr_json(1, fc_lang('动态转发成功')));
            }
        }

        // 转发数据
        $data = dr_sns_feed($id);
        if (!$data) {
            exit(fc_lang('此动态不存在'));
        }
        $data = $data['repost_id'] ? dr_sns_feed($data['repost_id']) : $data;

        // 表情符号
        $this->load->helper('directory');
        $this->template->assign('emotion', directory_map(WEBPATH.'api/emotions/', 1));

        $this->template->assign(array(
            'data' => $data,
            'group' => $this->db->where('uid', $this->uid)->order_by('ctime desc')->get('sns_follow_group')->result_array(),
        ));

        if ($this->callback) {
            ob_start();
            $this->template->display('sns_repost.html');
            $html = ob_get_contents();
            ob_clean();
            exit($this->callback.'('.json_encode(array('html' => $html)).')');
        } else {
            $this->template->display('sns_repost.html');
        }
    }

    // 查询分组下的好友
    public function select_user() {

        $gid = (int)$this->input->get('gid');
        $data = $this->db->where('gid', $gid)->where('fid', $this->uid)->get('sns_follow')->result_array();
        $result = '';
        if ($data) {
            foreach ($data as $t) {
                $result.= '<li onclick="dr_insert_user(\''.$t['username'].'\')">
                <a href="javascript:void(0);">
                <img src="'.dr_avatar($t['uid']).'" />'.($t['remark'] ? $t['remark'] : $t['username']).'</a></li>';
            }
        }

        if ($this->callback) {
            exit($this->callback.'('.json_encode(array('html' => $result)).')');
        } else {
            exit($result);
        }

    }

    // 赞
    public function digg() {

        $id = (int)$this->input->get('id');

        // 动态详情
        $data = $this->db->select('digg')->where('id', $id)->get('sns_feed')->row_array();
        if (!$data) {
            $msg = '-';
        } else {
            if ($this->db->where('uid', $this->uid)->where('fid', $id)->count_all_results('sns_feed_digg')) {
                // 已经赞了就取消赞
                $digg = max(intval($data['digg']) - 1, 0);
                $this->db->where('uid', $this->uid)->where('fid', $id)->delete('sns_feed_digg');
            } else {
                $digg = intval($data['digg']) + 1;
                $this->db->insert('sns_feed_digg', array(
                    'fid' => $id,
                    'uid' => $this->uid,
                ));
            }
            // 更新赞总数
            $this->db->where('id', $id)->update('sns_feed', array('digg' => $digg));
            $msg = $digg;
        }

        if ($this->callback) {
            exit($this->callback.'('.json_encode(array('html' => $msg)).')');
        } else {
            echo $msg;exit;
        }
    }


    // 收藏
    public function favorite() {

        $id = (int)$this->input->get('id');

        if ($this->db->where('uid', $this->uid)->where('fid', $id)->count_all_results('sns_feed_favorite')) {
            // 已经收藏就取消
            $this->db->where('uid', $this->uid)->where('fid', $id)->delete('sns_feed_favorite');
            $msg = fc_lang('收藏');
        } else {
            $this->db->insert('sns_feed_favorite', array(
                'fid' => $id,
                'uid' => $this->uid,
            ));
            $msg = fc_lang('取消收藏');
        }

        if ($this->callback) {
            exit($this->callback.'('.json_encode(array('html' => $msg)).')');
        } else {
            exit($msg);
        }
    }

    // 评论列表
    public function comment_list() {

        $fid = (int)$this->input->get('id');
        $more = (int)$this->input->get('more');
        $page = max((int)$this->input->get('page'), 1);

        // 动态详情
        $data = dr_sns_feed($fid);
        if (!$data) {
            if ($this->callback) {
                exit($this->callback.'('.json_encode(array('html' => '')).')');
            } else {
                exit('');
            }
        } else {
            // 显示方式
            $more = $more ? ($data['comment'] > 5 ? 1 : 0) : 0;
            $pagesize = $more ? 5 : $this->pagesize;
            $this->template->assign(array(
                'fid' => $fid,
                'more' => $more,
                'list' => $this->db->where('fid', $fid)->order_by('inputtime desc')->limit($pagesize, $pagesize * ($page - 1))->get('sns_comment')->result_array(),
            ));
            if ($this->callback) {
                ob_start();
                $this->template->display('sns_comment.html');
                $html = ob_get_contents();
                ob_clean();
                exit($this->callback.'('.json_encode(array('html' => $html)).')');
            } else {
                $this->template->display('sns_comment.html');
            }
        }
    }

}