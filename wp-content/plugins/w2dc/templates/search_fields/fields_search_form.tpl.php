<?php if ($search_fields || $search_fields_advanced): ?>
<div class="w2dc-clearfix"></div>
<script>
	(function($) {
		"use strict";
	
		$(function() {
			var fields_in_categories = new Array();
	<?php
	foreach ($search_fields_all AS $search_field): 
		if (!$search_field->content_field->isCategories() || $search_field->content_field->categories === array()): ?>
			fields_in_categories[<?php echo $search_field->content_field->id; ?>] = [];
	<?php else: ?>
			fields_in_categories[<?php echo $search_field->content_field->id; ?>] = [<?php echo implode(',', $search_field->content_field->categories); ?>];
	<?php endif; ?>
	<?php endforeach; ?>
	
			$(document).on("change", ".selected_tax_<?php echo W2DC_CATEGORIES_TAX; ?>", function() {
				hideShowFields($(this).val());
			});
	
			if ($(".selected_tax_<?php echo W2DC_CATEGORIES_TAX; ?>").length > 0) {
				hideShowFields($(".selected_tax_<?php echo W2DC_CATEGORIES_TAX; ?>").val());
			} else {
				hideShowFields(0);
			}
	
			function hideShowFields(id) {
				var selected_categories_ids = [id];
	
				$(".w2dc-field-search-block-<?php echo $search_form_id; ?>").hide();
				$.each(fields_in_categories, function(index, value) {
					var show_field = false;
					if (value != undefined) {
						if (value.length > 0) {
							var key;
							for (key in value) {
								var key2;
								for (key2 in selected_categories_ids)
									if (value[key] == selected_categories_ids[key2])
										show_field = true;
							}
						}
						if ((value.length == 0 || show_field) && $(".w2dc-field-search-block-"+index+"_<?php echo $search_form_id; ?>").length)
							$(".w2dc-field-search-block-"+index+"_<?php echo $search_form_id; ?>").show();
					}
				});
			}
		});
	})(jQuery);
</script>

<div id="w2dc_standard_search_fields_<?php echo $search_form_id; ?>" class="w2dc-search-fields-block">
	<?php foreach ($search_fields AS $search_field): ?>
	<div class="w2dc-search-content-field">
		<?php $search_field->renderSearch($search_form_id, $columns, $defaults); ?>
	</div>
	<?php endforeach; ?>
</div>
<?php if ($is_advanced_search_panel): ?>
<input type="hidden" name="use_advanced" id="use_advanced_<?php echo $search_form_id; ?>" value="<?php echo (int)$advanced_open; ?>" autocomplete="off" />
<div id="w2dc_advanced_search_fields_<?php echo $search_form_id; ?>" <?php if (!$advanced_open): ?>style="display: none;"<?php endif; ?> class="w2dc-search-fields-block">
	<?php foreach ($search_fields_advanced AS $search_field): ?>
	<div class="w2dc-search-content-field">
		<?php $search_field->renderSearch($search_form_id, $columns, $defaults); ?>
	</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>