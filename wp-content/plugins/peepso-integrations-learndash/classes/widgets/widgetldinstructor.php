<?php


class PeepSoWidgetLDInstructor extends WP_Widget
{
    public function __construct( $id = NULL, $name = NULL, $args= NULL ) {

        $id     = ( NULL !== $id )  ? $id   : 'PeepSoWidgetLDInstructor';
        $name   = ( NULL !== $name )? $name : __('PeepSo LearnDash: About Course Instructor', 'peepsolearndash');
        $args   = ( NULL !== $args )? $args : array('description' => __('Displays PeepSo details about the Course Instructor', 'peepsolearndash'),);

        parent::__construct(
            $id,
            $name,
            $args
        );
    }

    public function widget( $args, $instance ) {

        /**
         * Widget shows only if:
         * LearnDash Plugin is present

         * User is logged in
         * We are looking at a LearnDash Course, Lesson etc
         * The Course Instructor (post author) is not the same as current user
         */
        if(function_exists('learndash_get_course_id') && learndash_get_course_id() > 0) {
            $instance['course'] = get_post(learndash_get_course_id());
            $instance = apply_filters('peepso_widget_instance', $instance);
            PeepSoTemplate::exec_template('widgets', 'learndash-instructor', array('args' => $args, 'instance' => $instance));
        }
    }

    public function form( $instance ) {

        $instance['fields'] = array(
            // general
            'limit'     => FALSE,
            'title'     => TRUE,

            // peepso
            'integrated'   => FALSE,
            'position'  => FALSE,
            'hideempty' => FALSE,
        );

        if (!isset($instance['title'])) {
            $instance['title'] = __('About The Course Instructor', 'peepsolearndash');
        }

        $this->instance = $instance;

        $settings =  apply_filters('peepso_widget_form', array('html'=>'', 'that'=>$this,'instance'=>$instance));

        ob_start();
        $instructor_override = !empty($instance['instructor_override']) ? $instance['instructor_override'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('instructor_override'); ?>">
                <?php echo __('Override instructor ID', 'peepsolearndash'); ?>
                <input value="<?php echo $instructor_override;?>" class="widefat" id="<?php echo $this->get_field_id('instructor_override'); ?>"
                        name="<?php echo $this->get_field_name('instructor_override'); ?>">
            </label>

            <?php

            if($instructor_override && !get_user_by('id', $instructor_override)) {
                echo '<p style="color:red">' . __('Invalid Instructor ID', 'peepsolearndash') . '</p>';
            }

            ?>

            <small>
                <?php echo __('Leave empty to treat the Course Author as Course Instructor', 'peepsolearndash');?>
            </small>
        </p>

        <?php
        $enablechat = isset($instance['enablechat']) ? $instance['enablechat'] : 1;

        if(class_exists('PeepSoMessagesPlugin')) {
        ?>
            <p>
                <input name="<?php echo $this->get_field_name('enablechat'); ?>" class="ace ace-switch ace-switch-2"
                       id="<?php echo $this->get_field_id('enablechat'); ?>" type="checkbox" value="1"
                    <?php if(1 === $enablechat) echo ' checked="" ';?>>
                <label class="lbl" for="<?php echo $this->get_field_id('enablechat'); ?>">
                    <?php echo __('Enable "Chat" Button', 'peepsolearndash'); ?>
                </label>
            </p>
        <?php } else { ?>
            <input type="hidden" name="<?php echo $this->get_field_name('enablechat'); ?>" value="<?php echo $enablechat;?>" />
        <?php } ?>

        <p>
            <small>
                <?php echo __('Warning: this widget will print Course Instructors "about me" profile field without checking the field privacy.', 'peepsolearndash'); ?>
            </small>
        </p>

        <?php
        $settings['html'] .= ob_get_clean();
        echo $settings['html'];
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['instructor_override']        = isset($new_instance['instructor_override']) ? (int) $new_instance['instructor_override'] : '';
        $instance['enablechat']   = isset($new_instance['enablechat']) ? (int) $new_instance['enablechat'] : 0;

        return $instance;
    }
}

// EOF