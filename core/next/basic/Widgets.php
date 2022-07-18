<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widgets {
	public $widgets = array(
		'foruminfo'   => 'ForumInfo',
		'topicinfo'   => 'TopicInfo',
		'newposts'    => 'NewPosts',
		'search'      => 'Search',
		'onlineusers' => 'OnlineUsers',
		'statistics'  => 'Statistics',
		'topicsviews' => 'TopicsViews',
		'userprofile' => 'UserProfile',
		'usersthanks' => 'UsersThanks'
	);

	public function __construct() {
		add_action( 'gdbbx_init', array( $this, 'disable' ) );
		add_action( 'widgets_init', array( $this, 'init' ) );
	}

	public static function instance() : Widgets {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Widgets();
		}

		return $instance;
	}

	public function disable() {
		if ( gdbbx()->get( 'default_disable_recenttopics', 'widgets' ) ) {
			remove_action( 'bbp_widgets_init', array( 'BBP_Topics_Widget', 'register_widget' ) );
		}

		if ( gdbbx()->get( 'default_disable_recentreplies', 'widgets' ) ) {
			remove_action( 'bbp_widgets_init', array( 'BBP_Replies_Widget', 'register_widget' ) );
		}

		if ( gdbbx()->get( 'default_disable_topicviewslist', 'widgets' ) ) {
			remove_action( 'bbp_widgets_init', array( 'BBP_Views_Widget', 'register_widget' ) );
		}

		if ( gdbbx()->get( 'default_disable_login', 'widgets' ) ) {
			remove_action( 'bbp_widgets_init', array( 'BBP_Login_Widget', 'register_widget' ) );
		}

		if ( gdbbx()->get( 'default_disable_search', 'widgets' ) ) {
			remove_action( 'bbp_widgets_init', array( 'BBP_Search_Widget', 'register_widget' ) );
		}

		if ( gdbbx()->get( 'default_disable_stats', 'widgets' ) ) {
			remove_action( 'bbp_widgets_init', array( 'BBP_Stats_Widget', 'register_widget' ) );
		}
	}

	public function init() {
		foreach ( $this->widgets as $widget => $class ) {
			$class = 'Dev4Press\\Plugin\\GDBBX\\Widget\\' . $class;

			if ( class_exists( $class ) ) {
				register_widget( $class );
			}
		}
	}
}