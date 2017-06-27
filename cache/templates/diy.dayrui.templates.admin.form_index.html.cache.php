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
	<small><?php echo fc_lang('表单可以用于前端数据展示与收集，如留言板、反馈、证书展示，需要配合前端页面使用'); ?></small>
</h3>
<div class="portlet mylistbody">
	<div class="portlet-body">
		<div class="table-scrollable">

			<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

		<thead>
		<tr>
			<th width="70"><?php echo fc_lang('Id'); ?></th>
			<th><?php echo fc_lang('名称'); ?></th>
			<th ><?php echo fc_lang('表名称'); ?></th>
			<th><?php echo fc_lang('提交页模板'); ?></th>
			<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
		<tr>
			<td><?php echo $t['id']; ?></td>
			<td>
            <?php if ($this->ci->is_auth('admin/form/edit')) { ?>
            <a href="<?php echo dr_url('form/edit', array('id' => $t['id'])); ?>"><?php echo $t['name']; ?></a>
            <?php } else {  echo $t['name'];  } ?>
            </td>
			<td><?php echo $t['table']; ?></td>
			<td>form_<?php echo $t['table']; ?>.html</td>
			<td class="dr_option">
			<a class="alist" href="javascript:;" onClick="dr_dialog_show('<?php echo fc_lang('生成表单'); ?>', '<?php echo dr_url('form/toform', array('id'=>$t['id'])); ?>')"> <i class="fa fa-share-square-o"></i> <?php echo fc_lang('生成表单'); ?></a>
			<a class="ago" href="<?php echo SITE_URL; ?>index.php?c=form_<?php echo $t['table']; ?>" target="_blank"> <i class="fa fa-send"></i> <?php echo fc_lang('发布预览'); ?></a>
			<?php if ($this->ci->is_auth('admin/form/listc')) { ?><a class="alist" href="<?php echo dr_url('admin/form_'.$t['table'].'/index'); ?>"> <i class="fa fa-navicon"></i> <?php echo fc_lang('内容维护'); ?></a><?php }  if ($this->ci->is_auth('admin/field/index')) { ?><a class="aadd" href="<?php echo dr_url('admin/field/index', array('rname'=>'form-'.SITE_ID, 'rid'=>$t['id'])); ?>"> <i class="fa fa-plus-square"></i> <?php echo fc_lang('字段'); ?></a><?php }  if ($this->ci->is_auth('admin/form/edit')) { ?><a class="aedit" href="<?php echo dr_url('form/edit', array('id'=>$t['id'])); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php }  if ($this->ci->is_auth('admin/form/del')) { ?><a class="adel" href="javascript:;" onClick="return dr_confirm_url('<?php echo fc_lang('您确定要这样操作吗？'); ?>','<?php echo dr_url('form/del', array('id'=>$t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a><?php } ?>
            </td>
		</tr>
		<?php } } ?>
		</tbody>
		</table>
		</div>
	</div>
</div>

<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>