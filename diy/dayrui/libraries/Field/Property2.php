<?php

/**
 * Dayrui Website Management System
 *
 * @since		version 2.6.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class F_Property2 extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('商城_商品属性'); // 字段名称
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
		$this->ci->data[$field['ismain']][$field['fieldname']] = dr_array2string($value);
	}
	
	/**
	 * 字段入库后执行
	 */
	public function insert_last_value($cid, $value, $field) {

		$table = SITE_ID.'_'.APP_DIR.'_property_search';

		// 删除原数据
		$this->ci->link->where('cid', $cid)->delete($table);
		$value = dr_string2array($value);

		if ($value) {
			$catid = $this->ci->data[1]['catid'];
			$this->ci->load->model('property_model');
			$property = $this->ci->property_model->get_cat_data_insert($catid);
			// 添加数据（只添加可搜索的值）
			foreach ($value as $name => $t) {
				if (isset($property[$name]) && $property[$name] && (@strlen($t) || is_array($t))) {
					$pid = intval($property[$name]);
					if (is_array($t)) {
						foreach ($t as $v) {
							strlen($v) && $this->ci->link->insert($table, array(
								'cid' => $cid,
								'pid' => $pid,
								'value' => $v,
							));
						}
					} else {
						$this->ci->link->insert($table, array(
							'cid' => $cid,
							'pid' => $pid,
							'value' => $t,
						));
					}
					
				}
				
			}
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
		$value = @strlen($value) ? dr_string2array($value) : $_POST['data'][$name];
		$catid = defined('MODULE_CATID') && MODULE_CATID ? MODULE_CATID : 0;
		$str = '
		<fieldset class="blue pad-10" style="background-color: rgb(248, 248, 248); border: 1px solid rgb(236, 236, 236); width: 80%;">
			<div class="picList" id="dr_'.$name.'_result">
				'.dr_get_property($name, $catid, $value).'
			</div>
		</fieldset>
		<script>
		$(function(){
			dr_load_mall_property();
			$("#dr_catid").change(function(){
				dr_load_mall_property();
			});
		});
		function dr_load_mall_property() {
			$("#dr_'.$name.'_result").html("<div><img src=\"'.THEME_PATH.'admin/images/loading-mini.gif\" />&nbsp;'.fc_lang('数据加载中').'");
			$.ajax({
				type: "POST",
				dataType: "text",
				url: "/index.php?s=member&mod='.APP_DIR.'&c=api&m=property",
				data: {catid:$("#dr_catid").val(), value: '.json_encode($value).', name: "'.$name.'"},
				success: function(data) {
					if (data == "error") {
						$("#dr_'.$name.'_result").html("");
						$("#dr_row_'.$name.'").hide();
					} else {
						$("#dr_'.$name.'_result").html(data);
						$("#dr_row_'.$name.'").show();
					}
				},
				error: function(HttpRequest, ajaxOptions, thrownError) { }
			});
		}
		</script>';
		return $this->input_format($name, $text, $str);
	}
	
}