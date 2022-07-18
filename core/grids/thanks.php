<?php

if (!defined('ABSPATH')) {
    exit;
}

class gdbbx_grid_thanks extends d4p_grid {
    public $_sanitize_orderby_fields = array('a.action_id', 'a.post_id', 'user_from', 'user_to');
    public $_table_class_name = 'gdbbx-grid-thanks';

    public function __construct($args = array()) {
        parent::__construct(array(
            'singular' => 'thank',
            'plural' => 'thanks',
            'ajax' => false
        ));
    }

    private function _self($args, $getback = false) {
        $url = 'admin.php?page=gd-bbpress-toolbox-thanks-list&'.$args;

        if ($getback) {
            $url .= '&gdbbx_handler=getback';
            $url .= '&_wpnonce='.wp_create_nonce('gd-bbpress-toolbox-thanks');
            $url .= '&_wp_http_referer='.wp_unslash($_SERVER['REQUEST_URI']);
        }

        return self_admin_url($url);
    }

    private function _render_user($item, $type = 'from') {
        $user_id = $item->{'user_'.$type};
        $user = get_user_by('id', $user_id);

        if ($user) {
            $render = get_avatar($user->ID, 36);
            $render .= '<div>['.$user->ID.'] '.$user->display_name.'<br/>'.$user->user_email;

            $render .= '</div>';

            $actions = array(
                'filter-from' => sprintf('<a href="%s">%s</a>', $this->_self('filter-from='.$user->ID), __("Thanks From", "bbp-core")),
                'filter-to' => sprintf('<a href="%s">%s</a>', $this->_self('filter-to='.$user->ID), __("Thanks To", "bbp-core")),
                'visit' => sprintf('<a href="%s">%s</a>', bbp_get_user_profile_url($user->ID), __("Visit", "bbp-core"))
            );

            return $render.$this->row_actions($actions);
        } else {
            return sprintf(__("User with ID <strong>%s</strong> not found.", "bbp-core"), $user_id);
        }
    }

    protected function extra_tablenav($which) {
        if ($which == 'top') {
            $reported = array(
                '' => __("For Topics And Replies", "bbp-core"),
                bbp_get_topic_post_type() => __("For Topics Only", "bbp-core"),
                bbp_get_reply_post_type() => __("For Replies Only", "bbp-core")
            );

            $_sel_type = isset($_GET['filter-type']) && !empty($_GET['filter-type']) ? d4p_sanitize_slug($_GET['filter-type']) : '';
            $_sel_from = isset($_GET['filter-from']) && !empty($_GET['filter-from']) ? absint($_GET['filter-from']) : '';
            $_sel_to = isset($_GET['filter-to']) && !empty($_GET['filter-to']) ? absint($_GET['filter-to']) : '';

            echo '<div class="alignleft actions">';
            d4p_render_select($reported, array('selected' => $_sel_type, 'name' => 'filter-type'));
            echo '<span>'.__("From", "bbp-core").':</span><input style="width: 100px;" placeholder="'.__("User ID", "bbp-core").'" value="'.$_sel_from.'" name="filter-from" type="number" min="0" step="1" />';
            echo '<span>'.__("To", "bbp-core").':</span><input style="width: 100px;" placeholder="'.__("User ID", "bbp-core").'" value="'.$_sel_to.'" name="filter-to" type="number" min="0" step="1" />';
            submit_button(__("Filter", "bbp-core"), 'button', false, false, array('id' => 'gdbbx-thanks-submit'));
            echo '</div>';
        }
    }

    public function rows_per_page() {
        $user = get_current_user_id();
        $per_page = get_user_meta($user, 'gdbbx_rows_thanks_per_page', true);

        if (empty($per_page) || $per_page < 1) {
            $per_page = 25;
        }

        return $per_page;
    }

    public function get_columns() {
        return array(
            'id' => __("ID", "bbp-core"),
            'type' => '',
            'post' => __("Topic / Reply", "bbp-core"),
            'from' => __("From", "bbp-core"),
            'to' => __("To", "bbp-core"),
            'date' => __("Reported", "bbp-core"),
            'forum' => __("Forum", "bbp-core")
        );
    }

