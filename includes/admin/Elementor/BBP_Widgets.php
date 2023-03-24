<?php
namespace admin\Elementor;

class BBP_Widgets{
    public function __construct() {
        // Register Widgets
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

        // Register Category
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
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
   
}
