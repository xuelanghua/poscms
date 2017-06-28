<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<style>
    .uploadify-button {
        height: 30px;
        line-height: 30px;
        background-color: #3598DC;
        border: 1px solid #3598DC;
        border-radius: 5px;
        padding-top: -15px;
        text-align: center;
        /*vertical-align: middle;*/
        font-size: 15px;
        color: #FFF;
    }
    .uploadify:hover .uploadify-button {
        background-color: #217EBD;
    }

</style>
<link href="<?php echo THEME_PATH; ?>js/layer/laydate/need/layerDate.css" rel="stylesheet">

<script type="text/javascript">
$(function() {
    // 错误提示
    <?php if ($error) { ?>
    dr_tips('<?php echo $error['msg']; ?>', 3, '<?php echo intval($error['status']); ?>');
    <?php } ?>
    // 生成静态文件
    <?php if ($create) { ?>
    $.ajax({
        type: "GET",
        async: false,
        url:"<?php echo $create; ?>",
        dataType: "jsonp",
        success: function(json){ },
        error: function(){ }
    });
    $.ajax({
        type: "GET",
        async: false,
        url:"index.php?s=<?php echo APP_DIR; ?>&c=category&m=create_html&id=<?php echo $catid; ?>",
        dataType: "jsonp",
        success: function(json){ },
        error: function(){ }
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
    //
    <?php if ($data['syncatid']) { ?>
        $("#dr_syncat_num").html("<?php echo substr_count($data['syncatid'], '|') ?>");
        $("#dr_syncat_text").show();
    <?php } ?>
});
// 修改栏目时的页面跳转
function show_category_field(catid) {
    <?php if ($is_category_field) { ?>
    window.location.href = '<?php echo dr_url(APP_DIR."/home/".$ci->router->method, array("id"=>$data['id'])); ?>&catid='+catid;
    <?php } ?>
}
// 动态保存草稿数据
function dr_save_draft_data() {
    var catid = $('#dr_catid').val();
    $.ajax({
        type: "POST",
        dataType:"json",
        url: memberpath+'index.php?s=member&c=api&m=ajax_save_draft&sid=<?php echo SITE_ID; ?>&dir=<?php echo APP_DIR; ?>&did=<?php echo $did; ?>&catid='+catid,
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
        url: memberpath+'index.php?s=member&c=api&m=ajax_save_add&dir=<?php echo APP_DIR; ?>',
        data: $("#myform").serialize(),
        success: function(data) { },
        error: function(HttpRequest, ajaxOptions, thrownError) { }
    });
}
// 同步到其他栏目选择
function dr_syncat() {
    var url = "<?php echo dr_url(APP_DIR.'/home/syncat_ajax'); ?>&ids="+$("#dr_syncatid").val();
    art.dialog.open(url, { title: '<?php echo lang("cat-100"); ?>',
        ok: function () {
            var iframe = this.iframe.contentWindow;
            if (!iframe.document.body) {
                alert('iframe loading')
                return false;
            };
            var c = 0;
            var v = '';
            iframe.$("input[type='hidden']").each(function (i) {
                v+= $(this).val()+'|';
                c++;
            });
            $("#dr_syncatid").val(v);
            $("#dr_syncat_num").html(c);
            $("#dr_syncat_text").show();
            return true;
        },
        cancel: true
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
    <input name="page" id="page" type="hidden" value="<?php echo $page; ?>" />
    <input name="action" id="dr_action" type="hidden" value="back" />
    <input name="dr_id" id="dr_id" type="hidden" value="<?php echo $data['id']; ?>" />
    <input name="dr_module" id="dr_module" type="hidden" value="<?php echo APP_DIR; ?>" />
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
                        <span class="caption-subject font-green sbold uppercase"><?php echo fc_lang('基本内容'); ?></span>
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
                            <label class="col-md-2 control-label"><?php echo fc_lang('排序'); ?>：</label>
                            <div class="col-md-4">
                            	<input type="text" class="form-control" name="displayorder" />
                            </div>
                        </div>
						 <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('分类标题'); ?>：</label>
                            <div class="col-md-4">
                            	<input type="text" class="form-control" name="title" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('缩略图'); ?>：</label>
							<div class="col-xs-8">
                                <input type="hidden" name="thumb" id = "adpath" value="" />
                                <input type="hidden" name="id"  value="" />
                                <input name="name" id="upload_image" type="file" value="">
                            </div>
                        </div>
                         <!-- <div class="form-group" id="preload">
                            <label class="col-xs-3 control-label">预览照片：</label>
                            <div class="col-xs-8" id="picture" style="text-align: left;">
                                <?php if ($infos['cimge']) { ?>
                                <img src="<?php echo $userHeadimg; ?>" style="width:200px;height:200px" />
                                <else/>
                                <img src="<?php echo $infos['cimge']; ?>" style="width:200px;height:200px" />
                                <?php } ?>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('分类描述'); ?>：</label>
                            <div class="col-md-8">
                            	<textarea name="desc" id="" cols="150" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('是否显示'); ?>：</label>
                           	<div class="radio-list">
	                           	<label class="radio-inline">
								  <input type="radio" name="status" value="1"> 显示
								</label>
								<label class="radio-inline">
								  <input type="radio" name="status" value="0"> 隐藏
								</label>
							</div>
						</div>
                        <!-- <?php echo str_replace('col-md-9', 'col-md-10', $myfield); ?> -->
                    </div>
                </div>
            </div>

            <?php if ($flag || (!$data['id'] && $ci->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'syn2'))) { ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green sbold uppercase"><?php echo fc_lang('更多设置'); ?></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">
                        <?php if ($flag) { ?>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('推荐位'); ?>：</label>
                            <div class="col-md-9">
                                <div class="checkbox-list">
                                    <?php if (is_array($flag)) { $count=count($flag);foreach ($flag as $i=>$t) {  if ($t['name']) { ?>
                                    <label class="checkbox-inline"><input name="flag[]" type="checkbox" <?php if (@in_array($i, $myflag)) { ?>checked="checked" <?php } ?>value="<?php echo $i; ?>" /> <?php echo $t['name']; ?> </label>
                                    <?php }  } } ?>
                                </div>
                            </div>
                        </div>
                        <?php }  if ($ci->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'setting', 'syn2')) { ?>
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
                                <!--dayrui.com 测试有bug<label><button type="button" class="btn red" name="option" onClick="$('#action').val('timing');"> <i class="fa fa-save"></i> <?php echo fc_lang('定时发布'); ?></button></label>-->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="<?php echo THEME_PATH; ?>js/layer/laydate/laydate.js"></script>
<script src="<?php echo THEME_PATH; ?>js/uploadify/jquery.uploadify.min.js?v=1.0.0"></script>
<script>
$(function(){
    alert(1233);
    var headImg = {
        filePath: null
    };
    $('#upload_image').uploadify({
        "height" : 30,
        "swf" : "<?php echo THEME_PATH; ?>js/uploadify/uploadify.swf",
        "fileObjName" : "download",
        "buttonText" : "上传图片",
        "fileSizeLimit" : '1MB',
        "uploader" : 'http://localhost/index.php?s=member&c=api&m=upload_image',
        "width" : 80,
        "height": 30,
        'removeTimeout' : 1,
        'fileTypeExts' : '*.jpg; *.png; *.gif;',
        "onUploadSuccess" : function(file, data) {
            //console.log(this);
            // var data = $.parseJSON(data);
            console.log(data);
            console.log(file);
            console.log('data');
            // if(data.code == 1){
            //     var filePath = '<?php echo SYS_UPLOAD_PATH; ?>/'+date('Ym', SYS_TIME)+'/'+data.name;
            //     alert(filePath);
            //     headImg.filePath = filePath;
            //     $('#picture img').attr('src',filePath);
            //                         $("#adpath").val(filePath);
            //     $('#preload').css('display','block');
            // }else {
            //     // parent.layer.alert(data.name, 0, function() {});
            //     alert('13216');
            // }
        },
        'onFallback' : function() {
            // parent.layer.alert('未检测到兼容版本的Flash.', 0, function() {
            //     parent.layer.closeAll();
            // });
            alert('未检测到兼容版本的Flash');
        }
                    
    });
    function close(){
        // parent.layer.closeAll();
    }
    // var start = {
    //     elem: "#work_time",
    //     format: "YYYY/MM/DD hh:mm:ss",
    //     min: laydate.now(),
    //     min: '1900-01-01 00:00:00',
    //             max: "2099-06-16 23:59:59",
    //     istime: true,
    //     istoday: false,
    //     choose: function(datas) {
    //         end.min = datas;
    //         end.start = datas
    //     }
    // };
    // laydate(start);
});
</script>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>