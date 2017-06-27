<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {

	var _id = window.location.hash;
	if (_id && $(_id).length > 0) {
		var pos = $(_id).offset().top - 100;
		$("html,body").animate({ scrollTop: pos }, 1000);
	}
});
</script>
<div class="page-bar">
	<ul class="page-breadcrumb mylink">
		<?php echo $menu['link']; ?>
		<li><a href="javascript:;" onClick="return dr_confirm_url('<font color=red><b><?php echo fc_lang('该操作将会现有的菜单覆盖掉，您确定吗？'); ?></b></font>','<?php echo dr_url('menu/init'); ?>');"> <i class="fa fa-spinner"></i> <?php echo fc_lang('恢复菜单'); ?></a> <i class="fa fa-circle"></i> </li>
		<li> <a href="<?php echo dr_help_url(6); ?>" target="_blank"><i class="fa fa-book"></i> <?php echo fc_lang('在线帮助'); ?></a> </li>
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
	<small><?php echo fc_lang('您可用对后台菜单进行调整，添加、修改、删除菜单项，"模块"将会固定显示在"网站"菜单下面，不可更改'); ?></small>
</h3>

<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="order" />
<div class="portlet mylistbody">
	<div class="portlet-body">
		<div class="table-scrollable">

			<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

			<thead>
				<tr>
					<th width="20"></th>
					<th width="50"><?php echo fc_lang('排序'); ?></th>
					<th width="40"><?php echo fc_lang('可用'); ?></th>
					<th><?php echo fc_lang('名称'); ?></th>
					<th><?php if ($this->ci->is_auth('admin/menu/add')) { ?><a class="add" title="<?php echo fc_lang('添加'); ?>" href="<?php echo dr_dialog_url(dr_url('menu/add', array('pid'=>0)), 'add'); ?>"></a><?php } ?></th>
				</tr>
				</thead>
				<tbody>
				<?php echo $list; ?>
				<tr class="mtable_bottom">
					<th ><input class="toggle md-check" name="dr_select" type="checkbox" onClick="dr_selected()" />&nbsp;</th>
					<td colspan="99" >
						<?php if ($this->ci->is_auth('admin/menu/del')) { ?><button type="button" class="btn red btn-sm" value="" name="button" onClick="$('#action').val('del');return dr_confirm_del_all()"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button>&nbsp;<?php }  if ($this->ci->is_auth('admin/menu/edit')) { ?><button type="button" class="btn green btn-sm" value="" name="button" onclick="$('#action').val('order');return dr_confirm_del_all()" > <i class="fa fa-edit"></i> <?php echo fc_lang('排序'); ?></button>&nbsp;<div class="onShow"><?php echo fc_lang('排序按从小到大排列，最大支持99'); ?></div><?php } ?>
					</td>
				</tr>
				</tbody>
				</table>
		</div>
	</div>
</div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>