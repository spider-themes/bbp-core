<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LockForums extends Feature {
	public $feature_name = 'lock-forums';
	public $settings = array(
		'topic_form_locked'            => false,
		'topic_form_allow_super_admin' => true,
		'topic_form_allow_roles'       => array( 'bbp_keymaster' ),
		'topic_form_message'           => 'Forums are currently locked.',
		'reply_form_locked'            => false,
		'reply_form_allow_super_admin' => true,
		'reply_form_allow_roles'       => array( 'bbp_keymaster' ),
		'reply_form_message'           => 'Forums are currently locked.'
	);

	public function __construct() {
		parent::__construct();

		add_action( 'gdbbx_bbpress_template_first', array( $this, 'loader' ) );
	}

	public static function instance() : LockForums {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new LockForums();
		}

		return $instance;
	}

	public function loader() {
		if ( $this->is_topic_locked() && ! $this->allowed( 'topic_form_allow', 'lock-forums-topic-form' ) ) {
			$this->forum_lock_topic_form();
		}

		if ( $this->is_reply_locked() && ! $this->allowed( 'reply_form_allow', 'lock-forums-reply-form' ) ) {
			$this->forum_lock_reply_form();
		}
	}

	public function message_topic_form_locked( $topic_id = 0 ) {
		$topic_id = $topic_id == 0 ? bbp_get_topic_id() : $topic_id;
		$forum_id = $topic_id > 0 ? bbp_get_topic_forum_id( $topic_id ) : 0;

		$message = $forum_id == 0 ? '' : gdbbx_forum( $forum_id )->privacy()->get( 'lock_topic_form_message' );

		if ( empty( $message ) ) {
			$message = $this->settings['topic_form_message'];
		}

		return apply_filters( 'gdbbx_privacy_topic_locked_message', __( $message, "bbp-core" ), $topic_id, $forum_id );
	}

	public function message_reply_form_locked( $topic_id = 0 ) {
		$topic_id = $topic_id == 0 ? bbp_get_topic_id() : $topic_id;
		$forum_id = $topic_id > 0 ? bbp_get_topic_forum_id( $topic_id ) : 0;

		$message = $forum_id == 0 ? '' : gdbbx_forum( $forum_id )->privacy()->get( 'lock_reply_form_message' );

		if ( empty( $message ) ) {
			$message = $this->settings['topic_form_message'];
		}

		return apply_filters( 'gdbbx_privacy_reply_locked_message', __( $message, "bbp-core" ), $topic_id, $forum_id );
	}

	public function is_topic_locked( $topic_id = 0 ) : bool {
		$forum_id = $topic_id > 0 ? bbp_get_topic_forum_id( $topic_id ) : 0;

		$forum = gdbbx_forum( $forum_id )->privacy()->get( 'lock_topic_form' );

		$active = false;
		if ( $forum == 'default' ) {
			$active = $this->settings['topic_form_locked'];
		} else if ( $forum == 'yes' ) {
			$active = true;
		} else if ( $forum == 'no' ) {
			$active = false;
		}

		return (bool) apply_filters( 'gdbbx_privacy_is_topic_locked', $active, $topic_id, $forum_id );
	}

	public function is_reply_locked( $reply_id = 0 ) : bool {
		$forum_id = $reply_id > 0 ? bbp_get_reply_forum_id( $reply_id ) : 0;

		$forum = gdbbx_forum( $forum_id )->privacy()->get( 'lock_reply_form' );

		$active = false;
		if ( $forum == 'default' ) {
			$active = $this->settings['reply_form_locked'];
		} else if ( $forum == 'yes' ) {
			$active = true;
		} else if ( $forum == 'no' ) {
			$active = false;
		}

		return (bool) apply_filters( 'gdbbx_privacy_is_reply_locked', $active, $reply_id, $forum_id );
	}

	public function forum_lock_topic_form() {
		add_filter( 'bbp_get_template_part', array( $this, 'replace_forum_topic_form' ), 100000, 3 );
	}

	public function forum_lock_reply_form() {
		add_filter( 'bbp_get_template_part', array( $this, 'replace_forum_reply_form' ), 100000, 3 );
	}

	public function replace_forum_topic_form( $templates, $slug, $name ) {
		if ( $slug == 'form' && $name == 'topic' ) {
			$templates = array( 'gdbbx-form-topic-locked.php' );
		}

		return $templates;
	}

	public function replace_forum_reply_form( $templates, $slug, $name ) {
		if ( gdbbx_is_user_allowed_to_topic() ) {
			if ( $slug == 'form' && $name == 'reply' ) {
				$templates = array( 'gdbbx-form-reply-locked.php' );
			}
		}

		return $templates;
	}
}
