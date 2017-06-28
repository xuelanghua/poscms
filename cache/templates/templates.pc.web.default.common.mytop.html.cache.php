<?php if (IS_PC) { ?>
<div class="col-md-12 text-right" style="padding-top:5px">
    <small><a href="javascript:void(0);" onclick="dr_set_homepage('<?php echo SITE_URL; ?>')">设为首页</a> |</small>
    <small><a href="javascript:void(0);" onclick="dr_add_favorite('<?php echo SITE_URL; ?>','<?php echo SITE_TITLE; ?>')">加入收藏</a> |</small>
    <small><a href="<?php echo SITE_URL; ?>index.php?c=api&m=desktop&site=<?php echo SITE_ID; ?>&module=<?php echo MOD_DIR; ?>">放在桌面</a> |</small>
    <small><a href="<?php echo SITE_URL; ?>index.php?c=so">全站搜索</a></small>
</div>
<?php } ?>