<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;
use Dev4Press\Plugin\GDBBX\Basic\Enqueue;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AutoCloseTopics extends Feature {
	public $feature_name = 'auto-close-topics';
	public $settings = array(
		'status'                     => false,
		'active'                     => false,
		'notice'                     => true,
		'days'                       => 90,
		'modify_topic_form'          => false,
		'modify_reply_form'          => false,
		'modify_topic_form_location' => 'bbp_theme_after_topic_form_content',
		'modify_reply_form_location' => 'bbp_theme_after_reply_form_content',
		'modify_author'              => false,
		'modify_moderators'          => true,
		'notify_author'              => false,
		'notify_subscribers'         => false,
		'notify_active'              => false,
		'notify_shortcodes'          => true,
		'notify_content'             => '',
		'notify_subject'             => "[%BLOG_NAME%] The topic '%TOPIC_TITLE%' is now closed"
	);

	public $limit = 100;

	public function __construct() {
		parent::__construct();

		if ( is_user_logged_in() ) {
			add_action( 'gdbbx_template', array( $this, 'loader' ) );
		}

		add_action( 'gdbbx_daily_maintenance_job', array( $this, 'job' ) );
		add_action( 'gdbbx_cron_auto_close_topics', array( $this, 'auto_close_topics' ) );
		add_action( 'bbp_theme_before_reply_form_notices', array( $this, 'closing_notice' ) );

		if ( $this->settings['notify_author'] ) {
			UserSettings::instance()->register(
				'topic-auto-closed-notification',
				__( "My topic is automatically closed", "bbp-core" ),
				__( "Receive instant email notification your topic is closed due to the age or inactivity.", "bbp-core" ),
				'notifications',
				'checkbox',
				false
			);
		}
	}

	public static function instance() : AutoCloseTopics {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new AutoCloseTopics();
		}

		return $instance;
	}

	public function loader() {
		if ( $this->settings['modify_topic_form'] ) {
			add_action( 'bbp_theme_before_topic_form', array( $this, 'mod_topic_form' ) );

			add_action( 'bbp_new_topic', array( $this, 'topic_save' ) );
			add_action( 'bbp_edit_topic', array( $this, 'topic_save' ) );
		}

		if ( $this->settings['modify_reply_form'] ) {
			add_action( 'bbp_theme_before_reply_form', array( $this, 'mod_reply_form' ) );

			add_action( 'bbp_new_reply', array( $this, 'reply_save' ), 10, 2 );
			add_action( 'bbp_edit_reply', array( $this, 'reply_save' ), 10, 2 );
		}
	}

	public function job() {
		$this->auto_close_topics( true );
	}

	public function auto_close_topics( $first = false ) {
		if ( $first ) {
			$this->clear_auto_close_skip();
		}

		$this->run_auto_close();
	}

	public static function minimum_days_allowed() {
		return apply_filters( 'gdbbx_auto_close_topic_minimum_days_allowed', 7 );
	}

	public function topic_save( $topic_id ) {
		$this->save_topic_settings( $topic_id );
	}

	public function reply_save( $reply_id, $topic_id ) {
		$this->save_topic_settings( $topic_id );
	}

	public function clear_auto_close_skip() {
		$sql = "DELETE FROM " . gdbbx_db()->wpdb()->postmeta . " WHERE meta_key = '_gdbbx_auto_close_skip'";

		gdbbx_db()->query( $sql );
	}

	public function run_auto_close() {
		set_time_limit( 0 );

		$notify = $this->settings['notify_author'] || $this->settings['notify_subscribers'];

		$sql = $this->build_auto_close_query();

		$raw   = gdbbx_db()->get_results( $sql );
		$total = gdbbx_db()->get_found_rows();

		$forums = array();

		foreach ( $raw as $row ) {
			if ( ! isset( $forums[ $row->forum_id ] ) ) {
				$forums[ $row->forum_id ] = $this->auto_close_forum_rule( $row->forum_id );
			}
		}

		foreach ( $raw as $row ) {
			$rule = $this->process_auto_close_rule( $forums[ $row->forum_id ], $row->topic_id );

			if ( $rule['active'] && $rule['days'] < $row->last_active ) {
				$done = bbp_close_topic( $row->topic_id );

				if ( ! is_wp_error( $done ) ) {
					update_post_meta( $row->topic_id, '_gdbbx_auto_closed', time() );

					if ( $notify ) {
						$this->send_notifications( $row->topic_id );
					}
				}
			} else {
				update_post_meta( $row->topic_id, '_gdbbx_auto_close_skip', time() );
			}
		}

		if ( $total > count( $raw ) ) {
			wp_schedule_single_event( time() + 5, 'gdbbx_cron_auto_close_topics' );
		} else {
			$this->clear_auto_close_skip();
		}
	}

	public function auto_close_forum_rule( $forum_id ) : array {
		$_meta = gdbbx_forum( $forum_id )->topic_auto_close()->all();

		if ( ! isset( $_meta['active'] ) || $_meta['active'] == 'default' || empty( $_meta['active'] ) ) {
			$_meta['active'] = $this->settings['active'];
		} else {
			$_meta['active'] = $_meta['active'] == 'yes';
		}

		if ( ! isset( $_meta['notice'] ) || $_meta['notice'] == 'default' || empty( $_meta['notice'] ) ) {
			$_meta['notice'] = $this->settings['notice'];
		} else {
			$_meta['notice'] = $_meta['notice'] == 'yes';
		}

		if ( isset( $_meta['days'] ) && empty( $_meta['days'] ) ) {
			$_meta['days'] = $this->settings['days'];
		}

		$_meta['days'] = absint( $_meta['days'] );

		if ( $_meta['days'] < static::minimum_days_allowed() ) {
			$_meta['days'] = absint( static::minimum_days_allowed() );
		}

		if ( $_meta['days'] < 1 ) {
			$_meta['days'] = 1;
		}

		return $_meta;
	}

	public function process_auto_close_rule( $meta, $topic_id = 0 ) {
		$_meta_modify = get_post_meta( $topic_id, '_gdbbx_modify_auto_close', true );
		$_meta_days   = get_post_meta( $topic_id, '_gdbbx_modify_auto_close_days', true );

		if ( $_meta_modify !== false && ! empty( $_meta_modify ) ) {
			$meta['active'] = $_meta_modify == 'yes';
		}

		if ( $_meta_days !== false && ! empty( $_meta_days ) ) {
			$meta['days'] = absint( $_meta_days );
		}

		return $meta;
	}

	public function closing_notice() {
		$_meta = $this->auto_close_forum_rule( bbp_get_forum_id() );
		$_meta = $this->process_auto_close_rule( $_meta, bbp_get_topic_id() );

		if ( $_meta['active'] && $_meta['notice'] && bbp_is_topic_open() ) {
			$days = $_meta['days'];

			$message = sprintf( _n( "This topic will close <strong>%s day</strong> after the last reply.", "This topic will close <strong>%s days</strong> after the last reply.", $days, "bbp-core" ), $days );
			$notice  = '<div class="bbp-template-notice info"><p>' . $message . '</p></div>';

			echo apply_filters( 'gdbbx_auto_close_topic_notice', $notice, $message, $days );
		}
	}

	public function send_notifications( $topic_id = 0 ) : bool {
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		$_send_to_author      = $this->settings['notify_author'];
		$_send_to_subscribers = $this->settings['notify_subscribers'];

		if ( $_send_to_author === false && $_send_to_subscribers === false ) {
			return false;
		}

		$output   = gdbbx_mailer()->get_topic_author_and_subscribers( $topic_id, 'bbp_topic_auto_close_user_ids', $_send_to_author, $_send_to_subscribers, 'topic-auto-closed-notification' );
		$user_ids = $output['user_ids'] ?? array();
		$emails   = $output['emails'] ?? array();

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
Has been automatically closed due to inactivity.

Topic Link: %TOPIC_LINK%
-----------
You are receiving this email because you subscribed to a forum topic. Login and visit the topic to unsubscribe from these emails.

-----------
Do not reply to this email!", "Email message: notify on topic auto close", "bbp-core" );

		$subject = _x( "[%BLOG_NAME%] The topic '%TOPIC_TITLE%' is now closed", "Email title: notify on topic auto close", "bbp-core" );

		if ( $this->settings['notify_active'] ) {
			$message = ! empty( $this->settings['notify_content'] ) ? $this->settings['notify_content'] : '';
			$subject = ! empty( $this->settings['notify_subject'] ) ? $this->settings['notify_subject'] : '';
		}

		$message = d4p_replace_tags_in_content( $message, array(
			'BLOG_NAME'     => $blog_name,
			'FORUM_TITLE'   => $forum_title,
			'FORUM_LINK'    => get_permalink( $forum_id ),
			'TOPIC_TITLE'   => $topic_title,
			'TOPIC_AUTHOR'  => bbp_get_topic_author_display_name( $topic_id ),
			'TOPIC_CONTENT' => $topic_content,
			'TOPIC_LINK'    => $topic_url
		) );

		if ( $this->settings['notify_shortcodes'] ) {
			$message = do_shortcode( $message );
		}

		$subject = d4p_replace_tags_in_content( $subject, array(
			'BLOG_NAME'   => $blog_name,
			'TOPIC_TITLE' => $topic_title
		) );

		$message = apply_filters( 'bbp_topic_auto_close_mail_message', $message, $topic_id, $forum_id );
		if ( empty( $message ) ) {
			return false;
		}

		$subject = apply_filters( 'bbp_topic_auto_close_mail_title', $subject, $topic_id, $forum_id );
		if ( empty( $subject ) ) {
			return false;
		}

		$no_reply   = bbp_get_do_not_reply_address();
		$from_email = apply_filters( 'bbp_topic_auto_close_from_email', $no_reply );
		$headers    = array( 'From: ' . get_bloginfo( 'name' ) . ' <' . $from_email . '>' );

		foreach ( $emails as $email ) {
			$headers[] = 'Bcc: ' . $email;
		}

		$headers  = apply_filters( 'bbp_topic_auto_close_mail_headers', $headers );
		$to_email = apply_filters( 'bbp_topic_auto_close_to_email', $no_reply );

		do_action( 'bbp_pre_notify_topic_auto_close', $topic_id, $user_ids );

		wp_mail( $to_email, $subject, $message, $headers );

		do_action( 'bbp_post_notify_topic_auto_close', $topic_id, $user_ids );

		return true;
	}

	public function mod_topic_form() {
		$author = true;

		if ( bbp_is_topic_edit() ) {
			$author = bbp_get_topic_author_id() == bbp_get_current_user_id();
		}

		$this->mod_form_include( $author, 'topic' );
	}

	public function mod_reply_form() {
		$author = bbp_get_topic_author_id() == bbp_get_current_user_id();

		$this->mod_form_include( $author, 'reply' );
	}

	public function load_modify_fieldset() {
		include( gdbbx_get_template_part( 'gdbbx-form-auto-close.php' ) );

		Enqueue::instance()->core();
	}

	private function mod_form_include( $author, $form ) {
		$moderator = gdbbx_can_user_moderate();

		if ( ( $this->settings['modify_author'] && $author ) || ( $this->settings['modify_moderators'] && $moderator ) ) {
			if ( $form == 'topic' ) {
				add_action( $this->settings['modify_topic_form_location'], array( $this, 'load_modify_fieldset' ) );
			} else if ( $form == 'reply' ) {
				add_action( $this->settings['modify_reply_form_location'], array( $this, 'load_modify_fieldset' ) );
			}
		}
	}

	private function save_topic_settings( $topic_id ) {
		$_topic_modify = isset( $_POST['gdbbx_auto_close_modify'] ) ? d4p_sanitize_slug( $_POST['gdbbx_auto_close_modify'] ) : 'auto';
		$_topic_days   = isset( $_POST['gdbbx_auto_close_days'] ) ? absint( $_POST['gdbbx_auto_close_days'] ) : 0;

		if ( in_array( $_topic_modify, array( 'yes', 'no' ) ) ) {
			update_post_meta( $topic_id, '_gdbbx_modify_auto_close', $_topic_modify );
		} else {
			delete_post_meta( $topic_id, '_gdbbx_modify_auto_close' );
		}

		if ( $_topic_days >= self::minimum_days_allowed() ) {
			update_post_meta( $topic_id, '_gdbbx_modify_auto_close_days', $_topic_days );
		} else {
			delete_post_meta( $topic_id, '_gdbbx_modify_auto_close_days' );
		}
	}

	private function build_auto_close_query() : string {
		return "SELECT SQL_CALC_FOUND_ROWS p.ID AS topic_id, p.post_parent as forum_id, DATEDIFF(CURDATE(), CAST(ma.meta_value AS DATETIME)) AS last_active
                FROM " . gdbbx_db()->wpdb()->posts . " p
                INNER JOIN " . gdbbx_db()->wpdb()->postmeta . " ma ON ma.post_id = p.ID AND ma.meta_key = '_bbp_last_active_time'
                LEFT JOIN " . gdbbx_db()->wpdb()->postmeta . " mx ON mx.post_id = p.ID AND mx.meta_key = '_gdbbx_auto_close_skip'
                WHERE p.post_type = '" . bbp_get_topic_post_type() . "' AND p.post_status = 'publish' AND mx.meta_value IS NULL
                AND CAST(ma.meta_value AS DATETIME) < DATE_SUB(CURDATE(), INTERVAL " . self::minimum_days_allowed() . " DAY)
                ORDER BY topic_id ASC LIMIT " . $this->limit;
	}
}
