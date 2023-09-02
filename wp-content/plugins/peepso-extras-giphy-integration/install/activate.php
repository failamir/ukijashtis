<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class PeepSoGiphyInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		'giphy_api_key' => '3o6Zt9GewH6JbI812w',
		'giphy_display_limit' => 25,
		'giphy_rendition_comments' => 'fixed_width',
		'giphy_rendition_messages' => 'fixed_width',
	);

	public function plugin_activation( $is_core = FALSE )
	{
		parent::plugin_activation($is_core);
		return (TRUE);
	}

}