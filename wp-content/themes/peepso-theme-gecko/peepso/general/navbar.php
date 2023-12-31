<!-- ********* MODIFIED SINCE 2.7.0.0 ********* -->
<?php
// SETTINGS
// check what's the profile layout
global $wp_query;

$profile_layout = get_post_meta($wp_query->post->ID, 'gc_profile_layout', true);

if (!$profile_layout || $profile_layout == "default") :
?>
<!-- end: 2.7.0.0 -->
<?php
if(0==PeepSo::get_option('disable_navbar', 0)) {
    PeepSoTemplate::exec_template('general', 'js-unavailable');
    $PeepSoGeneral = PeepSoGeneral::get_instance();
    ?>

    <?php if (is_user_logged_in()) { ?>
        <div class="ps-toolbar ps-toolbar--desktop js-toolbar">
            <div class="ps-toolbar__menu">
                <?php echo $PeepSoGeneral->render_navigation('primary'); ?>
            </div>
            <div class="ps-toolbar__notifications">
                <?php echo $PeepSoGeneral->render_navigation('secondary'); ?>
            </div>
        </div>

        <div class="ps-toolbar">
            <div class="ps-toolbar__menu">
                <span>
                    <a href="#" class="ps-toolbar__toggle" onclick="return false;">
                        <i class="ps-icon-menu"></i>
                    </a>
                </span>
                <?php echo $PeepSoGeneral->render_navigation('mobile-secondary'); ?>
            </div>

            <div id="ps-main-nav" class="ps-toolbar__submenu">
                <?php echo $PeepSoGeneral->render_navigation('mobile-primary'); ?>
            </div>
        </div>
    <?php }
}

do_action('peepso_action_render_navbar_after');
?>

<!-- ********* MODIFIED SINCE 2.7.0.0 ********* -->
<?php endif; ?>
<!-- end: 2.7.0.0 -->
