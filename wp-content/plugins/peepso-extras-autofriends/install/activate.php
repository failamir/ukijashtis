<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class AutoFriendsPeepSoInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
	);

	public function plugin_activation( $is_core = FALSE )
	{
		parent::plugin_activation($is_core);
		return (TRUE);
	}

	public static function get_table_data()
	{
		$aRet = array(
			'user_autofriends' => "
				CREATE TABLE user_autofriends (
					af_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					af_user_id BIGINT(20) UNSIGNED NOT NULL,
					af_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (af_id)
				) ENGINE=InnoDB"
		);

		return $aRet;
	}

}