<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class BadgeOSPeepSoInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		'badgeos_integration_enable' => 1,
		'badgeos_show_link_in_profile_widget' => 1,
		'badgeos_show_link_in_profile_menu' => 1,
		'badgeos_create_posts_when_earns_new_badge' => 1,
		'badgeos_display_recent_badges_on_cover' => 1,
		'badgeos_limit_recent_badges_on_cover' => 10,
		'badgeos_display_recent_badges_on_profile_widget' => 1,
		'badgeos_limit_recent_badges_on_profile_widget' => 10,
	);

	public function plugin_activation( $is_core = FALSE )
	{
		//Add default BuddPress settings to each achievement type that may already exist.
        $args=array(
            'post_type' => 'achievement-type',
            'post_status' => 'publish',
            'posts_per_page' => -1
        );
        $query = new WP_Query($args);
        if( $query->have_posts() ) {
            while ($query->have_posts()) : $query->the_post();
                update_post_meta( get_the_ID(), '_badgeos_create_bp_activty', 'on' );
                update_post_meta( get_the_ID(), '_badgeos_show_bp_member_menu', 'on' );
            endwhile;
        }


		parent::plugin_activation($is_core);

		return (TRUE);
	}

/*	// optional DB table creation
	public static function get_table_data()
	{
		$aRet = array(
			'hello' => "
				CREATE TABLE `helloworld` (
					`hlw_id`				BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,

					PRIMARY KEY (`hello_id`),
				) ENGINE=InnoDB",
		);

		 return $aRet;
	}

	// optional notification emails
	public function get_email_contents()
	{
		$emails = array(
			'email_helloworld' => "Hello World!",
		);

		 return $emails;
	}

	// optional page with shortcode
	protected function get_page_data()
	{
		// default page names/locations
		$aRet = array(
			'hello' => array(
				'title' => __('PeepSo Hello World', 'peepso-hello-world'),
				'slug' => 'helloworld',
				'content' => '[peepso_hello]'
			),
		);

		return ($aRet);
	}*/
}