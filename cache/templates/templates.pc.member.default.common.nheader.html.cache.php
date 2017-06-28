<!DOCTYPE html>
<!--[if IE 8]> <html lang="zh-cn" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="zh-cn" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="zh-cn">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title><?php if ($meta_name) {  echo $meta_name; ?> - <?php } ?>会员中心</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta content="www.dayrui.com" name="author" />
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN THEME GLOBAL STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/global/css/components-rounded.min.css" rel="stylesheet" id="style_components" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
	<!-- END THEME GLOBAL STYLES -->
	<!-- BEGIN THEME LAYOUT STYLES -->
	<link href="<?php echo MEMBER_THEME_PATH; ?>layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo MEMBER_THEME_PATH; ?>layouts/layout/css/themes/light2.min.css" rel="stylesheet" type="text/css" id="style_color">
	<link href="<?php echo MEMBER_THEME_PATH; ?>layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo THEME_PATH; ?>admin/pages/css/error.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
	<!-- END THEME LAYOUT STYLES -->
	<link href="<?php echo THEME_PATH; ?>admin/css/table_form.css" rel="stylesheet" type="text/css" />
	<!--[if lt IE 9]>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/respond.min.js"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/excanvas.min.js"></script>
	<![endif]-->
	<script type="text/javascript">var memberpath = "<?php echo MEMBER_PATH; ?>";</script>
	<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/<?php echo SITE_LANGUAGE; ?>.js"></script>
	<!-- BEGIN CORE PLUGINS -->
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/js.cookie.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
	<!-- END CORE PLUGINS -->
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
	<script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>
	<!-- BEGIN THEME GLOBAL SCRIPTS -->
	<script src="<?php echo THEME_PATH; ?>admin/global/scripts/app.min.js" type="text/javascript"></script>
	<!-- END THEME GLOBAL SCRIPTS -->
	<!-- BEGIN THEME LAYOUT SCRIPTS -->
	<script src="<?php echo MEMBER_THEME_PATH; ?>layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
	<script src="<?php echo MEMBER_THEME_PATH; ?>layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
	<script src="<?php echo MEMBER_THEME_PATH; ?>layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
	<!-- END THEME LAYOUT SCRIPTS -->
	<link rel="stylesheet" href="<?php echo THEME_PATH; ?>js/ui-dialog.css">
	<script type="text/javascript"t src="<?php echo THEME_PATH; ?>js/dialog-plus.js"></script>
	<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
	<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/validate.js"></script>
	<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/admin.js"></script>
	<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dayrui.js"></script>
	<script type="text/javascript">
		$(function(){
			$.ajax({type: "GET", url:dr_url, dataType:'jsonp', jsonp:"callback", async: false,
				success: function (data) {
					if (data.status) {
						$(".dr_notece_num").html(data.status);
						dr_flash_title();
					} else {
						$(".dr_notece_num").html('0');
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) {

				}
			});
			$('.mysubmit').click(function(){
				dr_tips('数据处理中...', 3, 2);
			});
		});

		var dr_url = "<?php echo dr_member_url('api/notice'); ?>&"+Math.random();
		var dr_step = 0;
		var dr_caltitle = "【　　　】"+document.title;
		var dr_callbacktitle = "【新提醒】"+document.title;

		function dr_flash_title() {
			dr_step++;
			if (dr_step==3) {
				dr_step=1;
			}
			if (dr_step==1) {
				document.title=dr_callbacktitle;
			}
			if (dr_step==2) {
				document.title=dr_caltitle;
			}
			setTimeout("dr_flash_title()", 500);
		}


		function dr_selected2() {
			if ($("#dr_select").attr('ck') == "1") {
				$(".dr_select").removeAttr("checked");
				$("#dr_select").prop('ck', 0);
			} else {
				$(".dr_select").prop("checked", true);
				$("#dr_select").attr('ck', 1);
			}
		}
	</script>
	<style>
	.input-text-c, .input-text, .measure-input, textarea, input.date, input.endDate, .input-focus {
		border: 1px solid #c2cad8 !important;
		background: none;
	}
	.edui-default .edui-editor {
		border: 1px solid #c2cad8 !important;
	}
	.picList {
		padding: 0 10px;
	}
	</style>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-boxed">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner container">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="<?php echo dr_member_url(); ?>">
				<img src="<?php echo MEMBER_THEME_PATH; ?>layouts/layout/img/logo.png" alt="logo" class="logo-default" />
			</a>
			<div class="menu-toggler sidebar-toggler"> </div>
		</div>
		<!-- END LOGO -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<li class="dropdown dropdown-extended dropdown-notification">
					<a href="<?php echo SITE_URL; ?>" target="_blank" title="去首页" class="dropdown-toggle" >
						<i class="icon-home"></i>
					</a>
					<ul class="dropdown-menu">
					</ul>
				</li>
				<?php if (IS_MOBILE) { ?>
				<li class="dropdown dropdown-extended dropdown-notification">
					<a href="<?php echo dr_member_url('notice/go'); ?>"  class="dropdown-toggle" >
						<i class="icon-bell"></i>
						<span class="badge badge-default dr_notece_num"> 0 </span>
					</a>
					<ul class="dropdown-menu">
					</ul>
				</li>
				<li class="dropdown dropdown-extended dropdown-inbox">
					<a href="<?php echo dr_member_url('pm/index'); ?>" class="dropdown-toggle">
						<i class="icon-envelope-open"></i>
						<span class="badge badge-default"> <?php if ($newpm) { ?>新<?php } else { ?>0<?php } ?> </span>
					</a>
					<ul class="dropdown-menu">
					</ul>
				</li>
				<?php } else { ?>
				<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i class="icon-bell"></i>
						<span class="badge badge-default dr_notece_num"> 0 </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3>您有<span class="bold dr_notece_num">0</span>条未读提醒消息</h3>
							<a href="<?php echo dr_member_url('notice/go'); ?>">点击查看</a>
						</li>
					</ul>
				</li>
				<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i class="icon-envelope-open"></i>
						<span class="badge badge-default"> <?php if ($newpm) { ?>新<?php } else { ?>0<?php } ?> </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3><?php if ($newpm) { ?>您有新的未读站内短消息<?php } else { ?>您没有未读短消息<?php } ?></h3>
							<a href="<?php echo dr_member_url('pm/index'); ?>">点击查看</a>
						</li>
					</ul>
				</li>
				<?php } ?>
				<li class="dropdown dropdown-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<img class="img-circle" src="<?php echo $member['avatar_url']; ?>" />
						<span class="username username-hide-on-mobile"> <?php echo $member['username']; ?> </span>
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a href="<?php echo dr_member_url('account/index'); ?>">
								<i class="icon-user"></i> 我的资料 </a>
						</li>
						<li>
							<a href="<?php echo dr_member_url('account/password'); ?>">
								<i class="icon-lock"></i> 修改密码 </a>
						</li>
						<li>
							<a href="<?php echo dr_member_url('account/avatar'); ?>">
								<i class="icon-picture"></i> 上传头像
							</a>
						</li>
						<li>
							<a href="<?php echo dr_member_url('notice/go'); ?>">
								<i class="icon-bell"></i> 提醒消息
								<span class="badge dr_notece_num"> 0 </span>
							</a>
						</li>
						<li class="divider"> </li>
						<li><a href="javascript:;" onclick="dr_loginout('退出成功')">
								<i class="icon-key"></i> 我要退出 </a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"> </div>
<!-- END HEADER & CONTENT DIVIDER -->
<div class="container">
	<!-- BEGIN CONTAINER -->
	<div class="page-container">
		<!-- BEGIN SIDEBAR -->
		<div class="page-sidebar-wrapper">
			<!-- BEGIN SIDEBAR -->
			<div class="page-sidebar navbar-collapse collapse">
				<!-- BEGIN SIDEBAR MENU -->
				<ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
					<li class="sidebar-toggler-wrapper hide">
						<div class="sidebar-toggler"> </div>
					</li>

					<li class="nav-item start <?php if ($indexu) { ?> active<?php } ?>">
						<a href="<?php echo dr_member_url('home/index'); ?>" class="nav-link nav-toggle ">
							<i class="icon-home"></i>
							<span class="title">首页</span>
							<?php if ($indexu) { ?><span class="selected"></span><?php } ?>
						</a>
					</li>
					<?php if (is_array($menu)) { $count=count($menu);foreach ($menu as $top) { ?>
					<li class="heading">
						<h3 class="uppercase"><?php echo $top['name']; ?></h3>
					</li>
					<?php if (is_array($top['left'])) { $count=count($top['left']);foreach ($top['left'] as $left) { ?>
					<li class="nav-item <?php if ($left['id']==$menu_pid) { ?> active<?php } ?>">
						<a href="javascript:;" class="nav-link nav-toggle">
							<i class="<?php echo $left['icon']; ?>"></i>
							<span class="title"><?php echo $left['name']; ?></span>
							<span class="arrow"></span>
							<?php if ($left['id']==$menu_pid) { ?><span class="selected"></span><?php } ?>
						</a>
						<ul class="sub-menu">
							<?php if (is_array($left['link'])) { $count=count($left['link']);foreach ($left['link'] as $t) { ?>
							<li class="nav-item  <?php if ($t['id']==$menu_id) { ?> active open<?php } ?>">

								<a href="<?php if ($t['url']) {  echo $t['url'];  } else {  echo dr_member_url($t['uri']);  } ?>" <?php if ($t['target']) { ?>target="_blank"<?php } ?> class="nav-link ">
								<i class="<?php echo $t['icon']; ?>"></i>
								<span class="title"><?php echo $t['name']; ?></span>
								<?php if (isset($notices[$t['uri']])) { ?><span class="badge badge-success"><?php echo intval($notices[$t['uri']]); ?></span><?php } ?>
								</a>
							</li>
							<?php } } ?>
						</ul>
					</li>
					<?php } }  } } ?>
				</ul>
				<!-- END SIDEBAR MENU -->
				<!-- END SIDEBAR MENU -->
			</div>
			<!-- END SIDEBAR -->
		</div>
		<!-- END SIDEBAR -->
		<!-- BEGIN CONTENT -->
		<div class="page-content-wrapper">
			<!-- BEGIN CONTENT BODY -->
			<div class="page-content">
				<div class="page-bar" style="margin-bottom: 30px;">
					<ul class="page-breadcrumb">
						<li>
							<span>首页</span>
							<i class="fa fa-circle"></i>
						</li>
						<?php if (!IS_MOBILE) {  if ($menu[$menu_tid]) { ?>
							<li>
								<span><?php echo $menu[$menu_tid]['name']; ?></span>
								<i class="fa fa-circle"></i>
							</li>
							<?php }  if ($menu[$menu_tid]['left'][$menu_pid]) { ?>
							<li>
								<span><?php echo $menu[$menu_tid]['left'][$menu_pid]['name']; ?></span>
								<i class="fa fa-circle"></i>
							</li>
							<?php }  } ?>
						<li>
							<span><?php echo $meta_name; ?></span>
						</li>
					</ul>
					<?php if ($menu[$menu_tid]['left'][$menu_pid]) { ?>
					<div class="page-toolbar">
						<div class="btn-group pull-right">
							<button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"><i class="<?php echo $menu[$menu_tid]['left'][$menu_pid]['icon']; ?>"></i> <?php echo $menu[$menu_tid]['left'][$menu_pid]['name']; ?>
								<i class="fa fa-angle-down"></i>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<?php if (is_array($menu[$menu_tid]['left'][$menu_pid]['link'])) { $count=count($menu[$menu_tid]['left'][$menu_pid]['link']);foreach ($menu[$menu_tid]['left'][$menu_pid]['link'] as $t) { ?>
								<li>
									<a href="<?php if ($t['url']) {  echo $t['url'];  } else {  echo dr_member_url($t['uri']);  } ?>" <?php if ($t['target']) { ?>target="_blank"<?php } ?>>
										<i class="<?php echo $t['icon']; ?>"></i> <?php echo $t['name']; ?></a>
								</li>
								<?php } } ?>
							</ul>
						</div>
					</div>
					<?php } ?>
				</div>

