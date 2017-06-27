<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
date_default_timezone_set("Asia/chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");

// 验证用户
if (!$this->uid) {
    echo json_encode(array('state'=> fc_lang('会话超时，请重新登录')));exit;
}
// 是否允许上传附件
if (!$this->member['adminid'] && !$this->member_rule['is_upload']) {
    echo json_encode(array('state'=> fc_lang('您的会员组无权上传附件')));exit;
}
// 附件总大小判断
if (!$this->member['adminid'] && $this->member_rule['attachsize']) {
    $data = $this->db->select_sum('filesize')->where('uid', $this->uid)->get('attachment')->row_array();
    $filesize = (int)$data['filesize'];
    if ($filesize > $this->member_rule['attachsize'] * 1024 * 1024) {
        echo json_encode(array('state'=> dr_lang('附件空间不足！您的附件总空间%s，现有附件%s', $this->member_rule['attachsize'].'MB', dr_format_file_size($filesize))));exit;
    }
}

// 上传目录
define('DR_UE_PATH', SYS_UPLOAD_PATH);
if (!is_dir(SYS_UPLOAD_PATH)) {
    dr_mkdirs(SYS_UPLOAD_PATH);
}

$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("config.json")), true);
$action = $_GET['action'];

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    case 'uploadvideo':
    /* 上传文件 */
    case 'uploadfile':
        $result = include("action_upload.php");
        break;

    /* 列出图片 */
    case 'listimage':
        $result = include("action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include("action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include("action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));
    }
} else {
    echo $result;
}