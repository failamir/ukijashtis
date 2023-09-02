<?php

class PeepSoConfigSectionGiphy extends PeepSoConfigSectionAbstract
{
// Builds the groups array
	public function register_config_groups()
	{
		$this->context='left';
		$this->_giphy_general();
		$this->context='right';
		$this->_giphy_info();
	}

	private function _giphy_general()
	{
		// API Key
		$this->set_field(
			'giphy_api_key',
			__('Giphy API Key', 'peepso-giphy'),
			'text'
		);

        // Limit
        $options = array();
        for ($i = 5; $i <= 100; $i+=5) {
            $options[$i] = $i;
        }

        $this->args('options', $options);
        $this->set_field(
            'giphy_display_limit',
            __('Limit', 'peepso-giphy'),
            'select'
        );

        // Limit
        $options = array(
            'y'     => 'Y       - '.__('Illustrated content only', 'peepso-giphy'),
            'g'     => 'G       - '.__('Suitable for everyone', 'peepso-giphy'),
            'pg'    => 'PG      - '.__('May be inappropriate for children', 'peepso-giphy'),
            'pg-13' => 'PG-13   - '.__('May be inappropriate under the age of 13', 'peepso-giphy'),
            'r'     => 'R       - '.__('Inappropriate under the age of 17', 'peepso-giphy'),
            ''      => __('No limit','peepso-giphy'),
        );

        $this->args('options', $options);

        $this->set_field(
            'giphy_rating',
            __('Max content rating', 'peepso-giphy'),
            'select'
        );


        // size
        $options = array(
            'fixed_width' => 'fixed_width - '.__('Width set to 200px. Good for mobile use.', 'peepso-giphy'),
            'fixed_width_still'     => 'fixed_width_still - '.__('Static preview image for fixed_width', 'peepso-giphy'),
            'fixed_width_downsampled'     => 'fixed_width_downsampled - '.__('Width set to 200px. Reduced to 6 frames. Works well for unlimited scroll on mobile and as animated previews.', 'peepso-giphy'),
            'fixed_height'     => 'fixed_height - '.__('Height set to 200px. Good for mobile use.', 'peepso-giphy'),
            'fixed_height_still'     => 'fixed_height_still - '.__('Static preview image for fixed_height', 'peepso-giphy'),
            'fixed_height_downsampled'    => 'fixed_height_downsampled - '.__('Height set to 200px. Reduced to 6 frames to minimize file size to the lowest. Works well for unlimited scroll on mobile and as animated previews. See GIPHY.com on mobile web as an example.', 'peepso-giphy'),
            'fixed_width_small'     => 'fixed_width_small - '.__('Width set to 100px. Good for mobile keyboards', 'peepso-giphy'),
            'fixed_width_small_still'     => 'fixed_width_small_still - '.__('Static preview image for fixed_width_small', 'peepso-giphy'),
            'fixed_height_small'     => 'fixed_height_small - '.__('Height set to 100px. Good for mobile keyboards.', 'peepso-giphy'),
            'fixed_height_small_still'     => 'fixed_height_small_still - '.__('Static preview image for fixed_height_small', 'peepso-giphy'),
            'preview'     => 'preview - '.__('File size under 50kb. Duration may be truncated to meet file size requirements. Good for thumbnails and previews.', 'peepso-giphy'),
            'original'     => 'original - '.__('Original file size and file dimensions. Good for desktop use.', 'peepso-giphy'),
            'original_still'     => 'original_still - '.__('Preview image for original', 'peepso-giphy'),
        );

        $this->args('options', $options);

        $this->set_field(
            'giphy_rendition_comments',
            __('Giphy Rendition on Comments', 'peepso-giphy'),
            'select'
        );

        $this->args('options', $options);

        $this->set_field(
            'giphy_rendition_messages',
            __('Giphy Rendition on Messages', 'peepso-giphy'),
            'select'
        );



		$general_config = apply_filters('peepso_giphy_integration_general_config', array());

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
			__('General', 'peepso-giphy')
		);
	}

	private function _giphy_info()
	{
		# General description
		$this->set_field(
			'giphy_info_description',
			__('This plugin integrates world-famous <a href="https://giphy.com/" target="_blank">Giphy</a>. It is thanks to their service your community now has the possibility to share all of their amazing gifs in both comments and chat.','peepso-giphy'),
			'message'
		);

		$this->set_field(
			'giphy_info_logo',
			'<a href="https://giphy.com/" target="_blank"><img src="'.plugin_dir_url(__FILE__).'../../assets/images/powered-by.png" /></a>',
			'message'
		);

		$this->set_group(
			'info',
			__('Information', 'peepso-giphy')
		);
	}
}
