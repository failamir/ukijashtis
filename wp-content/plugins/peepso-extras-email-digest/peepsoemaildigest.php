<?php
/**
 * Plugin Name: PeepSo Extras: Email Digest
 * Plugin URI: https://301.es/?https://peepso.com
 * Description: Bring users back with automated newsletter
 * Author: PeepSo
 * Author URI: https://301.es/?https://peepso.com
 * Version: 2.7.7
 * Copyright: (c) 2015 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepso-email-digest
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

class PeepSoEmailDigest
{

	private static $_instance = NULL;

	const PLUGIN_VERSION = '2.7.7';
	const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE
	const MODULE_ID = 20;
	const PLUGIN_NAME = 'Extras: Email Digest';
	const PLUGIN_EDD = 43390;
	const PLUGIN_SLUG = 'peepso-email-digest';
	const CRON_URL = 'peepso_email_digest_event';
	const CRON_EMAIL_DIGEST_EVENT = 'peepso_email_digest_event';

	public $peepso_moods_content = FALSE;
	public $peepso_most_liked_post_id = 0;
	public $peepso_most_commented_post_id = 0;
		
	public $log_table = 'peepso_email_digest_log';
	public $content_table = 'peepso_email_digest_content';
	
	public $activity_table;
	public $like_table;
	public $user_table;
	public $mailqueue_table;

    const ICON = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjggMTI4Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzRhODZlODt9LmNscy0ye2ZpbGw6I2ZmZjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPmVtYWlsZGlnZXN0X2ljb248L3RpdGxlPjxnIGlkPSJCRyI+PHJlY3QgY2xhc3M9ImNscy0xIiB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIgcng9IjIwIiByeT0iMjAiLz48L2c+PGcgaWQ9IkxvZ28iPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTEwMi40NiwyNC40OWEyLjc5LDIuNzksMCwwLDEsMS4yMSwyLjg2TDkyLjIzLDk1LjkyYTIuODEsMi44MSwwLDAsMS0xLjQzLDIsMi43NCwyLjc0LDAsMCwxLTEuMzguMzYsMywzLDAsMCwxLTEuMDctLjIyTDY4LjEzLDg5LjgsNTcuMzIsMTAzYTIuNjMsMi42MywwLDAsMS0yLjE5LDEsMi40MSwyLjQxLDAsMCwxLTEtLjE4LDIuODYsMi44NiwwLDAsMS0xLjg3LTIuNjhWODUuNTZMOTAuODUsMzguMjksNDMuMTMsNzkuNTgsMjUuNDksNzIuMzVhMi44MywyLjgzLDAsMCwxLS4zNi01LjA5TDk5LjQyLDI0LjRhMi43MywyLjczLDAsMCwxLDEuNDMtLjRBMi43NywyLjc3LDAsMCwxLDEwMi40NiwyNC40OVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAgMCkiLz48L2c+PC9zdmc+';

    private static function ready() {
        return(class_exists('PeepSo') && self::PLUGIN_VERSION.self::PLUGIN_RELEASE == PeepSo::PLUGIN_VERSION.PeepSo::PLUGIN_RELEASE);
    }

	/**
	 * Initialize all variables, filters and actions
	 */
	private function __construct()
	{
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
        register_activation_hook(__FILE__, array(&$this, 'activate'));

        /** VERSION LOCKED hooks **/
        if(self::ready()) {
            add_action('peepso_init', array(&$this, 'init'));
            add_filter('peepso_profile_preferences', array(&$this, 'edit_preferences_fields'), 99);
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

	/**
	 * Loads the translation file for the PeepSo plugin
	 */
	public function load_textdomain()
	{
		$path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
		load_plugin_textdomain('peepso-email-digest', FALSE, $path);
	}

	/*
	 * Initialize the PeepSoTags plugin
	 */

	public function init()
	{
		global $wpdb;
		
		// Compare last version stored in transient with current version
		if ($this::PLUGIN_VERSION . $this::PLUGIN_RELEASE != get_transient($trans = 'peepso_' . $this::PLUGIN_SLUG . '_version')) {
			$this->activate();
			set_transient($trans, $this::PLUGIN_VERSION . $this::PLUGIN_RELEASE);
		}
		
		// set up tablename
		$this->activity_table = $wpdb->prefix . PeepSoActivity::TABLE_NAME;
		$this->like_table = $wpdb->prefix . PeepSoLike::TABLE;
		$this->user_table = $wpdb->prefix . PeepSoUser::TABLE;
		$this->mailqueue_table = $wpdb->prefix . PeepSoMailQueue::TABLE;
		
		$this->log_table = $wpdb->prefix . $this->log_table;
		$this->content_table = $wpdb->prefix . $this->content_table;

		// set up autoloading
		PeepSo::add_autoload_directory(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
		PeepSoTemplate::add_template_directory(plugin_dir_path(__FILE__));

		$input = new PeepSoInput();
		$debug_param = $input->value('ed', '', array('day','week','month'));

		$is_cron_url = (isset($_GET['peepso_email_digest_event'])) ? TRUE : FALSE;

		add_filter('peepso_email_digest_content', array(&$this, 'filter_email_digest'));
		add_action('peepso_mailqueue_after', array(&$this, 'action_mailqueue_after'));

		if (is_admin()) {
			add_action('admin_init', array(&$this, 'peepso_check'));
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			PeepSoEmailDigestAdmin::get_instance();
			add_filter('peepso_admin_config_tabs', array(&$this, 'admin_config_tabs'));
		}

		if ((defined('DOING_CRON') && DOING_CRON) || !empty($debug_param) || $is_cron_url === TRUE) {
			add_action('pre_user_query', array(PeepSo::get_instance(), 'filter_user_roles'));
			add_action('pre_user_query', array(&$this, 'filter_user_recipients'));

			if ($debug_param == 'day') {
				$this->cron_email_digest_generate('daily');
			} else if ($debug_param == 'week') {
				$this->cron_email_digest_generate('weekly');
			} else if ($debug_param == 'month') {
				$this->cron_email_digest_generate('monthly');
			}
		}

		if (PeepSo::get_option('email_digest_enable', 0) === 1) {
			if ($is_cron_url) {
				$this->cron_email_digest_generate(PeepSo::get_option('email_digest_schedule_type', 'daily'));
			}

			add_action(self::CRON_EMAIL_DIGEST_EVENT, array(&$this, 'cron_email_digest_generate'), 10, 1);
		}

		// Compare last version stored in transient with current version
		if( $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE != get_transient($trans = 'peepso_'.$this::PLUGIN_SLUG.'_version')) {
			set_transient($trans, $this::PLUGIN_VERSION.$this::PLUGIN_RELEASE);
			$this->activate();
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
	 * Plugin activation
	 * Check PeepSo
	 * Run installation
	 * @return bool
	 */
	public function activate()
	{
		if (!$this->peepso_check()) {
			return (FALSE);
		}

		require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'activate.php');
		$install = new PeepSoEmailDigestInstall();
		$res = $install->plugin_activation();
		if (FALSE === $res) {
			// error during installation - disable
			deactivate_plugins(plugin_basename(__FILE__));
		}

		return (TRUE);
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
				<?php echo sprintf(__('The %s plugin requires the PeepSo plugin to be installed and activated.', 'peepso-email-digest'), self::PLUGIN_NAME);?>
				<a href="plugin-install.php?tab=plugin-information&plugin=peepso-core&TB_iframe=true&width=772&height=291" class="thickbox">
					<?php echo __('Get it now!', 'peepso-email-digest');?>
				</a>
			</strong>
		</div>
		<?php
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
	 * Registers a tab in the PeepSo Config Toolbar
	 * PS_FILTER
	 *
	 * @param $tabs array
	 * @return array
	 */
	public function admin_config_tabs($tabs)
	{
		$tabs['email-digest'] = array(
			'label' => __('Email Digest', 'peepso-email-digest'),
			'icon'  => self::ICON,
			'tab' => 'email-digest',
			'description' => __('PeepSo Email Digest', 'peepso-email-digest'),
			'function' => 'PeepSoConfigSectionEmailDigest',
            'cat'   => 'extras',
		);

		return $tabs;
	}

	public function admin_enqueue_scripts()
	{
		$input = new PeepSoInput();
        // SQL injection safe
		if ($input->value('page', '', FALSE) == 'peepso_config' && $input->value('tab', '', FALSE) == 'email-digest') {
			wp_register_script('peepso-admin-config-peepso-email-digest', plugin_dir_url(__FILE__) . 'assets/js/peepso-admin-config.js', array('jquery'), PeepSoEmailDigest::PLUGIN_VERSION, TRUE);

			wp_localize_script('peepso-admin-config-peepso-email-digest', 'emaildigestdata', array(
				'preview_message_success' => __('Preview email was sent success', 'peepso-email-digest'),
				'preview_message_failed' => __('Preview email was sent failed', 'peepso-email-digest'),
				'clear_logs' => __('Clear Logs', 'peepso-email-digest'),
				'clear_logs_description' => sprintf(__('There are %d logs in your database. You can clear the logs by clicking the button below.', 'peepso-email-digest'), $this->get_logs_count()),
				'clear_logs_url' => admin_url('admin.php?page=peepso_config&tab=email-digest&clear_logs=1')
			));

			wp_enqueue_script('peepso-admin-config-peepso-email-digest');
		}
	}

	function cron_email_digest_generate($schedule_type)
	{
		global $wpdb;

		// check if digest email enabled
		if (PeepSo::get_option('email_digest_enable') === 1) {
			$config = PeepSoConfigSettings::get_instance();

			if (isset($_GET['email_digest_reset'])) {
				$wpdb->update($wpdb->usermeta, array('meta_value' => 0), array('meta_key' => 'peepso_email_digest_sent'));
				$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . 'peepso_mail_queue');

				$config->set_option('email_digest_batch', 1);
				$config->set_option('email_digest_schedule_timestamp', time());
				die();
			}

			if (PeepSo::get_option('email_digest_external_cron', 0) === 1) {
				$scheduled_timestamp = PeepSo::get_option('email_digest_schedule_timestamp');
			} else {
				$scheduled_timestamp = wp_next_scheduled(PeepSoEmailDigest::CRON_EMAIL_DIGEST_EVENT, array($schedule_type));
			}

			// get batch number
			$batch = PeepSo::get_option('email_digest_batch', 1);

			// if run cron manually and the time is too early
			if (PeepSo::get_option('email_digest_external_cron', 0) === 1 && $batch === 1 && current_time('timestamp') < $scheduled_timestamp && !isset($_GET['ed'])) {
				echo sprintf(__('Current time is %s, cron scheduled at %s', 'peepso-email-digest'), date('Y-m-d H:i:s', current_time('timestamp')), date('Y-m-d H:i:s', $scheduled_timestamp));
				die();
			}

			if ($batch == 1) {
				$wpdb->update($wpdb->usermeta, array('meta_value' => 0), array('meta_key' => 'peepso_email_digest_sent'));
			}

			// if temporary content exist
			$html = get_transient('peepso_cache_email_digest_html_content', '');

			if (empty($html)) {
				$input = new PeepSoInput();

				// SQL injection safe, it is passed to strtotime()
				$time = $input->value('time', current_time('Y-m-d'), FALSE);

				switch ($schedule_type) {
					case 'daily' :
						$date_start = strtotime('-1 days', strtotime($time));
						$date_end = strtotime(date('Y-m-d', $date_start));
						break;
					case 'weekly' :
						$date_start = strtotime('-7 days', strtotime($time));
						$date_end = strtotime('-1 days', strtotime($time));
						break;
					case 'monthly' :
						$date_start = strtotime(date('Y-m-1', strtotime('-1 month', strtotime($time))));
						$date_end = strtotime(date('Y-m-t', strtotime('-1 month', strtotime($time))));
				}

				$date_start = date('Y-m-d H:i:s', $date_start);
				$date_end = date('Y-m-d 23:59:59', $date_end);

				add_filter('peepso_build_email_digest_content', array(&$this, 'filter_build_email_digest_content'));
				add_filter('peepso_get_digest_post', array(&$this, 'filter_get_digest_post'));

				if (class_exists('PeepSoMoods')) {
					PeepSoMoods::get_instance()->init();
				}

				// generate email layout

				$html = '';

				// check if most liked post enabled
				if (PeepSo::get_option('email_digest_most_liked') === 1) {
					add_filter('peepso_get_most_liked', array(&$this, 'filter_most_liked'));
					$html = apply_filters('peepso_get_most_liked', array('date_start' => $date_start, 'date_end' => $date_end, 'html' => $html));
				}

				// check if most commented post enabled
				if (PeepSo::get_option('email_digest_most_commented') === 1) {
					add_filter('peepso_get_most_commented', array(&$this, 'filter_most_commented'));
					$html = apply_filters('peepso_get_most_commented', array('date_start' => $date_start, 'date_end' => $date_end, 'html' => $html));
				}

				// placed here to get post id of most commented and most liked
				// perform query to get post
				$post_results = apply_filters('peepso_get_digest_post', array('date_start' => $date_start, 'date_end' => $date_end));
				// check if number of post is meet with criteria
				// and send digest email option not depending on post number
				if (
						(count($post_results) < PeepSo::get_option('email_digest_activity_count') &&
						PeepSo::get_option('email_digest_send_less_activities') === 0) ||
						count($post_results) === 0
				) {
					// cancel generate digest email
					echo sprintf(__('There are only %d posts, required at least %d', 'peepso-email-digest'), count($post_results), PeepSo::get_option('email_digest_activity_count'));
					die();
				}

				$i = 0;
				if ($post_results) {
					foreach ($post_results as $post) {
						$html = apply_filters('peepso_build_email_digest_content', array('post' => $post, 'html' => $html, 'title' => $i == 0 ? __('Other popular posts', 'peepso-email-digest') : ''));
						$i++;
					}
				}

				// save html content
				set_transient('peepso_cache_email_digest_html_content', $html, 24 * HOUR_IN_SECONDS);
			}

			// for testing purpose only
			if (isset($_GET['ed'])) {
				echo $html;
			}

			$peepso_roles = PeepSoAdmin::get_instance()->get_translated_roles();
			unset($peepso_roles['ban'], $peepso_roles['register'], $peepso_roles['verified'], $peepso_roles['moderator']);

			$roles = array();
			foreach ($peepso_roles as $key => $val) {
				if (PeepSo::get_option('email_digest_role_' . $key) == 1) {
					$roles[] = $key;
				}
			}

			$limit = PeepSo::get_option('email_digest_per_batch', 100);

			// find all users
			$args = array(
				'orderby' => 'id',
				'order' => 'ASC',
				'peepso_roles' => $roles,
				'number' => $limit,
			);

			$user_query = new WP_User_Query($args);
			$user_results = $user_query->get_results();
			if (isset($_GET['debug_query'])) {
				echo '<pre>';
				print_r($user_query->request);
				echo '</pre>';
			}
			
			// for testing purpose only
			if (isset($_GET['ed'])) {
				echo '<pre>';
				echo 'Content from : ' . $date_start . ' to ' . $date_end .'<br/>';
				print_r(array_column($user_results, 'data'));
				echo '</pre>';
				
				if (!isset($_GET['keep_transient'])) {
					delete_transient('peepso_cache_email_digest_html_content');
				}

				if (!isset($_GET['force'])) {
					die();
				} 
			}

			new PeepSoError('Batch : ' . $batch, 'info', 'emaildigest');
			new PeepSoError('User query : ' . $user_query->request, 'info', 'emaildigest');

			if (count($user_results) > 0) {
				new PeepSoError('User found', 'info', 'emaildigest');
				// prepare data
				$data = array(
					'digestemailcontent' => $html
				);

				foreach ($user_results as $user_item) {
					new PeepSoError('Batch : ' . print_r($user_item, true), 'info', 'emaildigest');
					$user = PeepSoUser::get_instance($user_item->ID);
					$data = array_merge($data, $user->get_template_fields('user'));

					PeepSoMailQueue::add_message($user_item->ID, $data, PeepSo::get_option('email_digest_title', get_bloginfo('name') . ' - ' . __('Email Digest', 'peepso-email-digest')), 'digest', 'email_digest', self::MODULE_ID);
					update_user_meta($user_item->ID, 'peepso_email_digest_sent', 1);
				}

				$next_batch = $batch + 1;
			} else {
				new PeepSoError('No user found', 'info', 'emaildigest');

				$config->remove_option(array('email_digest_batch'));
				PeepSoEmailDigestAdmin::set_schedule_time(PeepSoEmailDigestAdmin::generate_schedule());
				$next_batch = 1;
			}

			$config->set_option('email_digest_batch', $next_batch);

			/*
			if first batch
			then create schedule for next 5 minutes
			until all user emails are sent
			*/
			if ($batch === 1) {
				$new_schedule_param = array(
					'time' => strtotime('+5 minutes', current_time('timestamp')),
					'type' => 'five_minutes',
					'args' => array($schedule_type)
				);
				PeepSoEmailDigestAdmin::set_schedule_time($new_schedule_param);
			}

			if (!isset($_GET['ed'])) {
				echo sprintf(__('Email digest sent to %d users', 'peepso-email-digest'), count($user_results));
			}

			die();
		} else {
			echo __('Email digest is not activated', 'peepso-email-digest');
			die();
		}
	}

	function filter_get_digest_post($args)
	{
		global $wpdb;

		$sql = "SELECT `{$wpdb->posts}`.*, `$this->activity_table`.* " .
				" FROM `{$wpdb->posts}` " .
				" LEFT JOIN `$this->activity_table` ON `act_external_id`=`{$wpdb->posts}`.`ID` " .
				' WHERE `post_type` = %s ' .
				' AND `post_status` = %s ' .
				' AND (`act_access` = %s OR `act_access` = %s)' .
				' AND `post_date` BETWEEN %s AND %s ' .
				' AND `ID` NOT IN (%d, %d) ' .
				" AND `ID` NOT IN (SELECT post_id FROM `{$wpdb->postmeta}` WHERE meta_key = %s) " .
				' ORDER BY `post_date` DESC' .
				' LIMIT %d';

		$sql = $wpdb->prepare($sql, PeepSoActivityStream::CPT_POST, 'publish', PeepSo::ACCESS_PUBLIC, PeepSo::ACCESS_MEMBERS, $args['date_start'], $args['date_end'], $this->peepso_most_commented_post_id, $this->peepso_most_liked_post_id, 'peepso_group_id', PeepSo::get_option('email_digest_activity_count'));

		if (isset($_GET['debug_query'])) {
			echo '<pre>';
			print_r($sql);
			echo '</pre>';
		}

		return $wpdb->get_results($sql);
	}

	function filter_most_liked($args)
	{
		global $wpdb;

		$sql = "SELECT *, count(like_external_id) as act_like_count " .
				" FROM `$this->like_table` " .
				" LEFT JOIN `$this->activity_table` ON `like_external_id` = `act_external_id`" .
				" LEFT JOIN `{$wpdb->posts}` ON `like_external_id` = `ID`" .
				" WHERE `post_type` = %s" .
				' AND `post_status` = %s' .
				' AND (`act_access` = %d OR `act_access` = %d) ' .
				' AND `post_date` BETWEEN %s AND %s ' .
				' AND `ID` != %d' .
				" AND `ID` NOT IN (SELECT post_id FROM `{$wpdb->postmeta}` WHERE meta_key = %s) " .
				' GROUP BY like_external_id' .
				' ORDER BY act_like_count DESC LIMIT 1';

		$sql = $wpdb->prepare($sql, PeepSoActivityStream::CPT_POST, 'publish', PeepSo::ACCESS_PUBLIC, PeepSo::ACCESS_MEMBERS, $args['date_start'], $args['date_end'], $this->peepso_most_commented_post_id, 'peepso_group_id');
		$post = $wpdb->get_row($sql);

		if (isset($_GET['debug_query'])) {
			echo '<pre>';
			print_r($sql);
			echo '</pre>';
		}

		if ($post) {
			if (PeepSo::get_option('email_digest_allow_duplicate', 0) == 0) {
				$this->peepso_most_liked_post_id = $post->ID;
			}
			$html = apply_filters('peepso_build_email_digest_content', array('post' => $post, 'html' => $args['html'], 'title' => __('Most liked post', 'peepso-email-digest')));
		} else {
			$html = $args['html'];
		}
		return $html;
	}

	function filter_most_commented($args)
	{
		global $wpdb;

		$sql = "SELECT * FROM `$this->activity_table`" .
				" LEFT JOIN `{$wpdb->posts}` ON `act_external_id` = `ID`" .
				" LEFT JOIN (SELECT count(`act_comment_object_id`) AS `act_comment_count`, act_comment_object_id " .
				" FROM `$this->activity_table` WHERE `act_comment_object_id` != 0 " .
				" GROUP BY `act_comment_object_id`) `comments` ON `comments`.`act_comment_object_id` = `$this->activity_table`.act_external_id" .
				" WHERE `post_type` = %s" .
				" AND `post_status` = %s" .
				" AND (`act_access` = %d OR `act_access` = %d)" .
				" AND `post_date` BETWEEN %s AND %s" .
				" AND `ID` != %d" .
				" AND `ID` NOT IN (SELECT post_id FROM `{$wpdb->postmeta}` WHERE meta_key = %s) " .
				" AND `$this->activity_table`.`act_comment_object_id` = 0" .
				" ORDER BY act_comment_count DESC LIMIT 1";

		$sql = $wpdb->prepare($sql, PeepSoActivityStream::CPT_POST, 'publish', PeepSo::ACCESS_PUBLIC, PeepSo::ACCESS_MEMBERS, $args['date_start'], $args['date_end'], $this->peepso_most_liked_post_id, 'peepso_group_id');
		$post = $wpdb->get_row($sql);

		if (isset($_GET['debug_query'])) {
			echo '<pre>';
			print_r($sql);
			echo '</pre>';
		}

		if ($post) {
			if (PeepSo::get_option('email_digest_allow_duplicate', 0) == 0) {
				$this->peepso_most_commented_post_id = $post->ID;
			}
			$html = apply_filters('peepso_build_email_digest_content', array('post' => $post, 'html' => $args['html'], 'title' => __('Most commented post', 'peepso-email-digest')));
		} else {
			$html = $args['html'];
		}
		return $html;
	}

	function filter_build_email_digest_content($args)
	{
		if (!empty($args['title'])) {
			$data['title'] = $args['title'];
		}

		$post = $args['post'];
		$user = PeepSoUser::get_instance($post->post_author);
		$data['user_name'] = $user->get_fullname();
		if (PeepSo::get_option('email_digest_use_images') == 1) {
			$data['user_avatar'] = $user->get_avatar();
		}
		$data['activity_url'] = PeepSo::get_page('activity') . '?status/' . $post->post_title;

		$post->post_content = apply_filters('peepso_remove_shortcodes', $post->post_content, $post->ID);
		$post->post_content = strip_tags($post->post_content);
		$post = apply_filters('peepso_email_digest_content', $post);

		if (isset($post->peepso_moods_content)) {
			$this->peepso_moods_content = TRUE;
		}

		$human_friendly = strlen($human_friendly = get_post_meta($post->ID, 'peepso_human_friendly', TRUE)) ? $human_friendly : $post->post_content;
		if (PeepSo::get_option('email_digest_limit_post_enable') === 1 && strlen($human_friendly) > PeepSo::get_option('email_digest_limit_post_length')) {
			//$data['post_content'] = substr($post->post_content, 0, PeepSo::get_option('email_digest_limit_post_length')) . '...';
            $data['post_content'] = trim(truncateHtml($human_friendly, PeepSo::get_option('email_digest_limit_post_length'), '&hellip;', false, true));
		} else {
			$data['post_content'] = $human_friendly;
		}

        // if the post is json-encoded
        if(stristr($post->post_content, PeepSo::BLOGPOSTS_SHORTCODE) && $blogpost = json_decode($post->post_content)) {

            if($blogpost = get_post($blogpost->post_id)) {

                $blog_title = $blogpost->post_title;
                $blog_content = $blogpost->post_content;

                if (PeepSo::get_option('email_digest_limit_post_enable') === 1 && strlen($blog_content) > PeepSo::get_option('email_digest_limit_post_length')) {
                    $blog_content = trim(truncateHtml($blog_content, PeepSo::get_option('email_digest_limit_post_length'), '&hellip;', false, true));
                }

                $content = array(
                    '<h2>',
                    $blog_title,
                    '</h2>',
                    $blog_content,
                );

                $data['post_content'] = implode('', $content);

                // action text
                $data['action_text'] = PeepSo::get_option('blogposts_activity_type_'.$blogpost->post_type.'_text', PeepSo::get_option('blogposts_activity_type_post_text_default'));
            }
        }

		// append peepso moods
		if (isset($post->peepso_moods_content)) {
			$data['post_content'] .= $post->peepso_moods_string;
		}

		// get post attachment
		if (isset($post->post_attachment) && PeepSo::get_option('email_digest_use_images') === 1) {
			$data['post_content'] .= $post->post_attachment;
		}

		$args['html'] .= PeepSoTemplate::exec_template('general', 'email-digest', $data, TRUE);
		return $args['html'];
	}

	/**
	 * Adds the show_on_stream override option to Profile > edit preferences
	 * @param  array $group_fields
	 * @return array
	 */
	public function edit_preferences_fields($group_fields)
	{
		$fields = array();

		$fields['peepso_email_digest_receive_enabled'] = array(
			'label-desc' => __('Receive Digest Email notifications', 'peepso-email-digest'),
			'type' => 'yesno_switch',
			'value' => (int) PeepSoEmailDigestModel::receive_enabled(get_current_user_id()),
			'loading' => TRUE,
		);

		$group_fields['email_digest'] = array(
			'title' => __('Email Digest', 'peepso-email-digest').'<div class="ps-preferences-notifications"></div>',
			'items' => $fields,
		);

		return ($group_fields);
	}

	function filter_user_recipients(WP_User_Query $user_query)
	{
		global $wpdb;

		if (!$user_query->query_vars['meta_key']) {
			$current_time = explode(' ', current_time( 'mysql' ));
			$current_time = $current_time[0];
			$mail_subject = PeepSo::get_option('email_digest_title');

			$user_query->query_where .= " AND `{$wpdb->users}`.`ID` NOT IN (SELECT `user_id` FROM `{$wpdb->usermeta}` WHERE (`meta_key` = 'peepso_email_digest_receive_enabled' AND `meta_value` = 0) OR (`meta_key` = 'peepso_email_digest_sent' AND `meta_value` = 1) GROUP BY `user_id`)";
			$user_query->query_where .= " AND `{$wpdb->users}`.`user_email` NOT IN (SELECT `edl_recipient` FROM `{$this->log_table}` WHERE `edl_sent` LIKE '%$current_time%' )";
			$user_query->query_where .= $wpdb->prepare(" AND `{$wpdb->users}`.`user_email` NOT IN (SELECT `mail_recipient` FROM `{$this->mailqueue_table}` WHERE `mail_subject` = '%s' )", $mail_subject);

			if (!isset($_GET['ed'])) {
				$send_inactive = PeepSo::get_option('email_digest_send_inactive', 1);
				$time = date('Y-m-d', strtotime("-$send_inactive days"));
				$user_query->query_where .= " AND `{$wpdb->users}`.`ID` IN (SELECT `usr_id` FROM `$this->user_table` WHERE `usr_last_activity` < '$time')";
			}

			return $user_query;
		}
	}

	function filter_email_digest($post)
	{
		if (class_exists('PeepSoVideos')) {
			$post_videos = PeepSoVideos::get_instance()->get_post_video($post->ID);

			if (!empty($post_videos)) {
				$video['image'] = $post_videos[0]->vid_thumbnail;
				$post->post_attachment = PeepSoTemplate::exec_template('general', 'email-digest-media', $video, TRUE);
			}
		}

		if (class_exists('PeepSoSharePhotos')) {
			$peepso_photos = PeepSoSharePhotos::get_instance();
			$peepso_photos->init();
			$photos = $peepso_photos->_photos_model->get_post_photos($post->ID);
			$count = count($photos);

			if ($count > 0) {
				$photo['image'] = $photos[0]->pho_thumbs['l'];
				$post->post_attachment = PeepSoTemplate::exec_template('general', 'email-digest-media', $photo, TRUE);
			}
		}

		return $post;
	}
	
	function action_mailqueue_after($mail) 
	{
		global $wpdb;

		if ($mail->mail_module_id == self::MODULE_ID) {
			$html = get_transient('peepso_cache_email_digest_html_content');
			
			// check if content already exist
			$sql = $wpdb->prepare("SELECT * FROM `$this->content_table` WHERE edc_message = %s", $html);
			$result = $wpdb->get_row($sql);

			if (is_null($result)) {
				$content_data = array(
					'edc_subject' => $mail->mail_subject,
					'edc_message' => $html
				);
				
				$wpdb->insert($this->content_table, $content_data);
				$id = $wpdb->insert_id;
			} else {
				$id = $result->edc_id;
			}
						
			$user = PeepSoUser::get_instance($mail->mail_user_id);

			$log_data = array(
				'edl_user_last_login' => $user->get_last_online(),
				'edl_sent' => current_time('mysql'),
				'edl_user_id' => intval($mail->mail_user_id),
				'edl_recipient' => $mail->mail_recipient,
				'edl_content_id' => $id,
			);

			$wpdb->insert($this->log_table, $log_data);
		}
		
	}
	
	/**
	 * Send email that was sent
	 * @param  int $edc_id
	 * @return boolean
	 */
	function preview_email($edc_id) 
	{
		global $wpdb;
				
		$sql = $wpdb->prepare("SELECT edc_message FROM " . $this->content_table . " WHERE `edc_id` = %d", $edc_id);
		$result = $wpdb->get_row($sql);
		
		if ($result && isset($result->edc_message)) {
			$data['digestemailcontent'] = $result->edc_message;

			$user = PeepSoUser::get_instance(get_current_user_id());
			$data = array_merge($data, $user->get_template_fields('user'));

			PeepSoMailQueue::add_message(get_current_user_id(), $data, __('Preview email was sent', 'peepso-email-digest'), 'digest', 'email_digest', 0, 1);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Clear email digest log
	 */
	public static function clear_logs() {
		global $wpdb;
		
		$wpdb->query("TRUNCATE TABLE `" . self::get_instance()->content_table . "`");
		$wpdb->query("TRUNCATE TABLE `" . self::get_instance()->log_table . "`");
		
		wp_redirect(admin_url('admin.php?page=peepso_config&tab=email-digest'));
		exit;
	}
	
	public function get_logs_count() {
		global $wpdb;
		return $wpdb->get_row("SELECT COUNT(*) as total FROM `" . self::get_instance()->log_table . "`")->total;
	}

}

PeepSoEmailDigest::get_instance();

// EOF
