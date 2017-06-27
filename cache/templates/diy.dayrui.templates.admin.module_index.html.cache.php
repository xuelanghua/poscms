<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	$(".table-list td").last().css('border-bottom','1px solid #EEEEEE');
});
function dr_module_export(url) {
	var throughBox = $.dialog.through;
	var dr_Dialog = throughBox({title: "生成须知"});
	dr_Dialog.content('<div style="padding:10px 20px"><li style="line-height:27px;list-style:none;">1、将当前模块生成新的字段配置文件</li><li style="line-height:27px;list-style:none;">2、新模块含自定义字段，不含栏目和数据</li><li style="line-height:27px;list-style:none;">3、原config目录数据将备份为_config目录</li><li style="line-height:27px;color:red;list-style:none;">4、当前模块目录必须有可写权限，否则无法创建</li><li style="line-height:45px;list-style:none;">模块新名称： <input id="dr_module_new_name" class="input-text" type="text" style="width:145px;height:28px" /></li></div>');
	dr_Dialog.button({name: "下一步", callback:function() {
			var win = $.dialog.top;
			var name = win.$("#dr_module_new_name").val();
			location.href = url+"&name="+name;
		},
		focus: true
	});
}
function dr_copy_module(url) {
	var throughBox = $.dialog.through;
	var dr_Dialog = throughBox({title: "<?php echo fc_lang('创建/复制模块'); ?>"});
	$.ajax({type: "GET", url:url, dataType:'text', success: function (text) {
			var win = $.dialog.top;
			dr_Dialog.content(text);
			dr_Dialog.button({name: "<?php echo fc_lang('执行'); ?>", callback:function() {
					win.$("#mark").val("0"); // 标示可以提交表单
					if (win.dr_form_check()) { // 按钮返回验证表单函数
						var _data = win.$("#myform").serialize();
						$.ajax({type: "POST",dataType:"json", url: url, data: _data, // 将表单数据ajax提交验证
							success: function(data) {
								if (data.status == 1) {
									dr_tips(data.code, 3, 1); 
									setTimeout("window.location.reload(true)", 3000);
								} else {
									dr_tips(data.code, 5); 
									return true;
								}
							},
							error: function(HttpRequest, ajaxOptions, thrownError) {
								alert(HttpRequest.responseText);
							}
						});
					}
					return false;
				},
				focus: true
			});
	    },
	    error: function(HttpRequest, ajaxOptions, thrownError) {
			alert(HttpRequest.responseText);
		}
	});
}
</script>
<style>
.dr_none td {background-color: #f8f8f8;}
</style>
<div class="page-bar">
	<ul class="page-breadcrumb mylink">
		<?php echo $menu['link']; ?>
		<li> <a href="javascript:dr_copy_module('<?php echo dr_url('module/copy',array('dir'=>'news')); ?>');"><i class="fa fa-plus"></i> <?php echo fc_lang('创建模块'); ?></a> <i class="fa fa-circle"></i> </li>
		<li> <a href="<?php echo dr_help_url(58); ?>" target="_blank"><i class="fa fa-book"></i> <?php echo fc_lang('在线帮助'); ?></a> </li>
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
	<small><?php echo fc_lang('共享模块栏目为传统网站模式；独立模块栏目为独立化设计，每个模块拥有不同的栏目'); ?></small>
</h3>

<form class="form-horizontal" action="" method="post" id="myform" name="myform">
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

					<thead>
					<tr>
						<th width="50" align="center"><?php echo fc_lang('排序'); ?></th>
						<th width="50" align="center"><?php echo fc_lang('可用'); ?></th>
						<th width="50" align="center"><?php echo fc_lang('类型'); ?></th>
						<th width="100" align="left"><?php echo fc_lang('名称'); ?></th>
						<th width="50" align="left"><?php echo fc_lang('目录'); ?></th>
						<th width="30" align="center"><?php echo fc_lang('站点'); ?></th>
						<th width="30" align="center"><?php echo fc_lang('版本'); ?></th>
						<th align="left" class="dr_option"><?php echo fc_lang('操作'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if (is_array($list[1])) { $count=count($list[1]);foreach ($list[1] as $dir=>$t) { ?>
					<tr>
						<td align="center"><?php if (!$t['space']) { ?><input class="input-text" style="text-align:center;padding-left:0" type="text" name="data[<?php echo $t['id']; ?>][displayorder]" value="<?php echo intval($t['displayorder']); ?>" size="3"/><?php } ?></td>
						<td align="center"><?php if ($this->ci->is_auth('module/edit') && !$t['space']) { ?><a href="javascript:;" onClick="return dr_dialog_set('<?php echo $t['disabled'] ? fc_lang('<font color=blue><b>你确定要启用它？启用后将正常使用</b></font>') : fc_lang('<font color=red><b>你确定要禁用它？禁用后将无法使用</b></font>'); ?>','<?php echo dr_url('module/disabled',array('id'=>$t['id'])); ?>');"><img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['disabled'] ? 0 : 1 ?>.gif"></a><?php } else { ?><img src="<?php echo THEME_PATH; ?>admin/images/<?php echo $t['disabled'] ? 0 : 1 ?>.gif"></a><?php } ?></td>
						<td align="left">
							<?php if ($t['space'] || $t['nodb']) { ?>
							<span class="badge badge-warning badge-roundless"> <?php echo fc_lang('无'); ?> </span>
							<?php } else {  if ($t['share']) { ?>
								<div class="btn-group">
										<span class="badge badge-success badge-roundless dropdown-toggle" data-toggle="dropdown"> <?php echo fc_lang('共享'); ?>
											<i class="fa fa-angle-down"></i>
										</span>
									<ul class="dropdown-menu" role="menu">
										<li role="presentation">
											<a role="menuitem"href="javascript:;"> <?php echo fc_lang('主表: %s', SITE_ID.'_'.$dir); ?></a>
										</li>
										<li role="presentation">
											<a role="menuitem"href="javascript:;"> <?php echo fc_lang('附表: %s', SITE_ID.'_'.$dir.'_data_0 ...'); ?></a>
										</li>
										<li role="presentation">
											<a role="menuitem"href="javascript:;"> <?php echo fc_lang('栏目: %s', SITE_ID.'_share_category'); ?></a>
										</li>
									</ul>
								</div>
								<?php } else { ?>
								<div class="btn-group">
									<span class="badge badge-info badge-roundless dropdown-toggle" data-toggle="dropdown"> <?php echo fc_lang('独立'); ?>
										<i class="fa fa-angle-down"></i>
									</span>
									<ul class="dropdown-menu" role="menu">
										<li role="presentation">
											<a role="menuitem"href="javascript:;"> <?php echo fc_lang('主表: %s', SITE_ID.'_'.$dir); ?></a>
										</li>
										<li role="presentation">
											<a role="menuitem"href="javascript:;"> <?php echo fc_lang('附表: %s', SITE_ID.'_'.$dir.'_data_0 ...'); ?></a>
										</li>
										<li role="presentation">
											<a role="menuitem"href="javascript:;"> <?php echo fc_lang('栏目: %s', SITE_ID.'_'.$dir.'_category'); ?></a>
										</li>
									</ul>
								</div>
								<?php }  } ?>
						</td>
						<td align="left"><?php echo $t['name']; ?></td>
						<td align="left"><?php echo $dir; ?></td>
						<td align="center"><?php if ($t['space']) {  } else { ?><a href="<?php echo dr_url('module/install', array('id'=>$t['id'])); ?>"><?php echo count($t['site']); ?></a><?php } ?></td>
						<td align="center"><?php echo $t['version']; ?></td>
						<td align="left" class="dr_option">
							<?php if (!$t['space']) {  if ($this->ci->is_auth('module/config')) { ?><a class="ago" href="<?php echo dr_url('module/config',array('id'=>$t['id'], 'all' => 1)); ?>"> <i class="fa fa-cog"></i> <?php echo fc_lang('配置'); ?></a><?php }  if ($this->ci->is_auth('module/install')) { ?><a class="alist" href="<?php echo dr_url('module/install', array('id'=>$t['id'])); ?>" style="color:green"> <i class="fa fa-cubes"></i> <?php echo fc_lang('站点管理'); ?></a><?php }  if (!$t['nodb']) {  if ($this->ci->is_auth('module/config')) { ?><a class="ago" href="javascript:;" onclick="dr_copy_module('<?php echo dr_url('module/copy',array('dir'=>$dir)); ?>')"> <i class="fa fa-copy"></i> <?php echo fc_lang('复制'); ?></a><?php }  if (SYS_DEBUG) { ?><a class="alist" href="javascript:;" onclick="dr_module_export('<?php echo dr_url('module/export',array('dir'=>$dir)); ?>')"> <i class="fa fa-share-square-o"></i> <?php echo fc_lang('生成'); ?></a><?php }  if ($this->ci->is_auth('admin/field/index')) { ?><a class="aadd" href="<?php echo $duri->uri2url('admin/field/index/rname/module/rid/'.$t['id']); ?>"> <i class="fa fa-plus-square"></i> <?php echo fc_lang('模块字段'); ?></a><?php }  if ($t['extend'] && $this->ci->is_auth('admin/field/index')) { ?><a class="aadd" href="<?php echo $duri->uri2url('admin/field/index/rname/extend/rid/'.$t['id']); ?>"> <i class="fa fa-plus-circle"></i> <?php echo fc_lang('扩展字段'); ?></a><?php }  if ($this->ci->is_auth('mform/index')) { ?><a class="alist" href="<?php echo dr_url('mform/index',array('dir'=>$dir)); ?>"> <i class="fa fa-tasks"></i> <?php echo fc_lang('表单'); ?></a><?php }  }  } else {  if ($this->ci->is_auth('module/config')) { ?><a class="ago" href="<?php echo dr_url('space/setting/space'); ?>"> <i class="fa fa-cog"></i> <?php echo fc_lang('配置'); ?></a><?php }  }  if ($this->ci->is_auth('module/uninstall')) { ?><a class="adel" href="javascript:;" onClick="return dr_confirm_url('<font color=red><b><?php echo fc_lang('该操作将会删除全部站点中的模块内容数据，您确定吗？'); ?></b></font>','<?php echo dr_url('module/uninstall',array('id'=>$t['id'])); ?>');"> <i class="fa fa-trash"></i> <?php echo fc_lang('卸载'); ?></a><?php }  if (!$t['site'][SITE_ID]['use'] && !$t['space']) { ?><a style="color:red" href="<?php echo dr_url('module/install', array('id'=>$t['id'])); ?>">[<?php echo fc_lang('当前站点尚未安装'); ?>]</a><?php } ?>

							<span id="dr_domain_<?php echo $dir; ?>"></span>
							<script type="text/javascript">
								<?php if (is_array($t['site'])) { $count=count($t['site']);foreach ($t['site'] as $s) {  if ($s['domain']) { ?>
									$.get("<?php echo dr_url('home/domain', array('domain' => $s['domain'])); ?>", function(data){
										if (data) {
											$("#dr_domain_<?php echo $dir; ?>").html("<a href='<?php echo dr_url('module/install',array('id'=>$t['id'])); ?>' style='color:red;'>域名解析异常</a>");
										}
									});
									<?php }  } } ?>
							</script>
						</td>
					</tr>
					<?php } }  if (is_array($list[0])) { $count=count($list[0]);foreach ($list[0] as $dir=>$t) { ?>
					<tr class="dr_none">
						<td align="center"><input class="input-text" style="text-align:center;padding-left:0" type="text" name="" value="0" size="3"/></td>
						<td align="center">
							<?php if ($this->ci->is_auth('module/install')) {  if ($t['space'] || $t['nodb']) { ?>
									<a href="javascript:void(0);" onclick="dr_install('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FineCMS官方设计室无法全面监控由第三方上传至商店里的应用、模块（以下简称应用程序），因此不保证应用程序的合法性、安全性、完整性、真实性或品质等。您从商店下载应用程序时，同意自行判断并承担所有风险，而不依赖于FineCMS官方设计室。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;在任何情况下，FineCMS官方设计室有权依法停止商店服务并采取相应行动，包括但不限于对于相关应用程序进行卸载、暂停服务的全部或部分、保存有关记录并向有关机关报告。由此对您及第三人可能造成的损失，FineCMS官方设计室不承担任何直接、间接或者连带的责任。', '<?php echo dr_url('module/install_all', array('dir'=>$dir)); ?>')" style="color:#00F">
								<?php } else { ?>
									<a href="javascript:void(0);" onclick="dr_install_share('内容模块有各自的主表和栏目表,相互无关联,完全独立化管理,负载能力极高', '<?php echo dr_url('module/install_all', array('dir'=>$dir)); ?>')" style="color:#00F">
								<?php }  } else { ?>
								<a href="javascript:;" style="color:#999">
							<?php }  echo fc_lang('安装'); ?></a>
						</td>
						<td align="left"></td>
						<td align="left"><?php echo $t['name']; ?></td>
						<td align="left"><?php echo $dir; ?></td>
						<td align="center">0</td>
						<td align="center"><?php echo $t['version']; ?></td>
						<td align="left" class="dr_option">
							<?php if ($admin['adminid'] == 1) { ?><a class="adel" href="javascript:;" onClick="return dr_confirm_url('<font color=red><b><?php echo fc_lang('该操作将会从磁盘中彻底删除它且数据不可恢复，您确定吗？'); ?></b></font>','<?php echo dr_url('module/delete',array('dir'=>$dir)); ?>');" style="color:#F00"> <i class="fa fa-close"></i> <?php echo fc_lang('删除'); ?></a><?php } ?>
						</td>
					</tr>
					<?php } }  if ($this->ci->is_auth('module/edit')) { ?>
					<tr class="mtable_bottom">
						<td align="left"></td>
						<td colspan="8" align="left">
							<button type="submit" class="btn green btn-sm"> <i class="fa fa-save"></i> <?php echo fc_lang('排序'); ?></button>&nbsp;<div class="onShow"><?php echo fc_lang('排序按从小到大排列，最大支持99'); ?></div>
						</td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>