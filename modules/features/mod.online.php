<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class bbpc_mod_online {
	public $settings  = [];
	public $guest_key = '';

	public function __construct() {
		$this->settings = bbpc()->group_get( 'online' );

		if ( ! is_admin() && $this->settings['active'] ) {
			if ( ! is_user_logged_in() && $this->settings['track_guests'] ) {
				$time = $this->_get_timestamp();
				$exp  = $time + $this->settings['window'];

				if ( ! isset( $_COOKIE[ $this->_cookie_online() ] ) ) {
					$this->guest_key = wp_rand( 1000, 9999 ) . '-' . $time . '-' . wp_rand( 1000, 9999 );
				} else {
					$this->guest_key = $_COOKIE[ $this->_cookie_online() ];
				}

				setcookie( $this->_cookie_online(), $this->guest_key, $exp, '/', COOKIE_DOMAIN );
			}

			add_action( 'template_redirect', [ $this, 'online_tracking' ] );

			if ( $this->settings['notice_for_forum'] ) {
				add_filter( 'bbp_get_single_forum_description', [ $this, 'forum_online_notice' ] );
			}

			if ( $this->settings['notice_for_topic'] ) {
				add_filter( 'bbp_get_single_topic_description', [ $this, 'topic_online_notice' ] );
			}

			if ( $this->settings['notice_for_view'] ) {
				add_action( 'bbp_template_before_single_view', [ $this, 'view_online_notice' ] );
			}

			if ( $this->settings['notice_for_profile'] ) {
				add_action( 'bbp_template_before_user_wrapper', [ $this, 'profile_online_notice' ] );
			}
		}
	}

	public function forum_online_notice( $notice ) {
		$counts = $this->_online_notice_data( 'forum', bbp_get_forum_id() );

		$text = sprintf( _x( 'Currently, there are %1$s and %2$s visiting this forum.', 'Online counts for forum', 'bbp-core' ), '<strong>' . $counts['users'] . '</strong>', '<strong>' . $counts['guests'] . '</strong>' );
		$text = apply_filters( 'bbpc_online_counts_notice_forum', '<div class="bbp-template-notice info">' . $text . '</div>', $counts );

		return $notice . $text;
	}

	public function topic_online_notice( $notice ) {
		$counts = $this->_online_notice_data( 'topic', bbp_get_topic_id() );

		$text = sprintf( _x( 'Currently, there are %1$s and %2$s visiting this topic.', 'Online counts for forum', 'bbp-core' ), '<strong>' . $counts['users'] . '</strong>', '<strong>' . $counts['guests'] . '</strong>' );
		$text = apply_filters( 'bbpc_online_counts_notice_topic', '<div class="bbp-template-notice info">' . $text . '</div>', $counts );

		return $notice . $text;
	}

	public function view_online_notice() {
		$counts = $this->_online_notice_data( 'view', bbp_get_view_id() );

		$text = sprintf( _x( 'Currently, there are %1$s and %2$s visiting this topics view.', 'Online counts for view', 'bbp-core' ), '<strong>' . $counts['users'] . '</strong>', '<strong>' . $counts['guests'] . '</strong>' );
		$text = apply_filters( 'bbpc_online_counts_notice_view', '<div class="bbp-template-notice info">' . $text . '</div>', $counts );

		echo $text;
	}

	public function profile_online_notice() {
		$counts = $this->_online_notice_data( 'profile', bbp_get_displayed_user_id() );

		$text = sprintf( _x( 'Currently, there are %1$s and %2$s visiting this user profile.', 'Online counts for view', 'bbp-core' ), '<strong>' . $counts['users'] . '</strong>', '<strong>' . $counts['guests'] . '</strong>' );
		$text = apply_filters( 'bbpc_online_counts_notice_profile', '<div class="bbp-template-notice info">' . $text . '</div>', $counts );

		echo $text;
	}

	public function is_online( $user_id = 0 ) {
		if ( $user_id == 0 ) {
			return true;
		}

		return bbpc_cache()->userstats_is_online( $user_id );
	}

	public function online_tracking() {
		if ( ! is_user_logged_in() && $this->settings['track_guests'] ) {
			if ( ! empty( $this->guest_key ) ) {
				$this->_add_to_database( 'guest', $this->guest_key );
			}
		} elseif ( is_user_logged_in() && $this->settings['track_users'] ) {
			$user = bbp_get_current_user_id();
			$role = bbp_get_user_role( $user );

			if ( $role === false ) {
				$role = bbp_get_spectator_role();
			}

			$this->_add_to_database( 'user', $user, $role );
		}
	}

	public function max() {
		return [
			'total'  => [
				'count'     => $this->settings['max_total_count'],
				'timestamp' => $this->settings['max_total_timestamp'],
			],
			'users'  => [
				'count'     => $this->settings['max_users_count'],
				'timestamp' => $this->settings['max_users_timestamp'],
			],
			'guests' => [
				'count'     => $this->settings['max_guests_count'],
				'timestamp' => $this->settings['max_guests_timestamp'],
			],
		];
	}

	public function online( $with_user_ids = true ) {
		$info = [
			'counts' => [
				'total'  => $this->settings['current_total_count'],
				'users'  => $this->settings['current_users_count'],
				'guests' => $this->settings['current_guests_count'],
				'roles'  => $this->settings['current_roles_counts'],
			],
			'roles'  => [],
		];

		if ( $with_user_ids ) {
			if ( bbpc_cache()->in( 'online-users', 'roles' ) ) {
				$info['roles'] = bbpc_cache()->get( 'online-users', 'roles', [] );
			} else {
				$info['roles'] = bbpc_db()->get_online_users_list();
				bbpc_cache()->set( 'online-users', 'roles', $info['roles'] );
			}
		}

		return $info;
	}

	private function _online_notice_data( $content, $id ) {
		$counts = bbpc_cache()->online_content_item( $content, $id );

		return [
			'data'    => $counts,
			'content' => $content,
			'id'      => $id,
			'users'   => sprintf( _nx( '%s user', '%s users', $counts['users'], 'Online users count', 'bbp-core' ), $counts['users'] ),
			'guests'  => sprintf( _nx( '%s guest', '%s guests', $counts['guests'], 'Online guests count', 'bbp-core' ), $counts['guests'] ),
		];
	}

	private function _get_timestamp() {
		return bbpc_db()->timestamp();
	}

	private function _cookie_online() {
		return bbpc_db()->prefix() . 'bbpc_online_activity';
	}

	private function _track_current_and_max() {
		$counts = bbpc_db()->count_online_overview();

		$guests = $counts['guests'];
		$users  = $counts['users'];
		$total  = $guests + $users;

		bbpc()->set( 'current_timestamp', $this->_get_timestamp(), 'online' );
		bbpc()->set( 'current_users_count', $users, 'online' );
		bbpc()->set( 'current_guests_count', $guests, 'online' );
		bbpc()->set( 'current_total_count', $total, 'online' );
		bbpc()->set( 'current_roles_counts', $counts['roles'], 'online' );

		if ( $users > absint( $this->settings['max_users_count'] ) ) {
			bbpc()->set( 'max_users_count', $users, 'online' );
			bbpc()->set( 'max_users_timestamp', $this->_get_timestamp(), 'online' );
		}

		if ( $guests > absint( $this->settings['max_guests_count'] ) ) {
			bbpc()->set( 'max_guests_count', $guests, 'online' );
			bbpc()->set( 'max_guests_timestamp', $this->_get_timestamp(), 'online' );
		}

		if ( $total > absint( $this->settings['max_total_count'] ) ) {
			bbpc()->set( 'max_total_count', $total, 'online' );
			bbpc()->set( 'max_total_timestamp', $this->_get_timestamp(), 'online' );
		}

		bbpc()->save( 'online' );
	}

	private function _add_to_database( $user_type, $user_key, $user_role = '' ) {
		$entry = [
			'user_type'  => $user_type,
			'user_key'   => $user_key,
			'user_role'  => $user_role,
			'content'    => 'general',
			'forum_id'   => 0,
			'topic_id'   => 0,
			'profile_id' => 0,
			'topic_view' => '',
		];

		if ( bbp_is_single_forum() ) {
			$entry['content']  = 'forum';
			$entry['forum_id'] = bbp_get_forum_id();
		} elseif ( bbp_is_single_topic() ) {
			$entry['content']  = 'topic';
			$entry['topic_id'] = bbp_get_topic_id();
			$entry['forum_id'] = bbp_get_topic_forum_id( $entry['topic_id'] );
		} elseif ( bbp_is_single_reply() ) {
			$entry['content']  = 'topic';
			$entry['topic_id'] = bbp_get_reply_topic_id();
			$entry['forum_id'] = bbp_get_topic_forum_id( $entry['topic_id'] );
		} elseif ( bbp_is_single_view() ) {
			$entry['content']    = 'view';
			$entry['topic_view'] = bbp_get_view_id();
		} elseif ( bbp_is_single_user() ) {
			$entry['content']    = 'profile';
			$entry['profile_id'] = bbp_get_displayed_user_id();
		}

		$status = bbpc_db()->add_online_entry( $entry );
		$counts = bbpc_db()->clean_online_table( $this->settings['window'] );

		if ( $status == 'added' || $counts == 0 ) {
			$this->_track_current_and_max();
		}
	}
}

/** @return bbpc_mod_online  */
function bbpc_module_online() {
	return bbpc_plugin()->modules['online'];
}
