<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<title>admin</title>
<link href="<?php echo THEME_PATH; ?>admin/css/index.css" rel="stylesheet" type="text/css" />
<link href="<?php echo THEME_PATH; ?>admin/css/table_form.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">var siteurl = "<?php echo SITE_PATH;  echo SELF; ?>";var memberpath = "<?php echo MEMBER_PATH; ?>";</script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/<?php echo SITE_LANGUAGE; ?>.js"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/validate.js"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/admin.js"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dayrui.js"></script>
<script type="text/javascript">
$(function() {
	$(".table-list tr").last().addClass("dr_border_none");
	$(".subnav .content-menu span").last().remove();
}); 
</script>
</head>
<body>
<div class="table-list" style="padding:20px 0;">
<table width="440" border="0" cellspacing="0" cellpadding="0" class="table_form">
<tr>
    <th align="right" width="120"><?php echo fc_lang('会员名称'); ?>：</th>
    <td align="left"><?php echo $data['username']; ?></td>
</tr>
<?php if ($data['email']) { ?>
<tr>
    <th align="right" width="120"><?php echo fc_lang('会员邮箱'); ?>：</th>
    <td align="left"><?php echo $data['email']; ?></td>
</tr>
<?php } ?>
<tr>
    <th align="right"><?php echo fc_lang('会员组'); ?>：</th>
    <td align="left">
	<?php $cache = $this->_cache_var('MEMBER'); eval('echo $cache'.$this->_get_var('group.$data[groupid].name').';');unset($cache);  if ($data['levelid']) { ?>&nbsp;（<?php $cache = $this->_cache_var('MEMBER'); eval('echo $cache'.$this->_get_var('group.$data[groupid].level.$data[levelid].name').';');unset($cache); ?>）<?php }  if ($data['overdue']) { ?>&nbsp;<?php echo fc_lang('过期时间：%s', dr_date($data['overdue']));  } ?></td>
</tr>
<?php if ($data['name']) { ?>
<tr>
    <th align="right"><?php echo fc_lang('联系方式'); ?>：</th>
    <td align="left"><?php echo htmlspecialchars($data['name']);  if ($data['phone']) { ?>&nbsp;（<?php echo $data['phone']; ?>）<?php } ?></td>
</tr>
<?php }  if ($data['regtime']) { ?>
<tr>
    <th align="right"><?php echo fc_lang('注册时间'); ?>：</th>
    <td align="left"><?php echo dr_date($data['regtime']);  if ($data['address']) { ?>（<a href="http://www.baidu.com/baidu?wd=<?php echo $data['regip']; ?>&tn=monline_dg" target="_blank"><?php echo $data['address']; ?></a>）<?php } ?></td>
</tr>
<?php } ?>
<tr>
    <th align="right"><?php echo SITE_MONEY; ?>：</th>
    <td align="left"><?php echo $data['money']; ?></td>
</tr>
<tr>
    <th align="right"><?php echo fc_lang('消费额'); ?>：</th>
    <td align="left"><?php echo $data['spend']; ?></td>
</tr>
<tr>
    <th align="right"><?php echo SITE_SCORE; ?>：</th>
    <td align="left"><?php echo $data['score']; ?></td>
</tr>
<tr>
    <th align="right"><?php echo SITE_EXPERIENCE; ?>：</th>
    <td align="left"><?php echo $data['experience']; ?></td>
</tr>
    <tr>
        <th align="right"><?php echo fc_lang('随机验证码'); ?>：</th>
        <td align="left"><?php echo $data['randcode']; ?></td>
    </tr>
</table>
</div>
</body>
</html>