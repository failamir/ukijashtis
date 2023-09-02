<?php

class PeepSoVipIconsModel
{
	public $vipicons;

	private $_user_id;

	public function __construct($all = TRUE)
	{
		$post_status = (TRUE == $all) ? "any" : "publish";

		$this->_user_id = get_current_user_id();
		$this->vipicons = array();
		$args = array(
			'post_type' 		=> array('peepso_vip', 'peepso_vip_user'),
			'orderby'			=> 'menu_order',
			'order'				=> 'ASC',
			'posts_per_page' 	=> -1,
			'post_status'		=> $post_status,
		);

		$posts = new WP_Query($args);

		foreach($posts->posts as $post) {

			$vipicon = array(
				'id'				=> $post->ID,
				'post_id'			=> $post->ID,
				'published'			=> intval(('publish' == $post->post_status)),
				'title' 			=> __($post->post_title,'peepso-vip'),
				'content' 			=> __($post->post_content,'peepso-vip'),
				'icon'				=> $post->post_excerpt,
				'icon_url'			=> plugin_dir_url(dirname(__FILE__)).'../assets/svg/'.$post->post_excerpt,
				'custom'			=> intval(('peepso_vip_user' == $post->post_type)),
				'order'				=> intval($post->menu_order),
				'has_default_title' => FALSE,
			);

			if(strstr($post->post_excerpt, 'peepsocustom-')) {
				$vipicon['icon_url'] = str_replace('peepsocustom-','', $post->post_excerpt);
			}

			$vipicon['class']	='ps-vipicon-'.$vipicon['id'];

			$this->vipicons[$vipicon['id']] = (object) $vipicon;
		}
	}

	public function vipicon($id) {
		if(!isset($this->vipicons[$id])) {
			// Default to a like to avoid fatals in unlikely case of missing icon
            $fallback = new stdClass();
            $fallback->published = FALSE;
			return $fallback;
		}

		return clone $this->vipicons[$id];
	}
}
