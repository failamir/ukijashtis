<?php 

class w2dc_terms_view {
	public $attrs;
	public $depth;
	public $columns;
	public $hide_empty;
	public $count;
	public $max_subterms;
	public $exact_terms = array();
	public $exact_terms_obj = array();
	public $col_md;
	public $tax;
	public $terms_icons_url;
	public $grid;
	public $grid_view;
	public $icons;
	public $menu;
	public $view_all_terms;
	public $directory;
	
	public function __construct($params) {
		$this->attrs = array_merge(array(
				'directory' => 0,
				'parent' => 0,
				'depth' => 2,
				'columns' => 2,
				'count' => true,
				'hide_empty' => false,
				'max_subterms' => 0,
				'exact_terms' => array(),
				'grid' => 0,
				'grid_view' => 0,
				'icons' => 1,
		), $params);

		$this->directory = $this->attrs['directory'];
		$this->parent = $this->attrs['parent'];
		$this->depth = $this->attrs['depth'];
		$this->columns = $this->attrs['columns'];
		$this->count = $this->attrs['count'];
		$this->hide_empty = $this->attrs['hide_empty'];
		$this->max_subterms = $this->attrs['max_subterms'];
		$this->grid = $this->attrs['grid'];
		$this->grid_view = $this->attrs['grid_view'];
		$this->icons = $this->attrs['icons'];
		$this->menu = $this->attrs['menu'];
		
		if (is_array($this->attrs['exact_terms']) && !empty($this->attrs['exact_terms'])) {
			foreach ($this->attrs['exact_terms'] AS $term) {
				if (is_numeric($term)) {
					if ($term_obj = get_term_by('id', $term, $this->tax)) {
						$this->exact_terms[] = $term_obj->term_id;
						$this->exact_terms_obj[] = $term_obj;
					}
				} else {
					if ($term_obj = get_term_by('slug', $term, $this->tax)) {
						$this->exact_terms[] = $term_obj->term_id;
						$this->exact_terms_obj[] = $term_obj;
					}
				}
			}
		}
		
		if ($this->attrs['depth'] > 2) {
			$this->depth = 2;
		}
		if ($this->depth == 0 || !is_numeric($this->depth)) {
			$this->depth = 1;
		}
		if ($this->columns > 4) {
			$this->columns = 4;
		}
		if ($this->columns == 0 || !is_numeric($this->columns)) {
			$this->columns = 1;
		}
		$this->col_md = 12/$this->columns;
	}
	
	public function getTerms($parent) {
		// we use array_merge with empty array because we need to flush keys in terms array
		//if ($this->count) {
			$terms = array_merge(
					// there is a wp bug with pad_counts in get_terms function - so we use this construction
					wp_list_filter(
							get_categories(array(
									'taxonomy' => $this->tax,
									'pad_counts' => true,
									'hide_empty' => $this->hide_empty,
									'include' => $this->exact_terms,
							)),
							array('parent' => $parent)
					), array());
		/* } else {
			$terms = array_merge(
					get_categories(array(
							'taxonomy' => $this->tax,
							'pad_counts' => true,
							'hide_empty' => $this->hide_empty,
							'parent' => $parent,
							'include' => $this->exact_terms,
					)), array());
		} */
		
		return $terms;
	}
	
	public function getCount($term) {
		if ($this->exact_terms) {
			$q = new WP_Query(array(
					'nopaging' => true,
					'tax_query' => array(
							array(
									'taxonomy' => $this->tax,
									'field' => 'id',
									'terms' => $term->term_id,
									'include_children' => true,
							),
					),
					'fields' => 'ids',
			));
			$terms_count = $q->post_count;
		} else {
			$terms_count = $term->count;
		}

		return $terms_count;
	}
	
	public function getWrapperClasses() {
		$classes[] = "w2dc-content";
		$classes[] = $this->wrapper_classes;
		$classes[] = "w2dc-terms-columns-" . $this->columns;
		if ($this->menu) {
			$classes[] = "w2dc-terms-menu";
		}
		if ($this->grid) {
			$classes[] = $this->grid_classes;
		}
		$classes[] = "w2dc-terms-depth-" . $this->depth;
		
		return implode(' ', $classes);
	}
	
	public function renderFeaturedImage($term, $size = array(600, 400)) {
		if ($image_url = $this->getTermImageUrl($term->term_id, $size)) {
			$featured_image = 'style="background-image: url(' . $image_url . ');"';
		} else {
			$featured_image = '';
		}

		return $featured_image;
	}

