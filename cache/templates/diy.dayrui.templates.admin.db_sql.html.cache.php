<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
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

<div class="portlet light bordered">
    <div class="portlet-body">
        <form action="" method="post" id="myform" name="myform">
            <div class="form-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="radio-list">
                            <lable class="radio-inline"><input name="id" type="radio" <?php if (!$id) { ?>checked<?php } ?> value="0" /> <?php echo fc_lang('默认'); ?></lable>
                            <?php if (is_array($ci->site_info)) { $count=count($ci->site_info);foreach ($ci->site_info as $i=>$t) { ?>
                            <lable class="radio-inline"><input name="id" type="radio" <?php if ($id && $i == $id) { ?>checked<?php } ?> value="<?php echo $i; ?>" /> <?php echo $t['SITE_NAME']; ?></lable>
                            <?php } } ?>
                        </div>
                    </div>
                </div>

                <div class="form-group row" style="margin: 0">
                    <div class="col-md-12">
                        <textarea class="form-control" style="height:150px" name="sql" /><?php echo $sql; ?></textarea>
                    </div>
                </div>
            </div>
            <table width="100%" class="table_form">
            <tr>
                <td>
                    <button type="submit" name="submit" class="btn green btn-sm"> <i class="fa fa-database"></i> <?php echo fc_lang('执行SQL'); ?></button>
                </td>
            </tr>
            <?php if (IS_POST) { ?>
            <tr>
                <td>
                <?php echo fc_lang('共执行<b>%s</b>条SQL语句', $mcount); ?>
                </td>
            </tr>
            <?php if ($result) { ?>
            <tr>
                <td>
                <pre><?php print_r($result); ?></pre>
                </td>
            </tr>
            <?php }  } ?>
        </table>
        </form>
    </div>
</div>

<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>