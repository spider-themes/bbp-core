<div>
    <p>
        <?php _e("One of the most important aspects in providing best experience to forum users, is to know what new content is available for each user when they visit back, including new topics, new replies, unread topics and more. To provide each user with such information, plugin can track each user activity.", "bbp-core"); ?>
    </p>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to track users online status?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("Plugin will track online status for each user and overall of all currently online users and visitors.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][tracking][online]" value="yes" id="gdbbx-wizard-tracking-online-yes" checked />
            <label for="gdbbx-wizard-tracking-online-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][tracking][online]" value="no" id="gdbbx-wizard-tracking-online-no" />
            <label for="gdbbx-wizard-tracking-online-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to track user activity and read status for content?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("Plugin will track topic read status for each topic and each user. Based on that, it will be able to display information about new and unread content on repeat visits.", "bbp-core"); ?></em>
        <span>
            <input class="gdbbx-wizard-connect-switch" data-connect="gdbbx-wizard-connect-tracking-activity" type="radio" name="gdbbx[wizard][tracking][activity]" value="yes" id="gdbbx-wizard-tracking-activity-yes" checked />
            <label for="gdbbx-wizard-tracking-activity-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input class="gdbbx-wizard-connect-switch" data-connect="gdbbx-wizard-connect-tracking-activity" type="radio" name="gdbbx[wizard][tracking][activity]" value="no" id="gdbbx-wizard-tracking-activity-no" />
            <label for="gdbbx-wizard-tracking-activity-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-connect-wrapper" id="gdbbx-wizard-connect-tracking-activity" style="display: block;">
    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php _e("Do you want to show activity badges for topics?", "bbp-core"); ?></p>
        <div>
            <em><?php _e("Based on the tracking data, plugin can show badges for new topics, unread topics, topics with new replies.", "bbp-core"); ?></em>
            <span>
                <input type="radio" name="gdbbx[wizard][tracking][topics]" value="yes" id="gdbbx-wizard-tracking-topics-yes" checked />
                <label for="gdbbx-wizard-tracking-topics-yes"><?php _e("Yes", "bbp-core"); ?></label>
            </span>
            <span>
                <input type="radio" name="gdbbx[wizard][tracking][topics]" value="no" id="gdbbx-wizard-tracking-topics-no" />
                <label for="gdbbx-wizard-tracking-topics-no"><?php _e("No", "bbp-core"); ?></label>
            </span>
        </div>
    </div>

    <div class="d4p-wizard-option-block d4p-wizard-block-yesno">
        <p><?php _e("Do you want to show activity badges for forums?", "bbp-core"); ?></p>
        <div>
            <em><?php _e("Based on the tracking data, plugin can show badges for new topic, unread topics, topics with new replies.", "bbp-core"); ?></em>
            <span>
                <input type="radio" name="gdbbx[wizard][tracking][forums]" value="yes" id="gdbbx-wizard-tracking-forums-yes" checked />
                <label for="gdbbx-wizard-tracking-forums-yes"><?php _e("Yes", "bbp-core"); ?></label>
            </span>
            <span>
                <input type="radio" name="gdbbx[wizard][tracking][forums]" value="no" id="gdbbx-wizard-tracking-forums-no" />
                <label for="gdbbx-wizard-tracking-forums-no"><?php _e("No", "bbp-core"); ?></label>
            </span>
        </div>
    </div>
</div>
