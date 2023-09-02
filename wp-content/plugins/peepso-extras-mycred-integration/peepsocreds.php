<?php
/**
 * Plugin Name: PeepSo Integrations: myCRED
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: <strong>Requires the MyCred plugin</strong>. Award myCRED points for performing actions in PeepSo
 * Author: PeepSo
 * Author URI: https://301.es/?https://peepso.com
 * Version: 2.7.7
 * Copyright: (c) 2015 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepsocreds
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

 Class PeepSoMyCreds {

	private static $_instance = NULL;

	const PLUGIN_VERSION = '2.7.7';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
	const PLUGIN_NAME = 'Integrations: myCRED';
	const PLUGIN_EDD = 69775;
	const PLUGIN_SLUG = 'peepso-mycred';

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6I2YyZjJmMjt9LmNscy0ye2ZpbGw6I2ZjYzU4OTt9LmNscy0ze2ZpbGw6IzRhMzkyZTt9LmNscy00e2ZpbGw6I2ZmZjt9LmNscy01e2ZpbGw6I2Y5YjM4Mjt9LmNscy02e2ZpbGw6bm9uZTtzdHJva2U6Izg1Njk0ODtzdHJva2UtbGluZWNhcDpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDoxMDtzdHJva2Utd2lkdGg6MnB4O308L3N0eWxlPjwvZGVmcz48dGl0bGU+bXljcmVkX2ljb248L3RpdGxlPjxnIGlkPSJCRyI+PHJlY3QgY2xhc3M9ImNscy0xIiB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgcng9IjIwIiByeT0iMjAiLz48L2c+PGcgaWQ9IkxvZ28iPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTMzLjg5LDQ0LjIxLDMxLjcyLDcyLjU0cy00LjY1LDI2LjE3LDMsMjcuNDZjMCwwLDIyLjA2LDIuOTIsNDguMTIsNCwyLjgxLS4yMiw3LjE0LTEyLjU0LDYuNi0yNi40OWwzLjQ2LTIzLjE0TDg4LjYsNDIuNDhaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwIDApIi8+PHBhdGggY2xhc3M9ImNscy0zIiBkPSJNMzUuNzIsNDUuNjFzNCw3LjU3LDUxLjc5LDBjMCwwLDQuMTEsMTAuOTIsMS45NSwzMS45LDAsMCwxMS40Ni05LjYyLDUuOTUtMzYuMzMtMS00LjU0LTEuMTEtNS45NSwxLjUtOC4zMywwLDAtLjg1LTEuMTktNS0uODdhMTUuODYsMTUuODYsMCwwLDEsOS4wOC0yLjdTODUuNTcsMTUuMjMsNDAuMTYsMzN2LTIuNmwtMywzLjE0LS42NS0zLTIuNiw0LjIyLTEtMy4xNHMtMTIuNTQsMjQuMzMtMS4xOSw0MVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48Y2lyY2xlIGN4PSI0Ni4xOSIgY3k9IjY1LjU5IiByPSI0LjMiLz48cmVjdCBjbGFzcz0iY2xzLTQiIHg9IjQ2LjIxIiB5PSI2Mi41OSIgd2lkdGg9IjMuMDMiIGhlaWdodD0iMy43OCIgcng9IjEuNTEiIHJ5PSIxLjUxIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMjAuMzEgMjEuOTgpIHJvdGF0ZSgtMjEuNSkiLz48Y2lyY2xlIGN4PSI3Ni42OCIgY3k9IjY4LjA4IiByPSI0LjMiLz48cmVjdCBjbGFzcz0iY2xzLTQiIHg9Ijc2LjciIHk9IjY1LjA3IiB3aWR0aD0iMy4wMyIgaGVpZ2h0PSIzLjc4IiByeD0iMS41MSIgcnk9IjEuNTEiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xOS4xIDMzLjMzKSByb3RhdGUoLTIxLjUpIi8+PGNpcmNsZSBjbGFzcz0iY2xzLTUiIGN4PSI0MS41MSIgY3k9Ijg1LjI0IiByPSIzLjE5Ii8+PGNpcmNsZSBjbGFzcz0iY2xzLTUiIGN4PSI3OC4yMiIgY3k9Ijg4LjQzIiByPSIzLjE5Ii8+PHBhdGggY2xhc3M9ImNscy02IiBkPSJNNjkuNzgsODguNDNINjQuMDZhNTAuNzUsNTAuNzUsMCwwLDEtOC42MS0uNzNsLTUuNzgtMSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAwKSIvPjwvZz48L3N2Zz4=';

    private static function ready() {
        return(self::ready_thirdparty() && class_exists('PeepSo') && self::PLUGIN_VERSION.self::PLUGIN_RELEASE == PeepSo::PLUGIN_VERSION.PeepSo::PLUGIN_RELEASE);
	}

	private static function ready_thirdparty() {
		if (!class_exists('myCRED_Core') || !get_option('mycred_setup_completed') || (function_exists('mycred_check') && !mycred_check())) {
			return (FALSE);
		}

		return (TRUE);
    }
	
	private function __construct() {

        /** VERSION INDEPENDENT hooks **/

        // Admin
        if (is_admin()) {
            add_action('admin_init', array(&$this, 'peepso_check'));
            add_filter('peepso_license_config', array(&$this, 'add_license_info'), 160);
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
            add_action('peepso_init', array(&$this, 'init'));
        }

        add_action('mycred_load_hooks', function() {
            require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'mycredhook.php';
        });
    }

    /**
     * Retrieve singleton class instance
     * @return PeepSoMyCreds instance
     */
    public static function get_instance()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self();
        }
        return (self::$_instance);
	}
	
	function init() {
        PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);

		if (is_admin()) {
			add_filter('peepso_admin_config_tabs', array($this, 'admin_config_tabs'), 90);

			add_action('admin_enqueue_scripts', function(){
				wp_enqueue_script('peepso-admin-mycred',
				plugin_dir_url(__FILE__) . 'assets/js/admin.js',
				array('jquery', 'underscore'), self::PLUGIN_VERSION, TRUE);
			});
		} else {
			// license checking
			if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
				return;
			}
	
			if (PeepSo::get_option('mycred_enabled', 1) === 1) {
				add_filter('peepso_user_activities_links', function($act_links){
					$url = PeepSoUrlSegments::get_instance();
	
					if ($url->get(0) === 'peepsoajax' && isset($_POST['user_id'])) {
						$user_id = (int) $_POST['user_id'];
					} else if ($url->get(1)) {
						$user = get_user_by('slug', $url->get(1));
	
						if (FALSE === $user) {
							$user_id = get_current_user_id();
						} else {
							$user_id = $user->ID;
						}
					} else {
						$user_id = get_current_user_id();
					}
	
					$account = mycred_get_account($user_id);
	
					$a['points'] = array(
						'label' => __('Points', 'peepsocreds'),
						'title' => __('User Points', 'peepsocreds'),
						'icon' => 'certificate',
						'count' => isset($account->balance['mycred_default']->current) ? $account->balance['mycred_default']->current : 0,
						'all_values' => TRUE,
						'order' => 35
					);
	
					$act_links = array_merge($a, $act_links);
					return $act_links;
				});

				if (PeepSo::get_option('mycred_point_history_enabled', 0) === 1) {
					add_action('peepso_navigation_profile', function ($links) {
						$links['points'] = array(
							'label'=> _x('Points', 'Profile link', 'peepsocreds'),
							'href' => 'points',
							'icon' => 'ps-icon-certificate'
						);

						return $links;
					});

					add_action('peepso_profile_segment_points', function() {
						$pro = PeepSoProfileShortcode::get_instance();

						$this->view_user_id = PeepSoUrlSegments::get_view_id($pro->get_view_user_id());
						
						PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));
						echo PeepSoTemplate::exec_template('profile', 'profile-mycred', array('view_user_id' => $this->view_user_id), TRUE);
					});
				}
			}
		}
	
		add_filter('mycred_ranking_row', function($layout, $template, $user, $position) {
	
			$layout = str_replace('/author/', '/' . PeepSo::get_option('page_profile', 'profile') . '/?', $layout);
	
			return $layout;
		}, 10, 4);

		if (PeepSo::get_option('override_admin_navbar', 0) === 1) {
			add_action( 'admin_bar_menu', function($wp_admin_bar) {
				$wp_admin_bar->remove_menu('mycred-account');
			});
		}

		add_filter('mycred_setup_hooks', array($this, 'my_peepsocreds_setup_hooks'));
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
	
	/**
	 * Registers a tab in the PeepSo Config Toolbar
	 * PS_FILTER
	 *
	 * @param $tabs array
	 * @return array
	 */
	public function admin_config_tabs($tabs)
	{
		$tabs['mycred'] = array(
			'label' => __('myCRED', 'peepsocreds'),
			'title' => __('myCRED', 'peepsocreds'),
			'tab' => 'mycred',
			'icon' => self::ICON,
			'description' => __('myCRED', 'peepsocreds'),
			'function' => 'PeepSoConfigSectionMyCred',
			'cat'   => 'integrations',
		);

		return $tabs;
	}

	public function activate() {

        if (!$this->peepso_check()) {
            return (FALSE);
		}

        return (TRUE);
    }
	

	/**
	 * Check if PeepSo class is present (ie the PeepSo plugin is installed and activated)
	 * If there is no PeepSo, immediately disable the plugin and display a warning
	 * Run license and new version checks against PeepSo.com
	 * @return bool
	 */
	function peepso_check()
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
                    <?php echo sprintf(__('PeepSo %s requires the MyCred plugin.', 'peepsolearndash'), self::PLUGIN_NAME);?>
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
	
	    /**
     * Display a message about PeepSo not present
     */
    public function peepso_disabled_notice()
    {
        ?>
        <div class="error peepso">
            <strong>
                <?php
				echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepsocreds'), self::PLUGIN_NAME),
                    ' <a href="plugin-install.php?tab=plugin-information&amp;plugin=peepso-core&amp;TB_iframe=true&amp;width=772&amp;height=291" class="thickbox">',
                    __('Get it now!', 'peepsocreds'),
                    '</a>';
                ?>
                <?php //echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-wpadverts'), self::PLUGIN_NAME);?>
            </strong>
        </div>
        <?php
	}
	
	function my_peepsocreds_setup_hooks($installed) { //, $point_type ) {
		// Add a custom hook
		$installed['peepsocreds'] = array(
			'title' => 'PeepSo',
			'description' => 'Receive points for PeepSo activities.',
			'callback' => array('myCREDHook')
		);

		return $installed;
	}

    /**
     * Loads the translation file for the PeepSo plugin
     */
    public function load_textdomain()
    {
        $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
        load_plugin_textdomain('peepsocreds', FALSE, $path);
    }
}

PeepSoMyCreds::get_instance();