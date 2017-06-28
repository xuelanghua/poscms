<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<style>
    .page-content {
        background: #eef1f5;
    }
</style>
<link href="<?php echo THEME_PATH; ?>admin/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
<?php if ($member['groupid']==1) {  if ($regverify == 1) { ?>
    <div class="alert alert-danger">
        <strong>您的账号还未进行邮件审核，如果长时间未收到邮件，建议<a href="<?php echo dr_member_url('login/resend'); ?>">重发邮件</a></strong>
    </div>
    <?php } else if ($regverify == 3) { ?>
    <div class="alert alert-danger">
        <p><strong>&nbsp;输入短信验证码审核账号</strong></p>
        <div class="input-group" style="margin-top:10px ">
            <div class="input-icon">
                <i class="fa fa-phone"></i>
                <input id="randcode" class="form-control" type="text" name="text" placeholder="输入短信验证码">
            </div>
            <span class="input-group-btn">
                <button class="btn btn-success" type="button" onclick="dr_verify_sms()">
                    <i class="fa fa-arrow-right fa-fw"></i> 立即验证</button>
                <button class="btn btn-success" type="button" onclick="dr_send_sms()">
                    <i class="fa fa-phone fa-fw"></i> 重新获取</button>
            </span>
        </div>
    </div>
<script type="text/javascript">
    function dr_verify_sms() {
        $.post("<?php echo dr_member_url('login/verify_sms'); ?>", {randcode: $("#randcode").val()}, function(data){
            if (data.status == '1') {
                dr_tips('审核成功', 3, 1);
                setTimeout('window.location.reload()',2000);
            } else {
                dr_tips(data.code);
            }
        }, 'json');
    }
    function dr_send_sms() {
        $.post("<?php echo dr_member_url('login/sendsms'); ?>", function(data){
            if (data.status == '1') {
                dr_tips(data.code, 3, 1);
            } else {
                dr_tips(data.code);
            }
        }, 'json');
    }
</script>
    <?php } else { ?>
    <div class="alert alert-danger">
        <strong>您的账号还未审核通过，请等待系统审核</strong>
    </div>
    <?php }  } ?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-sidebar">
            <!-- PORTLET MAIN -->
            <div class="portlet light profile-sidebar-portlet ">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">
                    <a href="<?php echo dr_member_url('account/avatar'); ?>"><img src="<?php echo dr_avatar($uid, 90); ?>" class="img-responsive"></a>
                </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"> <?php echo $member['username']; ?> </div>
                    <div class="profile-usertitle-job"> <?php echo dr_show_stars($member['levelstars']); ?></div>
                </div>
                <!-- END SIDEBAR USER TITLE -->
                <!-- SIDEBAR BUTTONS -->
                <div class="profile-userbuttons">
                    <a  class="btn btn-circle green btn-sm" href="<?php echo dr_member_url('account/upgrade'); ?>">升级权限</a>
                    <a class="btn btn-circle red btn-sm" href="<?php echo dr_member_url('pay/add'); ?>">在线充值</a>
                </div>
                <!-- END SIDEBAR BUTTONS -->
                <!-- SIDEBAR MENU -->
                <div class="profile-usermenu" style="margin-top:23px;">
                    <ul class="nav">
                        <li class="">
                            <a href="<?php echo dr_member_url('account/permission'); ?>">
                                <i class="fa fa-users"></i>  会员组：<?php echo $member['groupname']; ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo dr_member_url('pay/score'); ?>">
                                <i class="fa fa-diamond"></i> <?php echo SITE_SCORE; ?>：<?php echo $member['score']; ?></a>
                        </li>
                        <li>
                            <a href="<?php echo dr_member_url('pay/experience'); ?>">
                                <i class="fa fa-compass"></i> <?php echo SITE_EXPERIENCE; ?>：<?php echo $member['experience']; ?></a>
                        </li>
                        <li>
                            <a href="<?php echo dr_member_url('pay/index'); ?>">
                                <i class="fa fa-rmb"></i> <?php echo SITE_MONEY; ?>：<?php echo $member['money']; ?> 元</a>
                        </li>
                    </ul>
                </div>
                <!-- END MENU -->
            </div>
            <!-- END PORTLET MAIN -->
        </div>
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light  ">
                        <div class="portlet-title">
                            <div class="caption">
                                <a href="<?php echo dr_member_url('notice/go'); ?>" class="caption-subject font-blue bold uppercase">
                                    <i class="fa fa-bell-o "></i> 通知提醒
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <ul class="feeds">
                                <?php $mm=array('index', 'member' ,'module', 'app');  $table = $ci->db->dbprefix.'member_notice_'.(int)$member['tableid'];  $sql = "select * from `".$table."` where uid=".$member['uid']." order by inputtime desc limit 4";  $return = $this->list_tag("action=sql sql='$sql'"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                                <li>
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-default">
                                                    <i class="fa <?php echo $ci->get_cache('member-menu', 'uri', 'notice/'.$mm[$t['type']], 'icon'); ?>"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"><?php echo $t['content']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> <?php echo dr_fdate($t['inputtime'], 'm-d'); ?> </div>
                                    </div>
                                </li>
                                <?php } } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light  ">
                        <div class="portlet-title">
                            <div class="caption">
                                <a href="<?php echo dr_member_url('account/login'); ?>" class="caption-subject font-blue bold uppercase">
                                    <i class="fa fa-calendar "></i> 登录记录
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <ul class="feeds">
                                <?php $return = $this->list_tag("action=sql sql='select * from @#member_login where uid=$uid order by logintime desc limit 4'"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>

                                <li>
                                    <div class="desc" style="padding:5px;">
                                        <?php echo dr_date($t['logintime'], NULL, 'red'); ?>

                                        <a href="http://www.baidu.com/baidu?wd=<?php echo $t['loginip']; ?>" target="_blank"><?php echo $ci->dip->address($t['loginip']); ?></a>

                                        <?php echo dr_strcut($t['useragent'], 60); ?>
                                    </div>
                                </li>
                                <?php } } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>


<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>