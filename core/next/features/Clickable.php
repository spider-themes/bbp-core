<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Clickable extends Feature {
	public $feature_name = 'clickable';
	public $settings = array(
		'disable_make_clickable_topic' => false,
		'disable_make_clickable_reply' => false,
		'remove_clickable_urls'        => false,
		'remove_clickable_ftps'        => false,
		'remove_clickable_emails'      => false,
		'remove_clickable_mentions'    => false
	);

	public function __construct() {
		parent::__construct();

		$_priority = 40;

		if ( $this->settings['disable_make_clickable_topic'] ) {
			remove_filter( 'bbp_get_topic_content', 'bbp_make_clickable', $_priority );
		}

		if ( $this->settings['disable_make_clickable_reply'] ) {
			remove_filter( 'bbp_get_reply_content', 'bbp_make_clickable', $_priority );
		}

		if ( $this->settings['remove_clickable_urls'] ) {
			remove_filter( 'bbp_make_clickable', 'bbp_make_urls_clickable', 2 );
		}

		if ( $this->settings['remove_clickable_ftps'] ) {
			remove_filter( 'bbp_make_clickable', 'bbp_make_ftps_clickable', 4 );
		}

		if ( $this->settings['remove_clickable_emails'] ) {
			remove_filter( 'bbp_make_clickable', 'bbp_make_emails_clickable', 6 );
		}

		if ( $this->settings['remove_clickable_mentions'] ) {
			remove_filter( 'bbp_make_clickable', 'bbp_make_mentions_clickable', 8 );
		}
	}

	public static function instance() : Clickable {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Clickable();
		}

		return $instance;
	}
}
