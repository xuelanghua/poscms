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


<h3 class="page-title">
    <small>增加和修改操作之后必须要同步至微信公众平台服务端才能生效</small>
</h3>


<div class="portlet light bordered">
    <div class="portlet-body">
        <div class="table-scrollable v3table">
            <form action="" method="post" name="myform" id="myform">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?php echo fc_lang('名称'); ?></th>
                        <th width="100"><?php echo fc_lang('类型'); ?></th>
                        <th width="100"><?php echo fc_lang('粉丝数'); ?></th>
                        <th class="dr_option"><?php echo fc_lang('操作'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
                    <tr>
                        <td><?php echo $t['name']; ?></td>
                        <td><?php if ($t['wechat_id']==-1) { ?><span class="badge badge-warning"> 未同步 </span><?php } else if ($t['wechat_id']>99) { ?><span class="badge badge-success"> 自定义 </span><?php } else { ?><span class="badge badge-info"> 系统 </span><?php } ?></td>
                        <td><?php echo $t['count']; ?></td>
                        <td class="dr_option">
                            <?php if ($this->ci->is_auth('admin/wgroup/edit')) { ?><a class="aedit" href="<?php echo dr_dialog_url(dr_url('wgroup/edit',array('id' => $t['id'])), 'edit'); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php }  if ($t['wechat_id'] >=0 && $t['wechat_id'] <100) {  } else { ?><a class="adel" href="<?php echo dr_url('wgroup/del',array('id'=>$t['id'])); ?>"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a><?php } ?>
                        </td>
                    </tr>
                    <?php } } ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>