	public function renderIconImage($term) {
		if ($this->icons && $icon_url= $this->getTermIconFile($term->term_id)) {
			$icon_image = '<img class="w2dc-field-icon" src="' . $icon_url . '" />';
		} else {
			$icon_image = '';
		}

		return $icon_image;
	}

	public function renderTermCount($term) {
		if ($this->count) {
			$term_count = '<span class="' . $this->term_count_classes . '">' . $this->getCount($term) . '</span>';
		} else {
			$term_count = '';
		}

		return $term_count;
	}
	
	public function display() {
		global $w2dc_directory_flag;
		if ($this->directory) {
			$w2dc_directory_flag = $this->directory;
		}
		
		$terms = $this->getTerms($this->parent);
		
		if (!$terms && $this->exact_terms && (get_terms($this->tax, array('hide_empty' => false, 'parent' => $this->parent)))) {
			$terms = $this->exact_terms_obj;
		}

		if ($terms) {
			echo '<div class="' . $this->getWrapperClasses() . '">';
			switch ($this->grid_view) {
				case 0:
					$this->standardGridView($terms);
					break;
				case 1:
					$this->leftGridView($terms);
					break;
				case 2:
					$this->rightGridView($terms);
					break;
				case 3:
					$this->centerGridView($terms);
					break;
			}
			echo '</div>';
		}
		
		$w2dc_directory_flag = 0;
	}

	function standardGridView($terms) {
		$terms_number = count($terms);
		$counter = 0;
		$tcounter = 0;
		
		foreach ($terms AS $key=>$term) {
			$tcounter++;
			if ($counter == 0) {
				echo '<div class="w2dc-row ' . $this->row_classes . '">';
			}
		
			echo '<div class="w2dc-col-md-' . $this->col_md . '">';
			echo '<div class="' . $this->column_classes . '">';
			echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
			echo $this->_display($term->term_id, 1);
			echo '</div>';
		
			echo '</div>';
		
			$counter++;
			if ($counter == $this->columns || ($tcounter == $terms_number && $counter != $this->columns)) {
				echo '</div>';
			}
			if ($counter == $this->columns) {
				$counter = 0;
			}
		}
	}
	function _display($parent, $depth_level) {
		$html = '';
		if ($this->depth == 0 || !is_numeric($this->depth) || $this->depth > $depth_level) {
			$terms = $this->getTerms($parent);
			if ($terms) {
				$depth_level++;
				$counter = 0;
				$html .= '<div class="' . $this->subterms_classes . '">';
				$html .= '<ul>';
				foreach ($terms AS $term) {
					if ($this->count) {
						$term_count = '<span class="' . $this->term_count_classes . '">' . $this->getCount($term) . '</span>';
					} else {
						$term_count = '';
					}
		
					if ($this->icons && $icon_url = $this->getTermIconFile($term->term_id)) {
						$icon_image = '<img class="w2dc-field-icon" src="' . $icon_url . '" />';
					} else {
						$icon_image = '';
					}
		
					$counter++;
					if ($this->max_subterms != 0 && $counter > $this->max_subterms) {
						$html .= '<li class="' . $this->item_classes . '"><a href="' . get_term_link(intval($parent), $this->tax) . '">' . $this->view_all_terms . '</a></li>';
						break;
					} else
						$html .= '<li class="' . $this->item_classes . '"><a href="' . get_term_link($term) . '" title="' . $term->name . '">' . $icon_image . $term->name . $term_count . '</a></li>';
				}
				$html .= '</ul>';
				$html .= '</div>';
			}
		}
		return $html;
	}
	
	function leftGridView($terms) {
		$term = $terms[0];
		echo '<div class="w2dc-row w2dc-left-grid-view ' . $this->row_classes . '">';
			echo '<div class="w2dc-col-md-6">';
				echo '<div class="' . $this->column_classes . '">';
					echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term, array(600, 600)) . '><a class="w2dc-grid-item-tall" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
				echo '</div>';
			echo '</div>';
	
			$term = $terms[1];
			echo '<div class="w2dc-col-md-6">';
				echo '<div class="w2dc-row ' . $this->row_classes . '">';
					echo '<div class="w2dc-col-md-12">';
						echo '<div class="' . $this->column_classes . '">';
							echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
	
				echo '<div class="w2dc-row ' . $this->row_classes . '">';
					$term = $terms[2];
					echo '<div class="w2dc-col-md-6">';
						echo '<div class="' . $this->column_classes . '">';
							echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
						echo '</div>';
					echo '</div>';
		
					$term = $terms[3];
					echo '<div class="w2dc-col-md-6">';
						echo '<div class="' . $this->column_classes . '">';
							echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
	
