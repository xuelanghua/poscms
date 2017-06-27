<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
function dr_confirm_move() {
	var d = top.dialog({
		title: lang["tips"],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> <?php echo fc_lang("您确定要这样操作吗？"); ?>',
		okValue: lang['ok'],
		ok: function () {
			$('#action').val('move');
			var _data = $("#myform").serialize();
			var _url = window.location.href;
			if ((_data.split('ids')).length-1 <= 0) {
				d.close().remove();
				dr_tips(lang['select_null'], 2);
				return true;
			}
			// 将表单数据ajax提交验证
			$.ajax({type: "POST",dataType:"json", url: _url, data: _data,
				success: function(data) {
					d.close().remove();
					//验证成功
					if (data.status == 1) {
						dr_tips(data.code, 3, 1);
						$("input[name='ids[]']:checkbox:checked").each(function(){
							$.post("<?php echo $html_url; ?>c=show&m=create_html&id="+$(this).val(), {}, function(){});
						});
						$.post("<?php echo $html_url; ?>c=category&m=create_html&id="+$('#move_id').val(), {}, function(){});
						setTimeout('window.location.reload(true)', 3000); // 刷新页
						return true;
					} else {
						dr_tips(data.code, 3, 2);
						return true;
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {
					alert(HttpRequest.responseText);
				}
			});
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();
}
function dr_status(id, v) {
	var title = "";
	if (v == 9) {
		title = "<font color=red><b><?php echo fc_lang('您确定要将它关闭吗？'); ?></b></font>";
	} else {
		title = "<font color=blue><b><?php echo fc_lang('您确定要将它开启吗？'); ?></b></font>";
	}
	var d = top.dialog({
		title: lang["tips"],
		fixed: true,
		content: '<img src="/statics/js/skins/icons/question.png"> '+title,
		okValue: lang['ok'],
		ok: function () {
			$.ajax({type: "POST",dataType:"json", url: "<?php echo dr_url(APP_DIR.'/home/status'); ?>&id="+id+"&v="+v, success: function(data) {
				//验证成功
				if (data.status == 1) {
					dr_tips(data.code, 3, 1);
					if (data.id) {
						$.post("<?php echo $html_url; ?>c=show&m=create_html&id="+id, {}, function(){});
						$.post("<?php echo $html_url; ?>c=category&m=create_html&id="+data.id, {}, function(){});
					}
					setTimeout('window.location.reload(true)', 3000); // 刷新页
				} else {
					dr_tips(data.code);
				}
			},
				error: function(HttpRequest, ajaxOptions, thrownError) {
					alert(HttpRequest.responseText);
				}
			});
			return true
		},
		cancelValue: lang['cancel'],
		cancel: function () {}
	});
	d.show();
}
function dr_ts() {
    var v = $("#myform").serialize();
    if ((v.split('ids')).length-1 <= 0) {
        dr_tips(lang['select_null'], 2);
        return true;
    }
    var url = "<?php echo dr_url(APP_DIR.'/home/ts_ajax'); ?>";
    art.dialog.open(url, { title: '<?php echo fc_lang("推送"); ?>',
        ok: function () {
            var iframe = this.iframe.contentWindow;
            if (!iframe.document.body) {
                alert('iframe loading')
                return false;
            };
            var tab = iframe.$('#dr_tab').val();
            var url = "<?php echo dr_url(APP_DIR.'/home/ts_ajax'); ?>";
            url+='&ispost=1&tab='+tab;
            if (tab == 1) {
                // 推荐位
                var id = iframe.document.getElementsByName('dr_flag');
                var value = new Array();
                for (var i = 0; i < id.length; i++){
                    if (id[i].checked) {
                        value.push(id[i].value);
                    }
                }
                $.ajax({type: "POST", url:url+'&value='+value, data:v, dataType:'json', success: function (data) {
                    if (data.status == 1) {
                        dr_tips(data.code, 3, 1);
                        setTimeout('window.location.reload(true)', 3000); // 刷新页
                    } else {
                        dr_tips(data.code);
                    }
                }});
            } else if (tab == 2) {
				var id = iframe.document.getElementsByName('weixin');
				var value = new Array();
				for (var i = 0; i < id.length; i++){
					if (id[i].checked) {
						value.push(id[i].value);
					}
				}
				var url = "<?php echo dr_url('weixin/ts', array('mid'=>APP_DIR)); ?>&"+v;
				window.location.href = url;
            } else if (tab == 0) {
                var o = iframe.document.getElementById("dr_synid");  
				  var str = "";  
				  for(i=0;i<o.length;i++){     
					if(o.options[i].selected){  
						str+=o.options[i].value+",";  
					}  
				}  
				$.ajax({type: "POST", url:url+'&value='+str, data:v, dataType:'json', success: function (data) {
                    if (data.status == 1) {
                        dr_tips(data.code, 3, 1);
                        setTimeout('window.location.reload(true)', 3000); // 刷新页
                    } else {
                        dr_tips(data.code);
                    }
                }});
            }
            return true;
        },
        cancel: true
    });
    return;
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
				<li>
					<a href="<?php echo $list_url; ?>"> <?php echo fc_lang("已通过内容"); ?> </a>
				</li>
				<?php if ($flags) { ?>
				<li class="divider"> </li>
				<?php if (is_array($flags)) { $count=count($flags);foreach ($flags as $t) { ?>
				<li>
					<a href="<?php echo $t['url']; ?>"> <?php echo fc_lang($t['name']); ?> </a>
				</li>
				<?php } }  } ?>
			</ul>
		</div>
		<a href="<?php echo $post_url; ?>" class="btn btn-sm green"> <?php echo fc_lang('发布'); ?>
			<i class="fa fa-plus"></i>
		</a>
	</div>

	<div class="page-toolbar">
		<div class="btn-group pull-right">
			<button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false" data-hover="dropdown"> <?php echo fc_lang('操作菜单'); ?>
				<i class="fa fa-angle-down"></i>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<li>
					<a href="<?php echo dr_url(APP_DIR.'/home/index'); ?>"><i class="icon-table"></i> <?php echo fc_lang('全部内容'); ?></a></li>
				</li>
				<li>
					<a href="<?php echo $ci->duri->uri2url($post); ?>"> <i class="fa fa-plus"></i> <?php echo fc_lang('发布'); ?></a>
				</li>
				<li class="divider"> </li>
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
<?php if ($flag) { ?>
<h3 class="page-title">
	<small><?php echo fc_lang('推荐位用于对文档进行归档管理，如首页幻灯，今日视点，首页头条等等'); ?></small>
</h3>
<?php } else { ?>
<div class="mytopsearch">
	<form method="post" class="row" action="" name="searchform" id="searchform">
		<input name="search" id="search" type="hidden" value="1" />
		<div class="col-md-12">
			<label style="padding-right: 5px;"><?php echo $select2; ?></label>
			<label style="padding-right: 10px;"><i class="fa"></i></label>
			<label>
				<select name="data[field]" class="form-control">
					<option value="id" <?php if ($param['field']=='id') { ?>selected<?php } ?>>Id</option>
					<?php if (is_array($field)) { $count=count($field);foreach ($field as $t) {  if ($t['ismain'] && $t['fieldname'] != 'inputtime' && $t['fieldname'] != 'updatetime') { ?>
					<option value="<?php echo $t['fieldname']; ?>" <?php if ($param['field']==$t['fieldname']) { ?>selected<?php } ?>><?php echo $t['name']; ?></option>
					<?php }  } } ?>
				</select>
			</label>
			<label><i class="fa fa-caret-right"></i></label>
			<label style="padding-right: 20px;"><input type="text" class="form-control" placeholder="<?php echo fc_lang('多个Id可以用“,”分隔'); ?>" value="<?php echo $param['keyword']; ?>" name="data[keyword]" /></label>
			<label><?php echo fc_lang('录入时间'); ?> ：</label>
			<label><?php echo dr_field_input('start', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'110')), (int)$param['start']); ?></label>
			<label><i class="fa fa-minus"></i></label>
			<label style="margin-right:10px"><?php echo dr_field_input('end', 'Date', array('option'=>array('format'=>'Y-m-d','width'=>'110')), (int)$param['end']); ?></label>
			<label><button type="submit" class="btn green btn-sm" name="submit" > <i class="fa fa-search"></i> <?php echo fc_lang('搜索'); ?></button></label>
		</div>
	</form>
</div>
<?php } ?>

<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<!-- <?php if ($fn_include = $this->_load("$list_data_tpl")) include($fn_include); ?> -->
		</div>
	</div>
</div>

</form>
<div id="pages"><a><?php echo fc_lang('共%s条', $param['total']); ?></a><?php echo $pages; ?></div>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>