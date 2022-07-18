<?php

$thanks = gdbbx_db()->thanks_statistics();
$latest = gdbbx_db()->thanks_list_recent(5);

?>

<div class="d4p-group d4p-group-dashboard-card d4p-group-dashboard-thanks">
    <h3><?php _e("Thanks for Topics and Replies", "bbp-core"); ?></h3>
    <div class="d4p-group-stats">
        <ul>
            <li><a href="admin.php?page=gd-bbpress-toolbox-thanks-list&filter-type=<?php echo bbp_get_topic_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-topic d4p-icon-fw"></i> 
                    <strong><?php echo isset($thanks['types'][bbp_get_topic_post_type()]) ? $thanks['types'][bbp_get_topic_post_type()] : 0; ?></strong> 
                    <?php _e("For topics", "bbp-core"); ?></a>
            </li>
            <li><a href="admin.php?page=gd-bbpress-toolbox-thanks-list&filter-type=<?php echo bbp_get_reply_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-reply d4p-icon-fw"></i> 
                    <strong><?php echo isset($thanks['types'][bbp_get_reply_post_type()]) ? $thanks['types'][bbp_get_reply_post_type()] : 0; ?></strong> 
                    <?php _e("For replies", "bbp-core"); ?></a>
            </li>
        </ul><div class="d4p-clearfix"></div>
        <hr/>
        <ul>
            <li><a href="admin.php?page=gd-bbpress-toolbox-thanks-list">
                    <i aria-hidden="true" class="fa fa-users fa-fw"></i> 
                    <strong><?php echo isset($thanks['users']['from']) ? $thanks['users']['from'] : 0; ?></strong> 
                    <?php _e("From users", "bbp-core"); ?></a>
            </li>
            <li><a href="admin.php?page=gd-bbpress-toolbox-thanks-list">
                    <i aria-hidden="true" class="fa fa-users fa-fw"></i> 
                    <strong><?php echo isset($thanks['users']['to']) ? $thanks['users']['to'] : 0; ?></strong> 
                    <?php _e("To users", "bbp-core"); ?></a>
            </li>
        </ul><div class="d4p-clearfix"></div>
    </div>
    <div class="d4p-group-inner">
        <h4><?php _e("Recent Thanks", "bbp-core"); ?></h4>
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
                    $_user_link = bbp_get_user_profile_link($report->user_id);
                } else {
                    $_user_link = __("Unknown", "bbp-core");
                }

                $user = get_user_by('id', $report->post_author);

                if ($user) {
                    $_author_link = bbp_get_user_profile_link($report->post_author);
                } else {
                    $_author_link = __("Unknown", "bbp-core");
                }

                $_template = $report->post_type == bbp_get_topic_post_type() ? _x("%s thanked %s for topic %s.", "Dashboard thanks widget list items", "bbp-core") : _x("%s thanked %s for reply %s.", "Dashboard thanks widget list items", "bbp-core");

                echo sprintf($_template, $_user_link, $_author_link, '<a href="'.$url.'">'.$title.'</a>');

                ?>
            </li>
        <?php } ?>

        </ul><?php

        }

        ?>
    </div>
    <div class="d4p-group-footer">
        <a href="admin.php?page=gd-bbpress-toolbox-thanks-list" class="button-primary"><?php _e("All Thanks List", "bbp-core"); ?></a>
    </div>
</div>
