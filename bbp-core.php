<?php
/*
Plugin Name:       BBP Core
Plugin URI:        https://spider-themes.net/bbp-core
Description:       Expand bbPress powered forums with useful features like - private reply, solved topics ...
Author:            SpiderDevs
Author URI:        https://profiles.wordpress.org/spiderdevs/
Text Domain:       bbp-core
Version:           1.0.5
Requires at least: 5.0
Tested up to:      6.0.1
Requires PHP:      7.2
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
*/

use Carbon_Fields\Container;
use Carbon_Fields\Field;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bc_fs' ) ) {
	// Create a helper function for easy SDK access.
	function bc_fs() {
		global $bc_fs;

		if ( ! isset( $bc_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/freemius/start.php';

			$bc_fs = fs_dynamic_init(
				[
					'id'             => '10864',
					'slug'           => 'bbp-core',
					'type'           => 'plugin',
					'public_key'     => 'pk_41277ad11125f6e2a1b4e66f40164',
					'is_premium'     => false,
					'has_addons'     => false,
					'has_paid_plans' => false,
					'menu'           => [
						'slug'    => 'bbp-core',
						'account' => false,
						'support' => false,
					],
				]
			);
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
	 * Class constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->core_includes();

		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts' ], 12 );

		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

		$this->bbpc_hooks();
	}

	/**
	 * Define Plugin Constants.
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'BBPC_VERSION', self::VERSION );
		define( 'BBPC_FILE', __FILE__ );
		define( 'BBPC_DIR', __DIR__ . '/' );
		define( 'BBPC_URL', plugins_url( '/', __FILE__ ) );
		define( 'BBPC_ASSETS', BBPC_URL . 'assets/' );
		define( 'BBPC_IMG', BBPC_ASSETS . 'img/' );
	}

	/**
	 * File includes.
	 */
	public function core_includes() {
		require_once BBPC_DIR . '/includes/functions.php';
		require_once __DIR__ . '/includes/admin/menu/Approve_Topic.php';
		require_once __DIR__ . '/includes/admin/menu/Create_Forum.php';
		require_once __DIR__ . '/includes/admin/menu/Create_Topic.php';
		require_once __DIR__ . '/includes/admin/menu/Delete_Forum.php';
		require_once __DIR__ . '/includes/admin/menu/Delete_Topic.php';
	}

	/**
	 *  Initializing Bbp_core class.
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
	 * Actions on plugin activation.
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
	 * Initialize the plugin functionality.
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
	 * Load different features.
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
	 * Load Necessary assets for the plugin.
	 *
	 * @return void
	 */
	public function load_assets() {
		wp_enqueue_style( 'bbpc', BBPC_ASSETS . 'css/bbpc.css' );
		wp_enqueue_style( 'bbpc-voting', BBPC_ASSETS . 'css/bbpc-voting.css' );

		// BBP Voting.
		wp_enqueue_script( 'bbpc-voting', BBPC_ASSETS . 'js/bbpc-voting.js', [ 'jquery' ], BBPC_VERSION );
		wp_localize_script( 'bbpc-voting', 'bbp_voting_ajax_object', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
	}

	/**
	 * Load Admin Side styles and scripts.
	 *
	 * @return void
	 */
	public function load_admin_scripts() {
		wp_enqueue_style( 'bbpc-admin', BBPC_ASSETS . 'css/bbpc-admin.css' );
		wp_enqueue_style( 'bbpc-admin-global', BBPC_ASSETS . 'css/admin-global.css' );

		$current_url = ! empty( $_GET['page'] ) ? admin_url( 'admin.php?page=' ) . sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		$target_url  = admin_url( 'admin.php?page=bbp-core' );

		if ( $target_url == $current_url ) {
			wp_enqueue_style( 'normalize', BBPC_ASSETS . 'css/normalize.css' );
			wp_enqueue_style( 'nice-select', BBPC_ASSETS . 'css/nice-select.css' );
			wp_enqueue_style( 'jquery-ui', BBPC_ASSETS . 'css/admin-ui-style.css' );

			// Scripts.
			wp_enqueue_script( 'modernizr', BBPC_ASSETS . 'js/modernizr-3.11.2.min.js', [ 'jquery' ], '3.11.2', true );
			wp_enqueue_script( 'jquery-ui', BBPC_ASSETS . 'js/jquery-ui.js', [ 'jquery' ], '1.12.1', true );
			wp_enqueue_script( 'mixitup', BBPC_ASSETS . 'js/mixitup.min.js', [ 'jquery' ], '3.3.1', true );
			wp_enqueue_script( 'mixitup-multifilter', BBPC_ASSETS . 'js/mixitup-multifilter.js', [ 'jquery' ], '3.3.1', true );
			wp_enqueue_script( 'jquery-nice-select', BBPC_ASSETS . 'js/jquery.nice-select.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'tabby-polyfills', BBPC_ASSETS . 'js/tabby.polyfills.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'sortable', BBPC_ASSETS . 'js/Sortable.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'accordion', BBPC_ASSETS . 'js/accordion.min.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'bbpc-admin-main', BBPC_ASSETS . 'js/admin-main.js', [ 'jquery' ], '1.0', true );
			wp_enqueue_script( 'sweetalert', BBPC_ASSETS . 'js/sweetalert.min.js', [ 'jquery' ], '1.0', true );
			wp_localize_script(
				'jquery',
				'bbp_core_local_object',
				[
					'create_forum_title' => esc_html__( 'Enter Forum Title', 'bbp-core' ),
					'create_topic_title' => esc_html__( 'Enter Topic Title', 'bbp-core' ),
					'forum_delete_title' => esc_html__( 'Are you sure to delete?', 'bbp-core' ),
					'forum_delete_desc'  => esc_html__( "This forum will be deleted with all the topics and you won't be able to revert!", 'bbp-core' ),
					'topic_delete_desc'  => esc_html__( "This topic will be deleted and you won't be able to revert!", 'bbp-core' ),
					'BBPC_ASSETS'           		=> BBPC_ASSETS
				]
			);
		}
	}

	/**
	 * Actions and filter hooks in BBP Core plugin.
	 *
	 * @return void
	 */
	public function bbpc_hooks() {
		require BBPC_DIR . 'includes/hooks/actions.php';
		require BBPC_DIR . 'includes/hooks/image_sizes.php';
	}
}

/**
 * Initialize the bbp core plugin.
 *
 * @return \Bbp_core
 */
function bbp_core() {
	return Bbp_core::init();
}

bbp_core();
//TODO: Add hidden replies after reply count, clicking it would show hidden replies.
//TODO: Use cookies for selecting previously selected filter item, even after reload.
//TODO: Attachment feature

// TODO: Use topics, replies from gd bbpress plugin. Thumbnail, excerpt switcher in settings
// TODO: Move ama template designs to bbp core plugin, as forum theming, we will create multiple themeing for forums.
// TODO: Use pagination for topics, use bbp official pagination
//TODO: Design best answer of bbp core as bbp of docy
//TODO: When click on reply count, it will show the following replies, beside that, there will be pending replies.
//TODO: Test with official WordPress themes and other famous themes on worpdress repo.