    protected function get_sortable_columns() {
        return array(
            'id' => array('a.action_id', false),
            'from' => array('user_from', false),
            'to' => array('user_to', false),
            'post' => array('a.post_id', false),
            'date' => array('a.logged', false)
        );
    }

    public function column_id($item) {
        return $item->action_id;
    }

    public function column_date($item) {
        return mysql2date('Y.m.d', $item->logged).'<br/>@ '.mysql2date('H:m:s', $item->logged);
    }

    public function column_type($item) {
        return ucfirst($item->post_type);
    }

    public function column_post($item) {
        $post = $item->post_id;

        $title = '';
        $url = '';

        if (bbp_is_reply($post)) {
            $title = bbp_get_reply_title($post);
            $url = bbp_get_reply_url($post);
        } else if (bbp_is_topic($post)) {
            $title = bbp_get_topic_title($post);
            $url = get_permalink($post);
        }

        if ($url == '') {
            return '&minus;';
        } else {
            $actions = array(
                'visit' => sprintf('<a href="%s">%s</a>', $url, __("Visit", "bbp-core"))
            );

            return $title.$this->row_actions($actions);
        }
    }

    public function column_from($item) {
        return $this->_render_user($item, 'from');
    }

    public function column_to($item) {
        return $this->_render_user($item, 'to');
    }

    public function column_forum($item) {
        $forum_id = $item->forum_id;

        if ($forum_id == 0) {
            return '&minus;';
        } else {
            $actions = array(
                'visit' => sprintf('<a href="%s">%s</a>', get_permalink($forum_id), __("Visit", "bbp-core")),
                'topics' => sprintf('<a href="edit.php?post_type=topic&bbp_forum_id=%s">%s</a>', $forum_id, __("Topics", "bbp-core"))
            );

            return bbp_get_forum_title($forum_id).$this->row_actions($actions);
        }
    }

    public function prepare_items() {
        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $per_page = $this->rows_per_page();

        $_sel_type = isset($_GET['filter-type']) && !empty($_GET['filter-type']) ? "'".d4p_sanitize_slug($_GET['filter-type'])."'" : "'".bbp_get_topic_post_type()."', '".bbp_get_reply_post_type()."'";
        $_sel_from = isset($_GET['filter-from']) && !empty($_GET['filter-from']) ? absint($_GET['filter-from']) : 0;
        $_sel_to = isset($_GET['filter-to']) && !empty($_GET['filter-to']) ? absint($_GET['filter-to']) : 0;

        $sql = array(
            'select' => array(
                'a.action_id',
                'a.post_id',
                'a.user_id AS user_from',
                'p.post_author AS user_to',
                'a.logged',
                'p.post_type',
                'p.post_title',
                'm.meta_value AS forum_id'),
            'from' => array(
                gdbbx_db()->actions.' a',
                'INNER JOIN '.gdbbx_db()->wpdb()->posts.' p ON p.ID = a.post_id AND p.post_type IN ('.$_sel_type.')',
                'INNER JOIN '.gdbbx_db()->wpdb()->postmeta.' m ON p.ID = m.post_id AND m.meta_key = \'_bbp_forum_id\''),
            'where' => array(
                'a.`action` = \'thanks\''
            )
        );

        if ($_sel_from > 0) {
            $sql['where'][] = 'a.user_id = '.$_sel_from;
        }

        if ($_sel_to > 0) {
            $sql['where'][] = 'p.post_author = '.$_sel_to;
        }

        $orderby = !empty($_GET['orderby']) ? $this->sanitize_field('orderby', $_GET['orderby'], 'a.action_id') : 'a.action_id';
        $order = !empty($_GET['order']) ? $this->sanitize_field('order', $_GET['order'], 'DESC') : 'DESC';

        $paged = !empty($_GET['paged']) ? absint($_GET['paged']) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }

        $offset = intval(($paged - 1) * $per_page);

        $sql['order'] = $orderby.' '.$order;
        $sql['limit'] = $offset.', '.$per_page;

        $query = gdbbx_db()->build_query($sql);

        $this->items = gdbbx_db()->run($query);

        $total_rows = gdbbx_db()->get_found_rows();

        $this->set_pagination_args(array(
            'total_items' => $total_rows,
            'total_pages' => ceil($total_rows / $per_page),
            'per_page' => $per_page,
        ));
    }
}
