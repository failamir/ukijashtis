<?php 

class w2dc_frontend_controller {
	public $args = array();
	public $query;
	public $page_title;
	public $template;
	public $listings = array();
	public $search_form;
	public $map;
	public $paginator;
	public $breadcrumbs = array();
	public $base_url;
	public $messages = array();
	public $hash = null;
	public $levels_ids;
	public $do_initial_load = true;
	public $request_by = 'frontend_controller';

	public function __construct($args = array()) {
		apply_filters('w2dc_frontend_controller_construct', $this);
	}
	
	public function init($attrs = array()) {
		$this->args['logo_animation_effect'] = get_option('w2dc_logo_animation_effect');

		if (!$this->hash) {
			if (isset($attrs['uid']) && $attrs['uid']) {
				$this->hash = md5($attrs['uid']);
			} else {
				$this->hash = md5(get_class($this).serialize($attrs));
			}
		}
	}

	public function processQuery($load_map = true, $map_args = array()) {
		// this is special construction,
		// this needs when we order by any postmeta field, this adds listings to the list with "empty" fields
		if (($this->getQueryVars('orderby') == 'meta_value_num' || $this->getQueryVars('orderby') == 'meta_value') && ($this->getQueryVars('meta_key') != '_order_date')) {
			$args = $this->getQueryVars();

			// there is strange thing - WP adds `taxonomy` and `term_id` args to the root of query vars array
			// this may cause problems
			unset($args['taxonomy']);
			unset($args['term_id']);
			if (empty($args['s'])) {
				unset($args['s']);
			}
			
			$original_posts_per_page = $args['posts_per_page'];

			$ordered_posts_ids = get_posts(array_merge($args, array('fields' => 'ids', 'nopaging' => true)));
			//var_dump($ordered_posts_ids);
			$ordered_max_num_pages = ceil(count($ordered_posts_ids)/$original_posts_per_page) - (int) $ordered_posts_ids;

			$args['paged'] = $args['paged'] - $ordered_max_num_pages;
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = '_order_date';
			$args['order'] = 'DESC';
			$args['posts_per_page'] = $original_posts_per_page - $this->query->post_count;
			$all_posts_ids = get_posts(array_merge($args, array('fields' => 'ids', 'nopaging' => true)));
			$all_posts_count = count($all_posts_ids);
			//var_dump($all_posts_count);

			if ($this->query->found_posts) {
				$args['post__not_in'] = array_map('intval', $ordered_posts_ids);
				if (!empty($args['post__in']) && is_array($args['post__in'])) {
					$args['post__in'] = array_diff($args['post__in'], $args['post__not_in']);
					if (!$args['post__in']) {
						$args['posts_per_page'] = 0;
					}
				}
			}

			$unordered_query = new WP_Query($args);
			//var_dump($args);

			//var_dump($unordered_query->request);
			//var_dump($this->query->request);

			if ($args['posts_per_page']) {
				$this->query->posts = array_merge($this->query->posts, $unordered_query->posts);
			}

			$this->query->post_count = count($this->query->posts);
			$this->query->found_posts = $all_posts_count;
			$this->query->max_num_pages = ceil($all_posts_count/$original_posts_per_page);
		}

		if ($load_map) {
			if (!isset($map_args['map_markers_is_limit']))
				$map_args['map_markers_is_limit'] = get_option('w2dc_map_markers_is_limit');
			$this->map = new w2dc_maps($map_args, $this->request_by);
			$this->map->setUniqueId($this->hash);
			
			if (!$map_args['map_markers_is_limit'] && !$this->map->is_ajax_markers_management()) {
				$this->collectAllLocations();
			}
		}

		while ($this->query->have_posts()) {
			$this->query->the_post();

			$listing = new w2dc_listing;
			$listing->loadListingFromPost(get_post());
			$listing->logo_animation_effect = (isset($this->args['logo_animation_effect'])) ? $this->args['logo_animation_effect'] : get_option('w2dc_logo_animation_effect');

			if ($load_map && $map_args['map_markers_is_limit'] && !$this->map->is_ajax_markers_management())
				$this->map->collectLocations($listing);
			
			$this->listings[get_the_ID()] = $listing;
		}
		
		global $w2dc_address_locations, $w2dc_tax_terms_locations;
		// empty this global arrays - there may be some maps on one page with different arguments
		$w2dc_address_locations = array();
		$w2dc_tax_terms_locations = array();

		// this is reset is really required after the loop ends 
		wp_reset_postdata();
		
		remove_filter('posts_join', 'join_levels');
		remove_filter('posts_orderby', 'orderby_levels', 1);
		remove_filter('get_meta_sql', 'add_null_values');
	}
	
