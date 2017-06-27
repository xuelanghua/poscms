<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	<?php if ($data['uid'] && !$data['third']) { ?>
	$.getScript("<?php echo $service; ?>?c=check&uid=<?php echo $data['uid']; ?>&key=<?php echo $data['key']; ?>");
	<?php }  if ($data['third']) { ?>
	$('.dr_1').show();$('.dr_0').hide();
	<?php } else { ?>
	$('.dr_0').show();$('.dr_1').hide();
	<?php } ?>
});
</script>
<form class="form-horizontal" action="" method="post" id="myform" name="myform">
    <div class="page-bar">
        <ul class="page-breadcrumb mylink">
            <?php echo $menu['link']; ?>
            <li> <a href="<?php echo dr_help_url(108); ?>" target="_blank"><i class="fa fa-book"></i> <?php echo fc_lang('在线帮助'); ?></a> </li>
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
        <small></small>
    </h3>
    <div class="portlet light bordered myfbody">

        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-green sbold uppercase"><?php echo fc_lang('账号设置'); ?></span>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="portlet-body form">

                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('类型'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input name="aa" type="radio" value="0" onclick="$('.dr_0').show();$('.dr_1').hide();" <?php if (!$data['third']) { ?>checked="checked"<?php } ?> /> <?php echo fc_lang('官方接口'); ?></label>
                                    <label class="radio-inline"><input name="aa" type="radio" value="1" onclick="$('.dr_1').show();$('.dr_0').hide();" <?php if ($data['third']) { ?>checked="checked"<?php } ?> /> <?php echo fc_lang('第三方接口'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group dr_0">
                            <label class="col-md-2 control-label"><?php echo fc_lang('申请教程'); ?>：</label>
                            <div class="col-md-9">
                                <div class="form-control-static"><label><a href="http://help.dayrui.com/107.html" target="_blank">http://help.dayrui.com/107.html</a></label></div>
                            </div>
                        </div>
                        <div class="form-group dr_0">
                            <label class="col-md-2 control-label">SMS Uid：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[uid]" value="<?php echo $data['uid']; ?>" ></label>
                            </div>
                        </div>
                        <div class="form-group dr_0">
                            <label class="col-md-2 control-label">SMS Key：</label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="data[key]" value="<?php echo $data['key']; ?>" >
                            </div>
                        </div>
                        <?php if ($data['uid'] && !$data['third']) { ?>
                        <div class="form-group dr_0">
                            <label class="col-md-2 control-label"><?php echo fc_lang('剩余短信'); ?>：</label>
                            <div class="col-md-9">
                                <div class="form-control-static" id="dr_sms">....</div>
                            </div>
                        </div>
                        <?php } ?>
                    <div class="form-group dr_1">
                        <label class="col-md-2 control-label"><?php echo fc_lang('申请教程'); ?>：</label>
                        <div class="col-md-9">
                            <div class="form-control-static"><label><a href="http://help.dayrui.com/108.html" target="_blank">http://help.dayrui.com/108.html</a></label></div>
                        </div>
                    </div>

                    <div class="form-group dr_1">
                        <label class="col-md-2 control-label"><?php echo fc_lang('字段格式'); ?>：</label>
                        <div class="col-md-9">
                            <textarea class="form-control" style="height:120px" name="data[third]" /><?php echo $data['third']; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label class="col-md-2 control-label"><?php echo fc_lang('短信签名'); ?>：</label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="data[note]" value="<?php echo $data['note']; ?>" ></label>
                            <span class="help-block">保持在10个字符以内</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
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