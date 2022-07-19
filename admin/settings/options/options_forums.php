<?php
CSF::createSection(
	$prefix,
	[
		'title'  => __( 'Forums', 'bbp-core' ),
		'fields' => [
			[
				'type'    => 'subheading',
				'content' => __( 'New Posts', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'If the new topic or reply is posted since the last user visit, forum this topic belongs to, will be marked. For this to work, you need to enable user activity tracking.', 'bbp-core' ),
			],

			[
				'id'       => 'forum-post-badge',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Add new posts badge', 'bbp-core' ),
				'subtitle' => __( 'Add badge before the forum title.', 'bbp-core' ),
			],

			[
				'id'       => 'forum-post-tile-wrap',
				'type'     => 'switcher',
				'title'    => __( 'Wrap title in strong tag', 'bbp-core' ),
				'subtitle' => __( 'Wrap the forum title in the STRONG to attempt display it as bold to stand out in the list.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Unread Forum', 'bbp-core' ),
			],
		
			[
				'type'    => 'content',
				'content' => __( 'If the forum is not read by the user (taking into account the cutoff timestamp), forum will be marked as unread.', 'bbp-core' ),
			],

			[
				'id'       => 'unread-forum-badge',
				'type'     => 'switcher',
				'title'    => __( 'Add unread forum badge', 'bbp-core' ),
				'subtitle' => __( 'Add badge before the forum title.', 'bbp-core' ),
			],

			[
				'id'       => 'unread-forum-tile-wrap',
				'type'     => 'switcher',
				'title'    => __( 'Wrap title in strong tag', 'bbp-core' ),
				'subtitle' => __( 'Wrap the forum title in the STRONG to attempt display it as bold to stand out in the list.', 'bbp-core' ),
			],
		],
	]
);

