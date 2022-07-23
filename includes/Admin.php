<?php
class admin {
	/**
	 * Admin class construct
	 */
	public function __construct() {
		new admin\Menu();
		$this->load_csf();
	}

	/**
	 * Load Codestar Framework and related settings
	 *
	 * @return void
	 */
	public function load_csf() {
		require BBPC_DIR . 'includes/admin/settings/csf/codestar-framework.php';
		require BBPC_DIR . 'includes/admin/settings/options/settings.php';
	}
}
