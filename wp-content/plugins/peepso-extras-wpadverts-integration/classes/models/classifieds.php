<?php

/**
 * Class PeepSoClassifieds
 *
 * This class is used for getting data sets containing multiple classifieds, based on various conditions/filters
 *
 */

class PeepSoClassifieds
{
	public $user_id;
	public $search;
	public $location;

	public function get_classifieds($page, $limit, $order_by, $order, $search, $user_id = 0, $location = '', $category = NULL)
	{
		$taxonomy = null;
	    $meta = array();

	    $search = trim($search);

	    if($location) {
	        $meta[] = array('key'=>'adverts_location', 'value'=>$location, 'compare'=>'LIKE');
	    }

	    if($category > 0) {
	        $taxonomy =  array(
		            array(
		                'taxonomy' => 'advert_category',
		                'field'    => 'term_id',
		                'terms'    => $category,
		            ),
			);
	    }

	    $post_status = ['publish'];
	    if (($user_id == get_current_user_id() && get_current_user_id() !== 0) || PeepSo::is_admin()) {
	    	$post_status[] = 'expired';
	    }

	    $args = apply_filters( "adverts_list_query", array(
	        'author' => $user_id,
	        'post_type' => 'advert',
	        'post_status' => $post_status,
	        'posts_per_page' => $limit,
	        'paged' => $page,
	        's' => $search,
	        'meta_query' => $meta,
	        'tax_query' => $taxonomy,
	        'orderby' => array( 'menu_order' => 'DESC', 'date' => 'DESC' )
	    ));

	    if( ($category > 0) && is_tax( 'advert_category' ) ) {
	        $pbase = get_term_link( get_queried_object()->term_id, 'advert_category' );
	    } else {
	        $pbase = get_the_permalink();
	    }

	    $post_query = new WP_Query( $args );

		$classifieds = array();

		while ($post_query->have_posts()) {
			$post_query->the_post();
			$post = $post_query->post;

			$price = get_post_meta( $post->ID, "adverts_price", true );
			$price = ($price) ? esc_html( adverts_get_the_price( $post->ID, $price ) ) : "";
			$expires = get_post_meta( $post->ID, "_expiration_date", true );
			$is_owner = (get_current_user_id() == $post->post_author) ? true : false;
			$edit_url = '';
			if($is_owner) {
				$user = PeepSoUser::get_instance(get_current_user_id());
				$edit_url = $user->get_profileurl() . PeepSo::get_option('wpadverts_navigation_profile_slug', 'classifieds', TRUE) . '/manage/' . (PeepSo::get_option('disable_questionmark_urls', 0) === 0 ? '&' : '?') . 'advert_id=' . $post->ID;
			}
			if(!$is_owner && PeepSo::is_admin()) {
				$edit_url = admin_url('post.php?post=' . $post->ID . '&action=edit');
			}

			$user_id = get_current_user_id();

			$profile = PeepSoProfile::get_instance();
			$profile->init($post->post_author);
			$PeepSoUser = $profile->user;

			$item = array(
					'id' => $post->ID,
					'title' => $post->post_title,
					'image' => adverts_get_main_image( $post->ID ),
					'content' => $post->post_content,
					'permalink' => get_permalink( $post->ID ),
					'date_formated' => date_i18n( get_option( 'date_format' ), get_post_time( 'U', false, $post->ID ) ),
					'location' => get_post_meta( $post->ID, "adverts_location", true ),
					'expires' => esc_html( apply_filters( 'adverts_sh_manage_date', date_i18n( get_option('date_format'), $expires ), $post ) ),
					'is_expired' => (intval($expires) < time()) ? true : false,
					'price' => $price,
					'edit_url' => $edit_url,
					'is_owner' => $is_owner,
					'is_featured' => ( $post->menu_order ) ? true : false,
					'is_admin' => PeepSo::is_admin(),
					'user_id' => $post->post_author,
					'author' => $PeepSoUser->get_fullname(),
					'nonce_delete' => PeepSoWPAdverts::isVersion140() ? wp_create_nonce( sprintf( 'wpadverts-delete-%d', $post->ID ) ) : wp_create_nonce('wpadverts-delete')
				);
			$classifieds[] = $item;
		}

		wp_reset_postdata();

		return $classifieds;
	}
}

// EOF
