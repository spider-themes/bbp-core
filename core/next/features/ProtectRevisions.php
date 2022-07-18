<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProtectRevisions extends Feature {
	public $feature_name = 'protect-revisions';
	public $settings = array(
		'allow_author'       => true,
		'allow_topic_author' => true,
		'allow_super_admin'  => true,
		'allow_roles'        => null,
		'allow_visitor'      => false
	);

	public function __construct() {
		parent::__construct();

		remove_filter( 'bbp_get_topic_content', 'bbp_topic_content_append_revisions', 99 );
		remove_filter( 'bbp_get_reply_content', 'bbp_reply_content_append_revisions', 99 );

		add_filter( 'bbp_get_topic_content', array( $this, 'reply_content_append_revisions' ), 99, 2 );
		add_filter( 'bbp_get_reply_content', array( $this, 'reply_content_append_revisions' ), 99, 2 );
	}

	public static function instance() : ProtectRevisions {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new ProtectRevisions();
		}

		return $instance;
	}

	public function reply_content_append_revisions( $content = '', $id = 0 ) {
		if ( gdbbx()->is_inside_content_shortcode( $id ) ) {
			return $content;
		}

		if ( gdbbx_is_feed() ) {
			return $content;
		}

		$is_topic = bbp_is_topic( $id );
		$is_reply = bbp_is_reply( $id );

		$author       = $is_topic ? bbp_get_topic_author_id( $id ) : bbp_get_reply_author_id( $id );
		$author_topic = $is_reply ? bbp_get_topic_author_id( bbp_get_reply_topic_id( $id ) ) : $author;

		$user = bbp_get_current_user_id();

		$allowed = false;
		if ( $user == $author && $this->settings['allow_author'] ) {
			$allowed = true;
		}

		if ( ! $allowed && $is_reply && $user == $author_topic && $this->settings['allow_topic_author'] ) {
			$allowed = true;
		}

		if ( ! $allowed ) {
			$allowed = $this->allowed( 'allow' );
		}

		if ( $allowed ) {
			if ( $is_topic ) {
				$content = apply_filters( 'bbp_topic_append_revisions', $content . bbp_get_topic_revision_log( $id ), $content, $id );
			} else {
				$content = apply_filters( 'bbp_reply_append_revisions', $content . bbp_get_reply_revision_log( $id ), $content, $id );
			}
		}

		return $content;
	}
}
