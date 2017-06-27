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

class Home extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index() {

        $uid = (int)$this->input->get('uid');
        if ($uid) {
            $this->_space($uid); // 带会员uid参数时进入会员空间界面
        } else {
            // 识别会员空间二级域名
            $space_domain = $this->get_cache('member', 'setting', 'space', 'spacedomain');
            if ($space_domain) {
                $member_domain = $this->get_cache('member', 'setting', 'domain', SITE_ID); // 当前站点会员中心的域名
                $uid = $this->_get_space_uid(DOMAIN_NAME, $space_domain); // 获取到的会员uid
                if (!$uid && $member_domain && DOMAIN_NAME != $member_domain) {
                    // 当前域名和会员中心域名不一样时
                    $this->member_msg('此域名【'.DOMAIN_NAME.'】未生效');
                } elseif (!$uid && $member_domain && $member_domain != SITE_DOMAIN) {
                    // 当前域名和网站域名不一致时
                    $this->member_msg('此域名【'.DOMAIN_NAME.'】未生效');
                }
                if ($uid) {
                    $this->_space($uid, 1); // 带会员uid参数时进入会员空间界面
                }
            }
            // 开启伪静态时的获取uid方式
            if (!$uid && preg_match('/\/([0-9]+)\/index\.php/iU', $_SERVER['REQUEST_URI'], $match)) {
                $this->_space(intval($match[1]));
            }
        }

