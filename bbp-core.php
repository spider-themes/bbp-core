<?php
/*
Plugin Name:       BBP Core
Plugin URI:        https://spider-themes.net/bbp-core
Description:       Expand bbPress powered forums with useful features like - private reply, solved topics ...
Author:            SpiderDevs
Author URI:        https://profiles.wordpress.org/spiderdevs/
Text Domain:       bbp-core
Version:           1.0.0
Requires at least: 5.0
Tested up to:      6.0.1
Requires PHP:      7.2
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/autoloader.php';

final class BBP_Core {
	const VERSION = '1.0.0';

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->define_constants();

		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts' ], 12 );
		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

		$this->bbpc_hooks();
	}

	/**
	 * Define Plugin Constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'BBPC_VERSION', self::VERSION );
		define( 'BBPC_FILE', __FILE__ );
		define( 'BBPC_DIR', __DIR__ . '/' );
		define( 'BBPC_URL', plugins_url( '/', __FILE__ ) );
		define( 'BBPC_ASSETS', BBPC_URL . '/assets/' );
	}

	/**
	 *  Initializing Bbp_core class
	 *
	 * @return \Bbp_core
	 */
	static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}
	}

	/**
	 * Actions on plugin activation
	 *
	 * @return void
	 */
	public function activate() {
		$installed = get_option( 'bbpc_installed' );

		if ( ! $installed ) {
			update_option( 'bbpc_installed', time() );
		}

		update_option( 'bbpc_version', BBPC_VERSION );
	}


	/**
	 * Initialize the plugin functionality
	 *
	 * @return void
	 */
	public function init_plugin() {
		$this->load_features();

		if ( is_admin() ) {
			new Admin();
		} else {
			new Frontend();
		}
	}


	/**
	 * Load different features
	 *
	 * @return void
	 */
	public function load_features() {
		$opt = get_option( 'bbp_core_settings' );
		define( 'FEAT_PATH', plugin_dir_path( __FILE__ ) . 'includes/features/' );

		if ( class_exists( 'bbPress' ) ) {
			if ( $opt['is_solved_topics'] ?? true ) {
				require FEAT_PATH . 'bbp_solved_topic.php';
			}

			if ( $opt['is_private_replies'] ?? true ) {
				require FEAT_PATH . 'bbp-private-replies.php';
			}

			if ( $opt['is_votes'] ?? true ) {
				new features\bbp_voting();
			}
		}
	}

	/**
	 * Load Necessary assets for the plugin
	 *
	 * @return void
	 */
	public function load_assets() {
		wp_enqueue_style( 'bbpc', BBPC_ASSETS . 'css/bbpc.css' );
		wp_enqueue_style( 'bbpc-voting', BBPC_ASSETS . 'css/bbpc-voting.css' );

		// BBP Voting.
		wp_enqueue_script( 'bbpc-voting', BBPC_ASSETS . '/js/bbpc-voting.js', [ 'jquery' ], BBPC_VERSION );
		wp_localize_script( 'bbpc-voting', 'bbp_voting_ajax_object', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
	}

	/**
	 * Load Admin Side styles and scripts.
	 *
	 * @return void
	 */
	public function load_admin_scripts() {
		wp_enqueue_style( 'bbpc-admin', BBPC_ASSETS . 'css/bbpc-admin.css' );
	}

	/**
	 * Actions and filter hooks in BBP Core plugin
	 *
	 * @return void
	 */
	public function bbpc_hooks() {
		require BBPC_DIR . 'includes/hooks/actions.php';
	}
}

/**
 * Initialize the bbp core plugin
 *
 * @return \Bbp_core
 */
function bbp_core() {
	return Bbp_core::init();
}

bbp_core();

// TODO:: Add voting system
// TODO: Add voting below user pictures, use hook - bbp_theme_after_reply_author_details
// TODO: Check real world websites for front end design, e.g - stack overflow
