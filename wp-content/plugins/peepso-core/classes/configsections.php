<?php

class PeepSoConfigSections extends PeepSoConfigSectionAbstract {
    const SITE_ALERTS_SECTION = 'site_alerts_';

    public function register_config_groups() {
        $this->set_context( 'left' );
        $this->activity();
        $this->reporting();
        $this->wordfilter();

        $this->set_context( 'right' );

        // Don't show licenses box on our demo / d3mo site
        if ( ! isset( $_SERVER['HTTP_HOST'] ) || ! in_array( $_SERVER['HTTP_HOST'], [
                'demo.peepso.com',
                'd3mo.peepso.com',
                //'three.peepso.com'
            ] ) ) {
            $this->license();
        }
    }

    private function activity() {
        // # Separator Callout
        $this->set_field(
            'separator_general',
            __( 'General', 'peepso-core' ),
            'separator'
        );



        // # Hide Activity Stream From Guests
        $this->set_field(
            'site_activity_hide_stream_from_guest',
            __( 'Hide the activity stream from guests', 'peepso-core' ),
            'yesno_switch'
        );

        $stream_config = apply_filters( 'peepso_activity_stream_config', array() );

        if ( count( $stream_config ) > 0 ) {

            foreach ( $stream_config as $option ) {
                if ( isset( $option['descript'] ) ) {
                    $this->args( 'descript', $option['descript'] );
                }
                if ( isset( $option['int'] ) ) {
                    $this->args( 'int', $option['int'] );
                }
                if ( isset( $option['default'] ) ) {
                    $this->args( 'default', $option['default'] );
                }

                $this->set_field( $option['name'], $option['label'], $option['type'] );
            }
        }

        // # Separator Post Save
        $this->set_field(
            'separator_post_save',
            __( 'Saved Posts', 'peepso-core' ),
            'separator'
        );

        // # Message Post Save
        $this->set_field(
            'save_post_message',
            'This feature <b>requires the WP REST API</b>. Please make sure your server allows REST (wp-json) connections and that no security software is blocking GET, POST, PUT, DELETE and PATCH calls. <br/><br/>If anything blocks connections to URLs in the <b>/wp-json/peepso/</b> namespace, this and many future PeepSo features will not work.',
            'message'
        );


        // # Enable Post Save
        $this->args( 'descript', __( 'Allows users to save (favorite / bookmark) posts into a private "saved posts" collection.', 'peepso-core' ) );
        $this->set_field(
            'post_save_enable',
            __( 'Enable Saved Posts', 'peepso-core' ),
            'yesno_switch'
        );


        // # Separator Comments
        $this->set_field(
            'separator_comments',
            __( 'Comments', 'peepso-core' ),
            'separator'
        );

        $options = [];
        for($i=0;$i<=20;$i++) {
            $options[$i] = $i;
        }

        $options_loadmore = $options;
        unset($options_loadmore[0]);

        // # Number Of Comments To Display
        $this->args('options',$options);
        $this->set_field(
            'site_activity_comments',
            __( 'Display comments', 'peepso-core' ),
            'select'
        );

        // Show comments in batches
        $this->args('options',$options_loadmore);
        $this->set_field(
            'activity_comments_batch',
            __( 'Load more comments', 'peepso-core' ),
            'select'
        );

        // # Separator Pinned Post Comments
        $this->set_field(
            'separator_pinned_post_comments',
            __( 'Pinned post comments', 'peepso-core' ),
            'separator'
        );

        // # Number Of Comments To Display
        $this->args('options',$options);
        $this->args( 'default', 2 );
        $this->set_field(
            'site_activity_pinned_post_comments',
            __( 'Display comments', 'peepso-core' ),
            'select'
        );

        // Show comments in batches
        $this->args('options',$options_loadmore);
        $this->args( 'default', 5 );
        $this->set_field(
            'activity_comments_pinned_post_batch',
            __( 'Load more comments', 'peepso-core' ),
            'select'
        );

        /* READMORE */

        // # Separator Readmore
        $this->set_field(
            'separator_readmore',
            __( 'Read more', 'peepso-core' ),
            'separator'
        );

        // # Show Read More After N Characters
        $this->args( 'default', 1000 );
        $this->args( 'validation', array( 'numeric' ) );

        $this->set_field(
            'site_activity_readmore',
            __( "Show 'read more' after: [n] characters", 'peepso-core' ),
            'text'
        );


        // # Redirect To Single Post View
        $this->args( 'default', 2000 );
        $this->args( 'validation', array( 'numeric' ) );

        $this->set_field(
            'site_activity_readmore_single',
            __( 'Redirect to single post view when post is longer than: [n] characters', 'peepso-core' ),
            'text'
        );

        // Build Group
        $this->set_group(
            'activity',
            __( 'Activity', 'peepso-core' )
        );
    }

