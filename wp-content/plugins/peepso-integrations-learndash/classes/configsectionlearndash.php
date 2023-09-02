<?php

class PeepSoConfigSectionLearnDash extends PeepSoConfigSectionAbstract
{
    // Builds the groups array
    public function register_config_groups()
    {
        $this->context='left';
        $this->profiles();
        $this->profiles_two_column();
        $this->profiles_text();
        $this->profiles_featured_images();

        $this->context='right';
        $this->activity_enroll();
        $this->activity_complete();
        $this->advanced();

    }

    private function profiles() {

        // Enable Profiles Tab
        $this->args('descript', __('Show "LearnDash" tab in user profiles.','peepsolearndash'));
        $this->set_field(
            'ld_profile_enable',
            __('Enabled', 'peepsolearndash'),
            'yesno_switch'
        );

//        $this->args('descript', __('The profile tab will not appear if the user has no enrolled courses','peepsolearndash'));
//        $this->set_field(
//            'ld_profile_hideempty',
//            __('Hide when empty', 'peepsolearndash'),
//            'yesno_switch'
//        );

        // Tag
        $this->args('descript', __('Optional. Comma separated list of course IDs you want to hide from the profiles.','peepsolearndash'));
        $this->set_field(
            'ld_profile_hide_courses',
            __('Hide courses', 'peepsolearndash'),
            'text'
        );

//        // OrderBy
//        $options = array(
//            'title'         => __('Title','peepsolearndash'),
//            'date'          => __('Date created', 'peepsolearndash'),
//            'menu_order'    => __('Menu Order', 'peepsolearndash'),
//        );
//
//        $this->args('default', 'title');
//        $this->args('options', $options);
//        $this->set_field(
//            'ld_profile_orderby',
//            __('Order By', 'peepsolearndash'),
//            'select'
//        );
//
//        // Order
//        $options = array(
//            'ASC' => __('Ascending','peepsolearndash'),
//            'DESC' => __('Descending','peepsolearndash'),
//        );
//
//        $this->args('default', 2);
//        $this->args('options', $options);
//        $this->set_field(
//            'ld_profile_order',
//            __('Sort Order', 'peepsolearndash'),
//            'select'
//        );

        // Set group
        $this->set_group(
            'ld_profiles',
            __('Profiles', 'peepsolearndash')
        );
    }

    private function profiles_two_column()
    {
        $this->set_field(
            'ld_profile_two_column_enable',
            __('Enabled', 'peepsolearndash'),
            'yesno_switch'
        );

        $this->args('int', TRUE);
        $this->args('default', 350);

        $this->set_field(
            'ld_profile_two_column_height',
            __('Box height (px)', 'peepsolearndash'),
            'text'
        );


        $this->set_group(
            'ld_two_column',
            __('Two column layout', 'peepsolearndash')
        );

    }

    /**
     * Text Parsing
     */
    private function profiles_text()
    {
        $this->args('default', 1);
        $this->set_field(
            'ld_profile_titles',
            __('Course titles', 'peepsolearndash'),
            'yesno_switch'
        );

        $this->args('int', TRUE);
        $this->args('default', 50);
        $this->args('descript', __('0 for no content', 'peepsolearndash'));
        $this->set_field(
            'ld_profile_content_length',
            __('Description length (words)', 'peepsolearndash'),
            'text'
        );

        $this->args('descript', __('Forced removal of some shortcodes immune to native WP methods (eg Divi Builder and similar). This is an experimental feature, we recommend using plain-text excerpts instead.' ,'peepsolearndash'));
        $this->set_field(
            'ld_profile_content_force_strip_shortcodes',
            __('Aggressive shortcode removal', 'peepsolearndash'),
            'yesno_switch'
        );

        $this->set_group(
            'ld_text',
            __('Text', 'peepsolearndash')
        );
    }

