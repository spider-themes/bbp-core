<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Topics', 'bbp-core' ),
			'fields' => [
				[
					'id'       => 'is_solved_topics',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Solved Topics', 'bbp-core' ),
					'subtitle' => __( 'Enable/ Disable Solved Topics feature.', 'bbp-core' ),
				],
				[
					'id'       => 'is_auto_approval_topics',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Auto Approval', 'bbp-core' ),
					'subtitle' => __( 'Enable/ Disable Auto Approval feature.', 'bbp-core' ),
				],
				[
					'id'       	 => 'topic_pending_notice',
					'type'     	 => 'text',
					'title'    	 => __( 'Pending Notice', 'bbp-core' ),
					'default' 	 => __( 'Your topic is awaiting for moderation.', 'bbp-core' ),					
					'dependency' => [ 'is_auto_approval_topics', '==', 0, ]
				],
				[
					'id'       	 => 'anonymous_topic',
					'type'     	 => 'switcher',
					'title'    	 => __( 'Anonymous Topic', 'bbp-core' ),
					'subtitle' 	 => __( 'Allow anonymous to create topics.', 'bbp-core' ),		
				],
				[
					'id'       	 => 'anonymous_topic_label',
					'type'     	 => 'text',
					'title'    	 => __( 'Anonymous Label', 'bbp-core' ),
					'default' 	 => __( 'Post Anonymously', 'bbp-core' ),
					'dependency' => [ 'anonymous_topic', '==', 'true', ]	
				]
			]
		]
	);