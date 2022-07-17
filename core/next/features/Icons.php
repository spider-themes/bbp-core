<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Icons extends Feature {
	public $feature_name = 'icons';
	public $settings = array(
		'mode'                         => 'font',
		'forums_mark_closed_forum'     => true,
		'forums_mark_visibility_forum' => true,
		'forum_mark_attachments'       => true,
		'forum_mark_stick'             => true,
		'forum_mark_closed'            => true,
		'forum_mark_lock'              => true,
		'forum_mark_journal'           => false,
		'forum_mark_replied'           => false,
		'private_topics_icon'          => true,
		'private_replies_icon'         => false
	);

	public function __construct() {
		parent::__construct();

		add_action( 'bbp_theme_before_forum_title', array( $this, 'show_forums_icon_marks' ), 25 );
		add_action( 'bbp_theme_before_topic_title', array( $this, 'show_topics_icon_marks' ), 25 );
	}

	public static function instance() : Icons {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Icons();
		}

		return $instance;
	}

	public function mode() {
		return $this->get( 'mode' );
	}

	public function show_forums_icon_marks() {
		$forum_id = bbp_get_forum_id();

		if ( $this->settings['forums_mark_closed_forum'] && bbp_is_forum_closed( $forum_id ) ) {
			echo bbpc_signs()->closed_forum();
		}

		if ( $this->settings['forums_mark_visibility_forum'] && ! bbp_is_forum_public( $forum_id ) ) {
			if ( bbp_is_forum_private( $forum_id ) ) {
				echo bbpc_signs()->private_forum();
			} else if ( bbp_is_forum_hidden( $forum_id ) ) {
				echo bbpc_signs()->hidden_forum();
			}
		}
	}

	public function show_topics_icon_marks() {
		$topic_id = bbp_get_topic_id();

		if ( $this->settings['private_topics_icon'] && Plugin::instance()->is_enabled( 'private-topics' ) && bbpc_private_topics()->is_private( $topic_id ) ) {
			echo bbpc_signs()->private_topic();
		} else if ( $this->settings['private_replies_icon'] && Plugin::instance()->is_enabled( 'private-replies' ) && bbpc_private_replies()->has_private_replies( $topic_id ) ) {
			echo bbpc_signs()->private_replies();
		}

		if ( $this->settings['forum_mark_journal'] && Plugin::instance()->is_enabled( 'journal-topic' ) && JournalTopic::instance()->is_journal( $topic_id ) ) {
			echo bbpc_signs()->journal_topic();
		}

		if ( $this->settings['forum_mark_attachments'] && bbpc_cache()->attachments_has_topic_attachments( $topic_id ) ) {
			$attachments = bbpc_cache()->attachments_count_topic_attachments( $topic_id );
			echo bbpc_signs()->attachments( $attachments );
		}

		if ( $this->settings['forum_mark_closed'] && bbp_is_topic_closed( $topic_id ) ) {
			echo bbpc_signs()->closed_topic();
		}

		if ( Plugin::instance()->is_enabled( 'lock-topics' ) ) {
			if ( $this->settings['forum_mark_lock'] && LockTopics::instance()->is_topic_temp_locked( $topic_id ) ) {
				echo bbpc_signs()->locked_topic();
			}
		}

		if ( $this->settings['forum_mark_stick'] && bbp_is_topic_sticky( $topic_id ) ) {
			echo bbpc_signs()->sticky_topic();
		}

		if ( is_user_logged_in() && $this->settings['forum_mark_replied'] ) {
			if ( bbpc_cache()->userreplied_user_replied( $topic_id ) ) {
				echo bbpc_signs()->replied_to_topic();
			}
		}
	}
}
