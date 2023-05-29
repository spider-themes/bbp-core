<?php
class Admin {
	/**
	 * Admin class construct
	 */
	public function __construct() {
		
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );

		new admin\Menu();
		$this->load_csf();
	}

	/**
	 * Load Codestar Framework and related settings.
	 *
	 * @return void
	 */
	public function load_csf() {
		require BBPC_DIR . 'includes/admin/settings/codestar-framework/codestar-framework.php';
		
		if ( class_exists( 'BBPCorePro' ) ) {
			require BBPC_DIR . 'includes/admin/settings/options/pro-settings.php';
		} else {
			require BBPC_DIR . 'includes/admin/settings/options/settings.php';
		}
	}
	
	/**
	 * Add body class to admin pages.
	 *
	 * @param string $classes Body classes.
	 * @return string
	 */
	public function body_class( $classes ) {
		// if current page is ?page=bbp-core in admin.
		if ( isset( $_GET['page'] ) && 'bbp-core' === $_GET['page'] ) {
			$classes .= ' bbpc-forum-ui';
		}
		return $classes;
	}
}
