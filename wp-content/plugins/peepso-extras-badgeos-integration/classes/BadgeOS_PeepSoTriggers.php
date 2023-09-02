<?php

class BadgeOS_PeepSoTriggers
{
    /**
     * Create default album for user after user register
     * @param object PeepSoUser
     */
    public function register_approved($user)
    {
        // Grab the current trigger
        $this_trigger = current_filter();

        $triger = new BadgeOS_PeepSoTriggers();
        $triger->badgeos_peepso_trigger_event($user->get_id(), $this_trigger);
    }

    /**
     * Function called after avatar changed
     * @param user_id
     * @param dest_thumb
     * @param dest_full
     * @param dest_orig
     */
    public static function user_after_change_avatar($user_id, $dest_thumb, $dest_full, $dest_orig)
    {
        if(0 !== $user_id){

            // Grab the current trigger
            $this_trigger = current_filter();

            $triger = new BadgeOS_PeepSoTriggers();
            $triger->badgeos_peepso_trigger_event($user_id, $this_trigger);
        }
    }

    /**
     * Function called after cover changed
     * @param user_id
     * @param dest_file
     */
    public static function user_after_change_cover($user_id, $dest_file)
    {
        if(0 !== $user_id){

            // Grab the current trigger
            $this_trigger = current_filter();

            $triger = new BadgeOS_PeepSoTriggers();
            $triger->badgeos_peepso_trigger_event($user_id, $this_trigger);
        }
    }

    /**
     * @param int The WP post ID
     * @param int The act_id
     */
	public static function activity_after_add_post($post_id, $act_id)
	{
        $post = get_post( $post_id ); 
        $user_ID = $post->post_author;

		// Grab the current trigger
		$this_trigger = current_filter();

        $triger = new BadgeOS_PeepSoTriggers();
		$triger->badgeos_peepso_trigger_event($user_ID, $this_trigger);
	}

    /**
     * @param int The WP post ID
     * @param int The act_id
     */
	public static function activity_after_add_comment($post_id, $act_id)
	{
        $post = get_post( $post_id ); 
        $user_ID = $post->post_author;
        
		// Grab the current trigger
		$this_trigger = current_filter();
		
        $triger = new BadgeOS_PeepSoTriggers();
		$triger->badgeos_peepso_trigger_event($user_ID, $this_trigger);
	}

    /**
     * @param int from user id
     * @param int to user id
     */
    public static function friends_requests_after_add($from_id, $to_id)
    {
        // Grab the current trigger
        $this_trigger = current_filter();
        
        $triger = new BadgeOS_PeepSoTriggers();
        $triger->badgeos_peepso_trigger_event($from_id, $this_trigger);
    }

    /**
     * @param int from user id
     * @param int to user id
     */
	public static function friends_requests_after_accept($from_id, $to_id)
	{
		// Grab the current trigger
		$this_trigger = current_filter();
		
        $triger = new BadgeOS_PeepSoTriggers();
		$triger->badgeos_peepso_trigger_event($from_id, $this_trigger);
		$triger->badgeos_peepso_trigger_event($to_id, $this_trigger);
	}

    /**
     * @param int message_id
     */
    public static function messages_new_conversation($message_id)
    {
        if(0 !== $message_id)
        {
            $msg = get_post($message_id);

            $user_id = $msg->post_author;

            // Grab the current trigger
            $this_trigger = current_filter();
            
            $triger = new BadgeOS_PeepSoTriggers();
            $triger->badgeos_peepso_trigger_event($user_id, $this_trigger);
        }
    }

    /**
     * @param obj PeepSoGroup
     */
    public static function action_group_create($group)
    {
        // Grab the current trigger
        $this_trigger = current_filter();
        
        $triger = new BadgeOS_PeepSoTriggers();
        $triger->badgeos_peepso_trigger_event($group->owner_id, $this_trigger);
    }

    /**
     * @param int group_id
     * @param int user_id
     */
    public static function action_group_user_join($group_id, $user_id)
    {
        // Grab the current trigger
        $this_trigger = current_filter();
        $args['peepso_group_id'] = $group_id;
        
        $triger = new BadgeOS_PeepSoTriggers();
        $triger->badgeos_peepso_trigger_event($user_id, $this_trigger, $args);
    }