	function rightGridView($terms) {
		echo '<div class="w2dc-row w2dc-right-grid-view ' . $this->row_classes . '">';
			$term = $terms[0];
			echo '<div class="w2dc-col-md-6">';
				echo '<div class="w2dc-row ' . $this->row_classes . '">';
					echo '<div class="w2dc-col-md-12">';
						echo '<div class="' . $this->column_classes . '">';
							echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
	
				echo '<div class="w2dc-row ' . $this->row_classes . '">';
					$term = $terms[1];
					echo '<div class="w2dc-col-md-6">';
						echo '<div class="' . $this->column_classes . '">';
							echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
						echo '</div>';
					echo '</div>';
		
					$term = $terms[2];
					echo '<div class="w2dc-col-md-6">';
						echo '<div class="' . $this->column_classes . '">';
							echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
			$term = $terms[3];
			echo '<div class="w2dc-col-md-6">';
				echo '<div class="' . $this->column_classes . '">';
					echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term, array(600, 600)) . '><a class="w2dc-grid-item-tall" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
	
	function centerGridView($terms) {
		echo '<div class="w2dc-row w2dc-center-grid-view ' . $this->row_classes . '">';
			$term = $terms[0];
			echo '<div class="w2dc-col-md-3">';
				echo '<div class="' . $this->column_classes . '">';
					echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
				echo '</div>';
			echo '</div>';
	
			$term = $terms[1];
			echo '<div class="w2dc-col-md-6">';
				echo '<div class="' . $this->column_classes . '">';
					echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
				echo '</div>';
			echo '</div>';
			
			$term = $terms[2];
			echo '<div class="w2dc-col-md-3">';
				echo '<div class="' . $this->column_classes . '">';
					echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="w2dc-row w2dc-center-grid-view ' . $this->row_classes . '">';
			$term = $terms[3];
			echo '<div class="w2dc-col-md-7">';
				echo '<div class="' . $this->column_classes . '">';
					echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
				echo '</div>';
			echo '</div>';
	
			$term = $terms[4];
			echo '<div class="w2dc-col-md-5">';
				echo '<div class="' . $this->column_classes . '">';
					echo '<div class="' . $this->root_classes . ' ' . $this->item_classes . '" ' . $this->renderFeaturedImage($term) . '><a class="w2dc-grid-item-normal" href="' . get_term_link($term) . '" title="' . $term->name . '"><div class="w2dc-term-label">' . $this->renderIconImage($term) . $term->name . $this->renderTermCount($term) . '</div></a></div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}

class w2dc_categories_view extends w2dc_terms_view {
	public $tax = W2DC_CATEGORIES_TAX;
	public $wrapper_classes = 'w2dc-categories-table';
	public $row_classes = 'w2dc-categories-row';
	public $column_classes = 'w2dc-categories-column';
	public $root_classes = 'w2dc-categories-root';
	public $subterms_classes = 'w2dc-subcategories';
	public $item_classes = 'w2dc-category-item';
	public $term_count_classes = 'w2dc-category-count';
	public $grid_classes = 'w2dc-categories-grid';
	
	public function __construct($params) {
		parent::__construct($params);
		
		$this->view_all_terms = __("View all subcategories ->", "W2DC");
	}
	
	public function getTermIconFile($term_id) {
		if ($file = w2dc_getCategoryIconFile($term_id)) {
			return W2DC_CATEGORIES_ICONS_URL . $file;
		}
	}

	public function getTermImageUrl($term_id, $size) {
		return w2dc_getCategoryImageUrl($term_id, $size);
	}
}

class w2dc_locations_view extends w2dc_terms_view {
	public $tax = W2DC_LOCATIONS_TAX;
	public $wrapper_classes = 'w2dc-locations-table';
	public $row_classes = 'w2dc-locations-row';
	public $column_classes = 'w2dc-locations-column';
	public $root_classes = 'w2dc-locations-root';
	public $subterms_classes = 'w2dc-sublocations';
	public $item_classes = 'w2dc-location-item';
	public $term_count_classes = 'w2dc-location-count';
	public $grid_classes = 'w2dc-locations-grid';
	
	public function __construct($params) {
		parent::__construct($params);
		
		$this->view_all_terms = __("View all sublocations ->", "W2DC");
	}
	
	public function getTermIconFile($term_id) {
		if ($file = w2dc_getLocationIconFile($term_id)) {
			return W2DC_LOCATIONS_ICONS_URL . $file;
		}
	}

	public function getTermImageUrl($term_id, $size) {
		return w2dc_getLocationImageUrl($term_id, $size);
	}
}

?>