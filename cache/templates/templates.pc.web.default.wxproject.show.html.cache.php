<?php if ($fn_include = $this->_include("header.html", "/")) include($fn_include); ?>
<script language="javascript">
    // 这是加入收藏夹的ajax请求，我就随意写了一下提示信息，至于美化啊什么交给你们了
    function dr_favorite() {
        $.get("/index.php?s=<?php echo MOD_DIR; ?>&c=api&m=favorite&id=<?php echo $id; ?>", function(data){
            if (data == 1) {
                dr_tips("没有登录，不能收藏");
            } else if (data == 2) {
                dr_tips("文档不存在，无法收藏");
            } else if (data == 3) {
                dr_tips("更新收藏成功", 3, 1);
            } else if (data == 4) {
                dr_tips("收藏成功", 3, 1);
            }
        });
    }
</script>
<div class="page-container">
    <div class="page-content-wrapper">
        <?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
        <div class="page-content">
            <div class="container">

                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <a href="<?php echo SITE_URL; ?>">首页</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <?php if (!IS_SHARE) { ?>
                    <li>
                        <a href="<?php echo MODULE_URL; ?>"><?php echo MODULE_NAME; ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <?php }  echo dr_catpos($catid, '', true, '<li><a href="{url}">{name}</a><i class="fa fa-circle"></i></li>'); ?>
                    <li>
                        <span><?php echo $title; ?></span>
                    </li>
                </ul>

                <div class="page-content-inner">
                    <div class="search-page search-content-2">
                        <div class="row">
                            <div class="col-md-8">

                                <div class="portlet light">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <span class="caption-subject font-dark bold uppercase"><?php echo $title; ?></span>
                                        </div>
                                        <div class="actions">
                                            <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title="全屏查看"> </a>
                                        </div>
                                    </div>
                                    <div class="search-post-foot" style="padding-top:0px;text-align:left;padding-bottom: 10px">
                                        <div class="search-post-meta">
                                            <i class="icon-user font-blue"></i>
                                            <a href="javascript:;"><?php echo $author; ?></a>
                                        </div>
                                        <div class="search-post-meta">
                                            <i class="icon-calendar font-blue"></i>
                                            <a href="javascript:;"><?php echo $updatetime; ?></a>
                                        </div>
                                        <div class="search-post-meta">
                                            <i class="icon-fire font-blue"></i>
                                            <a href="javascript:;"><?php echo dr_show_hits($id); ?>次</a>
                                        </div>
                                        <div class="search-post-meta">
                                            <i class="icon-bubble font-blue"></i>
                                            <a href="javascript:;"><?php echo $comments; ?></a>
                                        </div>
                                        <div class="search-post-meta">
                                            <i class="icon-flag font-blue"></i>
                                            <a href="javascript:dr_favorite();">加入收藏</a>
                                        </div>
                                    </div>
                                    <div class="portlet-body" style="height: auto;">
                                        <?php echo $content; ?>
                                    </div>

                                    <div class="blog-single-foot">
                                        <ul class="blog-post-tags">
                                            <?php if (is_array($keyword_list)) { $count=count($keyword_list);foreach ($keyword_list as $name=>$url) { ?>
                                            <li class="uppercase">
                                                <a href="<?php echo $url; ?>" target="_blank"><?php echo $name; ?></a>
                                            </li>
                                            <?php } } ?>
                                        </ul>
                                        <p class="f14" style="margin-bottom: ">
                                            <strong>上一篇：</strong><?php if ($prev_page) { ?><a href="<?php echo $prev_page['url']; ?>"><?php echo $prev_page['title']; ?></a><?php } else { ?>没有了<?php } ?><br>
                                            <strong>下一篇：</strong><?php if ($next_page) { ?><a href="<?php echo $next_page['url']; ?>"><?php echo $next_page['title']; ?></a><?php } else { ?>没有了<?php } ?>
                                        </p>
                                    </div>

                                    <?php echo dr_module_comment(MOD_DIR, $id); ?>

                                </div>


                            </div>
                            <div class="col-md-4">
                                <!-- BEGIN PORTLET-->
                                <div class="portlet light ">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <span class="caption-subject font-dark bold uppercase">栏目索引</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body">
                                        <div class="todo-project-list">
                                            <ul class="nav nav-stacked">
                                                <!--循环输出当前栏目的同级栏目，定义返回值return=c-->
                                                <?php $a = array('badge-info', 'badge-success', 'badge-default', 'badge-danger');  $return_c = $this->list_tag("action=category pid=$cat[pid]  return=c"); if ($return_c) extract($return_c); $count_c=count($return_c); if (is_array($return_c)) { foreach ($return_c as $key_c=>$c) { ?>
                                                <li <?php if ($c['id']==$catid) { ?> class="active"<?php } ?>><a href="<?php echo $c['url']; ?>"><span class="badge <?php echo $a[array_rand($a)]; ?>"> <?php echo $c['total']; ?> </span><?php echo $c['name']; ?></a></li>
                                                <?php } } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="portlet light ">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <span class="caption-subject font-dark bold uppercase">相关内容</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body">
                                        <div class="todo-project-list">
                                            <ul class="nav nav-stacked">
                                                <!--此标签用于调用相关文章，tag=关键词1,关键词2，多个关键词,分隔，num=显示条数，field=显示字段-->
                                                <?php $return = $this->list_tag("action=related field=title,url,updatetime tag=$tag num=5"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                                                <li><a href="<?php echo $t['url']; ?>" title="<?php echo $t['title']; ?>"><span class="badge <?php echo $a[array_rand($a)]; ?>" style="float: left"> <?php echo $key+1; ?> </span>
                                                    &nbsp;<?php echo dr_strcut($t['title'], 33); ?></a></li>
                                                <?php } } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="portlet light ">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <span class="caption-subject font-dark bold uppercase">相关Tag</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body">
                                        <div class="todo-project-list">
                                            <div class="nav nav-stacked" style="line-height: 25px">
                                                <!--此标签用于调用tag标签，非当前模块需要加上model=模块名称,num=显示条数-->
                                                <?php $return = $this->list_tag("action=tag order=rand num=30"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                                                <a href="<?php echo $t['url']; ?>" title="点击量：<?php echo $t['hits']; ?>"><span class="badge <?php echo $a[array_rand($a)]; ?>"><?php echo $t['name']; ?></span></a>
                                                <?php } } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END PORTLET-->
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>



<?php if ($fn_include = $this->_include("footer.html", "/")) include($fn_include); ?>