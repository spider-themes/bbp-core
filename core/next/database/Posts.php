<?php

namespace SpiderDevs\Plugin\BBPC\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Posts extends Core {
	public static function instance() : Posts {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Posts();
		}

		return $instance;
	}

	public function get_new_posts( array $args ) : array {
		$post_types = array(
			bbp_get_topic_post_type(),
			bbp_get_reply_post_type()
		);

		$sql = "SELECT p.ID, p.post_type, p.post_author, 
            CAST(t.meta_value as UNSIGNED) as topic, 
       		CAST(m.meta_value as UNSIGNED) as forum
            FROM " . $this->wpdb()->posts . " p 
            INNER JOIN " . $this->wpdb()->postmeta . " m ON p.ID = m.post_id AND m.meta_key = '_bbp_forum_id'
            INNER JOIN " . $this->wpdb()->postmeta . " t ON p.ID = t.post_id AND t.meta_key = '_bbp_topic_id'
            WHERE p.post_type in ('" . join( "', '", $post_types ) . "')";

		if ( ! empty( $args['post_status'] ) ) {
			$sql .= " AND p.post_status IN ('" . join( "', '", $args['post_status'] ) . "')";
		}

		if ( ! empty( $args['include_forums'] ) ) {
			$sql .= " AND CAST(m.meta_value as UNSIGNED) IN (" . join( ", ", $args['include_forums'] ) . ")";
		} else if ( ! empty( $args['exclude_forums'] ) ) {
			$sql .= " AND CAST(m.meta_value as UNSIGNED) NOT IN (" . join( ", ", $args['exclude_forums'] ) . ")";
		}

		if ( $args['timestamp'] > 0 ) {
			$sql .= " AND p.post_date > '" . date( 'Y-m-d H:i:s', $args['timestamp'] ) . "'";
		}

		$sql .= " ORDER BY p.ID DESC LIMIT " . $args['offset'] . ", 1024";

		$raw = $this->run( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}

	public function get_new_replies( array $args ) : array {
		$sql = "SELECT p.ID, p.post_type, p.post_date as post_date, p.post_author, 
            CAST(t.meta_value as UNSIGNED) as topic, 
       		CAST(m.meta_value as UNSIGNED) as forum
            FROM " . bbpc_db()->wpdb()->posts . " p 
            INNER JOIN " . bbpc_db()->wpdb()->postmeta . " m ON p.ID = m.post_id AND m.meta_key = '_bbp_forum_id'
            INNER JOIN " . bbpc_db()->wpdb()->postmeta . " t ON p.ID = t.post_id AND t.meta_key = '_bbp_topic_id'
            WHERE p.post_type = '" . bbp_get_reply_post_type() . "'";

		if ( ! empty( $args['post_status'] ) ) {
			$sql .= " AND p.post_status IN ('" . join( "', '", $args['post_status'] ) . "')";
		}

		if ( ! empty( $args['include_forums'] ) ) {
			$sql .= " AND CAST(m.meta_value as UNSIGNED) IN (" . join( ", ", $args['include_forums'] ) . ")";
		} else if ( ! empty( $args['exclude_forums'] ) ) {
			$sql .= " AND CAST(m.meta_value as UNSIGNED) NOT IN (" . join( ", ", $args['exclude_forums'] ) . ")";
		}

		if ( $args['timestamp'] > 0 ) {
			$sql .= " AND p.post_date > '" . date( 'Y-m-d H:i:s', $args['timestamp'] ) . "'";
		}

		$sql .= " ORDER BY p.ID DESC LIMIT " . $args['offset'] . ", 1024";

		$raw = $this->run( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}

	public function get_new_topics( array $args ) : array {
		$sql = "SELECT p.ID, p.post_date, p.post_author, CAST(m.meta_value as UNSIGNED) as forum
            FROM " . bbpc_db()->wpdb()->posts . " p 
            INNER JOIN " . bbpc_db()->wpdb()->postmeta . " m ON p.ID = m.post_id AND m.meta_key = '_bbp_forum_id'
            WHERE p.post_type = '" . bbp_get_topic_post_type() . "'";

		if ( ! empty( $args['post_status'] ) ) {
			$sql .= " AND p.post_status IN ('" . join( "', '", $args['post_status'] ) . "')";
		}

		if ( ! empty( $args['include_forums'] ) ) {
			$sql .= " AND CAST(m.meta_value as UNSIGNED) IN (" . join( ", ", $args['include_forums'] ) . ")";
		} else if ( ! empty( $args['exclude_forums'] ) ) {
			$sql .= " AND CAST(m.meta_value as UNSIGNED) NOT IN (" . join( ", ", $args['exclude_forums'] ) . ")";
		}

		if ( $args['timestamp'] > 0 ) {
			$sql .= " AND p.post_date > '" . date( 'Y-m-d H:i:s', $args['timestamp'] ) . "'";
		}

		$sql .= " ORDER BY p.ID DESC LIMIT " . $args['offset'] . ", 1024";

		$raw = $this->run( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}
}
