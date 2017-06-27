<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pay_model extends CI_Model{

    public $cache_file;

    /**
     * 条件查询
     *
     * @param	object	$select	查询对象
     * @param	array	$param	条件参数
     * @return	array
     */
    private function _card_where(&$select, $param) {

        $_param = array();
        $this->cache_file = md5($this->duri->uri(1).$this->uid.SITE_ID.$this->input->ip_address().$this->input->user_agent()); // 缓存文件名称

        // 存在POST提交时，重新生成缓存文件
        if (IS_POST) {
            $data = $this->input->post('data');
            $this->cache->file->save($this->cache_file, $data, 3600);
            $param['search'] = 1;
        }

        // 存在search参数时，读取缓存文件
        if ($param['search'] == 1) {
            $data = $this->cache->file->get($this->cache_file);
            $_param['search'] = 1;
            $data['card'] && $select->where('card', $data['card']);
            if (strlen($data['status']) > 0 && !$data['status']) {
                $select->where('uid=0');
            } elseif ($data['status']) {
                $select->where('uid>0');
            }
            $data['username'] && $select->where('username', $data['username']);
        }

        return $_param;
    }

    /**
     * 数据分页显示
     *
     * @param	array	$param	条件参数
     * @param	intval	$page	页数
     * @param	intval	$total	总数据
     * @return	array
     */
    public function card_limit_page($param, $page, $total) {

        if (!$total) {
            $select	= $this->db->select('count(*) as total');
            $this->_card_where($select, $param);
            $data = $select->get('member_paycard')->row_array();
            unset($select);
            $total = (int)$data['total'];
            if (!$total) return array(array(), array('total' => 0));
        }

        $select	= $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $_param	= $this->_card_where($select, $param);
        $data = $select->order_by('inputtime DESC')->get('member_paycard')->result_array();
        $_param['total'] = $total;

        return array($data, $_param);
    }

    /*
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

        if ($data) {
            isset($data['start']) && $data['start'] && $data['start'] != $data['end'] && $select->where('inputtime BETWEEN ' . $data['start'] . ' AND ' . $data['end']);
            strlen($data['status']) > 0 && $select->where('status', (int)$data['status']);
            strlen($data['keyword']) > 0 && $select->where('(uid in (select uid from '.$this->db->dbprefix('member').' where `username`="'.$data['keyword'].'"))');
            strlen($data['type']) > 0 && ($data['type'] ==1 ? $select->where('value>0') : $select->where('value<0'));
        }

        return $data;
    }

    /*
     * 数据分页显示
     *
     * @param	array	$param	条件参数
     * @param	intval	$page	页数
     * @param	intval	$total	总数据
     * @return	array
     */
    public function limit_page($param, $page, $total) {

        if (!$total || IS_POST) {
            $select	= $this->db->select('count(*) as total');
            $this->_where($select, $param);
            $data = $select->get('member_paylog')->row_array();
            unset($select);
            $total = (int)$data['total'];
            if (!$total) return array(array(), array('total' => 0));
        }

        $select	= $this->db->limit(SITE_ADMIN_PAGESIZE, SITE_ADMIN_PAGESIZE * ($page - 1));
        $_param	= $this->_where($select, $param);
        $data = $select->order_by('inputtime DESC')->get('member_paylog')->result_array();
        $_param['total'] = $total;

        return array($data, $_param);
    }

    // 购物消费
    public function add_for_buy($money, $order, $module = APP_DIR) {

        if (!$money || !$order) {
            return FALSE;
        }

        // 将变动金额冻结
        $this->db->where('uid', $this->uid)->set('money', 'money-'.$money, FALSE)->set('spend', 'spend+'.$money, FALSE)->update('member');

        // 来自订单模块
        if ($module == 'order') {
            $sn = $order['sn'];
            $oid = $order['id'];
            $orders = $order['list'];
            // 判断是否组合订单
            if ($orders) {
                $oid = $note = '';
                foreach ($orders as $o) {
                    $oid.= $o['id'].',';
                    $note.= '<a href="'.SITE_URL.'index.php?s=member&mod=order&c=home&m=info&id='.$o['id'].'" target="_blank">'.$o['sn'].'</a>&nbsp;&nbsp;';
                }
                $oid = trim($oid, ',');
            } else {
                $note = '<a href="'.SITE_URL.'index.php?s=member&mod=order&c=home&m=info&id='.$oid.'" target="_blank">'.$sn.'</a>';
            }
            $note = fc_lang('购物消费，订单号：%s', $note);
        } elseif ($module == 'app') {
            // 来自自定义插件
            $oid = dr_array2string($order);
            $note = $order['title'];
        } else {
            $oid = $order;
            $note = '<a href="'.SITE_URL.'index.php?s=member&mod='.$module.'&c=order&m=show&id='.$order.'" target="_blank">'.$order.'</a>';
            $note = fc_lang('购物消费，订单号：%s', $note);
        }

        // 更新记录表
        $this->db->insert('member_paylog', array(
            'uid' => (int)$this->uid,
            'type' => 0,
            'note' => $note,
            'value' => -1 * $money,
            'order' => $oid,
            'status' => 1,
            'module' => $module,
            'inputtime' => SYS_TIME
        ));

        return $this->db->insert_id();
    }

    // 充值
    public function add($uid, $value, $note) {

        if (!$uid || !$value) {
            return NULL;
        }

        // 更新RMB
        $db = $this->db->where('uid', $uid);
        if ($value > 0) {
            $db->set('money', 'money+'.$value, FALSE);
        } else {
            $db->set('money', 'money-'.abs($value), FALSE);
            $db->set('spend', 'spend+'.abs($value), FALSE);
        }
        $db->update('member');
        unset($db);

        // 更新记录表
        $this->db->insert('member_paylog', array(
            'uid' => $uid,
            'type' => 0,
            'note' => $note,
            'value' => $value,
            'order' => 0,
            'status' => 1,
            'module' => '',
            'inputtime' => SYS_TIME,
        ));
    }

    // 卡密充值
    public function add_for_card($id, $money, $card) {

        if (!$id || $money < 0) {
            return NULL;
        }

        // 更新RMB
        $this->db->where('uid', $this->uid)->set('money', 'money+'.$money, FALSE)->update('member');

        // 更新记录表
        $this->db->insert('member_paylog', array(
            'uid' => $this->uid,
            'type' => 0,
            'note' => fc_lang('卡号：%s', $card),
            'order' => 0,
            'value' => $money,
            'module' => '',
            'status' => 1,
            'inputtime' => SYS_TIME
        ));

        // 更新卡密状态
        $this->db->where('id', $id)->update('member_paycard', array(
            'uid' => $this->uid,
            'usetime' => SYS_TIME,
            'username' => $this->member['username'],
        ));

        return $money;
    }

    // 生成充值卡
    public function card($money, $endtime, $i) {

        if (!$money || !$endtime) {
            return NULL;
        }

        mt_srand((double)microtime() * (1000000 + $i));
        $data = array(
            'uid' => 0,
            'card' => date('Ys').strtoupper(substr(md5(uniqid()), rand(0, 20), 8)).mt_rand(100000, 999999),
            'money' => $money,
            'usetime' => 0,
            'endtime' => $endtime,
            'username' => '',
            'password' => mt_rand(100000, 999999),
            'inputtime' => SYS_TIME,
        );

        return $this->db->insert('member_paycard', $data) ? $data : NULL;
    }

    // 支付成功，更改状态
    public function pay_success($sn, $money, $note = '') {

        list($a, $id, $uid, $module, $order) = explode('-', $sn);
        if (!$id || !$uid) {
            return NULL;
        }

        $this->uid = $this->ci->uid = $uid;
        $this->member = $this->ci->member = dr_member_info($uid);

        // 查询支付记录
        $data = $this->db->where('id', $id)->limit(1)->get('member_paylog')->row_array();
        if (!$data) {
            return NULL;
        } elseif ($data['status']) {
            return $data['module'];
        }

        $money = $money > 0 ? $money : $data['value'];

        // 标示支付订单成功
        $this->db->where('id', $id)->update('member_paylog', array('status' => 1, 'note' => $note));

        // 更新会员表金额
        $uid && $this->db->where('uid', $uid)->set('money', 'money+'.$money, FALSE)->update('member');

        // 订单直接付款
        if ($data['module']) {
            if ($data['module'] == 'app') {
                // 来自自定义插件
                $order = dr_string2array($data['order']);
                require_once FCPATH.'app/'.$order['app'].'/models/App_pay_model.php';
                $pay = new App_pay_model();
                $pay->member = $this->member;
                $pay->pay($id, $order);
            } elseif ($data['module'] == 'order') {
                // 来至订单模块
                require_once FCPATH.'module/order/models/Order_model.php';
                $order = new Order_model();
                $order->member = $this->member;
                $oids = @implode(',', dr_string2array($data['order']));
                $oids && $order->pay($this->db->where('id IN ('.$oids.')')->get(SITE_ID.'_order')->result_array(), $money, $id);
            }
        }

        // 支付成功挂钩点
        $this->hooks->call_hook('pay_success', $data);

        return $data['module'];
    }

    // 在线充值
    public function add_for_online($pay, $money, $module = APP_DIR, $order = array()) {

        if (!$pay || $money < 0) {
            return NULL;
        }

        $module = $module == 'member' ? '' : $module;
        // 更新记录表
        $this->db->insert('member_paylog', array(
            'uid' => $this->uid,
            'note' => '',
            'type' => $pay,
            'value' => $money,
            'order' => dr_array2string($order),
            'status' => 0,
            'module' => $module,
            'inputtime' => SYS_TIME
        ));

        $id = $this->db->insert_id();
        if (!$id) {
            return NULL;
        }

        $sn= 'FC-'.$id.'-'.$this->uid;
        if ($order) {
            if ($module == 'order') {
                $title = fc_lang('会员(%s)购物消费，购物订单ID：%s', $this->member['username'], implode(',', $order));
            } elseif ($module == 'app') {
                $sn = 'FC-'.$id.'-'.$this->uid.'-'.strtoupper($module).'-'.$order['id'];
                $title = $order['title'];
            } else {
                $sn = 'FC-'.$id.'-'.$this->uid.'-'.strtoupper($module).'-'.(string)$order;
                $title = fc_lang('会员(%s)购物消费，购物订单ID：%s', $this->member['username'], strtoupper($module).'-'.(string)$order);
            }
        } else {
            $title = fc_lang('会员充值(%s)', $this->member['username']);
        }

        $result = NULL;
        require_once WEBPATH.'api/pay/'.$pay.'/pay.php';

        return $result;
    }

    // 在线付款
    public function pay_for_online($id) {

        if (!$id) {
            return NULL;
        }

        // 查询支付记录
        $data = $this->db
                    ->where('id', $id)
                    ->where('uid', $this->uid)
                    ->where('status', 0)
                    ->select('value,type,order,module')
                    ->limit(1)
                    ->get('member_paylog')
                    ->row_array();
        if (!$data) {
            return NULL;
        }

        // 判断订单是否支付过，否则作废
        $sn= 'FC-'.$id.'-'.$this->uid;
        if ($data['module']) {
            if ($data['module'] == 'order') {
                $data['order'] = dr_string2array($data['order']);
                $title = fc_lang('会员(%s)购物消费，购物订单ID：%s', $this->member['username'], implode(',', $data['order']));
            } elseif ($data['module'] == 'app') {
                $order = dr_string2array($data['order']);
                $sn = 'FC-'.$id.'-'.$this->uid.'-'.strtoupper($data['module']).'-'.$order['id'];
                $title = $order['title'];
            } else {
                $sn = 'FC-'.$id.'-'.$this->uid.'-'.strtoupper($data['module']).'-'.$data['order'];
                $title = fc_lang('会员(%s)购物消费，购物订单ID：%s', $this->member['username'], strtoupper($data['module']).'-'.$data['order']);
            }
        } else {
            $title = fc_lang('会员充值(%s)', $this->member['username']);
        }

        $money = $data['value'];
        $result = NULL;
        require_once WEBPATH.'api/pay/'.$data['type'].'/pay.php';
        return $result;
        //return method_exists($this, '_get_'.$data['type']) ? call_user_func_array(array($this, '_get_'.$data['type']), array($id, $data['value'], $title, $sn)) : '';
    }

}