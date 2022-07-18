<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

use d4p_cache_core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cache extends d4p_cache_core {
	public $store = 'gdbbx';

	private $_forum_children_ids_run = false;

	public static function instance() : Cache {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Cache();
		}

		return $instance;
	}

	public function private_run_count_topic_replies( $posts ) {
		$raw = gdbbx_db_bulk()->count_private_replies_in_topic( $posts );

		foreach ( $raw as $topic => $count ) {
			$this->set( 'topic-private-replies', $topic, absint( $count ) );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'topic-private-replies', $post_id ) ) {
				$this->set( 'topic-private-replies', $post_id, 0 );
			}
		}
	}

	public function private_count_topic_replies( $topic_id ) {
		if ( ! $this->in( 'topic-private-replies', $topic_id ) ) {
			$this->private_run_count_topic_replies( array( $topic_id ) );
		}

		return $this->get( 'topic-private-replies', $topic_id, 0 );
	}

	public function tracking_forums_user_activity( $user_id ) {
		$list = gdbbx_db_cache()->tracking_forums_user_tracking( $user_id );

		foreach ( $list as $forum => $data ) {
			$this->set( 'forums-user-latest-activity', $forum, $data );
		}
	}

	public function get_forum_user_latest_activity( $forums ) {
		$latest = 0;

		foreach ( $forums as $forum_id ) {
			$data = $this->get( 'forums-user-latest-activity', $forum_id, array( 'latest' => 0 ) );

			if ( $data['latest'] > $latest ) {
				$latest = $data['latest'];
			}
		}

		return $latest == 0 ? false : $latest;
	}

	public function tracking_forums_activity() {
		$list = gdbbx_db_cache()->tracking_forums_activity();

		foreach ( $list as $forum => $data ) {
			$this->set( 'forums-latest-activity', $forum, $data );
		}
	}

	public function get_forum_latest_activity( $forum_id ) {
		$data = $this->get( 'forums-latest-activity', $forum_id, array( 'active' => 0, 'posted' => 0 ) );

		return $data['posted'] > 0 ? $data['posted'] : ( $data['active'] > 0 ? $data['active'] : false );
	}

	public function tracking_topic_last_visit( $topic_id, $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		if ( ! $this->in( 'topic-tracking', $topic_id ) ) {
			$latest = gdbbx_db_cache()->get_topic_last_visit( $user_id, $topic_id );
			$this->set( 'topic-tracking', $topic_id, $latest );
		}

		return $this->get( 'topic-tracking', $topic_id, false );
	}

	public function tracking_run_bulk_forums() {
		if ( $this->_forum_children_ids_run === false ) {
			$list = gdbbx_db_bulk()->find_forum_children_ids();

			$output = array();

			foreach ( $list as $child => $parent ) {
				if ( $parent > 0 ) {
					if ( ! isset( $output[ $parent ] ) ) {
						$output[ $parent ] = array();
					}

					$output[ $parent ][] = $child;
				}
			}

			foreach ( $output as $forum => $children ) {
				$this->set( 'forums-parent-child', $forum, $children );
			}

			$this->_forum_children_ids_run = true;
		}
	}

	public function tracking_run_bulk_topics( $posts, $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		$raw = gdbbx_db_bulk()->list_topics_last_visit( $posts, $user_id );

		foreach ( $raw as $post_id => $track ) {
			$this->set( 'topic-tracking', $post_id, $track );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'topic-tracking', $post_id ) ) {
				$this->set( 'topic-tracking', $post_id, false );
			}
		}
	}

	public function private_post( $post_id ) {
		if ( ! $this->in( 'post-private', $post_id ) ) {
			$this->private_run_bulk_posts( array( $post_id ) );
		}

		return $this->get( 'post-private', $post_id, false );
	}

	public function private_run_bulk_posts( $posts ) {
		$raw = gdbbx_db_bulk()->list_private_posts( $posts );

		foreach ( $raw as $post_id ) {
			$this->set( 'post-private', $post_id, true );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'post-private', $post_id ) ) {
				$this->set( 'post-private', $post_id, false );
			}
		}
	}

	public function userreplied_user_replied( $post_id, $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		if ( ! $this->in( 'user-replied', $post_id ) ) {
			$this->userreplied_run_bulk_topics( array( $post_id ), $user_id );
		}

		return $this->get( 'user-replied', $post_id, false );
	}

	public function userreplied_run_bulk_topics( $posts, $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		$raw = gdbbx_db_bulk()->user_replied_to_topics( $posts, $user_id );

		foreach ( $raw as $post_id ) {
			$this->set( 'user-replied', $post_id, true );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'user-replied', $post_id ) ) {
				$this->set( 'user-replied', $post_id, false );
			}
		}
	}

	public function userstats_count_posts( $user_id, $type = 'topic' ) {
		if ( ! $this->in( 'user-posts-count', $user_id ) ) {
			$this->userstats_run_bulk_counts( array( $user_id ) );
		}

		$data = $this->get( 'user-posts-count', $user_id, array(
			bbp_get_topic_post_type() => 0,
			bbp_get_reply_post_type() => 0
		) );

		if ( isset( $data[ $type ] ) ) {
			return $data[ $type ];
		}

		return 0;
	}

	public function userstats_run_bulk_counts( $users ) {
		$raw = gdbbx_db_bulk()->count_all_topics_replies( $users );

		foreach ( $raw as $user_id => $counts ) {
			$this->set( 'user-posts-count', $user_id, $counts );
		}

		foreach ( $users as $user_id ) {
			if ( ! $this->in( 'user-posts-count', $user_id ) ) {
				$this->set( 'user-posts-count', $user_id, array(
					bbp_get_topic_post_type() => 0,
					bbp_get_reply_post_type() => 0
				) );
			}
		}
	}

	public function userstats_is_online( $user_id ) {
		if ( ! $this->in( 'user-is-online', $user_id ) ) {
			$this->userstats_run_bulk_online( array( $user_id ) );
		}

		return $this->get( 'user-is-online', $user_id, false );
	}

	public function userstats_run_bulk_online( $users ) {
		$raw = gdbbx_db_bulk()->users_online_status( $users );

		foreach ( $raw as $user_id ) {
			$this->set( 'user-is-online', $user_id, true );
		}

		foreach ( $users as $user_id ) {
			if ( ! $this->in( 'user-is-online', $user_id ) ) {
				$this->set( 'user-is-online', $user_id, false );
			}
		}
	}

	public function online_content_item( $content, $id ) {
		if ( ! $this->in( $content . '-online-item', $id ) ) {
			$this->online_run_bulk_scope( $content, array( $id ) );
		}

		return $this->get( $content . '-online-item', $id, $item = array(
			'users'  => 0,
			'guests' => 0,
			'roles'  => array()
		) );
	}

	public function online_run_bulk_scope( $content, $ids ) {
		$raw = gdbbx_db_bulk()->content_online_status( $content, $ids );

		foreach ( $raw as $id => $item ) {
			$this->set( $content . '-online-item', $id, $item );
		}

		foreach ( $ids as $id ) {
			if ( ! $this->in( $content . '-online-item', $id ) ) {
				$this->set( $content . '-online-item', $id, $item = array(
					'users'  => 0,
					'guests' => 0,
					'roles'  => array()
				) );
			}
		}
	}

	public function attachments_has_attachments_errors( $post_id ) {
		if ( ! $this->in( 'attachments-errors', $post_id ) ) {
			$this->attachments_errors_run_bulk_counts( array( $post_id ) );
		}

		return $this->get( 'attachments-errors', $post_id, false );
	}

	public function attachments_has_attachments( $post_id ) {
		if ( ! $this->in( 'attachments-count', $post_id ) ) {
			$this->attachments_run_bulk_counts( array( $post_id ) );
		}

		$count = $this->get( 'attachments-count', $post_id, 0 );

		return $count > 0;
	}

	public function attachments_count_attachments( $post_id ) {
		if ( ! $this->in( 'attachments-count', $post_id ) ) {
			$this->attachments_run_bulk_counts( array( $post_id ) );
		}

		return $this->get( 'attachments-count', $post_id, 0 );
	}

	public function attachments_get_attachments_ids( $post_id ) {
		if ( ! $this->in( 'attachments-ids', $post_id ) ) {
			$this->attachments_run_bulk_counts( array( $post_id ) );
		}

		return $this->get( 'attachments-ids', $post_id, array() );
	}

	public function attachments_errors_run_bulk_counts( $posts ) {
		$raw = gdbbx_db_bulk()->get_attachments_errors_ids( $posts );

		foreach ( $raw as $post_id ) {
			$this->set( 'attachments-errors', $post_id, true );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'attachments-errors', $post_id ) ) {
				$this->set( 'attachments-errors', $post_id, false );
			}
		}
	}

	public function attachments_run_bulk_counts( $posts ) {
		$raw = gdbbx_db_bulk()->get_attachments_ids( $posts );

		foreach ( $raw as $post_id => $ids ) {
			$this->set( 'attachments-count', $post_id, count( $ids ) );
			$this->set( 'attachments-ids', $post_id, $ids );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'attachments-count', $post_id ) ) {
				$this->set( 'attachments-count', $post_id, 0 );
				$this->set( 'attachments-ids', $post_id, array() );
			}
		}
	}

	public function attachments_has_topic_attachments( $post_id ) {
		if ( ! $this->in( 'attachments-topic-count', $post_id ) ) {
			$this->attachments_run_bulk_topics_counts( array( $post_id ) );
		}

		$count = $this->get( 'attachments-topic-count', $post_id, 0 );

		return $count > 0;
	}

	public function attachments_count_topic_attachments( $post_id ) {
		if ( ! $this->in( 'attachments-topic-count', $post_id ) ) {
			$this->attachments_run_bulk_topics_counts( array( $post_id ) );
		}

		return $this->get( 'attachments-topic-count', $post_id, 0 );
	}

	public function attachments_run_bulk_topics_counts( $posts ) {
		$raw = gdbbx_db_bulk()->count_topic_attachments( $posts );

		foreach ( $raw as $post_id => $count ) {
			$this->set( 'attachments-topic-count', $post_id, $count );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'attachments-topic-count', $post_id ) ) {
				$this->set( 'attachments-topic-count', $post_id, 0 );
			}
		}
	}

	public function report_user_reported( $post_id, $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		if ( ! $this->in( 'report-list', $post_id ) ) {
			$users = gdbbx_db_cache()->reported( $post_id );
			$this->set( 'report-list', $post_id, $users );
		}

		$list = $this->get( 'report-list', $post_id, array() );

		return in_array( $user_id, $list );
	}

	public function report_is_reported( $post_id ) {
		if ( ! $this->in( 'report-list', $post_id ) ) {
			$users = gdbbx_db_cache()->reported( $post_id );
			$this->set( 'report-list', $post_id, $users );
		}

		$list = $this->get( 'report-list', $post_id, array() );

		return ! empty( $list );
	}

	public function report_run_bulk_list( $posts ) {
		$raw = gdbbx_db_bulk()->reported( $posts );

		foreach ( $raw as $post_id => $users ) {
			$this->set( 'report-list', $post_id, $users );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'report-list', $post_id ) ) {
				$this->set( 'report-list', $post_id, array() );
			}
		}
	}

	public function thanks_get_list( $post_id ) {
		if ( ! $this->in( 'thanks-list', $post_id ) ) {
			$thanks = gdbbx_db_cache()->thanks_list( $post_id );
			$this->set( 'thanks-list', $post_id, $thanks );
		}

		return $this->get( 'thanks-list', $post_id, array() );
	}

	public function thanks_run_bulk_list( $posts ) {
		$raw = gdbbx_db_bulk()->thanks_list( $posts );

		foreach ( $raw as $post_id => $users ) {
			$this->set( 'thanks-list', $post_id, $users );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'thanks-list', $post_id ) ) {
				$this->set( 'thanks-list', $post_id, array() );
			}
		}
	}

	public function thanks_get_given( $post_id, $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		if ( ! $this->in( 'thanks-thread-' . absint( $post_id ), $user_id ) ) {
			$thanks = gdbbx_db_cache()->thanks_given( $post_id, $user_id );
			$this->set( 'thanks-thread-' . absint( $post_id ), $user_id, $thanks );
		}

		return $this->get( 'thanks-thread-' . absint( $post_id ), $user_id, false );
	}

	public function thanks_run_bulk_given( $posts, $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		$raw = gdbbx_db_bulk()->thanks_given( $posts, $user_id );

		foreach ( $raw as $row ) {
			$this->set( 'thanks-thread-' . absint( $row->post_id ), $user_id, true );
		}

		foreach ( $posts as $post_id ) {
			if ( ! $this->in( 'thanks-thread-' . absint( $post_id ), $user_id ) ) {
				$this->set( 'thanks-thread-' . absint( $post_id ), $user_id, false );
			}
		}
	}

	public function thanks_get_count_given( $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		if ( ! $this->in( 'thanks-given', absint( $user_id ) ) ) {
			$thanks = gdbbx_db_cache()->count_all_thanks_given( $user_id );
			$this->set( 'thanks-given', absint( $user_id ), absint( $thanks ) );
		}

		return $this->get( 'thanks-given', $user_id, 0 );
	}

	public function thanks_get_count_received( $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = bbp_get_current_user_id();
		}

		if ( ! $this->in( 'thanks-received', absint( $user_id ) ) ) {
			$thanks = gdbbx_db_cache()->count_all_thanks_received( $user_id );
			$this->set( 'thanks-received', absint( $user_id ), absint( $thanks ) );
		}

		return $this->get( 'thanks-received', $user_id, 0 );
	}

	public function thanks_run_bulk_count_given( $users ) {
		$raw = gdbbx_db_bulk()->count_all_thanks_given( $users );

		foreach ( $raw as $row ) {
			$this->set( 'thanks-given', absint( $row->user_id ), absint( $row->thanks ) );
		}

		foreach ( $users as $user ) {
			if ( ! $this->in( 'thanks-given', absint( $user ) ) ) {
				$this->set( 'thanks-given', absint( $user ), 0 );
			}
		}
	}

	public function thanks_run_bulk_count_received( $users ) {
		$raw = gdbbx_db_bulk()->count_all_thanks_received( $users );

		foreach ( $raw as $row ) {
			$this->set( 'thanks-received', absint( $row->user_id ), absint( $row->thanks ) );
		}

		foreach ( $users as $user ) {
			if ( ! $this->in( 'thanks-received', absint( $user ) ) ) {
				$this->set( 'thanks-received', absint( $user ), 0 );
			}
		}
	}
}
