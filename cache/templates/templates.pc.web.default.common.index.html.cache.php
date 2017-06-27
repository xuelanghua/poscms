<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>

<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-head">
			<div class="container">
				<div class="page-title">
					<h1><small>欢迎使用POSCMS高级版全能网站管理系统</small></h1>
				</div>
			</div>
		</div>
		<div class="page-content">
			<div class="container">
				<div class="page-content-inner">

					<div class="row">
						<div class="col-md-4">
							<div class="bg-white">
								<link href="<?php echo HOME_THEME_PATH; ?>slide/slide.css" type="text/css" rel="stylesheet">
								<div id="myslide" style="width: auto; height: auto; overflow: hidden">
									<table width="100%" cellSpacing="0" cellPadding="0">
										<tr>
											<td class="pic" id="bimg">
												<?php $return = $this->list_tag("action=navigator type=1"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
												<div class="<?php if ($key==0) { ?>dis<?php } else { ?>undis<?php } ?>" name="f">
													<a href="<?php echo $t['url']; ?>" title="<?php echo $t['title']; ?>" <?php if ($t['target']) { ?>target="_blank"<?php } ?>><img alt="<?php echo $t['title']; ?>" style="width: 100%; height: 260px;" src="<?php echo dr_thumb($t['thumb'], 350, 260); ?>" border="0"></a>
												</div>
												<?php } } ?>
												<table id="font_hd" width="100%" cellSpacing="0" cellPadding="0">
													<tr>
														<td class="title" id="info">
															<?php $return = $this->list_tag("action=navigator type=1"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
															<div class="<?php if ($key==0) { ?>dis<?php } else { ?>undis<?php } ?>" name="f">
																<a href="<?php echo $t['url']; ?>" title="<?php echo $t['title']; ?>" <?php if ($t['target']) { ?>target="_blank"<?php } ?>><?php echo $t['title']; ?></a>
															</div>
															<?php } } ?>
														</td>
														<td id="simg" nowrap="nowrap" style="text-align:right">
															<?php $return = $this->list_tag("action=navigator type=1"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
															<div class="<?php if ($key==0) {  } else { ?>f1<?php } ?>" onclick=play(x[<?php echo $key; ?>],<?php echo $key; ?>) name="f"><?php echo $key+1; ?></div>
															<?php } } ?>
														</td>
													</tr>
												</table>
												<script src="<?php echo HOME_THEME_PATH; ?>slide/slide.js"></script>
											</td>
										</tr>
									</table>
								</div>

							</div>
						</div>
						<div class="col-md-8">
							<div class="portlet light  ">
								<div class="portlet-title ">
									<div class="row">
										<div class="col-md-12">
											<!--调用type=2的头条数据-->
											<?php $return = $this->list_tag("action=navigator type=2"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) {  if ($key==0) { ?>
											<h4><a cl href="<?php echo $t['url']; ?>" <?php if ($t['target']) { ?>target="_blank"<?php } ?> class="title"><?php echo $t['title']; ?></a></h4>
											<?php } else {  if ($key==1) { ?><p class="links"><?php } ?>
											<a href="<?php echo $t['url']; ?>" <?php if ($t['target']) { ?>target="_blank"<?php } ?>>[<?php echo $t['title']; ?>]</a>
											<?php if ($key==$count-1) { ?></p><?php }  }  } } ?>
										</div>
									</div>
								</div>
								<div class="portlet-body ">
									<div class="row">
										<div class="col-md-6">
											<ul class="list-unstyled">
												<!--调用新闻模块的“首页中间”属性的最新10条-->
												<?php $return = $this->list_tag("action=module flag=1 module=news order=updatetime num=5"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
												<li style="line-height: 23px"><span class="badge badge-empty badge-success"></span>&nbsp;<a href="<?php echo $t['url']; ?>" class="title"><?php echo dr_strcut($t['title'], 28); ?></a></li>
												<?php } } ?>
											</ul>
										</div>
										<div class="col-md-6">
											<ul class="list-unstyled">
												<!--调用新闻模块的“首页中间”属性的最新10条-->
												<?php $return = $this->list_tag("action=module flag=1 module=news order=updatetime num=5,5"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
												<li style="line-height: 23px"><span class="badge badge-empty badge-success"></span>&nbsp;<a href="<?php echo $t['url']; ?>" class="title"><?php echo dr_strcut($t['title'], 28); ?></a></li>
												<?php } } ?>
											</ul>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="portlet light" style="height: 90px; text-align: center;">
								<h4>POSCMS全能网站管理系统</h4>
							</div>
						</div>
					</div>

					<!-- 调用新闻模块 -->
					<div class="row">
						<div class="col-lg-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<i class=" icon-layers font-green"></i>
										<span class="caption-subject bold uppercase"><a class=" font-green" href="<?php echo SITE_URL; ?>index.php?s=news">新闻模块</a></span>
									</div>
									<ul class="nav nav-tabs">
										<!-- 调用新闻模块的顶级栏目 -->
										<?php $return = $this->list_tag("action=category module=news pid=0"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<li><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a></li>
										<?php } } ?>
									</ul>
								</div>
								<div class="portlet-body">
									<div class="row">
										<div class="col-lg-7">
											<div class="row">
												<div class="col-md-12 media">
													<!-- 调用新闻模块的“今日视点”属性的第一条 -->
													<?php $return = $this->list_tag("action=module flag=3 module=news order=updatetime num=1"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
													<a class="pull-left" href="javascript:;">
														<img class="media-object" src="<?php echo dr_thumb($t['thumb'], 80, 80); ?>" style="width:80px; height:80px;"> </a>
													<div class="media-body">
														<h5 class="media-heading"><a href="<?php echo $t['url']; ?>" class="title"><?php echo $t['title']; ?></a></h5>
														<p> <?php echo dr_strcut($t['description'],120); ?></p>
													</div>
													<?php } } ?>
												</div>
											</div>
											<div class="row" style="margin-top: 10px">
												<div class="col-md-6">
													<ul class="list-unstyled">
														<!-- 调用新闻模块的“阅读推荐”属性的10条数据 -->
														<?php $return = $this->list_tag("action=module flag=4 module=news order=updatetime num=10"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
														<li style="line-height: 23px"><span class="badge badge-empty badge-success"></span>&nbsp;<a href="<?php echo $t['url']; ?>" class="title"><?php echo dr_strcut($t['title'], 28); ?></a></li>
														<?php } } ?>
													</ul>
												</div>
												<div class="col-md-6">
													<ul class="list-unstyled">
														<!-- 调用新闻模块最新的数据 -->
														<?php $return = $this->list_tag("action=module module=news order=updatetime num=10"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
														<li style="line-height: 23px"><span class="badge badge-empty badge-success"></span>&nbsp;<a href="<?php echo $t['url']; ?>" class="title"><?php echo dr_strcut($t['title'], 28); ?></a></li>
														<?php } } ?>
													</ul>
												</div>
											</div>

										</div>
										<div class="col-lg-5">
											<div class="row">
											<!-- 调用新闻模块带“图片”的数据 -->
											<?php $return = $this->list_tag("action=module thumb=1 module=news order=updatetime num=9"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
												<div class="col-sm-4" style="overflow:hidden">
													<div class="tile-container">
														<div class="tile-thumbnail">
															<a href="javascript:;">
																<a href="<?php echo $t['url']; ?>" ><img src="<?php echo dr_thumb($t['thumb'], 120, 75); ?>" height="75"></a>
															</a>
														</div>
														<div class="tile-title">
															<h5><a href="<?php echo $t['url']; ?>" class="title"><?php echo dr_strcut($t['title'], 15); ?></a></h5>
														</div>
													</div>
												</div>
											<?php } } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- 调用图片模块 -->
					<div class="row">
						<div class="col-lg-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<i class=" icon-layers font-green"></i>
										<span class="caption-subject bold uppercase"><a class=" font-green" href="/index.php?s=photo">图片模块</a></span>
									</div>
									<ul class="nav nav-tabs">
										<!-- 调用其子栏目 -->
										<?php $return = $this->list_tag("action=category pid=0 module=photo"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<li><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a></li>
										<?php } } ?>
									</ul>
								</div>
								<div class="portlet-body">
									<div class="row">
										<!-- 最新数据 -->
										<?php $return = $this->list_tag("action=module module=photo order=updatetime num=8"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<div class="col-sm-3">
											<div class="tile-container">
												<div class="tile-thumbnail">
													<a href="javascript:;">
														<a href="<?php echo $t['url']; ?>" ><img src="<?php echo dr_thumb($t['thumb'], 250, 200); ?>" width="250" height="200"></a>
													</a>
												</div>
												<div class="tile-title">
													<h5><a href="<?php echo $t['url']; ?>" class="title"><?php echo dr_strcut($t['title'], 28); ?></a></h5>
												</div>
											</div>
										</div>
										<?php } } ?>
									</div>
								</div>
							</div>
						</div>
					</div>


					<!-- 调用下载模块 -->
					<div class="row">
						<div class="col-lg-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<i class=" icon-layers font-green"></i>
										<span class="caption-subject bold uppercase"><a class=" font-green" href="/index.php?s=down">下载模块</a></span>
									</div>
									<ul class="nav nav-tabs">
										<!-- 调用其子栏目 -->
										<?php $return = $this->list_tag("action=category pid=0 module=down"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<li><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a></li>
										<?php } } ?>
									</ul>
								</div>
								<div class="portlet-body">
									<div class="row">
										<!-- 最新数据 -->
										<?php $return = $this->list_tag("action=module module=down order=updatetime num=8"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<div class="col-sm-3">
											<div class="tile-container">
												<div class="tile-thumbnail">
													<a href="javascript:;">
														<a href="<?php echo $t['url']; ?>" ><img src="<?php echo dr_thumb($t['thumb'], 250, 200); ?>" width="250" height="200"></a>
													</a>
												</div>
												<div class="tile-title">
													<h5><a href="<?php echo $t['url']; ?>" class="title"><?php echo dr_strcut($t['title'], 28); ?></a></h5>
												</div>
											</div>
										</div>
										<?php } } ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- 友情链接 -->
					<div class="row">
						<div class="col-lg-12">
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<i class=" icon-layers font-green"></i>
										<span class="caption-subject bold uppercase">友情链接</span>
									</div>
								</div>
								<div class="portlet-body">
									<div class="row">
										<div class="col-md-12">
										<?php $return = $this->list_tag("action=navigator type=4"); if ($return) extract($return); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { ?>
										<a href="<?php echo $t['url']; ?>" title="<?php echo $t['title']; ?>" <?php if ($t['target']) { ?>target="_blank"<?php } ?>><?php echo $t['name']; ?></a>
										<?php } } ?>
											</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>



<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>