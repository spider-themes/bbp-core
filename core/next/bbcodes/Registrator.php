<?php

namespace Dev4Press\Plugin\GDBBX\BBCodes;

use Dev4Press\Plugin\GDBBX\Features\BBCodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Registrator {
	private $shortcodes = array();

	public function __construct() {
		$this->_init();
	}

	public static function instance() : Registrator {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Registrator();
		}

		return $instance;
	}

	public function run() {
		$valid = BBCodes::instance()->get_active_bbcodes();

		foreach ( array_keys( $this->shortcodes ) as $shortcode ) {
			if ( in_array( $shortcode, $valid ) ) {
				add_shortcode( $shortcode, array( $this, 'shortcode_' . $shortcode ) );
				add_shortcode( strtoupper( $shortcode ), array( $this, 'shortcode_' . $shortcode ) );
			}
		}
	}

	private function _init() {
		$this->shortcodes = array(
			'b'          => array(
				'name' => __( "Bold", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'i'          => array(
				'name' => __( "Italic", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'u'          => array(
				'name' => __( "Underline", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 ),
				'args' => array( 'style' => 'text-decoration: underline;' )
			),
			's'          => array(
				'name' => __( "Strikethrough", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'center'     => array(
				'name' => __( "Align Center", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 ),
				'args' => array( 'style' => 'text-align: center;' )
			),
			'right'      => array(
				'name' => __( "Align Right", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 ),
				'args' => array( 'style' => 'text-align: right;' )
			),
			'left'       => array(
				'name' => __( "Align Left", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 ),
				'args' => array( 'style' => 'text-align: left;' )
			),
			'justify'    => array(
				'name' => __( "Align Justify", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 ),
				'args' => array( 'style' => 'text-align: justify;' )
			),
			'sub'        => array(
				'name' => __( "Subscript", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'sup'        => array(
				'name' => __( "Superscript", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'pre'        => array(
				'name' => __( "Preformatted", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 1 )
			),
			'scode'      => array(
				'name' => __( "Source Code", "bbp-core" ),
				'atts' => array(
					'raw'       => 0,
					'lang'      => 'text',
					'line'      => 1,
					'gutter'    => true,
					'collapse'  => true,
					'class'     => '',
					'highlight' => ''
				)
			),
			'reverse'    => array(
				'name' => __( "Reverse", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 ),
				'args' => array( 'dir' => 'rtl' )
			),
			'list'       => array(
				'name' => __( "List", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'ol'         => array(
				'name' => __( "List: Ordered", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'ul'         => array(
				'name' => __( "List: Unordered", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'li'         => array(
				'name' => __( "List: Item", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'blockquote' => array(
				'name' => __( "Blockquote", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'area'       => array(
				'name' => __( "Area", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'border'     => array(
				'name' => __( "Border", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'div'        => array(
				'name' => __( "Block", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'br'         => array(
				'name' => __( "Line Break", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '' )
			),
			'hr'         => array(
				'name' => __( "Horizontal Line", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '' )
			),
			'anchor'     => array(
				'name' => __( "Anchor", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '' )
			),
			'size'       => array(
				'name' => __( "Font Size", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'color'      => array(
				'name' => __( "Font Color", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'hide'       => array(
				'name' => __( "Hide", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'spoiler'    => array(
				'name' => __( "Spoiler", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'color' => '', 'hover' => '', 'raw' => 0 )
			),
			'highlight'  => array(
				'name' => __( "Highlight", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'color' => '', 'background' => '', 'raw' => 0 )
			),
			'heading'    => array(
				'name' => __( "Heading", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'size' => '', 'raw' => 0 )
			),
			'forum'      => array(
				'name' => __( "Link Forum", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'topic'      => array(
				'name' => __( "Link Topic", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'reply'      => array(
				'name' => __( "Link Reply", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'url'        => array(
				'name' => __( "URL", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'target' => '_blank', 'rel' => '', 'raw' => 0 )
			),
			'email'      => array(
				'name' => __( "eMail", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'raw' => 0 )
			),
			'nfo'        => array(
				'name' => __( "NFO", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'title' => '' )
			),
			'embed'      => array(
				'name' => __( "Embed", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'width' => '', 'height' => '' )
			),
			'google'     => array(
				'name' => __( "Google Search", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'target' => '_blank', 'rel' => '', 'raw' => 0 )
			),
			'img'        => array(
				'name' => __( "Image", "bbp-core" ),
				'atts' => array(
					'style'  => '',
					'class'  => '',
					'alt'    => '',
					'title'  => '',
					'width'  => '',
					'height' => '',
					'float'  => ''
				)
			),
			'webshot'    => array(
				'name' => __( "Webshot", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'alt' => '', 'title' => '', 'width' => '' )
			),
			'youtube'    => array(
				'name' => __( "YouTube Video", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'width' => '', 'height' => '' )
			),
			'vimeo'      => array(
				'name' => __( "Vimeo Video", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'width' => '', 'height' => '' )
			),
			'note'       => array(
				'name' => __( "Note", "bbp-core" ),
				'atts' => array( 'raw' => 0 )
			),
			'iframe'     => array(
				'name' => __( "iframe", "bbp-core" ),
				'atts' => array( 'style' => '', 'class' => '', 'width' => '', 'height' => '', 'frameborder' => 0 )
			)
		);
	}

	private function _args( $code ) : array {
		return $this->shortcodes[ $code ]['args'] ?? array();
	}

	private function _atts( $code, $atts = array() ) : array {
		if ( isset( $atts[0] ) ) {
			$atts[ $code ] = substr( $atts[0], 1 );
			unset( $atts[0] );
		}

		$default          = $this->shortcodes[ $code ]['atts'];
		$default[ $code ] = '';

		if ( $code == 'spoiler' ) {
			$default['color'] = BBCodes::instance()->get( 'spoiler_color' );
			$default['hover'] = BBCodes::instance()->get( 'spoiler_hover' );
		} else if ( $code == 'highlight' ) {
			$default['color']      = BBCodes::instance()->get( 'highlight_color' );
			$default['background'] = BBCodes::instance()->get( 'highlight_background' );
		} else if ( $code == 'heading' ) {
			$default['size'] = BBCodes::instance()->get( 'heading_size' );
		}

		return shortcode_atts( $default, $atts );
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

	private function _content( $content, $raw = false ) {
		if ( $raw ) {
			return $content;
		} else {
			return do_shortcode( $content );
		}
	}

	private function _tag( $tag, $name, $content = null, $atts = array(), $args = array(), $no_class = false ) : string {
		$standard   = $no_class ? array() : array( 'class' => 'gdbbx-bbcode-' . $name );
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

		return $render;
	}

	private function _simple( $code, $tag, $name, $atts, $content = null ) : string {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( $code, $atts );
		$args = $this->_args( $code );

		return $this->_tag( $tag, $name, $content, $atts, $args );
	}

	private function _webshot( $url, $width = 0 ) {
		$_url = is_ssl() ? 'https' : 'http';
		$_url .= '://s.wordpress.com/mshots/v1/' . urlencode( $url );

		if ( $width > 0 ) {
			$_url .= '?w=' . $width;
		}

		return $_url;
	}

	private function _content_cleanup( $content ) {
		$clean = trim( $content, " \t\n\r\0\x0B{}" );

		return preg_replace( "/(^\s+)|(\s+$)/us", '', $clean );
	}

	public function shortcode_b( $atts, $content = null ) {
		return $this->_simple( 'b', 'strong', 'bold', $atts, $content );
	}

	public function shortcode_i( $atts, $content = null ) {
		return $this->_simple( 'i', 'em', 'italic', $atts, $content );
	}

	public function shortcode_u( $atts, $content = null ) {
		return $this->_simple( 'u', 'span', 'underline', $atts, $content );
	}

	public function shortcode_s( $atts, $content = null ) {
		return $this->_simple( 's', 'del', 'strikethrough', $atts, $content );
	}

	public function shortcode_right( $atts, $content = null ) {
		return $this->_simple( 'right', 'div', 'align-right', $atts, $content );
	}

	public function shortcode_center( $atts, $content = null ) {
		return $this->_simple( 'center', 'div', 'align-center', $atts, $content );
	}

	public function shortcode_left( $atts, $content = null ) {
		return $this->_simple( 'left', 'div', 'align-left', $atts, $content );
	}

	public function shortcode_justify( $atts, $content = null ) {
		return $this->_simple( 'justify', 'div', 'align-justify', $atts, $content );
	}

	public function shortcode_sub( $atts, $content = null ) {
		return $this->_simple( 'sub', 'sub', 'sub', $atts, $content );
	}

	public function shortcode_sup( $atts, $content = null ) {
		return $this->_simple( 'sup', 'sup', 'sup', $atts, $content );
	}

	public function shortcode_pre( $atts, $content = null ) {
		return $this->_simple( 'pre', 'pre', 'pre', $atts, $content );
	}

	public function shortcode_border( $atts, $content = null ) {
		return $this->_simple( 'border', 'fieldset', 'border', $atts, $content );
	}

	public function shortcode_reverse( $atts, $content = null ) {
		return $this->_simple( 'reverse', 'bdo', 'reverse', $atts, $content );
	}

	public function shortcode_blockquote( $atts, $content = null ) {
		return $this->_simple( 'blockquote', 'blockquote', 'blockquote', $atts, $content );
	}

	public function shortcode_heading( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'heading', $atts );
		$size = absint( $atts['size'] );

		if ( $size < 1 || $size > 6 ) {
			$size = 3;
		}

		$tag = 'h' . $size;

		unset( $atts['size'] );

		return $this->_tag( $tag, 'heading', $content, $atts );
	}

	public function shortcode_highlight( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'highlight', $atts );
		$args = array( 'style' => 'color: ' . $atts['color'] . '; background: ' . $atts['background'] );

		unset( $atts['color'] );
		unset( $atts['background'] );

		return $this->_tag( 'span', 'highlight', $content, $atts, $args );
	}

	public function shortcode_spoiler( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'spoiler', $atts );
		$args = array( 'style'      => 'color: ' . $atts['color'] . '; background: ' . $atts['color'],
		               'data-color' => $atts['color'],
		               'data-hover' => $atts['hover']
		);

		unset( $atts['color'] );
		unset( $atts['hover'] );

		return $this->_tag( 'span', 'spoiler', $content, $atts, $args );
	}

	public function shortcode_list( $atts, $content = null ) {
		return $this->_simple( 'list', 'ol', 'ol', $atts, $content );
	}

	public function shortcode_ol( $atts, $content = null ) {
		return $this->_simple( 'ol', 'ol', 'ol', $atts, $content );
	}

	public function shortcode_ul( $atts, $content = null ) {
		return $this->_simple( 'ul', 'ul', 'ul', $atts, $content );
	}

	public function shortcode_li( $atts, $content = null ) {
		return $this->_simple( 'li', 'li', 'li', $atts, $content );
	}

	public function shortcode_div( $atts, $content = null ) {
		return $this->_simple( 'div', 'div', 'div', $atts, $content );
	}

	public function shortcode_size( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'size', $atts );
		$args = isset( $this->shortcodes['size']['args'] ) ? $this->shortcodes['size']['args'] : array();

		if ( $atts['size'] != '' ) {
			$args['style'] = 'font-size: ' . $atts['size'];

			if ( is_numeric( $atts['size'] ) ) {
				$args['style'] .= 'px';
			}

			unset( $atts['size'] );
		}

		return $this->_tag( 'span', 'font-size', $content, $atts, $args );
	}

	public function shortcode_color( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'color', $atts );
		$args = isset( $this->shortcodes['color']['args'] ) ? $this->shortcodes['color']['args'] : array();

		if ( $atts['color'] != '' ) {
			$args['style'] = 'color: ' . $atts['color'];

			unset( $atts['color'] );
		}

		return $this->_tag( 'span', 'font-color', $content, $atts, $args );
	}

	public function shortcode_area( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'area', $atts );
		$args = $this->_args( 'area' );

		if ( $atts['area'] != '' ) {
			$content = '<legend>' . $atts['area'] . '</legend>' . $content;
		}

		return $this->_tag( 'fieldset', 'area', $content, $atts, $args );
	}

	public function shortcode_anchor( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'anchor', $atts );
		$args = $this->_args( 'anchor' );

		if ( $atts['anchor'] != '' ) {
			$args['name'] = $atts['anchor'];
		}

		return $this->_tag( 'a', 'anchor', $content, $atts, $args );
	}

	public function shortcode_br( $atts ) {
		$atts = $this->_atts( 'br', $atts );

		return $this->_tag( 'br', 'br', null, $atts );
	}

	public function shortcode_hr( $atts ) {
		$atts = $this->_atts( 'hr', $atts );

		return $this->_tag( 'hr', 'hr', null, $atts );
	}

	public function shortcode_nfo( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts  = $this->_atts( 'nfo', $atts );
		$title = $atts['title'] == '' ? 'NFO' : $atts['title'];

		$render = '<table class="gdbbx-bbcode-nfo ' . $atts['class'] . '" style="' . $atts['style'] . '"><tbody><tr><td class="gdbbx-bbcode-el-title">' . $title . ':</td></tr>';
		$render .= '<tr><td class="gdbbx-bbcode-el-content"><pre>' . $content . '</pre></td></tr></tbody></table>';

		return $render;
	}

	public function shortcode_reply( $atts, $content = null ) {
		$atts = $this->_atts( 'reply', $atts );

		$label = '';
		if ( $atts['reply'] != '' ) {
			$id = absint( $atts['reply'] );

			if ( is_string( $content ) ) {
				$label = $content;
			}
		} else {
			$id = $content;
		}

		$atts['href'] = get_permalink( $id );

		if ( $label == '' ) {
			$label = $atts['href'];
		}

		return $this->_tag( 'a', 'reply-link', $label, $atts );
	}

	public function shortcode_topic( $atts, $content = null ) {
		$atts = $this->_atts( 'topic', $atts );

		$label = '';
		if ( $atts['topic'] != '' ) {
			$id = absint( $atts['topic'] );

			if ( is_string( $content ) ) {
				$label = $content;
			}
		} else {
			$id = $content;
		}

		$atts['href'] = get_permalink( $id );

		if ( $label == '' ) {
			$label = $atts['href'];
		}

		return $this->_tag( 'a', 'topic-link', $label, $atts );
	}

	public function shortcode_forum( $atts, $content = null ) {
		$atts = $this->_atts( 'v', $atts );

		$label = '';
		if ( $atts['forum'] != '' ) {
			$id = absint( $atts['forum'] );

			if ( is_string( $content ) ) {
				$label = $content;
			}
		} else {
			$id = $content;
		}

		$atts['href'] = get_permalink( $id );

		if ( $label == '' ) {
			$label = $atts['href'];
		}

		return $this->_tag( 'a', 'forum-link', $label, $atts );
	}

	public function shortcode_hide( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'hide', $atts );

		$count = 0;
		if ( $atts['hide'] != '' ) {
			if ( $atts['hide'] == 'reply' ) {
				$count = - 1;
			} else if ( $atts['hide'] == 'thanks' ) {
				$count = - 2;
			} else {
				$count = absint( $atts['hide'] );
			}
		}

		$template = '';

		$topic = bbp_get_topic_id();
		$user  = bbp_get_current_user_id();

		if ( is_user_logged_in() ) {
			if ( bbp_is_user_keymaster() && BBCodes::instance()->get( 'hide_keymaster_always_allowed' ) ) {
				$to_hide = false;
			} else if ( $user == bbp_get_topic_author_id() ) {
				$to_hide = false;
			} else if ( $count == - 2 ) {
				$template = BBCodes::instance()->get( 'hide_content_thanks' );

				$to_hide = ! gdbbx_check_if_user_said_thanks_to_topic( $topic, $user );
			} else if ( $count == - 1 ) {
				$template = BBCodes::instance()->get( 'hide_content_reply' );

				$to_hide = ! gdbbx_check_if_user_replied_to_topic( $topic, $user );
			} else if ( $count > 0 ) {
				$total = bbp_get_user_reply_count_raw( bbp_get_current_user_id() ) +
				         bbp_get_user_topic_count_raw( bbp_get_current_user_id() );

				$to_hide = $count > $total;

				if ( $to_hide ) {
					$_tpl     = BBCodes::instance()->get( 'hide_content_count' );
					$template = str_replace( '%post_count%', $count, $_tpl );
				}
			} else {
				$to_hide = false;
			}
		} else {
			$template = BBCodes::instance()->get( 'hide_content_normal' );

			$to_hide = true;
		}

		$render = '<div class="gdbbx-bbcode-hide gdbbx-hide-' . ( $to_hide ? 'hidden' : 'visible' ) . '">';
		$render .= '<div class="gdbbx-hide-title">' . BBCodes::instance()->get( 'hide_title' ) . '</div>';
		$render .= '<div class="gdbbx-hide-content">';

		if ( $to_hide ) {
			$render .= do_shortcode( __( $template, "bbp-core" ) );
		} else {
			$render .= do_shortcode( $content );
		}

		$render .= '</div></div>';

		return $render;
	}

	public function shortcode_embed( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts    = $this->_atts( 'embed', $atts );
		$content = $this->_content_cleanup( $content );

		if ( $atts['embed'] != '' ) {
			$parts = explode( 'x', $atts['embed'], 2 );

			if ( count( $parts ) == 2 ) {
				$args['width']  = absint( $parts[0] );
				$args['height'] = absint( $parts[1] );
			}
		}

		$data = array();
		if ( $atts['width'] > 0 ) {
			$data['width'] = $atts['width'];
		}

		if ( $atts['height'] > 0 ) {
			$data['height'] = $atts['height'];
		}

		global $wp_embed;

		return $wp_embed->shortcode( $data, $content );
	}

	public function shortcode_url( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts    = $this->_atts( 'url', $atts );
		$content = $this->_content_cleanup( $content );

		$args = $this->_args( 'url' );

		if ( $atts['url'] != '' ) {
			$args['href'] = str_replace( array( '"', "'" ), '', $atts['url'] );
		} else {
			$args['href'] = $content;
		}

		return $this->_tag( 'a', 'url', $content, $atts, $args );
	}

	public function shortcode_email( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts    = $this->_atts( 'email', $atts );
		$content = $this->_content_cleanup( $content );

		$args = $this->_args( 'email' );

		if ( $atts['email'] != '' ) {
			$args['href'] = $atts['email'];
		} else {
			$args['href'] = $content;
			$content      = antispambot( $content );
		}

		$args['href'] = 'mailto:' . antispambot( $args['href'], 1 );

		return $this->_tag( 'a', 'url', $content, $atts, $args );
	}

	public function shortcode_webshot( $atts, $content = null ) {
		if ( is_null( $content ) || $content == '' ) {
			return '';
		}

		$atts    = $this->_atts( 'webshot', $atts );
		$content = $this->_content_cleanup( $content );

		$args        = $this->_args( 'webshot' );
		$args['src'] = $this->_webshot( $content, $args['width'] );

		$image = $this->_tag( 'img', 'image', null, $atts, $args );

		return $this->_tag( 'a', 'url', $image, $atts, array( 'href' => $args['src'] ) );
	}

	public function shortcode_img( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts    = $this->_atts( 'img', $atts );
		$content = $this->_content_cleanup( $content );

		$args        = $this->_args( 'img' );
		$args['src'] = $content;

		if ( $atts['img'] != '' ) {
			$parts = explode( 'x', $atts['img'], 2 );

			if ( count( $parts ) == 2 ) {
				$args['width']  = absint( $parts[0] );
				$args['height'] = absint( $parts[1] );
			}
		}

		if ( $atts['float'] == 'left' || $atts['float'] == 'right' ) {
			$atts['style'] .= ';float:' . $atts['float'] . ';';
		}

		unset( $atts['float'] );

		return $this->_tag( 'img', 'image', null, $atts, $args );
	}

	public function shortcode_google( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts = $this->_atts( 'google', $atts );

		$args = isset( $this->shortcodes['google']['args'] ) ? $this->shortcodes['google']['args'] : array();

		$protocol = is_ssl() ? 'https' : 'http';
		$link     = $protocol . '://www.google.';

		if ( $atts['google'] != '' ) {
			$link .= $atts['google'];
		} else {
			$link .= 'com';
		}

		$link .= '/search?q=' . urlencode( $content );

		$args['href'] = $link;

		return $this->_tag( 'a', 'google', $content, $atts, $args );
	}

	public function shortcode_youtube( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts    = $this->_atts( 'youtube', $atts );
		$content = $this->_content_cleanup( $content );

		if ( strpos( $content, 'youtube.com' ) === false && strpos( $content, 'youtu.be' ) === false ) {
			$protocol = is_ssl() ? 'https' : 'http';
			$url      = $protocol . '://www.youtube.com/watch?v=' . $content;
		} else {
			$url = $content;

			if ( is_ssl() && substr( $url, 0, 5 ) != 'https' ) {
				$url = 'https' . substr( $url, 4 );
			}
		}

		if ( $atts['youtube'] != '' ) {
			$parts = explode( 'x', $atts['youtube'], 2 );

			if ( count( $parts ) == 2 ) {
				$args['width']  = absint( $parts[0] );
				$args['height'] = absint( $parts[1] );
			}
		}

		$data = array();
		if ( $atts['width'] > 0 ) {
			$data['width'] = $atts['width'];
		}

		if ( $atts['height'] > 0 ) {
			$data['height'] = $atts['height'];
		}

		global $wp_embed;

		return $wp_embed->shortcode( $data, $url );
	}

	public function shortcode_vimeo( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts    = $this->_atts( 'vimeo', $atts );
		$content = $this->_content_cleanup( $content );

		if ( strpos( $content, 'vimeo.com' ) === false ) {
			$protocol = is_ssl() ? 'https' : 'http';
			$url      = $protocol . '://www.vimeo.com/' . $content;
		} else {
			$url = $content;

			if ( is_ssl() && substr( $url, 0, 5 ) != 'https' ) {
				$url = 'https' . substr( $url, 4 );
			}
		}

		if ( $atts['vimeo'] != '' ) {
			$parts = explode( 'x', $atts['vimeo'], 2 );

			if ( count( $parts ) == 2 ) {
				$args['width']  = absint( $parts[0] );
				$args['height'] = absint( $parts[1] );
			}
		}

		$data = array();
		if ( $atts['width'] > 0 ) {
			$data['width'] = $atts['width'];
		}

		if ( $atts['height'] > 0 ) {
			$data['height'] = $atts['height'];
		}

		global $wp_embed;

		return $wp_embed->shortcode( $data, $url );
	}

	public function shortcode_iframe( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$atts    = $this->_atts( 'iframe', $atts );
		$content = $this->_content_cleanup( $content );

		return '<iframe src="' . $content . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '" frameborder="' . $atts['frameborder'] . '"></iframe>';
	}

	public function shortcode_note( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		return '<!-- ' . $this->_content( $content ) . ' -->';
	}

	public function shortcode_scode( $atts, $content = null ) {
		if ( is_null( $content ) ) {
			return '';
		}

		$args = array( 'class' => 'gdbbx-bbcode-scode', 'raw' => 1 );
		$data = array();

		$atts = $this->_atts( 'scode', $atts );

		$lang = strtolower( $atts['lang'] );

		Source::instance()->enqueue();

		if ( ! Source::instance()->is_brush_valid( $lang ) ) {
			$lang = 'generic';
		}

		$data['data-enlighter-language']    = $lang;
		$data['data-enlighter-theme']       = strtolower( BBCodes::instance()->get( 'scode_enlighter' ) );
		$data['data-enlighter-linenumbers'] = ( $atts['gutter'] == 'true' ? 'true' : 'false' );
		$data['data-enlighter-lineoffset']  = absint( $atts['line'] );

		if ( ! empty( $atts['highlight'] ) ) {
			$highlight = explode( ',', $atts['highlight'] );
			$highlight = array_map( 'trim', $highlight );
			$highlight = array_map( 'absint', $highlight );
			$highlight = array_filter( $highlight );

			if ( ! empty( $highlight ) ) {
				$data['data-enlighter-highlight'] = join( ',', $highlight );
			}
		}

		return $this->_tag( 'pre', 'pre', $content, $args, $data );
	}
}