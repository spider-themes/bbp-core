<?php
namespace admin\Elementor;

class BBP_Widgets{
    public function __construct() {
        // Register Widgets
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

        // Register Category
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );

	    add_action( 'wp_enqueue_scripts', [ $this, 'register_widgets_assets' ] );
	    add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_elementor_editor_assets' ] );
    }

    // Register Widgets
    public function register_widgets( $widgets_manager ) {
        // Include Widget files     
        require_once( __DIR__ . '/Single_forum.php' ); 
        require_once( __DIR__ . '/Forum_Ajax.php' ); 
        require_once( __DIR__ . '/Forum_posts.php' ); 
        require_once( __DIR__ . '/Forums.php' );
        require_once( __DIR__ . '/Forum_Tab.php' ); 
        require_once( __DIR__ . '/Search.php' ); 
        $widgets_manager->register( new Single_forum() );
        $widgets_manager->register( new Forum_Ajax() );       
        $widgets_manager->register( new Forum_posts() );
        $widgets_manager->register( new Forums() );
        $widgets_manager->register( new Forum_Tab() );
        $widgets_manager->register( new Search() );
    }
    
    // Register category
    public function register_category( $elements_manager ) {
        $elements_manager->add_category(
            'bbp-core', [
                'title' => __( 'BBP Core', 'bbp-core' ),
            ]
        );
    }

	//  register bbpc custom css
	function register_widgets_assets() {
		wp_enqueue_style( 'bbpc-style', plugins_url( '/assets/css/custom.css', __FILE__ ) );
		wp_enqueue_style( 'bbpc-el-widgets', plugins_url( '/assets/css/el-widgets.css', __FILE__ ) );
		wp_enqueue_script( 'bbpc_js', plugins_url( '/assets/js/forumTab.js', __FILE__ ) );
	}

	function register_elementor_editor_assets() {
		wp_enqueue_style( 'bbpc-single-widgets_style', plugins_url( '/assets/css/elementor-editor.css', __FILE__ ) );
	}
}