<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
    function dr_to_key() {
        $.post("<?php echo dr_url('system/syskey'); ?>", function(data){
            $("#sys_key").val(data);
        });
        $.post("<?php echo dr_url('system/referer'); ?>", function(data){
            $("#sys_referer").val(data);
        });
    }
</script>

<div class="page-bar">
    <ul class="page-breadcrumb mylink">
        <?php echo $menu['link']; ?>
    </ul>
    <ul class="page-breadcrumb myname">
        <?php echo $menu['name']; ?>
    </ul>
    <div class="page-toolbar">
        <div class="btn-group pull-right">
            <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <?php echo fc_lang('操作菜单'); ?>
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

<form class="form-horizontal" action="" method="post" id="myform" name="myform">
    <input name="page" id="mypage" type="hidden" value="<?php echo $page; ?>" />
    <div class="portlet light bordered myfbody">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs" style="float:left;">
                <li class="<?php if ($page==0) { ?>active<?php } ?>">
                    <a href="#tab_0" data-toggle="tab" onclick="$('#mypage').val('0')"> <i class="fa fa-cog"></i> 公众号 </a>
                </li>
                <li class="<?php if ($page==2) { ?>active<?php } ?>">
                    <a href="#tab_2" data-toggle="tab" onclick="$('#mypage').val('2')"> <i class="fa fa-cubes"></i> 平台参数 </a>
                </li>
                <li class="<?php if ($page==3) { ?>active<?php } ?>">
                    <a href="#tab_3" data-toggle="tab" onclick="$('#mypage').val('3')"> <i class="fa fa-user"></i> 会员配置 </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">

                <div class="tab-pane <?php if ($page==0) { ?>active<?php } ?>" id="tab_0">
                    <div class="form-body">


                        <div class="form-group">
                            <label class="col-md-2 control-label">公众号名称：</label>
                            <div class="col-md-9">
                                <input class="form-control input-xlarge" type="text" name="data[cname]" value="<?php echo $data['cname']; ?>" >
                                <span class="help-block">填写公众号的帐号名称</span>
                            </div>
                        </div>
                        <div class="form-group" style="display: none">
                            <label class="col-md-2 control-label">公众号帐号：</label>
                            <div class="col-md-9">
                                <input class="form-control input-xlarge" type="text" name="data[account]" value="<?php echo $data['account']; ?>" >
                                <span class="help-block">填写公众号的帐号，一般为英文帐号</span>
                            </div>
                        </div>
                        <div class="form-group" style="display: none">
                            <label class="col-md-2 control-label">原始ID：</label>
                            <div class="col-md-9">
                                <input class="form-control input-xlarge" type="text" name="data[original]" value="<?php echo $data['original']; ?>" >
                                <span class="help-block">在给粉丝发送客服消息时,原始ID不能为空。建议您完善该选项</span>
                            </div>
                        </div>
                        <div class="form-group" style="display:none">
                            <label class="col-md-2 control-label">级别：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input type="radio" name="data[level]" <?php if (1 == $data['level']) { ?>checked<?php } ?> value="1" /> 普通订阅号</label>
                                    <label class="radio-inline"><input type="radio" name="data[level]" <?php if (2 == $data['level']) { ?>checked<?php } ?> value="2" /> 普通服务号</label>
                                    <label class="radio-inline"><input type="radio" name="data[level]" <?php if (3 == $data['level']) { ?>checked<?php } ?> value="3" /> 认证订阅号</label>
                                    <label class="radio-inline"><input type="radio" name="data[level]" <?php if (4 == $data['level']) { ?>checked<?php } ?> value="4" /> 认证服务号</label>
                                </div>
                                <span class="help-block">注意：即使公众平台显示为“未认证”, 但只要【公众号设置】/【账号详情】下【认证情况】显示资质审核通过, 即可认定为认证号.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">AppId：</label>
                            <div class="col-md-9">
                                <input class="form-control input-xlarge" type="text" name="data[key]" value="<?php echo $data['key']; ?>" >
                                <span class="help-block">请填写微信公众平台后台的AppId</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">AppSecret：</label>
                            <div class="col-md-9">
                                <input class="form-control input-xlarge" type="text" name="data[secret]" value="<?php echo $data['secret']; ?>" >
                                <span class="help-block">请填写微信公众平台后台的AppSecret, 只有填写这两项才能管理自定义菜单</span>
                            </div>
                        </div>
                        <div class="form-group" style="display:none">
                            <label class="col-md-2 control-label">微信二维码：</label>
                            <div class="col-md-9" style="padding-top:3px;">
                                <?php echo dr_field_input('qrcode', 'File', $field['qrcode']['setting'], $data['qrcode']); ?>
                                <span class="help-block">只支持JPG图片</span>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="tab-pane <?php if ($page==2) { ?>active<?php } ?>" id="tab_2">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label">URL：</label>
                            <div class="col-md-9">
                                <div class="form-control-static"><?php echo SITE_URL; ?>index.php?c=weixin</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Token：</label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[token]" id="sys_key" value="<?php echo $data['token']; ?>"  ></label>
                                <label><button class="btn btn-sm blue" type="button" name="button" onclick="dr_to_key()"> <?php echo fc_lang('生成'); ?> </button></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">EncodingAESKey：</label>
                            <div class="col-md-9">
                                <div class="form-control-static">随机生成得到密钥，不需要自己填写</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">消息加解密方式：</label>
                            <div class="col-md-9">
                                <div class="form-control-static">根据自己的需要选择其中一种</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane <?php if ($page==3) { ?>active<?php } ?>" id="tab_3">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label">关联会员模式：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input name="data[user_type]" type="radio" value="1" <?php if ($data['user_type']) { ?>checked<?php } ?> /> <?php echo fc_lang('自动注册'); ?></label>
                                    <label class="radio-inline"><input name="data[user_type]" type="radio" value="0" <?php if (!$data['user_type']) { ?>checked<?php } ?> /> <?php echo fc_lang('绑定会员'); ?></label>
                                </div>
                                <span class="help-block">自动注册模式是用户关注时自动创建一个会员号并归类到快捷登录会员组中</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">提醒绑定或注册会员：</label>
                            <div class="col-md-9">
                                <div class="radio-list">
                                    <label class="radio-inline"><input name="data[is_tuser]" type="radio" value="1" <?php if ($data['is_tuser']) { ?>checked<?php } ?> /> <?php echo fc_lang('开启'); ?></label>
                                    <label class="radio-inline"><input name="data[is_tuser]" type="radio" value="0" <?php if (!$data['is_tuser']) { ?>checked<?php } ?> /> <?php echo fc_lang('关闭'); ?></label>
                                </div>
                                <span class="help-block">当用户会话时提醒用户绑定或者注册本站会员</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">会员注册的提醒文字：</label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:80px; width:90%;" name="data[txt_tuser]"><?php echo $data['txt_tuser']; ?></textarea>
                                <span class="help-block">以上文字请不要带有链接,可以写QQ表情代码</span>
                            </div>
                        </div>


                    </div>
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
    </div>
</form>
<script type="text/javascript">
    function dr_to_key() {
        $.post("<?php echo dr_url('system/syskey'); ?>", function(data){
            $("#sys_key").val(data);
        });
    }
</script>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>