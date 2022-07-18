<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ReplyActions extends Feature {
	public $feature_name = 'reply-actions';
	public $settings = array(
		'edit'    => 'header',
		'move'    => 'header',
		'split'   => 'header',
		'trash'   => 'header',
		'spam'    => 'header',
		'approve' => 'header',
		'reply'   => 'header',
		'thanks'  => 'footer',
		'quote'   => 'footer',
		'report'  => 'footer'
	);

	public $links = array();
	public $defaults = array();
	public $has_footer = false;

	public function __construct() {
		parent::__construct();

		$this->defaults   = array_keys( $this->settings );
		$this->has_footer = Plugin::instance()->is_enabled( 'footer-actions' );

		add_filter( 'bbp_reply_admin_links', array( $this, 'reply_admin_links' ), 10, 2 );
		add_filter( 'gdbbx_reply_footer_links', array( $this, 'reply_footer_links' ), 10, 2 );
	}

	public static function instance() : ReplyActions {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new ReplyActions();
		}

		return $instance;
	}

	private function _generate_links( $reply_id ) {
		if ( $this->settings['thanks'] != 'hide' && gdbbx_say_thanks() !== false ) {
			$link = gdbbx_say_thanks()->get_thanks_link( $reply_id );

			if ( $link !== false ) {
				$this->links['thanks'] = $link;
			}
		}

		if ( $this->settings['report'] != 'hide' && gdbbx_report() !== false ) {
			$link = gdbbx_report()->get_report_link( $reply_id );

			if ( $link !== false ) {
				$this->links['report'] = $link;
			}
		}

		if ( $this->settings['quote'] != 'hide' && gdbbx_quote() !== false ) {
			$link = gdbbx_quote()->get_quote_link( $reply_id );

			if ( $link !== false ) {
				$this->links['quote'] = $link;
			}
		}
	}

	public function reply_admin_links( $links, $reply_id ) {
		$this->links = $links;
		$this->_generate_links( $reply_id );

		foreach ( $this->defaults as $key ) {
			if ( $this->settings[ $key ] != 'header' && isset( $links[ $key ] ) ) {
				unset( $links[ $key ] );
			}

			if ( isset( $this->links[ $key ] ) ) {
				if ( $this->settings[ $key ] == 'header' ) {
					if ( ! isset( $links[ $key ] ) ) {
						$links[ $key ] = $this->links[ $key ];
					}
				} else if ( $this->settings[ $key ] == 'footer' ) {
					if ( ! $this->has_footer ) {
						$links[ $key ] = $this->links[ $key ];
					}
				}
			}
		}

		return $links;
	}

	public function reply_footer_links( $links, $reply_id ) {
		foreach ( $this->defaults as $key ) {
			if ( $this->settings[ $key ] == 'footer' && isset( $this->links[ $key ] ) ) {
				$links[ $key ] = $this->links[ $key ];
			}
		}

		return $links;
	}
}
