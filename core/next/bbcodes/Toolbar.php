<?php

namespace Dev4Press\Plugin\GDBBX\BBCodes;

use Dev4Press\Plugin\GDBBX\Basic\Enqueue;
use Dev4Press\Plugin\GDBBX\Features\BBCodes;

class Toolbar {
	private $_buttons;
	private $_available;

	private $_sizes = array(
		'small',
		'medium',
		'large'
	);

	public function __construct() {
		$this->_available = BBCodes::instance()->get_toolbar_bbcodes();

		$this->_buttons = array(
			'b'          => array(
				'icon'  => 'bold',
				'title' => __( "Bold", "bbp-core" ),
				'code'  => '(b){content}(/b)'
			),
			'i'          => array(
				'icon'  => 'italic',
				'title' => __( "Italic", "bbp-core" ),
				'code'  => '(i){content}(/i)'
			),
			'u'          => array(
				'icon'  => 'underline',
				'title' => __( "Underline", "bbp-core" ),
				'code'  => '(u){content}(/u)'
			),
			's'          => array(
				'icon'  => 'strikethrough',
				'title' => __( "Strikethrough", "bbp-core" ),
				'code'  => '(s){content}(/s)'
			),
			'center'     => array(
				'icon'  => 'align-center',
				'title' => __( "Align: Center", "bbp-core" ),
				'code'  => '(center){content}(/center)'
			),
			'right'      => array(
				'icon'  => 'align-right',
				'title' => __( "Align: Right", "bbp-core" ),
				'code'  => '(right){content}(/right)'
			),
			'left'       => array(
				'icon'  => 'align-left',
				'title' => __( "Align: Left", "bbp-core" ),
				'code'  => '(left){content}(/left)'
			),
			'justify'    => array(
				'icon'  => 'align-justify',
				'title' => __( "Align: Justify", "bbp-core" ),
				'code'  => '(justify){content}(/justify)'
			),
			'sub'        => array(
				'icon'  => 'subscript',
				'title' => __( "Subscript", "bbp-core" ),
				'code'  => '(sub){content}(/sub)'
			),
			'sup'        => array(
				'icon'  => 'superscript',
				'title' => __( "Superscript", "bbp-core" ),
				'code'  => '(sup){content}(/sup)'
			),
			'br'         => array(
				'icon'  => 'turn-down',
				'title' => __( "Line Break", "bbp-core" ),
				'code'  => '(br)'
			),
			'hr'         => array(
				'icon'  => 'minus',
				'title' => __( "Horizontal Line", "bbp-core" ),
				'code'  => '(hr)'
			),
			'size'       => array(
				'icon'  => 'text-size',
				'title' => __( "Font Size", "bbp-core" ),
				'code'  => '(size size=\'{size}\'){content}(/size)'
			),
			'color'      => array(
				'icon'  => 'droplet',
				'title' => __( "Font Color", "bbp-core" ),
				'code'  => '(color color=\'{color}\'){content}(/color)'
			),
			'heading'    => array(
				'icon'  => 'heading',
				'title' => __( "Heading", "bbp-core" ),
				'code'  => '(heading){content}(/heading)'
			),
			'highlight'  => array(
				'icon'  => 'highlighter',
				'title' => __( "Highlight", "bbp-core" ),
				'code'  => '(highlight){content}(/highlight)'
			),
			'scode'      => array(
				'icon'  => 'code',
				'title' => __( "Source Code", "bbp-core" ),
				'code'  => '(scode lang=\'{language}\'){content}(/scode)'
			),
			'pre'        => array(
				'icon'  => 'formatted',
				'title' => __( "Preformatted", "bbp-core" ),
				'code'  => '(pre){content}(/pre)'
			),
			'blockquote' => array(
				'icon'  => 'quote-right',
				'title' => __( "Blockquote", "bbp-core" ),
				'code'  => '(blockquote){content}(/blockquote)'
			),
			'ol'         => array(
				'icon'  => 'list-ol',
				'title' => __( "List: Ordered", "bbp-core" ),
				'code'  => '(ol){content}(/ol)'
			),
			'ul'         => array(
				'icon'  => 'list-ul',
				'title' => __( "List: Unordered", "bbp-core" ),
				'code'  => '(ul){content}(/ul)'
			),
			'li'         => array(
				'icon'  => 'list',
				'title' => __( "List: Item", "bbp-core" ),
				'code'  => '(li){content}(/li)'
			),
			'url'        => array(
				'icon'  => 'link',
				'title' => __( "URL", "bbp-core" ),
				'code'  => '(url){url}(/url)'
			),
			'email'      => array(
				'icon'  => 'envelope',
				'title' => __( "Email", "bbp-core" ),
				'code'  => '(email){email}(/email)'
			),
			'spoiler'    => array(
				'icon'  => 'rectangle',
				'title' => __( "Spoiler", "bbp-core" ),
				'code'  => '(spoiler){content}(/spoiler)'
			),
			'hide'       => array(
				'icon'  => 'ban',
				'title' => __( "Hide", "bbp-core" ),
				'code'  => '(hide hide=\'reply\'){content}(/hide)'
			),
			'forum'      => array(
				'icon'  => 'folder',
				'title' => __( "Forum", "bbp-core" ),
				'code'  => '(forum){id}(/forum)'
			),
			'topic'      => array(
				'icon'  => 'message-text',
				'title' => __( "Topic", "bbp-core" ),
				'code'  => '(topic){id}(/topic)'
			),
			'reply'      => array(
				'icon'  => 'message-check',
				'title' => __( "Reply", "bbp-core" ),
				'code'  => '(reply){id}(/reply)'
			),
			'img'        => array(
				'icon'  => 'image',
				'title' => __( "Image", "bbp-core" ),
				'code'  => '(img){url}(/img)'
			),
			'youtube'    => array(
				'icon'  => 'youtube',
				'title' => __( "YouTube Video", "bbp-core" ),
				'code'  => '(youtube){url}(/youtube)'
			),
			'vimeo'      => array(
				'icon'  => 'vimeo',
				'title' => __( "Vimeo Video", "bbp-core" ),
				'code'  => '(vimeo){url}(/vimeo)'
			),
			'reverse'    => array(
				'icon'  => 'backward',
				'title' => __( "Reverse", "bbp-core" ),
				'code'  => '(reverse){content}(/reverse)'
			),
			'anchor'     => array(
				'icon'  => 'anchor',
				'title' => __( "Anchor", "bbp-core" ),
				'code'  => '(anchor anchor=\'{anchor}\'){content}(/anchor)'
			),
			'border'     => array(
				'icon'  => 'border-outer',
				'title' => __( "Border", "bbp-core" ),
				'code'  => '(border){content}(/border)'
			),
			'area'       => array(
				'icon'  => 'area',
				'title' => __( "Area", "bbp-core" ),
				'code'  => '(area area=\'{title}\'){content}(/area)'
			),
			'list'       => array(
				'icon'  => 'list-alt',
				'title' => __( "List", "bbp-core" ),
				'code'  => '(list){content}(/list)'
			),
			'quote'      => array(
				'icon'  => 'quote-left',
				'title' => __( "Quote", "bbp-core" ),
				'code'  => '(quote){content}(/quote)'
			),
			'nfo'        => array(
				'icon'  => 'square-info',
				'title' => __( "NFO", "bbp-core" ),
				'code'  => '(nfo title=\'{title}\'){content}(/nfo)'
			),
			'webshot'    => array(
				'icon'  => 'camera',
				'title' => __( "Webshot", "bbp-core" ),
				'code'  => '(webshot width={width}){url}(/webshot)'
			),
			'embed'      => array(
				'icon'  => 'square-plus',
				'title' => __( "Embed using oEmbed", "bbp-core" ),
				'code'  => '(embed){url}(/embed)'
			),
			'google'     => array(
				'icon'  => 'google',
				'title' => __( "Google Search URL", "bbp-core" ),
				'code'  => '(google){content}(/google)'
			),
			'iframe'     => array(
				'icon'  => 'frame',
				'title' => __( "Iframe", "bbp-core" ),
				'code'  => '(iframe){url}(/iframe)'
			),
			'note'       => array(
				'icon'  => 'file',
				'title' => __( "Hidden Note", "bbp-core" ),
				'code'  => '(note){content}(/note)'
			)
		);
	}

