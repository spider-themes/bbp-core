<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Private Replies', 'bbp-core' ),
			'fields' => [
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
