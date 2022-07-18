<?php

$reports = gdbbx_db()->report_statistics();
$latest = gdbbx_db()->report_list_recent(5);

?>

<div class="d4p-group d4p-group-dashboard-card d4p-group-dashboard-report">
    <h3><?php _e("Reported Topics and Replies", "bbp-core"); ?></h3>
    <div class="d4p-group-stats">
        <ul>
            <li><a href="admin.php?page=gd-bbpress-toolbox-reported-posts&filter-type=<?php echo bbp_get_topic_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-topic d4p-icon-fw"></i> 
                    <strong><?php echo isset($reports['topic']['total']) ? $reports['topic']['total'] : 0; ?></strong> 
                    <?php _e("For topics", "bbp-core"); ?></a>
            </li>
            <li><a href="admin.php?page=gd-bbpress-toolbox-reported-posts&filter-type=<?php echo bbp_get_topic_post_type(); ?>&filter-status=waiting">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-topic d4p-icon-fw"></i> 
                    <strong><?php echo isset($reports['topic']['active']) ? $reports['topic']['active'] : 0; ?></strong> 
                    <?php _e("Open reports", "bbp-core"); ?></a>
            </li>
        </ul><div class="d4p-clearfix"></div>
        <hr/>
        <ul>
            <li><a href="admin.php?page=gd-bbpress-toolbox-reported-posts&filter-type=<?php echo bbp_get_reply_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-reply d4p-icon-fw"></i> 
                    <strong><?php echo isset($reports['reply']['total']) ? $reports['reply']['total'] : 0; ?></strong> 
                    <?php _e("For replies", "bbp-core"); ?></a>
            </li>
            <li><a href="admin.php?page=gd-bbpress-toolbox-reported-posts&filter-type=<?php echo bbp_get_reply_post_type(); ?>&filter-status=waiting">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-reply d4p-icon-fw"></i> 
                    <strong><?php echo isset($reports['reply']['active']) ? $reports['reply']['active'] : 0; ?></strong> 
                    <?php _e("Open reports", "bbp-core"); ?></a>
            </li>
        </ul><div class="d4p-clearfix"></div>
    </div>
    <div class="d4p-group-inner">
        <h4><?php _e("Recent Reports", "bbp-core"); ?></h4>
        <?php

        if (empty($latest)) {

        ?><p><?php _e("Nothing to report.", "bbp-core"); ?></p><?php

        } else {

        ?><ul>

        <?php foreach ($latest as $report) { ?>
            <li>
                <?php 

                $url = bbp_is_topic($report->post_id) ? get_permalink($report->post_id) : bbp_get_reply_url($report->post_id);
                $title = bbp_is_topic($report->post_id) ? bbp_get_topic_title($report->post_id) : bbp_get_reply_title($report->post_id);

                $user = get_user_by('id', $report->user_id);

                if ($user) {
                    $_profile_link = bbp_get_user_profile_link($report->user_id);
                } else {
                    $_profile_link = __("Unknown", "bbp-core");
                }

                $_template = $report->post_type == bbp_get_topic_post_type() ? _x("%s reported topic %s: '%s'.", "Dashboard reports widget list items", "bbp-core") : _x("%s reported reply %s: '%s'.", "Dashboard reports widget list items", "bbp-core");

                echo sprintf($_template, $_profile_link, '<a href="'.$url.'">'.$title.'</a>', $report->report);

                ?>
            </li>
        <?php } ?>

        </ul><?php

        }

        ?>
    </div>
    <div class="d4p-group-footer">
        <a href="admin.php?page=gd-bbpress-toolbox-reported-posts" class="button-primary"><?php _e("All Reported Posts", "bbp-core"); ?></a>
    </div>
</div>
