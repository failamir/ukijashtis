<?php
/**
 * Plugin Name: PeepSo Monetization: Paid Memberships Pro
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: <strong>Requires the Paid Memberships Pro plugin.</strong> Paid Memberships Pro bridge.
 * Author: PeepSo
 * Author URI: https://301.es/?https://peepso.com
 * Version: 2.7.7
 * Copyright: (c) 2016 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-pmp
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */


class PeepSoPMP
{
	private static $_instance = NULL;

	const PLUGIN_NAME	 = 'Monetization: Paid Memberships Pro';
	const PLUGIN_VERSION = '2.7.7';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
    const PLUGIN_EDD = 43387;
    const PLUGIN_SLUG = 'peepso-pmp-integration';

        const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2YyZjJmMjt9LmNscy0ye2ZpbGw6bm9uZTt9LmNscy0ze2ZpbGw6IzI1OTZjNzt9LmNscy00e2ZpbGw6Izc3OWYyZTt9LmNscy01e2ZpbGw6Izc3Nzg3Yjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPnBtcF9pY29uPC90aXRsZT48ZyBpZD0iQkciPjxyZWN0IGNsYXNzPSJjbHMtMSIgd2lkdGg9IjEyOCIgaGVpZ2h0PSIxMjgiIHJ4PSIyMCIgcnk9IjIwIi8+PC9nPjxnIGlkPSJMb2dvIj48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik02NS40NywzNS4zMWE3LDcsMCwwLDAtLjkxLTMuMjUsNiw2LDAsMCwwLTIuMTMtMi4xNSw2Ljg1LDYuODUsMCwwLDAtMy0uOTIsOS4wOCw5LjA4LDAsMCwwLTMuNDMuNDEsMTEuNjksMTEuNjksMCwwLDAtMy4zLDEuNjUsMTMuMTYsMTMuMTYsMCwwLDAtMi42NSwyLjUxLDEyLjMzLDEyLjMzLDAsMCwwLTEuOCwzLjA4QTkuOTQsOS45NCwwLDAsMCw0Ny41Niw0MGwtLjE3LDUuNTEsMTguMzUtNC4yNVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cGF0aCBjbGFzcz0iY2xzLTMiIGQ9Ik05OS4wOCw0NS40N3EtMi4yOC44NS00LjU3LDEuNzdUOTAsNDkuMTFxLTIuMjMsMS00LjQ2LDJsLS4xMi4wNS0uMTEuMDUtLjExLjA1LS4xMS4wNXEtMS44LjgzLTMuNiwxLjd0LTMuNTMsMS43NnEtMS43NC44OS0zLjQ2LDEuODFUNzEuMSw1OC40M2wtMS4xOS42OC0xLjE4LjY4LTEuMTcuNjktMS4xNi43LTEsLjYxLTEsLjYyLTEsLjYyLTEsLjYzLS42NC40Mi0uNjQuNDMtLjY0LjQyLS42My40My0uMjIuMTUtLjIyLjE1LS4yMi4xNUw1OSw2NmwtLjc3LjU0LS43Ny41NC0uNzYuNTUtLjc2LjU1UTUzLjQ5LDcwLDUxLjEyLDcxLjl0LTQuNTUsMy45MnEtMi4xOSwyLTQuMjQsNC4xdC0zLjkzLDQuM2wtLjM0LjQtLjM0LjQtLjM0LjQxLS4zMy40MS0uMjUuMzEtLjI1LjMxLS4yNS4zMUwzNiw4Ny4xbC0uMTMtLjE2LS4xMy0uMTYtLjEzLS4xNS0uMTMtLjE1LTEuMi0xLjQtMS4wNy0xLjItMS0xLjA2LS45LTEtLjIzLS4yNS0uMjMtLjI1LS4yMy0uMjUtLjIyLS4yNS0xLTEuMWMtLjMzLS4zOC0uNjctLjc3LTEtMS4xOXMtLjcxLS44Ni0xLjA4LTEuMzUtLjc3LTEtMS4xOC0xLjU1bC0uNTgtLjc4TDI1LDc0bC0uNjMtLjktLjY3LTFjLjQ1LDEuNDUuODgsMi44MiwxLjI5LDQuMTFzLjgyLDIuNTYsMS4yMSwzLjc1Ljc3LDIuMzQsMS4xNCwzLjQzLjc0LDIuMTcsMS4xMSwzLjJsLjI1LjcxLjI1LjcxLjI1LjcuMjYuNy4xMi4zMi4xMi4zMi4xMi4zMi4xMi4zMi4xMi4zMy4xMy4zMy4xMy4zMy4xMy4zMy4wOS4yNC4wOS4yNC4wOS4yNC4wOS4yNCwwLC4xLDAsLjEsMCwuMSwwLC4xLjEzLjMzLjEzLjMzLjEzLjMzLjEzLjMzLDAsLjExLjA3LDAsLjgsMkwzMy4yMSw5OWwuOTMsMi4yNiwxLDIuNDdxLjg5LTEuNjMsMS44Mi0zLjIxdDEuODktMy4xMnExLTEuNTQsMi0zdDItM2wwLS4wNiwwLS4wNiwwLS4wNiwwLS4wNi40LS41Ni40LS41Ni40LS41NS40LS41NS4zNS0uNDcuMzUtLjQ3LjM1LS40Ni4zNi0uNDYuMDYtLjA5LjA2LS4wOC4wNi0uMDguMDYtLjA4LjQzLS41Ni40My0uNTUuNDMtLjU1LjQ0LS41NC40NC0uNTUuNDQtLjU0LjQ1LS41NC40NS0uNTRxMS42LTEuOSwzLjI1LTMuNzJUNTYuNDMsNzVxMS43Mi0xLjc2LDMuNDgtMy40NnQzLjYtMy4zM2wuNy0uNjIuNzEtLjYyLjcxLS42MS43MS0uNjEuMjEtLjE4LjIxLS4xOC4yMS0uMTguMjEtLjE4LjU3LS40Ny41Ny0uNDYuNTctLjQ2LjU3LS40Ni44OC0uNy44OS0uNjkuODktLjY4LjktLjY3LDEtLjc1LDEtLjc0LDEtLjczLDEtLjcyLDItMS4zNCwyLTEuMywyLTEuMjYsMi0xLjIxcTIuMjUtMS4zMSw0LjUyLTIuNXQ0LjYtMi4yOHEyLjMzLTEuMDksNC42Ni0ydDQuNy0xLjc3UTEwMS4zOSw0NC42MSw5OS4wOCw0NS40N1oiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cGF0aCBjbGFzcz0iY2xzLTQiIGQ9Ik05My42Miw2Ni4zM2wyLjkxLDIwLjI2TDUwLjMsODYuMzRsLS4yNC4yOS0uMjQuMjktLjI0LjI5LS4yNC4yOS0uMjIuMjdMNDguOSw4OGwtLjIxLjI3LS4yMS4yNyw0OS45Mi45TDk0LjkzLDY2LjIxWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIvPjxwYXRoIGNsYXNzPSJjbHMtNCIgZD0iTTUzLjcxLDgyLjM3bC0uNS41Ny0uNS41Ny0uNS41Nyw0MS0uMzJMOTAuNTYsNjQuMTRsLS4zMi0yLjM4TDg4LjUzLDYyLDkxLDgxLjA3bC0zNi43NC43NVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48cGF0aCBjbGFzcz0iY2xzLTQiIGQ9Ik04NSw1Ny43NWwtMi42My4zNS0uODEuNTItLjguNTItLjguNTMtLjguNTMtMS4wNy43M0w3Nyw2MS42NmwtMS4wNi43NS0xLC43Ni0uOTIuNjgtLjkxLjY5LS45MS42OS0uOS43LS41OS40Ni0uNTguNDctLjU4LjQ3LS41OC40Ny0uMjIuMTgtLjIyLjE4LS4yMi4xOC0uMjIuMTgtLjczLjYxLS43My42Mi0uNzIuNjMtLjcyLjYzcS0xLjE0LDEtMi4yNiwydC0yLjIyLDIuMXEtMS4xLDEuMDYtMi4xOCwyLjE1dC0yLjEzLDIuMmwzMS0xTDg1LjIyLDYwWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIvPjxwYXRoIGNsYXNzPSJjbHMtNSIgZD0iTTgyLDQxLjI3YTMsMywwLDAsMC0uNS0xLjM2LDMuMTIsMy4xMiwwLDAsMC0xLTEsMy43NiwzLjc2LDAsMCwwLTEuNDEtLjQ3LDQuNTUsNC41NSwwLDAsMC0xLjYyLjA5bC0zLjk1Ljkxdi0uMWwtMS4zMi4zMS0uNDMtNmExMS4wOCwxMS4wOCwwLDAsMC0xLjctNS4zQTkuODIsOS44MiwwLDAsMCw2Ni4zOSwyNWExMS40NCwxMS40NCwwLDAsMC00Ljg4LTEuMjgsMTUuMTMsMTUuMTMsMCwwLDAtNS40OS44MywxOS4xMywxOS4xMywwLDAsMC01LjE3LDIuNywyMS4yMSwyMS4yMSwwLDAsMC00LjExLDRBMjAsMjAsMCwwLDAsNDMuOTIsMzZhMTYuMjcsMTYuMjcsMCwwLDAtMS4yMiw1LjNsLS4yNyw1LjMtLjgyLjE5di4wOGwtMy4xNy43M2EzLjA3LDMuMDcsMCwwLDAtMSwuNDcsNCw0LDAsMCwwLS44OC44MkE0LjQ5LDQuNDksMCwwLDAsMzUuODksNTBhNCw0LDAsMCwwLS4yOSwxLjIxbC0yLjE3LDI4LDIuMzcsMi42OC41NS0xMC4wNmE5Ljg0LDkuODQsMCwwLDEsLjYzLTMsOS4zMyw5LjMzLDAsMCwxLDEuNC0yLjQ4LDcuNzksNy43OSwwLDAsMSwyLTEuOCw2LjA4LDYuMDgsMCwwLDEsMi40My0uODhsMS4wNy0uMTNhMy42NywzLjY3LDAsMCwxLTEuMTgtLjY0LDQsNCwwLDAsMS0uODktMSw0LjY4LDQuNjgsMCwwLDEtLjU1LTEuMzNBNS42OCw1LjY4LDAsMCwxLDQxLjEsNTlhNi41Niw2LjU2LDAsMCwxLC40My0yLjA3LDYuNzUsNi43NSwwLDAsMSwxLTEuNzhBNi4wNyw2LjA3LDAsMCwxLDQ0LDUzLjgxYTQuOSw0LjksMCwwLDEsMS43OC0uNzJoMGEzLjg5LDMuODksMCwwLDEsMS44LjA4LDMuNjcsMy42NywwLDAsMSwxLjQ5Ljg1LDQuMjEsNC4yMSwwLDAsMSwxLDEuNTEsNS4zNCw1LjM0LDAsMCwxLC4zNywyLDYuNDIsNi40MiwwLDAsMS0uMjQsMS43MSw2Ljc2LDYuNzYsMCwwLDEtLjY3LDEuNTcsNi41Niw2LjU2LDAsMCwxLTEsMS4zMyw1Ljc5LDUuNzksMCwwLDEtMS4yOCwxbC45NC0uMTJhNS43Miw1LjcyLDAsMCwxLDEuODguMDcsNS42NCw1LjY0LDAsMCwxLDEuNzEuNjZBNi4xOSw2LjE5LDAsMCwxLDUzLjI0LDY1YTcuMDksNy4wOSwwLDAsMSwxLDEuNDlMNTUsNjUuOWwwLTMuM2E2LjQsNi40LDAsMCwxLC4zNy0yLjIyLDYuNTYsNi41NiwwLDAsMSwxLjA3LTEuOUE2LjIsNi4yLDAsMCwxLDU4LDU3LjA3YTUuNDgsNS40OCwwLDAsMSwyLS43NGwuODktLjE0YTMuMywzLjMsMCwwLDEtMS0uNDQsMy4yNiwzLjI2LDAsMCwxLS44MS0uNzQsMy40NCwzLjQ0LDAsMCwxLS41My0xLDMuODUsMy44NSwwLDAsMS0uMjEtMS4xNyw0LjM1LDQuMzUsMCwwLDEsLjI2LTEuNTcsNC43OSw0Ljc5LDAsMCwxLDEuOTUtMi40M0E0LjMxLDQuMzEsMCwwLDEsNjIsNDguMjhhMy43LDMuNywwLDAsMSwxLjUyLDBBMy4zMSwzLjMxLDAsMCwxLDY1Ljc1LDUwYTMuNzEsMy43MSwwLDAsMSwuNCwxLjUxQTQuMjgsNC4yOCwwLDAsMSw2Niw1Mi44LDQuNiw0LjYsMCwwLDEsNjUuNTMsNTRhNC44LDQuOCwwLDAsMS0uNzksMSw0LjY3LDQuNjcsMCwwLDEtMSwuNzdsLjc4LS4xMmE1LjQxLDUuNDEsMCwwLDEsMS4yMSwwLDUuMjQsNS4yNCwwLDAsMSwxLjE1LjIyLDUuMTMsNS4xMywwLDAsMSwxLjA1LjQ3LDUuMjYsNS4yNiwwLDAsMSwuOTMuNjlsLS4yNi4xNmguMzNsMS4xNy0uNjlMNzAsNTVhNC4zNCw0LjM0LDAsMCwxLC4yMy0xLjY5QTQuNzYsNC43NiwwLDAsMSw3MSw1MS44YTUsNSwwLDAsMSwxLjMtMS4xMkE0LjgyLDQuODIsMCwwLDEsNzQsNTAuMDdsLjc0LS4xMmEzLDMsMCwwLDEtLjg3LS4zMSwyLjc3LDIuNzcsMCwwLDEtLjctLjU1LDIuNjYsMi42NiwwLDAsMS0uNDktLjc0LDIuNzgsMi43OCwwLDAsMS0uMjItLjksMy4wNSwzLjA1LDAsMCwxLC4xNS0xLjIyLDMuNDcsMy40NywwLDAsMSwuNi0xLjA4LDMuNzEsMy43MSwwLDAsMSwxLS44MywzLjY2LDMuNjYsMCwwLDEsMS4yMi0uNDcsMy4zNywzLjM3LDAsMCwxLDEuMjcsMCwzLDMsMCwwLDEsMS4wOS40NSwyLjY4LDIuNjgsMCwwLDEsMS4xNywyLDMsMywwLDAsMS0uMDcsMSwzLjMxLDMuMzEsMCwwLDEtLjM2LjkxLDMuNzQsMy43NCwwLDAsMS0xLjQzLDEuMzlsLjYzLS4xMWE0LjgzLDQuODMsMCwwLDEsMS4wOC0uMDYsNC41OCw0LjU4LDAsMCwxLDEsLjE4LDQuMzIsNC4zMiwwLDAsMSwuOTIuNGwuMjYuMTdoMGwuMzgtLjE5Ljc4LS4zOC43Ni0uMzdaTTQ3LjM4LDQ1LjU0LDQ3LjU2LDQwYTkuOTQsOS45NCwwLDAsMSwuNzMtMy4zOCwxMi4zMywxMi4zMywwLDAsMSwxLjgtMy4wOCwxMy4xNiwxMy4xNiwwLDAsMSwyLjY1LTIuNTFBMTEuNjksMTEuNjksMCwwLDEsNTYsMjkuNDEsOS4wOCw5LjA4LDAsMCwxLDU5LjQ3LDI5YTYuODUsNi44NSwwLDAsMSwzLC45Miw2LDYsMCwwLDEsMi4xMywyLjE1LDcsNywwLDAsMSwuOTEsMy4yNWwuMjYsNloiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48L2c+PC9zdmc+';
		
