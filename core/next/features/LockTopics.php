<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Manager\LockTopics as LockTopicsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LockTopics extends Feature {
	public $feature_name = 'lock-topics';
	public $settings = array(
		'lock' => true
	);

	public function __construct() {
		parent::__construct();

		add_action( 'bbpc_core', array( $this, 'ready' ) );
		add_action( 'bbpc_template', array( $this, 'loader' ) );
	}

	public static function instance() : LockTopics {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new LockTopics();
		}

		return $instance;
	}

	public function ready() {
		LockTopicsManager::instance();
	}

	public function loader() {
		if ( ! bbpc_current_user_can_moderate() && $this->is_topic_temp_locked() ) {
			$this->topic_lock_reply_form();
		}

		add_filter( 'bbp_get_reply_class', array( $this, 'topic_post_class' ), 10, 2 );
		add_filter( 'bbp_get_topic_class', array( $this, 'topic_post_class' ), 10, 2 );
	}

	public function topic_post_class( $classes, $topic_id ) {
		if ( $this->is_topic_temp_locked( $topic_id ) ) {
			$classes[] = 'locked-topic';
		}

		return $classes;
	}

	public function get_lock_link( $id ) {
		$locked = $this->is_topic_temp_locked( $id ) ? 'locked' : 'unlocked';

		$url = add_query_arg( 'id', $id, bbp_get_topic_permalink( $id ) );
		$url = add_query_arg( '_wpnonce', wp_create_nonce( 'bbpc_lock_' . $id ), $url );

		if ( $locked == 'locked' ) {
			$url = add_query_arg( 'action', 'unlock', $url );

			return '<a href="' . esc_url( $url ) . '" class="d4p-bbt-lock-link">' . esc_html__( "Unlock", "bbp-core" ) . '</a>';
		} else {
			$url = add_query_arg( 'action', 'lock', $url );

			return '<a href="' . esc_url( $url ) . '" class="d4p-bbt-lock-link">' . esc_html__( "Lock", "bbp-core" ) . '</a>';
		}
	}

	public function is_topic_temp_locked( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		return get_post_meta( $topic_id, '_bbpc_temp_lock', true ) === 'locked';
	}

	public function topic_lock_reply_form() {
		add_filter( 'bbp_get_template_part', array( $this, 'replace_topic_reply_form' ), 99999, 3 );
	}

	public function message_topic_reply_lock() {
		return apply_filters( 'bbpc_privacy_topic_reply_form_message', __( "This topic is temporarily locked.", "bbp-core" ) );
	}

	public function replace_topic_reply_form( $templates, $slug, $name ) {
		if ( $slug == 'form' && $name == 'reply' && ! bbp_is_reply_edit() ) {
			$templates = array( 'bbpc-form-lock.php' );
		}

		return $templates;
	}

	public function lock_topic( $topic_id = 0, $status = 'lock' ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		delete_post_meta( $topic_id, '_bbpc_temp_lock' );

		if ( $status == 'lock' ) {
			add_post_meta( $topic_id, '_bbpc_temp_lock', 'locked', true );
		}
	}
}
