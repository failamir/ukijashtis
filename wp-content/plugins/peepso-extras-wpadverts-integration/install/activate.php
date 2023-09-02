<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class WPAdvertsPeepSoInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		'wpadverts_moderation_enable' => 0,
		'wpadverts_post_to_stream_enable' => 0,
	);

	public function plugin_activation( $is_core = FALSE )
	{
		parent::plugin_activation($is_core);
		return (TRUE);
	}

	/*
	 * return default page names information
	 */
	protected function get_page_data()
	{
		// default page names/locations
		$aRet = array(
			'wpadverts' => array(
				'title' => __('Classifieds', 'peepso-wpadverts'),
				'slug' => 'classifieds',
				'content' => '[peepso_wpadverts]',
			),
		);

		return ($aRet);
	}
}

// EOF