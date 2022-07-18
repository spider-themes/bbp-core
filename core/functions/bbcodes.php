<?php

if (!defined('ABSPATH')) {
    exit;
}

function gdbbx_get_shortcodes_list() : array {
	return array(
		'attachment' => array('title' => __("Attachment", "bbp-core"), 'examples' => array('[attachment file="{file}"]', '[attachment file="{file}" width={x} height={y}]', '[attachment file="{file}" width={x} height={y} title="{title}" alt="{alt}" rel="{rel}"]'), 'note' => sprintf(__("Only %s attribute is required for the shortcode to render result.", "bbp-core"), "'file'")),
		'quote' => array('title' => __("Quote", "bbp-core"), 'examples' => array('[quote]{content}[/quote]', '[quote quote={id}]{content}[/quote]'), 'note' => sprintf(__("Attribute %s is the quoted topic or reply ID.", "bbp-core"), "'quote'")),
		'postquote' => array('title' => __("Post Quote", "bbp-core"), 'examples' => array('[postquote quote={id}]'), 'note' => sprintf(__("Attribute %s is the quoted topic or reply ID.", "bbp-core"), "'quote'")),
		'gdbbx_profile_items' => array('title' => __("User Profile Items", "bbp-core"), 'examples' => array('[gdbbx_profile_items user={user_id} items="{items_to_show}"]'), 'note' => sprintf(__("If user is not set, or if it is set to 0, it will use logged in user ID. Comma separated list of items to show include: %s.", "bbp-core"), "<strong>online_status, topics_count, replies_count, thanks_given, thanks_received, registration_date</strong>"))
	);
}

