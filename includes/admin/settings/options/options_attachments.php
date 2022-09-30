<?php
CSF::createSection(
	$prefix,
	[
		'id'    => 'attachments',
		'title' => esc_html__( 'Attachments', 'bbp-core' ),
	]
);

CSF::createSection(
	$prefix,
	[
		'parent' => 'attachments',
		'title'  => __( 'Attachments', 'bbp-core' ),
		'fields' => [
			[
				'id'      => 'is_attachment',
				'type'    => 'switcher',
				'default' => true,
				'title'   => __( 'Show attachments on topic replies.', 'bbp-core' ),
			],

			[
				'id'       => 'max_file_size',
				'type'     => 'number',
				'title'    => _x( 'Maximum File Size', 'bbp core maximum file size upload', 'bbp-core' ),
				'subtitle' => __( 'Input the values in Kilo Bytes (KB)', 'bbp-core' ),
				'default'  => 512,
			],

			[
				'id'      => 'max_file_uploads',
				'type'    => 'number',
				'title'   => _x( 'Maximum number of file to upload at once', 'bbp core maximum file size upload', 'bbp-core' ),
				'default' => 4,
				'class'   => 'eazydocs-pro-notice',
			],

			[
				'id'       => 'is_hide_attachment',
				'type'     => 'switcher',
				'default'  => true,
				'title'    => __( 'Hide Attachment from visitors', 'bbp-core' ),
				'text_on'  => 'Hide',
				'text_off' => 'Show',
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Who can upload files ', 'bbp-core' ),
			],

			[
				'id'      => 'users_can_upload',
				'type'    => 'checkbox',
				'title'   => __( 'Users who can upload attachments.', 'bbp-core' ),
				'options' => [
					'keymaster'   => 'Keymaster',
					'moderator'   => 'Moderator',
					'participant' => 'Participant',
					'spectator'   => 'Spectator',
					'blocked'     => 'Blocked',
				],
				'default' => [ 'keymaster', 'moderator', 'participant', 'spectator', 'blocked' ],
			],

			[
				'type'    => 'subheading',
				'content' => __( 'When an associated Topic or Reply has been deleted', 'bbp-core' ),
			],

			[
				'id'      => 'is_attachment_deletion',
				'type'    => 'switcher',
				'default' => false,
				'title'   => __( 'Delete the attachment along with forum/topic.', 'bbp-core' ),
				'class'   => 'eazydocs-pro-notice',
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Forums Integration', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __(
					'With these options you can modify the forums to include attachment elements.',
					'bbp-core'
				),
			],

			[
				'id'      => 'is_attachment_icon',
				'type'    => 'checkbox',
				'title'   => __( 'Attachment Icon', 'bbp-core' ),
				'default' => true,
			],

			[
				'id'      => 'is_file_type_icon',
				'type'    => 'checkbox',
				'title'   => __( 'File type Icons', 'bbp-core' ),
				'default' => true,
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Display of image attachments', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'Attached images can be displayed as thumbnails, and from here you can control this.', 'bbp-core' ),
			],

			[
				'id'      => 'is_with_caption',
				'type'    => 'checkbox',
				'title'   => __( 'With Caption', 'bbp-core' ),
				'default' => true,
			],

			[
				'id'      => 'is_in_line',
				'type'    => 'checkbox',
				'title'   => __( 'Inline', 'bbp-core' ),
				'default' => false,
			],

			[
				'id'    => 'css_class',
				'type'  => 'text',
				'title' => __( 'CSS Class', 'bbp-core' ),
			],

			[
				'id'          => 'rel_attribute',
				'type'        => 'text',
				'title'       => __( 'REL Attribute', 'bbp-core' ),
				'placeholder' => __( 'lightbox', 'bbp-core' ),
				'desc'        => __( 'You can use these tags: %ID%, %TOPIC%', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Image thumbnails size.', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'Changing thumbnails size affects only new image attachments. To use new size for old attachments, resize them using Regenerate Thumbnails plugin.', 'bbp-core' ),
			],

			[
				'id'    => 'attachment_image_x',
				'type'  => 'text',
				'title' => __( 'Thumbnail width', 'bbp-core' ),
				'desc'  => _x( 'px', 'attachment image thumbnail size', 'bbp-core' ),
			],

			[
				'id'    => 'attachment_image_y',
				'type'  => 'text',
				'title' => __( 'Thumbnail height', 'bbp-core' ),
				'desc'  => _x( 'px', 'attachment image thumbnail size', 'bbp-core' ),
			],
		],
	]
);

CSF::createSection(
	$prefix,
	[
		'title'  => __( 'Images', 'bbp-core' ),
		'parent' => 'attachments',
		'fields' => [
			[
				'type'    => 'subheading',
				'content' => __( 'Display of image attachments', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'Attached images can be displayed as thumbnails, and from here you can control this.', 'bbp-core' ),
			],

			[
				'id'      => 'is_with_caption',
				'type'    => 'checkbox',
				'title'   => __( 'With Caption', 'bbp-core' ),
				'default' => true,
			],

			[
				'id'      => 'is_in_line',
				'type'    => 'checkbox',
				'title'   => __( 'Inline', 'bbp-core' ),
				'default' => false,
			],

			[
				'id'    => 'css_class',
				'type'  => 'text',
				'title' => __( 'CSS Class', 'bbp-core' ),
			],

			//TODO: Lock it for pro version. By default it will show link, in pro lightbox option to be given
			[
				'id'          => 'rel_attribute',
				'type'        => 'text',
				'title'       => __( 'REL Attribute', 'bbp-core' ),
				'placeholder' => __( 'lightbox', 'bbp-core' ),
				'desc'        => __( 'You can use these tags: %ID%, %TOPIC%', 'bbp-core' ),
			],

			//TODO: PDF preview in image preview lightbox, people can take a look at pdf without downloading, like gmail

			[
				'type'    => 'subheading',
				'content' => __( 'Image thumbnails size.', 'bbp-core' ),
			],

			[
				'type'    => 'content',
				'content' => __( 'Changing thumbnails size affects only new image attachments. To use new size for old attachments, resize them using Regenerate Thumbnails plugin.', 'bbp-core' ),
			],

			[
				'id'    => 'attachment_image_x',
				'type'  => 'text',
				'title' => __( 'Thumbnail width', 'bbp-core' ),
				'desc'  => _x( 'px', 'attachment image thumbnail size', 'bbp-core' ),
			],

			[
				'id'    => 'attachment_image_y',
				'type'  => 'text',
				'title' => __( 'Thumbnail height', 'bbp-core' ),
				'desc'  => _x( 'px', 'attachment image thumbnail size', 'bbp-core' ),
			],



		],
	]
);
