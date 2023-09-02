<?php
echo $args['before_widget'];
?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
    <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
        <?php
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
        ?>
    </div>
    <?php
    $instructor_id = ($instance['instructor_override'] > 0) ? $instance['instructor_override'] : $instance['course']->post_author;
    $instructor = PeepSoUser::get_instance($instructor_id);


    ?>

    <div class="ps-ld__instructor">
        <div class="ps-ld__instructor-header">
            <div class="ps-avatar">
                <a href="<?php echo $instructor->get_profileurl();?>">
                    <img alt="<?php echo sprintf(__('%s - avatar', 'peepsolearndash'), $instructor->get_fullname());?>" title="<?php echo $instructor->get_profileurl();?>" src="<?php echo $instructor->get_avatar();?>">
                </a>
            </div>
            <?php
            ob_start();
            do_action('peepso_action_render_user_name_before', $instructor_id);
            $before_fullname = ob_get_clean();

            ob_start();
            do_action('peepso_action_render_user_name_after', $instructor_id);
            $after_fullname = ob_get_clean();

            ?>

            <?php echo $before_fullname; ?>
            <a class="ps-ld__instructor-name" href="<?php echo $instructor->get_profileurl();?>">
                <?php echo $instructor->get_fullname(); ?>
            </a>
            <?php echo $after_fullname; ?>
        </div>

        <div class="ps-ld__instructor-desc">
            <?php

            if(strlen($about = trim(PeepSoField::get_field_by_id('description', $instructor_id)->value))) {
                echo wpautop($about);
            }

            ?>
        </div>
    </div>



    <?php if($instance['enablechat'] && $instructor_id != get_current_user_id() && class_exists('PeepSoMessagesPlugin') && sfwd_lms_has_access( $instance['course']->ID, get_current_user_id() ) && PeepSo::check_permissions($instructor_id, PeepSoMessagesPlugin::PERM_SEND_MESSAGE, get_current_user_id())) { // PeepSo Chat?>


        <a class="ps-ld__instructor-chat ps-btn" href="#" onclick="peepso.messages.new_message(<?php echo $instructor_id ;?>, false, this); return false;">
            <i class="ps-icon-comments"></i> <?php echo sprintf(__('Start a chat with %s', 'peepsolearndash'), $instructor->get_firstname());?>
        </a>

    <?php } // PeepSo Chat ?>

</div>
<?php
echo $args['after_widget'];
// EOF