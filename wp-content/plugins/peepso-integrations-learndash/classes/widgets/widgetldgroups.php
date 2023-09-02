<?php


class PeepSoWidgetLDGroups extends WP_Widget
{
    public function __construct( $id = NULL, $name = NULL, $args= NULL ) {

        $id     = ( NULL !== $id )  ? $id   : 'PeepSoWidgetLDGroups';
        $name   = ( NULL !== $name )? $name : __('PeepSo LearnDash: Courses &amp; Groups integration', 'peepsolearndash');
        $args   = ( NULL !== $args )? $args : array('description' => __('Displays information about PeepSo Groups assigned to a LearnDash Course (on a Course page) or LearnDash Courses assigned to a PeepSo Group (on a Group page).', 'peepsolearndash'),);

        parent::__construct(
            $id,
            $name,
            $args
        );
    }

    public function widget( $args, $instance ) {

        if(!class_exists('PeepSoGroupsPlugin')) {
            return;
        }

        /**
         * If we are in Learndash Course context
         */
        if(function_exists('learndash_get_course_id') && learndash_get_course_id() > 0 && class_exists('PeepSoGroupsPlugin')) {
            $PeepSoCourseGroups = new PeepSoCourseGroups();
            $all_groups = $PeepSoCourseGroups->get_groups_by_course(learndash_get_course_id());


            $groups  = array();

            foreach($all_groups as $group_id) {
                $PeepSoGroupUser = new PeepSoGroupUser($group_id);
                if($PeepSoGroupUser->can('access')) {
                    $groups[] = new PeepSoGroup($group_id);
                }
            }

            $instance['groups'] = $groups;
            $instance = apply_filters('peepso_widget_instance', $instance);

            if(count($instance['groups'])) {
                PeepSoTemplate::exec_template('widgets', 'learndash-course-groups', array('args' => $args, 'instance' => $instance));
            }
        }

        /**
         * If we are in a PeepSo Group context and LearnDash is active
         */
        $PeepSoGroupsShortcode = PeepSoGroupsShortcode::get_instance();
        $group_id = $PeepSoGroupsShortcode->group_id;

        if (strlen($group_id) && function_exists('learndash_get_course_id')) {

            $PeepSoCourseGroups = new PeepSoCourseGroups();
            $courses = $PeepSoCourseGroups->get_courses_by_group($group_id);

            $instance['courses'] = $courses;


            $instance = apply_filters('peepso_widget_instance', $instance);
            if(count($instance['courses'])) {
                PeepSoTemplate::exec_template('widgets', 'learndash-group-courses', array('args' => $args, 'instance' => $instance));
            }
        }

    }

    public function form( $instance ) {

        $instance['fields'] = array(
            // general
            'limit'     => FALSE,
            'title'     => FALSE,

            // peepso
            'integrated'   => FALSE,
            'position'  => FALSE,
            'hideempty' => FALSE,
        );

        if (!isset($instance['title_cg'])) {
            $instance['title_cg'] = __('Groups related to this Course', 'peepsolearndash');
        }

        if (!isset($instance['title_gc'])) {
            $instance['title_gc'] = __('Courses related to this Group', 'peepsolearndash');
        }

        $this->instance = $instance;

        $settings =  apply_filters('peepso_widget_form', array('html'=>'', 'that'=>$this,'instance'=>$instance));

        ob_start();
        $title_cg = !empty($instance['title_cg']) ? $instance['title_cg'] : '';
        $title_gc = !empty($instance['title_gc']) ? $instance['title_gc'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title_cg'); ?>">
                <?php echo __('Title (Course Page)', 'peepsolearndash'); ?>
                <input value="<?php echo $title_cg;?>" class="widefat" id="<?php echo $this->get_field_id('title_cg'); ?>"
                       name="<?php echo $this->get_field_name('title_cg'); ?>">
            </label>

            <small>
                <?php echo __('Title when on PeepSo Group', 'peepsolearndash');?>
            </small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('title_gc'); ?>">
                <?php echo __('Title (Group Page)', 'peepsolearndash'); ?>
                <input value="<?php echo $title_gc;?>" class="widefat" id="<?php echo $this->get_field_id('title_gc'); ?>"
                        name="<?php echo $this->get_field_name('title_gc'); ?>">
            </label>

            <small>
                <?php echo __('Title when on PeepSo Group', 'peepsolearndash');?>
            </small>
        </p>

        <?php
        $settings['html'] .= ob_get_clean();
        echo $settings['html'];
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title_cg']    = isset($new_instance['title_cg']) ? strip_tags($new_instance['title_cg']) : '';
        $instance['title_gc']    = isset($new_instance['title_gc']) ? strip_tags($new_instance['title_gc']) : '';

        return $instance;
    }
}

// EOF