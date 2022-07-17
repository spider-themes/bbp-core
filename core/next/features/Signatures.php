<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Signatures extends Feature {
	public $feature_name = 'signatures';
	public $settings = array(
		'scope'                => 'global',
		'limiter'              => true,
		'length'               => 512,
		'super_admin'          => true,
		'roles'                => null,
		'edit_super_admin'     => true,
		'edit_roles'           => null,
		'editor'               => 'textarea',
		'enhanced_active'      => true,
		'enhanced_method'      => 'html',
		'enhanced_super_admin' => true,
		'enhanced_roles'       => null,
		'process_smilies'      => true,
		'process_chars'        => true,
		'process_autop'        => true
	);

	public $enabled = false;
	public $allowed = false;
	public $edit = false;
	public $enhanced = false;
	public $tinymce = false;
	public $bbcodes = false;
	public $html = false;

	private $_user_id = 0;

	public function __construct() {
		parent::__construct();

		require_once( BBPC_PATH . 'core/next/functions/signatures.php' );

		$this->allowed  = $this->allowed();
		$this->edit     = $this->allowed( 'edit', 'signatures-edit' );
		$this->enhanced = $this->settings['enhanced_active'] && $this->allowed( 'enhanced', 'signatures-edit' );

		if ( $this->enhanced ) {
			$method = $this->settings['enhanced_method'];
			$editor = $this->settings['editor'];

			$this->tinymce = $editor == 'tinymce' || $editor == 'tinymce_compact';

			if ( $method == 'bbcode' || $method == 'full' || $editor == 'bbcodes' ) {
				$this->bbcodes = true;
			}

			if ( $method == 'html' || $method == 'full' || $this->tinymce ) {
				$this->html = true;
			}
		}

		if ( $this->tinymce && $this->edit ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'tinymce_override' ) );
		}

		add_action( 'bbpc_init', array( $this, 'init' ) );
	}

	public static function instance() : Signatures {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Signatures();
		}

		return $instance;
	}

	public function __get( $name ) {
		if ( isset( $this->settings[ $name ] ) ) {
			return $this->settings[ $name ];
		}

		return '';
	}

	public function tinymce_override() {
		$load = bbp_is_user_home_edit() || bbp_is_single_user_edit() ||
		        ( bbpc_plugin()->buddypress && bp_is_user_profile_edit() );

		if ( $load ) {
			Enqueue::instance()->tinymce();
		}
	}

	public function init() {
		$this->add_content_filters();

		if ( $this->allowed && $this->edit ) {
			$this->attach_edit();
		}
	}

	public function attach_edit() {
		add_action( 'show_user_profile', array( $this, 'editor_form_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'editor_form_profile' ) );
		add_action( 'edit_user_profile_update', array( $this, 'editor_save' ) );
		add_action( 'personal_options_update', array( $this, 'editor_save' ) );

		add_action( 'bbp_user_edit_after', array( $this, 'editor_form_bbpress' ) );
		add_action( 'bbpc_user_edit_signature_info', array( $this, 'signature_info' ) );
	}

	public function add_content_filters() {
		if ( ! $this->enabled ) {
			$this->enabled = true;

			add_filter( 'bbp_get_topic_content', array( $this, 'reply_content' ), 98, 2 );
			add_filter( 'bbp_get_reply_content', array( $this, 'reply_content' ), 98, 2 );
		}
	}

	public function remove_content_filters() {
		$this->enabled = false;

		remove_filter( 'bbp_get_topic_content', array( $this, 'reply_content' ), 98 );
		remove_filter( 'bbp_get_reply_content', array( $this, 'reply_content' ), 98 );
	}

	public function editor_form_generic( $user_id = 0, $template = '' ) {
		$this->_user_id = $user_id;

		if ( $template == '' ) {
			$template = 'bbpc-form-signature-generic.php';
		}

		$template = apply_filters( 'bbpc_signature_generic_editor_template', $template );
		$template = bbpc_get_template_part( $template );

		include_once( apply_filters( 'bbpc_signature_generic_editor_file', $template ) );
	}

	public function editor_form_profile() {
		if ( ! is_admin() ) {
			return;
		}

		include_once( apply_filters( 'bbpc_signature_profile_editor_file', BBPC_PATH . 'forms/profile/signature.php' ) );
	}

	public function editor_form_bbpress() {
		$template = apply_filters( 'bbpc_signature_bbpress_editor_template', 'bbpc-form-signature-bbpress.php' );
		$template = bbpc_get_template_part( $template );

		include_once( apply_filters( 'bbpc_signature_bbpress_editor_file', $template ) );
	}

	public function get_signature_for_user( $user_id = 0 ) {
		if ( $user_id == 0 ) {
			$user_id = $this->_user_id == 0 ? get_current_user_id() : $this->_user_id;
		}

		$user = get_user_by( 'id', $user_id );

		$old_filter   = $user->filter;
		$user->filter = 'display';

		$_signature = bbpc_update_shorthand_bbcodes( $user->signature );

		$user->filter = $old_filter;

		return $_signature;
	}

	public function get_signature_for_profile_user() {
		global $profileuser;

		$old_filter          = $profileuser->filter;
		$profileuser->filter = 'display';

		$_signature = bbpc_update_shorthand_bbcodes( $profileuser->signature );

		$profileuser->filter = $old_filter;

		return $_signature;
	}

	public function get_signature_max_length() {
		return $this->settings['length'];
	}

	public function get_signature_for_bbpress_displayed_user() {
		return bbpc_update_shorthand_bbcodes( bbp_get_displayed_user_field( 'signature' ) );
	}

	public function signature_info() {
		$message = array();

		if ( ! $this->html && ! $this->bbcodes ) {
			$message[] = __( "You can use only plain text. HTML and BBCodes will be stripped.", "bbp-core" );
		} else {
			if ( $this->html ) {
				$message[] = __( "You can use HTML.", "bbp-core" );
			}

			if ( $this->bbcodes ) {
				$message[] = __( "You can use BBCodes.", "bbp-core" );
			}

			if ( ! $this->html ) {
				$message[] = __( "HTML will be stripped.", "bbp-core" );
			}

			if ( ! $this->bbcodes ) {
				$message[] = __( "BBCodes will be stripped.", "bbp-core" );
			}
		}

		echo join( ' ', $message );
	}

	public function format_signature( $sig ) {
		if ( ! $this->html ) {
			$sig = strip_tags( $sig );
		}

		if ( ! $this->bbcodes ) {
			$sig = strip_shortcodes( $sig );
		}

		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$sig = stripslashes( wp_filter_post_kses( addslashes( $sig ) ) );
		}

		if ( strlen( $sig ) > $this->settings['length'] ) {
			$sig = substr( $sig, 0, $this->settings['length'] );
		}

		return trim( $sig );
	}

	public function editor_save( $user_id ) {
		if ( isset( $_POST['signature'] ) ) {
			$sig = $this->format_signature( $_POST['signature'] );

			bbpc_update_raw_user_signature( $user_id, $sig );
		}
	}

	public function reply_content( $content, $reply_id = 0 ) {
		if ( bbpc()->is_inside_content_shortcode( $reply_id ) ) {
			return $content;
		}

		if ( bbpc_is_feed() ) {
			return $content;
		}

		if ( $reply_id == 0 ) {
			global $post;

			$user_id = $post->post_author;
		} else {
			$user_id = bbp_get_reply_author_id( $reply_id );
		}

		$sig = bbpc_user_signature( $user_id, array( 'echo' => false ) );

		if ( $sig != '' ) {
			$content .= apply_filters( 'bbpc_content_signature', $sig, $user_id );
		}

		Enqueue::instance()->core();

		return $content;
	}

	public function textarea_class() {
		$class = 'bbpc-signature';

		if ( $this->settings['limiter'] ) {
			$class .= ' bbpc-limiter-enabled';
		}

		return $class;
	}

	public function textarea_data() {
		$data = '';

		if ( $this->settings['limiter'] ) {
			$limit = $this->get_signature_max_length();
			$data  = ' data-chars="' . $limit . '" data-warning="' . floor( $limit * .9 ) . '"';
		}

		return $data;
	}

	public function generate_editor( $content ) {
		$_editor = $this->settings['editor'];

		if ($_editor == 'bbcodes' && !bbpc_is_bbcodes_toolbar_available()) {
			$_editor = 'textarea';
		}

		if ( $_editor == 'textarea' || $_editor == 'bbcodes' ) {
			if ( $_editor == 'bbcodes' ) {
				\Dev4Press\Plugin\BBPC\BBCodes\Toolbar::instance()->display();
			}

			echo '<textarea' . $this->textarea_data() . ' class="' . $this->textarea_class() . '" name="signature" id="signature" rows="5" cols="30">' . esc_attr( $content ) . '</textarea>';
		} else if ( $this->tinymce ) {
			$settings = apply_filters( 'bbpc_signature_tinymce_settings', array(
				'textarea_rows' => 5,
				'teeny'         => $this->settings['editor'] == 'tinymce_compact'
			) );

			wp_editor( $content, 'signature', $settings );
		}
	}
}
