<?php
// Require widget files
require plugin_dir_path(__FILE__) . 'Forum_Information.php';

// Register Widgets
add_action( 'widgets_init', function() {
    register_widget( 'BBPCorePro\WpWidgets\Forum_Information');
});