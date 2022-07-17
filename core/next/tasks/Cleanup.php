<?php

namespace SpiderDevs\Plugin\BBPC\Tasks;

use SpiderDevs\Plugin\BBPC\Database\Tasks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cleanup {
	public function __construct() {
	}

	public static function instance() : Cleanup {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Cleanup();
		}

		return $instance;
	}

	public function count_thanks_for_missing_posts() : int {
		return Tasks::instance()->count_thanks_for_missing_posts();
	}

	public function delete_thanks_for_missing_posts() : int {
		return Tasks::instance()->delete_thanks_for_missing_posts();
	}

	public function delete_author_ips_from_postmeta() : int {
		return Tasks::instance()->delete_author_ips_from_postmeta();
	}

	public function clear_user_favorites( $user_id ) {
		$ids = bbp_get_user_favorites_topic_ids( $user_id );

		foreach ( $ids as $id ) {
			bbp_remove_user_favorite( $user_id, $id );
		}
	}

	public function clear_user_topic_subscriptions( $user_id ) {
		$ids = bbp_get_user_subscribed_topic_ids( $user_id );

		foreach ( $ids as $id ) {
			bbp_remove_user_subscription( $user_id, $id );
		}
	}

	public function clear_user_forum_subscriptions( $user_id ) {
		$ids = bbp_get_user_subscribed_forum_ids( $user_id );

		foreach ( $ids as $id ) {
			bbp_remove_user_subscription( $user_id, $id );
		}
	}
}
