<?php

use Dev4Press\Plugin\GDBBX\Admin\Features;

if (!defined('ABSPATH')) { exit; }

$panels = array_merge(array(
    'index' => array(
        'title' => __("Features Index", "bbp-core"), 'icon' => 'puzzle-piece',
        'info' => __("This panel shows many of the plugin features, and from here you can enable or disable, and configure plugin features.", "bbp-core"))
    ), Features::instance()->get_features_for_display()
);

include(GDBBX_PATH.'forms/shared/top.php');

?>

<form method="post" action="" id="gdbbx-form-settings" autocomplete="off">
    <?php settings_fields('gd-bbpress-toolbox-settings'); ?>
    <input type="hidden" name="gdbbx_handler" value="postback" />

    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-puzzle-piece"></i>
                <h3><?php _e("Features", "bbp-core"); ?></h3>
                <?php if ($_panel != 'index') { ?>
                    <h4><i aria-hidden="true" class="<?php echo d4p_get_icon_class($panels[$_panel]['icon']); ?>"></i> <?php echo $panels[$_panel]['title']; ?></h4>
                <?php } ?>
            </div>
            <div class="d4p-panel-info">
                <?php echo $panels[$_panel]['info']; ?>
            </div>
            <?php if ($_panel != 'index') { ?>
                <div class="gdbbx-feature-more-control">
                    <button class="button-primary d4p-bulk-ctrl" type="button"><?php _e("More Controls", "bbp-core"); ?></button>

                    <div class="gdbbx-inner-ctrl-options">
                        <?php _e("If you want, you can reset all the settings for this Feature to default values.", "bbp-core"); ?>
                        <a href="<?php echo gdbbx_admin()->current_url(true); ?>&gdbbx_handler=getback&action=reset-feature&feature=<?php echo $_panel; ?>&_wpnonce=<?php echo wp_create_nonce('gdbbx-reset-feature-'.$_panel); ?>" class="button-primary"><?php _e("Reset Feature Settings", "bbp-core"); ?></a>
                    </div>
                </div>
                <div class="d4p-panel-buttons gdbbx-feature-submit">
                    <input type="submit" value="<?php _e("Save Settings", "bbp-core"); ?>" class="button-primary">
                </div>
            <?php } else { ?>
                <div class="gdbbx-features-bulk-control">
                    <button class="button-primary d4p-bulk-ctrl" type="button"><?php _e("Bulk Control", "bbp-core"); ?></button>
                    <div class="gdbbx-inner-ctrl-options">
                        <?php

                        echo '<a href="#checkall">'.__("Check All", "bbp-core").'</a>';
                        echo ' &middot; <a href="#uncheckall">'.__("Uncheck All", "bbp-core").'</a>';

                        ?>

                        <input type="submit" value="<?php _e("Save Settings", "bbp-core"); ?>" class="button-primary">
                    </div>
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
            $checkbox = false;

            foreach ($panels as $panel => $obj) {
                if ($panel == 'index') continue;

                switch ($obj['scope']) {
                    case 'admin':
                        $_scope = _x("Admin", "Feature Scope", "bbp-core");
                        break;
                    case 'front':
                        $_scope = _x("Frontend", "Feature Scope", "bbp-core");
                        break;
                    default:
                    case 'global':
                        $_scope = _x("Global", "Feature Scope", "bbp-core");
                        break;
                }

                $url = 'admin.php?page=gd-bbpress-toolbox-'.$_page.'&panel='.$panel;

                if (isset($obj['break'])) { ?>

                    <div style="clear: both"></div>
                    <div class="d4p-panel-break d4p-clearfix">
                        <h4><?php echo $obj['break']; ?></h4>
                    </div>
                    <div style="clear: both"></div>

                <?php } ?>

                <div class="d4p-options-panel gdbbx-feature-scope-<?php echo $obj['scope']; ?>">
                    <i aria-hidden="true" class="<?php echo d4p_get_icon_class($obj['icon']); ?>"></i>
                    <h5 aria-label="<?php echo $obj['info']; ?>" data-balloon-pos="up-left" data-balloon-length="large"><?php echo $obj['title']; ?></h5>
                    <div>
                        <?php

                        echo '<span class="feature-scope">'.$_scope.'</span>';

                        if ($obj['status'] != 'required') {
                            echo '<input class="feature-status" type="checkbox" name="gdbbxvalue[load]['.$panel.']" value="on"'.($obj['status'] == 'enabled' ? ' checked="checked"' : '').' />';
                        } else {
                            echo '<input name="gdbbxvalue[load]['.$panel.']" value="on" type="hidden" />';
                        }

                        ?>
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
