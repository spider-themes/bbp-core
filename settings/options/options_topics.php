<?php
CSF::createSection(
	$prefix,
	[
		'title'  => __( 'Topics', 'bbp-core' ),
		'fields' => [
			[
				'type'    => 'subheading',
				'content' => __( 'User read status tracking.', 'bbp-core' ),
			],

			[
				'id'       => 'user-activity-tracking',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'User Activity Tracking', 'bbp-core' ),
				'subtitle' => __( 'Every time user opens any forum, topic or reply page plugin will save activity timestamp.', 'bbp-core' ),
			],

			[
				'id'       => 'use-cutoff-timestamp',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Use cutoff timestamp', 'bbp-core' ),
				'subtitle' => __( 'Tracking data begins storing when plugin version 4.5 is installed. This moment will be stored to serve as cutoff for displaying unread topics to users. If this is not used, all old topics will be initially marked as \'unread\' to all users.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'New Replies.', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'If one or more new replies are added to the topic since the last time user visited a topic, this topic will be marked and link placed to lead to the first new reply for the current user.', 'bbp-core' ),
			],

			[
				'id'       => 'add-new-rely-badge',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Add new reply badge', 'bbp-core' ),
				'subtitle' => __( 'Add badge before the topic title.', 'bbp-core' ),
			],

			[
				'id'       => 'add-new-rely-icon',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Add new replies icon', 'bbp-core' ),
				'subtitle' => __( 'Add icon and link to the first new reply in the topic.', 'bbp-core' ),
			],

			[
				'id'       => 'wrap-new-topics-strong',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Wrap title in strong tag', 'bbp-core' ),
				'subtitle' => __( 'Wrap the topic title in the STRONG to attempt display it as bold to stand out in the list.', 'bbp-core' ),
			],

			[
				'id'       => 'replies-in-thread',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Mark replies in topic thread', 'bbp-core' ),
				'subtitle' => __( 'When topic is opened, all new replies will get a \'new reply\' badge.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'New Topics', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'If the new topic is posted since the last user visit, they will be marked. For this to work, you need to enable user activity tracking.', 'bbp-core' ),
			],

			[
				'id'       => 'replies-in-thread',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Add new topic badge', 'bbp-core' ),
				'subtitle' => __( 'Add badge before the topic title.', 'bbp-core' ),
			],

			[
				'id'       => 'new-topic-strong-wrap',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Wrap title in strong tag', 'bbp-core' ),
				'subtitle' => __( 'Wrap the topic title in the STRONG to attempt display it as bold to stand out in the list.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Unread Topics', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'If the topic is not read by the user (taking into account the cutoff timestamp), it will be marked as unread.', 'bbp-core' ),
			],

            [
				'id'       => 'unread-topic-badge',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Add unread topic badge', 'bbp-core' ),
				'subtitle' => __( 'Add badge before the topic title.', 'bbp-core' ),
			],

            [
				'id'       => 'unread-topic-badge',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Add unread topic badge', 'bbp-core' ),
				'subtitle' => __( 'Add badge before the topic title.', 'bbp-core' ),
			],

            [
				'id'       => 'unread-topic-badge',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Add unread topic badge', 'bbp-core' ),
				'subtitle' => __( 'Add badge before the topic title.', 'bbp-core' ),
			],

            [
				'id'       => 'unread-topic-strong-wrap',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Wrap title in strong tag', 'bbp-core' ),
				'subtitle' => __( 'Wrap the topic title in the STRONG to attempt display it as bold to stand out in the list.', 'bbp-core' ),
			],           
		],
	]
);

