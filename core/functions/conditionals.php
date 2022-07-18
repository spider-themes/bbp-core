<?php

use Dev4Press\Plugin\GDBBX\Basic\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

function gdbbx_is_module_loaded($module) : bool {
    return isset(gdbbx_plugin()->modules[$module]);
}

function gdbbx_is_topic_locked($topic_id = 0) : bool {
    return gdbbx_lock_forums()->is_topic_locked($topic_id);
}

function gdbbx_is_reply_locked($topic_id = 0) : bool {
    return gdbbx_lock_forums()->is_reply_locked($topic_id);
}

function gdbbx_is_topic_private($topic_id = 0) : bool {
    if ( Plugin::instance()->is_enabled('private-topics')) {
        return gdbbx_private_topics()->is_private($topic_id);
    } else {
        return false;
    }
}

function gdbbx_is_reply_private($reply_id = 0) : bool {
    if ( Plugin::instance()->is_enabled('private-replies')) {
        return gdbbx_private_replies()->is_private($reply_id);
    } else {
        return false;
    }
}

function gdbbx_is_user_allowed_to_topic($topic_id = 0, $user_id = 0) : bool {
    if ( Plugin::instance()->is_enabled('private-topics')) {
        return gdbbx_private_topics()->is_user_allowed($topic_id, $user_id);
    } else {
        return true;
    }
}

function gdbbx_is_user_allowed_to_reply($reply_id = 0, $user_id = 0) : bool {
    if ( Plugin::instance()->is_enabled('private-replies')) {
        return gdbbx_private_replies()->is_user_allowed($reply_id, $user_id);
    } else {
        return true;
    }
}
