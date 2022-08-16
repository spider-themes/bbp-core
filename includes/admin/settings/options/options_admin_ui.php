<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Admin UI', 'bbp-core' ),
			'fields' => [
				[
					'id'       => 'is_bbp_post_types_hidden',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Hide bbPress post types', 'bbp-core' ),
					'subtitle' => __( 'Native Forum, Topics and Replies post types menus will be hidden.', 'bbp-core' ),
				],
			],
		]
	);
