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
	<small><?php echo fc_lang('运用自定义字段功能会大大增强内容管理功能'); ?></small>
</h3>

<style>
.iii td {
	text-decoration:line-through
}
</style>
<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="order" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

		<thead>
		<tr>
			<th width="20"></th>
			<th><?php echo fc_lang('排序'); ?></th>
			<th>Id</th>
			<th><?php echo fc_lang('别名'); ?></th>
			<th><?php echo fc_lang('名称'); ?></th>
			<th><?php echo fc_lang('类别'); ?></th>
			<th style="text-align: center"><?php echo fc_lang('系统字段'); ?></th>
            <th style="text-align: center"><?php echo fc_lang('主表'); ?></th>
            <th style="text-align: center"><?php echo fc_lang('XSS过滤'); ?></th>
			<th style="text-align: center"><?php echo fc_lang('前端'); ?></th>
			<th style="text-align: center"><?php echo fc_lang('可用'); ?></th>
			<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
		<tr style="color:<?php echo $group[$t['fieldname']]; ?>;" <?php if ($t['fieldtype']!='Group' && $t['fieldtype']!='Merge' && $group[$t['fieldname']]) { ?> class="iii"<?php } ?>>
			<td><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
			<td><input class="input-text displayorder" type="text" name="data[<?php echo $t['id']; ?>][displayorder]" value="<?php echo $t['displayorder']; ?>" /></td>
            <td <?php if ($t['fieldtype']=='Group' || $t['fieldtype']=='Merge') { ?> style="font-weight:bold"<?php } ?>><?php echo $t['id']; ?></td>
            <td <?php if ($t['fieldtype']=='Group' || $t['fieldtype']=='Merge') { ?> style="font-weight:bold"<?php } ?>><?php echo $t['name']; ?></td>
			<td <?php if ($t['fieldtype']=='Group' || $t['fieldtype']=='Merge') { ?> style="font-weight:bold"<?php } ?>><?php echo $t['fieldname']; ?></td>
			<td <?php if ($t['fieldtype']=='Group' || $t['fieldtype']=='Merge') { ?> style="font-weight:bold"<?php } ?>><?php echo $t['fieldtype']; ?></td>
            <td style="text-align: center;font-size: 15px;"><?php if ($t['issystem']) { ?><i class="icon-ok-sign"></i><?php } else { ?><i class="icon-remove-sign"></i><?php } ?></td>
            <td style="text-align: center;font-size: 15px;"><?php if ($t['ismain']) { ?><i class="icon-ok-sign"></i><?php } else { ?><i class="icon-remove-sign"></i><?php } ?></td>
            <td style="text-align: center"><a href="<?php echo dr_url('field/option', array('rname' => $rname, 'rid' => $rid, 'op' => 'xss', 'id' => $t['id'])); ?>"><img src="<?php echo THEME_PATH; ?>admin/images/<?php if ($t['setting']['validate']['xss']) { ?>0<?php } else { ?>1<?php } ?>.gif"></a></td>
            <td style="text-align: center"><a href="<?php echo dr_url('field/option', array('rname' => $rname, 'rid' => $rid, 'op' => 'member', 'id' => $t['id'])); ?>"><img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['ismember']; ?>.gif"></a></td>
			<td style="text-align: center"><a href="javascript:;" onClick="return dr_dialog_set('<?php echo $t['disabled'] ? fc_lang('<font color=blue><b>你确定要启用它？启用后将正常使用</b></font>') : fc_lang('<font color=red><b>你确定要禁用它？禁用后将无法使用</b></font>'); ?>','<?php echo dr_url('field/option', array('rname' => $rname, 'rid' => $rid, 'op' => 'disabled', 'id' => $t['id'])); ?>');"><img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['disabled'] ? 0 : 1 ?>.gif"></a></td>
            <td class="dr_option">
			<a class="aedit" href="<?php echo dr_url('field/edit', array('rname' => $rname, 'rid' => $rid, 'id' => $t['id'])); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a>
            <?php if ($t['issystem']) { ?>
				<a class="adel" href="javascript:;" onClick="return dr_dialog_del('<?php echo fc_lang('这是系统字段，删除可能会影响后台的显示，您确定要这样操作吗？'); ?>','<?php echo dr_url('field/del', array('rname' => $rname,'rid' => $rid, 'id' => $t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a>
            <?php } else { ?>
            	<a class="adel" href="javascript:;" onClick="return dr_dialog_del('<?php echo fc_lang('您确定要这样操作吗？'); ?>','<?php echo dr_url('field/del', array('rname' => $rname,'rid' => $rid, 'id' => $t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a>
            <?php } ?>
			</td>
		</tr>
		<?php } } ?>
		<tr class="mtable_bottom">
			<th align="right"></th>
			<td colspan="11" align="left">
            <label><button type="button" class="btn red btn-sm" name="button" onClick="$('#action').val('del');return dr_confirm_set_all('<?php echo fc_lang('删除字段后无法恢复（系统字段请勿随意删除），您确定要这样操作吗？'); ?>')"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?> </button></label>
			<label><button type="button" class="btn green btn-sm" name="button" onclick="$('#action').val('order');return dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-edit"></i> <?php echo fc_lang('排序'); ?>  </button></label>
			<label><?php echo fc_lang('排序按从小到大排列，最大支持99'); ?></label>
            <?php if (count($group)>0) { ?><label><?php echo fc_lang('同一种随机颜色的字段表示在同一个分组'); ?></label><?php } ?>
			</td>
		</tr>
		</tbody>
		</table>
		</div>
	</div>
</div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>