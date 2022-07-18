<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class bbPress {
	public $loop_posts = array();
	public $loop_forums = array();
	public $loop_users = array();

	public $theme_package = 'default';

	public function __construct() {
		if ( get_option( '_bbp_theme_package_id' ) == 'quantum' ) {
			$this->theme_package = 'quantum';
		}

		add_action( 'bbp_template_before_replies_loop', array( $this, 'before_replies_loop' ) );
		add_action( 'bbp_template_before_topics_loop', array( $this, 'before_topics_loop' ) );
		add_action( 'bbp_template_before_forums_loop', array( $this, 'before_forums_loop' ) );
		add_action( 'bbp_forum_get_subforums', array( $this, 'forum_get_subforums' ) );

		add_filter( 'bbp_get_template_stack', array( $this, 'template_stack' ) );
	}

	public static function instance() : bbPress {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new bbPress();
		}

		return $instance;
	}

	public function before_replies_loop() {
		foreach ( bbpress()->reply_query->posts as $post ) {
			$this->loop_posts[] = $post->ID;

			if ( $post->post_author > 0 && ! in_array( $post->post_author, $this->loop_users ) ) {
				$this->loop_users[] = $post->post_author;
			}
		}

		do_action( 'gdbbx_template_before_replies_loop', $this->loop_posts, $this->loop_users );
	}

	public function before_topics_loop() {
		foreach ( bbpress()->topic_query->posts as $post ) {
			$this->loop_posts[] = $post->ID;

			if ( $post->post_author > 0 && ! in_array( $post->post_author, $this->loop_users ) ) {
				$this->loop_users[] = $post->post_author;
			}
		}

		do_action( 'gdbbx_template_before_topics_loop', $this->loop_posts, $this->loop_users );
	}

	public function before_forums_loop() {
		foreach ( bbpress()->forum_query->posts as $post ) {
			$this->loop_forums[] = $post->ID;
		}

		$this->loop_forums = array_unique( $this->loop_forums );

		do_action( 'gdbbx_template_before_forums_loop', $this->loop_forums );
	}

	public function forum_get_subforums( $sub ) {
		foreach ( $sub as $post ) {
			$this->loop_forums[] = $post->ID;
		}

		$this->loop_forums = array_unique( $this->loop_forums );

		do_action( 'gdbbx_template_before_subforums_loop', $this->loop_forums );

		return $sub;
	}

	public function template_stack( $stack ) {
		if ( $this->theme_package == 'quantum' ) {
			$stack[] = GDBBX_PATH . 'templates/quantum/bbpress';
		}

		$stack[] = GDBBX_PATH . 'templates/default/bbpress';
		$stack[] = GDBBX_PATH . 'templates/default/widgets';

		return $stack;
	}
}
