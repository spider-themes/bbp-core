<?php

if (!defined('ABSPATH')) { exit; }

$panels = array(
    'index' => array(
        'title' => __("Settings Index", "bbp-core"), 'icon' => 'cogs', 
        'info' => __("All plugin settings are split into several panels, and you access each starting from the right.", "bbp-core")),
    'widgets' => array(
        'title' => __("Widgets", "bbp-core"), 'icon' => 'puzzle-piece',
        'break' => __("Basic Settings", "bbp-core"),
        'info' => __("Enable or disable widgets included in the plugin, and disable some default bbPress widgets.", "bbp-core")),
    'files' => array(
        'title' => __("JS/CSS Files", "bbp-core"), 'icon' => 'file-code-o',
        'info' => __("Some additional controls for JS and CSS files loaded by the plugin.", "bbp-core")),
    'tracking' => array(
        'title' => __("Users Tracking", "bbp-core"), 'icon' => 'location-arrow',
        'break' => __("New and Unread Topics Tracking", "bbp-core"),
        'info' => __("Control the user activity tracking in the forums for purpose of determining new and unread topics.", "bbp-core")),
    'topic_read' => array(
        'title' => __("For Topics", "bbp-core"), 'icon' => 'd4p-icon-bbpress-topic',
        'info' => __("Control the way topics activity is displayed inside the topics list.", "bbp-core")),
    'forum_read' => array(
        'title' => __("For Forums", "bbp-core"), 'icon' => 'd4p-icon-bbpress-forum',
        'info' => __("Control the way topics activity is displayed inside the forums list.", "bbp-core"))
);

include(GDBBX_PATH.'forms/shared/top.php');

?>

<form method="post" action="" id="gdbbx-form-settings" autocomplete="off">
    <?php settings_fields('gd-bbpress-toolbox-settings'); ?>
    <input type="hidden" name="gdbbx_handler" value="postback" />

    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-cogs"></i>
                <h3><?php _e("Settings", "bbp-core"); ?></h3>
                <?php if ($_panel != 'index') { ?>
                <h4><i aria-hidden="true" class="<?php echo d4p_get_icon_class($panels[$_panel]['icon']); ?>"></i> <?php echo $panels[$_panel]['title']; ?></h4>
                <?php } ?>
            </div>
            <div class="d4p-panel-info">
                <?php echo $panels[$_panel]['info']; ?>
            </div>
            <?php if ($_panel != 'index') { ?>
                <div class="d4p-panel-buttons">
                    <input type="submit" value="<?php _e("Save Settings", "bbp-core"); ?>" class="button-primary">
                </div>
            <?php } ?>
            <div class="d4p-return-to-top">
                <a href="#wpwrap"><?php _e("Return to top", "bbp-core"); ?></a>
            </div>
        </div>
    </div>
    <div class="d4p-content-right">
        <?php

        if ($_panel == 'index') {
            foreach ($panels as $panel => $obj) {
                if ($panel == 'index') continue;

                $url = 'admin.php?page=gd-bbpress-toolbox-'.$_page.'&panel='.$panel;

                if (isset($obj['break'])) { ?>

                    <div style="clear: both"></div>
                    <div class="d4p-panel-break d4p-clearfix">
                        <h4><?php echo $obj['break']; ?></h4>
                    </div>
                    <div style="clear: both"></div>

                <?php } ?>

                <div class="d4p-options-panel">
                    <i aria-hidden="true" class="<?php echo d4p_get_icon_class($obj['icon']); ?>"></i>
                    <h5 aria-label="<?php echo $obj['info']; ?>" data-balloon-pos="up-left" data-balloon-length="large"><?php echo $obj['title']; ?></h5>
                    <div>
                        <a class="button-primary" href="<?php echo $url; ?>"><?php _e("Settings", "bbp-core"); ?></a>
                    </div>
                </div>
        
                <?php
            }
        } else {
            require_once(GDBBX_PATH.'d4plib/admin/d4p.functions.php');
            require_once(GDBBX_PATH.'d4plib/admin/d4p.settings.php');

            include(GDBBX_PATH.'core/admin/internal.php');

            $options = new gdbbx_admin_settings();

            $panel = gdbbx_admin()->panel;
            $groups = $options->get($panel);

            $render = new d4pSettingsRender($panel, $groups);
            $render->base = 'gdbbxvalue';
            $render->render();

            ?>

            <div class="clear"></div>
            <div style="padding-top: 15px; border-top: 1px solid #777; max-width: 800px;">
                <input type="submit" value="<?php _e("Save Settings", "bbp-core"); ?>" class="button-primary">
            </div>

            <?php

        }

        ?>
    </div>
</form>

<?php 

include(GDBBX_PATH.'forms/shared/bottom.php');
