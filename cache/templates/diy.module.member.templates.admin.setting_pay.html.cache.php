<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
<?php if (IS_POST) { ?>
dr_tips("<?php echo fc_lang('操作成功，正在刷新...'); ?>", 3, 1);
<?php } ?>
</script>
<form class="form-horizontal" action="" method="post" id="myform" name="myform">
    <div class="page-bar">
        <ul class="page-breadcrumb mylink">
            <?php echo $menu['link']; ?>

        </ul>
        <ul class="page-breadcrumb myname">
            <?php echo $menu['name']; ?>
        </ul>
        <div class="page-toolbar">
            <div class="btn-group pull-right">
                <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false" data-hover="dropdown"> <?php echo fc_lang('操作菜单'); ?>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <?php if (is_array($menu['quick'])) { $count=count($menu['quick']);foreach ($menu['quick'] as $t) { ?>
                    <li>
                        <a href="<?php echo $t['url']; ?>"><?php echo $t['icon'];  echo $t['name']; ?></a>
                    </li>
                    <?php } } ?>
                    <li class="divider"> </li>
                    <li>
                        <a href="javascript:window.location.reload();">
                            <i class="icon-refresh"></i> <?php echo fc_lang('刷新页面'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <h3 class="page-title">
        <small><?php echo fc_lang('支付系统支持多个第三方支付平台'); ?></small>
    </h3>

    <div style="margin-bottom: 70px">
    <?php if (is_array($pay)) { $count=count($pay);foreach ($pay as $dir=>$t) { ?>
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-green sbold uppercase"><?php echo $t['name']; ?></span>
            </div>
        </div>
        <div class="portlet-body">
            <table width="100%" class="table_form">
                <tr>
                    <th width="200"><?php echo fc_lang('可用'); ?>：</th>
                    <td><input name="setting[use][]" type="checkbox" <?php if (@in_array($dir, $setting['use'])) { ?>checked="checked"<?php } ?> value="<?php echo $dir; ?>" /></td>
                </tr>
                <?php require WEBPATH.'api/pay/'.$dir.'/setting.php';?>
                <tr>
                    <th style="border:none"><?php echo fc_lang('显示顺序'); ?>：</th>
                    <td style="border:none"><input type="text" name="setting[order][<?php echo $dir; ?>]" class="input-text" size="10" value="<?php echo intval($setting['order'][$dir]); ?>" /></td>
                </tr>
            </table>
        </div>
    </div>
    <?php } } ?>
    </div>

    <div class="myfooter">
        <div class="row">
            <div class="portlet-body form">
                <div class="form-body">
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn green"> <i class="fa fa-save"></i> <?php echo fc_lang('保存'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>