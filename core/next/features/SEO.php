<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SEO extends Feature {
	public $feature_name = 'seo';
	public $settings = array(
		'document_title_parts'          => true,
		'override_forum_title_replace'  => false,
		'override_forum_title_text'     => 'Forum: %FORUM_TITLE%',
		'override_topic_title_replace'  => false,
		'override_topic_title_text'     => '%FORUM_TITLE% - Topic: %TOPIC_TITLE%',
		'override_reply_title_replace'  => false,
		'override_reply_title_text'     => '%REPLY_TITLE%',
		'override_forum_excerpt'        => false,
		'override_topic_excerpt'        => false,
		'override_topic_length'         => 150,
		'private_topic_excerpt_replace' => true,
		'private_topic_excerpt_text'    => "Topic '%TOPIC_TITLE%' is marked as private.",
		'override_reply_excerpt'        => false,
		'override_reply_length'         => 150,
		'private_reply_excerpt_replace' => true,
		'private_reply_excerpt_text'    => "Reply to topic '%TOPIC_TITLE%' is marked as private.",
		'meta_description_forum'        => false,
		'meta_description_topic'        => false,
		'meta_description_reply'        => false
	);

	public $id = 0;

	public $forum = false;
	public $topic = false;
	public $reply = false;

	public function __construct() {
		parent::__construct();

		add_action( 'bbp_head', array( $this, 'head' ) );

		if ( $this->get( 'override_forum_title_replace' ) ) {
			add_filter( 'bbp_before_title_parse_args', array( $this, 'title_forum' ) );
		}

		if ( $this->get( 'override_topic_title_replace' ) ) {
			add_filter( 'bbp_before_title_parse_args', array( $this, 'title_topic' ) );
		}

		if ( $this->get( 'override_reply_title_replace' ) ) {
			add_filter( 'bbp_before_title_parse_args', array( $this, 'title_reply' ) );
		}

		if ( $this->get( 'override_topic_excerpt' ) ) {
			add_filter( 'get_the_excerpt', array( $this, 'excerpt_topic' ), 1 );
		}

		if ( $this->get( 'override_reply_excerpt' ) ) {
			add_filter( 'get_the_excerpt', array( $this, 'excerpt_reply' ), 1 );
		}

		if ( $this->get( 'document_title_parts' ) ) {
			add_filter( 'document_title_parts', array( $this, 'document_title_parts' ), 1000 );
		}
	}

	public static function instance() : SEO {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new SEO();
		}

		return $instance;
	}

	function document_title_parts( $parts ) {
		if ( empty( $parts['title'] ) && is_bbpress() ) {
			$parts['title'] = trim( bbp_title( '', '' ) );
		}

		return $parts;
	}

	public function head() {
		$post = get_post();

		if ( isset( $post->ID ) && $post->ID > 0 ) {
			$this->id = $post->ID;

			if ( bbp_is_forum( $this->id ) ) {
				$this->forum = true;

				if ( $this->get( 'meta_description_forum' ) ) {
					$this->meta_description();
				}
			}

			if ( bbp_is_topic( $this->id ) ) {
				$this->topic = true;

				if ( $this->get( 'meta_description_topic' ) ) {
					$this->meta_description();
				}
			}

			if ( bbp_is_reply( $this->id ) ) {
				$this->reply = true;

				if ( $this->get( 'meta_description_reply' ) ) {
					$this->meta_description();
				}
			}
		}

		if ( apply_filters( 'gdbbx_plugin_meta_generator', true ) ) {
			echo '<meta name="generator" content="GD bbPress Toolbox Pro ' . gdbbx()->info_version . ', Build ' . gdbbx()->info_build . '" />' . D4P_EOL;
		}
	}

	public function title_forum( $title ) {
		if ( bbp_is_single_forum() && ! bbp_is_forum_edit() ) {
			$title = __( $this->get( 'override_forum_title_text' ), "bbp-core" );

			$title = array(
				'text'   => d4p_replace_tags_in_content( $title, array(
					'FORUM_TITLE' => bbp_get_forum_title()
				) ),
				'format' => '%s'
			);
		}

		return $title;
	}

	public function title_topic( $title ) {
		if ( bbp_is_single_topic() && ! bbp_is_topic_edit() ) {
			$title = __( $this->get( 'override_topic_title_text' ), "bbp-core" );

			$title = array(
				'text'   => d4p_replace_tags_in_content( $title, array(
					'TOPIC_TITLE' => bbp_get_topic_title(),
					'FORUM_TITLE' => bbp_get_forum_title()
				) ),
				'format' => '%s'
			);
		}

		return $title;
	}

	public function title_reply( $title ) {
		if ( bbp_is_single_reply() && ! bbp_is_reply_edit() ) {
			$title = __( $this->get( 'override_reply_title_text' ), "bbp-core" );

			$title = array(
				'text'   => d4p_replace_tags_in_content( $title, array(
					'REPLY_TITLE' => bbp_get_reply_title(),
					'TOPIC_TITLE' => bbp_get_topic_title(),
					'FORUM_TITLE' => bbp_get_forum_title()
				) ),
				'format' => '%s'
			);
		}

		return $title;
	}

	public function excerpt_topic( $excerpt ) {
		$post = get_post();

		if ( $excerpt == '' && isset( $post->post_type ) && $post->post_type == bbp_get_topic_post_type() ) {
			gdbbx_signature_display_disable();
			gdbbx_attachments_display_disable();

			if ( gdbbx_is_topic_private( $post->ID ) && $this->get( 'private_topic_excerpt_replace' ) ) {
				$excerpt = __( $this->get( 'private_topic_excerpt_text' ), "bbp-core" );

				$excerpt = d4p_replace_tags_in_content( $excerpt, array(
					'TOPIC_TITLE' => bbp_get_topic_title( $post->ID )
				) );
			} else {
				$excerpt = bbp_get_topic_excerpt( 0, $this->get( 'override_topic_length' ) );
			}

			gdbbx_attachments_display_enable();
			gdbbx_signature_display_enable();
		}

		return $excerpt;
	}

	public function excerpt_reply( $excerpt ) {
		$post = get_post();

		if ( $excerpt == '' && isset( $post->post_type ) && $post->post_type == bbp_get_reply_post_type() ) {
			gdbbx_signature_display_disable();
			gdbbx_attachments_display_disable();

			if ( gdbbx_is_reply_private( $post->ID ) && $this->get( 'private_reply_excerpt_replace' ) ) {
				$excerpt = __( $this->get( 'private_reply_excerpt_text' ), "bbp-core" );

				$excerpt = d4p_replace_tags_in_content( $excerpt, array(
					'TOPIC_TITLE' => bbp_get_topic_title( $post->ID )
				) );
			} else {
				$excerpt = bbp_get_reply_excerpt( 0, $this->get( 'override_reply_length' ) );
			}

			gdbbx_attachments_display_enable();
			gdbbx_signature_display_enable();
		}

		return $excerpt;
	}

	public function meta_description() {
		$excerpt = get_the_excerpt();
		$excerpt = str_replace( array( "\r", "\n", "  " ), ' ', $excerpt );
		$excerpt = str_replace( array( "&hellip;", "&#8230;" ), '', $excerpt );
		$excerpt = d4p_text_length_limit( trim( $excerpt ), 150 );

		echo '<meta name="description" content="' . $excerpt . '">' . D4P_EOL;
	}
}
