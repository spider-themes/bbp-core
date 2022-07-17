<?php

namespace SpiderDevs\Plugin\BBPC\Tasks;

use SpiderDevs\Plugin\BBPC\Database\Tasks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Recalculations {
	public function __construct() {
	}

	public static function instance() : Recalculations {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Recalculations();
		}

		return $instance;
	}

	public function sub_forums_counts() {
		$raw = Tasks::instance()->get_sub_forums_count();

		foreach ( $raw as $row ) {
			$forum_id  = absint( $row->forum_id );
			$subforums = absint( $row->subforums );

			update_post_meta( $forum_id, '_bbp_forum_subforum_count', $subforums );
		}
	}
}