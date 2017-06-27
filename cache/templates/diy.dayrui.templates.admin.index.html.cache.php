<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title><?php echo fc_lang('%s后台中心', SITE_NAME); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/css/font-awesome/css/font-awesome.css"rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/css/table_form.css" rel="stylesheet"  />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-notific8/jquery.notific8.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/global/css/components-rounded.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>admin/my.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="<?php echo THEME_PATH; ?>admin/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <!--[if lt IE 9]>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/respond.min.js"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">var dr_index = 1;var siteurl = "<?php echo SITE_PATH;  echo SELF; ?>";var memberpath = "<?php echo MEMBER_PATH; ?>";var sys_theme = "<?php echo THEME_PATH; ?>admin/";</script>
    <script src="<?php echo THEME_PATH; ?>js/<?php echo SITE_LANGUAGE; ?>.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>js/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>js/jquery.cookie.js" type="text/javascript"></script>
    <link rel="stylesheet" href="<?php echo THEME_PATH; ?>js/ui-dialog.css">
    <script type="text/javascript"t src="<?php echo THEME_PATH; ?>js/dialog-plus.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
    <script src="<?php echo THEME_PATH; ?>js/validate.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>js/admin.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>js/dayrui.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.scrollTo.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery-notific8/jquery.notific8.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="<?php echo THEME_PATH; ?>admin/global/scripts/app.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>admin/tree/tree.js"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="<?php echo THEME_PATH; ?>admin/layouts/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>admin/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        var left_menu = <?php echo json_encode($left); ?>;
        function init_iframe() {
            if ($("#_left_menu").offset().top != $("#_right_menu").offset().top) {
                $(".my_top_module").remove();
                $("#select_top_module").show();
            }
        }
        $(function(){
            init_iframe();
            if ($(window).width() <= 824) {
                $(".hidden-480").hide();
            }
            <?php if (IS_MOBILE) { ?>
                $(".hidden-480").hide();
            <?php } ?>
            wSize();

            $("#sitelist li").click(function(){
                var id=$(this).attr('id');
                art.dialog.confirm("<font color=red><b><?php echo fc_lang('是否要切换站点？'); ?></b></font>", function(){
                    // ajax提交验证
                    $.ajax({type: "POST",dataType:"json", url: "<?php echo dr_url('site/select'); ?>", data: {id: id},
                        success: function(data) {
                            if (data.status == 1) {
                                //验证成功
                                dr_tips(data.code, 3, 1);
                                setTimeout('top.location.reload(true)', 2000);
                            } else {
                                dr_tips(data.code, 5);
                            }
                            return true;
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError) {

                        }
                    });
                    return true;
                });
            });
        });
        function getSidebarScrollHeight(){
            var $el = $("#left"),
                    $w = $(window),
                    $nav = $("#navigation");
            var height = $w['height']();

            if(($nav['hasClass']("navbar-fixed-top") && $w['scrollTop']() == 0) || $w['scrollTop']() == 0) height -= 40;

            if($el['hasClass']("sidebar-fixed") || $el['hasClass']("mobile-show")){
                $el['height'](height);
            }
        }
        // 隐藏和现实左侧菜单按钮
        function hideNav(){


        }
        // 强制隐藏左侧菜单按钮
        function hideNavShow(){
            var body = $('body');
            var sidebar = $('.page-sidebar');
            var sidebarMenu = $('.page-sidebar-menu');
            $(".sidebar-search", sidebar).removeClass("open");

            body.addClass("page-sidebar-closed");
            sidebarMenu.addClass("page-sidebar-menu-closed");
            if (body.hasClass("page-sidebar-fixed")) {
                sidebarMenu.trigger("mouseleave");
            }
            if ($.cookie) {
                $.cookie('sidebar_closed', '1');
            }

           // $(window).trigger('resize');
        }
        // 强制显示左侧菜单按钮
        function hideNavHide(){
            return;
            var body = $('body');
            var sidebar = $('.page-sidebar');
            var sidebarMenu = $('.page-sidebar-menu');
            $(".sidebar-search", sidebar).removeClass("open");

            body.removeClass("page-sidebar-closed");
            sidebarMenu.removeClass("page-sidebar-menu-closed");
            if ($.cookie) {
                $.cookie('sidebar_closed', '0');
            }

            //$(window).trigger('resize');
        }

        if(!Array.prototype.map)
            Array.prototype.map = function(fn,scope) {
                var result = [],ri = 0;
                for (var i = 0,n = this.length; i < n; i++){
                    if(i in this){
                        result[ri++]  = fn.call(scope ,this[i],i,this);
                    }
                }
                return result;
            };

        var getWindowSize = function(){
            return ["Height","Width"].map(function(name){
                return window["inner"+name] ||
                        document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
            });
        }
        window.onload = function (){

            if (!window.applicationCache) {
                alert("你的浏览器不支持HTML5，推荐使用Chrome或IE高版本浏览器");
                return false;
            }
            if(!+"\v1" && !document.querySelector) {
                alert("你的浏览器不支持HTML5，推荐使用Chrome或IE高版本浏览器");
                return false;
            } else {
                window.onresize = resize;
            }
            function resize() {
                wSize();
                return false;
            }
        }
        function wSize(){
            var str=getWindowSize();
            var strs= new Array(); //定义一数组
            strs=str.toString().split(","); //字符分割
            var heights = strs[0]-80,Body = $('body');$('#rightMain').height(heights);
        }
        function _M(mid, sid, url, name) {

        }
        function fn(i, url) {

            $("#rightMain").attr('src', url);
            $("#rightMain").attr("url", url);

            $('.i-t').removeClass('active');
            $('#fn_'+i).addClass('active');
            $(".dr_link").attr('class', 'dr_link nav-item');

            // 移动端隐藏菜单
            if ($(window).width() <= 824) {
                $('.page-sidebar').removeClass("in");
                $('.page-sidebar').attr("aria-expanded", "false");
            }
        }
        function _MP(id, url) {

            $("#rightMain").attr('src', url);
            $("#rightMain").attr("url", url);

            var fid = $("#_MP_"+id).attr("fid");

            $(".dr_left").attr('class', 'dr_left nav-item');
            $(".dr_link").attr('class', 'dr_link nav-item');
            $("#_MP_"+id).addClass('active open');
            $("#D_M_"+fid).addClass('active open');

            if (url.indexOf('http') == -1) {
                dr_loading();
            }

            // 移动端隐藏菜单
            if ($(window).width() <= 824) {
                $('.page-sidebar').removeClass("in");
                $('.page-sidebar').attr("aria-expanded", "false");
            }
        }
        function _MAP(mid, tid, url) {
            $(".my_top").removeClass('open');
            //$("#select_top_module").addClass('open');
            $("#select_my_top_"+tid).addClass('open');
            $("#my_top_"+tid).addClass('open');
            $(".page-sidebar-menu").html(left_menu[tid]);
            _MP(mid, url);


            $('#tree').explr({
                rememberState   : true,
                startCollapsed  : false,
                treeWidth   : 180
            });
        }
        function logout(){
            if (confirm("<?php echo fc_lang('您确定要退出吗？'); ?>"))
                top.location = '<?php echo dr_url("login/logout"); ?>';
            return false;
        }
        function dr_get_map() {
        }
        function dr_clear_map() {
        }
        function dr_loading() {
            $('.page-loading').remove();
            $('body').append('<div class="page-loading"><img src="<?php echo THEME_PATH; ?>admin/images/loading-mini.gif"/>&nbsp;&nbsp;<span><?php echo fc_lang('数据加载中...'); ?></span></div>');
            setTimeout(function(){
                $('.page-loading').remove();
            }, 5000);
        }
        function dr_set_color_value(v) {
            $.ajax({
                type: "GET",
                cache: false,
                url: "<?php echo dr_url('api/color'); ?>&v="+v,
                dataType: "json",
                success: function (res) {
                },
                error: function (xhr, ajaxOptions, thrownError) {
                }
            });
        }
        function dr_add_menu() {
            $.ajax({
                type: "GET",
                cache: false,
                url: "<?php echo dr_url('api/menu'); ?>&v="+encodeURIComponent($("#rightMain").attr("url")),
                dataType: "text",
                success: function (res) {
                   if (!res) {
                       dr_tips('<?php echo fc_lang("操作成功"); ?>',3, 1);
                   } else {
                       dr_tips(res);
                   }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                }
            });
        }
    </script>
    <style>
    .page-content {
        padding:0 !important;
        background-color: #f5f5f5;
    }
    </style>
