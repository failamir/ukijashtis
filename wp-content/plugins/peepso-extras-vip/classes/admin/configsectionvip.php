<?php

class PeepSoConfigSectionVip extends PeepSoConfigSectionAbstract
{
	// Builds the groups array
	public function register_config_groups()
	{
		$this->context='left';
		$this->_vip_general();

		$this->context='right';
		$this->_vip_information();
	}

	private function _vip_information()
	{
        $this->set_field(
            'vipso_guide1',
            __('<b>Managing the icons</b>: please go to  <a href="admin.php?page=peepso-manage&tab=vip-icons"><i>VIP Icons</i></a>. You can  also find it inside <i>PeepSo</i> option in the side menu.','peepso-vip'),
            'message'
        );

        $this->set_field(
            'vipso_guide2',
            __('<b>Assigning icons</b>: edit a particular user and configure their <i>VIP</i> section. Go to <a href="users.php">users</a> or try it on <a href="profile.php">yourself</a>','peepso-vip'),
            'message'
        );

        $this->set_field(
            'vipso_guide3',
            __('<b>Get more icons</b>:  <a href="http://www.flaticon.com/" target="_blank">http://www.flaticon.com/</a>','peepso-vip'),
            'message'
        );

        $this->set_field(
            'vipso_guide4',
            __('<b>Default icons</b>: made by <a href="http://www.flaticon.com/authors/roundicons" title="Roundicons" target="_blank">Roundicons</a> from <a href="http://www.flaticon.com" title="Flaticon" target="_blank">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>','peepso-vip'),
            'message'
        );


		// Build Group
		$this->set_group(
			'_vip_information',
			__('Quick Tips', 'peepso-vip')
		);
	}

	private function _vip_general()
	{
		// How to render
		$options = array(
			PeepSoVIPPlugin::VIP_ICON_BEFORE_FULLNAME => __('Left','peepso-vip'),
			PeepSoVIPPlugin::VIP_ICON_AFTER_FULLNAME => __('Right','peepso-vip')
		);
		$this->args('options', $options);
		$this->args('default', 1);
		$this->set_field(
			'vipso_where_to_display',
			__('Icon position relative to user name', 'peepso-vip'),
			'select'
		);

		$general_config = apply_filters('peepso_vip_general_config', array());

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

		$options = array();

		for($i=0; $i<=100;$i++) {
		    $options[$i]=$i;
        }

        $this->args('options', $options);
		$this->args('default', 10);

        $this->args('descript', __('Defines how many icons show next to user name in stream, widgets etc.','peepso-vip'));
        $this->set_field(
            'vipso_display_how_many',
            __('How many icons to show', 'peepso-core'),
            'select'
        );

        $this->args('default', '0');
        $this->args('descript', __('if user has more icons assigned than the limit, a small indicator with the amount of remaining icons will show. If there is only one remaining icon, it will be simply displayed.', 'peepso-vip'));
        $this->set_field(
            'vipso_display_more_icons_count',
            __('Show "more icons" indicator', 'vidso'),
            'yesno_switch'
        );

        // Build Group
		$this->set_group(
			'general',
			__('General', 'peepso-vip')
		);
	}
}