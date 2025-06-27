<?php
// Require widget files
require plugin_dir_path(__FILE__) . '/forum-info/Forum_Information.php';
require plugin_dir_path(__FILE__) . '/Forum_Topic_Info.php';;


// Register Widgets
add_action( 'widgets_init', function() {
    register_widget( 'BBPCore\WpWidgets\Forum_Information');
    register_widget( 'BBPCore\WpWidgets\Forum_Topic_Info');

    add_action('admin_enqueue_scripts', function($hook){
        if ( $hook === 'widgets.php' ) {
            wp_enqueue_style( 'bbp-core-wp-widget', BBPC_ASSETS . 'admin/css/wp-widget.css' );
        }
    });
});