</head>
<body scroll="no" style="overflow:hidden" class="page-sidebar-closed-hide-logo page-content-white page-header-fixed page-sidebar-fixed page-footer-fixed">

<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner ">
        <div class="page-logo">
            <a href="<?php echo SITE_URL; ?>" title="<?php echo DR_NAME; ?>" target="_blank"><?php echo DR_NAME; ?> </a>
            <div class="menu-toggler sidebar-toggler"> </div>
        </div>

        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
        <div id="_left_menu" class="top-menu pull-left">

            <ul class="nav navbar-nav pull-left">
                <?php $i=1;  if (is_array($top)) { $count=count($top);foreach ($top as $t) { ?>
                <li class="<?php if (strpos($t['mark'], 'module')===0) { ?>my_top_module<?php } ?> my_top dropdown <?php if ($i) { ?>open<?php } ?>" id="my_top_<?php echo $t['id']; ?>">
                    <a class="dropdown-toggle myname popovers" data-container="body" data-trigger="hover"  href="javascript:_MAP('<?php echo $t['link']['id']; ?>', '<?php echo $t['link']['tid']; ?>', '<?php echo $t['link']['url']; ?>');">
                        <i class="<?php echo $t['icon']; ?>"></i>
                        <br>
                        <i class="top-txt-menu"><?php echo fc_lang($t['name']); ?></i>
                    </a>
                </li>
                <?php $i=0;  } } ?>

                <li id="select_top_module" style="display:none;" class="my_top dropdown dropdown-extended dropdown-notification ">
                    <a href="javascript:;" class="dropdown-toggle popovers " data-container="body" data-trigger="hover" data-placement="bottom" data-content="<?php echo fc_lang('内容模块'); ?>" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="fa fa-th"></i>
                        <br>
                        <i class="top-txt-menu"><?php echo fc_lang('模块'); ?></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height:300px;" data-handle-color="#637283">
                                <?php if (is_array($top)) { $count=count($top);foreach ($top as $t) {  if (strpos($t['mark'], 'module')===0) { ?>
                                <li class="my_top" id="select_my_top_<?php echo $t['id']; ?>">
                                    <a style="padding: 8px 15px 8px;" href="javascript:_MAP('<?php echo $t['link']['id']; ?>', '<?php echo $t['id']; ?>', '<?php echo $t['link']['url']; ?>');"><span class="details"> <i class="<?php echo $t['icon']; ?>"></i> <?php echo fc_lang($t['name']); ?> </span></a>
                                </li>
                                <?php }  } } ?>
                            </ul>
                        </li>
                    </ul>
                </li>



            </ul>

        </div>
        <div id="_right_menu" class="top-menu">

            <ul class="nav navbar-nav pull-right">

                <li class="dropdown dropdown-dark">
                    <a href="<?php echo SITE_URL; ?>" target="_blank" class="dropdown-toggle popovers top-link" data-container="body" data-trigger="hover" data-placement="bottom" data-content="<?php echo fc_lang('网站首页'); ?>">
                        <i class="fa fa-home" style="font-size: 21px;"></i>
                    </a>
                </li>

                <?php if (count($mysite)>1) { ?>
                <li id="dr_select_site" class=" dropdown dropdown-extended dropdown-notification ">
                    <a href="javascript:;" class="dropdown-toggle popovers top-link" data-container="body" data-trigger="hover" data-placement="bottom" data-content="<?php echo fc_lang('站点切换'); ?>" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="icon-globe"></i>
                    </a>

                    <ul class="dropdown-menu">
                        <li class="external">
                            <h3><?php echo fc_lang('可管理%s个站点', '<span class="bold">'.count($mysite).'</span>'); ?></h3>
                        </li>
                        <li>
                            <ul id="sitelist" class="dropdown-menu-list scroller" style="height: 300px;" data-handle-color="#637283">
                                <?php if (is_array($mysite)) { $count=count($mysite);foreach ($mysite as $sid=>$name) { ?>
                                <li id="<?php echo $sid; ?>">
                                    <a href="javascript:;">
                                        <?php if ($sid==SITE_ID) { ?><span class="time" style="background:none"><i class="fa fa-check-square"></i></span><?php } ?>
                                        <span class="details"> <?php echo $name; ?> </span>
                                    </a>
                                </li>
                                <?php } } ?>

                            </ul>
                        </li>
                    </ul>
                </li>
                <?php }  if ($admin['usermenu']) { ?>
                <li  class="hidden-480 dropdown dropdown-extended dropdown-notification">
                    <a href="javascript:;" class="dropdown-toggle popovers top-link" data-container="body" data-trigger="hover" data-placement="bottom" data-content="<?php echo fc_lang('快捷菜单'); ?>" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="fa fa-paper-plane"></i>
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height:400px;" data-handle-color="#637283">
                                <?php if (is_array($admin['usermenu'])) { $count=count($admin['usermenu']);foreach ($admin['usermenu'] as $t) { ?>
                                <li>
                                    <a style="padding: 8px 15px 8px;" href="<?php echo $t['url']; ?>" target="right"><span class="details"> <?php echo fc_lang($t['name']); ?> </span></a>
                                </li>
                                <?php } } ?>

                            </ul>
                        </li>
                    </ul>
                </li>
                <?php } ?>



                <li class="dropdown dropdown-dark">
                    <a class="dropdown-toggle popovers top-link" data-container="body" data-trigger="hover" data-placement="bottom" data-content="<?php echo fc_lang('更新全站缓存'); ?>" href="<?php echo dr_url('home/cache'); ?>" target="right"><i class="icon-refresh"></i></a>
                </li>

                <li class="dropdown dropdown-user">
                    <a href="javascript:;" style=" height: 70px;" class="dropdown-toggle top-link" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="<?php echo $admin['username']; ?>" class="img-circle" src="<?php echo dr_avatar($admin['uid']); ?>" />
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li><a href="<?php echo dr_member_url('api/member'); ?>" target="_blank"><i class="fa fa-user"></i> <?php echo dr_strcut($admin['username'], 8); ?> </a></li>
                        <li><a href="<?php echo SITE_URL; ?>" target="_blank"><i class="fa fa-home"></i> <?php echo fc_lang('网站首页'); ?> </a></li>
                        <li><a href="javascript:;" onClick="logout();"><i class="fa fa-sign-in"></i> <?php echo fc_lang('退出系统1111'); ?></a></li>
                        <li class="divider"> </li>
                        <?php if ($ci->is_auth('admin/db/sql')) { ?>
                        <li><a href="<?php echo dr_url('db/sql'); ?>" target="right"><i class="fa fa-code"></i> <?php echo fc_lang('执行SQL'); ?></a></li>
                        <?php }  if ($ci->is_auth('admin/check/index')) { ?>
                        <li><a href="<?php echo dr_url('check/index'); ?>" target="right"><i class="fa fa-circle-o-notch"></i> <?php echo fc_lang('系统体检'); ?></a></li>
                        <?php } ?>
                        <li><a href="<?php echo dr_url('home/clear'); ?>" target="right"><i class="fa fa-trash"></i> <?php echo fc_lang('更新数据'); ?></a></li>

                        <li><a href="<?php echo dr_url('home/dbcache'); ?>" target="right"><i class="fa fa-database"></i> <?php echo fc_lang('更新表结构'); ?></a></li>
                        <li><a href="<?php echo dr_url('home/cache'); ?>" target="right"><i class="fa fa-refresh"></i> <?php echo fc_lang('更新全站缓存'); ?></a></li>
                   </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="clearfix"> </div>
