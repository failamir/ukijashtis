<?php
/**
 * Plugin Name: PeepSo Monetization: LearnDash
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: <strong>Requires the LearnDash plugin.</strong> Integrates LearnDash LMS into user profiles and streams.
 * Author: PeepSo
 * Author URI: https://301.es/?https://peepso.com
 * Version: 2.7.7
 * Copyright: (c) 2015 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepsolearndash
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class PeepSoLearnDash
{
	private static $_instance = NULL;

    const PLUGIN_EDD = 4401080;
    const PLUGIN_SLUG = 'learndash';

	const PLUGIN_NAME	 = 'Monetization: LearnDash';
	const PLUGIN_VERSION = '2.7.7';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
    const MODULE_ID = 6662;

    const LEARNDASH_MIN = '2.5.8';

    public $widgets = array(
        'PeepSoWidgetLDInstructor',
        'PeepSoWidgetLDGroups',
        //'PeepSoLDWidgetGroupCourses',
    );

    public static function get_instance()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self();
        }
        return (self::$_instance);
    }

    private static function ready_thirdparty() {
        $result = TRUE;

        if(!defined('LEARNDASH_VERSION') || 1 == version_compare(self::LEARNDASH_MIN, LEARNDASH_VERSION)) {
            $result = FALSE;
        }

        return $result;
    }

    private static function ready() {
        return(self::ready_thirdparty() && class_exists('PeepSo') && self::PLUGIN_VERSION.self::PLUGIN_RELEASE == PeepSo::PLUGIN_VERSION.PeepSo::PLUGIN_RELEASE);
    }

	private function __construct()
    {
        /** VERSION INDEPENDENT hooks **/

        // Admin
        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));
            add_filter('peepso_license_config', function($list){
                $list[] = array(
                    'plugin_slug' => self::PLUGIN_SLUG,
                    'plugin_name' => self::PLUGIN_NAME,
                    'plugin_edd' => self::PLUGIN_EDD,
                    'plugin_version' => self::PLUGIN_VERSION
                );
                return ($list);
            }, 160);
        }

        // Compatibility
		add_filter('peepso_all_plugins', function($plugins) {
            $plugins[plugin_basename(__FILE__)] = get_class($this);
            return $plugins;
        });

        // Translations
        add_action('plugins_loaded', array(&$this, 'load_textdomain'));

        // Activation
        register_activation_hook(__FILE__, array(&$this, 'activate'));

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            if (is_admin()) {
                add_action( 'wp_ajax_peepsolearndash_user_courses', array(&$this,'ajax_user_courses') );
                add_action( 'wp_ajax_nopriv_peepsolearndash_user_courses', array(&$this,'ajax_user_courses') );
            }
            add_action('peepso_init', array(&$this, 'init'));
            add_action('peepso_config_after_save-learndash', array(&$this, 'rebuild_cache'));
            add_filter('peepso_widgets', function ($widgets) {
                if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG, 0))
                {
                    return $widgets;
                }

                // register widgets
                foreach (scandir($widget_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR) as $widget) {
                    if (strlen($widget)>=5) require_once($widget_dir . $widget);
                }

                return array_merge($widgets, $this->widgets);
            });
        }
    }

    public function activate() {
        if (!$this->peepso_check()) {
            return (FALSE);
        }

        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
        $install = new PeepSoLearnDashInstall();
        $res = $install->plugin_activation();
        if (FALSE === $res) {
            // error during installation - disable
            deactivate_plugins(plugin_basename(__FILE__));
        }
        return (TRUE);
    }

	public function init()
	{
        if( $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE != get_transient($trans = 'peepso_'.$this::PLUGIN_SLUG.'_version')) {
            // activate returns false in case of missing license
            if($this->activate()) {
                set_transient($trans, $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE);
            }
        }

		PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
		PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

		// Admin hooks
		if (is_admin()) {

			add_action('admin_init', array(&$this, 'peepso_check'));

			add_filter('peepso_admin_config_tabs', function( $tabs ) {
                $tabs['learndash'] = array(
                    'label' => __('LearnDash', 'peepsolearndash'),
                    'icon' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzAwYTJlODt9LmNscy0ye2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPm1hcmtkb3duX2ljb248L3RpdGxlPjxnIGlkPSJCRyI+PHJlY3QgY2xhc3M9ImNscy0xIiB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgcng9IjIwIiByeT0iMjAiLz48L2c+PGcgaWQ9IkxvZ28iPjxwb2x5Z29uIGNsYXNzPSJjbHMtMiIgcG9pbnRzPSI2NS4zOTkgMjQuMzU2IDY0IDI2Ljk2MyA2NCA0Ny43MDQgMTA1LjQ4MSA0Ny43MDQgMTA2Ljg1IDQ1LjA3OSA2NS4zOTkgMjQuMzU2Ii8+PHBvbHlnb24gY2xhc3M9ImNscy0yIiBwb2ludHM9IjYyLjYwMSAyNC4zNTYgNjQgMjYuOTYzIDY0IDQ3LjcwNCAyMi41MTkgNDcuNzA0IDIxLjE1IDQ1LjA3OSA2Mi42MDEgMjQuMzU2Ii8+PHBvbHlnb24gY2xhc3M9ImNscy0yIiBwb2ludHM9IjYyLjYwMSA3MS4wNTIgNjQgNjguNDQ0IDY2Ljk2MyAzOS45NTMgMjEuMTUgNDYuNDUzIDIxLjE1IDUwLjMyOSA2Mi42MDEgNzEuMDUyIi8+PHBvbHlnb24gY2xhc3M9ImNscy0yIiBwb2ludHM9IjY1LjM5OSA3MS4wNTIgNjQgNjguNDQ0IDYxLjAzNyAzNy40NTMgMTA2LjEzMSA0Ni40NTkgMTA2Ljg1IDUwLjMyOSA2NS4zOTkgNzEuMDUyIi8+PGNpcmNsZSBjbGFzcz0iY2xzLTIiIGN4PSIxMDUuNDgxNDgiIGN5PSI0Ny43MDM3IiByPSIyLjk2Mjk2Ii8+PGNpcmNsZSBjbGFzcz0iY2xzLTIiIGN4PSIyMi41MTg1MiIgY3k9IjQ3LjcwMzciIHI9IjIuOTYyOTYiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMiIgY3g9IjY0IiBjeT0iNjguNDQ0NDQiIHI9IjIuOTYyOTYiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMiIgY3g9IjY0IiBjeT0iMjYuOTYyOTYiIHI9IjIuOTYyOTYiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik02OC4wNDc0MSw3Ni4zNTI1OWE1LjkwNDc1LDUuOTA0NzUsMCwwLDEtLjc1NTU1LjMxNDA4LDguNDQ4NjMsOC40NDg2MywwLDAsMS02LjU3MTg1LjAwMyw1LjY4NzU4LDUuNjg3NTgsMCwwLDEtLjc2NzQxLS4zMkwzNy4zMzMzMyw2NS4wNDNWNzcuMzMzMzNjMCw4LjE4MDc0LDExLjkzNzc4LDE0LjgxNDgxLDI2LjY2NjY3LDE0LjgxNDgxczI2LjY2NjY3LTYuNjM0MDgsMjYuNjY2NjctMTQuODE0ODFWNjUuMDQzWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTk5LjU1NTU2LDYwLjYwMTQ4VjkyLjE0ODE1YTIuOTYzLDIuOTYzLDAsMCwwLDUuOTI1OTMsMFY1Ny42Mzg1MloiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xMDguNDQ0NDQsOTIuMTQ4MTVjMCwzLjI3NDA3LTUuOTI1OTMsMTEuODUxODUtNS45MjU5MywxMS44NTE4NXMtNS45MjU5My04LjU3Nzc4LTUuOTI1OTMtMTEuODUxODVhNS45MjU5Myw1LjkyNTkzLDAsMSwxLDExLjg1MTg1LDBaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIDApIi8+PC9nPjwvc3ZnPg==',
                    'tab' => 'learndash',
                    'function' => 'PeepSoConfigSectionLearnDash',
                    'cat'   => 'integrations',
                );

                return $tabs;
            });
		}

		// Front hooks
		if(!is_admin()) {

		    // Profile segment
            $profile_slug = PeepSo::get_option('ld_navigation_profile_slug', 'courses', TRUE);

            add_action('peepso_profile_segment_'.$profile_slug,     function(){
                $pro = PeepSoProfileShortcode::get_instance();
                $this->view_user_id = PeepSoUrlSegments::get_view_id($pro->get_view_user_id());

                echo PeepSoTemplate::exec_template('learndash', 'profile-learndash', array('view_user_id' => $this->view_user_id), TRUE);
            });

            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        }

		// Global hooks

        // Profile tab link
        add_filter('peepso_navigation_profile', function($links){
            if(!PeepSo::get_option('ld_profile_enable')) {
                return $links;
            }

            $links['learndash'] = array(
                'href' => PeepSo::get_option('ld_navigation_profile_slug', 'courses', TRUE),
                'label'=> PeepSo::get_option('ld_navigation_profile_label', __('Courses', 'peepsolearndash'), TRUE),
                'icon' => PeepSo::get_option('ld_navigation_profile_icon', 'ps-icon-folder-open', TRUE),
            );

            return $links;
        });

		/**** ACTIVITY ****/

        // Activity - course enroll
        add_action('learndash_update_course_access', function( $user_id, $course_id, $access_list, $remove ) {

            // Exit if disabled in Config
            if(1 != PeepSo::get_option('ld_activity_enroll', 0)) {
                return;
            }

            // Exit if user is removed from course
            if ( $remove === true ) {
                return;
            }

            // Exit if post already created
            $umeta = 'peepso_ld_post_create_enroll_course_' . $course_id;
            if ( get_user_meta( $user_id, $umeta, TRUE ) == 1 ) {
                return;
            }

            // POST TO STREAM
            $extra = array(
                'module_id' => self::MODULE_ID,
                'act_access'=> PeepSo::get_option('ld_activity_enroll_privacy',PeepSoUser::get_instance($user_id)->get_profile_accessibility()),
            );

            $content=get_permalink($course_id);

            // create an activity item
            $act = PeepSoActivity::get_instance();
            $act_id = $act->add_post($user_id, $user_id, $content, $extra);

            update_post_meta($act_id, '_peepso_learndash_action_type', 'enroll');
            update_post_meta($act_id, '_peepso_learndash_course_id', "$course_id");
            #delete_post_meta($act_id, 'peepso_media');


            // Set usermeta to block post duplication
            add_user_meta( $user_id,  $umeta, 1, TRUE );
        }, 10, 4);

        // Activity - course complete
        add_action('learndash_before_course_completed', function( $data ) {

            // Exit if disabled in Config
            if(1 != PeepSo::get_option('ld_activity_complete', 0)) {
                return;
            }

            $user_id = $data['user']->ID;
            $course_id = $data['course']->ID;

            $course_progress_old = get_user_meta( $user_id, '_sfwd-course_progress', true );

            // Exit if user already has completed the course
            if ( isset( $course_progress_old[ $course_id ]['total'] ) && isset( $course_progress_old[ $course_id ]['completed'] ) && $course_progress_old[ $course_id ]['total'] == $course_progress_old[ $course_id ]['completed'] ) {
                return;
            }

            // Exit if post already created
            $umeta = 'peepso_ld_post_create_complete_course_' . $course_id;
            if ( get_user_meta( $user_id, $umeta, TRUE ) == 1 ) {
                return;
            }

            // POST TO STREAM
            $extra = array(
                'module_id' => self::MODULE_ID,
                'act_access'=> PeepSo::get_option('ld_activity_complete_privacy',PeepSoUser::get_instance($user_id)->get_profile_accessibility()),
            );

            $content=get_permalink($course_id);

            // create an activity item
            $act = PeepSoActivity::get_instance();
            $act_id = $act->add_post($user_id, $user_id, $content, $extra);

            update_post_meta($act_id, '_peepso_learndash_action_type', 'complete');
            update_post_meta($act_id, '_peepso_learndash_course_id', "$course_id");
            #delete_post_meta($act_id, 'peepso_media');


            // Set usermeta to block post duplication
            add_user_meta( $user_id,  $umeta, 1, TRUE );
        }, 10, 1);

        // Activity utils - action text
        add_filter('peepso_activity_stream_action', function($action, $post){
            if (self::MODULE_ID === intval($post->act_module_id)) {

                if('enroll' == get_post_meta($post->ID,  '_peepso_learndash_action_type', true)){
                    $action = PeepSo::get_option('ld_activity_enroll_action_text', __('enrolled in a course', 'peepsolearndash'), TRUE);
                }

                if('complete' == get_post_meta($post->ID,  '_peepso_learndash_action_type', true)){
                    $action = PeepSo::get_option('ld_activity_complete_action_text', __('completed a course', 'peepsolearndash'), TRUE);
                }

                $wp_post = get_post(get_post_meta($post->ID, '_peepso_learndash_course_id', TRUE));
                $action .= sprintf(' <a class="ps-learndash-action-title" href="%s">%s</a>', get_the_permalink($wp_post->ID), $wp_post->post_title);

                global $post;
                $post->post_content ='';
            }

            return ($action);
        }, 10, 2);

        // Activity utils - disable edits
        add_filter('peepso_post_filters', function($options){
            global $post;

            if (self::MODULE_ID === intval($post->act_module_id)) {

                // unset capability for editing post
                if (isset($options['edit'])) {
                    unset($options['edit']);
                }
            }


            return $options;
        }, 10,1);

        // Activity utils - disable repost
        add_filter('peepso_activity_post_actions', function($actions){
            if ($actions['post']->act_module_id == self::MODULE_ID) {
                unset($actions['acts']['repost']);
            }
            return $actions;
        });

        /**** GROUPS ****/

        if(class_exists('PeepSoGroupsPlugin')) {
            add_action('add_meta_boxes', function () {
                add_meta_box('peepso-learndash-course-groups', __('PeepSo Groups - related groups', 'peepsolearndash'), array(&$this, 'metabox_groups'), array('sfwd-courses'), 'advanced', 'low', array());
                add_meta_box('peepso-learndash-course-groups-auto', __('PeepSo Groups automation - course enrolled', 'peepsolearndash'), array(&$this, 'metabox_groups_auto'), array('sfwd-courses'), 'advanced', 'low', array());
            });

            // Groups - course enroll
            add_action('learndash_update_course_access', function( $user_id, $course_id, $access_list, $remove ) {

                // Exit if user is removed from course
                if ( $remove === true ) {
                    return;
                }

                $PeepSoCourseAutoGroups = new PeepSoCourseAutoGroups();

                $groups = $PeepSoCourseAutoGroups->get_groups_by_course($course_id);
                foreach ($groups as $key => $group) {
                    $PeepSoGroupUser = new PeepSoGroupUser($group, $user_id);
                    $PeepSoGroupUser->member_join();
                }
            }, 10, 4);

        }
        /**** VIP ****/
        if(class_exists('PeepSoVIPPlugin')) {
            add_action('add_meta_boxes', function () {
                add_meta_box('peepso-learndash-course-vip-complete', __('PeepSo VIP automation - course completed', 'peepsolearndash'), array(&$this, 'metabox_vip_complete'), array('sfwd-courses'), 'advanced', 'low', array());
            });

            // VIP - course complete
            add_action('learndash_before_course_completed', function( $data ) {

                $user_id = $data['user']->ID;
                $course_id = $data['course']->ID;

                $PeepSoCourseVIP = new PeepSoCourseVIP();
                $vipicons = $PeepSoCourseVIP->get_vipicons_by_course($course_id);

                if (!empty($vipicons)) {
                    $oldicons = get_user_meta( $user_id, 'peepso_vip_user_icon', TRUE );
                    if (!empty($oldicons)) {
                        $vipicons = array_merge($oldicons,$vipicons);
                    }
                    update_user_meta( $user_id, 'peepso_vip_user_icon', $vipicons );
                }
            }, 10, 1);
        }

        /**** METABOX SAVE ****/

        add_action('save_post', function($post_id){

            $post = get_post($post_id);
            if ('sfwd-courses' != $post->post_type || (defined('DOING_AJAX') && DOING_AJAX)) {
                return;
            }

            $PeepSoCourseGroups = new PeepSoCourseGroups();
            $groups = isset($_REQUEST['peepsogroups']) ? $_REQUEST['peepsogroups'] : array();
            $PeepSoCourseGroups->update_course_group($post_id, $groups);

            $PeepSoCourseAutoGroups = new PeepSoCourseAutoGroups();
            $groups = isset($_REQUEST['peepsogroupsauto']) ? $_REQUEST['peepsogroupsauto'] : array();
            $PeepSoCourseAutoGroups->update_course_group($post_id, $groups);

            $PeepSoCourseVIP = new PeepSoCourseVIP();
            $vipicons = isset($_REQUEST['peepsovipicons']) ? $_REQUEST['peepsovipicons'] : array();
            $PeepSoCourseVIP->update_course_vipicons($post_id, $vipicons);
        }, 10,1);
	}

    /**
     * Loads the translation file for the plugin
     */
    public function load_textdomain()
    {
        $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
        load_plugin_textdomain('peepsolearndash', FALSE, $path);
    }

    /**
     * Build AJAX response with user courses
     */
    public function ajax_user_courses()
    {
        ob_start();

        $input = new PeepSoInput();
        $owner = $input->int('user_id');
        $page  = $input->int('page', 1);

        $sort  = $input->value('sort', 'desc', array('asc','desc'));

        $limit = 10;
        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $course_ids = learndash_user_get_enrolled_courses( $owner, array(), true );


        // Course blacklist
        $hide_courses = PeepSo::get_option('ld_profile_hide_courses','');

        if(strlen($hide_courses)) {
            if(strstr($hide_courses, ',')) {
                $hide_courses = explode(',', $hide_courses);
            } else {
                $hide_courses = array($hide_courses);
            }

            $hide_courses = array_map(intval,$hide_courses);
        }

        if(count($course_ids)) {
            foreach($course_ids as $k=>$v) {
                if(in_array($v, $hide_courses)) {
                    unset($course_ids[$k]);
                }
            }
        }

        $courses = array();

        if(count($course_ids)) {
            $args = array(
                'post__in' => $course_ids,
                'post__not_id' => $hide_courses,
                'post_type' => 'sfwd-courses',
                'orderby' => 'post_name',
                'post_status' => 'publish',
                'order' => 'asc',
                'posts_per_page' => $limit,
                'offset' => $offset,
            );

            // Get the posts
            $courses = get_posts($args);
        }


        if (count($courses)) {

            $force_strip_shortcodes = PeepSo::get_option('ld_profile_content_force_strip_shortcodes', 0);

            // Iterate posts
            foreach ($courses as $course) {

                // Choose between short description, excerpt or post_content
                $meta = get_post_meta( $course->ID, '_sfwd-courses', true );
                $course_content = @$meta['sfwd-courses_course_short_description'];

                if(!strlen($course_content)) {
                    $course_content = get_the_excerpt($course->ID);
                }

                if(!strlen($course_content)) {
                    $course_content = $course->post_content;
                }

                $course_content = strip_shortcodes($course_content);

                if($force_strip_shortcodes) {
                    $course_content = preg_replace('/\[.*?\]/', '', $course_content);
                }

                $limit = intval(PeepSo::get_option('ld_profile_content_length',50));
                $course_content = wp_trim_words($course_content, $limit,'&hellip;');

                if(0 == $limit) {
                    $course_content = FALSE;
                }

                PeepSoTemplate::exec_template('learndash', 'course', array('course_content' => $course_content, 'course' => $course));
            }

            $resp['success']		= 1;

            $resp['html']			= ob_get_clean();
        } else {
            $message =  (get_current_user_id() == $owner) ? __('You have not enrolled in any courses yet', 'peepsold') : sprintf(__('%s has not enrolled in any courses yet', 'peepsolearndash'), PeepSoUser::get_instance($owner)->get_firstname());
            $resp['success']		= 0;
            $resp['error'] = PeepSoTemplate::exec_template('profile','no-results-ajax', array('message' => $message), TRUE);
        }

        $resp['page']			= $page;
        header('Content-Type: application/json');
        echo json_encode($resp);
        exit(0);
    }

    public function metabox_groups_auto() {
        ?>
        <p><?php echo __('Automatically add users to PeepSo Groups when they enroll in this course','peepsolearndash');?></p>
        <div style="max-height:350px;overflow:scroll">
        <table class="form-table">
            <tr class="user-admin-color-wrap">
                <td>
                    <fieldset id="peepso-groups" class="scheme-list">
                        <?php
                        $ld_course_id = $_REQUEST['post'];

                        $PeepSoCourseAutoGroups = new PeepSoCourseAutoGroups();
                        $selectedGroup = $PeepSoCourseAutoGroups->get_groups_by_course($ld_course_id);

                        $aGroups = PeepSoGroups::admin_get_groups(0, NULL, NULL, NULL, '', 'all');
                        foreach ($aGroups as $key => $group) {
                            ?>
                            <div class="color-option">
                                <input name="peepsogroupsauto[]" id="peepsogroupsauto<?php echo $key;?>" type="checkbox" value="<?php echo $group->id;?>" class="tog" <?php echo (in_array($group->id, $selectedGroup)) ? ' checked=checked':'';?>>
                                <label for="peepsogroupsauto<?php echo $key;?>">
                                    <img src="<?php echo $group->get_avatar_url();?>" style="width: 64px; height: 64px;">
                                    <div style="float:right;margin:4px;max-width:200px;">
                                    <?php echo $group->name;?>
                                    <br><small>
                                    <?php  if(intval($group->is_open)) { echo '<i class="ps-icon-globe"></i>' . __('Open', 'peepsolearndash'); }  ?>
                                    <?php  if(intval($group->is_closed)) { echo '<i class="ps-icon-lock"></i>'.__('Closed', 'peepsolearndash'); }  ?>
                                    <?php  if(intval($group->is_secret)) { echo '<i class="ps-icon-shield"></i>'.__('Secret', 'peepsolearndash'); }  ?>
                                    </small>
                                    </div>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </fieldset>
                </td>
            </tr>
        </table>
        </div>
        <?php
    }

    public function metabox_groups() {
        ?>
        <p><?php echo __('Show these Groups as "related" to this Course','peepsolearndash');?></p>
        <div style="max-height:350px;overflow:scroll">
        <table class="form-table">
            <tr class="user-admin-color-wrap">
                <td>
                    <fieldset id="peepso-groups" class="scheme-list">
                        <?php
                        $ld_course_id = $_REQUEST['post'];

                        $PeepSoCourseGroups = new PeepSoCourseGroups();
                        $selectedGroup = $PeepSoCourseGroups->get_groups_by_course($ld_course_id);

                        $aGroups = PeepSoGroups::admin_get_groups(0, NULL, NULL, NULL, '', 'all');
                        foreach ($aGroups as $key => $group) {
                            ?>
                            <div class="color-option">
                                <input name="peepsogroups[]" id="peepsogroups<?php echo $key;?>" type="checkbox" value="<?php echo $group->id;?>" class="tog" <?php echo (in_array($group->id, $selectedGroup)) ? ' checked=checked':'';?>>
                                <label for="peepsogroups<?php echo $key;?>">
                                    <img src="<?php echo $group->get_avatar_url();?>" style="width: 64px; height: 64px;">
                                    <div style="float:right;margin:4px;max-width:200px;">
                                        <?php echo $group->name;?>
                                        <br><small>
                                            <?php  if(intval($group->is_open)) { echo '<i class="ps-icon-globe"></i>' . __('Open', 'peepsolearndash'); }  ?>
                                            <?php  if(intval($group->is_closed)) { echo '<i class="ps-icon-lock"></i>'.__('Closed', 'peepsolearndash'); }  ?>
                                            <?php  if(intval($group->is_secret)) { echo '<i class="ps-icon-shield"></i>'.__('Secret', 'peepsolearndash'); }  ?>
                                        </small>
                                    </div>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </fieldset>
                </td>
            </tr>
        </table>
        </div>
        <?php
    }

    public function metabox_vip_complete() {
        ?>
        <p><?php echo __('Assign users these VIP Badges when they complete this course','peepsolearndash');?></p>
        <div style="max-height:350px;overflow:scroll">
            <table class="form-table">
                <tr class="user-admin-color-wrap">
                    <td>
                        <fieldset id="peepso-groups" class="scheme-list">
                            <?php
                            $ld_course_id = $_REQUEST['post'];

                            $PeepSoCourseVIP = new PeepSoCourseVIP();

                            $selectedIcon = $PeepSoCourseVIP->get_vipicons_by_course($ld_course_id);

                            $PeepSoVipIconsModel = new PeepSoVipIconsModel();
                            foreach ($PeepSoVipIconsModel->vipicons as $key => $value) {

                                ?>
                                <div class="color-option">
                                    <input name="peepsovipicons[]" id="peepsovipicons<?php echo $key;?>" type="checkbox" value="<?php echo $value->post_id;?>" class="tog" <?php echo in_array($value->post_id, $selectedIcon) ? ' checked=checked':'';?>>
                                    <label for="vip_icon_<?php echo $key;?>"><?php echo $value->title;?> <?php  if(!intval($value->published)) { echo "<small>(".__('unpublished', 'peepso-vip').")</small>"; }  ?></label>
                                    <img src="<?php echo $value->icon_url;?>" style="width: 16px; height: 16px;">
                                </div>
                                <?php
                            }
                            ?>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    public function enqueue_scripts()
    {
        // dynamic CSS
        $css = 'plugins/learndash/learndash-'.get_transient('peepso_learndash_css').'.css';
        if(!file_exists(PeepSo::get_peepso_dir().$css) ) {
            $this->rebuild_cache();
            $css = 'plugins/learndash/learndash-'.get_transient('peepso_learndash_css').'.css';
        }

        wp_enqueue_style('peepso-learndash-dynamic', PeepSo::get_peepso_uri().$css, array(), self::PLUGIN_VERSION, 'all');
        wp_enqueue_script('peepso-learndash', plugin_dir_url(__FILE__) . 'assets/js/bundle.min.js',
            array('peepso', 'peepso-page-autoload'), self::PLUGIN_VERSION, TRUE);
    }

	/**
	 * Check if PeepSo class is present (PeepSo is installed and activated)
	 * If there is no PeepSo, immediately disable the plugin and display a warning
	 * @return bool
	 */
	public function peepso_check()
	{
        if (!class_exists('PeepSo')) {

            add_action('admin_notices', function(){
                ?>
                <div class="error peepso">
                    <strong>
                        <?php echo sprintf(__('The %s requires the PeepSo plugin to be installed and activated.', 'peepsolearndash'), self::PLUGIN_NAME);?>
                        <a href="plugin-install.php?tab=plugin-information&plugin=peepso-core&TB_iframe=true&width=772&height=291" class="thickbox">
                            <?php echo __('Get it now!', 'peepsolearndash');?>
                        </a>
                    </strong>
                </div>
                <?php
            });

            unset($_GET['activate']);
            deactivate_plugins(plugin_basename(__FILE__));
            return (FALSE);
        }

        if (!self::ready_thirdparty()) {

            add_action('admin_notices', function() {

                ?>
                <div class="error peepso">
                        <?php echo sprintf(__('PeepSo %s requires the LearnDash plugin (%s or newer).', 'peepsolearndash'), self::PLUGIN_NAME, self::LEARNDASH_MIN);?>
                </div>
                <?php
            }, 999);
        }

        // PeepSo.com license check
        if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
            add_action('admin_notices', array(&$this, 'license_notice'));
        }

        if (isset($_GET['page']) && 'peepso_config' == $_GET['page'] && !isset($_GET['tab'])) {
            add_action('admin_notices', array(&$this, 'license_notice_forced'));
        }

        // PeepSo.com new version check
        // since 1.7.6
        if(method_exists('PeepSoLicense', 'check_updates_new')) {
            PeepSoLicense::check_updates_new(self::PLUGIN_EDD, self::PLUGIN_SLUG, self::PLUGIN_VERSION, __FILE__);
        }

		return (TRUE);
	}

    public function license_notice()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG);
    }

    public function license_notice_forced()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG, true);
    }

    public function rebuild_cache()
    {
        // Directory where CSS files are stored
        $path = PeepSo::get_peepso_dir().'plugins'.DIRECTORY_SEPARATOR.'learndash'.DIRECTORY_SEPARATOR;

        if (!file_exists($path) ) {
            @mkdir($path, 0755, TRUE);
        }

        // Try to remove the old file
        $old_file = $path.'learndash-'.get_transient('peepso_learndash_css').'.css';
        @unlink($old_file);

        // New cache
        delete_option('peepso_learndash_css');
        set_transient('peepso_learndash_css', time());

        $image_height = intval(PeepSo::get_option('ld_profile_featured_image_height', 150));
        $box_height = intval(PeepSo::get_option('ld_profile_two_column_height', 350));

        if($image_height < 1) {
            $image_height = 1;
        }

        if($box_height < 1 || !PeepSo::get_option('ld_profile_two_column_enable', 1)) {
            $box_height = 'auto';
        }

        // @todo cache this
        ob_start();
        ?>
        .ps-learndash__course-image {
        height: <?php echo $image_height;?>px;
        }

        .ps-learndash__course-image--left,
        .ps-learndash__course-image--right {
        width: <?php echo $image_height;?>px;
        }

        .ps-learndash__course{
        height: <?php echo $box_height;?>px;
        }
        <?php
        $css = ob_get_clean();

        update_option('peepso_learndash_css', $css);



        $file = $path.'learndash-'.get_transient('peepso_learndash_css').'.css';
        $h = fopen( $file, "a" );
        fputs( $h, $css );
        fclose( $h );
    }
}

PeepSoLearnDash::get_instance();

// EOF