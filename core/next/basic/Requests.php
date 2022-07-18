<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

use Dev4Press\Plugin\GDBBX\Tasks\Duplicate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Requests {
	public function __construct() {
		add_action( 'gdbbx_template', array( $this, 'loader' ) );
	}

	public static function instance() : Requests {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Requests();
		}

		return $instance;
	}

	public function loader() {
		add_action( 'bbp_get_request_dupe_topic', array( $this, 'duplicate_topic' ) );
		add_action( 'bbp_get_request_lock', array( $this, 'topic_locking' ) );
		add_action( 'bbp_get_request_unlock', array( $this, 'topic_locking' ) );
	}

	public function duplicate_topic() {
		$post_id = absint( $_GET['id'] );

		if ( ! gdbbx_current_user_can_moderate() ) {
			bbp_add_error( 'gdbbx_dupe_not_moderator', __( "<strong>ERROR</strong>: You can't duplicate topics.", "bbp-core" ) );
		}

		if ( ! bbp_verify_nonce_request( 'gdbbx_dupe_topic_' . $post_id ) ) {
			bbp_add_error( 'gdbbx_dupe_nonce', __( "<strong>ERROR</strong>: Are you sure you wanted to do that?", "bbp-core" ) );
		}

		if ( bbp_has_errors() ) {
			return;
		}

		$id = Duplicate::instance()->topic( $post_id );

		wp_redirect( get_permalink( $id ) );
		exit;
	}

	public function topic_locking() {
		$post_id = absint( $_GET['id'] );
		$action  = d4p_sanitize_extended( $_GET['action'] );

		if ( ! gdbbx_current_user_can_moderate() ) {
			bbp_add_error( 'gdbbx_lock_not_moderator', __( "<strong>ERROR</strong>: You can't lock topics.", "bbp-core" ) );
		}

		if ( ! bbp_verify_nonce_request( 'gdbbx_lock_' . $post_id ) ) {
			bbp_add_error( 'gdbbx_lock_nonce', __( "<strong>ERROR</strong>: Are you sure you wanted to do that?", "bbp-core" ) );
		}

		if ( bbp_has_errors() ) {
			return;
		}

		delete_post_meta( $post_id, '_gdbbx_temp_lock' );

		if ( $action == 'lock' ) {
			add_post_meta( $post_id, '_gdbbx_temp_lock', 'locked', true );
		}

		$url = remove_query_arg( array( '_wpnonce', 'id', 'action' ) );

		wp_redirect( $url );
		exit;
	}
}