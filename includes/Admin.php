<?php
class Admin {
	/**
	 * Admin class construct
	 */
	public function __construct() {
		if ( ! class_exists( 'bbPress' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notices' ] );
		}
		new admin\Menu();
		$this->load_csf();
	}

	/**
	 * Show admin notices when needed.
	 */
	public function admin_notices() {
		$message = sprintf(
			/* translators: 1: BBP Core 2: bbPress */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'bbp-core' ),
			'<strong>' . esc_html__( 'BBP Core Plugin', 'bbp-core' ) . '</strong>',
			'<strong>' . esc_html__( 'bbPress', 'bbp-core' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Load Codestar Framework and related settings
	 *
	 * @return void
	 */
	public function load_csf() {
		require BBPC_DIR . 'includes/admin/settings/codestar-framework/codestar-framework.php';
		require BBPC_DIR . 'includes/admin/settings/options/settings.php';
	}
}
