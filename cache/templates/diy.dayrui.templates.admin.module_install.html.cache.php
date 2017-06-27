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
	<small><?php echo fc_lang('模块支持安装到多个站点之中，支持绑定独立域名，自定义在字段可以共享，数据可以同步'); ?></small>
</h3>
<form class="form-horizontal" action="" method="post" id="myform" name="myform">
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

				<thead>
				<tr>
					<th width="80" align="left">Site</th>
					<th width="200" align="left"><?php echo fc_lang('网站名称'); ?></th>
					<th width="80" style="text-align: center"><?php echo fc_lang('生成静态'); ?></th>
					<th align="left" class="dr_option"><?php echo fc_lang('操作'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if (is_array($ci->site_info)) { $count=count($ci->site_info);foreach ($ci->site_info as $sid=>$t) {  if ($admin['adminid'] == 1 || ($admin['adminid'] > 1 && @in_array($sid, $admin['role']['site']))) { ?>
				<tr>
					<td align="left"><?php echo $sid; ?></td>
					<td align="left"><?php echo dr_strcut($t['SITE_NAME'], 25); ?></td>
					<td  style="text-align: center"><?php if ($data['site'][$sid]['html']) { ?><a class="badge badge-success" href="<?php echo dr_url('module/html', array('id' => $id, 'sid'=>$sid)); ?>"> <?php echo fc_lang('是'); ?> </a><?php } else { ?><a class="badge badge-warning" href="<?php echo dr_url('module/html', array('id' => $id, 'sid'=>$sid)); ?>"> <?php echo fc_lang('否'); ?> </a><?php } ?></td>
					<td align="left" class="dr_option">
					<?php if ($data['site'][$sid]['use']) {  if ($this->ci->is_auth('module/config')) { ?><a class="alist" href="<?php echo dr_url('module/install3',array('id'=>$id, 'sid'=>$sid)); ?>"> <i class="fa fa-cog"></i> <?php echo fc_lang('站点配置'); ?></a><?php }  if ($this->ci->is_auth('module/uninstall')) { ?><a class="adel" href="javascript:;" onClick="return dr_confirm_url('<font color=red><b><?php echo fc_lang('该操作将会删除当前站点中的模块内容数据，您确定吗？'); ?></b></font>','<?php echo dr_url('module/uninstall2',array('id'=>$id, 'dir'=>$dir, 'sid'=>$sid)); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('卸载'); ?></a><?php }  if ($this->ci->is_auth('module/uninstall')) { ?><a class="adel" href="javascript:;" onClick="return dr_confirm_url('<font color=red><b><?php echo fc_lang('你的操作将会把该站点中的该模块数据全部清空，此操作不可恢复，你确定吗？'); ?></b></font>','<?php echo dr_url('module/clear',array('id'=>$id, 'dir'=>$dir, 'sid'=>$sid)); ?>');"> <i class="fa fa-trash-o"></i> <?php echo fc_lang('清空'); ?></a><?php } ?><a class="ago" href="<?php if ($data['site'][$sid]['domain']) { ?>http://<?php echo $data['site'][$sid]['domain']; ?>/<?php } else {  echo $t['SITE_URL']; ?>index.php?s=<?php echo $data['dirname'];  } ?>" target="_blank"> <i class="fa fa-paper-plane"></i> <?php echo fc_lang('访问'); ?></a>
						<span id="dr_domain_<?php echo $sid; ?>"></span>
						<script type="text/javascript">
							<?php if ($data['site'][$sid]['domain']) { ?>
								$.get("<?php echo dr_url('home/domain', array('domain' => $data['site'][$sid]['domain'])); ?>", function(data){
									if (data) {
										$("#dr_domain_<?php echo $sid; ?>").html("&nbsp;&nbsp;<a href='<?php echo dr_url('module/install3',array('id'=>$id, 'sid'=>$sid)); ?>' style='color:red;'>域名解析异常</a>");
									}
								});
							<?php } ?>
						</script>
					<?php } else {  if ($this->ci->is_auth('module/install')) { ?><a class="adel" href="<?php echo dr_url('module/install2', array('id'=>$id, 'dir'=>$dir, 'sid'=>$sid)); ?>"><?php echo fc_lang('安装'); ?></a><?php }  } ?>
					</td>
				</tr>
				<?php }  } } ?>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>