<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	<?php if ($result) { ?>
	dr_tips('<?php echo fc_lang('操作成功，正在刷新...'); ?>', 3, 1);
	<?php } ?>
});
function test_email(id) {
	$("#dr_sending_"+id).html("Sending ... ");
	$.post("<?php echo dr_url('mail/test'); ?>&id="+id+"&"+Math.random(), function(data){
		alert(data);
		$("#dr_sending_"+id).html("");
	});
}
</script>
<div class="page-bar">
	<ul class="page-breadcrumb mylink">
		<?php echo $menu['link']; ?>
		<li> <a href="<?php echo dr_help_url(2078); ?>" target="_blank"><i class="fa fa-book"></i> <?php echo fc_lang('在线帮助'); ?></a> </li>

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
	<small><?php echo fc_lang('支持SMTP协议邮件发送，一组邮件服务器发送失败后，下一组服务器会尝试再次发送'); ?></small>
</h3>

<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

			<thead>
			<tr>
				<th width="33"></th>
				<th width="55"><?php echo fc_lang('排序'); ?></th>
				<th width="200"><?php echo fc_lang('服务器'); ?></th>
				<th width="200"><?php echo fc_lang('邮箱名称'); ?></th>
				<th width="77"><?php echo fc_lang('端口号'); ?></th>
				<th class="dr_option"></th>
			</tr>
			</thead>
			<tbody>
			<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
			<tr>
				<td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
				<td><input class="input-text displayorder" type="text" name="data[<?php echo $t['id']; ?>]" value="<?php echo intval($t['displayorder']); ?>" size="3"/></td>
				<td><?php echo $t['host']; ?></td>
				<td><?php echo $t['user']; ?></td>
				<td><?php echo $t['port']; ?></td>
				<td class="dr_option">
				<?php if ($this->ci->is_auth('admin/mail/edit')) { ?><a class="aedit" href="<?php echo dr_dialog_url(dr_url('mail/edit',array('id'=>$t['id'])), 'edit'); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php } ?>
				<a class="ago" href="javascript:;" onclick="test_email('<?php echo $t['id']; ?>')"> <i class="fa fa-location-arrow"></i> <?php echo fc_lang('发送测试'); ?></a>
				<span id="dr_sending_<?php echo $t['id']; ?>"></span>
				</td>
			</tr>
			<?php } } ?>
			<tr class="mtable_bottom">
				<th width="20" align="right" ><input class="toggle md-check" name="dr_select" id="dr_select" type="checkbox" onClick="dr_selected()" />&nbsp;</th>
				<td colspan="5" >
				<?php if ($this->ci->is_auth('admin/mail/del')) { ?><button type="button" class="btn red btn-sm" name="option" onClick="dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>');$('#action').val('del')"><i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button>&nbsp;&nbsp;<?php }  if ($this->ci->is_auth('admin/mail/edit')) { ?><button type="button" class="btn green btn-sm"  name="button" onClick="dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>');$('#action').val('update')"><i class="fa fa-refresh"></i> <?php echo fc_lang('排序'); ?></button>
					&nbsp;<div class="onShow"><?php echo fc_lang('排序按从小到大排列，最大支持99'); ?></div><?php } ?>
				</td>
			</tr>
			</tbody>
			</table>
		</div>
	</div>
</div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>