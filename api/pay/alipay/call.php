<?php

$pay = require WEBPATH.'api/pay/alipay/config.php';

$alipay_config = array(
    'key' => $pay['key'],
    'partner' => $pay['id'],
    'sign_type' => 'MD5',
    'transport' => 'http',
    'return_url' => SITE_URL.'api/pay/alipay/return_url.php',
    'notify_url' => SITE_URL.'api/pay/alipay/notify_url.php',
    'seller_email' => $pay['username'],
    'input_charset' => 'utf-8',
);

if (DR_PAY_FILE == 'return') {
    require WEBPATH.'api/pay/alipay/alipay_notify.class.php';
    $alipayNotify = new AlipayNotify($alipay_config);
    if ($verify_result = $alipayNotify->verifyReturn()) {
        //验证成功
        $trade_no = $_GET['trade_no']; //获取支付宝交易号
        $total_fee = $_GET['total_fee']; //获取总价格
        $out_trade_no = $_GET['out_trade_no'];	//获取订单号
        if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
            $money = number_format($total_fee, 2, '.', '');
            $module = $this->pay_model->pay_success($out_trade_no, $money, '交易号：'.$trade_no);
            $this->pay_msg(
                "即时到帐支付成功($money)",
                SITE_URL.'index.php?s=member&c=pay&m=call&module='.$module,
                1
            );
        } else {
            $this->pay_msg('trade_status='.$_GET['trade_status']);
        }
    } else {
        //验证失败
        $this->pay_msg('验证失败');
    }
} else {
    require WEBPATH.'api/pay/alipay/alipay_notify.class.php';
    //计算得出通知验证结果
    $alipayNotify = new AlipayNotify($alipay_config);
    if ($verify_result = $alipayNotify->verifyNotify()) {
        //验证成功
        $trade_no = $_POST['trade_no']; //获取支付宝交易号
        $total_fee = $_POST['total_fee']; //获取总价格
        $out_trade_no = $_POST['out_trade_no']; //获取订单号
        $this->pay_model->pay_success($out_trade_no, number_format($total_fee, 2, '.', ''), '交易号：'.$trade_no);
        echo "success"; //请不要修改或删除
    } else {
        //验证失败
        echo "fail";
    }
}