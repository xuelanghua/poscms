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
		<label><?php echo fc_lang('会员账号'); ?>：</label>
		<label><input type="text" class="form-control" value="<?php echo $param['keyword']; ?>" placeholder="<?php echo fc_lang('输入账号'); ?>" name="data[keyword]" /></label>

		<label>&nbsp;&nbsp;<?php echo fc_lang('时间'); ?> ：</label>
		<label><?php echo dr_field_input('start', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>100)), (int)$param['start']); ?></label>
		<label><i class="fa fa-minus"></i></label>
		<label><?php echo dr_field_input('end', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>80)), (int)$param['end']); ?></label>

		<label>&nbsp;&nbsp;<?php echo fc_lang('类型'); ?> ：</label>
		<label><select class="form-control" name="data[type]">
			<option value=""> -- </option>
			<option value="1" <?php if ($param['type']==1) { ?>selected<?php } ?>> <?php echo fc_lang('收入'); ?> </option>
			<option value="2" <?php if ($param['type']==2) { ?>selected<?php } ?>> <?php echo fc_lang('消费'); ?> </option>
		</select>
		</label>

		<label>&nbsp;&nbsp;<?php echo fc_lang('状态'); ?> ：</label>
		<label><select class="form-control" name="data[status]">
		<option value=""> -- </option>
		<option value="1" <?php if ($param['status']==1) { ?>selected<?php } ?>> <?php echo fc_lang('成功'); ?> </option>
		<option value="0" <?php if (!$param['status'] && strlen($param['status']) > 0) { ?>selected<?php } ?>> <?php echo fc_lang('等待付款'); ?> </option>
		</select>
		</label>
		<label><button type="submit" class="btn green btn-sm"> <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
	</form>
</div>

<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="del" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

				<thead>
				<tr>
					<th width="150"><?php echo fc_lang('时间'); ?></th>
					<th width="120"><?php echo fc_lang('会员'); ?></th>
					<th width="200"><?php echo SITE_MONEY; ?></th>
					<th width="90"><?php echo fc_lang('类型'); ?></th>
					<th width="120"><?php echo fc_lang('状态'); ?></th>
					<th class="dr_option"><?php echo fc_lang('备注说明'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
				<tr>
					<td><?php echo dr_date($t['inputtime']); ?></td>
					<td><?php if ($t['uid']) { ?><a onclick="dr_dialog_member('<?php echo $t['uid']; ?>')" href="javascript:;"><?php $m=dr_member_info($t['uid']); echo $m['username']; ?></a><?php } else {  echo fc_lang('游客');  } ?></td>
					<td><b><?php if ($t['value'] > 0) { ?><font color="#009933">+<?php echo $t['value']; ?></font><?php } else { ?><font color="#FF0000"><?php echo $t['value']; ?></font><?php } ?></b></td>
					<td><?php if ($t['type']) {  echo $pay[$t['type']]['name'];  } else {  echo fc_lang('自助');  } ?></td>
					<td><?php if ($t['status']) { ?><font color="#009933"><?php echo fc_lang('成功'); ?></font><?php } else { ?><font color="#FF0000"><?php echo fc_lang('等待付款'); ?></font><?php } ?></td>
					<td class="dr_option"><?php echo dr_lang_note($t['note']); ?></td>
				</tr>
				<?php } } ?>
				</tbody>
				</table>
		</div>
	</div>
</div>
</form>
<div id="pages" style="margin-top: 20px"><a><?php echo fc_lang('共%s条', $param['total']); ?></a><?php echo $pages; ?></div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>