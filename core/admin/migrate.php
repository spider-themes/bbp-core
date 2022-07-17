<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bbpc_settings_migration() : bool {
	$_save = [];

	// views //
	if ( isset( bbpc()->temp['tools']['view_newposts_active'] ) ) {
		foreach ( bbpc()->temp['tools'] as $key => $value ) {
			if ( substr( $key, 0, 5 ) == 'view_' ) {
				$new = str_replace( 'view_', 'custom-views__', $key );
				bbpc()->set( $new, bbpc()->temp['tools'][ $key ], 'features' );
			}
		}

		$_save[] = 'features';
	}

	// mime-types //
	if ( isset( bbpc()->temp['tools']['extra_mime_types'] ) ) {
		bbpc()->set( 'mime-types__list', bbpc()->temp['tools']['extra_mime_types'], 'features' );

		$_save[] = 'features';
	}

	// buddypress //
	if ( isset( bbpc()->temp['bbpress']['disable_buddypress_profile_override'] ) ) {
		bbpc()->set( 'disable_profile_override', bbpc()->temp['bbpress']['disable_buddypress_profile_override'], 'buddypress' );

		$_save[] = 'buddypress';
	}

	// clickable //
	if ( isset( bbpc()->temp['bbpress']['disable_make_clickable_topic'] ) ) {
		$list = [
			'disable_make_clickable_topic',
			'disable_make_clickable_reply',
			'remove_clickable_urls',
			'remove_clickable_ftps',
			'remove_clickable_emails',
			'remove_clickable_mentions',
		];

		foreach ( $list as $key ) {
			bbpc()->set( 'clickable__' . $key, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// privacy //
	if ( isset( bbpc()->temp['privacy']['disable_ip_logging'] ) ) {
		$list = [ 'disable_ip_logging', 'disable_ip_display' ];

		foreach ( $list as $key ) {
			bbpc()->set( 'privacy__' . $key, bbpc()->temp['privacy'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// objects //
	if ( isset( bbpc()->temp['tools']['add_forum_features'] ) ) {
		$list = [ 'add_forum_features', 'add_topic_features', 'add_reply_features' ];

		foreach ( $list as $key ) {
			bbpc()->set( 'objects__' . $key, bbpc()->temp['tools'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// publish //
	if ( isset( bbpc()->temp['bbpress']['bbp_is_site_public'] ) ) {
		$list = [ 'bbp_is_site_public' ];

		foreach ( $list as $key ) {
			bbpc()->set( 'publish__' . $key, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// thanks //
	if ( isset( bbpc()->temp['thanks']['active'] ) ) {
		$list = [
			'removal',
			'topic',
			'reply',
			'allow_super_admin',
			'allow_roles',
			'limit_display',
			'display_date',
			'notify_active',
			'notify_shortcodes',
			'notify_content',
			'notify_subject',
		];

		foreach ( $list as $key ) {
			bbpc()->set( 'thanks__' . $key, bbpc()->temp['thanks'][ $key ], 'features' );
		}

		$load = bbpc()->temp['thanks']['active'];
		bbpc()->set( 'thanks', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// protect-revisions //
	if ( isset( bbpc()->temp['bbpress']['revisions_reply_protection_active'] ) ) {
		$list = [
			'revisions_reply_protection_author',
			'revisions_reply_protection_topic_author',
			'revisions_reply_protection_super_admin',
			'revisions_reply_protection_roles',
			'revisions_reply_protection_visitor',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'revisions_reply_protection_', 'protect-revisions__allow_', $key );
			bbpc()->set( $new, bbpc()->temp['tools'][ $key ], 'features' );
		}

		$load = bbpc()->temp['bbpress']['revisions_reply_protection_active'];
		bbpc()->set( 'protect-revisions', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// admin-access //
	if ( isset( bbpc()->temp['tools']['admin_disable_active'] ) ) {
		bbpc()->set( 'admin-access__disable_roles', bbpc()->temp['thanks']['admin_disable_roles'], 'features' );

		$load = bbpc()->temp['tools']['admin_disable_active'];
		bbpc()->set( 'admin-access', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// tweaks //
	if ( isset( bbpc()->temp['bbpress']['topic_load_search_for_all_topics'] ) ) {
		$list = [
			'topic_load_search_for_all_topics',
			'forum_load_search_for_all_forums',
			'fix_404_headers_error',
			'remove_private_title_prefix',
			'participant_media_library_upload',
			'kses_allowed_override' => 'bbpress',
			'disable_bbpress_breadcrumbs',
			'title_length_override',
			'title_length_value',
			'apply_fitvids_to_content',
		];

		foreach ( $list as $key ) {
			bbpc()->set( 'tweaks__' . $key, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// profiles //
	if ( isset( bbpc()->temp['bbpress']['user_profile_hide_from_visitors'] ) ) {
		$list = [
			'user_profile_hide_from_visitors',
			'user_profile_extras_display',
			'user_profile_extras_actions',
			'user_profile_extras_private',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'user_profile_', 'profiles__', $key );
			bbpc()->set( $new, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// topics //
	if ( isset( bbpc()->temp['bbpress']['new_topic_minmax_active'] ) ) {
		$list = [
			'new_topic_minmax_active',
			'new_topic_min_title_length',
			'new_topic_min_content_length',
			'new_topic_max_title_length',
			'new_topic_max_content_length',
			'enable_lead_topic',
			'enable_topic_reversed_replies',
			'forum_list_topic_thumbnail',
		];

		foreach ( $list as $key ) {
			bbpc()->set( 'topics__' . $key, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// replies //
	if ( isset( bbpc()->temp['bbpress']['new_reply_minmax_active'] ) ) {
		$list = [
			'new_reply_minmax_active',
			'new_reply_min_title_length',
			'new_reply_min_content_length',
			'new_reply_max_title_length',
			'new_reply_max_content_length',
			'tags_in_reply_form_only_for_author',
			'reply_titles',
		];

		foreach ( $list as $key ) {
			bbpc()->set( 'replies__' . $key, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// topic-actions //
	if ( isset( bbpc()->temp['bbpress']['topic_links_remove_merge'] ) ) {
		bbpc()->set( 'topic-actions__merge', bbpc()->temp['bbpress']['topic_links_remove_merge'] ? 'hide' : 'header', 'features' );
		bbpc()->set( 'topic-actions__edit', bbpc()->temp['bbpress']['topic_links_edit_footer'] ? 'footer' : 'header', 'features' );
		bbpc()->set( 'topic-actions__reply', bbpc()->temp['bbpress']['topic_links_reply_footer'] ? 'footer' : 'header', 'features' );

		if ( bbpc()->temp['bbpress']['topic_single_copy_active'] ) {
			bbpc()->set( 'topic-actions__duplicate', bbpc()->temp['bbpress']['topic_single_copy_location'] == 'footer' ? 'footer' : 'header', 'features' );
		} else {
			bbpc()->set( 'topic-actions__duplicate', 'hide', 'features' );
		}

		bbpc()->set( 'topic-actions__lock', bbpc()->temp['lock']['button_topic_lock_location'] == 'footer' ? 'footer' : 'header', 'features' );
		bbpc()->set( 'topic-actions__report', bbpc()->temp['report']['location'] == 'footer' ? 'footer' : 'header', 'features' );
		bbpc()->set( 'topic-actions__thanks', bbpc()->temp['thanks']['location'] == 'footer' ? 'footer' : 'header', 'features' );
		bbpc()->set( 'topic-actions__quote', bbpc()->temp['tools']['quote_location'] == 'footer' ? 'footer' : 'header', 'features' );

		$_save[] = 'features';
	}

	// reply-actions //
	if ( isset( bbpc()->temp['bbpress']['reply_links_remove_split'] ) ) {
		bbpc()->set( 'reply-actions__split', bbpc()->temp['bbpress']['reply_links_remove_split'] ? 'hide' : 'header', 'features' );
		bbpc()->set( 'reply-actions__edit', bbpc()->temp['bbpress']['reply_links_edit_footer'] ? 'footer' : 'header', 'features' );
		bbpc()->set( 'reply-actions__reply', bbpc()->temp['bbpress']['reply_links_reply_footer'] ? 'footer' : 'header', 'features' );

		bbpc()->set( 'reply-actions__report', bbpc()->temp['report']['location'] == 'footer' ? 'footer' : 'header', 'features' );
		bbpc()->set( 'reply-actions__thanks', bbpc()->temp['thanks']['location'] == 'footer' ? 'footer' : 'header', 'features' );
		bbpc()->set( 'reply-actions__quote', bbpc()->temp['tools']['quote_location'] == 'footer' ? 'footer' : 'header', 'features' );

		$_save[] = 'features';
	}

	// icons //
	if ( isset( bbpc()->temp['bbpress']['forum_mark_stick'] ) ) {
		$list = [ 'forum_mark_stick', 'forum_mark_lock', 'forum_mark_replied', 'private_topics_icon' ];

		foreach ( $list as $key ) {
			bbpc()->set( 'icons__' . $key, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// forum-index //
	if ( isset( bbpc()->temp['bbpress']['forum_load_welcome_front'] ) ) {
		foreach ( bbpc()->temp['bbpress'] as $key => $value ) {
			if ( strpos( $key, 'forum_load_' ) === 0 ) {
				$new = str_replace( 'forum_load_', 'forum-index__', $key );
				bbpc()->set( $new, $value, 'features' );
			}
		}

		$load = bbpc()->temp['bbpress']['forum_load_welcome_front'] || bbpc()->temp['bbpress']['forum_load_statistics_front'];
		bbpc()->set( 'forum-index', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// disable-rss //
	if ( isset( bbpc()->temp['disable_rss']['active'] ) ) {
		foreach ( bbpc()->temp['disable_rss'] as $key => $value ) {
			if ( $key != 'active' ) {
				bbpc()->set( 'disable-rss__' . $key, $value, 'features' );
			}
		}

		$load = bbpc()->temp['disable_rss']['active'];
		bbpc()->set( 'disable-rss', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// visitors-redirect //
	if ( isset( bbpc()->temp['lock']['redirect_for_visitors'] ) ) {
		$list = [
			'redirect_for_visitors',
			'redirect_for_visitors_url',
			'redirect_hidden_forums',
			'redirect_hidden_forums_url',
			'redirect_private_forums',
			'redirect_private_forums_url',
			'redirect_blocked_users',
			'redirect_blocked_users_url',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'redirect_', 'visitors-redirect__', $key );
			bbpc()->set( $new, bbpc()->temp['lock'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// toolbar //
	if ( isset( bbpc()->temp['tools']['toolbar_active'] ) ) {
		$list = [
			'toolbar_super_admin',
			'toolbar_visitor',
			'toolbar_roles',
			'toolbar_title',
			'toolbar_information',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'toolbar_', 'toolbar__', $key );
			bbpc()->set( $new, bbpc()->temp['tools'][ $key ], 'features' );
		}

		$load = bbpc()->temp['tools']['toolbar_active'];
		bbpc()->set( 'toolbar', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// quote //
	if ( isset( bbpc()->temp['tools']['quote_active'] ) ) {
		$list = [ 'quote_method', 'quote_super_admin', 'quote_visitor', 'quote_roles', 'quote_full_content' ];

		foreach ( $list as $key ) {
			$new = str_replace( 'quote_', 'quote__', $key );
			bbpc()->set( $new, bbpc()->temp['tools'][ $key ], 'features' );
		}

		$load = bbpc()->temp['tools']['quote_active'];
		bbpc()->set( 'quote', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// users-stats //
	if ( isset( bbpc()->temp['tools']['users_stats_active'] ) ) {
		$list = [
			'users_stats_super_admin',
			'users_stats_visitor',
			'users_stats_roles',
			'users_stats_show_registration_date',
			'users_stats_show_topics',
			'users_stats_show_replies',
			'users_stats_show_thanks_given',
			'users_stats_show_thanks_received',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'users_stats_', 'users-stats__', $key );
			bbpc()->set( $new, bbpc()->temp['tools'][ $key ], 'features' );
		}

		$load = bbpc()->temp['tools']['users_stats_active'];
		bbpc()->set( 'users-stats', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// admin-widgets //
	if ( isset( bbpc()->temp['tools']['dashboard_widget_activity'] ) ) {
		bbpc()->set( 'admin-widgets__activity', bbpc()->temp['tools']['dashboard_widget_activity'], 'features' );
		bbpc()->set( 'admin-widgets__online', bbpc()->temp['online']['dashboard_widget'], 'features' );

		$load = bbpc()->temp['tools']['dashboard_widget_activity'] || bbpc()->temp['online']['dashboard_widget'];
		bbpc()->set( 'admin-widgets', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// canned-replies //
	if ( isset( bbpc()->temp['canned']['active'] ) ) {
		foreach ( bbpc()->temp['canned'] as $key => $value ) {
			if ( $key != 'active' ) {
				bbpc()->set( 'canned-replies__' . $key, $value, 'features' );
			}
		}

		$load = bbpc()->temp['canned']['active'];
		bbpc()->set( 'canned-replies', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// lock-forums //
	if ( isset( bbpc()->temp['lock']['topic_form_locked'] ) ) {
		foreach ( bbpc()->temp['lock'] as $key => $value ) {
			bbpc()->set( 'lock-forums__' . $key, $value, 'features' );
		}

		$_save[] = 'features';
	}

	// signatures //
	if ( isset( bbpc()->temp['tools']['signature_active'] ) ) {
		$list = [
			'signature_active',
			'signature_scope',
			'signature_limiter',
			'signature_length',
			'signature_super_admin',
			'signature_roles',
			'signature_edit_super_admin',
			'signature_edit_roles',
			'signature_editor',
			'signature_enhanced_active',
			'signature_enhanced_method',
			'signature_enhanced_super_admin',
			'signature_enhanced_roles',
			'signature_process_smilies',
			'signature_process_chars',
			'signature_process_autop',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'signature_', 'signatures__', $key );
			bbpc()->set( $new, bbpc()->temp['tools'][ $key ], 'features' );
		}

		$load = bbpc()->temp['tools']['signature_active'];
		bbpc()->set( 'signatures', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// seo-tweaks //
	if ( isset( bbpc()->temp['bbpress']['nofollow_topic_content'] ) ) {
		$list = [
			'nofollow_topic_content',
			'nofollow_reply_content',
			'nofollow_topic_author',
			'nofollow_reply_author',
		];

		foreach ( $list as $key ) {
			bbpc()->set( 'seo-tweaks__' . $key, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// report //
	if ( isset( bbpc()->temp['report']['active'] ) ) {
		foreach ( bbpc()->temp['report'] as $key => $value ) {
			if ( $key != 'active' ) {
				bbpc()->set( 'report__' . $key, $value, 'features' );
			}
		}

		$load = bbpc()->temp['report']['active'];
		bbpc()->set( 'report', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// private-topics //
	if ( isset( bbpc()->temp['bbpress']['private_topics'] ) ) {
		$list = [
			'private_topics_super_admin',
			'private_topics_roles',
			'private_topics_visitor',
			'private_topics_default',
			'private_topics_moderators_can_read',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'private_topics_', 'private-topics__', $key );
			bbpc()->set( $new, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$load = bbpc()->temp['bbpress']['private_topics'];
		bbpc()->set( 'private-topics', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// private-replies //
	if ( isset( bbpc()->temp['bbpress']['private_replies'] ) ) {
		$list = [
			'private_topics_super_admin',
			'private_topics_roles',
			'private_topics_visitor',
			'private_topics_default',
			'private_topics_moderators_can_read',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'private_replies_', 'private-replies__', $key );
			bbpc()->set( $new, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$load = bbpc()->temp['bbpress']['private_replies'];
		bbpc()->set( 'private-replies', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// auto-close-topics //
	if ( isset( bbpc()->temp['bbpress']['topic_auto_close_after_status'] ) ) {
		$list = [
			'topic_auto_close_after_active',
			'topic_auto_close_after_notice',
			'topic_auto_close_after_days',
			'topic_auto_close_after_notify_author',
			'topic_auto_close_after_notify_subscribers',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'topic_auto_close_after_', 'auto-close-topics__', $key );
			bbpc()->set( $new, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$load = bbpc()->temp['bbpress']['topic_auto_close_after_status'];
		bbpc()->set( 'auto-close-topics', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// auto-close-topics - notify //
	if ( isset( bbpc()->temp['bbpress']['notify_on_topic_auto_close_active'] ) ) {
		$list = [
			'notify_on_topic_auto_close_active',
			'notify_on_topic_auto_close_shortcodes',
			'notify_on_topic_auto_close_content',
			'notify_on_topic_auto_close_subject',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'notify_on_topic_auto_close_', 'auto-close-topics__notify_', $key );
			bbpc()->set( $new, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// close-topic-control //
	if ( isset( bbpc()->temp['bbpress']['reply_close_topic_checkbox_active'] ) ) {
		$list = [
			'reply_close_topic_checkbox_topic_author',
			'reply_close_topic_checkbox_super_admin',
			'reply_close_topic_checkbox_roles',
			'reply_close_topic_checkbox_form_position',
			'reply_close_topic_checkbox_notify_author',
			'reply_close_topic_checkbox_notify_subscribers',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'reply_close_topic_checkbox_', 'close-topic-control__', $key );
			bbpc()->set( $new, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$load = bbpc()->temp['bbpress']['reply_close_topic_checkbox_active'];
		bbpc()->set( 'close-topic-control', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// close-topic-control - notify //
	if ( isset( bbpc()->temp['bbpress']['notify_on_topic_checkbox_close_active'] ) ) {
		$list = [
			'notify_on_topic_checkbox_close_active',
			'notify_on_topic_checkbox_close_shortcodes',
			'notify_on_topic_checkbox_close_content',
			'notify_on_topic_checkbox_close_subject',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'notify_on_topic_checkbox_close_', 'close-topic-control__notify_', $key );
			bbpc()->set( $new, bbpc()->temp['bbpress'][ $key ], 'features' );
		}

		$_save[] = 'features';
	}

	// notifications //
	if ( isset( bbpc()->temp['bbpress']['new_topic_notification_keymaster'] ) ) {
		bbpc()->set( 'notifications__new_topic_keymaster', bbpc()->temp['bbpress']['new_topic_notification_keymaster'], 'features' );
		bbpc()->set( 'notifications__new_topic_moderator', bbpc()->temp['bbpress']['new_topic_notification_moderator'], 'features' );
		bbpc()->set( 'notifications__topic_on_edit', bbpc()->temp['bbpress']['topic_notification_on_edit'], 'features' );
		bbpc()->set( 'notifications__reply_on_edit', bbpc()->temp['bbpress']['reply_notification_on_edit'], 'features' );

		bbpc()->set( 'email-sender', bbpc()->temp['bbpress']['notify_subscribers_sender_active'], 'load' );
		bbpc()->set( 'email-sender__sender_name', bbpc()->temp['bbpress']['notify_subscribers_sender_name'], 'features' );
		bbpc()->set( 'email-sender__sender_email', bbpc()->temp['bbpress']['notify_subscribers_sender_email'], 'features' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// shortcodes //
	if ( isset( bbpc()->temp['bbpress']['bbcodes_attachment_caption'] ) ) {
		bbpc()->set( 'shortcodes__attachment_caption', bbpc()->temp['bbpress']['bbcodes_attachment_caption'], 'features' );
		bbpc()->set( 'shortcodes__attachment_video_caption', bbpc()->temp['bbpress']['bbcodes_attachment_video_caption'], 'features' );
		bbpc()->set( 'shortcodes__attachment_audio_caption', bbpc()->temp['bbpress']['bbcodes_attachment_audio_caption'], 'features' );
		bbpc()->set( 'shortcodes__quote_title', bbpc()->temp['bbpress']['bbcodes_quote_title'], 'features' );

		$_save[] = 'features';
	}

	// email overrides //
	if ( isset( bbpc()->temp['bbpress']['notify_subscribers_override_active'] ) ) {
		foreach ( bbpc()->temp['bbpress'] as $key => $value ) {
			if ( substr( $key, 0, 7 ) == 'notify_' ) {
				bbpc()->set( 'email-overrides__' . $key, $value, 'features' );
			}
		}

		$_save[] = 'features';
	}

	// bbcodes //
	if ( isset( bbpc()->temp['tools']['bbcodes_active'] ) ) {
		$_bbcodes = [
			'hide_title',
			'hide_content_normal',
			'hide_content_count',
			'hide_content_reply',
			'hide_content_thanks',
			'hide_keymaster_always_allowed',
			'spoiler_color',
			'spoiler_hover',
			'scode_script',
			'scode_enlighter',
			'scode_theme',
			'highlight_color',
			'highlight_background',
			'heading_size',
		];

		bbpc()->set( 'bbcodes__notice', bbpc()->temp['tools']['bbcodes_notice'], 'features' );
		bbpc()->set( 'bbcodes__bbpress_only', bbpc()->temp['tools']['bbcodes__bbpress_only'], 'features' );
		bbpc()->set( 'bbcodes__restricted', bbpc()->temp['tools']['bbcodes_special_action'], 'features' );

		foreach ( $_bbcodes as $name ) {
			if ( isset( bbpc()->temp['tools'][ 'bbcodes_' . $name ] ) ) {
				bbpc()->set( 'bbcodes__' . $name, bbpc()->temp['tools'][ 'bbcodes_' . $name ], 'features' );
			}
		}

		bbpc()->set( 'bbcodes', bbpc()->temp['tools']['bbcodes_active'], 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// rich editor //
	if ( isset( bbpc()->temp['tools']['editor_topic_tinymce'] ) ) {
		$list = [
			'editor_topic_teeny',
			'editor_topic_media_buttons',
			'editor_topic_wpautop',
			'editor_topic_quicktags',
			'editor_topic_textarea_rows',
			'editor_reply_teeny',
			'editor_reply_media_buttons',
			'editor_reply_wpautop',
			'editor_reply_quicktags',
			'editor_reply_textarea_rows',
		];

		foreach ( $list as $key ) {
			$new = str_replace( 'editor_', 'content-editor__tinymce_', $key );
			bbpc()->set( $new, bbpc()->temp['tools'][ $key ], 'features' );
		}

		bbpc()->set( 'content-editor__topic', bbpc()->temp['tools']['editor_topic_active'] ? 'tinymce' : 'textarea', 'features' );
		bbpc()->set( 'content-editor__reply', bbpc()->temp['tools']['editor_reply_active'] ? 'tinymce' : 'textarea', 'features' );

		$_save[] = 'features';
	}

	// bbcodes-toolbar //
	if ( isset( bbpc()->temp['tools']['bbcodes_toolbar_active'] ) ) {
		bbpc()->set( 'content-editor__bbcodes_topic_size', bbpc()->temp['tools']['bbcodes_toolbar_size'], 'features' );
		bbpc()->set( 'content-editor__bbcodes_topic_editor_fix', bbpc()->temp['tools']['bbcodes_toolbar_editor_fix'], 'features' );
		bbpc()->set( 'content-editor__bbcodes_reply_size', bbpc()->temp['tools']['bbcodes_toolbar_size'], 'features' );
		bbpc()->set( 'content-editor__bbcodes_reply_editor_fix', bbpc()->temp['tools']['bbcodes_toolbar_editor_fix'], 'features' );

		if ( bbpc()->get( 'content-editor__topic', 'features' ) == 'textarea' ) {
			bbpc()->set( 'content-editor__topic', bbpc()->temp['tools']['bbcodes_toolbar_active'] ? 'bbcodes' : 'textarea', 'features' );
		}

		if ( bbpc()->get( 'content-editor__reply', 'features' ) == 'textarea' ) {
			bbpc()->set( 'content-editor__reply', bbpc()->temp['tools']['bbcodes_toolbar_active'] ? 'bbcodes' : 'textarea', 'features' );
		}

		$_save[] = 'features';
		$_save[] = 'load';
	}

	if ( isset( bbpc()->temp['features']['editor__topic_tinymce'] ) ) {
		$list = [
			'teeny',
			'media_buttons',
			'wpautop',
			'quicktags',
			'textarea_rows',
		];

		foreach ( $list as $item ) {
			$topic = 'editor__topic_' . $item;
			$reply = 'editor__reply_' . $item;

			if ( isset( bbpc()->temp['features'][ $topic ] ) ) {
				$new = 'content-editor__tinymce_topic_' . $item;
				bbpc()->set( $new, bbpc()->temp['features'][ $topic ], 'features' );
			}

			if ( isset( bbpc()->temp['features'][ $reply ] ) ) {
				$new = 'content-editor__tinymce_reply_' . $item;
				bbpc()->set( $new, bbpc()->temp['features'][ $topic ], 'features' );
			}
		}

		if ( bbpc()->temp['features']['editor__topic_tinymce'] ) {
			bbpc()->set( 'content-editor__topic', bbpc()->temp['features']['editor__topic_tinymce'] ? 'tinymce' : 'textarea', 'features' );
		}

		if ( bbpc()->temp['features']['editor__reply_tinymce'] ) {
			bbpc()->set( 'content-editor__reply', bbpc()->temp['features']['editor__reply_tinymce'] ? 'tinymce' : 'textarea', 'features' );
		}

		$_save[] = 'features';
	}

	// editor //
	if ( bbpc()->get( 'editor_topic' ) == 'textarea' && bbpc()->get( 'editor_reply' ) == 'textarea' ) {
		if ( bbp_use_wp_editor() ) {
			if ( bbpc()->get( 'editor', 'load' ) ) {
				if ( bbpc()->get( 'editor__topic_tinymce', 'features' ) ) {
					bbpc()->set( 'editor_topic', 'tinymce' );

					$_save[] = 'settings';
				}

				if ( bbpc()->get( 'editor__reply_tinymce', 'features' ) ) {
					bbpc()->set( 'editor_reply', 'tinymce' );

					$_save[] = 'settings';
				}
			}
		} else {
			if ( bbpc()->get( 'bbcodes-toolbar', 'load' ) ) {
				bbpc()->set( 'editor_topic', 'bbcodes' );
				bbpc()->set( 'editor_reply', 'bbcodes' );

				$_save[] = 'settings';
			}
		}
	}

	// buddypress-tweaks //
	if ( isset( bbpc()->temp['buddypress']['disable_profile_override'] ) ) {
		bbpc()->set( 'buddypress-tweaks__disable_profile_override', bbpc()->temp['buddypress']['disable_profile_override'], 'features' );
		bbpc()->set( 'buddypress-tweaks', bbpc()->temp['buddypress']['disable_profile_override'], 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// buddypress-notification //
	if ( isset( bbpc()->temp['buddypress']['notifications_support'] ) ) {
		bbpc()->set( 'buddypress-notifications__thanks_received', bbpc()->temp['buddypress']['notifications_thanks_received'], 'features' );
		bbpc()->set( 'buddypress-notifications__reported', bbpc()->temp['buddypress']['notifications_post_reported'], 'features' );
		bbpc()->set( 'buddypress-notifications', bbpc()->temp['buddypress']['notifications_support'], 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// buddypress-signature //
	if ( isset( bbpc()->temp['buddypress']['xprofile_support'] ) ) {
		bbpc()->set( 'buddypress-signature__xfield_id', bbpc()->temp['buddypress']['xprofile_signature_field_id'], 'features' );
		bbpc()->set( 'buddypress-signature', bbpc()->temp['buddypress']['xprofile_support'], 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	// seo //
	if ( isset( bbpc()->temp['seo']['document_title_parts'] ) ) {
		foreach ( bbpc()->temp['seo'] as $key => $value ) {
			bbpc()->set( 'seo__' . $key, $value, 'features' );
		}

		$_save[] = 'features';
	}

	// attachments //
	if ( isset( bbpc()->temp['attachments']['attachments_active'] ) ) {
		foreach ( bbpc()->temp['attachments'] as $key => $value ) {
			if ( ! in_array( $key, [ 'attachments_active', 'validation_active' ] ) ) {
				bbpc()->set( 'attachments__' . $key, $value, 'features' );
			}
		}

		$method = bbpc()->temp['attachments']['validation_active'] ? 'enhanced' : 'classic';
		bbpc()->set( 'attachments__method', $method, 'features' );

		$mode = bbpc()->temp['attachments']['image_thumbnail_active'] ? 'mixed' : 'list';
		bbpc()->set( 'attachments__files_list_mode', $mode, 'features' );

		$load = bbpc()->temp['attachments']['attachments_active'];
		bbpc()->set( 'attachments', $load, 'load' );

		$_save[] = 'features';
		$_save[] = 'load';
	}

	$_save = array_unique( $_save );

	foreach ( $_save as $key ) {
		bbpc()->save( $key );
	}

	return ! empty( $_save );
}
