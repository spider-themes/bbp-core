<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class JournalTopic extends Feature {
	public $feature_name = 'journal-topic';
	public $settings = array(
		'allowed_roles'          => array( 'bbp_keymaster', 'bbp_moderator', 'bbp_participant' ),
		'allowed_in_forums'      => array(),
		'allowed_for_moderators' => false,
		'edit_for_moderators'    => true,
		'topic_form_position'    => 'bbp_theme_before_topic_form_submit_wrapper'
	);

	public $forum_id = 0;
	public $topic_id = 0;

	public function __construct() {
		parent::__construct();

		if ( is_user_logged_in() ) {
			add_action( 'gdbbx_template', array( $this, 'frontend' ) );
		}
	}

	public static function instance() : JournalTopic {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new JournalTopic();
		}

		return $instance;
	}

	public function is_user_allowed() : bool {
		return $this->allowed( 'allowed', 'journal-topic-allowed' );
	}

	public function is_forum_allowed( $forum_id = 0 ) : bool {
		if ( empty( $this->get( 'allowed_in_forums', array() ) ) ) {
			$allowed = true;
		} else {
			$allowed = $forum_id > 0 && in_array( $forum_id, $this->get( 'allowed_in_forums', array() ) );
		}

		return apply_filters( 'gdbbx_journal_topic_is_forum_allowed', $allowed, $forum_id );
	}

	public function is_journal( $topic_id = 0 ) : bool {
		$topic_id = bbp_get_topic_id( $topic_id );

		return get_post_meta( $topic_id, '_gdbbx_journal_topic', true ) === '1';
	}

	public function frontend() {
		add_filter( 'bbp_current_user_can_access_create_topic_form', array( $this, 'prepare_topic_form' ) );
		add_filter( 'bbp_current_user_can_access_create_reply_form', array( $this, 'prepare_reply_form' ), 90 );

		add_action( 'bbp_new_topic_post_extras', array( $this, 'process_journal_topic' ), 90 );
	}

	public function process_journal_topic( $topic_id ) {
		$author = bbp_get_topic_author_id( $topic_id );

		if ( $author > 0 ) {
			if ( $this->is_user_allowed() && $this->is_forum_allowed( bbp_get_topic_forum_id( $topic_id ) ) ) {
				if ( isset( $_POST['gdbbx_journal_topic'] ) && $_POST['gdbbx_journal_topic'] === '1' ) {
					update_post_meta( $topic_id, '_gdbbx_journal_topic', '1' );
				}
			}
		}
	}

	public function prepare_topic_form( $retval ) {
		if ( bbp_is_topic_edit() ) {
			return $retval;
		}

		$this->forum_id = apply_filters( 'gdbbx_journal_topic_prepare_topic_form_forum_id', bbp_get_forum_id() );

		if ( $this->is_user_allowed() && $this->is_forum_allowed( $this->forum_id ) ) {
			add_action( $this->settings['topic_form_position'], array(
				$this,
				'topic_journal_checkbox'
			), 9 );
		}

		return $retval;
	}

	public function topic_journal_checkbox() {
		include( gdbbx_get_template_part( 'gdbbx-form-topic-journal.php' ) );
	}

	public function prepare_reply_form( $retval ) {
		$journal = $this->is_journal( bbp_get_topic_id() );

		if ( $retval && $journal ) {
			$allowed = bbp_get_topic_author_id() == bbp_get_current_user_id();

			if ( ! $allowed && $this->get( 'allowed_for_moderators', false ) ) {
				$allowed = gdbbx_can_user_moderate();
			}

			if ( ! $allowed && bbp_is_reply_edit() && gdbbx_can_user_moderate() && $this->get( 'edit_for_moderators', false ) ) {
				$allowed = true;
			}

			if ( ! $allowed ) {
				$retval = false;
			}
		}

		return $retval;
	}
}
