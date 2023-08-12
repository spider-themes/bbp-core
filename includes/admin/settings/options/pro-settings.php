<?php
if ( class_exists( 'CSF' ) ) {

	// Set a unique slug-like ID.
	$prefix = 'bbp_core_settings';

	// Create options.
	CSF::createOptions(
		$prefix,
		[
			'framework_title' => __( 'BBP Core Settings', 'bbp-core-pro' ),
			'framework_class' => 'bbp-core-settings',
			'theme'           => 'dark',

			'menu_title'      => __( 'Settings', 'bbp-core-pro' ),
			'menu_slug'       => 'bbp-core-settings',
			'menu_type'       => 'submenu',
			'menu_parent'     => 'bbp-core',

			// Footer.
			'footer_text'     => '',
			'footer_after'    => '',
			'footer_credit'   => '',
		]
	);

	// Widgets Settings.
	define( 'BBPCPRO_SETTINGS_PATH', plugin_dir_path( __FILE__ ) );

	include BBPCPRO_SETTINGS_PATH . 'pro-options/options_admin_ui.php';
	include BBPCPRO_SETTINGS_PATH . 'pro-options/options_topics.php';
	include BBPCPRO_SETTINGS_PATH . 'pro-options/options_replies.php';
	include BBPCPRO_SETTINGS_PATH . 'pro-options/options_voting.php';
	include BBPCPRO_SETTINGS_PATH . 'pro-options/options_attachments.php';
	include BBPCPRO_SETTINGS_PATH . 'pro-options/options_mini_profile.php';
	include BBPCPRO_SETTINGS_PATH . 'pro-options/options_shortcode.php';
	
	if ( ! class_exists( 'bbPress' ) ) {
		return;
	}
 
}
