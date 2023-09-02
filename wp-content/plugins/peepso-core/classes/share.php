<?php

class PeepSoShare
{
	/*
	 * $share_links = array(
	 *		<SHARING_SERVICE> => array(
	 *			'icon' => <URL OF THE ICON TO USE>,
	 *			'url'  => <Share URL, need to have --peepso-url--, this will be replaced by the actual URL
	 *						to be shared>
	 *		);
	 * );
	 */
	private $share_links = array();

	private static $_instance = NULL;

	// list of allowed template tags
	public $template_tags = array(
		'show_links',		// display social sharing links
	);

	private function __construct()
	{
	}

	/*
	 * return singleton instance
	 */
	public static function get_instance()
	{
		if (NULL === self::$_instance)
			self::$_instance = new self();
		return (self::$_instance);
	}

	/*
	 * Returns the social sharing links as an array
	 * @return array The sharing links
	 */
	public function get_links( $all = FALSE)
	{
		$this->share_links = array(
            'peepso_my_profile' => array(
                'label' => 'My profile',
                'icon' => 'my-profile',
                'url'  => '#',
                'internal'=> TRUE,
                'desc' => 'Share as your post on this community',
                'class' => 'SOMETHING',
            ),
			'separator_1' => TRUE,
			'facebook' => array(
				'label' => 'Facebook',
				'icon' => 'facebook',
				'url'  => 'https://www.facebook.com/sharer.php?u=--peepso-url--'
			),
			'twitter' => array(
				'label' => 'Twitter',
				'icon' => 'twitter',
				'url'  => 'https://twitter.com/share?url=--peepso-url--'
			),
			'linkedin' => array(
				'label' => 'LinkedIn',
				'icon' => 'linkedin',
				'url'  => 'https://www.linkedin.com/shareArticle?mini=true&url=--peepso-url--&source=' . urlencode(get_bloginfo('name'))
			),
			'reddit' => array(
				'label' => 'Reddit',
				'icon' => 'reddit',
				'url'  => 'https://www.reddit.com/submit?url=--peepso-url--'
			),
			'pinterest' => array(
				'label' => 'Pinterest',
				'icon' => 'pinterest',
				'url'  => 'https://pinterest.com/pin/create/link/?url=--peepso-url--'
			),
			'whatsapp' => array(
				'label' => 'WhatsApp',
				'icon' => 'whatsapp',
				'url'  => 'https://api.whatsapp.com/send?text=--peepso-url--'
			),
			'google_bookmarks' => array(
				'label' => 'Google Bookmarks',
				'icon' => 'google',
				'url'  => 'https://www.google.com/bookmarks/mark?op=edit&bkmk=--peepso-url--'
			),
		);

		if(!PeepSo::is_dev_mode('new_sharing')) {
            unset($this->share_links['peepso_my_profile']);
            unset($this->share_links['separator_1']);
        }

		$this->share_links = apply_filters('peepso_share_links', $this->share_links);

		if(!$all) {
            foreach ($this->share_links as $key => $link) {

                if(!PeepSo::get_option('activity_social_sharing_provider_'.$key, 1)) {
                    unset($this->share_links[$key]);
                }
            }
        }

		return $this->share_links;
	}

	/*
	 * Template callback for display share links
	 */
	public function show_links()
	{
		echo '<div class="ps-sharebox">', PHP_EOL;
		$links = $this->get_links();
		if(count($links)) {
            foreach ($links as $key => $link) {
                if(is_array($link)) {
                    $class = '';
                    $class .= isset($link['class']) ? " {$link['class']} " : '';
                    $class .= isset($link['internal']) ? " internal " : '';

                    echo '<a class="ps-sharebox__item '.$class.'" href="', $link['url'], '" target="_blank">', PHP_EOL;
                    echo '<span class="ps-sharebox__icon ps-icon--social ps-icon--social-', $link['icon'], '">', $link['label'], '</span>', PHP_EOL;
                    echo '</a>', PHP_EOL;
                } elseif(stristr($key, 'separator')) {
                    // nothing for now?
                }
            }
        } else {
		    echo __('Sorry, it looks like the no social sharing platforms are enabled', 'peepso-core');
        }
		echo '</div>', PHP_EOL;
	}
}

// EOF
