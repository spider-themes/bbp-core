<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use SpiderDevs\Plugin\BBPC\Basic\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes extends Feature {
	public $feature_name = 'shortcodes';
	public $settings = array(
		'attachment_caption'       => 'hide',
		'attachment_video_caption' => 'hide',
		'attachment_audio_caption' => 'hide',
		'quote_title'              => 'user'
	);

	private $shortcodes;

	public function __construct() {
		parent::__construct();

		add_filter( 'bbp_get_reply_content', 'do_shortcode' );
		add_filter( 'bbp_get_topic_content', 'do_shortcode' );

		$this->shortcodes = array(
			'attachment'    => array(
				'name'  => __( "Attachment", "bbp-core" ),
				'atts'  => array(
					'style'    => '',
					'class'    => '',
					'file'     => '',
					'target'   => '_blank',
					'rel'      => '',
					'alt'      => '',
					'title'    => '',
					'width'    => '',
					'height'   => '',
					'align'    => 'alignnone',
					'autoplay' => false,
					'loop'     => false
				),
				'alias' => array( 'attachment', 'bbpc_attachment' )
			),
			'quote'         => array(
				'name'  => __( "Quote", "bbp-core" ),
				'atts'  => array( 'style' => '', 'class' => '', 'quote' => 0, 'raw' => 0 ),
				'alias' => array( 'quote', 'bbpc_quote' )
			),
			'postquote'     => array(
				'name'  => __( "Post Quote", "bbp-core" ),
				'atts'  => array( 'style' => '', 'class' => '', 'quote' => 0, 'raw' => 0 ),
				'alias' => array( 'postquote', 'bbpc_postquote' )
			),
			'profile_items' => array(
				'name'  => __( "Profile Items", "bbp-core" ),
				'atts'  => array( 'style' => '', 'class' => '', 'user' => 0, 'items' => '' ),
				'alias' => array( 'bbpc_profile_items' )
			)
		);

		foreach ( $this->shortcodes as $key => $items ) {
			foreach ( $items['alias'] as $shortcode ) {
				add_shortcode( $shortcode, array( $this, 'shortcode_' . $key ) );
				add_shortcode( strtoupper( $shortcode ), array( $this, 'shortcode_' . $key ) );
			}
		}
	}

	public static function instance() : Shortcodes {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Shortcodes();
		}

		return $instance;
	}

	private function _args( $code ) : array {
		return $this->shortcodes[ $code ]['args'] ?? array();
	}

	private function _atts( $code, $atts = array() ) : array {
		$default = $this->shortcodes[ $code ]['atts'];

		$atts = shortcode_atts( $default, $atts );

		return $atts;
	}

	private function _merge( $atts, $args, $attributes = array() ) : array {
		foreach ( $atts as $key => $value ) {
			if ( isset( $attributes[ $key ] ) && ( $key == 'class' || $key == 'style' ) ) {
				$attributes[ $key ] .= ' ' . $value;
			} else {
				$attributes[ $key ] = $value;
			}
		}

		foreach ( $args as $key => $value ) {
			if ( isset( $attributes[ $key ] ) && ( $key == 'class' || $key == 'style' ) ) {
				$attributes[ $key ] .= ' ' . $value;
			} else {
				$attributes[ $key ] = $value;
			}
		}

		return $attributes;
	}

	private function _tag( $tag, $name, $content = null, $atts = array(), $args = array(), $no_class = false ) : string {
		$standard   = $no_class ? array() : array( 'class' => 'bbpc-bbcode-' . $name );
		$attributes = $this->_merge( $atts, $args, $standard );

		$render = '<' . $tag;

		foreach ( $attributes as $key => $value ) {
			if ( trim( $value ) != '' && $key != 'raw' && $key != $name ) {
				$render .= ' ' . $key . '="' . trim( $value ) . '"';
			}
		}

		if ( is_null( $content ) ) {
			$render .= ' />';
		} else {
			$raw = isset( $atts['raw'] ) && $atts['raw'] == 1;

			$render .= '>';
			$render .= $this->_content( $content, $raw );
			$render .= '</' . $tag . '>';
		}

		Enqueue::instance()->core();
		Enqueue::instance()->widgets();

		return $render;
	}

	private function _content( $content, $raw = false ) : string {
		if ( $raw ) {
			return $content;
		} else {
			return do_shortcode( $content );
		}
	}

	public function shortcode_profile_items( $atts ) : string {
		$atts = $this->_atts( 'profile_items', $atts );

		$items = explode( ',', $atts['items'] );
		$items = array_map( 'trim', $items );

		$id = absint( $atts['user'] );
		$id = $id === 0 ? bbp_get_current_user_id() : $id;

		if ( $id > 0 && ! empty( $items ) ) {
			$list = array();
			$user = User::instance( $id );

			foreach ( $items as $item ) {
				$render = $user->render_item( $item );

				if ( ! empty( $render ) ) {
					$list[] = $render;
				}
			}

			$attributes = array(
				'style' => $atts['style'],
				'class' => $atts['class']
			);

			return $this->_tag( 'div', 'profile_items', join( '', $list ), $attributes );
		}

		return '';
	}

	public function shortcode_attachment( $atts ) : string {
		$atts = $this->_atts( 'attachment', $atts );

		if ( empty( $atts['title'] ) ) {
			unset( $atts['title'] );
		}

		if ( empty( $atts['alt'] ) ) {
			unset( $atts['alt'] );
		}

		if ( ! empty( $atts['file'] ) ) {
			$attachment = bbpc_get_attachment_id_from_name( $atts['file'] );

			if ( $attachment > 0 ) {
				$file = get_attached_file( $attachment );
				$ext  = pathinfo( $file, PATHINFO_EXTENSION );

				if ( function_exists( 'bbpc_attachments' ) ) {
					$id = bbp_get_reply_id();
					$id = $id > 0 ? $id : bbp_get_topic_id();
					$id = $id > 0 ? $id : get_the_ID();

					bbpc_attachments()->attachment_inserted( $id, $attachment );
				}

				$ax = get_post( $attachment );

				if ( in_array( $ext, bbpc()->get_image_extensions() ) ) {
					$title   = trim( $ax->post_title );
					$caption = trim( $ax->post_excerpt );

					$the_caption  = '';
					$show_caption = $this->get( 'attachment_caption', 'hide' );

					if ( $show_caption == 'auto' ) {
						$the_caption = empty( $caption ) ? $title : $caption;
					} else if ( $show_caption == 'caption' ) {
						$the_caption = empty( $caption ) ? '' : $caption;
					}

					$the_align = $atts['align'];

					if ( ! empty( $the_caption ) ) {
						unset( $atts['align'] );
					}

					$defaults = apply_filters( 'bbpc_attachment_image_defaults', array(
						'a'     => array(
							'target' => '_blank',
							'rel'    => '',
							'style'  => '',
							'class'  => '',
							'title'  => empty( $caption ) ? $title : $caption
						),
						'img'   => array(
							'width'  => '',
							'height' => '',
							'alt'    => empty( $caption ) ? $title : $caption
						),
						'thumb' => 'full'
					), $attachment );

					$atts_a   = shortcode_atts( $defaults['a'], $atts );
					$atts_img = shortcode_atts( $defaults['img'], $atts );

					$atts_a['href']  = wp_get_attachment_url( $attachment );
					$atts_img['src'] = $atts_a['href'];

					$image = wp_get_attachment_image_src( $attachment, $defaults['thumb'] );
					if ( $image ) {
						$atts_img['src'] = $image[0];
					}

					if ( empty( $the_caption ) ) {
						return $this->_tag( 'a', 'attachment', $this->_tag( 'img', 'attachment-image', null, $atts_img ), $atts_a );
					} else {
						$_img = $this->_tag( 'a', 'attachment', $this->_tag( 'img', 'attachment-image', null, $atts_img ), $atts_a );
						$_cap = $this->_tag( 'figcaption', 'caption', $the_caption, array( 'class' => 'wp-caption-text' ), array(), true );

						return $this->_tag( 'figure', 'attachment bbpc-with-caption ' . $the_align, $_img . $_cap, array( 'class' => 'wp-caption' ) );
					}
				} else if ( in_array( $ext, bbpc()->get_video_extensions() ) ) {
					$title   = trim( $ax->post_title );
					$caption = trim( $ax->post_excerpt );

					$the_caption  = '';
					$show_caption = $this->get( 'attachment_video_caption', 'hide' );

					if ( $show_caption == 'auto' ) {
						$the_caption = empty( $caption ) ? $title : $caption;
					} else if ( $show_caption == 'caption' ) {
						$the_caption = empty( $caption ) ? '' : $caption;
					}

					$atts_v = array(
						'src'      => wp_get_attachment_url( $attachment ),
						'loop'     => $atts['loop'],
						'autoplay' => $atts['autoplay']
					);

					if ( ! empty( $atts['width'] ) ) {
						$atts_v['width'] = absint( ( $atts['width'] ) );
					}

					if ( ! empty( $atts['height'] ) ) {
						$atts_v['height'] = absint( ( $atts['height'] ) );
					}

					$the_video = wp_video_shortcode( $atts_v );

					if ( empty( $the_caption ) ) {
						return $this->_tag( 'div', 'attachment', $the_video );
					} else {
						$_cap = $this->_tag( 'div', 'caption', $the_caption, array( 'class' => 'wp-caption-text' ), array(), true );

						return $this->_tag( 'div', 'attachment bbpc-with-caption', $the_video . $_cap );
					}
				} else if ( in_array( $ext, bbpc()->get_audio_extensions() ) ) {
					$title   = trim( $ax->post_title );
					$caption = trim( $ax->post_excerpt );

					$the_caption  = '';
					$show_caption = $this->get( 'attachment_audio_caption', 'hide' );

					if ( $show_caption == 'auto' ) {
						$the_caption = empty( $caption ) ? $title : $caption;
					} else if ( $show_caption == 'caption' ) {
						$the_caption = empty( $caption ) ? '' : $caption;
					}

					$atts_v = array(
						'src'      => wp_get_attachment_url( $attachment ),
						'loop'     => $atts['loop'],
						'autoplay' => $atts['autoplay']
					);

					$the_audio = wp_audio_shortcode( $atts_v );

					if ( empty( $the_caption ) ) {
						return $this->_tag( 'div', 'attachment', $the_audio );
					} else {
						$_cap = $this->_tag( 'div', 'caption', $the_caption, array( 'class' => 'wp-caption-text' ), array(), true );

						return $this->_tag( 'div', 'attachment bbpc-with-caption', $the_audio . $_cap );
					}
				} else {
					$defaults = apply_filters( 'bbpc_attachment_file_defaults', array(
						'target' => '_blank',
						'rel'    => '',
						'style'  => '',
						'class'  => '',
						'title'  => get_the_title( $attachment )
					), $attachment );

					$atts_a         = shortcode_atts( $defaults, $atts );
					$atts_a['href'] = wp_get_attachment_url( $attachment );

					return $this->_tag( 'a', 'attachment', get_the_title( $attachment ), $atts_a );
				}
			}
		}

		return '';
	}

	public function shortcode_postquote( $atts ) : string {
		$atts = $this->_atts( 'postquote', $atts );

		$quote = absint( $atts['quote'] );
		$post  = get_post( $quote );

		if ( $post && ( bbp_is_topic( $quote ) || bbp_is_reply( $quote ) ) ) {
			$url     = '';
			$ath     = '';
			$title   = '';
			$content = '';
			$private = false;
			$header  = $this->get( 'quote_title', 'user' );

			if ( bbp_is_topic( $quote ) ) {
				$url     = get_permalink( $quote );
				$ath     = $header == 'user' ? bbp_get_topic_author_display_name( $quote ) : '#' . $quote;
				$private = ! bbpc_is_user_allowed_to_topic( $quote );
			} else if ( bbp_is_reply( $quote ) ) {
				$url     = bbp_get_reply_url( $quote );
				$ath     = $header == 'user' ? bbp_get_reply_author_display_name( $quote ) : '#' . $quote;
				$private = ! bbpc_is_user_allowed_to_reply( $quote );
			}

			if ( ! empty( $url ) && $header != 'hide' ) {
				$full  = $header == 'user' ? $ath . ' ' . __( "wrote", "bbp-core" ) : $ath;
				$title = '<div class="bbpc-quote-title"><a href="' . $url . '">' . $full . ':</a></div>';
			}

			if ( $private ) {
				$content = __( "This quote contains content marked as private.", "bbp-core" );

				$atts['class'] = 'bbpc-quote-is-private';
			} else {
				bbpc()->set_inside_content_shortcode( $quote );

				if ( bbp_is_topic( $quote ) ) {
					$content = bbp_get_topic_content( $quote );
				} else if ( bbp_is_reply( $quote ) ) {
					$content = bbp_get_reply_content( $quote );
				}

				bbpc()->set_inside_content_shortcode( $quote, false );
			}

			return $this->_tag( 'blockquote', 'quote', $title . $content, $atts );
		}

		return '';
	}

	public function shortcode_quote( $atts, $content = null ) : string {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'quote', $atts );

		$title   = '';
		$private = false;
		$header  = $this->get( 'quote_title', 'user' );

		if ( $atts['quote'] != '' && $header != 'hide' ) {
			$url = '';
			$ath = '';

			$id = absint( $atts['quote'] );

			if ( bbp_is_topic( $id ) ) {
				$url     = get_permalink( $id );
				$ath     = $header == 'user' ? bbp_get_topic_author_display_name( $id ) : '#' . $id;
				$private = ! bbpc_is_user_allowed_to_topic( $id );
			} else if ( bbp_is_reply( $id ) ) {
				$url     = bbp_get_reply_url( $id );
				$ath     = $header == 'user' ? bbp_get_reply_author_display_name( $id ) : '#' . $id;
				$private = ! bbpc_is_user_allowed_to_reply( $id );
			}

			if ( ! empty( $url ) ) {
				$full  = $header == 'user' ? $ath . ' ' . __( "wrote", "bbp-core" ) : $ath;
				$title = '<div class="bbpc-quote-title"><a href="' . $url . '">' . $full . ':</a></div>';
			}
		}

		if ( $private ) {
			$content = __( "This quote contains content marked as private.", "bbp-core" );

			$atts['class'] = 'bbpc-quote-is-private';
		}

		return $this->_tag( 'blockquote', 'quote', $title . $content, $atts );
	}
}
