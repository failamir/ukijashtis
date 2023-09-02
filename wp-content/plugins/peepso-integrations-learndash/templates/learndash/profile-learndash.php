<div class="peepso ps-page-profile">
    <?php PeepSoTemplate::exec_template('general','navbar'); ?>

    <?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'learndash')); ?>

    <section id="mainbody" class="ps-page-unstyled ps-learndash-profile">
    <section id="component" role="article" class="ps-clearfix">


            <?php
            if(get_current_user_id()) {?>

                <div class="ps-learndash <?php echo PeepSo::get_option('ld_profile_two_column_enable', 0) ? 'ps-learndash--half': '' ?>
                                    ps-js-learndash ps-js-learndash--<?php echo apply_filters('peepso_user_profile_id', 0); ?>"
                     style="margin-bottom:10px"></div>

                <div class="ps-scroll ps-clearfix ps-js-learndash-triggerscroll">
                    <img class="post-ajax-loader ps-js-learndash-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
                </div>

            <?php

            } else {
                PeepSoTemplate::exec_template('general','login-profile-tab');
            }
            ?>

    </section><!--end component-->
    </section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>




