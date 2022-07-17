<?php

namespace SpiderDevs\Plugin\BBPC\Attachments;

use SpiderDevs\Plugin\BBPC\Basic\Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Upload {
	public $forum_id;
	public $user_id;

	private $_update_attachments;

	public function __construct() {
	}

	public static function instance() : Upload {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Upload();
		}

		return $instance;
	}

	public function run() {
		add_action( 'bbp_edit_reply', array( $this, 'save_reply' ), 10, 5 );
		add_action( 'bbp_edit_topic', array( $this, 'save_topic' ), 10, 4 );
		add_action( 'bbp_new_reply', array( $this, 'save_reply' ), 10, 5 );
		add_action( 'bbp_new_topic', array( $this, 'save_topic' ), 10, 4 );
	}

	public function save_topic( $topic_id, $forum_id, $anonymous_data, $topic_author ) {
		$this->save_reply( 0, $topic_id, $forum_id, $anonymous_data, $topic_author );
	}

	public function save_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author ) {
		$is_topic = $reply_id == 0;

		$post_id = $reply_id == 0 ? $topic_id : $reply_id;

		if ( isset( $_POST['bbpc']['remove-attachment'] ) ) {
			$attachments = (array) $_POST['bbpc']['remove-attachment'];

			foreach ( $attachments as $id => $action ) {
				$attachment_id = absint( $id );

				if ( $attachment_id > 0 && ( $action == 'delete' || $action == 'detach' ) ) {
					bbpc_attachments()->h()->delete_attachment( $attachment_id, $post_id, $action );
				}
			}
		}

		$uploads          = array();
		$original         = array();
		$uploads_captions = array();

		if ( $is_topic ) {
			$featured = bbpc_attachments()->get( 'topic_featured_image' );
		} else {
			$featured = bbpc_attachments()->get( 'reply_featured_image' );
		}

		$counter  = 0;
		$captions = isset( $_POST['bbpc-attachment_caption'] ) ? (array) $_POST['bbpc-attachment_caption'] : array();

		if ( ! empty( $_FILES ) && ! empty( $_FILES['bbpc-attachment'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			$errors    = new Error();
			$overrides = array( 'test_form' => false );

			foreach ( $_FILES['bbpc-attachment']['error'] as $key => $error ) {
				$file_name = $_FILES['bbpc-attachment']['name'][ $key ];

				if ( $error == UPLOAD_ERR_OK ) {
					$file = array(
						'name'     => $file_name,
						'type'     => $_FILES['bbpc-attachment']['type'][ $key ],
						'size'     => $_FILES['bbpc-attachment']['size'][ $key ],
						'tmp_name' => $_FILES['bbpc-attachment']['tmp_name'][ $key ],
						'error'    => $_FILES['bbpc-attachment']['error'][ $key ]
					);

					$file_name = sanitize_file_name( $file_name );

					if ( bbpc_attachments()->is_right_size( $file, $forum_id ) ) {
						$mimes = bbpc_attachments()->filter_mime_types( $forum_id );
						if ( ! is_null( $mimes ) && ! empty( $mimes ) ) {
							$overrides['mimes'] = $mimes;
						}

						$this->forum_id = $forum_id;
						$this->user_id  = $reply_author;

						if ( bbpc_attachments()->get( 'upload_dir_override' ) ) {
							add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
						}

						$upload = wp_handle_upload( $file, $overrides );

						if ( bbpc_attachments()->get( 'upload_dir_override' ) ) {
							remove_filter( 'upload_dir', array( $this, 'upload_dir' ) );
						}

						$caption = isset( $captions[ $counter ] ) ? sanitize_text_field( $captions[ $counter ] ) : '';

						if ( is_array( $upload ) && isset( $upload['error'] ) && ! empty( $upload['error'] ) ) {
							$errors->add( 'wp_upload', $upload['error'], $file_name );
						} else {
							$uploads[]          = $upload;
							$original[]         = $file_name;
							$uploads_captions[] = $caption;
						}
					} else {
						$errors->add( 'd4p_upload', 'File exceeds allowed file size.', $file_name );
					}
				} else {
					switch ( $error ) {
						default:
						case 'UPLOAD_ERR_NO_FILE':
							$errors->add( 'php_upload', 'File not uploaded.', $file_name );
							break;
						case 'UPLOAD_ERR_INI_SIZE':
							$errors->add( 'php_upload', 'Upload file size exceeds PHP maximum file size allowed.', $file_name );
							break;
						case 'UPLOAD_ERR_FORM_SIZE':
							$errors->add( 'php_upload', 'Upload file size exceeds FORM specified file size.', $file_name );
							break;
						case 'UPLOAD_ERR_PARTIAL':
							$errors->add( 'php_upload', 'Upload file only partially uploaded.', $file_name );
							break;
						case 'UPLOAD_ERR_CANT_WRITE':
							$errors->add( 'php_upload', 'Can\'t write file to the disk.', $file_name );
							break;
						case 'UPLOAD_ERR_NO_TMP_DIR':
							$errors->add( 'php_upload', 'Temporary folder for upload is missing.', $file_name );
							break;
						case 'UPLOAD_ERR_EXTENSION':
							$errors->add( 'php_upload', 'Server extension restriction stopped upload.', $file_name );
							break;
					}
				}

				$counter ++;
			}
		}

		if ( ! empty( $errors->errors ) && bbpc_attachments()->get( 'log_upload_errors' ) == 1 ) {
			foreach ( $errors->errors as $code => $errs ) {
				foreach ( $errs as $error ) {
					if ( $error[0] != '' && $error[1] != '' ) {
						add_post_meta( $post_id, '_bbp_attachment_upload_error', array(
								'code'    => $code,
								'file'    => $error[1],
								'message' => $error[0]
							)
						);
					}
				}
			}
		}

		if ( ! empty( $uploads ) ) {
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			$counter            = 0;
			$update_attachments = array();
			foreach ( $uploads as $_key => $upload ) {
				$wp_filetype = wp_check_filetype( basename( $upload['file'] ) );

				$att_name = basename( $upload['file'] );
				$org_name = $original[ $_key ];

				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', $att_name ),
					'post_excerpt'   => $uploads_captions[ $counter ],
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				$attach_id   = wp_insert_attachment( $attachment, $upload['file'], $post_id );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );

				bbpc_db()->assign_attachment( $post_id, $attach_id );

				wp_update_attachment_metadata( $attach_id, $attach_data );

				update_post_meta( $attach_id, '_bbp_attachment', '1' );
				update_post_meta( $attach_id, '_bbp_attachment_name', $att_name );
				update_post_meta( $attach_id, '_bbp_attachment_original_name', $org_name );

				$update_attachments[] = array( 'name' => $org_name, 'id' => $attach_id );

				$counter ++;
			}

			if ( ! empty( $update_attachments ) ) {
				$this->_update_attachments = $update_attachments;

				if ( $is_topic ) {
					add_action( 'bbp_edit_topic_post_extras', array( $this, 'update_post_content' ) );
					add_action( 'bbp_new_topic_post_extras', array( $this, 'update_post_content' ) );
				} else {
					add_action( 'bbp_edit_reply_post_extras', array( $this, 'update_post_content' ) );
					add_action( 'bbp_new_reply_post_extras', array( $this, 'update_post_content' ) );
				}
			}

			if ( current_theme_supports( 'post-thumbnails' ) ) {
				if ( $featured && ! has_post_thumbnail( $post_id ) ) {
					$ids = bbpc_cache()->attachments_get_attachments_ids( $post_id );

					$args = array(
						'post_type'           => 'attachment',
						'numberposts'         => 1,
						'post_status'         => null,
						'post_mime_type'      => 'image',
						'post__in'            => $ids,
						'orderby'             => 'ID',
						'order'               => 'ASC',
						'ignore_sticky_posts' => true
					);

					$images = get_posts( $args );

					if ( ! empty( $images ) ) {
						foreach ( $images as $image ) {
							set_post_thumbnail( $post_id, $image->ID );
						}
					}
				}
			}
		}

		bbpc_db()->update_topic_attachments_count( $topic_id );
	}

	public function update_post_content( $post_id ) {
		if ( empty( $this->_update_attachments ) ) {
			return;
		}

		$post    = get_post( $post_id );
		$content = $post->post_content;

		$matches  = array();
		$new_list = array();

		$preg = preg_match_all( '/\[attachment.+?file=["\'](?<attachment>.+?)["\']\]/i', $content, $matches );
		$list = $matches['attachment'] ?? array();
		$modd = array_map( 'sanitize_file_name', $list );

		if ( ! empty( $modd ) ) {
			foreach ( $this->_update_attachments as $att ) {
				$search  = $att['name'];
				$replace = $att['id'];

				foreach ( $modd as $_key => $file ) {
					if ( stripos( $file, $search ) !== false ) {
						$nfile             = str_replace( $search, $replace, $file );
						$new_list[ $_key ] = $nfile;
						break;
					}
				}
			}

			if ( ! empty( $new_list ) ) {
				foreach ( $list as $_key => $att ) {
					if ( isset( $new_list[ $_key ] ) ) {
						$content = str_replace( $att, $new_list[ $_key ], $content );
					}
				}

				wp_update_post( array(
					'ID'           => $post->ID,
					'post_content' => $content
				) );
			}

			$this->_update_attachments = array();
		}
	}

	public function upload_dir( $args ) {
		$new_dir = $this->_upload_dir_structure();

		$args['path']   = str_replace( $args['subdir'], '', $args['path'] ) . $new_dir;
		$args['url']    = str_replace( $args['subdir'], '', $args['url'] ) . $new_dir;
		$args['subdir'] = $new_dir;

		return $args;
	}

	private function _upload_dir_structure() : string {
		$base      = d4p_sanitize_file_path( bbpc_attachments()->get( 'upload_dir_forums_base' ) );
		$structure = bbpc_attachments()->get( 'upload_dir_structure' );

		$forum      = get_post( $this->forum_id );
		$forum_name = $forum->post_name;

		switch ( $structure ) {
			default:
			case '/forums':
				return '/' . $base;
			case '/forums/forum-id':
				return '/' . $base . '/' . $this->forum_id;
			case '/forums/forum-name':
				return '/' . $base . '/' . $forum_name;
			case '/forums/user-id':
				return '/' . $base . '/' . $this->user_id;
		}
	}
}