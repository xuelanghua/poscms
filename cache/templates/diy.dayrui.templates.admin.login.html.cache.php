<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
	<meta charset="utf-8" />
	<title><?php echo fc_lang('%s管理中心', SITE_NAME); ?></title>
	<meta name="robots" content="noindex,nofollow">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN THEME GLOBAL STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
	<!-- END THEME GLOBAL STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/pages/css/login-4.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/my.css" rel="stylesheet" type="text/css" />
	<!--[if lt IE 9]>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/respond.min.js"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/excanvas.min.js"></script>
	<![endif]-->
	<!-- BEGIN CORE PLUGINS -->
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/js.cookie.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>

	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>

	<script src="<?php echo THEME_PATH; ?>admin/global/scripts/app.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php $bg = array('"'.THEME_PATH.'admin/pages/media/bg/1.jpg"',
				'"'.THEME_PATH.'admin/pages/media/bg/2.jpg"',
				'"'.THEME_PATH.'admin/pages/media/bg/3.jpg"',
				'"'.THEME_PATH.'admin/pages/media/bg/4.jpg"');
		shuffle($bg);
		?>
		var sys_theme = "<?php echo THEME_PATH; ?>admin/";
		jQuery(document).ready(function() {
			$.backstretch([
						<?php echo implode(',', $bg); ?>
					], {
						fade: 1000,
						duration: 8000
					}
			);
			top.$('.page-loading').remove();
			<?php if ($username) { ?>
			$("#password").focus();
			<?php } else { ?>
			$("#username").focus();
			<?php }  if ($error) {  if ($id) { ?>
				$("#username").focus();
				<?php } else { ?>
				$("#password").focus();
				<?php }  } ?>
		});

		window.onload = function() {
			if (!window.applicationCache) {
				alert("你的浏览器不支持HTML5，推荐使用Chrome或IE高版本浏览器");
			}
		}
	</script>
</head>
<!-- END HEAD -->

<body class=" login">
<!-- BEGIN LOGO -->
<div class="logo login-logo">
	<a href="<?php echo SITE_URL; ?>"><?php echo DR_NAME; ?></a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	<form class="login-form" action="" method="post">
		<h3 class="form-title"><?php echo fc_lang('后台中心'); ?></h3>
		<?php if ($error) { ?>
		<div class="alert alert-danger">
			<button class="close" data-close="alert"></button>
			<span> <?php echo $error; ?> </span>
		</div>
		<?php } ?>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9"><?php echo fc_lang('账号'); ?></label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" id="username" name="username" value="<?php echo $username; ?>" placeholder="<?php echo fc_lang('账号'); ?>" /> </div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9"><?php echo fc_lang('密码'); ?></label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<input class="form-control placeholder-no-fix" type="password" autocomplete="off" id="password" name="password" placeholder="<?php echo fc_lang('密码'); ?>" /> </div>
		</div>
		<?php if (SITE_ADMIN_CODE) {  if (strlen(SYS_GEE_CAPTCHA_ID) > 10) { ?>
		<div class="form-group" style="padding-left: 4px;">
			<?php echo dr_geetest('float', 'mysubmit'); ?>
		</div>
		<?php } else { ?>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9"><?php echo fc_lang('验证码'); ?></label>
			<div class="input-icon">
				<label style="float: left"><input class="form-control placeholder-no-fix" type="text" autocomplete="off"  name="code" style="width:150px;" placeholder="<?php echo fc_lang('验证码'); ?>" /></label>
				<label style="float: right" class="dr_code"><?php echo dr_code(120, 35); ?></label>
			</div>
		</div>
		<?php }  } ?>
		<div class="form-actions">
			<label class="checkbox"></label>
			<button type="submit" class="btn green pull-right"> <?php echo fc_lang('登录'); ?> </button>
		</div>

	</form>
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright"> <?php echo SITE_NAME; ?> </div>
<!-- END COPYRIGHT -->

</body>

</html>
