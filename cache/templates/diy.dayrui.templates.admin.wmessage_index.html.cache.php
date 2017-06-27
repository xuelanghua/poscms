<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
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
            <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <?php echo fc_lang('操作菜单'); ?>
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

<div class="mytopsearch">
    <form class="row" method="post" action="" name="searchform" id="searchform">
        <input name="search" id="search" type="hidden" value="1" />
        <div class="col-md-12">
            <label>发布时间 ：</label>
            <label><?php echo dr_field_input('start', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'100')), (int)$param['start']); ?></label>
            <label><i class="fa fa-minus"></i></label>
            <label style="margin-right:10px"><?php echo dr_field_input('end', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'100')), (int)$param['end']); ?></label>
            <label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
        </div>
    </form>
</div>

<style>
    .table>tbody>tr>td {
        vertical-align: middle;
    }
</style>

<div class="portlet light bordered">
    <div class="portlet-body">
        <div class="table-scrollable v3table">
            <form action="" method="post" name="myform" id="myform">
                <input name="action" value="del" type="hidden">
                <table class="table">
                    <thead>
                    <tr>
                        <th width="20" align="right"></th>
                        <th width="60">消息</th>
                        <th></th>
                        <th>发送时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
                    <tr>
                        <td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
                        <td><img width="50" height="50" src="<?php echo $t['headimgurl']; ?>"></td>
                        <td><?php echo dr_deal_emoji($t['nickname']); ?>: <?php echo $t['content']; ?></td>
                        <td><?php echo dr_date($t['inputtime']); ?></td>
                    </tr>
                    <?php } } ?>
                    <tr>
                        <th width="20" align="right" style="border:none;padding-top:15px;"><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" />&nbsp;</th>
                        <td colspan="99" align="left" style="border:none">
                            <label><button data-toggle="confirmation" id="dr_confirm_set_all" data-original-title="<?php echo fc_lang('您确定要这样操作吗？'); ?>" type="button" class="btn red btn-sm" name="option"> <i class="fa fa-trash"></i> 删除 </button></label>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div id="pages"><a><?php echo fc_lang('共%s条', $total); ?></a><?php echo $pages; ?></div>
        </div>
    </div>
</div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>