    private function reporting() {

        // # Enable Reporting
        $this->args( 'children', array( 'site_reporting_types', 'reporting_notify' ) );
        $this->set_field(
            'site_reporting_enable',
            __( 'Enabled', 'peepso-core' ),
            'yesno_switch'
        );

        // # Automatically unpublish reported posts after X reports

        $this->options = array();

        for ( $i = 0; $i <= 50; $i ++ ) {
            $options[ $i ] = $i;
        }

        $this->args( 'options', $options );
        $this->args( 'default', 0 );
        $this->args( 'validation', array( 'numeric' ) );
        $this->args( 'descript', __( 'If a post is reported enough times, it will be automatically unpublished. Set to 0 to disable.', 'peepso-core' ) );

        $this->set_field(
            'site_reporting_num_unpublish_post',
            __( "Automatically unpublish posts after: [n] reports", 'peepso-core' ),
            'select'
        );

        // # Predefined  Text
        $this->args( 'raw', true );
        $this->args( 'multiple', true );
        $this->args( 'descript', __( 'One per line.', 'peepso-core' ) );
        $this->set_field(
            'site_reporting_types',
            __( 'Predefined report reasons', 'peepso-core' ),
            'textarea'
        );

        // # E-mail alerts
        $this->args( 'descript', __( 'ON: Administrators and Community Administrators will receive e-mails about new reports' ) );
        $this->set_field(
            'reporting_notify_email',
            __( 'E-mail alerts', 'peepso-core' ),
            'yesno_switch'
        );

        // # E-mail alerts
        $this->args( 'descript', __( 'One per line.', 'peepso-core' ) . ' ' . __( 'Additional e-mails to receive notifications about new reports.' ) );
        $this->set_field(
            'reporting_notify_email_list',
            __( 'Additional recipients', 'peepso-core' ),
            'textarea'
        );


        // Build Group
        $this->set_group(
            'reporting',
            __( 'Reporting', 'peepso-core' )
        );
    }

    private function wordfilter() {
        if(!function_exists('mb_substr') || !function_exists('mb_strlen')) {
            $this->set_field(
                'wordfilter_mb_warning',
                __('PHP functions mb_substr and mb_strlen are recommended for accurate text processing, especially for languages with accents (French, Spanish, Polish, Vietnamese etc) or using non-latin script (Russian, Chinese, Japanese etc).', 'peepso-core'),
                'message'
            );
        }
		# Enable WordFilter
		$this->args('default', 1);
		$this->set_field(
			'wordfilter_enable',
			__('Enabled', 'peepso-core'),
			'yesno_switch'
		);

		// Keywords to remove
		$this->args('validation', array('required', 'custom'));
		$this->args('validation_options',
            [
                [
                    'error_message' => __('Keywords cannot be empty and separated by comma.', 'peepso-core'),
                    'function' => array($this, 'check_keywords')
                ],
            ]
		);
		$this->args('descript', __('Separate words or phrases with a comma.', 'peepso-core'));
		$this->set_field(
			'wordfilter_keywords',
			__('Keywords to remove', 'peepso-core'),
			'textarea'
		);


		// what to filter
		$this->args('default', 1);
		$this->set_field(
			'wordfilter_type_' . PeepSoActivityStream::CPT_POST,
			__('Filter posts', 'peepso-core'),
			'yesno_switch'
		);

		$this->args('default', 1);
		$this->set_field(
			'wordfilter_type_' . PeepSoActivityStream::CPT_COMMENT,
			__('Filter comments', 'peepso-core'),
			'yesno_switch'
		);

		if ( class_exists('PeepSoMessagesPlugin') ) {
			$this->args('default', 1);
			$this->set_field(
				'wordfilter_type_' . PeepSoMessagesPlugin::CPT_MESSAGE,
				__('Filter chat messages', 'peepso-core'),
				'yesno_switch'
			);
		}

		// How to render
		$options = array(
			PeepSoWordFilter::WORDFILTER_FULL => '••••',
			PeepSoWordFilter::WORDFILTER_MIDDLE => 'W••d',
		);
		$this->args('options', $options);
		$this->args('default', 'on');
		$this->set_field(
			'wordfilter_how_to_render',
			__('How to render', 'peepso-core'),
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
			__('Filter character', 'peepso-core'),
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
			'wordfilter',
			__('Word Filter', 'peepso-core')
		);
    }

