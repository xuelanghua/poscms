<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * v3.2
 */

require FCPATH.'branch/fqb/D_Admin_Table.php';

class Wsms extends D_Admin_Table
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('weixin_model');
        // 判断会员组
        $group = $this->weixin_model->get_group();
        if (!$group) {
            $this->admin_msg('您还没有同步粉丝组...', dr_url('wgroup/index'));
        }
        $this->mydb = $this->db; // 数据库
        $this->tfield = 'subscribe_time';
        $this->mytable = $this->weixin_model->prefix . '_user';
        $this->myfield = $field = array(
            'username' => array(
                'name' => '会员账号',
                'ismain' => 1,
                'fieldname' => 'username',
            ),
        );
        $this->template->assign(array(
            'field' => $field,
            'groups' => $group,
            'menu' => $this->get_menu_v3(array(
                '消息发送' => array('admin/wsms/index', 'send'),
            )),
        ));
    }

    public function index() {

        if (IS_POST) {

            if (!$this->input->post('type')) {
                $this->admin_msg(fc_lang('正在开始发送...'), dr_url('wsms/send', array(
				'send_type' => $_POST ['send_type'], 
				'msg_type' => $_POST ['msg_type'], 
				'group_id' => $_POST ['group_id'], 
				'userid' => $_POST ['userid'], 
				'sc' => $_POST ['sc'], 
				'content' => $_POST ['content']
				)), 2);
            } else {
                $sendType = $_POST ['send_type'];
                if ($sendType == 2) {
                    // 分组群发
                    $groupId = $_POST ['group_id'];
                    $msg_id = $this->weixin_model->send_by_group ($groupId);
                } else {
                    $sendOpenid = array();
                    if ($_POST['userid']) {
                        // 指定粉丝
                        $users = $this->db->select('openid')->where_in('id', $_POST['userid'])->get($this->weixin_model->prefix.'_user')->result_array();
                    } else {
                        // 全部粉丝
                        $users = $this->db->select('openid')->get($this->weixin_model->prefix.'_user')->result_array();
                    }
                    if ($users) {
                        foreach ($users as $t) {
                            $sendOpenid[] = $t['openid'];
                        }
                    }
                    $msg_id = $this->weixin_model->send_by_openid($sendOpenid);
                }
                $this->admin_msg('发送完成 ('.$msg_id.')', '', 1);
            }


        }

        $this->template->display('wsms_index.html');
    }
	
		// 客服群发
	public function send() {
        $data = array();
        $data ['cTime'] = SYS_TIME;
        $data ['msgType'] = $_GET ['msg_type'];
        $data ['manager_id'] = $this->uid;
        $data ['content'] = $_GET ['content'];
        $data ['send_type'] = $sendType = $_GET ['send_type'];
        $data ['group_id'] = $groupId = $_GET ['group_id'];
        if ($data['msgType']=='appmsg'){
            $data['msgType']='news';
        }
        $sendOpenid = array();
        if ($_GET['userid']) {
            $users = $this->db->select('openid')->where_in('id', (int)$_GET['userid'])->get($this->weixin_model->prefix.'_user')->result_array();
            if ($users) {
                foreach ($users as $t) {
                    $sendOpenid[] = $t['openid'];
                }
            }
        }
        $openidArr = $data ['send_openids'] = $sendOpenid;
        if ($sendType == 3 && count($sendOpenid) <1 ) {
            $this->admin_msg('你还没有选择粉丝' );
        }

        $ok = (int)$this->input->get('ok');
        $sb = (int)$this->input->get('sb');

        $url =  dr_url('wsms/send', array(
            'send_type' => $_GET ['send_type'],
            'msg_type' => $_GET ['msg_type'],
            'group_id' => $_GET ['group_id'],
            'userid' => $_GET ['userid'],
            'content' => $_GET ['content'],
            'sc' => $_GET ['sc'],
            'sb' => $sb,
            'ok' => $ok,
        ));

        $page = max(1, (int)$this->input->get('page'));
        $psize = 10; // 每次发送n个粉丝


        if (!$sendOpenid) {
            if ($sendType == 1) {
                // 全部粉丝
                $openidArr = array();
                $allUser = $this->db->limit($psize, $psize * ($page - 1))->get($this->weixin_model->prefix.'_follow')->result_array();
                if (!$allUser) {
                    // 发送完毕
                    $this->admin_msg(fc_lang('发送完毕，成功('.$ok.')，失败('.$sb.')'), '', 1);
                }
                foreach ( $allUser as $k => $v ) {
                    $openidArr [] = $v['openid'];
                }
            } elseif ($sendType == 2) {
                // 按分组
                $openidArr = array();
                $allUser = $this->db->limit($psize, $psize * ($page - 1))->where('groupid', $groupId)->get($this->weixin_model->prefix.'_user')->result_array();
                if (!$allUser) {
                    // 发送完毕
                    $this->admin_msg(fc_lang('发送完毕，成功('.$ok.')，失败('.$sb.')'), '', 1);
                }
                foreach ( $allUser as $k => $v ) {
                    $openidArr[] = $v['openid'];
                }
            }
        } else {
            if ($page > 1) {
                $this->admin_msg(fc_lang('发送完毕，成功('.$ok.')，失败('.$sb.')'), '', 1);
            }
        }

        if (!$openidArr) {
            $this->admin_msg(fc_lang('发送完毕，成功('.$ok.')，失败('.$sb.')'), '', 1);
        }

        foreach ( $openidArr as $k) {

            if ($data ['msgType'] == 'text') {
                // 文本
                $data['news_group_id'] = '';
                $sc = (int)$_POST ['sc'];
                if ($sc) {
                    $text = $this->db->where('id', $sc)->get($this->weixin_model->prefix.'_material_text')->row_array();
                    $data['news_group_id'] = $text['content'];
                }
                if (!$data['news_group_id']) {
                    $data['news_group_id'] = $_GET['content'];
                }
                $result = $this->weixin_model->reply_text($k, $data['news_group_id']);
            } else if ($data ['msgType'] == 'news') {
                // 图文
                $data ['msgType'] = 'news';
                $data ['news_group_id'] = (int)$_GET ['sc'];
                if (empty($data ['news_group_id'] )) {
                    $this->admin_msg('请选择图文素材') ;
                }
                $result = $this->weixin_model->reply_news($k, $data['news_group_id']);

            } else if ($data ['msgType'] == 'image') {
                // 图片
                $image_material = (int)$_GET ['sc'];
                if ($image_material) {
                    // 由素材id查询数据
                    $imageMaterial = $this->db->where('id', $image_material)->get($this->weixin_model->prefix.'_material_image')->row_array();
                    $data ['image_id'] = $imageMaterial ['file'];
                    if ($imageMaterial ['media_id']) {
                        $data ['media_id'] = $imageMaterial ['media_id'];
                    } else {
                        $data ['media_id'] = $this->weixin_model->get_image_media_id($image_material['file']);
                    }
                    $result = $this->weixin_model->reply_image( $k, $data ['media_id'], '' );
                } else {
                    $this->admin_msg('请选择图片素材') ;
                }
            } else if ($data ['msgType'] == 'voice') {

                // 语音
                $data ['voice_id'] = $voiceId = (int)$_GET ['sc'];
                if (empty ( $voiceId )) {
                    $this->admin_msg ( '请选择语音素材' );
                }

                $voiceMaterial = $this->db->where('id', $voiceId)->get($this->weixin_model->prefix.'_material_file')->row_array();
                if ($voiceMaterial ['media_id']) {
                    $data ['media_id'] = $voiceMaterial ['media_id'];
                } else {
                    $data ['media_id'] = $this->weixin_model->get_file_media_id ( $voiceMaterial ['file'], 'voice' );
                }
                $result = $this->weixin_model->reply_voice ( $k, $data ['media_id'], '' );
            } else if ($data ['msgType'] == 'video') {
                // 视频
                $data ['video_id'] = $videoId = (int)$_GET ['sc'];
                if (empty ( $videoId )) {
                    $this->admin_msg ( '请选择视频素材' );
                }
                $videoMaterial = $this->db->where('id', $videoId)->get($this->weixin_model->prefix.'_material_file')->row_array();
                $data ['video_title'] = $videoMaterial ['title'];
                $data ['video_description'] = $videoMaterial ['introduction'];
                $data ['video_thumb'] = $this->weixin_model->get_thumb_media_id ();

                if ($videoMaterial ['media_id']) {
                    $data ['media_id'] = $videoMaterial ['media_id'];
                } else {
                    $data ['media_id'] = $this->weixin_model->get_file_media_id ( $videoMaterial ['file'], 'video' );
                }
                $result = $this->weixin_model->reply_video( $k, $data ['media_id'], '', $data ['video_thumb'], $videoMaterial ['title'], $data ['video_description'] );
            }
            if ($result ['status'] == 1) {
                    $ok ++;
                } else {
                    $sb ++;
                }
        }
                
		$this->admin_msg(fc_lang('正在开始发送到微信端('.$page.')...'), $url.'&page='.($page+1).'&ok='.$ok.'&sb='.$sb, 2);
	}

    /**
     * 粉丝选择数据读取
     */
    public function ajax_user() {


        $field['nickname'] = '微信昵称';
        $field['username'] = '会员账号';

        $param = array(
            'group' => -1,
        );

        if (IS_POST) {
            $data = $this->input->post('data');
            if ($data['group'] != -1) {
                $this->db->where('groupid', (int)$data['group']);
            }
            // 关键字和字段组合搜索
            if ($data['keyword']) {
                if ($data['field'] == 'nickname') {
                    $key = $data['keyword'];
                    $key2 = str_replace ( '\u', '\\\\\\\\u', trim ( dr_deal_emoji ($key, 0 ), '"' ) );
                    // 搜索用户表
                    $this->db->where("(nickname LIKE '%$key%' OR nickname LIKE '%$key2%')");
                } else {
                    $this->db->like('username', $data['keyword']);
                }
            }
            $param = $data;
        }

        $list = $this->db->limit(50)->order_by('subscribe_time DESC')->get($this->weixin_model->prefix.'_user')->result_array();

        $this->template->assign(array(
            'list' => $list,
            'field' => $field,
            'param' => $param,
            'groups' => $this->weixin_model->get_group(),
        ));
        $this->template->display('wsms_user.html');exit;
    }

    /**
     * 素材选择数据读取
     */
    public function ajax_sc() {

        $field['id'] = '素材id';
        $type = $this->input->get('type');
        $param = array();

        switch ($type) {

            case 'tw':
                $table = $this->weixin_model->prefix.'_material_news';
                $sql = 'SELECT *, count(id) as count FROM `'.$this->db->dbprefix($table).'`';
                if (IS_POST) {
                    $param = $this->input->post('data');
                    if ($param['keyword']) {
                        $sql.= ' WHERE `id` = '.intval($param['keyword']);
                    }
                }
                $list = $this->db->query($sql.' GROUP BY `group_id` ORDER BY `inputtime` DESC LIMIT 50')->result_array();
                if ($list) {
                    foreach ($list as $i => $t) {
                        if ($t['count'] > 1) {
                            $list[$i]['child'] = $this->db->where('id<>' . $t['id'])->where('group_id', $t['id'])->order_by('id asc')->get($table)->result_array();
                        }
                    }
                }
                break;
            case 'tp':
                $table = $this->weixin_model->prefix.'_material_image';
                if (IS_POST) {
                    $param = $this->input->post('data');
                    if ($param['keyword']) {
                        $this->db->where('id', intval($param['keyword']));
                    }
                }
                $list = $this->db->order_by('inputtime desc')->limit(50)->get($table)->result_array();

                break;
            case 'wz':
                $table = $this->weixin_model->prefix.'_material_text';
                if (IS_POST) {
                    $param = $this->input->post('data');
                    if ($param['keyword']) {
                        $this->db->where('id', intval($param['keyword']));
                    }
                }
                $list = $this->db->order_by('inputtime desc')->limit(50)->get($table)->result_array();

                break;
            case 'yy':
                $table = $this->weixin_model->prefix.'_material_file';
                if (IS_POST) {
                    $param = $this->input->post('data');
                    if ($param['keyword']) {
                        $this->db->where('id', intval($param['keyword']));
                    }
                }
                $list = $this->db->where('is_video', 0)->order_by('inputtime desc')->limit(50)->get($table)->result_array();
                $type = 'file';
                break;
            case 'sp':
                $table = $this->weixin_model->prefix.'_material_file';
                if (IS_POST) {
                    $param = $this->input->post('data');
                    if ($param['keyword']) {
                        $this->db->where('id', intval($param['keyword']));
                    }
                }
                $list = $this->db->where('is_video', 1)->order_by('inputtime desc')->limit(50)->get($table)->result_array();
                $type = 'file';
                break;
        }


        $this->template->assign(array(
            'list' => $list,
            'field' => $field,
            'param' => $param,
        ));
        $this->template->display('wsms_sc_'.$type.'.html');exit;
    }



    /*
	 * sendType:0 按组发 1：指定opendid
	 * groupid :0 指所有用户
	 */
    function _get_user_openid($sendType = 0, $groupId = 0, $openidStr = '') {

        if ($sendType == 1) {
            // 全部粉丝
            $uidArr = array();
            $allUser = $this->db->get($this->weixin_model->prefix.'_follow')->result_array();
            foreach ( $allUser as $k => $v ) {
                $uidArr [] = $v['openid'];
            }
            return $uidArr;
        } elseif ($sendType == 2) {
            // 按分组
            $uidArr = array();
            $allUser = $this->db->where('groupid', $groupId)->get($this->weixin_model->prefix.'_user')->result_array();
            foreach ( $allUser as $k => $v ) {
                $uidArr[] = $v['openid'];
            }
            return $uidArr;
        } else {
            return $openidStr;
        }
    }
}