<?php

namespace SpiderDevs\Plugin\BBPC\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bulk extends Core {
	public static function instance() : Bulk {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Bulk();
		}

		return $instance;
	}

	public function thanks_list( $posts ) : array {
		if ( empty( $posts ) ) {
			return array();
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS post_id, user_id FROM " . $this->actions . " WHERE action = 'thanks' AND 
            post_id IN (" . join( ',', (array) $posts ) . ") ORDER BY post_id ASC, logged DESC";

		$raw     = $this->run( $sql );
		$results = array();

		foreach ( $raw as $row ) {
			if ( ! isset( $results[ $row->post_id ] ) ) {
				$results[ $row->post_id ] = array();
			}

			$results[ $row->post_id ][] = $row->user_id;
		}

		return $results;
	}

	public function thanks_given( $posts, $user_id ) : array {
		if ( empty( $posts ) || empty( $user_id ) ) {
			return array();
		}

		$sql = $this->wpdb()->prepare(
			"SELECT post_id FROM " . $this->actions . " WHERE post_id IN (" . join( ', ', $posts ) . ") AND 
             user_id = %d AND action = 'thanks'",
			$user_id
		);

		$raw = $this->get_results( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}

	public function count_all_thanks_given( $users ) : array {
		if ( empty( $users ) ) {
			return array();
		}

		$sql = "SELECT user_id, COUNT(*) as thanks FROM " . $this->actions . " WHERE action = 'thanks' AND 
            user_id IN (" . join( ', ', (array) $users ) . ") GROUP BY user_id";

		$raw = $this->get_results( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}

	public function count_all_thanks_received( $users ) : array {
		if ( empty( $users ) ) {
			return array();
		}

		$sql = "SELECT p.post_author as user_id, COUNT(*) as thanks FROM " . $this->wpdb()->posts . " p INNER JOIN " . $this->actions . " a ON 
            a.post_id = p.ID WHERE a.action = 'thanks' AND p.post_author IN (" . join( ', ', (array) $users ) . ") 
            GROUP BY p.post_author";

		$raw = $this->get_results( $sql );

		return is_array( $raw ) ? (array) $raw : array();
	}

	public function users_online_status( $users ) : array {
		if ( empty( $users ) ) {
			return array();
		}

		$sql = "SELECT DISTINCT user_key FROM " . $this->online . " WHERE user_type = 'user' AND user_key IN ('" . join( "', '", $users ) . "')";
		$raw = $this->get_results( $sql );

		if ( empty( $raw ) ) {
			return array();
		}

		$list = wp_list_pluck( $raw, 'user_key' );
		$list = array_map( 'absint', $list );

		return array_filter( $list );
	}

	public function content_online_status( $content, $ids = array() ) : array {
		switch ( $content ) {
			default:
			case 'forum':
				$column = 'forum_id';
				break;
			case 'topic':
				$column = 'topic_id';
				break;
			case 'view':
				$column = 'topic_view';
				$ids    = preg_filter( '/^/', "'", $ids );
				$ids    = preg_filter( '/$/', "'", $ids );
				break;
			case 'profile':
				$column = 'profile_id';
				break;
		}

		$sql = "SELECT " . $column . " as id, user_type, user_role, count(DISTINCT user_key) AS online FROM " . $this->online;

		if ( ! empty( $column ) ) {
			$sql .= " WHERE " . $column . " IN (" . join( ", ", $ids ) . ")";
		}

		$sql .= " GROUP BY " . $column . ", user_type, user_role";

		$raw = $this->get_results( $sql );

		$out = array();

		$items = array();
		foreach ( $raw as $row ) {
			if ( ! isset( $items[ $row->id ] ) ) {
				$items[ $row->id ] = array();
			}

			$items[ $row->id ][] = $row;
		}

		foreach ( $items as $id => $raw ) {
			$item = array( 'users' => 0, 'guests' => 0, 'roles' => array() );

			$roles = bbpc_get_user_roles();
			foreach ( array_keys( $roles ) as $role ) {
				$item['roles'][ $role ] = 0;
			}

			foreach ( $raw as $row ) {
				$online = absint( $row->online );

				if ( $row->user_type == 'guest' ) {
					$item['guests'] = $online;
				} else {
					$item['users'] = $online;

					if ( isset( $item['roles'][ $row->user_role ] ) ) {
						$item['roles'][ $row->user_role ] = $online;
					}
				}
			}

			$out[ $id ] = $item;
		}

		return $out;
	}

	public function count_all_topics_replies( $users ) : array {
		if ( empty( $users ) ) {
			return array();
		}

		$sql = "SELECT post_author AS user_id, post_type, COUNT(*) AS posts FROM " . $this->wpdb()->posts . "  
            WHERE post_type IN ('" . bbp_get_topic_post_type() . "', '" . bbp_get_reply_post_type() . "') 
            AND post_status IN ('publish', 'private', 'closed') 
            AND post_author IN (" . join( ', ', (array) $users ) . ") GROUP BY post_author, post_type";

		$raw     = $this->get_results( $sql );
		$results = array();

		foreach ( $raw as $row ) {
			if ( ! isset( $results[ $row->user_id ] ) ) {
				$results[ $row->user_id ] = array(
					bbp_get_topic_post_type() => 0,
					bbp_get_reply_post_type() => 0
				);
			}

			$results[ $row->user_id ][ $row->post_type ] = $row->posts;
		}

		return $results;
	}

	public function get_attachments_errors_ids( $posts ) : array {
		if ( empty( $posts ) ) {
			return array();
		}

		$sql = "SELECT post_id FROM " . $this->wpdb()->postmeta . " WHERE post_id IN (" . join( ',', (array) $posts ) . ") AND meta_key = '_bbp_attachment_upload_error'";
		$raw = $this->run( $sql );

		return wp_list_pluck( $raw, 'post_id' );
	}

	public function get_attachments_ids( $posts ) : array {
		if ( empty( $posts ) ) {
			return array();
		}

		$sql = "SELECT * FROM " . $this->attachments . " WHERE post_id IN (" . join( ',', (array) $posts ) . ")";

		$raw     = $this->run( $sql );
		$results = array();

		foreach ( $raw as $row ) {
			if ( ! isset( $results[ $row->post_id ] ) ) {
				$results[ $row->post_id ] = array();
			}

			$results[ $row->post_id ][] = $row->attachment_id;
		}

		return $results;
	}

	public function count_topic_attachments( $posts ) : array {
		if ( empty( $posts ) ) {
			return array();
		}

		$sql = "SELECT post_id, CAST(meta_value AS UNSIGNED) AS attachments FROM " . $this->wpdb()->postmeta . " WHERE 
                meta_key = '_bbp_attachments_count' AND post_id IN (" . join( ',', (array) $posts ) . ")";

		$raw     = $this->run( $sql );
		$results = array();

		foreach ( $raw as $row ) {
			$results[ $row->post_id ] = $row->attachments;
		}

		return $results;
	}

	public function user_replied_to_topics( $topics, $user_id ) : array {
		if ( empty( $topics ) || empty( $user_id ) ) {
			return array();
		}

		$sql = $this->wpdb()->prepare(
			"SELECT DISTINCT CAST(m.meta_value AS UNSIGNED) AS topic_id FROM " . $this->wpdb()->postmeta . " m 
             INNER JOIN " . $this->wpdb()->posts . " p ON p.ID = m.post_id 
             WHERE m.meta_key = '_bbp_topic_id' AND m.meta_value IN (" . join( ', ', (array) $topics ) . ") AND 
             m.post_id != m.meta_value AND p.post_author = %d",
			$user_id
		);

		return wp_list_pluck( $this->get_results( $sql ), 'topic_id' );
	}

	public function list_topic_replies( $topics ) : array {
		if ( empty( $topics ) ) {
			return array();
		}

		$sql = "SELECT CAST(meta_value as UNSIGNED) as reply FROM " . $this->wpdb()->postmeta . " WHERE 
                meta_value != '0' AND meta_key = '_bbp_last_reply_id' AND post_id IN (" . join( ', ', (array) $topics ) . ")";
		$raw = $this->run( $sql );

		return wp_list_pluck( $raw, 'reply' );
	}

	public function list_topics_last_visit( $topics, $user_id ) : array {
		if ( empty( $topics ) || empty( $user_id ) ) {
			return array();
		}

		$query = $this->wpdb()->prepare(
			"SELECT forum_id, topic_id, reply_id, latest FROM " . $this->tracker . " WHERE 
             user_id = %s AND topic_id IN (" . join( ',', (array) $topics ) . ")",
			$user_id );

		$raw     = $this->run( $query );
		$results = array();

		foreach ( $raw as $row ) {
			$row->latest               = mysql2date( 'G', $row->latest );
			$results[ $row->topic_id ] = $row;
		}

		return $results;
	}

	public function find_forum_children_ids() : array {
		$query = $this->wpdb()->prepare(
			"SELECT ID, post_parent FROM " . $this->wpdb()->posts . " WHERE post_type = %s AND post_status IN ('publish', 'private')",
			bbp_get_forum_post_type()
		);

		$raw = d4p_transient_sql_query( $query, bbpc_plugin()->get_transient_key( 'forums_children_ids' ), 'results' );

		return wp_list_pluck( $raw, 'post_parent', 'ID' );
	}

	public function list_private_posts( $posts ) : array {
		if ( empty( $posts ) ) {
			return array();
		}

		$sql = "SELECT post_id FROM " . $this->wpdb()->postmeta . " WHERE meta_value = '1' 
                AND meta_key IN ('_bbp_topic_is_private', '_bbp_reply_is_private') AND post_id IN (" . join( ', ', (array) $posts ) . ")";
		$raw = $this->run( $sql );

		return wp_list_pluck( $raw, 'post_id' );
	}

	public function reported( $posts ) : array {
		$sql = "SELECT a.post_id, a.user_id FROM " . $this->actions . " a INNER JOIN " . $this->actionmeta . " m ON m.action_id = a.action_id
            WHERE a.post_id IN (" . join( ',', (array) $posts ) . ") AND a.action = 'report' AND m.meta_key = 'status' AND m.meta_value = 'waiting'";

		$raw     = $this->run( $sql );
		$results = array();

		foreach ( $raw as $row ) {
			if ( ! isset( $results[ $row->post_id ] ) ) {
				$results[ $row->post_id ] = array();
			}

			$results[ $row->post_id ][] = $row->user_id;
		}

		return $results;
	}

	public function count_private_replies_in_topic( $topics ) : array {
		$sql = "SELECT i.meta_value AS topic, COUNT(*) AS replies
                FROM " . $this->wpdb()->postmeta . " p 
                INNER JOIN " . $this->wpdb()->postmeta . " i ON i.post_id = p.post_id AND i.meta_value IN (" . join( ',', (array) $topics ) . ") 
                WHERE p.meta_value = '1' 
                AND p.meta_key = '_bbp_reply_is_private'
                AND i.meta_key = '_bbp_topic_id'
                GROUP BY i.meta_value";

		$raw = $this->get_results( $sql );

		if ( ! empty( $raw ) ) {
			return wp_list_pluck( $raw, 'replies', 'topic' );
		}

		return array();
	}
}