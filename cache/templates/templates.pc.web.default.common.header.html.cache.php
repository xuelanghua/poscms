<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="zh-cn">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title><?php echo $meta_title; ?></title>
    <meta name="keywords" content="<?php echo $meta_keywords; ?>" />
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/pages/css/search.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/apps/css/todo-2.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="<?php echo HOME_THEME_PATH; ?>layouts/layout3/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo HOME_THEME_PATH; ?>layouts/layout3/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="<?php echo HOME_THEME_PATH; ?>layouts/layout3/css/custom.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <!--[if lt IE 9]>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/respond.min.js"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="<?php echo THEME_PATH; ?>admin/global/scripts/app.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="<?php echo HOME_THEME_PATH; ?>layouts/layout3/scripts/layout.min.js" type="text/javascript"></script>
    <script src="<?php echo HOME_THEME_PATH; ?>layouts/layout3/scripts/demo.min.js" type="text/javascript"></script>
    <script src="<?php echo HOME_THEME_PATH; ?>layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->

    <!--关键JS开始-->
    <script type="text/javascript">var memberpath = "<?php echo MEMBER_PATH; ?>";</script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/<?php echo SITE_LANGUAGE; ?>.js"></script>
    <link rel="stylesheet" href="<?php echo THEME_PATH; ?>js/ui-dialog.css">
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dialog-plus.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dayrui.js"></script>
    <!--关键js结束-->
    <?php if (IS_MOBILE) { ?>
    <style>
    .page-breadcrumb {
        padding-top: 15px;
    }
    </style>
    <?php } ?>
    <!-- END HEAD -->
</head>
<body class="page-container-bg-solid page-header-menu-fixed page-boxed">
<!-- BEGIN HEADER -->
<div class="page-header">
    <!-- BEGIN HEADER TOP -->
    <div class="page-header-top">

        <div class="container">
            <div class="row" id="dr_mytop">
                <?php echo dr_ajax_html('dr_mytop', 'mytop.html'); ?>
                <!-- 这一段内容采用动态调用的方式 -->
            </div>
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo HOME_THEME_PATH; ?>layouts/layout3/img/logo.png" alt="<?php echo SITE_TITLE; ?>" class="logo-default">
                </a>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN 菜单按钮 -->
            <a href="javascript:;" class="menu-toggler"></a>
            <!-- END 菜单按钮 -->
            <!-- BEGIN 会员登录信息 -->
            <div class="top-menu" id="dr_member_info">

            </div>
            <script type="text/javascript">
                $.ajax({
                    type: "GET",
                    url:"<?php echo SITE_URL; ?>index.php?c=api&m=member&format=jsonp",
                    dataType: "jsonp",
                    success: function(json){
                        $("#dr_member_info").html(json.html);
                    },
                    error: function(){ }
                });
            </script>
            <!-- END 会员登录信息 -->
        </div>
    </div>
    <!-- END HEADER TOP -->
    <!-- BEGIN HEADER MENU -->
    <div class="page-header-menu">
        <div class="container2">
            <!-- 全站搜索框 -->
            <form class="search-form" method="get" target="_blank" action="<?php echo SITE_URL; ?>index.php">
                <input name="c" type="hidden" value="so">
                <input name="module" type="hidden" value="<?php if (defined('MOD_DIR')) {  echo MOD_DIR;  } ?>">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="输入搜索关键字" name="keyword">
					<span class="input-group-btn">
						<a href="javascript:;" class="btn submit">
                            <i class="icon-magnifier"></i>
                        </a>
					</span>
                </div>
            </form>
            <!-- 调用网站导航菜单 -->
            <div class="hor-menu  ">
                <ul class="nav navbar-nav">
                    <li id="dr_nav_0" class="menu-dropdown classic-menu-dropdown <?php if ($indexc) { ?>active<?php } ?>">
                        <a href="<?php echo SITE_URL; ?>" title="<?php echo SITE_TITLE; ?>">首页</a>
                    </li>



                    <!--第一层：调用栏目-->
                    <?php $return = $this->list_tag("action=category module=share pid=0"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                    <li class="menu-dropdown classic-menu-dropdown <?php if ((MOD_DIR == $t['mid'] && $catid && in_array($catid, $t['catids'])) || (MOD_DIR && $t['mid']==MOD_DIR)) { ?> active<?php } ?>">
                        <a href="<?php echo $t['url']; ?>" title="<?php echo $t['name']; ?>"><?php echo $t['name']; ?></a>
                        <?php if ($t['child']) { ?>
                        <ul class="dropdown-menu pull-left">
                            <?php $return_t2 = $this->list_tag("action=category module=share pid=$t[id]  return=t2"); if ($return_t2) extract($return_t2); $count_t2=count($return_t2); if (is_array($return_t2)) { foreach ($return_t2 as $key_t2=>$t2) { ?>
                            <li class="<?php if ($t2['child']) { ?> dropdown-submenu<?php }  if ($catid && in_array($catid, $t2['catids'])) { ?> active<?php } ?>">
                                <a href="<?php echo $t2['url']; ?>" class="nav-link nav-toggle " title="<?php echo $t2['name']; ?>">
                                    <?php echo $t2['name']; ?>
                                </a>
                                <?php if ($t2['child']) { ?>
                                <ul class="dropdown-menu pull-left">
                                    <?php $return_t3 = $this->list_tag("action=category module=share pid=$t2[id]  return=t3"); if ($return_t3) extract($return_t3); $count_t3=count($return_t3); if (is_array($return_t3)) { foreach ($return_t3 as $key_t3=>$t3) { ?>
                                    <li class="<?php if ($catid && in_array($catid, $t3['catids'])) { ?> active<?php } ?>">
                                        <a href="<?php echo $t3['url']; ?>" title="<?php echo $t3['name']; ?>">
                                            <?php echo $t3['name']; ?>
                                        </a>
                                    </li>
                                    <?php } } ?>
                                </ul>
                                <?php } ?>
                            </li>
                            <?php } } ?>
                        </ul>
                        <?php } ?>
                    </li>
                    <?php } } ?>


                    <!--调用所有独立模块-->
                    <?php $return = $this->list_tag("action=cache name=module"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) {  if (!$t['share']) { ?>
                    <li class="menu-dropdown classic-menu-dropdown <?php if ((MOD_DIR && $t['dirname']==MOD_DIR)) { ?>active<?php } ?>">
                        <a href="<?php echo $t['url']; ?>" title="<?php echo $t['title']; ?>"><?php echo $t['name']; ?></a>
                    </li>
                    <?php }  } } ?>


                </ul>
            </div>
            <!-- END MEGA MENU -->
        </div>
    </div>
    <!-- END HEADER MENU -->
</div>
<!-- END HEADER -->