<div class="page-container">
    <div class="page-sidebar-wrapper">
        <div class="page-sidebar navbar-collapse collapse">
            <?php if (SYS_NEWS) { ?>
            <div class="page-sidebar navbar-collapse collapse">
            <div class="sidebar-search-wrapper">
                <form class="sidebar-search" action="http://help.dayrui.com/index.php" method="get" target="_blank">
                    <input name="c" type="hidden" value="search" />
                    <input name="m" type="hidden" value="index" />
                    <input name="fc" type="hidden" value="<?php echo DR_LICENSE_ID; ?>" />
                    <input name="domain" type="hidden" value="<?php echo SITE_URL; ?>" />
                    <a href="javascript:;" class="remove">
                        <i class="icon-close"></i>
                    </a>
                    <div class="input-group" style="border-radius:0">
                        <input type="text" style="border-radius:0" class="form-control" name="keyword" placeholder="<?php echo fc_lang('搜索帮助...'); ?>">
                            <span class="input-group-btn">
                                <a href="javascript:;" class="btn submit">
                                    <i class="icon-magnifier"></i>
                                </a>
                            </span>
                    </div>
                </form>
            </div>
                <?php } else { ?>

                <div class="page-sidebar navbar-collapse collapse" style="padding-top:45px">
            <?php } ?>

            <ul class="page-sidebar-menu page-sidebar-menu-fixed page-header-fixed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top:0px">


            <?php echo reset($left); ?>

            </ul>


        </div>
    </div>

    <div class="page-content-wrapper">

        <div class="page-content">


            <iframe name="right" id="rightMain" src="<?php echo dr_url('home/main'); ?>&cache=<?php echo SYS_TIME; ?>" url="<?php echo dr_url('home/main'); ?>&cache=<?php echo SYS_TIME; ?>" frameborder="false" scrolling="auto" style="border:none; margin-bottom:0px;" width="100%" height="auto" allowtransparency="true"></iframe>

        </div>
    </div>
    <a href="javascript:;" class="page-quick-sidebar-toggler">
        <i class="icon-login"></i>
    </a>
</div>

</body>
</html>