    /**
     * Function called after avatar changed
     * @param user_id
     * @param dest_thumb
     * @param dest_full
     * @param dest_orig
     */
    public static function groups_after_change_avatar($group_id, $dest_thumb, $dest_full, $dest_orig)
    {
        if(0 !== $group_id){

            $PeepSoGroup = new PeepSoGroup($group_id);

            $user_id = get_current_user_id();

            // Grab the current trigger
            $this_trigger = current_filter();
            
            $triger = new BadgeOS_PeepSoTriggers();
            $triger->badgeos_peepso_trigger_event($user_id, $this_trigger);
        }
    }

    /**
     * Function called after cover changed
     * @param user_id
     * @param dest_file
     */
    public static function groups_after_change_cover($group_id, $dest_file)
    {
        if(0 !== $group_id){

            $PeepSoGroup = new PeepSoGroup($group_id);

            $user_id = get_current_user_id();

            // Grab the current trigger
            $this_trigger = current_filter();
            
            $triger = new BadgeOS_PeepSoTriggers();
            $triger->badgeos_peepso_trigger_event($user_id, $this_trigger);
        }
    }

    /**
     * Function called after checkout membership
     * @param user_id
     * @param dest_file
     */
    public static function action_pmp_checkout($user_id, $morder)
    {
        if(0 !== $user_id && !empty($morder->membership_id) ){

            // Grab the current trigger
            $this_trigger = current_filter();
            $args['peepso_pmp_level_id'] = $morder->membership_id;
            
            $triger = new BadgeOS_PeepSoTriggers();
            $triger->badgeos_peepso_trigger_event($user_id, $this_trigger, $args);
        }
    }

    /**
     * Handle each of our peepso triggers
     *
     * @since 1.0.0
     */
    function badgeos_peepso_trigger_event( $user_ID, $this_trigger, $args = array() ) {
        global $wpdb;

        $blog_id = get_current_blog_id();

        $user_data = get_user_by( 'id', $user_ID );

        // check if badgeos integration enable
        if( !PeepSo::get_option('badgeos_integration_enable', 1) ) {
            return ;
        }

        // Sanity check, if we don't have a user object, bail here
        if ( ! is_object( $user_data ) ) {
            return ;
        }

        // Update hook count for this user
        $new_count = badgeos_update_user_trigger_count( $user_ID, $this_trigger, $blog_id );

        // Mark the count in the log entry
        badgeos_post_log_entry( null, $user_ID, null, sprintf( __( '%1$s triggered %2$s (%3$dx)', 'badgeos-peepso' ), $user_data->user_login, $this_trigger, $new_count ) );

        // Now determine if any badges are earned based on this trigger event
        $triggered_achievements = $wpdb->get_results( $wpdb->prepare(
            "
            SELECT post_id
            FROM   $wpdb->postmeta
            WHERE  meta_key = '_badgeos_peepso_trigger'
                   AND meta_value = %s
            ",
            $this_trigger
        ) );
        foreach ( $triggered_achievements as $achievement ) {
            # Since we are triggering multiple times based on group joining, we need to check if we're on the peepso_action_group_user_join filter.
            if ( 'peepso_action_group_user_join' == current_filter() ) {
                # We only want to trigger this when we're checking for the appropriate triggered group ID.
                $group_id = get_post_meta( $achievement->post_id, '_badgeos_peepso_group_id', true );
                if ( $group_id == $args['peepso_group_id'] ) {
		            badgeos_maybe_award_achievement_to_user( $achievement->post_id, $user_ID, $this_trigger, $blog_id, $args );
		        }
            # Since we are triggering multiple times based on membership checkkout, we need to check if we're on the peepso_action_pmp_checkout filter.
            } elseif( 'peepso_action_pmp_checkout' == current_filter() ) {
                # We only want to trigger this when we're checking for the appropriate triggered group ID.
                $pmp_level_id = get_post_meta( $achievement->post_id, '_badgeos_peepso_pmp_level_id', true );
                if ( $pmp_level_id == $args['peepso_pmp_level_id'] ) {
                    badgeos_maybe_award_achievement_to_user( $achievement->post_id, $user_ID, $this_trigger, $blog_id, $args );
                }
            } else {
                badgeos_maybe_award_achievement_to_user( $achievement->post_id, $user_ID, $this_trigger, $blog_id, $args );
    		}
        }
    }
}