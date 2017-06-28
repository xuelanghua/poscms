<?php if ($html) {  if ($fn_include = $this->_include("header.html", "/")) include($fn_include); ?>
<div class="page-container">
    <div class="page-content-wrapper">
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
                        <a href="<?php echo $cdata['url']; ?>"><?php echo $cdata['title']; ?></a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span>评论</span>
                    </li>
                </ul>
                <div class="page-content-inner">
                    <div class="portlet light">
<?php } else { ?>
<!--关键JS开始-->
<!--关键js结束-->
<?php } ?>
<!--评论主体-->
<link rel="stylesheet" type="text/css" href="<?php echo THEME_PATH; ?>comment/css/embed.css" />
<link href="<?php echo THEME_PATH; ?>admin/css/table_form.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var comment_url = '<?php echo $curl; ?>';
function dr_todo_ajax() {
    <?php echo $js; ?>(0, 1);
}
</script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>comment/embed.js"></script>
<div id="ds-reset">
    <div class="ds-replybox" id="dr_post_form" style="zoom:1;">
        <form class="ds_form_post" method="post" id="myform">
            <div class="ds-user">
                <?php if ($myfield) { ?>
                <table class="myfield table_form" width="100%">
                    <tbody>
                    <?php echo $myfield; ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
            <?php if ($is_review) { ?>
            <div class="ds-review" id="dr_review_post">
                <ul>
                    <?php if (is_array($review['option'])) { $count=count($review['option']);foreach ($review['option'] as $i=>$t) {  if ($t['use']) { ?>
                    <li>
                        <input id="dr_review_option_<?php echo $i; ?>" type="hidden" name="review[<?php echo $i; ?>]" value="0">
                        <span class="opname">
                        <?php echo $t['name']; ?>：
                        </span>
                        <span class="commstar">
                            <?php if (is_array($review['value'])) { $count=count($review['value']);foreach ($review['value'] as $ii=>$v) { ?>
                            <a id="dr_review_value_<?php echo $i; ?>_<?php echo $ii; ?>" class="dr_review_value_<?php echo $i; ?> star<?php echo $ii; ?>" href="javascript:dr_review_value(<?php echo $i; ?>, <?php echo $ii; ?>);" title="<?php echo $v['name']; ?>">&nbsp;</a>
                            <?php } } ?>
                        </span>
                    </li>
                    <?php }  } } ?>
                </ul>
            </div>
            <div class="ds-clear"></div>
            <?php } ?>
            <a class="ds-avatar" href="javascript:;">
                <img src="<?php echo dr_avatar($member['uid']); ?>">
            </a>
            <div class="ds-textarea-wrapper ds-rounded-top">
                <textarea class="J_CmFormField" name="content" placeholder="说点什么吧…"></textarea>
            </div>
            <div class="ds-post-toolbar">
                <div class="ds-post-options ds-gradient-bg"></div>
                <button class="ds-post-button" type="button" onclick="dr_post_comment()">评论</button>
                <?php if ($code) { ?>
                <div class="ds-post-code">
                    <input type="text" name="code" placeholder="验证码" class='input-block-level' />
                    <label class="dr_code"><?php echo dr_code(100, 28); ?></label>
                </div>
                <?php } ?>
                <div class="ds-toolbar-buttons">
                    <a class="ds-toolbar-button ds-add-emote" onclick="dr_show_bq()" title="插入表情"></a>
                </div>
            </div>
        </form>
    </div>
    <?php if ($use) { ?>
    <div class="ds-comments-info">
        <div class="ds-sort">
            <a class="ds-order-desc <?php if (!$type) { ?>ds-current<?php } ?>" href="javascript:<?php echo $js; ?>(0, 1);">
                最新
            </a>
            <a class="ds-order-asc <?php if ($type==1) { ?>ds-current<?php } ?>" href="javascript:<?php echo $js; ?>(1, 1);">
                最早
            </a>
            <a class="ds-order-hot <?php if ($type==2) { ?>ds-current<?php } ?>" href="javascript:<?php echo $js; ?>(2, 1);">
                最热
            </a>
            <a class="ds-order-hot <?php if ($type==3) { ?>ds-current<?php } ?>" href="javascript:<?php echo $js; ?>(3, 1);">
                评分最高
            </a>
        </div>
        <span class="ds-comment-count">
            <a class="ds-comments-tab-duoshuo ds-current" href="javascript:void(0);">
                <span class="ds-highlight"><?php echo $commnets; ?></span>条评论
            </a>
        </span>
    </div>

    <ul id="dr_comment_list" class="ds-comments" style="opacity: 1; ">

        <?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
        <li class="ds-post">
            <div class="ds-post-self">
                <div class="ds-avatar">
                    <a rel="nofollow author" href="javascript:;;">
                        <img src="<?php echo dr_avatar($t['uid']); ?>" >
                    </a>
                </div>
                <div class="ds-comment-body">
                    <div class="ds-comment-header">
                        <a class="ds-user-name ds-highlight" href="javascript:;;">
                            <?php echo $t['author']; ?>
                        </a>
                    </div>
                    <p>
                        <?php echo dr_sns_content($t['content']); ?>
                    </p>
                    <div class="ds-comment-footer ds-comment-actions">
                        <span class="ds-time">
                            <?php echo dr_fdate($t['inputtime']); ?>
                        </span>
                        <?php if (dr_comment_is_reply($is_reply, $member, $t['uid'])) { ?>
                        <a class="ds-post-reply" href="javascript:void(0);" onclick="dr_reply_show(<?php echo $t['id']; ?>, '<?php echo $t['author']; ?>')">
                            <span class="ds-ui-icon"></span>
                            回复(<?php echo count($t['rlist']); ?>)
                        </a>
                        <?php } ?>
                        <a class="ds-post-likes" href="javascript:void(0);" onclick="dr_zc_comment(<?php echo $t['id']; ?>)">
                            <span class="ds-ui-icon"></span>
                            支持(<span id="dr_comment_zc_<?php echo $t['id']; ?>"><?php echo $t['support']; ?></span>)
                        </a>
                        <a class="ds-post-report" href="javascript:void(0);" onclick="dr_fd_comment(<?php echo $t['id']; ?>)">
                            <span class="ds-ui-icon"></span>
                            反对(<span id="dr_comment_fd_<?php echo $t['id']; ?>"><?php echo $t['oppose']; ?></span>)
                        </a>
                        <?php if ($member['adminid']) { ?>
                        <a class="ds-post-delete" href="javascript:void(0);" onclick="dr_delete_comment(<?php echo $t['id']; ?>)">
                            <span class="ds-ui-icon"></span>
                            删除
                        </a>
                        <?php } ?>
                        <a href="javascript:void(0);" style="float: right">评分：<?php echo $t['avgsort']; ?>分</a>
                    </div>
                    <div class="ds-replybox ds-replybox2 ds-inline-replybox " id="dr_reply_<?php echo $t['id']; ?>" style="display:none;">
                    </div>
                </div>
            </div>
        </li>
        <?php if ($t['rlist']) { ?>
        <ul class="ds-children">
            <?php if (is_array($t['rlist'])) { $count=count($t['rlist']);foreach ($t['rlist'] as $r) { ?>
            <li class="ds-post">
                <div class="ds-post-self">
                    <div class="ds-avatar">
                        <a rel="nofollow author" href="javascript:;;" title="">
                            <img src="<?php echo dr_avatar($r['uid']); ?>" >
                        </a>
                    </div>
                    <div class="ds-comment-body">
                        <div class="ds-comment-header">
                            <a class="ds-user-name ds-highlight" data-qqt-account="" href="javascript:;;"
                               rel="nofollow" data-userid="0">
                                <?php echo $r['author']; ?>
                            </a>
                        </div>
                        <p>
                            <?php echo dr_sns_content($r['content']); ?>
                        </p>
                        <div class="ds-comment-footer ds-comment-actions">
                            <span class="ds-time">
                            <?php echo dr_fdate($r['inputtime']); ?>
                            </span>
                            <?php if (dr_comment_is_reply($is_reply, $member, $r['uid'])) { ?>
                            <a class="ds-post-reply" href="javascript:void(0);" onclick="dr_reply_show(<?php echo $r['id']; ?>, '<?php echo $r['author']; ?>')">
                                <span class="ds-ui-icon"></span>
                                回复
                            </a>
                            <?php } ?>
                            <a class="ds-post-likes" href="javascript:void(0);" onclick="dr_zc_comment(<?php echo $r['id']; ?>)">
                                <span class="ds-ui-icon"></span>
                                支持(<span id="dr_comment_zc_<?php echo $r['id']; ?>"><?php echo $r['support']; ?></span>)
                            </a>
                            <a class="ds-post-report" href="javascript:void(0);" onclick="dr_fd_comment(<?php echo $r['id']; ?>)">
                                <span class="ds-ui-icon"></span>
                                反对(<span id="dr_comment_fd_<?php echo $r['id']; ?>"><?php echo $r['oppose']; ?></span>)
                            </a>
                            <?php if ($member['adminid']) { ?>
                            <a class="ds-post-delete" href="javascript:void(0);" onclick="dr_delete_comment(<?php echo $r['id']; ?>)">
                                <span class="ds-ui-icon"></span>
                                删除
                            </a>
                            <?php } ?>
                        </div>
                        <div class="ds-replybox ds-replybox2 ds-inline-replybox" id="dr_reply_<?php echo $r['id']; ?>">
                        </div>
                    </div>
                </div>
            </li>
            <?php } } ?>
        </ul>
        <?php }  } } ?>
    </ul>
    <div class="ds-paginator" style="">
        <div class="ds-border"> </div>
        <?php echo $pages; ?>
    </div>
    <?php } else { ?>
    <div class="ds-close-comment">
    系统关闭了评论功能
    </div>
    <?php } ?>

    <a name="respond"></a>
    <div id="ds-smilies-tooltip" style="width: <?php if (IS_PC) { ?>370<?php } else { ?>200<?php } ?>px;display: none;">
        <div class="ds-smilies-container">
            <ul>
                <?php if (is_array($emotion)) { $count=count($emotion);foreach ($emotion as $name=>$file) { ?>
                <li>
                    <img src="<?php echo $file; ?>" alt="[<?php echo $name; ?>]" title="[<?php echo $name; ?>]">
                </li>
                <?php } } ?>
            </ul>
        </div>
        <div id="ds-foot5">
            &nbsp;&nbsp;&nbsp;
        </div>
    </div>
</div>
<?php if ($html) { ?>
            </div>
            </div>
            </div>
        </div>
    </div>
</div>
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include);  } ?>