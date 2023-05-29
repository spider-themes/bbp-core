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
		add_action('wp_enqueue_scripts', [$this, 'frontend_scripts'], 999 );
	}
	
	/**
	 * Register scripts and styles [ FRONTEND ]
	 */
	public function frontend_scripts() {
		
		wp_enqueue_style( 'bbpc-frontend-global', BBPC_ASSETS . 'frontend/css/frontend.css', array(), BBPC_VERSION );

		// Elementor widgets styles
		wp_enqueue_style( 'bbpc-el-widgets', BBPC_ASSETS . 'css/el-widgets.css', );
		wp_enqueue_style( 'bbpc-style', BBPC_ASSETS . 'css/custom.css' );
		
		// Elementor widgets scripts
		wp_enqueue_script( 'bbpc_js',  BBPC_ASSETS . 'js/forumTab.js', ['jquery'], true, true );

		wp_enqueue_script( 'bbpc-frontend-js', BBPC_ASSETS . 'frontend/js/frontend.js', array('jquery'), BBPC_VERSION, true );
		wp_register_script( 'bbpc-ajax', BBPC_ASSETS . 'js/ajax.js', array('jquery'), BBPC_VERSION, true );		
		
		// localize
		wp_localize_script( 'bbpc-frontend-js', 'bbpc_localize_script', array(
			'ajaxurl' 						=> admin_url( 'admin-ajax.php' ),
			'nonce'   						=> wp_create_nonce( 'bbpc-nonce' ),
			'bbpc_subscribed_link' 			=> wp_kses_post(bbp_get_forum_subscription_link()),
			'bbpc_subscribed_forum_title'	=> bbpc_forum_title(),
			'bbpc_subscribed_forum_id'		=> bbp_get_forum_id()
		) );

		$dynamic_css = ":root { --bbpc_brand_color: " . bbpc_get_opt('bbpc_brand_color') . "; }";
		wp_add_inline_style( 'bbpc-frontend-global', $dynamic_css );
	}	
}