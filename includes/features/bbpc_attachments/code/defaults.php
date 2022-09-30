<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: We need to get rid of these things
class GDATTDefaults {
	var $default_options = [
		'version'                      => '4.3.1',
		'date'                         => '2022.05.16.',
		'build'                        => 2435,
		'status'                       => 'Stable',
		'product_id'                   => 'bbp-core',
		'edition'                      => 'free',
		'revision'                     => 0,
		'grid_topic_counter'           => 1,
		'grid_reply_counter'           => 1,
		'delete_attachments'           => 'detach',
		'include_always'               => 1,
		'hide_from_visitors'           => 1,
		'max_file_size'                => 512,
		'max_to_upload'                => 4,
		'roles_to_upload'              => null,
		'is_attachment_icon'              => 1,
		'is_attachment_icons'             => 1,
		'image_thumbnail_active'       => 1,
		'image_thumbnail_inline'       => 0,
		'image_thumbnail_caption'      => 1,
		'image_thumbnail_rel'          => 'lightbox',
		'image_thumbnail_css'          => '',
		'image_thumbnail_size_x'       => 128,
		'image_thumbnail_size_y'       => 72,
		'log_upload_errors'            => 1,
		'errors_visible_to_admins'     => 1,
		'errors_visible_to_moderators' => 1,
		'errors_visible_to_author'     => 1,
		'delete_visible_to_admins'     => 'both',
		'delete_visible_to_moderators' => 'no',
		'delete_visible_to_author'     => 'no',
	];

	function __construct() {
	}
}

$bbpc_upload_error_messages = [
	__( 'File exceeds allowed file size.', 'bbp-core' ),
	__( 'File not uploaded.', 'bbp-core' ),
	__( 'Upload file size exceeds PHP maximum file size allowed.', 'bbp-core' ),
	__( 'Upload file size exceeds FORM specified file size.', 'bbp-core' ),
	__( 'Upload file only partially uploaded.', 'bbp-core' ),
	__( "Can't write file to the disk.", 'bbp-core' ),
	__( 'Temporary folder for upload is missing.', 'bbp-core' ),
	__( 'Server extension restriction stopped upload.', 'bbp-core' ),
];
