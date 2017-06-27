<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	set_required(<?php echo intval($data['setting']['validate']['required']); ?>);
	show_field_option("<?php echo $data['fieldtype']; ?>");
	<?php if ($result) { ?>
	dr_tips('<font color=red><?php echo $result; ?></font>', 3);
	<?php }  if ($code) { ?>
	d_tips('<?php echo $code; ?>', 0);
	<?php } ?>
});
function dr_form_check() {
	if (d_required('name')) return false;
	if (d_required('fieldname')) return false;
	return true;
}
function show_field_option(type) {
	$("#dr_loading").show();
	$.post('<?php echo MEMBER_PATH; ?>index.php?s=member&c=api&m=field&rand='+Math.random(),{ type:type, module:'<?php echo $module; ?>', relatedid:'<?php echo $relatedid; ?>', relatedname:'<?php echo $relatedname; ?>', id:<?php echo intval($data['id']); ?>}, function(data){
		$('#dr_option').html(data);
		App.init();
		$("#dr_loading").hide();

	});
}
function set_required(id) {
	if (id == 0) {
		$('#required').hide();
	} else {
		$('#required').show();
	}
}
function dr_confirm_zb(title) {
    art.dialog.confirm("<font color=red><b>"+title+"</b></font>", function() {
        return true;
    },function() {
        $("#iszb1").prop("checked", true);
        $("#iszb2").prop("checked", false);
    });
}
</script>
<form class="form-horizontal" action="" method="post" name="myform" id="myform" onsubmit="return dr_form_check()">
<input name="page" id="page" type="hidden" value="<?php echo $page; ?>" />
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
		<small><?php echo fc_lang('运用自定义字段功能会大大增强内容管理功能'); ?></small>
	</h3>
	<div class="portlet light bordered" style="margin-bottom:70px;">
		<div class="portlet-title tabbable-line">
			<ul class="nav nav-tabs" style="float:left;">
				<li class="active">
					<a href="#tab_0" data-toggle="tab"> <i class="fa fa-cog"></i> <?php echo fc_lang('基本设置'); ?> </a>
				</li>
				<li class="">
					<a href="#tab_1" data-toggle="tab"> <i class="fa fa-crop"></i> <?php echo fc_lang('数据验证'); ?> </a>
				</li>
				<li class="">
					<a href="#tab_2" data-toggle="tab"> <i class="fa fa-user"></i> <?php echo fc_lang('字段权限'); ?> </a>
				</li>
			</ul>
		</div>
		<div class="portlet-body">
			<div class="tab-content">

				<div class="tab-pane active" id="tab_0">
					<div class="form-body">

						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('别名'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[name]" value="<?php echo htmlspecialchars($data['name']); ?>" id="dr_name" onblur="d_topinyin('fieldname','name');" /></label>
								<span class="help-block" id="dr_name_tips"><?php echo fc_lang('为字段取个名字，例如：文档标题、作者、来源等等'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('名称'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text"  name="data[fieldname]" value="<?php echo $data['fieldname']; ?>" <?php if ($data['id']) { ?>disabled<?php } ?> id="dr_fieldname" /></label>
								<span class="help-block" id="dr_fieldname_tips"><?php echo fc_lang('只能由英文字母、数字组成'); ?></span>
							</div>
						</div>
						<?php if ($ismain) { ?>
						<input name="data[ismain]" type="hidden" value="1" />
						<?php } else { ?>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('主表'); ?>：</label>
							<div class="col-md-9">
								<div class="radio-list">
									<label class="radio-inline"><input type="radio" id="iszb1" name="data[ismain]" value="0" <?php if ($id) { ?>disabled<?php }  echo dr_set_radio('ismain', $data['ismain'], '0', TRUE); ?> /> <?php echo fc_lang('否'); ?></label>
									<label class="radio-inline"><input type="radio" id="iszb2" name="data[ismain]" onclick="dr_confirm_zb('<?php echo fc_lang("主表字段太多时可能会影响性能，您确认要进行此操作吗？"); ?>')" value="1" <?php if ($id) { ?>disabled<?php }  echo dr_set_radio('ismain', $data['ismain'], '1'); ?> /> <?php echo fc_lang('是'); ?></label>
								</div>
								<span class="help-block"><?php echo fc_lang('选“是”时，在list循环或者搜索时可以调用该字段；选“否”时会将字段添加到附表中不能参与list循环或搜索'); ?></span>
							</div>
						</div>
						<?php }  if ($issearch) { ?>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('是否搜索'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[issearch]" value="1" <?php if ($data['issearch']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('选择“是”时，栏目搜索时会用的到，无实际意义'); ?></span>
							</div>
						</div>
						<?php } ?>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('类别'); ?>：</label>
							<div class="col-md-9">
								<label><select class="form-control" id="dr_fieldtype" name="data[fieldtype]" onChange="show_field_option(this.value)" <?php if ($id) { ?>disabled<?php } ?>>
									<option value=""> -- </option>
									<?php if (is_array($ftype)) { $count=count($ftype);foreach ($ftype as $t) { ?>
									<option value="<?php echo $t['id']; ?>" <?php if ($t['id']==$data['fieldtype']) { ?> selected="selected"<?php } ?>> <?php echo $t['name']; ?>（<?php echo $t['id']; ?>） </option>
									<?php } } ?>
									</select></label>
								<label id="dr_loading" style="display:none">&nbsp;&nbsp;&nbsp;<img src="<?php echo THEME_PATH; ?>admin/images/loading-mini.gif" height="10" /></label>
							</div>
						</div>
					</div>
					<div class="form-body" id="dr_option">

					</div>
				</div>
				<div class="tab-pane" id="tab_1">
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('是否必填'); ?>：</label>
							<div class="col-md-9">
								<div class="radio-list">
									<label class="radio-inline"><input type="radio" name="data[setting][validate][required]" value="0" <?php if ($data['setting']['validate']['required']==0) { ?>checked<?php } ?> onclick="set_required(0)" /> <?php echo fc_lang('否'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[setting][validate][required]" value="1" <?php if ($data['setting']['validate']['required']==1) { ?>checked<?php } ?> onclick="set_required(1)" /> <?php echo fc_lang('是'); ?></label>
								</div>
							</div>
						</div>
						<div id="required" style="display:none">
							<div class="form-group">
								<label class="col-md-2 control-label"><?php echo fc_lang('正则验证'); ?>：</label>
								<div class="col-md-9">
									<label><input class="form-control" type="text" name="data[setting][validate][pattern]" value="<?php echo $data['setting']['validate']['pattern']; ?>" id="dr_pattern" size="30" /></label>
									<label><select class="form-control" onchange="javascript:$('#dr_pattern').val(this.value)" name="pattern_select">
										<option value=""><?php echo fc_lang('正则验证'); ?></option>
										<option value="/^[0-9.-]+$/"><?php echo fc_lang('数字'); ?></option>
										<option value="/^[0-9-]+$/"><?php echo fc_lang('整数'); ?></option>
										<option value="/^[a-z]+$/i"><?php echo fc_lang('字母'); ?></option>
										<option value="/^[0-9a-z]+$/i"><?php echo fc_lang('数字+字母'); ?></option>
										<option value="/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/">E-mail</option>
										<option value="/^[0-9]{5,20}$/">QQ</option>
										<option value="/^http:\/\//"><?php echo fc_lang('URL链接'); ?></option>
										<option value="/^(1)[0-9]{10}$/"><?php echo fc_lang('手机号码'); ?></option>
										<option value="/^[0-9-]{6,13}$/"><?php echo fc_lang('电话号码'); ?></option>
										<option value="/^[0-9]{6}$/"><?php echo fc_lang('邮政编码'); ?></option>
									</select></label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-2 control-label"><?php echo fc_lang('验证提示'); ?>：</label>
								<div class="col-md-9">
									<input class="form-control" type="text" name="data[setting][validate][errortips]" value="<?php echo $data['setting']['validate']['errortips']; ?>" id="dr_errortips" />
									<span class="help-block"><?php echo fc_lang('当字段校验未通过时的提示信息，如“标题必须在80字以内”等'); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('禁止修改'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[setting][validate][isedit]" value="1" <?php if ($data['setting']['validate']['isedit']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('是'); ?>" data-off-text="<?php echo fc_lang('否'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('提交之后将不能修改字段值，此选项不针对后台验证'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('XSS过滤'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[setting][validate][xss]" value="1" <?php if ($data['setting']['validate']['xss']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('关闭'); ?>" data-off-text="<?php echo fc_lang('开启'); ?>" data-off-color="success" data-on-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('开启之后相关字符会被替换掉'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('校验函数/方法'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[setting][validate][check]" value="<?php echo $data['setting']['validate']['check']; ?>" id="dr_check" /></label>
								<span class="help-block"><?php echo fc_lang('例如对会员名的重复验证等，格式参考手册，请勿乱填'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('过滤函数'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[setting][validate][filter]" value="<?php echo $data['setting']['validate']['filter']; ?>" id="dr_filter" /></label>
								<span class="help-block"><?php echo fc_lang('如url补全、去除html、生成随机码等等，格式参考手册，请勿乱填'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('字段提示'); ?>：</label>
							<div class="col-md-9">
								<input class="form-control" type="text" name="data[setting][validate][tips]" value="<?php echo $data['setting']['validate']['tips']; ?>" />
								<span class="help-block"><?php echo fc_lang('对字段简短的提示，来说明这个字段是用来干什么的'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('表单附加属性'); ?>：</label>
							<div class="col-md-9">
								<textarea class="form-control" style="height:120px" name="data[setting][validate][formattr]"><?php echo $data['setting']['validate']['formattr']; ?></textarea>
								<span class="help-block"><?php echo fc_lang('可以通过此处加入js事件、ajax验证、css等（慎用style与class）'); ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="tab_2">
					<div class="form-body">

						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('禁用'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[disabled]" value="1" <?php if ($data['disabled']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('是'); ?>" data-off-text="<?php echo fc_lang('否'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('禁用了就不能使用'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('在表单哪个位置显示'); ?>：</label>
							<div class="col-md-9">
								<div class="radio-list">
									<label class="radio-inline"><input type="radio" name="data[setting][is_right]" value="0" <?php if (!$data['setting']['is_right']) { ?>checked<?php } ?>  /> <?php echo fc_lang('默认'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[setting][is_right]" value="1" <?php if (1==$data['setting']['is_right']) { ?>checked<?php } ?> /> <?php echo fc_lang('右侧'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[setting][is_right]" value="2" <?php if (2==$data['setting']['is_right']) { ?>checked<?php } ?> /> <?php echo fc_lang('手动'); ?></label>
								</div>
								<span class="help-block"><?php echo fc_lang('选择“右侧”时，发布模块内容时字段显示在右侧部分; 当选择"手动"时，需要开发者手动调用 ');  echo '{';?>$diyfield}
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('后台不显示该字段'); ?>：</label>
							<div class="col-md-9">
								<div class="checkbox-list">
									<?php if (is_array($role)) { $count=count($role);foreach ($role as $t) {  if ($t['id']>1) { ?>
									<label class="checkbox-inline"><input type="checkbox" name="data[setting][show_admin][]" value="<?php echo $t['id']; ?>" <?php if (@in_array($t['id'], $data['setting']['show_admin'])) { ?> checked<?php } ?> /> <?php echo $t['name']; ?></label>
									<?php }  } } ?>
								</div>
								<span class="help-block"><?php echo fc_lang('勾选之后，该角色将不会看到这个字段'); ?></span>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('前端显示'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[ismember]" value="1" <?php if ($data['ismember']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('是'); ?>" data-off-text="<?php echo fc_lang('否'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('如果想前端表单或者会员中心不显示那么请选择“否”，否则选“是”'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('前端不显示该字段'); ?>：</label>
							<div class="col-md-9">
								<div class="checkbox-list">
									<label class="checkbox-inline"><input type="checkbox" name="data[setting][show_member][]" value="0" <?php if (@in_array(0, $data['setting']['show_member'])) { ?> checked<?php } ?> /> <?php echo fc_lang('游客'); ?></label>
									<?php $return = $this->list_tag("action=cache name=MEMBER.group"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
									<label class="checkbox-inline"><input type="checkbox" name="data[setting][show_member][]" value="<?php echo $t['id']; ?>" <?php if (@in_array($t['id'], $data['setting']['show_member'])) { ?> checked<?php } ?> /> <?php echo $t['name']; ?></label>
									<?php } } ?>
								</div>
								<span class="help-block"><?php echo fc_lang('勾选之后，该会员组将不会看到这个字段，如果“前端显示”关闭了此功能就无效'); ?></span>
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
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>