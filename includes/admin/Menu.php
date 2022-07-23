<?php
namespace admin;

class menu {
	function __construct() {
		add_action( 'admin_menu', [ $this, 'bbpc_admin_menu' ] );
	}

	/**
	 * Create Admin menu
	 *
	 * @return void
	 */
	public function bbpc_admin_menu() {
		add_menu_page( __( 'BBP Core', 'bbp-core' ), __( 'BBP Core', 'bbp-core' ), 'manage_options', 'bbp-core', [ $this, 'bbpc_plugin_page' ], 'dashicons-buddicons-bbpress-logo', 20 );
	}

	/**
	 * Plugin page callback function
	 *
	 * @return void
	 */
	public function bbpc_plugin_page() {
		?>

		<?php
	}
}
