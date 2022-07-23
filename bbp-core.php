<?php
/*
Plugin Name:       BBP Core
Plugin URI:        https://spider-themes.net/bbp-core
Description:       Expand bbPress powered forums with attachments upload, BBCodes support, signatures, widgets, quotes, toolbar menu, activity tracking, enhanced widgets, extra views...
Author:            SpiderDevs
Author URI:        https://profiles.wordpress.org/spiderdevs/
Text Domain:       bbp-core
Version:           1.0.0
Requires at least: 5.3
Tested up to:      5.9
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
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts' ] );
		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
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
		if ( is_admin() ) {
			new Admin();
		} else {
			new Frontend();
		}
	}

	/**
	 * Load Necessary assets for the plugin
	 *
	 * @return void
	 */
	public function load_assets() {
		wp_enqueue_style( 'bbpc', BBPC_ASSETS . 'css/bbpc.css' );
	}

	/**
	 * Load Admin Side styles and scripts.
	 *
	 * @return void
	 */
	public function load_admin_scripts(){
		wp_enqueue_style( 'bbpc-admin', BBPC_ASSETS . 'css/bbpc-admin.css' );
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

// TODO: Bring in from Ama core
//TODO:: Add voting system
