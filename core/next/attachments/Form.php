<?php

namespace SpiderDevs\Plugin\BBPC\Attachments;

use SpiderDevs\Plugin\BBPC\Basic\BB;
use SpiderDevs\Plugin\BBPC\Basic\Enqueue;
use SpiderDevs\Plugin\BBPC\Features\Attachments;
use SpiderDevs\Plugin\BBPC\Features\Icons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Form {
	public $id = 0;
	public $file_size = 0;
	public $attachments = array();

	public $skip_missing;

	public function __construct() {
	}

	public static function instance() : Form {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Form();
		}

		return $instance;
	}

	public function run() {
		add_filter( 'bbpc_script_values', array( $this, 'script_values' ) );

		add_action( bbpc_attachments()->get( 'form_position_reply' ), array( $this, 'embed_form_reply' ) );
		add_action( bbpc_attachments()->get( 'form_position_topic' ), array( $this, 'embed_form_topic' ) );

		add_action( 'bbpc_attachments_form_notices', array( $this, 'form_notices' ) );
	}

	public function script_values( $values ) : array {
		$forum_id = BB::i()->get_forum_id();

		$allowed_extensions  = false;
		$insert_into_content = false;

		if ( bbpc_attachments()->get( 'insert_into_content' ) ) {
			if ( bbpc_attachments()->allowed( 'insert_into_content', 'attachments_insert_into_content' ) ) {
				$insert_into_content = true;
			}
		}

		if ( bbpc_attachments()->get( 'mime_types_limit_active' ) ) {
			$allowed_extensions = strtolower( join( ' ', bbpc_attachments()->get_file_extensions( $forum_id ) ) );
		}

		$values['load'][]      = 'attachments';
		$values['attachments'] = apply_filters( 'bbpc_attachments_script_values', array(
			'method'              => bbpc_attachments()->get( 'method' ),
			'max_files'           => bbpc_attachments()->get_max_files( $forum_id ),
			'max_size'            => bbpc_attachments()->get_file_size( $forum_id ) * 1024,
			'limiter'             => ! bbpc_attachments()->is_no_limit(),
			'auto_new_file'       => bbpc_attachments()->get( 'enhanced_auto_new' ),
			'set_caption_file'    => bbpc_attachments()->get( 'enhanced_set_caption' ),
			'allowed_extensions'  => $allowed_extensions,
			'insert_into_content' => $insert_into_content,
			'text'                => array(
				'select_file'               => _x( "Select File", "Attachments Dialog", "bbp-core" ),
				'file_name'                 => _x( "Name", "Attachments Dialog, File Name", "bbp-core" ),
				'file_size'                 => _x( "Size", "Attachments Dialog, File Size", "bbp-core" ),
				'file_type'                 => _x( "Extension", "Attachments Dialog, File Extension", "bbp-core" ),
				'file_validation'           => _x( "Error!", "Attachments Dialog, Validation", "bbp-core" ),
				'file_validation_size'      => _x( "The file is too big.", "Attachments Dialog, Validation", "bbp-core" ),
				'file_validation_type'      => _x( "File type not allowed.", "Attachments Dialog, Validation", "bbp-core" ),
				'file_validation_duplicate' => _x( "You can't select the same file twice.", "Attachments Dialog, Validation", "bbp-core" ),
				'file_remove'               => _x( "Remove this file", "Attachments Dialog", "bbp-core" ),
				'file_shortcode'            => _x( "Insert into content", "Attachments Dialog", "bbp-core" ),
				'file_caption'              => _x( "Set file caption", "Attachments Dialog", "bbp-core" ),
				'file_caption_placeholder'  => _x( "Caption...", "Attachments Dialog", "bbp-core" ),
			)
		) );

		return $values;
	}

	public function embed_form_topic() {
		$forum_id = BB::i()->get_forum_id();

		if ( $forum_id == 0 ) {
			if ( bbpc_attachments()->get( 'forum_not_defined' ) == 'show' ) {
				$this->embed_form( true );
			}
		} else {
			if ( bbpc_attachments()->in_topic_form( $forum_id ) ) {
				$this->embed_form();
			}
		}
	}

	public function embed_form_reply() {
		$forum_id = BB::i()->get_forum_id();

		if ( bbpc_attachments()->in_reply_form( $forum_id ) ) {
			$this->embed_form();
		}
	}

	public function embed_form( $forced = false ) {
		$forum_id     = BB::i()->get_forum_id();
		$is_this_edit = bbp_is_topic_edit() || bbp_is_reply_edit();

		$can_upload = apply_filters( 'bbpc_attachments_allow_upload', bbpc_attachments()->is_user_allowed(), $forum_id );

		if ( $can_upload ) {
			if ( $forced || bbpc_attachments()->is_active( $forum_id ) ) {
				$this->file_size = apply_filters( 'bbpc_attachments_max_file_size', bbpc_attachments()->get_file_size( $forum_id ), $forum_id );

				if ( $is_this_edit ) {
					$this->id          = bbp_is_topic_edit() ? bbp_get_topic_id() : bbp_get_reply_id();
					$this->attachments = bbpc_get_post_attachments( $this->id );

					if ( ! empty( $this->attachments ) ) {
						include( bbpc_get_template_part( 'bbpc-form-attachment-edit.php' ) );
					}
				}

				include( bbpc_get_template_part( 'bbpc-form-attachment.php' ) );

				Enqueue::instance()->attachments();
			}
		}
	}

	public function form_notices() {
		if ( bbpc_attachments()->is_no_limit() ) {
			$message = __( "Your account has the ability to upload any attachment regardless of size and type.", "bbp-core" );

			echo apply_filters( 'bbpc_notice_attachments_no_limit', '<div class="bbp-template-notice info"><p>' . $message . '</p></div>', $message );
		} else {
			$file_size = d4p_filesize_format( $this->file_size * 1024, 2 );

			$message = sprintf( __( "Maximum file size allowed is %s.", "bbp-core" ), '<strong>' . $file_size . '</strong>' );

			echo apply_filters( 'bbpc_notice_attachments_limit_file_size', '<div class="bbp-template-notice"><p>' . $message . '</p></div>', $message, $file_size );

			if ( bbpc_attachments()->get( 'mime_types_limit_active' ) && bbpc_attachments()->get( 'mime_types_limit_display' ) ) {
				$show = bbpc_attachments()->get_file_extensions();

				$message = sprintf( __( "File types allowed for upload: %s.", "bbp-core" ), '<strong>.' . join( '</strong>, <strong>.', $show ) . '</strong>' );

				echo apply_filters( 'bbpc_notice_attachments_limit_file_types', '<div class="bbp-template-notice"><p>' . $message . '</p></div>', $message, $show );
			}
		}
	}

	public function embed_edit_form() {
		$this->skip_missing = bbpc_attachments()->get( 'file_skip_missing' );

		d4p_include( 'functions', 'admin', BBPC_D4PLIB );

		$_icons = bbpc_attachments()->get( 'attachment_icons' );
		$_type  = Icons::instance()->mode();

		$_deletion = bbpc_attachments()->get( 'delete_method' ) == 'edit';

		$actions = array();

		if ( $_deletion ) {
			$post      = get_post( $this->id );
			$author_id = $post->post_author;

			$allow = bbpc_attachments()->get_deletion_status( $author_id );

			if ( $allow != 'no' ) {
				$actions[''] = __( "Do Nothing", "bbp-core" );

				if ( $allow == 'delete' || $allow == 'both' ) {
					$actions['delete'] = __( "Delete", "bbp-core" );
				}

				if ( $allow == 'detach' || $allow == 'both' ) {
					$actions['detach'] = __( "Detach", "bbp-core" );
				}
			}
		}

		$content = '<div class="bbpc-attachments bbpc-attachments-edit">';
		$content .= '<input type="hidden" />';
		$content .= '<ol';

		if ( $_icons ) {
			switch ( $_type ) {
				case 'images':
					$content .= ' class="with-icons"';
					break;
				case 'font':
					$content .= ' class="with-font-icons"';
					break;
			}
		}

		$content .= '>';

		foreach ( $this->attachments as $attachment ) {
			$insert = array( '<a role="button" class="bbpc-attachment-insert" href="#' . $attachment->ID . '">' . __( "insert into content", "bbp-core" ) . '</a>' );

			$file = get_attached_file( $attachment->ID );

			if ( ( $file === false || empty( $file ) ) && $this->skip_missing ) {
				continue;
			}

			$ext      = pathinfo( $file, PATHINFO_EXTENSION );
			$filename = pathinfo( $file, PATHINFO_BASENAME );
			$url      = wp_get_attachment_url( $attachment->ID );

			$a_title  = $filename;
			$html     = $filename;
			$class_li = '';

			if ( $_icons && $_type == 'images' ) {
				$class_li = "bbpc-image bbpc-image-" . Attachments::instance()->icon( $ext );
				$html     = '<i></i> ' . $html;
			}

			if ( $_icons && $_type == 'font' ) {
				$html = Attachments::instance()->render_attachment_icon( $ext ) . $html;
			}

			$item = '<li id="bbpc-attachment-id-' . $attachment->ID . '" class="bbpc-attachment bbpc-attachment-' . $ext . ' ' . $class_li . '">';
			$item .= '<a href="' . $url . '" title="' . $a_title . '" download>' . $html . '</a>';
			$item .= ' [' . join( ' | ', $insert ) . ']';

			if ( ! empty( $actions ) ) {
				$item .= d4p_render_select( $actions, array(
					'name' => 'bbpc[remove-attachment][' . $attachment->ID . ']',
					'echo' => false
				), array( 'title' => __( "Attachment Actions", "bbp-core" ) ) );
			}

			$item .= '</li>';

			$content .= $item;
		}

		$content .= '</ol>';
		$content .= '</div>';

		return $content;
	}
}