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
	<small><?php echo fc_lang('在会员模型中可以把会员自定义字段划分给指定会员组使用'); ?></small>
</h3>
<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

		<thead>
		<tr>
			<th width="20"></th>
			<th width="40">Id</th>
			<th width="80" style="text-align: center"><?php echo fc_lang('排序'); ?></th>
			<th><?php echo fc_lang('名称'); ?></th>
			<th><?php echo fc_lang('价格要求'); ?></th>
			<th style="text-align: center"><?php echo fc_lang('允许申请'); ?></th>
			<?php if (MEMBER_OPEN_SPACE) { ?><th style="text-align: center"><?php echo fc_lang('使用空间'); ?></th><?php } ?>
			<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
		</tr>
		</thead>
		<tbody>

		<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
		<tr id="dr_row_<?php echo $t['id']; ?>">
			<td><input name="ids[]" type="checkbox" value="<?php echo $t['id']; ?>" class="toggle md-check dr_select" /></td>
			<td><?php echo $t['id']; ?></td>
			<td style="text-align: center"><input class="input-text displayorder" type="text" name="data[<?php echo $t['id']; ?>][displayorder]" value="<?php echo $t['displayorder']; ?>" /></td>
			<td><?php if ($this->ci->is_auth('member/admin/group/edit')) { ?><a href="<?php echo dr_url('member/group/edit', array('id' => $t['id'])); ?>"><?php echo $t['name']; ?></a><?php } else {  echo $t['name'];  } ?></td>
			<td><?php echo $t['price']; ?></td>
			<td style="text-align: center" align="center"><a href="<?php echo dr_url('member/group/option', array('op' => 'apply', 'id' => $t['id'])); ?>"><img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['allowapply']; ?>.gif"></a></td>
			<?php if (MEMBER_OPEN_SPACE) { ?><td style="text-align: center" align="center"><a href="<?php echo dr_url('member/group/option', array('op' => 'space', 'id' => $t['id'])); ?>"><img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['allowspace']; ?>.gif"></a></td><?php } ?>
			<td class="dr_option">
			<?php if ($t['id']>2 && $this->ci->is_auth('member/admin/level/index')) { ?><a class="alist" href="<?php echo dr_url('member/level/index', array('gid' => $t['id'])); ?>"> <i class="fa fa-table"></i> <?php echo fc_lang('等级管理'); ?></a><?php }  if ($this->ci->is_auth('member/admin/group/edit')) { ?><a class="aedit" href="<?php echo dr_url('member/group/edit',array('id' => $t['id'])); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php }  if ($this->ci->is_auth('member/admin/group/del') &&  $t['id']>3) { ?><a class="adel" href="javascript:;" onClick="return dr_dialog_del('<?php echo fc_lang('您确定要这样操作吗？'); ?>','<?php echo dr_url('member/group/del', array('id' => $t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a><?php }  if ($t['id']>2 && !$t['level']) { ?><b><font color=red><?php echo fc_lang('会员组无效，请添加会员等级'); ?></font></b><?php } ?>
			</td>
		</tr>
		<?php } } ?>
		<tr class="mtable_bottom">
			<th width="20" ><input class="toggle md-check" name="dr_select" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
			<td colspan="98" >
			<?php if ($this->ci->is_auth('member/admin/group/del')) { ?>
				<button type="button" class="btn red btn-sm" value="" name="option" onClick="$('#action').val('del');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button><?php }  if ($this->ci->is_auth('member/admin/group/edit')) { ?>
				<button type="button" class="btn green btn-sm" value="" name="option" onClick="$('#action').val('edit');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-edit"></i> <?php echo fc_lang('排序'); ?></button><?php } ?>
			</td>
		</tr>
		</tbody>
		</table>
		</div>
	</div>
</div>

</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>