<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.2&key="></script></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
<style  type="text/css">
*{ padding:0; margin:0}
body{ font-size: 12px;}
#toolbar{ background-color:#E5ECF9;zoom:1; height:24px; line-height:24px; padding:0 12px; margin-top:3px; position:relative}
#toolbar a{ display:inline-block;zoom:1;*display:inline; color:#4673CC}
#toolbar a.mark,#toolbar a.map{ background: url(<?php echo THEME_PATH; ?>admin/images/map_mark.png) no-repeat left 50%; padding:0 0 0 20px}
#toolbar a.map{ background-image:url(<?php echo THEME_PATH; ?>admin/images/map.png); margin-left:12px}
#toolbar .right{ float:right;}
#toolbar .CityBox{ position:absolute; left:40px; top:30px; background-color:#fff; border:1px solid #8BA4D8; padding:2px; z-index:1; width:200px; display:none}
#toolbar .CityBox h4{ background-color:#E5ECF9; line-height:20px; height:20px; padding:0 6px; color:#6688CC; position:relative}
#toolbar .CityBox h4 div.top{background: url(<?php echo THEME_PATH; ?>admin/images/topo.png) no-repeat; height:6px; width:11px; position:absolute; top:-9px; left:38px; line-height:normal; font-size:0 }
#toolbar .CityBox .content{ padding:6px; height:150px;overflow-y:auto; padding-bottom:8px}
#toolbar .CityBox a.close{ background: url(<?php echo THEME_PATH; ?>admin/images/cross.png) no-repeat left 3px; display:block; width:16px; height:16px;position: absolute;outline:none;right:3px; bottom:1px}
#toolbar .CityBox a.close:hover{background-position: left -46px}
#toolbar .CityBox .line{ height:6px; border-bottom:1px solid #EBEBEB; margin-bottom:5px;}
#mapObj{height:<?php echo $height; ?>px; width:<?php echo $width; ?>px; padding-top:1px}
</style>
</head>
<body>
<div id="toolbar">
	<div class="selCity">
    	<div class="right">
		    <a href="javascript:;" class="mark" onClick="addMarker();"><?php echo fc_lang('添加标注'); ?></a>
			<a href="javascript:;" onClick="removeMarker();" class="map"><?php echo fc_lang('重置地图'); ?></a>
		</div>
    	<strong id="curCity"><?php echo $city; ?></strong> [<a onClick="mapClose();" id="curCityText" href="javascript:;"><?php echo fc_lang('更换城市'); ?></a>]
    </div>
    <div class="CityBox">
    	<h4><?php echo fc_lang('城市'); ?><div class="top"></div><a href="javascript:;" class="close" onClick="mapClose();"></a></h4>
        <div class="content">
			<p>
			<?php if (is_array($list)) { $count=count($list);foreach ($list as $t) { ?>
				<a onclick="keywordSearch('<?php echo $t; ?>')" href="javascript:;"><?php echo $t; ?></a> 
			<?php } } ?>
			</p>
            <div class="line"></div>
			<input type="text" onclick="$(this).val('');" id="citywd" name="citywd" style="width:140px; height:18px" value="<?php echo fc_lang('输入城市名'); ?>">
			<input type="submit" onclick="keywordSearch()" class="city_submit" value="<?php echo fc_lang('搜索'); ?>">
        </div>
    </div>
</div>
<input name="data[<?php echo $name; ?>]" id="<?php echo $name; ?>" type="hidden" value="<?php echo $value; ?>">
<div id="mapObj" class="view"></div>
<script type="text/javascript">  
var mapObj = new BMap.Map("mapObj");// 创建地图实例  
//向地图中添加缩放控件
var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
mapObj.addControl(ctrl_nav);
mapObj.enableDragging();//启用地图拖拽事件，默认启用(可不写)
mapObj.enableScrollWheelZoom();//启用地图滚轮放大缩小
mapObj.enableDoubleClickZoom();//启用鼠标双击放大，默认启用(可不写)
mapObj.enableKeyboard();//启用键盘上下左右键移动地图
if($('#<?php echo $name; ?>').val()) {
	drawPoints();
} else {
	mapObj.centerAndZoom("<?php echo $city; ?>");
} 
//设置切换城市
function keywordSearch(city) {
	if(city==null || city=='') {
		var city=$("#citywd").val();
	}
	mapObj.setCenter(city);
	$("#curCity").html(city);
}

function drawPoints(){
	var data = $('#<?php echo $name; ?>').val();
	var data = data.split(',');
	var lngX = data[0];
	var latY = data[1];
	var zoom = <?php echo $level; ?>;
	mapObj.centerAndZoom(new BMap.Point(lngX,latY),zoom);
	// 创建图标对象
	var myIcon = new BMap.Icon('<?php echo THEME_PATH; ?>admin/images/mak.png', new BMap.Size(27, 45));

	// 创建标注对象并添加到地图
	var center = mapObj.getCenter();
	var point = new BMap.Point(lngX,latY);
	var marker = new BMap.Marker(point, {icon: myIcon});
	marker.enableDragging();
	mapObj.addOverlay(marker);
	var ZoomLevel = mapObj.getZoom();
	marker.addEventListener("dragend", function(e){
		$('#<?php echo $name; ?>').val(e.point.lng+','+e.point.lat); 
	}) 
}

function addMarker(){ 
	  mapObj.clearOverlays();
	  // 创建图标对象
	  var myIcon = new BMap.Icon('<?php echo THEME_PATH; ?>admin/images/mak.png', new BMap.Size(27, 45));
	  // 创建标注对象并添加到地图
	  var center = mapObj.getCenter();
	  var point = new BMap.Point(center.lng,center.lat);
	  var marker = new BMap.Marker(point, {icon: myIcon});
	  marker.enableDragging();
	  mapObj.addOverlay(marker);
	  var ZoomLevel = mapObj.getZoom();
	  $('#<?php echo $name; ?>').val(center.lng+','+center.lat);
	  marker.addEventListener("dragend", function(e){  
		$('#<?php echo $name; ?>').val(e.point.lng+','+e.point.lat); 
	}) 
}

function mapClose(){
	var CityBox=$(".CityBox");
	if(CityBox.css('display')=='none'){
		CityBox.show();
	}else{
		CityBox.hide();
	}
}

function removeMarker() {
	mapObj.clearOverlays();
	mapObj.centerAndZoom("<?php echo $city; ?>");
	$("#curCity").html('<?php echo $city; ?>');
	$('#<?php echo $name; ?>').val('');
}
</script> 
</body>
</html>