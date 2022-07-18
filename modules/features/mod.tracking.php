<?php

use Dev4Press\Plugin\GDBBX\Basic\Enqueue;

if (!defined('ABSPATH')) { exit; }

class gdbbx_mod_tracking {
    public $trackers = array();
    public $forums = array();
    public $thread_reply_id = null;
    public $cutoff = 0;
    public $user_id;

    function __construct() {
	    if (!is_admin()) {
		    if (gdbbx()->get('latest_use_cutoff_timestamp', 'tools')) {
			    $this->cutoff = gdbbx()->get_core('unread_cutoff');
		    }

		    $this->user_id = bbp_get_current_user_id();

		    if ($this->user_id > 0 && gdbbx()->get('track_last_activity_active', 'tools')) {
			    add_action('bbp_template_before_single_forum', array($this, 'user_last_active'));
			    add_action('bbp_template_before_single_reply', array($this, 'user_last_active'));
			    add_action('bbp_template_before_single_topic', array($this, 'user_last_active'));
			    add_action('bbp_template_before_lead_topic', array($this, 'user_last_active'));
			    add_action('bbp_template_before_user_wrapper', array($this, 'user_last_active'));
		    }

		    if (gdbbx()->get('latest_track_active', 'tools')) {
			    if (is_user_logged_in() && gdbbx()->get('latest_track_users_topic', 'tools')) {
				    add_action('bbp_template_after_single_topic', array($this, 'latest_users_topic'));
				    add_action('bbp_theme_before_topic_title', array($this, 'latest_topic_before'), 1);
				    add_action('bbp_theme_before_forum_title', array($this, 'latest_forum_before'), 1);
				    add_action('gdbbx_template_before_forums_loop', array($this, 'before_forums_loop'), 10, 1);
				    add_action('gdbbx_template_before_topics_loop', array($this, 'before_topics_loop'), 10, 2);
			    }

			    if (is_user_logged_in() && gdbbx()->get('latest_topic_new_replies_in_thread', 'tools')) {
				    add_action('bbp_template_before_replies_loop', array($this, 'new_replies_thread'));
			    }

			    $this->current_session_cookie();

			    $this->visit_tracking_cookie();
		    }
	    }
    }

    public function user_last_active() {
        if ($this->user_id > 0) {
            gdbbx_plugin()->update_user_last_activity($this->user_id, $this->timestamp());
        }
    }

    public function before_forums_loop($forums) {
        gdbbx_cache()->tracking_run_bulk_forums();
        gdbbx_cache()->tracking_forums_activity();
        gdbbx_cache()->tracking_forums_user_activity($this->user_id);
    }

    public function before_topics_loop($posts, $users) {
        gdbbx_cache()->tracking_run_bulk_topics($posts);

        $replies = gdbbx_db_bulk()->list_topic_replies($posts);

        $items = array_merge($posts, $replies);
        $items = array_map('absint', $items);
        $items = array_unique($items);

        sort($items);

        d4p_posts_cache_by_ids($items);
        update_meta_cache('post', $items);
    }

    private function _cookie_tracking() {
        return gdbbx_db()->prefix().'gdbbx_tracking_activity';
    }

    private function _cookie_session() {
        return gdbbx_db()->prefix().'gdbbx_session_activity';
    }

    public function timestamp() {
        return gdbbx_db()->timestamp();
    }

    public function calculate_the_f() {
        $forum_id = bbp_get_forum_id();
        $forums = gdbbx_get_forum_children_ids($forum_id);
        $forums[] = $forum_id;

        $visitd = gdbbx_cache()->get_forum_user_latest_activity($forums);
        $latest = gdbbx_cache()->get_forum_latest_activity($forum_id);

        $F = apply_filters('gdbbx_user_forum_visit_control', array(
            'user_last_activity' => GDBBX_LAST_ACTIVTY,
            'user_last_visit' => $visitd,
            'user_visited' => $visitd !== false,
            'forum_last_active' => $latest,
            'forum_is_unread' => $latest !== false &&
                $latest > $this->cutoff &&
                $visitd === false,
            'forum_has_new_posts' => $visitd !== false &&
                $visitd < $latest
        ));

        $this->forums[$forum_id] = $F;

        return $F;
    }

