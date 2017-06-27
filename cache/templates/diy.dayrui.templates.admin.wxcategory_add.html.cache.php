<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
    <?php if ($error) { ?>
    dr_tips('<font color=red><?php echo $error['msg']; ?></font>', 3);
    d_tips('<?php echo $error['error']; ?>', 0);
    <?php }  if ($create) { ?>
    $.post('<?php echo $create; ?>&rand='+Math.random(),{ }, function(data){
    });
    <?php } ?>
    //每隔5秒保存表单数据
    <?php if (!$data['id'] && !$did) { ?>
    setInterval("dr_save_add_data()", 5000);
    <?php } ?>
    // 加载草稿
    $("#dr_cgbox").mouseover(function(){
        $(".dr_cgbox").show();
    }).mouseout(function(){
        $(".dr_cgbox").hide();
    });
    $(".dr_cgbox_select").click(function(){
        var did = $(this).attr("did");
        var islock = $(this).attr("islock");
        if (did != undefined && islock == "0") {
            window.location.href = '<?php echo $draft_url; ?>&did='+did;
        }
    });
    // 自动保存草稿
    <?php if ($did) { ?>
        setInterval("dr_save_draft_data()", 5000);
    <?php } ?>
});
// 动态保存草稿数据
function dr_save_draft_data() {
    $.ajax({
        type: "POST",
        dataType:"json",
        url: memberpath+'index.php?s=member&c=api&m=ajax_save_draft&sid=<?php echo SITE_ID; ?>&dir=<?php echo APP_DIR; ?>&did=<?php echo $did; ?>',
        data: $("#myform").serialize(),
        success: function(data) { },
        error: function(HttpRequest, ajaxOptions, thrownError) { }
    });
}
// 动态保存表单数据
function dr_save_add_data() {
    $.ajax({
        type: "POST",
        dataType:"json",
        url: memberpath+'index.php?s=member&c=api&m=ajax_save_add&dir=<?php echo APP_DIR; ?>_extend',
        data: $("#myform").serialize(),
        success: function(data) { },
        error: function(HttpRequest, ajaxOptions, thrownError) { }
    });
}
// 删除草稿数据
function delete_draft(did){
    $("#dr_row_cgbox_"+did).attr("islock", 1);
    var num = parseInt($("#dr_cg_nums").html());
    $.ajax({
        type: "POST",
        dataType:"json",
        url: memberpath+'index.php?s=member&c=api&m=ajax_delete_draft&sid=<?php echo SITE_ID; ?>&dir=<?php echo APP_DIR; ?>&did='+did,
        success: function(data) {
            $("#dr_cgbox_"+did).attr("islock", 0);
            if (data == did) {
                $("#dr_cgbox_"+did).remove();
                $("#dr_cg_nums").html(num - 1);
            } else {
                dr_tips(data);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) { }
    });
}
</script>
<form class="form-horizontal" action="" method="post" id="myform" name="myform">
<input name="action" id="dr_action" type="hidden" value="back" />
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

    <div class="row" style="margin-top:20px;margin-bottom: 50px;">
        <div class="col-md-9">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green sbold uppercase"><?php echo fc_lang('产品分类添加'); ?></span>
                    </div>
                    <?php if ($draft_list) { ?>
                    <div class="actions">
                        <div class="btn-group">
                            <a class="btn green-haze btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> <i class="fa fa-edit"></i> <?php echo fc_lang('草稿'); ?> <span class="badge badge-success" id="dr_cg_nums"><?php echo count($draft_list); ?></span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right" style="width:320px">
                                <?php if (is_array($draft_list)) { $count=count($draft_list);foreach ($draft_list as $t) { ?>
                                <li id="dr_cgbox_<?php echo $t['id']; ?>">
                                    <a href="javascript:;" class="dr_cgbox_select" id="dr_row_cgbox_<?php echo $t['id']; ?>" did="<?php echo $t['id']; ?>" islock="0"><?php if ($t['title']) {  echo $t['title'];  } else { ?>---<?php } ?> <span class="badge badge-s-danger" onclick="delete_draft('<?php echo $t['id']; ?>')"> <i class="fa fa-trash"></i> <?php echo dr_date($t['inputtime']); ?></span></a>
                                </li>
                                <?php } } ?>
                            </ul>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="portlet-body">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('名称'); ?>：</label>
                            <div class="col-md-9">
                                <div class="form-control-static"><?php echo $content['title']; ?></div>
                            </div>
                        </div>
                        <?php echo str_replace('col-md-9', 'col-md-10', $myfield); ?>
                    </div>
                </div>
            </div>

            <?php if ($ci->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'syn2')) { ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green sbold uppercase"><?php echo fc_lang('更多设置'); ?></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">
                        <?php if ($ci->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'syn2')) { ?>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('同步微博'); ?>：</label>
                            <div class="col-md-9">
                                <label><input type="checkbox" name="sina_share" value="1" <?php if (!$data['id']) { ?>checked="checked"<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small"> </label>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="col-md-3">
            <div class="portlet light bordered">
                <div class="portlet-body">
                    <div class="form-body">
                        <?php echo $sysfield; ?>
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
                                <button type="submit" class="btn blue" onclick="$('#dr_action').val('draft')"> <i class="fa fa-save"></i> <?php echo fc_lang('保存草稿'); ?></button>
                                <button type="submit" class="btn green" onclick="$('#dr_action').val('back')"> <i class="fa fa-save"></i> <?php echo fc_lang('保存并返回'); ?></button>
                                <?php if (!$data['id']) { ?>
                                <button type="submit" class="btn default" onclick="$('#dr_action').val('continue')"> <i class="fa fa-save"></i> <?php echo fc_lang('保存并继续'); ?></button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>