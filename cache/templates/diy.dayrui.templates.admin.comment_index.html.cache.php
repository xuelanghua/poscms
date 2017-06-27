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
	<form method="post" class="row" action="" name="searchform" id="searchform">
		<input name="search" id="search" type="hidden" value="1" />
		<?php if ($is_review && $index) { ?>
		<div class="col-md-12">
			<?php if (is_array($review['option'])) { $count=count($review['option']);foreach ($review['option'] as $i=>$t) {  if ($t['use']) {  echo $t['name']; ?>：<?php echo $index['sort'.$i]; ?>&nbsp;&nbsp;&nbsp;
			<?php }  } }  echo fc_lang('平均分'); ?>：<?php echo $index['avgsort']; ?>
		</div>
		<div class="bk10"></div>
		<?php } ?>
		<div class="col-md-12">
			<label>
				<select name="data[field]" class="form-control">
					<?php if (is_array($field)) { $count=count($field);foreach ($field as $t) { ?>
					<option value="<?php echo $t['fieldname']; ?>" <?php if ($param['field']==$t['fieldname']) { ?>selected<?php } ?>><?php echo $t['name']; ?></option>
					<?php } } ?>
				</select>
			</label>
			<label><i class="fa fa-caret-right"></i></label>
			<label style="padding-right: 20px;"><input type="text" class="form-control" placeholder="<?php echo fc_lang('多个Id可以用“,”分隔'); ?>" value="<?php echo $param['keyword']; ?>" name="data[keyword]" /></label>

			<label><?php echo fc_lang('录入时间'); ?> ：</label>
			<label><?php echo dr_field_input('start', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'100')), (int)$param['start']); ?></label>
			<label><i class="fa fa-minus"></i></label>
			<label style="margin-right:10px"><?php echo dr_field_input('end', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'100')), (int)$param['end']); ?></label>
			<label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
		</div>
	</form>
</div>

<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="order" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

					<thead>
					<tr>
						<th></th>
						<?php if (!$param['cid']) { ?><th class="<?php echo ns_sorting('title'); ?>" name="title"><?php echo fc_lang('评论主题'); ?></th><?php } ?>
						<th class="<?php echo ns_sorting('content'); ?>" name="content"><?php echo fc_lang('评论内容'); ?></th>
						<th class="<?php echo ns_sorting('status'); ?>" name="status"><?php echo fc_lang('状态'); ?></th>
						<th class="<?php echo ns_sorting('uid'); ?>" name="uid"><?php echo fc_lang('作者'); ?></th>
						<th class="<?php echo ns_sorting('loginip'); ?>" name="loginip"><?php echo fc_lang('地区'); ?></th>
						<th class="<?php echo ns_sorting('inputtime'); ?>" name="inputtime"><?php echo fc_lang('时间段'); ?></th>
						<th><?php echo fc_lang('操作'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
					<tr id="dr_row_<?php echo $t['id']; ?>">
						<td><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
						<?php if (!$param['cid']) { ?><td><a href="<?php echo SITE_URL;  echo $t['url']; ?>" target="_blank"><?php echo dr_keyword_highlight(dr_strcut(dr_clearhtml($t['title']), 30), $param['keyword']); ?></a></td><?php } ?>
						<td><a href="<?php echo SITE_URL;  echo $t['url']; ?>" target="_blank"><?php echo dr_sns_content(dr_keyword_highlight(dr_strcut(dr_clearhtml($t['content']), 70), $param['keyword'])); ?></a></td>
						<td><?php if ($t['status']) { ?><span class="label label-sm label-success"><?php echo fc_lang('已审核'); ?></span><?php } else { ?><span class="label label-sm label-danger"><?php echo fc_lang('未审核'); ?></span><?php } ?></td>
						<td><?php if ($t['uid']) { ?><a href="javascript:;" onclick="dr_dialog_member('<?php echo $t['uid']; ?>')"><?php echo dr_strcut($t['author'], 10); ?></a><?php } else {  echo fc_lang('游客');  } ?></td>
						<td><a href="http://www.baidu.com/baidu?wd=<?php echo $t['inputip']; ?>" target="_blank"><?php echo $ci->dip->address($t['inputip']); ?></a></td>
						<td><?php echo dr_date($t['inputtime'], NULL, 'red'); ?></td>
						<td>
							<a class="btn green btn-xs" href="<?php echo $show_url; ?>&id=<?php echo $t['id']; ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('查看/修改'); ?></a>
						</td>
					</tr>
					<?php } } ?>
					<tr class="mtable_bottom">
						<th width="20" ><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
						<td colspan="99" >
							<button data-toggle="confirmation" id="dr_confirm_set_all" data-original-title="<?php echo fc_lang('您确定要这样操作吗？'); ?>" type="button" class="btn red btn-sm" name="option"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button>
							<button data-toggle="confirmation" id="dr_confirm_verify" data-original-title="<?php echo fc_lang('您确定要这样操作吗？'); ?>" type="button" class="btn green btn-sm" name="option"> <i class="fa fa-edit"></i> <?php echo fc_lang('审核'); ?></button>
						</td>
					</tr>
					</tbody>
				</table>
		</div>

	</div>
</div>

</form>
<div id="pages"><a><?php echo fc_lang('共%s条', $total); ?></a><?php echo $pages; ?></div>


<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>