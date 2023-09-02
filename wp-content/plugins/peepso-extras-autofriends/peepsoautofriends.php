<?php
/**
 * Plugin Name: PeepSo Extras: AutoFriends
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: Plugin that makes everyone in the community friends with a specific user.
 * Tags: peepso, autofriends
 * Author: PeepSo
 * Version: 2.7.7
 * Author URI: https://301.es/?https://peepso.com
 * Copyright: (c) 2017 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: autofriends-peepso
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */


class PeepSoAutoFriendsPlugin {

    private static $_instance = NULL;

    const PLUGIN_EDD = 91427;
    const PLUGIN_SLUG = 'autofriends';
    
    const PLUGIN_NAME    = 'Extras: AutoFriends';
    const PLUGIN_VERSION = '2.7.7';
    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzliM2Q4OTt9LmNscy0ye2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPmF1dG9mcmllbmRzX2ljb248L3RpdGxlPjxnIGlkPSJCRyI+PHJlY3QgY2xhc3M9ImNscy0xIiB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgcng9IjIwIiByeT0iMjAiLz48L2c+PGcgaWQ9IkxvZ28iPjxnIGlkPSJuZXR3b3JrX3NlY3VyaXR5Ij48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik04OCw1Ny4zOXYtN2ExMy4zMywxMy4zMywwLDEsMC01LjMzLDB2NS43NkEyNC40OCwyNC40OCwwLDAsMCw4MCw1NmEyMy44OCwyMy44OCwwLDAsMC0xNSw1LjI2TDQ4LjQ1LDQ0LjY4QTEzLjMzLDEzLjMzLDAsMSwwLDM0LjY3LDUwLjRWNzIuMjdhMTMuMzQsMTMuMzQsMCwxLDAsNS4zMywwVjUwLjRhMTMuMjgsMTMuMjgsMCwwLDAsNC42OC0xLjk1TDYxLjI2LDY1QTI0LDI0LDAsMSwwLDg4LDU3LjM5Wm0tOCw0MUExOC4zNiwxOC4zNiwwLDEsMSw5OC4zNSw4MCwxOC4zOSwxOC4zOSwwLDAsMSw4MCw5OC4zNVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik03MS41Myw4OC44NGMwLDEuNDEsMy4xOCwyLjgyLDguNDcsMi44Miw1LDAsOC40Ny0xLjQxLDguNDctMi44MiwwLTIuODItMy4zMi01LjY1LTguNDctNS42NVM3MS41Myw4Niw3MS41Myw4OC44NFoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik04Ny4wNiw3NC43M2E3LjA2LDcuMDYsMCwxLDAtMi4wNyw1QTcsNywwLDAsMCw4Ny4wNiw3NC43M1oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48L2c+PC9nPjwvc3ZnPg==';

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
            add_action('peepso_init', array(&$this, 'init'));
        }
    }

    /**
     * Retrieve singleton class instance
     * @return PeepSoAutoFriends instance
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

            // JS file
            add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

            // config tabs
            add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));
        } else {
            if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
                return;
            }
        }

        // @since 1.9.9 - simply hook into the WP user creation event
        add_action('user_register', array(&$this, 'autofriends_new'));
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
        load_plugin_textdomain('autofriends-peepso', FALSE, $path);
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

        if (!class_exists('PeepSoFriendsPlugin')) {
            add_action('admin_notices', array(&$this, 'friendso_disabled_notice'));
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
     * Display a message about PeepSo not present
     */
    public function peepso_disabled_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php 
                echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'autofriends-peepso'), self::PLUGIN_NAME),
                    ' <a href="plugin-install.php?tab=plugin-information&amp;plugin=peepso-core&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">',
                    __('Get it now!', 'autofriends-peepso'),
                    '</a>';
                ?>
            </strong>
        </div>
        <?php
    }

    /**
     * Display a message about FriendSo not present
     */
    public function friendso_disabled_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php 
                echo __('Please make sure FriendSo is installed and activated.', 'autofriends-peepso'),
                    ' <a href="https://www.peepso.com/downloads/friendso/" target="_blank">',
                    __('Get it now!', 'autofriends-peepso'),
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
        $install = new AutoFriendsPeepSoInstall();
        $res = $install->plugin_activation();
        if (FALSE === $res) {
            // error during installation - disable
            deactivate_plugins(plugin_basename(__FILE__));
        }
        return (TRUE);
    }

    public function admin_enqueue_scripts()
    {
        wp_register_style('autocompleteautofriends-css', plugin_dir_url(__FILE__) . 'assets/css/jquery.auto-complete.css', NULL, self::PLUGIN_VERSION, 'all');
        wp_register_script('autocompleteautofriends-js', plugin_dir_url(__FILE__) . 'assets/js/jquery.auto-complete.min.js', array('jquery'));
    	wp_register_script('adminuserautofriends-js', plugin_dir_url(__FILE__) . 'assets/js/adminautofriends.js',
            array('peepso'), self::PLUGIN_VERSION, TRUE);
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
        $tabs['autofriends'] = array(
            'label' => __('AutoFriends', 'autofriends-peepso'),
            'icon' => self::ICON,
            'tab' => 'autofriends',
            'description' => __('PeepSo - AutoFriends', 'autofriends-peepso'),
            'function' => 'PeepSoConfigSectionAutoFriends',
            'cat'   => 'extras',
        );

        return $tabs;
    }

    /**
     * FRONTEND
     * ========
     *
     */

    public function autofriends_new($to_user_id) {
        $users = new PeepSoUserAutoFriendsModel();
        $items = $users->get_users();

        if(count($items)>0) {
            foreach ($items as $af) {
                $from_user_id = $af['af_user_id'];
                $peepso_friends = PeepSoFriendsPlugin::get_instance();
                $peepso_friends->model->add_friend($from_user_id, $to_user_id);
            }
        }
    }
}

PeepSoAutoFriendsPlugin::get_instance();
