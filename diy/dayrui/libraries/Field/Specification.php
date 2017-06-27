<?php

/**
 * Dayrui Website Management System
 *
 * @since		version 2.6.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class F_Specification extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('商城_商品规格'); // 字段名称
		$this->fieldtype = array('TEXT' => '255'); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
		$option['width'] = isset($option['width']) ? $option['width'] : '80%';
		return '<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
				</div>
			</div>';
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
		return dr_string2array($value);
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		
		$value = $this->ci->post[$field['fieldname']];
		if (is_array($value) && $value['value'] && isset($value['use']) && $value['use']) {
			$price = array();
			$quantity = 0;
			foreach ($value['value'] as $oname => $v) {
				$price[] = $v['price'];
				$quantity+= intval($v['quantity']);
			}
			$_POST['data']['order_price'] = min($price);
			$_POST['data']['order_quantity'] = $quantity;
		} else {
			$value = array();
		}
		//echo '<pre>';print_r($_POST['data']['order_specification']);exit;
		$this->ci->data[$field['ismain']][$field['fieldname']] = dr_array2string($value);
	}
	
	/**
	 * 字段入库后执行
	 */
	public function insert_last_value($cid, $value, $field) {

        $value = dr_string2array($value);
        if (is_array($value) && $value['value'] && isset($value['use']) && $value['use']) {
            $price = array();
            $quantity = 0;
            foreach ($value['value'] as $oname => $v) {
                $price[] = $v['price'];
                $quantity+= intval($v['quantity']);
            }
            // 更新价格和库存
            $this->ci->link->where('id', $cid)->update(SITE_ID.'_'.APP_DIR, array(
                'order_price' => min($price),
                'order_quantity' => $quantity,
            ));
        }
	}
	
	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	string	$value	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').'&nbsp;'.$cname.'：';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<div class="onShow" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</div>' : '';
		// 字段默认值
		$value = @strlen($value) ? dr_string2array($value) : ($_POST['data'][$name]);
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '80%';

		// 计算是否包含自定义规格
		$diy_id = 0;
		if (is_array($value) && $value['diy_value']) {
			$dids = array();
			foreach ($value['diy_value'] as $did => $v) {
				$dids[] = (int)str_replace('value', '', $did);
			}
			$diy_id = max($dids);
		}

		// 格式化价格
		$ovalue = array();
		if (is_array($value) && $value['value']) {
			foreach ($value['value'] as $oname => $v) {
				$ovalue[$oname.'_price'][] = $v['price'];
				$ovalue[$oname.'_quantity'][] = intval($v['quantity']);
				$ovalue[$oname.'_sn'][] = $v['sn'];
			}
		}

		$use = isset($value['use']) && $value['use'] ? 1 : 0;
		$catid = defined('MODULE_CATID') && MODULE_CATID ? MODULE_CATID : 0;
		$result = dr_get_spec($name, $catid, $value);
		$open = $use && $result ? 1 : 0;
		
		$str = '
		<div class="radio-list">
		<label class="radio-inline"><input id="dr_box_spec_1" type="radio" onclick="dr_spec_use(1)" name="data['.$name.'][use]" value="1" '.($open ? 'checked=""' : '').'> '.fc_lang('开启').'</label>
		<label class="radio-inline"><input id="dr_box_spec_0" type="radio" onclick="dr_spec_use(0)" name="data['.$name.'][use]" value="0" '.(!$open ? 'checked=""' : '').'> '.fc_lang('关闭').'</label>
		</div>
		<script type="text/javascript" src="'.THEME_PATH.'js/finecms.sku.js"></script>
		<fieldset id="dr_spec_table" class="blue pad-10" style="'.(!$open ? 'display:none;' : '').'background-color: #F8F8F8;border: 1px solid #ECECEC;width:'.$width.(is_numeric($width) ? 'px' : '').'">
			<a class="add" style="margin-left:10px;margin-bottom:15px;" title="'.fc_lang('添加属性分组').'" href="javascript:dr_spec_add_group();"></a>
			<a class="del" style="margin-right:10px;margin-bottom:15px;float:right" title="'.fc_lang('删除所有自定义属性和分组').'" href="javascript:dr_spec_delete();"></a>
			<div class="picList myspec" id="dr_'.$name.'_result">
				'.$result.'
			</div>
			<div style="font-weight: bold;margin-bottom:-10px;clear:both;padding:10px;background: #ffffff;">'.fc_lang('定价').'</div>
			<div class="picList" id="dr_mall_sku" style="clear:both;padding:10px;background: #ffffff;">
				<span style="color:red">'.fc_lang('您需要选择所有的规格属性，才能组合成完整的规格信息。').'</span>
			</div>
		</fieldset>
		<style>
		.color-box {
			width: 12px;
			height: 12px;
			display: inline-block;
			margin: 0 3px;
			position: relative;
			top: 3px;
			border: 1px solid #ddd;
		}
		.myspec {
		    clear:both;
		}
		.myspec .input-text {
			font: 12px/1.5 "Microsoft YaHei","微软雅黑",SimSun,"宋体",Heiti,"黑体",sans-serif;
			color: #004499;
		}
		.Father_Footer {
			padding-bottom: 15px;
			border-top:1px dashed #D7D7D7;
			padding-top:5px;
			margin-bottom:15px;
			margin-top:5px;
		}
		.Father_Title {
			font-weight: bold;
			margin-bottom:10px;
			border-bottom: 1px solid #D7D7D7;
		}
		.sku-style th {
			background-color: #EDEDED;
			border: 1px solid #D7D7D7 !important;
			font-weight: 400;
			height: 25px;
			text-align: center;
			vertical-align: middle;
			padding:0;
		}
		.sku-style td {
			border: 1px solid #D7D7D7 !important;
			padding: 3px 5px;
			text-align: center;
			vertical-align: middle;
			height: 25px;
			min-width: 60px;
		}
		.sku-li {
		    clear:both;
		    padding-top:5px;
		    padding:0 10px;
            background: #ffffff;
		}
		.md-check {
			margin-top:0px !important
		}
		</style>
		<script>
		var diy_id = '.$diy_id.';
		var arrayValue = new Array();　//默认值
		arrayValue = '.json_encode($ovalue).';
		$(function(){
			'.($catid ? '' : 'dr_load_mall_specification();').'
			'.($result && $use ? '$("#dr_row_order_price").hide();
						$("#dr_row_order_quantity").hide();
						$("#dr_row_order_sn").hide();
						dr_select_mall_spec();' : '').'
			$("#dr_catid").change(function(){
				dr_load_mall_specification();
			});
			
		});
		function dr_spec_use(id) {
			if (id == 1) {
				$("#dr_spec_table").show();
				$("#dr_row_order_sn").hide();
				$("#dr_row_order_price").hide();
				$("#dr_row_order_quantity").hide();
				dr_select_mall_spec();
				$("#uniform-dr_box_spec_1 span").addClass("checked"); 
				$("#uniform-dr_box_spec_0 span").removeClass("checked");
			} else {
				$("#dr_spec_table").hide();
				$("#dr_row_order_sn").show();
				$("#dr_row_order_price").show();
				$("#dr_row_order_quantity").show();
				$("#uniform-dr_box_spec_0 span").addClass("checked"); 
				$("#uniform-dr_box_spec_1 span").removeClass("checked"); 
			}
		}
		function dr_spec_delete() {
		    $("#dr_'.$name.'_result").html("<div><img src=\"'.THEME_PATH.'admin/images/loading-mini.gif\" />&nbsp;'.fc_lang('数据加载中').'");
			$.ajax({
				type: "POST",
				dataType: "text",
				url: "/index.php?s=member&mod='.APP_DIR.'&c=api&m=specification&nodiy=1",
				data: {catid:$("#dr_catid").val(), name: "'.$name.'"},
				success: function(data) {
					if (data == "error") {
						dr_spec_use(0);
						$("#dr_'.$name.'_result").html("");
						$("#dr_mall_sku").html("");
					} else {
						$("#dr_'.$name.'_result").html(data);
						$("#dr_mall_sku").html("");
						dr_select_mall_spec();
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) { }
			});
		}
		function dr_load_mall_specification() {
			$("#dr_row_order_sn").hide(); 
			$("#dr_row_order_price").hide();
			$("#dr_row_order_quantity").hide();
			$("#dr_'.$name.'_result").html("<div><img src=\"'.THEME_PATH.'admin/images/loading-mini.gif\" />&nbsp;'.fc_lang('数据加载中').'");
			$.ajax({
				type: "POST",
				dataType: "text",
				url: "/index.php?s=member&mod='.APP_DIR.'&c=api&m=specification",
				data: {catid:$("#dr_catid").val(), value: '.json_encode($value).', name: "'.$name.'"},
				success: function(data) {
					if (data == "error") {
						dr_spec_use(0);
						$("#dr_'.$name.'_result").html("");
					} else {
						$("#dr_'.$name.'_result").html(data);
						dr_select_mall_spec();
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) { }
			});
		}
		function dr_spec_add_group() {
			var i = $(".Father_Title").length;
			var id = "group"+i;
			var cname = id;
			var html = "<div class=\"sku-li\">";
			html+= "<ul class=\"Father_Title\">";
			html+= "<li title=\""+cname+"\"><input onclick=\"dr_spec_select_group(this, \'"+id+"\')\" class=\"toggle md-check\" type=\"checkbox\" value=\"\" checked />&nbsp;<label id=\"dr_spec_edit_name_"+id+"\" style=\"display:none;\"><input onblur=\"dr_spec_save_group(this, \'"+id+"\')\" id=\"dr_spec_add_group_"+cname+"\" class=\"input-text\" name=\"data['.$name.'][diy_group]["+id+"]\" size=\"8\" type=\"text\" value=\""+id+"\"></label><a id=\"dr_spec_show_name_"+id+"\" onclick=\"dr_spec_edit_group(\'"+id+"\')\">"+cname+"</a></li>";
			html+= "</ul>";
			html+= "<ul class=\"Father_Item"+i+"\" id=\"dr_spec_item_"+cname+"\"></ul>";
			html+= "<div style=\"clear:both;\"></div>";
			html+= "<div class=\"Father_Footer\"><input style=\"margin-top:-5px !important\" onclick=\"dr_spec_add_value(this, \'"+cname+"\')\" class=\"toggle md-check\" type=\"checkbox\" value=\"\" />&nbsp;<label><input id=\"dr_spec_add_value_"+cname+"\" placeholder=\"自定义\" class=\"input-text\" size=\"8\" type=\"text\" value=\"\"></label>&nbsp;&nbsp;&nbsp;</div>";
			html+= "</div>";
			$("#dr_'.$name.'_result").append(html);
			dr_select_mall_spec();
		}
		function dr_spec_add_value(obj, cname) {
			$(obj).attr("checked", false);
			diy_id ++;
			var vname = $("#dr_spec_add_value_"+cname).val();
			var value = "value"+diy_id;
			if (!vname) {
				dr_tips("'.fc_lang('名称未填写').'");
				return;
			}
			var html = "<li style=\"float:left;padding:0\"><input style=\"margin-top:-5px !important\" class=\"dr_update_spec_value toggle md-check\" onclick=\"dr_select_mall_spec2(this)\"type=\"checkbox\" name=\"data['.$name.'][option]["+cname+"][]\" value=\""+value+"\" vname=\""+vname+"\" checked />&nbsp;<label><input name=\"data['.$name.'][diy_value]["+value+"]\" class=\"input-text\" size=\"8\" onblur=\"dr_update_spec_value(this)\" type=\"text\" value=\""+vname+"\"></label>&nbsp;&nbsp;&nbsp;</li>";
			$("#dr_spec_item_"+cname).append(html);
			dr_select_mall_spec();
			$("#dr_spec_add_value_"+cname).val("");
		}
		</script>';
		return $this->input_format($name, $text, $str);
	}
	
}