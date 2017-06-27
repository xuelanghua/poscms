<script type="text/javascript">
$(function() { //防止回车提交表单
	document.onkeydown = function(e){ 
		var ev = document.all ? window.event : e;
		if (ev.keyCode==13) {
			$("#mark").val("1"); // 标识不能提交表单
		}
	}
	$(".t_1").hide();
	$(".t_2").hide();
	$(".t_<?php echo $menu_url; ?>").show();
	$("._type").click(function(){
		var t = $(this).val();	
		$(".t_1").hide();
		$(".t_2").hide();
		$(".t_"+t).show();
	});
	$(".table_form th").last().css('border','none');
	$(".table_form td").last().css('border','none');
});
function dr_form_check() {
	if ($("#dr_name").val() == '') {
		dr_form_tips('name', false, '<?php echo dr_lang("名称不能为空"); ?>');
		return false;
	} else {
		dr_form_tips('name', true, '&nbsp;');
	}
	if ($("#mark").val() == 0) { 
		return true;
	} else {
		return false;
	}
}
</script>
<div class="table-list" style="width:550px;">
<form action="" method="post" id="myform" name="myform" onsubmit="return dr_form_check()">
<input name="mark" id="mark" type="hidden" value="0">
<input name="data[istop]" type="hidden" value="<?php echo $data['istop']; ?>">
<input name="data[pid]" type="hidden" value="<?php echo $data['pid']; ?>">
<table width="100%" class="table_form">
<tr>
    <th width="100"><font color="red">*</font>&nbsp;<?php echo fc_lang('类型'); ?>： </th>
    <td><?php echo $menu_name; ?></td>
</tr>
<?php if ($select) { ?>
<tr>
    <th width="100"><font color="red">*</font>&nbsp;<?php echo fc_lang('上级'); ?>： </th>
    <td><?php echo $select; ?></td>
</tr>
<?php } ?>
<tr>
    <th><font color="red">*</font>&nbsp;<?php echo fc_lang('名称'); ?>： </th>
    <td>
    <input class="input-text" type="text" name="data[name]" id="dr_name" value="<?php echo $data['name']; ?>" size="20" />
    <div class="onShow" id="dr_name_tips"><?php echo fc_lang('给它一个描述名称'); ?></div>
    </td>
</tr>
<?php if ($menu_type) { ?>
<tr>
    <th>&nbsp;<?php echo fc_lang('类型'); ?>： </th>
    <td>
    <input name="_type" type="radio" value="2" class="_type" <?php if ($menu_url==2) { ?> checked<?php } ?>>&nbsp;&nbsp;<label><?php echo fc_lang('CI风格'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="_type" type="radio" value="1" class="_type" <?php if ($menu_url==1) { ?> checked<?php } ?>>&nbsp;&nbsp;<label><?php echo fc_lang('URL链接'); ?></label>
    </td>
</tr>
<tr class="t_2">
    <th>&nbsp;Directory： </th>
    <td>
    <input name="data[directory]" type="radio" value="admin" <?php if (empty($uri['directory']) || $uri['directory']=='admin') { ?> checked<?php } ?>>&nbsp;&nbsp;<label>admin</label>&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="data[directory]" type="radio" value="member" <?php if ($uri['directory']=='member') { ?> checked<?php } ?>>&nbsp;&nbsp;<label>member</label>
    <div class="onShow" id="dr_s_tips"><?php echo fc_lang('在fc中被定义为admin或者member'); ?></div>
    </td>
</tr>
<tr class="t_2">
    <th>&nbsp;<?php echo fc_lang('目录'); ?>： </th>
    <td>
    <input class="input-text" type="text" name="data[dir]" id="dr_c" value="<?php echo $uri['dir']; ?>" size="20" />
    <div class="onShow" id="dr_s_tips"><?php echo fc_lang('模块、应用或者会员目录'); ?></div>
    </td>
</tr>
<tr class="t_2">
    <th>&nbsp;Class： </th>
    <td>
    <input class="input-text" type="text" name="data[class]" id="dr_c" value="<?php echo $uri['class']; ?>" size="20" />
    <div class="onShow" id="dr_c_tips"><?php echo fc_lang('CI控制器名称，参数c'); ?></div>
    </td>
</tr>
<tr class="t_2">
    <th>&nbsp;Method： </th>
    <td>
	<input class="input-text" type="text" name="data[method]" id="dr_m" value="<?php echo $uri['method']; ?>" size="20" />
    <div class="onShow" id="dr_m_tips"><?php echo fc_lang('CI方法名称，参数m'); ?></div>
    </td>
</tr>
<tr class="t_2">
    <th>&nbsp;<?php echo fc_lang('附加参数'); ?>： </th>
    <td>
	<input class="input-text" type="text" name="data[param]" id="dr_param" value="<?php echo $uri['param_str']; ?>" size="20" />
    <div class="onShow" id="dr_param_tips"><?php echo fc_lang('格式：参数1/值/参数2/值..'); ?></div>
    </td>
</tr>
<tr class="t_1">
    <th>&nbsp;Url： </th>
    <td>
	<input class="input-text" type="text" name="data[url]" id="dr_url" value="<?php echo $data['url']; ?>" size="30" />
    <div class="onShow" id="dr_url_tips"><?php echo fc_lang('指定一个URL地址'); ?></div>
    </td>
</tr>
<?php } ?>
<tr>
    <th>&nbsp;Ico： </th>
    <td>
        <input class="input-text" type="text" name="data[icon]" value="<?php echo $data['icon']; ?>" size="20" />
        <div class="onShow"><a href="<?php echo dr_member_url('api/icon'); ?>" target="_blank"><?php echo fc_lang('菜单前面的图标，点击查看更多图标'); ?></a></div>
    </td>
</tr>
</table>
</form>
</div>