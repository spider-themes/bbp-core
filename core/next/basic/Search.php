<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Search {
	public function __construct() {
		add_filter( 'bbp_get_search_results_url', array( $this, 'search_url' ) );
		add_filter( 'bbp_after_has_search_results_parse_args', array( $this, 'search_args' ) );
		add_filter( 'bbp_get_search_title', array( $this, 'search_title' ), 10, 2 );
	}

	public static function instance() : Search {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Search();
		}

		return $instance;
	}

	public function search_url( $url ) {
		if ( isset( $_GET['bbx-mode'] ) && $_GET['bbx-mode'] == 'current' && absint( $_GET['bbx-forum'] ) > 0 ) {
			$url = add_query_arg( 'bbx-forum', absint( $_GET['bbx-forum'] ), $url );
		}

		return $url;
	}

	public function search_args( $args ) {
		$forum = isset( $_GET['bbx-forum'] ) ? absint( $_GET['bbx-forum'] ) : 0;

		if ( $forum > 0 && bbp_is_forum( $forum ) ) {
			$args['post_type'] = array( bbp_get_topic_post_type(), bbp_get_reply_post_type() );

			$args['meta_query'] = array(
				array(
					'key'   => '_bbp_forum_id',
					'value' => $forum,
					'type'  => 'UNSIGNED'
				)
			);
		}

		return $args;
	}

	public function search_title( $title, $search_terms ) {
		$forum = isset( $_GET['bbx-forum'] ) ? absint( $_GET['bbx-forum'] ) : 0;

		if ( $forum > 0 && bbp_is_forum( $forum ) && ! empty( $search_terms ) ) {
			$forum_title = bbp_get_forum_title( $forum );

			$title = sprintf( esc_html__( "Search Results for '%s' in '%s' forum", "bbp-core" ), esc_attr( $search_terms ), $forum_title );
		}

		return $title;
	}
}
