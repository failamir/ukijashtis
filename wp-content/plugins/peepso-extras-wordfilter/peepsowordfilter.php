<?php
/**
 * Plugin Name: PeepSo Extras: WordFilter
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: Filter unwanted words
 * Tags: peepso, wordfilter
 * Author: PeepSo
 * Version: 2.7.7
 * Author URI: https://301.es/?https://peepso.com
 * Copyright: (c) 2017 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordfilter-peepso
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */


class PeepSoWordFilterPlugin {

    private static $_instance = NULL;

    const PLUGIN_EDD = 87649;
    const PLUGIN_SLUG = 'wordfilter';

    const PLUGIN_NAME    = 'Extras: WordFilter';
    const PLUGIN_VERSION = '2.7.7';
    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2UyMmIzOTt9LmNscy0ye2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPndvcmRmaWx0ZXJfaWNvbjwvdGl0bGU+PGcgaWQ9IkJHIj48cmVjdCBjbGFzcz0iY2xzLTEiIHdpZHRoPSIxMjgiIGhlaWdodD0iMTI4IiByeD0iMjAiIHJ5PSIyMCIvPjwvZz48ZyBpZD0iTG9nbyI+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNNzEsNzcuNThINTAuMDlWNTYuNzFBMS42NCwxLjY0LDAsMCwwLDQ4LjM1LDU1LDI0LjM1LDI0LjM1LDAsMSwwLDcyLjcsNzkuMzIsMS42NCwxLjY0LDAsMCwwLDcxLDc3LjU4WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTg0Ljg3LDIzLjY3SDc0LjA5QTE5LjMsMTkuMywwLDAsMCw1NSw0Mi44VjcxYTEuNjQsMS42NCwwLDAsMCwxLjc0LDEuNzRIODQuODdBMTkuMywxOS4zLDAsMCwwLDEwNCw1My41OFY0Mi44QTE5LjMsMTkuMywwLDAsMCw4NC44NywyMy42N1ptLS4zNSwzOS42NWExLjY0LDEuNjQsMCwwLDEtMS43NCwxLjc0aC03YTEuNjQsMS42NCwwLDAsMS0xLjc0LTEuNzR2LTdhMS42NCwxLjY0LDAsMCwxLDEuNzQtMS43NGg3YTEuNjQsMS42NCwwLDAsMSwxLjc0LDEuNzRabS0uNy0xMS4xM2ExLjU5LDEuNTksMCwwLDEtMS43NCwxLjM5SDc2LjUyYTIuMzEsMi4zMSwwLDAsMS0xLjc0LTEuMzlMNzEuNjUsMzMuMDZBMi4wOSwyLjA5LDAsMCwxLDcyLDMxLjY3YTEuODEsMS44MSwwLDAsMSwxLjM5LS43SDg0Ljg3YTIuNjQsMi42NCwwLDAsMSwxLjM5LjdjLjM1LjM1LjM1LjcuMzUsMS4zOVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48L2c+PC9zdmc+';

    // how to render
    const WORDFILTER_FULL = 1;
    const WORDFILTER_MIDDLE = 2;

    // shift characters to obfuscate plain keywords
    const CHARACTER_SHIFT = 5;

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
     * @return Wordfilter-PeepSo instance
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

            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
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
        load_plugin_textdomain('wordfilter-peepso', FALSE, $path);
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
     * Display a message about PeepSo not present
     */
    public function peepso_disabled_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php
				echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'wordfilter-peepso'), self::PLUGIN_NAME),
                    ' <a href="plugin-install.php?tab=plugin-information&amp;plugin=peepso-core&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">',
                    __('Get it now!', 'wordfilter-peepso'),
                    '</a>';
                ?>
                <?php //echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'wordfilter-peepso'), self::PLUGIN_NAME);?>
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
        $install = new WordfilterPeepSoInstall();
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
        wp_register_script('peepso-admin-config-wordfilter', plugin_dir_url(__FILE__) . 'assets/js/peepso-wordfilter-admin-config.js', array('jquery'), self::PLUGIN_VERSION, TRUE);

        wp_enqueue_script('peepso-admin-config-wordfilter');
    }

    /**
     * Enqueue custom scripts and styles
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        if (PeepSo::get_option('wordfilter_enable', 0)) {
            wp_enqueue_script('peepso-wordfilter', plugin_dir_url(__FILE__) . 'assets/js/bundle.min.js',
                array('peepso'), self::PLUGIN_VERSION, TRUE);

            add_filter('peepso_data', function($data) {
                $keywords = explode( ',', PeepSo::get_option('wordfilter_keywords', '') );
                for ( $i = 0; $i < count( $keywords ); $i++ ) {
                    $keywords[ $i ] = $this->shift( $keywords[ $i ], self::CHARACTER_SHIFT );
                }

                $data['wordfilter'] = array(
                    'keywords' => $keywords,
                    'shift' => self::CHARACTER_SHIFT,
                    'mask' => PeepSo::get_option('wordfilter_character', '•'),
                    'type' => PeepSo::get_option('wordfilter_how_to_render', 1),
                    'filter_posts' => PeepSo::get_option('wordfilter_type_' . PeepSoActivityStream::CPT_POST, 1),
                    'filter_comments' => PeepSo::get_option('wordfilter_type_' . PeepSoActivityStream::CPT_COMMENT, 1),
                );

                if ( class_exists('PeepSoMessagesPlugin') ) {
                    $data['wordfilter']['filter_messages'] = PeepSo::get_option('wordfilter_type_' . PeepSoMessagesPlugin::CPT_MESSAGE, 1);
                }

                return $data;
            }, 10, 1);
        }
    }

    /**
     * Keyword characters shifter.
     *
     * @param string $keyword
     * @param int $shift
     * @return string
     */
    private function shift( $keyword = '', $offset = 0 )
    {
        $new_keyword = '';

        for ($i=0; $i < strlen($keyword); $i++) {
            $c = $keyword[$i];
            $islower = $c >= 'a' && $c <= 'z';
            $isupper = $c >= 'A' && $c <= 'Z';

            if ( $islower || $isupper  ) {
                $code = ord($c) - ($islower ? ord('a') : ord('A'));
                // shift the character code
                $code = $code + $offset;
                // normalize out-of-bound code
                $code = $code < 0 ? $code + 26 : $code % 26;
                // update character based on the new code
                $c = chr($code + ($islower ? ord('a') : ord('A')));
            }

            $new_keyword .= $c;
        }

        return $new_keyword;
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
        $tabs['wordfilter'] = array(
            'label' => __('WordFilter', 'wordfilter-peepso'),
            'icon' => self::ICON,
            'tab' => 'wordfilter',
            'description' => __('PeepSo - WordFilter', 'wordfilter-peepso'),
            'function' => 'PeepSoConfigSectionWordFilter',
            'cat'   => 'extras',
        );

        return $tabs;
    }
}

PeepSoWordFilterPlugin::get_instance();
