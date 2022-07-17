<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notifications extends Feature {
	public $feature_name = 'notifications';
	public $settings = array(
		'new_topic_keymaster' => false,
		'new_topic_moderator' => false,
		'new_reply_keymaster' => false,
		'new_reply_moderator' => false,
		'topic_on_edit'       => false,
		'reply_on_edit'       => false
	);

	public function __construct() {
		parent::__construct();

		if ( $this->get( 'topic_on_edit', false ) ) {
			add_action( 'bbp_edit_topic_post_extras', array( $this, 'notify_subscribers_on_topic_edit' ) );
			add_action( 'bbp_theme_before_topic_form_submit_wrapper', array(
				$this,
				'topic_notify_on_update_checkbox'
			) );
		}

		if ( $this->get( 'reply_on_edit', false ) ) {
			add_action( 'bbp_edit_reply_post_extras', array( $this, 'notify_subscribers_on_reply_edit' ) );
			add_action( 'bbp_theme_before_reply_form_submit_wrapper', array(
				$this,
				'reply_notify_on_update_checkbox'
			) );
		}

		if ( $this->get( 'new_topic_keymaster', false ) || $this->get( 'new_topic_moderator', false ) ) {
			add_action( 'bbp_new_topic', array( $this, 'notify_moderators_on_new_topic' ), 15, 2 );
		}

		if ( $this->get( 'new_reply_keymaster', false ) || $this->get( 'new_reply_moderator', false ) ) {
			add_action( 'bbp_new_reply', array( $this, 'notify_moderators_on_new_reply' ), 15, 3 );
		}
	}

	public static function instance() : Notifications {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Notifications();
		}

		return $instance;
	}

	public function topic_notify_on_update_checkbox() {
		if ( bbp_is_topic_edit() ) {
			include( bbpc_get_template_part( 'bbpc-form-notify-on-topic-edit.php' ) );
		}
	}

	public function reply_notify_on_update_checkbox() {
		if ( bbp_is_reply_edit() ) {
			include( bbpc_get_template_part( 'bbpc-form-notify-on-reply-edit.php' ) );
		}
	}

	public function notify_subscribers_on_reply_edit( $reply_id ) {
		if ( ! bbp_is_subscriptions_active() ) {
			return;
		}

		$reply_id = bbp_get_reply_id( $reply_id );
		$topic_id = bbp_get_reply_topic_id( $reply_id );

		if ( ! bbp_is_topic_published( $topic_id ) ) {
			return;
		}

		$_send_to_author       = isset( $_POST['bbpc_notify_on_edit_author'] ) && $_POST['bbpc_notify_on_edit_author'] == 1;
		$_send_to_topic_author = isset( $_POST['bbpc_notify_on_edit_topic_author'] ) && $_POST['bbpc_notify_on_edit_topic_author'] == 1;
		$_send_to_subscribers  = isset( $_POST['bbpc_notify_on_edit_subscribers'] ) && $_POST['bbpc_notify_on_edit_subscribers'] == 1;

		if ( $_send_to_topic_author === false && $_send_to_author === false && $_send_to_subscribers === false ) {
			return;
		}

		$reply_editor_name = bbpc_get_user_display_name();

		$reply_url  = bbp_get_reply_url( $reply_id );
		$topic_url  = bbp_get_topic_permalink( $topic_id );
		$reply_data = bbpc_mailer()->get_reply_content( $reply_id, $topic_id );

		/**
		 * @var string $blog_name
		 * @var string $forum_title
		 * @var string $topic_title
		 * @var string $reply_title
		 * @var string $reply_content
		 */
		extract( $reply_data );

		// For plugins to filter messages per reply/topic/user
		$message = _x( "%REPLY_EDITOR% edited reply:

%REPLY_CONTENT%

Reply Link: %REPLY_LINK%

-----------
You are receiving this email because you subscribed to a forum topic:
%TOPIC_TITLE%
%TOPIC_LINK%

Login and visit the topic or your profile page to unsubscribe from these emails.", "Email message: notify subscribers on reply edit", "bbp-core" );

		$message = d4p_replace_tags_in_content( $message, array(
			'BLOG_NAME'     => $blog_name,
			'FORUM_TITLE'   => $forum_title,
			'FORUM_LINK'    => get_permalink( bbp_get_topic_forum_id( $topic_id ) ),
			'TOPIC_TITLE'   => $topic_title,
			'TOPIC_LINK'    => $topic_url,
			'REPLY_TITLE'   => $reply_title,
			'REPLY_LINK'    => $reply_url,
			'REPLY_EDITOR'  => $reply_editor_name,
			'REPLY_CONTENT' => $reply_content
		) );

		$message = apply_filters( 'bbp_reply_edit_subscription_mail_message', $message, $reply_id );
		if ( empty( $message ) ) {
			return;
		}

		$subject = _x( "[%BLOG_NAME%] Reply edited: %REPLY_TITLE%", "Email title: notify subscribers on reply edit", "bbp-core" );
		$subject = d4p_replace_tags_in_content( $subject, array(
			'BLOG_NAME'   => $blog_name,
			'REPLY_TITLE' => $reply_title,
			'TOPIC_TITLE' => $topic_title,
			'FORUM_TITLE' => $forum_title
		) );

		$subject = apply_filters( 'bbp_reply_edit_subscription_mail_title', $subject, $reply_id );
		if ( empty( $subject ) ) {
			return;
		}

		$no_reply   = bbp_get_do_not_reply_address();
		$from_email = apply_filters( 'bbp_reply_edit_subscription_from_email', $no_reply );
		$headers    = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>' );

		$user_ids = array();

		if ( $_send_to_author ) {
			$user_ids[] = bbp_get_reply_author_id( $reply_id );
		}

		if ( $_send_to_topic_author ) {
			$user_ids[] = bbp_get_topic_author_id( $topic_id );
		}

		if ( $_send_to_subscribers ) {
			$user_ids = array_merge( $user_ids, bbp_get_topic_subscribers( $topic_id ) );
		}

		$user_ids = apply_filters( 'bbp_reply_edit_subscription_user_ids', $user_ids );
		if ( empty( $user_ids ) ) {
			return;
		}

		$user_ids = array_unique( $user_ids );
		$user_ids = array_filter( $user_ids );

		foreach ( (array) $user_ids as $user_id ) {
			if ( (int) $user_id === (int) get_current_user_id() || $user_id == 0 ) {
				continue;
			}

			$user = get_userdata( $user_id );

			if ( $user ) {
				$headers[] = 'Bcc: ' . get_userdata( $user_id )->user_email;
			}
		}

		$headers  = apply_filters( 'bbp_reply_edit_subscription_mail_headers', $headers );
		$to_email = apply_filters( 'bbp_reply_edit_subscription_to_email', $no_reply );

		do_action( 'bbp_pre_notify_reply_edit_subscribers', $reply_id, $user_ids );

		wp_mail( $to_email, $subject, $message, $headers );

		do_action( 'bbp_post_notify_reply_edit_subscribers', $reply_id, $user_ids );
	}

	public function notify_subscribers_on_topic_edit( $topic_id ) {
		if ( ! bbp_is_subscriptions_active() ) {
			return;
		}

		$topic_id = bbp_get_topic_id( $topic_id );

		if ( ! bbp_is_topic_published( $topic_id ) ) {
			return;
		}

		$_send_to_author      = isset( $_POST['bbpc_notify_on_edit_author'] ) && $_POST['bbpc_notify_on_edit_author'] == 1;
		$_send_to_subscribers = isset( $_POST['bbpc_notify_on_edit_subscribers'] ) && $_POST['bbpc_notify_on_edit_subscribers'] == 1;

		if ( $_send_to_author === false && $_send_to_subscribers === false ) {
			return;
		}

		$output   = bbpc_mailer()->get_topic_author_and_subscribers( $topic_id, 'bbp_topic_edit_subscription_user_ids', $_send_to_author, $_send_to_subscribers );
		$user_ids = isset( $output['user_ids'] ) ? $output['user_ids'] : array();
		$emails   = isset( $output['emails'] ) ? $output['emails'] : array();

		if ( empty( $user_ids ) ) {
			return;
		}

		$topic_editor_name = bbpc_get_user_display_name();

		$topic_url  = bbp_get_topic_permalink( $topic_id );
		$topic_data = bbpc_mailer()->get_topic_content( $topic_id );

		/**
		 * @var string $blog_name
		 * @var string $forum_title
		 * @var string $topic_title
		 * @var string $topic_content
		 */
		extract( $topic_data );

		// For plugins to filter messages per reply/topic/user
		$message = _x( "%TOPIC_EDITOR% edited topic:

%TOPIC_CONTENT%

Topic Link: %TOPIC_LINK%

-----------

You are receiving this email because you subscribed to a forum topic.

Login and visit the topic or your profile page to unsubscribe from these emails.", "Email message: notify subscribers on topic edit", "bbp-core" );

		$message = d4p_replace_tags_in_content( $message, array(
			'BLOG_NAME'     => $blog_name,
			'FORUM_TITLE'   => $forum_title,
			'FORUM_LINK'    => get_permalink( bbp_get_topic_forum_id( $topic_id ) ),
			'TOPIC_TITLE'   => $topic_title,
			'TOPIC_EDITOR'  => $topic_editor_name,
			'TOPIC_LINK'    => $topic_url,
			'TOPIC_CONTENT' => $topic_content
		) );

		$message = apply_filters( 'bbp_topic_edit_subscription_mail_message', $message, $topic_id );
		if ( empty( $message ) ) {
			return;
		}

		$subject = _x( "[%BLOG_NAME%] Topic edited: %TOPIC_TITLE%", "Email title: notify subscribers on topic edit", "bbp-core" );
		$subject = d4p_replace_tags_in_content( $subject, array(
			'BLOG_NAME'   => $blog_name,
			'TOPIC_TITLE' => $topic_title,
			'FORUM_TITLE' => $forum_title
		) );

		$subject = apply_filters( 'bbp_topic_edit_subscription_mail_title', $subject, $topic_id );
		if ( empty( $subject ) ) {
			return;
		}

		$no_reply   = bbp_get_do_not_reply_address();
		$from_email = apply_filters( 'bbp_topic_edit_subscription_from_email', $no_reply );
		$headers    = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>' );

		foreach ( $emails as $email ) {
			$headers[] = 'Bcc: ' . $email;
		}

		$headers  = apply_filters( 'bbp_topic_edit_subscription_mail_headers', $headers );
		$to_email = apply_filters( 'bbp_topic_edit_subscription_to_email', $no_reply );

		do_action( 'bbp_pre_notify_topic_edit_subscribers', $topic_id, $user_ids );

		wp_mail( $to_email, $subject, $message, $headers );

		do_action( 'bbp_post_notify_topic_edit_subscribers', $topic_id, $user_ids );
	}

	public function notify_moderators_on_new_topic( $topic_id = 0, $forum_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_forum_id( $forum_id );

		if ( ! bbp_is_topic_published( $topic_id ) ) {
			return;
		}

		$topic_author_id   = bbp_get_topic_author_id( $topic_id );
		$topic_author_name = bbp_get_topic_author_display_name( $topic_id );

		$topic_url  = bbp_get_topic_permalink( $topic_id );
		$topic_data = bbpc_mailer()->get_topic_content( $topic_id );

		/**
		 * @var string $blog_name
		 * @var string $forum_title
		 * @var string $topic_title
		 * @var string $topic_content
		 */
		extract( $topic_data );

		// For plugins to filter messages per reply/topic/user
		$message = _x( "%TOPIC_AUTHOR% posted new topic:

%TOPIC_CONTENT%

Topic Link: %TOPIC_LINK%

-----------

This email is sent to keymasters and moderators when new topic is created.", "Email message: notify moderators on new topic", "bbp-core" );

		$message = d4p_replace_tags_in_content( $message, array(
			'BLOG_NAME'     => $blog_name,
			'FORUM_LINK'    => get_permalink( $forum_id ),
			'FORUM_TITLE'   => $forum_title,
			'TOPIC_LINK'    => $topic_url,
			'TOPIC_TITLE'   => $topic_title,
			'TOPIC_AUTHOR'  => $topic_author_name,
			'TOPIC_CONTENT' => $topic_content
		) );

		$message = apply_filters( 'bbp_new_topic_moderators_mail_message', $message, $topic_id, $forum_id );
		if ( empty( $message ) ) {
			return;
		}

		$subject = _x( "[%BLOG_NAME%] New topic posted: %TOPIC_TITLE%", "Email title: notify moderators on new topic", "bbp-core" );
		$subject = d4p_replace_tags_in_content( $subject, array(
			'BLOG_NAME'   => $blog_name,
			'TOPIC_TITLE' => $topic_title,
			'FORUM_TITLE' => $forum_title
		) );

		$subject = apply_filters( 'bbp_new_topic_moderators_mail_title', $subject, $topic_id, $forum_id );
		if ( empty( $subject ) ) {
			return;
		}

		$no_reply   = bbp_get_do_not_reply_address();
		$from_email = apply_filters( 'bbp_new_topic_moderators_from_email', $no_reply );
		$headers    = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>' );

		$send_to_users = array();
		$users_lists   = array();
		$subscribers   = bbp_get_forum_subscribers( $forum_id );

		if ( $this->get( 'new_topic_keymaster', false ) ) {
			$users_lists = array_merge( $users_lists, bbpc_get_keymasters() );
		}

		if ( $this->get( 'new_topic_moderator', false ) ) {
			$users_lists = array_merge( $users_lists, bbpc_get_moderators() );
		}

		foreach ( $users_lists as $user ) {
			if ( ( is_array( $subscribers ) && in_array( $user->ID, $subscribers ) ) || $user->ID == $topic_author_id ) {
				continue;
			}

			$send_to_users[] = $user->user_email;
		}

		$send_to_users = array_unique( $send_to_users );
		$send_to_users = array_filter( $send_to_users );

		$send_to_users = apply_filters( 'bbp_new_topic_moderators_emails', $send_to_users );

		if ( ! empty( $send_to_users ) ) {
			foreach ( $send_to_users as $email ) {
				$headers[] = 'Bcc: ' . $email;
			}
		}

		$headers  = apply_filters( 'bbp_new_topic_moderators_mail_headers', $headers );
		$to_email = apply_filters( 'bbp_new_topic_moderators_to_email', $no_reply );

		do_action( 'bbp_pre_notify_new_topic_moderators', $topic_id, $send_to_users );

		wp_mail( $to_email, $subject, $message, $headers );

		do_action( 'bbp_post_notify_new_topic_moderators', $topic_id, $send_to_users );
	}

	public function notify_moderators_on_new_reply( $reply_id = 0, $topic_id = 0, $forum_id = 0 ) {
		$reply_id = bbp_get_reply_id( $reply_id );
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_forum_id( $forum_id );

		if ( ! bbp_is_reply_published( $reply_id ) ) {
			return;
		}

		$reply_author_id   = bbp_get_reply_author_id( $reply_id );
		$reply_author_name = bbp_get_reply_author_display_name( $reply_id );

		$reply_url  = bbp_get_reply_url( $reply_id );
		$topic_url  = bbp_get_topic_permalink( $topic_id );
		$topic_data = bbpc_mailer()->get_reply_content( $reply_id, $topic_id );

		/**
		 * @var string $blog_name
		 * @var string $forum_title
		 * @var string $topic_title
		 * @var string $reply_title
		 * @var string $reply_content
		 */
		extract( $topic_data );

		// For plugins to filter messages per reply/topic/user
		$message = _x( "%REPLY_AUTHOR% posted new reply:

%REPLY_CONTENT%

Topic Link: %REPLY_LINK%

-----------

This email is sent to keymasters and moderators when new topic is created.", "Email message: notify moderators on new reply", "bbp-core" );
		$message = d4p_replace_tags_in_content( $message, array(
			'BLOG_NAME'     => $blog_name,
			'FORUM_LINK'    => get_permalink( $forum_id ),
			'FORUM_TITLE'   => $forum_title,
			'REPLY_LINK'    => $reply_url,
			'REPLY_TITLE'   => $reply_title,
			'REPLY_AUTHOR'  => $reply_author_name,
			'REPLY_CONTENT' => $reply_content,
			'TOPIC_LINK'    => $topic_url,
			'TOPIC_TITLE'   => $topic_title
		) );

		$message = apply_filters( 'bbp_new_reply_moderators_mail_message', $message, $reply_id, $topic_id, $forum_id );
		if ( empty( $message ) ) {
			return;
		}

		$subject = _x( "[%BLOG_NAME%] New reply posted for topic: %TOPIC_TITLE%", "Email title: notify moderators on new reply", "bbp-core" );
		$subject = d4p_replace_tags_in_content( $subject, array(
			'BLOG_NAME'   => $blog_name,
			'TOPIC_TITLE' => $topic_title,
			'FORUM_TITLE' => $forum_title
		) );

		$subject = apply_filters( 'bbp_new_reply_moderators_mail_title', $subject, $reply_id, $topic_id, $forum_id );
		if ( empty( $subject ) ) {
			return;
		}

		$no_reply   = bbp_get_do_not_reply_address();
		$from_email = apply_filters( 'bbp_new_reply_moderators_from_email', $no_reply );
		$headers    = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>' );

		$send_to_users = array();
		$users_lists   = array();
		$subscribers   = bbp_get_topic_subscribers( $topic_id );

		if ( $this->get( 'new_reply_keymaster', false ) ) {
			$users_lists = array_merge( $users_lists, bbpc_get_keymasters() );
		}

		if ( $this->get( 'new_reply_moderator', false ) ) {
			$users_lists = array_merge( $users_lists, bbpc_get_moderators() );
		}

		foreach ( $users_lists as $user ) {
			if ( ( is_array( $subscribers ) && in_array( $user->ID, $subscribers ) ) || $user->ID == $reply_author_id ) {
				continue;
			}

			$send_to_users[] = $user->user_email;
		}

		$send_to_users = array_unique( $send_to_users );
		$send_to_users = array_filter( $send_to_users );

		$send_to_users = apply_filters( 'bbp_new_reply_moderators_emails', $send_to_users );

		if ( ! empty( $send_to_users ) ) {
			foreach ( $send_to_users as $email ) {
				$headers[] = 'Bcc: ' . $email;
			}
		}

		$headers  = apply_filters( 'bbp_new_reply_moderators_mail_headers', $headers );
		$to_email = apply_filters( 'bbp_new_reply_moderators_to_email', $no_reply );

		do_action( 'bbp_pre_notify_new_reply_moderators', $reply_id, $send_to_users );

		wp_mail( $to_email, $subject, $message, $headers );

		do_action( 'bbp_post_notify_new_reply_moderators', $reply_id, $send_to_users );
	}
}