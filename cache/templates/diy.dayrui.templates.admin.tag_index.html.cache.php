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
	<form method="get" action="" name="searchform" id="searchform">
		<input name="s" type="hidden" value="<?php echo APP_DIR; ?>" />
		<input name="c" type="hidden" value="tag" />
		<input name="m" type="hidden" value="index" />
		<label><?php echo fc_lang('关键字'); ?>：</label>
		<label><input type="text" class="form-control" placeholder="<?php echo fc_lang('关键字'); ?>" value="<?php echo $param['kw']; ?>" name="kw" /></label>
		<label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
	</form>
</div>

<form action="" method="post" name="myform" id="myform">
	<input name="action" type="hidden" value="del" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

		<thead>
		<tr>
			<th width="20" align="right"></th>
			<th width="50" class="<?php echo ns_sorting('id'); ?>" name="id" align="left">Id</th>
			<th width="200" class="<?php echo ns_sorting('name'); ?>" name="name" align="left"><?php echo fc_lang('名称'); ?></th>
			<th width="100" class="<?php echo ns_sorting('code'); ?>" name="code" align="left">Tag</th>
			<th width="80" class="<?php echo ns_sorting('hits'); ?>" name="hits" align="center"><?php echo fc_lang('点击量'); ?></th>
			<th align="left" class="dr_option"><?php echo fc_lang('操作'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
		<tr id="dr_row_<?php echo $t['id']; ?>">
			<td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
			<td align="left"><?php echo $t['id']; ?></td>
			<td align="left"><?php echo dr_keyword_highlight($t['name'], $param['kw']); ?></a></td>
			<td align="left"><?php echo $t['code']; ?></td>
			<td align="center"><?php echo $t['hits']; ?></td>
			<td align="left" class="dr_option">
			<a class="ago" href="<?php echo dr_url_prefix(dr_tag_url($mod, $t['code'])); ?>" target="_blank"> <i class="fa fa-send"></i> <?php echo fc_lang('访问'); ?></a><?php if ($this->ci->is_auth(APP_DIR.'/admin/tag/edit')) { ?><a class="aedit" href="<?php echo dr_dialog_url(dr_url(APP_DIR.'/tag/edit', array('id' => $t['id'])), 'edit'); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php }  if ($this->ci->is_auth(APP_DIR.'/admin/tag/del')) { ?><a class="adel" href="javascript:;" onClick="return dr_dialog_del('<?php echo fc_lang('您确定要这样操作吗？'); ?>','<?php echo dr_url(APP_DIR.'/tag/del',array('id'=>$t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></a><?php } ?>
			</td>
		</tr>
		<?php } } ?>
		<tr class="mtable_bottom">
			<th width="20"  ><input class=" toggle md-check" name="dr_select" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
			<td colspan="6" >
			<?php if ($this->ci->is_auth(APP_DIR.'/admin/tag/del')) { ?>
				<button type="button" class="btn red btn-sm" name="option" onClick="$('#action').val('del');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"><i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button>
			<?php } ?>
			</td>
		</tr>
		</tbody>
		</table>
	</div>
</div>
</form>
<div id="pages"><a><?php echo fc_lang('共%s条', $param['total']); ?></a><?php echo $pages; ?></div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>