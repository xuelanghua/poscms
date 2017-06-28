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

<div class="mytopsearch">
    <form method="post" class="row" action="" name="searchform" id="searchform">
        <input name="search" id="search" type="hidden" value="1" />
        <div class="col-md-12">
            <label>
                <select name="data[field]" class="form-control">
                    <?php if (is_array($field)) { $count=count($field);foreach ($field as $t) { ?>
                    <option value="<?php echo $t['fieldname']; ?>" <?php if ($param['field']==$t['fieldname']) { ?>selected<?php } ?>><?php echo $t['name']; ?></option>
                    <?php } } ?>
                </select>
            </label>
            <label><i class="fa fa-caret-right"></i></label>
            <label style="padding-right: 20px;"><input type="text" class="form-control" placeholder="" value="<?php echo $param['keyword']; ?>" name="data[keyword]" /></label>
            <label><?php echo fc_lang('时间段'); ?> ：</label>
            <label><?php echo dr_field_input('start', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'100')), (int)$param['start']); ?></label>
            <label><i class="fa fa-minus"></i></label>
            <label style="margin-right:10px"><?php echo dr_field_input('end', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'100')), (int)$param['end']); ?></label>
            <label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
        </div>
    </form>
</div>


<form action="" method="post" name="myform" id="myform">
    <div class="portlet mylistbody">
        <div class="portlet-body">
            <div class="table-scrollable">

                <table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

                <thead>
                <tr>
                    <?php if ($member['adminid']==1) { ?>
                    <th width="20" align="right"></th>
                    <?php } ?>
                    <th width="80"><?php echo fc_lang('类型'); ?></th>
                    <th><?php echo fc_lang('提醒内容'); ?></th>
                    <th width="150"><?php echo fc_lang('提醒时间'); ?></th>
                    <th width="100"><?php echo fc_lang('状态'); ?></th>
                    <th width="150"><?php echo fc_lang('处理人'); ?></th>
                    <th width="150"><?php echo fc_lang('处理时间'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
                <tr>
                    <?php if ($member['adminid']==1) { ?>
                    <td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
                    <?php } ?>
                    <td><?php if ($t['type'] =='system') { ?>
                        <span class="label label-sm label-danger"><?php echo fc_lang('系统'); ?></span>
                        <?php } else if ($t['type'] =='content') { ?>
                        <span class="label label-sm label-warning"><?php echo fc_lang('内容'); ?></span>
                        <?php } else if ($t['type'] =='app') { ?>
                        <span class="label label-sm label-info"><?php echo fc_lang('应用'); ?></span>
                        <?php } else if ($t['type'] =='member') { ?>
                        <span class="label label-sm label-success"><?php echo fc_lang('会员'); ?></span>
                        <?php } ?></td>
                    <td>
                        <a href="<?php echo dr_url('notice/go', array('id' => $t['id'])); ?>"><?php echo $t['msg']; ?></a></td>
                    <td><?php echo dr_date($t['inputtime'], NULL, 'red'); ?></td>
                    <td><?php if ($t['status'] ==0) { ?>
                        <span class="label label-sm label-danger"><?php echo fc_lang('未处理'); ?></span>
                    <?php } else if ($t['status'] ==1) { ?>
                        <span class="label label-sm label-warning"><?php echo fc_lang('已查看'); ?></span>
                    <?php } else if ($t['status'] ==2) { ?>
                        <span class="label label-sm label-info"><?php echo fc_lang('处理中'); ?></span>
                    <?php } else if ($t['status'] ==3) { ?>
                        <span class="label label-sm label-success"><?php echo fc_lang('处理完成'); ?></span>
                    <?php } ?>
                    </td>
                    <td><?php if ($t['uid']) { ?>
                        <a href="javascript:;" onclick="dr_dialog_member('<?php echo $t['uid']; ?>')"><?php echo dr_strcut($t['username'], 10); ?></a>
                        <?php } else { ?>
                        <a href="<?php echo dr_url('notice/go', array('id' => $t['id'])); ?>" class="btn btn-xs yellow"> <i class="fa fa-send"></i> <?php echo fc_lang('立即处理'); ?> </a>
                        <?php } ?></td>
                    <td><?php echo dr_date($t['updatetime'], NULL, 'red'); ?></td>
                </tr>
                <?php } }  if ($member['adminid']==1) { ?>
                <tr class="mtable_bottom">
                    <th align="right"  ><input class="toggle md-check" name="dr_select" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
                    <td colspan="33 ">
                        <button type="button" class="btn red btn-sm" name="option" onClick="dr_confirm_del_all()"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
</form>
<div id="pages"><a><?php echo fc_lang('共%s条', $param['total']); ?></a><?php echo $pages; ?></div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>