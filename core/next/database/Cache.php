<?php

namespace Dev4Press\Plugin\GDBBX\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cache extends Core {
	public static function instance() : Cache {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Cache();
		}

		return $instance;
	}

	public function tracking_forums_user_tracking( $user_id ) : array {
		$sql = "SELECT t.forum_id, t.topic_id, t.reply_id, t.latest
                FROM " . $this->tracker . " t INNER JOIN (
                SELECT forum_id, MAX(latest) AS latest FROM " . $this->tracker . " 
                WHERE user_id = " . absint( $user_id ) . " GROUP BY forum_id) r 
                ON r.forum_id = t.forum_id AND r.latest = t.latest
                ORDER BY t.forum_id";

		$raw = $this->get_results( $sql, ARRAY_A );

		$list = array();

		foreach ( $raw as $row ) {
			$row['latest']            = strtotime( $row['latest'] );
			$list[ $row['forum_id'] ] = $row;
		}

		return $list;
	}

	public function tracking_forums_activity() : array {
		$post_statuses = array(
			bbp_get_public_status_id(),
			bbp_get_private_status_id(),
			bbp_get_hidden_status_id()
		);

		$sql = "SELECT p.ID AS `id`, m.meta_value AS `active`, l.meta_value AS `posted`
                FROM " . $this->wpdb()->posts . " p 
                LEFT JOIN " . $this->wpdb()->postmeta . " m ON m.post_id = p.ID AND m.meta_key = '_bbp_last_active_time'
                LEFT JOIN " . $this->wpdb()->postmeta . " l ON l.post_id = p.ID AND l.meta_key = '_bbp_last_post_time'
                WHERE p.post_type = '" . bbp_get_forum_post_type() . "' AND p.post_status IN ('" . join( "', '", $post_statuses ) . "') 
                ORDER BY p.ID ASC";
		$raw = $this->get_results( $sql );

		$list = array();

		foreach ( $raw as $row ) {
			$list[ $row->id ] = array(
				'active' => $row->active ? gdbbx_plugin()->datetime()->timestamp_local_to_gmt( strtotime( $row->active ) ) : 0,
				'posted' => $row->posted ? gdbbx_plugin()->datetime()->timestamp_local_to_gmt( strtotime( $row->posted ) ) : 0
			);
		}

		return $list;
	}

	public function get_topic_last_visit( $user_id, $topic_id ) {
		$query = $this->wpdb()->prepare(
			"SELECT forum_id, reply_id, latest FROM " . $this->tracker . " WHERE user_id = %s AND topic_id = %s",
			$user_id, $topic_id );

		$latest = $this->get_row( $query );

		if ( is_null( $latest ) ) {
			return false;
		} else {
			$latest->latest = mysql2date( 'G', $latest->latest );
		}

		return $latest;
	}

	public function reported( $post_id ) : array {
		$sql = $this->wpdb()->prepare(
			"SELECT user_id FROM " . $this->actions . " a INNER JOIN " . $this->actionmeta . " m ON m.action_id = a.action_id
            WHERE a.post_id = %d AND a.action = 'report' AND m.meta_key = 'status' AND m.meta_value = 'waiting'",
			$post_id
		);

		return wp_list_pluck( $this->run( $sql ), 'user_id' );
	}

	public function thanks_list( $post_id ) : array {
		$sql = $this->wpdb()->prepare(
			"SELECT SQL_CALC_FOUND_ROWS user_id, logged FROM " . $this->actions . " WHERE action = 'thanks' AND 
            post_id = %d ORDER BY logged DESC",
			$post_id
		);

		$raw = $this->run( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}

	public function thanks_given( $post_id, $user_id ) : bool {
		$sql = $this->wpdb()->prepare(
			"SELECT COUNT(*) FROM " . $this->actions . " WHERE post_id = %d AND user_id = %d AND action = 'thanks'",
			$post_id, $user_id
		);

		return $this->get_var( $sql ) > 0;
	}

	public function count_all_thanks_given( $user_id ) : int {
		$sql = $this->wpdb()->prepare(
			"SELECT COUNT(*) FROM " . $this->actions . " WHERE action = 'thanks' AND user_id = %d",
			$user_id
		);

		$raw = $this->get_var( $sql );

		return is_numeric( $raw ) ? absint( $raw ) : 0;
	}

	public function count_all_thanks_received( $user_id ) : int {
		$sql = $this->wpdb()->prepare(
			"SELECT COUNT(*) FROM " . $this->wpdb()->posts . " p INNER JOIN " . $this->actions . " a ON 
            a.post_id = p.ID WHERE a.action = 'thanks' AND p.post_author = %d",
			$user_id
		);

		$raw = $this->get_var( $sql );

		return is_numeric( $raw ) ? absint( $raw ) : 0;
	}
}