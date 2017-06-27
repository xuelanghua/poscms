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

require WEBPATH.'api/pay/alipay/wap/alipay_notify.class.php';

if (DR_PAY_FILE == 'return') {
    //计算得出通知验证结果
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyReturn();
    if($verify_result) {
        //验证成功
        $trade_no = $_GET['trade_no']; //获取支付宝交易号
        $total_fee = $_GET['total_fee']; //获取总价格
        $out_trade_no = $_GET['out_trade_no'];	//获取订单号
        $money = number_format($total_fee, 2, '.', '');
        $module = $this->pay_model->pay_success($out_trade_no, $money, '交易号：'.$trade_no);
        $this->pay_msg(
            "支付成功",
            SITE_URL.'index.php?s=member&c=pay&m=call&module='.$module,
            1
        );
    } else {
        $this->pay_msg('验证失败');
    }
} else {
    //计算得出通知验证结果
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyNotify();
    if($verify_result) {
        //验证成功

        //解密（如果是RSA签名需要解密，如果是MD5签名则下面一行清注释掉）
        $notify_data = $alipayNotify->decrypt($_POST['notify_data']);
        //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        $doc = new DOMDocument();
        $doc->loadXML($notify_data);
        if (!empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue)) {
            //验证成功
            $trade_no =$doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue; //获取支付宝交易号
            $total_fee = $doc->getElementsByTagName( "total_fee" )->item(0)->nodeValu; //获取总价格
            $out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue; //获取订单号
            $this->pay_model->pay_success($out_trade_no, number_format($total_fee, 2, '.', ''), '交易号：'.$trade_no);
            echo "success"; //请不要修改或删除
        }
    } else {
        //验证失败
        echo "fail";
        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
}