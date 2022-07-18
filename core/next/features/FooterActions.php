<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FooterActions extends Feature {
	public $feature_name = 'footer-actions';
	public $has_settings = false;

	public function __construct() {
		parent::__construct();

		add_action( 'bbp_theme_after_topic_content', array( $this, 'footer_topic_links' ), 100 );
		add_action( 'bbp_theme_after_reply_content', array( $this, 'footer_reply_links' ), 100 );
	}

	public static function instance() : FooterActions {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new FooterActions();
		}

		return $instance;
	}

	public function footer_topic_links() {
		$links = $this->_topic_links();

		if ( $links != '' ) {
			echo '<div class="gdbbx-footer-meta">' . $links . '</div>';
		}
	}

	public function footer_reply_links() {
		$links = $this->_reply_links();

		if ( $links != '' ) {
			echo '<div class="gdbbx-footer-meta">' . $links . '</div>';
		}
	}

	private function _topic_links( $args = array() ) {
		$r = bbp_parse_args( $args, array(
			'id'     => bbp_get_topic_id(),
			'before' => '<span class="gdbbx-admin-links">',
			'after'  => '</span>',
			'sep'    => ' | ',
			'links'  => array()
		), 'get_topic_footer_links' );

		if ( empty( $r['links'] ) ) {
			$r['links'] = apply_filters( 'gdbbx_topic_footer_links', array(), $r['id'] );
		}

		if ( empty( $r['links'] ) ) {
			return '';
		}

		$links  = implode( $r['sep'], array_filter( $r['links'] ) );
		$retval = $r['before'] . $links . $r['after'];

		return apply_filters( 'gdbbx_get_topic_footer_links', $retval, $r, $args );
	}

	function _reply_links( $args = array() ) {
		$r = bbp_parse_args( $args, array(
			'id'     => 0,
			'before' => '<span class="gdbbx-admin-links">',
			'after'  => '</span>',
			'sep'    => ' | ',
			'links'  => array()
		), 'get_reply_footer_links' );

		$r['id'] = bbp_get_reply_id( (int) $r['id'] );

		if ( bbp_is_topic( $r['id'] ) ) {
			return $this->_topic_links( $args );
		}

		if ( ! bbp_is_reply( $r['id'] ) ) {
			return '';
		}

		if ( bbp_is_topic_trash( bbp_get_reply_topic_id( $r['id'] ) ) ) {
			return '';
		}

		if ( empty( $r['links'] ) ) {
			$r['links'] = apply_filters( 'gdbbx_reply_footer_links', array(), $r['id'] );
		}

		if ( empty( $r['links'] ) ) {
			return '';
		}

		$links  = implode( $r['sep'], array_filter( $r['links'] ) );
		$retval = $r['before'] . $links . $r['after'];

		return apply_filters( 'gdbbx_get_reply_footer_links', $retval, $r, $args );
	}
}
