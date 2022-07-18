<div class="d4p-group d4p-group-information">
    <h3><?php _e("Important", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <?php _e("With this tool you import all plugin settings from the JSON formatted file made using the Export tool. If you made changes to this file, the import will not be possible.", "bbp-core"); ?><br/><br/>
        <strong><?php _e("Export file created with the plugin version before 4.2 can't be imported!", "bbp-core"); ?></strong>
    </div>
</div>

<div class="d4p-group d4p-group-information">
    <h3><?php _e("Import File", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <?php _e("Select file you want to import", "bbp-core"); ?>:
        <br/><br/>
        <input type="file" name="import_file" accept=".json" />
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Settings to Import", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input checked="checked" type="checkbox" class="widefat" name="gdbbxtools[import][settings]" value="on" /> <?php _e("Basic Settings", "bbp-core"); ?>
        </label>
        <label>
            <input checked="checked" type="checkbox" class="widefat" name="gdbbxtools[import][attachments]" value="on" /> <?php _e("Attachments Settings", "bbp-core"); ?>
        </label>
        <label>
            <input checked="checked" type="checkbox" class="widefat" name="gdbbxtools[import][features]" value="on" /> <?php _e("Features Settings and features Load status", "bbp-core"); ?>
        </label>
        <label>
            <input checked="checked" type="checkbox" class="widefat" name="gdbbxtools[import][online]" value="on" /> <?php _e("Online Tracking Settings", "bbp-core"); ?>
        </label>
        <label>
            <input checked="checked" type="checkbox" class="widefat" name="gdbbxtools[import][seo]" value="on" /> <?php _e("SEO Settings", "bbp-core"); ?>
        </label>
        <label>
            <input checked="checked" type="checkbox" class="widefat" name="gdbbxtools[import][widgets]" value="on" /> <?php _e("Widgets Activation Settings", "bbp-core"); ?>
        </label>
        <label>
            <input checked="checked" type="checkbox" class="widefat" name="gdbbxtools[import][buddypress]" value="on" /> <?php _e("BuddyPress Related Settings", "bbp-core"); ?>
        </label>
    </div>
</div>
