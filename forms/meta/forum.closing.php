<?php

use Dev4Press\Plugin\GDBBX\Features\AutoCloseTopics;

global $_meta;

?>

<p>
    <label for="gdbbx_settings_topic_auto_close_after_active">
    <?php _e("Auto close topics", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_settings(), array('id' => 'gdbbx_settings_topic_auto_close_after_active', 'name' => 'gdbbx_settings[topic_auto_close_after_active]', 'class' => 'widefat', 'selected' => $_meta['topic_auto_close_after_active'])); ?>
</p>
<p>
    <label for="gdbbx_settings_topic_auto_close_after_notice"><?php _e("Show notice in reply form", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_settings(), array('id' => 'gdbbx_settings_topic_auto_close_after_notice', 'name' => 'gdbbx_settings[topic_auto_close_after_notice]', 'class' => 'widefat', 'selected' => $_meta['topic_auto_close_after_notice'])); ?>
</p>
<p>
    <label for="gdbbx_settings_topic_auto_close_after_days"><?php _e("Close after days", "bbp-core"); ?></label>
    <input name="gdbbx_settings[topic_auto_close_after_days]" id="gdbbx_settings_topic_auto_close_after_days" value="<?php echo esc_attr($_meta['topic_auto_close_after_days']) ?>" class="widefat" type="number" min="<?php echo esc_attr(AutoCloseTopics::minimum_days_allowed()); ?>" step="1" />
    <em><?php _e("Leave empty to use global number of days.", "bbp-core"); ?></em>
</p>
