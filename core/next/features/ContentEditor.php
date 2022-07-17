<?php

namespace SpiderDevs\Plugin\BBPC\Features;

use SpiderDevs\Plugin\BBPC\Base\Feature;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContentEditor extends Feature {
	public $feature_name = 'content-editor';
	public $settings = array(
		'topic'                       => 'textarea',
		'reply'                       => 'textarea',
		'bbcodes_topic_size'          => 'medium',
		'bbcodes_topic_editor_fix'    => true,
		'bbcodes_reply_size'          => 'medium',
		'bbcodes_reply_editor_fix'    => true,
		'tinymce_topic_teeny'         => false,
		'tinymce_topic_media_buttons' => false,
		'tinymce_topic_wpautop'       => true,
		'tinymce_topic_quicktags'     => true,
		'tinymce_topic_textarea_rows' => 12,
		'tinymce_reply_teeny'         => false,
		'tinymce_reply_media_buttons' => false,
		'tinymce_reply_wpautop'       => true,
		'tinymce_reply_quicktags'     => true,
		'tinymce_reply_textarea_rows' => 12
	);
	private $tinymce_keys = array(
		'teeny',
		'media_buttons',
		'wpautop',
		'quicktags',
		'textarea_rows'
	);

	public function __construct() {
		parent::__construct();

		add_action( 'bbp_theme_before_topic_form', array( $this, 'init_topic' ) );
		add_action( 'bbp_theme_before_reply_form', array( $this, 'init_reply' ) );
	}

	public static function instance() : ContentEditor {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new ContentEditor();
		}

		return $instance;
	}

	public function for_topic() {
		return $this->get( 'topic' );
	}

	public function for_reply() {
		return $this->get( 'reply' );
	}

	public function init_topic() {
		switch ( $this->for_topic() ) {
			case 'bbcodes':
				$this->do_bbcodes();
				break;
			case 'tinymce':
				$this->do_tinymce();
				break;
			case 'richarea':
				$this->do_richarea();
				break;
			default:
			case 'textarea':
				$this->do_textarea();
				break;
		}
	}

	public function init_reply() {
		switch ( $this->for_reply() ) {
			case 'bbcodes':
				$this->do_bbcodes( 'reply' );
				break;
			case 'tinymce':
				$this->do_tinymce( 'reply' );
				break;
			case 'richarea':
				$this->do_richarea( 'reply' );
				break;
			default:
			case 'textarea':
				$this->do_textarea( 'reply' );
				break;
		}
	}

	public function tinymce_apply( $args ) {
		$context = $args['context'];

		if ( in_array( $context, array( 'topic', 'reply' ) ) ) {
			$args['tinymce'] = true;

			$base = 'tinymce_' . $context . '_';

			foreach ( $this->tinymce_keys as $key ) {
				if ( isset( $this->settings[ $base . $key ] ) ) {
					$args[ $key ] = $this->settings[ $base . $key ];
				}
			}

			bbpc_roles()->update_roles();
			bbpc_roles()->update_role_before_render();

			Enqueue::instance()->tinymce();
		}

		return $args;
	}

	public function bbcodes_display_topic() {
		$this->bbcodes_display();
	}

	public function bbcodes_display_reply() {
		$this->bbcodes_display( 'reply' );
	}

	public function bbcodes_editor_fix( $args ) {
		$args['editor_class'] .= ' bbpc-editor-fix';

		return $args;
	}

	private function do_bbcodes( $form = 'topic' ) {
		$this->do_textarea();

		if ( bbpc_is_bbcodes_toolbar_available() ) {
			if ( $form == 'topic' ) {
				add_action( 'bbp_theme_before_topic_form_content', array( $this, 'bbcodes_display_topic' ), 10000 );
			} else if ( $form == 'reply' ) {
				add_action( 'bbp_theme_before_reply_form_content', array( $this, 'bbcodes_display_reply' ), 10000 );
			}

			$this->bbcodes_apply_editor_fix();
		}
	}

	private function do_tinymce( $form = 'topic' ) {
		$this->do_richarea();

		add_filter( 'bbp_after_get_the_content_parse_args', array( $this, 'tinymce_apply' ) );
	}

	private function do_richarea( $form = 'topic' ) {
		add_filter( 'bbp_use_wp_editor', '__return_true', 1000 );
	}

	private function do_textarea( $form = 'topic' ) {
		add_filter( 'bbp_use_wp_editor', '__return_false', 1000 );
	}

	private function bbcodes_display( $form = 'topic' ) {
		$size = $this->get( 'bbcodes_' . $form . '_size' );

		echo '<div class="bbpc-newpost-bbcodes">';

		\Dev4Press\Plugin\BBPC\BBCodes\Toolbar::instance()->display( $size );

		echo '</div>';
	}

	private function bbcodes_apply_editor_fix( $form = 'topic' ) {
		if ( $this->get( 'bbcodes_' . $form . '_editor_fix' ) ) {
			add_filter( 'bbp_after_get_the_content_parse_args', array( $this, 'bbcodes_editor_fix' ) );
		}
	}
}
