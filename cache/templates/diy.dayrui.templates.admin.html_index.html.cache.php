<?php if ($fn_include = $this->_include("nheader.html")) include($fn_include); ?>

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
	<small>系统仅可用生成网站首页、模块栏目、模块内容、模块首页</small>
</h3>

<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-green"><i class="fa fa-file"></i> <?php echo fc_lang('静态生成'); ?></span>
		</div>
	</div>
	<div class="portlet-body">
		<label><a href="<?php echo dr_url('html/tohtml'); ?>" class="btn green onloading"> <?php echo fc_lang('生成网站首页'); ?> </a></label>
		<?php if (is_array($module)) { $count=count($module);foreach ($module as $t) {  if ($t['ishtml'] && !$t['share']) { ?>
		<label><a href="<?php echo dr_url('html/tohtml'); ?>&dir=<?php echo $t['dirname']; ?>" class="btn green onloading"> <?php echo fc_lang('生成%s首页', $t['name']); ?> </a></label>
		<?php }  } } ?>
	</div>
</div>

<?php if ($share) { ?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-green"><i class="fa fa-file-o"></i> <?php echo fc_lang('共享模块'); ?></span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<form action="index.php?c=category&m=html" method="post">
			<input type="hidden" name="type" value="html" id="category_share">
			<div class="col-sm-4">
				<p class="text-center"><?php echo $share; ?></p>
				<p class="text-center">
					<button type="submit" onclick="$('#category_share').val('html')" class="btn green noloading"><i class="fa fa-refresh"></i> <?php echo fc_lang('生成共享栏目'); ?> </button>
				</p>
			</div>
			</form>
		</div>
	</div>
</div>
<?php }  if (is_array($module)) { $count=count($module);foreach ($module as $t) { ?>
<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-green"><i class="fa <?php echo $t['icon']; ?>"></i> <?php echo fc_lang($t['name']); ?></span>
		</div>
	</div>
	<div class="portlet-body">
		<?php if ($t['ishtml']) { ?>
		<div class="row">
			<?php if (!$t['share']) { ?>
			<form action="index.php?s=<?php echo $t['dirname']; ?>&c=category&m=html" method="post">
			<input type="hidden" name="type" value="html" id="category_<?php echo $t['dirname']; ?>">
			<div class="col-sm-4">
				<p class="text-center"><?php echo $t['select']; ?></p>
				<p class="text-center">
					<button type="submit" onclick="$('#category_<?php echo $t['dirname']; ?>').val('html')" class="btn green noloading"><i class="fa fa-refresh"></i> <?php echo fc_lang('生成栏目'); ?> </button>
				</p>
			</div>
			</form>
			<?php } ?>
			<form action="index.php?s=<?php echo $t['dirname']; ?>&c=show&m=html" method="post">
			<input type="hidden" name="type" value="html" id="show_<?php echo $t['dirname']; ?>">
			<div class="col-sm-4">
				<p class="text-center"><?php echo $t['select']; ?></p>
				<p class="text-center">
					<button type="submit" onclick="$('#show_<?php echo $t['dirname']; ?>').val('html')" class="btn green noloading"><i class="fa fa-refresh"></i> <?php echo fc_lang('生成内容'); ?> </button>
				</p>
			</div>
			</form>
		</div>
		<?php } else { ?>
		<a href="<?php echo $t['html_url']; ?>">此模块没有开启静态生成功能</a>
		<?php } ?>
	</div>
</div>
<?php } }  if ($fn_include = $this->_include("nfooter.html")) include($fn_include); ?>