function gdbbx_get_bbcodes_list() : array {
    return array(
        'br' => array('title' => __("Line Break", "bbp-core"), 'examples' => array('[br]')),
        'hr' => array('title' => __("Horizontal Line", "bbp-core"), 'examples' => array('[hr]')),
        'b' => array('title' => __("Bold", "bbp-core"), 'examples' => array('[b]{content}[/b]')),
        'i' => array('title' => __("Italic", "bbp-core"), 'examples' => array('[i]{content}[/i]')),
        'u' => array('title' => __("Underline", "bbp-core"), 'examples' => array('[u]{content}[/u]')),
        's' => array('title' => __("Strikethrough", "bbp-core"), 'examples' => array('[s]{content}[/s]')),
        'heading' => array('title' => __("Heading", "bbp-core"), 'examples' => array('[heading]{content}[/heading]', '[heading size={size}]{content}[/heading]')),
        'highlight' => array('title' => __("Highlight", "bbp-core"), 'examples' => array('[highlight]{content}[/highlight]', '[highlight color="{color}" background="{color}"]{content}[/highlight]')),
        'center' => array('title' => __("Align: Center", "bbp-core"), 'examples' => array('[center]{content}[/center]')),
        'right' => array('title' => __("Align: Right", "bbp-core"), 'examples' => array('[right]{content}[/right]')),
        'left' => array('title' => __("Align: Left", "bbp-core"), 'examples' => array('[left]{content}[/left]')),
        'justify' => array('title' => __("Align: Justify", "bbp-core"), 'examples' => array('[justify]{content}[/justify]')),
        'sub' => array('title' => __("Subscript", "bbp-core"), 'examples' => array('[sub]{content}[/sub]')),
        'sup' => array('title' => __("Superscript", "bbp-core"), 'examples' => array('[sup]{content}[/sup]')),
        'reverse' => array('title' => __("Reverse", "bbp-core"), 'examples' => array('[reverse]{content}[/reverse]')),
        'size' => array('title' => __("Font Size", "bbp-core"), 'examples' => array('[size size="{size}"]{content}[/size]')),
        'color' => array('title' => __("Font Color", "bbp-core"), 'examples' => array('[color color="{color}"]{content}[/color]')),
        'pre' => array('title' => __("Preformatted", "bbp-core"), 'examples' => array('[pre]{content}[/pre]')),
        'scode' => array('title' => __("Source Code", "bbp-core"), 'examples' => array('[scode]{content}[/scode]', '[scode lang="{language}"]{content}[/scode]')),
        'blockquote' => array('title' => __("Blockquote", "bbp-core"), 'examples' => array('[blockquote]{content}[/blockquote]')),
        'border' => array('title' => __("Border", "bbp-core"), 'examples' => array('[border]{content}[/border]')),
        'area' => array('title' => __("Area", "bbp-core"), 'examples' => array('[area]{content}[/area]', '[area area="{title}"]{content}[/area]')),
        'list' => array('title' => __("List", "bbp-core"), 'examples' => array('[list]{content}[/list]')),
        'ol' => array('title' => __("List: Ordered", "bbp-core"), 'examples' => array('[ol]{content}[/ol]')),
        'ul' => array('title' => __("List: Unordered", "bbp-core"), 'examples' => array('[ul]{content}[/ul]')),
        'li' => array('title' => __("List: Item", "bbp-core"), 'examples' => array('[li]{content}[/li]')),
        'anchor' => array('title' => __("Anchor", "bbp-core"), 'examples' => array('[anchor anchor="{anchor}"]{text}[/anchor]')),
        'spoiler' => array('title' => __("Spoiler", "bbp-core"), 'examples' => array('[spoiler]{content}[/spoiler]', '[spoiler color="{color}" hover="{color}"]{content}[/spoiler]')),
        'hide' => array('title' => __("Hide", "bbp-core"), 'examples' => array('[hide]{content}[/hide]', '[hide hide={post_count}]{content}[/hide]', '[hide hide="reply"]{content}[/hide]', '[hide hide="thanks"]{content}[/hide]')),
        'forum' => array('title' => __("Forum", "bbp-core"), 'examples' => array('[forum]{id}[/forum]', '[forum forum={id}]{title}[/forum]')),
        'topic' => array('title' => __("Topic", "bbp-core"), 'examples' => array('[topic]{id}[/topic]', '[topic topic={id}]{title}[/topic]')),
        'reply' => array('title' => __("Reply", "bbp-core"), 'examples' => array('[reply]{id}[/reply]', '[reply topic={id}]{title}[/reply]')),
        'nfo' => array('title' => __("NFO", "bbp-core"), 'examples' => array('[nfo]{content}[/nfo]', '[nfo title="{title}"]{content}[/nfo]'), 'class' => 'advanced'),
        'url' => array('title' => __("URL", "bbp-core"), 'examples' => array('[url]{link}[/url]', '[url url="{link}"]{text}[/url]'), 'class' => 'advanced'),
        'email' => array('title' => __("Email", "bbp-core"), 'examples' => array('[email]{email}[/email]', '[email email="{email}"]{text}[/email]'), 'class' => 'advanced'),
        'img' => array('title' => __("Image", "bbp-core"), 'examples' => array('[img]{image_url}[/img]', '[img img="{width}x{height}"]{image_url}[/img]', '[img width={x} height={y}]{image_url}[/img]'), 'class' => 'advanced'),
        'webshot' => array('title' => __("Webshot", "bbp-core"), 'examples' => array('[webshot]{url}[/webshot]', '[webshot width={width}]{url}[/webshot]'), 'class' => 'advanced'),
        'embed' => array('title' => __("Embed using oEmbed", "bbp-core"), 'examples' => array('[embed]{url}[/embed]', '[embed embed="{width}x{height}"]{url}[/embed]', '[embed width={x} height={y}]{url}[/embed]'), 'class' => 'advanced'),
        'youtube' => array('title' => __("YouTube Video", "bbp-core"), 'examples' => array('[youtube]{id}[/youtube]', '[youtube youtube={width}x{height}]{id}[/youtube]', '[youtube width={x} height={y}]{id}[/youtube]', '[youtube]{url}[/youtube]', '[youtube youtube="{width}x{height}"]{url}[/youtube]', '[youtube width={x} height={y}]{url}[/youtube]'), 'class' => 'advanced'),
        'vimeo' => array('title' => __("Vimeo Video", "bbp-core"), 'examples' => array('[vimeo]{id}[/vimeo]', '[vimeo vimeo="{width}x{height}"]{id}[/vimeo]', '[vimeo width={x} height={y}]{id}[/vimeo]', '[vimeo]{url}[/vimeo]', '[vimeo vimeo="{width}x{height}"]{url}[/vimeo]', '[vimeo width={x} height={y}]{url}[/vimeo]'), 'class' => 'advanced'),
        'google' => array('title' => __("Google Search URL", "bbp-core"), 'examples' => array('[google]{search}[/google]'), 'class' => 'advanced'),
        'iframe' => array('title' => __("Iframe", "bbp-core"), 'examples' => array('[iframe]{url}[/iframe]', '[iframe width={x} height={y} border="{width}"]{url}[/iframe]'), 'class' => 'restricted'),
        'note' => array('title' => __("Hidden Note", "bbp-core"), 'examples' => array('[note]{content}[/note]'), 'class' => 'restricted')
    );
}
