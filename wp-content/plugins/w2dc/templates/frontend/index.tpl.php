		<div class="w2dc-content w2dc-index-page">
			<?php w2dc_renderMessages(); ?>
			
			<?php $frontpanel_buttons = new w2dc_frontpanel_buttons(); ?>
			<?php $frontpanel_buttons->display(); ?>

			<?php
			if (get_option('w2dc_main_search'))
				$frontend_controller->search_form->display();
			?>

			<?php if (get_option('w2dc_show_categories_index')): ?>
			<?php w2dc_displayCategoriesTable()?>
			<?php endif; ?>

			<?php if (get_option('w2dc_show_locations_index')): ?>
			<?php w2dc_displayLocationsTable(); ?>
			<?php endif; ?>

			<?php if (get_option('w2dc_map_on_index')): ?>
			<?php $frontend_controller->map->display(false, false, get_option('w2dc_enable_radius_search_circle'), get_option('w2dc_enable_clusters'), true, true, false, get_option('w2dc_default_map_height'), false, 10, w2dc_getSelectedMapStyleName(), get_option('w2dc_search_on_map'), get_option('w2dc_enable_draw_panel'), false, get_option('w2dc_enable_full_screen'), get_option('w2dc_enable_wheel_zoom'), get_option('w2dc_enable_dragging_touchscreens'), get_option('w2dc_center_map_onclick')); ?>
			<?php endif; ?>

			<?php if (get_option('w2dc_listings_on_index')): ?>
			<?php w2dc_renderTemplate('frontend/listings_block.tpl.php', array('frontend_controller' => $frontend_controller)); ?>
			<?php else: ?>
			<div class="w2dc-content" id="w2dc-controller-<?php echo $frontend_controller->hash; ?>" data-controller-hash="<?php echo $frontend_controller->hash; ?>"></div>
			<?php endif; ?>
		</div>