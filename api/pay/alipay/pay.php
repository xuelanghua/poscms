<?php

$pay = require WEBPATH.'api/pay/alipay/config.php';

$alipay_config['key'] = $pay['key'];
$alipay_config['partner'] = $pay['id'];
$alipay_config['sign_type'] = 'MD5';
$alipay_config['transport'] = 'http';
$alipay_config['return_url'] = SITE_URL.'api/pay/alipay/return_url.php';
$alipay_config['notify_url'] = SITE_URL.'api/pay/alipay/notify_url.php';
$alipay_config['input_charset']= 'utf-8';
$alipay_config['seller_email'] = $pay['username'];


if ($this->mobile && $pay['wap']) {
    // 手机接口

    $alipay_config['return_url'] = SITE_URL.'api/pay/alipay/wap_return_url.php';
    $alipay_config['notify_url'] = SITE_URL.'api/pay/alipay/wap_notify_url.php';

    require_once(WEBPATH.'api/pay/alipay/wap/alipay_submit.class.php');

    /**************************调用授权接口alipay.wap.trade.create.direct获取授权码token**************************/
    //返回格式
    $format = "xml";
    $v = "2.0";
    $req_id = date('Ymdhis').rand(0, 9999);
    //必填，须保证每次请求都是唯一

    //**req_data详细信息**
    $notify_url = $alipay_config['notify_url'];
    $call_back_url = $alipay_config['return_url'];
    $merchant_url = SITE_URL;
    $seller_email = $alipay_config['seller_email'];
    $out_trade_no = $sn;
    $subject = $title;
    $total_fee = $money;
    $req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . $seller_email . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';

    /************************************************************/

    //构造要请求的参数数组，无需改动
    $para_token = array(
        "service" => "alipay.wap.trade.create.direct",
        "partner" => trim($alipay_config['partner']),
        "sec_id" => trim($alipay_config['sign_type']),
        "format"	=> $format,
        "v"	=> $v,
        "req_id"	=> $req_id,
        "req_data"	=> $req_data,
        "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
    );

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);

    $html_text = $alipaySubmit->buildRequestHttp($para_token);

    //URLDECODE返回的信息
    $html_text = urldecode($html_text);

    //解析远程模拟提交后返回的信息
    $para_html_text = $alipaySubmit->parseResponse($html_text);

    //获取request_token
    $request_token = $para_html_text['request_token'];


    /**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/

    $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';

    //构造要请求的参数数组，无需改动
    $parameter = array(
        "service" => "alipay.wap.auth.authAndExecute",
        "partner" => trim($alipay_config['partner']),
        "sec_id" => trim($alipay_config['sign_type']),
        "format"	=> $format,
        "v"	=> $v,
        "req_id"	=> $req_id,
        "req_data"	=> $req_data,
        "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
    );

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);
    $result['form'] = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');

} else {
    // PC 接口

    require WEBPATH.'api/pay/alipay/alipay_submit.class.php';
    require WEBPATH.'api/pay/alipay/alipay_service.class.php';

    /**************************请求参数**************************/

    //构造要请求的参数数组
    $parameter = array(
        'service'			=> 'create_direct_pay_by_user',
        'payment_type'    => '1',

        'partner'			=> trim($alipay_config['partner']),
        '_input_charset'	=> trim(strtolower($alipay_config['input_charset'])),
        'seller_email'	=> trim($alipay_config['seller_email']),
        'return_url'		=> trim($alipay_config['return_url']),
        'notify_url'		=> trim($alipay_config['notify_url']),

        'out_trade_no'	=> $sn,
        'subject'			=> $title,
        'body'				=> fc_lang('会员(%s)支付订单ID：%s', $this->member['username'], $id),
        'total_fee'		=> $money,
    );

    //构造即时到帐接口
    $alipayService = new AlipayService($alipay_config);
    $result['form'] = $alipayService->create_direct_pay_by_user($parameter);
}