<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dayrui Website Management System
 *
 * @since		version 2.5.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */
	
class Sns_model extends CI_Model{

	public $cache_file;
    
	/**
	 * 初始化
	 */
    public function __construct() {
        parent::__construct();
    }

    // 互动配置
    public function config($uid, $data = array()) {

        if ($data) {
            $data['uid'] = $uid;
            $this->db->replace('sns_config', $data);
        }

        $data = $this->db->where('uid', $uid)->get('sns_config')->row_array();
        if (!$data) {
            $data = array(
                'uid' => $uid,
                'show_all' => 1,
                'show_fans' => 1,
                'show_follow' => 1,
            );
            $this->db->replace('sns_config', $data);
        }

        return $data;
    }

    /**
     * 处理会员的关注
     * $uid 需要关注的人
     * $fid 操作关注的人
     * $gid 关注分组
     * 返回
     * 0    关注失败
     * 1    关注成功
     * 2    相互关注
     * -1   取消关注
     */
    public function following($uid, $fid = 0, $gid = 0) {

        // 被关注的人
        $fid = $fid ? $fid : $this->uid;
        if ($uid == $fid) {
            return 0;
        }

        // 查询此人是否被关注
        if ($this->db->where('fid', $fid)->where('uid', $uid)->count_all_results('sns_follow')) {
            // 被关注时就取消关注
            $this->db->where('fid', $fid)->where('uid', $uid)->delete('sns_follow');
            $this->db->where('uid', $fid)->where('fid', $uid)->update('sns_follow', array('isdouble' => 0));
            return -1;
        } else {
            // 当此人没被关注时，我们就添加到关注中
            $m = $this->member_model->get_base_member($uid);
            $f = $fid == $this->uid ? $this->member : $this->member_model->get_base_member($fid);
            if (!$m || !$f) {
                return 0;
            }
            $this->db->insert('sns_follow', array(
                'uid' => $uid,
                'fid' => $fid,
                'gid' => $gid,
                'ctime' => SYS_TIME,
                'remark' => '',
                'isdouble' => 0,
                'username' => $m['username'],
                'fusername' => $f['username'],
            ));
            // 查询我是否被此人也关注
            if ($this->db->where('uid', $fid)->where('fid', $uid)->count_all_results('sns_follow')) {
                $this->db->where('uid', $fid)->where('fid', $uid)->update('sns_follow', array('isdouble' => 1));
                $this->db->where('fid', $fid)->where('uid', $uid)->update('sns_follow', array('isdouble' => 1));
                return 2;
            } else {
                return 1;
            }
        }
    }

    // 删除动态
    public function delete($id) {

        // 查询记录是否存在
        $data = $this->db->where('id', $id)->get('sns_feed')->row_array();
        if (!$data) {
            return;
        }

        // 删除记录
        $this->db->where('id', $id)->delete('sns_feed');
        $this->db->where('fid', $id)->delete('sns_comment');
        $this->db->where('fid', $id)->delete('sns_topic_index');
        $this->db->where('fid', $id)->delete('sns_feed_digg');
        $this->db->where('fid', $id)->delete('sns_feed_favorite');
        $this->ci->set_cache_data('sns-feed-'.$id, '', 1);

        // 删除附件
        if ($data['images']) {
            $images = @explode('|', $data['images']);
            if ($images) {
                $this->load->model('attachment_model');
                foreach ($images as $id) {
                    $this->attachment_model->delete($this->uid, '', $id);
                }
            }
        }
    }

    // 删除话题
    public function delete_topic($id) {

        // 查询话题的相关动态
        $data = $this->db->where('tid', $id)->get('sns_topic_index')->result_array();
        if ($data) {
            foreach ($data as $t) {
                // 删除相关动态
                $this->delete($t['fid']);
            }
        }

        $this->db->where('id', $id)->delete('sns_topic');
        $this->db->where('tid', $id)->delete('sns_topic_index');

    }

    // 删除动态评论
    public function delete_comment($id, $fid) {

        $this->db->where('id', $id)->delete('sns_comment');
        $this->db->where('id', $fid)->set('comment', 'comment-1', FALSE)->update('sns_feed');
        $this->ci->set_cache_data('sns-feed-'.$fid, '', 1);

    }

    /**
     * 条件查询
     *
     * @param	object	$select	查询对象
     * @param	array	$param	条件参数
     * @return	array
     */
    private function _feed_where(&$select, $param) {

        // 缓存文件名称
        $file = md5($this->duri->uri(1).$this->uid.SITE_ID.$this->input->ip_address().$this->input->user_agent());
        $_param = array();

        // 存在POST提交时，重新生成缓存文件
        if (IS_POST) {
            $data = $this->input->post('data');
            foreach ($data as $i => $t) {
                if (!$t) {
                    unset($data[$i]);
                }
            }
            $this->cache->file->save($file, $data, 3600);
            $param['search'] = 1;
        }

        // 存在search参数时，读取缓存文件
        if ($param['search'] == 1) {
            $data = $this->cache->file->get($file);
            $_param['search'] = 1;
            if (isset($data['keyword']) && $data['keyword'] && $data['field']) {
                if ($data['field'] == 'uid') {
                    $id = array();
                    $ids = explode(',', $data['keyword']);
                    foreach ($ids as $i) {
                        $id[] = (int) $i;
                    }
                    $select->where_in('uid', $id);
                } else {
                    $select->like($data['field'], urldecode($data['keyword']));
                }
            }
        }

        return array($_param, $data);
    }

    /**
     * 数据分页显示
     *
     * @param	array	$param	条件参数
     * @param	intval	$page	页数
     * @param	intval	$total	总数据
     * @return	array
     */
    public function feed_limit_page($param, $page, $total) {

        if (!$total || IS_POST) {
            $select = $this->db->select('count(*) as total');
            $this->_feed_where($select, $param);
            $data = $select->get('sns_feed')->row_array();
            unset($select);
            $total = (int) $data['total'];
            if (!$total) {
                return array(array(), array('total' => 0));
            }
            $page = 1;
        }

        $select = $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        list($_param, $_search) = $this->_feed_where($select, $param);
        $order = dr_get_order_string(isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0 ? $this->input->get('order', TRUE) : 'inputtime desc', 'inputtime desc');
        $data = $select->order_by($order)->get('sns_feed')->result_array();
        $_param['total'] = $total;
        $_param['order'] = $order;

        return array($data, $_param, $_search);
    }


    /**
     * 数据分页显示
     *
     * @param	array	$param	条件参数
     * @param	intval	$page	页数
     * @param	intval	$total	总数据
     * @return	array
     */
    public function topic_limit_page($param, $page, $total) {

        if (!$total || IS_POST) {
            $select = $this->db->select('count(*) as total');
            $this->_feed_where($select, $param);
            $data = $select->get('sns_topic')->row_array();
            unset($select);
            $total = (int) $data['total'];
            if (!$total) {
                return array(array(), array('total' => 0));
            }
            $page = 1;
        }

        $select = $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        list($_param, $_search) = $this->_feed_where($select, $param);
        $order = isset($_GET['order']) && strpos($_GET['order'], "undefined") !== 0 ? $this->input->get('order') : 'inputtime desc';
        $data = $select->order_by($order)->get('sns_topic')->result_array();
        $_param['total'] = $total;
        $_param['order'] = $order;

        return array($data, $_param, $_search);
    }

}