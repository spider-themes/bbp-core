<?php

$views = array();

$_views = array(
    array('group' => 'general', 'name' => 'bbx-home', 'title' => __("Forums Home", "bbp-core")),
    array('group' => 'profile', 'name' => 'bbx-profile', 'title' => __("Profile", "bbp-core")),
    array('group' => 'profile', 'name' => 'bbx-topics', 'title' => __("Topics Started", "bbp-core")),
    array('group' => 'profile', 'name' => 'bbx-replies', 'title' => __("Replies Created", "bbp-core")),
    array('group' => 'profile', 'name' => 'bbx-favorites', 'title' => __("Favorites", "bbp-core")),
    array('group' => 'profile', 'name' => 'bbx-subscriptions', 'title' => __("Subscriptions", "bbp-core")),
    array('group' => 'profile', 'name' => 'bbx-edit', 'title' => __("Profile Edit", "bbp-core")),
    array('group' => 'access', 'name' => 'bbx-login', 'title' => __("Login", "bbp-core")),
    array('group' => 'access', 'name' => 'bbx-logout', 'title' => __("Logout", "bbp-core")),
    array('group' => 'access', 'name' => 'bbx-register', 'title' => __("Register", "bbp-core"))
);

foreach ($_views as $item) {
    $view = new stdClass();

    $view->classes = array();
    $view->type = $item['name'];
    $view->object_id = $item['name'];
    $view->title = $item['title'];
    $view->object = 'bbx-extra';

    $view->menu_item_parent = null;
    $view->url = null;
    $view->xfn = null;
    $view->db_id = null;
    $view->target = null;
    $view->attr_title = null;

    $views[$item['group']][$item['name']] = $view;
}

$walker = new Walker_Nav_Menu_Checklist(array());

?>
<div id="bbx-extra" class="posttypediv">
    <ul class="taxonomy-tabs add-menu-item-tabs" id="taxonomy-category-tabs">
        <li class="tabs"><?php _e("Extra Pages", "bbp-core"); ?></li>
    </ul>
    <div id="tabs-panel-bbx-extra" class="tabs-panel tabs-panel-active">
        <h4><?php _e("General Links", "bbp-core"); ?></h4>
        <ul id="bbx-extra-checklist" class="categorychecklist form-no-clear">
        <?php

            echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $views['general']), 0, (object) array('walker' => $walker));

        ?>
        </ul>

        <h4><?php _e("Logged user Profile", "bbp-core"); ?></h4>
        <ul id="bbx-extra-checklist" class="categorychecklist form-no-clear">
        <?php

            echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $views['profile']), 0, (object) array('walker' => $walker));

        ?>
        </ul>

        <h4><?php _e("Account Access", "bbp-core"); ?></h4>
        <ul id="bbx-extra-checklist" class="categorychecklist form-no-clear">
        <?php

            echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $views['access']), 0, (object) array('walker' => $walker));

        ?>
        </ul>
    </div>
</div>
<p class="button-controls">
    <span class="add-to-menu">
        <input type="submit" class="button-secondary submit-add-to-menu" value="<?php esc_attr_e("Add to Menu", "bbp-core"); ?>" name="add-bbx-extra-menu-item" id="submit-bbx-extra" />
        <span class="spinner"></span>
    </span>
</p>