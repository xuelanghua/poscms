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
	<form method="post" action="" name="searchform" id="searchform">
		<?php if (IS_PC) { ?><label><?php echo fc_lang('会员名称'); ?>：</label><?php } ?>
		<label><input type="text" class="form-control" placeholder="<?php echo fc_lang('会员名称'); ?>" value="<?php echo $_POST['keyword']; ?>" name="keyword" /></label>
		<label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
	</form>
</div>


<form action="" method="post" name="myform" id="myform">
	<input type="hidden" name="action" value="del">
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">
					<thead>
					<tr>
						<th width="20"></th>
						<th>Uid</th>
						<th><?php echo fc_lang('会员名称'); ?></th>
						<th><?php echo fc_lang('姓名'); ?></th>
						<th><?php echo fc_lang('角色组'); ?></th>
						<th><?php echo fc_lang('操作'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
					<tr id="dr_row_<?php echo $t['uid']; ?>">
						<td><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['uid']; ?>" /></td>
						<td><?php echo $t['uid']; ?></td>
						<td><a href="javascript:;" onclick="dr_dialog_member('<?php echo $t['uid']; ?>')"><?php echo dr_keyword_highlight($t['username'], $param['keyword']); ?></a></td>
						<td><?php echo $t['realname']; ?></td>
						<td><a href="<?php echo dr_url('root/index', array('roleid' => $t['adminid'])); ?>"><?php echo $t['name']; ?></a></td>
						<td class="dr_option">
							<?php if ($this->ci->is_auth('admin/root/log')) { ?><a class="label label-sm label-success" href="<?php echo dr_url('root/log',array('uid' => $t['uid'])); ?>"> <i class="fa fa-calendar"></i> <?php echo fc_lang('登录日志'); ?></a><?php }  if ($this->ci->is_auth('admin/root/auth')) { ?><a class="label label-sm label-danger" href="<?php echo dr_url('role/user',array('uid' => $t['uid'])); ?>"> <i class="fa fa-cog"></i> <?php echo fc_lang('单独权限'); ?></a><?php }  if ($this->ci->is_auth('admin/root/edit')) { ?><a class="label label-sm label-info" href="<?php echo dr_dialog_url(dr_url('root/edit',array('id' => $t['uid'])), 'edit'); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php }  if ($this->ci->is_auth('admin/root/del') && $t['uid']>1) { ?><a class="label label-sm label-danger" href="javascript:;" onClick="return dr_dialog_del('<?php echo fc_lang('您确定要这样操作吗？'); ?>','<?php echo dr_url('root/del',array('id' => $t['uid'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a><?php } ?>
						</td>
					</tr>
					<?php } } ?>
					<tr class="mtable_bottom">
						<th><input class="toggle md-check" name="dr_select" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
						<td colspan="99">

							<?php if ($this->ci->is_auth('admin/root/del')) { ?>
							<button data-toggle="confirmation" id="bs_confirmation_delete" data-original-title="<?php echo fc_lang('确定将会员从管理员中移除？'); ?>" type="button" class="btn red btn-sm" name="option" > <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button>
							<?php } ?>
						</td>
					</tr>
					</tbody>
				</table>
			</div>

		</div>
	</div>
</form>


<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>