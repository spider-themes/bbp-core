<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Features', 'bbp-core' ),
			'fields' => [
				[
					'id'       => 'is_solved_topics',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Solved Topics', 'bbp-core' ),
					'subtitle' => __( 'Enable/ Disable Solved Topics feature.', 'bbp-core' ),
				],

				[
					'id'       => 'is_private_replies',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Private Replies', 'bbp-core' ),
					'subtitle' => __( 'Enable/ Disable Private Replies feature.', 'bbp-core' ),
				],
			],
		]
	);
