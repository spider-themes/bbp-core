<?php
// Control core classes for avoid errors
	$prefix = 'bbpc-options';

	CSF::createOptions( $prefix, array(

		// framework title
		'framework_title'         => 'BBPress Core Plugin Options',

		// menu settings
		'menu_title'              => __('BBP Core', 'bbp-core'),
		'menu_slug'               => 'bbp-core',
		'menu_type'               => 'menu',
		'menu_capability'         => 'manage_options',
		'menu_icon'               => 'dashicons-buddicons-bbpress-logo',
		'show_in_customizer'      => true,

		// admin bar menu settings
		'admin_bar_menu_icon'     => 'dashicons-buddicons-bbpress-logo',

		// theme and wrapper classname
		'theme'                   => 'dark',
		'class'                   => '',

		'footer_text' => '',

		// external default values
		'defaults'                => array(),

	) );

	//
	// Create a section
	CSF::createSection( $prefix, array(
		'title'  => 'Tab Title 1',
		'fields' => array(

			//
			// A text field
			array(
				'id'    => 'opt-text',
				'type'  => 'text',
				'title' => 'Simple Text',
			),

		)
	) );

	//
	// Create a section
	CSF::createSection( $prefix, array(
		'title'  => 'Tab Title 2',
		'fields' => array(

			// A textarea field
			array(
				'id'    => 'opt-textarea',
				'type'  => 'textarea',
				'title' => 'Simple Textarea',
			),

		)
	) );
