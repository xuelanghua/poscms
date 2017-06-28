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
	<small><?php echo fc_lang('模块表单是对模块内容的一种扩展，如内容评论、内容报名、内容留言、内容反馈等等'); ?></small>
</h3>
<div class="portlet mylistbody">
	<div class="portlet-body">
		<div class="table-scrollable">

			<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

			<thead>
			<tr>
				<th width="40" align="center"><?php echo fc_lang('可用'); ?></th>
				<th width="100" align="left"><?php echo fc_lang('名称'); ?></th>
				<th width="80" align="left"><?php echo fc_lang('表名称'); ?></th>
				<th width="120" align="left"><?php echo fc_lang('提交页模板'); ?></th>
				<th align="left" class="dr_option"><?php echo fc_lang('操作'); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
			<tr>
				<td align="center">
					<?php if ($this->ci->is_auth('admin/mform/edit')) { ?>
					<a href="javascript:;" onClick="return dr_dialog_set('<?php echo $t['disabled'] ? fc_lang('<font color=blue><b>你确定要启用它？启用后将正常使用</b></font>') : fc_lang('<font color=red><b>你确定要禁用它？禁用后将无法使用</b></font>'); ?>','<?php echo dr_url('mform/disabled', array('sid' => $sid, 'mid' => $mid, 'id' => $t['id'])); ?>');">
						<img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['disabled'] ? 0 : 1 ?>.gif">
					</a>
					<?php } else { ?>
					<img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['disabled'] ? 0 : 1 ?>.gif">
					<?php } ?>
				</td>
				<td align="left">
				<?php if ($this->ci->is_auth('admin/mform/edit')) { ?>
				<a href="<?php echo dr_url('mform/edit', array('dir' => $dir, 'id' => $t['id'])); ?>"><?php echo $t['name']; ?></a>
				<?php } else {  echo $t['name'];  } ?>
				</td>
				<td align="left"><?php echo $t['table']; ?></td>
				<td align="left">form_<?php echo $t['table']; ?>.html</td>
				<td align="left" class="dr_option">
				<?php if ($this->ci->is_auth('admin/mform/listc')) { ?><a class="alist" href="<?php echo dr_url($dir.'/form_'.$t['table'].'/index'); ?>"> <i class="fa fa-navicon"></i> <?php echo fc_lang('内容维护'); ?></a><?php }  if ($this->ci->is_auth('admin/field/index')) { ?><a class="aadd" href="<?php echo dr_url('admin/field/index', array('rname' => 'mform-'.$dir, 'rid' => $t['id'])); ?>"> <i class="fa fa-plus-square"></i> <?php echo fc_lang('字段'); ?></a><?php }  if ($this->ci->is_auth('admin/mform/edit')) { ?><a class="aedit" href="<?php echo dr_url('mform/edit', array('dir' => $dir, 'id' => $t['id'])); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php }  if ($this->ci->is_auth('admin/mform/del')) { ?><a class="adel" href="javascript:;" onClick="return dr_confirm_url('<?php echo fc_lang('您确定要这样操作吗？'); ?>','<?php echo dr_url('mform/del', array('dir' => $dir, 'id' => $t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a><?php } ?>
				</td>
			</tr>
			<?php } } ?>
			</tbody>
			</table>
		</div>
	</div>
</div>

<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>