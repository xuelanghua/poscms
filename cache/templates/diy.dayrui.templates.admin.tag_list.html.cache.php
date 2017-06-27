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
        <?php if ($pid) { ?>
        <div class="col-md-12 col-sm-12" style="padding-top:2px">
            <label><a class="btn red btn-sm" href="<?php echo dr_url('tag/add', array('pid'=>$pid)); ?>"> <i class="fa fa-plus"></i> <?php echo fc_lang('添加子词'); ?></a></label>
            <label><a class="btn blue btn-sm" href="<?php echo dr_url('tag/all_add', array('pid'=>$pid)); ?>"> <i class="fa fa-plus"></i> <?php echo fc_lang('批量子词'); ?></a></label>
        </div>
        <?php } else { ?>
        <div class="col-md-12">
            <label>
                <select name="data[field]" class="form-control">
                    <?php if (is_array($field)) { $count=count($field);foreach ($field as $t) {  if ($t['ismain'] && $t['fieldname'] != 'inputtime') { ?>
                    <option value="<?php echo $t['fieldname']; ?>" <?php if ($param['field']==$t['fieldname']) { ?>selected<?php } ?>><?php echo $t['name']; ?></option>
                    <?php }  } } ?>
                </select>
            </label>
            <label><i class="fa fa-caret-right"></i></label>
            <label style="padding-right: 20px;"><input type="text" class="form-control" value="<?php echo $param['keyword']; ?>" name="data[keyword]" /></label>
            <label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
        </div>
        <?php } ?>
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
                <table class="table ">
                    <thead>
                    <tr class="heading">
                        <th class="myselect">

                        </th>
                        <th width="200" class="<?php echo ns_sorting('name'); ?>" name="name"><?php echo fc_lang('名称'); ?></th>
                        <th width="150" class="<?php echo ns_sorting('code'); ?>" name="code"><?php echo fc_lang('别名'); ?></th>
                        <th width="100" class="<?php echo ns_sorting('hits'); ?>" name="hits"><?php echo fc_lang('点击量'); ?></th>
                        <th><?php echo fc_lang('操作'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
                    <tr class="odd gradeX" id="dr_row_<?php echo $t['id']; ?>">
                        <td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
                        <td><?php echo $t['name']; ?></td>
                        <td><?php echo $t['code']; ?></td>
                        <td><?php echo $t['hits']; ?></td>
                        <td>
                            <?php if (!$pid) { ?>
                            <label><a href="<?php echo dr_url('tag/index', array('pid'=>$t['id'])); ?>" class="btn btn-xs dark"> <i class="fa fa-tag"></i> <?php echo fc_lang('子词管理（%s）', $t['total']); ?></a></label>
                            <?php } ?>
                            <label><a href="<?php echo dr_url('tag/edit', array('id'=>$t['id'])); ?>" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a></label>

                            <label><a class="btn btn-xs yellow" href="<?php echo dr_url_prefix(dr_tags_url($t['code'])); ?>" target="_blank"> <i class="fa fa-send"></i> <?php echo fc_lang('访问'); ?></a></label>

                        </td>
                    </tr>
                    <?php } } ?>
                    <tr>
                        <th width="20" align="right" style="border:none;padding-top:15px;"><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" />&nbsp;</th>
                        <td colspan="99" align="left" style="border:none">
                            <label><button data-toggle="confirmation" id="dr_confirm_set_all" data-original-title="<?php echo fc_lang('您确定要这样操作吗？'); ?>" type="button" class="btn red btn-sm" name="option"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?> </button></label>

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