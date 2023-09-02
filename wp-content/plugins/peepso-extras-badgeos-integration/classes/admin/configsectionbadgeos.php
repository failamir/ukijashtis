<?php

class PeepSoConfigSectionBadgeOS extends PeepSoConfigSectionAbstract
{
// Builds the groups array
	public function register_config_groups()
	{
		$this->context='left';
		$this->_badgeos_general();
	}

	private function _badgeos_general()
	{

		# General description
		$this->set_field(
			'badgeos_general_description',
			__('Configure the use of PeepSo - BadgeOS Integration','badgeos-peepso'),
			'message'
		);

		# Enable BadgeOS Integration
		$this->args('default', 1);
		$this->args('descript', __('Turn on the use of BadgeOS Integration', 'badgeos-peepso'));
		$this->set_field(
			'badgeos_integration_enable',
			__('Enable BadgeOS Integration', 'badgeos-peepso'),
			'yesno_switch'
		);

		# Show Group Creation date on Groups listing
		$this->args('default', 1);
		$this->args('descript', __('Turn on to show \'Badges\' link in the PeepSo Profile menu', 'badgeos-peepso'));
		$this->set_field(
			'badgeos_show_link_in_profile_menu',
			__('Show \'Badges\' link in the PeepSo Profile menu','badgeos-peepso'),
			'yesno_switch'
		);

		# Show Group Creation date on Groups listing
		$this->args('default', 1);
		#$this->args('descript', __('Create posts when user earns a new badge', 'badgeos-peepso'));
		$this->set_field(
			'badgeos_create_posts_when_earns_new_badge',
			__('Create posts when user earns a new badge','badgeos-peepso'),
			'yesno_switch'
		);	

if(version_compare( PeepSo::PLUGIN_VERSION, '1.7.0', '>' )) {
		$this->args('default', 10);
		#$this->args('descript', __('Create posts when user earns a new badge', 'badgeos-peepso'));
		$this->set_field(
			'badgeos_display_recent_badges_on_cover',
			__('Display recent badges on profile cover','badgeos-peepso'),
			'yesno_switch'
		);

		$options_limit = array();
		for ($i = 1; $i <= 10; $i++) {
			$options_limit[$i] = $i;
		}

		$args = array(
			'descript' => "",
			'field_wrapper_class' => 'controls col-sm-8',
			'field_label_class' => 'control-label col-sm-4',
			'options' => $options_limit,
		);
		$this->args = $args;
		$this->set_field(
			'badgeos_limit_recent_badges_on_cover',
			__('How many recent badges to display on the cover', 'badgeos-peepso'),
			'select'
		);
}


if(version_compare(PeepSo::PLUGIN_VERSION, '1.7.1', '>')) {
		$this->args('default', 10);
		#$this->args('descript', __('Create posts when user earns a new badge', 'badgeos-peepso'));
		$this->set_field(
			'badgeos_display_recent_badges_on_profile_widget',
			__('Display recent badges on profile widget','badgeos-peepso'),
			'yesno_switch'
		);

		$options_limit = array();
		for ($i = 1; $i <= 10; $i++) {
			$options_limit[$i] = $i;
		}

		$args = array(
			'descript' => "",
			'field_wrapper_class' => 'controls col-sm-8',
			'field_label_class' => 'control-label col-sm-4',
			'options' => $options_limit,
		);
		$this->args = $args;
		$this->set_field(
			'badgeos_limit_recent_badges_on_profile_widget',
			__('How many recent badges to display on the profile widget', 'badgeos-peepso'),
			'select'
		);
}
		$general_config = apply_filters('peepso_badgeos_general_config', array());

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
			__('General', 'badgeos-peepso')
		);
	}

}