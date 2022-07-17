<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\BB;
use SpiderDevs\Plugin\BBPC\BBCodes\Registrator;
use SpiderDevs\Plugin\BBPC\BBCodes\Source;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBCodes extends Feature {
	public $feature_name = 'bbcodes';
	public $settings = array(
		'notice'                        => true,
		'bbpress_only'                  => true,
		'restricted'                    => 'info',
		'hide_title'                    => 'Hidden Content',
		'hide_content_normal'           => 'You must be logged in to see hidden content.',
		'hide_content_count'            => 'You must be logged in and have at least %post_count% posts on this website.',
		'hide_content_reply'            => 'You must reply before you can see hidden content.',
		'hide_content_thanks'           => 'You must say thanks to topic author before you can see hidden content.',
		'hide_keymaster_always_allowed' => true,
		'spoiler_color'                 => '#111111',
		'spoiler_hover'                 => '#eeeeee',
		'scode_enlighter'               => 'enlighter',
		'highlight_color'               => '#222222',
		'highlight_background'          => '#ffffb0',
		'heading_size'                  => 3
	);

	private $_bbcodes = array();
	private $_active = array();
	private $_allowed = array();
	private $_toolbar = array();
	private $_restrict = array();

	private $_visitor = true;
	private $_role = '';

	public function __construct() {
		parent::__construct();

		$this->_prepare();
		$this->_run();
	}

	public static function instance() : BBCodes {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new BBCodes();
		}

		return $instance;
	}

	public function load() {
		add_filter( 'bbpc_script_values', array( $this, 'script_values' ) );
	}

	public function core() {
		if ( $this->get( 'bbpress_only' ) ) {
			add_filter( 'pre_do_shortcode_tag', array( $this, 'limit_to_bbpress' ), 10, 4 );
		}

		if ( $this->get( 'notice' ) ) {
			add_action( 'bbp_theme_before_reply_form_notices', array( $this, 'show_notice' ) );
			add_action( 'bbp_theme_before_topic_form_notices', array( $this, 'show_notice' ) );
		}

		Registrator::instance()->run();

		if ( in_array( 'scode', $this->_active ) ) {
			Source::instance();
		}

		$_filters = array(
			'bbp_new_reply_pre_insert',
			'bbp_new_topic_pre_insert',
			'bbp_edit_reply_pre_insert',
			'bbp_edit_topic_pre_insert'
		);

		if ( ! empty( $this->_restrict ) ) {
			d4p_add_filter( $_filters, array( $this, 'content_strip_on_save' ) );
		}
	}

	public function script_values( $values ) : array {
		$values['load'][] = 'bbcodes';

		return $values;
	}

	public function get_all_bbcodes() : array {
		return $this->_bbcodes;
	}

	public function get_active_bbcodes() : array {
		return $this->_active;
	}

	public function get_allowed_bbcodes() : array {
		return $this->_allowed;
	}

	public function get_toolbar_bbcodes() : array {
		return $this->_toolbar;
	}

	public function show_notice() {
		$messages = array(
			apply_filters( 'bbpc_notice_bbcodes_message_format', __( "You can use BBCodes to format your content.", "bbp-core" ) )
		);

		if ( count( $this->_active ) != count( $this->_allowed ) ) {
			$messages[] = apply_filters( 'bbpc_notice_bbcodes_message_advanced', __( "Your account can't use all available BBCodes, they will be stripped before saving.", "bbp-core" ) );
		}

		$notice = '<div class="bbp-template-notice"><p>' . join( '<br/>', $messages ) . '</p></div>';

		echo apply_filters( 'bbpc_notice_bbcodes_status', $notice, $messages );
	}

	public function content_strip_on_save( $reply_data ) {
		$reply_data['post_content'] = $this->_strip( $reply_data['post_content'] );

		return $reply_data;
	}

	public function limit_to_bbpress( $result, $tag, $attr, $m ) {
		if ( in_array( strtolower( $tag ), $this->_active ) && ! $this->_in_scope( $tag ) ) {
			return $m[0];
		}

		return $result;
	}

	public function render( $bbcode, $atts, $content = null ) : string {
		if ( method_exists( bbpc_module_bbcodes(), 'shortcode_' . $bbcode ) ) {
			return Registrator::instance()->{'shortcode_' . $bbcode}( $atts, $content );
		}

		return '';
	}

	private function _regex( $list ) : string {
		$tagregexp = join( '|', $list );

		return '\\['
		       . '(\\[?)'
		       . "($tagregexp)"
		       . '\\b'
		       . '('
		       . '[^\\]\\/]*'
		       . '(?:'
		       . '\\/(?!\\])'
		       . '[^\\]\\/]*'
		       . ')*?'
		       . ')'
		       . '(?:'
		       . '(\\/)'
		       . '\\]'
		       . '|'
		       . '\\]'
		       . '(?:'
		       . '('
		       . '[^\\[]*+'
		       . '(?:'
		       . '\\[(?!\\/\\2\\])'
		       . '[^\\[]*+'
		       . ')*+'
		       . ')'
		       . '\\[\\/\\2\\]'
		       . ')?'
		       . ')'
		       . '(\\]?)';

	}

	private function _strip( $content ) {
		$pattern = $this->_regex( $this->_restrict );

		return preg_replace_callback( "/$pattern/s", array( $this, '_strip_replace' ), $content );
	}

	private function _strip_replace( $m ) : string {
		if ( $this->get( 'restricted' ) == 'info' ) {
			return '[blockquote]' . __( "BBCode you used is not allowed.", "bbp-core" ) . '[/blockquote]';
		} else {
			return '';
		}
	}

	private function _in_scope( $tag ) : bool {
		global $post;

		$scope = $post instanceof WP_Post && BB::i()->is_bbpress_post_type( $post->post_type );

		return apply_filters( 'bbpc_bbcode_in_valid_scope', $scope, $tag );
	}

	private function _prepare() {
		$raw = bbpc()->group_get( 'bbcodes' );

		if ( is_user_logged_in() ) {
			global $current_user;

			$valid = bbpc_list_user_roles();

			if ( is_array( $current_user->roles ) ) {
				$matched = array_intersect( $current_user->roles, $valid );

				if ( ! empty( $matched ) ) {
					$roles          = array_values( $matched );
					$this->_role    = $roles[0];
					$this->_visitor = false;
				}
			}
		}

		foreach ( $raw as $code => $data ) {
			$this->_bbcodes[] = $code;

			if ( $data['status'] ) {
				$this->_active[] = $code;

				if ( $this->_visitor ) {
					if ( $data['visitor'] ) {
						$this->_allowed[] = $code;
					} else {
						$this->_restrict[] = $code;
					}
				} else {
					if ( $data['roles'] === true ) {
						$this->_allowed[] = $code;
					} else if ( in_array( $this->_role, $data['roles'] ) ) {
						$this->_allowed[] = $code;
					} else {
						$this->_restrict[] = $code;
					}
				}

				if ( $data['toolbar'] && in_array( $code, $this->_allowed ) ) {
					$this->_toolbar[] = $code;
				}
			}
		}
	}

	private function _run() {
		add_action( 'bbpc_core', array( $this, 'core' ) );
		add_action( 'bbpc_template', array( $this, 'load' ) );
	}
}