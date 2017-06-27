<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>

<link href="<?php echo THEME_PATH; ?>admin/global/plugins/codemirror/theme/neat.css" rel="stylesheet" type="text/css" />
<link href="<?php echo THEME_PATH; ?>admin/global/plugins/codemirror/lib/codemirror.css" rel="stylesheet" type="text/css" />

<script src="<?php echo THEME_PATH; ?>admin/global/plugins/codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/codemirror/mode/htmlmixed/htmlmixed.js" type="text/javascript"></script>
<script src="<?php echo THEME_PATH; ?>admin/global/plugins/codemirror/mode/css/css.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        CodeMirror.fromTextArea(document.getElementById("system_file"), {
            lineNumbers: !0,
            matchBrackets: !0,
            styleActiveLine: !0,
            theme: "neat",
            mode: "javascript"
        })
        CodeMirror.fromTextArea(document.getElementById("system_file2"), {
            lineNumbers: !0,
            matchBrackets: !0,
            styleActiveLine: !0,
            theme: "neat",
            mode: "javascript"
        })
    });
</script>
<div class="page-bar">
    <ul class="page-breadcrumb mylink">
        <?php echo $menu['link']; ?>

        <li> <a href="<?php echo dr_help_url(2108); ?>" target="_blank"><i class="fa fa-book"></i> <?php echo fc_lang('在线帮助'); ?></a> </li>
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
    <small><?php echo fc_lang('缓存目录请设置为禁止外部访问的权限'); ?></small>
</h3>

<form class="form-horizontal" action="" method="post" id="myform" name="myform">
    <input name="page" id="mypage" type="hidden" value="<?php echo $page; ?>" />
    <div class="portlet light bordered myfbody">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs" style="float:left;">
                <li class="<?php if ($page==0) { ?>active<?php } ?>">
                    <a href="#tab_0" data-toggle="tab" onclick="$('#mypage').val('0')"> <i class="fa fa-cog"></i> <?php echo fc_lang('高级分离Beta'); ?> </a>
                </li>
                <li class="<?php if ($page==1) { ?>active<?php } ?>">
                    <a href="#tab_1" data-toggle="tab" onclick="$('#mypage').val('1')"> <i class="fa fa-upload"></i> <?php echo fc_lang('附件配置'); ?> </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">

                <div class="tab-pane <?php if ($page==0) { ?>active<?php } ?>" id="tab_0">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('核心目录'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo FCPATH; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('缓存目录'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo CACHEPATH; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('系统风格目录'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo THEME_PATH; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('系统模板目录'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo TPLPATH; ?>">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="tab-pane <?php if ($page==1) { ?>active<?php } ?>" id="tab_1">
                    <div class="form-body">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php echo fc_lang('附件归档'); ?>：</label>
                                <div class="col-md-9">
                                    <input type="checkbox" name="data[SYS_ATTACHMENT_DB]" value="TRUE" <?php if ($data['SYS_ATTACHMENT_DB']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                    <span class="help-block"><?php echo fc_lang('开启之后附件将分为已使用的附件和未使用的附件，归档存储'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php echo fc_lang('系统附件目录'); ?>：</label>
                                <div class="col-md-9">
                                    <input class="form-control input-xlarge" type="text" name="data[SYS_ATTACHMENT_DIR]" value="<?php echo $data['SYS_ATTACHMENT_DIR']; ?>" >
                                    <span class="help-block"><?php echo fc_lang('并非上传目录，是供管理员浏览附件的目录，相对于系统根目录'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php echo fc_lang('缩略图片目录'); ?>：</label>
                                <div class="col-md-9">
                                    <input class="form-control input-xlarge" type="text" name="data[SYS_THUMB_DIR]" value="<?php echo $data['SYS_THUMB_DIR']; ?>" >
                                    <span class="help-block"><?php echo fc_lang('指定图片缩略图目录，相对于系统根目录，默认api/thumb/'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php echo fc_lang('附件上传目录'); ?>：</label>
                                <div class="col-md-9">
                                    <input class="form-control input-xlarge" type="text" name="data[SYS_UPLOAD_DIR]" value="<?php echo $data['SYS_UPLOAD_DIR']; ?>" >
                                    <span class="help-block"><?php if ($is_upload) {  echo fc_lang('此目录必须有读写权限，绝对路径请以“/”开头');  } else { ?><font color="red">此目录不存在</font><?php } ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php echo fc_lang('附件URL地址'); ?>：</label>
                                <div class="col-md-9">
                                    <input class="form-control input-xlarge" type="text" name="data[SYS_ATTACHMENT_URL]" value="<?php echo $data['SYS_ATTACHMENT_URL']; ?>" >
                                    <span class="help-block"><?php echo fc_lang('可以为附件目录指定域名，用于分离附件，留空表示默认本站地址（站外保存时必须指定域名）'); ?></span>
                                </div>
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
    </div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>