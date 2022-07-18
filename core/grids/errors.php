<?php

if (!defined('ABSPATH')) {
    exit;
}

class gdbbx_grid_errors extends d4p_grid {
    public $_sanitize_orderby_fields = array('m.meta_id', 'p.post_title', 'p.post_author', 'p.post_parent');
    public $_checkbox_field = 'meta_id';
    public $_table_class_name = 'gdbbx-grid-errors';

    public function __construct($args = array()) {
        parent::__construct(array(
            'singular' => 'error',
            'plural' => 'errors',
            'ajax' => false
        ));
    }

    protected function extra_tablenav($which) {
        if ($which == 'top') {
            $attached = array(
                '' => __("For Topics And Replies", "bbp-core"),
                bbp_get_topic_post_type() => __("For Topics Only", "bbp-core"),
                bbp_get_reply_post_type() => __("For Replies Only", "bbp-core")
            );

            $_sel_attached = isset($_GET['filter-attached']) && !empty($_GET['filter-attached']) ? d4p_sanitize_slug($_GET['filter-attached']) : '';

            echo '<div class="alignleft actions">';
            d4p_render_select($attached, array('selected' => $_sel_attached, 'name' => 'filter-attached'));
            submit_button(__("Filter", "bbp-core"), 'button', false, false, array('id' => 'gdbbx-attchments-submit'));
            echo '</div>';
        }
    }

