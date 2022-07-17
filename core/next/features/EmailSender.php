<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EmailSender extends Feature {
	public $feature_name = 'email-sender';
	public $settings = array(
		'sender_name'  => '',
		'sender_email' => ''
	);

	public function __construct() {
		parent::__construct();

		add_action( 'bbp_pre_notify_subscribers', array( $this, 'subscription_notify_hook_sender' ) );
		add_action( 'bbp_pre_notify_forum_subscribers', array( $this, 'subscription_notify_hook_sender' ) );
		add_action( 'bbp_pre_notify_topic_edit_subscribers', array( $this, 'subscription_notify_hook_sender' ) );
		add_action( 'bbp_pre_notify_reply_edit_subscribers', array( $this, 'subscription_notify_hook_sender' ) );
		add_action( 'bbp_pre_notify_new_topic_moderators', array( $this, 'subscription_notify_hook_sender' ) );
		add_action( 'bbp_pre_notify_new_reply_moderators', array( $this, 'subscription_notify_hook_sender' ) );
		add_action( 'bbp_pre_notify_topic_auto_close', array( $this, 'subscription_notify_hook_sender' ) );
		add_action( 'bbp_pre_notify_topic_manual_close', array( $this, 'subscription_notify_hook_sender' ) );

		add_action( 'bbp_post_notify_subscribers', array( $this, 'subscription_notify_unhook_sender' ) );
		add_action( 'bbp_post_notify_forum_subscribers', array( $this, 'subscription_notify_unhook_sender' ) );
		add_action( 'bbp_post_notify_topic_edit_subscribers', array( $this, 'subscription_notify_unhook_sender' ) );
		add_action( 'bbp_post_notify_reply_edit_subscribers', array( $this, 'subscription_notify_unhook_sender' ) );
		add_action( 'bbp_post_notify_new_topic_moderators', array( $this, 'subscription_notify_unhook_sender' ) );
		add_action( 'bbp_post_notify_new_reply_moderators', array( $this, 'subscription_notify_unhook_sender' ) );
		add_action( 'bbp_post_notify_topic_auto_close', array( $this, 'subscription_notify_unhook_sender' ) );
		add_action( 'bbp_post_notify_topic_manual_close', array( $this, 'subscription_notify_unhook_sender' ) );
	}

	public static function instance() : EmailSender {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new EmailSender();
		}

		return $instance;
	}

	public function subscription_notify_hook_sender() {
		add_filter( 'wp_mail_from', array( $this, 'mail_from_email' ), 10000000 );
		add_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ), 10000000 );
	}

	public function subscription_notify_unhook_sender() {
		remove_filter( 'wp_mail_from', array( $this, 'mail_from_email' ), 10000000 );
		remove_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ), 10000000 );
	}

	public function mail_from_email( $email ) {
		$start = $this->get( 'sender_email', '' );

		if ( $start != '' ) {
			$email = $start;
		}

		return $email;
	}

	public function mail_from_name( $name ) {
		$start = $this->get( 'sender_name', '' );

		if ( $start != '' ) {
			$name = $start;
		}

		return $name;
	}
}
