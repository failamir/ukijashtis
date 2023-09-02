<?php

class PeepSoWPAdvertsShortcode
{
	const SHORTCODE	= 'peepso_wpadverts';

	private static $_instance 	= NULL;
	public $url 				= NULL;
	public $adv_id	 			= NULL;

	private function __construct()
    {
		add_filter('peepso_page_title', array(&$this,'peepso_page_title'));
	}

	public static function get_instance()
	{
		if (NULL === self::$_instance) {
			self::$_instance = new self();
		}
		return (self::$_instance);
	}

    public static function description() {
        return __('Displays the WpAdverts listing.','peepso-wpadverts');
    }

    public static function post_state() {
        return _x('PeepSo', 'Page listing', 'peepso-wpadverts') . ' - ' . __('WP Adverts', 'peepso-wpadverts');
    }


	public function set_page( $url )
	{
		if(!$url instanceof PeepSoUrlSegments) {
            return;
        }

        $this->url = $url;

		$adv_id = $url->get(1);
		// Attempting a single group view
		// A adv ID can be numeric or a string (unique URL identifier)
		if( strlen($adv_id) ) {

			// if adv_id is action create
			if($adv_id == 'create') {
				// unregister "classifieds" listing
				remove_shortcode(self::SHORTCODE);
				// replace with "classifieds" view
				add_shortcode(self::SHORTCODE, array(self::get_instance(), 'shortcode_create_ads'));
			} elseif($adv_id == 'category') {
				// unregister "classifieds" listing
				remove_shortcode(self::SHORTCODE);
				// replace with "classifieds" view
				add_shortcode(self::SHORTCODE, array(self::get_instance(), 'shortcode_category'));
			}
		}
	}

    public function peepso_page_title( $title )
    {
        if(self::SHORTCODE == $title['title'] || $title['title'] == 'peepso_activity') {

			$title['newtitle'] = __('Classifieds', 'peepso-wpadverts');

		}

        return $title;
    }


	/**
	 * Registers the callback function for the peepso_messages shortcode.
	 */
	public static function register_shortcodes()
	{
		add_shortcode(self::SHORTCODE, array(self::get_instance(), 'shortcode_wpadverts'));
	}


	public function shortcode_create_ads()
	{
		PeepSo::reset_query();

		$ret = PeepSoTemplate::get_before_markup();

		$moderate = "";
		if(PeepSo::get_option('wpadverts_moderation_enable', 1) && !PeepSo::is_admin()) {
			$moderate = ' moderate="1"';
		}

		$ads_form = do_shortcode('[adverts_add'.$moderate.']');

		$ret .= PeepSoTemplate::exec_template('wpadverts', 'wpadverts-add', array('ads_form' => $ads_form), TRUE);
		$ret .= PeepSoTemplate::get_after_markup();

		return ($ret);
	}

	public function shortcode_manage_ads()
	{
		PeepSo::reset_query();

		$ret = PeepSoTemplate::get_before_markup();

		// modify manage link
		add_filter('adverts_manage_baseurl', array(&$this, 'manage_baseurl'));
		// add_filter('adverts_template_load', array(&$this, 'override_template'));
		$ads_manage = do_shortcode('[adverts_manage]');
		// remove_filter('adverts_template_load', array(&$this, 'override_template'));
		remove_filter('adverts_manage_baseurl', array(&$this, 'manage_baseurl'));

		$ret .= PeepSoTemplate::exec_template('wpadverts', 'wpadverts-manage', array('ads_manage' => $ads_manage), TRUE);
		$ret .= PeepSoTemplate::get_after_markup();

		return ($ret);
	}

	public function shortcode_category()
	{
		PeepSo::reset_query();

		$ret = PeepSoTemplate::get_before_markup();

		// modify manage link
		add_filter('adverts_manage_baseurl', array(&$this, 'manage_baseurl'));
		$ads_category = do_shortcode('[adverts_categories]');
		remove_filter('adverts_manage_baseurl', array(&$this, 'manage_baseurl'));

		$user = PeepSoUser::get_instance(get_current_user_id());
		$manage_url = $user->get_profileurl() . PeepSo::get_option('wpadverts_navigation_profile_slug', 'classifieds', TRUE) . '/manage/';

		$ret .= PeepSoTemplate::exec_template('wpadverts', 'wpadverts-category', array('ads_category' => $ads_category, 'manage_url' => $manage_url), TRUE);
		$ret .= PeepSoTemplate::get_after_markup();

		return ($ret);
	}

	public function manage_baseurl($url)
	{
		if(!$url instanceof PeepSoUrlSegments) {
            $url = PeepSoUrlSegments::get_instance();
            $section = $url->get(3);
        }

		if($section == 'manage') {
			$user = PeepSoUser::get_instance(get_current_user_id());
			$url = $user->get_profileurl() . PeepSo::get_option('wpadverts_navigation_profile_slug', 'classifieds', TRUE) . '/manage/';
		}

		return $url;
	}

	public function shortcode_wpadverts()
	{
		PeepSo::reset_query();

		$ret = PeepSoTemplate::get_before_markup();

		// list / search groups
		$input = new PeepSoInput();
		$search = $input->value('search', NULL, false);
		$location = $input->value('location', NULL, false);

		$num_results = 0;

		// special case - 404, group hidden, you've been banned etc
		if( FALSE === $this->adv_id ) {
			$ret .= PeepSoTemplate::do_404();
		} else {
			wp_enqueue_script('peepso-wpdverts-classifieds');

			$categories = adverts_taxonomies();

			$user = PeepSoUser::get_instance(get_current_user_id());
			$manage_url = $user->get_profileurl() . PeepSo::get_option('wpadverts_navigation_profile_slug', 'classifieds', TRUE) . '/manage/';
			$ret .= PeepSoTemplate::exec_template('wpadverts', 'wpadverts', array('search' => $search, 'num_results' => $num_results, 'location' => $location, 'manage_url' => $manage_url, 'adverts_categories' => $categories), TRUE);
		}

		$ret .= PeepSoTemplate::get_after_markup();

		return ($ret);
	}

}

// EOF
