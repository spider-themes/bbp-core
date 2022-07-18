<div>
    <p>
        <?php _e("Welcome to the setup wizard for GD bbPress Toolbox Pro plugin! Here you can quickly set up the plugin, and if you need to adjust all the plugin features in more detail, you can do that later through various plugin panels.", "bbp-core"); ?>
    </p>
    <p>
        <?php _e("Using this wizard will reconfigure the plugin. Each option might affect one or more plugin settings.", "bbp-core"); ?>
    </p>
    <p>
        <?php _e("Let's start with few basics.", "bbp-core"); ?>
    </p>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to add Forums menu into WordPress toolbar?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("This toolbar contains links for quick access to all the forums, topic views, bbPress settings and GD bbPress Toolbox Pro settings.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][toolbar]" value="yes" id="gdbbx-wizard-intro-toolbar-yes" checked />
            <label for="gdbbx-wizard-intro-toolbar-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][toolbar]" value="no" id="gdbbx-wizard-intro-toolbar-no" />
            <label for="gdbbx-wizard-intro-toolbar-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to enable signatures for your forum users?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("Each user will be able to define own signature, and signature will be displayed at the bottom of each topic or reply.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][signatures]" value="yes" id="gdbbx-wizard-intro-signatures-yes" checked />
            <label for="gdbbx-wizard-intro-signatures-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][signatures]" value="no" id="gdbbx-wizard-intro-signatures-no" />
            <label for="gdbbx-wizard-intro-signatures-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to allow use of BBCodes for the content formatting?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("BBCodes are used to format content, and they can be used with various features, including quotes, simple BBCodes toolbar, in signatures. They are standard feature in many other forum systems.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][bbcodes]" value="yes" id="gdbbx-wizard-intro-bbcodes-yes" checked />
            <label for="gdbbx-wizard-intro-bbcodes-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][bbcodes]" value="no" id="gdbbx-wizard-intro-bbcodes-no" />
            <label for="gdbbx-wizard-intro-bbcodes-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>

<div class="d4p-wizard-option-block d4p-wizard-block-yesno">
    <p><?php _e("Do you want to enable quotes for your forum users?", "bbp-core"); ?></p>
    <div>
        <em><?php _e("With quotes, your users can quote topic or reply content when responding to make the conversation easier to follow.", "bbp-core"); ?></em>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][quotes]" value="yes" id="gdbbx-wizard-intro-quotes-yes" checked />
            <label for="gdbbx-wizard-intro-quotes-yes"><?php _e("Yes", "bbp-core"); ?></label>
        </span>
        <span>
            <input type="radio" name="gdbbx[wizard][intro][quotes]" value="no" id="gdbbx-wizard-intro-quotes-no" />
            <label for="gdbbx-wizard-intro-quotes-no"><?php _e("No", "bbp-core"); ?></label>
        </span>
    </div>
</div>
