<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DisableRSS extends Feature {
	public $feature_name = 'disable-rss';
	public $settings = array(
		'view_feed'           => false,
		'view_feed_redirect'  => 'parent',
		'forum_feed'          => false,
		'forum_feed_redirect' => 'parent',
		'topic_feed'          => false,
		'topic_feed_redirect' => 'parent',
		'reply_feed'          => false,
		'reply_feed_redirect' => 'forums'
	);

	public function __construct() {
		parent::__construct();

		add_filter( 'bbp_request', array( $this, 'feed_trap' ), 5 );
	}

	public static function instance() : DisableRSS {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new DisableRSS();
		}

		return $instance;
	}

	function feed_trap( $query_vars = array() ) {
		if ( isset( $query_vars['feed'] ) ) {
			if ( isset( $query_vars['post_type'] ) ) {
				$post_type  = false;
				$post_types = array(
					bbp_get_forum_post_type(),
					bbp_get_topic_post_type(),
					bbp_get_reply_post_type()
				);

				$qv_array = (array) $query_vars['post_type'];

				foreach ( $post_types as $bbp_pt ) {
					if ( in_array( $bbp_pt, $qv_array, true ) ) {
						$post_type = $bbp_pt;
						break;
					}
				}

				if ( ! empty( $post_type ) ) {
					switch ( $post_type ) {
						case bbp_get_forum_post_type():
							if ( $this->settings['forum_feed'] ) {
								$action = $this->settings['forum_feed_redirect'];

								if ( ! isset( $query_vars['name'] ) && $action == 'parent' ) {
									$action = 'forums';
								}

								switch ( $action ) {
									case '404':
										return array( 'error' => '404' );
									case 'home';
										wp_safe_redirect( get_site_url() );
										exit;
									case 'forums':
										wp_safe_redirect( bbp_get_forums_url() );
										exit;
									case 'parent';
										$page = get_page_by_path( $query_vars['name'], OBJECT, bbp_get_forum_post_type() );

										if ( is_array( $page ) ) {
											$page = $page[0];
										}

										if ( $page && isset( $page->ID ) ) {
											wp_safe_redirect( get_permalink( $page->ID ) );
										} else {
											wp_safe_redirect( bbp_get_forums_url() );
										}
										exit;
								}
							}
							break;
						case bbp_get_topic_post_type():
							if ( $this->settings['topic_feed'] ) {
								$action = $this->settings['topic_feed_redirect'];

								if ( ! isset( $query_vars['name'] ) && $action == 'parent' ) {
									$action = 'forums';
								}

								switch ( $action ) {
									case '404':
										return array( 'error' => '404' );
									case 'home';
										wp_safe_redirect( get_site_url() );
										exit;
									case 'forums':
										wp_safe_redirect( bbp_get_forums_url() );
										exit;
									case 'parent';
										$page = bbpc_get_topic_id_from_slug( $query_vars['name'] );

										if ( $page ) {
											wp_safe_redirect( get_permalink( $page ) );
										} else {
											wp_safe_redirect( bbp_get_forums_url() );
										}
										exit;
								}
							}
							break;
						case bbp_get_reply_post_type():
							if ( $this->settings['reply_feed'] ) {
								$action = $this->settings['topic_feed_redirect'];

								switch ( $action ) {
									case '404':
										return array( 'error' => '404' );
									case 'home';
										wp_safe_redirect( get_site_url() );
										exit;
									case 'forums':
									case 'parent';
										wp_safe_redirect( bbp_get_forums_url() );
										exit;
								}
							}
							break;
					}
				}
			} else if ( isset( $query_vars[ bbp_get_view_rewrite_id() ] ) ) {
				$view = $query_vars[ bbp_get_view_rewrite_id() ];

				if ( ! empty( $view ) && $this->settings['view_feed'] ) {
					$action = $this->settings['view_feed_redirect'];

					switch ( $action ) {
						case '404':
							return array( 'error' => '404' );
						case 'home';
							wp_safe_redirect( get_site_url() );
							exit;
						case 'forums':
							wp_safe_redirect( bbp_get_forums_url() );
							exit;
						case 'parent';
							wp_safe_redirect( bbp_get_view_url( $view ) );
							exit;
					}
				}
			}
		}

		return $query_vars;
	}
}
