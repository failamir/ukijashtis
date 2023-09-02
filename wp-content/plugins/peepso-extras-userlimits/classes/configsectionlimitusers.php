<?php

class PeepSoConfigSectionLimitUsers extends PeepSoConfigSectionAbstract
{
	public $all_roles;

// Builds the groups array
	public function register_config_groups()
	{
        if(isset($_GET['limitusers_tutorial_reset'])) {
            delete_user_meta(get_current_user_id(), 'peepso_limitusers_admin_tutorial_hide');
            PeepSo::redirect(admin_url().'admin.php?page=peepso_config&tab=limitusers');
        }

        if(isset($_GET['limitusers_tutorial_hide'])) {
            add_user_meta(get_current_user_id(), 'peepso_limitusers_admin_tutorial_hide', 1, TRUE);
            PeepSo::redirect(admin_url().'admin.php?page=peepso_config&tab=limitusers');
        }

        // display the admin tutorial unless this user has already hidden it
        if(1 != get_user_meta(get_current_user_id(), 'peepso_limitusers_admin_tutorial_hide', TRUE)) {
            ob_start();
            PeepSoTemplate::exec_template('limitusers', 'admin_tutorial');

            $peepso_admin = PeepSoAdmin::get_instance();
            $peepso_admin->add_notice(ob_get_clean(), '');
        }


		global $wp_roles;
		$this->all_roles = $wp_roles->roles;

		// LEFT
        $this->context = 'left';
        
        $this->domain_blacklist();

        // hide
        $this->box(
            'hide',
            __('Hide Users From Listings', 'peepsolimitusers'),
            __('Selected users will not show in any PeepSo user listing.<br/>Administrators will always see everyone.', 'peepsolimitusers')
        );


        // posts
        $this->box(
            'posts',
            __('Disable new posts', 'peepsolimitusers'),
            __('Selected users will not be able to create any new posts.<br/>That includes posting in groups.', 'peepsolimitusers')
        );

        // repost
        if (PeepSo::get_option('site_repost_enable', TRUE)) {
            $this->box(
                'repost',
                __('Disable RePost', 'peepsolimitusers'),
                __('Selected users will not be able to RePost (share) any posts.', 'peepsolimitusers')
            );
        }

        // posts
        $this->box(
            'comments',
            __('Disable comments', 'peepsolimitusers'),
            __('Selected users will not be able to add any comments.<br/>That includes commenting in groups.', 'peepsolimitusers')
        );

		// RIGHT
		$this->context = 'right';


        // friends
        if(class_exists('PeepSoFriends')) {
            $this->box(
                'friends',
                __('Friends - disable sending friend requests', 'peepsolimitusers'),
                __('Selected users will not be able to send friend requests.<br/>They can receive and accept friend requests.', 'peepsolimitusers')
            );
        }

        // groups
        if(class_exists('PeepSoGroups')) {
            $this->box(
                'groups',
                __('Groups - disable group join', 'peepsolimitusers'),
                __('Selected Users will be unable to join groups or be invited .<br/>They can still access / manage groups created earlier.', 'peepsolimitusers')
            );
        }

        // groups_create
        if(class_exists('PeepSoGroups')) {
            $this->box(
                'groups_create',
                __('Groups - disable group creation', 'peepsolimitusers'),
                __('Selected Users will be unable to create groups.<br/>They can still manage groups created earlier.', 'peepsolimitusers')
            );
        }

        // messages
        if(class_exists('PeepSoMessagesPlugin')) {
            $this->box(
                'messages',
                __('Chat - disable starting new threads', 'peepsolimitusers'),
                __('Selected users will not be able to create new message threads.<br/>They can still participate in existing threads.', 'peepsolimitusers')
            );
        }

        // photos
        if(class_exists('PeepSoSharePhotos')) {
            $this->box(
                'photos',
                __('Photos - disable photo uploads', 'peepsolimitusers'),
                __('Selected users will not be able to upload photos in posts and comments and create albums.<br/>They can change their avatar and cover.', 'peepsolimitusers')
            );
        }

        // polls
        if(class_exists('PeepSoPolls')) {
            $this->box(
                'polls',
                __('Polls - disable new polls', 'peepsolimitusers'),
                __('Selected users will not be able to create new polls.<br/>They can vote.', 'peepsolimitusers')
            );
        }

        // videos
        if(class_exists('PeepSoVideos')) {
            $this->box(
                'videos',
                __('Videos - disable videos', 'peepsolimitusers'),
                __('Selected users will not be able to embed / upload videos in their posts.<br/>They can post video links.', 'peepsolimitusers')

            );
        }
	}

