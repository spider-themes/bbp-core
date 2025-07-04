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
		add_action('elementor/widgets/widgets_registered', [$this, 'bbpc_elementor_script'], 999 );
	}
	
	/**
	 * Register scripts and styles [ ELEMENTOR ]
	*/
	public function bbpc_elementor_script(){
		// Elementor widgets scripts
		wp_enqueue_style( 'bbpc-el-widgets', BBPC_FRONT_ASS . 'css/el-widgets.css' );
		wp_enqueue_style( 'elegant-icon', BBPC_VEND . 'elegant-icon/style.css' );
		wp_register_script( 'bbpc_js',  BBPC_FRONT_ASS . 'js/forumTab.js', ['jquery'], true, true );
		wp_register_script( 'bbpc-frontend-js', BBPC_FRONT_ASS . 'js/frontend.js', array('jquery'), BBPC_VERSION, true );
		wp_register_script( 'bbpc-ajax', BBPC_FRONT_ASS . 'js/ajax.js', array('jquery'), BBPC_VERSION, true );

		if ( is_rtl() ) {
			wp_enqueue_style( 'bbpc-rtl', BBPC_FRONT_ASS . 'css/bbpc-main-rtl.css' );
		}
	}
	
	/**
	 * Register scripts and styles [ FRONTEND ]
	 */
	public function frontend_scripts() {

		if ( ! class_exists( 'bbPress' ) ) {
			return;
		}

		wp_register_script( 'bbpc-wp-widget', BBPC_ASSETS . 'frontend/js/wp-widgets.js', array('jquery'), BBPC_VERSION, true );	
		
		// localize script
		wp_localize_script( 'jquery', 'bbpc_localize_script', array(
			'ajaxurl' 						=> admin_url( 'admin-ajax.php' ),
			'nonce'   						=> wp_create_nonce( 'bbpc-nonce' ),
			'bbpc_subscribed_link' 			=> wp_kses_post(bbp_get_forum_subscription_link()),
			'bbpc_subscribed_forum_title'	=> bbpc_forum_title(),
			'bbpc_subscribed_forum_id'		=> bbp_get_forum_id()
		) );

		$dynamic_css = ":root { --bbpc_brand_color: " . bbpc_get_opt('bbpc_brand_color') . "; }";
		wp_add_inline_style( 'bbpc_localize_script', $dynamic_css );

		wp_register_script( 'bbpc-voting', BBPC_FRONT_ASS . 'voting/bbpc-voting.js', [ 'jquery' ], BBPC_VERSION );
		wp_register_style( 'bbpc', BBPC_FRONT_ASS . 'css/bbpc.css' );
		
		if ( class_exists( 'bbPress' ) && bbpc_forum_and_topic_page() ) {
			wp_enqueue_style( 'bbpc' );
			wp_enqueue_style( 'bbpc-voting', BBPC_FRONT_ASS . 'voting/bbpc-voting.css' );
			wp_enqueue_script( 'bbpc-voting' );
		}
	}
}