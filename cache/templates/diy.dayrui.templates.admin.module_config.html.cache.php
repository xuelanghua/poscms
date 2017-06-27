<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	<?php if ($result) { ?>
	dr_tips('<?php echo $result; ?>');
	<?php }  if ($member_rule) { ?>
	$(".dr_select_all input").click(function(data){
		var _class = $(this).attr("class");
		if ($(this).prop('checked')) {
			$("."+_class).prop("checked",true);
		} else {
			$("."+_class).prop("checked",false);
		}
	});
	<?php } ?>
	// 会员组权限联动
	$(".dr_select_all input").click(function(data){
		var _class = $(this).attr("class");
		if ($(this).prop('checked')) {
			$("."+_class).prop("checked",true);
		} else {
			$("."+_class).prop("checked",false);
		}
	});
     // 关闭搜索
    <?php if ($data['setting']['search']['close']) { ?>dr_set_search(1);<?php } ?>
});
function dr_set_search(v){
    if (v == 1) {
        $(".dr_search").hide();
    } else {
        $(".dr_search").show();
    }
}
function sitetips(_this) {
	var id = $(_this).attr("sid");
	if (!$(_this).prop("checked")) {
		art.dialog.confirm("<font color=red><b><?php echo fc_lang('你的操作将会把该站点中的相关表全部删除掉，此操作不可恢复，你确定吗？'); ?></b></font>", function(){
			$(".dr_site_"+id).hide();
			return true;
		}, function(){
			$(".dr_site_"+id).show();
			$(_this).prop("checked", "checked");
		});
	} else {
		$(".dr_site_"+id).show();
	}
}

function dr_admin_rule(id, url, title) {
	var throughBox = $.dialog.through; // 创建窗口
	var dr_Dialog = throughBox({title: title});
	// ajax调用窗口内容
	$.ajax({type: "GET", url:url, dataType:'text', success: function (text) {
			var win = $.dialog.top;
			dr_Dialog.content(text);
			// 添加按钮
			dr_Dialog.button({name: fc_lang[36], callback:function() {
					win.$("#mark").val("0"); // 标示可以提交表单
					if (win.dr_form_check()) { // 按钮返回验证表单函数
						var _data = win.$("#myform").serialize();
						$.ajax({type: "POST",dataType:"json", url: url, data: _data, // 将表单数据ajax提交验证
							success: function(data) {
								$("#dr_status_"+id).attr("class", "onCorrect");
								$("#dr_status_"+id).html("&nbsp;&nbsp;");
								dr_tips(fc_lang[37], 2, 1);
								dr_Dialog.close();
							},
							error: function(HttpRequest, ajaxOptions, thrownError) {
								alert(HttpRequest.responseText);
							}
						});
					}
					return false;
				},
				focus: true
			});
	    },
	    error: function(HttpRequest, ajaxOptions, thrownError) {
			alert(HttpRequest.responseText);
		}
	});
}

