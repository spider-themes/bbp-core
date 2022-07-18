<?php

use Dev4Press\Plugin\GDBBX\Admin\Enqueue;
use Dev4Press\Plugin\GDBBX\Admin\Features;
use Dev4Press\Plugin\GDBBX\Admin\Grids;
use Dev4Press\Plugin\GDBBX\Admin\Help;
use Dev4Press\Plugin\GDBBX\Admin\MetaBoxes;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;
use Dev4Press\Plugin\GDBBX\Features\CannedReplies;

if (!defined('ABSPATH')) {
    exit;
}

class gdbbx_admin_core {
    public $plugin = 'gd-bbpress-toolbox';

    public $debug;

    public $page = false;
    public $panel = false;
    public $free = array();

    public $menu_items;
    public $page_ids = array();

    function __construct() {
        add_action('gdbbx_plugin_core_ready', array($this, 'core'));

        if (is_multisite()) {
            add_filter('wpmu_drop_tables', array($this, 'wpmu_drop_tables'));
        }
    }

    public function wpmu_drop_tables($drop_tables) {
        return array_merge($drop_tables, gdbbx_db()->db_site);
    }

    public function core() {
        $this->debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

        if (gdbbx_has_bbpress()) {
            $this->init();

            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'admin_menu'), 9);
            add_action('current_screen', array($this, 'current_screen'));
            add_filter('gdbbx_save_settings_value', array($this, 'save_settings'), 10, 3);
        }

        if (gdbbx()->is_install()) {
            add_action('admin_notices', array($this, 'install_notice'));
        }

        if (gdbbx()->is_update()) {
            add_action('admin_notices', array($this, 'update_notice'));
        }

        $this->free = gdbbx()->has_free_plugins();

        if (!empty($this->free)) {
            add_action('admin_notices', array($this, 'free_plugins_notice'));
        }

        Features::instance();
        MetaBoxes::instance();
	    Grids::instance();
        Help::instance();
	    Enqueue::instance();
    }

    public function save_settings($group, $name, $value) {
        if ($group == 'load' && $name == 'rewriter') {
            flush_rewrite_rules();

            remove_filter('gdbbx_save_settings_value', array($this, 'save_settings'), 10);
        }
    }

    public function current_url($with_panel = true) : string {
        $page = 'admin.php?page='.$this->plugin.'-';

        $page.= $this->page;

        if ($with_panel && $this->panel !== false && $this->panel != '') {
            $page.= '&panel='.$this->panel;
        }

        return self_admin_url($page);
    }

    public function free_plugins_notice() {
        if (!empty($this->free)) {
            echo '<div class="error"><p>';
            echo sprintf(__("GD bbPress Toolbox Pro detected that following plugins are still active: %s. They need to be disabled before you can use GD bbPress Toolbox.", "bbp-core"),
                '<strong>'.join('</strong>, <strong>', $this->free).'</strong>');
            echo '<br>'.sprintf(__("You can <a href='%s'>open plugins page</a> to disable them manually, or <a href='%s'>click here</a> to disabled them automatically.", "bbp-core"), admin_url('plugins.php'), admin_url('admin.php?page=gd-bbpress-toolbox-front&gdbbx_handler=getback&action=gdbbx-disable-free'));
            echo '</p></div>';
        }
    }

    public function update_notice() {
        if (current_user_can('install_plugins') && $this->page === false) {
            echo '<div class="updated"><p>';
            echo __("GD bbPress Toolbox Pro is updated, and you need to review the update process.", "bbp-core");
            echo ' <a href="'.admin_url('admin.php?page=gd-bbpress-toolbox-front').'">'.__("Click Here", "bbp-core").'</a>.';
            echo '</p></div>';
        }
    }

    public function install_notice() {
        if (current_user_can('install_plugins') && $this->page === false) {
            echo '<div class="updated"><p>';
            echo __("GD bbPress Toolbox Pro is activated and it needs to finish installation.", "bbp-core");
            echo ' <a href="'.admin_url('admin.php?page=gd-bbpress-toolbox-front').'">'.__("Click Here", "bbp-core").'</a>.';
            echo '</p></div>';
        }
    }

    public function init() {
        $this->menu_items = apply_filters('gdbbx_admin_menu_items', array(
            'front' => array('title' => __("Dashboard", "bbp-core"), 'icon' => 'home', 'cap' => 'gdbbx_moderation'),
            'about' => array('title' => __("About", "bbp-core"), 'icon' => 'info-circle'),
            'features' => array('title' => __("Features", "bbp-core"), 'icon' => 'puzzle-piece'),
            'settings' => array('title' => __("Settings", "bbp-core"), 'icon' => 'cogs'),
            'users' => array('title' => __("Users", "bbp-core"), 'icon' => 'users', 'cap' => 'gdbbx_moderation_users'),
            'attachments' => array('title' => __("Attachments", "bbp-core"), 'icon' => 'file-text-o', 'cap' => 'gdbbx_moderation_attachments'),
            'reported-posts' => array('title' => __("Reported Posts", "bbp-core"), 'icon' => 'exclamation-triangle', 'cap' => 'gdbbx_moderation_report'),
            'thanks-list' => array('title' => __("Thanks List", "bbp-core"), 'icon' => 'check-square', 'cap' => 'gdbbx_moderation_attachments'),
            'errors' => array('title' => __("Errors Log", "bbp-core"), 'icon' => 'bug', 'cap' => 'gdbbx_moderation'),
            'bbcodes' => array('title' => __("BBCodes", "bbp-core"), 'icon' => 'code', 'cap' => 'gdbbx_moderation'),
            'wizard' => array('title' => __("Setup Wizard", "bbp-core"), 'icon' => 'magic'),
            'tools' => array('title' => __("Tools", "bbp-core"), 'icon' => 'wrench')
        ));

        if (! Plugin::instance()->is_enabled('bbcodes')) {
            unset($this->menu_items['bbcodes']);
        }

	    if (! Plugin::instance()->is_enabled('thanks')) {
		    unset($this->menu_items['thanks-list']);
	    }

        if (! Plugin::instance()->is_enabled('report')) {
            unset($this->menu_items['reported-posts']);
        }
    }

    public function admin_init() {
        global $submenu;

        d4p_include('grid', 'admin', GDBBX_D4PLIB);

        if ( Plugin::instance()->is_enabled('canned-replies')) {
            if (isset($submenu['gd-bbpress-toolbox-front'])) {
                $index = count($this->menu_items);

                $canned = $submenu['gd-bbpress-toolbox-front'][$index];
                $canned[0] = __(CannedReplies::instance()->settings['post_type_plural'], "bbp-core");
                unset($submenu['gd-bbpress-toolbox-front'][$index]);

                array_splice($submenu['gd-bbpress-toolbox-front'], 4, 0, array($canned));
            }
        }
    }

    public function admin_menu() {
        $parent = 'gd-bbpress-toolbox-front';

        $this->page_ids[] = add_menu_page(
                'GD bbPress Toolbox Pro',
                'bbPress Toolbox',
                'gdbbx_moderation',
                        $parent,
                        array($this, 'panel_general'),
                        gdbbx_plugin()->svg_icon);

        foreach($this->menu_items as $item => $data) {
            $cap = isset($data['cap']) ? $data['cap'] : 'gdbbx_moderation';

            $this->page_ids[] = add_submenu_page($parent,
                            'GD bbPress Toolbox Pro: '.$data['title'],
                            $data['title'],
                            $cap,
                            'gd-bbpress-toolbox-'.$item,
                            array($this, 'panel_general'));
        }

        $this->admin_load_hooks();
    }

    public function get_post_type() {
        $post_type = '';

        if (isset($_GET['post_type'])) {
            $post_type = $_GET['post_type'];
        } else {
            global $post;

            if ($post) {
                $post_type = $post->post_type;
            }
        }

        if (in_array($post_type, array(
            bbp_get_forum_post_type(),
            bbp_get_topic_post_type(),
            bbp_get_reply_post_type()
        ))) {
            return $post_type;
        } else {
            return false;
        }
    }

    public function admin_load_hooks() {
        foreach ($this->page_ids as $id) {
            add_action('load-'.$id, array($this, 'load_admin_page'));
        }

	    do_action('gdbbx_admin_load_hooks');
    }

    public function load_admin_page() {
        do_action('gdbbx_load_admin_page_'.$this->page);

        if ($this->panel !== false && $this->panel != '') {
            do_action('gdbbx_load_admin_page_'.$this->page.'_'.$this->panel);
        }

        Help::instance()->plugin();
    }

    public function current_screen($screen) {
        if (isset($_GET['panel']) && $_GET['panel'] != '') {
            $this->panel = d4p_sanitize_slug($_GET['panel']);
        }

        $id = $screen->id;

        if ($id == 'toplevel_page_gd-bbpress-toolbox-front') {
            $this->page = 'front';
        } else if (substr($id, 0, 40) == 'bbpress-toolbox_page_gd-bbpress-toolbox-') {
            $this->page = substr($id, 40);
        }

        if (isset($_POST['gdbbx_handler']) && $_POST['gdbbx_handler'] == 'postback') {
            require_once(GDBBX_PATH.'core/admin/postback.php');

            new gdbbx_admin_postback();
        } else if (isset($_GET['gdbbx_handler']) && $_GET['gdbbx_handler'] == 'getback') {
            require_once(GDBBX_PATH.'core/admin/getback.php');

            new gdbbx_admin_getback();
        }
    }

    public function install_or_update() : bool {
        if (gdbbx()->is_install() && GDBBX_RUN_INSTALL) {
            include(GDBBX_PATH.'forms/install.php');

            return true;
        } else if (gdbbx()->is_update() && GDBBX_RUN_UPDATE) {
            include(GDBBX_PATH.'forms/update.php');

            return true;
        }

        return false;
    }

    public function panel_general() {
        if (!$this->install_or_update()) {
            $_current_page = $this->page;

            $path = apply_filters('gdbbx_admin_menu_panel_'.$_current_page, GDBBX_PATH.'forms/'.$_current_page.'.php');

            if (!file_exists($path)) {
                $path = GDBBX_PATH.'forms/shared/invalid.php';
            }

            include($path);
        }
    }
}

global $_gdbbx_core_admin;
$_gdbbx_core_admin = new gdbbx_admin_core();

function gdbbx_admin() : gdbbx_admin_core {
    global $_gdbbx_core_admin;
    return $_gdbbx_core_admin;
}
