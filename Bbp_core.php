<?php

/*
Plugin Name:       BBP Core
Plugin URI:        https://helpdesk.spider-themes.net/bbp-core
Description:       Responsive and modern theme to fully replace default bbPress theme templates and styles, with multiple colour schemes, options panel and customizer control.
Author:            spiderdevs
Author URI:        https://profiles.wordpress.org/spiderdevs/
Text Domain:       bbp-core
Version:           1.0.0
Requires at least: 5.0
Tested up to:      5.9
Requires PHP:      7.4
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

final class Bbp_core{
	const VERSION  = '1.0.0';

	public function __construct() {
		$this->define_constants();
		$this->core_loads();
		
		register_activation_hook(__FILE__, [$this, 'activate']);
		add_action( 'wp_enqueue_scripts', [$this, 'load_assets' ], 11);
		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

		$this->core_loads();
	}

	/**
	 * Define Plugin Constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'BBPC_PATH', __DIR__ );

		define( 'BBPC_VERSION', self::VERSION );
		define( 'BBPC_FILE', __FILE__ );
		define( 'BBPC_DIR', __DIR__ );
		define( 'BBPC_URL', plugins_url( '/', __FILE__ ) );
		define( 'BBPC_ASSETS', BBPC_URL . '/assets' );

		define( "BBPC_LIB", BBPC_PATH . '/d4plib/' );
		define( "BBPC_LIB_URL", BBPC_URL . '/d4plib/' );
		define( "BBPC_THEME", BBPC_PATH . '/templates/quantum/' );
	}

	/**
	 *  Initializing Bbpc class
	 *
	 * @return \Bbp_core
	 */
	static function init(){
		static $instance = false;

		if(!$instance){
			$instance = new self();
		}
	}

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
			new \Bbp\Core\Admin();
		} else {
			new \Bbp\Core\Frontend();
		}
	}

	/**
	 * Load files with the core plugin
	 *
	 * @return void
	 */
	public function core_loads() {

	}

	public function load_assets() {

	}
}

/**
 * Initialize the bbp core plugin
 *
 * @return \Bbp_core
 */
function bbp_core(){
	return Bbp_core::init();
}

bbp_core();