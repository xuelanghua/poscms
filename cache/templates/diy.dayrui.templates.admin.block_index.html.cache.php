<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
function dr_get_block(id, i) {
	if (i == 1) {
		var html = '{'+'dr_block(\''+id+'\')'+'}';
	} else if (i == 2) {
		var html = '{'+'dr_block(\''+id+'\')'+'}';
	} else if (i == 3) {
		var html = '{'+'dr_get_file('+'dr_block(\''+id+'\')'+')}';
	} else if (i == 4) {
		var html = '{'+'php $block='+'dr_block(\''+id+'\');'+'}';
		html+= '<br>{';
		html+= 'loop ';
		html+= '$block';
		html+= '.file $file}';
		//html+= '<br>{'+'loop'+' $block['file']'+' file}';
		html+= '<br>{'+'dr_get_file($file'+')}';
		html+= '<br>{/'+'loop '+'}';
	}
	var body = '<div style="padding:20px;font-size:14px">'+html+'</div>';
	var throughBox = art.dialog.through;
	throughBox({
		content: body,
		title: "<?php echo fc_lang('调用方式'); ?>"
	});	
}
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
	<small><?php echo fc_lang('自定义内容用于存储一小段信息，各个页面能够灵活的调用'); ?></small>
</h3>


<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="del" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

				<thead>
				<tr>
					<th width="20" align="right"></th>
					<th ><?php echo fc_lang('别名'); ?></th>
					<th ><?php echo fc_lang('名称'); ?></th>
					<th width="100"><?php echo fc_lang('类型'); ?></th>
					<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) {  $t=dr_get_block_value($t); ?>
				<tr>
					<td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
					<td><?php echo $t['code']; ?></td>
					<td><?php echo $t['name']; ?></td>
					<td><?php echo $type[$t['i']]; ?></td>
					<td class="dr_option">
					<?php if ($this->ci->is_auth('admin/block/edit')) { ?><a class="aedit" href="<?php echo dr_url('block/edit',array('id'=>$t['id'])); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php } ?>
					<a class="alist" href="javascript:;" onclick="dr_get_block('<?php echo $t['code']; ?>', '<?php echo $t['i']; ?>')"> <i class="fa fa-search"></i> <?php echo fc_lang('调用方式'); ?></a>
					</td>
				</tr>
				<?php } } ?>
				<tr class="mtable_bottom">
					<th width="20" ><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" />&nbsp;</th>
					<td colspan="5" >
					<?php if ($this->ci->is_auth('admin/block/del')) { ?>
						<button data-toggle="confirmation" id="dr_confirm_set_all" data-original-title="<?php echo fc_lang('您确定要这样操作吗？'); ?>" type="button" class="btn red btn-sm" name="option"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button>
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