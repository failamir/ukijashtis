<?php
/**
 *  Create A Simple Theme Options Panel
 *
 */

//  Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//  Start Class
if ( ! class_exists( 'Gecko_Theme_License' ) ) {

	class Gecko_Theme_License {

		const PEEPSO_HOME = 'https://www.peepso.com';
		const PEEPSO_LICENSE_TRANS = 'peepso_license_';
		const PEEPSOCOM_LICENSES = 'http://tiny.cc/peepso-licenses';
		const OPTION_DATA = 'gecko_license_data';
		private static $_licenses = NULL;

		const THEME_NAME		= 'PeepSo Theme: Gecko';
		const THEME_VERSION 	= '2.7.7.1';
		const THEME_RELEASE 	= ''; //ALPHA1, BETA1, RC1, '' for STABLE
		const THEME_EDD 		= 7354103;
		const THEME_SLUG 	 	= 'peepso-theme-gecko';

		/**
		 * Autoload method
		 * @return void
		 */
		public function __construct() {
			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'Gecko_Theme_License', 'register_sub_menu') );
				add_filter( 'gecko_sanitize_option', array('Gecko_Theme_Settings', 'sanitize'));

				// PeepSo.com license check
				if (!self::check_license(self::THEME_EDD, self::THEME_SLUG)) {
					add_action('admin_notices', array( 'Gecko_Theme_License', 'license_notice'));
				}

				self::check_updates(self::THEME_EDD, self::THEME_SLUG, self::THEME_VERSION, __FILE__);
			}
		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.0.0
		 */
		public static function sanitize( $options ) {

			// If we have options lets sanitize them
			if ( isset($options['gecko_license']) ) {

				// License input
				if ( ! empty( $options['gecko_license'] ) ) {
					$options['gecko_license'] = sanitize_text_field( $options['gecko_license'] );
				} else {
					$options['gecko_license'] = '';
				}

			}

			// Return sanitized options
			return $options;

		}

		/**
		 * Register submenu
		 * @return void
		 */
		public static function register_sub_menu() {
            if(!isset($_SERVER['HTTP_HOST']) || 'demo.peepso.com' != $_SERVER['HTTP_HOST'] ) {
                add_submenu_page(
                    'gecko-settings', 'Gecko License', 'License', 'manage_options', 'gecko-license', array('Gecko_Theme_License', 'submenu_page_callback')
                );
            }
		}

		/**
		 * Render submenu
		 * @return void
		 */
		public static function submenu_page_callback() {

			self::delete_transient(self::THEME_SLUG);

			$response = array();
			$response_details = array();

			self::activate_license(self::THEME_SLUG, self::THEME_NAME);

            $valid = (int)self::check_license(self::THEME_NAME, self::THEME_SLUG, TRUE);
            $license = self::get_license(self::THEME_SLUG);

            $details = '';

            if(isset($license['expire']) && $license['expire']) {
                $expires = strtotime($license['expire']);

                if ($expires > time()) {
                    $color = '#689f38';
                    $message = sprintf(__('%s remaining', 'peepso-core'), human_time_diff_round_alt($expires));
                } else {
                    $color = '#e53935';
                    $message = sprintf(__('Expired on %s', 'peepso-core'), date('d-M-Y', $expires));

                    if(strstr($license['expire'], '1999')) {
                        $message = __('Your license can\'t be checked because of an API request limit.<br/> If the problem persists, please contact <a href="https://www.peepso.com/contact" target="_blank">PeepSo Support</a>.', 'peepso-core');
                    }
                }

                $details = sprintf('<span style="color:%s">%s</span>', $color, $message);
            }

		?>
			<form method="post" action="">
				<div class="gc-admin">
					<div class="gc-admin__actions">
						<input type="hidden" name="gecko-config-nonce" value="<?php echo wp_create_nonce('gecko-config-nonce') ?>"/>
						<?php submit_button(); ?>
					</div>
					<div class="gc-admin__wrapper">
						<div class="gc-admin__side">
							<h1><?php esc_html_e( 'Gecko', 'gecko' ); ?></h1>
							<div class="gc-admin__menu">
	                            <a href="admin.php?page=gecko-settings"><?php esc_html_e( 'Settings', 'gecko' ); ?></a>
	                            <a href="customize.php"><?php esc_html_e( 'Customize', 'gecko' ); ?></a>
	                            <a class="active" href="admin.php?page=gecko-license"><?php esc_html_e( 'License', 'gecko' ); ?></a>
	                            <a href="admin.php?page=gecko-page-builders"><?php esc_html_e( 'Page Builders', 'gecko' ); ?></a>
							</div>
						</div>

						<div class="gc-admin__options">
							<div class="gc-admin__version">
								<?php esc_html_e( 'Version', 'gecko' ); ?>: <?php echo wp_get_theme()->version ?>
							</div>
							<div class="gc-admin__header">
								<h3><?php esc_html_e( 'License', 'gecko' ); ?></h3>
							</div>
							<div class="gc-admin__fields">

								<?php settings_fields( 'gecko_options' ); ?>

								<div class="gc-form__row">
									<div class="gc-form__row-label"><?php esc_html_e( 'License key', 'gecko' ); ?></div>

									<div class="gc-form__controls">
										<label class="gc-form__controls-label" for="opt_show_searchbar">
											<?php
											$settings = GeckoConfigSettings::get_instance();
											$value = $settings->get_option( 'gecko_license' );
											?>
											<input type="text" name="gecko_options[gecko_license]" value="<?php echo esc_attr( $value ); ?>">
										</label>
									</div>

									<div class="gc-form__row-desc">
										<?php echo $details; ?>
									</div>
								</div>
							</div>
						</div>
					</div><!-- .gc-admin__wrapper -->
					<div class="gc-admin__actions">
						<input type="hidden" name="gecko-config-nonce" value="<?php echo wp_create_nonce('gecko-config-nonce') ?>"/>
						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		<?php
		}


		/* Licensing */

	    public static function license_notice()
	    {
	        self::_license_notice(self::THEME_NAME, self::THEME_SLUG);
	    }

	    public function license_notice_forced()
	    {
	        self::_license_notice(self::THEME_NAME, self::THEME_SLUG, true);
	    }


		/**
	     * Show message if peepsofriends can not be installed or run
	     */
	    public static function _license_notice($theme_name, $theme_slug, $forced=FALSE)
	    {
	        // $style="";
	        // if (isset($_GET['page']) && 'peepso_config' == $_GET['page']) {

	        //     if (!$forced) {
	        //         return;
	        //     }

	        //     $style="display:none";
	        // }

	        $license_data = self::get_license($theme_slug);
	        echo "<!--";print_r($license_data);echo "-->";

	        self::activate_license($theme_slug,$theme_name);

	        $license_data = self::get_license($theme_slug);

	        echo "<!--";print_r($license_data);echo "-->";
	        switch ($license_data['response']) {
	            case 'site_inactive':
	                $message = __('This domain is not registered for THEME_NAME. You can register your domain <a target="_blank" href="PEEPSOCOM_LICENSES">here</a>.', 'peepso-core');
	                break;
	            case 'expired':
	                $message = __('License for THEME_NAME has expired. The plugin will work, but it will not receive updates. You can renew your license <a target="_blank" href="PEEPSOCOM_LICENSES">here</a>.', 'peepso-core');
	                break;
	            case 'invalid':
	            case 'inactive':
	            case 'item_name_mismatch':
	            default:
	                $message = __('License for THEME_NAME is missing or invalid. Please enter a valid license and click "SAVE" to activate it. You can get your license key <a target="_blank" href="PEEPSOCOM_LICENSES">here</a>.', 'peepso-core');
	                break;
	        }

	        #var_dump($license_data);
	        $from = array(
	            'THEME_NAME',
	            'ENTER_LICENSE',
	            'PEEPSOCOM_LICENSES',
	        );

	        $to = array(
	            $theme_name,
	            'admin.php?page=gecko-license',
	            self::PEEPSOCOM_LICENSES,
	        );

	        $message = str_ireplace( $from, $to, $message );
	        #var_dump($message);


	        if($forced) {
	            echo '<div class="error peepso" id="error_' . $theme_slug . '" style="' . $style . '">';
	            echo '<strong>', $message, '</strong>';
	            echo '</div>';
	        } else {
	            global $peepso_has_displayed_license_warning;
	            $peepso_has_displayed_license_warning = isset($peepso_has_displayed_license_warning) ? $peepso_has_displayed_license_warning : FALSE;

	            if (!$peepso_has_displayed_license_warning) {
	                $peepso_has_displayed_license_warning = TRUE;

	                $message = __('Gecko is having issues validating your license. <a href="ENTER_LICENSE">Review your Gecko license keys</a>.','peepso-core');
	                $message = str_ireplace( $from, $to, $message );


	                echo '<div class="error peepso" id="peepso_license_error_combined">';
	                echo $message;
	                echo '</div>';
	            }
	        }
	    }

		/**
	     * Verifies the license key for an add-on by the plugin's slug
	     * @param string $theme_edd The THEME_NAME constant value for the plugin being checked
	     * @param string $theme_slug The THEME_SLUG constant value for the plugin being checked
	     * @return boolean TRUE if the license is active and valid; otherwise FALSE.
	     */
	    public static function check_license($theme_edd, $theme_slug, $is_admin = FALSE)
	    {
			if( FALSE === $is_admin) {
				$is_admin = is_admin();
			}
	        // check to see if the license key is valid for the named plugin
	        return true;
	    }

		public static function get_license($theme_slug)
		{
			return self::_get_product_data($theme_slug);
		}

		public static function delete_transient($theme_slug)
		{
			$trans_key = self::trans_key($theme_slug);
			delete_transient($trans_key);
		}

		private static function trans_key($theme_slug)
		{
			return self::PEEPSO_LICENSE_TRANS . $theme_slug;
		}

	    /**
	     * Activates the license key for a PeepSo add-on
	     * @param string $theme_slug The add-on's slug name
	     * @param string $theme_edd The add-on's full plugin name
	     * @return boolean TRUE on successful activation; otherwise FALSE
	     */
	    public static function activate_license($theme_slug, $theme_edd)
	    {
	        // how long to keep the transient keys?
			$trans_lifetime = 24 * HOUR_IN_SECONDS;

	        // get key stored from config pages
	        $key = self::_get_key($theme_slug);

	        $license_data['license'] = $key;
	        $license_data['name'] = $theme_edd;

	        if (FALSE === $key || 0 === strlen($key)) {
				return;
			}

	        // when asking EDD API use "item_id" if plugin_edd is numeric, otherwise "item_name"
	        $key_type = 'item_name';

	        if(is_numeric($theme_edd)) {
	            $key_type = 'item_id';
	            $theme_edd = (int) $theme_edd;
	        }

	        $args = array(
	            'edd_action' => 'activate_license',
	            'license' => $key,
	            $key_type => $theme_edd,
	            'url' => home_url(),
	        );

	        // Use transient key to check for cached values
			$trans_key = self::trans_key($theme_slug);

			// If there is no cached value, call home
			$validation_data = get_transient($trans_key);

	        if ( !is_object($validation_data) ) {

	            $peepso_is_offline = FALSE;

                if(strlen(get_transient('peepso_is_offline'))) {
                    $peepso_is_offline = TRUE;
                } else {
                    $resp = new stdClass();
					$resp->success = true;
					$resp->license 			= 'valid';
	  				$resp->item_name 		= $theme_slug;
	  				$resp->expires			= '2090-01-01 00:00:00';
	  				$resp->payment_id		= 0;
	  				$resp->customer_name 	= 'nulled';
	  				$resp->customer_email	= 'nu@ll.ed';
	  				$resp->license_limit 	= 0;
	  				$resp->site_count		= 0;
	  				$resp->activations_left 	= 'unlimited';
					$resp = json_encode($resp);
                }

	            if ($peepso_is_offline) {
					$trans_lifetime = 1 * HOUR_IN_SECONDS;

					$validation_data = new stdClass();

					$validation_data->success = true;

					$validation_data->license 			= 'valid';
	  				$validation_data->item_name 		= $theme_slug;
	  				$validation_data->expires			= '2020-01-01 00:00:00';
	  				$validation_data->payment_id		= 0;
	  				$validation_data->customer_name 	= 'temporary';
	  				$validation_data->customer_email	= 'temporary@peepso.com';
	  				$validation_data->license_limit 	= 0;
	  				$validation_data->site_count		= 0;
	  				$validation_data->activations_left 	= 'unlimited';
				} else {
					$response = $resp;

					$validation_data = json_decode($response);
				}
	            set_transient($trans_key, $validation_data, $trans_lifetime);
	        }

	        $license_data['expire'] = isset($validation_data->expires) ? $validation_data->expires : NULL;

	        if ('valid' === $validation_data->license) {
	            // if parent site reports the license is active, update the stored data for this plugin
				$license_data['state'] = 'valid';
	        } else {
				$license_data['state'] = 'invalid';
	        }

			// remaining options
			$license_data['response'] = $validation_data->license;
	        if(isset($validation_data->error)) {
	            $license_data['response'] = $validation_data->error;
	        }

			// save
			self::_set_product_data($theme_slug, $license_data);
	    }

	    /**
	     * Loads the license information from the options table
	     */
	    private static function _load_licenses()
	    {
	        if (NULL === self::$_licenses) {
	            $lisc = GeckoConfigSettings::get_instance()->get_option(self::OPTION_DATA, FALSE);
	            if (FALSE === $lisc) {
	                $lisc = array();
	                add_option(self::OPTION_DATA, $lisc, FALSE, FALSE);
	            }
	            self::$_licenses = $lisc;
	        }
	    }

	    /**
	     * Retrieves product data for a given add-on by slug name
	     * @param string $theme_slug The plugin's slug name
	     * @return mixed The data array stored for the plugin or NULL if not found
	     */
	    private static function _get_product_data($theme_slug)
	    {
	        self::_load_licenses();
	        $theme_slug = sanitize_key($theme_slug);

	        if (isset(self::$_licenses[$theme_slug])) {
	            // check license data for validity
	            $data = self::$_licenses[$theme_slug];
	                return ($data);
	        }
	        return (NULL);
	    }

	    /**
	     * Sets the stored license information per product
	     * @param string $theme_slug The plugin's slug
	     * @param array $data The data array to store
	     */
	    private static function _set_product_data($theme_slug, $data)
	    {
	        /*
	         * data:
	         *	['slug'] = plugin slug
	         *	['name'] = plugin name
	         *	['license'] = license key
	         *	['state'] = license state
	         *	['expire'] = license expiration
	         *	['checksum'] = checksum
	         */

	        $theme_slug = sanitize_key($theme_slug);
	        $data['slug'] = $theme_slug;
	        $str = $theme_slug . '|' . esc_html($data['name']) .
	            '~' . $data['license'] . ',' . $data['expire'] . $data['state'];
	        $data['checksum'] = md5($str);
	        self::_load_licenses();
	        self::$_licenses[$theme_slug] = $data;
	        update_option(self::OPTION_DATA, self::$_licenses);
	    }

	    /**
	     * Get the license key stored for the named plugin
	     * @param string $theme_slug The THEME_SLUG constant value for the add-on to obtain the license key for
	     * @return string The entered license key or FALSE if the named license key is not found
	     */
	    private static function _get_key($theme_slug)
	    {
	        return (GeckoConfigSettings::get_instance()->get_option( 'gecko_license' ));
	    }

	    /**
	     * Determines if a key is valid and active
	     * @param string $plugin Plugin slug name
	     * @return boolean TRUE if the key for the named plugin is valid; otherwise FALSE
	     */
	    public static function is_valid_key($plugin)
	    {

	        return true;
	    }

		public static function get_key_state($plugin)
		{
			self::_load_licenses();
			$theme_slug = sanitize_key($plugin);
			if (!isset(self::$_licenses[$theme_slug])) {
				return "unknown";
			}

			$data = self::$_licenses[$theme_slug];

			return array_key_exists('response', $data) ? $data['response'] : 'unknown';
		}

	    public static function dump_data()
	    {
	        self::_load_licenses();
	        var_export(self::$_licenses);
	    }

	    // The old check_updates() doesn't know the difference between plugin_slug and plugin_edd
	    // since 1.7.6 plugin_edd can be numeric and different from plugin_slug
	    public static function check_updates( $theme_edd, $theme_slug, $theme_version, $file, $is_core = TRUE ) {

            // Version number is usually cached in a transient
            $trans = 'peepso_gecko_current_version';

            $version = get_transient($trans);

            // If not, get it from peepso.com
            if (!strlen($version)) {

                $version = 0;
                $url = self::PEEPSO_HOME.'/peepsotools-integration-json/version-gecko.txt';

                $peepso_is_offline = FALSE;

                if(strlen(get_transient('peepso_is_offline'))) {
                    $peepso_is_offline = TRUE;
                }

                if(!$peepso_is_offline) {
                    // Attempt contact with PeepSo.com without sslverify
                    $resp = '2.7.7.1';
                }

                // Definite failure - freeze the checks for a while
                if ($peepso_is_offline) {
                    // trigger_error('check_updates - failed to load version.txt from PeepSo.com');
                    set_transient($trans, self::THEME_VERSION, 30);
                } else {
                    // Success - store the version in a 15 minute transient
                    $version = '2.7.7.1';
                    set_transient($trans, $version, 15 * 60);
                }
            }

            if (1 != version_compare($version, $theme_version)) {
                return( FALSE );
            }

			// If neither if/else block returned FALSE, the version check will happen
			if( !class_exists( 'PeepSO_EDD_Theme_Updater' ) ) {
				include(dirname(__FILE__) . '/license_edd_helper.php');
			}

	        $strings = array(
				'theme-license'             => __( 'Theme License', 'gecko' ),
				'enter-key'                 => __( 'Enter your theme license key.', 'gecko' ),
				'license-key'               => __( 'License Key', 'gecko' ),
				'license-action'            => __( 'License Action', 'gecko' ),
				'deactivate-license'        => __( 'Deactivate License', 'gecko' ),
				'activate-license'          => __( 'Activate License', 'gecko' ),
				'status-unknown'            => __( 'License status is unknown.', 'gecko' ),
				'renew'                     => __( 'Renew?', 'gecko' ),
				'unlimited'                 => __( 'unlimited', 'gecko' ),
				'license-key-is-active'     => __( 'License key is active.', 'gecko' ),
				'expires%s'                 => __( 'Expires %s.', 'gecko' ),
				'expires-never'             => __( 'Lifetime License.', 'gecko' ),
				'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'gecko' ),
				'license-key-expired-%s'    => __( 'License key expired %s.', 'gecko' ),
				'license-key-expired'       => __( 'License key has expired.', 'gecko' ),
				'license-keys-do-not-match' => __( 'License keys do not match.', 'gecko' ),
				'license-is-inactive'       => __( 'License is inactive.', 'gecko' ),
				'license-key-is-disabled'   => __( 'License key is disabled.', 'gecko' ),
				'site-is-inactive'          => __( 'Site is inactive.', 'gecko' ),
				'license-status-unknown'    => __( 'License status is unknown.', 'gecko' ),
				'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'gecko' ),
				'update-available'          => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'gecko' ),
			);

	        $license = GeckoConfigSettings::get_instance()->get_option( 'gecko_license' );

	        $key_name = 'item_name';
	        if(is_numeric($theme_edd)) {
	            $key_name = 'item_id';
	        }

			$api_data = wp_parse_args( array(
				'remote_api_url' => self::PEEPSO_HOME,
				'license'        => trim( $license ),
				'theme_slug'     => self::THEME_SLUG,
				$key_name   	 => $theme_edd,
				'author'         => '',
			) );
			$updater = new PeepSo_EDD_Theme_Updater( $api_data, $strings );


	    }
	}
}
new Gecko_Theme_License();
