<?php
/**
 * Plugin Name: PeepSo Integrations: BadgeOS
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: <strong>Requires the BadgeOS plugin.</strong> Bridge BadgeOS and PeepSo points and badge systems
 * Tags: peepso, badgeos, integration
 * Author: PeepSo
 * Version: 2.7.7
 * Author URI: https://301.es/?https://peepso.com
 * Copyright: (c) 2017 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: badgeos-peepso
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */


class BadgeOS_PeepSo {

    private static $_instance = NULL;

    const PLUGIN_EDD = 73621;
    const PLUGIN_SLUG = 'PeepSo-badgeOS';

    const PLUGIN_NAME    = 'Integrations: BadgeOS';
    const PLUGIN_VERSION = '2.7.7';
    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
    const MODULE_ID      = 1000;

    /**
     * BadgeOS compatibility version
     */
    const BADGEOS_VER_MIN = '1.4.0';

    // post meta key for photo type (avatar/cover)
    const POST_META_KEY_BADGEOS_TYPE          = '_peepso_badgeos_type';
    const POST_META_KEY_BADGEOS_TYPE_ACHIEVEMENT   = '_peepso_badgeos_type_achievement';
    const POST_META_KEY_BADGEOS_ACHIEVEMENT_ID     = '_peepso_badgeos_achievement_id';

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2U3NGU4MTt9LmNscy0yLC5jbHMtM3tmaWxsOiNmZmY7fS5jbHMtMntzdHJva2U6I2ZmZjtzdHJva2UtbWl0ZXJsaW1pdDoxMDtzdHJva2Utd2lkdGg6NHB4O308L3N0eWxlPjwvZGVmcz48dGl0bGU+YmFkZ2Vvc19pY29uPC90aXRsZT48ZyBpZD0iQkciPjxyZWN0IGNsYXNzPSJjbHMtMSIgd2lkdGg9IjEyOCIgaGVpZ2h0PSIxMjgiIHJ4PSIyMCIgcnk9IjIwIi8+PC9nPjxnIGlkPSJMb2dvIj48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0yNy40OCw3OS43Yy0uMzUsMC0uNTYtLjE3LS41Ni0uNTJWNzdjMC0uMzUuMTctLjUyLjU2LS41MmgzLjNWNTEuNzVoLTMuM2MtLjM1LDAtLjU2LS4xNy0uNTYtLjUyVjQ5YzAtLjM1LjE3LS41Mi41Ni0uNTJoMTRjNiwwLDkuMTcsMy40OCw5LjE3LDguMzVhNy4zMSw3LjMxLDAsMCwxLTMuODcsNi41NmM0LjE3LDEuMjIsNS41Niw0LDUuNTYsNy4zOUM1Mi4zNSw3Niw0OSw3OS43LDQxLjUyLDc5LjdaTTQxLjIyLDYyLjE4YzIuNjUsMCw1LjYxLTEuNDMsNS42MS01LDAtNC4yNi0zLjE3LTUuMzktNi4wOS01LjM5SDM0LjU3VjYyLjE4Wm0wLDE0LjI2YzQuMzUsMCw3LjMtMS43NCw3LjMtNiwwLTMuNjUtMi41Ni01LjQzLTctNS40M0gzNC41N1Y3Ni40NFoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cGF0aCBjbGFzcz0iY2xzLTMiIGQ9Ik01Ny4yMiw1OS42N0M1Ny4yMiw1Miw2Mi44Nyw0Ny41Miw2OSw0Ny41MlM4MC42NCw1Miw4MC42NCw1OS42Nyw3NS41Niw3Mi4xMiw2OSw3Mi4xMiw1Ny4yMiw2Ny4wNyw1Ny4yMiw1OS42N1ptMjAuMjkuMWMwLTYuNDYtNC41NC05LjU5LTguNTUtOS41OXMtOC41OCwzLjEzLTguNTgsOS41OSw0LjM0LDkuNjIsOC41OCw5LjYyUzc3LjUxLDY2LjA5LDc3LjUxLDU5Ljc3WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIvPjxwYXRoIGNsYXNzPSJjbHMtMyIgZD0iTTg0LjM0LDcxLjg4Yy0uMywwLS40NC0uMTMtLjQ0LS40VjYzLjZhLjQxLjQxLDAsMCwxLC40NC0uNDRoMi4xMmEuNC40LDAsMCwxLC40LjQ0di43MWE3LjI1LDcuMjUsMCwwLDAsNy4yLDUuMDhjMy40MywwLDUuNTItMS40OCw1LjUyLTQuMDcsMC01LjU1LTE0Ljk0LTIuNjItMTQuOTQtMTEuMjcsMC0zLjY3LDMtNi41Myw3Ljg3LTYuNTMsMy40NywwLDUuNDgsMS4xOCw2LjY2LDIuNjZsLS4wNy0yLjA5YS4zOC4zOCwwLDAsMSwuNDQtLjRoMS44OGEuMzYuMzYsMCwwLDEsLjQuNHY3LjMzYS4zNi4zNiwwLDAsMS0uNC40SDk5LjMyYS4zNi4zNiwwLDAsMS0uNC0uNHYtLjk0YzAtMS4zOC0xLjU4LTQuMjEtNi4wOS00LjIxLTMsMC00Ljk1LDEuNjItNC45NSwzLjc3LDAsNS44NSwxNS4yMSwzLDE1LjIxLDExLjI3LDAsMy42My0yLjQ5LDYuOC04LjQ0LDYuOC00LjY4LDAtNy4xNy0yLjUyLTgtMy45NGwwLDMuM2EuMzYuMzYsMCwwLDEtLjQuNFoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cmVjdCBjbGFzcz0iY2xzLTMiIHg9IjU4LjY5IiB5PSI3Ny45OSIgd2lkdGg9IjQ0LjI0IiBoZWlnaHQ9IjIuNDkiIHJ4PSIxLjI1IiByeT0iMS4yNSIvPjwvZz48L3N2Zz4=';


    private static function ready_thirdparty() {

        $result = FALSE;

        if ( class_exists('BadgeOS') && version_compare( BadgeOS::$version, self::BADGEOS_VER_MIN, '>=' ) ) {
            $result = TRUE;
        }

        return $result;
    }

    private static function ready() {
        return(self::ready_thirdparty() && class_exists('PeepSo') && self::PLUGIN_VERSION.self::PLUGIN_RELEASE == PeepSo::PLUGIN_VERSION.PeepSo::PLUGIN_RELEASE);
    }

