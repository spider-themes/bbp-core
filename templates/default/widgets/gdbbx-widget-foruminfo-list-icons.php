<div class="gdbbx-widget-the-info-list">
    <?php if (isset($results['show_parent_forum'])) { ?>
    <div class="gdbbx-widget-the-info-parent-forum">
        <label><i class="gdbbx-icon gdbbx-fw gdbbx-icon-<?php echo $results['show_parent_forum']['icon']; ?>"></i> <?php echo $results['show_parent_forum']['label']; ?>: </label>
        <span><?php echo $results['show_parent_forum']['value']; ?></span>
    </div>
    <?php } ?>

    <?php if (isset($results['show_count_topics'])) { ?>
    <div class="gdbbx-widget-the-info-count gdbbx-widget-the-info-count-topics">
        <span><i class="gdbbx-icon gdbbx-fw gdbbx-icon-<?php echo $results['show_count_topics']['icon']; ?>"></i> <?php echo sprintf($results['show_count_topics']['label_alt'], $results['show_count_topics']['value']); ?></span>
    </div>
    <?php } ?>

    <?php if (isset($results['show_count_replies'])) { ?>
    <div class="gdbbx-widget-the-info-count gdbbx-widget-the-info-count-replies">
        <span><i class="gdbbx-icon gdbbx-fw gdbbx-icon-<?php echo $results['show_count_replies']['icon']; ?>"></i> <?php echo sprintf($results['show_count_replies']['label_alt'], $results['show_count_replies']['value']); ?></span>
    </div>
    <?php } ?>

    <?php if (isset($results['show_last_post_user'])) { ?>
    <div class="gdbbx-widget-the-info-last-post-user">
        <label><i class="gdbbx-icon gdbbx-fw gdbbx-icon-<?php echo $results['show_last_post_user']['icon']; ?>"></i> <?php echo $results['show_last_post_user']['label']; ?>: </label>
        <span><?php echo $results['show_last_post_user']['value']; ?></span>
    </div>
    <?php } ?>

    <?php if (isset($results['show_last_activity'])) { ?>
    <div class="gdbbx-widget-the-info-last-activity">
        <label><i class="gdbbx-icon gdbbx-fw gdbbx-icon-<?php echo $results['show_last_activity']['icon']; ?>"></i> <?php echo $results['show_last_activity']['label']; ?>: </label>
        <span><?php echo $results['show_last_activity']['value']; ?></span>
    </div>
    <?php } ?>

    <?php if (isset($results['show_subscribe'])) { ?>
    <div class="gdbbx-widget-the-info-subscribe">
        <span><i class="gdbbx-icon gdbbx-fw gdbbx-icon-<?php echo $results['show_subscribe']['icon']; ?>"></i> <?php echo $results['show_subscribe']['value']; ?></span>
    </div>
    <?php } ?>
</div>