<?php

if (!defined('ABSPATH')) {
    exit;
}

class gdbbx_grid_users extends d4p_grid {
    public $_table_class_name = 'gdbbx-grid-users';
    public $_checkbox_field = 'ID';
    public $current_view = 'default';

    public $bbp_roles = array();

    public function __construct($args = array()) {
        $this->current_view = isset($_GET['view']) ? $_GET['view'] : 'default';

        $this->bbp_roles = gdbbx_get_user_roles();

        parent::__construct(array(
            'singular' => 'user',
            'plural' => 'users',
            'ajax' => false
        ));
    }

    protected function get_views() {
        return array(
            'default' => '<a class="'.($this->current_view == 'default' ? 'current' : '').'" href="admin.php?page=gd-bbpress-toolbox-users">'.__("All Forum Users", "bbp-core").'</a>',
            'topic_favorites' => '<a class="'.($this->current_view == 'topic_favorites' ? 'current' : '').'" href="admin.php?page=gd-bbpress-toolbox-users&view=topic_favorites">'.__("Users with Favorite Topics", "bbp-core").'</a>',
            'topic_subscriptions' => '<a class="'.($this->current_view == 'topic_subscriptions' ? 'current' : '').'" href="admin.php?page=gd-bbpress-toolbox-users&view=topic_subscriptions">'.__("Users with Topic Subscriptions", "bbp-core").'</a>',
            'forum_subscriptions' => '<a class="'.($this->current_view == 'forum_subscriptions' ? 'current' : '').'" href="admin.php?page=gd-bbpress-toolbox-users&view=forum_subscriptions">'.__("Users with Forum Subscriptions", "bbp-core").'</a>'
        );
    }

    protected function extra_tablenav($which) {
        if ($which == 'top') {
            echo '<div class="alignleft actions">';
            $roles = array_merge(
                array('' => __("All User Roles", "bbp-core")),
                $this->bbp_roles
            );

            $_sel_role = isset($_GET['filter-role']) && !empty($_GET['filter-role']) ? d4p_sanitize_slug($_GET['filter-role']) : '';
            d4p_render_select($roles, array('selected' => $_sel_role, 'name' => 'filter-role'));

            if ($this->current_view != 'default') {
                if ($this->current_view == 'forum_subscriptions') {
                    $_sel_forum = isset($_GET['filter-forum']) && !empty($_GET['filter-forum']) ? absint($_GET['filter-forum']) : 0;

                    bbp_dropdown(
                        array(
                            'selected' => $_sel_forum,
                            'select_id' => 'filter-forum',
                            'show_none' => esc_html__("All Forums", "bbp-core")
                        )
                    );
                } else {
                    $_sel_topic = isset($_GET['filter-topic']) && !empty($_GET['filter-topic']) ? absint($_GET['filter-topic']) : 0;

                    echo '<input title="'.__("Topic ID", "bbp-core").'" style="width: 100px;" min=0 type="number" placeholder="'.__("Topic ID", "bbp-core").'" value="'.$_sel_topic.'" name="filter-topic" />';
                }
            }

            submit_button(__("Filter", "bbp-core"), 'button', false, false, array('id' => 'gdbbx-users-submit'));
            echo '</div>';
        }
    }

    protected function get_bulk_actions() {
        return array(
            'unsubfav' => __("Clear Subscriptions and Favorites", "bbp-core"),
            'unfavtop' => __("Clear Topic Favorites", "bbp-core"),
            'unsuball' => __("Clear Topic and Forum Subscriptions", "bbp-core"),
            'unsubfor' => __("Clear Forum Subscriptions", "bbp-core"),
            'unsubtop' => __("Clear Topic Subscriptions", "bbp-core"),
        );
    }

