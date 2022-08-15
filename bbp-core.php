<?php
/*
Plugin Name:       BBP Core
Plugin URI:        https://spider-themes.net/bbp-core
Description:       Expand bbPress powered forums with useful features like - private reply, solved topics ...
Author:            SpiderDevs
Author URI:        https://profiles.wordpress.org/spiderdevs/
Text Domain:       bbp-core
Version:           1.0.1
Requires at least: 5.0
Tested up to:      6.0.1
Requires PHP:      7.2
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bc_fs' ) ) {
    // Create a helper function for easy SDK access.
    function bc_fs() {
        global $bc_fs;

        if ( ! isset( $bc_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $bc_fs = fs_dynamic_init( array(
                'id'                  => '10864',
                'slug'                => 'bbp-core',
                'type'                => 'plugin',
                'public_key'          => 'pk_41277ad11125f6e2a1b4e66f40164',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'bbp-core',
                    'account'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $bc_fs;
    }

    // Init Freemius.
    bc_fs();
    // Signal that SDK was initiated.
    do_action( 'bc_fs_loaded' );
}

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
		define( 'BBPC_IMG', BBPC_ASSETS . '/img/' );
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
		define( 'BBPC_FEAT_PATH', plugin_dir_path( __FILE__ ) . 'includes/features/' );

		if ( class_exists( 'bbPress' ) ) {
			if ( $opt['is_solved_topics'] ?? true ) {
				require BBPC_FEAT_PATH . 'bbp_solved_topic.php';
			}

			if ( $opt['is_private_replies'] ?? true ) {
				require BBPC_FEAT_PATH . 'bbp-private-replies.php';
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

		// Custom UI assets.
		$current_url = ! empty( $_GET['page'] ) ? admin_url( 'admin.php?page=' ) . sanitize_text_field( $_GET['page'] ) : '';
		$target_url  = admin_url( 'admin.php?page=bbp-core' );

		if ( $target_url == $current_url ) {
			wp_enqueue_style( 'normalize', BBPC_ASSETS . 'css/normalize.css' );
			wp_enqueue_style( 'nice-select', BBPC_ASSETS . 'css/nice-select.css' );
			wp_enqueue_style( 'jquery-ui', BBPC_ASSETS . 'css/ui-style.css' );

			// Scripts.
			wp_enqueue_script( 'modernizr', BBPC_ASSETS . 'js/modernizr-3.11.2.min.js', [ 'jquery' ], '3.11.2', true );
			wp_enqueue_script( 'jquery-ui', BBPC_ASSETS . 'js/jquery-ui.js', [ 'jquery' ], '1.12.1', true );
			wp_enqueue_script( 'mixitup', BBPC_ASSETS . 'js/mixitup.min.js', [ 'jquery' ], '3.3.1', true );
			wp_enqueue_script( 'mixitup-multifilter', BBPC_ASSETS . 'js/mixitup-multifilter.js', [ 'jquery' ], '3.3.1', true );
			wp_enqueue_script( 'jquery-nice-select', BBPC_ASSETS . 'js/jquery.nice-select.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'tabby-polyfills', BBPC_ASSETS . 'js/tabby.polyfills.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'sortable', BBPC_ASSETS . 'js/Sortable.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'accordion', BBPC_ASSETS . 'js/accordion.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'bbpc-main', BBPC_ASSETS . 'js/main.js', [ 'jquery' ], '1.0', true );
		}
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
// TODO: Move Best Answer to the plugin

// TODO: Settings > Use forum menu or not, if used this, give option to hide those post types, search for it, use filters
// TODO: Use topics, replies from gd bbpress plugin. Thumbnail, excerpt switcher in settings
// TODO: Use topics, forums etc as tabs.
// TODO: Use pagination for topics
// TODO: Bring the js from wp includes folder
// TODO: Sweet alert for deletion @delwer
// TODO: Fix search buttons @delwer
// TODO: Remove add new
// TODO: Remove underline from icons

// TODO: Add voting feature description in readme.txt
// TODO: Preview of ama on plugin page

// TODO: Create blocks for the widgets from gd bbpress plugin, with carbon fields.
// TODO: Move ama template designs to bbp core plugin, as forum theming, we will create multiple themeing for forums.
