<?php

namespace Dev4Press\Plugin\GDBBX\Basic;

use Dev4Press\Plugin\GDBBX\Features\Icons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {
	private $rtl;
	private $bulk_js;
	private $bulk_css;
	private $settings_loaded = false;
	private $flatpickr = array(
		'load' => 'd4plib3-flatpickr',
		'code' => ''
	);
	private $core_is_done = false;

	public function __construct() {
		$this->register_styles();
		$this->register_scripts();

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_files' ), 1 );
	}

	public static function instance() : Enqueue {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Enqueue();
		}

		return $instance;
	}

	public function init() {
		$this->rtl = is_rtl();

		$this->bulk_js  = gdbbx()->get( 'load_bulk_js' );
		$this->bulk_css = gdbbx()->get( 'load_bulk_css' );
	}

	public function locale() {
		return apply_filters( 'plugin_locale', get_user_locale() );
	}

	public function enqueue_files() {
		if ( is_admin() ) {
			return;
		}

		if ( apply_filters( 'gdbbx_enqueue_files', gdbbx()->get( 'load_always' ) || gdbbx_is_bbpress() ) ) {
			$this->core();
		}

		$this->widgets();
	}

	public function fitvids() {
		if ( gdbbx()->get( 'load_fitvids' ) ) {
			wp_enqueue_script( 'd4plib-fitvids' );

			do_action( 'gdbbx_enqueue_done_fitvids' );
		}
	}

	public function tinymce() {
		wp_enqueue_style( $this->rtl_handle( 'gdbbx-front-tinymce' ) );

		do_action( 'gdbbx_enqueue_done_tinymce' );
	}

	public function widgets() {
		if ( ! $this->bulk_css ) {
			wp_enqueue_style( $this->rtl_handle( 'gdbbx-front-widgets' ) );
		} else {
			$this->core();
		}

		do_action( 'gdbbx_enqueue_done_widgets' );
	}

	public function toolbar() {
		if ( ! $this->bulk_css ) {
			$this->font();

			wp_enqueue_style( $this->rtl_handle( 'gdbbx-front-toolbar' ) );
		} else {
			$this->core();
		}

		if ( ! $this->bulk_js ) {
			wp_enqueue_script( 'gdbbx-front-toolbar' );
		} else {
			$this->core();
		}
	}

	public function attachments() {
		$this->core();

		if ( ! $this->bulk_css ) {
			wp_enqueue_style( $this->rtl_handle( 'gdbbx-front-attachments' ) );
		}

		if ( ! $this->bulk_js ) {
			wp_enqueue_script( 'gdbbx-front-attachments' );
		}

		do_action( 'gdbbx_enqueue_done_attachments' );
	}

	public function icons() {
		if ( Icons::instance()->settings['mode'] == 'images' ) {
			wp_enqueue_style( 'gdbbx-image-icons' );

			do_action( 'gdbbx_enqueue_done_icons' );
		}
	}

	public function font() {
		if ( ! $this->bulk_css ) {
			wp_enqueue_style( 'gdbbx-font-icons' );
		}

		do_action( 'gdbbx_enqueue_done_font' );
	}

	public function schedule() {
		$this->core();

		wp_enqueue_style( 'd4plib3-flatpickr' );
		wp_enqueue_script( $this->flatpickr['load'] );

		do_action( 'gdbbx_enqueue_done_schedule' );
	}

	public function core() {
		if ( ! $this->core_is_done ) {
			$this->icons();
			$this->fitvids();
			$this->font();

			if ( $this->bulk_css ) {
				wp_enqueue_style( $this->rtl_handle( 'gdbbx-toolbox' ) );
			} else {
				wp_enqueue_style( $this->rtl_handle( 'gdbbx-front-features' ) );
			}

			if ( $this->bulk_js ) {
				wp_enqueue_script( 'gdbbx-toolbox' );
			} else {
				wp_enqueue_script( 'gdbbx-front-features' );
			}

			do_action( 'gdbbx_enqueue_done_core' );

			$this->register_settings();

			$this->core_is_done = true;
		}
	}

	public function is_rtl() {
		return $this->rtl;
	}

	public function rtl_handle( $name ) {
		if ( $this->is_rtl() ) {
			$name .= '-rtl';
		}

		return $name;
	}

	public function file( $type, $name, $path ) : string {
		$get = GDBBX_URL . 'templates/default/' . $path . '/' . $name;

		if ( ! gdbbx_plugin()->debug ) {
			$get .= '.min';
		}

		$get .= '.' . $type;

		return $get;
	}

	public function register_styles() {
		$_font_embedded = gdbbx()->get( 'font_icons_embedded' );
		$_font_name     = $_font_embedded ? 'font-embed' : 'font';
		$_bulk_name     = $_font_embedded ? 'toolbox-embed' : 'toolbox';

		wp_register_style( 'gdbbx-toolbox', $this->file( 'css', $_bulk_name, 'css' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-toolbox-rtl', $this->file( 'css', 'toolbox-rtl', 'css' ), array( 'gdbbx-toolbox' ), gdbbx()->file_version() );

		wp_register_style( 'gdbbx-font-icons', $this->file( 'css', $_font_name, 'css' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-image-icons', $this->file( 'css', 'icons', 'css' ), array(), gdbbx()->file_version() );

		wp_register_style( 'gdbbx-front-widgets', $this->file( 'css', 'widgets', 'css' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-widgets-rtl', $this->file( 'css', 'widgets-rtl', 'css' ), array( 'gdbbx-front-widgets' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-toolbar', $this->file( 'css', 'toolbar', 'css' ), array( 'gdbbx-font-icons' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-toolbar-rtl', $this->file( 'css', 'toolbar-rtl', 'css' ), array( 'gdbbx-front-toolbar' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-tinymce', $this->file( 'css', 'tinymce', 'css' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-tinymce-rtl', $this->file( 'css', 'tinymce-rtl', 'css' ), array( 'gdbbx-front-tinymce' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-features', $this->file( 'css', 'features', 'css' ), array( 'gdbbx-font-icons' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-features-rtl', $this->file( 'css', 'features-rtl', 'css' ), array( 'gdbbx-front-features' ), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-attachments', $this->file( 'css', 'attachments', 'css' ), array(), gdbbx()->file_version() );
		wp_register_style( 'gdbbx-front-attachments-rtl', $this->file( 'css', 'attachments-rtl', 'css' ), array( 'gdbbx-front-attachments' ), gdbbx()->file_version() );

		wp_register_style( 'd4plib3-flatpickr', GDBBX_URL . 'd4pjs/flatpickr/flatpickr.min.css', array(), '4.6.3' );
	}

	public function register_scripts() {
		wp_register_script( 'd4plib-fitvids', GDBBX_URL . 'd4pjs/fitvids/jquery.fitvids' . ( gdbbx_plugin()->debug ? '' : '.min' ) . '.js', array( 'jquery' ), gdbbx()->file_version(), true );

		wp_register_script( 'gdbbx-toolbox', $this->file( 'js', 'toolbox', 'js' ), array( 'jquery' ), gdbbx()->file_version(), true );

		wp_register_script( 'gdbbx-front-features', $this->file( 'js', 'features', 'js' ), array( 'jquery' ), gdbbx()->file_version(), true );
		wp_register_script( 'gdbbx-front-toolbar', $this->file( 'js', 'toolbar', 'js' ), array(
			'jquery',
			'gdbbx-front-features'
		), gdbbx()->file_version(), true );
		wp_register_script( 'gdbbx-front-attachments', $this->file( 'js', 'attachments', 'js' ), array(
			'jquery',
			'gdbbx-front-features'
		), gdbbx()->file_version(), true );

		wp_register_script( 'd4plib3-flatpickr', GDBBX_URL . 'd4pjs/flatpickr/flatpickr.min.js', array( 'jquery' ), '4.6.3', true );

		$locale = $this->locale();

		if ( ! empty( $locale ) ) {
			$code = strtolower( substr( $locale, 0, 2 ) );

			if ( in_array( $code, array( 'de', 'es', 'fr', 'it', 'ja', 'nl', 'pl', 'pt', 'ru', 'sr' ) ) ) {
				wp_register_script( 'd4plib3-flatpickr-' . $code, GDBBX_URL . 'd4pjs/flatpickr/l10n/' . $code . '.js', array( 'd4plib3-flatpickr' ), '4.6.3', true );

				$this->flatpickr['code'] = $code;
				$this->flatpickr['load'] = 'd4plib3-flatpickr-' . $code;
			}
		}
	}

	private function register_settings() {
		if ( $this->settings_loaded ) {
			return;
		}

		$handle = $this->bulk_js ? 'gdbbx-toolbox' : 'gdbbx-front-features';
		$values = apply_filters( 'gdbbx_script_values', array(
			'url'              => admin_url( 'admin-ajax.php' ),
			'now'              => time(),
			'wp_editor'        => bbp_use_wp_editor(),
			'last_cookie'      => gdbbx()->session_cookie_expiration(),
			'flatpickr_locale' => $this->flatpickr['code'],
			'load'             => array(),
			'text'             => array(
				'are_you_sure' => __( "Are you sure? Operation is not reversible.", "bbp-core" )
			)
		) );

		wp_localize_script( $handle, 'gdbbx_data', $values );

		$this->settings_loaded = true;
	}
}
