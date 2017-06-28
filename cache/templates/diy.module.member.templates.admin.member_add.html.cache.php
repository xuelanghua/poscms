<script type="text/javascript">
//防止回车提交表单
$(function() {
	document.onkeydown = function(e){ 
		var ev = document.all ? window.event : e;
		if (ev.keyCode==13) {
			$("#mark").val("1"); // 标识不能提交表单
		}
	}
});
function dr_form_check() {
	if ($("#mark").val() == 0) { 
		return true;
	} else {
		return false;
	}
}
<?php if ($all) { ?>
$('.dr_all').show();$('.dr_one').hide();
<?php } else { ?>
$('.dr_all').hide();$('.dr_one').show();
<?php } ?>
</script>
<form style="width:450px;" class="form-horizontal" action="" method="post" id="myform" name="myform" onsubmit="return dr_form_check()">
<input name="mark" id="mark" type="hidden" value="0">
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo fc_lang('模式'); ?>：</label>
        <div class="col-md-9">
            <div class="radio-list" style="padding-left: 20px;">
                <label class="radio-inline"><input name="all" type="radio" value="0" onClick="$('.dr_all').hide();$('.dr_one').show();" <?php if (!$all) { ?>checked<?php } ?>> <?php echo fc_lang('单个'); ?></label>
                <label class="radio-inline"><input name="all" type="radio" value="1" onClick="$('.dr_all').show();$('.dr_one').hide();" <?php if ($all) { ?>checked<?php } ?>> <?php echo fc_lang('批量'); ?></label>
            </div>
        </div>
    </div>
    <div class="form-group dr_one">
        <label class="col-md-3 control-label"><?php echo fc_lang('账号'); ?>：</label>
        <div class="col-md-7">
            <input class="form-control" type="text" name="data[username]" id="dr_username" value="<?php echo $data['username']; ?>" >
        </div>
    </div>
    <div class="form-group dr_one">
        <label class="col-md-3 control-label"><?php echo fc_lang('密码'); ?>：</label>
        <div class="col-md-7">
            <input class="form-control" type="text" name="data[password]" id="dr_password" value="<?php echo $data['password']; ?>" >
        </div>
    </div>
    <div class="form-group dr_one">
        <label class="col-md-3 control-label"><?php echo fc_lang('邮箱'); ?>：</label>
        <div class="col-md-7">
            <input class="form-control" type="text" name="data[email]" id="dr_email" value="<?php echo $data['email']; ?>" >
        </div>
    </div>
    <div class="form-group dr_one">
        <label class="col-md-3 control-label"><?php echo fc_lang('手机'); ?>：</label>
        <div class="col-md-7">
            <input class="form-control" type="text" name="data[phone]" id="dr_phone" value="<?php echo $data['phone']; ?>" >
        </div>
    </div>
    <div class="form-group dr_all">
        <label class="col-md-3 control-label"><?php echo fc_lang('批量'); ?>：</label>
        <div class="col-md-9">
            <textarea class="form-control" style="width:260px;height:150px" name="info" /><?php echo $info; ?></textarea>
            <font color="#999999" id="dr_info_tips"><?php echo fc_lang('格式：会员名称|密码|email|手机号 [回车换行]'); ?></font>
        </div>
    </div>
    <div class="form-group ">
        <label class="col-md-3 control-label"><?php echo fc_lang('会员组'); ?>：</label>
        <div class="col-md-9">
            <label>
            <select name="data[groupid]" class="form-control">
                <option value=""> -- </option>
                <?php $return = $this->list_tag("action=cache name=MEMBER.group"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) {  if ($t['id']) { ?>
                <option value="<?php echo $t['id']; ?>" <?php if ($t['id']==$data['groupid']) { ?>selected<?php } ?>> <?php echo $t['name']; ?> </option>
                <?php }  } } ?>
            </select>
            </label>
        </div>
    </div>
</div>
</form>