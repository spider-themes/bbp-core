<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

use SpiderDevs\Plugin\BBPC\Database\Posts as PostsDB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Posts {
	public function __construct() {

	}

	public static function instance() : Posts {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Posts();
		}

		return $instance;
	}

	public function get_new_posts( array $atts = array() ) : array {
		$defaults = array(
			'timestamp'      => 0,
			'offset'         => 0,
			'limit'          => 32,
			'access_check'   => true,
			'include_forums' => array(),
			'exclude_forums' => array(),
			'post_status'    => array(
				bbp_get_public_status_id(),
				bbp_get_closed_status_id()
			)
		);

		$args = wp_parse_args( $atts, $defaults );

		$topics = array();
		$list   = array();

		$raw = PostsDB::instance()->get_new_posts( $args );

		foreach ( $raw as $row ) {
			$topic = $row->post_type == bbp_get_topic_post_type() ? $row->ID : $row->topic;

			if ( ! in_array( $topic, $topics ) ) {
				$topics[] = $topic;

				$list[] = array(
					'type'     => $row->post_type,
					'id'       => $row->ID,
					'author'   => $row->post_author,
					'parent'   => $row->topic,
					'forum'    => $row->forum,
					'activity' => '',
					'topic'    => $topic
				);
			}
		}

		$this->_fetch_posts( $list );

		if ( $args['access_check'] ) {
			$list = $this->_user_access_rights( $list, $args['limit'] );
		}

		$items = array_slice( $list, 0, $args['limit'] );

		return $this->_update_freshness( $items );
	}

	public function get_new_replies( array $atts = array() ) : array {
		$defaults = array(
			'timestamp'      => 0,
			'offset'         => 0,
			'limit'          => 1000,
			'access_check'   => true,
			'include_forums' => array(),
			'exclude_forums' => array(),
			'post_status'    => array(
				bbp_get_public_status_id(),
				bbp_get_closed_status_id()
			)
		);

		$args = wp_parse_args( $atts, $defaults );

		$raw = PostsDB::instance()->get_new_replies( $args );

		$list = array();

		foreach ( $raw as $row ) {
			$date = bbp_get_time_since( bbp_convert_date( $row->post_date ) );

			$list[] = array(
				'type'     => 'reply',
				'id'       => $row->ID,
				'author'   => $row->post_author,
				'parent'   => $row->topic,
				'forum'    => $row->forum,
				'activity' => $date,
				'topic'    => $row->topic
			);
		}

		$this->_fetch_posts( $list );

		if ( $args['access_check'] ) {
			$list = $this->_user_access_rights( $list, $args['limit'] );
		}

		return array_slice( $list, 0, $args['limit'] );
	}

	public function get_new_topics( array $atts = array() ) : array {
		$defaults = array(
			'timestamp'      => 0,
			'offset'         => 0,
			'limit'          => 32,
			'access_check'   => true,
			'include_forums' => array(),
			'exclude_forums' => array(),
			'post_status'    => array( bbp_get_public_status_id(), bbp_get_closed_status_id() )
		);

		$args = wp_parse_args( $atts, $defaults );

		$raw = PostsDB::instance()->get_new_topics( $args );

		$list = array();

		foreach ( $raw as $row ) {
			$date = bbp_get_time_since( bbp_convert_date( $row->post_date ) );

			$list[] = array(
				'type'     => 'topic',
				'id'       => $row->ID,
				'author'   => $row->post_author,
				'parent'   => 0,
				'forum'    => $row->forum,
				'activity' => $date,
				'topic'    => $row->ID
			);
		}

		$this->_fetch_posts( $list );

		if ( $args['access_check'] ) {
			$list = $this->_user_access_rights( $list, $args['limit'] );
		}

		return array_slice( $list, 0, $args['limit'] );
	}

	private function _fetch_posts( array $list ) {
		$items = array_merge(
			wp_list_pluck( $list, 'topic' ),
			wp_list_pluck( $list, 'id' ),
			wp_list_pluck( $list, 'forum' )
		);
		$items = array_map( 'absint', $items );
		$items = array_unique( $items );

		sort( $items );

		d4p_posts_cache_by_ids( $items );
		update_meta_cache( 'post', $items );
	}

	private function _update_freshness( array $list ) : array {
		foreach ( $list as &$item ) {
			$item['activity'] = bbp_get_topic_last_active_time( $item['topic'] );
		}

		return $list;
	}

	private function _user_access_rights( array $list, int $limit ) : array {
		$results = array();

		$user_id = bbp_get_current_user_id();

		foreach ( $list as $item ) {
			$include  = true;
			$forum_id = $item['forum'];

			if ( bbp_is_forum_private( $forum_id ) ) {
				if ( ! current_user_can( 'read_private_forums' ) ) {
					$include = false;
				}
			} else if ( bbp_is_forum_hidden( $forum_id ) ) {
				if ( ! current_user_can( 'read_hidden_forums' ) ) {
					$include = false;
				}
			}

			if ( $include ) {
				if ( $item['type'] == 'topic' ) {
					if ( ! bbpc_is_user_allowed_to_topic( $item['id'], $user_id ) ) {
						$include = false;
					}
				} else if ( $item['type'] == 'reply' ) {
					if ( ! bbpc_is_user_allowed_to_reply( $item['id'], $user_id ) ) {
						$include = false;
					}
				}
			}

			if ( $include ) {
				$results[] = $item;
			}

			if ( count( $results ) == $limit ) {
				break;
			}
		}

		return $results;
	}
}
