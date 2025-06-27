<?php
// Create a section.
$filter_options = [
	'open'     => esc_html__( 'Open', 'bbp-core' ),
	'closed'   => esc_html__( 'Closed', 'bbp-core' ),
	'hidden'   => esc_html__( 'Hidden', 'bbp-core' ),
	'no_reply' => esc_html__( 'No Reply', 'bbp-core' ),
	'solved'   => esc_html__( 'Solved', 'bbp-core' ),
	'unsolved' => esc_html__( 'Unsolved', 'bbp-core' ),
	'all'      => esc_html__( 'All Topics', 'bbp-core' ),
	'trash'    => esc_html__( 'Trash', 'bbp-core' ),
];

$default_options = [
	'.open-topics'   => esc_html__( 'Open', 'bbp-core' ),
	'.closed-topics' => esc_html__( 'Closed', 'bbp-core' ),
	'.hidden-topics' => esc_html__( 'Hidden', 'bbp-core' ),
	'.no-reply'      => esc_html__( 'No Reply', 'bbp-core' ),
	'.solved'        => esc_html__( 'Solved', 'bbp-core' ),
	'.unsolved'      => esc_html__( 'Unsolved', 'bbp-core' ),
	'all'            => esc_html__( 'All Topics', 'bbp-core' )
];

$default_filter_options = [ 'open', 'closed', 'hidden', 'no_reply', 'all', 'trash' ];

CSF::createSection(
	$prefix,
	[
		'title'  => esc_html__( 'Forum Builder', 'bbp-core' ),
		'icon'   => 'dashicons dashicons-dashboard',
		'fields' => [
			[
				'id'         => 'is_bbp_post_types_hidden',
				'type'       => 'switcher',
				'default'    => false,
				'title'      => esc_html__( 'Classic bbPress post types', 'bbp-core' ),
				'subtitle'   => esc_html__( 'Since you can manage the Forums from our unified forums UI, you can hide or disable the classic bbPress menus.', 'bbp-core' ),
				'desc'       => esc_html__( 'Native Forum, Topics and Replies post types menus will be hidden. ', 'bbp-core' ),
				'text_on'    => esc_html__( 'Show', 'bbp-core' ),
				'text_off'   => esc_html__( 'Hide', 'bbp-core' ),
				'text_width' => 85
			],

			[
				'id'          => 'default_filter',
				'type'        => 'select',
				'title'       => esc_html__( 'Choose default filter', 'bbp-core' ),
				'placeholder' => esc_html__( 'Select an option', 'bbp-core' ),
				'options'     => $default_options,
				'default'     => '.open-topics',
			],

			[
				'id'       => 'filter_buttons',
				'type'     => 'button_set',
				'title'    => esc_html__( 'Filters to show on admin page.', 'bbp-core' ),
				'class'    => 'bbpc-filter-btn-settings',
				'multiple' => true,
				'options'  => $filter_options,
				'default'  => $default_filter_options,
			],
		],
	]
);
