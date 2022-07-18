<?php

if (!defined('ABSPATH')) { exit; }

$_classes = array('d4p-wrap', 'wpv-'.GDBBX_WPV, 'd4p-page-install');

?>
<div class="<?php echo join(' ', $_classes); ?>">
    <div class="d4p-header">
        <div class="d4p-plugin">
            GD bbPress Toolbox Pro
        </div>
    </div>
    <div class="d4p-content">
        <div class="d4p-content-left">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-magic"></i>
                <h3><?php _e("Installation", "bbp-core"); ?></h3>
            </div>
            <div class="d4p-panel-info">
                <?php _e("Before you continue, make sure plugin installation was successful.", "bbp-core"); ?>
            </div>
        </div>
        <div class="d4p-content-right">
            <div class="d4p-update-info">
                <?php

                include(GDBBX_PATH.'forms/setup/db.php');
                include(GDBBX_PATH.'forms/setup/transfer.php');
                include(GDBBX_PATH.'forms/setup/forums.php');

                ?>

                <h3><?php _e("All Done", "bbp-core"); ?></h3>
                <?php

                    gdbbx()->set('install', false, 'info');
                    gdbbx()->set('update', false, 'info', true);

                    _e("Installation completed.", "bbp-core");

                ?>
                <br/><br/><a class="button-primary" href="admin.php?page=gd-bbpress-toolbox-about&install=on"><?php _e("Click here to continue.", "bbp-core"); ?></a>
            </div>
            <?php echo gdbbx_plugin()->recommend('install'); ?>
        </div>
    </div>
</div>
