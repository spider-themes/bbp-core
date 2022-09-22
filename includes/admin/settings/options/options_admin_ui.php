<?php
	// Create a section.
	$filter_options = [
		'open'     => 'Open',
		'closed'   => 'Closed',
		'hidden'   => 'Hidden',
		'no_reply' => 'No Reply',
		'solved'   => 'Solved',
		'unsolved' => 'Unsolved',
		'all'      => 'All Topics',
		'trash'      => 'Trash',
	];

	$default_options = [
		'.open-topics'     => 'Open',
		'.closed-topics'   => 'Closed',
		'.hidden-topics'   => 'Hidden',
		'.no-reply' => 'No Reply',
		'.solved'   => 'Solved',
		'.unsolved' => 'Unsolved',
		'all'      => 'All Topics'
	];

	$default_filter_options = [ 'open', 'closed', 'hidden', 'no_reply', 'solved', 'unsolved', 'all', 'trash' ];

	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Admin UI', 'bbp-core' ),
			'fields' => [
				[
					'id'       => 'is_bbp_post_types_hidden',
					'type'     => 'switcher',
					'default'  => false,
					'title'    => __( 'Hide bbPress post types', 'bbp-core' ),
					'subtitle' => __( 'Native Forum, Topics and Replies post types menus will be hidden.', 'bbp-core' ),
				],

				[
					'id'          => 'default_filter',
					'type'        => 'select',
					'title'       => __( 'Choose default filter', 'bbp-core' ),
					'placeholder' => 'Select an option',
					'options'     => $default_options,
					'default'     => '.open-topics',
				],

				[
					'id'       => 'filter_buttons',
					'type'     => 'button_set',
					'title'    => __( 'Filters to show on admin page.', 'bbp-core' ),
					'class'    => 'bbpc-filter-btn-settings',
					'multiple' => true,
					'options'  => $filter_options,
					'default'  => $default_filter_options,
				],
			],
		]
	);
