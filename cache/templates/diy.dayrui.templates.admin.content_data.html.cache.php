
<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

<thead>
    <tr>
        <th></th>
        <th width="50" style="text-align:center"><?php echo fc_lang('排序'); ?></th>
        <th class="<?php echo ns_sorting('title'); ?>" name="title"><?php echo fc_lang('主题'); ?></th>
        <?php if (!IS_SHARE) { ?><th class="<?php echo ns_sorting('catid'); ?>" name="catid"><?php echo fc_lang('栏目分类'); ?></th><?php } ?>
        <th class="<?php echo ns_sorting('author'); ?>" name="author"><?php echo fc_lang('录入作者'); ?></th>
        <th class="<?php echo ns_sorting('updatetime'); ?>" name="updatetime"><?php echo fc_lang('更新时间'); ?></th>
        <th width="80" class="<?php echo ns_sorting('status'); ?>" name="status"><?php echo fc_lang('状态'); ?></th>
        <th><?php echo fc_lang('操作'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
    <tr class="dr_row_hide" id="dr_row_<?php echo $t['id']; ?>">
        <td><input name="ids[]" type="checkbox" class="dr_select toggle md-check" value="<?php echo $t['id']; ?>" /></td>
        <td style="text-align:center"><input class="input-text displayorder" type="text" name="data[<?php echo $t['id']; ?>][displayorder]" value="<?php echo $t['displayorder']; ?>" /></td>
        <td><a title="<?php echo dr_clearhtml($t['title']); ?>" class="onloading" href="<?php if ($t['status'] == 9) {  echo dr_url_prefix($t['url']);  } else {  echo SITE_URL; ?>index.php?s=<?php echo APP_DIR; ?>&c=show&id=<?php echo $t['id'];  } ?>" target="_blank" ><?php if ($t['thumb']) { ?><img src="<?php echo THEME_PATH; ?>admin/images/img.png" align="absmiddle" height="18" width="15">&nbsp;<?php }  echo dr_keyword_highlight(dr_strcut(dr_clearhtml($t['title']), 40), $param['keyword']);  if ($t['link_id'] >0) { ?><img align="absmiddle" src="<?php echo THEME_PATH; ?>admin/images/link2.png"><?php } ?></a></td>
        <?php if (!IS_SHARE) { ?><td><a title="<?php echo dr_cat_value($t['catid'], 'name'); ?>" href="<?php if ($flag) {  echo dr_url(APP_DIR.'/home/index', array('flag'=>$flag,'catid'=>$t['catid']));  } else {  echo dr_url(APP_DIR.'/home/index', array('catid'=>$t['catid']));  } ?>"><?php echo dr_strcut(dr_cat_value($t['catid'], 'name'), 12); ?></a></td><?php } ?>
        <td><a href="javascript:;" onclick="dr_dialog_member('<?php echo $t['uid']; ?>')"><?php echo dr_strcut($t['author'], 10); ?></a></td>
        <td><?php echo dr_date($t['updatetime'], NULL, 'red'); ?></td>
        <td><label><?php if ($t['status'] == 9) { ?><a class="btn blue btn-xs" href="javascript:dr_status('<?php echo $t['id']; ?>', '<?php echo $t['status']; ?>');"><?php echo fc_lang('正常'); ?></a><?php } else { ?><a class="btn red btn-xs" href="javascript:dr_status(<?php echo $t['id']; ?>, '<?php echo $t['status']; ?>');"><?php echo fc_lang('关闭'); ?></a><?php } ?></label></td>
        <td>
            <?php if ($this->ci->is_auth(APP_DIR.'/admin/home/edit')) { ?>
            <label>
                <a href="<?php echo dr_url(APP_DIR.'/home/edit',array('id'=>$t['id'])); ?>" class="btn btn-xs green onloading">
                    <i class="fa fa-edit"></i> <?php echo fc_lang('修改'); ?> </a></label>
            <?php }  if ($form) { ?>
            <label>
            <div class="btn-group">
                <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> <i class="fa fa-table"></i> <?php echo fc_lang('表单'); ?>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php if (is_array($form)) { $count=count($form);foreach ($form as $a) { ?>
                    <li><a class="onloading" href="<?php echo $a['url']; ?>&cid=<?php echo $t['id']; ?>"><?php echo $a['name']; ?> <span class="badge badge-info"> <?php echo intval($t[$a['field']]); ?> </span> </a></li>
                    <?php } } ?>
                </ul>
            </div>
            </label>
            <?php }  if ($app) { ?>
            <label>
            <div class="btn-group">
                <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> <i class="fa fa-cloud"></i> <?php echo fc_lang('应用'); ?>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php if (is_array($app)) { $count=count($app);foreach ($app as $a) { ?>
                    <li><a class="onloading" href="<?php echo $a['url']; ?>&cid=<?php echo $t['id']; ?>&catid=<?php echo $t['catid']; ?>&module=<?php echo APP_DIR; ?>"><?php echo $a['name'];  if ($a['field']!='null') { ?> <span class="badge badge-info"> <?php echo intval($t[$a['field']]); ?> </span> <?php } ?></a></li>
                    <?php } } ?>
                </ul>
            </div>
            </label>
            <?php }  if ($extend) { ?>
            <label>
            <a class="btn btn-xs purple onloading" href="<?php echo dr_url(APP_DIR.'/extend/index',array('cid'=>$t['id'],'catid'=>$get['catid'])); ?>"> <i class="fa fa-list"></i> <?php echo fc_lang('章节管理'); ?></a>
            </label>
            <?php }  if ($ci->get_cache('comment', 'comment-module-'.APP_DIR, 'value', 'use')) { ?>
            <label>
            <a class="btn btn-xs blue onloading" href="<?php echo dr_url(APP_DIR.'/comment/index',array('cid'=>$t['id'])); ?>"> <i class="fa fa-comments"></i> <?php echo fc_lang('评论(%s)', $t['comments']); ?></a>
            </label>
            <?php } ?>
        </td>
    </tr>
    <?php } } ?>
    <tr class="mtable_bottom">
        <th width="20" ><input name="dr_select" class="toggle md-check" id="dr_select" type="checkbox" onClick="dr_selected()" /></th>
        <td colspan="99" >
            <?php if (!$get['flag']) {  if ($this->ci->is_auth(APP_DIR.'/admin/home/del')) { ?><label><button type="button" class="btn red btn-sm" name="option" onClick="$('#action').val('del');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button></label><?php }  } else {  if ($this->ci->is_auth(APP_DIR.'/admin/home/edit')) { ?><label><button type="button" class="btn red btn-sm" name="option" onClick="$('#action').val('flag');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-flag"></i> <?php echo fc_lang('移出'); ?></button></label><?php }  }  if ($this->ci->is_auth(APP_DIR.'/admin/home/edit')) { ?>
            <label><button type="button" class="btn green btn-sm" name="option" onClick="$('#action').val('order');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-edit"></i>  <?php echo fc_lang('排序'); ?></button></label>
            <label><button type="button" class="btn blue btn-sm" name="option" onClick="dr_confirm_move();"> <i class="fa fa-share"></i>  <?php echo fc_lang('移动至'); ?></button></label>
            <label><?php echo $select; ?></label>
            <label><button type="button" class="btn yellow btn-sm" name="option" onClick="dr_ts()"> <i class="fa fa-send"></i> <?php echo fc_lang('推送'); ?> </button></label>
            <?php }  if ($this->ci->is_auth('html/index') && (($ci->get_cache('module-'.SITE_ID.'-'.APP_DIR, 'html')) || (IS_SHARE && $param['catid'] && $ci->get_cache('module-'.SITE_ID.'-share', 'category', $param['catid'], 'setting', 'html')))) { ?>
            <label><button type="submit" class="btn green btn-sm" name="option" onClick="$('#action').val('html');"> <i class="fa fa-html5"></i>  <?php echo fc_lang('生成静态'); ?></button></label>
            <?php } ?>
            <!--dayrui.com 测试有bug<label><button type="button" class="btn red btn-sm" name="option" onClick="$('#action').val('del');dr_confirm_set_all('<?php echo fc_lang('您确定要这样操作吗？'); ?>')"> <i class="fa fa-trash"></i> <?php echo fc_lang('放入回收站'); ?></button></label>-->

        </td>
    </tr>
    </tbody>
</table>
