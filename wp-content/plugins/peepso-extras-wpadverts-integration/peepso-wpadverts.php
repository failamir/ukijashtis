<?php
/**
 * Plugin Name: PeepSo Monetization: WPAdverts
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: <strong>Requires the WpAdverts plugin</strong>. Integrate WPAdverts  with PeepSo
 * Tags: peepso, wpadverts, integration
 * Author: PeepSo
 * Version: 2.7.7
 * Author URI: https://301.es/?https://peepso.com
 * Copyright: (c) 2015 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-wpadverts
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class PeepSoWPAdverts {

    private static $_instance = NULL;

    const PLUGIN_EDD = '607967';
    const PLUGIN_SLUG = 'PeepSo-WPAdverts';

    const PLUGIN_NAME    = 'Monetization: WPAdverts';
    const PLUGIN_VERSION = '2.7.7';
    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
    const MODULE_ID      = 1002;

    const PLUGIN_DIR_PATH = '';

    // post meta key for photo type (avatar/cover)
    const POST_META_KEY_WPADVERTS_TYPE          = '_peepso_wpadverts_type';
    const POST_META_KEY_WPADVERTS_TYPE_CLASSIFIEDS   = '_peepso_wpadverts_type_classifieds';
    const POST_META_KEY_WPADVERTS_CLASSIFIEDS_ID     = '_peepso_wpadverts_classifieds_id';

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2U3NGMzYzt9LmNscy0ye2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPldQQWR2ZXJ0c19pY29uPC90aXRsZT48ZyBpZD0iQkciPjxyZWN0IGNsYXNzPSJjbHMtMSIgd2lkdGg9IjEyOCIgaGVpZ2h0PSIxMjgiIHJ4PSIyMCIgcnk9IjIwIi8+PC9nPjxnIGlkPSJMb2dvIj48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik04My42Niw1Ni4xNHMtMTMuMTUtNS40NS0yNi4zLTIxTDUyLjA5LDQ5Ljc1cy03LTEuODgtOS4yMSw0LjUxLDMuOTUsMTAsMy45NSwxMEw0MSw4MHMxOS4yNS00LjMyLDMzLjktLjk0WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTg2Ljg1LDU3LjU1LDc4LjIxLDgwUzkzLDgyLjQ0LDk4LjUsODUuNDRMMTA0LjcsNjdTODcuOTIsNTguNzIsODYuODUsNTcuNTVaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIDApIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNMTA3LDcxLjVsLTMuMzgsMTAuNzZzNS42NywxLjkyLDYuNzktMS4wOVMxMTQuNzYsNzMuMzYsMTA3LDcxLjVaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIDApIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNNzYuNTIsODUuNDQsNjksMTAyLjkybDkuMjEsNS4yNkw4My42Niw5NS40bDMuOTUtMS44OCwyLjgyLTUuODJBMjkuMTksMjkuMTksMCwwLDAsNzYuNTIsODUuNDRaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIDApIi8+PHJlY3QgY2xhc3M9ImNscy0yIiB4PSI0Mi4zMiIgeT0iMTguMTkiIHdpZHRoPSIyLjQ0IiBoZWlnaHQ9IjE3LjY2IiByeD0iMS4xNCIgcnk9IjEuMTQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC03LjE0IDM0LjYpIHJvdGF0ZSgtNDAuMzMpIi8+PHJlY3QgY2xhc3M9ImNscy0yIiB4PSIzMi40MyIgeT0iMjguMTQiIHdpZHRoPSIyLjQ0IiBoZWlnaHQ9IjE3LjY2IiByeD0iMS4xNCIgcnk9IjEuMTQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xNS43NiA0NC41OCkgcm90YXRlKC01Ni40NCkiLz48cmVjdCBjbGFzcz0iY2xzLTIiIHg9IjI2LjMzIiB5PSI0MS4xMSIgd2lkdGg9IjIuNDQiIGhlaWdodD0iMTcuNjYiIHJ4PSIxLjE0IiByeT0iMS4xNCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTI4Ljc3IDU4LjkxKSByb3RhdGUoLTcwLjE2KSIvPjxyZWN0IGNsYXNzPSJjbHMtMiIgeD0iMjMuNjEiIHk9IjU0LjMzIiB3aWR0aD0iMi40NCIgaGVpZ2h0PSIxNy42NiIgcng9IjEuMTQiIHJ5PSIxLjE0IiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtNDAuMTEgODIuODYpIHJvdGF0ZSgtODUuNDIpIi8+PHJlY3QgY2xhc3M9ImNscy0yIiB4PSIxNi45NSIgeT0iNzYuMTciIHdpZHRoPSIxNy42NiIgaGVpZ2h0PSIyLjQ0IiByeD0iMS4xNCIgcnk9IjEuMTQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xNC44IDYuNjMpIHJvdGF0ZSgtMTEuNDEpIi8+PC9nPjwvc3ZnPg==';

    public $shortcodes= array(
        'peepso_wpadverts' => 'PeepSoWPAdvertsShortcode::shortcode_wpadverts',
    );

    private static function ready_thirdparty() {
        $result = TRUE;

        if (!function_exists('adverts_init')) {
            $result = FALSE;
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
        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            if (is_admin()) {
                add_action('peepso_config_before_save-wpadverts', array(&$this, 'before_save_wpadverts'));
            }
            
            add_action('peepso_init', array(&$this, 'init'));

            // Hook into PeepSo routing, enables single item view (eg /wpadverts/?1235/)
            add_filter('peepso_check_query', array(&$this, 'filter_check_query'), 10, 3);


            add_filter('peepso_activity_remove_shortcode', array(&$this, 'peepso_activity_remove_shortcode'));
            add_filter('peepso_filter_shortcodes', function($list) { return array_merge($list, $this->shortcodes);});
        }
    }

    /**
     * Retrieve singleton class instance
     * @return PeepSoWPAdverts instance
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
        PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));

            // config tabs
            add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'), -1);
        } else {
            if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
                return;
            }

            // post to activity
            add_action('peepso_activity_post_attachment', array(&$this, 'attach_classifieds'), 20, 1);
            add_filter('peepso_activity_stream_action', array(&$this, 'activity_stream_action'), 10, 2);
            add_filter('peepso_activity_post_actions',      array(&$this, 'modify_post_actions'),50); // priority set to last

            // If User Classifieds Enable
            if (PeepSo::get_option('wpadverts_user_classifieds_enable', 1)) {
                // PeepSo Navigation
                add_filter('peepso_navigation', array(&$this, 'filter_peepso_navigation'));
                add_filter('peepso_navigation_profile', array(&$this, 'filter_peepso_navigation_profile'));

                // Profile Slug
                $profile_slug = PeepSo::get_option('wpadverts_navigation_profile_slug', 'classifieds', TRUE);
                add_action('peepso_profile_segment_'.$profile_slug,     array(&$this, 'action_profile_segment_classifieds'));

                // Register Shortcodes
                PeepSoWPAdvertsShortcode::register_shortcodes();
            }

            // overrides wpadverts template
            if (!PeepSo::get_option('wpadverts_overrides_disable', 0)) {
                add_action("adverts_template_load", array(&$this, "custom_list_template"));
            }
            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

            if(1 == PeepSo::get_option('wpadverts_chat_enable', 0) ) {
                add_action('adverts_tpl_single_bottom', function() {
                    global $post;
                    if('advert' != $post->post_type || !get_current_user_id() || $post->post_author == get_current_user_id() || !class_exists('PeepSoMessages')) { return; }
                    ?>
                    <a href="#" class="ps-js-wpadverts-message" data-id="<?php echo $post->post_author;?>">
                        <i class="ps-icon-envelope-alt"></i><span><?php echo __('Send Message', 'peepso-wpadverts');?></span>
                        <img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" style="display:none" /></a>
                    <?php
                    self::get_instance()->enqueue_scripts();
                    wp_enqueue_script('peepso-wpdverts-classifieds');
                    echo '<div style="display:none">';
                    do_action('peepso_activity_dialogs');
                    echo '</div>';
                }, 99);
            }
        }

        add_action( 'save_post', array(&$this, 'save_classifieds'), 10, 3 );

        // delete stream when ad deleted
        add_action('before_delete_post', array(&$this, 'delete_stream_classifieds'), 10, 1);
    }

    public function peepso_activity_remove_shortcode( $content )
    {
        foreach($this->shortcodes as $shortcode=>$class) {
            foreach($this->shortcodes as $shortcode=>$class) {
                $from = array('['.$shortcode.']','['.$shortcode);
                $to = array('&#91;'.$shortcode.'&#93;', '&#91;'.$shortcode);
                $content = str_ireplace($from, $to, $content);
            }
        }
        return $content;
    }

    public function before_save_wpadverts() {
        $_POST['wpadverts_navigation_profile_slug'] = strtolower(preg_replace('/[^a-z0-9\-\_\.]+/i', "", $_POST['wpadverts_navigation_profile_slug']));
    }

    public function license_notice()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG);
    }

    public function license_notice_forced()
    {
        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG, true);
    }

    public function filter_check_query($sc, $page, $url)
    {
        if(PeepSoWPAdvertsShortcode::SHORTCODE == $page ) {
            $sc = PeepSoWPAdvertsShortcode::get_instance();
            $sc->set_page($url);
        }
    }

    /**
     * Loads the translation file for the PeepSo plugin
     */
    public function load_textdomain()
    {
        $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
        load_plugin_textdomain('peepso-wpadverts', FALSE, $path);
    }

    /**
     * Hooks into PeepSo for compatibility checks
     * @param $plugins
     * @return mixed
     */
    public function filter_all_plugins($plugins)
    {
        $plugins[plugin_basename(__FILE__)] = get_class($this);
        return $plugins;
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

        if (!self::ready_thirdparty()) {

            add_action('admin_notices', function() {

                ?>
                <div class="error peepso">
                    <?php echo sprintf(__('PeepSo %s requires the WpAdverts plugin.', 'peepsolearndash'), self::PLUGIN_NAME);?>
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
				echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-wpadverts'), self::PLUGIN_NAME),
                    ' <a href="plugin-install.php?tab=plugin-information&amp;plugin=peepso-core&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">',
                    __('Get it now!', 'peepso-wpadverts'),
                    '</a>';
                ?>
                <?php //echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-wpadverts'), self::PLUGIN_NAME);?>
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
        $install = new WPAdvertsPeepSoInstall();
        $res = $install->plugin_activation();
        if (FALSE === $res) {
            // error during installation - disable
            deactivate_plugins(plugin_basename(__FILE__));
        }
        return (TRUE);
    }

    public function admin_enqueue_scripts()
    {
    	//
    }

    /**
     * Enqueue custom scripts and styles
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('peepso-wpdverts-classifieds', plugin_dir_url(__FILE__) . 'assets/js/bundle.min.js', array('peepso', 'peepso-page-autoload'), self::PLUGIN_VERSION, TRUE);
        wp_localize_script('peepso', 'peepsowpadvertsdata', array(
            'listItemTemplate' => PeepSoTemplate::exec_template('wpadverts', 'wpadverts-item', NULL, TRUE),
            'user_id' => get_current_user_id(),
            'module_id' => self::MODULE_ID,
            'lang' => array(
                'more' => __('More', 'peepso-wpadverts'),
                'less' => __('Less', 'peepso-wpadverts'),
                'member' => __('member', 'peepso-wpadverts'),
                'members' => __('members', 'peepso-wpadverts'),
            )
        ));
    }

    /**
     * Publish post
     *
     * @param int $post_id The post ID.
     * @param post $post The post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    public function save_classifieds($post_id, $post, $update)
    {
        /*
         * In production code, $slug should be set only once in the plugin,
         * preferably as a class property, rather than in each function that needs it.
         */
        $post_type = get_post_type($post_id);

        // If this isn't a 'advert' post, don't publish it.
        if ( "advert" != $post_type ) return;

        // If post to stream option disabled, don't publish it.
        if ( !PeepSo::get_option('wpadverts_post_to_stream_enable', 1) ) return;

        // If moderation enabled and post_status is `advert-pending`
        if (PeepSo::get_option('wpadverts_moderation_enable', 1) && $post->post_status == 'advert-pending') return;

        // If post status is pending
        if ( $post->post_status == 'pending' || $post->post_status == 'advert_tmp' ) return;

        // If stream already posted. skip creating new stream.
        $args = array(
            'post_status'      => array('publish', 'pending', 'trash', 'expired'),
            'post_type'        => PeepSoActivityStream::CPT_POST,
            'meta_query' => array(
                array(
                    'key'     => self::POST_META_KEY_WPADVERTS_CLASSIFIEDS_ID,
                    'value'   => $post_id,
                    'compare' => '=',
                ),
            ),
        );

        $existing_post = get_posts( $args );
        if( $existing_post ) {
            if($post->post_status == 'trash') {
                $status = 'trash';
            } elseif($post->post_status == 'expired') {
                $status = 'expired';
            }else {
                $status = 'publish';
            }

            // Update the post into the database
            $ad_post = array(
                'ID'           => $existing_post[0]->ID,
                'post_status'  => $status,
                'post_date'		=> $post->post_date,
                'post_date_gmt' => $post->post_date_gmt,
            );
            wp_update_post( $ad_post );
            return ;
        }

        // - Post to stream
        $this->classifieds_id = $post_id;
        add_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'), 10, 1);

        $user_id = $post->post_author;
        $content = '';
        $extra = array(
            'module_id' => self::MODULE_ID,
            'act_access' => PeepSo::get_option('wpadverts_post_privacy', PeepSo::ACCESS_PUBLIC),
            'post_date'		=> $post->post_date,
            'post_date_gmt' => $post->post_date_gmt,
        );

        $peepso_activity = PeepSoActivity::get_instance();
        $stream_id = $peepso_activity->add_post($user_id, $user_id, $content, $extra);
        add_post_meta($stream_id, self::POST_META_KEY_WPADVERTS_TYPE, self::POST_META_KEY_WPADVERTS_TYPE_CLASSIFIEDS, true);
        add_post_meta($stream_id, self::POST_META_KEY_WPADVERTS_CLASSIFIEDS_ID, $post_id);

        remove_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'));

    }

    /**
     * Checks if empty content is allowed
     * @param string $allowed
     * @return boolean always returns TRUE
     */
    public function activity_allow_empty_content($allowed)
    {
        if(isset($this->classifieds_id)) {
            $allowed = TRUE;
        }

        return ($allowed);
    }

    /**
     * Delete stream
     * @param int $post_id
     */
    public function delete_stream_classifieds($post_id)
    {
        /*
         * In production code, $slug should be set only once in the plugin,
         * preferably as a class property, rather than in each function that needs it.
         */
        $post_type = get_post_type($post_id);
        $post = get_post($post_id);

        // If this isn't a 'advert' post, don't publish it.
        if ( "advert" != $post_type ) return;

        // If stream exists delete activity.
        $args = array(
            'post_status'      => array('publish', 'pending', 'trash'),
            'post_type'        => PeepSoActivityStream::CPT_POST,
            'meta_query' => array(
                array(
                    'key'     => self::POST_META_KEY_WPADVERTS_CLASSIFIEDS_ID,
                    'value'   => $post_id,
                    'compare' => '=',
                ),
            ),
        );

        $existing_post = get_posts( $args );
        if( !$existing_post ) return ;

        global $wpdb;

        $act = $wpdb->get_row( $wpdb->prepare( "SELECT * from $wpdb->prefix" . PeepSoActivity::TABLE_NAME . " WHERE act_external_id= %s AND act_module_id = %s AND act_owner_id = %s", $existing_post[0]->ID, self::MODULE_ID, $existing_post[0]->post_author));
        if( !$act ) return ;
        $activity = PeepSoActivity::get_instance();
        $activity->delete_activity($act->act_id);

        // In some setups, this line causes posts being randomly deleted. This constant patches it up.
        if(!defined('PEEPSO_WPADS_DELETE_OVERRIDE') || !PEEPSO_WPADS_DELETE_OVERRIDE) {
            $activity->delete_post($existing_post[0]->ID);
        }
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
        $tabs['wpadverts'] = array(
            'label' => __('WPAdverts', 'peepso-wpadverts'),
            'icon' => self::ICON,
            'tab' => 'wpadverts',
            'description' => __('PeepSo - WPAdverts Integration', 'peepso-wpadverts'),
            'function' => 'PeepSoConfigSectionWPAdverts',
            'cat'   => 'integrations',
        );

        return $tabs;
    }

    /**
     * FRONTEND
     * ========
     *
     */


    /**
     * Attach the ads to the post display
     * @param  object $post The post
     */
    public function attach_classifieds($stream_post = NULL)
    {
        $ads_type = get_post_meta($stream_post->ID, self::POST_META_KEY_WPADVERTS_TYPE, true);

        if($ads_type === self::POST_META_KEY_WPADVERTS_TYPE_CLASSIFIEDS) {
            $ads_id = get_post_meta($stream_post->ID, self::POST_META_KEY_WPADVERTS_CLASSIFIEDS_ID, true);

            $post = get_post( $ads_id );
            $type = $post->post_type;
            $expires = get_post_meta( $post->ID, "_expiration_date", true );

            $image = adverts_get_main_image( $ads_id );
            $price = adverts_get_the_price( $ads_id );
            $date = date_i18n( get_option( 'date_format' ), get_post_time( 'U', false, $post->ID ) );
            $location = get_post_meta( $post->ID, "adverts_location", true );
            $expires = esc_html( apply_filters( 'adverts_sh_manage_date', date_i18n( get_option('date_format'), $expires ), $post ) );
            $class_featured = '';
            if( $post->menu_order ) {
                $class_featured = ' ps-classified__item--featured';
            }

            // Setup our entry content
            $content = '<div class="ps-classified__item ps-classified__item--stream' . $class_featured . '">';
                $content .= '<div class="ps-classified__item-body">';

                    if($image) {
                        $content .= '<div class="ps-classified__item-image"><a href="'. get_permalink( $ads_id ) . '"><img src="' . adverts_get_main_image( $ads_id ) . '" /></a></div>';
                    }

                    $content .= '<h3 class="ps-classified__item-title"><a href="'. get_permalink( $ads_id ) . '">'. $post->post_title . '</a></h3>';

                    if($price) {
                        $content .= '<div class="ps-classified__item-details"><a class="ps-classified__item-price" href="'. get_permalink( $ads_id ) . '">'.$price.'</a></div>';
                    }

                    $content .= '<div class="ps-classified__item-desc">' . wpautop( $post->post_content ) . '</div>';
                $content .= '</div>';
                $content .= '<div class="ps-classified__item-footer ps-text--muted">';
                    $content .= '<span><i class="ps-icon-clock"></i> '. $date .'</span> <span><i class="ps-icon-map-marker"></i> '. $location .'</span> ';
                    if((get_current_user_id() == $post->post_author) || is_admin()) {
                        $content .= '<span><i class="ps-icon-clock"></i> '. __('expires', 'peepso-wpadverts') .': '. $expires .'</span>';
                    }
                    $content .= '<div class="ps-classified__item-actions">';
                        if (class_exists('PeepSoMessages') && get_current_user_id() != $post->post_author && 1 == PeepSo::get_option('wpadverts_chat_enable', 0)) {
                            $content .= '<a href="#" class="ps-js-wpadverts-message" data-id="' . $post->post_author . '">';
                            $content .= '<i class="ps-icon-envelope-alt"></i><span>' . __('Send Message', 'peepso-wpadverts') . '</span> ';
                            $content .= '<img src="' . PeepSo::get_asset('images/ajax-loader.gif') . '" style="display:none" /></a> ';
                        }
                        $content .= '<a href="' . get_permalink( $ads_id ) . '" class="ps-link--more"><i class="ps-icon-info-circled"></i><span>' . __('More', 'peepso-wpadverts') . '</span></a>';
                    $content .= '</div>';
                $content .= '</div>';
            $content .= '</div>';

            echo $content;

            // enqueue script handler
            wp_enqueue_script('peepso-wpdverts-classifieds');
        }
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

            $ads_type = get_post_meta($post->ID, self::POST_META_KEY_WPADVERTS_TYPE, true);
            if($ads_type === self::POST_META_KEY_WPADVERTS_TYPE_CLASSIFIEDS) {
                $action = __(' posted a new ad', 'peepso-wpadverts');
            }
        }

        return ($action);
    }



    /**
     * Change act_id on repost button act_id to follow parent's act_id.
     * @param array $options The default options per post
     * @return  array
     */
    public function modify_post_actions($options)
    {
        $post = $options['post'];

        if (self::MODULE_ID === intval($post->act_module_id)) {

            $ads_type = get_post_meta($post->ID, self::POST_META_KEY_WPADVERTS_TYPE, true);
            if($ads_type === self::POST_META_KEY_WPADVERTS_TYPE_CLASSIFIEDS) {
                // disable repost function for classifieds post
                unset($options['acts']['repost']);
            }
        }

        return ($options);
    }

    /**
     * Render classifieds in user profile
     */
    public function action_profile_segment_classifieds()
    {
        $pro = PeepSoProfileShortcode::get_instance();
        $this->view_user_id = PeepSoUrlSegments::get_view_id($pro->get_view_user_id());

        $url = PeepSoUrlSegments::get_instance();
        if ($url->get(3) == 'manage') {

            $shortcode = PeepSoWPAdvertsShortcode::get_instance();
            echo $shortcode->shortcode_manage_ads();

        } else {

            wp_enqueue_script('peepso-wpdverts-classifieds');

            echo PeepSoTemplate::exec_template('wpadverts', 'profile-wpadverts', array('view_user_id' => $this->view_user_id), TRUE);
        }
    }

    /*
     * PeepSo navigation
     */

    public function filter_peepso_navigation($navigation)
    {
        $user = PeepSoUser::get_instance(get_current_user_id());

        $navigation['wpadverts'] = array(
            'href' => PeepSo::get_page('wpadverts'),
            'label' => PeepSo::get_option('wpadverts_navigation_label', __('Classifieds', 'peepso-wpadverts'), TRUE),
            'icon' => 'ps-icon-bullhorn',

            'primary'           => TRUE,
            'secondary'         => FALSE,
            'mobile-primary'    => TRUE,
            'mobile-secondary'  => FALSE,
            'widget'            => TRUE,
        );

        return ($navigation);
    }

    /**
     * Profile Segments - add link
     * @param $links
     * @return mixed
     */
    public function filter_peepso_navigation_profile($links)
    {
        $links['wpadverts'] = array(
            'href' => PeepSo::get_option('wpadverts_navigation_profile_slug', 'classifieds', TRUE),
            'label'=> PeepSo::get_option('wpadverts_navigation_profile_label', __('Classifieds', 'peepso-wpadverts'), TRUE),
            'icon' => 'ps-icon-bullhorn'
        );

        return $links;
    }

    public static function isVersion140() {

        require_once(ABSPATH.'wp-admin/includes/plugin.php');

        $helloReflection = new ReflectionFunction('adverts_init');
        $plugin_data = get_plugin_data( $helloReflection->getFilename() );
        $plugin_version = isset($plugin_data['Version']) ? $plugin_data['Version'] : '';
        
        if (version_compare($plugin_version, '1.4.0', '>=')) {
            return true;
        }

        return false;
    }


    function custom_list_template( $tpl ) {

        $prefix = '';
        if (self::isVersion140()) {
            $prefix = '140';
        }

        // $tpl is an absolute path to a file, for example
        // /home/simpliko/public_html/wp-content/plugins/wpadverts/templates/list.php

        $basename = basename( $tpl );
        // $basename is just a filename for example list.php

        if( $basename == "add.php" ) {
            return dirname( __FILE__ ) . "/templates/overrides" . $prefix . "/add.php";
        } elseif( $basename == "add-preview.php" ) {
            return dirname( __FILE__ ) . "/templates/overrides" . $prefix . "/add-preview.php";
        } elseif( $basename == "add-save.php" ) {
            return dirname( __FILE__ ) . "/templates/overrides" . $prefix . "/add-save.php";
        } elseif( $basename == "manage.php" ) {
            return dirname( __FILE__ ) . "/templates/overrides" . $prefix . "/manage.php";
        } elseif( $basename == "manage-edit.php" ) {
            return dirname( __FILE__ ) . "/templates/overrides" . $prefix . "/manage-edit.php";
        } elseif( $basename == "single.php" ) {
            return dirname( __FILE__ ) . "/templates/overrides" . $prefix . "/single.php";
        } elseif( $basename == "categories-top.php" ) {
            return dirname( __FILE__ ) . "/templates/overrides" . $prefix . "/categories-top.php";
        } else {
            return $tpl;
        }
    }
}

PeepSoWPAdverts::get_instance();
