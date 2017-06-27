<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>
<script type="text/javascript">
$(function() {
	$(".select-cat").click(function(){
		var action = $(this).attr("action");
		var childs = $(this).attr("childs");
		var catid = $(this).attr("catid");
		var catids= new Array(); //定义一数组
		catids = childs.split(","); //字符分割
		if (action == 'close') {
			$.cookie('dr_<?php echo SITE_ID; ?>_share_<?php echo $this->ci->router->class; ?>_'+catid, null,{path:"/",expires: -1});
			$(this).attr("action", "open");
			$(this).html("[-]");
			for (i=0;i<catids.length ;i++ ) {   
				if (catids[i] != catid) {
					$(".dr_catid_"+catids[i]).show();
				}
			}
		} else {
			// 关闭状态存储cookie
			$.cookie('dr_<?php echo SITE_ID; ?>_share_<?php echo $this->ci->router->class; ?>_'+catid, 1);
			$(this).attr("action", "close");
			$(this).html("[+]");
			for (i=0;i<catids.length ;i++ ) {   
				if (catids[i] != catid) {
					$(".dr_catid_"+catids[i]).hide();
				}
			}
		}
	});
	$(".select-cat").each(function(){
		var childs = $(this).attr("childs");
		var catid = $(this).attr("catid");
		var cache = $.cookie('dr_<?php echo SITE_ID; ?>_share_<?php echo $this->ci->router->class; ?>_'+catid);
		if (cache) {
			$(this).attr("action", "close");
			$(this).html("[+]");
			var catids= new Array(); //定义一数组
			catids = childs.split(","); //字符分割
			for (i=0;i<catids.length ;i++ ) {   
				if (catids[i] != catid) {
					$(".dr_catid_"+catids[i]).hide();
				}
			}
		} 
	});
});
function dr_html() {
	alert('aa');
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
	<small><?php echo fc_lang('共享栏目是对共享模块内容的分类管理, 可用于全部共享模块中使用'); ?></small>
</h3>

<form action="" method="post" name="myform" id="myform">
	<input name="action" id="action" type="hidden" value="order" />
	<div class="portlet mylistbody">
		<div class="portlet-body">
			<div class="table-scrollable">

				<table class="mytable table table-striped table-bordered table-hover table-checkable dataTable">

			<thead>
			<tr>
				<th width="20" align="right"></th>
				<th width="50"><?php echo fc_lang('排序'); ?></th>
				<th>Id</th>
				<th><?php echo fc_lang('栏目名称'); ?></th>
				<th><?php echo fc_lang('栏目目录'); ?></th>
				<th><?php echo fc_lang('栏目类型'); ?></th>
				<th><?php echo fc_lang('共享模块'); ?></th>
				<th><?php echo fc_lang('静态'); ?></th>
				<th class="dr_option"><?php echo fc_lang('操作'); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php echo $list; ?>
			<tr class="mtable_bottom">
				<th  ><input name="dr_select" id="dr_select" class="toggle md-check" type="checkbox" onClick="dr_selected()" /></th>
				<td colspan="99" >
				<?php if ($this->ci->is_auth('category_share/del')) { ?><button data-toggle="confirmation" id="dr_confirm_set_all" data-original-title="<?php echo fc_lang('您确定要这样操作吗？'); ?>" type="button" class="btn red btn-sm" name="option"> <i class="fa fa-trash"></i> <?php echo fc_lang('删除'); ?></button><?php }  if ($this->ci->is_auth('category_share/edit')) { ?><button data-toggle="confirmation" id="dr_confirm_order" data-original-title="<?php echo fc_lang('排序按从小到大排列，最大支持99'); ?>" type="button" class="btn green btn-sm" name="option"> <i class="fa fa-edit"></i> <?php echo fc_lang('排序'); ?></button><?php } ?>
				</td>
			</tr>
			</tbody>
			</table>
		</div>
	</div>
</div>
</form>
<?php if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>