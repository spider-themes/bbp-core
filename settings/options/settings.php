<?php

use Elementor\Settings_Page;

if ( class_exists( 'CSF' ) ) {

	// Set a unique slug-like ID.
	$prefix = 'bbp_core_settings';

	// Create options.
	CSF::createOptions(
		$prefix,
		[
			'framework_title' => __( 'BBP Core Settings', 'bbp-core' ),
			'framework_class' => 'bbp-core-settings',
			'theme'           => 'dark',

			'menu_title'      => 'BBP Core Settings',
			'menu_slug'       => 'bbp-core-settings',
			'menu_type'       => 'submenu',
			'menu_parent'     => 'gd-bbpress-toolbox-front', // TODO: Might need changing soon

			// Footer.
			'footer_text'     => '',
			'footer_after'    => '',
			'footer_credit'   => '',
		]
	);

	// Widgets Settings.
	define( 'SETTINGS_PATH', plugin_dir_path( __FILE__ ) );

	include SETTINGS_PATH . 'options_widgets.php';
	include SETTINGS_PATH . 'options_js_css_files.php';
	include SETTINGS_PATH . 'options_user_tracking.php';
	include SETTINGS_PATH . 'options_topics.php';
	include SETTINGS_PATH . 'options_forums.php';

    //TODO: Move all individual features to bbp core plugin


}

