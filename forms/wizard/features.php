<div>
    <p>
        <?php _e("Plugin has number of modules, and here you can enable or disable some of them.", "bbp-core"); ?>
    </p>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want use Canned Replies?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("Canned replies are very useful feature for giving quick answers with predefined replies. It can be used by keymasters and moderators.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][canned]" value="yes" id="gdbbx-wizard-modules-canned-yes" checked />
            <label for="gdbbx-wizard-modules-canned-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][canned]" value="no" id="gdbbx-wizard-modules-canned-no" />
            <label for="gdbbx-wizard-modules-canned-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want use Say Thanks module?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("Thanks module allows users to say thanks to other users for their topics and replies.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][thanks]" value="yes" id="gdbbx-wizard-modules-thanks-yes" checked />
            <label for="gdbbx-wizard-modules-thanks-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][thanks]" value="no" id="gdbbx-wizard-modules-thanks-no" />
            <label for="gdbbx-wizard-modules-thanks-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to allow users to report topics and replies content?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("If the topic or reply content is inappropriate, spam, harassment... This will help your moderators to quickly identify content in need of moderation with the help of the forum users.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][report]" value="yes" id="gdbbx-wizard-modules-report-yes" />
            <label for="gdbbx-wizard-modules-report-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][report]" value="no" id="gdbbx-wizard-modules-report-no" checked />
            <label for="gdbbx-wizard-modules-report-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to allow users to create private topics and replies?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("Private topics and replies can be read only by author, keymasters and moderators. Topic author will be able to read private replies in own topic.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][private]" value="yes" id="gdbbx-wizard-modules-private-yes" />
            <label for="gdbbx-wizard-modules-private-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][private]" value="no" id="gdbbx-wizard-modules-private-no" checked />
            <label for="gdbbx-wizard-modules-private-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to show user information in the topic/reply thread?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("This includes user online status, topics and replies count, number of thanks given and received (if thanks module is active) and more.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][stats]" value="yes" id="gdbbx-wizard-modules-stats-yes" />
            <label for="gdbbx-wizard-modules-stats-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][modules][stats]" value="no" id="gdbbx-wizard-modules-stats-no" checked />
            <label for="gdbbx-wizard-modules-stats-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>
