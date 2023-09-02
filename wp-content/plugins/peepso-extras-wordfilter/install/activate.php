<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class WordfilterPeepSoInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		'wordfilter_enable' => 1,
		'wordfilter_keywords' => 'samplebadword, anothersamplebadword',
		'wordfilter_how_to_render' => 1,
		'wordfilter_character' => '*'
	);

	public function plugin_activation( $is_core = FALSE )
	{
		parent::plugin_activation($is_core);
		return (TRUE);
	}

}