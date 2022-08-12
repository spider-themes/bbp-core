<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Admin UI', 'bbp-core' ),
			'fields' => [
				[
					'id'       => 'is_custom_ui',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Enable Custom Admin UI', 'bbp-core' ),
					'subtitle' => __( 'Native Forum, Topics and Replies post types menus will be hidden.', 'bbp-core' ),
				],
			],
		]
	);
