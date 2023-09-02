<?php
require_once(PeepSo::get_plugin_dir() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'install.php');

class PeepSoVIPInstall extends PeepSoInstall
{

	// optional default settings
	protected $default_config = array(
		'vipso_where_to_display' => 1,
	);

	public function plugin_activation( $is_core = FALSE )
	{
		parent::plugin_activation($is_core);

		$defaults = array(

			0 => array(
				'post_title' 		=> __('Verified icon 1','peepso-vip'),
				'post_content' 		=> __('Verified icon 1','peepso-vip'),
				'post_excerpt'		=> 'def_1.svg',
				'post_status'		=> 'publish',
			),

			1 => array(
				'post_title'		=> __('Verified icon 2','peepso-vip'),
				'post_content'		=> __('Verified icon 2','peepso-vip'),
				'post_excerpt'		=> 'def_2.svg',
				'post_status'		=> 'publish',

			),

			2 => array(
				'post_title'		=> __('Verified icon 3','peepso-vip'),
				'post_content'		=> __('Verified icon 3','peepso-vip'),
				'post_excerpt'		=> 'def_3.svg',
				'post_status'		=> 'publish',

			),

			3 => array(
				'post_title'		=> __('Verified icon 4','peepso-vip'),
				'post_content'		=> __('Verified icon 4','peepso-vip'),
				'post_excerpt'		=> 'def_4.svg',
				'post_status'		=> 'publish',
			),

			4 => array(
				'post_title'		=> __('Verified icon 5','peepso-vip'),
				'post_content'		=> __('Verified icon 5','peepso-vip'),
				'post_excerpt'		=> 'def_5.svg',
				'post_status'		=> 'publish',
			),

			5 => array(
				'post_title'		=> __('Verified icon 6','peepso-vip'),
				'post_content'		=> __('Verified icon 6','peepso-vip'),
				'post_excerpt'		=> 'def_6.svg',
				'post_status'		=> 'publish',
			),

			6 => array(
				'post_title'		=> __('Verified icon 7','peepso-vip'),
				'post_content'		=> __('Verified icon 7','peepso-vip'),
				'post_excerpt'		=> 'def_7.svg',
				'post_status'		=> 'publish',
			),
			
			7 => array(
				'post_title'		=> __('Verified icon 8','peepso-vip'),
				'post_content'		=> __('Verified icon 8','peepso-vip'),
				'post_excerpt'		=> 'def_8.svg',
				'post_status'		=> 'publish',
			),
		);

		$default_args = array(
			'post_type' => 'peepso_vip',
		);



		$order = 0;

		foreach($defaults as $id=>$args) {

			$search = array_merge($default_args,array('post_parent' => $id));

			$posts = new WP_Query($search);

			if(!count($posts->posts)) {
				$args = array_merge($args, $search);
				$args['menu_order'] = $order;
				$order += 1;
				wp_insert_post($args);
			}
		}

		return (TRUE);
	}

}