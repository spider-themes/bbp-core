<?php

if (!defined('ABSPATH')) { exit; }

class gdbbx_admin_settings {
    private $settings;

    function __construct() {
        $this->init();
    }

    public function get($panel, $group = '') {
        if ($group == '') {
            return $this->settings[$panel];
        } else {
            return $this->settings[$panel][$group];
        }
    }

    public function features_load() {
        $list = array();

        foreach ($this->settings as $block) {
            foreach ($block as $obj) {
                foreach ($obj['settings'] as $o) {
                    if ($o->type == 'load') {
                        $list[] = $o;
                    }
                }
            }
        }

        return $list;
    }

    public function settings($panel) {
        $list = array();

        foreach ($this->settings[$panel] as $obj) {
            foreach ($obj['settings'] as $o) {
                $list[] = $o;
            }
        }

        return $list;
    }

    private function init() {
        $this->settings = array(
            'files' => array(
                'files_libraries' => array('name' => __("Additional Libiraries", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('settings', 'load_fitvids', __("FitVids", "bbp-core"), __("Load FitVids library for making YouTube and Vimeo videos responsive. If you already load this library in some other way, disable this option to avoid duplication.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('load_fitvids'))
                )),
                'files_loading' => array('name' => __("CSS and JS files loading", "bbp-core"), 'settings' => array(
	                new d4pSettingElement('settings', 'load_bulk_js', __("Load Bulk JS", "bbp-core"), __("Load most of the JS as one single file, instead of loading individual JS components files. Bulk file replaces 3 individual JS files.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('load_bulk_js')),
	                new d4pSettingElement('settings', 'load_bulk_css', __("Load Bulk CSS", "bbp-core"), __("Load most of the CSS as one single file, instead of loading individual CSS components files. Bulk file replaces 5 individual CSS files. RTL support is loaded as additional file.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('load_bulk_css')),
	                new d4pSettingElement('settings', 'font_icons_embedded', __("Embedded Icons Font", "bbp-core"), __("Load the font with icons version of the file that has WOFF and WOFF2 fonts embedded into CSS. This will improve the font loading, and eliminate this font as render blocking.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('font_icons_embedded'))
                )),
                'files_advanced' => array('name' => __("Advanced loading settings", "bbp-core"), 'settings' => array(
	                new d4pSettingElement('settings', 'load_always', __("Always Load", "bbp-core"), __("If you use shortcodes to embed forums, and you rely on plugin to add JS and CSS, you also need to enable this option to skip checking for bbPress specific pages.", "bbp-core").' '.__("This option is not needed anymore, but if you still have issues with loaded files, enable it.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('load_always'))
                ))
            ),
            'widgets' => array(
                'widgets' => array('name' => __("Plugin Widgets", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('widgets', 'widget_userprofile', __("User Profile", "bbp-core"), __("Logged in user profile with useful links and stats.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_userprofile', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_usersthanks', __("Top Thanked Users", "bbp-core"), __("Logged in user profile with useful links and stats.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_usersthanks', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_statistics', __("Statistics", "bbp-core"), __("Enhanced list of important forum statistics.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_statistics', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_topicinfo', __("Topic Information", "bbp-core"), __("Show information about the topic currently displayed.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_topicinfo', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_foruminfo', __("Forum Information", "bbp-core"), __("Show information about the forum currently displayed.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_foruminfo', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_search', __("Search", "bbp-core"), __("Expanded search widget with option to search current forum only.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_search', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_onlineusers', __("Online Users", "bbp-core"), __("Show the list of users currently online.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_onlineusers', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_newposts', __("New posts List", "bbp-core"), __("List of new topics or topics with new replies.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_newposts', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'widget_topicsviews', __("Topics Views List", "bbp-core"), __("Selectable list of topics views.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('widget_topicsviews', 'widgets'), null, array(), array('label' => __("Enable Widget", "bbp-core")))
                )),
                'default_widgets' => array('name' => __("Default bbPress Widgets", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('widgets', 'default_disable_recenttopics', __("Recent Topics", "bbp-core"), __("If you use this plugin 'New Posts List' widget, you can disable default one.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('default_disable_recenttopics', 'widgets'), null, array(), array('label' => __("Disable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'default_disable_recentreplies', __("Recent Replies", "bbp-core"), __("If you use this plugin 'New Posts List' widget, you can disable default one.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('default_disable_recentreplies', 'widgets'), null, array(), array('label' => __("Disable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'default_disable_topicviewslist', __("Topics Views List", "bbp-core"), __("If you use this plugin 'Topics Views List' widget, you can disable default one.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('default_disable_topicviewslist', 'widgets'), null, array(), array('label' => __("Disable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'default_disable_login', __("Login", "bbp-core"), __("If you use this plugin 'User Profile' widget, you can disable default one.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('default_disable_login', 'widgets'), null, array(), array('label' => __("Disable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'default_disable_search', __("Search", "bbp-core"), __("If you use this plugin 'Search' widget, you can disable default one.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('default_disable_search', 'widgets'), null, array(), array('label' => __("Disable Widget", "bbp-core"))),
                    new d4pSettingElement('widgets', 'default_disable_stats', __("Statistics", "bbp-core"), __("If you use this plugin 'Statistics' widget, you can disable default one.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('default_disable_stats', 'widgets'), null, array(), array('label' => __("Disable Widget", "bbp-core")))
                ))
            ),
            'forum_read' => array(
                'forum_read_new_posts' => array('name' => __("New Posts", "bbp-core"),
                    'kb' => array('label' => __("KB", "bbp-core"), 'url' => 'forums-logged-in-users-read-tracking'), 'settings' => array(
                    new d4pSettingElement('', '', __("Important", "bbp-core"), __("If the new topic or reply is posted since the last user visit, forum this topic belongs to, will be marked. For this to work, you need to enable user activity tracking.", "bbp-core"), d4pSettingType::INFO),
                    new d4pSettingElement('', '', '', '', d4pSettingType::HR),
                    new d4pSettingElement('tools', 'latest_forum_new_posts_badge', __("Add new posts badge", "bbp-core"), __("Add badge before the forum title.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_forum_new_posts_badge', 'tools')),
                    new d4pSettingElement('tools', 'latest_forum_new_posts_strong_title', __("Wrap title in strong tag", "bbp-core"), __("Wrap the forum title in the STRONG to attempt display it as bold to stand out in the list.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_forum_new_posts_strong_title', 'tools'))
                )),
                'forum_read_unread_forum' => array('name' => __("Unread Forum", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('', '', __("Important", "bbp-core"), __("If the forum is not read by the user (taking into account the cutoff timestamp), forum will be marked as unread.", "bbp-core"), d4pSettingType::INFO),
                    new d4pSettingElement('', '', '', '', d4pSettingType::HR),
                    new d4pSettingElement('tools', 'latest_forum_unread_forum_badge', __("Add unread forum badge", "bbp-core"), __("Add badge before the forum title.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_forum_unread_forum_badge', 'tools')),
                    new d4pSettingElement('tools', 'latest_forum_unread_forum_strong_title', __("Wrap title in strong tag", "bbp-core"), __("Wrap the forum title in the STRONG to attempt display it as bold to stand out in the list.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_forum_unread_forum_strong_title', 'tools'))
                ))
            ),
            'topic_read' => array(
                'topic_read_tracking' => array('name' => __("User read status tracking", "bbp-core"),
                    'kb' => array('label' => __("KB", "bbp-core"), 'url' => 'topics-logged-in-users-read-tracking'), 'settings' => array(
                    new d4pSettingElement('tools', 'latest_track_users_topic', __("Active", "bbp-core"), __("Track users access to topics, latest reply for topic and use it to mark unread content.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_track_users_topic', 'tools')),
                    new d4pSettingElement('tools', 'latest_use_cutoff_timestamp', __("Use cutoff timestamp", "bbp-core"), __("Tracking data begins storing when plugin version 4.5 is installed. This moment will be stored to serve as cutoff for displaying unread topics to users. If this is not used, all old topics will be initially marked as 'unread' to all users.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_use_cutoff_timestamp', 'tools'))
                )),
                'topic_read_new_replies' => array('name' => __("New Replies", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('', '', __("Important", "bbp-core"), __("If one or more new replies are added to the topic since the last time user visited a topic, this topic will be marked and link placed to lead to the first new reply for the current user.", "bbp-core"), d4pSettingType::INFO),
                    new d4pSettingElement('', '', '', '', d4pSettingType::HR),
                    new d4pSettingElement('tools', 'latest_topic_new_replies_badge', __("Add new reply badge", "bbp-core"), __("Add badge before the topic title.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_new_replies_badge', 'tools')),
                    new d4pSettingElement('tools', 'latest_topic_new_replies_mark', __("Add new replies icon", "bbp-core"), __("Add icon and link to the first new reply in the topic.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_new_replies_mark', 'tools')),
                    new d4pSettingElement('tools', 'latest_topic_new_replies_strong_title', __("Wrap title in strong tag", "bbp-core"), __("Wrap the topic title in the STRONG to attempt display it as bold to stand out in the list.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_new_replies_strong_title', 'tools')),
                    new d4pSettingElement('tools', 'latest_topic_new_replies_in_thread', __("Mark replies in topic thread", "bbp-core"), __("When topic is opened, all new replies will get a 'new reply' badge.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_new_replies_in_thread', 'tools'))
                )),
                'topic_read_new_topics' => array('name' => __("New Topics", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('', '', __("Important", "bbp-core"), __("If the new topic is posted since the last user visit, they will be marked. For this to work, you need to enable user activity tracking.", "bbp-core"), d4pSettingType::INFO),
                    new d4pSettingElement('', '', '', '', d4pSettingType::HR),
                    new d4pSettingElement('tools', 'latest_topic_new_topic_badge', __("Add new topic badge", "bbp-core"), __("Add badge before the topic title.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_new_topic_badge', 'tools')),
                    new d4pSettingElement('tools', 'latest_topic_new_topic_strong_title', __("Wrap title in strong tag", "bbp-core"), __("Wrap the topic title in the STRONG to attempt display it as bold to stand out in the list.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_new_topic_strong_title', 'tools'))
                )),
                'topic_read_unread_topics' => array('name' => __("Unread Topics", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('', '', __("Important", "bbp-core"), __("If the topic is not read by the user (taking into account the cutoff timestamp), it will be marked as unread.", "bbp-core"), d4pSettingType::INFO),
                    new d4pSettingElement('', '', '', '', d4pSettingType::HR),
                    new d4pSettingElement('tools', 'latest_topic_unread_topic_badge', __("Add unread topic badge", "bbp-core"), __("Add badge before the topic title.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_unread_topic_badge', 'tools')),
                    new d4pSettingElement('tools', 'latest_topic_unread_topic_strong_title', __("Wrap title in strong tag", "bbp-core"), __("Wrap the topic title in the STRONG to attempt display it as bold to stand out in the list.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_topic_unread_topic_strong_title', 'tools'))
                ))
            ),
            'tracking' => array(
                'user_activity' => array('name' => __("User activity tracking", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('tools', 'track_last_activity_active', __("Active", "bbp-core"), __("Everytime user opens any forum, topic or reply page plugin will save activity timestamp.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('track_last_activity_active', 'tools'))
                )),
                'user_tracking' => array('name' => __("Advanced user tracking", "bbp-core"), 'settings' => array(
	                new d4pSettingElement('tools', 'latest_track_active', __("Active", "bbp-core"), __("This is advanced tracking that covers tracking of read status for topics and forums, and read statuses. This type of tracking depends on the cookies, and you need to configure activity and session cookies expiration too.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('latest_track_active', 'tools')),
	                new d4pSettingElement('', '', __("Cookies expiration", "bbp-core"), '', d4pSettingType::HR),
	                new d4pSettingElement('tools', 'track_basic_cookie_expiration', __("Activity tracking cookie", "bbp-core"), __("Value is in days.", "bbp-core"), d4pSettingType::NUMBER, gdbbx()->get('track_basic_cookie_expiration', 'tools')),
	                new d4pSettingElement('tools', 'track_current_session_cookie_expiration', __("Current session cookie", "bbp-core"), __("Value is in minutes.", "bbp-core"), d4pSettingType::NUMBER, gdbbx()->get('track_current_session_cookie_expiration', 'tools'))
                )),
                'online_status' => array('name' => __("Track online status for users and guests", "bbp-core"), 'settings' => array(
                    new d4pSettingElement('online', 'active', __("Active", "bbp-core"), '', d4pSettingType::BOOLEAN, gdbbx()->get('active', 'online')),
                    new d4pSettingElement('online', 'track_users', __("Track Users", "bbp-core"), __("If enabled, plugin will track online status logged in users.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('track_users', 'online')),
                    new d4pSettingElement('online', 'track_guests', __("Track Guests", "bbp-core"), __("If enabled, plugin will track online status for guests - users that are not logged in.", "bbp-core").' '.__("This type of tracking depends on the special tracking cookie used for visitors only.", "bbp-core"), d4pSettingType::BOOLEAN, gdbbx()->get('track_guests', 'online')),
                    new d4pSettingElement('online', 'window', __("Online period", "bbp-core"), __("Value is in seconds.", "bbp-core"), d4pSettingType::INTEGER, gdbbx()->get('window', 'online')),
                    new d4pSettingElement('', '', __("Notices with online counts", "bbp-core"), '', d4pSettingType::HR),
                    new d4pSettingElement('', '', __("Description", "bbp-core"), __("Notices are displayed on top of the page, and they will show number of users and guests currently viewing the forum, topic, profile or view.", "bbp-core"), d4pSettingType::INFO),
                    new d4pSettingElement('online', 'notice_for_forum', __("For Forums", "bbp-core"), '', d4pSettingType::BOOLEAN, gdbbx()->get('notice_for_forum', 'online')),
                    new d4pSettingElement('online', 'notice_for_topic', __("For Topics", "bbp-core"), '', d4pSettingType::BOOLEAN, gdbbx()->get('notice_for_topic', 'online')),
                    new d4pSettingElement('online', 'notice_for_view', __("For Topics Views", "bbp-core"), '', d4pSettingType::BOOLEAN, gdbbx()->get('notice_for_view', 'online')),
                    new d4pSettingElement('online', 'notice_for_profile', __("For User Profiles", "bbp-core"), '', d4pSettingType::BOOLEAN, gdbbx()->get('notice_for_profile', 'online'))
                ))
            )
        );

        $this->settings = apply_filters('gdbbx_internal_settings', $this->settings);
    }
}
