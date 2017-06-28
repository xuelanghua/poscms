<?php if ($fn_include = $this->_include("header.html", "/")) include($fn_include); ?>

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
                        <span>列表</span>
                    </li>
                </ul>

                <div class="page-content-inner">
                    <div class="search-page search-content-2">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="search-container ">
                                    <ul class="search-container">
                                        <!--分页显示列表数据-->
                                        <?php $return = $this->list_tag("action=module catid=$catid field=title,url,updatetime,description,keywords,hits order=updatetime page=1"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
                                        <li class="search-item clearfix">
                                            <div class="search-content text-left">
                                                <h4 class="search-title">
                                                    <a title="<?php echo $t['title']; ?>" href="<?php echo $t['url']; ?>"><?php echo $t['title']; ?></a>
                                                </h4>
                                                <p class="search-desc"><?php echo $t['description']; ?></p>
                                                <div class="search-post-foot">
                                                    <ul class="search-post-tags">
                                                        <?php $kw=@explode(',', $t['keywords']);  if (is_array($kw)) { $count=count($kw);foreach ($kw as $a) {  if ($a) { ?>
                                                        <li class="uppercase">
                                                            <a href="<?php echo dr_tag_url(APP_DIR, $a); ?>" target="_blank"><?php echo $a; ?></a>
                                                        </li>
                                                        <?php }  } } ?>
                                                    </ul>
                                                    <div class="search-post-meta">
                                                        <i class="icon-calendar font-blue"></i>
                                                        <a href="javascript:;"><?php echo $t['updatetime']; ?></a>
                                                    </div>
                                                    <div class="search-post-meta">
                                                        <i class="icon-fire font-blue"></i>
                                                        <a href="javascript:;"><?php echo $t['hits']; ?>次</a>
                                                    </div>
                                                    <div class="search-post-meta">
                                                        <i class="icon-bubble font-blue"></i>
                                                        <a href="javascript:;"><?php echo $t['comments']; ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php } } ?>
                                    </ul>
                                    <div class="search-pagination">
                                        <ul class="pagination">
                                            <?php echo $pages; ?>
                                        </ul>
                                    </div>
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
                                            <span class="caption-subject font-dark bold uppercase">阅读排行榜</span>
                                        </div>

                                    </div>
                                    <div class="portlet-body">
                                        <div class="todo-project-list">
                                            <ul class="nav nav-stacked">
                                                <!--我们按点击排序-->
                                                <?php $return = $this->list_tag("action=module catid=$catid field=title,url order=hits num=9"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
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
                                                <?php $return = $this->list_tag("action=tag order=rand num=40"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
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