	private function domain_blacklist() {

        // # Blacklist Domain
        $this->args('descript', __('When enabled, the e-mails on the Black list will be marked as invalid. E-mail validation is checked before domain validation.
        ', 'peepsolimitusers'));
        $this->set_field(
            'limitusers_blacklist_domain_enable',
            __('Block selected domains', 'peepsolimitusers'),
            'yesno_switch'
        );

        // # Predefined  Text
        $this->args('raw', TRUE);
        $this->args('multiple', TRUE);
        $this->args('descript', __('One per line.','peepsolimitusers'));
        $this->set_field(
            'limitusers_blacklist_domain',
            __('Blocked domains', 'peepsolimitusers'),
            'textarea'
        );

        // # Whitelist Domain
        $this->args('descript', __('When enabled, domains on the White list will NOT be blocked. E-mails coming from other domains will be rejected.', 'peepsolimitusers'));
        $this->set_field(
            'limitusers_whitelist_domain_enable',
            __('Allow only selected domains', 'peepsolimitusers'),
            'yesno_switch'
        );

        // # Predefined  Text
        $this->args('raw', TRUE);
        $this->args('multiple', TRUE);
        $this->args('descript', __('One per line.','peepsolimitusers'));
        $this->set_field(
            'limitusers_whitelist_domain',
            __('Allowed domains', 'peepsolimitusers'),
            'textarea'
        );

        // Build Group
        $this->set_group(
            'allow_disallow_domain',
            __('Allow / Disallow Registrations From Certain Email Domains', 'peepsolimitusers')
        );
    }

	private function box($section, $title, $description, $roles = TRUE)
	{
		$this->set_field(
			'message_' . $section,
			$description,
			'message'
		);

		if(TRUE == $roles) {
			foreach ($this->all_roles as $key => $role) {

				$type = 'yesno_switch';
				$name = __($role['name']);

				if ('administrator' == $key && 'hide' != $section) {
					$type = 'message';
					$name = __('Administrator can\'t be limited', 'peepsolimitusers');
				}

				$this->set_field(
					'limitusers_' . $section . '_role_' . $key,
					$name,
					$type
				);
			}
		}

		$this->set_field(
			'separator_' . $section . '_completeness',
			__('Based on profile completeness', 'peepsolimitusers'),
			'separator'
		);


		for($i=0;$i<=100;$i+=10)
        {
            $options [$i] = "$i%";
        }

        $options[0] = __('-- no limit --', 'peepsolimitusers');

        $this->args('options', $options);

		$this->set_field(
			'limitusers_' . $section . '_completeness_min',
			__('Require', 'peepsolimitusers'),
            'select'
		);
        // usr_avatar_custom
		$this->set_field(
            'separator_' . $section . '_avatar',
            __('Based on avatar', 'peepsolimitusers'),
            'separator'
        );

        $this->set_field(
            'limitusers_' . $section . '_avatar',
            'Users who did not upload an avatar',
            'yesno_switch'
        );
        
		$this->set_field(
            'separator_' . $section . '_cover',
            __('Based on cover', 'peepsolimitusers'),
            'separator'
        );

        $this->set_field(
            'limitusers_' . $section . '_cover',
            'Users who did not upload a cover',
            'yesno_switch'
        );

		$this->set_group(
			'peepso_limitusers_' . $section,
			__($title, 'peepsolimitusers')
		);
	}
}