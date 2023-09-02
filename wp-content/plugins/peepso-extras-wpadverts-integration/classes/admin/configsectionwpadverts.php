<?php

class PeepSoConfigSectionWPAdverts extends PeepSoConfigSectionAbstract
{
	// Builds the groups array
	public function register_config_groups()
	{
        $this->context='left';
        $this->general();
        $this->chat();

        $this->context='right';
        $this->stream();
        $this->advanced();
	}

	private function general()
	{
        # Enable User Classifieds Features
        $this->args('default', 1);
        $this->args('descript', __('Classifieds menu will be available on User Profiles.', 'peepso-wpadverts'));
        $this->set_field(
            'wpadverts_user_classifieds_enable',
            __('Enable User Classifieds', 'peepso-wpadverts'),
            'yesno_switch'
        );

        # Disable Template Overrides
        $this->args('default', 1);
        $this->args('descript', __('Use default WPAdverts template.', 'peepso-wpadverts'));
        $this->set_field(
            'wpadverts_overrides_disable',
            __('Disable Template Overrides', 'peepso-wpadverts'),
            'yesno_switch'
        );

		# Enable Moderation when adding new Classifieds
		$this->args('default', 1);
		$this->args('descript', __('Classifieds need to be accepted by Admin before being published', 'peepso-wpadverts'));
		$this->set_field(
			'wpadverts_moderation_enable',
			__('Enable WPAdverts Moderation', 'peepso-wpadverts'),
			'yesno_switch'
		);

        $options = array(
            1 => __('List', 'peepso-wpadverts'),
            2 => __('Grid', 'peepso-wpadverts'),
        );
        $this->args('default', 1);
        $this->args('options', $options);
        $this->set_field(
            'wpadverts_display_ads_as',
            __('Display Ads as', 'peepso-wpadverts'),
            'select'
        );

        # Allow guess access for classified page
        $this->args('default', 1);
        $this->set_field(
            'wpadverts_allow_guest_access_to_classifieds',
            __('Allow guest access to Classifieds', 'peepso-wpadverts'),
            'yesno_switch'
        );
        
		// Build Group
		$this->set_group(
			'general',
			__('General', 'peepso-wpadverts')
		);
	}

	private function stream() {

        # Post To stream
        $this->args('default', 1);
        $this->args('descript', __('Turn on to post classifieds to stream when user create new classifieds', 'peepso-wpadverts'));
        $this->set_field(
            'wpadverts_post_to_stream_enable',
            __('Post to Stream', 'peepso-wpadverts'),
            'yesno_switch'
        );

        // # Default privacy for stream post
        $privacy = PeepSoPrivacy::get_instance();
        $privacy_settings = $privacy->get_access_settings();

        $options = array();

        foreach($privacy_settings as $key => $value) {
            $options[$key] = $value['label'];
        }

        $this->args('default', PeepSo::ACCESS_PUBLIC);
        $this->args('options', $options);
        $this->set_field(
            'wpadverts_post_privacy',
            __('Default post privacy', 'peepso-wpadverts'),
            'select'
        );

        // Build Group
        $this->set_group(
            'general',
            __('Activity Stream Integration', 'peepso-wpadverts')
        );
    }

    private function chat()
    {
        if(!class_exists('PeepSoMessagesPlugin')) {
            $url = '<a href="https://peepso.com/addons?add=263" target="_blank">PeepSo Core: Chat</a>';
            $this->set_field(
                'blogposts_submissions_enable_descript',
                sprintf(__('This feature requires the %s plugin.', 'peepso-wpadverts'), $url),
                'message'
            );
        } else {
            $this->set_field(
                'wpadverts_chat_enable',
                __('Display Chat button', 'peepso-wpadverts'),
                'yesno_switch'
            );
        }
        // Build Group
        $this->set_group(
            'chat',
            __('Chat Integration', 'peepso-wpadverts')
        );
    }

	private function advanced() {

	    $this->set_field(
	        'wpadverts_advanced_warning',
            sprintf(__('This section is for <b>advanced users only</b>.<br/>When modifying the profile slug, please do NOT use any keyword already in use (eg %s).', 'peepso-wpadverts'),'"photos", "videos", "groups"'),
            'message'
        );
        # Main navigation label
        $this->args('descript', __('Leave empty for default value', 'peepso-wpadverts'));
        $this->set_field(
            'wpadverts_navigation_label',
            __('Menu item label (main)', 'peepso-wpadverts'),
            'text'
        );

        # Profile segment label
        $this->args('descript', __('Leave empty for default value', 'peepso-wpadverts'));
        $this->set_field(
            'wpadverts_navigation_profile_label',
            __('Menu item label (profiles)', 'peepso-wpadverts'),
            'text'
        );

        # Profile segment slug
        $this->args('descript', __('Leave empty for default value', 'peepso-wpadverts') .'. Example: /profile/?' . PeepSoUser::get_instance()->get_username() . '/' . PeepSo::get_option('wpadverts_navigation_profile_slug','classifieds',TRUE));
        $this->set_field(
            'wpadverts_navigation_profile_slug',
            __('Profile slug', 'peepso-wpadverts'),
            'text'
        );



        // Build Group
        $this->set_group(
            'Advanced',
            __('Advanced', 'peepso-wpadverts')
        );
    }

}