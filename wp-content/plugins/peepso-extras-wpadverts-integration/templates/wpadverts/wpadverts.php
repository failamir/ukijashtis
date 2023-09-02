<div class="peepso">
	<?php 
	PeepSoTemplate::exec_template('general','navbar'); 
	if (PeepSo::get_option('wpadverts_allow_guest_access_to_classifieds', 0) === 0) {
		PeepSoTemplate::exec_template('general', 'register-panel');
	}
	if (get_current_user_id() || PeepSo::get_option('wpadverts_allow_guest_access_to_classifieds', 0) === 1) { ?>
		<section id="mainbody" class="ps-page-unstyled">
			<section id="component" role="article" class="ps-clearfix">
				<div class="ps-page-actions">
					<a class="ps-btn ps-btn-small" href="<?php echo PeepSo::get_page('wpadverts') . (PeepSo::get_option('disable_questionmark_urls', 0) === 0 ? '?' : '') . 'create/';?>">
						<?php echo __('Create', 'peepso-wpadverts');?>
					</a>

				</div>

				<h4 class="ps-page-title"><?php echo __('Classifieds', 'peepso-wpadverts'); ?></h4>

				<div class="ps-tabs__wrapper">
					<div class="ps-tabs ps-tabs--arrows">
						<div class="ps-tabs__item current"><a href="<?php echo PeepSo::get_page('wpadverts');?>"><?php echo __('Classifieds', 'peepso-wpadverts'); ?></a></div>
						<div class="ps-tabs__item"><a href="<?php echo PeepSo::get_page('wpadverts').'?category/';?>"><?php echo __('Classifieds Categories', 'peepso-wpadverts'); ?></a></div>
					</div>
				</div>

				<form class="ps-form ps-form-search" role="form" name="form-peepso-search" onsubmit="return false;">
					<div class="ps-form-row">
						<input placeholder="<?php echo __('Start typing to search...', 'peepso-wpadverts');?>" type="text" class="ps-input ps-js-classifieds-query" name="query" value="<?php echo esc_attr($search); ?>" />
					</div>
					<a href="#" class="ps-form-search-opt" onclick="return false;">
						<span class="ps-icon-cog"></span>
					</a>
				</form>
				<?php
				$default_sorting = '';
				if(!strlen(esc_attr($search)))
				{
					$default_sorting = PeepSo::get_option('classifieds_default_sorting','id');
				}

				?>
				<div class="ps-js-page-filters">
					<div class="ps-page-filters ps-page-filters--classifieds">
						<div class="ps-filters-row">
							<label><?php echo __('Location', 'peepso-wpadverts'); ?></label>
							<input placeholder="<?php echo __('Start typing to search...', 'peepso-wpadverts');?>" type="text" class="ps-input ps-js-classifieds-location" name="location" value="<?php echo esc_attr($location); ?>" />
						</div>

						<?php if($adverts_categories) { ?>

							<div class="ps-filters-row">
								<label><?php echo __('Category', 'peepso-wpadverts'); ?></label>
								<select class="ps-select ps-js-classifieds-category" style="margin-bottom:5px">
									<option value="0"><?php echo __('All categories', 'peepso-wpadverts'); ?></option>
									<?php
									if(count($adverts_categories)) {
										$PeepSoInput = new PeepSoInput();
										$category = $PeepSoInput->int('category', 0);
										foreach($adverts_categories as $id=>$cat) {
											$selected = "";
											if($cat['value']==$category) {
												$selected = ' selected="selected"';
											}
											echo "<option value=\"{$cat['value']}\"{$selected}>".str_repeat(' - ', $cat['depth'])."{$cat['text']}</option>";
										}
									}
									?>
								</select>
							</div>

						<?php } // ENDIF ?>

					</div>
				</div>

				<?php
				// Get columns number from WPAdverts config

				$columns = "";

				if (class_exists('Adverts')) {
					if (PeepSo::get_option('wpadverts_display_ads_as') == "2") { // if Grid view selected 
						$columns = 'ps-classifieds__grid ps-classifieds__grid--' . adverts_config( 'config.ads_list_default__columns' );
					}
				}
				?>

				<div class="ps-clearfix mb-20"></div>
				<div class="ps-classifieds <?php echo $columns; ?> ps-clearfix ps-js-classifieds"></div>
				<div class="ps-scroll ps-clearfix ps-js-classifieds-triggerscroll">
					<img class="post-ajax-loader ps-js-classifieds-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
				</div>
			</section>
		</section>
	<?php } ?>
</div><!--end row-->

<?php

if(get_current_user_id()) {
	PeepSoTemplate::exec_template('activity', 'dialogs');
}
