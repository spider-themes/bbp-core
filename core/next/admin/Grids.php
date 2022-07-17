<?php

namespace SpiderDevs\Plugin\BBPC\Admin;

use bbpc_grid_attachments;
use bbpc_grid_errors;
use bbpc_grid_reports;
use bbpc_grid_thanks;
use bbpc_grid_users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Grids {
	public $options = array(
		'bbpc_rows_users_per_page',
		'bbpc_rows_thanks_per_page',
		'bbpc_rows_attachments_per_page',
		'bbpc_rows_errors_per_page',
		'bbpc_rows_reports_per_page'
	);

	public function __construct() {
		add_filter( 'set-screen-option', array( $this, 'screen_options_grid_rows_save' ), 10, 3 );
		add_action( 'bbpc_admin_load_hooks', array( $this, 'load_hooks' ) );
	}

	public static function instance() : Grids {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Grids();
		}

		return $instance;
	}

	public function screen_options_grid_rows_save( $status = '', $option = '', $value = '' ) {
		if ( in_array( $option, $this->options ) ) {
			return absint( $value );
		}

		return $status;
	}

	public function load_hooks() {
		add_action( 'load-bbpress-toolbox_page_bbp-core-users', array(
			$this,
			'screen_options_grid_rows_users'
		) );
		add_action( 'load-bbpress-toolbox_page_bbp-core-reported-posts', array(
			$this,
			'screen_options_grid_rows_reports'
		) );
		add_action( 'load-bbpress-toolbox_page_bbp-core-thanks-list', array(
			$this,
			'screen_options_grid_rows_thanks'
		) );
		add_action( 'load-bbpress-toolbox_page_bbp-core-attachments', array(
			$this,
			'screen_options_grid_rows_attachments'
		) );
		add_action( 'load-bbpress-toolbox_page_bbp-core-errors', array(
			$this,
			'screen_options_grid_rows_errors'
		) );
	}

	public function screen_options_grid_rows_thanks() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'bbpc_rows_thanks_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( BBPC_PATH . 'core/grids/thanks.php' );

		new bbpc_grid_thanks();
	}

	public function screen_options_grid_rows_reports() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'bbpc_rows_reports_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( BBPC_PATH . 'core/grids/reports.php' );

		new bbpc_grid_reports();
	}

	public function screen_options_grid_rows_users() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'bbpc_rows_users_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( BBPC_PATH . 'core/grids/users.php' );

		new bbpc_grid_users();
	}

	public function screen_options_grid_rows_attachments() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'bbpc_rows_attachments_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( BBPC_PATH . 'core/grids/attachments.php' );

		new bbpc_grid_attachments();
	}

	public function screen_options_grid_rows_errors() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'bbpc_rows_errors_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( BBPC_PATH . 'core/grids/errors.php' );

		new bbpc_grid_errors();
	}
}
