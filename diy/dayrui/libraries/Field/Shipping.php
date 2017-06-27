<?php

/**
 * Dayrui Website Management System
 *
 * @since		version 2.6.0
 * @author		Dayrui <dayrui@gmail.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

class F_Shipping extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = fc_lang('商城_运费策略'); // 字段名称
		$this->fieldtype = array('INT' => '10'); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'INT'; // 当用户没有选择字段类型时的缺省值
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
		return intval($value);
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		
		$value = $this->ci->post[$field['fieldname']];
		$this->ci->data[$field['ismain']][$field['fieldname']] = intval($value);
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
		$value = intval($value);
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : '';
		$str = '<label><select class="form-control" '.$disabled.' name="data['.$name.']" id="dr_'.$name.'" '.$attr.' >';
		$str.= '<option value="0" '.(!$value ? ' selected' : '').'>'.fc_lang('包邮').'</option>';
		$options = $this->ci->link->where('uid', $this->ci->uid)->get(SITE_ID.'_'.APP_DIR.'_shipping')->result_array();
		if ($options) {
			foreach ($options as $t) {
				$str.= '<option value="'.$t['id'].'" '.($t['id']==$value ? ' selected' : '').'>'.$t['name'].'</option>';
			}
		}
		$str.= '</select></label>&nbsp;&nbsp;<label><a href="'.dr_member_url(APP_DIR.'/shipping/index').'" style="color:blue" target="_blank"> '.fc_lang('[新建运费模板]').'</a></label>';
		return $this->input_format($name, $text, $str);
	}
	
}