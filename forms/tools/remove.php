<div class="d4p-group d4p-group-information">
    <h3><?php _e("Important", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <?php _e("This tool can remove plugin settings saved in the WordPress options table, individual settings for forums related to this plugin and individual settings for users related to this plugin (tracking and signature).", "bbp-core"); ?><br/><br/>
        <?php _e("Deletion operations are not reversible, and it is highly recommended to create database backup before proceeding with this tool.", "bbp-core"); ?> 
        <?php _e("If you choose to remove plugin settings, that will also reinitialize all plugin settings to default values.", "bbp-core"); ?>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Remove plugin settings", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][settings][all]" value="on" /> <?php _e("All The Settings", "bbp-core"); ?>
        </label>
        <div class="d4p-setting-hr" style="margin: 15px 0">
            <span><?php _e("Or, choose individual settings groups", "bbp-core"); ?></span>
            <hr>
        </div>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][settings][settings]" value="on" /> <?php _e("Basic Settings", "bbp-core"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][settings][features]" value="on" /> <?php _e("Features Settings and Features Load status", "bbp-core"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][settings][bbcodes]" value="on" /> <?php _e("BBCodes Activity Settings", "bbp-core"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][settings][online]" value="on" /> <?php _e("Online Tracking Settings", "bbp-core"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][settings][widgets]" value="on" /> <?php _e("Widgets Activation Settings", "bbp-core"); ?>
        </label>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Remove various meta data", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][forums]" value="on" /> <?php _e("All Forums Settings meta fields", "bbp-core"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][tracking]" value="on" /> <?php _e("All Users Latest activity meta field", "bbp-core"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][signature]" value="on" /> <?php _e("All Users Signatures meta fields", "bbp-core"); ?>
        </label>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Remove plugin CRON jobs", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][cron]" value="on" /> <?php _e("All Plugin CRON Jobs", "bbp-core"); ?>
        </label>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Remove database data and tables", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][drop]" value="on" /> <?php _e("Remove plugins database tables and all data in them", "bbp-core"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][truncate]" value="on" /> <?php _e("Remove all data from database tables", "bbp-core"); ?>
        </label>
        <div class="d4p-setting-hr" style="margin: 15px 0">
            <span><?php _e("Database tables that will be affected", "bbp-core"); ?></span>
            <hr>
        </div>
        <ul style="list-style: inside disc;">
            <li><?php echo gdbbx_db()->actions; ?></li>
            <li><?php echo gdbbx_db()->actionmeta; ?></li>
            <li><?php echo gdbbx_db()->attachments; ?></li>
            <li><?php echo gdbbx_db()->online; ?></li>
            <li><?php echo gdbbx_db()->tracker; ?></li>
        </ul>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Disable Plugin", "bbp-core"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdbbxtools[remove][disable]" value="on" /> <?php _e("Disable plugin", "bbp-core"); ?>
        </label>
    </div>
</div>
