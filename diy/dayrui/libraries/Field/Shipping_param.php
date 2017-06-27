<?php

/**
 * Dayrui Website Management System
 *
 * @since		version 2.6.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class F_Shipping_param extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('商城_运输参数'); // 字段名称
		$this->fieldtype = array('TEXT' => ''); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
		return '';
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
		return dr_array2string($value);
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
		$value = @strlen($value) ? dr_string2array($value) : '';
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : '';
		$str = '<label>'.fc_lang('体积').'：</label><label><input size=5 class="form-control" type="text" name="data['.$name.'][m3]" id="dr_'.$name.'_m3" value="'.(isset($value['m3']) ? $value['m3'] : '').'" '.$disabled.' '.$attr.' /></label><label>&nbsp;'.fc_lang('立方米').'&nbsp;&nbsp;&nbsp;&nbsp;</label>';
		$str.= '<label>'.fc_lang('重量').'：</label><label><input size=5 class="form-control" type="text" name="data['.$name.'][kg]" id="dr_'.$name.'_kg" value="'.(isset($value['kg']) ? $value['kg'] : '').'" '.$disabled.' '.$attr.' /></label><label>&nbsp;'.fc_lang('千克').'</label>';
		$str.= $tips;
		return $this->input_format($name, $text, $str);
	}
	
}