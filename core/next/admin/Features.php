<?php

namespace Dev4Press\Plugin\GDBBX\Admin;

use d4pSettingElement;
use d4pSettingType;
use Dev4Press\Plugin\GDBBX\Basic\BB;
use Dev4Press\Plugin\GDBBX\Basic\Helper;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;
use Dev4Press\Plugin\GDBBX\Features\AutoCloseTopics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Features {
	public $list;

	public function __construct() {
		$this->init_features();

		add_filter( 'gdbbx_internal_settings', array( $this, 'internal' ) );
	}

	public static function instance() : Features {
		static $instance = false;

		if ( $instance === false ) {
			$instance = new Features();
		}

		return $instance;
	}

	private function init_features() {
		$this->list = array(
			'icons'               => array(
				'icon'  => 'picture-o',
				'scope' => 'front',
				'title' => __( "Icons", "bbp-core" ),
				'info'  => __( "Control icons or marks added in the content lists for topics or forums.", "bbp-core" )
			),
			'tweaks'              => array(
				'icon'  => 'check-square',
				'scope' => 'global',
				'title' => __( "Tweaks", "bbp-core" ),
				'info'  => __( "Control over various tweaks and improvements for the bbPress powered forums.", "bbp-core" )
			),
			'shortcodes'          => array(
				'icon'  => 'code',
				'scope' => 'global',
				'title' => __( "Shortcodes", "bbp-core" ),
				'info'  => __( "Various shortcodes that are always active, not connected to the BBCodes.", "bbp-core" )
			),
			'content-editor'      => array(
				'icon'  => 'pencil',
				'scope' => 'front',
				'title' => __( "Content Editor", "bbp-core" ),
				'info'  => __( "Centralized control over the topic and reply form content editor with options to control different types of editors.", "bbp-core" )
			),
			'topic-actions'       => array(
				'icon'  => 'd4p-icon-bbpress-topic',
				'scope' => 'front',
				'title' => __( "Topic Actions", "bbp-core" ),
				'info'  => __( "Control available bbPress and Toolbox actions for each topic.", "bbp-core" )
			),
			'reply-actions'       => array(
				'icon'  => 'd4p-icon-bbpress-reply',
				'scope' => 'front',
				'title' => __( "Reply Actions", "bbp-core" ),
				'info'  => __( "Control available bbPress and Toolbox actions for each reply.", "bbp-core" )
			),
			'user-settings'       => array(
				'icon'  => 'user-o',
				'scope' => 'global',
				'title' => __( "User Settings", "bbp-core" ),
				'info'  => __( "Add additional settings into user profile and bbPress user profile edit.", "bbp-core" )
			),
			'custom-views'        => array(
				'icon'  => 'files-o',
				'scope' => 'global',
				'title' => __( "Custom Topic Views", "bbp-core" ),
				'info'  => __( "Add more topic views. Plugin can register basic, moderation and private topics views.", "bbp-core" )
			),
			'attachments'         => array(
				'icon'  => 'paperclip',
				'scope' => 'global',
				'title' => __( "Attachments", "bbp-core" ),
				'info'  => __( "Implement attachments for topics and replies with controls over upload, validation and much more.", "bbp-core" )
			),
			'bbcodes'             => array(
				'icon'  => 'pencil-square',
				'scope' => 'global',
				'title' => __( "BBCodes", "bbp-core" ),
				'info'  => __( "Implement standard and expanded set of BBCodes with additional settings related to some of the BBCodes.", "bbp-core" )
			),
			'post-anonymously'    => array(
				'icon'  => 'user-secret',
				'scope' => 'global',
				'title' => __( "Post Anonymously", "bbp-core" ),
				'info'  => __( "Allow registered users to post anonymously in all or selected forums only.", "bbp-core" )
			),
			'journal-topic'       => array(
				'icon'  => 'pencil-square-o',
				'scope' => 'global',
				'title' => __( "Journal Topic", "bbp-core" ),
				'info'  => __( "Allow registered users to create topic where only they can post replies.", "bbp-core" )
			),
			'rewriter'            => array(
				'icon'  => 'link',
				'scope' => 'global',
				'title' => __( "URL Rewriter", "bbp-core" ),
				'info'  => __( "Enhance the permalinks structure for forum content and tweak some of the aspects of permalinks structure.", "bbp-core" )
			),
			'privacy'             => array(
				'icon'  => 'user-secret',
				'scope' => 'global',
				'title' => __( "Privacy", "bbp-core" ),
				'info'  => __( "Control how the bbPress deals with the user IP address storing and display.", "bbp-core" )
			),
			'mime-types'          => array(
				'icon'  => 'file',
				'scope' => 'global',
				'title' => __( "Extra MIME Types", "bbp-core" ),
				'info'  => __( "Add additional MIME types into WordPress files upload system for use with Attachments.", "bbp-core" )
			),
			'notifications'       => array(
				'icon'  => 'envelope-open',
				'scope' => 'global',
				'title' => __( "Notifications", "bbp-core" ),
				'info'  => __( "Add more email notification types into bbPress.", "bbp-core" )
			),
			'email-sender'        => array(
				'icon'  => 'share',
				'scope' => 'global',
				'title' => __( "Email Sender", "bbp-core" ),
				'info'  => __( "Control Name and Email used for the 'From' field for sending emails related to bbPress forums.", "bbp-core" )
			),
			'email-overrides'     => array(
				'icon'  => 'envelope-o',
				'scope' => 'global',
				'title' => __( "Email Overrides", "bbp-core" ),
				'info'  => __( "Control overrides for all the bbPress and Toolbox notification emails content.", "bbp-core" )
			),
			'objects'             => array(
				'icon'  => 'thumb-tack',
				'scope' => 'global',
				'title' => __( "Content Objects", "bbp-core" ),
				'info'  => __( "Expand forums, topics and replies post type objects with additional features.", "bbp-core" )
			),
			'signatures'          => array(
				'icon'  => 'user',
				'scope' => 'global',
				'title' => __( "Signatures", "bbp-core" ),
				'info'  => __( "Allow users to setup own signatures that will be added to each topic and reply user posts.", "bbp-core" )
			),
			'thanks'              => array(
				'icon'  => 'thumbs-up',
				'scope' => 'global',
				'title' => __( "Say Thanks", "bbp-core" ),
				'info'  => __( "Allow users to say thanks to topic and/or replies authors.", "bbp-core" )
			),
			'report'              => array(
				'icon'  => 'exclamation-triangle',
				'scope' => 'global',
				'title' => __( "Report", "bbp-core" ),
				'info'  => __( "Allow users to report topics or replies for errors or inappropriate content.", "bbp-core" )
			),
			'canned-replies'      => array(
				'icon'  => 'reply',
				'scope' => 'global',
				'title' => __( "Canned Replies", "bbp-core" ),
				'info'  => __( "Allow forum users to insert predefined replies into the reply content.", "bbp-core" )
			),
			'toolbar'             => array(
				'icon'  => 'list-alt',
				'scope' => 'global',
				'title' => __( "Toolbar", "bbp-core" ),
				'info'  => __( "Add new menu into WordPress toolbar with links to forums, views, bbPress related settings and more.", "bbp-core" )
			),
			'private-topics'      => array(
				'icon'  => 'user-circle',
				'scope' => 'global',
				'title' => __( "Private Topics", "bbp-core" ),
				'info'  => __( "Allow users to set topics as private and limit who can read the topic.", "bbp-core" )
			),
			'private-replies'     => array(
				'icon'  => 'user-circle',
				'scope' => 'global',
				'title' => __( "Private Replies", "bbp-core" ),
				'info'  => __( "Allow users to set replies as private and limit who can read the reply.", "bbp-core" )
			),
			'lock-forums'         => array(
				'icon'  => 'lock',
				'scope' => 'global',
				'title' => __( "Lock Forums", "bbp-core" ),
				'info'  => __( "Lock forums and prevent use of new topic or new reply forms with customized messages.", "bbp-core" )
			),
			'lock-topics'         => array(
				'icon'  => 'lock',
				'scope' => 'global',
				'title' => __( "Lock Topics", "bbp-core" ),
				'info'  => __( "Lock individual topics from gettings new replies. Locked topics are not gettings closed.", "bbp-core" )
			),
			'auto-close-topics'   => array(
				'icon'  => 'eye-slash',
				'scope' => 'global',
				'title' => __( "Auto Close Topics", "bbp-core" ),
				'info'  => __( "Automatic close old and inactive topics using daily maintenance job.", "bbp-core" )
			),
			'close-topic-control' => array(
				'icon'  => 'check-square-o',
				'scope' => 'front',
				'title' => __( "Close Topic Control", "bbp-core" ),
				'info'  => __( "Add checkbox to reply form that can be used to close the topic after reply is saved.", "bbp-core" )
			),
			'topics'              => array(
				'icon'  => 'd4p-icon-bbpress-topic',
				'scope' => 'front',
				'title' => __( "Topics", "bbp-core" ),
				'info'  => __( "Various tweaks and features related to the topics.", "bbp-core" )
			),
			'schedule-topic'      => array(
				'icon'  => 'calendar-check-o',
				'scope' => 'front',
				'title' => __( "Schedule Topic", "bbp-core" ),
				'info'  => __( "Allow scheduling topics to be published automatically in the future.", "bbp-core" )
			),
			'replies'             => array(
				'icon'  => 'd4p-icon-bbpress-reply',
				'scope' => 'front',
				'title' => __( "Replies", "bbp-core" ),
				'info'  => __( "Various tweaks and features related to the replies.", "bbp-core" )
			),
			'footer-actions'      => array(
				'icon'  => 'outdent',
				'scope' => 'front',
				'title' => __( "Footer Actions", "bbp-core" ),
				'info'  => __( "Add a footer actions area to topics and replies similar to the actions area in the topics and replies header.", "bbp-core" )
			),
			'seo'                 => array(
				'icon'  => 'search-plus',
				'scope' => 'front',
				'title' => __( "SEO", "bbp-core" ),
				'info'  => __( "Main controls over the Search Engine Optimization - title, description and more.", "bbp-core" )
			),
			'seo-tweaks'          => array(
				'icon'  => 'search',
				'scope' => 'front',
				'title' => __( "SEO Tweaks", "bbp-core" ),
				'info'  => __( "Control over various tweaks related to search engine optimization of the forums.", "bbp-core" )
			),
			'snippets'            => array(
				'icon'  => 'search-plus',
				'scope' => 'front',
				'title' => __( "Rich Snippets", "bbp-core" ),
				'info'  => __( "Add rich snippets used by search engine to enhance the search engine results for the forum.", "bbp-core" )
			),
			'clickable'           => array(
				'icon'  => 'hand-pointer-o',
				'scope' => 'front',
				'title' => __( "Clickable Control", "bbp-core" ),
				'info'  => __( "Control automatic conversion of links and mentions into HTML clickable controls.", "bbp-core" )
			),
			'forum-index'         => array(
				'icon'  => 'd4p-icon-bbpress-forum',
				'scope' => 'front',
				'title' => __( "Forum Index", "bbp-core" ),
				'info'  => __( "Add welcome and statistics blocks to the main forums index page.", "bbp-core" )
			),
			'users-stats'         => array(
				'icon'  => 'user-plus',
				'scope' => 'front',
				'title' => __( "Users Stats", "bbp-core" ),
				'info'  => __( "Add additional user information into author box visible with each topic and reply.", "bbp-core" )
			),
			'quote'               => array(
				'icon'  => 'quote-right',
				'scope' => 'front',
				'title' => __( "Quotes", "bbp-core" ),
				'info'  => __( "Implement the topic and reply quotes using HTML or BBCodes.", "bbp-core" )
			),
			'protect-revisions'   => array(
				'icon'  => 'calendar-o',
				'scope' => 'front',
				'title' => __( "Protect Revisions", "bbp-core" ),
				'info'  => __( "Hide topic and reply revisions from most users, and select which user roles and authors can see revisions.", "bbp-core" )
			),
			'visitors-redirect'   => array(
				'icon'  => 'external-link-square',
				'scope' => 'front',
				'title' => __( "Visitors Redirect", "bbp-core" ),
				'info'  => __( "Prevent non-logged users (visitors or guests) to view some types of the forum pages.", "bbp-core" )
			),
			'profiles'            => array(
				'icon'  => 'user',
				'scope' => 'front',
				'title' => __( "User Profiles", "bbp-core" ),
				'info'  => __( "Control the visibility and hide user profiles to guests and display of some extra information inside the profiles.", "bbp-core" )
			),
			'disable-rss'         => array(
				'icon'  => 'rss',
				'scope' => 'front',
				'title' => __( "Disable RSS Feeds", "bbp-core" ),
				'info'  => __( "Disable bbPress RSS Feeds and redirect RSS requests to parent topics or forums.", "bbp-core" )
			),
			'publish'             => array(
				'icon'  => 'eye',
				'scope' => 'front',
				'title' => __( "Forum Public", "bbp-core" ),
				'info'  => __( "Change the bbPress forums visibility status and make it public or private.", "bbp-core" )
			),
			'admin-access'        => array(
				'icon'  => 'exclamation-circle',
				'scope' => 'admin',
				'title' => __( "Admin Access", "bbp-core" ),
				'info'  => __( "Prevent users by user role from being able to access bbPress panels and pages on the admin side.", "bbp-core" )
			),
			'admin-columns'       => array(
				'icon'  => 'dashboard',
				'scope' => 'admin',
				'title' => __( "Admin Columns", "bbp-core" ),
				'info'  => __( "Add extra information columns to the admin side Forums, Topics, Replies and Users tables.", "bbp-core" )
			),
			'admin-widgets'       => array(
				'icon'  => 'puzzle-piece',
				'scope' => 'admin',
				'title' => __( "Admin Widgets", "bbp-core" ),
				'info'  => __( "Add extra widgets to the WordPress admin side dashboard with forums related information.", "bbp-core" )
			)
		);

		if ( Plugin::instance()->buddypress ) {
			$this->list['buddypress-tweaks'] = array(
				'icon'  => 'd4p-logo-buddypress',
				'scope' => 'front',
				'title' => __( "BuddyPress Tweaks", "bbp-core" ),
				'info'  => __( "Tweaks related to bbPress integration with BuddyPress.", "bbp-core" )
			);

			$this->list['buddypress-notifications'] = array(
				'icon'  => 'd4p-logo-buddypress',
				'scope' => 'global',
				'title' => __( "BuddyPress Notifications", "bbp-core" ),
				'info'  => __( "Use BuddyPress Notifications system for Thanks and Reports features.", "bbp-core" )
			);

			$this->list['buddypress-signature'] = array(
				'icon'  => 'd4p-logo-buddypress',
				'scope' => 'global',
				'title' => __( "Signature for BuddyPress", "bbp-core" ),
				'info'  => __( "Add new XProfile field to BuddyPress for adding user signature editing.", "bbp-core" )
			);
		}
	}

	public function internal( $settings ) {
		$_footer_actions = Plugin::instance()->is_enabled( 'footer-actions' );

		$_ce_topic = gdbbx()->get( 'content-editor__topic', 'features' );
		$_ce_reply = gdbbx()->get( 'content-editor__reply', 'features' );

		$_footer_message = $_footer_actions
			? __( "Footer Actions are enabled.", "bbp-core" )
			:
			sprintf( __( "Footer Actions are disabled, %s.", "bbp-core" ), ' <a href="' . gdbbx_admin()->current_url( true ) . '&gdbbx_handler=getback&action=enable-feature&feature=footer-actions&_wpnonce=' . wp_create_nonce( 'gdbbx-enable-feature-footer-actions' ) . '">' . __( "Click to Enable", "bbp-core" ) . '</a>' );

		$settings['icons'] = array(
			'icons'        => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			),
			'icons_mode'   => array(
				'name'     => __( "Icons Mode", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'icons__mode', __( "For Icons Use", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'icons__mode', 'features' ), 'array', $this->data_attachment_icon_method() )
				)
			),
			'icons_forums' => array(
				'name'     => __( "Forums List Icons", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Forums Visibility", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'icons__forums_mark_visibility_forum', __( "Attachments", "bbp-core" ), __( "Mark forums as 'private' or 'hidden' if they are not public.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forums_mark_visibility_forum', 'features' ), null, array(), array( 'label' => __( "Forum is not public", "bbp-core" ) ) ),
					new d4pSettingElement( '', '', __( "Additional forum statuses", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'icons__forums_mark_closed_forum', __( "Closed", "bbp-core" ), __( "Mark forums that are closed for new topics and replies.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forums_mark_closed_forum', 'features' ), null, array(), array( 'label' => __( "Forum is closed for new posts", "bbp-core" ) ) )
				)
			),
			'icons_topics' => array(
				'name'     => __( "Topics List Icons", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Topics and Replies with Attachments", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'icons__forum_mark_attachments', __( "Attachments", "bbp-core" ), __( "Mark topics that have one or more attachments uploaded to topic and/or replies.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forum_mark_attachments', 'features' ), null, array(), array( 'label' => __( "Topic and replies have one or more attachments", "bbp-core" ) ) ),
					new d4pSettingElement( '', '', __( "Private Topics and Replies", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'icons__private_topics_icon', __( "Private topic", "bbp-core" ), __( "Mark topics that have set as private.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__private_topics_icon', 'features' ), null, array(), array( 'label' => __( "Topic is private", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'icons__private_replies_icon', __( "Private replies", "bbp-core" ), __( "Mark topics that have private replies.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__private_replies_icon', 'features' ), null, array(), array( 'label' => __( "Topic has private replies", "bbp-core" ) ) ),
					new d4pSettingElement( '', '', __( "Temporarily locked topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'icons__forum_mark_lock', __( "Lock", "bbp-core" ), __( "Mark topics that are temporarily locked.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forum_mark_lock', 'features' ), null, array(), array( 'label' => __( "Topic is temporarily locked", "bbp-core" ) ) ),
					new d4pSettingElement( '', '', __( "Additional topic statuses", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'icons__forum_mark_journal', __( "Journal", "bbp-core" ), __( "Mark topics that are created as Journal Topics.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forum_mark_journal', 'features' ), null, array(), array( 'label' => __( "Topic is Journal", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'icons__forum_mark_stick', __( "Sticky", "bbp-core" ), __( "Mark topics that are set as stick or front stick.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forum_mark_stick', 'features' ), null, array(), array( 'label' => __( "Topic is stuck, or stuck to front", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'icons__forum_mark_closed', __( "Closed", "bbp-core" ), __( "Mark topics that are closed for new replies.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forum_mark_closed', 'features' ), null, array(), array( 'label' => __( "Topic is closed for replies", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'icons__forum_mark_replied', __( "Reply", "bbp-core" ), __( "Mark topics where current user replies at least once.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'icons__forum_mark_replied', 'features' ), null, array(), array( 'label' => __( "Logged in user replied in topic", "bbp-core" ) ) )
				)
			)
		);

		$settings['user-settings'] = array(
			'user-settings' => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			)
		);

		$settings['shortcodes'] = array(
			'shortcodes'            => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			),
			'shortcodes_attachment' => array(
				'name'     => __( "Attachment", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'shortcodes__attachment_caption', __( "Caption for images", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'shortcodes__attachment_caption', 'features' ), 'array', $this->data_bbcodes_attachment_caption() ),
					new d4pSettingElement( 'features', 'shortcodes__attachment_video_caption', __( "Caption for videos", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'shortcodes__attachment_video_caption', 'features' ), 'array', $this->data_bbcodes_attachment_caption() ),
					new d4pSettingElement( 'features', 'shortcodes__attachment_audio_caption', __( "Caption for audios", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'shortcodes__attachment_audio_caption', 'features' ), 'array', $this->data_bbcodes_attachment_caption() )
				)
			),
			'shortcodes_quote'      => array(
				'name'     => __( "Quote", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'shortcodes__quote_title', __( "Title", "bbp-core" ), __( "Quoted text can have a title. This option controls what title is displayed.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'shortcodes__quote_title', 'features' ), 'array', $this->data_bbcodes_quote_titles() )
				)
			)
		);

		$settings['content-editor'] = array(
			'content-editor'       => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			),
			'content-editor_topic' => array(
				'name'     => __( "Topic Form", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'content-editor__topic', __( "Editor To Use", "bbp-core" ), '', d4pSettingType::RADIOS, gdbbx()->get( 'content-editor__topic', 'features' ), 'array', $this->data_available_editors(), array( 'wrapper_class' => 'gdbbx-content-editor-topic-selection' ) ),
					new d4pSettingElement( '', '', __( "BBCodes Settings", "bbp-core" ), '', d4pSettingType::HR, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-bbcodes ' . ( $_ce_topic == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', __( "What you need to know", "bbp-core" ), __( "BBCodes Toolbar is added on top of the basic textarea for content editing. This toolbar is not an editor, it just includes convenient way to wrap content in with the desired BBCode.", "bbp-core" ), d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-bbcodes ' . ( $_ce_topic == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', '', __( "BBCodes Feature has to be enabled for this toolbar to work. If BBCodes are disabled, the toolbar will not be visible.", "bbp-core" ), d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-bbcodes ' . ( $_ce_topic == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', '', '<a href="admin.php?page=gd-bbpress-toolbox-features&panel=bbcodes" class="button-primary">' . __( "BBCodes Settings", "bbp-core" ) . '</a>', d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-bbcodes ' . ( $_ce_topic == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__bbcodes_topic_size', __( "Toolbar Icons Size", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'content-editor__bbcodes_topic_size', 'features' ), 'array', $this->data_bbcodes_toolbar_size(), array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-bbcodes ' . ( $_ce_topic == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__bbcodes_topic_editor_fix', __( "Apply editor styling fix", "bbp-core" ), __( "This fix will apply styling changes to textarea editor to fit better with the toolbar.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__bbcodes_topic_editor_fix', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-bbcodes ' . ( $_ce_topic == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', __( "TinyMCE Settings", "bbp-core" ), '', d4pSettingType::HR, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', __( "What you need to know", "bbp-core" ), __( "GD bbPress Toolbox Pro doesn't implement the TinyMCE editor, it only enables it and it can set publicly available options. TinyMCE is implemented by WordPress and options to enable it are hidden in bbPress. GD bbPress Toolbox Pro has no control over the TinyMCE editor. To learn more about it, check out the article linked below.", "bbp-core" ), d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', '', '<a href="https://support.dev4press.com/kb/article/tinymce-configuration-and-limitations/" target="_blank" class="button-primary">' . __( "TinyMCE configuration and limitations", "bbp-core" ) . '</a>', d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_topic_teeny', __( "Compact Editor", "bbp-core" ), __( "This is lightweight version of the editor with only subset of commonly used buttons.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_topic_teeny', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_topic_media_buttons', __( "Media Buttons", "bbp-core" ), __( "Displays the section with the media button and other buttons that third party plugins can add. But, in many cases, these extra buttons can be broken because the editor is loaded on the front end.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_topic_media_buttons', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_topic_quicktags', __( "Quicktags", "bbp-core" ), __( "Displays the Visual and HTML tabs. If it is disabled, editor will be Visual only.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_topic_quicktags', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_topic_textarea_rows', __( "Editor rows", "bbp-core" ), '', d4pSettingType::NUMBER, gdbbx()->get( 'content-editor__tinymce_topic_textarea_rows', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_topic_wpautop', __( "WPAutoP Filter", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_topic_wpautop', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-topic gdbbx-content-editor-topic-tinymce ' . ( $_ce_topic == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) )
				)
			),
			'content-editor_reply' => array(
				'name'     => __( "Reply Form", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'content-editor__reply', __( "Editor To Use", "bbp-core" ), '', d4pSettingType::RADIOS, gdbbx()->get( 'content-editor__reply', 'features' ), 'array', $this->data_available_editors(), array( 'wrapper_class' => 'gdbbx-content-editor-reply-selection' ) ),
					new d4pSettingElement( '', '', __( "BBCodes Settings", "bbp-core" ), '', d4pSettingType::HR, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-bbcodes ' . ( $_ce_reply == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', __( "What you need to know", "bbp-core" ), __( "BBCodes Toolbar is added on top of the basic textarea for content editing. This toolbar is not an editor, it just includes convenient way to wrap content in with the desired BBCode.", "bbp-core" ), d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-bbcodes ' . ( $_ce_reply == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', '', __( "BBCodes Feature has to be enabled for this toolbar to work. If BBCodes are disabled, the toolbar will not be visible.", "bbp-core" ), d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-bbcodes ' . ( $_ce_reply == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', '', '<a href="admin.php?page=gd-bbpress-toolbox-features&panel=bbcodes" class="button-primary">' . __( "BBCodes Settings", "bbp-core" ) . '</a>', d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-bbcodes ' . ( $_ce_reply == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__bbcodes_reply_size', __( "Toolbar Icons Size", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'content-editor__bbcodes_reply_size', 'features' ), 'array', $this->data_bbcodes_toolbar_size(), array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-bbcodes ' . ( $_ce_reply == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__bbcodes_reply_editor_fix', __( "Apply editor styling fix", "bbp-core" ), __( "This fix will apply styling changes to textarea editor to fit better with the toolbar.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__bbcodes_reply_editor_fix', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-bbcodes ' . ( $_ce_reply == 'bbcodes' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', __( "TinyMCE Settings", "bbp-core" ), '', d4pSettingType::HR, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', __( "What you need to know", "bbp-core" ), __( "GD bbPress Toolbox Pro doesn't implement the TinyMCE editor, it only enables it and it can set publicly available options. TinyMCE is implemented by WordPress and options to enable it are hidden in bbPress. GD bbPress Toolbox Pro has no control over the TinyMCE editor. To learn more about it, check out the article linked below.", "bbp-core" ), d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( '', '', '', '<a href="https://support.dev4press.com/kb/article/tinymce-configuration-and-limitations/" target="_blank" class="button-primary">' . __( "TinyMCE configuration and limitations", "bbp-core" ) . '</a>', d4pSettingType::INFO, '', '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_reply_teeny', __( "Compact Editor", "bbp-core" ), __( "This is lightweight version of the editor with only subset of commonly used buttons.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_reply_teeny', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_reply_media_buttons', __( "Media Buttons", "bbp-core" ), __( "Displays the section with the media button and other buttons that third party plugins can add. But, in many cases, these extra buttons can be broken because the editor is loaded on the front end.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_reply_media_buttons', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_reply_quicktags', __( "Quicktags", "bbp-core" ), __( "Displays the Visual and HTML tabs. If it is disabled, editor will be Visual only.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_reply_quicktags', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_reply_textarea_rows', __( "Editor rows", "bbp-core" ), '', d4pSettingType::NUMBER, gdbbx()->get( 'content-editor__tinymce_reply_textarea_rows', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) ),
					new d4pSettingElement( 'features', 'content-editor__tinymce_reply_wpautop', __( "WPAutoP Filter", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'content-editor__tinymce_reply_wpautop', 'features' ), '', '', array( 'wrapper_class' => 'gdbbx-content-editor-reply gdbbx-content-editor-reply-tinymce ' . ( $_ce_reply == 'tinymce' ? 'gdbbx-select-type-show' : '' ) ) )
				)
			)
		);

		$settings['custom-views'] = array(
			'custom-views'            => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			),
			'custom-views-settings'   => array(
				'name'     => __( "Various settings", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'custom-views__enable_feed', __( "RSS Feed", "bbp-core" ), __( "Enable RSS feed option for all views that don't require user to be logged in.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__enable_feed', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__with_pending', __( "Include Pending", "bbp-core" ), __( "Including pending topics in some of the views. This affects New Posts views, and view for Latest Topics, only for the users with moderation capabilities.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__with_pending', 'features' ) )
				)
			),
			'custom-views-basic'      => array(
				'name'     => __( "Basic custom topics views", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Topics with most replies", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__mostreplies_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__mostreplies_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mostreplies_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mostreplies_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mostreplies_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mostreplies_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "Latest topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__latesttopics_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__latesttopics_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__latesttopics_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__latesttopics_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__latesttopics_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__latesttopics_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "Topics by freshness", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__topicsfresh_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__topicsfresh_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__topicsfresh_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__topicsfresh_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__topicsfresh_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__topicsfresh_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "Most thanked topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__mostthanked_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__mostthanked_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mostthanked_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mostthanked_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mostthanked_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mostthanked_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "New posts: Last day", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__newposts24h_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__newposts24h_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts24h_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts24h_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts24h_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts24h_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "New posts: Last 3 days", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__newposts3dy_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__newposts3dy_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts3dy_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts3dy_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts3dy_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts3dy_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "New posts: Last week", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__newposts7dy_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__newposts7dy_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts7dy_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts7dy_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts7dy_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts7dy_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "New posts: Last month", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__newposts1mn_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__newposts1mn_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts1mn_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts1mn_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts1mn_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts1mn_slug', 'features' ) )
				)
			),
			'custom-views-moderation' => array(
				'name'     => __( "Moderation custom topics views", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Pending topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__pending_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__pending_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__pending_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__pending_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__pending_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__pending_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "Spammed topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__spam_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__spam_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__spam_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__spam_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__spam_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__spam_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "Trashed topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__trash_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__trash_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__trash_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__trash_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__trash_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__trash_slug', 'features' ) )
				)
			),
			'custom-views-personal'   => array(
				'name'     => __( "Personal custom topics views", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "New posts since last visit", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__newposts_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__newposts_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__newposts_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__newposts_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "My scheduled topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__myfuture_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__myfuture_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myfuture_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myfuture_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myfuture_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myfuture_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "My active topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__myactive_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__myactive_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myactive_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myactive_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myactive_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myactive_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "All my topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__mytopics_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__mytopics_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mytopics_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mytopics_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mytopics_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mytopics_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "Topics with my reply", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__myreply_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__myreply_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myreply_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myreply_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myreply_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myreply_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "My topics with no replies", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__mynoreplies_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__mynoreplies_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mynoreplies_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mynoreplies_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mynoreplies_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mynoreplies_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "My topics with no replies", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__mymostreplies_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__mymostreplies_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mymostreplies_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mymostreplies_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mymostreplies_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mymostreplies_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "My most thanked topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__mymostthanked_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__mymostthanked_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mymostthanked_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mymostthanked_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mymostthanked_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mymostthanked_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "My favorite topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__myfavorite_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__myfavorite_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myfavorite_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myfavorite_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__myfavorite_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__myfavorite_slug', 'features' ) ),
					new d4pSettingElement( '', '', __( "My subscribed topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'custom-views__mysubscribed_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'custom-views__mysubscribed_active', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mysubscribed_title', __( "Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mysubscribed_title', 'features' ) ),
					new d4pSettingElement( 'features', 'custom-views__mysubscribed_slug', __( "URL Slug", "bbp-core" ), __( "Only letters, numbers and dashes are allowed, it has to be URL safe.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'custom-views__mysubscribed_slug', 'features' ) )
				)
			)
		);

		$settings['tweaks'] = array(
			'tweaks'               => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			),
			'tweaks_status_404'    => array(
				'name'     => __( "Status Header 404", "bbp-core" ),
				'kb'       => array( 'label' => __( "KB", "bbp-core" ), 'url' => 'fixing-404-header-error' ),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__fix_404_headers_error', __( "Fix the 404 Errors", "bbp-core" ), __( "Due to the WordPress query limitations, user profile and views pages in bbPress return with 404 status.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__fix_404_headers_error', 'features' ) )
				)
			),
			'tweaks_media_library' => array(
				'name'     => __( "Allow Participants to use Media Library", "bbp-core" ),
				'kb'       => array(
					'label' => __( "KB", "bbp-core" ),
					'url'   => 'media-library-access-for-participants'
				),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__participant_media_library_upload', __( "Add Media button in TinyMCE", "bbp-core" ), __( "If you use TinyMCE editor, Participants can't use Media Library and Add Media button. By enabling this option, you allow Participants to do this.", "bbp-core" ) . ' ' . __( "Users will have access to own files in the media library, but, they will be able to upload files through media library dialog, and this plugin can't control how this dialog is used.", "bbp-core" ) . ' <strong>' . __( "This operation is not recommended, and you are doing it at your own risk!", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__participant_media_library_upload', 'features' ) )
				)
			),
			'tweaks_editor_tags'   => array(
				'name'     => __( "Expand KSES allowed HTML tags and attributes", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__kses_allowed_override', __( "Allowed tags list", "bbp-core" ), __( "By default, only some HTML tags and attributes are allowed when adding HTML in topics or replies. This option allows you to expand list of supported tags and attributes.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'tweaks__kses_allowed_override', 'features' ), 'array', $this->data_kses_allowed_tags_override() )
				)
			),
			'tweaks_search'        => array(
				'name'     => __( "Search Form", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Notice", "bbp-core" ), __( "The search is global, it is not limited to current forum or topic! In some cases, you might need to adjust theme styling for proper display of the form and surrounding elements.", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'tweaks__forum_load_search_for_all_forums', __( "Search for all forums", "bbp-core" ), __( "Default bbPress search form will be displayed on top of all forums.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__forum_load_search_for_all_forums', 'features' ) ),
					new d4pSettingElement( 'features', 'tweaks__topic_load_search_for_all_topics', __( "Search for all topics", "bbp-core" ), __( "Default bbPress search form will be displayed on top of all topics.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__topic_load_search_for_all_topics', 'features' ) )
				)
			),
			'tweaks_title_length'  => array(
				'name'     => __( "HTML Maximum Title Length", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__title_length_override', __( "Custom Length", "bbp-core" ), __( "This value is set for title HTML tag through default bbPress filter.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__title_length_override', 'features' ) ),
					new d4pSettingElement( 'features', 'tweaks__title_length_value', __( "Maximum Length Allowed", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'tweaks__title_length_value', 'features' ) )
				)
			),
			'tweaks_title_prefix'  => array(
				'name'     => __( "Private Title Prefix", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__remove_private_title_prefix', __( "The prefix", "bbp-core" ), __( "WordPress adds 'Private' prefix to private forums or topic titles. With this option, you can remove this prefix.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__remove_private_title_prefix', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) )
				)
			),
			'tweaks_breadcrumbs'   => array(
				'name'     => __( "bbPress Breadcrumbs", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__disable_bbpress_breadcrumbs', __( "Disable Breadcrumbs", "bbp-core" ), __( "This option will disable default bbPress breadcrumbs.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__disable_bbpress_breadcrumbs', 'features' ), null, array(), array( 'label' => __( "Disable", "bbp-core" ) ) )
				)
			),
			'tweaks_freshness'     => array(
				'name'     => __( "Freshness Display", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__alternative_freshness_display', __( "Alternative Format", "bbp-core" ), __( "Display freshness using alternative and shorter format. This tweak affects both admin side and frontend.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__alternative_freshness_display', 'features' ) )
				)
			),
			'tweaks_user_roles'    => array(
				'name'     => __( "User Roles Display", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'tweaks__hide_user_roles_from_users', __( "Hide from user profile links", "bbp-core" ), __( "In few areas, bbPress show user forum role along with the user profile link and profile avatar. If you enable this option, the user role will be hidden from all regular users, and only keymasters and moderators will see the role.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'tweaks__hide_user_roles_from_users', 'features' ) )
				)
			)
		);

		$settings['topic-actions'] = array(
			'topic-actions'      => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			),
			'topic-actions_list' => array(
				'name'     => __( "Actions Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Information", "bbp-core" ), __( "If the 'Footer Actions' feature is disabled, all actions set to be added to Footer, will be added to standard Header actions block.", "bbp-core" ) . '<br/><strong>' . $_footer_message . '</strong>', d4pSettingType::INFO ),
					new d4pSettingElement( '', '', __( "Public", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'topic-actions__reply', __( "Reply", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__reply', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( '', '', __( "Keymasters and Moderators", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'topic-actions__edit', __( "Edit", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__edit', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__merge', __( "Merge", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__merge', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__close', __( "Close", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__close', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__stick', __( "Stick", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__stick', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__trash', __( "Trash", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__trash', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__spam', __( "Spam", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__spam', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__approve', __( "Approve", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__approve', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( '', '', __( "Toolbox Features", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'topic-actions__duplicate', __( "Duplicate", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__duplicate', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__lock', __( "Lock", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__lock', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__thanks', __( "Thanks", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__thanks', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__report', __( "Report", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__report', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'topic-actions__quote', __( "Quote", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'topic-actions__quote', 'features' ), 'array', $this->data_actions_location() )
				)
			)
		);

		$settings['reply-actions'] = array(
			'reply-actions'      => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Active", "bbp-core" ), __( "This feature is always active, and it can't be disabled. You can enable or disable individual settings included.", "bbp-core" ), d4pSettingType::INFO )
				)
			),
			'reply-actions_list' => array(
				'name'     => __( "Actions Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Information", "bbp-core" ), __( "If the 'Footer Actions' feature is disabled, all actions set to be added to Footer, will be added to standard Header actions block.", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', __( "Public", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'reply-actions__reply', __( "Reply", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__reply', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( '', '', __( "Keymasters and Moderators", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'reply-actions__edit', __( "Edit", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__edit', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'reply-actions__move', __( "Move", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__move', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'reply-actions__split', __( "Split", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__split', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'reply-actions__trash', __( "Trash", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__trash', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'reply-actions__spam', __( "Spam", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__spam', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'reply-actions__approve', __( "Approve", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__approve', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( '', '', __( "Toolbox Features", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'reply-actions__thanks', __( "Thanks", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__thanks', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'reply-actions__report', __( "Report", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__report', 'features' ), 'array', $this->data_actions_location() ),
					new d4pSettingElement( 'features', 'reply-actions__quote', __( "Quote", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'reply-actions__quote', 'features' ), 'array', $this->data_actions_location() )
				)
			)
		);

		$settings['bbcodes'] = array(
			'bbcodes'           => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'bbcodes', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'bbcodes', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'bbcodes_basic'     => array(
				'name'     => __( "Main BBCodes Settings", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'bbcodes__notice', __( "Form Notice", "bbp-core" ), __( "If the BBCodes support is active, you can display notice in the new topic/reply form.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'bbcodes__notice', 'features' ) ),
					new d4pSettingElement( 'features', 'bbcodes__bbpress_only', __( "bbPress Only", "bbp-core" ), __( "Processing of the BBCodes can be limited only to bbPress implemented forums, topics and replies.", "bbp-core" ) . ' <strong>' . __( "If you are using BuddyPress Nuovo templates, BBCode for Italic can cause the problem with some elements of the BuddyPress using underscore templates to render. To avoid that, you need to enable this option, or disable Italic BBCode.", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'bbcodes__bbpress_only', 'features' ) )
				)
			),
			'bbcodes_limit'     => array(
				'name'     => __( "Limit BBCodes Use", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Information", "bbp-core" ), __( "BBCodes use can be limited for different user roles, and you can also limit use the BBCodes in the BBCodes Toolbar. Individual BBCodes control is done on the dedicated panel.", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', '', '<a href="admin.php?page=gd-bbpress-toolbox-bbcodes" class="button-primary">' . __( "BBCodes Control Panel", "bbp-core" ) . '</a>', d4pSettingType::INFO ),
					new d4pSettingElement( 'features', 'bbcodes__restricted', __( "Restriction action", "bbp-core" ), __( "If user uses some shortcodes that are not allowed for the user role, you can choose what will happen with such content. Changes will be made on saving the content, and if the user entered content with BBCode not allowed for the user role, that will be removed or replaced.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'bbcodes__restricted', 'features' ), 'array', $this->data_bbcodes_replacement() )
				)
			),
			'bbcodes_scode'     => array(
				'name'     => __( "Source Code", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'bbcodes__scode_enlighter', __( "Enlighter Theme", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'bbcodes__scode_enlighter', 'features' ), 'array', $this->data_bbcodes_enlighter_theme() )
				)
			),
			'bbcodes_hide'      => array(
				'name'     => __( "Hide", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'bbcodes__hide_title', __( "Title", "bbp-core" ), '', d4pSettingType::HTML, gdbbx()->get( 'bbcodes__hide_title', 'features' ) ),
					new d4pSettingElement( '', '', __( "Show when hidden", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'bbcodes__hide_content_normal', __( "Content: Normal", "bbp-core" ), __( "When HIDE is set with no value, user must be logged in to see hidden content.", "bbp-core" ), d4pSettingType::HTML, gdbbx()->get( 'bbcodes__hide_content_normal', 'features' ) ),
					new d4pSettingElement( 'features', 'bbcodes__hide_content_count', __( "Content: Counts", "bbp-core" ), __( "When HIDE is set to integer value, user must be logged in and have at least the amount of posts in the forum as specified to see the content.", "bbp-core" ) . ' ' . __( "This condition can be checked only for logged-in users, anonymous posts can't be taken into account.", "bbp-core" ), d4pSettingType::HTML, gdbbx()->get( 'bbcodes__hide_content_count', 'features' ) ),
					new d4pSettingElement( 'features', 'bbcodes__hide_content_reply', __( "Content: Reply", "bbp-core" ), __( "When HIDE is set to 'reply', user must reply to the topic to see the content.", "bbp-core" ) . ' ' . __( "This condition can be checked only for logged-in users, anonymous posts can't be taken into account.", "bbp-core" ) . ' ' . __( "For non-logged in visitors, a Normal message will be displayed.", "bbp-core" ), d4pSettingType::HTML, gdbbx()->get( 'bbcodes__hide_content_reply', 'features' ) ),
					new d4pSettingElement( 'features', 'bbcodes__hide_content_thanks', __( "Content: Say Thanks", "bbp-core" ), __( "When HIDE is set to 'thanks', user must say thanks to the topic author to see the content.", "bbp-core" ) . ' ' . __( "For non-logged in visitors, normal message will be displayed.", "bbp-core" ), d4pSettingType::HTML, gdbbx()->get( 'bbcodes__hide_content_thanks', 'features' ) ),
					new d4pSettingElement( '', '', __( "Exceptions", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'bbcodes__hide_keymaster_always_allowed', __( "Keymaster always allowed", "bbp-core" ), __( "If enabled, keymaster will be able to always see hidden content.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'bbcodes__hide_keymaster_always_allowed', 'features' ) )
				)
			),
			'bbcodes_spoiler'   => array(
				'name'     => __( "Spoiler", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'bbcodes__spoiler_color', __( "Main Color", "bbp-core" ), '', d4pSettingType::COLOR, gdbbx()->get( 'bbcodes__spoiler_color', 'features' ) ),
					new d4pSettingElement( 'features', 'bbcodes__spoiler_hover', __( "Hover Background Color", "bbp-core" ), '', d4pSettingType::COLOR, gdbbx()->get( 'bbcodes__spoiler_hover', 'features' ) )
				)
			),
			'bbcodes_highlight' => array(
				'name'     => __( "Highlight", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'bbcodes__highlight_color', __( "Text Color", "bbp-core" ), '', d4pSettingType::COLOR, gdbbx()->get( 'bbcodes__highlight_color', 'features' ) ),
					new d4pSettingElement( 'features', 'bbcodes__highlight_background', __( "Background Color", "bbp-core" ), '', d4pSettingType::COLOR, gdbbx()->get( 'bbcodes__highlight_background', 'features' ) )
				)
			),
			'bbcodes_heading'   => array(
				'name'     => __( "Heading", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'bbcodes__heading_size', __( "Default Size", "bbp-core" ), __( "Heading will render H{size} tag.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'bbcodes__heading_size', 'features' ), 'array', $this->data_bbcodes_heading() )
				)
			)
		);

		$settings['rewriter'] = array(
			'rewriter'           => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'rewriter', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'rewriter_hierarchy' => array(
				'name'     => __( "Alternative URL hierarchy", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Notice", "bbp-core" ), __( "Changing rewrite rules for topics and replies might not work properly if you have some other plugins that are customizing rewrite rules.", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'rewriter__topic_hierarchy', __( "For Topics", "bbp-core" ), __( "URL's for topics will include full forums hierarchy.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__topic_hierarchy', 'features' ) ),
					new d4pSettingElement( 'features', 'rewriter__reply_hierarchy', __( "For Replies", "bbp-core" ), __( "URL's for replies will include full forums and parent topic hierarchy. This is used only for reply URL's showing standalone reply or reply edit page.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__reply_hierarchy', 'features' ) )
				)
			),
			'rewriter_cleanup'   => array(
				'name'     => __( "Remove rewrite rules", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Notice", "bbp-core" ), __( "These options will remove rewrite rules generated by WordPress. If you are not sure what will happen, don't use these options!", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', __( "Forums", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'rewriter__forum_remove_attachments_rules', __( "Attachments rules", "bbp-core" ), __( "This will remove attachments rules used to display individual media files attached to the post.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__forum_remove_attachments_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'rewriter__forum_remove_comments_rules', __( "Comments rules", "bbp-core" ), __( "This will remove comments page rules used for displaying comments for a post. bbPress content doesn't use comments, and these rules are useless.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__forum_remove_comments_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'rewriter__forum_remove_feeds_rules', __( "RSS Feed rules", "bbp-core" ), __( "This will remove RSS feed URL's. If you don't use RSS feeds, you can disable these rules.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__forum_remove_feeds_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( '', '', __( "Topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'rewriter__topic_remove_attachments_rules', __( "Attachments rules", "bbp-core" ), __( "This will remove attachments rules used to display individual media files attached to the post.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__topic_remove_attachments_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'rewriter__topic_remove_comments_rules', __( "Comments rules", "bbp-core" ), __( "This will remove comments page rules used for displaying comments for a post. bbPress content doesn't use comments, and these rules are useless.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__topic_remove_comments_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'rewriter__topic_remove_feeds_rules', __( "RSS Feed rules", "bbp-core" ), __( "This will remove RSS feed URL's. If you don't use RSS feeds, you can disable these rules.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__topic_remove_feeds_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( '', '', __( "Replies", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'rewriter__reply_remove_attachments_rules', __( "Attachments rules", "bbp-core" ), __( "This will remove attachments rules used to display individual media files attached to the post.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__reply_remove_attachments_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'rewriter__reply_remove_comments_rules', __( "Comments rules", "bbp-core" ), __( "This will remove comments page rules used for displaying comments for a post. bbPress content doesn't use comments, and these rules are useless.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__reply_remove_comments_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'rewriter__reply_remove_feeds_rules', __( "RSS Feed rules", "bbp-core" ), __( "This will remove RSS feed URL's. If you don't use RSS feeds, you can disable these rules.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'rewriter__reply_remove_feeds_rules', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) )
				)
			)
		);

		$settings['attachments'] = array(
			'attachments'             => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'attachments', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'attachments_activation'  => array(
				'name'     => __( "Activation", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Attachments Form", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__method', __( "Interface", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__method', 'features' ), 'array', $this->data_attachments_method() ),
					new d4pSettingElement( '', '', __( "Content Integration", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__topics', __( "For Topics", "bbp-core" ), __( "Add attachments upload to topic form.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__topics', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__replies', __( "For Replies", "bbp-core" ), __( "Add attachments upload to reply form.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__replies', 'features' ) ),
					new d4pSettingElement( '', '', __( "Attachments Access", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__roles_to_upload', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'attachments__roles_to_upload', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'attachments_integration' => array(
				'name'     => __( "Integration", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Forms", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__form_position_topic', __( "Topic Form", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__form_position_topic', 'features' ), 'array', $this->data_form_position_topic() ),
					new d4pSettingElement( 'features', 'attachments__form_position_reply', __( "Reply Form", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__form_position_reply', 'features' ), 'array', $this->data_form_position_reply() ),
					new d4pSettingElement( '', '', __( "Files List", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__files_list_position', __( "Embed Position", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__files_list_position', 'features' ), 'array', $this->data_files_position_topic() ),
					new d4pSettingElement( 'features', 'attachments__files_list_mode', __( "Display Mode", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__files_list_mode', 'features' ), 'array', $this->data_files_display_mode() ),
					new d4pSettingElement( 'features', 'attachments__image_thumbnail_columns', __( "Thumbnails Mode Columns", "bbp-core" ), '', d4pSettingType::ABSINT, gdbbx()->get( 'attachments__image_thumbnail_columns', 'features' ), null, array(), array(
						'min' => 2,
						'max' => 8
					) ),
					new d4pSettingElement( 'features', 'attachments__files_list_roles', __( "Visible for user roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'attachments__files_list_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', __( "Files List for Visitors", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__hide_from_visitors', __( "Hide attachments list", "bbp-core" ), __( "If enabled, only logged in users will be able to see attachments list.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__hide_from_visitors', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__preview_for_visitors', __( "Show previews only", "bbp-core" ), __( "If enabled, attachments list will be visible. But, only file names and image thumbnails will be visible, no links will be included", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__preview_for_visitors', 'features' ) ),
					new d4pSettingElement( '', '', __( "Files List file attributes", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__file_target_blank', __( "Target set to blank page", "bbp-core" ), __( "All displayed attachments links will lead to open blank page to display attachment (for images or documents).", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__file_target_blank', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__download_link_attribute', __( "Download attribute", "bbp-core" ), __( "Each link will have download attribute set, and for supported browser will force click on the link to download the file.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__download_link_attribute', 'features' ) ),
					new d4pSettingElement( '', '', __( "Files List Tweaks", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__hide_attachments_when_in_content', __( "Hide files inserted into content", "bbp-core" ), __( "If the attachment is inserted into content using [attachment] BBCode, it will be hidden from the list of attachments below the post.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__hide_attachments_when_in_content', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__file_skip_missing', __( "Skip files that are missing", "bbp-core" ), __( "It can happen that the file gets deleted from the storage, but it is still listed in the database. If that is the case, with this option, such file will not be displayed.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__file_skip_missing', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__attachment_icons', __( "Include file Type Icons", "bbp-core" ), __( "When attachments are displayed as a list, show icons in front of the file link for easier identification of the file type.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__attachment_icons', 'features' ) )
				)
			),
			'attachments_images'      => array(
				'name'     => __( "Images as Thumbnails", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Display control", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__image_thumbnail_size', __( "Thumbnails Size", "bbp-core" ), __( "Changing thumbnails size affects only new image attachments. To use new size for old attachments, resize them using Regenerate Thumbnails plugin.", "bbp-core" ), d4pSettingType::X_BY_Y, gdbbx()->get( 'attachments__image_thumbnail_size', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__image_thumbnail_caption', __( "With caption", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__image_thumbnail_caption', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__image_thumbnail_inline', __( "Inline", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__image_thumbnail_inline', 'features' ) ),
					new d4pSettingElement( '', '', __( "Thumbnail attributes", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__image_thumbnail_css', __( "CSS class", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'attachments__image_thumbnail_css', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__image_thumbnail_rel', __( "REL attribute", "bbp-core" ), __( "You can use these tags", "bbp-core" ) . ' %ID%, %TOPIC%', d4pSettingType::TEXT, gdbbx()->get( 'attachments__image_thumbnail_rel', 'features' ) ),
					new d4pSettingElement( '', '', __( "Auto-generate featured image", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__topic_featured_image', __( "For Topic", "bbp-core" ), __( "First image uploaded to topic, will be set as featured (if topic has no featured image set already).", "bbp-core" ) . ' <strong>' . __( "For this to work, theme must have Post Thumbnails support.", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__topic_featured_image', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__reply_featured_image', __( "For Reply", "bbp-core" ), __( "First image uploaded to reply, will be set as featured (if reply has no featured image set already).", "bbp-core" ) . ' <strong>' . __( "For this to work, theme must have Post Thumbnails support.", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__reply_featured_image', 'features' ) )
				)
			),
			'attachments_limits'      => array(
				'name'     => __( "Upload Limits", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Default Limits", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__max_file_size', __( "Maximum file size", "bbp-core" ), __( "Size set in KB.", "bbp-core" ) . ' ' . sprintf( __( "Current server configuration allows maximum file size of <strong>%s KB</strong>.", "bbp-core" ), Helper::instance()->max_server_allowed() ), d4pSettingType::ABSINT, gdbbx()->get( 'attachments__max_file_size', 'features' ), '', '', array(
						'max' => Helper::instance()->max_server_allowed(),
						'min' => 1
					) ),
					new d4pSettingElement( 'features', 'attachments__max_to_upload', __( "Maximum files to upload", "bbp-core" ), __( "This is the number of files allowed to upload at once, it doesn't limit total number of files per topic or reply.", "bbp-core" ), d4pSettingType::NUMBER, gdbbx()->get( 'attachments__max_to_upload', 'features' ) ),
					new d4pSettingElement( '', '', __( "MIME Types", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__mime_types_limit_active', __( "Filter by MIME Type", "bbp-core" ), __( "If this option is active, only MIME Types selected below will be allowed to upload.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__mime_types_limit_active', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__mime_types_list', __( "Allowed MIME Types", "bbp-core" ), __( "List shows extensions allowed by WordPress, and if you hover over the names of the extensions you will see which MIME type they belong to.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'attachments__mime_types_list', 'features' ), 'array', BB::i()->get_mime_types_list(), array( 'class' => 'gdbbx-bbcodes' ) ),
					new d4pSettingElement( '', '', __( "Upload with No Limits", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__roles_no_limit', __( "Available to roles", "bbp-core" ), __( "Users with these roles will be able to upload files regardless of the limits. These users will be able to upload files of any size and any number of files and any file type allowed by the system.", "bbp-core" ) . ' ' . sprintf( __( "Current server configuration allows maximum file size of <strong>%s KB</strong>.", "bbp-core" ), Helper::instance()->max_server_allowed() ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'attachments__roles_no_limit', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'attachments_enhanced'    => array(
				'name'     => __( "Enhanced Interface", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Button to Insert into content", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__insert_into_content', __( "Include Insert button", "bbp-core" ), __( "If enabled, the plugin will show 'Insert into content' button for each attachment.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__insert_into_content', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__insert_into_content_roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'attachments__insert_into_content_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', __( "Various enhancements", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__enhanced_set_caption', __( "Set caption for file", "bbp-core" ), __( "If the file has some generic name, users can set the textual caption to be used instead of the real file name.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__enhanced_set_caption', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__enhanced_auto_new', __( "Auto-add new file", "bbp-core" ), __( "Hide button to add another file, and instead attaching a file, automatically adds new file control.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__enhanced_auto_new', 'features' ) )
				)
			),
			'attachments_features'    => array(
				'name'     => __( "Features", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Errors Logging", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__log_upload_errors', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__log_upload_errors', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__errors_visible_to_admins', __( "Visible to administrators", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__errors_visible_to_admins', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__errors_visible_to_moderators', __( "Visible to moderators", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__errors_visible_to_moderators', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__errors_visible_to_author', __( "Visible to author", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__errors_visible_to_author', 'features' ) ),
					new d4pSettingElement( '', '', __( "Bulk Download", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__bulk_download', __( "Bulk Download Link", "bbp-core" ), __( "If the topic or reply has more then one attachment, you will be able to use the Link 'Download all attachments'.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__bulk_download', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__bulk_download_listed', __( "Only when listing attachments", "bbp-core" ), __( "If enabled, Bulk Download link will be displayed only if the Attachments area is visible. If you enabled option to hide attachments that are added to the content, Attachments area might be hidden if no files are to be listed.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__bulk_download_listed', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__bulk_download_visitor', __( "Available to visitors", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__bulk_download_visitor', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__bulk_download_roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'attachments__bulk_download_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', __( "Upload Directory", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( '', '', __( "Important", "bbp-core" ), __( "Read all the provided information first. Do not enable this option unless you are sure what it will do exactly.", "bbp-core" ) . $this->info_upload_dir(), d4pSettingType::INFO ),
					new d4pSettingElement( 'features', 'attachments__upload_dir_override', __( "Override upload directory", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__upload_dir_override', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__upload_dir_forums_base', __( "Base directory name", "bbp-core" ), __( "This will be used to format the directory name selected below. This has to be file system safe, slug string using alphanumeric characters only including dashes, with no spaces or special characters.", "bbp-core" ), d4pSettingType::SLUG, gdbbx()->get( 'attachments__upload_dir_forums_base', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__upload_dir_structure', __( "Directory structure", "bbp-core" ), __( "Selected format will be replaced with these values before upload.", "bbp-core" ) . $this->info_upload_dir_format(), d4pSettingType::SELECT, gdbbx()->get( 'attachments__upload_dir_structure', 'features' ), 'array', $this->data_upload_dir_format() ),
					new d4pSettingElement( '', '', __( "Topic Thread Attachments Overview", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__topic_thread_list', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__topic_thread_list', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__topic_thread_list_action', __( "Location", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__topic_thread_list_action', 'features' ), 'array', $this->data_forum_thread_attachments_actions() ),
					new d4pSettingElement( 'features', 'attachments__topic_thread_list_format', __( "Display format", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__topic_thread_list_format', 'features' ), 'array', $this->data_forum_thread_attachments_format() ),
					new d4pSettingElement( 'features', 'attachments__topic_thread_list_items', __( "Files per page", "bbp-core" ), '', d4pSettingType::ABSINT, gdbbx()->get( 'attachments__topic_thread_list_items', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__topic_thread_list_columns', __( "Thumbnails Columns", "bbp-core" ), '', d4pSettingType::ABSINT, gdbbx()->get( 'attachments__topic_thread_list_columns', 'features' ), null, array(), array(
						'min' => 2,
						'max' => 8
					) ),
					new d4pSettingElement( 'features', 'attachments__topic_thread_list_roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'attachments__topic_thread_list_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'attachments_deletion'    => array(
				'name'     => __( "Deletion of Attachments", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Basic Settings", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__delete_method', __( "Method", "bbp-core" ), __( "This will control how the options to delete attachments are presented.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'attachments__delete_method', 'features' ), 'array', $this->data_attachment_delete_method() ),
					new d4pSettingElement( '', '', __( "Deletion of Topics and Replies", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__delete_attachments', __( "Action", "bbp-core" ), __( "This control what happens to attachments once the topic or reply with attachments is deleted.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'attachments__delete_attachments', 'features' ), 'array', $this->data_attachment_topic_delete() ),
					new d4pSettingElement( '', '', __( "Deletion of Attachments", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__delete_visible_to_admins', __( "Administrators", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__delete_visible_to_admins', 'features' ), 'array', $this->data_attachment_file_delete() ),
					new d4pSettingElement( 'features', 'attachments__delete_visible_to_moderators', __( "Moderators", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__delete_visible_to_moderators', 'features' ), 'array', $this->data_attachment_file_delete() ),
					new d4pSettingElement( 'features', 'attachments__delete_visible_to_author', __( "Author", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'attachments__delete_visible_to_author', 'features' ), 'array', $this->data_attachment_file_delete() )
				)
			),
			'attachments_advanced'    => array(
				'name'     => __( "Advanced", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Media Library", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__attachments_from_media_library', __( "Hide files from Media Library", "bbp-core" ), __( "Since all attachments are added to WordPress Media Library, they will show up in the media selection popups. If you want to avoid that, and hide attachments from showing in Media Library, you can enable this option - attachments will still use Media Library, but you will not see them when browsing media library.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__attachments_from_media_library', 'features' ) ),
					new d4pSettingElement( '', '', __( "Attachments Form", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__show_form_notices', __( "Display form notices", "bbp-core" ), __( "Including information about allowed file sizes, types and other important messages related to upload.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__show_form_notices', 'features' ) ),
					new d4pSettingElement( 'features', 'attachments__mime_types_limit_display', __( "Display allowed types", "bbp-core" ), __( "If active, plugin will show list of allowed types in the upload form as notice.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'attachments__mime_types_limit_display', 'features' ) ),
					new d4pSettingElement( '', '', __( "Topic form integration", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'attachments__forum_not_defined', __( "Forum not defined", "bbp-core" ), __( "If the forum ID for the topic form can't be detected (the form is not part of the forum), the attachment form is not available by default. You can change that here, but be careful - if you use forum based attachments control, users may be able to upload files in the forum that you have disabled for attachments use.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'attachments__forum_not_defined', 'features' ), 'array', $this->data_forum_not_defined() )
				)
			)
		);

		$settings['journal-topic'] = array(
			'journal-topic'             => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'journal-topic', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'journal-topic', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'journal-topic_allowed'     => array(
				'name'     => __( "Users and Forums", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'journal-topic__allowed_roles', __( "Available to roles", "bbp-core" ), __( "Only users with selected roles will be able to create journal topics.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'journal-topic__allowed_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'journal-topic__allowed_in_forums', __( "Available in forums", "bbp-core" ), __( "If no forums are selected, option will be available in all forums.", "bbp-core" ), d4pSettingType::CHECKBOXES_HIERARCHY, gdbbx()->get( 'journal-topic__allowed_in_forums', 'features' ), 'array', $this->data_bbpress_forums_list(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', __( "Keymasters and Moderators", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'journal-topic__allowed_for_moderators', __( "Allow to reply", "bbp-core" ), __( "If enabled, moderators and keymasters will be able to reply to Journal topics they have not started.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'journal-topic__allowed_for_moderators', 'features' ) ),
					new d4pSettingElement( 'features', 'journal-topic__edit_for_moderators', __( "Allow to edit", "bbp-core" ), __( "If enabled, moderators and keymasters will be able to edit the topic and replies in the Journal topics. If this option is disabled, frontend editing for Journal posts will be completely disabled.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'journal-topic__edit_for_moderators', 'features' ) )
				)
			),
			'journal-topic_integration' => array(
				'name'     => __( "Integration", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'journal-topic__topic_form_position', __( "Topic Form Position", "bbp-core" ), __( "Choose where the Journal Topic checkbox is displayed for new topic form.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'journal-topic__topic_form_position', 'features' ), 'array', $this->data_form_position_topic() )
				)
			)
		);

		$settings['post-anonymously'] = array(
			'post-anonymously'             => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'post-anonymously', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'post-anonymously', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'post-anonymously_allowed'     => array(
				'name'     => __( "Users and Forums", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'post-anonymously__allowed_roles', __( "Available to roles", "bbp-core" ), __( "Only users with selected roles will be able to see the option for anonymous posting.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'post-anonymously__allowed_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'post-anonymously__allowed_in_forums', __( "Available in forums", "bbp-core" ), __( "If no forums are selected, option will be available in all forums.", "bbp-core" ), d4pSettingType::CHECKBOXES_HIERARCHY, gdbbx()->get( 'post-anonymously__allowed_in_forums', 'features' ), 'array', $this->data_bbpress_forums_list(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'post-anonymously_integration' => array(
				'name'     => __( "Integration", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'post-anonymously__topic_form_position', __( "Topic Form Position", "bbp-core" ), __( "Choose where the Post Anonymously checkbox is displayed for new topic form.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'post-anonymously__topic_form_position', 'features' ), 'array', $this->data_form_position_topic() ),
					new d4pSettingElement( 'features', 'post-anonymously__reply_form_position', __( "Reply Form Position", "bbp-core" ), __( "Choose where the Post Anonymously checkbox is displayed for new reply form.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'post-anonymously__reply_form_position', 'features' ), 'array', $this->data_form_position_reply() ),
				)
			),
			'post-anonymously_user'        => array(
				'name'     => __( "Anonymous Account", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'post-anonymously__anonymous_name', __( "Name for the posting", "bbp-core" ), __( "Anonymous account will be used (and bbPress has to be configured to allow anonymous posting), and it requires name to use for display purposes. This can contain {{HASH}} tag that will be replaced with partial auto generated MD5 hash.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'post-anonymously__anonymous_name', 'features' ) ),
					new d4pSettingElement( 'features', 'post-anonymously__anonymous_email', __( "Email for the posting", "bbp-core" ), __( "Email can be anything that looks real, but it is fake. Again, {{HASH}} can be used to replace to make email unique.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'post-anonymously__anonymous_email', 'features' ) ),
					new d4pSettingElement( 'features', 'post-anonymously__anonymous_hash', __( "HASH source", "bbp-core" ), __( "One or more selected items from this list will be used to generate hash. If the hash is forum ID dependent, and you move the topic to different forum, next hash for same user will be different from previous for that topic.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'post-anonymously__anonymous_hash', 'features' ), 'array', $this->data_anon_hash_source() )
				)
			),
			'post-anonymously_store'       => array(
				'name'     => __( "Store Anonymous to Real account link", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'post-anonymously__original_author_store_method', __( "Link Anonymous to Real account", "bbp-core" ), __( "If the link is stored, selected users can see the identity of anonymous accounts. If no link is stored, or if the link is deleted after amount of days specified below, no one can link real and anonymous accounts. Changing this option or option for days limit will affect existing links that have been saved.", "bbp-core" ), d4pSettingType::RADIOS, gdbbx()->get( 'post-anonymously__original_author_store_method', 'features' ), 'array', $this->data_anon_store_link() ),
					new d4pSettingElement( 'features', 'post-anonymously__original_author_store_days', __( "Limit link lifetime", "bbp-core" ), __( "If the link is set to 'Limited' value, this is number of days for the limit.", "bbp-core" ), d4pSettingType::ABSINT, gdbbx()->get( 'post-anonymously__original_author_store_days', 'features' ), null, array(), array(
						'label_unit' => __( "days", "bbp-core" ),
						"min"        => 1
					) ),
					new d4pSettingElement( 'features', 'post-anonymously__original_author_store_roles', __( "Visible to roles", "bbp-core" ), __( "If the link is stored, only users with selected roles will be able to see the link.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'post-anonymously__original_author_store_roles', 'features' ), 'array', $this->data_high_level_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'post-anonymously_forced'      => array(
				'name'     => __( "Forced Anonymous posting in selected forums", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'post-anonymously__forced_in_forums', __( "Forced in forums", "bbp-core" ), __( "Select forums where all the posts will be made as anonymous. If no forums are selected, forced posting will be disabled.", "bbp-core" ), d4pSettingType::CHECKBOXES_HIERARCHY, gdbbx()->get( 'post-anonymously__forced_in_forums', 'features' ), 'array', $this->data_bbpress_forums_list(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'post-anonymously__forced_exception_roles', __( "Exception roles", "bbp-core" ), __( "Users with selected roles will be able to choose if they want to post anonymously in the forced forums.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'post-anonymously__forced_exception_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			)
		);

		$settings['snippets'] = array(
			'snippets'             => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'snippets', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'snippets', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'snippets_breadcrumbs' => array(
				'name'     => __( "Breadcrumbs", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'snippets__breadcrumbs', __( "Add JSON-LD snippet", "bbp-core" ), __( "This option will modify bbPress generated breadcrumbs to make them Google Rich Snippet compatible. This will work only if you have not modified bbPress breadcrumbs in some other way.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'snippets__breadcrumbs', 'features' ) )
				)
			),
			'snippets_topic_dfp'   => array(
				'name'     => __( "Discussion Forum Posting", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'snippets__topic_dfp', __( "Add JSON-LD snippet", "bbp-core" ), sprintf( __( "This option add %s snippet to every topic.", "bbp-core" ), 'DiscussionForumPosting' ), d4pSettingType::BOOLEAN, gdbbx()->get( 'snippets__topic_dfp', 'features' ) ),
					new d4pSettingElement( '', '', __( "Fallback Image", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'snippets__topic_dfp_fallback_image', __( "Fallback Image", "bbp-core" ), __( "Each snippet has to have image. If your topic doesn't have featured image, you need to specify the fallback image to be used for all topics without featured image. If you use Attachments upload, you can enable option to automatically make first attached image a featured image for the topic.", "bbp-core" ), d4pSettingType::IMAGE, gdbbx()->get( 'snippets__topic_dfp_fallback_image', 'features' ) ),
					new d4pSettingElement( '', '', __( "Optional Elements", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'snippets__topic_dfp_include_article_body', __( "Topic Content", "bbp-core" ), __( "If you choose to include the topic content, it will filtered and all HTML and empty lines will be stripped. Plugin will attempt to return very much stripped down version of the content.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'snippets__topic_dfp_include_article_body', 'features' ) ),
					new d4pSettingElement( '', '', __( "Topic Author", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'snippets__topic_dfp_include_author_profile_url', __( "Include profile URL", "bbp-core" ), __( "Include URL to the author forum profile.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'snippets__topic_dfp_include_author_profile_url', 'features' ) ),
					new d4pSettingElement( 'features', 'snippets__topic_dfp_include_author_website_url', __( "Include website URL", "bbp-core" ), __( "Include URL to the author website, if the website URL is provided through the profile.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'snippets__topic_dfp_include_author_website_url', 'features' ) ),
					new d4pSettingElement( '', '', __( "Publisher Information", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'snippets__topic_dfp_publisher_type', __( "Type", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'snippets__topic_dfp_publisher_type', 'features' ), 'array', $this->data_snippet_type() ),
					new d4pSettingElement( 'features', 'snippets__topic_dfp_publisher_name', __( "Name", "bbp-core" ), __( "This element is required.", "bbp-core" ) . ' ' . __( "If empty, website name will be used.", "bbp-core" ), d4pSettingType::TEXT, gdbbx()->get( 'snippets__topic_dfp_publisher_name', 'features' ) ),
					new d4pSettingElement( 'features', 'snippets__topic_dfp_publisher_logo', __( "Logo", "bbp-core" ), __( "This element is required.", "bbp-core" ), d4pSettingType::IMAGE, gdbbx()->get( 'snippets__topic_dfp_publisher_logo', 'features' ) ),
				)
			)
		);

		$settings['clickable'] = array(
			'clickable'         => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'clickable', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'clickable', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'clickable_filters' => array(
				'name'     => __( "Disable Clickable Filters", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'clickable__disable_make_clickable_topic', __( "For Topics", "bbp-core" ), __( "This filter will convert strings into clickable HTML elements for URL, email and other things.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'clickable__disable_make_clickable_topic', 'features' ), null, array(), array( 'label' => __( "Disable Filter", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'clickable__disable_make_clickable_reply', __( "For Replies", "bbp-core" ), __( "This filter will convert strings into clickable HTML elements for URL, email and other things.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'clickable__disable_make_clickable_reply', 'features' ), null, array(), array( 'label' => __( "Disable Filter", "bbp-core" ) ) )
				)
			),
			'clickable_actions' => array(
				'name'     => __( "Remove Clickable Actions", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Notice", "bbp-core" ), __( "These actions will be executed if one or both 'Make Clickable' filters are active.", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'clickable__remove_clickable_urls', __( "URLs", "bbp-core" ), __( "Remove replacing the text URLs with the working link tags.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'clickable__remove_clickable_urls', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'clickable__remove_clickable_ftps', __( "FTPs", "bbp-core" ), __( "Remove replacing the text FTPs with the working link tags.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'clickable__remove_clickable_ftps', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'clickable__remove_clickable_emails', __( "Emails", "bbp-core" ), __( "Remove replacing the text emails with the working link tags.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'clickable__remove_clickable_emails', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'clickable__remove_clickable_mentions', __( "@Mentions", "bbp-core" ), __( "Remove replacing the @mentions with the links to the user profiles.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'clickable__remove_clickable_mentions', 'features' ), null, array(), array( 'label' => __( "Remove", "bbp-core" ) ) ),
				)
			)
		);

		$settings['privacy'] = array(
			'privacy'    => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'privacy', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'privacy', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'privacy_ip' => array(
				'name'     => __( "IP", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'privacy__disable_ip_logging', __( "IP Logging", "bbp-core" ), __( "This will stop bbPress from logging IP addresses with each post.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'privacy__disable_ip_logging', 'features' ), null, array(), array( 'label' => __( "Disabled", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'privacy__disable_ip_display', __( "IP Display", "bbp-core" ), __( "IP addresses are visible to forum keymaster role. This will stop bbPress from displaying IP addresses.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'privacy__disable_ip_display', 'features' ), null, array(), array( 'label' => __( "Disabled", "bbp-core" ) ) )
				)
			)
		);

		$settings['seo'] = array(
			'seo'             => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'seo', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'seo-head_title'  => array(
				'name'     => __( "Title Tag", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'seo__document_title_parts', __( "Support for new themes", "bbp-core" ), __( "Themes with the 'title-tag' support don't show the default bbPress generated TITLE tag. With this option, support for new themes is added.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__document_title_parts', 'features' ) )
				)
			),
			'seo-forums_seo'  => array(
				'name'     => __( "Forum", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'seo__override_forum_title_replace', __( "Meta Title", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__override_forum_title_replace', 'features' ), null, array(), array( 'label' => __( "Replace with Custom Text", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo__override_forum_title_text', '', __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%', d4pSettingType::TEXT, gdbbx()->get( 'seo__override_forum_title_text', 'features' ) ),
					new d4pSettingElement( 'features', 'seo__meta_description_forum', __( "Meta Description Tag", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__meta_description_forum', 'features' ) )
				)
			),
			'seo-topics_seo'  => array(
				'name'     => __( "Topic", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'seo__override_topic_title_replace', __( "Meta Title", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__override_topic_title_replace', 'features' ), null, array(), array( 'label' => __( "Replace with Custom Text", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo__override_topic_title_text', '', __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %TOPIC_TITLE%, %FORUM_TITLE%', d4pSettingType::TEXT, gdbbx()->get( 'seo__override_topic_title_text', 'features' ) ),
					new d4pSettingElement( 'features', 'seo__override_topic_excerpt', __( "Excerpt", "bbp-core" ), __( "Use this only if you want to take private content into account or have extra control, or your SEO plugin has problems with getting proper excerpt.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__override_topic_excerpt', 'features' ), null, array(), array( 'label' => __( "Override Default", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo__override_topic_length', __( "Excerpt Length", "bbp-core" ), '', d4pSettingType::NUMBER, gdbbx()->get( 'seo__override_topic_length', 'features' ) ),
					new d4pSettingElement( 'features', 'seo__private_topic_excerpt_replace', __( "Private Topics Excerpt", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__private_topic_excerpt_replace', 'features' ), null, array(), array( 'label' => __( "Replace with Custom Text", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo__private_topic_excerpt_text', '', __( "Private topic content will be replaced with this text. You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %TOPIC_TITLE%', d4pSettingType::TEXT, gdbbx()->get( 'seo__private_topic_excerpt_text', 'features' ) ),
					new d4pSettingElement( 'features', 'seo__meta_description_topic', __( "Meta Description Tag", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__meta_description_topic', 'features' ) )
				)
			),
			'seo-replies_seo' => array(
				'name'     => __( "Reply", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'seo__override_reply_title_replace', __( "Meta Title", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__override_reply_title_replace', 'features' ), null, array(), array( 'label' => __( "Replace with Custom Text", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo__override_reply_title_text', '', __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %REPLY_TITLE%, %TOPIC_TITLE%, %FORUM_TITLE%', d4pSettingType::TEXT, gdbbx()->get( 'seo__override_reply_title_text', 'features' ) ),
					new d4pSettingElement( 'features', 'seo__override_reply_excerpt', __( "Excerpt", "bbp-core" ), __( "Use this only if you want to take private content into account or have extra control, or your SEO plugin has problems with getting proper excerpt.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__override_reply_excerpt', 'features' ), null, array(), array( 'label' => __( "Override Default", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo__override_reply_length', __( "Excerpt Length", "bbp-core" ), '', d4pSettingType::NUMBER, gdbbx()->get( 'seo__override_reply_length', 'features' ) ),
					new d4pSettingElement( 'features', 'seo__private_reply_excerpt_replace', __( "Private Reply Excerpt", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__private_reply_excerpt_replace', 'features' ), null, array(), array( 'label' => __( "Replace with Custom Text", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo__private_reply_excerpt_text', '', __( "Private topic content will be replaced with this text. You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %TOPIC_TITLE%', d4pSettingType::TEXT, gdbbx()->get( 'seo__private_reply_excerpt_text', 'features' ) ),
					new d4pSettingElement( 'features', 'seo__meta_description_reply', __( "Meta Description Tag", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo__meta_description_reply', 'features' ) )
				)
			)
		);

		$settings['seo-tweaks'] = array(
			'seo-tweaks'          => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'seo-tweaks', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo-tweaks', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'seo-tweaks_private'  => array(
				'name'     => __( "Private Topics and Replies", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'seo-tweaks__noindex_private_topic', __( "Private Topic", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo-tweaks__noindex_private_topic', 'features' ), null, array(), array( 'label' => __( "Robots Meta NoIndex", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo-tweaks__noindex_private_reply', __( "Private Reply", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'seo-tweaks__noindex_private_reply', 'features' ), null, array(), array( 'label' => __( "Robots Meta NoIndex", "bbp-core" ) ) )
				)
			),
			'seo-tweaks_nofollow' => array(
				'name'     => __( "bbPress NoFollow for Links", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'seo-tweaks__nofollow_topic_content', __( "Topic Content", "bbp-core" ), __( "bbPress modifies all links in topic content and adds (and overrides) 'nofollow' rel attribute.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo-tweaks__nofollow_topic_content', 'features' ), null, array(), array( 'label' => __( "Enabled NoFollow", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo-tweaks__nofollow_reply_content', __( "Reply Content", "bbp-core" ), __( "bbPress modifies all links in reply content and adds (and overrides) 'nofollow' rel attribute.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo-tweaks__nofollow_reply_content', 'features' ), null, array(), array( 'label' => __( "Enabled NoFollow", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo-tweaks__nofollow_topic_author', __( "Topic Author Link", "bbp-core" ), __( "bbPress modifies topic author links and adds (and overrides) 'nofollow' rel attribute.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo-tweaks__nofollow_topic_author', 'features' ), null, array(), array( 'label' => __( "Enabled NoFollow", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'seo-tweaks__nofollow_reply_author', __( "Reply Author Link", "bbp-core" ), __( "bbPress modifies reply author links and adds (and overrides) 'nofollow' rel attribute.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'seo-tweaks__nofollow_reply_author', 'features' ), null, array(), array( 'label' => __( "Enabled NoFollow", "bbp-core" ) ) )
				)
			)
		);

		$settings['users-stats'] = array(
			'users-stats'            => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'users-stats', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'users-stats_visibility' => array(
				'name'     => __( "Show user statistics", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'users-stats__super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'users-stats__visitor', __( "Available to visitors", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__visitor', 'features' ) ),
					new d4pSettingElement( 'features', 'users-stats__roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'users-stats__roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'users-stats_elements'   => array(
				'name'     => __( "Choose what to show", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'users-stats__show_online_status', __( "Show online status", "bbp-core" ), __( "Only if online status tracking is enabled.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__show_online_status', 'features' ) ),
					new d4pSettingElement( 'features', 'users-stats__show_registration_date', __( "Show registration date", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__show_registration_date', 'features' ) ),
					new d4pSettingElement( 'features', 'users-stats__show_topics', __( "Show topics count", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__show_topics', 'features' ) ),
					new d4pSettingElement( 'features', 'users-stats__show_replies', __( "Show replies count", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__show_replies', 'features' ) ),
					new d4pSettingElement( 'features', 'users-stats__show_thanks_given', __( "Show thanks given count", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__show_thanks_given', 'features' ) ),
					new d4pSettingElement( 'features', 'users-stats__show_thanks_received', __( "Show thanks received count", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'users-stats__show_thanks_received', 'features' ) )
				)
			)
		);

		$settings['quote'] = array(
			'quote'          => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'quote', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'quote', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'quote_allowed'  => array(
				'name'     => __( "Allow use of Quotes", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'quote__super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'quote__super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'quote__visitor', __( "Available to visitors", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'quote__visitor', 'features' ) ),
					new d4pSettingElement( 'features', 'quote__roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'quote__roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'quote_settings' => array(
				'name'     => __( "Basic Settings", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'quote__method', __( "Quote Method", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'quote__method', 'features' ), 'array', $this->data_quote_button_method() ),
					new d4pSettingElement( 'features', 'quote__full_content', __( "Shortcode to use", "bbp-core" ), __( "If Post Quote is selected, when quote button is used for full post (not selection), shortcode 'postquote' will be used with post ID only.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'quote__full_content', 'features' ), 'array', $this->data_quote_bbcode() )
				)
			)
		);

		$settings['visitors-redirect'] = array(
			'visitors-redirect'           => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'visitors-redirect', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'visitors-redirect', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'visitors-redirect_nonlogged' => array(
				'name'     => __( "Redirect visitors", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'visitors-redirect__for_visitors', __( "Status", "bbp-core" ), __( "If non-logged user (or visitor) attempts to access any forum page, it will be redirected to custom URL or home page or login page.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'visitors-redirect__for_visitors', 'features' ), 'array', $this->data_redirect_visitor() ),
					new d4pSettingElement( 'features', 'visitors-redirect__for_visitors_url', __( "Custom URL", "bbp-core" ), __( "If empty, it will redirect to website home page.", "bbp-core" ), d4pSettingType::LINK, gdbbx()->get( 'visitors-redirect__for_visitors_url', 'features' ) )
				)
			),
			'visitors-redirect_hidden'    => array(
				'name'     => __( "Redirect hidden forums access attempts", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'visitors-redirect__hidden_forums', __( "Status", "bbp-core" ), __( "Any user trying to access hidden forum, and has no rights to do that, will be redirected to custom URL or login page.", "bbp-core" ) . ' ' . __( "If this option is disabled, user will see the 404 page.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'visitors-redirect__hidden_forums', 'features' ), 'array', $this->data_redirect_visitor() ),
					new d4pSettingElement( 'features', 'visitors-redirect__hidden_forums_url', __( "Custom URL", "bbp-core" ), __( "If empty, it will redirect to website home page.", "bbp-core" ), d4pSettingType::LINK, gdbbx()->get( 'visitors-redirect__hidden_forums_url', 'features' ) )
				)
			),
			'visitors-redirect_private'   => array(
				'name'     => __( "Redirect private forums access attempts", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'visitors-redirect__private_forums', __( "Status", "bbp-core" ), __( "Any user trying to access a private forum, and has no rights to do that, will be redirected to custom URL or login page.", "bbp-core" ) . ' ' . __( "If this option is disabled, user will see the 404 page.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'visitors-redirect__private_forums', 'features' ), 'array', $this->data_redirect_visitor() ),
					new d4pSettingElement( 'features', 'visitors-redirect__private_forums_url', __( "Custom URL", "bbp-core" ), __( "If empty, it will redirect to website home page.", "bbp-core" ), d4pSettingType::LINK, gdbbx()->get( 'visitors-redirect__private_forums_url', 'features' ) )
				)
			),
			'visitors-redirect_blocked'   => array(
				'name'     => __( "Redirect blocked users", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'visitors-redirect__blocked_users', __( "Status", "bbp-core" ), __( "Any blocked user trying to access forums, will be redirected to custom URL.", "bbp-core" ) . ' ' . __( "If this option is disabled, user will see the 404 page.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'visitors-redirect__blocked_users', 'features' ), 'array', $this->data_redirect_blocked_visitor() ),
					new d4pSettingElement( 'features', 'visitors-redirect__blocked_users_url', __( "Custom URL", "bbp-core" ), __( "If empty, it will redirect to website home page.", "bbp-core" ), d4pSettingType::LINK, gdbbx()->get( 'visitors-redirect__blocked_users_url', 'features' ) )
				)
			),
			'visitors-redirect_topic'     => array(
				'name'     => __( "Redirect topics with no access", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'visitors-redirect__noaccess_topic', __( "Status", "bbp-core" ), __( "If user or visitor tries to access topic that is in the hidden or private forum and has no access to it, will be redirected to custom URL or login page.", "bbp-core" ) . ' ' . __( "If this option is disabled, user will see the 404 page.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'visitors-redirect__noaccess_topic', 'features' ), 'array', $this->data_redirect_visitor() ),
					new d4pSettingElement( 'features', 'visitors-redirect__noaccess_topic_url', __( "Custom URL", "bbp-core" ), __( "If empty, it will redirect to website home page.", "bbp-core" ), d4pSettingType::LINK, gdbbx()->get( 'visitors-redirect__noaccess_topic_url', 'features' ) )
				)
			)
		);

		$settings['toolbar'] = array(
			'toolbar'          => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'toolbar', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'toolbar', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'toolbar__control' => array(
				'name'     => __( "Toolbar Control", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'toolbar__super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'toolbar__super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'toolbar__visitor', __( "Available to visitors", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'toolbar__visitor', 'features' ) ),
					new d4pSettingElement( 'features', 'toolbar__roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'toolbar__roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'toolbar__looks'   => array(
				'name'     => __( "Additional Settings", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'toolbar__title', __( "Menu Title", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'toolbar__title', 'features' ) ),
					new d4pSettingElement( 'features', 'toolbar__information', __( "Information Submenu", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'toolbar__information', 'features' ) )
				)
			)
		);

		$settings['objects'] = array(
			'objects'       => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'objects', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'objects', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'objects_forum' => array(
				'name'     => __( "Forum Extra Features", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'objects__add_forum_features', __( "Forum Features", "bbp-core" ), __( "These will be added when registering forum post type.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'objects__add_forum_features', 'features' ), 'array', $this->data_extra_features(), array( 'class' => 'gdbbx-bbcodes' ) )
				)
			),
			'objects_topic' => array(
				'name'     => __( "Topic Extra Features", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'objects__add_topic_features', __( "Topic Features", "bbp-core" ), __( "These will be added when registering topic post type.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'objects__add_topic_features', 'features' ), 'array', $this->data_extra_features(), array( 'class' => 'gdbbx-bbcodes' ) )
				)
			),
			'objects_reply' => array(
				'name'     => __( "Reply Extra Features", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'objects__add_reply_features', __( "Reply Features", "bbp-core" ), __( "These will be added when registering reply post type.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'objects__add_reply_features', 'features' ), 'array', $this->data_extra_features(), array( 'class' => 'gdbbx-bbcodes' ) )
				)
			)
		);

		$settings['publish'] = array(
			'publish'        => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'publish', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'publish', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'publish_status' => array(
				'name'     => __( "Site public status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'publish__bbp_is_site_public', __( "Status", "bbp-core" ), __( "Some bbPress features depend on the site public status. This option will override the default status generated based on the WordPress settings.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'publish__bbp_is_site_public', 'features' ), 'array', $this->data_site_public() )
				)
			)
		);

		$settings['mime-types'] = array(
			'mime-types'      => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'kb'       => array( 'label' => __( "KB", "bbp-core" ), 'url' => 'attachments-mime-types' ),
				'settings' => array(
					new d4pSettingElement( 'load', 'mime-types', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'mime-types', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'mime-types_list' => array(
				'name'     => __( "Additional MIME Types", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'mime-types__list', __( "MIME Types", "bbp-core" ), '', d4pSettingType::EXPANDABLE_PAIRS, gdbbx()->get( 'mime-types__list', 'features' ), '', array(), array(
						'label_key'           => __( "Extensions (Vertical pipe separated)", "bbp-core" ),
						'label_value'         => __( "MIME Type", "bbp-core" ),
						'label_button_add'    => __( "Add New MIME Type", "bbp-core" ),
						'label_buttom_remove' => __( "Remove", "bbp-core" )
					) ),
				)
			)
		);

		$settings['private-topics'] = array(
			'private-topics'          => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'private-topics', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-topics', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'private-topics_settings' => array(
				'name'     => __( "Private Topics", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'private-topics__super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'private-topics__super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'private-topics__visitor', __( "Available to visitors", "bbp-core" ), __( "If anonymous (visitor) creates private topic only administrators and moderators can read the topic.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-topics__visitor', 'features' ) ),
					new d4pSettingElement( 'features', 'private-topics__roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'private-topics__roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'private-topics__moderators_can_read', __( "Moderators Access", "bbp-core" ), __( "By default, all moderators (and administrators) can read private posts. You can disable that with this option. But, this is only frontend option, moderators and administrators can read everything on the admin side.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-topics__moderators_can_read', 'features' ) ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'private-topics__default', __( "Default", "bbp-core" ), __( "This is related to new topics only, not edits.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'private-topics__default', 'features' ), 'array', $this->data_private_checked_status() ),
					new d4pSettingElement( 'features', 'private-topics__form_position', __( "Form Position", "bbp-core" ), __( "Choose where the private checkbox is displayed.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'private-topics__form_position', 'features' ), 'array', $this->data_form_position_topic() )
				)
			)
		);

		$settings['private-replies'] = array(
			'private-replies'          => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'private-replies', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-replies', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'private-replies_settings' => array(
				'name'     => __( "Private Replies", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'private-replies__super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'private-replies__super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'private-replies__visitor', __( "Available to visitors", "bbp-core" ), __( "If anonymous (visitor) creates private reply only administrators and moderators can read the reply.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-replies__visitor', 'features' ) ),
					new d4pSettingElement( 'features', 'private-replies__roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'private-replies__roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'private-replies__moderators_can_read', __( "Moderators Access", "bbp-core" ), __( "By default, all moderators (and administrators) can read private posts. You can disable that with this option. But, this is only frontend option, moderators and administrators can read everything on the admin side.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-replies__moderators_can_read', 'features' ) ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'private-replies__default', __( "Default", "bbp-core" ), __( "This is related to new replies only, not edits.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'private-replies__default', 'features' ), 'array', $this->data_private_checked_status() ),
					new d4pSettingElement( 'features', 'private-replies__threaded', __( "Threaded Replies", "bbp-core" ), __( "If enabled, plugin will support threaded replies. Author of parent reply will see private replies to his replies. Currently, this works only for direct descendant replies only.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-replies__threaded', 'features' ) ),
					new d4pSettingElement( 'features', 'private-replies__form_position', __( "Form Position", "bbp-core" ), __( "Choose where the private checkbox is displayed.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'private-replies__form_position', 'features' ), 'array', $this->data_form_position_reply() ),
					new d4pSettingElement( 'features', 'private-replies__css_hide', __( "Hide using CSS/JS", "bbp-core" ), __( "Hide private reply in the topic thread from users with no access rights using CSS and JavaScript (this might not work with every theme).", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'private-replies__css_hide', 'features' ) )
				)
			)
		);

		$settings['thanks'] = array(
			'thanks'         => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'thanks', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'thanks_allow'   => array(
				'name'     => __( "Available for roles", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'thanks__allow_super_admin', __( "Super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks__allow_super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'thanks__allow_roles', __( "Roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'thanks__allow_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'thanks_options' => array(
				'name'     => __( "Controls", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'thanks__removal', __( "Allow thanks removal", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks__removal', 'features' ) ),
					new d4pSettingElement( 'features', 'thanks__topic', __( "Available for Topics", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks__topic', 'features' ) ),
					new d4pSettingElement( 'features', 'thanks__reply', __( "Available for Replies", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks__reply', 'features' ) )
				)
			),
			'thanks_display' => array(
				'name'     => __( "Display", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'thanks__limit_display', __( "Limit displayed", "bbp-core" ), __( "This will limit number of users to show inside the thanks block. Too many users displayed can slow down loading.", "bbp-core" ), d4pSettingType::INTEGER, gdbbx()->get( 'thanks__limit_display', 'features' ) ),
					new d4pSettingElement( 'features', 'thanks__display_date', __( "Thanks date", "bbp-core" ), __( "Show date or age of the thanks with each displayed user.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'thanks__display_date', 'features' ), 'array', $this->data_thanks_date_display() )
				)
			),
			'thanks_notify'  => array(
				'name'     => __( "Notifications", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'thanks__notify_active', __( "To author", "bbp-core" ), __( "Send notification to topic or reply authors when they get new thanks.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks__notify_active', 'features' ) ),
					new d4pSettingElement( '', '', __( "Override notification content", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'thanks__notify_override', __( "Override content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks__notify_override', 'features' ) ),
					new d4pSettingElement( 'features', 'thanks__notify_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %THANKS_AUTHOR%, %POST_TITLE%, %POST_LINK%, %FORUM_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'thanks__notify_content', 'features' ) ),
					new d4pSettingElement( 'features', 'thanks__notify_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %THANKS_AUTHOR%, %POST_TITLE%, %FORUM_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'thanks__notify_subject', 'features' ) ),
					new d4pSettingElement( '', '', __( "Additional settings", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'thanks__notify_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'thanks__notify_shortcodes', 'features' ) )
				)
			)
		);

		$settings['admin-access'] = array(
			'admin-access'       => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'admin-access', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-access', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'admin-access_roles' => array(
				'name'     => __( "Select roles to have access", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'admin-access__disable_roles', __( "Access for roles", "bbp-core" ), __( "Super admin will always have full access. All roles with limited access will still have limited access even if allowed access here.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'admin-access__disable_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			)
		);

		$settings['report'] = array(
			'report'        => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'report', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'report', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'report_status' => array(
				'name'     => __( "Reporting Basics", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'report__report_mode', __( "Mode", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'report__report_mode', 'features' ), 'array', $this->data_report_mode() ),
					new d4pSettingElement( 'features', 'report__allow_roles', __( "Roles", "bbp-core" ), __( "Only users with selected roles can post reports.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'report__allow_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'report__scroll_form', __( "Scroll to Form", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'report__scroll_form', 'features' ) ),
				)
			),
			'report_info'   => array(
				'name'     => __( "Display Report Information", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'report__show_report_status', __( "Status", "bbp-core" ), __( "For each reported topic or reply show the notice that it is reported.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'report__show_report_status', 'features' ) ),
					new d4pSettingElement( 'features', 'report__show_report_status_to_moderators_only', __( "To Moderators only", "bbp-core" ), __( "Only keymasters and moderators will be able to see the reported message.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'report__show_report_status_to_moderators_only', 'features' ) )
				)
			),
			'report_notify' => array(
				'name'     => __( "Notifications", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'report__notify_active', __( "Send", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'report__notify_active', 'features' ) ),
					new d4pSettingElement( 'features', 'report__notify_keymasters', __( "Send to Keymasters", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'report__notify_keymasters', 'features' ) ),
					new d4pSettingElement( 'features', 'report__notify_moderators', __( "Send to Moderators", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'report__notify_moderators', 'features' ) ),
					new d4pSettingElement( 'features', 'report__notify_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'report__notify_shortcodes', 'features' ) ),
					new d4pSettingElement( 'features', 'report__notify_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %REPORT_AUTHOR%, %REPORT_CONTENT%, %REPORT_LINK%, %REPORT_TITLE%, %REPORTS_LIST%, %FORUM_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'report__notify_content', 'features' ) ),
					new d4pSettingElement( 'features', 'report__notify_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %REPORT_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'report__notify_subject', 'features' ) )
				)
			)
		);

		$settings['signatures'] = array(
			'signatures'            => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'signatures', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'signatures_control'    => array(
				'name'     => __( "User Signatures", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'signatures__limiter', __( "Limit Counter", "bbp-core" ), __( "Use JavaScript to show signature length and limit. This will not work if the TinyMCE editor is used for signatures.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__limiter', 'features' ) ),
					new d4pSettingElement( 'features', 'signatures__length', __( "Maximum Length", "bbp-core" ), '', d4pSettingType::NUMBER, gdbbx()->get( 'signatures__length', 'features' ) ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'signatures__super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'signatures__roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'signatures__roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'signatures__scope', __( "Store signature as", "bbp-core" ), __( "If you run WordPress Network, with bbPress used on more then one website in the network, it is better to use Local storage, or signature would be the same on each website.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'signatures__scope', 'features' ), 'array', $this->data_signature_scopes() )
				)
			),
			'signatures_editing'    => array(
				'name'     => __( "Signatures Editing", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Important", "bbp-core" ), __( "You can limit the ability to edit the signatures only to selected roles. This way, signatures will be enabled, but only some users will be able to edit them. You can set only for the Keymaster to be able to edit signatures for other users.", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', '', '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'signatures__edit_super_admin', __( "Super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__edit_super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'signatures__edit_roles', __( "Roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'signatures__edit_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			),
			'signatures_enhanced'   => array(
				'name'     => __( "Enhanced Signatures", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'signatures__enhanced_active', __( "Active", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__enhanced_active', 'features' ) ),
					new d4pSettingElement( 'features', 'signatures__enhanced_super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__enhanced_super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'signatures__enhanced_roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'signatures__enhanced_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'signatures__enhanced_method', __( "Allowed Content", "bbp-core" ), __( "If the editor type is set to TinyMCE, HTML will be allowed regardless of this option.", "bbp-core" ) . ' <strong>' . sprintf( __( "Make sure to read <a href='%s' target='_blank'>this article</a> before configuring this option to understand limitations related to frontend signature editing.", "bbp-core" ), 'https://support.dev4press.com/kb/article/signatures-with-bbcodes-editing-limitations/' ) . '</strong>', d4pSettingType::SELECT, gdbbx()->get( 'signatures__enhanced_method', 'features' ), 'array', $this->data_enhanced_signature_method() ),
					new d4pSettingElement( 'features', 'signatures__editor', __( "Editor Type", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'signatures__editor', 'features' ), 'array', $this->data_enhanced_editor_types() )
				)
			),
			'signatures_processing' => array(
				'name'     => __( "Display Processing", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'signatures__process_smilies', __( "Convert Smilies", "bbp-core" ), __( "Convert smilies characters into inline images.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__process_smilies', 'features' ) ),
					new d4pSettingElement( 'features', 'signatures__process_chars', __( "Convert Chars", "bbp-core" ), __( "Run standard WordPress Unicode chars conversion and cleanup.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__process_chars', 'features' ) ),
					new d4pSettingElement( 'features', 'signatures__process_autop', __( "Convert AutoP", "bbp-core" ), __( "Run standard WordPress AutoP function.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'signatures__process_autop', 'features' ) )
				)
			)
		);

		$settings['lock-topics'] = array(
			'lock-topics'            => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'lock-topics', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'lock-topics', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'lock-forums_topic_form' => array(
				'name'     => __( "Lock Topic Control", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'lock-topics__lock', __( "Status", "bbp-core" ), __( "Show lock/unlock options for individual topics. The option will not be available in the topics belonging to the forums that are already locked.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'lock-topics__lock', 'features' ) )
				)
			)
		);

		$settings['lock-forums'] = array(
			'lock-forums'            => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'lock-forums', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'lock-forums', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'lock-forums_topic_form' => array(
				'name'     => __( "Topic Form", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'lock-forums__topic_form_locked', __( "Status", "bbp-core" ), __( "Topic form (edit or new) will be disabled. Only user roles listed below can create or edit topics.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'lock-forums__topic_form_locked', 'features' ), null, array(), array( 'label' => __( "Disable Topic Form", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'lock-forums__topic_form_allow_super_admin', __( "Allowed to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'lock-forums__topic_form_allow_super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'lock-forums__topic_form_allow_roles', __( "Allowed to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'lock-forums__topic_form_allow_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'lock-forums__topic_form_message', __( "Lock message", "bbp-core" ), __( "If the form is locked, this message will be displayed instead.", "bbp-core" ), d4pSettingType::TEXT_HTML, gdbbx()->get( 'lock-forums__topic_form_message', 'features' ) )
				)
			),
			'lock-forums_reply_form' => array(
				'name'     => __( "Reply Form", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'lock-forums__reply_form_locked', __( "Status", "bbp-core" ), __( "Reply form (edit or new) will be disabled. Only user roles listed below can create or edit replies.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'lock-forums__reply_form_locked', 'features' ), null, array(), array( 'label' => __( "Disable Reply Form", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'lock-forums__reply_form_allow_super_admin', __( "Allowed to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'lock-forums__reply_form_allow_super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'lock-forums__reply_form_allow_roles', __( "Allowed to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'lock-forums__reply_form_allow_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'lock-forums__reply_form_message', __( "Lock message", "bbp-core" ), __( "If the form is locked, this message will be displayed instead.", "bbp-core" ), d4pSettingType::TEXT_HTML, gdbbx()->get( 'lock-forums__reply_form_message', 'features' ) )
				)
			)
		);

		$settings['canned-replies'] = array(
			'canned-replies'        => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'kb'       => array( 'label' => __( "KB", "bbp-core" ), 'url' => 'canned-replies' ),
				'settings' => array(
					new d4pSettingElement( 'load', 'canned-replies', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'canned-replies', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'canned-replies_status' => array(
				'name'     => __( "Activity", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'canned-replies__canned_roles', __( "User roles", "bbp-core" ), __( "User with selected roles will be able to see the list of canned replies and insert them into content.", "bbp-core" ), d4pSettingType::CHECKBOXES, gdbbx()->get( 'canned-replies__canned_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'canned-replies__use_taxonomy', __( "Use Categories", "bbp-core" ), __( "If you plan to add many canned replies, it is a good idea to keep them categorized.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'canned-replies__use_taxonomy', 'features' ) ),
					new d4pSettingElement( 'features', 'canned-replies__auto_close_on_insert', __( "Auto close on insert", "bbp-core" ), __( "Canned Replies box will auto close when you click to insert reply into editor.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'canned-replies__auto_close_on_insert', 'features' ) )
				)
			),
			'canned-replies_labels' => array(
				'name'     => __( "Labels", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Post Type", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'canned-replies__post_type_singular', __( "Singular", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'canned-replies__post_type_singular', 'features' ) ),
					new d4pSettingElement( 'features', 'canned-replies__post_type_plural', __( "Plural", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'canned-replies__post_type_plural', 'features' ) ),
					new d4pSettingElement( '', '', __( "Category", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'canned-replies__taxonomy_singular', __( "Singular", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'canned-replies__taxonomy_singular', 'features' ) ),
					new d4pSettingElement( 'features', 'canned-replies__taxonomy_plural', __( "Plural", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'canned-replies__taxonomy_plural', 'features' ) )
				)
			)
		);

		$settings['protect-revisions'] = array(
			'protect-revisions'        => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'protect-revisions', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'protect-revisions', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'protect-revisions_access' => array(
				'name'     => __( "Select who can view revisions", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Authors", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'protect-revisions__allow_author', __( "Post author", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'protect-revisions__allow_author', 'features' ) ),
					new d4pSettingElement( 'features', 'protect-revisions__allow_topic_author', __( "Topic author (for replies)", "bbp-core" ), __( "If the post is reply, this will take into account author of the topic too.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'protect-revisions__allow_topic_author', 'features' ) ),
					new d4pSettingElement( '', '', __( "Other Users and Visitors", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'protect-revisions__allow_super_admin', __( "Super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'protect-revisions__allow_super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'protect-revisions__allow_visitor', __( "Visitors/Guests", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'protect-revisions__allow_visitor', 'features' ) ),
					new d4pSettingElement( 'features', 'protect-revisions__allow_roles', __( "User Roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'protect-revisions__allow_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			)
		);

		$settings['footer-actions'] = array(
			'footer-actions' => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'footer-actions', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'footer-actions', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			)
		);

		$settings['admin-widgets'] = array(
			'admin-widgets'      => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'admin-widgets', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-widgets', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'admin-widgets_list' => array(
				'name'     => __( "Add Widgets", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'admin-widgets__online', __( "Online Users", "bbp-core" ), __( "This will add Online Users dashboard widget.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-widgets__online', 'features' ), null, array(), array( 'label' => __( "Add Widget", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'admin-widgets__activity', __( "Latest Activity", "bbp-core" ), __( "This will add Latest Activity dashboard widget that includes recent topics and replies.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-widgets__activity', 'features' ), null, array(), array( 'label' => __( "Add Widget", "bbp-core" ) ) )
				)
			)
		);

		$settings['admin-columns'] = array(
			'admin-columns'         => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'admin-columns', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'admin-columns_forums'  => array(
				'name'     => __( "Forums Panel", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'admin-columns__forum_subscriptions', __( "Subscribers Count", "bbp-core" ), __( "Column with count of users that subscribed to the forum.", "bbp-core" ) . '<br/><strong>' . __( "This feature requires bbPress 2.6 or newer.", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__forum_subscriptions', 'features' ) )
				)
			),
			'admin-columns_topics'  => array(
				'name'     => __( "Topics Panel", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'admin-columns__topic_attachments', __( "Attachments Count", "bbp-core" ), __( "Column with number of attachments.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__topic_attachments', 'features' ) ),
					new d4pSettingElement( 'features', 'admin-columns__topic_private', __( "Private Status", "bbp-core" ), __( "Column with privacy status.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__topic_private', 'features' ) ),
					new d4pSettingElement( 'features', 'admin-columns__topic_subscriptions', __( "Subscribers Count", "bbp-core" ), __( "Column with count of users that subscribed to the topic.", "bbp-core" ) . '<br/><strong>' . __( "This feature requires bbPress 2.6 or newer.", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__topic_subscriptions', 'features' ) ),
					new d4pSettingElement( 'features', 'admin-columns__topic_favorites', __( "Favourites Count", "bbp-core" ), __( "Column with count of users that favorited the topic.", "bbp-core" ) . '<br/><strong>' . __( "This feature requires bbPress 2.6 or newer.", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__topic_favorites', 'features' ) )
				)
			),
			'admin-columns_replies' => array(
				'name'     => __( "Replies Panel", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'admin-columns__reply_attachments', __( "Attachments Count", "bbp-core" ), __( "Column with number of attachments.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__reply_attachments', 'features' ) ),
					new d4pSettingElement( 'features', 'admin-columns__reply_private', __( "Private Status", "bbp-core" ), __( "Column with privacy status.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__reply_private', 'features' ) )
				)
			),
			'admin-columns_users'   => array(
				'name'     => __( "Users Panel", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'admin-columns__user_content', __( "Topics/Replies Count", "bbp-core" ), __( "Two columns for topics and replies with counts and links to filter them by user.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__user_content', 'features' ) ),
					new d4pSettingElement( 'features', 'admin-columns__user_last_activity', __( "Last activity", "bbp-core" ), __( "Column with the user last activity date and time.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'admin-columns__user_last_activity', 'features' ) )
				)
			)
		);

		$settings['forum-index'] = array(
			'forum-index'         => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'forum-index', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'forum-index_welcome' => array(
				'name'     => __( "User welcome overview", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'forum-index__welcome_front', __( "Load", "bbp-core" ), __( "Main forums index, underneath the list of forums, will show the basic forums statistics.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__welcome_front', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__welcome_filter', __( "Location", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'forum-index__welcome_filter', 'features' ), 'array', $this->data_forum_index_filters() ),
					new d4pSettingElement( 'features', 'forum-index__welcome_front_roles', __( "Show to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'forum-index__welcome_front_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( '', '', __( "Welcome Back Block", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'forum-index__welcome_show_links', __( "Show important links for a user", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__welcome_show_links', 'features' ) )
				)
			),
			'forum-index_stats'   => array(
				'name'     => __( "Forums statistics overview", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'forum-index__statistics_front', __( "Load", "bbp-core" ), __( "Main forums index, underneath the list of forums, will show the basic forums statistics.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_front', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_filter', __( "Location", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'forum-index__statistics_filter', 'features' ), 'array', $this->data_forum_index_filters() ),
					new d4pSettingElement( 'features', 'forum-index__statistics_front_roles', __( "Show to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'forum-index__statistics_front_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_front_visitor', __( "Show to visitors", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_front_visitor', 'features' ) ),
					new d4pSettingElement( '', '', __( "Users Block", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_online', __( "Show active users block", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_online', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_online_overview', __( "Show online users overview", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_online_overview', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_online_top', __( "Show most online users", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_online_top', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_users', __( "Show active users", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'forum-index__statistics_show_users', 'features' ), 'array', $this->data_active_users_period() ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_users_colors', __( "Show users color coded", "bbp-core" ), __( "Each user will be displayed with different color according to user role.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_users_colors', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_users_avatars', __( "Show users avatars", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_users_avatars', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_users_links', __( "Show users linked", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_users_links', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_users_limit', __( "Limit displayed users", "bbp-core" ), __( "Showing long list of users can be performance intensive.", "bbp-core" ), d4pSettingType::ABSINT, gdbbx()->get( 'forum-index__statistics_show_users_limit', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_legend', __( "Show colors legend", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_legend', 'features' ) ),
					new d4pSettingElement( '', '', __( "Statistics Block", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_statistics', __( "Show statistics block", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_statistics', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_statistics_totals', __( "Show totals counts", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_statistics_totals', 'features' ) ),
					new d4pSettingElement( 'features', 'forum-index__statistics_show_statistics_newest_user', __( "Show newest user", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'forum-index__statistics_show_statistics_newest_user', 'features' ) )
				)
			)
		);

		$settings['profiles'] = array(
			'profiles'         => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'profiles', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'profiles', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'profiles_protect' => array(
				'name'     => __( "Protect Profile Pages", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'profiles__hide_from_visitors', __( "Hide from visitors", "bbp-core" ), __( "If enabled, user profile pages content will be hidden from non-logged users (visitors).", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'profiles__hide_from_visitors', 'features' ) )
				)
			),
			'profiles_thanks'  => array(
				'name'     => __( "Thanks Information BLock", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'profiles__thanks_display', __( "Show the block", "bbp-core" ), __( "Show the number of thanks user has given and received.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'profiles__thanks_display', 'features' ) ),
					new d4pSettingElement( 'features', 'profiles__thanks_private', __( "Keep the block private", "bbp-core" ), __( "Only account owner will be able to see this block on own profile, it will be hidden from other users.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'profiles__thanks_private', 'features' ) )
				)
			),
			'profiles_extras'  => array(
				'name'     => __( "Extra Information Block", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'profiles__extras_display', __( "Show the block", "bbp-core" ), __( "Profile page will include extra information block that includes overview of the subscriptions and favorites.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'profiles__extras_display', 'features' ) ),
					new d4pSettingElement( 'features', 'profiles__extras_actions', __( "Show actions with the block", "bbp-core" ), __( "Actions to unsubscribe from all forums and topics and remove all favorites. Only account owner can see these actions.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'profiles__extras_actions', 'features' ) ),
					new d4pSettingElement( 'features', 'profiles__extras_private', __( "Keep the block private", "bbp-core" ), __( "Only account owner will be able to see this block on own profile, it will be hidden from other users.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'profiles__extras_private', 'features' ) )
				)
			)
		);

		$settings['topics'] = array(
			'topics'        => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'topics', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'topics', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'topics_minmax' => array(
				'name'     => __( "Save Topic - Title and Content length", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'topics__new_topic_minmax_active', __( "Length control", "bbp-core" ), __( "When new topic is saved, minimal and maximal length for title and content will be enforced.", "bbp-core" ) . ' ' . __( "Set value to zero to ignore it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'topics__new_topic_minmax_active', 'features' ) ),
					new d4pSettingElement( 'features', 'topics__new_topic_min_title_words', __( "Min Title Words", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'topics__new_topic_min_title_words', 'features' ) ),
					new d4pSettingElement( 'features', 'topics__new_topic_min_title_length', __( "Min Title Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'topics__new_topic_min_title_length', 'features' ) ),
					new d4pSettingElement( 'features', 'topics__new_topic_max_title_length', __( "Max Title Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'topics__new_topic_max_title_length', 'features' ) ),
					new d4pSettingElement( 'features', 'topics__new_topic_min_content_length', __( "Min Content Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'topics__new_topic_min_content_length', 'features' ) ),
					new d4pSettingElement( 'features', 'topics__new_topic_max_content_length', __( "Max Content Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'topics__new_topic_max_content_length', 'features' ) )
				)
			),
			'topics_tweaks' => array(
				'name'     => __( "Various Settings", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'topics__enable_lead_topic', __( "Display Lead Topic", "bbp-core" ), __( "Show main thread topic on top separated from replies.", "bbp-core" ) . ' <strong>' . __( "This option might not work with every theme, make sure to test it!", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'topics__enable_lead_topic', 'features' ) ),
					new d4pSettingElement( 'features', 'topics__enable_topic_reversed_replies', __( "Reversed replies Order", "bbp-core" ), __( "When displaying topic, replies will be reversed, and on top you will see latest reply. If the Lead topic is enabled, topic post will remain on the top, if not, topic post will be the last.", "bbp-core" ) . ' <strong>' . __( "This feature is not compatible with bbPress Replies Threading.", "bbp-core" ) . '</strong>', d4pSettingType::BOOLEAN, gdbbx()->get( 'topics__enable_topic_reversed_replies', 'features' ) ),
					new d4pSettingElement( 'features', 'topics__forum_list_topic_thumbnail', __( "Show Thumbnail", "bbp-core" ), __( "If there is a thumbnail (featured image) set for topic, or plugin can find image in topic content, it will display the thumbnail before topic title in the topics list.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'topics__forum_list_topic_thumbnail', 'features' ) )
				)
			)
		);

		$settings['replies'] = array(
			'replies'      => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'replies', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'replies', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'reply_minmax' => array(
				'name'     => __( "Save Reply - Title and Content length", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'replies__new_reply_minmax_active', __( "Length control", "bbp-core" ), __( "When new reply is saved, minimal and maximal length for title and content will be enforced.", "bbp-core" ) . ' ' . __( "Set value to zero to ignore it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'replies__new_reply_minmax_active', 'features' ) ),
					new d4pSettingElement( 'features', 'replies__new_reply_min_title_words', __( "Min Title Words", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'replies__new_reply_min_title_words', 'features' ) ),
					new d4pSettingElement( 'features', 'replies__new_reply_min_title_length', __( "Min Title Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'replies__new_reply_min_title_length', 'features' ) ),
					new d4pSettingElement( 'features', 'replies__new_reply_max_title_length', __( "Max Title Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'replies__new_reply_max_title_length', 'features' ) ),
					new d4pSettingElement( 'features', 'replies__new_reply_min_content_length', __( "Min Content Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'replies__new_reply_min_content_length', 'features' ) ),
					new d4pSettingElement( 'features', 'replies__new_reply_max_content_length', __( "Max Content Length", "bbp-core" ), '', d4pSettingType::INTEGER, gdbbx()->get( 'replies__new_reply_max_content_length', 'features' ) )
				)
			),
			'reply_tags'   => array(
				'name'     => __( "Various Settings", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'replies__tags_in_reply_form_only_for_author', __( "Topic tags in reply form", "bbp-core" ), __( "Reply form contains topic tags box, and anyone replying can change the tags assigned. If enabled, this option will show this field only for topic author.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'replies__tags_in_reply_form_only_for_author', 'features' ), null, array(), array( 'label' => __( "Only for topic author", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'replies__reply_titles', __( "Reply Titles", "bbp-core" ), __( "By default, replies don't have titles. But, with this option, reply editor will have an extra field to set the reply title.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'replies__reply_titles', 'features' ) )
				)
			)
		);

		$settings['disable-rss'] = array(
			'disable-rss'       => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'disable-rss', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'disable-rss', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'disable-rssviews'  => array(
				'name'     => __( "Topic Views RSS", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'disable-rss__view_feed', __( "Status", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'disable-rss__view_feed', 'features' ), null, array(), array( 'label' => __( "Disable Feed", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'disable-rss__view_feed_redirect', __( "Redirect to", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'disable-rss__view_feed_redirect', 'features' ), 'array', $this->data_disable_rss() )
				)
			),
			'disable-rss_forum' => array(
				'name'     => __( "Forums RSS", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'disable-rss__forum_feed', __( "Status", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'disable-rss__forum_feed', 'features' ), null, array(), array( 'label' => __( "Disable Feed", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'disable-rss__forum_feed_redirect', __( "Redirect to", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'disable-rss__forum_feed_redirect', 'features' ), 'array', $this->data_disable_rss() )
				)
			),
			'disable-rss_topic' => array(
				'name'     => __( "Topics RSS", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'disable-rss__topic_feed', __( "Status", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'disable-rss__topic_feed', 'features' ), null, array(), array( 'label' => __( "Disable Feed", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'disable-rss__topic_feed_redirect', __( "Redirect to", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'disable-rss__topic_feed_redirect', 'features' ), 'array', $this->data_disable_rss() )
				)
			),
			'disable-rss_reply' => array(
				'name'     => __( "Replies RSS", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'disable-rss__reply_feed', __( "Status", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'disable-rss__reply_feed', 'features' ), null, array(), array( 'label' => __( "Disable Feed", "bbp-core" ) ) ),
					new d4pSettingElement( 'features', 'disable-rss__reply_feed_redirect', __( "Redirect to", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'disable-rss__reply_feed_redirect', 'features' ), 'array', $this->data_disable_rss() )
				)
			)
		);

		$settings['auto-close-topics'] = array(
			'auto-close-topics'          => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'kb'       => array(
					'label' => __( "KB", "bbp-core" ),
					'url'   => 'auto-close-inactive-topics'
				),
				'settings' => array(
					new d4pSettingElement( 'load', 'auto-close-topics', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'auto-close-topics_settings' => array(
				'name'     => __( "Auto close inactive topics", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( '', '', __( "Notice", "bbp-core" ), __( "These options can be set and overridden for individual forums. You can even disable global Auto Close below, and only enable it for some forums by editing individual forums.", "bbp-core" ), d4pSettingType::INFO ),
					new d4pSettingElement( '', '', __( "Global auto close settings", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'auto-close-topics__active', __( "Auto Close", "bbp-core" ), __( "If the topic doesn't get any new replies after predefined amount of time, it will be automatically closed.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__active', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__notice', __( "Show Notice", "bbp-core" ), __( "Notice will be displayed inside the reply form about the auto-closing.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__notice', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__days', __( "Days of inactivity", "bbp-core" ), '', d4pSettingType::ABSINT, gdbbx()->get( 'auto-close-topics__days', 'features' ), '', '', array( 'min' => AutoCloseTopics::minimum_days_allowed() ) )
				)
			),
			'auto-close-topics_modify'   => array(
				'name'     => __( "Modify close terms", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'auto-close-topics__modify_topic_form', __( "For topic form", "bbp-core" ), __( "Include the auto close controls in the new or edit topic form allowing change of the auto close rules.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__modify_topic_form', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__modify_topic_form_location', __( "Topic Form Position", "bbp-core" ), __( "Choose where the modify auto close block is displayed.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'auto-close-topics__modify_topic_form_location', 'features' ), 'array', $this->data_form_position_topic() ),
					new d4pSettingElement( 'features', 'auto-close-topics__modify_reply_form', __( "For reply form", "bbp-core" ), __( "Include the auto close controls in the new or edit reply form allowing change of the auto close rules for the topic reply belongs to.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__modify_reply_form', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__modify_reply_form_location', __( "Reply Form Position", "bbp-core" ), __( "Choose where the modify auto close block is displayed.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'auto-close-topics__modify_reply_form_location', 'features' ), 'array', $this->data_form_position_reply() ),
					new d4pSettingElement( '', '', __( "Members allowed to modify the terms", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'auto-close-topics__modify_author', __( "Allowed for topic author", "bbp-core" ), __( "Topic author will be allowed to change auto close topic rules.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__modify_author', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__modify_moderators', __( "Allowed for moderators", "bbp-core" ), __( "Moderators and keymasters will be allowed to change auto close topic rules.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__modify_moderators', 'features' ) )
				)
			),
			'auto-close-topics_notify'   => array(
				'name'     => __( "Notifications", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'auto-close-topics__notify_author', _x( "To author", "Sending email notification", "bbp-core" ), __( "Topic author will receive email notification when the topic is closed.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__notify_author', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__notify_subscribers', _x( "To subscribers", "Sending email notification", "bbp-core" ), __( "All topic subscribers will receive email notification when the topic is closed.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__after_notify_subscribers', 'features' ) ),
					new d4pSettingElement( '', '', __( "Override notification content", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'auto-close-topics__notify_active', __( "Override content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__notify_active', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__notify_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %FORUM_LINK%, %TOPIC_AUTHOR%, %TOPIC_CONTENT%, %TOPIC_LINK%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'auto-close-topics__notify_content', 'features' ) ),
					new d4pSettingElement( 'features', 'auto-close-topics__notify_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'auto-close-topics__notify_subject', 'features' ) ),
					new d4pSettingElement( '', '', __( "Additional settings", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'auto-close-topics__notify_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'auto-close-topics__notify_shortcodes', 'features' ) )
				)
			)
		);

		$settings['notifications'] = array(
			'notifications'            => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'notifications', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'notifications', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'notifications_new_topic'  => array(
				'name'     => __( "Notify when new topic is added", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'notifications__new_topic_keymaster', __( "Notify Keymasters", "bbp-core" ), __( "When a new topic is added, keymasters will be notified.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'notifications__new_topic_keymaster', 'features' ) ),
					new d4pSettingElement( 'features', 'notifications__new_topic_moderator', __( "Notify Moderators", "bbp-core" ), __( "When a new topic is added, moderators will be notified.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'notifications__new_topic_moderator', 'features' ) )
				)
			),
			'notifications_new_reply'  => array(
				'name'     => __( "Notify when new reply is added", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'notifications__new_reply_keymaster', __( "Notify Keymasters", "bbp-core" ), __( "When a new reply is added, keymasters will be notified.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'notifications__new_reply_keymaster', 'features' ) ),
					new d4pSettingElement( 'features', 'notifications__new_reply_moderator', __( "Notify Moderators", "bbp-core" ), __( "When a new reply is added, moderators will be notified.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'notifications__new_reply_moderator', 'features' ) )
				)
			),
			'notifications_edit_topic' => array(
				'name'     => __( "Notify on Topic edit", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'notifications__topic_on_edit', __( "Include in edit form", "bbp-core" ), __( "Plugin will add new block with checkboxes to send notifications to topic author and/or subscribers when the topic was edited.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'notifications__topic_on_edit', 'features' ) )
				)
			),
			'notifications_edit_reply' => array(
				'name'     => __( "Notify on Reply edit", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'notifications__reply_on_edit', __( "Include in edit form", "bbp-core" ), __( "Plugin will add new block with checkboxes to send notifications to reply author and/or topic subscribers when the reply was edited.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'notifications__reply_on_edit', 'features' ) )
				)
			)
		);

		$settings['email-sender'] = array(
			'email-sender'        => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'email-sender', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'email-sender', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'email-sender_sender' => array(
				'name'     => __( "Notifications Sender", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'email-sender__sender_name', __( "Sender name", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'email-sender__sender_name', 'features' ) ),
					new d4pSettingElement( 'features', 'email-sender__sender_email', __( "Sender email", "bbp-core" ), '', d4pSettingType::TEXT, gdbbx()->get( 'email-sender__sender_email', 'features' ) ),
				)
			)
		);

		$settings['email-overrides'] = array(
			'email-overrides'                       => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'email-overrides', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'email-overrides__notify_on_topic_edit' => array(
				'name'     => __( "Topic Edit Notify Subscribers", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_edit_active', __( "Modify notification content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_edit_active', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_edit_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_edit_shortcodes', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_edit_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %TOPIC_EDITOR%, %TOPIC_AUTHOR%, %TOPIC_CONTENT%, %TOPIC_EDIT%, %TOPIC_LINK%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'email-overrides__notify_subscribers_edit_content', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_edit_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'email-overrides__notify_subscribers_edit_subject', 'features' ) )
				)
			),
			'email-overrides__notify_on_reply_edit' => array(
				'name'     => __( "Reply Edit Notify Subscribers", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_reply_edit_active', __( "Modify notification content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_reply_edit_active', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_reply_edit_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_reply_edit_shortcodes', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_reply_edit_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %TOPIC_TITLE%, %REPLY_EDITOR%, %REPLY_CONTENT% %REPLY_AUTHOR%, %REPLY_EDIT%, %REPLY_LINK%, %REPLY_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'email-overrides__notify_subscribers_reply_edit_content', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_reply_edit_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %REPLY_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'email-overrides__notify_subscribers_reply_edit_subject', 'features' ) )
				)
			),
			'email-overrides__notify_topic_mod'     => array(
				'name'     => __( "New Topic for Keymasters and Moderators", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_topic_active', __( "Modify notification content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_moderators_topic_active', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_topic_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_moderators_topic_shortcodes', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_topic_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %FORUM_LINK%, %TOPIC_AUTHOR%, %TOPIC_CONTENT%, %TOPIC_LINK%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'email-overrides__notify_moderators_topic_content', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_topic_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'email-overrides__notify_moderators_topic_subject', 'features' ) )
				)
			),
			'email-overrides__notify_reply_mod'     => array(
				'name'     => __( "New Reply for Keymasters and Moderators", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_reply_active', __( "Modify notification content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_moderators_reply_active', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_reply_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_moderators_reply_shortcodes', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_reply_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %FORUM_LINK%, %REPLY_AUTHOR%, %REPLY_CONTENT%, %TOPIC_LINK%, %REPLY_TITLE%, %REPLY_LINK%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'email-overrides__notify_moderators_reply_content', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_moderators_reply_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'email-overrides__notify_moderators_reply_subject', 'features' ) )
				)
			),
			'email-overrides__notify_email'         => array(
				'name'     => __( "Topic Subscribe Notifications Email", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_override_active', __( "Modify notification content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_override_active', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_override_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_override_shortcodes', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_override_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %REPLY_AUTHOR%, %REPLY_CONTENT%, %REPLY_TITLE%, %REPLY_LINK%, %TOPIC_AUTHOR%, %TOPIC_LINK%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'email-overrides__notify_subscribers_override_content', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_override_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'email-overrides__notify_subscribers_override_subject', 'features' ) )
				)
			),
			'email-overrides__notify_forum'         => array(
				'name'     => __( "Forum Subscribe Notifications Email", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_forum_override_active', __( "Modify notification content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_forum_override_active', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_forum_override_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'email-overrides__notify_subscribers_forum_override_shortcodes', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_forum_override_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %FORUM_LINK%, %TOPIC_AUTHOR%, %TOPIC_TITLE%, %TOPIC_LINK%, %TOPIC_CONTENT%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'email-overrides__notify_subscribers_forum_override_content', 'features' ) ),
					new d4pSettingElement( 'features', 'email-overrides__notify_subscribers_forum_override_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'email-overrides__notify_subscribers_forum_override_subject', 'features' ) )
				)
			)
		);

		$settings['schedule-topic'] = array(
			'schedule-topic'           => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'schedule-topic', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'schedule-topic', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'schedule-topic_available' => array(
				'name'     => __( "Schedule topics", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'schedule-topic__form_location', __( "Topic Form Position", "bbp-core" ), __( "Choose where the schedule topic block is displayed.", "bbp-core" ), d4pSettingType::SELECT, gdbbx()->get( 'schedule-topic__form_location', 'features' ), 'array', $this->data_form_position_topic() ),
					new d4pSettingElement( '', '', __( "Members allowed to schedule topics", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'schedule-topic__allow_super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'schedule-topic__allow_super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'schedule-topic__allow_roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'schedule-topic__allow_roles', 'features' ), 'array', gdbbx_get_user_roles(), array( 'class' => 'gdbbx-roles' ) )
				)
			)
		);

		$settings['close-topic-control'] = array(
			'close-topic-control'          => array(
				'name'     => __( "Feature Status", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'load', 'close-topic-control', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'close-topic-control', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
				)
			),
			'close-topic-control_settings' => array(
				'name'     => __( "Close topic checkbox in reply form", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'close-topic-control__topic_author', __( "Available to topic author", "bbp-core" ), __( "If the post is reply, this will take into account author of the topic too.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'close-topic-control__topic_author', 'features' ) ),
					new d4pSettingElement( 'features', 'close-topic-control__super_admin', __( "Available to super admin", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'close-topic-control__super_admin', 'features' ) ),
					new d4pSettingElement( 'features', 'close-topic-control__roles', __( "Available to roles", "bbp-core" ), '', d4pSettingType::CHECKBOXES, gdbbx()->get( 'close-topic-control__roles', 'features' ), 'array', $this->data_high_level_user_roles(), array( 'class' => 'gdbbx-roles' ) ),
					new d4pSettingElement( 'features', 'close-topic-control__form_position', __( "Form Position", "bbp-core" ), '', d4pSettingType::SELECT, gdbbx()->get( 'close-topic-control__form_position', 'features' ), 'array', $this->data_form_position_reply() )
				)
			),
			'close-topic-control_notify'   => array(
				'name'     => __( "Notifications", "bbp-core" ),
				'settings' => array(
					new d4pSettingElement( 'features', 'close-topic-control__notify_author', _x( "To author", "Sending email notification", "bbp-core" ), __( "Topic author will receive email notification when the topic is closed.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'close-topic-control__notify_author', 'features' ) ),
					new d4pSettingElement( 'features', 'close-topic-control__notify_subscribers', _x( "To subscribers", "Sending email notification", "bbp-core" ), __( "All topic subscribers will receive email notification when the topic is closed.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'close-topic-control__notify_subscribers', 'features' ) ),
					new d4pSettingElement( '', '', __( "Override notification content", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'close-topic-control__notify_active', __( "Modify notification content", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'close-topic-control__notify_active', 'features' ) ),
					new d4pSettingElement( 'features', 'close-topic-control__notify_content', __( "Notification content", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %FORUM_LINK%, %TOPIC_AUTHOR%, %CLOSED_USER%, %TOPIC_CONTENT%, %TOPIC_LINK%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::HTML, gdbbx()->get( 'close-topic-control__notify_content', 'features' ) ),
					new d4pSettingElement( 'features', 'close-topic-control__notify_subject', __( "Notification subject", "bbp-core" ), __( "You can use special tags that will be replaced with actual values", "bbp-core" ) . ': %FORUM_TITLE%, %CLOSED_USER%, %TOPIC_TITLE%, %BLOG_NAME%', d4pSettingType::TEXT, gdbbx()->get( 'close-topic-control__notify_subject', 'features' ) ),
					new d4pSettingElement( '', '', __( "Additional settings", "bbp-core" ), '', d4pSettingType::HR ),
					new d4pSettingElement( 'features', 'close-topic-control__notify_shortcodes', __( "Process shortcodes", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'close-topic-control__notify_shortcodes', 'features' ) )
				)
			)
		);

		if ( Plugin::instance()->buddypress ) {
			$settings['buddypress-tweaks'] = array(
				'buddypress-tweaks'          => array(
					'name'     => __( "Feature Status", "bbp-core" ),
					'settings' => array(
						new d4pSettingElement( 'load', 'buddypress-tweaks', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-tweaks', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
					)
				),
				'buddypress-tweaks-settings' => array(
					'name'     => __( "bbPress Profiles URL", "bbp-core" ),
					'settings' => array(
						new d4pSettingElement( 'features', 'buddypress-tweaks__disable_profile_override', __( "Overrides to BuddyPress", "bbp-core" ), __( "When BuddyPress is active, it will override bbPress profiles, and replace them with own profiles. If you prefer having forum profiles, use this option.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-tweaks__disable_profile_override', 'features' ), null, array(), array( 'label' => __( "Disable Overrides", "bbp-core" ) ) )
					)
				)
			);

			$settings['buddypress-notifications'] = array(
				'buddypress-notifications'          => array(
					'name'     => __( "Feature Status", "bbp-core" ),
					'settings' => array(
						new d4pSettingElement( 'load', 'buddypress-notifications', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-notifications', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
					)
				),
				'buddypress-notifications-settings' => array(
					'name'     => __( "Notifications", "bbp-core" ),
					'settings' => array(
						new d4pSettingElement( 'features', 'buddypress-notifications__thanks_received', __( "On Thanks Received", "bbp-core" ), __( "When the user receives thanks for own topic or reply, notification will be added to BuddyPress notifications system.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-notifications__thanks_received', 'features' ) ),
						new d4pSettingElement( 'features', 'buddypress-notifications__post_reported', __( "On Topic/Reply Report", "bbp-core" ), __( "When the topic or reply gets reported, notification will be added to BuddyPress notifications system for administrators and moderators.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-notifications__post_reported', 'features' ) )
					)
				)
			);

			$settings['buddypress-signature'] = array(
				'buddypress-signature'      => array(
					'name'     => __( "Feature Status", "bbp-core" ),
					'settings' => array(
						new d4pSettingElement( 'load', 'buddypress-signature', __( "Active", "bbp-core" ), __( "This feature will be loaded only if activated. If you don't need this feature, disable it.", "bbp-core" ), d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-signature', 'load' ), null, array(), array( 'label' => __( "Feature is active", "bbp-core" ) ) )
					)
				),
				'buddypress-signature-info' => array(
					'name'     => __( "Important Information", "bbp-core" ),
					'settings' => array(
						new d4pSettingElement( '', '', __( "Important", "bbp-core" ), __( "The plugin adds specialized 'Signature Textarea' field type. Please, do not use this field for other extended profile fields, it should be used only for the field created by this plugin.", "bbp-core" ), d4pSettingType::INFO )
					)
				)
			);

			if ( Plugin::instance()->is_enabled( 'signatures' ) ) {
				$_field_id = gdbbx()->get( 'buddypress-signature__xfield_id', 'features' );

				if ( $_field_id == 0 ) {
					$settings['buddypress-signature']['buddypress-signature-info']['settings'][] = new d4pSettingElement( '', '', __( "XProfile Field", "bbp-core" ), __( "The signature field for Extended profile is not added to the BuddyPress yet. Use the option below to create this field if you want for your users to be able to edit forum signature from their BuddyPress Extended profile.", "bbp-core" ), d4pSettingType::INFO );
					$settings['buddypress-signature']['buddypress-signature-info']['settings'][] = new d4pSettingElement( 'features', 'buddypress-signature__xfield_add', __( "Create Field", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-signature__xfield_add', 'features' ) );
				} else if ( ! gdbbx_buddypress_signature()->has_signature_field() ) {
					$settings['buddypress-signature']['buddypress-signature-info']['settings'][] = new d4pSettingElement( '', '', __( "XProfile Field", "bbp-core" ), __( "The signature field for Extended profile was created earlier, but it is missing now. Use the option below to create this field again if you want for your users to be able to edit forum signature from their BuddyPress Extended profile.", "bbp-core" ), d4pSettingType::INFO );
					$settings['buddypress-signature']['buddypress-signature-info']['settings'][] = new d4pSettingElement( 'features', 'buddypress-signature__xfield_add', __( "Create Field", "bbp-core" ), '', d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-signature__xfield_add', 'features' ) );
				} else {
					$settings['buddypress-signature']['buddypress-signature-info']['settings'][] = new d4pSettingElement( '', '', __( "XProfile Field", "bbp-core" ), __( "The signature field for Extended profile configured properly. You can modify the field to change it's name, but make sure it is always set to use 'Signature Textarea' field type, or the field will not work as expected.", "bbp-core" ), d4pSettingType::INFO );
					$settings['buddypress-signature']['buddypress-signature-info']['settings'][] = new d4pSettingElement( 'features', 'buddypress-signature__xfield_del', '', '', d4pSettingType::BOOLEAN, gdbbx()->get( 'buddypress-signature__xfield_del', 'features' ), '', '', array( 'label' => __( "Remove the signature field", "bbp-core" ) ) );
				}
			} else {
				$settings['buddypress-signature']['buddypress-signature-info']['settings'][] = new d4pSettingElement( '', '', __( "Signatures Disabled", "bbp-core" ), __( "The signatures module is disabled.", "bbp-core" ), d4pSettingType::INFO );
			}
		}

		return $settings;
	}

	public function get_features_for_display() : array {
		$list = array(
			'always'   => array(
				'icons',
				'tweaks',
				'shortcodes',
				'content-editor',
				'topic-actions',
				'reply-actions',
				'user-settings',
				'custom-views'
			),
			'enabled'  => array(),
			'disabled' => array()
		);

		foreach ( array_keys( $this->list ) as $feature ) {
			if ( in_array( $feature, $list['always'] ) ) {
				continue;
			}

			if ( gdbbx()->get( $feature, 'load' ) ) {
				$list['enabled'][] = $feature;
			} else {
				$list['disabled'][] = $feature;
			}
		}

		$out = array();

		$_added = false;
		foreach ( $list['always'] as $feature ) {
			$value           = $this->list[ $feature ];
			$value['status'] = 'required';

			if ( ! $_added ) {
				$value['break'] = __( "Always Enabled", "bbp-core" );
				$_added         = true;
			}

			$out[ $feature ] = $value;
		}

		$_added = false;
		foreach ( $list['enabled'] as $feature ) {
			$value           = $this->list[ $feature ];
			$value['status'] = 'enabled';

			if ( ! $_added ) {
				$value['break'] = __( "Enabled", "bbp-core" );
				$_added         = true;
			}

			$out[ $feature ] = $value;
		}

		$_added = false;
		foreach ( $list['disabled'] as $feature ) {
			$value           = $this->list[ $feature ];
			$value['status'] = 'disabled';

			if ( ! $_added ) {
				$value['break'] = __( "Disabled", "bbp-core" );
				$_added         = true;
			}

			$out[ $feature ] = $value;
		}

		return $out;
	}

	private function data_extra_features() : array {
		return array(
			'thumbnail'     => __( "Thumbnail", "bbp-core" ),
			'excerpt'       => __( "Excerpt", "bbp-core" ),
			'custom-fields' => __( "Custom Fields", "bbp-core" )
		);
	}

	private function data_site_public() : array {
		return array(
			'auto'    => __( "No change", "bbp-core" ),
			'public'  => __( "Site is public", "bbp-core" ),
			'private' => __( "Site is private", "bbp-core" )
		);
	}

	private function data_snippet_type() : array {
		return array(
			'Organization' => __( "Organization", "bbp-core" ),
			'Person'       => __( "Person", "bbp-core" )
		);
	}

	private function data_thanks_date_display() : array {
		return array(
			'no'   => __( "Don't show", "bbp-core" ),
			'date' => __( "Show date", "bbp-core" ),
			'age'  => __( "Show age", "bbp-core" )
		);
	}

	private function data_kses_allowed_tags_override() : array {
		return array(
			'bbpress'  => __( "Default bbPress list of tags and attributes", "bbp-core" ),
			'expanded' => __( "Expanded range of tags and attributes", "bbp-core" ),
			'post'     => __( "Wide range of tags and attributes as for WordPress posts", "bbp-core" )
		);
	}

	private function data_actions_location() : array {
		return array(
			'header' => __( "Header - bbPress Default", "bbp-core" ),
			'footer' => __( "Footer - Added by Toolbox Plugin", "bbp-core" ),
			'hide'   => __( "Hide", "bbp-core" )
		);
	}

	private function data_attachment_icon_method() : array {
		return array(
			'images' => __( "Images", "bbp-core" ),
			'font'   => __( "Font Icons", "bbp-core" )
		);
	}

	private function data_forum_index_filters() : array {
		return array(
			'before' => __( "Before Forum Index", "bbp-core" ),
			'after'  => __( "After Forum Index", "bbp-core" )
		);
	}

	private function data_active_users_period() : array {
		return array(
			0     => __( "Currently online", "bbp-core" ),
			30    => __( "Active in the past 30 minutes", "bbp-core" ),
			60    => __( "Active in the past 60 minutes", "bbp-core" ),
			120   => __( "Active in the past 2 hours", "bbp-core" ),
			720   => __( "Active in the past 12 hours", "bbp-core" ),
			1440  => __( "Active in the past 24 hours", "bbp-core" ),
			10080 => __( "Active in the past 7 days", "bbp-core" )
		);
	}

	private function data_disable_rss() : array {
		return array(
			'home'   => __( "Home Page", "bbp-core" ),
			'404'    => __( "Error 404 Page", "bbp-core" ),
			'forums' => __( "Main Forums Page", "bbp-core" ),
			'parent' => __( "Parent Page", "bbp-core" )
		);
	}

	private function data_quote_button_method() : array {
		return array(
			'bbcode' => __( "Shortcode", "bbp-core" ),
			'html'   => 'HTML'
		);
	}

	private function data_quote_bbcode() : array {
		return array(
			'quote'     => __( "Quote", "bbp-core" ),
			'postquote' => __( "Post Quote", "bbp-core" )
		);
	}

	private function data_signature_scopes() : array {
		return array(
			'global' => __( "Global for the whole network", "bbp-core" ),
			'blog'   => __( "Local for each blog", "bbp-core" )
		);
	}

	private function data_enhanced_editor_types() : array {
		return array(
			'textarea'        => __( "Normal Textarea", "bbp-core" ),
			'tinymce'         => __( "TinyMCE Full", "bbp-core" ),
			'tinymce_compact' => __( "TinyMCE Compact", "bbp-core" ),
			'bbcodes'         => __( "BBCodes Toolbar", "bbp-core" )
		);
	}

	private function data_enhanced_signature_method() : array {
		return array(
			'plain'  => __( "Plain Text", "bbp-core" ),
			'html'   => __( "HTML", "bbp-core" ),
			'bbcode' => __( "BBCodes", "bbp-core" ),
			'full'   => __( "HTML and BBCodes", "bbp-core" )
		);
	}

	private function data_report_mode() : array {
		return array(
			'form'    => __( "Standard form with required message", "bbp-core" ),
			'confirm' => __( "Simple confirmation dialog to send report", "bbp-core" ),
			'button'  => __( "Send report without any confirmation", "bbp-core" )
		);
	}

	private function data_private_checked_status() : array {
		return array(
			'unchecked' => __( "Unchecked", "bbp-core" ),
			'checked'   => __( "Checked", "bbp-core" )
		);
	}

	private function data_form_position_topic() : array {
		return array(
			'bbp_theme_before_topic_form_title'          => __( "Before title", "bbp-core" ),
			'bbp_theme_after_topic_form_title'           => __( "After title", "bbp-core" ),
			'bbp_theme_before_topic_form_content'        => __( "Before content", "bbp-core" ),
			'bbp_theme_after_topic_form_content'         => __( "After content", "bbp-core" ),
			'bbp_theme_before_topic_form_submit_wrapper' => __( "At the end", "bbp-core" )
		);
	}

	private function data_form_position_reply() : array {
		return array(
			'bbp_theme_before_reply_form_content'        => __( "Before content", "bbp-core" ),
			'bbp_theme_after_reply_form_content'         => __( "After content", "bbp-core" ),
			'bbp_theme_before_reply_form_submit_wrapper' => __( "At the end", "bbp-core" )
		);
	}

	private function data_high_level_user_roles() : array {
		return array(
			bbp_get_keymaster_role() => __( "Keymaster", "bbp-core" ),
			bbp_get_moderator_role() => __( "Moderator", "bbp-core" )
		);
	}

	private function data_bbcodes_attachment_caption() : array {
		return array(
			'hide'    => __( "Hide", "bbp-core" ),
			'auto'    => __( "Attachment caption or file name", "bbp-core" ),
			'caption' => __( "Attachment caption only", "bbp-core" )
		);
	}

	private function data_bbcodes_quote_titles() : array {
		return array(
			'hide' => __( "Hide", "bbp-core" ),
			'user' => __( "Quoted text author display name", "bbp-core" ),
			'id'   => __( "Quoted text topic or reply ID", "bbp-core" )
		);
	}

	private function data_redirect_visitor() : array {
		return array(
			'no'     => __( "No redirection", "bbp-core" ),
			'custom' => __( "Custom URL", "bbp-core" ),
			'login'  => __( "Login with redirect", "bbp-core" )
		);
	}

	private function data_redirect_blocked_visitor() : array {
		return array(
			'no'     => __( "No redirection", "bbp-core" ),
			'custom' => __( "Custom URL", "bbp-core" )
		);
	}

	private function data_attachments_method() : array {
		return array(
			'classic'  => __( "Classic", "bbp-core" ),
			'enhanced' => __( "Enhanced", "bbp-core" )
		);
	}

	private function data_attachment_topic_delete() : array {
		return array(
			'detach' => __( "Leave attachments in media library", "bbp-core" ),
			'delete' => __( "Delete attachments", "bbp-core" ),
			'nohing' => __( "Do nothing", "bbp-core" )
		);
	}

	private function data_attachment_delete_method() : array {
		return array(
			'edit'    => __( "Through edit pages", "bbp-core" ),
			'default' => __( "Via inline links", "bbp-core" ),
			'hide'    => __( "Hide deletion options", "bbp-core" )
		);
	}

	private function data_attachment_file_delete() : array {
		return array(
			'no'     => __( "Don't allow to delete", "bbp-core" ),
			'detach' => __( "Only detach from topic/reply", "bbp-core" ),
			'delete' => __( "Delete from Media Library", "bbp-core" ),
			'both'   => __( "Allow both delete and detach", "bbp-core" )
		);
	}

	private function data_files_position_topic() : array {
		return array(
			'content' => __( "Attach at the end of post content", "bbp-core" ),
			'after'   => __( "Place after the post content", "bbp-core" )
		);
	}

	private function data_files_display_mode() : array {
		return array(
			'list'  => __( "Show all files as list", "bbp-core" ),
			'thumb' => __( "Show all files as thumbnails", "bbp-core" ),
			'mixed' => __( "Show images as thumbnails, other files as list", "bbp-core" )
		);
	}

	private function data_forum_not_defined() : array {
		return array(
			'hide' => __( "Hide attachment uploader", "bbp-core" ),
			'show' => __( "Show attachment uploader with global settings", "bbp-core" )
		);
	}

	private function data_forum_thread_attachments_format() : array {
		return array(
			'list'       => __( "Files List", "bbp-core" ),
			'thumbnails' => __( "Thumbnails", "bbp-core" )
		);
	}

	private function data_forum_thread_attachments_actions() : array {
		return array(
			'skip'                             => __( "Do not add automatically", "bbp-core" ),
			'bbp_template_before_single_topic' => __( "Before the Topic", "bbp-core" ),
			'bbp_template_after_single_topic'  => __( "After the Topic", "bbp-core" )
		);
	}

	private function data_upload_dir_format() : array {
		return array(
			'/forums'            => '/forums',
			'/forums/user-id'    => '/forums/user-id',
			'/forums/forum-name' => '/forums/forum-name',
			'/forums/forum-id'   => '/forums/forum-id'
		);
	}

	private function data_anon_hash_source() : array {
		return array(
			'topic_id'   => __( "Topic ID", "bbp-core" ),
			'forum_id'   => __( "Forum ID", "bbp-core" ),
			'user_id'    => __( "Real user ID", "bbp-core" ),
			'user_email' => __( "Real user Email", "bbp-core" )
		);
	}

	private function data_anon_store_link() : array {
		return array(
			'limited'   => __( "Store link for limited number of days", "bbp-core" ),
			'unlimited' => __( "Store link for unlimited time", "bbp-core" ),
			'none'      => __( "Do not store link", "bbp-core" )
		);
	}

	private function data_bbpress_forums_list( $args = array() ) : array {
		$defaults = array(
			'post_type'   => bbp_get_forum_post_type(),
			'numberposts' => - 1,
		);

		$args = wp_parse_args( $args, $defaults );

		$_forums = get_posts( $args );

		$forums = array();

		foreach ( $_forums as $forum ) {
			$forums[ $forum->ID ] = (object) array(
				'id'     => $forum->ID,
				'url'    => get_permalink( $forum->ID ),
				'parent' => $forum->post_parent,
				'title'  => $forum->post_title
			);
		}

		return $forums;
	}

	private function data_bbcodes_replacement() : array {
		return array(
			'info'   => __( "When saved, replace with notice", "bbp-core" ),
			'delete' => __( "When saved, remove from content", "bbp-core" )
		);
	}

	private function data_bbcodes_enlighter_theme() : array {
		return array(
			'enlighter'  => __( "Default", "bbp-core" ),
			'classic'    => __( "Classic", "bbp-core" ),
			'atomic'     => __( "Atomic", "bbp-core" ),
			'beyond'     => __( "Beyond", "bbp-core" ),
			'bootstrap4' => __( "Bootstrap 4", "bbp-core" ),
			'dracula'    => __( "Dracula", "bbp-core" ),
			'droide'     => __( "Droide", "bbp-core" ),
			'eclipse'    => __( "Eclipse", "bbp-core" ),
			'godzilla'   => __( "Godzilla", "bbp-core" ),
			'minimal'    => __( "Minimal", "bbp-core" ),
			'monokai'    => __( "Monokai", "bbp-core" ),
			'mowtwo'     => __( "Mowtwo", "bbp-core" ),
			'rowhammer'  => __( "Rowhammer", "bbp-core" )
		);
	}

	private function data_bbcodes_heading() : array {
		return array(
			1 => 'H1',
			2 => 'H2',
			3 => 'H3',
			4 => 'H4',
			5 => 'H5',
			6 => 'H6'
		);
	}

	private function data_bbcodes_toolbar_size() : array {
		return array(
			'small'  => __( "Small", "bbp-core" ),
			'medium' => __( "Medium", "bbp-core" ),
			'large'  => __( "Large", "bbp-core" )
		);
	}

	private function data_available_editors() : array {
		return array(
			'textarea' => __( "Basic Textarea (Default)", "bbp-core" ),
			'richarea' => __( "Quicktags Textarea", "bbp-core" ),
			'tinymce'  => __( "TinyMCE Rich Editor", "bbp-core" ),
			'bbcodes'  => __( "BBCodes Toolbar with Basic Textarea", "bbp-core" )
		);
	}

	private function info_upload_dir() : string {
		$items = array(
			__( "Enabling or disabling this feature doesn't affect any of the attachments that are uploaded previously.", "bbp-core" ),
			__( "There is no reliable way to move already uploaded files to newly activated upload directory structure.", "bbp-core" ),
			__( "Uploads are still located in the base website uploads directory, that can't be changed, changes are only for directory under the uploads directory.", "bbp-core" ),
			__( "This only affects the location of the files in the file system, it has no impact on the files visibility or accessibility.", "bbp-core" )
		);

		return '<ul><li>' . join( '</li><li>', $items ) . '</li></ul>';
	}

	private function info_upload_dir_format() : string {
		$items = array(
			'&middot; <strong>forums</strong>: ' . __( "Replaced by the base directory name set above.", "bbp-core" ),
			'&middot; <strong>user-id</strong>: ' . __( "Replaced by the ID of the user upload the file.", "bbp-core" ),
			'&middot; <strong>forum-id</strong>: ' . __( "Replaced by the ID of the current forum.", "bbp-core" ),
			'&middot; <strong>forum-name</strong>: ' . __( "Replaced by the slug of the current forum.", "bbp-core" )
		);

		return '<br/>' . join( '<br/>', $items );
	}
}