    public function calculate_the_t() {
        $user_id = $this->user_id;
        $topic_id = bbp_get_topic_id();

        $last_visit = gdbbx_cache()->tracking_topic_last_visit($topic_id, $user_id);

        $author = bbp_get_topic_author_id($topic_id);
        $visitd = $last_visit !== false ? $last_visit->latest : false;
        $replie = $last_visit !== false ? $last_visit->reply_id : 0;
        $latest = gdbbx_get_topic_last_reply_time($topic_id);
        $topics = gdbbx_get_topic_post_time($topic_id);

        $T = array(
            'user_last_activity' => GDBBX_LAST_ACTIVTY,
            'user_last_visit' => $visitd,
            'user_visited' => $visitd !== false,
            'topic_first_active' => $topics,
            'topic_last_active' => $latest,
            'topic_last_reply' => $replie == 0 ? 
                bbp_get_topic_last_reply_id($topic_id) : 
                gdbbx_db()->get_topic_next_reply_id($topic_id, $replie),
            'topic_is_unread' => $latest > $this->cutoff &&
                $visitd === false && 
                $author != $user_id,
            'topic_is_new' => GDBBX_LAST_ACTIVTY > 0 && 
                $topics > GDBBX_LAST_ACTIVTY && 
                $visitd === false && 
                $author != $user_id,
            'topic_has_new_replies' => $visitd !== false && 
                $visitd < $latest
        );

        if ($T['topic_last_reply'] == 0) {
            $T['topic_has_new_replies'] = false;
        }

        $T = apply_filters('gdbbx_user_topic_visit_control', $T, $user_id, $topic_id);

        if (!$T['topic_has_new_replies']) {
            $T['topic_has_new_replies'] = $visitd === false && $user_id === $author && $T['topic_last_reply'] > 0 && $T['topic_last_active'] > $T['user_last_activity'];
        }
        
        $this->trackers[$topic_id] = $T;

        return $T;
    }

    public function new_replies_thread() {
        $T = $this->calculate_the_t();

        if ($T['topic_has_new_replies']) {
            $this->thread_reply_id = (int)$T['topic_last_reply'];

            add_action('bbp_theme_after_reply_admin_links', array($this, 'topic_thread_reply'));
        }
    }

    public function topic_thread_reply() {
        if (bbp_get_reply_id() >= $this->thread_reply_id) {
            echo apply_filters('gdbbx_reply_badge_new', 
                        '<span title="'.__("New Reply", "bbp-core").'" class="gdbbx-badge-new-reply">'._x("new", "Badge for reply in the single topic.", "bbp-core").'</span>');

            Enqueue::instance()->core();
        }
    }

    public function latest_forum_before() {
        $_strong = false;
        $_enqueue = false;

        $forum_id = bbp_get_forum_id();

        $F = $this->calculate_the_f();

        if ($F['forum_has_new_posts']) {
            if (gdbbx()->get('latest_forum_new_posts_strong_title', 'tools')) {
                $_strong = true;
            }

            if (gdbbx()->get('latest_forum_new_posts_badge', 'tools')) {
                echo apply_filters('gdbbx_forum_badge_new_posts', 
                        '<span title="'.__("Forum has new posts", "bbp-core").'" class="gdbbx-badge-new-posts">'._x("new posts", "Badge for new posts in the forums list.", "bbp-core").'</span>', 
                        $forum_id, $F);
                $_enqueue = true;
            }
        } else if ($F['forum_is_unread']) {
            if (gdbbx()->get('latest_forum_unread_forum_strong_title', 'tools')) {
                $_strong = true;
            }

            if (gdbbx()->get('latest_forum_unread_forum_badge', 'tools')) {
                echo apply_filters('gdbbx_forum_badge_unread', 
                        '<span title="'.__("Unread Forum", "bbp-core").'" class="gdbbx-badge-unread-forum">'._x("unread", "Badge for unread topics in the forums list.", "bbp-core").'</span>', 
                        $forum_id, $F);
                $_enqueue = true;
            }
        }

        if ($_strong) {
            add_action('bbp_theme_before_forum_title', array($this, 'title_strong_before'), 10000);
            add_action('bbp_theme_after_forum_title', array($this, 'title_strong_after'), 1);
        }

        if ($_enqueue) {
            Enqueue::instance()->core();
        }
    }

