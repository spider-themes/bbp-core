<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Error {
	public $errors = array();

	function __construct() {
	}

	function add( $code, $message, $data ) {
		$this->errors[ $code ][] = array( $message, $data );
	}
}