<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BB {
	public function __construct() {

	}

	public static function i() : BB {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new BB();
		}

		return $instance;
	}

	public static function is() : BB {
		return BB::i();
	}

	public function get_forum_id() : int {
		$forum_id = bbp_get_forum_id();

		if ( $forum_id == 0 ) {
			if ( bbp_is_topic_edit() ) {
				$topic_id = bbp_get_topic_id();
				$forum_id = bbp_get_topic_forum_id( $topic_id );
			} else if ( bbp_is_reply_edit() ) {
				$reply_id = bbp_get_reply_id();
				$forum_id = bbp_get_reply_forum_id( $reply_id );
			}
		}

		return $forum_id;
	}

	public function get_mime_types_list() : array {
		$list = get_allowed_mime_types();

		$show = array();

		foreach ( $list as $mime => $type ) {
			$show[ $mime ] = '<span title="' . $type . '">' . $mime . '</span>';
		}

		return $show;
	}

	public function is_bbpress_post_type( $post_type ) : bool {
		if (
			in_array( $post_type, array(
				bbp_get_forum_post_type(),
				bbp_get_topic_post_type(),
				bbp_get_reply_post_type()
			) ) ) {
			return true;
		} else {
			return false;
		}
	}
}