<?php

namespace SpiderDevs\Plugin\BBPC\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helper {
	function __construct() {
	}

	public static function instance() : Helper {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Helper();
		}

		return $instance;
	}

	public function max_server_allowed() {
		return floor( d4p_php_ini_size_value( 'upload_max_filesize' ) / KB_IN_BYTES );
	}
}
