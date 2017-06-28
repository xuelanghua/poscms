<div class="page-head">
    <div class="container">
        <div class="page-title">
            <!--循环栏目作为导航栏目，pid=0表示顶级栏目-->
            <ul class="list-inline btn-group btn-group-xs btn-group-solid">
            <?php $return = $this->list_tag("action=category pid=0"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
            <li><a class="<?php if (@in_array($catid, $t['catids'])) { ?>label label-info<?php } ?>" href="<?php echo dr_mobile_category_url(MOD_DIR, $t['id']); ?>"><?php echo $t['name']; ?></a></li>
            <?php } } ?>
            <li><a class="" href="<?php echo dr_search_url(); ?>">模块内搜索</a></li>
            </ul>
        </div>
    </div>
</div>