        $this->template->assign(array(
            'meta_title' => $this->space['title'],
            'meta_keywords' => $this->space['keywords'],
            'meta_description' => $this->space['description'],
        ));
        $this->template->display('index.html');
    }

    /**
     * 会员空间页
     */
    private function _space($uid, $is_domain = 0) {

        if (!MEMBER_OPEN_SPACE) {
            $this->member_msg(fc_lang('系统已经关闭了空间功能'));
        }

        define('IS_SPACE', $uid);

        $this->load->model('space_model');
        $this->load->model('space_category_model');
        $space = $this->space_model->get($uid);
        if (!$space) {
            $this->template->assign('theme', THEME_PATH.'space/default/');
            $this->member_msg(fc_lang('该会员的空间还没有创建'));
        }
        if (!$space['status']) {
            $this->member_msg(fc_lang('空间正在审核中'));
        }

        // 判断是否是自定义域名
        if (!$is_domain && $this->_space_show($uid)) {
            redirect(dr_member_url('member/api/access', array('uid' => $uid)), 'refresh');
            exit;
        }

        // 格式化空间信息
        $space = $this->field_format_value($this->get_cache('member', 'spacefield'), $space, 1);
        $style = $space['style'] ? $space['style'] : 'default';
        $theme = THEME_PATH.'space/'.$style.'/';

        $member = $this->member_model->get_member($uid);
        // 会员组使用权限判断
        if (!$member['allowspace']) {
            $this->member_msg(fc_lang('此空间因无权限使用而关闭'));
        }

        $action = dr_safe_replace($this->input->get('action', TRUE));
        $selected = 0; // 默认选中首页菜单
        $category = $this->space_category_model->get_data(0, $uid, 1);

        switch ($action) {

            case 'category': // 栏目处理

                $id = (int)$this->input->get('id');
                $cat = $category[$id];
                if (!$cat) {
                    $this->msg(fc_lang('空间栏目不存在'));
                }

                switch ($cat['type']) {

                    case 0: // 外链
                        if (!$cat['link']) {
                            $this->msg(fc_lang('此栏目的外链不存在'));
                        }
                        redirect($cat['link'], 'location', 301);
                        return NULL;
                        break;

                    case 1: // 模型
                        $model = $this->get_cache('space-model', $cat['modelid']);
                        if (!$model) {
                            $this->msg(fc_lang('此栏目模型不存在'));
                        }
                        $template = 'list_'.$model['table'].'.html';
                        // 选中顶级栏目
                        $temp = explode(',', $cat['pids']);
                        $selected = $temp[1] ? $temp[1] : $id;
                        break;

                    case 2: // 单页
                        $template = 'page.html';
                        // 选中顶级栏目
                        $temp = explode(',', $cat['pids']);
                        $selected = $temp[1] ? $temp[1] : $id;
                        // 单页验证是否存在子栏目
                        if ($cat['child']) {
                            $temp = explode(',', $cat['childids']);
                            if (isset($temp[1]) && $category[$temp[1]]) {
                                $id = $temp[1];
                                $cat = $category[$id];
                            }
                        }
                        break;
                }
                // 栏目下级或者同级栏目
                $related = $parent = array();
                if ($cat['pid']) {
                    foreach ($category as $t) {
                        if ($t['pid'] == $cat['pid']) {
                            $related[] = $t;
                            if ($cat['child']) {
                                $parent = $cat;
                            } else {
                                $parent = $category[$t['pid']];
                            }
                        }
                    }
                } elseif ($cat['child']) {
                    $parent = $cat;
                    foreach ($category as $t) {
                        if ($t['pid'] == $cat['id']) {
                            $related[] = $t;
                        }
                    }
                }

                $this->template->assign(array(
                    'cat' => $cat,
                    'catid' => $id,
                    'parent' => $parent,
                    'related' => $related,
                    'modelid' => $cat['modelid'],
                    'urlrule' => dr_space_list_url($uid, $id, TRUE),
                ));

                if ($cat['title']) {
                    $title = $cat['title'];
                } else {
                    $title = implode('-', array_reverse(explode('{-}', dr_space_catpos($uid, $id, '{-}', FALSE)))).'-'.$space['name'];
                }

                $this->template->assign(array(
                    'meta_title' => $title,
                    'meta_keywords' => $cat['keywords'],
                    'meta_description' => $cat['description'],
                ));
                break;

            case 'show': // 内容处理

                $id = (int)$this->input->get('id');
                $mid = (int)$this->input->get('mid');
                $mod = $this->get_cache('space-model', $mid);
                if (!$mod) {
                    $this->msg(fc_lang('此栏目模型不存在'));
                }

                $name = $this->db->dbprefix('space_'.$mod['table']).'-space-show-'.$id;
                $data = $this->get_cache_data($name);

                if (!$data) {
                    $this->load->model('space_content_model');
                    $this->space_content_model->tablename = $this->db->dbprefix('space_'.$mod['table']);
                    $data = $this->space_content_model->get($uid, $id);
                    if (!$data) {
                        $this->msg(fc_lang('数据不存在'));
                    }
                    if (!$data['status'] && $data['uid'] != $this->uid) {
                        $this->msg(fc_lang('该内容正在审核之中，您无权限查看'));
                    }

                    $cat = $category[$data['catid']];
                    if (!$cat) {
                        $this->msg(fc_lang('空间栏目不存在'));
                    }

                    // 检测转向字段
                    foreach ($mod['field'] as $t) {
                        if ($t['fieldtype'] == 'Redirect' && $data[$t['fieldname']]) {
                            redirect($data[$t['fieldname']], 'location', 301);
                            exit;
                        }
                    }

                    // 上一篇文章
                    $data['prev_page'] = $this->db
                        ->where('catid', $data['catid'])
                        ->where('id<', $id)
                        ->where('status', 1)
                        ->order_by('id desc')
                        ->limit(1)
                        ->get($this->space_content_model->tablename)
                        ->row_array();
                    // 下一篇文章
                    $data['next_page'] = $this->db
                        ->where('catid', $data['catid'])
                        ->where('id>', $id)
                        ->where('status', 1)
                        ->order_by('id asc')
                        ->limit(1)
                        ->get($this->space_content_model->tablename)
                        ->row_array();

                    $this->set_cache_data($name, $data, SYS_CACHE_SPACE);
                } else {
                    $cat = $category[$data['catid']];
                    if (!$cat) {
                        $this->msg(fc_lang('空间栏目不存在'));
                    }
                }

                // 格式化输出自定义字段
                $fields = $mod['field'];
                $fields['inputtime'] = array('fieldtype' => 'Date');
                $fields['updatetime'] = array('fieldtype' => 'Date');
                $data = $this->field_format_value($fields, $data, max(1, (int)$this->input->get('page')));

                // 栏目下级或者同级栏目
                $related = $parent = array();
                if ($cat['pid']) {
                    foreach ($category as $t) {
                        if ($t['pid'] == $cat['pid']) {
                            $related[] = $t;
                            if ($cat['child']) {
                                $parent = $cat;
                            } else {
                                $parent = $category[$t['pid']];
                            }
                        }
                    }
                } elseif ($cat['child']) {
                    $parent = $cat;
                    foreach ($category as $t) {
                        if ($t['pid'] == $cat['id']) {
                            $related[] = $t;
                        }
                    }
                }
                $template = 'show_'.$mod['table'].'.html';
                // 选中顶级栏目
                $temp = explode(',', $cat['pids']);
                $selected = $temp[1] ? $temp[1] : $cat['id'];


                $this->template->assign($data);
                $this->template->assign(array(
                    'cat' => $cat,
                    'catid' => $cat['id'],
                    'parent' => $parent,
                    'related' => $related,
                    'modelid' => $cat['modelid'],
                ));

                $temp = dr_space_catpos($uid, $cat['id'], '{-}', FALSE);
                $temp = explode('{-}', $temp);
                $catstr = implode(SITE_SEOJOIN, array_reverse($temp));
                $this->template->assign(array(
                    'meta_title' => ($data['content_title'] ? $data['content_title'].SITE_SEOJOIN : '').$data['title'].SITE_SEOJOIN.$catstr.SITE_SEOJOIN.$space['name'],
                    'meta_keywords' => $data['keywords'],
                    'meta_description' => dr_strcut(dr_clearhtml($data['content']), 200, ''),
                ));
                break;
            case 'sns': // sns部分
                $template = $this->_sns($space);
                break;
            default: // 首页或者其他自定义页面
                $template = $action ? $action.'.html' : 'index.html';
                $this->template->assign(array(
                    'meta_title' => $space['title'] ? $space['title'] : $space['name'],
                    'meta_keywords' => $space['keywords'],
                    'meta_description' => $space['description'],
                ));
                break;
        }
        // 更新访问量pv
        $this->db->where('uid', $uid)->update('space', array('hits' => $space['hits'] + 1));
        // 空间地址
        $space['url'] = dr_space_url($uid);
        // 会员姓名
        $space['mname'] = $space['cname'] = $member['name'];
        // 我收藏的
        $favorite = array();
        if ($this->uid) {
            $temp = $this->db->where('uid', $this->uid)->get('sns_feed_favorite')->result_array();
            if ($temp) {
                foreach ($temp as $t) {
                    $favorite[] = $t['fid'];
                }
                unset($temp);
            }
        }

        $this->template->assign(array(
            'uid' => $uid,
            'style' => $style,
            'theme' => $theme,
            'space' => $space + $member,
            'spaceid' => $uid,
            'tableid' => (int)substr((string)$uid, -1, 1),
            'selected' => $selected,
            'category' => $category,
            'favorite' => $favorite,
            'space_count' => array(
                'feed' => $this->db->where('uid', $uid)->count_all_results('sns_feed'),
                'fans' => $this->db->where('uid', $uid)->count_all_results('sns_follow'),
                'follow' => $this->db->where('fid', $uid)->count_all_results('sns_follow'),
            ),
        ));

        $this->template->space($style);
        $this->template->display($template);
        exit;
    }

    // sns 页面
    private function _sns($space) {

        $this->load->model('sns_model');
        $name = $this->input->get('name');
        $urlrule = '';

        switch ($name) {

            case 'follow': // 关注列表
                $title = fc_lang('TA的关注');
                $template = 'follow.html';
                break;

            case 'fans': // 粉丝列表
                $title = fc_lang('TA的粉丝');
                $template = 'fans.html';
                break;

            case 'topic': // 话题列表
                $id = (int)$this->input->get('id');
                $topic = $this->db->where('id', $id)->get('sns_topic')->row_array();
                if (!$topic) {
                    $this->msg(fc_lang('此话题不存在'));
                }
                $this->template->assign(array(
                    'topic' => $topic,
                    'topic_sql' => 'select * from '.$this->db->dbprefix('sns_feed').' where id IN (select fid from '.$this->db->dbprefix('sns_topic_index').' where tid='.$topic['id'].') order by inputtime desc',
                ));
                $title = fc_lang('话题#%s#', $topic['name']);
                $urlrule = dr_space_sns_url($space['uid'], 'topic', $id, '[page]');
                $template = 'topic.html';
                break;

            case 'show': // 动态详情
                $id = (int)$this->input->get('id');
                $data = dr_sns_feed($id);
                if (!$data) {
                    $this->msg(fc_lang('此动态不存在'));
                }
                $title = fc_lang('TA的动态');
                $template = 'show_feed.html';
                $this->template->assign(array(
                    'data' => $data,
                ));
                break;

            case 'access': // 访客列表
                $title = fc_lang('TA的访客');
                $template = 'access.html';
                break;

            default: // 动态列表
                $title = fc_lang('TA的动态');
                $template = 'list_feed.html';
                break;
        }

        $this->template->assign(array(
            'title' => $title,
            'urlrule' => $urlrule ? $urlrule : dr_space_sns_url($space['uid'], $name, '[page]'),
            'meta_title' => $title.SITE_SEOJOIN.($space['title'] ? $space['title'] : $space['name']),
            'meta_keywords' => $space['keywords'],
            'meta_description' => $space['description'],
        ));
        return $template;
    }


}