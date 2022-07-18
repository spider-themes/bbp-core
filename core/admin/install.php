<?php

if (!defined('ABSPATH')) { exit; }

function gdbbx_list_database_tables() {
    global $wpdb;

    return array(
        $wpdb->prefix.'gdbbx_actionmeta' => 4,
        $wpdb->prefix.'gdbbx_actions' => 6,
        $wpdb->prefix.'gdbbx_attachments' => 2,
        $wpdb->prefix.'gdbbx_online' => 10,
        $wpdb->prefix.'gdbbx_tracker' => 5
    );
}

function gdbbx_install_database() {
    global $wpdb;

    $charset_collate = '';

    if (!empty($wpdb->charset)) {
        $charset_collate = "default CHARACTER SET $wpdb->charset";
    }

    if (!empty($wpdb->collate)) {
        $charset_collate.= " COLLATE $wpdb->collate";
    }

    $tables = array(
        'actionmeta' => $wpdb->prefix.'gdbbx_actionmeta',
        'actions' => $wpdb->prefix.'gdbbx_actions',
        'attachments' => $wpdb->prefix.'gdbbx_attachments',
        'online' => $wpdb->prefix.'gdbbx_online',
        'tracker' => $wpdb->prefix.'gdbbx_tracker'
    );

    $query = "CREATE TABLE ".$tables['actionmeta']." (
meta_id bigint(20) unsigned NOT NULL auto_increment,
action_id bigint(20) unsigned NOT NULL default '0',
meta_key varchar(128) NULL default NULL,
meta_value longtext NULL,
PRIMARY KEY  (meta_id),
KEY action_id (action_id),
KEY meta_key (meta_key)
) ".$charset_collate.";

CREATE TABLE ".$tables['actions']." (
action_id bigint(20) unsigned NOT NULL auto_increment,
user_id bigint(20) unsigned NOT NULL default '0',
post_id bigint(20) unsigned NOT NULL default '0',
logged datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'gmt',
action varchar(128) NOT NULL default '',
reference_id bigint(20) unsigned NOT NULL default '0',
PRIMARY KEY  (action_id),
KEY user_id (user_id),
KEY post_id (post_id),
KEY action (action),
KEY reference_id (reference_id)
) ".$charset_collate.";

CREATE TABLE ".$tables['attachments']." (
post_id bigint(20) unsigned NOT NULL default '0',
attachment_id bigint(20) unsigned NOT NULL default '0',
UNIQUE KEY post_id_attachment_id (post_id,attachment_id),
KEY post_id (post_id),
KEY attachment_id (attachment_id)
) ".$charset_collate.";

CREATE TABLE ".$tables['online']." (
id bigint(20) unsigned NOT NULL auto_increment,
logged timestamp NOT NULL default CURRENT_TIMESTAMP,
user_type varchar(20) NOT NULL default '',
user_key varchar(20) NOT NULL default '',
user_role varchar(128) NOT NULL default '',
content varchar(20) NOT NULL default 'topic' COMMENT 'topic,forum,view,profile',
forum_id bigint(20) unsigned NOT NULL default '0',
topic_id bigint(20) unsigned NOT NULL default '0',
profile_id bigint(20) unsigned NOT NULL default '0',
topic_view varchar(128) NOT NULL default '',
PRIMARY KEY  (id),
KEY logged (logged),
KEY user_type (user_type),
KEY user_key (user_key),
KEY user_role (user_role),
KEY content (content),
KEY forum_id (forum_id),
KEY topic_id (topic_id),
KEY profile_id (profile_id),
KEY topic_view (topic_view)
) ".$charset_collate.";

CREATE TABLE ".$tables['tracker']." (
user_id bigint(20) unsigned NOT NULL,
topic_id bigint(20) unsigned NOT NULL,
forum_id bigint(20) unsigned NOT NULL,
reply_id bigint(20) unsigned NOT NULL,
latest datetime NOT NULL,
PRIMARY KEY  (user_id,topic_id),
KEY forum_id (forum_id),
KEY reply_id (reply_id),
KEY latest (latest)
) ".$charset_collate.";";

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');

    return dbDelta($query);
}

function gdbbx_check_database() {
    global $wpdb;

    $result = array();
    $tables = gdbbx_list_database_tables();

    foreach ($tables as $table => $count) {
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $table");

            if ($count != count($columns)) {
                $result[$table] = array("status" => "error", "msg" => __("Some columns are missing.", "bbp-core"));
            } else {
                $result[$table] = array("status" => "ok");
            }
        } else {
            $result[$table] = array("status" => "error", "msg" => __("Table missing.", "bbp-core"));
        }
    }

    return $result;
}

