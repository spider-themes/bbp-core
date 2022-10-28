<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Solved Topics', 'bbp-core' ),
			'fields' => [
				[
					'id'       => 'is_solved_topics',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Solved Topics', 'bbp-core' ),
					'subtitle' => __( 'Enable/ Disable Solved Topics feature.', 'bbp-core' ),
				],
			],
		]
	);
