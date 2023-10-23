<?php
class Admin {
	/**
	 * Admin class construct
	 */
	public function __construct() {
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );
		new admin\Menu();
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

		// if has no pro plan.
		if ( empty( bc_fs()->is_plan( 'pro' ) ) ) {
			$classes .= ' bbpc-no-pro';
		}

		return $classes;
	}
}
