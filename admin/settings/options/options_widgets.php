<?php
	// Create a section.
	CSF::createSection(
		$prefix,
		[
			'title'  => __( 'Widgets', 'bbp-core' ),
			'fields' => [
				[
					'type'    => 'subheading',
					'content' => __( 'Plugin Widgets', 'bbp-core' ),
				],

				[
					'id'       => 'user-profile',
					'type'     => 'switcher',
					'title'    => __( 'User Profile', 'bbp-core' ),
					'default'  => 1,
					'subtitle' => __( 'Logged in user profile with useful links and stats.', 'bbp-core' ),
				],

				[
					'id'       => 'top-thanked-users',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Top Thanked Users', 'bbp-core' ),
					'subtitle' => __( 'Logged in user profile with useful links and stats.', 'bbp-core' ),
				],

				[
					'id'       => 'statistics',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Statistics', 'bbp-core' ),
					'subtitle' => __( 'Enhanced list of important forum statistics.', 'bbp-core' ),
				],

				[
					'id'       => 'topic-information',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Topic Information', 'bbp-core' ),
					'subtitle' => __( 'Show information about the topic currently displayed.', 'bbp-core' ),
				],

				[
					'id'       => 'forum-information',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Forum Information', 'bbp-core' ),
					'subtitle' => __( 'Show information about the topic currently displayed.', 'bbp-core' ),
				],

				[
					'id'       => 'search',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Search', 'bbp-core' ),
					'subtitle' => __( 'Expanded search widget with option to search current forum only.', 'bbp-core' ),
				],

				[
					'id'       => 'online-users',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Online Users', 'bbp-core' ),
					'subtitle' => __( 'Show the list of users currently online.', 'bbp-core' ),
				],
				[
					'id'       => 'new-post-list',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'New posts List', 'bbp-core' ),
					'subtitle' => __( 'List of new topics or topics with new replies.', 'bbp-core' ),
				],

				[
					'id'       => 'topic-views-list',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Topics Views List', 'bbp-core' ),
					'subtitle' => __( 'Selectable list of topics views.', 'bbp-core' ),
				],

				[
					'type'    => 'subheading',
					'content' => __( 'Default bbPress Widgets', 'bbp-core' ),
				],

				[
					'id'       => 'recent-topics',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Recent Topics', 'bbp-core' ),
					'subtitle' => __( 'If you use this plugin \'New Posts List\' widget, you can disable default one.', 'bbp-core' ),
				],

				[
					'id'       => 'recent-replies',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Recent Replies', 'bbp-core' ),
					'subtitle' => __( 'If you use this plugin \'New Posts List\' widget, you can disable default one.', 'bbp-core' ),
				],

				[
					'id'       => 'topic-views-list',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Topic Views List', 'bbp-core' ),
					'subtitle' => __( 'If you use this plugin \'Topics Views List\' widget, you can disable default one.', 'bbp-core' ),
				],

				[
					'id'       => 'topic-views-list',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Topic Views List', 'bbp-core' ),
					'subtitle' => __( 'If you use this plugin \'Topics Views List\' widget, you can disable default one.', 'bbp-core' ),
				],

				[
					'id'       => 'search-default',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Search', 'bbp-core' ),
					'subtitle' => __( 'If you use this plugin \'Search\' widget, you can disable default one.', 'bbp-core' ),
				],

				[
					'id'       => 'statistics-default',
					'type'     => 'switcher',
					'default'  => 1,
					'title'    => __( 'Statistics', 'bbp-core' ),
					'subtitle' => __( 'If you use this plugin \'Statistics\' widget, you can disable default one.', 'bbp-core' ),
				],
			],
		]
	);
