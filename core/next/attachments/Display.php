<?php

namespace Dev4Press\Plugin\GDBBX\Attachments;

use Dev4Press\Plugin\GDBBX\Basic\Enqueue;
use Dev4Press\Plugin\GDBBX\Features\Attachments;
use Dev4Press\Plugin\GDBBX\Features\Icons;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Display {
	private $enabled = false;

	public $id = 0;
	public $post;
	public $attachments;
	public $attachments_all;
	public $author_id;

	public $icons;
	public $type;
	public $download;
	public $deletion;
	public $skip_missing;
	public $mode;
	public $columns = 3;

	public function __construct() {
		$this->type         = Icons::instance()->mode();
		$this->icons        = gdbbx_attachments()->get( 'attachment_icons' );
		$this->mode         = gdbbx_attachments()->get( 'files_list_mode' );
		$this->skip_missing = gdbbx_attachments()->get( 'file_skip_missing' );
		$this->download     = gdbbx_attachments()->get( 'download_link_attribute' ) ? ' download' : '';
		$this->deletion     = gdbbx_attachments()->get( 'delete_method' ) == 'default';
		$this->columns      = gdbbx_attachments()->get( 'image_thumbnail_columns' );
	}

	public static function instance() : Display {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Display();
		}

		return $instance;
	}

	public function run() {
		$visible = gdbbx_attachments()->allowed( 'files_list', 'attachments_files_list' );
		$visible = apply_filters( 'gdbbx_attachment_show_files_list', $visible );

		if ( $visible && ! $this->enabled ) {
			$this->enabled = true;

			if ( gdbbx_attachments()->get( 'files_list_position' ) == 'content' ) {
				add_filter( 'bbp_get_reply_content', array( $this, 'embed_attachments' ), 100, 2 );
				add_filter( 'bbp_get_topic_content', array( $this, 'embed_attachments' ), 100, 2 );
			} else if ( gdbbx_attachments()->get( 'files_list_position' ) == 'after' ) {
				add_action( 'bbp_theme_after_topic_content', array( $this, 'after_attachments' ), 20 );
				add_action( 'bbp_theme_after_reply_content', array( $this, 'after_attachments' ), 20 );
			}
		}
	}

	public function remove_content_filters() {
		$this->enabled = false;

		remove_filter( 'bbp_get_topic_content', array( $this, 'embed_attachments' ), 100 );
		remove_filter( 'bbp_get_reply_content', array( $this, 'embed_attachments' ), 100 );
	}

	public function after_attachments() {
		$id = bbp_get_reply_id();

		if ( $id == 0 ) {
			$id = bbp_get_topic_id();
		}

		echo $this->embed_attachments( '', $id );
	}

	public function embed_attachments( $content, $id ) {
		if ( gdbbx()->is_inside_content_shortcode( $id ) ) {
			return $content;
		}

		if ( gdbbx_is_feed() ) {
			return $content;
		}

		if ( gdbbx_cache()->attachments_has_attachments( $id ) || gdbbx_cache()->attachments_has_attachments_errors( $id ) ) {
			if ( gdbbx_cache()->attachments_has_attachments( $id ) ) {
				$content .= $this->attachments( $id );
			}

			if ( gdbbx_cache()->attachments_has_attachments_errors( $id ) ) {
				$content .= $this->errors( $id );
			}
		}

		return $content;
	}

	private function attachments( $id ) : string {
		$this->id        = absint( $id );
		$this->post      = get_post( $this->id );
		$this->author_id = $this->post->post_author;

		$this->attachments_all = gdbbx_get_post_attachments( $id );

		if ( gdbbx_attachments()->get( 'hide_attachments_when_in_content', false ) ) {
			$inserted = gdbbx_attachments()->get_inserted_attachments( $id );

			$this->attachments = array();

			foreach ( $this->attachments_all as $file ) {
				if ( ! in_array( absint( $file->ID ), $inserted ) ) {
					$this->attachments[] = $file;
				}
			}
		} else {
			$this->attachments = $this->attachments_all;
		}

		$content = '';
		$bulk    = $this->_bulk();

		if ( ! empty( $this->attachments ) || ! empty( $bulk ) ) {
			$content .= '<div class="gdbbx-attachments">';
			$content .= '<h6 class="__title">' . __( "Attachments", "bbp-core" ) . ':</h6>';

			if ( $this->_hidden() ) {
				$content .= $this->_visitor();
			} else {
				$content .= $this->_user();
				$content .= $bulk;
			}

			$content .= '</div>';
		}

		Enqueue::instance()->attachments();

		return $content;
	}

	private function errors( $id ) : string {
		global $user_ID;

		if ( $this->id == 0 || $this->id != $id ) {
			$this->id        = absint( $id );
			$this->post      = get_post( $this->id );
			$this->author_id = $this->post->post_author;
		}

		$content = '';

		if ( ( gdbbx_attachments()->get( 'errors_visible_to_author' ) == 1 && $this->author_id == $user_ID ) || ( gdbbx_attachments()->get( 'errors_visible_to_admins' ) == 1 && d4p_is_current_user_admin() ) || ( gdbbx_attachments()->get( 'errors_visible_to_moderators' ) == 1 && gdbbx_is_current_user_bbp_moderator() ) ) {
			$content .= $this->_errors();
		}

		Enqueue::instance()->attachments();

		return $content;
	}

	private function _bulk() : string {
		$bulk = '';

		if ( count( $this->attachments_all ) > 1 ) {
			if ( gdbbx_attachments()->get( 'bulk_download' ) && gdbbx_attachments()->allowed( 'bulk_download', 'attachments_bulk_download' ) ) {
				$do = ! gdbbx_attachments()->get( 'bulk_download_listed' ) || ! empty( $this->attachments );

				if ( $do ) {
					$topic_id = bbp_get_topic_id();

					$url = get_permalink( $topic_id );
					$url = add_query_arg( 'gdbbx-bulk-download', $this->id, $url );

					$bulk .= '<div class="__bulk">';
					$bulk .= '<a href="' . $url . '">' . __( "Download All Files", "bbp-core" ) . '</a>';
					$bulk .= '</div>';
				}
			}
		}

		return $bulk;
	}

	private function _errors() : string {
		$content = '';

		$errors = get_post_meta( $this->id, '_bbp_attachment_upload_error' );

		if ( ! empty( $errors ) ) {
			$content .= '<div class="gdbbx-attachments-errors">';
			$content .= '<h6 class="__title">' . __( "Upload Errors", "bbp-core" ) . ':</h6>';
			$content .= '<ol class="__errors-list">';

			foreach ( $errors as $error ) {
				$content .= '<li><strong>' . esc_html( $error['file'] ) . '</strong>: ' . __( $error['message'], "bbp-core" ) . '</li>';
			}

			$content .= '</ol></div>';
		}

		return $content;
	}

	private function _hidden() : bool {
		if ( ! is_user_logged_in() ) {
			return gdbbx_attachments()->is_hidden_from_visitors() || gdbbx_attachments()->is_preview_for_visitors();
		}

		return false;
	}

	private function _visitor() : string {
		$content = '';

		if ( ! gdbbx_attachments()->is_hidden_from_visitors() && gdbbx_attachments()->is_preview_for_visitors() ) {
			$content .= $this->_user( true );
		}

		$message = sprintf( __( "You must be <a href='%s'>logged in</a> to access attached files.", "bbp-core" ), wp_login_url( get_permalink() ) );

		$content .= apply_filters( 'gdbbx_notice_attachments_visitor', '<div class="gdbbx-attachments-login-message bbp-template-notice"><p>' . $message . '</p></div>', $message );

		return $content;
	}

	private function _user( bool $preview = false ) : string {
		$actions = $preview ? false : $this->deletion;

		$files = array(
			'img' => array(),
			'lst' => array()
		);

		foreach ( $this->attachments as $attachment ) {
			$file = get_attached_file( $attachment->ID );

			if ( ( $file === false || empty( $file ) ) && $this->skip_missing ) {
				continue;
			}

			$ext = pathinfo( $file, PATHINFO_EXTENSION );
			$img = Attachments::instance()->is_thumbnail_type( $ext );

			if ( $this->mode == 'list' || ( $this->mode == 'mixed' && ! $img ) ) {
				$files['lst'][] = $this->render_file_as_link( $attachment, $file, $this->id, $actions, $preview );
			} else {
				$files['img'][] = $this->render_file_as_thumbnail( $attachment, $file, $this->id, $actions, $preview );
			}
		}

		$content = '';

		if ( ! empty( $files['img'] ) ) {
			$files['img'] = apply_filters( 'gdbbx_attachments_display_user_list_images', $files['img'] );
			$content      .= '<div class="gdbbx-attachments-files-container">';
			$content      .= '<ol class="__files-list __with-thumbnails __columns-' . $this->columns . '">' . join( '', $files['img'] ) . '</ol>';
			$content      .= '</div>';
		}

		if ( ! empty( $files['lst'] ) ) {
			$list_class   = $this->type == 'images' ? '__with-icons' : '__with-font-icons';
			$files['lst'] = apply_filters( 'gdbbx_attachments_display_user_list_files', $files['lst'] );
			$content      .= '<div class="gdbbx-attachments-files-container">';
			$content      .= '<ol class="__files-list __without-thumbnails ' . $list_class . '">' . join( '', $files['lst'] ) . '</ol>';
			$content      .= '</div>';
		}

		return $content;
	}

	private function _file_actions( int $file, int $post, int $author ) : array {
		$actions = array();

		$action_url = add_query_arg( 'att_id', $file );
		$action_url = add_query_arg( 'bbp_id', $post, $action_url );

		$allow = Attachments::instance()->get_deletion_status( $author );

		if ( $allow == 'delete' || $allow == 'both' ) {
			$_url      = add_query_arg( '_wpnonce', wp_create_nonce( 'gdbbx-attachment-delete-' . $post . '-' . $file ), $action_url );
			$actions[] = '<a class="gdbbx-attachment-confirm" href="' . esc_url( add_query_arg( 'gdbbx-action', 'delete', $_url ) ) . '">' . __( "delete", "bbp-core" ) . '</a>';
		}

		if ( $allow == 'detach' || $allow == 'both' ) {
			$_url      = add_query_arg( '_wpnonce', wp_create_nonce( 'gdbbx-attachment-detach-' . $post . '-' . $file ), $action_url );
			$actions[] = '<a class="gdbbx-attachment-confirm" href="' . esc_url( add_query_arg( 'gdbbx-action', 'detach', $_url ) ) . '">' . __( "detach", "bbp-core" ) . '</a>';
		}

		return $actions;
	}

	private function _render_actions( array $actions ) : string {
		if ( ! empty( $actions ) ) {
			return '<span class="__actions">[' . join( ' | ', $actions ) . ']</span>';
		}

		return '';
	}

	public function render_file_as_thumbnail( WP_Post $attachment, string $file, int $post_id, bool $actions = true, bool $preview = false ) : string {
		$actions = $actions && ! $preview ? $this->_file_actions( $attachment->ID, $post_id, $attachment->post_author ) : array();

		$ext      = pathinfo( $file, PATHINFO_EXTENSION );
		$filename = pathinfo( $file, PATHINFO_BASENAME );
		$url      = wp_get_attachment_url( $attachment->ID );
		$caption  = gdbbx_attachments()->get( 'image_thumbnail_caption' );
		$title    = $attachment->post_excerpt != '' ? $attachment->post_excerpt : $filename;
		$target   = gdbbx_attachments()->get( 'file_target_blank' ) ? '_blank' : '_self';

		$link_rel   = '';
		$link_class = array(
			'ext-' . $ext
		);

		$item_classes = array(
			'gdbbx-attachment',
			'gdbbx-attachment-' . $ext,
			'__thumb'
		);

		if ( Attachments::instance()->is_thumbnail_type( $ext ) ) {
			$html = wp_get_attachment_image( $attachment->ID, 'gdbbx-thumb' );
		}

		if ( empty( $html ) ) {
			$size = gdbbx_attachments()->get( 'image_thumbnail_size' );
			$size = explode( 'x', $size );

			$html = '<span style="width: ' . $size[0] . 'px; height: ' . $size[1] . 'px; line-height: ' . $size[1] . 'px;" class="__thumb-holder">' . $ext . '</span>';
		} else {
			$_class = gdbbx_attachments()->get( 'image_thumbnail_css' );

			if ( ! empty( $_class ) ) {
				$link_class[] = $_class;
			}

			$link_rel = apply_filters( 'gdbbx_image_thumbnail_rel', gdbbx_attachments()->get( 'image_thumbnail_rel' ), $attachment, $ext );

			if ( ! empty( $link_rel ) ) {
				$link_rel = ' rel="' . $link_rel . '"';
				$link_rel = str_replace( '%ID%', $post_id, $link_rel );
				$link_rel = str_replace( '%TOPIC%', bbp_get_topic_id(), $link_rel );
				$link_rel = str_replace( '%EXT%', $ext, $link_rel );
			}
		}

		$item_classes = apply_filters( 'gdbbx_attachments_display_user_file_classes', $item_classes, $attachment );

		$item = '<li id="gdbbx-attachment-id-' . $attachment->ID . '" class="' . join( ' ', $item_classes ) . '">';

		if ( $caption ) {
			$item .= '<div class="__caption">';
		}

		if ( $preview ) {
			$item .= $html;
			$link = $title;
		} else {
			$item .= '<a class="' . join( ' ', $link_class ) . '"' . $link_rel . ' href="' . $url . '" title="' . $title . '" target="' . $target . '">' . $html . '</a>';
			$link = '<a href="' . $url . '"' . $this->download . ' target="' . $target . '">' . $title . '</a>';
		}

		if ( $caption ) {
			$item .= '<p class="__text">' . $link;
			$item .= $this->_render_actions( $actions );
			$item .= '</p>';
			$item .= '</div>';
		} else {
			$item .= $this->_render_actions( $actions );
		}

		$item .= '</li>';

		return $item;
	}

	public function render_file_as_link( WP_Post $attachment, string $file, int $post_id, bool $actions = true, bool $preview = false ) : string {
		$actions = $actions && ! $preview ? $this->_file_actions( $attachment->ID, $post_id, $attachment->post_author ) : array();

		$ext      = pathinfo( $file, PATHINFO_EXTENSION );
		$filename = pathinfo( $file, PATHINFO_BASENAME );
		$url      = wp_get_attachment_url( $attachment->ID );
		$caption  = gdbbx_attachments()->get( 'image_thumbnail_caption' );
		$title    = $attachment->post_excerpt != '' ? $attachment->post_excerpt : ( empty( $caption ) ? $filename : $caption );
		$target   = gdbbx_attachments()->get( 'file_target_blank' ) ? '_blank' : '_self';

		$link_rel   = '';
		$link_class = array(
			'ext-' . $ext
		);

		$item_classes = array(
			'gdbbx-attachment',
			'gdbbx-attachment-' . $ext,
			'__link'
		);

		$html = $filename;

		if ( $this->icons && $this->type == 'images' ) {
			$item_classes[] = 'gdbbx-image';
			$item_classes[] = 'gdbbx-image-' . Attachments::instance()->icon( $ext );
		}

		if ( $this->icons && $this->type == 'font' ) {
			$html = Attachments::instance()->render_attachment_icon( $ext ) . $html;
		}

		$item_classes = apply_filters( 'gdbbx_attachments_display_user_file_classes', $item_classes, $attachment );

		$item = '<li id="gdbbx-attachment-id-' . $attachment->ID . '" class="' . join( ' ', $item_classes ) . '">';

		if ( $preview ) {
			$item .= $html;
		} else {
			$item .= '<a class="' . join( ' ', $link_class ) . '"' . $link_rel . $this->download . ' href="' . $url . '" title="' . $title . '" target="' . $target . '">' . $html . '</a>';
			$item .= $this->_render_actions( $actions );
		}

		$item .= '</li>';

		return $item;
	}
}
