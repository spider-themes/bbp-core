<?php

namespace Dev4Press\Plugin\GDBBX\Admin;

use gdbbx_grid_attachments;
use gdbbx_grid_errors;
use gdbbx_grid_reports;
use gdbbx_grid_thanks;
use gdbbx_grid_users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Grids {
	public $options = array(
		'gdbbx_rows_users_per_page',
		'gdbbx_rows_thanks_per_page',
		'gdbbx_rows_attachments_per_page',
		'gdbbx_rows_errors_per_page',
		'gdbbx_rows_reports_per_page'
	);

	public function __construct() {
		add_filter( 'set-screen-option', array( $this, 'screen_options_grid_rows_save' ), 10, 3 );
		add_action( 'gdbbx_admin_load_hooks', array( $this, 'load_hooks' ) );
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
		add_action( 'load-bbpress-toolbox_page_gd-bbpress-toolbox-users', array(
			$this,
			'screen_options_grid_rows_users'
		) );
		add_action( 'load-bbpress-toolbox_page_gd-bbpress-toolbox-reported-posts', array(
			$this,
			'screen_options_grid_rows_reports'
		) );
		add_action( 'load-bbpress-toolbox_page_gd-bbpress-toolbox-thanks-list', array(
			$this,
			'screen_options_grid_rows_thanks'
		) );
		add_action( 'load-bbpress-toolbox_page_gd-bbpress-toolbox-attachments', array(
			$this,
			'screen_options_grid_rows_attachments'
		) );
		add_action( 'load-bbpress-toolbox_page_gd-bbpress-toolbox-errors', array(
			$this,
			'screen_options_grid_rows_errors'
		) );
	}

	public function screen_options_grid_rows_thanks() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'gdbbx_rows_thanks_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( GDBBX_PATH . 'core/grids/thanks.php' );

		new gdbbx_grid_thanks();
	}

	public function screen_options_grid_rows_reports() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'gdbbx_rows_reports_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( GDBBX_PATH . 'core/grids/reports.php' );

		new gdbbx_grid_reports();
	}

	public function screen_options_grid_rows_users() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'gdbbx_rows_users_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( GDBBX_PATH . 'core/grids/users.php' );

		new gdbbx_grid_users();
	}

	public function screen_options_grid_rows_attachments() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'gdbbx_rows_attachments_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( GDBBX_PATH . 'core/grids/attachments.php' );

		new gdbbx_grid_attachments();
	}

	public function screen_options_grid_rows_errors() {
		$args = array(
			'label'   => __( "Rows", "bbp-core" ),
			'default' => 25,
			'option'  => 'gdbbx_rows_errors_per_page'
		);

		add_screen_option( 'per_page', $args );

		require_once( GDBBX_PATH . 'core/grids/errors.php' );

		new gdbbx_grid_errors();
	}
}
