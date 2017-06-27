<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Pay extends M_Controller {
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->load->model('pay_model');
    }

	// 支付成功后的跳转
	public function call() {

		$url = SITE_URL.'index.php?s=member&c=pay';
		$module = $this->input->get('module');
		
		// 订单模块跳转到买家中心
		$module == 'order' && $url = SITE_URL.'index.php?s=member&mod=order&c=home&m=index';

		redirect($url, 'refresh');
		exit;
	}
	
	/**
     * 在线充值付款跳转
     */
	public function go() {
		if ($data = $this->pay_model->pay_for_online((int)$this->input->get('id'))) {
            if (!isset($data['error'])) {
                if (isset($data['form']) && $data['form']) {
                    $this->member_msg(fc_lang('正在为您跳转到支付页面，请稍后...').'<div style="display:none">'.$data['form'].'</div>', 'javascript:;', 2, 0);
                } elseif (isset($data['url']) && $data['url']) {
                    $this->member_msg(fc_lang('正在为您跳转到支付页面，请稍后...'), $data['url'], 2, 0);
                } else {
                    $this->template->assign(array(
                        'pay' => $data,
                    ));
                    $this->template->display('pay_result.html');
                    exit;
                }
            } else {
                $this->member_msg($data['error']);
            }
		} else {
			$this->member_msg(fc_lang('充值失败，未知错误'));
		}
	}
	
	/**
     * 在线充值
     */
    public function add() {
	
		$money = (double)$this->input->get('money');
		
		if (IS_POST) {
			
			$pay = $this->input->post('pay');
			$money = (double)$this->input->post('money');
			
			if (!$money > 0) {
				$error = fc_lang('请输入一个有效的充值金额');
			} elseif (!$pay) {
				$error = fc_lang('请选择一种支付方式');
			} else {
				if ($data = $this->pay_model->add_for_online($pay, $money)) {
                    if (!isset($data['error'])) {
                        if (isset($data['form']) && $data['form']) {
                            $this->member_msg(fc_lang('正在为您跳转到支付页面，请稍后...').'<div style="display:none">'.$data['form'].'</div>', 'javascript:;', 2, 0);
                        } elseif (isset($data['url']) && $data['url']) {
                            $this->member_msg(fc_lang('正在为您跳转到支付页面，请稍后...'), $data['url'], 2, 0);
                        } else {
							(IS_AJAX || IS_API_AUTH) && exit(dr_json(1, $data['html']));
                            $this->template->assign(array(
                                'pay' => $data,
                            ));
                            $this->template->display('pay_result.html');
                            exit;
                        }
                    } else {
                        $this->member_msg($data['error']);
                    }
				} else {
					$error = fc_lang('充值失败，未知错误');
				}
			}
			(IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error));
		}
		$this->template->assign(array(
            'pay' => $pay,
			'list' => $this->get_pay_api(1),
			'money' => $money > 0 ? $money : '',
			'result_error' => $error,
		));
		$this->template->display('pay_add.html');
	}
	
	/**
     *  转账服务
     */
    public function transfer() {

        $error = '';
        if (IS_POST) {
            $data = $this->input->post('data');
            $member = $this->db->where('username', dr_safe_replace($data['username']))->get('member')->row_array();
            if (!$member) {
                $error = fc_lang('会员不存在');
            } elseif ($this->uid == $member['uid']) {
                $error = fc_lang('不能对自己转账');
            } else {
                if ($data['type']) {
                    // x
                    $value = abs((int)$data['value']);
                    if ($value <= 0) {
                        $error = fc_lang('请输入请输入数量');
                    } elseif ($value > $this->member['score']) {
                        $error = fc_lang(SITE_SCORE.'不足！本次需要%s'.SITE_SCORE.'，当前余额%s'.SITE_SCORE.'', $value, $this->member['score']);
                    } else {
                        $this->member_model->update_score(1, $this->uid, -$value, '', fc_lang('为会员【%s】转账'.SITE_SCORE.'%s', $member['username'], $value));
                        $this->member_model->update_score(1, $member['uid'], $value, '', fc_lang('收到会员【%s】转账'.SITE_SCORE.'%s', $member['username'], $value));
                        $this->member_msg(fc_lang('转账成功'), dr_member_url('pay/score'), 1);
                    }
                } else {
                    // r
                    $value = abs((float)$data['value']);
                    if ($value < 0.01) {
                        $error = fc_lang('金额无效，请重新填写');
                    } elseif ($value > $this->member['money']) {
                        $error = fc_lang(SITE_MONEY.'不足！本次需要%s'.SITE_MONEY.'，当前余额%s'.SITE_MONEY.'', $value, $this->member['money']);
                    } else {
                        $this->pay_model->add($this->uid, -$value, '为会员【%s】转账￥%s元'.$member['username'].','.$value);
                        $this->pay_model->add($member['uid'], $value, '收到会员【%s】转账的￥%s元'.$this->member['username'].','.$value);
                        $this->member_msg(fc_lang('转账成功'), dr_member_url('pay/index'), 1);
                    }
                }
            }
			(IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error));
        }

        $this->template->assign(array(
            'data' => $data,
            'result_error' => $error,
        ));
        $this->template->display('pay_transfer.html');

    }

	/**
     * 资金兑换
     */
    public function convert() {
		
		$error = '';
		if (IS_POST) {
			$type = (int)$this->input->post('type');
			if ($type) {
				// 兑换人民币
				$money = abs((int)$this->input->post('score1'));
				$score = (float)$money * SITE_CONVERT;
				if (!$money) {
					$error = fc_lang('请输入'.SITE_MONEY.'金额');
				} elseif ($score > $this->member['score']) {
					$error = fc_lang('账号余额不足');
				} else {
					// 虚拟币减少
					$this->member_model->update_score(1, $this->uid, -$score, '', '自助兑换服务');
					// 人民币增加
					$this->pay_model->add($this->uid, $money, '自助兑换服务');
					$this->member_msg(fc_lang('兑换成功'), dr_member_url('pay/index'), 1);
				}
			} else {
				// 兑换虚拟币
				$score = abs((int)$this->input->post('score0'));
				$money = (float)$score/SITE_CONVERT;
				if (!$score) {
					$error = fc_lang('请输入'.SITE_SCORE.'数量');
				} elseif ($money > $this->member['money']) {
					$error = fc_lang('账号余额不足');
				} else {
					// 虚拟币增加
					$this->member_model->update_score(1, $this->uid, $score, '', '自助兑换服务');
					// 人民币减少
					$this->pay_model->add($this->uid, -$money, '自助兑换服务');
					$this->member_msg(fc_lang('兑换成功'), dr_member_url('pay/score'), 1);
				}
			}

			(IS_AJAX || IS_API_AUTH) && exit(dr_json(0, $error));
		}
	
		$this->template->assign(array(
			'result_error' => $error,
		));
		$this->template->display('pay_convert.html');
	}

	// 条件查询
	private function _where($select, $param) {
		$select->where('uid', $this->uid);
		$param['kw'] && $select->like('id', (int)$param['kw']);
	}

	/**
     * 充值记录
     */
    public function index() {

		// 接收参数
		$total = (int)$this->input->get('total');

		// 查询结果
		$list = array();
		if (!$total) {
			$this->db->select('count(*) as total');
			$this->db->where('uid', $this->uid)->where('value>0');
			$this->input->get('kw') && $this->db->where('id', (int)$this->input->get('kw'));
			$data = $this->db->get('member_paylog')->row_array();
			$total = (int)$data['total'];
		}

		if ($total) {
			$page = max((int)$this->input->get('page'), 1);
			$this->db->where('uid', $this->uid)->where('value>0');
			$this->input->get('kw') && $this->db->where('id', (int)$this->input->get('kw'));
			$this->db->limit($this->pagesize, $this->pagesize * ($page - 1));
			$list = $this->db->order_by('inputtime DESC')->get('member_paylog')->result_array();
		}

		$this->template->assign(array(
			'list' => $list,
			'type' => $this->get_pay_api(1),
			'pages'	=> $this->get_member_pagination(dr_member_url($this->router->class.'/'.$this->router->method).'&total='.$total, $total),
			'page_total' => $total,
		));
		$this->template->display('pay_index.html');
	}
	
	/**
     * 消费记录
     */
    public function spend() {

		// 接收参数
		$total = (int)$this->input->get('total');

		// 查询结果
		$list = array();
		if (!$total) {
			$this->db->select('count(*) as total');
			$this->db->where('uid', $this->uid)->where('value<0');
			$this->input->get('kw') && $this->db->where('id', (int)$this->input->get('kw'));
			$data = $this->db->get('member_paylog')->row_array();
			$total = (int)$data['total'];
		}

		if ($total) {
			$page = max((int)$this->input->get('page'), 1);
			$this->db->where('uid', $this->uid)->where('value<0');
			$this->input->get('kw') && $this->db->where('id', (int)$this->input->get('kw'));
			$this->db->limit($this->pagesize, $this->pagesize * ($page - 1));
			$list = $this->db->order_by('inputtime DESC')->get('member_paylog')->result_array();
		}

		$this->template->assign(array(
			'list' => $list,
			'type' => $this->get_pay_api(1),
			'pages'	=> $this->get_member_pagination(dr_member_url($this->router->class.'/'.$this->router->method).'&total='.$total, $total),
			'page_total' => $total,
		));
		$this->template->display('pay_index.html');
	}
	
	/**
     * 经验值
     */
    public function experience() {

		$total = (int)$this->input->get('total');

		// 查询结果
		$list = array();
		if (!$total) {
			$this->db->select('count(*) as total');
			$this->db->where('uid', $this->uid)->where('type', 0);
			$data = $this->db->get('member_scorelog')->row_array();
			$total = (int)$data['total'];
		}

		if ($total) {
			$page = max((int)$this->input->get('page'), 1);
			$this->db->where('uid', $this->uid)->where('type', 0);
			$this->db->limit($this->pagesize, $this->pagesize * ($page - 1));
			$list = $this->db->order_by('inputtime DESC')->get('member_scorelog')->result_array();
		}

		$this->template->assign(array(
			'list' => $list,
			'pages'	=> $this->get_member_pagination(dr_member_url($this->router->class.'/'.$this->router->method).'&total='.$total, $total),
			'page_total' => $total,
		));
		$this->template->display('score.html');
	}
	
	/**
     * 虚拟币
     */
    public function score() {

		$total = (int)$this->input->get('total');

		// 查询结果
		$list = array();
		if (!$total) {
			$this->db->select('count(*) as total');
			$this->db->where('uid', $this->uid)->where('type', 1);
			$data = $this->db->get('member_scorelog')->row_array();
			$total = (int)$data['total'];
		}

		if ($total) {
			$page = max((int)$this->input->get('page'), 1);
			$this->db->where('uid', $this->uid)->where('type', 1);
			$this->db->limit($this->pagesize, $this->pagesize * ($page - 1));
			$list = $this->db->order_by('inputtime DESC')->get('member_scorelog')->result_array();
		}

		$this->template->assign(array(
			'list' => $list,
			'pages'	=> $this->get_member_pagination(dr_member_url($this->router->class.'/'.$this->router->method).'&total='.$total, $total),
			'page_total' => $total,
		));
		$this->template->display('score.html');
	}
}