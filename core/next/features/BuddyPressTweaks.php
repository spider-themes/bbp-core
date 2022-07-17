<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BuddyPressTweaks extends Feature {
	public $feature_name = 'buddypress-tweaks';
	public $settings = array(
		'disable_profile_override' => false
	);

	public function __construct() {
		parent::__construct();

		if ( $this->get( 'disable_profile_override' ) ) {
			add_action( 'bp_init', array( $this, 'disable_overrides' ) );
		}
	}

	public static function instance() : BuddyPressTweaks {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new BuddyPressTweaks();
		}

		return $instance;
	}

	public function disable_overrides() {
		remove_filter( 'bbp_pre_get_user_profile_url', array(
			bbpress()->extend->buddypress->members,
			'get_user_profile_url'
		) );
		remove_filter( 'bbp_pre_get_favorites_permalink', array(
			bbpress()->extend->buddypress->members,
			'get_favorites_permalink'
		) );
		remove_filter( 'bbp_pre_get_subscriptions_permalink', array(
			bbpress()->extend->buddypress->members,
			'get_subscriptions_permalink'
		) );
		remove_filter( 'bbp_pre_get_user_topics_created_url', array(
			bbpress()->extend->buddypress->members,
			'get_topics_created_url'
		) );
		remove_filter( 'bbp_pre_get_user_replies_created_url', array(
			bbpress()->extend->buddypress->members,
			'get_replies_created_url'
		) );
		remove_filter( 'bbp_pre_get_user_engagements_url', array(
			bbpress()->extend->buddypress->members,
			'get_engagements_permalink'
		) );
	}
}