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
	<small></small>
</h3>
<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="order" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

		<thead>
		<tr>
			<th width="20"></th>
			<th><?php echo fc_lang('主题'); ?></th>
			<th><?php echo fc_lang('栏目分类'); ?></th>
			<th><?php echo fc_lang('更新时间'); ?></th>
			<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) {  
        $c=dr_string2array($t['content']);
        if ($t['eid']) {
            $title=$c['name'];
            if ($t['eid'] == -1) {
                $url=dr_url(APP_DIR.'/extend/add',array('cid'=>$t['cid'], 'catid'=>$t['catid'], 'did'=>$t['id']));
            } else {
                $url=dr_url(APP_DIR.'/extend/edit',array('cid'=>$t['cid'], 'catid'=>$t['catid'], 'id'=>$t['eid'], 'did'=>$t['id']));
            }
            $gurl=SITE_URL.'index.php?s='.APP_DIR.'&c=extend&type=draft&id='.$t['id'];
        } else {
            $title=$c['title'];
            if ($t['cid']) {
                $url=dr_url(APP_DIR.'/home/edit',array('id'=>$t['cid'], 'did'=>$t['id']));
            } else {
                $url=dr_url(APP_DIR.'/home/add',array('did'=>$t['id']));
            }
			$gurl=SITE_URL.'index.php?s='.APP_DIR.'&c=show&type=draft&id='.$t['id'];
        }
        ?>
		<tr id="dr_row_<?php echo $t['id']; ?>">
			<td><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
			<td><a href="<?php echo $url; ?>"><?php if ($c['thumb']) { ?><font color="#FF0000"><?php echo fc_lang('[图]'); ?></font><?php }  echo dr_clearhtml($title); ?></a></td>
			<td><?php $cache = $this->_cache_var('CATEGORY'); eval('echo $cache'.$this->_get_var('$t[catid].name').';');unset($cache); ?></td>
			<td><?php echo dr_date($t['inputtime'], NULL, 'red'); ?></td>
			<td class="dr_option">
				<a href="<?php echo $gurl; ?>" target="_blank" class="ago"> <i class="fa fa-send"></i> <?php echo fc_lang('访问'); ?></a>
				<a href="<?php echo $url; ?>" class="aadd"> <i class="fa fa-plus"></i> <?php echo fc_lang('发布'); ?></a>
			</td>
		</tr>
		<?php } } ?>
		<tr class="mtable_bottom">
			<th width="20"  ><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()"/></th>
			<td colspan="6"  >
			<?php if ($this->ci->is_auth(APP_DIR.'/admin/home/del')) { ?><button type="button" class="btn red btn-sm" value="" name="option" onClick="dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')" > <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button><?php } ?>
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