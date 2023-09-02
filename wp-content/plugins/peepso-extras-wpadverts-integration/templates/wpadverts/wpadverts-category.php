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

				<h4 class="ps-page-title"><?php echo __('Classifieds Categories', 'peepso-wpadverts'); ?></h4>

				<div class="ps-tabs__wrapper">
					<div class="ps-tabs ps-tabs--arrows">
						<div class="ps-tabs__item"><a href="<?php echo PeepSo::get_page('wpadverts');?>"><?php echo __('Classifieds', 'peepso-wpadverts'); ?></a></div>
						<div class="ps-tabs__item current"><a href="<?php echo PeepSo::get_page('wpadverts').'/?category/';?>"><?php echo __('Classifieds Categories', 'peepso-wpadverts'); ?></a></div>
					</div>
				</div>

				<?php echo $ads_category;?>
			</section>
		</section>
	<?php } ?>
</div><!--end row-->

<?php

if(get_current_user_id()) {
	PeepSoTemplate::exec_template('activity', 'dialogs');
}
