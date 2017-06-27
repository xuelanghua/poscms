<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	<?php if ($error) { ?>
	dr_tips("<?php echo $error; ?>");
	<?php } ?>
}); 
</script>
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
    <small></small>
</h3>

<form class="form-horizontal" action="" method="post" id="myform" name="myform">
    <div class="portlet light bordered myfbody">

        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-green sbold uppercase"><?php echo fc_lang('搜索'); ?></span>
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="portlet-body form">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('按表名称搜索'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['name']); ?>" name="data[name]" />
                                <span class="help-block"><?php echo fc_lang('必须是表的全名，如“dr_1_page”表示站点1的单页表'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('按表主键搜索'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo $data['id']; ?>" name="data[id]" />
                                <span class="help-block"><?php echo fc_lang('必须是整数格式，如“1”表示上面“dr_1_page”表中id=1的附件（必须配合上面的表名称搜索）'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('按扩展名搜索'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo $data['ext']; ?>" name="data[ext]" />
                                <span class="help-block"><?php echo fc_lang('多个扩展名以“,”分隔，如“jpg,gif,png,rar,zip”'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('按会员名搜索'); ?>：</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="<?php echo $data['author']; ?>" name="data[author]" />
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
                                <button type="submit" class="btn green"> <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>