	public $widgets = array(
	);

    private static function ready_thirdparty() {
        $result = TRUE;

        if ( !defined('PMPRO_DIR') ) {
            $result = FALSE;
        }

        return $result;
    }

    private static function ready() {
        return(self::ready_thirdparty() && class_exists('PeepSo') && self::PLUGIN_VERSION.self::PLUGIN_RELEASE == PeepSo::PLUGIN_VERSION.PeepSo::PLUGIN_RELEASE);
    }


    private function __construct()
	{
		if ( ! session_id() ) {
			session_start();
		}

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
        add_filter('peepso_all_plugins', function($plugins){
            $plugins[plugin_basename(__FILE__)] = get_class($this);
            return $plugins;
        });

        // Translations
        add_action('plugins_loaded', function(){
            $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
            load_plugin_textdomain('peepso-pmp', FALSE, $path);
        });

        // Activation
        register_activation_hook(__FILE__, array(&$this, 'activate'));

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            add_action('peepso_init', array(&$this, 'init'));
            add_filter('peepso_widgets', array(&$this, 'hide_widget'), 40, 1);
        }
	}

	/*
	 * retrieve singleton class instance
	 * @return instance reference to plugin
	 */
	public static function get_instance()
	{
		if (NULL === self::$_instance)
			self::$_instance = new self();
		return (self::$_instance);
	}

	/*
	 * Initialize the PeepSoPMP plugin
	 */
	public function init()
	{
		PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
		PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

		if (is_admin()) {

            add_action('admin_init', array(&$this, 'peepso_check'));
            add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));

            // PeepSo PMP Integration with PeepSo Groups
            if(class_exists('PeepSoGroupsPlugin')) {
            	add_action('pmpro_membership_level_after_other_settings', array(&$this, 'action_after_other_settings_group'));
            	add_action('pmpro_save_membership_level', array(&$this, 'action_after_save_membership_level_group'), 10, 1);
            	add_action('pmpro_delete_membership_level', array(&$this, 'action_after_delete_membership_level_group'), 10, 1);
            } 

            // PeepSo PMP Integration with PeepSo VIP
            if(class_exists('PeepSoVIPPlugin')) {
            	add_action('pmpro_membership_level_after_other_settings', array(&$this, 'action_after_other_settings_vip'));
            	add_action('pmpro_save_membership_level', array(&$this, 'action_after_save_membership_level_vip'), 10, 1);
            	add_action('pmpro_delete_membership_level', array(&$this, 'action_after_delete_membership_level_vip'), 10, 1);

            	add_action( 'personal_options_update', array(&$this, 'save_membership_user_profile_fields' ), 100);
                add_action( 'edit_user_profile_update', array(&$this, 'save_membership_user_profile_fields' ), 100);
			}
			
            add_action('pmpro_delete_order', array(&$this, 'action_delete_order'), 20, 2);
        } else {
	
            if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
                return;
            }

			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

			// call after registration complete
			add_action('peepso_register_new_user', array(&$this, 'redirect_to_pmpro'), 100, 1);
			add_action('peepso_register_segment_fail_membership', array(&$this, 'fail_membership'), 10, 1);

			#$registered_user_id = get_transient( 'peepso_user_id_after_register');
			//print_r($_COOKIE);
			$registered_user_id = isset($_SESSION['peepso_user_id_after_register'])? $_SESSION['peepso_user_id_after_register']:false;
			if ( false !== ( $registered_user_id ) ) {
				wp_set_current_user($registered_user_id);

				add_filter('pmpro_confirmation_url',array(&$this, 'modify_confirmation_url'));
				add_action('pmpro_after_checkout', array(&$this, 'after_checkout'), 10, 2);
			}

			add_action('pmpro_after_change_membership_level', array(&$this, 'after_change_membership_level'), 10, 3);

			if (PeepSo::get_option('pmp_integration_hide_membership_tab', 0) === 0) {
				add_filter('peepso_navigation_profile', function($links) {
					if(isset($links['_user_id']) && get_current_user_id() == $links['_user_id']) {
						$links['pmp'] = array(
							'href' => PeepSo::get_option('pmp_navigation_profile_slug', 'membership', TRUE),
							'label' => PeepSo::get_option('pmp_navigation_profile_label', __('Membership', 'peepso-pmp'), TRUE),
							'icon' => 'ps-icon-vcard'
						);
					}
	
					return $links;
				});
			}

			add_filter('peepso_mailqueue_allowed', function($user_id, $is_allowed) {
				$integration = intval(PeepSo::get_option('pmp_integration_enabled'));
				$email_notification = intval(PeepSo::get_option('pmp_integration_email_notification_enabled', 0));

				if (1 !== $integration || 1 === $email_notification) {
					return TRUE;
				}

				$allowed_caller = array(
					'send_activation',
					'resend_activation',
					'retrieve_password'
				);
				foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $caller) {
					if (in_array($caller['function'], $allowed_caller)) {
						return TRUE;
					}
				}

				global $wpdb;
				$sql = $wpdb->prepare("SELECT COUNT(*) AS total FROM $wpdb->pmpro_memberships_users WHERE user_id = %d AND status = 'active'", $user_id);
				$result = $wpdb->get_row($sql);
				if (isset($result) && $result->total > 0) {
					return TRUE;
				} else {
					return FALSE;
				}

			}, 10, 2);

            $profile_slug = PeepSo::get_option('pmp_navigation_profile_slug', 'membership', TRUE);
            add_action('peepso_profile_segment_'.$profile_slug,   function() {
                PeepSoTemplate::exec_template('pmp','profile-membership');
            });

            add_filter('peepso_groups_follower_send_notification_email', function($sendNotificationEmail, $user) {
            	//is there an end date?
				$membership_level = pmpro_getMembershipLevelForUser($user->get_id());
				$end_date = (!empty($membership_level) && !empty($membership_level->enddate)); // Returned as UTC timestamp
				$wp_tz =  get_option( 'timezone_string' );
			
				// Convert UTC to local time
	            if ( $end_date ) {
		            $membership_level->enddate = strtotime( $wp_tz, $membership_level->enddate );
	            }

	            if($end_date && (intval($membership_level->enddate) <= strtotime(current_time("timestamp")))) { 
	            	return FALSE;
	            }

            	return $sendNotificationEmail;
            }, 10, 2);
        }

        add_action('peepso_profile_completeness_redirect', array(&$this, 'action_membership_completeness_redirect'));

 		// Compare last version stored in transient with current version
 		if( $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE != get_transient($trans = 'peepso_'.$this::PLUGIN_SLUG.'_version')) {
 			set_transient($trans, $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE);
 			$this->activate();
 		}
	}

	/**
	 * PeepSo PMP integration membership completeness
	 * 
	 */
	public function action_membership_completeness_redirect() {

		if(!get_current_user_id()) {
			return false;
		}

		// check if site have exist levels
		$levels = pmpro_getAllLevels();
		if(count($levels) === 0 ) {
			return true;
		}

		if(pmpro_hasMembershipLevel()) {
			return TRUE;
		}

		$section = 'pmp_integration_';		
		$integration = intval(PeepSo::get_option($section . 'enabled'));
		if(1 !== $integration) {
			return TRUE;
		}

		$url_levels = pmpro_url("levels");
		if(empty($url_levels)) {
			return TRUE;
		}

		// check if site have exist levels
		if(!empty($url_levels)) {
			PeepSo::redirect($url_levels);
			exit;
		}

		return TRUE;
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
            add_action('admin_notices', function() {

                ?>
                <div class="error peepso">
                    <?php echo sprintf(__('PeepSo %s requires the Paid Memberships Pro.', 'peepsolearndash'), self::PLUGIN_NAME);?>
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
				<?php echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-pmp'), self::PLUGIN_NAME);?>
				<a href="plugin-install.php?tab=plugin-information&plugin=peepso-core&TB_iframe=true&width=772&height=291" class="thickbox">
					<?php echo __('Get it now!', 'peepso-pmp');?>
				</a>
			</strong>
		</div>
		<?php
	}


    public function version_notice()
    {
        PeepSo::version_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG, $this->version_check);
    }


    /*
     * Called on first activation
     */
	public function activate()
	{
		if (!$this->peepso_check()) {
			return (FALSE);
		}

		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
		$install = new PeepSoPMPInstall();
		$res = $install->plugin_activation();
		if (FALSE === $res) {
			// error during installation - disable
			deactivate_plugins(plugin_basename(__FILE__));
		}

		return (TRUE);
	}

	/**
	 * Registers a tab in the PeepSo Config Toolbar
	 * PS_FILTER
	 *
	 * @param $tabs array
	 * @return array
	 */
	public function admin_config_tabs( $tabs )
	{
		$tabs['pmp-integration'] = array(
			'label' => __('PMP Integration', 'peepso-pmp'),
			'icon' => self::ICON,
			'tab' => 'pmp-integration',
			'description' => __('PeepSo PMP Integration', 'peepso-pmp'),
			'function' => 'PeepSoConfigSectionPMPIntegration',
            'cat'   => 'integrations',
		);

		return $tabs;
	}

    /**
     * Hide about me widget when user view register page
     * @param array $widgets an array of peepso widgets
     * @return array $widgets
     */
    public function hide_widget($widgets)
    {
    	$section = 'pmp_integration_';		
		$integration = intval(PeepSo::get_option($section . 'enabled'));
		if(1 !== $integration) {
			return $widgets;
		}

    	// get current URL 
    	$curr_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    	$path = trim(substr($curr_url, strlen(site_url())),'/');
		$parts = explode('/', $path);

		// get PMP account page
		$pmpro_account = get_permalink(get_option('pmpro_account_page_id'));		
		$path2 = trim(substr($pmpro_account, strlen(site_url())),'/');
		$parts2 = explode('/', $path2);

		// check if user logged in and page is PMP account page
		if($parts2[0] === $parts[0] && !is_user_logged_in()) {
			#$key = array_search('PeepSoWidgetMe', $widgets);    	
	    	#if(array_key_exists($key, $widgets)) {
	    	#	unset($widgets[$key]);
	    	#}

	    	// unset all widget
	    	foreach ($widgets as $key => $value) {
	    		unset($widgets[$key]);
	    	}
		}

    	return $widgets;
    }

	/**
	 * Registers the needed scripts and styles
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_style('peepsopmpintegration', plugin_dir_url(__FILE__) . 'assets/pmp-integration.css', array('peepso'), self::PLUGIN_VERSION, 'all');
		wp_enqueue_script('peepsopmpintegration', plugin_dir_url(__FILE__) . 'assets/pmp-integration.js', array('peepso'), self::PLUGIN_VERSION, TRUE);
	}

	/**
	 * Redirect ro PMPro
	 */
	public function redirect_to_pmpro($user_id)
	{
		$section = 'pmp_integration_';		
		$integration = intval(PeepSo::get_option($section . 'enabled'));
		if(1 !== $integration || empty(pmpro_url("levels"))) {
			return;
		}

		$u = PeepSoUser::get_instance($user_id);
		if(FALSE ===$u->get_email()) { 
			wp_redirect(PeepSo::get_page('register') . '?fail_membership/');
			exit;
		}

		// set temporary data for one hour
		#set_transient( 'peepso_user_id_after_register', $user_id, HOUR_IN_SECONDS );

		if(isset( $_SESSION['peepso_user_id_after_register'] ))	{
			unset( $_SESSION['peepso_user_id_after_register'] );
		}
		#setcookie( 'peepso_user_id_after_register', $user_id, time() + ( 60 * 60 ) );		
		$_SESSION['peepso_user_id_after_register'] = $user_id;

		// redirect to pmpro levels
		wp_redirect(pmpro_url("levels"));
		exit;		
	}

	/**
	 * after_checkout
	 */
	public function after_checkout($user_id, $morder)
	{
		// delete temporary user_id
		#delete_transient( 'peepso_user_id_after_register');
		if(isset($_SESSION['peepso_user_id_after_register']))
		{
			unset( $_SESSION['peepso_user_id_after_register'] );
		}

		// join to selected group
		if(class_exists('PeepSoGroupsPlugin')) 
		{
			$membership_level_id = $morder->membership_id;
			if($membership_level_id) {
				$membershipLevelGroup = new PeepSoMembershipLevelGroup();
				$aGroup = $membershipLevelGroup->get_groups_by_level($membership_level_id);
				foreach ($aGroup as $key => $group) {
					$_model = new PeepSoGroupUser($group, $user_id);
					$_model->member_join();
				}
			}
		}

		// assign vip icons
		if(class_exists('PeepSoVIPPlugin')) 
		{
			$membership_level_id = $morder->membership_id;
			if($membership_level_id) {
				$membershipLevelVIP = new PeepSoMembershipLevelVIP();
				$aVip = $membershipLevelVIP->get_vip_by_level($membership_level_id);
				update_user_meta( $user_id, 'peepso_vip_user_icon', '' );
				update_user_meta( $user_id, 'peepso_vip_user_icon', $aVip );
			}
		}

		do_action('peepso_action_pmp_checkout', $user_id, $morder);

		$this->register_success();
	}

	/**
	 * After change membership level
	 */ 
	public function after_change_membership_level($membership_level_id, $user_id, $cancel_level_id)
	{
		// join to selected group
		if(class_exists('PeepSoGroupsPlugin') && ($membership_level_id)) 
		{
			$membershipLevelGroup = new PeepSoMembershipLevelGroup();
			$aGroup = $membershipLevelGroup->get_groups_by_level($membership_level_id);
			foreach ($aGroup as $key => $group) {
				$_model = new PeepSoGroupUser($group, $user_id);
				$_model->member_join();
			}
		}

		// assign vip icons
		if(class_exists('PeepSoVIPPlugin') && ($membership_level_id)) 
		{
			$membershipLevelVIP = new PeepSoMembershipLevelVIP();
			$aVip = $membershipLevelVIP->get_vip_by_level($membership_level_id);
			update_user_meta( $user_id, 'peepso_vip_user_icon', '' );
			update_user_meta( $user_id, 'peepso_vip_user_icon', $aVip );
		}
	}

	/**
	 * Modify confirmation URL
	 */
	public function modify_confirmation_url($rurl, $user_id, $pmpro_level)
	{
		return (PeepSo::get_page('register') . '?confirmation/');
	}

	/**
	 * Fail process membership
	 */
	public function fail_membership() 
	{
		echo PeepSoTemplate::exec_template('pmp', 'fail_membership', array(), TRUE);
	}

	/**
	 * Redirect to success page
	 */
	public function register_success() 
	{
		global $current_user, $morder;
		
		$sendemails = apply_filters( "pmpro_send_checkout_emails", true);
		if($sendemails) { // Send the e-mails only if the flag is set to true

			//setup some values for the emails
			if ( ! empty( $morder ) ) {
				$invoice = new MemberOrder( $morder->id );
			} else {
				$invoice = null;
			}

			//send email to member
			$pmproemail = new PMProEmail();
			$pmproemail->sendCheckoutEmail( $current_user, $invoice );

			//send email to admin
			$pmproemail = new PMProEmail();
			$pmproemail->sendCheckoutAdminEmail( $current_user, $invoice );
		}		
		
		wp_redirect(PeepSo::get_page('register') . '?success');
		exit;
	}

	/*
	 * Methods below are used solely as an integration with the PMP membership level section
	 */
	public function action_after_other_settings_group() 
	{
		?>
		<h3 class="topborder"><?php echo __('PeepSo Groups', 'peepso-pmp' );?></h3>
		<p><?php echo __('Automatically add users who pick this membership level to the following groups.', 'peepso-pmp' );?></p>
		<table class="form-table">
            <tr class="user-admin-color-wrap">
                <td>
                    <fieldset id="peepso-groups" class="scheme-list">
                        <?php
                        $aGroups = PeepSoGroups::admin_get_groups(0, NULL, NULL, NULL, '', 'all');
                        $level_id = $_REQUEST['edit'];
                        $membershipLevelGroup = new PeepSoMembershipLevelGroup();
                        $selectedGroup = $membershipLevelGroup->get_groups_by_level($level_id);
                        foreach ($aGroups as $key => $group) {
                            ?>
                            <div class="color-option">
                            	<input name="peepsogroups[]" id="peepsogroups<?php echo $key;?>" type="checkbox" value="<?php echo $group->id;?>" class="tog" <?php echo (in_array($group->id, $selectedGroup)) ? ' checked=checked':'';?>>
                                <label for="peepsogroups<?php echo $key;?>">
                                <img src="<?php echo $group->get_avatar_url();?>" style="width: 32px; height: 32px;">
                                <?php echo $group->name;?> 
                                <?php  if(intval($group->is_open)) { echo "<small>(".__('open', 'peepso-pmp').")</small>"; }  ?>
                                <?php  if(intval($group->is_closed)) { echo "<small>(".__('closed', 'peepso-pmp').")</small>"; }  ?>
                                <?php  if(intval($group->is_secret)) { echo "<small>(".__('secret', 'peepso-pmp').")</small>"; }  ?>
                                </label>
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

	public function action_after_save_membership_level_group($membership_level_id) 
	{
		// save membership level groups
		if ($membership_level_id) {

			$membershipLevelGroup = new PeepSoMembershipLevelGroup();
			$groups = isset($_REQUEST['peepsogroups']) ? $_REQUEST['peepsogroups'] : array();
			$membershipLevelGroup->update_membership_level_group($membership_level_id, $groups);

			return true;
		}

		return false;
	}

	public function action_after_delete_membership_level_group($membership_level_id)
	{
		// clean up membership level group table.
		if($membership_level_id) {
			$membershipLevelGroup = new PeepSoMembershipLevelGroup();
			$groups = array();
			$membershipLevelGroup->update_membership_level_group($membership_level_id, $groups);

			return true;
		}

		return false;
	}



	/*
	 * Methods below are used solely as an integration with the PMP membership level section
	 */
	public function action_after_other_settings_vip() 
	{
		?>
		<h3 class="topborder"><?php echo __('PeepSo VIP', 'peepso-pmp' );?></h3>
		<p><?php echo __('Automatically assign users a selected VIP icon who pick this membership level .', 'peepso-pmp' );?></p>
		<table class="form-table">
            <tr class="user-admin-color-wrap">
                <td>
                    <fieldset id="peepso-groups" class="scheme-list">
                    	<?php
                        $PeepSoVipIconsModel = new PeepSoVipIconsModel();
                        $level_id = $_REQUEST['edit'];
                        $membershipLevelVIP = new PeepSoMembershipLevelVIP();
                        $selectedIcon = $membershipLevelVIP->get_vip_by_level($level_id);
                        ?>
                        	<div class="color-option">
                                <input name="peepso_vip_user_icon" id="vip_icon_0" type="radio" value="0" class="tog"  <?php echo (0 == $selectedIcon) ? ' checked=checked':'';?>>
                                <label for="vip_icon_0"><?php echo __('No Icon', 'peepso-pmp');?></label>
                            </div>
                        <?php
                        foreach ($PeepSoVipIconsModel->vipicons as $key => $value) {
                            ?>
                            <div class="color-option">
                                <input name="peepso_vip_user_icon" id="vip_icon_<?php echo $key;?>" type="radio" value="<?php echo $value->post_id;?>" class="tog" <?php echo ($value->post_id == $selectedIcon) ? ' checked=checked':'';?>>
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

	public function action_after_save_membership_level_vip($membership_level_id) 
	{
		// save membership level vip
		if ($membership_level_id) {

			$membershipLevelVIP = new PeepSoMembershipLevelVIP();
			$peepso_vip_user_icon = isset($_REQUEST['peepso_vip_user_icon']) ? [$_REQUEST['peepso_vip_user_icon']] : array();
			$membershipLevelVIP->update_membership_level_vip($membership_level_id, $peepso_vip_user_icon);

			return true;
		}

		return false;
	}

	public function action_after_delete_membership_level_vip($membership_level_id)
	{
		// clean up membership level group table.
		if($membership_level_id) {
			$membershipLevelVIP = new PeepSoMembershipLevelVIP();
			$vips = array();
			$membershipLevelVIP->update_membership_level_vip($membership_level_id, $vips);

			return true;
		}

		return false;
	}

	public function save_membership_user_profile_fields($user_id)
	{
		if ( !current_user_can( 'edit_user', $user_id ) ) {
            return (FALSE);
        }
        
        //level change
    	if(isset($_REQUEST['membership_level']))
    	{

	        $level_id = $_REQUEST['membership_level'];

	        $membershipLevelVIP = new PeepSoMembershipLevelVIP();
			$selectedIcon = $membershipLevelVIP->get_vip_by_level($level_id);

			$icons = get_the_author_meta( 'peepso_vip_user_icon', $user_id );
			if (intval($selectedIcon) > 0) {
				$icons[] = $selectedIcon;
				$icons = array_unique($icons);

				update_user_meta( $user_id, 'peepso_vip_user_icon', $icons );
			}
		}
	}

	public function action_delete_order($order_id, $order)
	{
		new PeepSoError('Member Order: '.var_export($order, TRUE), 'error', 'pmp-plugins');
		new PeepSoError('User Id: '.$order->user_id, 'debug', 'pmp-plugins');
		new PeepSoError('Membership Id: '.$order->membership_id, 'debug', 'pmp-plugins');
	}
}

PeepSoPMP::get_instance();

// EOF