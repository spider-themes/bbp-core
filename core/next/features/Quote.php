<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Quote extends Feature {
	public $feature_name = 'quote';
	public $settings = array(
		'method'       => 'bbcode',
		'full_content' => 'postquote',
		'super_admin'  => true,
		'visitor'      => false,
		'roles'        => null
	);

	private $allowed = null;

	function __construct() {
		parent::__construct();

		if ( $this->allowed() && ! bbpc_plugin()->is_search ) {
			add_filter( 'bbpc_script_values', array( $this, 'script_values' ) );

			add_filter( 'bbp_get_reply_content', array( $this, 'quote_reply_content' ), 90, 2 );
			add_filter( 'bbp_get_topic_content', array( $this, 'quote_topic_content' ), 90, 2 );
		}
	}

	public static function instance() : Quote {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Quote();
		}

		return $instance;
	}

	public function script_values( $values ) {
		$values['load'][] = 'quote';
		$values['quote']  = apply_filters( 'bbpc_quote_script_values', array(
			'method' => $this->settings['method'],
			'bbcode' => $this->settings['full_content'],
			'wrote'  => _x( "%s wrote", "Quote Author Line", "bbp-core" )
		) );

		return $values;
	}

	public function check_if_allowed() {
		if ( is_null( $this->allowed ) ) {
			$this->allowed = bbp_current_user_can_access_create_reply_form();
		}

		if ( bbp_is_search_results() || bbp_is_user_home() ) {
			$this->allowed = false;
		}

		return $this->allowed;
	}

	private function _quote( $id ) {
		$is_reply = bbp_is_reply( $id );

		$allowed = $is_reply ? bbpc_is_user_allowed_to_reply( $id ) : bbpc_is_user_allowed_to_topic( $id );

		if ( $allowed ) {
			if ( $this->settings['method'] == 'html' ) {
				if ( $is_reply ) {
					$url = bbp_get_reply_url( $id );
					$ath = bbp_get_reply_author_display_name( $id );
				} else {
					$url = bbp_get_topic_permalink( $id );
					$ath = bbp_get_topic_author_display_name( $id );
				}

				return '<a role="button" href="#" data-id="' . $id . '" data-url="' . $url . '" data-author="' . $ath . '" class="bbpc-link-quote">' . $this->_string( 'quote' ) . '</a>';
			} else {
				return '<a role="button" href="#" data-id="' . $id . '" class="bbpc-link-quote">' . $this->_string( 'quote' ) . '</a>';
			}
		} else {
			return false;
		}
	}

	public function get_quote_link( $id ) {
		$allowed = $this->check_if_allowed();

		if ( apply_filters( 'bbpc_quote_show_link', $allowed, $id ) ) {
			return $this->_quote( $id );
		}

		return false;
	}

	public function quote_reply_content( $content, $reply_id ) {
		if ( bbpc()->is_inside_content_shortcode( $reply_id ) ) {
			return $content;
		}

		if ( bbpc_is_feed() ) {
			return $content;
		}

		Enqueue::instance()->core();

		if ( $this->check_if_allowed() ) {
			return '<div class="bbpc-quote-wrapper" id="bbpc-quote-wrapper-' . bbp_get_reply_id() . '">' . $content . '</div>';
		} else {
			return $content;
		}
	}

	public function quote_topic_content( $content, $topic_id ) {
		if ( bbpc()->is_inside_content_shortcode( $topic_id ) ) {
			return $content;
		}

		if ( bbpc_is_feed() ) {
			return $content;
		}

		Enqueue::instance()->core();

		if ( $this->check_if_allowed() ) {
			return '<div class="bbpc-quote-wrapper" id="bbpc-quote-wrapper-' . bbp_get_topic_id() . '">' . $content . '</div>';
		} else {
			return $content;
		}
	}

	private function _string( $name ) {
		switch ( $name ) {
			default:
			case 'quote':
				return apply_filters( 'bbpc_quote_string_quote', __( "Quote", "bbp-core" ) );
		}
	}
}