    private function __construct() {

        /** VERSION INDEPENDENT hooks **/

        // Admin
        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));
            add_filter('peepso_license_config', function($list) {
                $list[] = array(
                    'plugin_slug' => self::PLUGIN_SLUG,
                    'plugin_name' => self::PLUGIN_NAME,
                    'plugin_edd' => self::PLUGIN_EDD,
                    'plugin_version' => self::PLUGIN_VERSION
                );
                return ($list);
                return $list;
            });
        }

        // Compatibility
        add_filter('peepso_all_plugins', function($plugins){
            $plugins[plugin_basename(__FILE__)] = get_class($this);
            return $plugins;
        });

        // Translations
        add_action('plugins_loaded', function(){
            $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
            load_plugin_textdomain('badgeos-peepso', FALSE, $path);
        });

        // Activation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            if (is_admin()) {
                // badgesos step ui
                add_action( 'badgeos_steps_ui_html_after_trigger_type', array(&$this, 'action_badgeos_step_peepso_trigger_select'), 10, 2 );
                add_action( 'badgeos_steps_ui_html_after_trigger_type', array(&$this, 'action_badgeos_peepso_step_group_select'), 10, 2 );
                add_action( 'badgeos_steps_ui_html_after_trigger_type', array(&$this, 'action_badgeos_peepso_step_pmp_level_select'), 10, 2 );
                add_filter( 'badgeos_save_step', array(&$this, 'badgeos_peepso_save_step'), 10, 3 );
                add_action( 'admin_footer', array(&$this, 'badgeos_bp_step_js') );
            } 

            add_action('peepso_init', array(&$this, 'init'));


            // awarding badges to user
            add_action('badgeos_award_achievement', array(&$this, 'badgeos_award_achievement_peepso_activity'), 10, 5 );

            add_filter( 'badgeos_activity_triggers', array(&$this, 'badgeos_peepso_activity_triggers' ));
            add_filter( 'badgeos_get_step_requirements', array(&$this, 'badgeos_peepso_step_requirements'), 10, 2 );

            // PeepSo Action Hooks
            $this->peepso_triggers = array(
                __( 'PeepSo Activity', 'badgeos-peepso' ) => array(
                    'peepso_user_after_change_avatar' => array('label' => __('Change Profile Avatar.', 'badgeos-peepso'), 'num_args' => 4),
                    'peepso_user_after_change_cover' => array('label' => __('Change Profile Cover.', 'badgeos-peepso'), 'num_args' => 2),
                    'peepso_register_approved' => array('label' => __('Admin Approves User Account.', 'badgeos-peepso'), 'num_args' => 1),
                    'peepso_activity_after_add_post' => array('label' => __( 'Write a post / status update.', 'badgeos-peepso' ), 'num_args' => 2),
                    'peepso_activity_after_add_comment' => array('label' => __( 'Comment on a post / status update.', 'badgeos-peepso' ), 'num_args' => 2),
                ),
            );
        }
    }

    /**
     * Retrieve singleton class instance
     * @return BadgeOS-PeepSo instance
     */
    public static function get_instance()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self();
        }
        return (self::$_instance);
    }

    public function init()
    {
        PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
        if(!class_exists('BadgeOS_PeepSoTriggers')) {
            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'BadgeOS_PeepSoTriggers.php');
        }
        PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));

            // JS file
            add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

            // config tabs
            add_filter('peepso_admin_config_tabs',          array(&$this, 'admin_config_tabs'));
        } else {
            if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
                return;
            }

            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

            // profile segments
            add_action('peepso_profile_segment_badges',     array(&$this, 'filter_profile_segment_badges'));
            add_filter( 'user_deserves_achievement', array(&$this, 'badgeos_peepso_user_deserves_community_step'), 15, 3 );

            // post to activity
            add_action('peepso_activity_post_attachment', array(&$this, 'attach_badges'), 20, 1);
            add_filter('peepso_activity_stream_action', array(&$this, 'activity_stream_action'), 10, 2);

            // modify notification link
            add_action('peepso_profile_notification_link', array(&$this, 'filter_profile_notification_link'), 10, 2);
            if(version_compare( PeepSo::PLUGIN_VERSION, '1.7.0', '>' )) {
                add_action('peepso_profile_cover_full_before_name', array(&$this, 'action_cover_full_after_name'), 10, 1);
            }

            if(version_compare(PeepSo::PLUGIN_VERSION, '1.7.1', '>')) {
                add_action('peepso_action_widget_profile_name_after', array(&$this, 'action_widget_profile_name_after'), 10, 1);
                add_action('peepso_action_userbar_user_name_after', array(&$this, 'action_widget_profile_name_after'), 10, 1);
            }
        }

        // PeepSo navigation
        add_filter('peepso_navigation_profile', array(&$this, 'filter_peepso_navigation_profile'));

        add_filter('peepso_widgets', array(&$this, 'register_widgets'));

        // render achievement list
        add_filter('badgeos_render_achievement', array(&$this, 'filter_badgeos_render_achievements'), 10, 2);

        if(class_exists('PeepSoFriendsPlugin'))
        {
            $this->peepso_triggers[__( 'PeepSo Friends', 'badgeos-peepso' )] = array(
                'peepso_friends_requests_after_add' => array('label' => __( 'Send a Friend Request.', 'badgeos-peepso' ), 'num_args' => 2),
                'peepso_friends_requests_after_accept' => array('label' => __( 'Add a new friend.', 'badgeos-peepso' ), 'num_args' => 2),
            );
        }

        if(class_exists('PeepSoGroupsPlugin'))
        {
            $this->peepso_triggers[__( 'PeepSo Groups', 'badgeos-peepso' )] = array(
                'peepso_action_group_create' => array('label' => __( 'Create a Group.', 'badgeos-peepso' ), 'num_args' => 1),
                'peepso_action_group_user_join' => array('label' => __( 'Join a Group.', 'badgeos-peepso' ), 'num_args' => 2),
                'peepso_groups_after_change_avatar' => array('label' => __( 'Change Group Avatar.', 'badgeos-peepso' ), 'num_args' => 4),
                'peepso_groups_after_change_cover' => array('label' => __( 'Change Group Cover.', 'badgeos-peepso' ), 'num_args' => 2),
            );
        }

        if(class_exists('PeepSoMessagesPlugin'))
        {
            $this->peepso_triggers[__( 'PeepSo Messages', 'badgeos-peepso' )] = array(
                'peepso_messages_new_conversation' => array('label' => __( 'Send a Message.', 'badgeos-peepso' ), 'num_args' => 1),
            );
        }

        if ( defined( 'PMPRO_DIR' ) ) {
            $this->peepso_triggers[__( 'PeepSo PMP Integration', 'badgeos-peepso' )] = array(
                'peepso_action_pmp_checkout' => array('label' => __( 'Checkout membership level upon registration.', 'badgeos-peepso' ), 'num_args' => 2),
            );
        }
        
        $this->badgeos_peepso_load_peepso_triggers();
    }

    public function license_notice()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG);
    }

    public function license_notice_forced()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG, true);
    }


    /**
     * Check if PeepSo class is present (ie the PeepSo plugin is installed and activated)
     * If there is no PeepSo, immediately disable the plugin and display a warning
     * Run license and new version checks against PeepSo.com
     * @return bool
     */
    public function peepso_check()
    {
        if (!class_exists('PeepSo')) {
            add_action('admin_notices', array(&$this, 'peepso_disabled_notice'));
            unset($_GET['activate']);
            deactivate_plugins(plugin_basename(__FILE__));
            return (FALSE);
        }

        if(!self::ready_thirdparty()) {
            add_action('admin_notices',function() {
                ?>
                <div class="error peepso">
                    <?php echo sprintf(__('PeepSo %s requires the BadgeOS plugin (%s or newer).', 'badgeos-peepso'), self::PLUGIN_NAME, self::BADGEOS_VER_MIN);?>
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

    /**
     * Display a message about PeepSo not present
     */
    public function peepso_disabled_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php
				echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'badgeos-peepso'), self::PLUGIN_NAME),
                    ' <a href="plugin-install.php?tab=plugin-information&amp;plugin=peepso-core&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">',
                    __('Get it now!', 'badgeos-peepso'),
                    '</a>';
                ?>
                <?php //echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'badgeos-peepso'), self::PLUGIN_NAME);?>
            </strong>
        </div>
        <?php
    }

    /**
     * Activation hook for the plugin.
     *
     * @since 1.0.0
     */
    public function activate() {

        if (!$this->peepso_check()) {
            return (FALSE);
        }

        require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
        $install = new BadgeOSPeepSoInstall();
        $res = $install->plugin_activation();
        if (FALSE === $res) {
            // error during installation - disable
            deactivate_plugins(plugin_basename(__FILE__));
        }
        return (TRUE);
    }

    public function admin_enqueue_scripts()
    {
        wp_register_script('peepso-badgeos-admin-config', plugin_dir_url(__FILE__) . 'assets/js/badgeos-admin-config.js', array('jquery'), self::PLUGIN_VERSION, TRUE);
        wp_enqueue_script('peepso-badgeos-admin-config');
    }

    /**
     * Enqueue custom scripts and styles
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {


        // // Grab the global PeepSo object
        // global $bp;

        // // If we're on a BP activity page
        // if ( isset( $bp->current_component ) && 'activity' == $bp->current_component ) {
        //     wp_enqueue_style( 'badgeos-front' );
        // }

    }

    /**
     * BACKEND SETTINGS
     * ================
     */

    /**
     * Registers a tab in the PeepSo Config Toolbar
     * PS_FILTER
     *
     * @param $tabs array
     * @return array
     */
    public function admin_config_tabs( $tabs )
    {

        $tabs['badgeos'] = array(
            'label' => __('BadgeOS', 'badgeos-peepso'),
            'icon' => self::ICON,
            'tab' => 'badgeos',
            'description' => __('PeepSo - BadgeOS Integration', 'badgeos-peepso'),
            'function' => 'PeepSoConfigSectionBadgeOS',
            'cat'   => 'integrations',
        );

        return $tabs;
    }

    /**
     * FRONTEND
     * ========
     *
     * PeepSo navigation
     */

    /**
     * Modify link notification
     * @param array $link
     * @param array $note_data
     * @return string $link
     */
    public function filter_profile_notification_link($link, $note_data)
    {
        if (in_array($note_data['not_type'], array('new_badge'))) {

            $badgeos_type = get_post_meta($note_data['not_external_id'], self::POST_META_KEY_BADGEOS_TYPE, true);

            if($badgeos_type === self::POST_META_KEY_BADGEOS_TYPE_ACHIEVEMENT) {
                $achievement_id = get_post_meta($note_data['not_external_id'], self::POST_META_KEY_BADGEOS_ACHIEVEMENT_ID, true);

                #$link = get_permalink( $achievement_id );
                $user = PeepSoUser::get_instance($note_data['not_user_id']);
                $link = $user->get_profileurl() . 'badges';

            }
        }
        return $link;
    }

    /**
     * Create PeepSo Activity when a user earns an achievement.
     *
     * @since 1.0.0
     */
    public function badgeos_award_achievement_peepso_activity( $user_id, $achievement_id, $this_trigger, $site_id, $args ) {
        if ( ! $user_id || ! $achievement_id ) {
            return false;
        }

        // check if badgeos integration enable
        if( !PeepSo::get_option('badgeos_integration_enable', 1) || !PeepSo::get_option('badgeos_create_posts_when_earns_new_badge', 1)) {
            return false;
        }

        $post = get_post( $achievement_id );
        $type = $post->post_type;

        // Don't make activity posts for step post type
        if ( 'step' == $type ) {
            return false;
        }

        // Check if option is on/off
        // $achievement_type = get_page_by_title( str_replace('-',' ', $type), 'OBJECT', 'achievement-type' );
        // $can_peepso_activity = get_post_meta( $achievement_type->ID, '_badgeos_create_peepso_activty', true );
        // if ( ! $can_bp_activity ) {
        //     return false;
        // }

        // Grab the singular name for our achievement type
        $post_type_singular_name = strtolower( get_post_type_object( $type )->labels->singular_name );

        $this->achievement_id = $achievement_id;
        add_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'), 10, 1);

        $content = '';
        $extra = array(
            'module_id' => self::MODULE_ID,
            'act_access' => PeepSo::ACCESS_PUBLIC,
        );

// photos
if(class_exists('PeepSoSharePhotos')) {
    remove_action('peepso_activity_after_add_post', array(PeepSoSharePhotos::get_instance(), 'after_add_post'));
    remove_action('peepso_activity_after_add_post', array(PeepSoSharePhotos::get_instance(), 'after_add_post_album'));
    remove_action('peepso_activity_after_add_post', array(PeepSoSharePhotos::get_instance(), 'after_add_post_cover'));
    remove_action('peepso_activity_after_add_post', array(PeepSoSharePhotos::get_instance(), 'after_add_post_avatar'));
}

// group
if(class_exists('PeepSoGroupsPlugin'))
{
    remove_action('peepso_activity_after_add_post', array(PeepSoGroupsPlugin::get_instance(), 'after_add_post'));
    remove_action('peepso_activity_after_add_post', array(PeepSoGroupsPlugin::get_instance(), 'action_add_post_cover'));
    remove_action('peepso_activity_after_add_post', array(PeepSoGroupsPlugin::get_instance(), 'after_add_post_avatar'));
}

// tags
if(class_exists('PeepSoTags')) {
    remove_action('peepso_activity_after_add_post', array(PeepSoTags::get_instance(), 'after_save_post'));
}

// videos
if(class_exists('PeepSoVideos')) {
    remove_action('peepso_activity_after_add_post', array(PeepSoVideos::get_instance(), 'after_add_post'));
}

        $peepso_activity = PeepSoActivity::get_instance();
        $post_id = $peepso_activity->add_post($user_id, $user_id, $content, $extra);
        add_post_meta($post_id, self::POST_META_KEY_BADGEOS_TYPE, self::POST_META_KEY_BADGEOS_TYPE_ACHIEVEMENT, true);
        add_post_meta($post_id, self::POST_META_KEY_BADGEOS_ACHIEVEMENT_ID, $achievement_id);

        // prevent achievement activity posted to group
        delete_post_meta($post_id, 'peepso_group_id');

        // send onsite notification
        $notif = new PeepSoNotifications();
        $notif->add_notification($user_id, $user_id, __(' congratulations! You earned a new badge', 'badgeos-peepso'), 'new_badge', self::MODULE_ID, $post_id);

        remove_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'));

    }

    /**
     * Change the activity stream item action string
     * @param  string $action The default action string
     * @param  object $post   The activity post object
     * @return string
     */
    public function activity_stream_action($action, $post)
    {
        if (self::MODULE_ID === intval($post->act_module_id)) {

            $badgeos_type = get_post_meta($post->ID, self::POST_META_KEY_BADGEOS_TYPE, true);
            if($badgeos_type === self::POST_META_KEY_BADGEOS_TYPE_ACHIEVEMENT) {
                $action = __(' earned a new badge!', 'badgeos-peepso');
            }
        }

        return ($action);
    }

    /**
     * Attach the badges to the post display
     * @param  object $post The post
     */
    public function attach_badges($stream_post = NULL)
    {
        $badgeos_type = get_post_meta($stream_post->ID, self::POST_META_KEY_BADGEOS_TYPE, true);

        if($badgeos_type === self::POST_META_KEY_BADGEOS_TYPE_ACHIEVEMENT) {
            $achievement_id = get_post_meta($stream_post->ID, self::POST_META_KEY_BADGEOS_ACHIEVEMENT_ID, true);

            $post = get_post( $achievement_id );
            $type = $post->post_type;

            $congratulations_text = get_post_meta($achievement_id, '_badgeos_congratulations_text', true);
            $congratulations_text = !empty($congratulations_text) ? $congratulations_text : $post->post_excerpt;

            // Setup our entry content
            $content = '<div class="badgeos-achievements-list-item user-has-earned">';
            $content .= '<div class="badgeos-item-image"><a href="'. get_permalink( $achievement_id ) . '">' . badgeos_get_achievement_post_thumbnail( $achievement_id ) . '</a></div>';
            $content .= '<div class="badgeos-item-description">' . '<h5 class="badgeos-item-title"><a href="'. get_permalink( $achievement_id ) . '">'. $post->post_title . '</a></h2>' . wpautop( $congratulations_text ) . '</div>';
            $content .= '</div>';

            echo $content;
        }
    }

    /**
     * Checks if empty content is allowed
     * @param string $allowed
     * @return boolean always returns TRUE
     */
    public function activity_allow_empty_content($allowed)
    {
        if(isset($this->achievement_id)) {
            $allowed = TRUE;
        }

        return ($allowed);
    }

    /*
     * PeepSo profiles
     */

    /**
     * Profile Segments - add link
     * @param $links
     * @return mixed
     */
    public function filter_peepso_navigation_profile($links)
    {
        if(PeepSo::get_option('badgeos_integration_enable', 1) && PeepSo::get_option('badgeos_show_link_in_profile_menu', 1)) {
            $links['badges'] = array(
                'href' => 'badges',
                'label'=> __('Badges', 'badgeos-peepso'),
                'icon' => 'ps-icon-star-empty'
            );
        }

        return $links;
    }


    /**
     * Render badges in user profile
     */
    public function filter_profile_segment_badges()
    {
        $pro = PeepSoProfileShortcode::get_instance();
        $this->view_user_id = PeepSoUrlSegments::get_view_id($pro->get_view_user_id());

        $achievement_types = badgeos_get_network_achievement_types_for_user( $this->view_user_id );
        // Eliminate step cpt from array
        if ( ( $key = array_search( 'step', $achievement_types ) ) !== false ) {
            unset( $achievement_types[$key] );
            $achievement_types = array_values( $achievement_types );
        }

        $type = '';

        if ( is_array( $achievement_types ) && !empty( $achievement_types ) ) {
            foreach ( $achievement_types as $achievement_type ) {
                $name = get_post_type_object( $achievement_type )->labels->name;
                $slug = str_replace( ' ', '-', strtolower( $name ) );
                if ( $slug && strpos( $_SERVER['REQUEST_URI'], $slug ) ) {
                    $type = $achievement_type;
                }
            }
            if ( empty( $type ) ) {
                $type = $achievement_types[0];
            }
        }

        $atts = array(
            'type'        => $type,
            'limit'       => '10',
            'show_filter' => 'false',
            'show_search' => 'false',
            'group_id'    => '0',
            'user_id'     => $this->view_user_id,
            'wpms'        => badgeos_ms_show_all_achievements(),
        );

        $list_badges = badgeos_achievements_list_shortcode( $atts );

        echo PeepSoTemplate::exec_template('badges', 'profile-badges', array('view_user_id' => $this->view_user_id, 'list_badges' => $list_badges), TRUE);
    }

    public function action_cover_full_after_name($user_id)
    {
        if(!PeepSo::get_option('badgeos_display_recent_badges_on_cover', 1)) {
            return ;
        }

        global $blog_id, $post;

        // Setup our query vars
        $type       = "all";
        $limit      = PeepSo::get_option('badgeos_limit_recent_badges_on_cover', 10);
        $offset     = 0;
        $count      = 0;
        $filter     = "completed";
        $search     = false;
        $orderby    = "menu_order";
        $order      = "ASC";
        $wpms       = false;
        $include    = array();
        $exclude    = array();
        $meta_key   = '';
        $meta_value = '';
        $old_post   = $post;
        // Convert $type to properly support multiple achievement types
        if ( 'all' == $type ) {
            $type = badgeos_get_achievement_types_slugs();
            // Drop steps from our list of "all" achievements
            $step_key = array_search( 'step', $type );
            if ( $step_key )
                unset( $type[$step_key] );
        } else {
            $type = explode( ',', $type );
        }

        // Get the current user if one wasn't specified
        if( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        // Build $include array
        if ( !is_array( $include ) ) {
            $include = explode( ',', $include );
        }

        // Build $exclude array
        if ( !is_array( $exclude ) ) {
            $exclude = explode( ',', $exclude );
        }

        // Initialize our output and counters
        $achievements = '';
        $achievement_count = 0;
        $query_count = 0;

        // Grab our hidden badges (used to filter the query)
        $hidden = badgeos_get_hidden_achievement_ids( $type );

        // If we're polling all sites, grab an array of site IDs
        if( $wpms && $wpms != 'false' )
            $sites = badgeos_get_network_site_ids();
        // Otherwise, use only the current site
        else
            $sites = array( $blog_id );

        // Loop through each site (default is current site only)
        foreach( $sites as $site_blog_id ) {

            // If we're not polling the current site, switch to the site we're polling
            if ( $blog_id != $site_blog_id ) {
                switch_to_blog( $site_blog_id );
            }

            // Grab our earned badges (used to filter the query)
            $earned_ids = badgeos_get_user_earned_achievement_ids( $user_id, $type );

            // Query Achievements
            $args = array(
                'post_type'      => $type,
                'orderby'        => $orderby,
                'order'          => $order,
                'posts_per_page' => $limit,
                'offset'         => $offset,
                'post_status'    => 'publish',
                'post__not_in'   => array_diff( $hidden, $earned_ids )
            );

            // Filter - query completed or non completed achievements
            if ( $filter == 'completed' ) {
                $args[ 'post__in' ] = array_merge( array( 0 ), $earned_ids );
            }elseif( $filter == 'not-completed' ) {
                $args[ 'post__not_in' ] = array_merge( $hidden, $earned_ids );
            }

            if ( '' !== $meta_key && '' !== $meta_value ) {
                $args[ 'meta_key' ] = $meta_key;
                $args[ 'meta_value' ] = $meta_value;
            }

            // Include certain achievements
            if ( !empty( $include ) ) {
                $args[ 'post__not_in' ] = array_diff( $args[ 'post__not_in' ], $include );
                $args[ 'post__in' ] = array_merge( array( 0 ), array_diff( $include, $args[ 'post__in' ] ) );
            }

            // Exclude certain achievements
            if ( !empty( $exclude ) ) {
                $args[ 'post__not_in' ] = array_merge( $args[ 'post__not_in' ], $exclude );
            }

            // Search
            if ( $search ) {
                $args[ 's' ] = $search;
            }

            // Loop Achievements
            $achievement_posts = new WP_Query( $args );
            $query_count += $achievement_posts->found_posts;
            while ( $achievement_posts->have_posts() ) : $achievement_posts->the_post();
                $achievements .= $this->badgeos_render_profile_cover( $user_id, get_the_ID() );
                $achievement_count++;
            endwhile;

            wp_reset_query();
            $post = $old_post;
        }

        echo $achievements;
    }

    public function action_widget_profile_name_after($user_id)
    {
        if(!PeepSo::get_option('badgeos_display_recent_badges_on_profile_widget', 1)) {
            return ;
        }

        global $blog_id;

        // Setup our query vars
        $type       = "all";
        $limit      = PeepSo::get_option('badgeos_limit_recent_badges_on_profile_widget', 10);
        $offset     = 0;
        $count      = 0;
        $filter     = "completed";
        $search     = false;
        $orderby    = "menu_order";
        $order      = "ASC";
        $wpms       = false;
        $include    = array();
        $exclude    = array();
        $meta_key   = '';
        $meta_value = '';

        // Convert $type to properly support multiple achievement types
        if ( 'all' == $type ) {
            $type = badgeos_get_achievement_types_slugs();
            // Drop steps from our list of "all" achievements
            $step_key = array_search( 'step', $type );
            if ( $step_key )
                unset( $type[$step_key] );
        } else {
            $type = explode( ',', $type );
        }

        // Get the current user if one wasn't specified
        if( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        // Build $include array
        if ( !is_array( $include ) ) {
            $include = explode( ',', $include );
        }

        // Build $exclude array
        if ( !is_array( $exclude ) ) {
            $exclude = explode( ',', $exclude );
        }

        // Initialize our output and counters
        $achievements = '';
        $achievement_count = 0;
        $query_count = 0;

        // Grab our hidden badges (used to filter the query)
        $hidden = badgeos_get_hidden_achievement_ids( $type );

        // If we're polling all sites, grab an array of site IDs
        if( $wpms && $wpms != 'false' )
            $sites = badgeos_get_network_site_ids();
        // Otherwise, use only the current site
        else
            $sites = array( $blog_id );

        // Loop through each site (default is current site only)
        foreach( $sites as $site_blog_id ) {

            // If we're not polling the current site, switch to the site we're polling
            if ( $blog_id != $site_blog_id ) {
                switch_to_blog( $site_blog_id );
            }

            // Grab our earned badges (used to filter the query)
            $earned_ids = badgeos_get_user_earned_achievement_ids( $user_id, $type );

            // Query Achievements
            $args = array(
                'post_type'      => $type,
                'orderby'        => $orderby,
                'order'          => $order,
                'posts_per_page' => $limit,
                'offset'         => $offset,
                'post_status'    => 'publish',
                'post__not_in'   => array_diff( $hidden, $earned_ids )
            );

            // Filter - query completed or non completed achievements
            if ( $filter == 'completed' ) {
                $args[ 'post__in' ] = array_merge( array( 0 ), $earned_ids );
            }elseif( $filter == 'not-completed' ) {
                $args[ 'post__not_in' ] = array_merge( $hidden, $earned_ids );
            }

            if ( '' !== $meta_key && '' !== $meta_value ) {
                $args[ 'meta_key' ] = $meta_key;
                $args[ 'meta_value' ] = $meta_value;
            }

            // Include certain achievements
            if ( !empty( $include ) ) {
                $args[ 'post__not_in' ] = array_diff( $args[ 'post__not_in' ], $include );
                $args[ 'post__in' ] = array_merge( array( 0 ), array_diff( $include, $args[ 'post__in' ] ) );
            }

            // Exclude certain achievements
            if ( !empty( $exclude ) ) {
                $args[ 'post__not_in' ] = array_merge( $args[ 'post__not_in' ], $exclude );
            }

            // Search
            if ( $search ) {
                $args[ 's' ] = $search;
            }

            // Loop Achievements
            $achievement_posts = new WP_Query( $args );
            $query_count += $achievement_posts->found_posts;
            while ( $achievement_posts->have_posts() ) : $achievement_posts->the_post();
                $achievements .= $this->badgeos_render_profile_widget( $user_id, get_the_ID() );
                $achievement_count++;
            endwhile;

            // wp_reset_query();

        }

        if(intval($achievement_count) > 0)
        {
            echo '<div class="ps-badgeos__widget-title">' . _n('Recently earned badge', 'Recently earned badges', $achievement_count, 'badgeos-peepso') . '</div>';
            echo '<div class="ps-badgeos__widget">' . $achievements . '</div>';
        }
    }

    private function badgeos_render_profile_cover($user_ID, $achievement)
    {
        // If we were given an ID, get the post
        if ( is_numeric( $achievement ) ) {
            $achievement = get_post( $achievement );
        }

        // check if user has earned this Achievement, and add an 'earned' class
        //$earned_status = badgeos_get_user_achievements( array( 'user_id' => $user_ID, 'achievement_id' => absint( $achievement->ID ) ) ) ? 'user-has-earned' : 'user-has-not-earned';

        // check if user has earned this Achievement, and add an 'earned' class
        //$earned_status = badgeos_get_user_achievements( array( 'user_id' => $user_ID, 'achievement_id' => absint( $achievement->ID ) ) ) ? 'user-has-earned' : 'user-has-not-earned';

        // Setup our credly classes
        //$credly_class = '';
        //$credly_ID = '';

        // If the achievement is earned and givable, override our credly classes
        //if ( 'user-has-earned' == $earned_status && $giveable = credly_is_achievement_giveable( $achievement->ID, $user_ID ) ) {
        //    $credly_class = ' share-credly addCredly';
        //    $credly_ID = 'data-credlyid="'. absint( $achievement->ID ) .'"';
        //}

        // Apply this on badge wrapper if needed
        // id="badgeos-achievements-list-item-' . $achievement->ID . '" class="ps-badgeos__item--cover '. $earned_status . $credly_class .'"'. $credly_ID .'

        // Each Achievement
            $output = '';
            $output .= '<div class="ps-badgeos__item--cover" >';

                // Achievement Image
                $output .= '<a href="' . get_permalink( $achievement->ID ) . '">' . badgeos_get_achievement_post_thumbnail( $achievement->ID ) . '</a>';

            $output .= '</div>';

        // Return our filterable markup
        return $output;
    }

    private function badgeos_render_profile_widget($user_ID, $achievement)
    {
        // If we were given an ID, get the post
        if ( is_numeric( $achievement ) ) {
            $achievement = get_post( $achievement );
        }

        // check if user has earned this Achievement, and add an 'earned' class
        //$earned_status = badgeos_get_user_achievements( array( 'user_id' => $user_ID, 'achievement_id' => absint( $achievement->ID ) ) ) ? 'user-has-earned' : 'user-has-not-earned';

        // check if user has earned this Achievement, and add an 'earned' class
        //$earned_status = badgeos_get_user_achievements( array( 'user_id' => $user_ID, 'achievement_id' => absint( $achievement->ID ) ) ) ? 'user-has-earned' : 'user-has-not-earned';

        // Setup our credly classes
        //$credly_class = '';
        //$credly_ID = '';

        // If the achievement is earned and givable, override our credly classes
        //if ( 'user-has-earned' == $earned_status && $giveable = credly_is_achievement_giveable( $achievement->ID, $user_ID ) ) {
        //    $credly_class = ' share-credly addCredly';
        //    $credly_ID = 'data-credlyid="'. absint( $achievement->ID ) .'"';
        //}

        // Apply this on badge wrapper if needed
        // id="badgeos-achievements-list-item-' . $achievement->ID . '" class="ps-badgeos__item--cover '. $earned_status . $credly_class .'"'. $credly_ID .'

        // Each Achievement
            $output = '';
            $output .= '<div class="ps-badgeos__item--cover" >';

                // Achievement Image
                $output .= '<a href="' . get_permalink( $achievement->ID ) . '">' . badgeos_get_achievement_post_thumbnail( $achievement->ID ) . '</a>';

            $output .= '</div>';

        // Return our filterable markup
        return $output;
    }

    /**
     * Modify Render
     */
    public function filter_badgeos_render_achievements($output, $achievement_id)
    {
        if(count($output)) {
            global $user_ID;

            // If we were given an ID, get the post
            if ( is_numeric( $achievement ) ) {
                $achievement = get_post( $achievement );
            }

            // make sure our JS and CSS is enqueued
            wp_enqueue_script( 'badgeos-achievements' );
            wp_enqueue_style( 'badgeos-widget' );

            // check if user has earned this Achievement, and add an 'earned' class
            $earned_status = badgeos_get_user_achievements( array( 'user_id' => $user_ID, 'achievement_id' => absint( $achievement->ID ) ) ) ? 'user-has-earned' : 'user-has-not-earned';

            // Setup our credly classes
            $credly_class = '';
            $credly_ID = '';

            // If the achievement is earned and givable, override our credly classes
            if ( 'user-has-earned' == $earned_status && $giveable = credly_is_achievement_giveable( $achievement->ID, $user_ID ) ) {
                $credly_class = ' share-credly addCredly';
                $credly_ID = 'data-credlyid="'. absint( $achievement->ID ) .'"';
            }

            // Each Achievement
            $output = '';
            $output .= '<div id="badgeos-achievements-list-item-' . $achievement->ID . '" class="ps-badgeos__item-wrapper '. $earned_status . $credly_class .'"'. $credly_ID .'>';
            $output .= '<div class="ps-badgeos__item">';

                // Achievement Image
                $output .= '<div class="ps-badgeos__item-image">';
                $output .= '<a href="' . get_permalink( $achievement->ID ) . '">' . badgeos_get_achievement_post_thumbnail( $achievement->ID ) . '</a>';
                $output .= '</div>';

                // Achievement Content
                $output .= '<div class="ps-badgeos__item-desc">';

                    // Achievement Title
                    $output .= '<a class="ps-badgeos__item-title" href="' . get_permalink( $achievement->ID ) . '">' . get_the_title( $achievement->ID ) .'</a>';

                    // Achievement Short Description
                    $output .= '<div class="ps-badgeos__item-excerpt">';
                    $output .= '<span>'. badgeos_achievement_points_markup( $achievement->ID ) .'</span>';
                    $excerpt = !empty( $achievement->post_excerpt ) ? $achievement->post_excerpt : $achievement->post_content;
                    $output .= wpautop( apply_filters( 'get_the_excerpt', $excerpt ) );
                    $output .= '</div>';

                    // Render our Steps
                    if ( $steps = badgeos_get_required_achievements_for_achievement( $achievement->ID ) ) {
                        $output.='<div class="ps-badgeos__item-attached">';
                            $output.='<div id="show-more-'.$achievement->ID.'" class="badgeos-open-close-switch"><a class="show-hide-open" data-badgeid="'. $achievement->ID .'" data-action="open" href="#">' . __( 'Show Details', 'badgeos' ) . '</a></div>';
                            $output.='<div id="badgeos_toggle_more_window_'.$achievement->ID.'" class="ps-badgeos__item-overlay badgeos-extras-window">'. badgeos_get_required_achievements_for_achievement_list_markup( $steps, $achievement->ID ) .'</div>';
                        $output.= '</div>';
                    }

                $output .= '</div>';

            $output .= '</div>';
            $output .= '</div>';


        }

        // Return our filterable markup
        return $output;
    }

    /**
     * STEP UI
     */
    /**
     * Update badgeos_get_step_requirements to include our custom requirements
     *
     * @since  1.0.0
     * @param  array   $requirements The current step requirements
     * @param  integer $step_id      The given step's post ID
     * @return array                 The updated step requirements
     */
    public function badgeos_peepso_step_requirements( $requirements, $step_id ) {
        // Add our new requirements to the list
        $requirements['peepso_trigger'] = get_post_meta( $step_id, '_badgeos_peepso_trigger', true );
        $requirements['peepso_group_id'] = get_post_meta( $step_id, '_badgeos_peepso_group_id', true );
        $requirements['peepso_pmp_level_id'] = get_post_meta( $step_id, '_badgeos_peepso_pmp_level_id', true );

        // Return the requirements array
        return $requirements;
    }

    /**
     * Filter the BadgeOS Triggers selector with our own options
     *
     * @since  1.0.0
     * @param  array $triggers The existing triggers array
     * @return array           The updated triggers array
     */
    public function badgeos_peepso_activity_triggers( $triggers ) {
        $triggers['peepso_trigger'] = __( 'PeepSo Community Engagement', 'badgeos-peepso' );
        return $triggers;
    }

    /**
     * Add a PeepSo Triggers selector to the Steps UI
     *
     * @since 1.0.0
     * @param integer $step_id The given step's post ID
     * @param integer $post_id The given parent post's post ID
     */
    public function action_badgeos_step_peepso_trigger_select( $step_id, $post_id ) {


        // Setup our select input
        echo '<select name="peepso_trigger" class="select-peepso-trigger select-peepso-trigger-' . $post_id . '">';
        echo '<option value="">' . __( 'Select a PeepSo Trigger', 'badgeos-peepso' ) . '</option>';

        // Loop through all of our peepso trigger groups
        $current_selection = get_post_meta( $step_id, '_badgeos_peepso_trigger', true );
        $peepso_triggers = $this->peepso_triggers;

        if ( !empty( $peepso_triggers ) ) {
            foreach ( $peepso_triggers as $optgroup_name => $triggers ) {
                echo '<optgroup label="' . $optgroup_name . '">';
                // Loop through each trigger in the group
                foreach ( $triggers as $trigger_hook => $trigger_name )
                    echo '<option' . selected( $current_selection, $trigger_hook, false ) . ' value="' . $trigger_hook . '">' . $trigger_name['label'] . '</option>';
                echo '</optgroup>';
            }
        }

        echo '</select>';

    }

    /**
     * Add a PeepSo group selector to the Steps UI
     *
     * @since 1.0.0
     * @param integer $step_id The given step's post ID
     * @param integer $post_id The given parent post's post ID
     */
    public function action_badgeos_peepso_step_group_select( $step_id, $post_id ) {

        // Setup our select input
        echo '<select name="peepso_group_id" class="select-peepso-group-id select-peepso-group-id-' . $post_id . '">';
        echo '<option value="">' . __( 'Select a Group', 'badgeos-peepso' ) . '</option>';

        // Loop through all existing PeepSo groups and include them here
        if ( class_exists('PeepSoGroupsPlugin') ) {
            $current_selection = get_post_meta( $step_id, '_badgeos_peepso_group_id', true );
            $peepso_groups = PeepSoGroups::admin_get_groups(0,300);
            if ( !empty( $peepso_groups ) ) {
                foreach ( $peepso_groups as $group ) {
                    echo '<option' . selected( $current_selection, $group->id, false ) . ' value="' . $group->id . '">' . $group->name . '</option>';
                }
            }
        }
        echo '</select>';

    }

    /**
     * Add a PMP membership level selector to the Steps UI
     *
     * @since 1.0.0
     * @param integer $step_id The given step's post ID
     * @param integer $post_id The given parent post's post ID
     */
    public function action_badgeos_peepso_step_pmp_level_select( $step_id, $post_id ) {

        // Setup our select input
        echo '<select name="peepso_pmp_level_id" class="select-peepso-pmp-level-id select-peepso-pmp-level-id-' . $post_id . '">';
        echo '<option value="">' . __( 'Select a Membership Level', 'badgeos-peepso' ) . '</option>';

        // Loop through all existing PeepSo groups and include them here
        if ( function_exists( 'pmpro_getAllLevels' ) ) {
            $pmpro_levels = pmpro_getAllLevels(false, true);
            $current_selection = get_post_meta( $step_id, '_badgeos_peepso_pmp_level_id', true );
            if ( !empty( $pmpro_levels ) ) {
                foreach ( $pmpro_levels as $level ) {
                    echo '<option' . selected( $current_selection, $level->id, false ) . ' value="' . $level->id . '">' . $level->name . '</option>';
                }
            }
        }
        echo '</select>';

    }

    /**
     * AJAX Handler for saving all steps
     *
     * @since  1.0.0
     * @param  string  $title     The original title for our step
     * @param  integer $step_id   The given step's post ID
     * @param  array   $step_data Our array of all available step data
     * @return string             Our potentially updated step title
     */
    public function badgeos_peepso_save_step( $title, $step_id, $step_data ) {

        // If we're working on a peepso trigger
        if ( 'peepso_trigger' == $step_data['trigger_type'] ) {

            // Update our peepso trigger post meta
            update_post_meta( $step_id, '_badgeos_peepso_trigger', $step_data['peepso_trigger'] );

            // Rewrite the step title
            $title = $step_data['peepso_trigger_label'];

            // If we're looking to join a specific group...
            if ( 'peepso_action_group_user_join' == $step_data['peepso_trigger'] && class_exists('PeepSoGroupsPlugin') ) {

                // Store our group ID in meta
                update_post_meta( $step_id, '_badgeos_peepso_group_id', $step_data['peepso_group_id'] );

                $group = new PeepSoGroup($step_data['peepso_group_id']);

                // Pass along our custom post title
                $title = sprintf( __( 'Join group "%s"', 'badgeos-peepso' ), $group->name );
            }

            // If we're looking to join a spesific level
            if ( 'peepso_action_pmp_checkout' == $step_data['peepso_trigger'] && function_exists( 'pmpro_getAllLevels' ) ) {

                // Store our group ID in meta
                update_post_meta( $step_id, '_badgeos_peepso_pmp_level_id', $step_data['peepso_pmp_level_id'] );

                $level = pmpro_getMembershipLevelForUser($step_data['peepso_pmp_level_id']);

                // Pass along our custom post title
                $title = sprintf( __( 'Checkout membership level "%s"', 'badgeos-peepso' ), $level->name );
            }
        }

        // Send back our custom title
        return $title;
    }

    /**
     * Include custom JS for the BadgeOS Steps UI
     *
     * @since 1.0.0
     */
    public function badgeos_bp_step_js() { ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {

            // Listen for our change to our trigger type selector
            $( document ).on( 'change', '.select-trigger-type', function() {

                var trigger_type = $(this);

                // Show our group selector if we're awarding based on a specific group
                if ( 'peepso_trigger' == trigger_type.val() ) {
                    trigger_type.siblings('.select-peepso-trigger').show().change();
                } else {
                    trigger_type.siblings('.select-peepso-trigger').hide().change();
                }

            });

            // Listen for our change to our trigger type selector
            $( document ).on( 'change', '.select-peepso-trigger', function() {

                var trigger_type = $(this);

                // Show our group selector if we're awarding based on a specific group
                if ( 'peepso_action_group_user_join' == trigger_type.val() ) {
                    trigger_type.siblings('.select-peepso-group-id').show();
                } else {
                    trigger_type.siblings('.select-peepso-group-id').hide();
                }

                // Show our pmp level selector if we're awarding based on a specific pmp level
                if ( 'peepso_action_pmp_checkout' == trigger_type.val() ) {
                    trigger_type.siblings('.select-peepso-pmp-level-id').show();
                } else {
                    trigger_type.siblings('.select-peepso-pmp-level-id').hide();
                }

            });

            // Trigger a change so we properly show/hide our community menues
            $('.select-trigger-type').change();

            // Inject our custom step details into the update step action
            $(document).on( 'update_step_data', function( event, step_details, step ) {
                step_details.peepso_trigger = $('.select-peepso-trigger', step).val();
                step_details.peepso_trigger_label = $('.select-peepso-trigger option', step).filter(':selected').text();
                step_details.peepso_group_id = $('.select-peepso-group-id', step).val();
                step_details.peepso_pmp_level_id = $('.select-peepso-pmp-level-id', step).val();
            });

        });
        </script>
    <?php
    }

    /**
     * Action Rules Engine
     */
    /**
     * Load up our peepso triggers so we can add actions to them
     *
     * @since 1.0.0
     */
    public function badgeos_peepso_load_peepso_triggers() {

        // Grab our peepso triggers
        $peepso_triggers = $this->peepso_triggers;
        if ( !empty( $peepso_triggers ) ) {
            foreach ( $peepso_triggers as $optgroup_name => $triggers ) {
                foreach ( $triggers as $trigger_hook => $trigger_name ) {
                    add_action( $trigger_hook, array('BadgeOS_PeepSoTriggers', substr($trigger_hook, 7)), 100, $trigger_name['num_args']);
                }
            }
        }

    }

    /**
     * Check if user deserves a community trigger step
     *
     * @since  1.0.0
     * @param  bool    $return         Whether or not the user deserves the step
     * @param  integer $user_id        The given user's ID
     * @param  integer $achievement_id The given achievement's post ID
     * @return bool                    True if the user deserves the step, false otherwise
     */
    public function badgeos_peepso_user_deserves_community_step( $return, $user_id, $achievement_id ) {

        // If we're not dealing with a step, bail here
        if ( 'step' != get_post_type( $achievement_id ) )
            return $return;

        // Grab our step requirements
        $requirements = badgeos_get_step_requirements( $achievement_id );

        // If the step is triggered by community actions...
        if ( 'peepso_trigger' == $requirements['trigger_type'] ) {

            // Grab the trigger count
            $trigger_count = badgeos_get_user_trigger_count( $user_id, $requirements['peepso_trigger'] );

            // If we meet or exceed the required number of checkins, they deserve the step
            if ( $trigger_count >= $requirements['count'] )
                $return = true;
            else
                $return = false;
        }

        return $return;
    }

}

BadgeOS_PeepSo::get_instance();
