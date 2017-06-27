<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include);  $_pages=$pages; ?>
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
		<label><?php echo fc_lang('会员组'); ?>：</label>
		<label>
			<select class="form-control" name="data[groupid]">
				<option value=""> -- </option>
				<?php $return = $this->list_tag("action=cache name=MEMBER.group"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
				<option value="<?php echo $t['id']; ?>" <?php if ($t['id']==$param['groupid']) { ?>selected<?php } ?>> <?php echo $t['name']; ?> </option>
				<?php } } ?>
			</select>
		</label>
		<label>
			<select class="form-control" name="data[field]">
				<?php if (is_array($field)) { $count=count($field);foreach ($field as $t) { ?>
				<option value="<?php echo $t['fieldname']; ?>" <?php if ($param['field']==$t['fieldname']) { ?>selected<?php } ?>><?php echo $t['name']; ?></option>
				<?php } } ?>
				<option value="uid" <?php if ($param['field']=='uid') { ?>selected<?php } ?>>uid</option>
			</select>
		</label>
		<label><input type="text" class="form-control" value="<?php echo $param['keyword']; ?>" placeholder="<?php echo fc_lang('多个Id可以用“,”分隔'); ?>" name="data[keyword]" /></label>
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
			<th width="10"></th>
			<th>Uid</th>
			<th class="<?php echo ns_sorting('username'); ?>" name="username" ><?php echo fc_lang('会员'); ?></th>
			<th class="<?php echo ns_sorting('groupid'); ?>" name="groupid" ><?php echo fc_lang('会员组'); ?></th>
			<th class="<?php echo ns_sorting('experience'); ?>" name="experience"><?php echo SITE_EXPERIENCE; ?></th>
			<th class="<?php echo ns_sorting('score'); ?>" name="score"><?php echo SITE_SCORE; ?></th>
			<th class="<?php echo ns_sorting('money'); ?>" name="money"><?php echo SITE_MONEY; ?></th>
			<th class="<?php echo ns_sorting('regtime'); ?>" name="regtime" ><?php echo fc_lang('注册时间'); ?></th>
			<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
		<tr id="dr_row_<?php echo $t['uid']; ?>">
			<td><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['uid']; ?>" /></td>
			<td><?php echo $t['uid']; ?></td>
			<td>
            <a onclick="dr_dialog_member('<?php echo $t['uid']; ?>')" href="javascript:;">
			<?php if ($t['groupid']==2 && !$t['username']) {  echo get_member_nickname($t['uid']); ?>&nbsp;<font color=red><?php echo fc_lang('未完善资料'); ?></font>
			<?php } else {  echo dr_keyword_highlight($t['username'], $param['keyword']);  } ?>
            </a>
			</td>
			<td><font <?php if ($t['groupid']==1) { ?>color="#FF0000"<?php } ?>><?php $cache = $this->_cache_var('MEMBER'); eval('echo $cache'.$this->_get_var('group.$t[groupid].name').';');unset($cache); ?></font></td>
			<td><a <?php if ($this->ci->is_auth('member/admin/home/experience')) { ?>href="<?php echo dr_url('member/home/experience',array('uid'=>$t['uid'])); ?>" style="color: blue;text-decoration: underline;font-size: 12px;"<?php } ?>><?php echo $t['experience']; ?></a></td>
			<td><a <?php if ($this->ci->is_auth('member/admin/home/score')) { ?>href="<?php echo dr_url('member/home/score',array('uid'=>$t['uid'])); ?>" style="color: blue;text-decoration: underline;font-size: 12px;"<?php } ?>><?php echo $t['score']; ?></a></td>
			<td><?php echo $t['money']; ?></td>
			<td><?php echo dr_date($t['regtime'], NULL, 'red'); ?></td>
			<td class="dr_option">
			<?php if ($this->ci->is_auth('member/admin/home/edit')) { ?><a class="aedit" <?php if ($t['id']==1) { ?>href="javascript:;"<?php } else { ?>href="<?php echo dr_url('member/home/edit',array('uid'=>$t['uid'])); ?>"<?php } ?>> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php }  if (MEMBER_OPEN_SPACE && $this->ci->is_auth('space/admin/space/edit')) { ?><a class="alist" href="<?php echo dr_url('space/space/edit',array('uid' => $t['uid'])); ?>"> <i class="fa fa-database"></i> <?php echo fc_lang('空间'); ?></a><?php }  if ($member['adminid']==1) { ?><a class="ago" href="<?php echo dr_member_url('api/ologin'); ?>&uid=<?php echo $t['uid']; ?>" target="_blank"> <i class="fa fa-send"></i> <?php echo fc_lang('登录会员'); ?></a><?php } ?>
			</td>
		</tr> 
		<?php } } ?>
		<tr class="mtable_bottom">
        	<th width="20"  ><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
			<td colspan="10"  >
            <?php if ($this->ci->is_auth('member/admin/home/del')) { ?>
				<label><button type="button" class="btn red btn-sm" name="option" onClick="$('#action').val('del');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"><i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button></label>
            <?php }  if ($this->ci->is_auth('member/admin/home/edit')) { ?>
			<label><button type="button" class="btn green btn-sm" name="option" onClick="$('#action').val('update');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"><i class="fa fa-edit"></i> <?php echo fc_lang('修改为'); ?></button></label>
			<label><select class="form-control" name="groupid">
			<?php $return = $this->list_tag("action=cache name=MEMBER.group"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
            <option value="<?php echo $t['id']; ?>" <?php if ($t['id']==3) { ?>selected<?php } ?>> <?php echo $t['name']; ?> </option>
            <?php } } ?>
        	</select></label>
            <?php } ?>
			</td>
		</tr>
		</tbody>
		</table>
		</div>
	</div>
</div>
</form>
<div id="pages"><a><?php echo fc_lang('共%s条', $param['total']); ?></a><?php echo $_pages; ?></div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>