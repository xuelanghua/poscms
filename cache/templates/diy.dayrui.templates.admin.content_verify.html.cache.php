<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
function setflag(i) {
    if (i == -1) {
        $('#flag_back').show();
    } else {
        $('#flag_back').hide();
    }
}
function dr_confirm_verfiy() {
	art.dialog.confirm("<?php echo fc_lang('您确定要这样操作吗？'); ?>", function(){
		$('#action').val('flag');
		var _data = $("#myform").serialize();
		var _url = window.location.href;
		if ((_data.split('ids')).length-1 <= 0) {
			dr_tips(lang['select_null'], 2);
			return true;
		}
		// 将表单数据ajax提交验证
		$.ajax({type: "POST",dataType:"json", url: _url, data: _data,
			success: function(data) {
                //验证成功
                if (data.status == 1) {
                    var ret = data.code;
                    for(var o in ret){
                        dr_tips(ret[o], 5);
                    }
                    var html = data.id;
                    for(var o in html){
                        $.post(html[o], {}, function(){});
                    }
                    setTimeout('window.location.reload(true)', 5000); // 刷新页
                } else if (data.status == 2) {
                    var html = data.id;
                    for(var o in html){
                        $.post(html[o], {}, function(){});
                    }
                    dr_tips(data.code, 3, 1);
                    setTimeout('window.location.reload(true)', 3000); // 刷新页
                } else {
                    dr_tips(data.code);
                    return true;
                }
			},
			error: function(HttpRequest, ajaxOptions, thrownError) {
				alert(HttpRequest.responseText);
			}
		});
		return true;
	});
	return false;
}
</script>
<div class="page-bar">
	<div class="page-toolbar pull-left">
		<div class="btn-group">
			<button type="button" class="btn blue btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
				<?php echo $meta_name; ?>
				<i class="fa fa-angle-down"></i>
			</button>
			<ul class="dropdown-menu" role="menu">
				<?php if (is_array($menu)) { $count=count($menu);foreach ($menu as $name=>$t) { ?>
				<li>
					<a href="<?php echo $t['url']; ?>"> <?php echo $name; ?> <span class="badge badge-info"> <?php echo $t['count']; ?> </span> </a>
				</li>
				<?php } } ?>
			</ul>
		</div>
	</div>

</div>
<h3 class="page-title">
	<small><?php echo fc_lang('前端会员的投稿需要管理角色组审核之后才能正常显示'); ?></small>
</h3>
<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="order" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

		<thead>
		<tr>
			<th width="20" align="right"></th>
			<th width="80">Id</th>
			<th><?php echo fc_lang('主题'); ?></th>
			<th><?php echo fc_lang('栏目分类'); ?></th>
			<th width="120"><?php echo fc_lang('录入作者'); ?></th>
			<th width="150"><?php echo fc_lang('更新时间'); ?></th>
			<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) {  $c=dr_string2array($t['content']); ?>
		<tr id="dr_row_<?php echo $t['id']; ?>">
			<td align="right"><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
			<td><?php echo $t['id']; ?></td>
			<td><?php if ($c['is_new']) { ?><font color="red"><?php echo fc_lang('新'); ?> </font><?php } else { ?><font color="blue"><?php echo fc_lang('改'); ?> </font><?php } ?><a href="<?php echo dr_url(APP_DIR.'/home/verifyedit',array('id'=>$t['id'])); ?>"><?php echo dr_clearhtml($c['title']); ?></a></td>
			<td><a href="<?php echo dr_url(APP_DIR.'/home/verify', array('status'=>$param['status'],'catid'=>$t['catid'])); ?>"><?php $cache = $this->_cache_var('CATEGORY'); eval('echo $cache'.$this->_get_var('$t[catid].name').';');unset($cache); ?></a></td>
			<td><a href="javascript:;" onclick="dr_dialog_member('<?php echo $c['uid']; ?>')"><?php echo dr_strcut($t['author'], 10); ?></a></td>
			<td><?php echo dr_date($t['inputtime'], NULL, 'red'); ?></td>
			<td class="dr_option">
			<a href="<?php echo SITE_URL; ?>index.php?s=<?php echo APP_DIR; ?>&c=show&type=verify&id=<?php echo $t['id']; ?>" target="_blank" class="ago"> <i class="fa fa-send"></i> <?php echo fc_lang('访问'); ?></a>
			<?php if ($this->ci->is_auth(APP_DIR.'/admin/home/verifyedit')) { ?><a class="aedit" href="<?php echo dr_url(APP_DIR.'/home/verifyedit',array('id'=>$t['id'])); ?>"> <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?></a><?php } ?>
			</td>
		</tr>
		<?php } } ?>
		<tr class="mtable_bottom">
			<th width="20"  ><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()"/></th>
			<td colspan="9" >
			<?php if ($this->ci->is_auth(APP_DIR.'/admin/home/del')) { ?><label><button type="button" class="btn red btn-sm" value="" name="option" onClick="$('#action').val('del');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button></label><?php }  if ($this->ci->is_auth(APP_DIR.'/admin/home/verifyedit')) { ?>
				<label><button type="button" class="btn green btn-sm" value="" name="option" onClick="dr_confirm_verfiy()"> <i class="fa fa-edit"></i> <?php echo fc_lang('标记为'); ?></button></label>
				<label><select class="form-control" name="flagid" onchange="setflag(this.value)">
			<option value="1"><?php echo fc_lang('通过'); ?></option>
			<option value="-1"><?php echo fc_lang('退回'); ?></option>
			</select></label>
				<label><input id="flag_back" type="text" name="backcontent" class="form-control input-xlarge" style="display: none;" /></label>
			<?php } ?>
			</td>
		</tr>
		</tbody>
		</table>
		</div>
	</div>
</div>
</form>
<div id="pages"><a><?php echo fc_lang('共%s条', $param['total']); ?></a><?php echo $pages; ?></div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>