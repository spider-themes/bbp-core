<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Topic Replies', 'bbp-core' ),
			'icon'   => 'dashicons dashicons-format-chat',
			'fields' => [
				[
					'id'       => 'is_bbpc_insert_media',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'BBPC Insert Media', 'bbp-core' ),
					'subtitle' => __( 'Enable/Disable the custom image insert button with upload & URL modal for bbPress editors.', 'bbp-core' ),
					'class'   	 => 'st-pro-notice'
				],				
				[
					'id'       => 'is_auto_approval_replies',
					'type'     => 'switcher',
					'default'  => true,
					'title'    => __( 'Auto Approval', 'bbp-core' ),
					'subtitle' => __( 'Enable/ Disable Auto Approval feature.', 'bbp-core' ),
					'class'    => 'st-pro-notice'
				],
				[
					'id'         => 'is_attachment_replies',
					'type'       => 'button_set',
					'title'    	 => __( 'Replies with Attachments', 'bbp-core' ),
					'subtitle' 	 => __( 'Approve or Unapprove if attachment is added with the reply', 'bbp-core' ),
					'class'    	 => 'st-pro-notice',
					'options'    => array(
						'1'   	 => 'Approve',
						'0' 	 => 'Unapprove',
					),
					'default'    => '1',
					'dependency' => [ 'is_auto_approval_replies', '==', 'true', ],
				],
				[
					'id'       => 'is_private_replies',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Private Replies', 'bbp-core' ),
					'subtitle' => __( 'Enable/ Disable Private Replies feature.', 'bbp-core' ),
				],
				[
					'id'       	 => 'anonymous_reply',
					'type'     	 => 'switcher',
					'title'    	 => __( 'Anonymous Reply', 'bbp-core' ),
					'subtitle' 	 => __( 'Allow anonymous to reply a topic.', 'bbp-core' ),			
					'class'   	 => 'st-pro-notice'
				],
				[
					'id'       	 => 'anonymous_reply_label',
					'type'     	 => 'text',
					'title'    	 => __( 'Anonymous Label', 'bbp-core' ),
					'default' 	 => __( 'Post Anonymously', 'bbp-core' ),
					'dependency' => [ 'anonymous_reply', '==', 'true', ],	
					'class'   	 => 'st-pro-notice'
				]
			],
		]
	);