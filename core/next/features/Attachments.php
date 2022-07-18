<?php

namespace Dev4Press\Plugin\GDBBX\Features;

use Dev4Press\Plugin\GDBBX\Attachments\Bulk;
use Dev4Press\Plugin\GDBBX\Attachments\Display;
use Dev4Press\Plugin\GDBBX\Attachments\Form;
use Dev4Press\Plugin\GDBBX\Attachments\Handlers;
use Dev4Press\Plugin\GDBBX\Attachments\Topic;
use Dev4Press\Plugin\GDBBX\Attachments\Upload;
use Dev4Press\Plugin\GDBBX\Base\Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Attachments extends Feature {
	public $feature_name = 'attachments';
	public $settings = array(
		'method'                              => 'enhanced', // classic, enhanced
		'topics'                              => true,
		'replies'                             => true,
		'roles_to_upload'                     => null,
		'max_file_size'                       => 512,
		'max_to_upload'                       => 8,
		'max_to_upload_per_post'              => 8,
		'roles_no_limit'                      => array( 'bbp_keymaster' ),
		'forum_not_defined'                   => 'hide',
		'enhanced_auto_new'                   => true,
		'enhanced_set_caption'                => true,
		'insert_into_content'                 => true,
		'insert_into_content_roles'           => null,
		'hide_attachments_when_in_content'    => false,
		'hide_attachments_from_media_library' => false,
		'form_position_topic'                 => 'bbp_theme_before_topic_form_submit_wrapper',
		'form_position_reply'                 => 'bbp_theme_before_reply_form_submit_wrapper',
		'files_list_position'                 => 'content',
		'files_list_roles'                    => null,
		'files_list_mode'                     => 'list', // list, thumbs, mixed
		'topic_thread_list'                   => false,
		'topic_thread_list_action'            => 'bbp_template_before_single_topic',
		'topic_thread_list_format'            => 'list',
		'topic_thread_list_items'             => 8,
		'topic_thread_list_columns'           => 4,
		'topic_thread_list_roles'             => array( 'bbp_keymaster', 'bbp_moderator' ),
		'show_form_notices'                   => true,
		'mime_types_limit_active'             => false,
		'mime_types_limit_display'            => false,
		'mime_types_list'                     => array(),
		'upload_dir_override'                 => false,
		'upload_dir_structure'                => '/forums/forum-name',
		'upload_dir_forums_base'              => 'forums',
		'topic_featured_image'                => false,
		'reply_featured_image'                => false,
		'grid_topic_counter'                  => true,
		'grid_reply_counter'                  => true,
		'delete_method'                       => 'default',
		'delete_attachments'                  => 'detach',
		'hide_from_visitors'                  => true,
		'preview_for_visitors'                => false,
		'file_skip_missing'                   => true,
		'file_target_blank'                   => false,
		'bulk_download'                       => false,
		'bulk_download_listed'                => true,
		'bulk_download_roles'                 => null,
		'bulk_download_visitor'               => false,
		'attachment_icons'                    => true,
		'download_link_attribute'             => true,
		'image_thumbnail_columns'             => 3,
		'image_thumbnail_inline'              => true,
		'image_thumbnail_caption'             => true,
		'image_thumbnail_rel'                 => 'lightbox',
		'image_thumbnail_css'                 => '',
		'image_thumbnail_size'                => '128x72',
		'log_upload_errors'                   => true,
		'errors_visible_to_admins'            => true,
		'errors_visible_to_moderators'        => true,
		'errors_visible_to_author'            => true,
		'delete_visible_to_admins'            => 'both',
		'delete_visible_to_moderators'        => 'no',
		'delete_visible_to_author'            => 'no'
	);

	private $icons = array(
		'code'       => 'c|cc|h|js|class|json',
		'xml'        => 'xml',
		'excel'      => 'xla|xls|xlsx|xlt|xlw|xlam|xlsb|xlsm|xltm',
		'word'       => 'docx|dotx|docm|dotm',
		'image'      => 'png|gif|jpg|jpeg|jpe|jp|bmp|tif|tiff',
		'psd'        => 'psd',
		'ai'         => 'ai',
		'archive'    => 'zip|rar|gz|gzip|tar',
		'text'       => 'txt|asc|nfo',
		'powerpoint' => 'pot|pps|ppt|pptx|ppam|pptm|sldm|ppsm|potm',
		'pdf'        => 'pdf',
		'html'       => 'htm|html|css',
		'video'      => 'avi|asf|asx|wax|wmv|wmx|divx|flv|mov|qt|mpeg|mpg|mpe|mp4|m4v|ogv|mkv',
		'documents'  => 'odt|odp|ods|odg|odc|odb|odf|wp|wpd|rtf',
		'audio'      => 'mp3|m4a|m4b|mp4|m4v|wav|ra|ram|ogg|oga|mid|midi|wma|mka',
		'icon'       => 'ico'
	);

	private $font_icons = array(
		'file-code'       => 'code|xml|html',
		'file-image'      => 'image|psd|ai|icon',
		'file-pdf'        => 'pdf',
		'file-excel'      => 'excel',
		'file-word'       => 'word',
		'file-powerpoint' => 'powerpoint',
		'file-lines'      => 'text|documents',
		'file-video'      => 'video',
		'file-archive'    => 'archive',
		'file-audio'      => 'audio',
		'file'            => 'generic'
	);

	private $thumb_types = array(
		'pdf',
		'svg',
		'png',
		'gif',
		'jpg',
		'jpeg',
		'jpe',
		'bmp'
	);

	public $inserted = array();

	public static function instance() : Attachments {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Attachments();
			$instance->run();
		}

		return $instance;
	}

	private function run() {
		Handlers::instance();

		add_action( 'gdbbx_init', array( $this, 'thumbnail' ) );

		if ( ! is_admin() ) {
			add_action( 'gdbbx_core', array( $this, 'frontend' ) );
		}
	}

	public function h() : Handlers {
		return Handlers::instance();
	}

	public function thumbnail() {
		$size = $this->get( 'image_thumbnail_size' );
		$size = explode( 'x', $size );

		$args = apply_filters( 'gdbbx_attachments_thumb_image_size_args', array(
			'width'  => $size[0],
			'height' => $size[1],
			'crop'   => true
		) );

		add_image_size( 'gdbbx-thumb', $args['width'], $args['height'], $args['crop'] );
	}

	public function frontend() {
		Bulk::instance()->run();
		Form::instance()->run();
		Display::instance()->run();
		Upload::instance()->run();

		if ( $this->get( 'topic_thread_list' ) && $this->allowed( 'topic_thread_list', 'attachments_topic_thread_list' ) ) {
			Topic::instance()->run();
		}

		add_action( 'gdbbx_template_before_replies_loop', array( $this, 'before_replies_loop' ) );
		add_action( 'gdbbx_template_before_topics_loop', array( $this, 'before_topics_loop' ) );

		$this->icons       = apply_filters( 'gdbbx_attachments_icons_sets', $this->icons );
		$this->font_icons  = apply_filters( 'gdbbx_attachments_font_icons_sets', $this->font_icons );
		$this->thumb_types = apply_filters( 'gdbbx_attachments_thumb_extensions', $this->thumb_types );
	}

	public function is_thumbnail_type( $extension ) : bool {
		return in_array( $extension, $this->thumb_types );
	}

	public function render_attachment_icon( $ext ) : string {
		$icon = $this->icon( $ext );

		$cls = 'gdbbx-icon gdbbx-icon-';
		foreach ( $this->font_icons as $fa => $list ) {
			$list = explode( '|', $list );

			if ( in_array( $icon, $list ) ) {
				$cls .= $fa;
			}
		}

		return '<i class="' . $cls . ' gdbbx-fw"></i> ';
	}

	public function icon( $ext ) : string {
		foreach ( $this->icons as $icon => $list ) {
			$list = explode( '|', $list );

			if ( in_array( $ext, $list ) ) {
				return $icon;
			}
		}

		return 'generic';
	}

	public function attachment_inserted( $id, $attachment_id ) {
		if ( ! isset( $this->inserted[ $id ] ) ) {
			$this->inserted[ $id ] = array();
		}

		$this->inserted[ $id ][] = absint( $attachment_id );
	}

	public function get_inserted_attachments( $id ) {
		if ( isset( $this->inserted[ $id ] ) && ! empty( $this->inserted[ $id ] ) ) {
			return $this->inserted[ $id ];
		}

		return array();
	}

	public function get_file_size( $forum_id = 0 ) {
		$size = $this->get( 'max_file_size' );

		$forum    = gdbbx_forum( $forum_id )->attachments()->get( 'max_file_size_override' );
		$override = gdbbx_forum( $forum_id )->attachments()->get( 'max_file_size' );

		if ( $override > 0 && $forum == 'yes' ) {
			$size = $override;
		}

		return apply_filters( 'gdbbx_attachments_max_file_size', $size, gdbbx_forum()->forum() );
	}

	public function get_max_files( $forum_id = 0 ) {
		$files = $this->get( 'max_to_upload' );

		$forum    = gdbbx_forum( $forum_id )->attachments()->get( 'max_to_upload_override' );
		$override = gdbbx_forum( $forum_id )->attachments()->get( 'max_to_upload' );

		if ( $override > 0 && $forum == 'yes' ) {
			$files = $override;
		}

		return apply_filters( 'gdbbx_attachments_max_to_upload', $files, gdbbx_forum()->forum() );
	}

	public function get_file_extensions( $forum_id = 0 ) : array {
		$list = $this->get( 'mime_types_list' );

		$forum    = gdbbx_forum( $forum_id )->attachments()->get( 'mime_types_list_override' );
		$override = gdbbx_forum( $forum_id )->attachments()->get( 'mime_types_list' );

		if ( ! empty( $override ) && $forum == 'yes' ) {
			$list = $override;
		}

		$show = array();
		foreach ( $list as $i ) {
			$show = array_merge( $show, explode( '|', $i ) );
		}

		return (array) apply_filters( 'gdbbx_attachments_extensions_list', $show, gdbbx_forum()->forum() );
	}

	public function filter_mime_types( $forum_id = 0 ) {
		if ( $this->is_no_limit() ) {
			return null;
		}

		if ( $this->get( 'mime_types_limit_active' ) ) {
			$full = get_allowed_mime_types();
			$list = $this->get( 'mime_types_list' );

			$forum    = gdbbx_forum( $forum_id )->attachments()->get( 'mime_types_list_override' );
			$override = gdbbx_forum( $forum_id )->attachments()->get( 'mime_types_list' );

			if ( ! empty( $override ) && $forum == 'yes' ) {
				$list = $override;
			}

			$filtered = array();
			foreach ( $full as $key => $mime ) {
				if ( in_array( $key, $list ) ) {
					$filtered[ $key ] = $mime;
				}
			}

			return $filtered;
		} else {
			return null;
		}
	}

	public function is_hidden_from_visitors( $forum_id = 0 ) : bool {
		$forum = gdbbx_forum( $forum_id )->attachments()->get( 'hide_from_visitors' );

		$hide = false;
		if ( $forum == 'default' ) {
			$hide = $this->get( 'hide_from_visitors' );
		} else if ( $forum == 'yes' ) {
			$hide = true;
		} else if ( $forum == 'no' ) {
			$hide = false;
		}

		return (bool) apply_filters( 'gdbbx_attachments_is_hidden_from_visitors', $hide, gdbbx_forum()->forum() );
	}

	public function is_preview_for_visitors( $forum_id = 0 ) : bool {
		$forum = gdbbx_forum( $forum_id )->attachments()->get( 'preview_for_visitors' );

		$hide = false;
		if ( $forum == 'default' ) {
			$hide = $this->get( 'preview_for_visitors' );
		} else if ( $forum == 'yes' ) {
			$hide = true;
		} else if ( $forum == 'no' ) {
			$hide = false;
		}

		return (bool) apply_filters( 'gdbbx_attachments_is_preview_for_visitors', $hide, gdbbx_forum()->forum() );
	}

	public function is_active( $forum_id = 0 ) : bool {
		$forum = gdbbx_forum( $forum_id )->attachments()->get( 'status' );

		$active = $forum == 'default' || $forum == 'yes';

		return (bool) apply_filters( 'gdbbx_attachments_forum_enabled', $active, gdbbx_forum()->forum() );
	}

	public function in_topic_form( $forum_id = 0 ) : bool {
		$forum = gdbbx_forum( $forum_id )->attachments()->get( 'topic_form' );

		$active = false;
		if ( $forum == 'default' ) {
			$active = $this->get( 'topics' );
		} else if ( $forum == 'yes' ) {
			$active = true;
		} else if ( $forum == 'no' ) {
			$active = false;
		}

		return (bool) apply_filters( 'gdbbx_attachments_forum_topic_form', $active, gdbbx_forum()->forum() );
	}

	public function in_reply_form( $forum_id = 0 ) : bool {
		$forum = gdbbx_forum( $forum_id )->attachments()->get( 'reply_form' );

		$active = false;
		if ( $forum == 'default' ) {
			$active = $this->get( 'replies' );
		} else if ( $forum == 'yes' ) {
			$active = true;
		} else if ( $forum == 'no' ) {
			$active = false;
		}

		return (bool) apply_filters( 'gdbbx_attachments_forum_reply_form', $active, gdbbx_forum()->forum() );
	}

	public function is_right_size( $file, $forum_id = 0 ) : bool {
		if ( $this->is_no_limit() ) {
			return true;
		}

		$file_size = $this->get_file_size( $forum_id );

		return $file['size'] <= $file_size * KB_IN_BYTES;
	}

	public function is_bulk_download_allowed() : bool {
		return $this->get( 'bulk_download' ) && $this->allowed( 'bulk_download', 'attachments_bulk_download' );
	}

	public function is_user_allowed() : bool {
		$allowed = false;

		if ( is_user_logged_in() ) {
			if ( is_null( $this->get( 'roles_to_upload' ) ) ) {
				$allowed = true;
			} else {
				global $current_user;

				$value = $this->get( 'roles_to_upload' );

				if ( ! is_array( $value ) ) {
					$allowed = true;
				}

				if ( is_array( $current_user->roles ) ) {
					$matched = array_intersect( $current_user->roles, $value );
					$allowed = ! empty( $matched );
				}
			}
		}

		return (bool) apply_filters( 'gdbbx_attachments_is_user_allowed', $allowed );
	}

	public function is_no_limit() : bool {
		$allowed = false;

		if ( is_user_logged_in() ) {
			$value = $this->get( 'roles_no_limit' );

			if ( is_array( $value ) ) {
				global $current_user;

				if ( is_array( $current_user->roles ) ) {
					$matched = array_intersect( $current_user->roles, $value );
					$allowed = ! empty( $matched );
				}
			}
		}

		return (bool) apply_filters( 'gdbbx_attachments_is_user_with_no_limit', $allowed );
	}

	public function get_deletion_status( $author_id ) {
		$allow = 'no';

		if ( gdbbx_is_current_user_bbp_keymaster() ) {
			$allow = $this->get( 'delete_visible_to_admins' );
		} else if ( gdbbx_is_current_user_bbp_moderator() ) {
			$allow = $this->get( 'delete_visible_to_moderators' );
		} else if ( $author_id == bbp_get_current_user_id() ) {
			$allow = $this->get( 'delete_visible_to_author' );
		}

		return $allow;
	}

	public function before_replies_loop( $posts ) {
		gdbbx_cache()->attachments_run_bulk_counts( $posts );
		gdbbx_cache()->attachments_errors_run_bulk_counts( $posts );
	}

	public function before_topics_loop( $posts ) {
		gdbbx_cache()->attachments_run_bulk_topics_counts( $posts );
	}
}
