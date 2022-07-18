<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mailer {
	public function __construct() {
	}

	public static function instance() : Mailer {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Mailer();
		}

		return $instance;
	}

	public function topic_revision_log( $topic_id = 0 ) : string {
		$topic_id     = bbp_get_topic_id( $topic_id );
		$revision_log = bbp_get_topic_raw_revision_log( $topic_id );

		if ( empty( $topic_id ) || empty( $revision_log ) || ! is_array( $revision_log ) ) {
			return __( "No log saved.", "bbp-core" );
		}

		$revisions = bbp_get_topic_revisions( $topic_id );
		if ( empty( $revisions ) ) {
			return __( "No log saved.", "bbp-core" );
		}

		$r = '';

		foreach ( (array) $revisions as $revision ) {
			$reason = '';
			if ( ! empty( $revision_log[ $revision->ID ] ) ) {
				$reason = $revision_log[ $revision->ID ]['reason'];
			}

			$author = bbp_get_topic_author_display_name( $revision->ID );

			if ( ! empty( $reason ) ) {
				$r .= sprintf( __( "This topic was modified by %s. Reason: %s", "bbp-core" ), $author, esc_html( $reason ) ) . "\n";
			}
		}

		if ( empty( $r ) ) {
			return __( "No log saved.", "bbp-core" );
		}

		return $r;
	}

	public function reply_revision_log( $reply_id = 0 ) : string {
		$reply_id     = bbp_get_reply_id( $reply_id );
		$revision_log = bbp_get_reply_raw_revision_log( $reply_id );

		if ( empty( $reply_id ) || empty( $revision_log ) || ! is_array( $revision_log ) ) {
			return __( "No log saved.", "bbp-core" );
		}

		$revisions = bbp_get_reply_revisions( $reply_id );
		if ( empty( $revisions ) ) {
			return __( "No log saved.", "bbp-core" );
		}

		$r = '';

		foreach ( (array) $revisions as $revision ) {
			$reason = '';

			if ( ! empty( $revision_log[ $revision->ID ] ) ) {
				$reason = $revision_log[ $revision->ID ]['reason'];
			}

			$author = bbp_get_reply_author_display_name( $revision->ID );

			if ( ! empty( $reason ) ) {
				$r .= sprintf( __( "This reply was modified by %s. Reason: %s", "bbp-core" ), $author, esc_html( $reason ) ) . "\n";
			}
		}

		if ( empty( $r ) ) {
			return __( "No log saved.", "bbp-core" );
		}

		return $r;
	}

	public function get_topic_content( $topic_id ) : array {
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		remove_all_filters( 'bbp_get_topic_content' );
		remove_all_filters( 'bbp_get_topic_title' );
		remove_all_filters( 'bbp_get_forum_title' );
		remove_all_filters( 'the_title' );

		$topic_title   = gdbbx_email_clean_content( bbp_get_topic_title( $topic_id ) );
		$forum_title   = gdbbx_email_clean_content( bbp_get_forum_title( $forum_id ) );
		$topic_content = gdbbx_email_clean_content( bbp_get_topic_content( $topic_id ) );
		$blog_name     = gdbbx_email_clean_content( get_option( 'blogname' ) );

		return compact( 'topic_title', 'topic_content', 'blog_name', 'forum_title' );
	}

	public function get_reply_content( $reply_id, $topic_id ) : array {
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		remove_all_filters( 'bbp_get_topic_content' );
		remove_all_filters( 'bbp_get_reply_content' );
		remove_all_filters( 'bbp_get_topic_title' );
		remove_all_filters( 'bbp_get_reply_title' );
		remove_all_filters( 'bbp_get_forum_title' );
		remove_all_filters( 'the_title' );

		$forum_title   = gdbbx_email_clean_content( bbp_get_forum_title( $forum_id ) );
		$topic_title   = gdbbx_email_clean_content( bbp_get_topic_title( $topic_id ) );
		$reply_title   = gdbbx_email_clean_content( gdbbx_get_reply_title( $reply_id ) );
		$reply_content = gdbbx_email_clean_content( bbp_get_reply_content( $reply_id ) );
		$blog_name     = gdbbx_email_clean_content( get_option( 'blogname' ) );

		return compact( 'topic_title', 'reply_title', 'reply_content', 'blog_name', 'forum_title' );
	}

	public function get_topic_author_and_subscribers( $topic_id, $filter, $_send_to_author = true, $_send_to_subscribers = true, $option_key = '' ) : array {
		$user_ids = array();
		$emails   = array();
		$author   = bbp_get_topic_author_id( $topic_id );

		if ( $_send_to_author && ( empty( $option_key ) || gdbbx_user( $author )->get( $option_key ) ) ) {
			$user_ids[] = $author;
		}

		if ( $_send_to_subscribers ) {
			$user_ids = array_merge( $user_ids, bbp_get_topic_subscribers( $topic_id ) );
		}

		$user_ids = apply_filters( $filter, $user_ids );

		if ( empty( $user_ids ) ) {
			return array();
		}

		$user_ids = array_unique( $user_ids );
		$user_ids = array_filter( $user_ids );

		foreach ( $user_ids as $user_id ) {
			if ( (int) $user_id === get_current_user_id() || $user_id == 0 ) {
				continue;
			}

			$user = get_userdata( $user_id );

			if ( $user ) {
				$emails[] = $user->user_email;
			}
		}

		return array( 'user_ids' => $user_ids, 'emails' => $emails );
	}
}