<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EmailOverrides extends Feature {
	public $feature_name = 'email-overrides';
	public $settings = array(
		'notify_subscribers_override_active'           => false,
		'notify_subscribers_override_shortcodes'       => true,
		'notify_subscribers_override_content'          => '',
		'notify_subscribers_override_subject'          => '[%BLOG_NAME%] New reply for: %TOPIC_TITLE%',
		'notify_subscribers_forum_override_active'     => false,
		'notify_subscribers_forum_override_shortcodes' => true,
		'notify_subscribers_forum_override_content'    => '',
		'notify_subscribers_forum_override_subject'    => '[%BLOG_NAME%] New topic in forum %FORUM_TITLE%: %TOPIC_TITLE%',
		'notify_subscribers_edit_active'               => false,
		'notify_subscribers_edit_shortcodes'           => true,
		'notify_subscribers_edit_content'              => '',
		'notify_subscribers_edit_subject'              => '[%BLOG_NAME%] Topic edited: %TOPIC_TITLE%',
		'notify_subscribers_reply_edit_active'         => false,
		'notify_subscribers_reply_edit_shortcodes'     => true,
		'notify_subscribers_reply_edit_content'        => '',
		'notify_subscribers_reply_edit_subject'        => '[%BLOG_NAME%] Reply edited: %REPLY_TITLE%',
		'notify_moderators_topic_active'               => false,
		'notify_moderators_topic_shortcodes'           => true,
		'notify_moderators_topic_content'              => '',
		'notify_moderators_topic_subject'              => '[%BLOG_NAME%] New topic in forum %FORUM_TITLE%: %TOPIC_TITLE%',
		'notify_moderators_reply_active'               => false,
		'notify_moderators_reply_shortcodes'           => true,
		'notify_moderators_reply_content'              => '',
		'notify_moderators_reply_subject'              => '[%BLOG_NAME%] New reply to %TOPIC_TITLE% in forum %FORUM_TITLE%'
	);

	public function __construct() {
		parent::__construct();

		if ( $this->get( 'notify_moderators_topic_active' ) ) {
			add_filter( 'bbp_new_topic_moderators_mail_message', array(
				$this,
				'new_topic_moderators_mail_message'
			), 10, 3 );
			add_filter( 'bbp_new_topic_moderators_mail_title', array(
				$this,
				'new_topic_moderators_mail_title'
			), 10, 3 );
		}

		if ( $this->get( 'notify_moderators_reply_active' ) ) {
			add_filter( 'bbp_new_reply_moderators_mail_message', array(
				$this,
				'new_reply_moderators_mail_message'
			), 10, 4 );
			add_filter( 'bbp_new_reply_moderators_mail_title', array(
				$this,
				'new_reply_moderators_mail_title'
			), 10, 4 );
		}

		if ( $this->get( 'notify_subscribers_reply_edit_active' ) ) {
			add_filter( 'bbp_reply_edit_subscription_mail_message', array(
				$this,
				'subscription_reply_edit_mail_message'
			), 10, 2 );
			add_filter( 'bbp_reply_edit_subscription_mail_title', array(
				$this,
				'subscription_reply_edit_mail_title'
			), 10, 2 );
		}

		if ( $this->get( 'notify_subscribers_edit_active' ) ) {
			add_filter( 'bbp_topic_edit_subscription_mail_message', array(
				$this,
				'subscription_topic_edit_mail_message'
			), 10, 2 );
			add_filter( 'bbp_topic_edit_subscription_mail_title', array(
				$this,
				'subscription_topic_edit_mail_title'
			), 10, 2 );
		}

		if ( $this->get( 'notify_subscribers_forum_override_active' ) ) {
			add_filter( 'bbp_forum_subscription_mail_message', array(
				$this,
				'subscription_forum_mail_message'
			), 10, 3 );
			add_filter( 'bbp_forum_subscription_mail_title', array( $this, 'subscription_forum_mail_title' ), 10, 3 );
		}

		if ( $this->get( 'notify_subscribers_override_active' ) ) {
			add_filter( 'bbp_subscription_mail_message', array( $this, 'subscription_mail_message' ), 10, 3 );
			add_filter( 'bbp_subscription_mail_title', array( $this, 'subscription_mail_title' ), 10, 3 );
		}
	}

	public static function instance() : EmailOverrides {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new EmailOverrides();
		}

		return $instance;
	}

	public function subscription_reply_edit_mail_message( $message, $reply_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_reply_edit_content' ) );

		if ( ! empty( $start ) ) {
			$topic_id = bbp_get_reply_topic_id( $reply_id );

			$tags = apply_filters( 'bbpc_tags_subscription_reply_edit_mail_message', array(
				'BLOG_NAME'     => get_option( 'blogname' ),
				'FORUM_TITLE'   => bbp_get_forum_title( bbp_get_topic_forum_id( $topic_id ) ),
				'TOPIC_TITLE'   => bbp_get_topic_title( $topic_id ),
				'REPLY_TITLE'   => bbpc_get_reply_title( $reply_id ),
				'REPLY_LINK'    => bbp_get_reply_url( $reply_id ),
				'REPLY_EDITOR'  => bbpc_get_user_display_name(),
				'REPLY_AUTHOR'  => bbp_get_reply_author_display_name( $reply_id ),
				'REPLY_CONTENT' => bbp_get_reply_content( $reply_id ),
				'REPLY_EDIT'    => bbpc_mailer()->reply_revision_log( $reply_id )
			), $reply_id );

			if ( $this->get( 'notify_subscribers_reply_edit_shortcodes' ) ) {
				$start                 = do_shortcode( $start );
				$tags['REPLY_CONTENT'] = do_shortcode( $tags['REPLY_CONTENT'] );
			}

			$message = d4p_replace_tags_in_content( $start, $tags );
			$message = bbpc_email_clean_content( $message );
		}

		return $message;
	}

	public function subscription_reply_edit_mail_title( $title, $reply_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_reply_edit_subject' ) );

		if ( ! empty( $start ) ) {
			$topic_id = bbp_get_reply_topic_id( $reply_id );

			$tags = apply_filters( 'bbpc_tags_subscription_reply_edit_mail_title', array(
				'BLOG_NAME'   => get_option( 'blogname' ),
				'TOPIC_TITLE' => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'REPLY_TITLE' => strip_tags( bbpc_get_reply_title( $reply_id ) )
			), $reply_id );

			$title = d4p_replace_tags_in_content( $start, $tags );
			$title = bbpc_email_clean_content( $title, apply_filters( 'bbpc_cleanup_subscription_reply_edit_mail_title_strip_tags', true ) );
		}

		return $title;
	}

	public function subscription_topic_edit_mail_message( $message, $topic_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_edit_content' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_subscription_topic_edit_mail_message', array(
				'BLOG_NAME'     => get_option( 'blogname' ),
				'FORUM_TITLE'   => bbp_get_forum_title( bbp_get_topic_forum_id( $topic_id ) ),
				'TOPIC_TITLE'   => bbp_get_topic_title( $topic_id ),
				'TOPIC_LINK'    => get_permalink( $topic_id ),
				'TOPIC_EDITOR'  => bbpc_get_user_display_name(),
				'TOPIC_AUTHOR'  => bbp_get_topic_author_display_name( $topic_id ),
				'TOPIC_CONTENT' => bbp_get_topic_content( $topic_id ),
				'TOPIC_EDIT'    => bbpc_mailer()->topic_revision_log( $topic_id )
			), $topic_id );

			if ( $this->get( 'notify_subscribers_edit_shortcodes' ) ) {
				$start                 = do_shortcode( $start );
				$tags['TOPIC_CONTENT'] = do_shortcode( $tags['TOPIC_CONTENT'] );
			}

			$message = d4p_replace_tags_in_content( $start, $tags );
			$message = bbpc_email_clean_content( $message, apply_filters( 'bbpc_cleanup_subscription_topic_edit_mail_message_strip_tags', false ) );
		}

		return $message;
	}

	public function subscription_topic_edit_mail_title( $title, $topic_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_edit_subject' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'subscription_topic_edit_mail_title', array(
				'BLOG_NAME'   => get_option( 'blogname' ),
				'TOPIC_TITLE' => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'FORUM_TITLE' => bbp_get_forum_title( bbp_get_topic_forum_id( $topic_id ) )
			), $topic_id );

			$title = d4p_replace_tags_in_content( $start, $tags );
			$title = bbpc_email_clean_content( $title );
		}

		return $title;
	}

	public function subscription_mail_message( $message, $reply_id, $topic_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_override_content' ) );

		if ( ! empty( $start ) ) {
			$tags = $tags = apply_filters( 'bbpc_tags_subscription_mail_message', array(
				'BLOG_NAME'     => get_option( 'blogname' ),
				'FORUM_TITLE'   => bbp_get_forum_title( bbp_get_topic_forum_id( $topic_id ) ),
				'TOPIC_TITLE'   => bbp_get_topic_title( $topic_id ),
				'TOPIC_LINK'    => get_permalink( $topic_id ),
				'TOPIC_AUTHOR'  => bbp_get_topic_author_display_name( $topic_id ),
				'REPLY_LINK'    => bbp_get_reply_url( $reply_id ),
				'REPLY_AUTHOR'  => bbp_get_reply_author_display_name( $reply_id ),
				'REPLY_CONTENT' => bbp_get_reply_content( $reply_id ),
				'REPLY_TITLE'   => bbpc_get_reply_title( $reply_id )
			), $reply_id, $topic_id );

			if ( $this->get( 'notify_subscribers_override_shortcodes' ) ) {
				$start                 = do_shortcode( $start );
				$tags['REPLY_CONTENT'] = do_shortcode( $tags['REPLY_CONTENT'] );
			}

			$message = d4p_replace_tags_in_content( $start, $tags );
			$message = bbpc_email_clean_content( $message, apply_filters( 'bbpc_cleanup_subscription_mail_message_strip_tags', true ) );
		}

		return $message;
	}

	public function subscription_mail_title( $title, $reply_id, $topic_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_override_subject' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_subscription_topic_edit_mail_title', array(
				'BLOG_NAME'   => get_option( 'blogname' ),
				'TOPIC_TITLE' => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'FORUM_TITLE' => bbp_get_forum_title( bbp_get_topic_forum_id( $topic_id ) )
			), $reply_id, $topic_id );

			$title = d4p_replace_tags_in_content( $start, $tags );
			$title = bbpc_email_clean_content( $title );
		}

		return $title;
	}

	public function subscription_forum_mail_message( $message, $topic_id, $forum_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_forum_override_content' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_subscription_forum_mail_message', array(
				'BLOG_NAME'     => get_option( 'blogname' ),
				'TOPIC_TITLE'   => bbp_get_topic_title( $topic_id ),
				'TOPIC_LINK'    => get_permalink( $topic_id ),
				'TOPIC_AUTHOR'  => bbp_get_topic_author_display_name( $topic_id ),
				'TOPIC_CONTENT' => bbp_get_topic_content( $topic_id ),
				'FORUM_LINK'    => bbp_get_forum_permalink( $forum_id ),
				'FORUM_TITLE'   => bbp_get_forum_title( $forum_id )
			), $topic_id, $forum_id );

			if ( $this->get( 'notify_subscribers_forum_override_shortcodes' ) ) {
				$start                 = do_shortcode( $start );
				$tags['TOPIC_CONTENT'] = do_shortcode( $tags['TOPIC_CONTENT'] );
			}

			$message = d4p_replace_tags_in_content( $start, $tags );
			$message = bbpc_email_clean_content( $message, apply_filters( 'bbpc_cleanup_subscription_forum_mail_message_strip_tags', true ) );
		}

		return $message;
	}

	public function subscription_forum_mail_title( $title, $topic_id, $forum_id ) : string {
		$start = trim( $this->get( 'notify_subscribers_forum_override_subject' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_subscription_forum_mail_title', array(
				'BLOG_NAME'   => get_option( 'blogname' ),
				'TOPIC_TITLE' => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'FORUM_TITLE' => strip_tags( bbp_get_forum_title( $forum_id ) )
			), $topic_id, $forum_id );

			$title = d4p_replace_tags_in_content( $start, $tags );
			$title = bbpc_email_clean_content( $title );
		}

		return $title;
	}

	public function new_topic_moderators_mail_message( $message, $topic_id, $forum_id ) : string {
		$start = trim( $this->get( 'notify_moderators_topic_content' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_new_topic_moderators_mail_message', array(
				'BLOG_NAME'     => get_option( 'blogname' ),
				'TOPIC_TITLE'   => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'TOPIC_LINK'    => get_permalink( $topic_id ),
				'TOPIC_AUTHOR'  => bbp_get_topic_author_display_name( $topic_id ),
				'TOPIC_CONTENT' => bbp_get_topic_content( $topic_id ),
				'FORUM_TITLE'   => bbp_get_forum_title( $forum_id ),
				'FORUM_LINK'    => get_permalink( $forum_id )
			), $topic_id, $forum_id );

			if ( $this->get( 'notify_moderators_topic_shortcodes' ) ) {
				$start                 = do_shortcode( $start );
				$tags['TOPIC_CONTENT'] = do_shortcode( $tags['TOPIC_CONTENT'] );
			}

			$message = d4p_replace_tags_in_content( $start, $tags );
			$message = bbpc_email_clean_content( $message, apply_filters( 'bbpc_cleanup_new_topic_moderators_mail_message_strip_tags', true ) );
		}

		return $message;
	}

	public function new_topic_moderators_mail_title( $title, $topic_id, $forum_id ) : string {
		$start = trim( $this->get( 'notify_moderators_topic_subject' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_new_topic_moderators_mail_title', array(
				'BLOG_NAME'   => get_option( 'blogname' ),
				'TOPIC_TITLE' => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'FORUM_TITLE' => strip_tags( bbp_get_forum_title( $forum_id ) )
			), $topic_id, $forum_id );

			$title = d4p_replace_tags_in_content( $start, $tags );
			$title = bbpc_email_clean_content( $title );
		}

		return $title;
	}

	public function new_reply_moderators_mail_message( $message, $reply_id, $topic_id, $forum_id ) : string {
		$start = trim( $this->get( 'notify_moderators_reply_content' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_new_reply_moderators_mail_message', array(
				'BLOG_NAME'     => get_option( 'blogname' ),
				'REPLY_TITLE'   => strip_tags( bbpc_get_reply_title( $reply_id ) ),
				'REPLY_LINK'    => bbp_get_reply_url( $reply_id ),
				'REPLY_AUTHOR'  => bbp_get_reply_author_display_name( $reply_id ),
				'TOPIC_TITLE'   => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'TOPIC_AUTHOR'  => bbp_get_topic_author_display_name( $topic_id ),
				'TOPIC_CONTENT' => bbp_get_topic_content( $topic_id ),
				'FORUM_TITLE'   => bbp_get_forum_title( $forum_id ),
				'FORUM_LINK'    => get_permalink( $forum_id )
			), $topic_id, $forum_id );

			if ( $this->get( 'notify_moderators_topic_shortcodes' ) ) {
				$start                 = do_shortcode( $start );
				$tags['TOPIC_CONTENT'] = do_shortcode( $tags['TOPIC_CONTENT'] );
			}

			$message = d4p_replace_tags_in_content( $start, $tags );
			$message = bbpc_email_clean_content( $message, apply_filters( 'bbpc_cleanup_new_reply_moderators_mail_message_strip_tags', true ) );
		}

		return $message;
	}

	public function new_reply_moderators_mail_title( $title, $reply_id, $topic_id, $forum_id ) : string {
		$start = trim( $this->get( 'notify_moderators_reply_subject' ) );

		if ( ! empty( $start ) ) {
			$tags = apply_filters( 'bbpc_tags_new_reply_moderators_mail_title', array(
				'BLOG_NAME'   => get_option( 'blogname' ),
				'REPLY_TITLE' => strip_tags( bbpc_get_reply_title( $reply_id ) ),
				'TOPIC_TITLE' => strip_tags( bbp_get_topic_title( $topic_id ) ),
				'FORUM_TITLE' => strip_tags( bbp_get_forum_title( $forum_id ) )
			), $topic_id, $forum_id );

			$title = d4p_replace_tags_in_content( $start, $tags );
			$title = bbpc_email_clean_content( $title );
		}

		return $title;
	}
}
