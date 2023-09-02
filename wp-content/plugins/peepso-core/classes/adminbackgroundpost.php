<?php

class PeepSoAdminBackgroundPost
{
	public static function administration()
	{
		self::enqueue_scripts();

		PeepSoTemplate::exec_template('background-post','admin_background_post');
	}

	public static function enqueue_scripts()
	{
		wp_deregister_script('peepso-admin-manage-background-post');
		wp_enqueue_script('peepso-admin-manage-background-post', PeepSo::get_asset('js/admin/manage-background-post.js'),
			array('peepso', 'jquery-ui-sortable'), PeepSo::PLUGIN_VERSION, TRUE);

		wp_enqueue_style('peepso-admin-manage-background-post', PeepSo::get_asset('css/admin-background-post.css'),
			array(), PeepSo::PLUGIN_VERSION, 'all');
	}
}