	public function collectAllLocations() {
		$args = $this->getQueryVars();
		
		unset($args['orderby']);
		unset($args['order']);
		$args['nopaging'] = 1;
		$unlimited_query = new WP_Query($args);
		while ($unlimited_query->have_posts()) {
			$unlimited_query->the_post();
		
			$listing = new w2dc_listing;
			$listing->loadListingFromPost(get_post());
		
			$this->map->collectLocations($listing);
		}
	}
	
	public function getQueryVars($var = null) {
		if (is_null($var)) {
			return $this->query->query_vars;
		} else {
			if (isset($this->query->query_vars[$var])) {
				return $this->query->query_vars[$var];
			}
		}
		return false;
	}
	
	public function getPageTitle() {
		return $this->page_title;
	}

	public function getBreadCrumbs($separator = ' » ') {
		return implode($separator, $this->breadcrumbs);
	}

	public function getBaseUrl() {
		return $this->base_url;
	}
	
	public function where_levels_ids($where = '') {
		if ($this->levels_ids)
			$where .= " AND (w2dc_levels.id IN (" . implode(',', $this->levels_ids) . "))";
		return $where;
	}
	
	public function getListingsDirectory() {
		global $w2dc_instance;
		
		if (isset($this->args['directories']) && !empty($this->args['directories'])) {
			if (is_object($this->args['directories'])) {
				return $this->args['directories'];
			} elseif (is_string($this->args['directories'])) {
				if ($directories_ids = array_filter(explode(',', $this->args['directories']), 'trim')) {
					if (count($directories_ids) == 1 && ($directory = $w2dc_instance->directories->getDirectoryById($directories_ids[0]))) {
						return $directory;
					}
				}
			}
		}
		
		return $w2dc_instance->current_directory;
	}
	
	public function getListingClasses() {
		$classes = array();
		
		if ($this->listings[get_the_ID()]->level->featured) {
			$classes[] = 'w2dc-featured';
		}
		if ($this->listings[get_the_ID()]->level->sticky) {
			$classes[] = 'w2dc-sticky';
		}
		if (!empty($this->args['summary_on_logo_hover'])) {
			$classes[] = 'w2dc-summary-on-logo-hover';
		}
		if (!empty($this->args['hide_content'])) {
			$classes[] = 'w2dc-hidden-content';
		}
		return $classes;
	}
	
	public function display() {
		$output =  w2dc_renderTemplate($this->template, array('frontend_controller' => $this), true);
		wp_reset_postdata();
	
		return $output;
	}
}

/**
 * join levels_relationships and levels tables into the query
 * 
 * */
function join_levels($join = '') {
	global $wpdb;

	$join .= " LEFT JOIN {$wpdb->w2dc_levels_relationships} AS w2dc_lr ON w2dc_lr.post_id = {$wpdb->posts}.ID ";
	$join .= " LEFT JOIN {$wpdb->w2dc_levels} AS w2dc_levels ON w2dc_levels.id = w2dc_lr.level_id ";

	return $join;
}

/**
 * sticky and featured listings in the first order
 * 
 */
function orderby_levels($orderby = '') {
	$orderby = " w2dc_levels.sticky DESC, w2dc_levels.featured DESC, " . $orderby;
	return $orderby;
}

/**
 * sticky and featured listings in the first order
 * 
 */
function where_sticky_featured($where = '') {
	$where .= " AND (w2dc_levels.sticky=1 OR w2dc_levels.featured=1)";
	return $where;
}

/**
 * Listings with empty values must be sorted as well
 * 
 */
function add_null_values($clauses) {
	$clauses['where'] = preg_replace("/wp_postmeta\.meta_key = '_content_field_([0-9]+)'/", "(wp_postmeta.meta_key = '_content_field_$1' OR wp_postmeta.meta_value IS NULL)", $clauses['where']);
	return $clauses;
}


