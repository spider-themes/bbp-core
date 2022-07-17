<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Snippets extends Feature {
	public $feature_name = 'snippets';
	private $crumbs = array();
	public $settings = array(
		'breadcrumbs'                          => true,
		'topic_dfp'                            => true,
		'topic_dfp_fallback_image'             => 0,
		'topic_dfp_include_article_body'       => false,
		'topic_dfp_include_author_profile_url' => true,
		'topic_dfp_include_author_website_url' => true,
		'topic_dfp_publisher_type'             => 'Organization',
		'topic_dfp_publisher_name'             => '',
		'topic_dfp_publisher_logo'             => 0
	);

	public function __construct() {
		parent::__construct();

		if ( $this->settings['breadcrumbs'] ) {
			add_filter( 'bbp_breadcrumbs', array( $this, 'rich_snippet_crumbs' ) );
			add_filter( 'bbp_get_breadcrumb', array( $this, 'rich_snippet_trail' ) );
		}

		if ( $this->settings['topic_dfp'] ) {
			add_action( 'bbp_template_after_single_topic', array( $this, 'discussion_forum_posting' ) );
		}
	}

	public static function instance() : Snippets {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Snippets();
		}

		return $instance;
	}

	public function rich_snippet_crumbs( $crumbs ) {
		$this->crumbs = array();

		for ( $i = 0; $i < count( $crumbs ); $i ++ ) {
			$crumb = $crumbs[ $i ];
			$url   = '';
			$label = '';

			if ( strpos( $crumb, 'href=' ) !== false ) {
				preg_match( '/<a[^>]+href=([\'"])(.+?)\1[^>]*>(.+?)<\/a>/i', $crumb, $result );

				if ( ! empty( $result ) ) {
					if ( isset( $result[2] ) ) {
						$url = $result[2];
					}

					if ( isset( $result[3] ) ) {
						$label = $result[3];
					}
				}
			} else {
				preg_match( '/>(.+?)</', $crumb, $result );

				if ( ! empty( $result ) ) {
					$url = d4p_current_url();

					if ( isset( $result[1] ) ) {
						$label = $result[1];
					}
				}
			}

			if ( ! empty( $url ) && ! empty( $label ) ) {
				$this->crumbs[] = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'item'     => array(
						'@id'  => $url,
						'name' => $label
					)
				);
			}
		}

		return $crumbs;
	}

	public function rich_snippet_trail( $trail ) {
		$render = '';

		if ( ! empty( $this->crumbs ) ) {
			$object = array(
				'@context'        => 'http://schema.org/',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $this->crumbs
			);

			$render = '<script type="application/ld+json">';
			$render .= json_encode( $object );
			$render .= '</script>';
		}

		return $trail . $render;
	}

	public function discussion_forum_posting() {
		$topic_id = bbp_get_topic_id();

		if ( apply_filters( 'bbpc_rich_snippet_discussion_forum_posting', true, $topic_id ) ) {
			$this->_add_dfp_snippet( $topic_id );
		}
	}

	private function _add_dfp_snippet( $topic_id ) {
		$snippet = array(
			'@context'             => 'https://schema.org',
			'@type'                => 'DiscussionForumPosting',
			'@id'                  => bbp_get_topic_permalink( $topic_id ),
			'headline'             => bbp_get_topic_title( $topic_id ),
			'articleBody'          => $this->_get_article_body( $topic_id ),
			'articleSection'       => bbp_get_forum_title( bbp_get_topic_forum_id( $topic_id ) ),
			'datePublished'        => date( 'r', get_post_timestamp( $topic_id ) ),
			'dateModified'         => mysql2date( 'r', get_post_meta( $topic_id, '_bbp_last_active_time', true ), false ),
			'author'               => array(
				'@type' => 'Person',
				'name'  => bbp_get_topic_author_display_name( $topic_id ),
				'url'   => bbp_get_topic_author_url( $topic_id )
			),
			'image'                => array(
				'@type' => 'ImageObject',
				'url'   => $this->_get_featured_image( $topic_id )
			),
			'interactionStatistic' => array(
				'@type'                => 'InteractionCounter',
				'interactionType'      => 'https://schema.org/ReplyAction',
				'userInteractionCount' => bbp_get_topic_reply_count( $topic_id )
			),
			'publisher'            => array(
				'@type' => $this->settings['topic_dfp_publisher_type'],
				'name'  => $this->_get_publisher_name(),
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => $this->_get_publisher_logo()
				)
			),
			'mainEntityOfPage'     => array(
				'@type' => 'WebPage',
				'@id'   => bbp_get_forums_url()
			)
		);

		$user = get_user_by( 'id', bbp_get_topic_author_id() );
		if ( $user !== false ) {
			if ( ! empty( $user->user_url ) ) {
				$snippet['author']['sameAs'] = $user->user_url;
			}
		}

		$snippet = apply_filters( 'bbpc_rich_snippet_discussion_forum_posting_snippet', $snippet, $topic_id );

		$render = '<script type="application/ld+json">';
		$render .= json_encode( $snippet );
		$render .= '</script>';

		echo $render;
	}

	private function _get_article_body( $topic_id ) {
		$content = get_post_field( 'post_content', $topic_id );
		$content = wptexturize( $content );
		$content = convert_chars( $content );
		$content = capital_P_dangit( $content );
		$content = convert_smilies( $content );
		$content = force_balance_tags( $content );
		$content = wpautop( $content );
		$content = do_shortcode( $content );
		$content = strip_tags( $content );

		$content = str_replace( array( "\n", "\r\n" ), ' ', $content );

		return trim( $content );
	}

	private function _get_featured_image( $topic_id ) {
		$_id = get_post_thumbnail_id( $topic_id );

		if ( ! $_id ) {
			$_id = $this->settings['topic_dfp_fallback_image'];
		}

		$_id = apply_filters( 'bbpc_rich_snippet_discussion_forum_posting_featured_image_id', $_id, $topic_id );

		if ( $_id && $_id > 0 ) {
			return wp_get_attachment_image_url( $_id, 'full' );
		}

		return '';
	}

	private function _get_publisher_name() {
		return empty( $this->settings['topic_dfp_publisher_name'] ) ? get_option( '' ) : $this->settings['topic_dfp_publisher_name'];
	}

	private function _get_publisher_logo() {
		$_id = $this->settings['topic_dfp_publisher_logo'];

		if ( $_id > 0 ) {
			return wp_get_attachment_image_url( $_id, 'full' );
		}

		return '';
	}
}
