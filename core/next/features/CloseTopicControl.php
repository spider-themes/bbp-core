<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CloseTopicControl extends Feature {
	public $feature_name = 'close-topic-control';
	public $settings = array(
		'topic_author'       => true,
		'super_admin'        => true,
		'roles'              => array( 'bbp_keymaster' ),
		'form_position'      => 'bbp_theme_before_reply_form_submit_wrapper',
		'notify_author'      => false,
		'notify_subscribers' => false,
		'notify_active'      => false,
		'notify_shortcodes'  => true,
		'notify_content'     => '',
		'notify_subject'     => "[%BLOG_NAME%] The topic '%TOPIC_TITLE%' is now closed"
	);

	public function __construct() {
		parent::__construct();

		add_action( $this->settings['form_position'], array( $this, 'close_topic_checkbox' ), 9 );

		add_action( 'bbp_new_reply', array( $this, 'close_topic_save' ), 1, 2 );
		add_action( 'bbp_edit_reply', array( $this, 'close_topic_save' ), 1, 2 );

		if ( $this->settings['notify_author'] ) {
			UserSettings::instance()->register(
				'topic-closed-notification',
				__( "Moderators closed my topic", "bbp-core" ),
				__( "Receive instant email notification when moderator or keymaster close your topic.", "bbp-core" ),
				'notifications',
				'checkbox',
				false
			);
		}
	}

	public static function instance() : CloseTopicControl {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new CloseTopicControl();
		}

		return $instance;
	}

	public function close_topic_checkbox() {
		$allowed = $this->allowed();

		if ( ! $allowed && is_user_logged_in() ) {
			if ( $this->settings['topic_author'] && bbp_get_topic_author_id() == bbp_get_current_user_id() ) {
				$allowed = true;
			}
		}

		if ( $allowed ) {
			$label = apply_filters( 'gdbbx_close_topic_checkbox_label', __( "Close this topic", "bbp-core" ) );

			?>

            <p>
                <input name="gdbbx_close_topic" id="gdbbx_close_topic" type="checkbox" value="close"/>
                <label for="gdbbx_close_topic"><?php echo esc_html( $label ); ?></label>
            </p>

			<?php
		}
	}

	public function close_topic_save( $reply_id = 0, $topic_id = 0 ) {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$allowed = $this->allowed();

		if ( $allowed ) {
			$allowed = gdbbx_current_user_can_moderate();
		}

		if ( ! $allowed ) {
			if ( $this->settings['topic_author'] && bbp_get_topic_author_id( $topic_id ) == bbp_get_current_user_id() ) {
				$allowed = true;
			}
		}

		if ( $allowed ) {
			if ( isset( $_POST['gdbbx_close_topic'] ) && $_POST['gdbbx_close_topic'] == 'close' ) {
				$done = bbp_close_topic( $topic_id );

				if ( ! is_wp_error( $done ) && $done !== false ) {
					$notify = $this->settings['notify_author'] || $this->settings['notify_subscribers'];

					if ( $notify ) {
						$this->send_notifications( $topic_id );
					}
				}
			}
		}
	}

	public function send_notifications( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		$_send_to_author      = $this->settings['notify_author'];
		$_send_to_subscribers = $this->settings['notify_subscribers'];

		if ( $_send_to_author === false && $_send_to_subscribers === false ) {
			return false;
		}

		$output   = gdbbx_mailer()->get_topic_author_and_subscribers( $topic_id, 'bbp_topic_manual_close_user_ids', $_send_to_author, $_send_to_subscribers, 'topic-closed-notification' );
		$user_ids = isset( $output['user_ids'] ) ? $output['user_ids'] : array();
		$emails   = isset( $output['emails'] ) ? $output['emails'] : array();

		if ( empty( $user_ids ) ) {
			return false;
		}

		$topic_url  = bbp_get_topic_permalink( $topic_id );
		$topic_data = gdbbx_mailer()->get_topic_content( $topic_id );

		/**
		 * @var string $blog_name
		 * @var string $forum_title
		 * @var string $topic_title
		 * @var string $topic_content
		 */
		extract( $topic_data );

		// For plugins to filter messages per reply/topic/user
		$message = _x( "Topic: %TOPIC_TITLE%
In the forum: %FORUM_TITLE%
Has been closed by %CLOSED_USER%.

Topic Link: %TOPIC_LINK%
-----------
You are receiving this email because you subscribed to a forum topic. Login and visit the topic to unsubscribe from these emails.

-----------
Do not reply to this email!", "Email message: notify on topic manual close", "bbp-core" );

		$subject = _x( "[%BLOG_NAME%] The topic '%TOPIC_TITLE%' is now closed", "Email title: notify on topic manual close", "bbp-core" );

		if ( $this->settings['notify_active'] ) {
			$message = ! empty( $this->settings['notify_content'] ) ? $this->settings['notify_content'] : '';
			$subject = ! empty( $this->settings['notify_subject'] ) ? $this->settings['notify_subject'] : '';
		}

		$message = d4p_replace_tags_in_content( $message, array(
			'BLOG_NAME'     => $blog_name,
			'CLOSED_USER'   => gdbbx_get_user_display_name(),
			'FORUM_TITLE'   => $forum_title,
			'FORUM_LINK'    => get_permalink( $forum_id ),
			'TOPIC_TITLE'   => $topic_title,
			'TOPIC_CONTENT' => $topic_content,
			'TOPIC_LINK'    => $topic_url
		) );

		$message = apply_filters( 'bbp_topic_manual_close_mail_message', $message, $topic_id, $forum_id );
		if ( empty( $message ) ) {
			return;
		}

		if ( $this->settings['notify_shortcodes'] ) {
			$message = do_shortcode( $message );
		}

		$subject = d4p_replace_tags_in_content( $subject, array(
			'BLOG_NAME'   => $blog_name,
			'CLOSED_USER' => gdbbx_get_user_display_name(),
			'TOPIC_TITLE' => $topic_title,
			'FORUM_TITLE' => $forum_title
		) );

		$subject = apply_filters( 'bbp_topic_manual_close_mail_title', $subject, $topic_id, $forum_id );
		if ( empty( $subject ) ) {
			return;
		}

		$no_reply   = bbp_get_do_not_reply_address();
		$from_email = apply_filters( 'bbp_topic_manual_close_from_email', $no_reply );
		$headers    = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>' );

		foreach ( $emails as $email ) {
			$headers[] = 'Bcc: ' . $email;
		}

		$headers  = apply_filters( 'bbp_topic_manual_close_mail_headers', $headers );
		$to_email = apply_filters( 'bbp_topic_manual_close_to_email', $no_reply );

		do_action( 'bbp_pre_notify_topic_manual_close', $topic_id, $user_ids );

		wp_mail( $to_email, $subject, $message, $headers );

		do_action( 'bbp_post_notify_topic_manual_close', $topic_id, $user_ids );

		return true;
	}
}
