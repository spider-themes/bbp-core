<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

use Dev4Press\Plugin\GDBBX\Features\Icons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Template {
	public function __construct() {
		add_action( 'gdbbx_template_before_topics_loop', array( $this, 'before_topics_loop' ), 10, 2 );
		add_action( 'gdbbx_template_before_replies_loop', array( $this, 'before_replies_loop' ), 10, 2 );
	}

	public static function instance() : Template {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Template();
		}

		return $instance;
	}

	public function before_topics_loop( $posts, $users ) {
		if ( is_user_logged_in() && Icons::instance()->settings['forum_mark_replied'] ) {
			gdbbx_cache()->userreplied_run_bulk_topics( $posts );
		}

		if ( Plugin::instance()->is_enabled( 'private-topics' ) ) {
			$run = bbp_is_single_forum() ? gdbbx_private_topics()->is_enabled_topic_private() : count( $posts ) > 1;

			if ( $run ) {
				gdbbx_cache()->private_run_bulk_posts( $posts );
			}
		}

		if ( Plugin::instance()->is_enabled( 'private-replies' ) ) {
			gdbbx_cache()->private_run_count_topic_replies( $posts );
		}
	}

	public function before_replies_loop( $posts, $users ) {
		if ( Plugin::instance()->is_enabled( 'private-replies' ) && gdbbx_private_replies()->is_enabled_reply_private() ) {
			gdbbx_cache()->private_run_bulk_posts( $posts );
		}
	}
}
