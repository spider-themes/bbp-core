<?php

namespace SpiderDevs\Plugin\BBPC\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tasks extends Core {
	public static function instance() : Tasks {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Tasks();
		}

		return $instance;
	}

	public function count_thanks_for_missing_posts() : int {
		$sql = "SELECT COUNT(*) FROM " . $this->actions . " WHERE `action` = 'thanks' AND post_id NOT IN (SELECT ID FROM " . $this->wpdb()->posts . ")";

		$raw = $this->get_var( $sql );

		return is_numeric( $raw ) ? absint( $raw ) : 0;
	}

	public function delete_thanks_for_missing_posts() : int {
		$sql = "DELETE FROM " . $this->actions . " WHERE `action` = 'thanks' AND post_id NOT IN (SELECT ID FROM " . $this->wpdb()->posts . ")";

		$this->query( $sql );

		return $this->rows_affected();
	}

	public function delete_author_ips_from_postmeta() : int {
		$sql = "DELETE FROM " . $this->wpdb()->postmeta . " WHERE `meta_key` = '_bbp_author_ip'";

		$this->query( $sql );

		return $this->rows_affected();
	}

	public function get_sub_forums_count() : array {
		$sql = $this->prepare( "SELECT post_parent AS forum_id, COUNT(*) AS subforums FROM " . $this->wpdb()->posts . " 
		WHERE post_type = %s AND post_status IN ('private', 'hidden', 'publish') AND post_parent > 0
		GROUP BY post_parent ORDER BY post_parent ASC", bbp_get_forum_post_type() );

		$raw = $this->get_results( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}
}
