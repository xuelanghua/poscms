<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<div class="page-bar">
	<ul class="page-breadcrumb mylink">
		<?php echo $menu['link']; ?>
		<li> <a href="<?php echo dr_help_url(11); ?>" target="_blank"><i class="fa fa-book"></i> <?php echo fc_lang('在线帮助'); ?></a> </li>

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
	<small><?php echo fc_lang('站点是系统的核心部分，各个站点数据独立，可以设置站点分库管理'); ?></small>
</h3>
<form action="" method="post" name="myform" id="myform">
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

						<thead>
						<tr>
							<th width="20" align="right"></th>
							<th width="50" align="center"><?php echo fc_lang('状态'); ?></th>
							<th width="50" align="center">Site</th>
							<th width="200" align="left"><?php echo fc_lang('网站名称'); ?></th>
							<th width="250" align="left"><?php echo fc_lang('网站域名'); ?></th>
							<th align="left" class="dr_option"><?php echo fc_lang('操作'); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php if (is_array($list)) { $count=count($list);foreach ($list as $sid=>$t) {  if ($admin['adminid'] == 1 || ($admin['adminid'] > 1 && @in_array($sid, $admin['role']['site']))) { ?>
						<tr>
							<td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $sid; ?>" /></td>
							<td align="center"><a href="<?php echo dr_url('site/config',array('id' => $sid)); ?>"><img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['setting']['SITE_CLOSE'] ? 0 : 1 ?>.gif" /></a></td>
							<td align="center"><a href="<?php echo dr_url('site/config',array('id' => $sid)); ?>"><?php echo $sid; ?></a></td>
							<td align="left"><input class="input-text" style="height: 25px;" type="text" name="data[<?php echo $sid; ?>][name]" value="<?php echo $t['name']; ?>" required /></td>
							<td align="left"><input class="input-text" style="height: 25px;" type="text" name="data[<?php echo $sid; ?>][domain]" value="<?php echo $t['domain']; ?>" required /></td>
							<td align="left" class="dr_option">
								<a class="ago" href="http://<?php echo $t['domain'];  echo SITE_PATH; ?>" target="_blank"> <i class="fa fa-paper-plane"></i> <?php echo fc_lang('访问'); ?></a><?php if ($this->ci->is_auth('site/config')) { ?><a class="aedit" href="<?php echo dr_url('site/config',array('id' => $sid)); ?>"> <i class="fa fa-cog"></i> <?php echo fc_lang('配置'); ?></a><?php }  if ($this->ci->is_auth('site/del') && $sid > 1) { ?><a class="adel" href="javascript:;" onClick="return dr_confirm_url('<?php echo fc_lang('您确定要这样操作吗？'); ?>','<?php echo dr_url('site/del', array('id' => $t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a><?php } ?>
								<span id="dr_domain_<?php echo $sid; ?>"></span>
								<script type="text/javascript">
									$.get("<?php echo dr_url('home/domain', array('domain' => $t['domain'])); ?>", function(data){
										if (data) {
											$("#dr_domain_<?php echo $sid; ?>").html("<a href='<?php echo dr_url('site/config',array('id'=>$sid)); ?>' style='color:red;'>域名解析异常</a>");
										}
									});
								</script>
							</td>
						</tr>
						<?php }  } } ?>
						<tr class="mtable_bottom">
							<th align="right"><input class="toggle md-check" name="dr_select" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
							<td colspan="6" align="left">
								<?php if ($this->ci->is_auth('site/edit')) { ?><botton type="submit" class="btn green btn-sm noloading" name="submit" onClick="return dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>');"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></botton><?php } ?>
							</td>
						</tr>
						</tbody>
					</table>
		</div>
	</div>
</div>
</form>

<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>