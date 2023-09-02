<?php

class PeepSoConfigSectionWordFilter extends PeepSoConfigSectionAbstract
{
// Builds the groups array
	public function register_config_groups()
	{
		$this->context='left';
		$this->_wordfilter_general();
	}

	private function _wordfilter_general()
	{
	    if(!function_exists('mb_substr') || !function_exists('mb_strlen')) {
            $this->set_field(
                'wordfilter_mb_warning',
                __('PHP functions mb_substr and mb_strlen are recommended for accurate text processing, especially for languages with accents (French, Spanish, Polish, Vietnamese etc) or using non-latin script (Russian, Chinese, Japanese etc).', 'wordfilter-peepso'),
                'message'
            );
        }
		# Enable WordFilter
		$this->args('default', 1);
		$this->set_field(
			'wordfilter_enable',
			__('Enabled', 'wordfilter-peepso'),
			'yesno_switch'
		);

		// Keywords to remove
		$this->args('validation', array('required', 'custom'));
		$this->args('validation_options',
			array(
			'error_message' => __('Keywords cannot be empty and separated by comma.', 'wordfilter-peepso'),
			'function' => array($this, 'check_keywords')
			)
		);
		$this->args('descript', __('Separate words or phrases with a comma.', 'wordfilter-peepso'));
		$this->set_field(
			'wordfilter_keywords',
			__('Keywords to remove', 'wordfilter-peepso'),
			'textarea'
		);


		// what to filter
		$this->args('default', 1);
		$this->set_field(
			'wordfilter_type_' . PeepSoActivityStream::CPT_POST,
			__('Filter posts', 'wordfilter-peepso'),
			'yesno_switch'
		);

		$this->args('default', 1);
		$this->set_field(
			'wordfilter_type_' . PeepSoActivityStream::CPT_COMMENT,
			__('Filter comments', 'wordfilter-peepso'),
			'yesno_switch'
		);

		if ( class_exists('PeepSoMessagesPlugin') ) {
			$this->args('default', 1);
			$this->set_field(
				'wordfilter_type_' . PeepSoMessagesPlugin::CPT_MESSAGE,
				__('Filter chat messages', 'wordfilter-peepso'),
				'yesno_switch'
			);
		}

		// How to render
		$options = array(
			PeepSoWordFilterPlugin::WORDFILTER_FULL => '••••',
			PeepSoWordFilterPlugin::WORDFILTER_MIDDLE => 'W••d',
		);
		$this->args('options', $options);
		$this->args('default', 'on');
		$this->set_field(
			'wordfilter_how_to_render',
			__('How to render', 'wordfilter-peepso'),
			'select'
		);

		// Filter character
		$options = array(
            '•' => '••••',
			'*' => '****',
			'#' => '####',
		);
		$this->args('options', $options);
		$this->set_field(
			'wordfilter_character',
			__('Filter character', 'wordfilter-peepso'),
			'select'
		);

		$general_config = apply_filters('peepso_wordfilter_general_config', array());

		if(count($general_config) > 0 ) {

			foreach ($general_config as $option) {
				if(isset($option['descript'])) {
					$this->args('descript', $option['descript']);
				}
				if(isset($option['int'])) {
					$this->args('int', $option['int']);
				}
				if(isset($option['default'])) {
					$this->args('default', $option['default']);
				}

				$this->set_field($option['name'], $option['label'], $option['type']);
			}
		}


		// Build Group
		$this->set_group(
			'general',
			__('General', 'wordfilter-peepso')
		);
	}

	/**
	 * Checks if the keywords value is valid.
	 * @param  string $value keywords to filter
	 * @return boolean
	 */
	public function check_keywords($value)
	{
		$keywords = explode(',', $value);
		$ret = TRUE;
		foreach ($keywords as $word) {
			$word = trim($word);
			if(empty($word)) {
				$ret = FALSE;
			}
		}

		return $ret;
	}
}