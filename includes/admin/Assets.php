<?php
namespace admin;

/**
 * Class Assets
 * @package EazyDocs\Admin
 * @package BBPCorePro\Admin
 */
class Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts'], 999);
	}
	
	public function admin_scripts() {		
		wp_enqueue_style( 'bbpc-admin-css', BBPC_ASSETS . '/admin/css/admin.css', array(), BBPC_VERSION );
		wp_enqueue_script( 'bbpc-admin-js', BBPC_ASSETS . '/admin/js/admin.js', array( 'jquery' ), BBPC_VERSION, true );
	}	
}