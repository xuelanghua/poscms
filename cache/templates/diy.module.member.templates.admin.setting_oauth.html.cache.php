<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function(){
	$("#dr_remove").sortable();
	<?php if ($result) { ?>
	dr_tips('<?php echo $result; ?>', 3, 1);
	<?php } ?>
});
</script>
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
	<small><?php echo fc_lang('修改配置时要保存之后才会生效；鼠标移至图标处可实现排序'); ?></small>
</h3>
<form action="" method="post" name="myform" id="myform">
<div class="portlet light bordered">
	<div class="portlet-body">
		<div class="table-scrollable v3table">
		<table class="table">
		<thead>
		<tr>
			<th>OAuth</th>
			<th>App Id</th>
			<th>App Key</th>
			<th class="dr_option"><?php echo fc_lang('可用'); ?></th>
		</tr>
		</thead>
		<tbody id="dr_remove">
		<?php if (is_array($oauth)) { $count=count($oauth);foreach ($oauth as $id=>$name) {  $t=$data[$id];$i=$id; ?>
		<tr>
			<td><?php echo $name; ?></td>
			<td><input class="input-text" type="text" style="width:200px" name="data[key][<?php echo $i; ?>]" value="<?php echo $t['key']; ?>" /></td>
			<td><input class="input-text" type="text" style="width:300px" name="data[secret][<?php echo $i; ?>]" value="<?php echo $t['secret']; ?>" /></td>
			<td class="dr_option"><input name="data[use][<?php echo $id; ?>][<?php echo $i; ?>]" type="checkbox" value="1" <?php if ($t['use']) { ?>checked<?php } ?> /></td>
		</tr>
		<?php } } ?>
		</tbody>
		</table>
		</div>
	</div>
</div>
	<div class="myfooter">
		<div class="row">
			<div class="portlet-body form">
				<div class="form-body">
					<div class="form-actions">
						<div class="row">
							<div class="col-md-12 text-center">
								<button type="submit" class="btn green"> <i class="fa fa-save"></i> <?php echo fc_lang('保存'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>