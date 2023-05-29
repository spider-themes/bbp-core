<?php
namespace admin;

/**
 * Class Assets
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
		wp_enqueue_script( 'bbpc-notify-review', BBPC_ASSETS . '/admin/js/review.js', array( 'jquery' ), BBPC_VERSION, true );

        // Localize the script with new data
        $ajax_url = admin_url('admin-ajax.php');
        $wpml_current_language = apply_filters('wpml_current_language', null);
        if ( !empty($wpml_current_language) ) {
            $ajax_url = add_query_arg('wpml_lang', $wpml_current_language, $ajax_url);
        }

        wp_localize_script( 'jquery', 'bbpc_local_object',
            array(
                'ajaxurl' 					=> $ajax_url,
                'nonce' 					=> wp_create_nonce('bbpc-admin-nonce')               
            )
        );
	}	
}