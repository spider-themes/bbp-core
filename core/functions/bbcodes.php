<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bbpc_get_shortcodes_list() : array {
	return [
		'attachment'         => [
			'title'    => __( 'Attachment', 'bbp-core' ),
			'examples' => [ '[attachment file="{file}"]', '[attachment file="{file}" width={x} height={y}]', '[attachment file="{file}" width={x} height={y} title="{title}" alt="{alt}" rel="{rel}"]' ],
			'note'     => sprintf( __( 'Only %s attribute is required for the shortcode to render result.', 'bbp-core' ), "'file'" ),
		],
		'quote'              => [
			'title'    => __( 'Quote', 'bbp-core' ),
			'examples' => [ '[quote]{content}[/quote]', '[quote quote={id}]{content}[/quote]' ],
			'note'     => sprintf( __( 'Attribute %s is the quoted topic or reply ID.', 'bbp-core' ), "'quote'" ),
		],
		'postquote'          => [
			'title'    => __( 'Post Quote', 'bbp-core' ),
			'examples' => [ '[postquote quote={id}]' ],
			'note'     => sprintf( __( 'Attribute %s is the quoted topic or reply ID.', 'bbp-core' ), "'quote'" ),
		],
		'bbpc_profile_items' => [
			'title'    => __( 'User Profile Items', 'bbp-core' ),
			'examples' => [ '[bbpc_profile_items user={user_id} items="{items_to_show}"]' ],
			'note'     => sprintf( __( 'If user is not set, or if it is set to 0, it will use logged in user ID. Comma separated list of items to show include: %s.', 'bbp-core' ), '<strong>online_status, topics_count, replies_count, thanks_given, thanks_received, registration_date</strong>' ),
		],
	];
}

