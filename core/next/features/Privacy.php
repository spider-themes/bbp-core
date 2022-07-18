<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Privacy extends Feature {
	public $feature_name = 'privacy';
	public $settings = array(
		'disable_ip_logging' => false,
		'disable_ip_display' => false
	);

	public function __construct() {
		parent::__construct();

		if ( $this->settings['disable_ip_logging'] ) {
			add_filter( 'bbp_current_author_ip', '__return_empty_string' );
		}

		if ( $this->settings['disable_ip_display'] ) {
			add_filter( 'bbp_get_author_ip', '__return_empty_string' );
		}
	}

	public static function instance() : Privacy {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Privacy();
		}

		return $instance;
	}
}
