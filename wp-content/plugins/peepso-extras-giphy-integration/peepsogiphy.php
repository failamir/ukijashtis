<?php
/**
 * Plugin Name: PeepSo Integrations: GIPHY
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: Send GIPHY gifs and stickers in comments and chat
 * Author: PeepSo
 * Author URI: https://301.es/?https://peepso.com
 * Version: 2.7.7
 * Copyright: (c) 2015 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-giphy
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class PeepSoGiphyPlugin
{
	private static $_instance = NULL;

    const PLUGIN_EDD = 106644;
    const PLUGIN_SLUG = 'giphy';

    const PLUGIN_NAME    = 'Integrations: GIPHY';
    const PLUGIN_VERSION = '2.7.7';
    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
    const POST_META_KEY_GIPHY = 'peepso_giphy';

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzFhMWExYTt9LmNscy0ye2ZpbGw6IzBmOTt9LmNscy0ze2ZpbGw6IzkzZjt9LmNscy00e2ZpbGw6IzBjZjt9LmNscy01e2ZpbGw6I2ZmOTt9LmNscy02e2ZpbGw6I2Y2Njt9LmNscy03e2ZpbGw6IzRjMTk3Zjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPmdpcGh5X2ljb248L3RpdGxlPjxnIGlkPSJCRyI+PHJlY3QgY2xhc3M9ImNscy0xIiB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgcng9IjIwIiByeT0iMjAiLz48L2c+PGcgaWQ9IkxvZ28iPjxnIGlkPSJfR3JvdXBfIiBkYXRhLW5hbWU9IiZsdDtHcm91cCZndDsiPjxyZWN0IGlkPSJfUGF0aF8iIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTIiIHg9IjMxLjg3IiB5PSIzMy4yIiB3aWR0aD0iOC4wMyIgaGVpZ2h0PSI2Mi42OCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTEuNyAwLjk4KSByb3RhdGUoLTEuNTIpIi8+PHJlY3QgaWQ9Il9QYXRoXzIiIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTMiIHg9Ijg4LjMiIHk9IjQ3Ljc3IiB3aWR0aD0iOC4wNCIgaGVpZ2h0PSI0Ni42MSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTEuODYgMi40OCkgcm90YXRlKC0xLjUyKSIvPjxyZWN0IGlkPSJfUGF0aF8zIiBkYXRhLW5hbWU9IiZsdDtQYXRoJmd0OyIgY2xhc3M9ImNscy00IiB4PSIzMi44IiB5PSI5NS4xMSIgd2lkdGg9IjY0LjI3IiBoZWlnaHQ9IjguMDQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0yLjYxIDEuNzYpIHJvdGF0ZSgtMS41MikiLz48cmVjdCBpZD0iX1BhdGhfNCIgZGF0YS1uYW1lPSImbHQ7UGF0aCZndDsiIGNsYXNzPSJjbHMtNSIgeD0iMzAuOTMiIHk9IjI0Ljc1IiB3aWR0aD0iNDAuMTciIGhlaWdodD0iOC4wNCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuNzUgMS4zNykgcm90YXRlKC0xLjUyKSIvPjxwb2x5Z29uIGlkPSJfUGF0aF81IiBkYXRhLW5hbWU9IiZsdDtQYXRoJmd0OyIgY2xhc3M9ImNscy02IiBwb2ludHM9Ijg3LjQ3IDM5Ljg1IDg3LjI2IDMxLjgyIDc5LjIzIDMyLjAzIDc5LjAyIDI0IDcwLjk4IDI0LjIxIDcxLjYyIDQ4LjMxIDk1LjcyIDQ3LjY3IDk1LjUxIDM5LjY0IDg3LjQ3IDM5Ljg1Ii8+PHJlY3QgaWQ9Il9QYXRoXzYiIGRhdGEtbmFtZT0iJmx0O1BhdGgmZ3Q7IiBjbGFzcz0iY2xzLTciIHg9Ijg3Ljc5IiB5PSI0Ny43NyIgd2lkdGg9IjguMDQiIGhlaWdodD0iOC4wNCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTEuMzQgMi40Nikgcm90YXRlKC0xLjUyKSIvPjwvZz48L2c+PC9zdmc+';

    private static function ready() {
        return(class_exists('PeepSo') && self::PLUGIN_VERSION.self::PLUGIN_RELEASE == PeepSo::PLUGIN_VERSION.PeepSo::PLUGIN_RELEASE);
    }

    private function __construct() {

        /** VERSION INDEPENDENT hooks **/

        // Admin
        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));
            add_filter('peepso_license_config', array(&$this, 'add_license_info'), 160);
        }

        // Compatibility
        add_filter('peepso_all_plugins', array($this, 'filter_all_plugins'));

        // Translations
        add_action('plugins_loaded', array(&$this, 'load_textdomain'));

        // Activation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            if (is_admin()) {
                add_action('admin_init', array(&$this, 'giphy_check'));
            }

            add_action('peepso_init', array(&$this, 'init'));
        }
    }

    /**
     * Retrieve singleton class instance
     * @return PeepSoGiphyPlugin instance
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
            add_action('admin_init', array(&$this, 'giphy_check'));

            // JS file
            // add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

            // config tabs
            add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));
        } else {
            if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
                 return;
            }

            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

            add_filter('peepso_post_types_message', array(&$this, 'post_types'));
            add_filter('peepso_postbox_tabs', array(&$this, 'postbox_tabs'));

            // comments addons
            add_filter('peepso_commentsbox_interactions', array(&$this, 'commentsbox_interactions'), 20, 2);
            add_filter('peepso_commentsbox_addons', array(&$this, 'commentsbox_addons'), 10, 2);
            add_action('peepso_activity_post_attachment', array(&$this, 'comments_attach_giphy'), 20, 1);
            add_action('peepso_activity_comment_attachment', array(&$this, 'comments_attach_giphy'), 10);
            add_filter('peepso_activity_allow_empty_comment', array(&$this, 'activity_allow_empty_comment'), 10, 1);
            add_action('peepso_activity_after_add_comment', array(&$this, 'after_add_comment'), 10, 2);
            add_action('peepso_activity_after_save_comment', array(&$this, 'after_save_comment'), 10, 2);
            add_filter('peepso_activity_comment_actions',   array(&$this, 'modify_comments_actions'),100); // priority set to last
            add_filter('peepso_message_input_addons',   array(&$this, 'message_input_addons'), 20, 1);

            // chat integration
            add_action('peepso_activity_after_add_post', array(&$this, 'after_add_post'), 20, 2);
        }
    }

    /**
     * Adds the license key information to the config metabox
     * @param array $list The list of license key config items
     * @return array The modified list of license key items
     */
    public function add_license_info($list)
    {
        $data = array(
            'plugin_slug' => self::PLUGIN_SLUG,
            'plugin_name' => self::PLUGIN_NAME,
            'plugin_edd' => self::PLUGIN_EDD,
            'plugin_version' => self::PLUGIN_VERSION
        );
        $list[] = $data;
        return ($list);
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
     * Loads the translation file for the PeepSo plugin
     */
    public function load_textdomain()
    {
        $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
        load_plugin_textdomain('peepso-giphy', FALSE, $path);
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
     * Check if Giphy API key has been provided
     * If there is no PeepSo, immediately disable the plugin and display a warning
     * Run license and new version checks against PeepSo.com
     * @return bool
     */
    public function giphy_check()
    {
        if(!$this->peepso_check()) {
            return FALSE;
        }

        $giphy_key = PeepSo::get_option('giphy_api_key', FALSE);
        if (empty($giphy_key) or $giphy_key === FALSE) {
            add_action('admin_notices', array(&$this, 'peepso_giphy_notice'));
            return (FALSE);
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
				echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-giphy'), self::PLUGIN_NAME),
                    ' <a href="plugin-install.php?tab=plugin-information&amp;plugin=peepso-core&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">',
                    __('Get it now!', 'peepso-giphy'),
                    '</a>';
                ?>
            </strong>
        </div>
        <?php
    }

    /**
     * Display a message about Giphy API Key not present
     */
    public function peepso_giphy_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php
                echo __('Please provide Giphy API Key.', 'peepso-giphy'),
                    ' <a href="http://api.giphy.com" target="_blank">',
                    __('Get it now!', 'peepso-giphy'),
                    '</a>';
                ?>
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
        $install = new PeepSoGiphyInstall();
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
        wp_register_style('peepso-giphy', plugin_dir_url(__FILE__) . 'assets/css/giphy.css', array('peepso'), PeepSo::PLUGIN_VERSION, 'all');
        wp_enqueue_style('peepso-giphy');

        wp_register_script('peepso-giphy', plugin_dir_url(__FILE__) . 'assets/js/bundle.min.js', array('peepso'), PeepSo::PLUGIN_VERSION, TRUE);
        wp_localize_script('peepso-giphy', 'peepsogiphydata', array(
            'dialogGiphyTemplate' => PeepSoTemplate::exec_template('giphy', 'dialog-giphy', NULL, TRUE),
            'giphy_api_key' => PeepSo::get_option('giphy_api_key', ''),
            'giphy_rating' => PeepSo::get_option('giphy_rating', ''),
            'giphy_rendition_comments' => PeepSo::get_option('giphy_rendition_comments', ''),
            'giphy_rendition_messages' => PeepSo::get_option('giphy_rendition_messages', ''),
            'giphy_display_limit' => PeepSo::get_option('giphy_display_limit', 25),
        ));

        wp_enqueue_script('peepso-giphy');
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
        $tabs['giphy'] = array(
            'label' => __('GIPHY', 'peepso-giphy'),
            'icon' => self::ICON,
            'tab' => 'giphy',
            'description' => __('GIPHY', 'peepso-giphy'),
            'function' => 'PeepSoConfigSectionGiphy',
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
     * Adds Giphy tab to the available post type options
     * @param  array $post_types
     * @return array
     */
    public function post_types($post_types)
    {
        $post_types['giphy'] = array(
            'icon' => 'giphy',
            'name' => __('Giphy', 'peepso-giphy'),
            'class' => 'ps-postbox__menu-item',
        );

        return $post_types;
    }

    /**
     * Displays the UI for the Giphy post type
     * @return string The input html
     */
    public function postbox_tabs($tabs)
    {
        $tabs['giphy'] = PeepSoTemplate::exec_template('giphy', 'postbox-giphy', NULL, TRUE);
        return $tabs;
    }


    /**
     * This function inserts the GIPHY UI on the comments box
     * @param array $interactions is the formated html code that get inserted in the postbox
     * @param int $post_id Post content ID
     */
    public function commentsbox_interactions($interactions, $post_id = FALSE)
    {
        wp_enqueue_script('peepso-giphy');

        $interactions['stickerpipe'] = array(
            'icon' => 'giphy',
            'class' => 'ps-list-item ps-js-comment-giphy',
            'title' => __('Send gif', 'peepso-giphy')
        );

        return ($interactions);
    }

    /**
     * This function inserts the photo UI on the comments box
     * @param array $interactions is the formated html code that get inserted in the postbox
     * @param int $post_id Post content ID
     */
    public function commentsbox_addons($addons, $post_id = FALSE)
    {
        $giphy = array();

        if ($post_id) {
            $giphy['src'] = get_post_meta($post_id, self::POST_META_KEY_GIPHY, true);
        }

        $html = PeepSoTemplate::exec_template('giphy', 'comment-addon', $giphy, TRUE);
        array_push($addons, $html);
        return ($addons);
    }

    /**
     * Checks if empty comment is allowed
     * @param string $allowed
     * @return boolean always returns TRUE
     */
    public function activity_allow_empty_comment($allowed)
    {
        $input = new PeepSoInput();
        // SQL injection safe - not used in SQL
        $giphy = $input->value('giphy', FALSE, FALSE);
        if(FALSE !== $giphy) {
            $allowed = TRUE;
        }

        return ($allowed);
    }

    /**
     * Displays the embeded media on the comment.
     * - peepso_activity_comment_attachment
     * @param WP_Post The current post object
     */
    public function comments_attach_giphy($stream_comment = NULL)
    {
        $giphy = get_post_meta($stream_comment->ID, self::POST_META_KEY_GIPHY, true);
        if(empty($giphy)) {
            return;
        }

        PeepSoTemplate::exec_template('giphy', 'comments-content', array('stream_comment' => $stream_comment, 'giphy' => $giphy));
    }

    /**
     * This function will save the postmeta for photo comments
     * @param int $post_id The post ID
     * @param int $act_id The activity ID
     */
    public function after_add_comment($post_id, $act_id)
    {
        $input = new PeepSoInput();

        // SQL injection safe - add_post_meta sanitizes it
        $giphy = $input->value('giphy', FALSE, FALSE);

        // #3048 Re-add url scheme if needed.
        if ( !empty($giphy)) {
            if ( ! preg_match( '/^[a-z]+:\/\//i', $giphy ) ) {
                $giphy = 'https://' . $giphy;
            }
        }

        if(FALSE !== $giphy) {
            add_post_meta($post_id, self::POST_META_KEY_GIPHY, $giphy, TRUE);
        }
    }

    /**
     * This function will save/update the postmeta for photo comments
     * @param object $post The post
     */
    public function after_save_comment($post_id, $activity)
    {
        $input = new PeepSoInput();

        // SQL injection safe - add_post_meta sanitizes it
        $giphy = $input->value('giphy', FALSE, FALSE);

        // delete photo
        if(FALSE === $giphy) {
            delete_post_meta($post_id, self::POST_META_KEY_GIPHY);
            return;
        }

        // #3048 Re-add url scheme if needed.
        if ( !empty($giphy)) {
            if ( ! preg_match( '/^[a-z]+:\/\//i', $giphy ) ) {
                $giphy = 'https://' . $giphy;
            }
        }

        $giphy_meta = get_post_meta($post_id, self::POST_META_KEY_GIPHY, TRUE);

        if(!empty($giphy_meta)) {
            if($giphy_meta === $giphy) {
                return; // same giphy
            }
            // delete previous giphy
            delete_post_meta($post_id, self::POST_META_KEY_GIPHY);
        }

        add_post_meta($post_id, self::POST_META_KEY_GIPHY, $giphy, TRUE);
    }

    /**
     * Change act_id on repost button act_id to follow parent's act_id.
     * @param array $options The default options per post
     * @return  array
     */
    public function modify_comments_actions($options)
    {
        global $post;

        $giphy = get_post_meta($post->ID, self::POST_META_KEY_GIPHY, true);
        $match = preg_match("/\[\[(.*?)\]\]/i", $giphy);
        if(!$match) {
            return ($options);
        }

        unset($options['edit']);

        return ($options);
    }

    /**
     * Add additional GIPHY addon to message input
     * @param array $options The additional addons to be attached to message input
     * @return  array
     */
    public function message_input_addons($addons)
    {
        $addons[] = PeepSoTemplate::exec_template('giphy', 'message-input', NULL, TRUE);
        return ($addons);
    }

    /**
     * This function manipulates giphy upload on chat box
     * @param int $post_id The post ID
     * @param int $act_id The activity ID
     */
    public function after_add_post($post_id, $act_id)
    {
        $input = new PeepSoInput();

        // SQL injection safe - add_post_meta sanitizes it
        $giphy = $input->value('giphy', '', FALSE);

        // SQL injection safe - not used in SQL
        if (!empty($giphy) && 'giphy' === $input->value('type', '', FALSE)) {
            // delete photo
            if(FALSE === $giphy) {
                delete_post_meta($post_id, self::POST_META_KEY_GIPHY);
                return;
            }

            $giphy_meta = get_post_meta($post_id, self::POST_META_KEY_GIPHY, TRUE);

            if(!empty($giphy_meta)) {
                if($giphy_meta === $giphy) {
                    return; // same giphy
                }
                // delete previous giphy
                delete_post_meta($post_id, self::POST_META_KEY_GIPHY);
            }

            add_post_meta($post_id, self::POST_META_KEY_GIPHY, $giphy, TRUE);
        }
    }
}

PeepSoGiphyPlugin::get_instance();

// EOF
