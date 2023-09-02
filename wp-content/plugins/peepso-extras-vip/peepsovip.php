<?php
/**
 * Plugin Name: PeepSo Extras: VIP
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: Assign custom badges to users
 * Tags: peepso, vip
 * Author: PeepSo
 * Version: 2.7.7
 * Author URI: https://301.es/?https://peepso.com
 * Copyright: (c) 2017 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-vip
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class PeepSoVIPPlugin {
    private static $_instance = NULL;

    const PLUGIN_EDD = 97358;
    const PLUGIN_SLUG = 'vip';

    const PLUGIN_NAME    = 'Extras: VIP';
    const PLUGIN_VERSION = '2.7.7';
    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

    const VIP_ICON_BEFORE_FULLNAME = 0;
    const VIP_ICON_AFTER_FULLNAME = 1;

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzFhMWExYTt9LmNscy0ye2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPnZpcF9pY29uPC90aXRsZT48ZyBpZD0iQkciPjxyZWN0IGNsYXNzPSJjbHMtMSIgd2lkdGg9IjEyOCIgaGVpZ2h0PSIxMjgiIHJ4PSIyMCIgcnk9IjIwIi8+PC9nPjxnIGlkPSJMb2dvIj48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0zNy42LDg4LjksMzIsNTUuN2wyMCw4LDEyLTI0LDEyLDI0LDIwLThMOTAuNCw4OC45YTguMTQsOC4xNCwwLDAsMS04LDYuOEg0NS4yQTcuNzMsNy43MywwLDAsMSwzNy42LDg4LjlaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIDApIi8+PGNpcmNsZSBjbGFzcz0iY2xzLTIiIGN4PSI2NCIgY3k9IjM5LjciIHI9IjgiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMiIgY3g9Ijk2IiBjeT0iNTUuNyIgcj0iOCIvPjxjaXJjbGUgY2xhc3M9ImNscy0yIiBjeD0iMzIiIGN5PSI1NS43IiByPSI4Ii8+PC9nPjwvc3ZnPg==';

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

        // translations
        add_action('plugins_loaded', array(&$this, 'load_textdomain'));

        // Activation
        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        /** VERSION LOCKED hooks **/
        if(self::ready()){
            add_action('peepso_init', array(&$this, 'init'));
        }
    }

    /**
     * Retrieve singleton class instance
     * @return PeepSoVIPPlugin instance
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

            // add vip to profile
            if(PeepSo::is_admin()) {
                add_action('show_user_profile', array(&$this, 'vip_user_profile_fields'));
                add_action('edit_user_profile', array(&$this, 'vip_user_profile_fields'));
                add_action( 'personal_options_update', array(&$this, 'save_vip_user_profile_fields' ));
                add_action( 'edit_user_profile_update', array(&$this, 'save_vip_user_profile_fields' ));
            }

            // JS file
            add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

            // config tabs
            add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));

            add_filter('peepso_admin_manage_tabs', function($tabs){
                $tabs['vip-icons'] = array(
                    'label' => __('VIP Icons', 'vidso'),
                    'icon' => self::ICON,
                    'tab' => 'vip-icons',
                    'description' => '',
                    'function' => array('PeepSoVipIconAdmin', 'administration'),
                    'cat'   => 'extras',
                );

                return $tabs;
            });

		    add_action('manage_users_columns', array(&$this, 'filter_user_list_columns'));
		    add_action('manage_users_custom_column', array(&$this, 'filter_custom_user_column'), 10, 3);
        } else {
            if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
                return;
            }

            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        }
        #add_filter('peepso_before_get_fullname', array(&$this, 'before_fullname'), 10, 2);
        add_action('peepso_action_render_user_name_before', array(&$this, 'before_display_name'), 10, 2);
        add_action('peepso_action_render_user_name_after', array(&$this, 'after_display_name'), 10, 2);

        // Add VIP icons to UserBar widget
        add_action('peepso_action_userbar_user_name_before', array(&$this, 'before_display_name'), 10, 2);
        add_action('peepso_action_userbar_user_name_before', array(&$this, 'after_display_name'), 10, 2);

        // AJAX endpoint to see user's icons
        if(get_current_user_id()) {
            add_action('wp_ajax_peepso_vip_user_icons', array(&$this, 'user_icons'));
            add_action('wp_ajax_nopriv_peepso_vip_user_icons', array(&$this, 'user_icons'));
        }

        add_filter('peepso_hovercard', function($data, $user_id) {
            $data['vip'] = $this->get_user_icons($user_id);
            return $data;
        }, 10, 2);
    }

    public function get_user_icons($user_id) {
        $icons = new PeepSoVipIconsModel();
        $user_icons = (array) get_the_author_meta('peepso_vip_user_icon', $user_id);
        $result = array();

        foreach ($icons->vipicons as $id => $icon) {
            if (1 != $icon->published) {
                continue;
            }
            if ( !in_array($id, $user_icons) ) {
                continue;
            }
            $result[$id] = $icon;
        }

        return $result;
    }

    public function user_icons() {
        $input = new PeepSoInput();
        $user_id = $input->int('user_id',0);
        $result = $this->get_user_icons($user_id);
        die( json_encode($result) );
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
        load_plugin_textdomain('peepso-vip', FALSE, $path);
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
                echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-vip'), self::PLUGIN_NAME),
                ' <a href="plugin-install.php?tab=plugin-information&amp;plugin=peepso-core&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">',
                __('Get it now!', 'peepso-vip'),
                '</a>';
                ?>
            </strong>
        </div>
        <?php
    }

    /**
     * Register a tab in the PeepSo Dashboard
     * @param $tabs array
     * @return array
     */
    public function filter_admin_dashboard_tabs($tabs)
    {
        $tabs['red']['peepso-vip'] = array(
            'slug' => 'peepso-vip',
            'menu' => __('VIP Icons', 'peepso-vip'),
            'icon' => 'star-filled',
            'count' => '',
            'function' => array('PeepSoVipIconAdmin', 'administration'),
        );

        return $tabs;
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
        $install = new PeepSoVIPInstall();
        $res = $install->plugin_activation();
        if (FALSE === $res) {
            // error during installation - disable
            deactivate_plugins(plugin_basename(__FILE__));
        }
        return (TRUE);
    }

    public function admin_enqueue_scripts()
    {
        wp_enqueue_style('peepso-vip-admin', plugin_dir_url(__FILE__) . 'assets/css/admin.css');
    }

    /**
     * Enqueue custom scripts and styles
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('peepso-vip', plugin_dir_url(__FILE__) . 'assets/js/bundle.min.js',
            array('peepso'), self::PLUGIN_VERSION, TRUE);
        add_filter('peepso_data', function($data) {
            $data['vip'] = array(
                'popoverEnable' => PeepSo::get_option('hovercards_enable', 1) == 0,
                'popoverTemplate' => PeepSoTemplate::exec_template('peepsovip', 'popover', NULL, TRUE),
                'hovercardTemplate' => PeepSoTemplate::exec_template('peepsovip', 'hovercard', NULL, TRUE),
            );
            return $data;
        }, 10, 1 );
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
        $tabs['peepsovip'] = array(
            'label' => __('VIP', 'peepso-vip'),
            'icon' => self::ICON,
            'tab' => 'peepsovip',
            'description' => __('PeepSo - VIP', 'peepso-vip'),
            'function' => 'PeepSoConfigSectionVIP',
            'cat'   => 'extras',
        );

        return $tabs;
    }

    public function vip_user_profile_fields($user)
    {
        ?>
        <h3><?php echo __('VIP', 'peepso-vip');?></h3>
        <table class="form-table">
            <tr class="user-admin-color-wrap">
                <th scope="row"><?php echo __('Icon to display next to name/username in PeepSo', 'peepso-vip');?></th>
                <td>
                    <fieldset id="vip-icons" class="scheme-list">
                        <legend class="screen-reader-text"><span><?php echo __('Icon to display', 'peepso-vip');?></span></legend>
                        <?php
                        $PeepSoVipIconsModel = new PeepSoVipIconsModel();
                        $selectedIcon = get_the_author_meta( 'peepso_vip_user_icon', $user->ID );
                        if (!is_array($selectedIcon)) {
                            $selectedIcon = [$selectedIcon];
                        }
                        foreach ($PeepSoVipIconsModel->vipicons as $key => $value) {
                            ?>
                            <div class="color-option">
                                <input name="peepso_vip_user_icon[]" id="vip_icon_<?php echo $key;?>" type="checkbox" value="<?php echo $value->post_id;?>" class="tog" <?php echo (in_array($value->post_id, $selectedIcon)) ? 'checked="checked"' : '';?>>
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

        <?php
    }

    public function save_vip_user_profile_fields($user_id)
    {
        if ( !current_user_can( 'edit_user', $user_id ) ) {
            return (FALSE);
        }

        update_user_meta( $user_id, 'peepso_vip_user_icon', $_POST['peepso_vip_user_icon'] );
    }

    /**
     * vip core
     *
     */

    public function more_icons($amount, $last_icon, $user_id) {

        if(!PeepSo::get_option('vipso_display_more_icons_count', 0)){
            return;
        }

        if($amount == 1) {
            echo $last_icon;
        }

        if($amount>1) {
            ?>
            <div class="ps-vip__counter ps-js-vip-badge" data-id="<?php echo $user_id ?>">
                +<?php echo $amount; ?></div>
            <?php
        }
    }

    public function before_display_name($user_id)
    {
        $icons = get_the_author_meta( 'peepso_vip_user_icon', $user_id ) ;
        $icons = (!is_array($icons) && !empty($icons)) ? [$icons] : $icons;
        $display = PeepSo::get_option('vipso_where_to_display', 1);
        $limit = PeepSo::get_option('vipso_display_how_many', 10);
        if( $display == self::VIP_ICON_BEFORE_FULLNAME && is_array($icons) && count($icons) > 0 && $limit>0) {
            $PeepSoVipIconsModel = new PeepSoVipIconsModel();

            $i=0;
            $class = '';
            $more = 0;
            $last_icon = '';

            foreach ($icons as $icon) {

                $vipicon = $PeepSoVipIconsModel->vipicon($icon);
                if(intval($vipicon->published) == 1) {

                    if($i>=$limit) {
                        $class = 'ps-img-vipicons--hidden ps-js-vip-badge-hidden';
                        $more++;
                    }

                    echo '<img src="' . $vipicon->icon_url . '" alt="'.$vipicon->title.'"  title="'.$vipicon->title
                        .'" class="ps-img-vipicons ps-js-vip-badge '.$class.'" data-id="'.$user_id.'"> ';

                    $last_icon = '<img src="' . $vipicon->icon_url . '" alt="'.$vipicon->title.'"  title="'.$vipicon->title
                        .'" class="ps-img-vipicons ps-js-vip-badge" data-id="'.$user_id.'"> ';

                    $i++;
                }
            }

            echo $this->more_icons($more, $last_icon, $user_id);
        }

    }

    public function after_display_name($user_id)
    {
        $icons = get_the_author_meta( 'peepso_vip_user_icon', $user_id );
        $icons = (!is_array($icons) && !empty($icons)) ? [$icons] : $icons;
        $display = PeepSo::get_option('vipso_where_to_display', 1);
        $limit = PeepSo::get_option('vipso_display_how_many', 10);
        if( $display == self::VIP_ICON_AFTER_FULLNAME && is_array($icons) && count($icons) > 0) {
            $PeepSoVipIconsModel = new PeepSoVipIconsModel();

            $i=0;
            $class = '';
            $more = 0;
            $last_icon = '';

            foreach ($icons as $icon) {

                $vipicon = $PeepSoVipIconsModel->vipicon($icon);
                if(intval($vipicon->published) == 1) {

                    if($i>=$limit) {
                        $class = 'ps-img-vipicons--hidden ps-js-vip-badge-hidden';
                        $more++;
                    }

                    echo ' <img src="' . $vipicon->icon_url . '" alt="'.$vipicon->title.'" title="'.$vipicon->title
                        .'" class="ps-img-vipicons ps-js-vip-badge '.$class.'" data-id="'.$user_id.'">';

                    $last_icon = '<img src="' . $vipicon->icon_url . '" alt="'.$vipicon->title.'"  title="'.$vipicon->title
                        .'" class="ps-img-vipicons ps-js-vip-badge" data-id="'.$user_id.'"> ';

                    $i++;
                }
            }

            echo $this->more_icons($more, $last_icon, $user_id);
        }
    }

    public function filter_user_list_columns($columns)
	{
        $columns['peepso_vip'] = __('PeepSo VIP icons', 'peepso-core');
		return $columns;
	}

    public function filter_custom_user_column($value, $column, $id)
	{
        $PeepSoVipIconsModel = new PeepSoVipIconsModel();

		switch ($column)
		{
            case 'peepso_vip':
                $icons = get_the_author_meta('peepso_vip_user_icon', $id);
                $icons = (!is_array($icons) && !empty($icons)) ? [$icons] : $icons;
                if (is_array($icons) && count($icons) > 0) {
                    foreach ($icons as $icon) {
                        $vipicon = $PeepSoVipIconsModel->vipicon($icon);
                        if (intval($vipicon->published) == 1) {
                            $value .= ' <img width="16px" src="' . $vipicon->icon_url . '" alt="' . $vipicon->title . '" title="' .
                                $vipicon->title . '" data-id="' . $id . '">';
                        }
                    }
                }
                break; 
		}
		return $value;
	}
}

PeepSoVIPPlugin::get_instance();