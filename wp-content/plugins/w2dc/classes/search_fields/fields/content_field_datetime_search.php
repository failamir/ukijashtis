<?php 

class w2dc_content_field_datetime_search extends w2dc_content_field_search {
	public $min_max_value;
	
	public function isParamOfThisField($param) {
		if ($param == 'field_' . $this->content_field->slug . '_min' || $param == 'field_' . $this->content_field->slug . '_max') {
			return true;
		}
	}

	public function renderSearch($search_form_id, $columns = 2, $defaults = array()) {
		wp_enqueue_script('jquery-ui-datepicker');

		if (is_null($this->min_max_value)) {
			if (isset($defaults['field_' . $this->content_field->slug . '_min'])) {
				$val = $defaults['field_' . $this->content_field->slug . '_min'];
				if (!is_numeric($val)) {
					$val = strtotime($val);
				}
				$this->min_max_value['min'] = $val;
			}
			if (isset($defaults['field_' . $this->content_field->slug . '_max'])) {
				$val = $defaults['field_' . $this->content_field->slug . '_max'];
				if (!is_numeric($val)) {
					$val = strtotime($val);
				}
				$this->min_max_value['max'] = $val;
			}
		}
		
		if ($i18n_file = w2dc_getDatePickerLangFile(get_locale())) {
			wp_register_script('datepicker-i18n', $i18n_file, array('jquery-ui-datepicker'));
			wp_enqueue_script('datepicker-i18n');
		}

		w2dc_renderTemplate('search_fields/fields/datetime_input.tpl.php', array('search_field' => $this, 'columns' => $columns, 'dateformat' => w2dc_getDatePickerFormat(), 'search_form_id' => $search_form_id));
	}
	
	public function validateSearch(&$args, $defaults = array(), $include_GET_params = true) {
		$field_index = 'field_' . $this->content_field->slug . '_min';

		if ($include_GET_params)
			$this->min_max_value['min'] = ((w2dc_getValue($_REQUEST, $field_index, false) !== false) ? w2dc_getValue($_REQUEST, $field_index) : w2dc_getValue($defaults, $field_index));
		else
			$this->min_max_value['min'] = w2dc_getValue($defaults, $field_index, false);

		if ($this->min_max_value['min'] !== false && ((is_numeric($this->min_max_value['min']) && $this->min_max_value['min'] > 0) || strtotime($this->min_max_value['min']))) {
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
					'key' => '_content_field_' . $this->content_field->id . '_date',
					'value' => $this->min_max_value['min'],
					'type' => 'numeric',
					'compare' => '>='
			);
		}

		$field_index = 'field_' . $this->content_field->slug . '_max';

		if ($include_GET_params)
			$this->min_max_value['max'] = ((w2dc_getValue($_REQUEST, $field_index, false) !== false) ? w2dc_getValue($_REQUEST, $field_index) : w2dc_getValue($defaults, $field_index));
		else
			$this->min_max_value['max'] = w2dc_getValue($defaults, $field_index, false);

		if ($this->min_max_value['max'] !== false && ((is_numeric($this->min_max_value['max']) && $this->min_max_value['max'] > 0) || strtotime($this->min_max_value['max']))) {
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query'][] = array(
					'key' => '_content_field_' . $this->content_field->id . '_date',
					'value' => $this->min_max_value['max'],
					'type' => 'numeric',
					'compare' => '<='
			);
		}
	}
	
	public function getBaseUrlArgs(&$args) {
		$field_index = 'field_' . $this->content_field->slug . '_min';
		if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index] && is_numeric($_REQUEST[$field_index]))
			$args[$field_index] = $_REQUEST[$field_index];
	
		$field_index = 'field_' . $this->content_field->slug . '_max';
		if (isset($_REQUEST[$field_index]) && $_REQUEST[$field_index] && is_numeric($_REQUEST[$field_index]))
			$args[$field_index] = $_REQUEST[$field_index];
	}
	
	public function getVCParams() {
		return array(
				array(
					'type' => 'datefieldmin',
					'param_name' => 'field_' . $this->content_field->slug . '_min',
					'heading' => __('From ', 'W2DC') . $this->content_field->name,
					'field_id' => $this->content_field->id,
				),
				array(
					'type' => 'datefieldmax',
					'param_name' => 'field_' . $this->content_field->slug . '_max',
					'heading' => __('To ', 'W2DC') . $this->content_field->name,
					'field_id' => $this->content_field->id,
				)
			);
	}
	
	public function resetValue() {
		$this->min_max_value = array('min' => '', 'max' => '');
	}
}

add_action('vc_before_init', 'w2dc_vc_init_datefield');
function w2dc_vc_init_datefield() {
	vc_add_shortcode_param('datefieldmin', 'w2dc_datefieldmin_param');
	vc_add_shortcode_param('datefieldmax', 'w2dc_datefieldmax_param');

	if (!function_exists('w2dc_datefieldmin_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		function w2dc_datefieldmin_param($settings, $value) {
			if (!is_numeric($value))
				$value = strtotime($value);
			return w2dc_renderTemplate('search_fields/fields/datetime_input_vc_min.tpl.php', array('settings' => $settings, 'value' => $value, 'dateformat' => w2dc_getDatePickerFormat()), true);
		}
	}
	if (!function_exists('w2dc_datefieldmax_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		function w2dc_datefieldmax_param($settings, $value) {
			if (!is_numeric($value))
				$value = strtotime($value);
			return w2dc_renderTemplate('search_fields/fields/datetime_input_vc_max.tpl.php', array('settings' => $settings, 'value' => $value, 'dateformat' => w2dc_getDatePickerFormat()), true);
		}
	}
}
?>