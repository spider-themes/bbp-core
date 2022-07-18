<?php

use Dev4Press\Plugin\GDBBX\Basic\Plugin;
use Dev4Press\Plugin\GDBBX\Basic\Statistics;

$statistics = Statistics::instance()->forums_stats();

?>

<div class="d4p-group d4p-group-dashboard-card d4p-group-dashboard-basic">
    <h3><?php _e("Forums Status", "bbp-core"); ?></h3>
    <div class="d4p-group-stats">
        <ul>
            <li><a href="edit.php?post_type=<?php echo bbp_get_forum_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-forum d4p-icon-fw"></i> 
                    <strong><?php echo $statistics['forum_count']; ?></strong> 
                    <?php _e("Forums", "bbp-core"); ?></a>
            </li>
            <li><a href="edit-tags.php?taxonomy=<?php echo bbp_get_topic_tag_tax_id(); ?>&post_type=<?php echo bbp_get_topic_post_type(); ?>">
                    <i aria-hidden="true" class="fa fa-tags fa-fw"></i> 
                    <strong><?php echo $statistics['topic_tag_count']; ?></strong> 
                    <?php _e("Topic Tags", "bbp-core"); ?></a>
            </li>
            <li><a href="edit.php?post_type=<?php echo bbp_get_reply_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-reply d4p-icon-fw"></i> 
                    <strong><?php echo $statistics['reply_count']; ?></strong> 
                    <?php _e("Replies", "bbp-core"); ?></a>
            </li>
            <li><a href="edit.php?post_type=<?php echo bbp_get_topic_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-topic d4p-icon-fw"></i> 
                    <strong><?php echo $statistics['topic_count']; ?></strong> 
                    <?php _e("Topics", "bbp-core"); ?></a>
            </li>
        </ul><div class="d4p-clearfix"></div>
        <hr/>
        <ul>
            <li><a href="edit.php?post_status=<?php echo bbp_get_closed_status_id(); ?>&post_type=<?php echo bbp_get_topic_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-topic d4p-icon-fw"></i> 
                    <strong><?php echo $statistics['topic_count_closed']; ?></strong> 
                    <?php _e("Closed Topics", "bbp-core"); ?></a>
            </li>
            <?php if ( Plugin::instance()->is_enabled('canned-replies')) { ?>
            <li><a href="edit.php?post_type=bbx_canned_reply">
                    <i aria-hidden="true" class="fa fa-reply fa-fw"></i> 
                    <strong><?php echo $statistics['canned_replies_count']; ?></strong> 
                    <?php _e("Canned Replies", "bbp-core"); ?></a>
            </li>
            <?php } ?>
        </ul><div class="d4p-clearfix"></div>
        <hr/>
        <ul>
            <li><a href="admin.php?page=gd-bbpress-toolbox-attachments">
                    <i aria-hidden="true" class="fa fa-paperclip fa-fw"></i>
                    <strong><?php echo $statistics['attachments_count']; ?></strong>
                    <?php _e("Attachments", "bbp-core"); ?></a>
            </li>
            <li><a href="admin.php?page=gd-bbpress-toolbox-attachments">
                    <i aria-hidden="true" class="fa fa-paperclip fa-fw"></i>
                    <strong><?php echo $statistics['attachments_unique']; ?></strong>
                    <?php _e("Unique Attachments", "bbp-core"); ?></a>
            </li>
            <li><a href="admin.php?page=gd-bbpress-toolbox-attachments&filter-attached=<?php echo bbp_get_topic_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-topic d4p-icon-fw"></i>
                    <strong><?php echo $statistics['attachments_topic_count']; ?></strong>
                    <?php _e("Attachments in Topics", "bbp-core"); ?></a>
            </li>
            <li><a href="admin.php?page=gd-bbpress-toolbox-attachments&filter-attached=<?php echo bbp_get_reply_post_type(); ?>">
                    <i aria-hidden="true" class="d4p-icon d4p-icon-bbpress-reply d4p-icon-fw"></i>
                    <strong><?php echo $statistics['attachments_reply_count']; ?></strong>
                    <?php _e("Attachments in Replies", "bbp-core"); ?></a>
            </li>
        </ul><div class="d4p-clearfix"></div>
    </div>
    <div class="d4p-group-inner">
        <h4><?php _e("Recent Activity", "bbp-core"); ?></h4>
        <p>
            <?php

            $day = gdbbx_db()->count_recent_posts(DAY_IN_SECONDS, null, true);

            echo '<label>'.__("In the past 24 hours", "bbp-core").':</label>';

            $topic = isset($day[bbp_get_topic_post_type()]) ? $day[bbp_get_topic_post_type()] : 0;
            $reply = isset($day[bbp_get_reply_post_type()]) ? $day[bbp_get_reply_post_type()] : 0;

            $_topics = sprintf(_n("%s Topic", "%s Topics", $topic, "bbp-core"), $topic);
            $_replies = sprintf(_n("%s Reply", "%s Replies", $reply, "bbp-core"), $reply);

            echo sprintf(__("Published <strong>%s</strong> and <strong>%s</strong>.", "bbp-core"), $_topics, $_replies);

            ?>
        </p>
        <p>
            <?php

            $week = gdbbx_db()->count_recent_posts(WEEK_IN_SECONDS, null, true);

            echo '<label>'.__("In the past 7 days", "bbp-core").':</label>';

            $topic = isset($week[bbp_get_topic_post_type()]) ? $week[bbp_get_topic_post_type()] : 0;
            $reply = isset($week[bbp_get_reply_post_type()]) ? $week[bbp_get_reply_post_type()] : 0;

            $_topics = sprintf(_n("%s Topic", "%s Topics", $topic, "bbp-core"), $topic);
            $_replies = sprintf(_n("%s Reply", "%s Replies", $reply, "bbp-core"), $reply);

            echo sprintf(__("Published <strong>%s</strong> and <strong>%s</strong>.", "bbp-core"), $_topics, $_replies);

            ?>
        </p>
    </div>
    <div class="d4p-group-footer">
        <a href="<?php echo get_post_type_archive_link(bbp_get_forum_post_type()); ?>" class="button-primary"><?php _e("Forums Index", "bbp-core"); ?></a>
    </div>
</div>