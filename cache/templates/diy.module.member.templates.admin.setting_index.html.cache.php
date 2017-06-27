<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	<?php if ($result) { ?>
	dr_tips('<?php echo fc_lang('操作成功，正在刷新...'); ?>', 3, 1);
	<?php } ?>
});
</script>

<form class="form-horizontal" action="" method="post" id="myform" name="myform">
	<input name="page" id="mypage" type="hidden" value="<?php echo $page; ?>" />
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

	<div class="portlet light bordered myfbody">
		<div class="portlet-title tabbable-line">
			<ul class="nav nav-tabs" style="float:left;">
				<li class="<?php if ($page==0) { ?>active<?php } ?>">
					<a href="#tab_0" data-toggle="tab" onclick="$('#mypage').val('0')"> <i class="fa fa-cog"></i> <?php echo fc_lang('基本设置'); ?> </a>
				</li>
				<li class="<?php if ($page==1) { ?>active<?php } ?>">
					<a href="#tab_1" data-toggle="tab" onclick="$('#mypage').val('1')"> <i class="fa fa-reorder"></i> <?php echo fc_lang('绑定域名'); ?> </a>
				</li>
				<li class="<?php if ($page==2) { ?>active<?php } ?>">
					<a href="#tab_2" data-toggle="tab" onclick="$('#mypage').val('2')"> <i class="fa fa-user-plus"></i> <?php echo fc_lang('注册设置'); ?> </a>
				</li>
				<li class="<?php if ($page==3) { ?>active<?php } ?>">
					<a href="#tab_3" data-toggle="tab" onclick="$('#mypage').val('3')"> <i class="fa fa-chevron-circle-right"></i> <?php echo fc_lang('登录设置'); ?> </a>
				</li>
				<li class="<?php if ($page==4) { ?>active<?php } ?>">
					<a href="#tab_4" data-toggle="tab" onclick="$('#mypage').val('4')"> <i class="fa fa-slack"></i> Ucenter </a>
				</li>
				<li class="<?php if ($page==5) { ?>active<?php } ?>">
					<a href="#tab_5" data-toggle="tab" onclick="$('#mypage').val('5')"> <i class="fa fa-tasks"></i> <?php echo fc_lang('字段格式'); ?> </a>
				</li>
				<li class="<?php if ($page==9) { ?>active<?php } ?>">
					<a href="#tab_9" data-toggle="tab" onclick="$('#mypage').val('9')"> <i class="fa fa-code"></i> UCSSO </a>
				</li>
			</ul>
		</div>
		<div class="portlet-body">
			<div class="tab-content">

				<div class="tab-pane  <?php if ($page==9) { ?>active<?php } ?>" id="tab_9">
					<div class="form-body">

						<div class="form-group">
							<label class="col-md-2 control-label">UCSSO：</label>
							<div class="col-md-9">
								<div class="radio-list">
									<label class="radio-inline"><input type="radio" name="data[ucsso]" value="1" <?php echo dr_set_radio('ucsso', $data['ucsso'], '1'); ?> /> <?php echo fc_lang('开启'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[ucsso]" value="0" <?php echo dr_set_radio('ucsso', $data['ucsso'], '0', TRUE); ?> /> <?php echo fc_lang('关闭'); ?></label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">网站通信地址：</label>
							<div class="col-md-9">
								<label><input readonly class="form-control input-large" type="text" value="<?php echo trim(SITE_URL, '/'); ?>"/></label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">其他通信地址：</label>
							<div class="col-md-9">
								<label><textarea readonly style="height:80px" class="form-control input-large"><?php if (is_array($synurl)) { $count=count($synurl);foreach ($synurl as $url) {  if ($url != trim(SITE_URL, '/')) {  echo $url;  echo PHP_EOL;  }  } } ?></textarea></label>
								<span class="help-block">多站点或者绑定域名时，将这些URL复制到UCSSO中的“网站其他地址”中</span>

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label">通信配置代码：</label>
							<div class="col-md-9">
								<textarea  style="height:180px" class="form-control" name="data[ucssocfg]"><?php echo $data['ucssocfg']; ?></textarea>
								<span class="help-block">通信配置代码复制到这里</span>

							</div>
						</div>

					</div>
				</div>

				<div class="tab-pane  <?php if ($page==0) { ?>active<?php } ?>" id="tab_0">
					<div class="form-body">

						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('是否强制上传头像'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[avatar]" value="1" <?php if ($data['avatar']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('是否强制完善资料'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[complete]" value="1" <?php if ($data['complete']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('在快捷授权登录时，请关闭此次，否则可能出现审核被拒绝的情况'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('是否强制实名认证'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[auth]" value="1" <?php if ($data['auth']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block">需要安装“会员实名认证”应用才能生效</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('是否开启手机认证'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[ismobile]" value="1" <?php if ($data['ismobile']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('需要开启短信功能，“系统”-“短信系统”-“设置账号”'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('是否强制手机认证'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[mobile]" value="1" <?php if ($data['mobile']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('需要开启短信功能，“系统”-“短信系统”-“设置账号”'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('会员中心分页数量'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[pagesize]" value="<?php echo $data['pagesize']; ?>" ></label>
								<span class="help-block"><?php echo fc_lang('会员中心数据每页显示数量'); ?></span>
							</div>
						</div>


					</div>
				</div>

				<div class="tab-pane  <?php if ($page==1) { ?>active<?php } ?>" id="tab_1">
					<div class="form-body">

						<?php if (is_array($ci->site_info)) { $count=count($ci->site_info);foreach ($ci->site_info as $sid=>$t) { ?>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo dr_strcut($t['SITE_NAME'], 25); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[domain][<?php echo $sid; ?>]" value="<?php echo $data['domain'][$sid]; ?>" ></label>

								<?php if ($data['domain'][$sid]) {  if ($data['domain'][$sid] == SITE_DOMAIN) { ?>
								<span class="help-block"><?php echo fc_lang('此域名【%s】不能与当前站点域名相同', $data['domain'][$sid]); ?></span>
								<?php } else { ?>
								<script>
									$.get("<?php echo dr_url('home/domain', array('domain' => $data['domain'][$sid])); ?>", function(data){
										if (data) {
											$("#dr_domian_<?php echo $sid; ?>").html(data);
										} else {
											$("#dr_domian_<?php echo $sid; ?>").hide();
										}
									});
								</script>
								<span id="dr_domian_<?php echo $sid; ?>" class="help-block"></span>
								<?php }  } else { ?>
								<span class="help-block"><?php echo fc_lang('域名格式：i.dayrui.com'); ?></span>
								<?php } ?>
							</div>
						</div>
						<?php } } ?>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('URL规则'); ?>：</label>
							<div class="col-md-9">
								<label>
									<select class="form-control" name="data[urlrule]">
										<option value="0"> -- </option>
										<?php $return_u = $this->list_tag("action=cache name=urlrule  return=u"); if ($return_u) extract($return_u); $count_u=count($return_u); if (is_array($return_u)) { foreach ($return_u as $key_u=>$u) {  if ($u['type']==6) { ?><option value="<?php echo $u['id']; ?>" <?php if ($u['id']==$data['urlrule']) { ?>selected<?php } ?>> <?php echo $u['name']; ?> </option><?php }  } } ?>
									</select>
								</label>
								<label>&nbsp;&nbsp;<a href="<?php echo dr_url('urlrule/index'); ?>" style="color:blue !important"><?php echo fc_lang('[URL规则管理]'); ?></a></label>
							</div>
						</div>

					</div>
				</div>

				<div class="tab-pane  <?php if ($page==2) { ?>active<?php } ?>" id="tab_2">
					<div class="form-body">


						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('会员注册'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[register]" value="1" <?php if ($data['register']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('注册验证码'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[regcode]" value="1" <?php if ($data['regcode']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('注册时开通空间'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[regspace]" value="1" <?php if ($data['regspace']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('注册字段选择'); ?>：</label>
							<div class="col-md-9">
								<div class="checkbox-list">
									<label class="checkbox-inline"><input type="checkbox" name="data[regfield][]" value="username" <?php if (@in_array('username', $data['regfield'])) { ?> checked<?php } ?> /> <?php echo fc_lang('账号'); ?></label>
									<label class="checkbox-inline"><input type="checkbox" name="data[regfield][]" value="phone" <?php if (@in_array('phone', $data['regfield'])) { ?> checked<?php } ?> /> <?php echo fc_lang('手机号'); ?></label>
									<label class="checkbox-inline"><input type="checkbox" name="data[regfield][]" value="email" <?php if (@in_array('email', $data['regfield'])) { ?> checked<?php } ?> /> <?php echo fc_lang('邮箱'); ?></label>
								</div>
								<span class="help-block"><?php echo fc_lang('至少选择一个字段或多个字段组合'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('新会员审核'); ?>：</label>
							<div class="col-md-9">
								<div class="radio-list">
									<label class="radio-inline"><input type="radio" name="data[regverify]" value="1" <?php echo dr_set_radio('regverify', $data['regverify'], '1'); ?> /> <?php echo fc_lang('邮件'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[regverify]" value="2" <?php echo dr_set_radio('regverify', $data['regverify'], '2'); ?> /> <?php echo fc_lang('人工'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[regverify]" value="3" <?php echo dr_set_radio('regverify', $data['regverify'], '3'); ?> /> <?php echo fc_lang('手机验证码'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[regverify]" value="0" <?php echo dr_set_radio('regverify', $data['regverify'], '0', TRUE); ?> /> <?php echo fc_lang('关闭'); ?></label>
								</div>
								<span class="help-block"><?php echo fc_lang('若使用手机验证码必须选择手机注册模式'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('同一IP注册间隔限制'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[regiptime]" value="<?php echo $data['regiptime']; ?>" ></label>
								<span class="help-block"><?php echo fc_lang('单位小时'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('会员名规则（正则）'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[regnamerule]" id="dr_regnamerule" value="<?php echo $data['regnamerule']; ?>"/></label>
									<label><select class="form-control" onchange="javascript:$('#dr_regnamerule').val(this.value)" name="pattern_select">
									<option value=""><?php echo fc_lang('正则验证'); ?></option>
									<option value="/.*/"><?php echo fc_lang('不限制'); ?></option>
									<option value="/^[0-9.-]+$/"><?php echo fc_lang('数字'); ?></option>
									<option value="/^[0-9-]+$/"><?php echo fc_lang('整数'); ?></option>
									<option value="/^[a-z]+$/i"><?php echo fc_lang('字母'); ?></option>
									<option value="/^[0-9a-z]+$/i"><?php echo fc_lang('数字+字母'); ?></option>
									<option value="/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/">E-mail</option>
									<option value="/^[0-9]{5,20}$/">QQ</option>
									<option value="/^(1)[0-9]{10}$/"><?php echo fc_lang('手机号码'); ?></option>
									<option value="/^[0-9-]{6,13}$/"><?php echo fc_lang('电话号码'); ?></option>
									<option value="/^[0-9]{6}$/"><?php echo fc_lang('邮政编码'); ?></option>
								</select></label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('不允许注册的会员名'); ?>：</label>
							<div class="col-md-9">
								<input class="form-control" type="text" name="data[regnotallow]" value="<?php echo $data['regnotallow']; ?>" >
								<span class="help-block"><?php echo fc_lang('多个会员名以分号“,”分隔'); ?></span>
							</div>
						</div>

					</div>
				</div>

				<div class="tab-pane  <?php if ($page==3) { ?>active<?php } ?>" id="tab_3">
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('登录验证码'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[logincode]" value="1" <?php if ($data['logincode']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('开启'); ?>" data-off-text="<?php echo fc_lang('关闭'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('快捷登陆模式'); ?>：</label>
							<div class="col-md-9">
								<input type="checkbox" name="data[regoauth]" value="1" <?php if ($data['regoauth']) { ?>checked<?php } ?> data-on-text="<?php echo fc_lang('直接注册'); ?>" data-off-text="<?php echo fc_lang('绑定账号'); ?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
								<span class="help-block"><?php echo fc_lang('绑定账号指必须注册一个本站账号；直接注册指用登录者的昵称直接注册本站账号'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('默认登录超时时间'); ?>：</label>
							<div class="col-md-9">
								<label><input class="form-control" type="text" name="data[loginexpire]" value="<?php echo $data['loginexpire'] ? $data['loginexpire'] : 86400; ?>" ></label>
								<span class="help-block"><?php echo fc_lang('单位秒，默认为86400秒，超时之后自动退出账号'); ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane  <?php if ($page==4) { ?>active<?php } ?>" id="tab_4">
					<div class="form-body">


						<div class="form-group">
							<label class="col-md-2 control-label">Ucenter：</label>
							<div class="col-md-9">
								<div class="radio-list">
									<label class="radio-inline"><input type="radio" name="data[ucenter]" value="1" <?php echo dr_set_radio('ucenter', $data['ucenter'], '1'); ?> /> <?php echo fc_lang('开启'); ?></label>
									<label class="radio-inline"><input type="radio" name="data[ucenter]" value="0" <?php echo dr_set_radio('ucenter', $data['ucenter'], '0', TRUE); ?> /> <?php echo fc_lang('关闭'); ?></label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('通信的主URL'); ?>：</label>
							<div class="col-md-9">
								<label><input readonly class="form-control input-large" type="text" name="ucenterapi" value="<?php echo trim(SITE_URL, '/'); ?>"/></label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('通信的其他URL'); ?>：</label>
							<div class="col-md-9">
								<label><textarea readonly style="height:80px" class="form-control input-large" name="ucenterapi"><?php if (is_array($synurl)) { $count=count($synurl);foreach ($synurl as $url) {  if ($url != trim(SITE_URL, '/')) {  echo $url;  echo PHP_EOL;  }  } } ?></textarea></label>
								<span class="help-block"><?php echo fc_lang('多站点或者绑定域名时，将这些URL复制到Ucenter中的“应用的其他 URL”中'); ?></span>

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('UC配置信息'); ?>：</label>
							<div class="col-md-9">
								<textarea  style="height:180px" class="form-control" name="data[ucentercfg]"><?php echo $data['ucentercfg']; ?></textarea>

							</div>
						</div>

					</div>
				</div>

				<div class="tab-pane  <?php if ($page==5) { ?>active<?php } ?>" id="tab_5">
					<div class="form-body">


						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('PC端格式'); ?>：</label>
							<div class="col-md-9">
								<textarea  style="height:100px" class="form-control" name="data[field]"><?php echo $data['field']; ?></textarea>

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('移动端格式'); ?>：</label>
							<div class="col-md-9">
								<textarea  style="height:100px" class="form-control" name="data[mbfield]"><?php echo $data['mbfield']; ?></textarea>

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('合并字段PC端格式'); ?>：</label>
							<div class="col-md-9">
								<textarea  style="height:100px" class="form-control" name="data[mergefield]"><?php echo $data['mergefield']; ?></textarea>

							</div>
						</div>
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('合并字段移动端格式'); ?>：</label>
							<div class="col-md-9">
								<textarea  style="height:100px" class="form-control" name="data[mbmergefield]"><?php echo $data['mbmergefield']; ?></textarea>

								<span class="help-block"><?php echo fc_lang('字段英文名称：{name}；字段中文名称：{text}；表单显示：{value}<br>此格式用于定义会员中心的所有自定义字段输出格式，如资料修改、文章发布/修改等'); ?></span>
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