    public function latest_topic_before() {
        $_strong = false;
        $_enqueue = false;

        $user_id = bbp_get_current_user_id();
        $topic_id = bbp_get_topic_id();

        $T = $this->calculate_the_t();

        if ($T['topic_is_new']) {
            if (gdbbx_db()->user_replied_to_topic($topic_id, $user_id)) {
                $T['topic_is_new'] = false;
            }
        }

        if ($T['topic_is_new']) {
            if (gdbbx()->get('latest_topic_new_topic_strong_title', 'tools')) {
                $_strong = true;
            }

            if (gdbbx()->get('latest_topic_new_topic_badge', 'tools')) {
                echo apply_filters('gdbbx_topic_badge_new', 
                        '<span title="'.__("New Topic", "bbp-core").'" class="gdbbx-badge-new-topic">'._x("new", "Badge for new topics in the topics list.", "bbp-core").'</span>', 
                        $topic_id, $T);
                $_enqueue = true;
            }
        } else if ($T['topic_is_unread']) {
            if (gdbbx()->get('latest_topic_unread_topic_strong_title', 'tools')) {
                $_strong = true;
            }

            if (gdbbx()->get('latest_topic_unread_topic_badge', 'tools')) {
                echo apply_filters('gdbbx_topic_badge_unread', 
                        '<span title="'.__("Unread Topic", "bbp-core").'" class="gdbbx-badge-unread-topic">'._x("unread", "Badge for unread topics in the topics list.", "bbp-core").'</span>', 
                        $topic_id, $T);
                $_enqueue = true;
            }
        }

        if ($T['topic_has_new_replies']) {
            if (gdbbx()->get('latest_topic_new_replies_strong_title', 'tools')) {
                $_strong = true;
            }

            if (gdbbx()->get('latest_topic_new_replies_badge', 'tools')) {
                echo apply_filters('gdbbx_new_reply_badge_unread', 
                        '<span title="'.__("Topic has new replies", "bbp-core").'" class="gdbbx-badge-new-reply-topic">'._x("new reply", "Badge for new replies in the topics list.", "bbp-core").'</span>', 
                        $topic_id, $T);
                $_enqueue = true;
            }

            if (gdbbx()->get('latest_topic_new_replies_mark', 'tools')) {
                add_action('bbp_theme_after_topic_title', array($this, 'title_new_replies_mark'));
            }
        }

        if ($_strong) {
            add_action('bbp_theme_before_topic_title', array($this, 'title_strong_before'), 10000);
            add_action('bbp_theme_after_topic_title', array($this, 'title_strong_after'), 1);
        }

        if ($_enqueue) {
            Enqueue::instance()->core();
        }
    }

    public function title_strong_before() {
        echo '<strong>';

        remove_action('bbp_theme_before_forum_title', array($this, 'title_strong_before'), 10000);
        remove_action('bbp_theme_before_topic_title', array($this, 'title_strong_before'), 10000);
    }

    public function title_strong_after() {
        echo '</strong>';

        remove_action('bbp_theme_after_forum_title', array($this, 'title_strong_after'), 1);
        remove_action('bbp_theme_after_topic_title', array($this, 'title_strong_after'), 1);
    }

    public function title_new_replies_mark() {
        $topic_id = bbp_get_topic_id();
        $reply_id = $this->trackers[$topic_id]['topic_last_reply'];

        if ($reply_id > 0) {
            $url = bbp_get_reply_url($reply_id);

            echo apply_filters('gdbbx_topic_badge_new_replies',
                '<a title="'.__("First new reply", "bbp-core").'" class="gdbbx-new-topic-replies" href="'.$url.'">'.gdbbx_signs()->new_replies().'</a>',
                $topic_id, $url, $this->trackers[$topic_id]);

            Enqueue::instance()->core();
        }

        remove_action('bbp_theme_after_topic_title', array($this, 'title_new_replies_mark'));
    }

    public function latest_users_topic() {
        $user_id = $this->user_id;

        $topic_id = bbp_get_topic_id();
        $forum_id = bbp_get_forum_id();
        $reply_id = bbp_get_topic_last_reply_id($topic_id);

        if ($user_id != 0 && $topic_id != 0 && $forum_id != 0) {
            gdbbx_db()->track_topic_visit($user_id, $topic_id, $forum_id, $reply_id);
        }
    }

    public function current_session_cookie() {
        $activity = 0;

        if (!isset($_COOKIE[$this->_cookie_session()])) {
            global $userdata;

            $user_id = isset($userdata) ? $userdata->ID : 0;

            if ($user_id > 0) {
                $activity = gdbbx_plugin()->get_user_last_activity($user_id);
            } else {
                if (isset($_COOKIE[$this->_cookie_tracking()])) {
                    $activity = intval($_COOKIE[$this->_cookie_tracking()]);
                }
            }

            setcookie($this->_cookie_session(), $activity, gdbbx()->session_cookie_expiration(), '/', COOKIE_DOMAIN);
        } else {
            $activity = $_COOKIE[$this->_cookie_session()];
        }

        define('GDBBX_LAST_ACTIVTY', intval($activity));
    }

    public function visit_tracking_cookie() {
        setcookie($this->_cookie_tracking(), $this->timestamp(), gdbbx()->tracking_cookie_expiration(), '/', COOKIE_DOMAIN);
    }
}
