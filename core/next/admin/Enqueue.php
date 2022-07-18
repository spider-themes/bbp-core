<?php

namespace Dev4Press\Plugin\GDBBX\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {
	private $rtl;
	private $debug;

	public function __construct() {
		$this->rtl   = is_rtl();
		$this->debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ), 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public static function instance() {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Enqueue();
		}

		return $instance;
	}

	public function enqueue_scripts( $hook ) {
		if ( gdbbx_admin()->page !== false ) {
			$this->wp();

			wp_enqueue_style( 'gdbbx-fontawesome' );
			wp_enqueue_style( 'gdbbx-balloons' );
			wp_enqueue_style( 'gdbbx-plugin' );
			wp_enqueue_script( 'gdbbx-plugin' );

			switch ( gdbbx_admin()->page ) {
				case 'wizard':
					wp_enqueue_style( 'd4plib-wizard' );
					wp_enqueue_script( 'd4plib-wizard' );
					wp_enqueue_style( 'd4plib-grid' );
					break;
				case 'about':
					wp_enqueue_style( 'd4plib-grid' );
					break;
				case 'features':
					wp_enqueue_style( 'gdbbx-features' );
					break;
				case 'front':
					wp_enqueue_style( 'gdbbx-dashboard' );
					break;
			}
		} else {
			switch ( $hook ) {
				case 'edit.php':
				case 'users.php':
					wp_enqueue_style( 'gdbbx-columns' );
					break;
				case 'index.php':
					wp_enqueue_style( 'gdbbx-admin' );
					break;
				case 'widgets.php':
					wp_enqueue_style( 'd4plib-widgets' );
					wp_enqueue_script( 'gdbbx-widgets' );
					break;
				case 'post.php':
				case 'post-new.php':
					if ( gdbbx_admin()->get_post_type() !== false ) {
						wp_enqueue_media();

						wp_enqueue_style( 'gdbbx-metabox' );
						wp_enqueue_script( 'gdbbx-metabox' );
					}
					break;
			}
		}
	}

	private function file_library( $type, $name ) : string {
		$get = GDBBX_URL . 'd4plib/resources/';

		if ( $name == 'font' ) {
			$get .= 'font/styles';
		} else {
			$get .= $type . '/' . $name;
		}

		if ( ! $this->debug ) {
			$get .= '.min';
		}

		return $get . '.' . $type;
	}

	private function file( $type, $name ) : string {
		$get = GDBBX_URL . 'admin/' . $type . '/' . $name;

		if ( ! $this->debug && $type != 'font' ) {
			$get .= '.min';
		}

		return $get . '.' . $type;
	}

	public function wp() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-form' );

		wp_enqueue_script( 'wpdialogs' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_media();
	}

	public function register_styles() {
		wp_register_style( 'd4plib-font', $this->file_library( 'css', 'font' ), array(), d4p_library_enqueue_ver() );
		wp_register_style( 'd4plib-widgets', $this->file_library( 'css', 'widgets' ), array(), d4p_library_enqueue_ver() );
		wp_register_style( 'd4plib-shared', $this->file_library( 'css', 'shared' ), array( 'd4plib-font' ), d4p_library_enqueue_ver() );
		wp_register_style( 'd4plib-admin', $this->file_library( 'css', 'admin' ), array(
			'd4plib-shared',
			'd4plib-font'
		), d4p_library_enqueue_ver() );
		wp_register_style( 'd4plib-wizard', $this->file_library( 'css', 'wizard' ), array( 'd4plib-shared' ), d4p_library_enqueue_ver() );
		wp_register_style( 'd4plib-grid', $this->file_library( 'css', 'grid' ), array( 'd4plib-shared' ), d4p_library_enqueue_ver() );
		wp_register_style( 'd4plib-metabox', $this->file_library( 'css', 'meta' ), array(
			'd4plib-shared',
			'wp-jquery-ui-dialog'
		), d4p_library_enqueue_ver() );

		wp_register_style( 'gdbbx-fontawesome', GDBBX_URL . 'd4plib/resources/fontawesome/css/font-awesome.min.css', array(), '4.7.0' );
		wp_register_style( 'gdbbx-features', $this->file( 'css', 'features' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-dashboard', $this->file( 'css', 'dashboard' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-columns', $this->file( 'css', 'columns' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-admin', $this->file( 'css', 'admin' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-balloons', $this->file( 'css', 'balloon' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-plugin', $this->file( 'css', 'core' ), array( 'd4plib-admin' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-metabox', $this->file( 'css', 'meta' ), array( 'd4plib-metabox' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-widgets', $this->file( 'js', 'widgets' ), array(
			'd4plib-widgets',
			'jquery-ui-sortable'
		), gdbbx()->file_version(), true );
	}

	public function register_scripts() {
		wp_register_script( 'd4plib-areyousure', GDBBX_URL . 'd4pjs/are-you-sure/jquery.are-you-sure.min.js', array( 'jquery' ), d4p_library_enqueue_ver(), true );

		wp_register_script( 'd4plib-shared', $this->file_library( 'js', 'shared' ), array(
			'jquery',
			'wp-color-picker'
		), d4p_library_enqueue_ver(), true );
		wp_register_script( 'd4plib-admin', $this->file_library( 'js', 'admin' ), array(
			'jquery',
			'd4plib-shared'
		), d4p_library_enqueue_ver(), true );
		wp_register_script( 'd4plib-wizard', $this->file_library( 'js', 'wizard' ), array( 'jquery' ), d4p_library_enqueue_ver(), true );
		wp_register_script( 'd4plib-metabox', $this->file_library( 'js', 'meta' ), array(
			'jquery',
			'wpdialogs'
		), d4p_library_enqueue_ver(), true );
		wp_register_script( 'd4plib-widgets', $this->file_library( 'js', 'widgets' ), array( 'jquery' ), d4p_library_enqueue_ver(), true );

		wp_register_script( 'gdbbx-plugin', $this->file( 'js', 'admin' ), array(
			'd4plib-admin',
			'd4plib-areyousure'
		), gdbbx()->file_version(), true );
		wp_register_script( 'gdbbx-metabox', $this->file( 'js', 'meta' ), array( 'd4plib-metabox' ), gdbbx()->file_version(), true );
		wp_register_script( 'gdbbx-widgets', $this->file( 'js', 'widgets' ), array(
			'd4plib-widgets',
			'jquery-ui-sortable'
		), gdbbx()->file_version(), true );

		wp_localize_script( 'd4plib-shared', 'd4plib_admin_data', array(
			'string_media_image_remove'  => __( "Remove", "bbp-core" ),
			'string_media_image_preview' => __( "Preview", "bbp-core" ),
			'string_media_image_title'   => __( "Select Image", "bbp-core" ),
			'string_media_image_button'  => __( "Use Selected Image", "bbp-core" ),
			'string_are_you_sure'        => __( "Are you sure you want to do this?", "bbp-core" ),
			'string_image_not_selected'  => __( "Image not selected.", "bbp-core" )
		) );

		wp_localize_script( 'gdbbx-plugin', 'gdbbx_admin_data', array(
			'page'  => gdbbx_admin()->page,
			'panel' => gdbbx_admin()->panel
		) );

		wp_localize_script( 'gdbbx-metabox', 'gdbbx_meta_data', array(
			'nonce'                      => wp_create_nonce( 'gdbbx-admin-internal' ),
			'wp_version'                 => GDBBX_WPV,
			'button_icon_ok'             => '<span class="dashicons dashicons-yes"></span>   ',
			'button_icon_cancel'         => '<span class="dashicons dashicons-no-alt"></span> ',
			'button_icon_delete'         => '<span class="dashicons dashicons-trash"></span> ',
			'button_icon_detach'         => '<span class="dashicons dashicons-editor-unlink"></span> ',
			'dialog_button_ok'           => _x( "OK", "Dialog Button", "bbp-core" ),
			'dialog_button_cancel'       => _x( "Cancel", "Dialog Button", "bbp-core" ),
			'dialog_button_delete'       => _x( "Delete", "Dialog Button", "bbp-core" ),
			'dialog_button_detach'       => _x( "Detach", "Dialog Button", "bbp-core" ),
			'dialog_title_areyousure'    => _x( "Are you sure you want to do this?", "Dialog question", "bbp-core" ),
			'dialog_content_pleasewait'  => _x( "Please Wait...", "Dialog loading status", "bbp-core" ),
			'dialog_content_failed'      => _x( "Request Failed.", "Attachment dialog result", "bbp-core" ),
			'string_media_dialog_title'  => _x( "Select one or more files to attach", "Attachment dialog", "bbp-core" ),
			'string_media_dialog_button' => _x( "Attach Selected Files", "Attachment dialog", "bbp-core" )
		) );
	}
}
