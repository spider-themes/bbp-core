<?php

use Dev4Press\Plugin\GDBBX\Tasks\Cleanup;

if (!defined('ABSPATH')) { exit; }

$_thanks_count = Cleanup::instance()->count_thanks_for_missing_posts();

?>

<div class="d4p-group d4p-group-information">
    <h3><?php _e("Important", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <?php _e("These tools will remove data from the database. Make sure to read information provided with each tool. Create database backup before using these tools to avoid data loss in case you change your mind.", "bbp-core"); ?>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Remove orphaned thanks records", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <p><?php _e("After you delete topics and replies, any thanks said to deleted topics and replies authors will remain in the database and it will be still counted towards user statistics. Using this tool, you can remove these orphaned thanks.", "bbp-core"); ?></p>
        <p><?php echo sprintf(__("Orphaned thanks found: %s.", "bbp-core"), '<strong>'.$_thanks_count.'</strong>'); ?></p>

        <?php if ($_thanks_count == 0) { ?>
            <p><strong><?php _e("Nothing found, this tool is currently unavailable", "bbp-core"); ?></strong></p>
        <?php } else { ?>
            <p><input type="checkbox" class="widefat" name="gdbbxtools[cleanup][thanks]" value="on" /> <?php _e("Delete all orphaned thanks records", "bbp-core"); ?></p>
        <?php } ?>
    </div>
</div>
