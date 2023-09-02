<div class="peepso ps-page-profile">
	<?php PeepSoTemplate::exec_template('general','navbar'); ?>

	<?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'wpadverts')); ?>

	<section id="mainbody" class="ps-page-unstyled ps-page--classifieds">
		<section id="component" role="article" class="ps-clearfix">

            <?php if(get_current_user_id()) { ?>
			<div class="ps-page-actions">
				<a class="ps-btn ps-btn-small" href="<?php echo PeepSo::get_page('wpadverts') . (PeepSo::get_option('disable_questionmark_urls', 0) === 0 ? '?' : '') . 'create/';?>">
					<?php echo __('Create', 'peepso-wpadverts');?>
				</a>
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
			<div class="ps-clearfix ps-classifieds <?php echo $columns; ?> ps-js-classifieds ps-js-classifieds--<?php echo apply_filters('peepso_user_profile_id', 0); ?>"></div>
			<div class="ps-scroll ps-classifieds-scroll ps-js-classifieds-triggerscroll ps-js-classifieds-triggerscroll--<?php echo apply_filters('peepso_user_profile_id', 0); ?>">
				<img class="post-ajax-loader ps-js-classifieds-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
			</div>
            <?php } else {
                PeepSoTemplate::exec_template('general','login-profile-tab');
            }?>
		</section><!--end component-->
	</section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