    private function profiles_featured_images()
    {
        $this->set_field(
            'ld_profile_featured_image_enable',
            __('Show featured images', 'peepsolearndash'),
            'yesno_switch'
        );

        $this->args('descript', __('Display an empty box if an image is not found (to maintain the layout)', 'peepsolearndash'));
        $this->set_field(
            'ld_profile_featured_image_enable_if_empty',
            __('Placeholder', 'peepsolearndash'),
            'yesno_switch'
        );

        $options = array(
            'top'   => __('Top (rectangle)', 'peepsolearndash'),
            'left'  => __('Left (square)', 'peepsolearndash'),
            'right' => __('Right (square)', 'peepsolearndash'),
        );

        $this->args('options', $options);

        $this->set_field(
            'ld_profile_featured_image_position',
            __('Position', 'peepsolearndash'),
            'select'
        );

        $this->args('int', TRUE);
        $this->args('default', 150);

        // Once again the args will be included automatically. Note that args set before previous field are gone
        $this->set_field(
            'ld_profile_featured_image_height',
            __('Height (px)', 'peepsolearndash'),
            'text'
        );


        $this->set_group(
            'ld_featured_image',
            __('Featured Images', 'peepsolearndash')
        );

    }

    private function activity_enroll() {

        // Enable Profiles Tab
        $this->args('descript', __('Creates an activity entry when user enrolls in a course.','peepsolearndash'));
        $this->set_field(
            'ld_activity_enroll',
            __('Post to activity', 'peepsolearndash'),
            'yesno_switch'
        );


        // Enrolled Header Text
        $this->args('descript', __('Leave empty for default','peepsolearndash'));
        $this->set_field(
            'ld_activity_enroll_action_text',
            __('Action text', 'peepsolearndash'),
            'text'
        );

        // Privacy
        $privacy = PeepSoPrivacy::get_instance();
        $privacy_settings = $privacy->get_access_settings();

        $options = array();

        foreach($privacy_settings as $key => $value) {
            if(in_array($key, array(30,40))) { continue; }
            $options[$key] = $value['label'];
        }

        $this->args('options', $options);

        $this->set_field(
            'ld_activity_enroll_privacy',
            __('Default privacy', 'peepsolearndash'),
            'select'
        );

        // Set group
        $this->set_group(
            'ld_activity_enroll_group',
            __('Action - user enrolled in a course', 'peepsolearndash')
        );
    }

    private function activity_complete() {

        // Enable Profiles Tab
        $this->args('descript', __('Creates an activity entry when user completes a course.','peepsolearndash'));
        $this->set_field(
            'ld_activity_complete',
            __('Post to activity', 'peepsolearndash'),
            'yesno_switch'
        );


        // Action text
        $this->args('descript', __('Leave empty for default','peepsolearndash'));
        $this->set_field(
            'ld_activity_complete_action_text',
            __('Action text', 'peepsolearndash'),
            'text'
        );

        // Privacy
        $privacy = PeepSoPrivacy::get_instance();
        $privacy_settings = $privacy->get_access_settings();

        $options = array();

        foreach($privacy_settings as $key => $value) {
            if(in_array($key, array(30,40))) { continue; }
            $options[$key] = $value['label'];
        }

        $this->args('options', $options);

        $this->set_field(
            'ld_activity_complete_privacy',
            __('Default privacy', 'peepsolearndash'),
            'select'
        );

        // Set group
        $this->set_group(
            'ld_activity_complete_group',
            __('Action - user completed a course', 'peepsolearndash')
        );
    }

    private function advanced() {

        // Profile segment label
        $this->args('descript', __('Leave empty for default value', 'peepsolearndash'));
        $this->set_field(
            'ld_navigation_profile_label',
            __('Profile label', 'peepsolearndash'),
            'text'
        );

        // Profile segment slug
        $this->args('descript', __('Leave empty for default value', 'peepsolearndash') . '. Example: /profile/?' . PeepSoUser::get_instance()->get_username() . '/' . PeepSo::get_option('ld_navigation_profile_slug', 'courses', TRUE));
        $this->set_field(
            'ld_navigation_profile_slug',
            __('Profile slug', 'peepsolearndash'),
            'text'
        );

        // Profile segment icon
        $this->args('descript', __('FontAwesome (or similar). Leave empty for default value', 'peepsolearndash'));
        $this->set_field(
            'ld_navigation_profile_icon',
            __('Custom icon CSS class', 'peepsolearndash'),
            'text'
        );

        // Set group
        $this->set_group(
            'ld_advanced',
            __('Advanced', 'peepsolearndash'),
            sprintf(__('This section is for <b>advanced users only</b>.<br/>When modifying the profile slug, please do NOT use any keyword already in use (eg %s).', 'peepso-wpadverts'), '"photos", "videos", "groups"')
        );
    }

}