function gdbbx_convert_attachments_assignments() {
    global $wpdb;

    $sql = "INSERT IGNORE INTO ".$wpdb->prefix."gdbbx_attachments 
            SELECT p.ID AS post_id, a.ID AS attachment_id FROM ".$wpdb->posts." a 
            INNER JOIN ".$wpdb->posts." p ON p.ID = a.post_parent WHERE a.post_type = 'attachment' 
            AND p.post_type IN ('".bbp_get_topic_post_type()."', '".bbp_get_reply_post_type()."')
            ORDER BY p.ID ASC";
    $wpdb->query($sql);

    return $wpdb->rows_affected;
}

function gdbbx_convert_forum_settings() {
    global $wpdb;

    $sql = "select * from ".$wpdb->postmeta." where meta_key in ('_gdbbatt_settings', '_gdbbx_privacy_settings') order by post_id";
    $raw = $wpdb->get_results($sql);

    $result = array('forums' => 0);
    $posts = array();

    foreach ($raw as $row) {
        $posts[$row->post_id][$row->meta_key] = maybe_unserialize($row->meta_value);
    }

    foreach ($posts as $post_id => $data) {
        $settings = gdbbx_default_forum_settings();

        foreach ($data as $meta_key => $obj) {
            if (is_array($obj) && !empty($obj)) {
                if ($meta_key == '_gdbbx_privacy_settings') {
                    if ($obj['disable_topic_private'] == 1) {
                        $settings['privacy_enable_topic_private'] = 'no';
                    }

                    if ($obj['disable_reply_private'] == 1) {
                        $settings['privacy_enable_reply_private'] = 'no';
                    }
                }

                if ($meta_key == '_gdbbatt_settings') {
                    if ($obj['disable'] == 1) {
                        $settings['attachments_status'] = 'no';
                    }

                    if ($obj['to_override'] == 1) {
                        $settings['attachments_max_file_size_override'] = 'yes';
                        $settings['attachments_max_to_upload_override'] = 'yes';
                        $settings['attachments_hide_from_visitors'] = $obj['hide_from_visitors'] == 1 ? 'yes' : 'no';

                        $settings['attachments_max_file_size'] = $obj['max_file_size'];
                        $settings['attachments_max_to_upload'] = $obj['max_to_upload'];
                    }
                }
            }
        }

        update_post_meta($post_id, '_gdbbx_settings', $settings);
        delete_post_meta($post_id, '_gdbbatt_settings');
        delete_post_meta($post_id, '_gdbbx_privacy_settings');

        $result['forums']++;
    }

    return $result;
}

function gdbbx_forum_last_post_date() {
    global $wpdb;

    $dates = array();
    $result = array('forums' => 0);

    $sql = "SELECT p.ID FROM ".$wpdb->posts." p LEFT JOIN ".$wpdb->postmeta." m ON m.post_id = p.ID AND m.meta_key = '_bbp_last_post_time'
            WHERE p.post_type = '".bbp_get_forum_post_type()."' AND m.meta_value IS NULL;";
    $raw = $wpdb->get_results($sql);
    $forums = wp_list_pluck($raw, 'ID');

    if (!empty($forums)) {
        $sql = "SELECT CAST(am.meta_value AS UNSIGNED) AS forum_id, max(ap.post_date) AS last_post_date FROM ".$wpdb->posts." ap 
            INNER JOIN ".$wpdb->postmeta." am ON am.post_id = ap.ID AND am.meta_key = '_bbp_forum_id'
            WHERE ap.post_type IN ('".bbp_get_topic_post_type()."', '".bbp_get_reply_post_type()."') 
            AND CAST(am.meta_value AS UNSIGNED) IN (".join(',', $forums).") GROUP BY forum_id";
        $raw = $wpdb->get_results($sql);
        $list = wp_list_pluck($raw, 'last_post_date', 'forum_id');

        foreach ($forums as $forum_id) {
            if (isset($list[$forum_id])) {
                $dates[$forum_id] = $list[$forum_id];
            } else {
                $children = gdbbx_get_forum_children_ids($forum_id);

                if (empty($children)) {
                    $dates[$forum_id] = get_post_time('Y-m-d H:i:s', false, $forum_id);
                } else {
                    $max = '0000-00-00 00:00:00';

                    foreach ($children as $child_id) {
                        if (isset($list[$child_id])) {
                            if ($list[$child_id] > $max) {
                                $max = $list[$child_id];
                            }
                        }
                    }

                    if ($max == '0000-00-00 00:00:00') {
                        $max = get_post_time('Y-m-d H:i:s', false, $forum_id);
                    }

                    $dates[$forum_id] = $max;
                }
            }
        }

        foreach ($dates as $forum_id => $last_post_date) {
            update_post_meta($forum_id, '_bbp_last_post_time', $last_post_date);
            $result['forums']++;
        }
    }

    return $result;
}

function gdbbx_truncate_database_tables() {
    global $wpdb;

    $tables = array_keys(gdbbx_list_database_tables());

    foreach ($tables as $table) {
        $wpdb->query("TRUNCATE TABLE ".$table);
    }
}

function gdbbx_drop_database_tables() {
    global $wpdb;

    $tables = array_keys(gdbbx_list_database_tables());

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS ".$table);
    }
}