    private function license() {
        $this->set_field(
            'bundle',
            __( 'Use a PeepSo Bundle license', 'peepso-core' ),
            'yesno_switch'
        );

        $this->set_field(
            'bundle_license',
            __( 'PeepSo Bundle License Key', 'peepso-core' ),
            'text'
        );

        if ( isset( $_GET['peepso_debug'] ) ) {
            PeepSo3_Mayfly::del( 'peepso_config_licenses_bundle' );
        }

        $bundle = PeepSo3_Mayfly::get( 'peepso_config_licenses_bundle' );

        if ( empty( $bundle ) ) {
            $url = PeepSoAdmin::PEEPSO_URL . '/peepsotools-integration-json/peepso_config_licenses_bundle.html';

            // Attempt contact with PeepSo.com without sslverify
            $resp = wp_remote_get( add_query_arg( array(), $url ), array( 'timeout' => 10, 'sslverify' => false ) );

            // In some cases sslverify is needed
            if ( is_wp_error( $resp ) ) {
                $resp = wp_remote_get( add_query_arg( array(), $url ), array( 'timeout' => 10, 'sslverify' => true ) );
            }

            if ( is_wp_error( $resp ) ) {

            } else {
                $bundle = $resp['body'];
                PeepSo3_Mayfly::set( 'peepso_config_licenses_bundle', $bundle, 3600 * 24 );
            }
        }

        $this->set_field(
            'bundle_message',
            $bundle,
            'message'
        );

        // Get all licensed PeepSo products
        $products = apply_filters( 'peepso_license_config', array() );

        if ( count( $products ) ) {

            $new_products = array();
            foreach ( $products as $prod ) {

                $key = $prod['plugin_name'];

                if ( strstr( $prod['plugin_name'], ':' ) ) {
                    $name                = explode( ':', $prod['plugin_name'] );
                    $prod['cat']         = $name[0];
                    $prod['plugin_name'] = $name[1];
                }

                if ( !isset($prod['cat']) || !strlen($prod['cat']) ) {
                    $prod['cat'] = $prod['plugin_name'];
                }

                $new_products[ $key ] = $prod;
            }

            ksort( $new_products );

            // Loop through the list and build fields
            $prev_cat = null;
            foreach ( $new_products as $prod ) {

                if ( isset( $prod['cat'] ) && $prev_cat != $prod['cat'] ) {
                    $this->set_field(
                        'cat_' . $prod['cat'],
                        $prod['cat'],
                        'separator'
                    );

                    $prev_cat = $prod['cat'];
                }
                // label contains some extra HTML for  license checking AJAX to hook into
                $label = $prod['plugin_name'];
                $label .= ' <small style=color:#cccccc>';
                $label .= $prod['plugin_version'] . '</small>';
                $label .= ' <span class="license_status_check" id="' . $prod['plugin_slug'] . '" data-plugin-name="' . $prod['plugin_edd'] . '"><img src="images/loading.gif"></span>';

                $this->set_field(
                    'site_license_' . $prod['plugin_slug'],
                    $label,
                    'text'
                );
            }
        }

        // Build Group
        $this->set_group(
            'license',
            __( 'License Key Configuration', 'peepso-core' ),
            __( 'This is where you configure the license keys for each PeepSo add-on. You can find your license numbers <a target="_blank" href="https://www.peepso.com/my-licenses/">here</a>. Please copy them here and click SAVE at the bottom of this page.', 'peepso-core' )
            . ' ' . sprintf( __( 'We are detecting %s as your install URL. Please make sure your "supported domain" is configured properly.', 'peepso-core' ), str_ireplace( array(
                'http://',
                'https://'
            ), '', home_url() ) )
            . '<br><br><b>'
            . __( 'If some licenses are not validating, please make sure to click the SAVE button.', 'peepso-core' )
            . ' </b><br/>'
            . __( 'If that does not help, please <a target="_blank" href="https://www.peepso.com/contact/">Contact Support</a>.', 'peepso-core' )

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

// EOF
