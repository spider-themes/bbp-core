<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Feed {
	public $is_feed = false;

	function __construct() {
		add_filter( 'bbp_request', array( $this, 'feed_trap' ), 3 );
	}

	public static function instance() : Feed {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Feed();
		}

		return $instance;
	}

	public function feed_trap( $query_vars = array() ) {
		if ( isset( $query_vars['feed'] ) ) {
			if ( isset( $query_vars['post_type'] ) ) {
				$post_type  = false;
				$post_types = array(
					bbp_get_forum_post_type(),
					bbp_get_topic_post_type(),
					bbp_get_reply_post_type()
				);

				$qv_array = (array) $query_vars['post_type'];

				foreach ( $post_types as $bbp_pt ) {
					if ( in_array( $bbp_pt, $qv_array, true ) ) {
						$post_type = $bbp_pt;
						break;
					}
				}

				if ( ! empty( $post_type ) ) {
					$this->is_feed = true;
				}
			} else if ( isset( $query_vars[ bbp_get_view_rewrite_id() ] ) ) {
				$this->is_feed = true;
			}
		}

		return $query_vars;
	}
}
