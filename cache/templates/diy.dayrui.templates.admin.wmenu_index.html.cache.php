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
    <small>
        <h3 class="page-title">
            <small>
                自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单，菜单最多4个汉字，多出来的部分将会以“...”代替。
            </small>
        </h3>
    </small>
</h3>

<style>
    .table>tbody>tr>td {
        vertical-align: middle;
    }
</style>

<div class="portlet light bordered">
    <div class="portlet-body">
        <div class="table-scrollable v3table">
            <form action="" method="post" name="myform" id="myform">
                <input name="action" id="action" type="hidden" value="order" />
                <table class="table" width="100%">
                    <thead>
                    <tr>
                        <th width="20" align="right"></th>
                        <th width="30" align="center"><?php echo fc_lang('排序'); ?></th>
                        <th width="200" align="left">菜单名称</th>
                        <th style="text-align: center"></th>
                        <th align="left" class="dr_option"><?php if ($this->ci->is_auth('admin/wmenu/add')) { ?><a class="add" title="<?php echo fc_lang('添加'); ?>" href="<?php echo dr_url('wmenu/add', array('pid'=>0)); ?>"></a><?php } ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (is_array($list)) { $count=count($list);foreach ($list as $c) { ?>
                    <tr>
                        <td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $c['id']; ?>"></td>
                        <td align="center">
                            <input class="input-text displayorder" type="text" name="data[<?php echo $c['id']; ?>][displayorder]" value="<?php echo $c['displayorder']; ?>"></td>
                        <td><a href="<?php echo dr_url('wmenu/edit', array('id'=>$c['id'])); ?>"><?php echo $c['name']; ?></a></td>
                        <td align="center"><?php if (!$c['data']) { ?><span class="badge badge-success"><?php echo $type[$c['type']]; ?></span><?php } ?></td>
                        <td align="left" class="dr_option">
                            <a class="aedit" href="<?php echo dr_url('wmenu/edit', array('id'=>$c['id'])); ?>"> <i class="fa fa-edit"></i> 修改菜单</a>
                            <a class="aadd" href="<?php echo dr_url('wmenu/add', array('pid'=>$c['id'])); ?>"> <i class="fa fa-plus"></i> 添加下级</a>
                        </td>
                    </tr>
                    <?php if (is_array($c['data'])) { $count=count($c['data']);foreach ($c['data'] as $t) { ?>
                    <tr>
                        <td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>"></td>
                        <td align="center">
                            <input class="input-text displayorder" type="text" name="data[<?php echo $t['id']; ?>][displayorder]" value="<?php echo $t['displayorder']; ?>"></td>
                        <td>&nbsp; ├─&nbsp;&nbsp;<a href="<?php echo dr_url('wmenu/edit', array('id'=>$t['id'])); ?>"><?php echo $t['name']; ?></a></td>
                        <td align="center"><span class="badge badge-success"><?php echo $type[$t['type']]; ?></span></td>
                        <td align="left" class="dr_option">
                            <a  class="aedit"href="<?php echo dr_url('wmenu/edit', array('id'=>$t['id'])); ?>"> <i class="fa fa-edit"></i> 修改菜单</a>
                        </td>
                    </tr>
                    <?php } }  } } ?>
                    <tr>
                        <th align="right" style="padding-top:11px;"><input name="dr_select" class=" toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
                        <td colspan="7" align="left">
                            <?php if ($this->ci->is_auth('admin/wmenu/del')) { ?><button type="button" class="btn red btn-sm" value="<?php echo fc_lang('删除'); ?>" name="button" onClick="$('#action').val('del');return dr_confirm_del_all()"> <i class="fa fa-trash"></i> 删除</button><?php }  if ($this->ci->is_auth('admin/wmenu/edit')) { ?><button type="button" class="btn green btn-sm" value="<?php echo fc_lang('排序'); ?>" name="button" onclick="$('#action').val('order');return dr_confirm_del_all()"> <i class="fa fa-edit"></i> 排序</button><div class="onShow"><?php echo fc_lang('排序按从小到大排列，最大支持99'); ?></div><?php } ?>
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