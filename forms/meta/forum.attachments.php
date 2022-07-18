<?php

use Dev4Press\Plugin\GDBBX\Basic\BB;
use Dev4Press\Plugin\GDBBX\Basic\Helper;

global $_meta;

?>

<input type="hidden" name="gdbbatt_forum_meta" value="edit" />

<h4><?php _e("Attachments Status", "bbp-core"); ?>:</h4>
<p>
    <label for="gdbbx_settings_attachments_status"><?php _e("Enable Attachments", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_settings(), array('id' => 'gdbbx_settings_attachments_status', 'name' => 'gdbbx_settings[attachments_status]', 'class' => 'widefat', 'selected' => $_meta['attachments_status'])); ?>
</p>

<p>
    <label for="gdbbx_settings_attachments_hide_from_visitors"><?php _e("Hide uploaded files from visitors", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_settings(), array('id' => 'gdbbx_settings_attachments_hide_from_visitors', 'name' => 'gdbbx_settings[attachments_hide_from_visitors]', 'class' => 'widefat', 'selected' => $_meta['attachments_hide_from_visitors'])); ?>
</p>

<p>
    <label for="gdbbx_settings_attachments_preview_for_visitors"><?php _e("Show only files previews to visitors", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_settings(), array('id' => 'gdbbx_settings_attachments_preview_for_visitors', 'name' => 'gdbbx_settings[attachments_preview_for_visitors]', 'class' => 'widefat', 'selected' => $_meta['attachments_preview_for_visitors'])); ?>
</p>

<hr/>

<h4><?php _e("Topic and Reply Forms", "bbp-core"); ?>:</h4>
<p>
    <label for="gdbbx_settings_attachments_topic_form"><?php _e("Add to topic form", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_settings(), array('id' => 'gdbbx_settings_attachments_topic_form', 'name' => 'gdbbx_settings[attachments_topic_form]', 'class' => 'widefat', 'selected' => $_meta['attachments_topic_form'])); ?>
</p>

<p>
    <label for="gdbbx_settings_attachments_reply_form"><?php _e("Add to reply form", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_settings(), array('id' => 'gdbbx_settings_attachments_reply_form', 'name' => 'gdbbx_settings[attachments_reply_form]', 'class' => 'widefat', 'selected' => $_meta['attachments_reply_form'])); ?>
</p>

<hr/>

<h4><?php _e("Allowed size", "bbp-core"); ?>:</h4>
<p>
    <label for="gdbbx_settings_attachments_max_file_size_override"><?php _e("Status", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_override(), array('id' => 'gdbbx_settings_attachments_max_file_size_override', 'name' => 'gdbbx_settings[attachments_max_file_size_override]', 'class' => 'widefat gdbbx-override', 'selected' => $_meta['attachments_max_file_size_override'])); ?>
</p>
<div style="display: <?php echo $_meta['attachments_max_file_size_override'] == 'yes' ? 'block' : 'none'; ?>;">
    <label for="gdbbx_settings_attachments_max_file_size"><?php _e("Maximum file size allowed", "bbp-core"); ?></label>
    <input type="number" class="widefat" value="<?php echo $_meta['attachments_max_file_size']; ?>" name="gdbbx_settings[attachments_max_file_size]" id="gdbbx_settings_attachments_max_file_size" min="1" step="1" max="<?php echo Helper::instance()->max_server_allowed(); ?>" />
</div>

<hr/>

<h4><?php _e("Number of files", "bbp-core"); ?>:</h4>
<p>
    <label for="gdbbx_settings_attachments_max_to_upload_override"><?php _e("Status", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_override(), array('id' => 'gdbbx_settings_attachments_max_to_upload_override', 'name' => 'gdbbx_settings[attachments_max_to_upload_override]', 'class' => 'widefat gdbbx-override', 'selected' => $_meta['attachments_max_to_upload_override'])); ?>
</p>
<div style="display: <?php echo $_meta['attachments_max_to_upload_override'] == 'yes' ? 'block' : 'none'; ?>;">
    <label for="gdbbx_settings_attachments_max_to_upload"><?php _e("Maximum files to upload", "bbp-core"); ?></label>
    <input type="number" class="widefat" value="<?php echo $_meta['attachments_max_to_upload']; ?>" name="gdbbx_settings[attachments_max_to_upload]" id="gdbbx_settings_attachments_max_to_upload" min="1" step="1" />
</div>

<hr/>

<h4><?php _e("Allowed MIME types", "bbp-core"); ?>:</h4>
<p>
    <label for="gdbbx_settings_attachments_mime_types_list_override"><?php _e("Status", "bbp-core"); ?></label>
    <?php d4p_render_select(gdbbx_select_forum_override(), array('id' => 'gdbbx_settings_attachments_mime_types_list_override', 'name' => 'gdbbx_settings[attachments_mime_types_list_override]', 'class' => 'widefat gdbbx-override', 'selected' => $_meta['attachments_mime_types_list_override'])); ?>
</p>
<div class="d4plib-metabox-checkboxes" style="display: <?php echo $_meta['attachments_mime_types_list_override'] == 'yes' ? 'block' : 'none'; ?>;">
    <div class="d4plib-metabox-check-uncheck">
        <a href="#checkall"><?php _e("Check All", "bbp-core"); ?></a>
         | <a href="#uncheckall"><?php _e("Uncheck All", "bbp-core"); ?></a>
    </div>

    <?php

    $mime_types = BB::i()->get_mime_types_list();

    $name_base = 'gdbbx_settings[attachments_mime_types_list][]';
    $value = $_meta['attachments_mime_types_list'];
    $value = is_null($value) || (is_array($value) && empty($value)) ? array_keys($mime_types) : (array)$value;

    foreach ($mime_types as $mime => $label) {
        $sel = in_array($mime, $value) ? ' checked="checked"' : '';

        echo sprintf('<label><input type="checkbox" value="%s" name="%s"%s class="widefat" />%s</label>', 
                $mime, $name_base, $sel, $label);
    }

    ?>
</div>