<?php
CSF::createSection(
	$prefix,
	[
		'title'  => __( 'User Tracking', 'bbp-core' ),
		'fields' => [
			[
				'type'    => 'subheading',
				'content' => __( 'User activity tracking', 'bbp-core' ),
			],

			[
				'id'       => 'user-activity-tracking',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'User Activity Tracking', 'bbp-core' ),
				'subtitle' => __( 'Every time user opens any forum, topic or reply page plugin will save activity timestamp.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Advanced user tracking', 'bbp-core' ),
			],

			[
				'id'       => 'advanced-user-tracking',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'User Activity Tracking', 'bbp-core' ),
				'subtitle' => __( 'This is advanced tracking that covers tracking of read status for topics and forums, and read statuses. This type of tracking depends on the cookies, and you need to configure activity and session cookies expiration too.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Cookies expiration', 'bbp-core' ),
			],

			[
				'id'       => 'activity-tracking-cookie',
				'type'     => 'number',
				'title'    => __( 'Activity tracking cookie', 'bbp-core' ),
				'subtitle' => __( 'Value is in days', 'bbp-core' ),
				'default'  => 365,
			],

			[
				'id'       => 'current-session-cookie',
				'type'     => 'number',
				'title'    => __( 'Current session cookie', 'bbp-core' ),
				'subtitle' => __( 'Value is in minutes.', 'bbp-core' ),
				'default'  => 60,
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Track online status for users and guests', 'bbp-core' ),
			],

			[
				'id'      => 'track-online-users-guests',
				'type'    => 'switcher',
				'default' => 1,
				'title'   => __( 'Track online status for users and guests', 'bbp-core' ),
			],

			[
				'id'       => 'track-users',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Track Users', 'bbp-core' ),
				'subtitle' => __( 'If enabled, plugin will track online status logged in users.', 'bbp-core' ),
			],

			[
				'id'       => 'track-guests',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Track Guests', 'bbp-core' ),
				'subtitle' => __( 'If enabled, plugin will track online status for guests - users that are not logged in. This type of tracking depends on the special tracking cookie used for visitors only.', 'bbp-core' ),
			],

			[
				'id'       => 'online-period',
				'type'     => 'number',
				'title'    => __( 'Online Period', 'bbp-core' ),
				'subtitle' => __( 'Value is in seconds.', 'bbp-core' ),
				'default'  => 180,
			],

			[
				'type'    => 'subheading',
				'style'   => 'info',
				'content' => __( 'Notices with online counts', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'Notices are displayed on top of the page, and they will show number of users and guests currently viewing the forum, topic, profile or view.', 'bbp-core' ),
			],

			[
				'id'    => 'notice-forums',
				'type'  => 'switcher',
				'title' => __( 'For Forums', 'bbp-core' ),
			],

			[
				'id'    => 'notice-topics',
				'type'  => 'switcher',
				'title' => __( 'For Forums', 'bbp-core' ),
			],

			[
				'id'    => 'notice-topic-views',
				'type'  => 'switcher',
				'title' => __( 'For Forums', 'bbp-core' ),
			],

			[
				'id'    => 'notice-user-profiles',
				'type'  => 'switcher',
				'title' => __( 'For User Profiles', 'bbp-core' ),
			],
		],
	]
);
