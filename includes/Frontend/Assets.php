<?php
namespace Frontend;

/**
 * Class Assets
 * @package EazyDocs\Admin
 */
class Assets {

	/**
	 * Assets constructor.
	 */
	public function __construct() {
		add_action('wp_enqueue_scripts', [$this, 'frontend_scripts'], 999);
	}
	
	/**
	 * Register scripts and styles [ FRONTEND ]
	 */
	public function frontend_scripts() {		
		wp_enqueue_style( 'bbpc-frontend-css', BBPCOREPRO_ASSETS . '/frontend/css/frontend.css', array(), BBPCOREPRO_VERSION );
		wp_enqueue_script( 'bbpc-frontend-js', BBPCOREPRO_ASSETS . '/frontend/js/frontend.js', array( 'jquery' ), BBPCOREPRO_VERSION, true );
		
		// localize
		if ( function_exists('is_bbpress') ){
			wp_localize_script( 'jquery', 'bbpc_localize_script', array(
				'ajaxurl' 						=> admin_url( 'admin-ajax.php' ),
				'nonce'   						=> wp_create_nonce( 'bbpc-nonce' ),
				'bbpc_subscribed_link' 			=> wp_kses_post(bbp_get_forum_subscription_link()),
				'bbpc_subscribed_forum_title'	=> bbpc_forum_title(),
				'bbpc_subscribed_forum_id'		=> bbp_get_forum_id()

			) );
		}
	}	
}