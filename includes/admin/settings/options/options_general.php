<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'General', 'bbp-core' ),
			'fields' => [			
				[
					'id'          => 'bbpc_brand_color',
					'type'        => 'color',
					'title'       => esc_html__( 'Frontend Brand Color', 'bbp-core' ),
					'default'     => '#4c4cf1',
					'output'      => ':root',
					'output_mode' => '--bbpc_brand_color',
				]
			]
		]
	);