function bbpc_get_bbcodes_list() : array {
	return [
		'br'         => [
			'title'    => __( 'Line Break', 'bbp-core' ),
			'examples' => [ '[br]' ],
		],
		'hr'         => [
			'title'    => __( 'Horizontal Line', 'bbp-core' ),
			'examples' => [ '[hr]' ],
		],
		'b'          => [
			'title'    => __( 'Bold', 'bbp-core' ),
			'examples' => [ '[b]{content}[/b]' ],
		],
		'i'          => [
			'title'    => __( 'Italic', 'bbp-core' ),
			'examples' => [ '[i]{content}[/i]' ],
		],
		'u'          => [
			'title'    => __( 'Underline', 'bbp-core' ),
			'examples' => [ '[u]{content}[/u]' ],
		],
		's'          => [
			'title'    => __( 'Strikethrough', 'bbp-core' ),
			'examples' => [ '[s]{content}[/s]' ],
		],
		'heading'    => [
			'title'    => __( 'Heading', 'bbp-core' ),
			'examples' => [ '[heading]{content}[/heading]', '[heading size={size}]{content}[/heading]' ],
		],
		'highlight'  => [
			'title'    => __( 'Highlight', 'bbp-core' ),
			'examples' => [ '[highlight]{content}[/highlight]', '[highlight color="{color}" background="{color}"]{content}[/highlight]' ],
		],
		'center'     => [
			'title'    => __( 'Align: Center', 'bbp-core' ),
			'examples' => [ '[center]{content}[/center]' ],
		],
		'right'      => [
			'title'    => __( 'Align: Right', 'bbp-core' ),
			'examples' => [ '[right]{content}[/right]' ],
		],
		'left'       => [
			'title'    => __( 'Align: Left', 'bbp-core' ),
			'examples' => [ '[left]{content}[/left]' ],
		],
		'justify'    => [
			'title'    => __( 'Align: Justify', 'bbp-core' ),
			'examples' => [ '[justify]{content}[/justify]' ],
		],
		'sub'        => [
			'title'    => __( 'Subscript', 'bbp-core' ),
			'examples' => [ '[sub]{content}[/sub]' ],
		],
		'sup'        => [
			'title'    => __( 'Superscript', 'bbp-core' ),
			'examples' => [ '[sup]{content}[/sup]' ],
		],
		'reverse'    => [
			'title'    => __( 'Reverse', 'bbp-core' ),
			'examples' => [ '[reverse]{content}[/reverse]' ],
		],
		'size'       => [
			'title'    => __( 'Font Size', 'bbp-core' ),
			'examples' => [ '[size size="{size}"]{content}[/size]' ],
		],
		'color'      => [
			'title'    => __( 'Font Color', 'bbp-core' ),
			'examples' => [ '[color color="{color}"]{content}[/color]' ],
		],
		'pre'        => [
			'title'    => __( 'Preformatted', 'bbp-core' ),
			'examples' => [ '[pre]{content}[/pre]' ],
		],
		'scode'      => [
			'title'    => __( 'Source Code', 'bbp-core' ),
			'examples' => [ '[scode]{content}[/scode]', '[scode lang="{language}"]{content}[/scode]' ],
		],
		'blockquote' => [
			'title'    => __( 'Blockquote', 'bbp-core' ),
			'examples' => [ '[blockquote]{content}[/blockquote]' ],
		],
		'border'     => [
			'title'    => __( 'Border', 'bbp-core' ),
			'examples' => [ '[border]{content}[/border]' ],
		],
		'area'       => [
			'title'    => __( 'Area', 'bbp-core' ),
			'examples' => [ '[area]{content}[/area]', '[area area="{title}"]{content}[/area]' ],
		],
		'list'       => [
			'title'    => __( 'List', 'bbp-core' ),
			'examples' => [ '[list]{content}[/list]' ],
		],
		'ol'         => [
			'title'    => __( 'List: Ordered', 'bbp-core' ),
			'examples' => [ '[ol]{content}[/ol]' ],
		],
		'ul'         => [
			'title'    => __( 'List: Unordered', 'bbp-core' ),
			'examples' => [ '[ul]{content}[/ul]' ],
		],
		'li'         => [
			'title'    => __( 'List: Item', 'bbp-core' ),
			'examples' => [ '[li]{content}[/li]' ],
		],
		'anchor'     => [
			'title'    => __( 'Anchor', 'bbp-core' ),
			'examples' => [ '[anchor anchor="{anchor}"]{text}[/anchor]' ],
		],
		'spoiler'    => [
			'title'    => __( 'Spoiler', 'bbp-core' ),
			'examples' => [ '[spoiler]{content}[/spoiler]', '[spoiler color="{color}" hover="{color}"]{content}[/spoiler]' ],
		],
		'hide'       => [
			'title'    => __( 'Hide', 'bbp-core' ),
			'examples' => [ '[hide]{content}[/hide]', '[hide hide={post_count}]{content}[/hide]', '[hide hide="reply"]{content}[/hide]', '[hide hide="thanks"]{content}[/hide]' ],
		],
		'forum'      => [
			'title'    => __( 'Forum', 'bbp-core' ),
			'examples' => [ '[forum]{id}[/forum]', '[forum forum={id}]{title}[/forum]' ],
		],
		'topic'      => [
			'title'    => __( 'Topic', 'bbp-core' ),
			'examples' => [ '[topic]{id}[/topic]', '[topic topic={id}]{title}[/topic]' ],
		],
		'reply'      => [
			'title'    => __( 'Reply', 'bbp-core' ),
			'examples' => [ '[reply]{id}[/reply]', '[reply topic={id}]{title}[/reply]' ],
		],
		'nfo'        => [
			'title'    => __( 'NFO', 'bbp-core' ),
			'examples' => [ '[nfo]{content}[/nfo]', '[nfo title="{title}"]{content}[/nfo]' ],
			'class'    => 'advanced',
		],
		'url'        => [
			'title'    => __( 'URL', 'bbp-core' ),
			'examples' => [ '[url]{link}[/url]', '[url url="{link}"]{text}[/url]' ],
			'class'    => 'advanced',
		],
		'email'      => [
			'title'    => __( 'Email', 'bbp-core' ),
			'examples' => [ '[email]{email}[/email]', '[email email="{email}"]{text}[/email]' ],
			'class'    => 'advanced',
		],
		'img'        => [
			'title'    => __( 'Image', 'bbp-core' ),
			'examples' => [ '[img]{image_url}[/img]', '[img img="{width}x{height}"]{image_url}[/img]', '[img width={x} height={y}]{image_url}[/img]' ],
			'class'    => 'advanced',
		],
		'webshot'    => [
			'title'    => __( 'Webshot', 'bbp-core' ),
			'examples' => [ '[webshot]{url}[/webshot]', '[webshot width={width}]{url}[/webshot]' ],
			'class'    => 'advanced',
		],
		'embed'      => [
			'title'    => __( 'Embed using oEmbed', 'bbp-core' ),
			'examples' => [ '[embed]{url}[/embed]', '[embed embed="{width}x{height}"]{url}[/embed]', '[embed width={x} height={y}]{url}[/embed]' ],
			'class'    => 'advanced',
		],
		'youtube'    => [
			'title'    => __( 'YouTube Video', 'bbp-core' ),
			'examples' => [ '[youtube]{id}[/youtube]', '[youtube youtube={width}x{height}]{id}[/youtube]', '[youtube width={x} height={y}]{id}[/youtube]', '[youtube]{url}[/youtube]', '[youtube youtube="{width}x{height}"]{url}[/youtube]', '[youtube width={x} height={y}]{url}[/youtube]' ],
			'class'    => 'advanced',
		],
		'vimeo'      => [
			'title'    => __( 'Vimeo Video', 'bbp-core' ),
			'examples' => [ '[vimeo]{id}[/vimeo]', '[vimeo vimeo="{width}x{height}"]{id}[/vimeo]', '[vimeo width={x} height={y}]{id}[/vimeo]', '[vimeo]{url}[/vimeo]', '[vimeo vimeo="{width}x{height}"]{url}[/vimeo]', '[vimeo width={x} height={y}]{url}[/vimeo]' ],
			'class'    => 'advanced',
		],
		'google'     => [
			'title'    => __( 'Google Search URL', 'bbp-core' ),
			'examples' => [ '[google]{search}[/google]' ],
			'class'    => 'advanced',
		],
		'iframe'     => [
			'title'    => __( 'Iframe', 'bbp-core' ),
			'examples' => [ '[iframe]{url}[/iframe]', '[iframe width={x} height={y} border="{width}"]{url}[/iframe]' ],
			'class'    => 'restricted',
		],
		'note'       => [
			'title'    => __( 'Hidden Note', 'bbp-core' ),
			'examples' => [ '[note]{content}[/note]' ],
			'class'    => 'restricted',
		],
	];
}
