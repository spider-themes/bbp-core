<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

use d4p_core_four;
use d4p_datetime_core;
use Dev4Press\Plugin\GDBBX\Features\AdminAccess;
use Dev4Press\Plugin\GDBBX\Features\AdminColumns;
use Dev4Press\Plugin\GDBBX\Features\AdminWidgets;
use Dev4Press\Plugin\GDBBX\Features\Attachments;
use Dev4Press\Plugin\GDBBX\Features\AutoCloseTopics;
use Dev4Press\Plugin\GDBBX\Features\BBCodes;
use Dev4Press\Plugin\GDBBX\Features\BuddyPressNotifications;
use Dev4Press\Plugin\GDBBX\Features\BuddyPressSignature;
use Dev4Press\Plugin\GDBBX\Features\BuddyPressTweaks;
use Dev4Press\Plugin\GDBBX\Features\CannedReplies;
use Dev4Press\Plugin\GDBBX\Features\Clickable;
use Dev4Press\Plugin\GDBBX\Features\CloseTopicControl;
use Dev4Press\Plugin\GDBBX\Features\ContentEditor;
use Dev4Press\Plugin\GDBBX\Features\CustomViews;
use Dev4Press\Plugin\GDBBX\Features\DisableRSS;
use Dev4Press\Plugin\GDBBX\Features\EmailOverrides;
use Dev4Press\Plugin\GDBBX\Features\EmailSender;
use Dev4Press\Plugin\GDBBX\Features\FooterActions;
use Dev4Press\Plugin\GDBBX\Features\ForumIndex;
use Dev4Press\Plugin\GDBBX\Features\Icons;
use Dev4Press\Plugin\GDBBX\Features\JournalTopic;
use Dev4Press\Plugin\GDBBX\Features\LockForums;
use Dev4Press\Plugin\GDBBX\Features\LockTopics;
use Dev4Press\Plugin\GDBBX\Features\MIMETypes;
use Dev4Press\Plugin\GDBBX\Features\Notifications;
use Dev4Press\Plugin\GDBBX\Features\Objects;
use Dev4Press\Plugin\GDBBX\Features\PostAnonymously;
use Dev4Press\Plugin\GDBBX\Features\Privacy;
use Dev4Press\Plugin\GDBBX\Features\PrivateReplies;
use Dev4Press\Plugin\GDBBX\Features\PrivateTopics;
use Dev4Press\Plugin\GDBBX\Features\Profiles;
use Dev4Press\Plugin\GDBBX\Features\ProtectRevisions;
use Dev4Press\Plugin\GDBBX\Features\Publish;
use Dev4Press\Plugin\GDBBX\Features\Quote;
use Dev4Press\Plugin\GDBBX\Features\Replies;
use Dev4Press\Plugin\GDBBX\Features\ReplyActions;
use Dev4Press\Plugin\GDBBX\Features\Report;
use Dev4Press\Plugin\GDBBX\Features\Rewriter;
use Dev4Press\Plugin\GDBBX\Features\ScheduleTopic;
use Dev4Press\Plugin\GDBBX\Features\SEO;
use Dev4Press\Plugin\GDBBX\Features\SEOTweaks;
use Dev4Press\Plugin\GDBBX\Features\Shortcodes;
use Dev4Press\Plugin\GDBBX\Features\Signatures;
use Dev4Press\Plugin\GDBBX\Features\Snippets;
use Dev4Press\Plugin\GDBBX\Features\Thanks;
use Dev4Press\Plugin\GDBBX\Features\Toolbar;
use Dev4Press\Plugin\GDBBX\Features\TopicActions;
use Dev4Press\Plugin\GDBBX\Features\Topics;
use Dev4Press\Plugin\GDBBX\Features\Tweaks;
use Dev4Press\Plugin\GDBBX\Features\UserSettings;
use Dev4Press\Plugin\GDBBX\Features\UsersStats;
use Dev4Press\Plugin\GDBBX\Features\VisitorsRedirect;
use gdbbx_mod_online;
use gdbbx_mod_tracking;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	private $_first_template = false;
	private $_first_request = false;
	private $_date_time;

	public $svg_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDY0IDY0IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zOnNlcmlmPSJodHRwOi8vd3d3LnNlcmlmLmNvbS8iIHN0eWxlPSJmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtzdHJva2UtbGluZWpvaW46cm91bmQ7c3Ryb2tlLW1pdGVybGltaXQ6MjsiPgogICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMC4wNDY5NzI1LDAsMCwwLjA0Njk3MjUsLTguMTM3NDEsLTEzLjU3NSkiPgogICAgICAgIDxwYXRoIGQ9Ik01MDAuNSwyODlMNzM2LjQ5Miw0MjUuMjVMNzM2LjQ5Miw2OTcuNzVMNTAwLjUsODM0TDI2NC41MDgsNjk3Ljc1TDI2NC41MDgsNDI1LjI1TDUwMC41LDI4OVoiIHN0eWxlPSJmaWxsOnJnYigyMzgsMjM4LDIzOCk7Ii8+CiAgICAgICAgPHBhdGggZD0iTTUwMC41LDI4OUw3MzYuNDkyLDQyNS4yNUw3MzYuNDkyLDY5Ny43NUw1MDAuNSw4MzRMMjY0LjUwOCw2OTcuNzVMMjY0LjUwOCw0MjUuMjVMNTAwLjUsMjg5Wk0yOTYuMTM4LDQ0My41MTFMMjk2LjEzOCw2NzkuNDg5TDUwMC41LDc5Ny40NzdMNzA0Ljg2Miw2NzkuNDg5TDcwNC44NjIsNDQzLjUxMUw1MDAuNSwzMjUuNTIzTDI5Ni4xMzgsNDQzLjUxMVoiIHN0eWxlPSJmaWxsOnJnYigyMzgsMjM4LDIzOCk7Ii8+CiAgICA8L2c+CiAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgwLjA0Njk3MjUsMCwwLDAuMDQ2OTcyNSwyLjk0NzcxLDUuNjI0OTUpIj4KICAgICAgICA8cGF0aCBkPSJNNTAwLjUsMjg5TDczNi40OTIsNDI1LjI1TDczNi40OTIsNjk3Ljc1TDUwMC41LDgzNEwyNjQuNTA4LDY5Ny43NUwyNjQuNTA4LDQyNS4yNUw1MDAuNSwyODlaIiBzdHlsZT0iZmlsbDpyZ2IoMjM4LDIzOCwyMzgpOyIvPgogICAgICAgIDxwYXRoIGQ9Ik01MDAuNSwyODlMNzM2LjQ5Miw0MjUuMjVMNzM2LjQ5Miw2OTcuNzVMNTAwLjUsODM0TDI2NC41MDgsNjk3Ljc1TDI2NC41MDgsNDI1LjI1TDUwMC41LDI4OVpNMjgwLjMyMyw0MzQuMzgxTDI4MC4zMjMsNjg4LjYxOUw1MDAuNSw4MTUuNzM5TDcyMC42NzcsNjg4LjYxOUw3MjAuNjc3LDQzNC4zODFMNTAwLjUsMzA3LjI2MUwyODAuMzIzLDQzNC4zODFaIiBzdHlsZT0iZmlsbDpyZ2IoMjM4LDIzOCwyMzgpOyIvPgogICAgPC9nPgogICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMC4wNDY5NzI1LDAsMCwwLjA0Njk3MjUsLTguMTM3NDEsMjQuODI1KSI+CiAgICAgICAgPHBhdGggZD0iTTUwMC41LDI4OUw3MzYuNDkyLDQyNS4yNUw3MzYuNDkyLDY5Ny43NUw1MDAuNSw4MzRMMjY0LjUwOCw2OTcuNzVMMjY0LjUwOCw0MjUuMjVMNTAwLjUsMjg5Wk0zMjMuNTA2LDQ1OS4zMTNMMzIzLjUwNiw2NjMuNjg4TDUwMC41LDc2NS44NzVMNjc3LjQ5NCw2NjMuNjg3TDY3Ny40OTQsNDU5LjMxM0w1MDAuNSwzNTcuMTI1TDMyMy41MDYsNDU5LjMxM1oiIHN0eWxlPSJmaWxsOnJnYigyMzgsMjM4LDIzOCk7Ii8+CiAgICA8L2c+CiAgICA8ZyB0cmFuc2Zvcm09Im1hdHJpeCgwLjA0Njk3MjUsMCwwLDAuMDQ2OTcyNSwyNS4xMTgsNS42MjQ5NSkiPgogICAgICAgIDxwYXRoIGQ9Ik01MDAuNSwyODlMNzM2LjQ5Miw0MjUuMjVMNzM2LjQ5Miw2OTcuNzVMNTAwLjUsODM0TDI2NC41MDgsNjk3Ljc1TDI2NC41MDgsNDI1LjI1TDUwMC41LDI4OVpNMzIzLjUwNiw0NTkuMzEzTDMyMy41MDYsNjYzLjY4OEw1MDAuNSw3NjUuODc1TDY3Ny40OTQsNjYzLjY4N0w2NzcuNDk0LDQ1OS4zMTJMNTAwLjUsMzU3LjEyNUwzMjMuNTA2LDQ1OS4zMTNaIiBzdHlsZT0iZmlsbDpyZ2IoMjM4LDIzOCwyMzgpOyIvPgogICAgPC9nPgogICAgPGcgdHJhbnNmb3JtPSJtYXRyaXgoMC4wNDY5NzI1LDAsMCwwLjA0Njk3MjUsMTQuMDMyOCwtMTMuNTc1KSI+CiAgICAgICAgPHBhdGggZD0iTTUwMC41LDI4OUw3MzYuNDkyLDQyNS4yNUw3MzYuNDkyLDY5Ny43NUw1MDAuNSw4MzRMMjY0LjUwOCw2OTcuNzVMMjY0LjUwOCw0MjUuMjVMNTAwLjUsMjg5WiIgc3R5bGU9ImZpbGw6cmdiKDIzOCwyMzgsMjM4KTsiLz4KICAgICAgICA8cGF0aCBkPSJNNTAwLjUsMjg5TDczNi40OTIsNDI1LjI1TDczNi40OTIsNjk3Ljc1TDUwMC41LDgzNEwyNjQuNTA4LDY5Ny43NUwyNjQuNTA4LDQyNS4yNUw1MDAuNSwyODlaTTI4MC4zMjMsNDM0LjM4MUwyODAuMzIzLDY4OC42MTlMNTAwLjUsODE1LjczOUw3MjAuNjc3LDY4OC42MTlMNzIwLjY3Nyw0MzQuMzgxTDUwMC41LDMwNy4yNjFMMjgwLjMyMyw0MzQuMzgxWiIgc3R5bGU9ImZpbGw6cmdiKDIzOCwyMzgsMjM4KTsiLz4KICAgIDwvZz4KICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDAuMDQ2OTcyNSwwLDAsMC4wNDY5NzI1LDE0LjAzMjgsMjQuODI1KSI+CiAgICAgICAgPHBhdGggZD0iTTUwMC41LDI4OUw3MzYuNDkyLDQyNS4yNUw3MzYuNDkyLDY5Ny43NUw1MDAuNSw4MzRMMjY0LjUwOCw2OTcuNzVMMjY0LjUwOCw0MjUuMjVMNTAwLjUsMjg5WiIgc3R5bGU9ImZpbGw6cmdiKDIzOCwyMzgsMjM4KTsiLz4KICAgICAgICA8cGF0aCBkPSJNNTAwLjUsMjg5TDczNi40OTIsNDI1LjI1TDczNi40OTIsNjk3Ljc1TDUwMC41LDgzNEwyNjQuNTA4LDY5Ny43NUwyNjQuNTA4LDQyNS4yNUw1MDAuNSwyODlaTTI4MC4zMjMsNDM0LjM4MUwyODAuMzIzLDY4OC42MTlMNTAwLjUsODE1LjczOUw3MjAuNjc3LDY4OC42MTlMNzIwLjY3Nyw0MzQuMzgxTDUwMC41LDMwNy4yNjFMMjgwLjMyMyw0MzQuMzgxWiIgc3R5bGU9ImZpbGw6cmdiKDIzOCwyMzgsMjM4KTsiLz4KICAgIDwvZz4KPC9zdmc+Cg==';

	public $buddypress = false;
	public $debug = false;

	public $modules = array();
	public $objects = array();

	public $is_search = false;
	public $is_feed = false;

	public $load = array();

	public function __construct() {
		$this->_date_time = new d4p_datetime_core();
	}

	public static function instance() : Plugin {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Plugin();
			$instance->run();
		}

		return $instance;
	}

	private function run() {
		gdbbx_roles();

		add_action( 'plugins_loaded', array( $this, 'core' ) );
		add_action( 'after_setup_theme', array( $this, 'theme' ) );

		add_action( 'gdbbx_cron_daily_maintenance_job', array( $this, 'daily_maintenance_job' ) );
		add_action( 'gdbbx_clear_bulk_directory', array( $this, 'clear_bulk_directory' ) );

		add_filter( 'bbp_get_template_part', array( $this, 'template_part_first' ), 0 );
		add_action( 'bbp_post_request', array( $this, 'request_first' ), 0 );

		add_action( 'template_redirect', array( $this, 'template_redirect' ), 7 );
		add_action( 'gdbbx_plugin_settings_loaded', array( $this, 'early' ) );
	}

	/** @return d4p_datetime_core */
	public function datetime() : d4p_datetime_core {
		return $this->_date_time;
	}

	public function is_enabled( $name ) {
		return $this->load[ $name ];
	}

	public function core() {
		global $wp_version;

		$version = substr( str_replace( '.', '', $wp_version ), 0, 2 );
		define( 'GDBBX_WPV', intval( $version ) );

		$this->debug      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		$this->buddypress = gdbbx_has_buddypress();

		if ( GDBBX_WPV < 53 ) {
			add_action( 'admin_notices', array( $this, 'system_requirements_problem' ) );
		}

		if ( gdbbx_has_bbpress() ) {
			$this->translations();
			$this->cron();

			add_action( 'bbp_init', array( $this, 'load' ), 1 );
			add_action( 'bbp_init', array( $this, 'init' ), 2 );
			add_action( 'bbp_feed', array( $this, 'feed' ), 5 );

			add_action( 'bbp_ready', array( $this, 'ready' ) );

			do_action( 'gdbbx_plugin_core_ready' );
		} else {
			add_action( 'admin_notices', array( $this, 'bbpress_requirements_problem' ) );
		}
	}

	public function early() {
		if ( ! gdbbx_has_bbpress() ) {
			return;
		}

		$this->load = gdbbx()->group_get( 'load' );

		$this->run_early();
	}

	public function load() {
		if ( function_exists( 'bbp_is_search' ) ) {
			$this->is_search = bbp_is_search();
		}

		bbPress::instance();
		Requests::instance();
		Feed::instance();
		Search::instance();

		$this->run_global();

		if ( is_admin() ) {
			$this->run_admin();
		} else {
			$this->run_frontend();
		}

		$this->modules();

		Widgets::instance();
		Enqueue::instance();
		Template::instance();
		Navigation::instance();
	}

	public function init() {
		do_action( 'gdbbx_init' );
	}

	public function feed() {
		$this->is_feed = true;

		do_action( 'gdbbx_feed' );
	}

	public function ready() {
		do_action( 'gdbbx_core' );
	}

	public function template_redirect() {
		do_action( 'gdbbx_template' );
	}

	public function request_first() {
		if ( ! $this->_first_request ) {
			do_action( 'gdbbx_bbpress_request_first' );

			$this->_first_request = true;
		}

		remove_action( 'bbp_post_request', array( $this, 'request_first' ), 0 );
	}

	public function template_part_first( $templates ) {
		if ( ! $this->_first_template ) {
			do_action( 'gdbbx_bbpress_template_first' );

			$this->_first_template = true;
		}

		remove_filter( 'bbp_get_template_part', array( $this, 'template_part_first' ), 0 );

		return $templates;
	}

	public function modules() {
		if ( ! D4P_CRON ) {
			require_once( GDBBX_PATH . 'modules/features/mod.tracking.php' );
			$this->modules['tracking'] = new gdbbx_mod_tracking();

			require_once( GDBBX_PATH . 'modules/features/mod.online.php' );
			$this->modules['online'] = new gdbbx_mod_online();
		}
	}

	public function theme() {
		require_once( GDBBX_PATH . 'core/functions/theme.php' );
	}

	public function cron() {
		if ( ! wp_next_scheduled( 'gdbbx_cron_daily_maintenance_job' ) ) {
			$cron_hour = apply_filters( 'gdbbx_cron_daily_maintenance_job_hour', 8 );
			$cron_time = mktime( $cron_hour, 5, 0, date( 'm' ), date( 'd' ), date( 'Y' ) );

			wp_schedule_event( $cron_time, 'daily', 'gdbbx_cron_daily_maintenance_job' );
		}
	}

	public function translations() {
		load_plugin_textdomain( 'gd-bbpress-toolbox', false, 'gd-bbpress-toolbox/languages' );
		load_plugin_textdomain( 'd4plib', false, 'gd-bbpress-toolbox/d4plib/languages' );
	}

	public function clear_bulk_directory() {
		$dir = wp_upload_dir();

		if ( $dir['error'] === false ) {
			$file_path = trailingslashit( $dir['basedir'] ) . 'gdbbx/';

			$proceed = true;
			if ( ! file_exists( $file_path ) ) {
				$proceed = wp_mkdir_p( $file_path );
			}

			if ( $proceed ) {
				$age = apply_filters( 'gdbbx_bulk_download_cleanup_age', 1800 );

				$files = d4p_scan_dir( $file_path, 'files', array( 'bbx' ) );

				foreach ( $files as $file ) {
					$parts = explode( '-', $file );

					if ( count( $parts ) > 2 ) {
						$time = absint( $parts[1] );

						if ( $time + $age < time() ) {
							unlink( $file_path . $file );
						}
					}
				}
			}
		}
	}

	public function daily_maintenance_job() {
		do_action( 'gdbbx_daily_maintenance_job' );
	}

	public function get_transient_key( $name ) : string {
		$version = absint( str_replace( '.', '', gdbbx()->info_version ) );

		return 'gdbbx_v' . $version . '_' . $name;
	}

	public function user_meta_key_last_activity() : string {
		return gdbbx_db()->prefix() . 'bbp_last_activity';
	}

	public function get_user_last_activity( $user_id ) : int {
		$timestamp = get_user_meta( $user_id, $this->user_meta_key_last_activity(), true );

		if ( $timestamp == '' ) {
			$timestamp = get_user_meta( $user_id, 'bbp_last_activity', true );
		}

		return intval( $timestamp );
	}

	public function update_user_last_activity( $user_id, $timestamp = 0 ) {
		update_user_meta( $user_id, $this->user_meta_key_last_activity(), $timestamp );
	}

	public function deactivate() {
		deactivate_plugins( 'gd-bbpress-toolbox/gd-bbpress-toolbox.php', false );
	}

	public function system_requirements_problem() {
		?>

        <div class="notice notice-error">
            <p><?php _e( "GD bbPress Toolbox Pro requires WordPress 5.3 or newer. Plugin will now be disabled. To use this plugin, upgrade WordPress to 5.1 or newer version.", "bbp-core" ); ?></p>
        </div>

		<?php

		$this->deactivate();
	}

	public function bbpress_requirements_problem() {
		?>

        <div class="notice notice-error">
            <p><?php _e( "GD bbPress Toolbox Pro requires bbPress plugin for WordPress version 2.6.2 or newer. Plugin will now be disabled. To use this plugin, make sure you are using bbPress 2.6.2 or newer version.", "bbp-core" ); ?></p>
        </div>

		<?php

		$this->deactivate();
	}

	public function recommend( $panel = 'update' ) : string {
		d4p_includes( array(
			array( 'name' => 'ip', 'directory' => 'classes' ),
			array( 'name' => 'four', 'directory' => 'classes' )
		), GDBBX_D4PLIB );

		$four = new d4p_core_four( 'plugin', 'gd-bbpress-toolbox', gdbbx()->info_version, gdbbx()->info_build );
		$four->ad();

		return $four->ad_render( $panel );
	}

	private function run_early() {
		UserSettings::instance();

		if ( $this->load['objects'] ) {
			Objects::instance();
		}

		if ( $this->load['publish'] ) {
			Publish::instance();
		}

		if ( $this->load['visitors-redirect'] ) {
			VisitorsRedirect::instance();
		}

		if ( $this->load['disable-rss'] ) {
			DisableRSS::instance();
		}

		if ( $this->load['mime-types'] ) {
			MIMETypes::instance();
		}

		if ( $this->load['email-sender'] ) {
			EmailSender::instance();
		}

		if ( $this->load['email-overrides'] ) {
			EmailOverrides::instance();
		}

		if ( $this->load['auto-close-topics'] ) {
			AutoCloseTopics::instance();
		}
	}

	private function run_global() {
		Icons::instance();
		Tweaks::instance();

		TopicActions::instance();
		ReplyActions::instance();

		CustomViews::instance();
		Shortcodes::instance();

		if ( $this->load['bbcodes'] ) {
			BBCodes::instance();
		}

		if ( $this->load['attachments'] ) {
			Attachments::instance();
		}

		if ( $this->load['rewriter'] ) {
			Rewriter::instance();
		}

		if ( $this->load['privacy'] ) {
			Privacy::instance();
		}

		if ( $this->load['post-anonymously'] ) {
			PostAnonymously::instance();
		}

		if ( $this->load['journal-topic'] ) {
			JournalTopic::instance();
		}

		if ( $this->load['notifications'] ) {
			Notifications::instance();
		}

		if ( $this->load['toolbar'] ) {
			Toolbar::instance();
		}

		if ( $this->load['lock-forums'] ) {
			LockForums::instance();
		}

		if ( $this->load['lock-topics'] ) {
			LockTopics::instance();
		}

		if ( $this->load['clickable'] ) {
			Clickable::instance();
		}

		if ( $this->load['signatures'] ) {
			Signatures::instance();
		}

		if ( $this->load['topics'] ) {
			Topics::instance();
		}

		if ( $this->load['private-topics'] ) {
			PrivateTopics::instance();
		}

		if ( $this->load['replies'] ) {
			Replies::instance();
		}

		if ( $this->load['close-topic-control'] ) {
			CloseTopicControl::instance();
		}

		if ( $this->load['private-topics'] ) {
			PrivateTopics::instance();
		}

		if ( $this->load['private-replies'] ) {
			PrivateReplies::instance();
		}

		if ( $this->load['thanks'] ) {
			Thanks::instance();
		}

		if ( $this->load['report'] ) {
			Report::instance();
		}

		if ( $this->load['canned-replies'] ) {
			CannedReplies::instance();
		}

		if ( $this->buddypress ) {
			if ( $this->load['buddypress-tweaks'] ) {
				BuddyPressTweaks::instance();
			}

			if ( $this->load['buddypress-notifications'] ) {
				BuddyPressNotifications::instance();
			}

			if ( $this->load['buddypress-signature'] ) {
				BuddyPressSignature::instance();
			}
		}
	}

	private function run_admin() {
		if ( ! is_super_admin() && $this->load['admin-access'] ) {
			AdminAccess::instance();
		}

		if ( gdbbx_can_user_moderate() ) {
			if ( $this->load['admin-widgets'] ) {
				AdminWidgets::instance();
			}

			if ( $this->load['admin-columns'] ) {
				AdminColumns::instance();
			}
		}
	}

	private function run_frontend() {
        ContentEditor::instance();

		if ( $this->load['footer-actions'] ) {
			FooterActions::instance();
		}

		if ( $this->load['seo'] ) {
			SEO::instance();
		}

		if ( $this->load['seo-tweaks'] ) {
			SEOTweaks::instance();
		}

		if ( $this->load['forum-index'] ) {
			ForumIndex::instance();
		}

		if ( $this->load['users-stats'] ) {
			UsersStats::instance();
		}

		if ( $this->load['profiles'] ) {
			Profiles::instance();
		}

		if ( $this->load['quote'] ) {
			Quote::instance();
		}

		if ( $this->load['protect-revisions'] ) {
			ProtectRevisions::instance();
		}

		if ( $this->load['snippets'] ) {
			Snippets::instance();
		}

		if ( $this->load['schedule-topic'] ) {
			ScheduleTopic::instance();
		}
	}
}