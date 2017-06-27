<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script language="javascript">
<?php if ($error) { ?>
$(function() {
	dr_tips('<font color=red><?php echo $error; ?></font>', 3);
});
<?php } ?>
function dr_load_url() {
	var catid = $("#dr_catid").val();
	if (catid==0) {
		dr_tips("<font color=red><?php echo fc_lang('请选择一个的栏目'); ?></font>", 3);
		return;
	}
}
function dr_select_all() {
	$("#dr_catid").find("option").attr("selected", "selected");
}
</script>
<form class="form-horizontal" action="" method="post" id="myform" name="myform">
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
		<small><?php echo fc_lang('批量设置URL规则可以加载某一个栏目的URL规则，再同步更新至其他栏目'); ?></small>
	</h3>

	<div class="portlet light bordered myfbody">
		<div class="portlet-body">
			<div class="row">
				<div class="portlet-body form">
					<div class="form-body">
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('栏目选择'); ?>：</label>
							<div class="col-md-9">
								<label><?php echo $select; ?></label>

								<label><button type="button" onclick="dr_select_all()" name="button" class="btn blue btn-sm"> <i class="fa fa-arrow-left"></i>  <?php echo fc_lang('全选'); ?> </button> </label>
								<span class="help-block"> <?php echo fc_lang('以下规则为空时表示上面选择的栏目关闭自定义URL功能'); ?> </span>
							</div>
						</div>
					</div>

					<div class="form-body">
						<div class="form-group">
							<label class="col-md-2 control-label"><?php echo fc_lang('URL规则'); ?>：</label>
							<div class="col-md-9">
								<label>
								<select class="form-control" name="urlrule">
									<option value="0"> -- </option>
									<?php $return_u = $this->list_tag("action=cache name=urlrule  return=u"); if ($return_u) extract($return_u); $count_u=count($return_u); if (is_array($return_u)) { foreach ($return_u as $key_u=>$u) {  if ($u['type']==3) { ?><option value="<?php echo $u['id']; ?>"> <?php echo $u['name']; ?> </option><?php }  } } ?>
								</select>
								</label>
								<label> &nbsp;&nbsp;<a href="<?php echo dr_url('urlrule/index'); ?>" style="color:blue !important"><?php echo fc_lang('[URL规则管理]'); ?></a> </label>
							</div>
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
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>