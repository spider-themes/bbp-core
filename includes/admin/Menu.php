<?php
namespace admin;

class Menu {
	function __construct() {
		add_action( 'admin_menu', [ $this, 'bbpc_admin_menu' ] );
	}

	/**
	 * Create Admin menu
	 *
	 * @return void
	 */
	public function bbpc_admin_menu() {
		$capability = 'manage_options';
		add_menu_page( __( 'BBP Core', 'bbp-core' ), __( 'BBP Core', 'bbp-core' ), $capability, 'bbp-core', [ $this, 'bbpc_plugin_page' ], 'dashicons-buddicons-bbpress-logo', 20 );
		add_submenu_page( 'bbp-core', __( 'BBP Core Dashboard', 'bbp-core' ), __( 'Dashboard', 'bbp-core' ), $capability, 'admin.php?page=bbp-core-dashboard', [ $this, 'bbpc_statistics_dashboard' ] );
	}

	/**
	 * Plugin page callback function.
	 *
	 * @return void
	 */
	public function bbpc_plugin_page() {
		include plugin_dir_path( __FILE__ ) . '/menu/admin_ui.php';
	}

	/**
	 * Dashboard Statistics.
	 *
	 * @return void
	 */
	public function bbpc_statistics_dashboard() {
		include plugin_dir_path( __FILE__ ) . '/menu/statistics.php';
	}
}
