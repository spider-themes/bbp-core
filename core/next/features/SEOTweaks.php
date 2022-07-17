<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SEOTweaks extends Feature {
	public $feature_name = 'seo-tweaks';
	public $settings = array(
		'noindex_private_topic'  => true,
		'noindex_private_reply'  => true,
		'nofollow_topic_content' => true,
		'nofollow_reply_content' => true,
		'nofollow_topic_author'  => true,
		'nofollow_reply_author'  => true
	);

	public function __construct() {
		parent::__construct();

		add_action( 'bbp_head', array( $this, 'head' ) );

		$_priority = 60;

		if ( ! $this->settings['nofollow_topic_content'] ) {
			remove_filter( 'bbp_get_topic_content', 'bbp_rel_nofollow', $_priority );
		}

		if ( ! $this->settings['nofollow_reply_content'] ) {
			remove_filter( 'bbp_get_reply_content', 'bbp_rel_nofollow', $_priority );
		}

		if ( ! $this->settings['nofollow_topic_author'] ) {
			remove_filter( 'bbp_get_topic_author_link', 'bbp_rel_nofollow' );
		}

		if ( ! $this->settings['nofollow_reply_author'] ) {
			remove_filter( 'bbp_get_reply_author_link', 'bbp_rel_nofollow' );
		}
	}

	public static function instance() : SEOTweaks {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new SEOTweaks();
		}

		return $instance;
	}

	public function head() {
		$post = get_post();

		if ( isset( $post->ID ) && $post->ID > 0 ) {
			if ( bbp_is_topic( $post->ID ) ) {
				if ( $this->settings['noindex_private_topic'] && bbpc_is_topic_private( $post->ID ) ) {
					bbpc_no_robots();
				}
			} else if ( bbp_is_reply( $post->ID ) ) {
				if ( $this->settings['noindex_private_reply'] && bbpc_is_reply_private( $post->ID ) ) {
					bbpc_no_robots();
				}
			}
		}
	}
}