add_filter('w2dc_order_args', 'w2dc_order_listings', 10, 3);
function w2dc_order_listings($order_args = array(), $defaults = array(), $include_GET_params = true) {
	global $w2dc_instance;
	
	// adapted for Relevanssi
	if (w2dc_is_relevanssi_search($defaults)) {
		return $order_args;
	}

	if ($include_GET_params && isset($_GET['order_by']) && $_GET['order_by']) {
		$order_by = $_GET['order_by'];
		$order = w2dc_getValue($_GET, 'order', 'ASC');
	} else {
		if (isset($defaults['order_by']) && $defaults['order_by']) {
			$order_by = $defaults['order_by'];
			$order = w2dc_getValue($defaults, 'order', 'ASC');
		} else {
			$order_by = 'post_date';
			$order = 'DESC';
		}
	}

	$order_args['orderby'] = $order_by;
	$order_args['order'] = $order;

	if ($order_by == 'rand' || $order_by == 'random') {
		// do not order by rand in search results
		//if ($_REQUEST['w2dc_action'] != 'search') {
			if (get_option('w2dc_orderby_sticky_featured')) {
				add_filter('posts_join', 'join_levels');
				add_filter('posts_orderby', 'orderby_levels', 1);
			}
			$order_args['orderby'] = 'rand';
		/* } else {
			$order_by = 'post_date';
		} */
	}

	if ($order_by == 'title') {
		//$order_args['orderby'] = 'title';
		$order_args['orderby'] = array('title' => $order_args['order'], 'meta_value_num' => 'ASC');
		$order_args['meta_key'] = '_order_date';
		if (get_option('w2dc_orderby_sticky_featured')) {
			add_filter('posts_join', 'join_levels');
			add_filter('posts_orderby', 'orderby_levels', 1);
		}
	} elseif ($order_by == 'post_date' || get_option('w2dc_orderby_sticky_featured')) {
		// Do not affect levels weights when already ordering by posts IDs
		if (!isset($order_args['orderby']) || $order_args['orderby'] != 'post__in') {
			add_filter('posts_join', 'join_levels');
			add_filter('posts_orderby', 'orderby_levels', 1);
			add_filter('get_meta_sql', 'add_null_values');
		}

		if ($order_by == 'post_date') {
			$w2dc_instance->order_by_date = true;
			// First of all order by _order_date parameter
			$order_args['orderby'] = 'meta_value_num';
			$order_args['meta_key'] = '_order_date';
		} else
			$order_args = array_merge($order_args, $w2dc_instance->content_fields->getOrderParams($defaults));
	} else {
		$order_args = array_merge($order_args, $w2dc_instance->content_fields->getOrderParams($defaults));
	}

	return $order_args;
}

class w2dc_query_search extends WP_Query {
	function __parse_search($q) {
		$x = $this->parse_search($q);
		return $x;
	}
}
add_filter('posts_clauses', 'posts_clauses', 10, 2);
function posts_clauses($clauses, $q) {
	if ($title = $q->get('_meta_or_title')) {
		$qu['s'] = $title;
		$w2dc_query_search = new w2dc_query_search;

		$tax_query_vars = array();
		if (!empty($q->query_vars['tax_query'])) {
			$tax_query_vars = $q->query_vars['tax_query'];
		}
        $tq = new WP_Tax_Query($tax_query_vars);

        global $wpdb;
        $tc = $tq->get_sql($wpdb->posts, 'ID');
        
        if ($tc['where'] && ($search_sql = $w2dc_query_search->__parse_search($qu))) {
	        $clauses['where'] = str_ireplace( 
	           $search_sql, 
	            ' ', 
	            $clauses['where'] 
	        );
	        $clauses['where'] = str_ireplace( 
	            $tc['where'], 
	            ' ', 
	            $clauses['where'] 
	        );
	        $clauses['where'] .= sprintf( 
	            " AND ( ( 1=1 %s ) OR ( 1=1 %s ) ) ", 
	            $tc['where'],
	            $search_sql
	        );
        }
    }
    return $clauses;
}

function w2dc_what_search($args, $defaults = array(), $include_GET_params = true) {
	if ($include_GET_params) {
		$args['s'] = w2dc_getValue($_GET, 'what_search', w2dc_getValue($defaults, 'what_search'));
	} else {
		$args['s'] = w2dc_getValue($defaults, 'what_search');
	}
	
	$args['s'] = stripslashes($args['s']);
	
	$args['s'] = apply_filters('w2dc_search_param_what_search', $args['s']);

	// 's' parameter must be removed when it is empty, otherwise it may case WP_query->is_search = true
	if (empty($args['s'])) {
		unset($args['s']);
	}

	return $args;
}
add_filter('w2dc_search_args', 'w2dc_what_search', 10, 3);