	public static function instance() : Toolbar {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Toolbar();
		}

		return $instance;
	}

	public function display( $size = false ) {
		$size = $size === false || !in_array( $size, $this->_sizes ) ? 'medium' : $size;

		$this->render( $size );

		Enqueue::instance()->toolbar();
	}

	private function render( $size = 'small' ) {
		$class = 'gdbbx-bbcodes-toolbar gdbbx-buttonbar-size-' . $size;

		echo '<div role="toolbar" class="' . $class . '">';
		echo '<div class="gdbbx-buttonbar-inner">';

		do_action( 'gdbbx_bbcode_toolbar_buttons_before' );

		$id = 1;
		foreach ( $this->_buttons as $code => $obj ) {
			if ( in_array( $code, $this->_available ) ) {
				$this->button( $code, $obj, $id );

				$id ++;
			}
		}

		do_action( 'gdbbx_bbcode_toolbar_buttons_after' );

		echo '</div>';
		echo '</div>';
	}

	private function button( $code, $obj, $id ) {
		$button_id = 'gdbbx-button-' . $id;

		echo '<div id="' . $button_id . '" role="button" class="gdbbx-buttonbar-button gdbbx-buttonbar-button-' . $code . '" aria-labelledby="' . $button_id . '" aria-label="' . $obj['title'] . '">';
		echo '<button class="gdbbx-button" role="presentation" type="button" title="' . $obj['title'] . '" data-code="' . $code . '" data-bbcode="' . $obj['code'] . '">' . $this->icon( $code, $obj ) . '</button>';
		echo '</div>';
	}

	private function icon( $code, $obj ) {
		$icon = apply_filters( 'gdbbx_bbcode_toolbar_button_icon', false, $code, $obj );

		if ( $icon !== false ) {
			return $icon;
		}

		$class = "gdbbx-icon gdbbx-icon-" . $obj['icon'];

		if ( $code == 'br' ) {
			$class .= " gdbbx-rotate-90";
		}

		return '<i aria-hidden="true" class="' . $class . '"></i><span class="gdbbx-accessibility-show-for-sr">' . $obj['title'] . '</span>';
	}
}