function dr_set_flag(id) {
	art.dialog.open('<?php echo dr_url("module/flag"); ?>', {
		title: '<?php echo fc_lang("收费设置"); ?>',
		init: function () {
			var iframe = this.iframe.contentWindow;
			$(".dr_flag_"+id).each(function(){
				var iid = $(this).attr('iid');
				iframe.document.getElementById('top_dr_flag_'+iid).value = Math.abs($("#dr_flag_"+id+"_"+iid).val());
			});
		},
		ok: function () {
			var iframe = this.iframe.contentWindow;
			if (!iframe.document.body) {
				alert('iframe loading')
				return false;
			};
			$(".dr_flag_"+id).each(function(){
				var iid = $(this).attr('iid');
				$("#dr_flag_"+id+"_"+iid).val(iframe.document.getElementById('top_dr_flag_'+iid).value);
			});
			$("#dr_status_"+id).html(" <i class='fa fa-check-square'></i>");
			return true;
		},
		cancel: true
	});
}
</script>
<form class="form-horizontal" action="" method="post" id="myform" name="myform">
<input name="page" id="page" type="hidden" value="<?php echo $page; ?>" />
	<div class="page-bar">
		<ul class="page-breadcrumb mylink">
			<?php if ($all) {  echo $menu['link'];  } else { ?>
			<li class="blue"><a href="javascript:;"><i class="fa fa-cog"></i> <?php echo fc_lang('配置'); ?></a> <i class="fa fa-circle"></i> </li>
			<?php } ?>

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
		<small><?php echo fc_lang('模块是系统的核心部分，可以在“模块商店”下载文章模块、商城模块、下载模块以及商家店铺模块等等'); ?></small>
	</h3>

	<div class="portlet light bordered myfbody">
		<div class="portlet-title tabbable-line">
			<ul class="nav nav-tabs" style="float:left;">
				<li class="active">
					<a href="#tab_0" data-toggle="tab"> <i class="fa fa-cog"></i> <?php echo fc_lang('基本设置'); ?> </a>
				</li>
				<li class="">
					<a href="#tab_1" data-toggle="tab"> <i class="fa fa-user"></i> <?php echo fc_lang('会员权限'); ?> </a>
				</li>
				<li class="">
					<a href="#tab_2" data-toggle="tab"> <i class="fa fa-flag"></i> <?php echo fc_lang('推荐位'); ?> </a>
				</li>
				<li class="">
					<a href="#tab_4" data-toggle="tab"> <i class="fa fa-search"></i> <?php echo fc_lang('搜索设置'); ?> </a>
				</li>
				<li class="">
					<a href="#tab_5" data-toggle="tab"> <i class="fa fa-weibo"></i> <?php echo fc_lang('同步微博'); ?> </a>
				</li>
				<?php if ($mycfg) { ?>
				<li class="">
					<a href="#tab_6" data-toggle="tab"> <i class="fa fa-cog"></i> <?php echo fc_lang('我的配置'); ?> </a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<div class="portlet-body">
			<div class="tab-content">


				<?php if ($mycfg) { ?>
				<div id="tab_6" class="tab-pane">
					<?php if ($fn_include = $this->_load("$mycfg")) include($fn_include); ?>
				</div>
				<?php } ?>

				<div class="tab-pane active" id="tab_0">
					<div class="form-body">

						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('模块名称'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="name" value="<?php echo $name; ?>" ></label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('网站地图'); ?>：</label>
							<div class="col-md-9">

								<input type="checkbox" name="data[sitemap]" value="1" <?php if ($data['sitemap']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('关闭之后更新数据将不会出现在sitemap.xml中'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('父栏目可发布内容'); ?>：</label>
							<div class="col-md-9">

								<input type="checkbox" name="data[setting][pcatpost]" value="1" <?php if ($data['setting']['pcatpost']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('开启之后父栏目具有发布权限，默认关闭（无发布权限）'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('百度Ping服务'); ?>：</label>
							<div class="col-md-9">

								<input type="checkbox" name="data[setting][bdping]" value="1" <?php if ($data['setting']['bdping']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('将新内容自动提交到百度Ping服务器'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('栏目内容同步'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[setting][syncat]" value="1" <?php if ($data['setting']['syncat']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('开启之后发布内容时可以选择将内容同步到其他栏目'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('附件同步删除'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[setting][attdel]" value="1" <?php if ($data['setting']['attdel']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('开启之后删除内容时同时删除它关联的全部附件'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('后台默认排序'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control input-xlarge" type="text" name="data[setting][order]" value="<?php if ($data['setting']['order']) {  echo $data['setting']['order'];  } else { ?>displayorder DESC,updatetime DESC<?php } ?>" ></label>
							</div>
						</div>
						<?php if ($extend) { ?>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('后台子内容默认排序'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control input-xlarge" type="text" name="data[setting][order_extend]" value="<?php if ($data['setting']['order_extend']) {  echo $data['setting']['order_extend'];  } else { ?>displayorder DESC,updatetime DESC<?php } ?>" ></label>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>

				<div class="tab-pane " id="tab_1">
					<div class="form-body">
						<?php $groups[0]=array('id'=>0, 'name'=>fc_lang('游客')); $groups+= $ci->get_cache('member', 'group');  if (is_array($groups)) { $count=count($groups);foreach ($groups as $group) {  if ($group['id']>2) { ?>
						<div class="form-group dr_one">
							<label class="col-md-2 control-label"><?php echo $group['name']; ?>：</label>
							<div class="col-md-9"></div>
						</div>
						<?php if (is_array($group['level'])) { $count=count($group['level']);foreach ($group['level'] as $level) {  $id=$group['id'].'_'.$level['id']; ?>
						<div class="form-group dr_one">
							<label class="col-md-2 control-label"><?php echo $level['name']; ?>：</label>
							<div class="col-md-9">
								<label><input type="checkbox" name="data[setting][member][<?php echo $id; ?>]" <?php if ($data['setting']['member'][$id]) { ?>checked<?php } ?> value="1" data-on-text="<?php echo fc_lang('拒绝访问'); ?>" data-off-text="<?php echo fc_lang('开启'); ?>" data-off-color="success" data-on-color="danger" class="make-switch" data-size="small"></label>

							</div>
						</div>
						<?php } }  } else {  $id=$group['id']; ?>
						<div class="form-group dr_one">
							<label class="col-md-2 control-label"><?php echo $group['name']; ?>：</label>
							<div class="col-md-9">
								<label><input type="checkbox" name="data[setting][member][<?php echo $id; ?>]" <?php if ($data['setting']['member'][$id]) { ?>checked<?php } ?> value="1" data-on-text="<?php echo fc_lang('拒绝访问'); ?>" data-off-text="<?php echo fc_lang('开启'); ?>" data-off-color="success" data-on-color="danger" class="make-switch" data-size="small"></label>
							</div>
						</div>
						<?php }  } } ?>
					</div>
				</div>

				<div class="tab-pane " id="tab_2">
					<div class="form-body">

						<?php for ($i = 1; $i <= 9; $i ++) { ?>
						<div class="form-group">
							<label class="col-md-2 control-label">(<?php echo $i; ?>)：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[setting][flag][<?php echo $i; ?>][name]" type="text" value="<?php echo isset($data['setting']['flag'][$i]['name']) ? $data['setting']['flag'][$i]['name'] : ''; ?>"  ></label>
								<?php $return_group = $this->list_tag("action=cache name=MEMBER.group  return=group"); if ($return_group) extract($return_group); $count_group=count($return_group); if (is_array($return_group)) { foreach ($return_group as $key_group=>$group) {  if ($group['id'] > 2) {  if (is_array($group['level'])) { $count=count($group['level']);foreach ($group['level'] as $level) {  $iid=$group['id'].'_'.$level['id']; ?>
								<input name="data[setting][flag][<?php echo $i; ?>][<?php echo $iid; ?>]" class="dr_flag_<?php echo $i; ?>" iid="<?php echo $iid; ?>" id="dr_flag_<?php echo $i; ?>_<?php echo $iid; ?>" type="hidden" value="<?php echo intval($data['setting']['flag'][$i][$iid]); ?>" />
								<?php } }  }  } } ?>
								<label style="margin-left: 10px;"><a href="javascript:;" onclick="dr_set_flag('<?php echo $i; ?>')"><?php echo fc_lang('收费设置'); ?></a></label>
								<label id="dr_status_<?php echo $i; ?>"></label>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>



				<div class="tab-pane " id="tab_4">
					<div class="form-body">

						<div class="form-group">
							<label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('关闭搜索'); ?>：</label>
							<div class="col-md-9">
								<div class="radio-list">
									<label class="radio-inline"><input type="radio" name="data[setting][search][close]" <?php if ($data['setting']['search']['close']) { ?>checked<?php } ?> onclick="dr_set_search(1)" value="1" /> <?php echo fc_lang('是'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[setting][search][close]" <?php if (!$data['setting']['search']['close']) { ?>checked<?php } ?> onclick="dr_set_search(0)" value="0" /> <?php echo fc_lang('否'); ?></label>
								</div>
								<span class="help-block"><?php echo fc_lang('关闭后将不能进行内容搜索'); ?></span>
							</div>
						</div>

						<div class="form-group dr_search">
							<label class="col-md-2 control-label"><?php echo fc_lang('集成栏目页'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[setting][search][catsync]" value="1" <?php if ($data['setting']['search']['catsync']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('开启之后访问栏目页会定向到搜索页面，使栏目模板无效'); ?></span>
							</div>
						</div>
						<div class="form-group dr_search">
							<label class="col-md-2 control-label"><?php echo fc_lang('关键词匹配字段'); ?>：</label>
							<div class="col-md-9">
								<input class="form-control input-xlarge" type="text" name="data[setting][search][field]" value="<?php if ($data['setting']['search']['field']) {  echo $data['setting']['search']['field'];  } else { ?>title,keywords<?php } ?>" >
								<span class="help-block"><?php echo fc_lang('默认关键词匹配字段是：title,keywords，只能设置主表字段，以逗号分隔'); ?></span>
							</div>
						</div>

						<div class="form-group dr_search">
							<label class="col-md-2 control-label"><?php echo fc_lang('最大搜索量'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[setting][search][total]" value="<?php if (isset($data['setting']['search']['total'])) {  echo $data['setting']['search']['total'];  } else { ?>500<?php } ?>" ></label>
								<span class="help-block"><?php echo fc_lang('指搜索时最大显示的数据量，填写0表示全部显示（不建议填写0，一般用户只会看前几页）'); ?></span>
							</div>
						</div>
						<div class="form-group dr_search">
							<label class="col-md-2 control-label"><?php echo fc_lang('最小关键字长度'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[setting][search][length]" value="<?php if ($data['setting']['search']['length']) {  echo $data['setting']['search']['length'];  } else { ?>4<?php } ?>" ></label>
								<span class="help-block"><?php echo fc_lang('搜索关键字最小字符长度，一个汉字占两位'); ?></span>
							</div>
						</div>

						<div class="form-group dr_search">
							<label class="col-md-2 control-label"><?php echo fc_lang('搜索参数连接符号'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[setting][search][param_join]" value="<?php if ($data['setting']['search']['param_join']) {  echo $data['setting']['search']['param_join'];  } else { ?>-<?php } ?>" ></label>
								<span class="help-block"><?php echo fc_lang('用于伪静态时搜索参数的连接，默认-，例如: 字段1-值-字段2-值'); ?></span>
							</div>
						</div>
						<div class="form-group dr_search">
							<label class="col-md-2 control-label"><?php echo fc_lang('搜索字段映射关系'); ?>：</label>
							<div class="col-md-9">
								<textarea class="form-control" rows="7" name="data[setting][search][param_field]"><?php echo $data['setting']['search']['param_field']; ?></textarea>
								<span class="help-block"><?php echo fc_lang('字段映射是指伪静态时将搜索字段重新命名，例如keyword|k: 意思是把k作为keyword，多个字段映射回车符号分隔'); ?></span>
							</div>
						</div>

					</div>
				</div>

				<div class="tab-pane " id="tab_5">
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('同步微博'); ?>：</label>
							<div class="col-md-9">

								<input type="checkbox" name="data[setting][syn2]" value="1" <?php if ($data['setting']['syn2']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('开启之后发布内容时自动同步绑定的微博之中'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('自定义同步字段'); ?>：</label>
							<div class="col-md-9">
								<label><select class="form-control" name="data[setting][syn2field]">
									<option value=""> -- </option>
									<?php if (is_array($field)) { $count=count($field);foreach ($field as $t) { ?>
									<option value="<?php echo $t['fieldname']; ?>" <?php if ($data['setting']['syn2field'] == $t['fieldname']) { ?>selected<?php } ?>><?php echo $t['name']; ?></option>
									<?php } } ?>
								</select></label>
								<span class="help-block"><?php echo fc_lang('同步时会将你选定的字段内容（必须是纯文本内容）作为同步内容'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('将Tag作为话题插入'); ?>：</label>
							<div class="col-md-9">

								<input type="checkbox" name="data[setting][syn2tag]" value="1" <?php if ($data['setting']['syn2tag']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('当内容中无话题时才将Tag作为话题放到内容的前面，如#Tag1# #Tag2#'); ?></span>
							</div>
						</div>

					</div>
				</div>



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