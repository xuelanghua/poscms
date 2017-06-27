<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	<?php if (IS_POST) { ?>
	dr_tips('<?php echo fc_lang("操作成功"); ?>', 3, 1);
    <?php } ?>
    $("#dr_review_option").sortable();
});
</script>
<form class="form-horizontal" action="" method="post" id="myform" name="myform">
<input name="page" id="page" type="hidden" value="<?php echo $page; ?>" />
    <div class="page-bar">
        <ul class="page-breadcrumb mylink">
            <?php echo $menu['link']; ?>

        </ul>
        <ul class="page-breadcrumb myname">
            <?php echo $menu['name']; ?>
        </ul>
        <div class="page-toolbar">
            <div class="btn-group pull-right">
                <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false" data-hover="dropdown"> <?php echo fc_lang('操作菜单'); ?>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <?php if (is_array($menu['quick'])) { $count=count($menu['quick']);foreach ($menu['quick'] as $t) { ?>
                    <li>
                        <a href="<?php echo $t['url']; ?>"><?php echo $t['icon'];  echo $t['name']; ?></a>
                    </li>
                    <?php } } ?>
                    <li class="divider"> </li>
                    <li>
                        <a href="javascript:window.location.reload();">
                            <i class="icon-refresh"></i> <?php echo fc_lang('刷新页面'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <h3 class="page-title">
        <small></small>
    </h3>

    <div class="portlet light bordered myfbody">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs" style="float:left;">
                <li class="active">
                    <a href="#tab_0" data-toggle="tab"> <i class="fa fa-cog"></i> <?php echo fc_lang('评论配置'); ?> </a>
                </li>
                <li class="">
                    <a href="#tab_1" data-toggle="tab"> <i class="fa fa-user"></i> <?php echo fc_lang('会员权限'); ?> </a>
                </li>
                <li class="">
                    <a href="#tab_2" data-toggle="tab"> <i class="fa fa-cubes"></i> <?php echo fc_lang('字段格式'); ?> </a>
                </li>
                <li class="">
                    <a href="#tab_3" data-toggle="tab"> <i class="fa fa-comments"></i> <?php echo fc_lang('点评配置'); ?> </a>
                </li>
                <li class="">
                    <a href="#tab_4" data-toggle="tab"> <i class="fa fa-location-arrow"></i> <?php echo fc_lang('优化设置'); ?> </a>
                </li>
                <?php if ($mycfg) { ?>
                <li class="">
                    <a href="#tab_5" data-toggle="tab"> <i class="fa fa-cog"></i> <?php echo fc_lang('我的配置'); ?> </a>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">

                <div class="tab-pane active" id="tab_0">
                    <div class="form-body">
                        <div class="form-group">
                        <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('评论功能'); ?>：</label>
                        <div class="col-md-9">
                            <div class="radio-list">
                                <label class="radio-inline"><input type="radio" name="data[use]" value="1" <?php if ($data['use']) { ?>checked<?php } ?> /> <?php echo fc_lang('是'); ?></label>
                                <label class="radio-inline"><input type="radio" name="data[use]" value="0" <?php if (!$data['use']) { ?>checked<?php } ?> /> <?php echo fc_lang('否'); ?></label>
                            </div>
                        </div>
                    </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('评论审核'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[verify]" value="1" <?php if ($data['verify']) { ?>checked<?php } ?> /> <?php echo fc_lang('是'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[verify]" value="0" <?php if (!$data['verify']) { ?>checked<?php } ?> /> <?php echo fc_lang('否'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('只允许评论一次'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[num]" value="1" <?php if ($data['num']) { ?>checked<?php } ?> /> <?php echo fc_lang('是'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[num]" value="0" <?php if (!$data['num']) { ?>checked<?php } ?> /> <?php echo fc_lang('否'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('禁止对自己评论'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[my]" value="1" <?php if ($data['my']) { ?>checked<?php } ?> /> <?php echo fc_lang('是'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[my]" value="0" <?php if (!$data['my']) { ?>checked<?php } ?> /> <?php echo fc_lang('否'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('购买之后才允许评论'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[buy]" value="1" <?php if ($data['buy']) { ?>checked<?php } ?> /> <?php echo fc_lang('是'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[buy]" value="0" <?php if (!$data['buy']) { ?>checked<?php } ?> /> <?php echo fc_lang('否'); ?></label>
                                </div>
                                <span class="help-block"><?php echo fc_lang('只能与订单模块配合使用'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('评论回复权限'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[reply]" value="1" <?php if ($data['reply']==1) { ?>checked<?php } ?> /> <?php echo fc_lang('都允许'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[reply]" value="2" <?php if ($data['reply']==2) { ?>checked<?php } ?> /> <?php echo fc_lang('仅自己'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[reply]" value="0" <?php if (!$data['reply']) { ?>checked<?php } ?> /> <?php echo fc_lang('禁止所有'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"><font color="red">*</font>&nbsp;<?php echo fc_lang('分页显示数量'); ?>：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[pagesize]" value="<?php echo $data['pagesize']; ?>" ></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_1">
                    <div class="form-body">
                        <?php $groups[0]=array('id'=>0, 'name'=>fc_lang('游客')); $groups+= $ci->get_cache('member', 'group'); ?>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-9">
                                <label class="radio-inline"><?php echo fc_lang('禁用'); ?></label>
                                <label class="radio-inline"><?php echo fc_lang('验证码'); ?></label>
                                <label class="radio-inline"><?php echo fc_lang('评论%s', SITE_EXPERIENCE); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                <label class="radio-inline"><?php echo fc_lang('评论%s', SITE_SCORE); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                <label class="radio-inline"><?php echo fc_lang('评论间隔'); ?></label>
                            </div>
                        </div>
                        <?php if (is_array($groups)) { $count=count($groups);foreach ($groups as $group) {  if ($group['id']>2) { ?>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"><?php echo $group['name']; ?>：</label>
                            <div class="col-md-9"></div>
                        </div>
                        <?php if (is_array($group['level'])) { $count=count($group['level']);foreach ($group['level'] as $level) {  $id=$group['id'].'_'.$level['id']; ?>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"><?php echo $level['name']; ?>：</label>
                            <div class="col-md-9">
                                <label class="radio-inline"><input type="checkbox" name="data[permission][<?php echo $id; ?>][disabled]" <?php if ($data['permission'][$id]['disabled']) { ?>checked="checked"<?php } ?> value="1" /></label>
                                <label class="radio-inline"><input type="checkbox" name="data[permission][<?php echo $id; ?>][code]" <?php if ($data['permission'][$id]['code']) { ?>checked="checked"<?php } ?> value="1" /></label>
                                <label class="radio-inline"><input class="input-text" type="text" name="data[permission][<?php echo $id; ?>][experience]" value="<?php echo $data['permission'][$id]['experience']; ?>" size="10" /></label>
                                <label class="radio-inline"><input class="input-text" type="text" name="data[permission][<?php echo $id; ?>][score]" value="<?php echo $data['permission'][$id]['score']; ?>" size="10" /></label>
                                <label class="radio-inline"><input class="input-text" type="text" name="data[permission][<?php echo $id; ?>][time]" value="<?php echo $data['permission'][$id]['time']; ?>" size="10" /></label>
                            </div>
                        </div>
                        <?php } }  } else {  $id=$group['id']; ?>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"><?php echo $group['name']; ?>：</label>
                            <div class="col-md-9">
                                <label class="radio-inline"><input type="checkbox" name="data[permission][<?php echo $id; ?>][disabled]" <?php if ($data['permission'][$id]['disabled']) { ?>checked="checked"<?php } ?> value="1" /></label>
                                <label class="radio-inline"><input type="checkbox" name="data[permission][<?php echo $id; ?>][code]" <?php if ($data['permission'][$id]['code']) { ?>checked="checked"<?php } ?> value="1" /></label>
                                <label class="radio-inline"><input class="input-text" type="text" name="data[permission][<?php echo $id; ?>][experience]" value="<?php echo $data['permission'][$id]['experience']; ?>" size="10" /></label>
                                <label class="radio-inline"><input class="input-text" type="text" name="data[permission][<?php echo $id; ?>][score]" value="<?php echo $data['permission'][$id]['score']; ?>" size="10" /></label>
                                <label class="radio-inline"><input class="input-text" type="text" name="data[permission][<?php echo $id; ?>][time]" value="<?php echo $data['permission'][$id]['time']; ?>" size="10" /></label>
                            </div>
                        </div>
                        <?php }  } } ?>
                    </div>
                </div>
                <div class="tab-pane" id="tab_2">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo fc_lang('字段格式'); ?>：</label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:150px" name="data[format]" /><?php echo $data['format']; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"><?php echo fc_lang('游客字段选择'); ?>：</label>
                            <div class="col-md-9">
                                <?php if (is_array($myfield2)) { $count=count($myfield2);foreach ($myfield2 as $t) { ?>
                                <label class="radio-inline"><input type="checkbox" name="data[field][]" <?php if (@in_array($t['fieldname'], $data['field'])) { ?>checked<?php } ?> value="<?php echo $t['fieldname']; ?>" /> <?php echo $t['name']; ?></label>
                                <?php } } ?>
                                <span class="help-block"><?php echo fc_lang('当游客评论时才显示这些字段；登录用户隐藏这些字段（此功能暂时无效，正在设计中）'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_3">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('点评功能'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[review][use]" value="1" <?php if ($data['review']['use']) { ?>checked<?php } ?> /> <?php echo fc_lang('开启'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[review][use]" value="0" <?php if (!$data['review']['use']) { ?>checked<?php } ?> /> <?php echo fc_lang('关闭'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('总分形式'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[review][score]" value="5" <?php if ($data['review']['score']==5) { ?>checked<?php } ?> /> <?php echo fc_lang('五分制'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[review][score]" value="10" <?php if ($data['review']['score']==10) { ?>checked<?php } ?> /> <?php echo fc_lang('十分制'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[review][score]" value="100" <?php if ($data['review']['score']==100) { ?>checked<?php } ?> /> <?php echo fc_lang('百分制'); ?></label>
                                </div>
                                <span class="help-block"><?php echo fc_lang('显示主题的各个评分项的数值形式'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('分数小数点'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[review][point]" value="0" <?php if (!$data['review']['point']) { ?>checked<?php } ?> /> <?php echo fc_lang('不显示'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[review][point]" value="1" <?php if ($data['review']['point']==1) { ?>checked<?php } ?> /> <?php echo fc_lang('一位数'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[review][point]" value="2" <?php if ($data['review']['point']==2) { ?>checked<?php } ?> /> <?php echo fc_lang('两位数'); ?></label>
                                </div>
                                <span class="help-block"><?php echo fc_lang('各项得分的显示是否显示小数点'); ?></span>
                            </div>
                        </div>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"><?php echo fc_lang('点评选项'); ?>：</label>
                            <div class="col-md-9">
                                <label class="radio-inline"><?php echo fc_lang('可用'); ?></label>
                                <label class="radio-inline"><?php echo fc_lang('字段'); ?></label>
                                <label class="radio-inline"><?php echo fc_lang('名称'); ?></label>
                            </div>
                        </div>
                        <?php if (is_array($data['review']['option'])) { $count=count($data['review']['option']);foreach ($data['review']['option'] as $i=>$t) { ?>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-9">
                                <label class="radio-inline"><input type="checkbox" name="data[review][option][<?php echo $i; ?>][use]" <?php if ($t['use']) { ?>checked<?php } ?> value="1"></label>
                                <label class="radio-inline">sort<?php echo $i; ?></label>
                                <label class="radio-inline"><input class="form-control" type="text" name="data[review][option][<?php echo $i; ?>][name]" value="<?php echo $t['name']; ?>" size="15" /></label>
                            </div>
                        </div>
                        <?php } } ?>

                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"><?php echo fc_lang('点评值'); ?>：</label>
                            <div class="col-md-9">
                                <label class="radio-inline"><?php echo fc_lang('星级'); ?></label>
                                <label class="radio-inline"><?php echo fc_lang('名称'); ?></label>
                            </div>
                        </div>
                        <?php if (is_array($data['review']['value'])) { $count=count($data['review']['value']);foreach ($data['review']['value'] as $i=>$t) { ?>
                        <div class="form-group dr_one">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-9">
                                <label class="radio-inline"><?php echo $i; ?>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                <label class="radio-inline"><input class="form-control" type="text" name="data[review][value][<?php echo $i; ?>][name]" value="<?php echo $t['name']; ?>" size="15" /></label>
                            </div>
                        </div>
                        <?php } } ?>
                    </div>
                </div>
                <div class="tab-pane" id="tab_4">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label" style="padding-top: 10px;"><?php echo fc_lang('开启数据分表'); ?>：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[fenbiao][use]" value="1" <?php if ($data['fenbiao']['use']) { ?>checked<?php } ?> /> <?php echo fc_lang('开启'); ?></label>
                                    <label class="radio-inline"><input type="radio" name="data[fenbiao][use]" value="0" <?php if (!$data['fenbiao']['use']) { ?>checked<?php } ?> /> <?php echo fc_lang('关闭'); ?></label>
                                </div>
                                <span class="help-block"><?php echo fc_lang('开启分表可大大提高数据的负载力，系统采用5w数据基数分表存储，但无法进行综合查询和统计'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($mycfg) { ?>
                <div class="tab-pane" id="tab_5">
                    <?php if ($fn_include = $this->_load("$mycfg")) include($fn_include); ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="myfooter">
        <div class="row">
            <div class="portlet-body form">
                <div class="form-body">
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn green"> <i class="fa fa-save"></i> <?php echo fc_lang('保存'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>