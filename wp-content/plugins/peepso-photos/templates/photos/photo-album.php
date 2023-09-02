<div class="peepso ps-page-profile">
    <?php PeepSoTemplate::exec_template('general', 'navbar'); ?>

    <?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'photos')); ?>

    <section id="mainbody" class="ps-page-unstyled">
        <section id="component" role="article" class="ps-clearfix">
            <div class="ps-page__actions">
                <a class="ps-btn ps-btn-small" href="<?php echo $photos_url; ?>"><i class="ps-icon-angle-left"></i> <?php echo __('Back to Photos', 'picso'); ?></a>
            </div>
            <h4 class="ps-page-title">
                <?php echo sprintf (__('%s Album', 'picso'), __($the_album->pho_album_name, 'picso')); ?>
            </h4>
            <div class="ps-page-filters">
                <select class="ps-select ps-full ps-js-photos-sortby">
                    <option value="desc"><?php echo __('Newest first', 'picso');?></option>
                    <option value="asc"><?php echo __('Oldest first', 'picso');?></option>
                </select>
            </div>

            <div class="ps-clearfix mb-20"></div>
            <div class="ps-photos ps-js-photos ps-js-photos--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>"></div>
            <div class="ps-scroll ps-js-photos-triggerscroll ps-js-photos-triggerscroll--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>">
                <img class="post-ajax-loader ps-js-photos-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
            </div>
            <div class="ps-clearfix mb-20"></div>
        </section><!--end component-->
    </section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