function w2dc_address($args, $defaults = array(), $include_GET_params = true) {
	global $wpdb, $w2dc_address_locations;

	if ($include_GET_params) {
		$address = w2dc_getValue($_GET, 'address', w2dc_getValue($defaults, 'address'));
		$search_location = w2dc_getValue($_GET, 'location_id', w2dc_getValue($defaults, 'location_id'));
	} else {
		$search_location = w2dc_getValue($defaults, 'location_id');
		$address = w2dc_getValue($defaults, 'address');
	}
	
	$search_location = apply_filters('w2dc_search_param_location_id', $search_location);
	$address = apply_filters('w2dc_search_param_address', $address);
	
	$where_sql_array = array();
	if ($search_location && is_numeric($search_location)) {
		$term_ids = get_terms(W2DC_LOCATIONS_TAX, array('child_of' => $search_location, 'fields' => 'ids', 'hide_empty' => false));
		$term_ids[] = $search_location;
		$where_sql_array[] = "(location_id IN (" . implode(', ', $term_ids) . "))";
	}
	
	if ($address) {
		$where_sql_array[] = $wpdb->prepare("(address_line_1 LIKE '%%%s%%' OR address_line_2 LIKE '%%%s%%' OR zip_or_postal_index LIKE '%%%s%%')", $address, $address, $address);
		
		// Search keyword in locations terms
		$t_args = array(
				'taxonomy'      => array(W2DC_LOCATIONS_TAX),
				'orderby'       => 'id',
				'order'         => 'ASC',
				'hide_empty'    => true,
				'fields'        => 'tt_ids',
				'name__like'    => $address
		);
		$address_locations = get_terms($t_args);

		foreach ($address_locations AS $address_location) {
			$term_ids = get_terms(W2DC_LOCATIONS_TAX, array('child_of' => $address_location, 'fields' => 'ids', 'hide_empty' => false));
			$term_ids[] = $address_location;
			$where_sql_array[] = "(location_id IN (" . implode(', ', $term_ids) . "))";
		}
	}

	if ($where_sql_array) {
		$results = $wpdb->get_results("SELECT id, post_id FROM {$wpdb->w2dc_locations_relationships} WHERE " . implode(' OR ', $where_sql_array), ARRAY_A);
		$post_ids = array();
		foreach ($results AS $row) {
			$post_ids[] = $row['post_id'];
			$w2dc_address_locations[] = $row['id'];
		}
		if ($post_ids) {
			$args['post__in'] = $post_ids;
		} else {
			// Do not show any listings
			$args['post__in'] = array(0);
		}	
	}
	return $args;
}
add_filter('w2dc_search_args', 'w2dc_address', 10, 3);

function w2dc_base_url_args($args) {
	if (isset($_REQUEST['w2dc_action']) && $_REQUEST['w2dc_action'] == 'search') {
			$args['w2dc_action'] = 'search';
		if (isset($_REQUEST['what_search']) && $_REQUEST['what_search'])
			$args['what_search'] = urlencode($_REQUEST['what_search']);
		if (isset($_REQUEST['address']) && $_REQUEST['address'])
			$args['address'] = urlencode($_REQUEST['address']);
		if (isset($_REQUEST['location_id']) && $_REQUEST['location_id'] && is_numeric($_REQUEST['location_id']))
			$args['location_id'] = $_REQUEST['location_id'];
	}
	
	// Required in ajax controller for get_pagenum_link() filter
	if (get_option('w2dc_ajax_initial_load')) {
		if (isset($_REQUEST['order_by']) && $_REQUEST['order_by'])
			$args['order_by'] = $_REQUEST['order_by'];
		if (isset($_REQUEST['order']) && $_REQUEST['order'])
			$args['order'] = $_REQUEST['order'];
	}

	return $args;
}
add_filter('w2dc_base_url_args', 'w2dc_base_url_args');

