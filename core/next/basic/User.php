<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

use Dev4Press\Plugin\GDBBX\Features\UserSettings;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class User {
	/** @var int */
	public $id;
	/** @var WP_User */
	public $user;

	public function __construct( $id = 0 ) {
		$this->id = $id;

		$user = get_user_by( 'id', $id );

		if ( $user instanceof WP_User ) {
			$this->user = $user;
		} else {
			$this->id = 0;
		}
	}

	public static function instance( $id = 0 ) : User {
		static $instance = array();

		$id = absint( $id );
		$id = $id == 0 ? bbp_get_current_user_id() : $id;

		if ( ! isset( $instance[ $id ] ) ) {
			$instance[ $id ] = new User( $id );
		}

		return $instance[ $id ];
	}

	public function get_default( $name, $fallback = null ) {
		$obj = UserSettings::instance()->find( $name );

		if ( $obj === false ) {
			return $fallback;
		}

		return $obj->default;
	}

	public function get( $name ) {
		$value = get_user_option( $name, $this->id );

		if ( $value === false ) {
			return $this->get_default( $name, false );
		}

		return $value;
	}

	public function is_user() : bool {
		return $this->id > 0;
	}

	public function is_online() : bool {
		return gdbbx_module_online()->is_online( $this->id );
	}

	public function count_topics() : int {
		return gdbbx_cache()->userstats_count_posts( $this->id, bbp_get_topic_post_type() );
	}

	public function count_replies() : int {
		return gdbbx_cache()->userstats_count_posts( $this->id, bbp_get_reply_post_type() );
	}

	public function count_thanks_given() : int {
		return gdbbx_cache()->thanks_get_count_given( $this->id );
	}

	public function count_thanks_received() : int {
		return gdbbx_cache()->thanks_get_count_received( $this->id );
	}

	public function render_item( $item ) : string {
		$method = 'render_item_' . $item;

		if ( method_exists( $this, $method ) ) {
			return call_user_func( array( $this, $method ) );
		}

		return '';
	}

	public function render_item_online_status() : string {
		$online = $this->is_online();

		return apply_filters( 'gdbbx_user_stats_online_status',
			'<div class="gdbbx-user-stats-block gdbbx-user-stats-online-status">
                 <span class="gdbbx-label gdbbx-status-' . ( $online ? 'online' : 'offline' ) . '">' . ( $online ? __( "Online", "bbp-core" ) : __( "Offline", "bbp-core" ) ) . '</span>
                 </div>', $online );
	}

	public function render_item_topics_count() : string {
		$topics = $this->count_topics();

		return apply_filters( 'gdbbx_user_stats_topics_count',
			'<div class="gdbbx-user-stats-block gdbbx-user-stats-topics">
                 <span class="gdbbx-label">' . __( "Topics", "bbp-core" ) . ':</span> <span class="gdbbx-value">' . $topics . '</span>
                 </div>', $topics );
	}

	public function render_item_replies_count() : string {
		$replies = $this->count_replies();

		return apply_filters( 'gdbbx_user_stats_replies_count',
			'<div class="gdbbx-user-stats-block gdbbx-user-stats-replies">
                 <span class="gdbbx-label">' . __( "Replies", "bbp-core" ) . ':</span> <span class="gdbbx-value">' . $replies . '</span>
                 </div>', $replies );
	}

	public function render_item_thanks_given() : string {
		$thanks_given = $this->count_thanks_given();

		return apply_filters( 'gdbbx_user_stats_thanks_given_count',
			'<div class="gdbbx-user-stats-block gdbbx-user-stats-thanks-given">
                     <span class="gdbbx-label">' . __( "Has thanked", "bbp-core" ) . ':</span> <span class="gdbbx-value">' . sprintf( _n( "%s time", "%s times", $thanks_given, "bbp-core" ), $thanks_given ) . '</span>
                     </div>', $thanks_given );
	}

	public function render_item_thanks_received() : string {
		$thanks_received = $this->count_thanks_received();

		return apply_filters( 'gdbbx_user_stats_thanks_received_count',
			'<div class="gdbbx-user-stats-block gdbbx-user-stats-thanks-received">
                     <span class="gdbbx-label">' . __( "Been thanked", "bbp-core" ) . ':</span> <span class="gdbbx-value">' . sprintf( _n( "%s time", "%s times", $thanks_received, "bbp-core" ), $thanks_received ) . '</span>
                     </div>', $thanks_received );
	}

	public function render_item_registration_date() : string {
		if ( $this->is_user() ) {
			$date   = $this->user->user_registered;
			$format = apply_filters( 'gdbbx_user_stats_registered_date_format', get_option( 'date_format' ) );

			return apply_filters( 'gdbbx_user_stats_registered_on',
				'<div class="gdbbx-user-stats-block gdbbx-user-stats-registered">
                 <span class="gdbbx-label">' . __( "Registered On", "bbp-core" ) . ':</span> <span class="gdbbx-value">' . mysql2date( $format, $date ) . '</span>
                 </div>', $date, $format );
		} else {
			return '';
		}
	}
}
