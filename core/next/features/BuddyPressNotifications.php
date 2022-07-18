<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BuddyPressNotifications extends Feature {
	public $feature_name = 'buddypress-notifications';
	public $settings = array(
		'thanks_received' => false,
		'post_reported'   => false
	);

	public function __construct() {
		parent::__construct();

		add_filter( 'bp_notifications_get_registered_components', array( $this, 'the_component' ) );
		add_filter( 'bp_notifications_get_notifications_for_user', array( $this, 'the_handler' ), 10, 8 );

		if ( $this->get( 'thanks_received', 'buddypress' ) ) {
			add_action( 'gdbbx_say_thanks_saved', array( $this, 'notify_on_thanks' ), 10, 3 );
		}

		if ( $this->get( 'post_reported', 'buddypress' ) ) {
			add_action( 'gdbbx_post_reported', array( $this, 'notify_on_report' ), 10, 3 );
		}
	}

	public static function instance() : BuddyPressNotifications {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new BuddyPressNotifications();
		}

		return $instance;
	}

	public function the_component( $component_names = array() ) {
		if ( ! is_array( $component_names ) ) {
			$component_names = array();
		}

		array_push( $component_names, 'gdbbx' );

		return $component_names;
	}

	public function the_handler( $action, $item_id, $secondary_item_id, $total_items, $format, $component_action_name, $component_name, $id ) {
		if ( $component_action_name === 'gdbbx_thanks_received' ) {
			$url   = bbp_is_topic( $item_id ) ? bbp_get_topic_permalink( $item_id ) : bbp_get_reply_url( $item_id );
			$title = bbp_is_topic( $item_id ) ? bbp_get_topic_title( $item_id ) : gdbbx_get_reply_title( $item_id );

			$total_items = absint( $total_items );

			if ( $total_items > 1 ) {
				$filter = 'gdbbx_multiple_new_thanks_notification';
				$text   = sprintf( esc_html_x( "You have received %d new thanks", "BuddyPress Notification message", "bbp-core" ), $total_items );
			} else {
				$filter = 'gdbbx_single_new_thanks_notification';
				$text   = ! empty( $secondary_item_id )
					? sprintf( esc_html_x( "You have received new thanks for '%s' from %s", "BuddyPress Notification message", "bbp-core" ), $title, bp_core_get_user_displayname( $secondary_item_id ) )
					: sprintf( esc_html_x( "You have received new thanks for '%s'", "BuddyPress Notification message", "bbp-core" ), $title );
			}

			if ( $format === 'string' ) {
				return apply_filters( $filter, '<a href="' . esc_url( $url ) . '" title="' . esc_attr_x( "Thanks Received", "BuddyPress Notification message", "bbp-core" ) . '">' . esc_html( $text ) . '</a>', $item_id, $secondary_item_id, $total_items, $text, $url );
			} else {
				return apply_filters( $filter, array(
					'text' => $text,
					'link' => $url
				), $item_id, $secondary_item_id, $total_items, $text, $url );
			}
		} else if ( $component_action_name === 'gdbbx_post_reported' ) {
			$url   = bbp_is_topic( $item_id ) ? bbp_get_topic_permalink( $item_id ) : bbp_get_reply_url( $item_id );
			$title = bbp_is_topic( $item_id ) ? bbp_get_topic_title( $item_id ) : gdbbx_get_reply_title( $item_id );

			$total_items = absint( $total_items );

			if ( $total_items > 1 ) {
				$filter = 'gdbbx_multiple_new_report_notification';
				$text   = sprintf( esc_html_x( "%d forum posts reported", "BuddyPress Notification message", "bbp-core" ), $total_items );
			} else {
				$filter = 'gdbbx_single_new_report_notification';
				$text   = ! empty( $secondary_item_id )
					? sprintf( esc_html_x( "Forum post '%s' reported by %s", "BuddyPress Notification message", "bbp-core" ), $title, bp_core_get_user_displayname( $secondary_item_id ) )
					: sprintf( esc_html_x( "Forum post '%s' reported", "BuddyPress Notification message", "bbp-core" ), $title );
			}

			if ( $format === 'string' ) {
				return apply_filters( $filter, '<a href="' . esc_url( $url ) . '" title="' . esc_attr__( "Post Report", "bbp-core" ) . '">' . esc_html( $text ) . '</a>', $item_id, $secondary_item_id, $total_items, $text, $url );
			} else {
				return apply_filters( $filter, array(
					'text' => $text,
					'link' => $url
				), $item_id, $secondary_item_id, $total_items, $text, $url );
			}
		} else {
			return $action;
		}
	}

	public function notify_on_report( $post_id, $user_id, $report ) {
		if ( ! function_exists( 'bp_notifications_add_notification' ) ) {
			return;
		}

		$users = array_merge( gdbbx_get_keymasters(), gdbbx_get_moderators() );

		$author_id = 0;

		if ( bbp_is_topic( $post_id ) ) {
			$author_id = bbp_get_topic_author_id( $post_id );
		} else if ( bbp_is_reply( $post_id ) ) {
			$author_id = bbp_get_reply_author_id( $post_id );
		}

		$args = array(
			'item_id'           => $post_id,
			'secondary_item_id' => $author_id,
			'component_name'    => 'gdbbx',
			'component_action'  => 'gdbbx_post_reported',
			'date_notified'     => bp_core_current_time(),
			'is_new'            => 1
		);

		foreach ( $users as $user ) {
			if ( $user->ID != $user_id ) {
				$args['user_id'] = $user->ID;

				bp_notifications_add_notification( $args );
			}
		}
	}

	public function notify_on_thanks( $post_id, $user_id, $thanks_id ) {
		if ( ! function_exists( 'bp_notifications_add_notification' ) ) {
			return;
		}

		$author_id = 0;

		if ( bbp_is_topic( $post_id ) ) {
			$author_id = bbp_get_topic_author_id( $post_id );
		} else if ( bbp_is_reply( $post_id ) ) {
			$author_id = bbp_get_reply_author_id( $post_id );
		}

		if ( $author_id > 0 ) {
			$args = array(
				'user_id'           => $author_id,
				'item_id'           => $post_id,
				'secondary_item_id' => $user_id,
				'component_name'    => 'gdbbx',
				'component_action'  => 'gdbbx_thanks_received',
				'date_notified'     => bp_core_current_time(),
				'is_new'            => 1
			);

			bp_notifications_add_notification( $args );
		}
	}
}