    public function rows_per_page() {
        $user = get_current_user_id();
        $per_page = get_user_meta($user, 'gdbbx_rows_users_per_page', true);

        if (empty($per_page) || $per_page < 1) {
            $per_page = 25;
        }

        return $per_page;
    }

    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'ID' => __("ID", "bbp-core"),
            'avatar' => '',
            'username' => __("Username", "bbp-core"),
            'email' => __("Email And Name", "bbp-core"),
            'topics' => __("Topics", "bbp-core"),
            'replies' => __("Replies", "bbp-core"),
            'engagements' => __("Engagements", "bbp-core"),
            'role' => __("Role", "bbp-core"),
            'sig' => __("Signature", "bbp-core"),
            'date' => __("Active", "bbp-core")
        );

        return $columns;
    }

    protected function get_sortable_columns() {
        return array(
            'ID' => array('ID', false),
            'username' => array('user_login', false),
            'email' => array('user_email', false),
            'topics' => array('usr.topics', false),
            'replies' => array('usr.replies', false)
        );
    }

    public function column_avatar($item) {
        return get_avatar($item->ID, 40);
    }

    public function column_engagements($item) {
        $engagements = isset($item->data->engagements)
            ? $item->data->engagements
            : array(
                'topic_subscriptions' => 0,
                'forum_subscriptions' => 0,
                'topic_favorites' => 0);

        $value = array(
            sprintf(__("Forum Subscriptions: %s", "bbp-core"), '<strong>'.$engagements['forum_subscriptions'].'</strong>'),
            sprintf(__("Topic Subscriptions: %s", "bbp-core"), '<strong>'.$engagements['topic_subscriptions'].'</strong>'),
            sprintf(__("Topic Favorites: %s", "bbp-core"), '<strong>'.$engagements['topic_favorites'].'</strong>')
        );

        return join('<br/>', $value);
    }

    public function column_topics($item) {
        $value = isset($item->data->forums['topic']) ? $item->data->forums['topic'] : 0;

        if ($value > 0) {
            $value = '<a href="'.admin_url("edit.php?post_type=topic&amp;author=$item->ID").'">'.$value.'</a>';
        }

        return $value;
    }

    public function column_replies($item) {
        $value = isset($item->data->forums['reply']) ? $item->data->forums['reply'] : 0;

        if ($value > 0) {
            $value = '<a href="'.admin_url("edit.php?post_type=reply&amp;author=$item->ID").'">'.$value.'</a>';
        }

        return $value;
    }

    public function column_email($item) {
        $render = '<u>'.$item->user_email.'</u><br/>';
        $render .= $item->first_name.' '.$item->last_name;

        return $render;
    }

    public function column_role($item) {
        $roles = array();

        foreach ($item->roles as $role) {
            $_role = $this->bbp_roles[ $role ] ?? '';

            if ($_role != '') {
                $roles[] = $_role;
            }
        }

        return !empty($roles) ? join('<br/>', $roles) : __("None", "bbp-core");
    }

    public function column_username($item) {
        $actions = array();

        $render = "<strong>".$item->user_login."</strong>";

        if (current_user_can('edit_users')) {
            $edit_link = esc_url(add_query_arg('wp_http_referer', urlencode(wp_unslash($_SERVER['REQUEST_URI'])), get_edit_user_link($item->ID)));
            $render = "<strong><a href=\"$edit_link\">$item->user_login</a></strong>";

            $actions['edit'] = '<a href="'.$edit_link.'">'.__("Edit", "bbp-core").'</a>';
            $actions['view'] = '<a href="'.bbp_get_user_profile_url($item->ID).'">'.__("View", "bbp-core").'</a>';
        }

        return $render.$this->row_actions($actions);
    }

    public function column_sig($item) {
        $sig = $item->signature;

        $render = '';

        if ($sig != '') {
            $sig = convert_smilies($sig);
            $sig = do_shortcode($sig);

            $render = '<div class="gdbbx-signature">'.$sig.'</div>';
        }

        return $render;
    }

    public function column_date($item) {
        if ($item->{gdbbx_plugin()->user_meta_key_last_activity()} != '') {
            $time = intval($item->{gdbbx_plugin()->user_meta_key_last_activity()}) + d4p_gmt_offset() * 3600;

            return date('Y.m.d', $time).'<br/>@ '.date('H:i:s', $time);
        } else if ($item->bbp_last_activity != '') {
            $time = intval($item->bbp_last_activity) + d4p_gmt_offset() * 3600;

            return date('Y.m.d', $time).'<br/>@ '.date('H:i:s', $time);
        } else {
            return '—';
        }
    }

    public function prepare_items() {
        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        $per_page = $this->rows_per_page();

        $_sel_role = isset($_GET['filter-role']) && !empty($_GET['filter-role']) ? d4p_sanitize_slug($_GET['filter-role']) : '';
        $_sel_search = isset($_GET['s']) && $_GET['s'] != '' ? d4p_sanitize_basic($_GET['s']) : '';

        $paged = !empty($_GET['paged']) ? absint($_GET['paged']) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }

        $args = array(
            'number' => $per_page,
            'offset' => ($paged - 1) * $per_page,
            'role' => $_sel_role,
            'fields' => 'all_with_meta',
            'toolbox' => 'yes'
        );

        if ($_sel_search != '') {
            $args['search'] = '*'.$_sel_search.'*';
            $args['search_columns'] = array('user_login', 'user_email', 'user_nicename');
        }

        if (isset($_REQUEST['orderby'])) {
            $args['orderby'] = $_REQUEST['orderby'];
        }

        if (isset($_REQUEST['order'])) {
            $args['order'] = $_REQUEST['order'];
        }

        add_action('pre_user_query', array($this, 'users_query'));

        $wp_user_search = new WP_User_Query($args);

        $this->items = $wp_user_search->get_results();

        $this->set_pagination_args(array(
            'total_items' => $wp_user_search->get_total(),
            'total_pages' => ceil($wp_user_search->get_total() / $per_page),
            'per_page' => $per_page,
        ));

        $this->calculate_counts();
        $this->calculate_engagements();
    }

    public function users_query($query) {
        $post_types = array(
            bbp_get_forum_post_type(),
            bbp_get_topic_post_type(),
            bbp_get_reply_post_type()
        );

        $join = "SELECT DISTINCT post_author AS ID FROM ".gdbbx_db()->wpdb()->posts." WHERE post_type in ('".join("', '", $post_types)."')  UNION SELECT DISTINCT CAST(m.meta_value AS UNSIGNED) AS ID FROM ".gdbbx_db()->wpdb()->postmeta." m INNER JOIN ".gdbbx_db()->wpdb()->posts." p ON p.ID = m.post_id WHERE m.meta_key IN ('_bbp_subscription', '_bbp_favorite') AND post_type IN ('".bbp_get_forum_post_type()."', '".bbp_get_topic_post_type()."')";
        $query->query_from .= " INNER JOIN (".$join.") gdbbx_valid ON gdbbx_valid.ID = ".gdbbx_db()->wpdb()->users.".ID ";

        if ($this->current_view != 'default') {
            if ($this->current_view == 'forum_subscriptions') {
                $_sel_forum = isset($_GET['filter-forum']) && !empty($_GET['filter-forum']) ? absint($_GET['filter-forum']) : 0;

                if ($_sel_forum == 0) {
                    $query->query_where .= " AND (".gdbbx_db()->wpdb()->users.".ID IN (SELECT DISTINCT CAST(m.meta_value AS UNSIGNED) AS user_id FROM ".gdbbx_db()->wpdb()->postmeta." m INNER JOIN ".gdbbx_db()->wpdb()->posts." p ON p.ID = m.post_id  WHERE m.meta_key = '_bbp_subscription' AND p.post_type IN ('".bbp_get_forum_post_type()."')))";
                } else {
                    $query->query_where .= " AND (".gdbbx_db()->wpdb()->users.".ID IN (SELECT DISTINCT CAST(m.meta_value AS UNSIGNED) AS user_id FROM ".gdbbx_db()->wpdb()->postmeta." m WHERE m.meta_key = '_bbp_subscription' AND m.post_id = ".$_sel_forum."))";
                }
            } else if ($this->current_view == 'topic_subscriptions' || $this->current_view == 'topic_favorites') {
                $_key = $this->current_view == 'topic_subscriptions' ? '_bbp_subscription' : '_bbp_favorite';
                $_sel_topic = isset($_GET['filter-topic']) && !empty($_GET['filter-topic']) ? absint($_GET['filter-topic']) : 0;

                if ($_sel_topic == 0) {
                    $query->query_where .= " AND (".gdbbx_db()->wpdb()->users.".ID IN (SELECT DISTINCT CAST(m.meta_value AS UNSIGNED) AS user_id FROM ".gdbbx_db()->wpdb()->postmeta." m INNER JOIN ".gdbbx_db()->wpdb()->posts." p ON p.ID = m.post_id  WHERE m.meta_key = '".$_key."' AND p.post_type IN ('".bbp_get_topic_post_type()."')))";
                } else {
                    $query->query_where .= " AND (".gdbbx_db()->wpdb()->users.".ID IN (SELECT DISTINCT CAST(m.meta_value AS UNSIGNED) AS user_id FROM ".gdbbx_db()->wpdb()->postmeta." m WHERE m.meta_key = '".$_key."' AND m.post_id = ".$_sel_topic."))";
                }
            }
        }

        if ($query->query_vars['orderby'] == 'usr.replies' || $query->query_vars['orderby'] == 'usr.topics') {
            if ($query->query_vars['orderby'] == 'usr.replies') {
                $query->query_from .= " LEFT JOIN (SELECT post_author, count(*) as replies FROM ".gdbbx_db()->wpdb()->posts." WHERE post_type = 'reply' AND post_status IN ('publish', 'pending', 'closed') GROUP BY post_author) usr ON usr.post_author = ".gdbbx_db()->wpdb()->users.".ID";
            } else if ($query->query_vars['orderby'] == 'usr.topics') {
                $query->query_from .= " LEFT JOIN (SELECT post_author, count(*) as topics FROM ".gdbbx_db()->wpdb()->posts." WHERE post_type = 'topic' AND post_status IN ('publish', 'pending', 'closed') GROUP BY post_author) usr ON usr.post_author = ".gdbbx_db()->wpdb()->users.".ID";
            }

            $query->query_orderby = 'ORDER BY '.$query->query_vars['orderby'].' '.$query->query_vars['order'];
        }
    }

    private function calculate_counts() {
        $users = array_keys($this->items);

        if (!empty($users)) {
            $sql = "SELECT post_type, post_author, count(*) AS counter FROM ".gdbbx_db()->wpdb()->posts." WHERE post_type IN ('".bbp_get_reply_post_type()."', '".bbp_get_topic_post_type()."') AND post_status IN ('pending', 'publish', 'closed') AND post_author IN (".join(', ', $users).") GROUP BY post_type, post_author";
            $raw = gdbbx_db()->get_results($sql);

            foreach ($raw as $row) {
                if (!isset($this->items[$row->post_author]->data->forums)) {
                    $this->items[$row->post_author]->data->forums = array();
                }

                $this->items[$row->post_author]->data->forums[$row->post_type] = $row->counter;
            }
        }
    }

    private function calculate_engagements() {
        $users = array_keys($this->items);

        if (!empty($users)) {
            $sql = "SELECT CAST(m.meta_value AS UNSIGNED) AS user_id, SUBSTR(m.meta_key, 6) AS type, p.post_type, COUNT(*) AS counter 
                    FROM ".gdbbx_db()->wpdb()->postmeta." m INNER JOIN ".gdbbx_db()->wpdb()->posts." p ON p.ID = m.post_id 
                    WHERE m.meta_key IN ('_bbp_subscription', '_bbp_favorite') AND m.meta_value IN (".join(', ', $users).")
                    AND post_type IN ('".bbp_get_forum_post_type()."', '".bbp_get_topic_post_type()."')
                    GROUP BY m.meta_value, m.meta_key, p.post_type";
            $raw = gdbbx_db()->get_results($sql);

            foreach ($raw as $row) {
                if (!isset($this->items[$row->user_id]->data->engagements)) {
                    $this->items[$row->user_id]->data->engagements = array(
                        'topic_subscriptions' => 0,
                        'forum_subscriptions' => 0,
                        'topic_favorites' => 0
                    );
                }

                if ($row->post_type == bbp_get_forum_post_type() && $row->type == 'subscription') {
                    $this->items[$row->user_id]->data->engagements['forum_subscriptions'] = absint($row->counter);
                }

                if ($row->post_type == bbp_get_topic_post_type() && $row->type == 'subscription') {
                    $this->items[$row->user_id]->data->engagements['topic_subscriptions'] = absint($row->counter);
                }

                if ($row->post_type == bbp_get_topic_post_type() && $row->type == 'favorite') {
                    $this->items[$row->user_id]->data->engagements['topic_favorites'] = absint($row->counter);
                }
            }
        }
    }
}
