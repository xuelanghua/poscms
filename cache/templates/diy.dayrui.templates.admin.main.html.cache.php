<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
    $(function(){
        $.getScript("<?php echo dr_url('home/mtotal'); ?>");
        // 检查是否更新
        <?php if ($member['adminid'] == 1) { ?>
        $.ajax({type: "GET", url:'http://www.poscms.net/index.php?c=fc&m=version&my=<?php echo DR_VERSION_ID; ?>', dataType:'jsonp',jsonp:"callback",
            success: function (data) {
                $("#finecms_version").html(data.html);
            }
        });
        <?php } ?>
    });
</script>

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?php echo dr_url('home/main'); ?>"><?php echo fc_lang('网站后台'); ?></a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a class="blue"><?php echo fc_lang('总览'); ?></a>
        </li>
    </ul>
    <div class="page-toolbar">
    </div>
</div>
<!-- END PAGE BAR -->

<!-- BEGIN PAGE TITLE-->
<h3 class="page-title">
    <small></small>
</h3>

<?php if ($ip) { ?>
<div class="alert alert-block alert-danger fade in">
    <button type="button" class="close" data-dismiss="alert"></button>
    <p> <?php echo $ip; ?> </p>
</div>
<?php } ?>
<dayrui>



<?php if (!is_file(WEBPATH.'config/license.php')) { ?>
<div class="alert alert-block alert-danger fade in">
    <button type="button" class="close" data-dismiss="alert"></button>
    <p> <a style="color:red" href="javascript:;" onclick="dr_tips('网站上线之后（本地或局域网可忽略），请在客户平台进行授权域名，并下载授权证书，放到网站的./config/目录之下', 999)">软件正版授权证书不存在，请上线之后立即处理，否则官方将终止您的账户...</a> </p>
</div>
<?php }  if ($fn_include = $this->_include("main2.html")) include($fn_include); ?>

<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart font-green-sharp" style="font-size: 20px;"></i>
                    <span class="caption-subject font-green-sharp  "><?php echo fc_lang('数据统计'); ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <div>
                    <table class="table mtable table-nomargin table-bordered table-striped table-bordered">
                        <thead>
                        <tr>
                            <th style="text-align:center"><?php echo fc_lang('模块'); ?></th>
                            <th style="text-align:center"><?php echo fc_lang('总数据'); ?></th>
                            <th style="text-align:center"><?php echo fc_lang('今日'); ?></th>
                            <th style="text-align:center"><?php echo fc_lang('审核'); ?></th>
                            <th style="text-align:center"><?php echo fc_lang('扩展审核'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (is_array($mtotal)) { $count=count($mtotal);foreach ($mtotal as $dir=>$t) { ?>
                        <tr>
                            <td style="text-align:center"><?php echo $t['name']; ?></td>
                            <td style="text-align:center"><a class="drlabel drlabel-success" href="<?php echo $t['content']; ?>" id="<?php echo $dir; ?>_content"><img src="<?php echo THEME_PATH; ?>admin/images/mloading.gif"></a></td>
                            <td style="text-align:center"><a class="drlabel drlabel-success" href="<?php echo $t['today']; ?>" id="<?php echo $dir; ?>_today"><img src="<?php echo THEME_PATH; ?>admin/images/mloading.gif"></a></td>
                            <td style="text-align:center"><a class="drlabel drlabel-important" href="<?php echo $t['content_verify']; ?>" id="<?php echo $dir; ?>_content_verify"><img src="<?php echo THEME_PATH; ?>admin/images/mloading.gif"></a></td>
                            <td style="text-align:center"><a class="drlabel drlabel-important" href="<?php echo $t['extend_verify']; ?>" id="<?php echo $dir; ?>_extend_verify"><img src="<?php echo THEME_PATH; ?>admin/images/mloading.gif"></a></td>
                        </tr>
                        <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cog font-red-sunglo" style="font-size: 20px;"></i>
                    <span class="caption-subject font-red-sunglo">系统信息</span>
                </div>
            </div>
            <div class="portlet-body">
                <div>
                    <table class="table table-light mtable">
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('授权域名'); ?>：</td>
                            <td>&nbsp;<a href="http://www.dayrui.com/license/domain/<?php echo $sqdomain; ?>" target="_blank"><?php echo $sqdomain; ?></a></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('程序内核'); ?>：</td>
                            <td>&nbsp;<a href="<?php echo dr_url('upgrade/index'); ?>"> POSCMS&nbsp;v<?php echo DR_VERSION; ?>&nbsp; <b style="color:red" id="finecms_version"></b> </a></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('服务器环境'); ?>：</td>
                            <td>&nbsp;<?php echo $server; ?> 、PHP<?php echo PHP_VERSION; ?> 、MySql<?php echo $sqlversion; ?></td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('上传最大值'); ?>：</td>
                            <td>&nbsp;<?php echo @ini_get("upload_max_filesize"); ?> </td>
                        </tr>
                        <tr>
                            <td class="mleft" align="right"><?php echo fc_lang('POST最大值'); ?>：</td>
                            <td>&nbsp;<?php echo @ini_get("post_max_size"); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-volume-down font-blue-hoki" style="font-size: 20px;"></i>
                    <span class="caption-subject font-blue-hoki"><?php echo fc_lang('通知提醒'); ?></span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="">
                        <a href="<?php echo $notice_url; ?>" class="active"> <?php echo fc_lang('全部'); ?> </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div >
                    <?php if (!$notice) { ?>
                    <div style="text-align: center"> <?php echo fc_lang('无任何提醒'); ?> </div>
                    <?php } else { ?>

                    <ul class="feeds">
                        <?php if (is_array($notice)) { $count=count($notice);foreach ($notice as $t) { ?>
                        <li>
                            <a href="<?php echo dr_url('notice/go', array('id' => $t['id'])); ?>">
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <?php if ($t['type'] =='system') { ?>
                                            <div class="label label-sm label-danger">
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                            <?php } else if ($t['type'] =='content') { ?>
                                            <div class="label label-sm label-warning">
                                                <i class="fa fa-th-large"></i>
                                            </div>
                                            <?php } else if ($t['type'] =='app') { ?>
                                            <div class="label label-sm label-info">
                                                <i class="fa fa-cloud"></i>
                                            </div>
                                            <?php } else if ($t['type'] =='member') { ?>
                                            <div class="label label-sm label-success">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc mdesc"> <?php echo $t['msg']; ?> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> <?php echo dr_date($t['inputtime'],'m-d'); ?> </div>
                                </div>
                            </a>
                        </li>
                        <?php } } ?>
                    </ul>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>


<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>