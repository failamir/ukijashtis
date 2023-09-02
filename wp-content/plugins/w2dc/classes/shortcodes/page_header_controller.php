<?php 

class w2dc_page_header_controller extends w2dc_frontend_controller {

	public function init($args = array()) {
		parent::init($args);

		$shortcode_atts = array_merge(array(
				
		), $args);

		$this->args = $shortcode_atts;
		
		add_filter('breadcrumb_trail_items', array($this, 'setDirectoryBreadcrumbs'), 10, 2);

		apply_filters('w2dc_page_header_controller_construct', $this);
	}
	
	public function getFeaturedImage() {
		global $w2dc_instance;
	
		$image_url = '';
	
		if (
		$w2dc_instance &&
		(
				($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) ||
				($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) ||
				($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))
		)
		) {
			if ($directory_controller->is_category) {
				$categories_ids[] = $directory_controller->category->term_id;
				$categories_ids = array_merge(w2dc_get_term_parents_ids($directory_controller->category->term_id, W2DC_CATEGORIES_TAX), $categories_ids);
				foreach ($categories_ids AS $id) {
					if ($image_url = w2dc_getCategoryImageUrl($id, array(WDT_FEATURED_SIZE_WIDTH, WDT_FEATURED_SIZE_HEIGHT))) {
						break;
					}
				}
			} elseif ($directory_controller->is_location) {
				$locations_ids[] = $directory_controller->location->term_id;
				$locations_ids = array_merge(w2dc_get_term_parents_ids($directory_controller->location->term_id, W2DC_LOCATIONS_TAX), $locations_ids);
				foreach ($locations_ids AS $id) {
					if ($image_url = w2dc_getLocationImageUrl($id, array(WDT_FEATURED_SIZE_WIDTH, WDT_FEATURED_SIZE_HEIGHT))) {
						break;
					}
				}
			} elseif ($directory_controller->is_single) {
				$image_url = $directory_controller->listing->get_logo_url(array(WDT_FEATURED_SIZE_WIDTH, WDT_FEATURED_SIZE_HEIGHT));
			}
		}
	
		if (!$image_url) {
			$image_url = get_the_post_thumbnail_url(null, array(WDT_FEATURED_SIZE_WIDTH, WDT_FEATURED_SIZE_HEIGHT));
		}
	
		return $image_url;
	}
	
	public function getPageHeaderTitle($title = '') {
		global $w2dc_instance;
	
		if (
		$w2dc_instance &&
		(
				($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) ||
				($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) ||
				($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))
		)
		) {
			if ($directory_controller->page_title) {
				$title = $directory_controller->page_title;
			}
		}
	
		if (!$title) {
			if (is_front_page() && 'posts' === get_option('show_on_front')) {
				$title = bloginfo('name');
			}
			elseif (is_home() && ($blog_page_id = get_option('page_for_posts')) > 0) {
				$title = bloginfo('name');
			}
			elseif (is_singular()) {
				$title = single_post_title('', false);
			}
			elseif (is_archive()) {
				$title = strip_tags(get_the_archive_title());
			}
			elseif (is_search()) {
				$title = sprintf( __('Search Results for: %s', 'WDT'), get_search_query());
			}
			elseif (is_404()) {
				$title = __('404!', 'WDT');
			}
		}
	
		return $title;
	}
	
	public function setDirectoryBreadcrumbs($items, $args) {
		global $w2dc_instance;
	
		if (
		$w2dc_instance &&
		(
				($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_MAIN_SHORTCODE)) ||
				($directory_controller = $w2dc_instance->getShortcodeProperty(W2DC_LISTING_SHORTCODE)) ||
				($directory_controller = $w2dc_instance->getShortcodeProperty('webdirectory-listing'))
		) &&
		!empty($directory_controller->breadcrumbs)
		) {
			$items = $directory_controller->breadcrumbs;
		}
		return $items;
	}

	public function display() {
		return w2dc_renderTemplate('frontend/page_header.tpl.php', array(
			'featured_image' => $this->getFeaturedImage(),
			'page_title' => $this->getPageHeaderTitle(),
		), true);
	}
}

?>