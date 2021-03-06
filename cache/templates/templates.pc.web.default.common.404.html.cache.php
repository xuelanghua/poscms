<?php if ($fn_include = $this->_include("header.html", "/")) include($fn_include); ?>
<meta name="robots" content="noindex, nofollow" />

<link href="<?php echo THEME_PATH; ?>admin/pages/css/error.min.css" rel="stylesheet" type="text/css" />
<div class="page-container">
    <div class="page-content">
        <div class="container">
            <!-- BEGIN PAGE BREADCRUMBS -->
            <ul class="page-breadcrumb breadcrumb">
                <li>
                    <a href="<?php echo SITE_URL; ?>">首页</a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span>404</span>
                </li>
            </ul>
            <!-- END PAGE BREADCRUMBS -->
            <!-- BEGIN PAGE CONTENT INNER -->
            <div class="page-content-inner">
                <div class="row">
                    <div class="col-md-12 page-404">
                        <div class="number font-green"> 404 </div>
                        <div class="details">
                            <h3>没有找到您要访问的页面</h3>
                            <p> <?php echo $msg; ?></p>
                            <form class="search-form" method="get" target="_blank" action="<?php echo SITE_URL; ?>index.php">
                                <input name="c" type="hidden" value="so">
                                <input name="module" type="hidden" value="<?php echo MOD_DIR; ?>">
                                <div class="input-group input-medium">
                                    <input type="text" class="form-control" placeholder="输入搜索关键字" name="keyword">
                                <span class="input-group-btn">
                                            <button type="submit" class="btn green">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT INNER -->
        </div>
    </div>
</div>

<?php if ($fn_include = $this->_include("footer.html", "/")) include($fn_include); ?>