    public function rows_per_page() {
        $user = get_current_user_id();
        $per_page = get_user_meta($user, 'gdbbx_rows_errors_per_page', true);

        if (empty($per_page) || $per_page < 1) {
            $per_page = 25;
        }

        return $per_page;
    }

    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'id' => __("ID", "bbp-core"),
            'file' => __("File", "bbp-core"),
            'message' => __("Message", "bbp-core"),
            'author' => __("Uploader", "bbp-core"),
            'topic' => __("Topic / Reply", "bbp-core"),
            'forum' => __("Forum", "bbp-core"),
            'date' => __("Date", "bbp-core")
        );
    }

    protected function get_sortable_columns() {
        return array(
            'id' => array('m.meta_id', false),
            'file' => array('p.post_title', false),
            'author' => array('p.post_author', false),
            'topic' => array('p.post_parent', false)
        );
    }

    protected function get_bulk_actions() {
        return array(
            'delete' => __("Delete", "bbp-core")
        );
    }

    public function column_id($item) {
        return $item->meta_id;
    }

    public function column_message($item) {
        return $item->error['message'];
    }

    public function column_file($item) {
        $actions = array(
            'delete' => sprintf('<a href="admin.php?page=gd-bbpress-toolbox-errors&single-action=%s&error=%s&gdbbx_handler=getback&_wpnonce=%s">%s</a>', 'delete', $item->meta_id, wp_create_nonce('gd-bbpress-toolbox-error'), __("Delete", "bbp-core"))
        );

        $type = $this->attachment_type($item);

        $render = !empty($type) ? $type.': ' : '';
        $render .= '<strong>'.esc_html($item->error['file']).'</strong>';

        return $render.$this->row_actions($actions);
    }

    public function column_topic($item) {
        $topic_id = $item->ID;

        $title = '';

        if ($item->post_type == 'reply') {
            $title .= __("Reply", "bbp-core").': <strong>'.$item->ID.'</strong><br/>';
            $title .= bbp_get_reply_title_fallback(bbp_get_reply_title($item->ID), $item->ID);
            $url = bbp_get_reply_url($item->ID);
        } else {
            $title .= __("Topic", "bbp-core").': <strong>'.$item->ID.'</strong><br/>';
            $title .= bbp_get_topic_title($topic_id);
            $url = get_permalink($topic_id);
        }

        $actions = array(
            'narrow' => sprintf('<a href="admin.php?page=gd-bbpress-toolbox-errors&bbp_topic_id=%s">%s</a>', $topic_id, __("Filter", "bbp-core")),
            'visit' => sprintf('<a href="%s">%s</a>', $url, __("Visit", "bbp-core")),
            'edit' => sprintf('<a href="post.php?post=%s&action=edit">%s</a>', $item->ID, __("Edit", "bbp-core")),
        );

        return $title.$this->row_actions($actions);
    }

    public function column_forum($item) {
        if ($item->post_type == bbp_get_topic_post_type()) {
            $forum_id = bbp_get_topic_forum_id($item->ID);
        } else {
            $forum_id = bbp_get_reply_forum_id($item->ID);
        }

        $actions = array(
            'visit' => sprintf('<a href="%s">%s</a>', get_permalink($forum_id), __("Visit", "bbp-core")),
            'edit' => sprintf('<a href="post.php?post=%s&action=edit">%s</a>', $forum_id, __("Edit", "bbp-core")),
            'topics' => sprintf('<a href="edit.php?post_type=topic&bbp_forum_id=%s">%s</a>', $forum_id, __("Topics", "bbp-core"))
        );

        return bbp_get_forum_title($forum_id).$this->row_actions($actions);
    }

    public function column_author($item) {
        $user = get_user_by('id', $item->post_author);

        if ($user) {
            return '<a href="user-edit.php?user_id='.$item->post_author.'">'.$user->display_name.'</a>';
        } else {
            return '-';
        }
    }

    public function column_date($item) {
        return mysql2date('Y.m.d', $item->post_date).'<br/>@ '.mysql2date('H:m:s', $item->post_date);
    }

    private function attachment_type($item) {
        if (preg_match('/^.*?\.(\w+)$/', $item->error['file'], $matches)) {
            return esc_html(strtoupper($matches[1]));
        } else {
            return '';
        }
    }

    public function prepare_items() {
        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $per_page = $this->rows_per_page();

        $_sel_attached = isset($_GET['filter-attached']) && !empty($_GET['filter-attached']) ? "'".d4p_sanitize_slug($_GET['filter-attached'])."'" : "'".bbp_get_topic_post_type()."', '".bbp_get_reply_post_type()."'";
        $_sel_topic = isset($_GET['bbp_topic_id']) && !empty($_GET['bbp_topic_id']) ? absint($_GET['bbp_topic_id']) : '';

        $query_where = array("p.post_type in (".$_sel_attached.")", "m.meta_key = '_bbp_attachment_upload_error'");
        $query_errors = "SELECT SQL_CALC_FOUND_ROWS m.meta_id, m.meta_value, p.* FROM ".gdbbx_db()->wpdb()->postmeta." m INNER JOIN ".gdbbx_db()->wpdb()->posts." p ON p.ID = m.post_id";

        if ($_sel_topic != '') {
            $replies = gdbbx_db()->get_topic_replies_ids($_sel_topic);
            $replies[] = $_sel_topic;
            $query_where[] = "p.ID in (".join(', ', $replies).")";
        }

        if (isset($_GET['s']) && $_GET['s'] != '') {
            $query_where[] = "(p.`post_title` LIKE '%".$_GET['s']."%')";
        }

        if (!empty($query_where)) {
            $query_errors .= ' WHERE '.join(' AND ', $query_where);
        }

        $orderby = !empty($_GET['orderby']) ? $this->sanitize_field('orderby', $_GET['orderby'], 'm.meta_id') : 'm.meta_id';
        $order = !empty($_GET['order']) ? $this->sanitize_field('order', $_GET['order'], 'DESC') : 'DESC';

        $query_errors .= " ORDER BY $orderby $order";

        $paged = !empty($_GET['paged']) ? absint($_GET['paged']) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }

        $offset = intval(($paged - 1) * $per_page);
        $query_errors .= " LIMIT $offset, $per_page";

        $this->items = gdbbx_db()->get_results($query_errors);

        for ($i = 0; $i < count($this->items); $i++) {
            $this->items[$i]->error = maybe_unserialize($this->items[$i]->meta_value);
        }

        $total_rows = gdbbx_db()->get_found_rows();

        $this->set_pagination_args(array(
            'total_items' => $total_rows,
            'total_pages' => ceil($total_rows / $per_page),
            'per_page' => $per_page,
        ));
    }
}