function w2dc_related_shortcode_args($shortcode_atts) {
	global $w2dc_instance;
	
	if ((isset($shortcode_atts['directories']) && $shortcode_atts['directories'] == 'related') || (isset($shortcode_atts['related_directory']) && $shortcode_atts['related_directory'])) {
		if (($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))) {
			if ($directory_controller->is_home || $directory_controller->is_search || $directory_controller->is_category || $directory_controller->is_location || $directory_controller->is_tag) {
				$shortcode_atts['directories'] = $w2dc_instance->current_directory->id;
			} elseif ($directory_controller->is_single) {
				$shortcode_atts['directories'] = $directory_controller->listing->directory->id;
				$shortcode_atts['post__not_in'] = $directory_controller->listing->post->ID;
			}
		}
	}

	if ((isset($shortcode_atts['categories']) && $shortcode_atts['categories'] == 'related') || (isset($shortcode_atts['related_categories']) && $shortcode_atts['related_categories'])) {
		if (($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))) {
			if ($directory_controller->is_category) {
				$shortcode_atts['categories'] = $directory_controller->category->term_id;
			} elseif ($directory_controller->is_single) {
				if ($terms = get_the_terms($directory_controller->listing->post->ID, W2DC_CATEGORIES_TAX)) {
					$terms_ids = array();
					foreach ($terms AS $term)
						$terms_ids[] = $term->term_id;
					$shortcode_atts['categories'] = implode(',', $terms_ids);
				}
				$shortcode_atts['post__not_in'] = $directory_controller->listing->post->ID;
			}
		}
	}

	if ((isset($shortcode_atts['locations']) && $shortcode_atts['locations'] == 'related') || (isset($shortcode_atts['related_locations']) && $shortcode_atts['related_locations'])) {
		if (($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))) {
			if ($directory_controller->is_location) {
				$shortcode_atts['locations'] = $directory_controller->location->term_id;
			} elseif ($directory_controller->is_single) {
				if ($terms = get_the_terms($directory_controller->listing->post->ID, W2DC_LOCATIONS_TAX)) {
					$terms_ids = array();
					foreach ($terms AS $term)
						$terms_ids[] = $term->term_id;
					$shortcode_atts['locations'] = implode(',', $terms_ids);
				}
				$shortcode_atts['post__not_in'] = $directory_controller->listing->post->ID;
			}
		}
	}

	if (isset($shortcode_atts['related_tags']) && $shortcode_atts['related_tags']) {
		if (($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))) {
			if ($directory_controller->is_tag) {
				$shortcode_atts['tags'] = $directory_controller->tag->term_id;
			} elseif ($directory_controller->is_single) {
				if ($terms = get_the_terms($directory_controller->listing->post->ID, W2DC_TAGS_TAX)) {
					$terms_ids = array();
					foreach ($terms AS $term)
						$terms_ids[] = $term->term_id;
					$shortcode_atts['tags'] = implode(',', $terms_ids);
				}
				$shortcode_atts['post__not_in'] = $directory_controller->listing->post->ID;
			}
		}
	}

	if (isset($shortcode_atts['author']) && $shortcode_atts['author'] === 'related') {
		if (($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) || ($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))) {
			if ($directory_controller->is_single) {
				$shortcode_atts['author'] = $directory_controller->listing->post->post_author;
				$shortcode_atts['post__not_in'] = $directory_controller->listing->post->ID;
			}
		} elseif ($user_id = get_the_author_meta('ID')) {
			$shortcode_atts['author'] = $user_id;
		}
	}

	return $shortcode_atts;
}
add_filter('w2dc_related_shortcode_args', 'w2dc_related_shortcode_args');

function w2dc_set_directory_args($args, $directories_ids = array()) {
	global $w2dc_instance;
	
	if ($w2dc_instance->directories->isMultiDirectory()) {
		if (!isset($args['meta_query']))
			$args['meta_query'] = array();
	
		$args['meta_query'] = array_merge($args['meta_query'], array(
				array(
						'key' => '_directory_id',
						'value' => $directories_ids,
						'compare' => 'IN',
				)
		));
	}

	return $args;
}

function w2dc_keywordInCategorySearch($keyword) {
	if (w2dc_getValue($_REQUEST, 'w2dc_action') == 'search' && ($categories = array_filter(explode(',', w2dc_getValue($_REQUEST, 'categories')), 'trim')) && count($categories) == 1) {
		if (!is_wp_error($category = get_term(array_pop($categories), W2DC_CATEGORIES_TAX))) {
			$keyword = trim(str_ireplace(htmlspecialchars_decode($category->name), '', $keyword));
		}
	}
	return $keyword;
}
add_filter('w2dc_search_param_what_search', 'w2dc_keywordInCategorySearch');

function w2dc_addressInLocationSearch($address) {
	if (w2dc_getValue($_REQUEST, 'w2dc_action') == 'search' && ($location_id = array_filter(explode(',', w2dc_getValue($_REQUEST, 'location_id')), 'trim')) && count($location_id) == 1) {
		if (!is_wp_error($location = get_term(array_pop($location_id), W2DC_LOCATIONS_TAX))) {
			$address = trim(str_ireplace(htmlspecialchars_decode($location->name), '', $address));
		}
	}
	return $address;
}
add_filter('w2dc_search_param_address', 'w2dc_addressInLocationSearch');


?>