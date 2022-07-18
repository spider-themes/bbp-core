<fieldset class="bbp-form gdbbx-signature">
    <legend><?php bbp_is_user_home() ? _e("Your Forum Signature", "bbp-core") : _e("User Forum Signature", "bbp-core"); ?></legend>
    <?php do_action('gdbbx_user_edit_before_signature'); ?>

    <div class="<?php echo gdbbx_signature_editor_class(); ?>">
        <label for="signature"><?php _e("Signature", "bbp-core"); ?></label>

        <?php

        $signature = gdbbx_signature()->get_signature_for_bbpress_displayed_user();
        gdbbx_render_signature_editor($signature);

        ?>

        <span class="description">
            <?php echo sprintf(__("Signature length is limited to %s characters.", "bbp-core"), gdbbx_signature()->get_signature_max_length()); ?><br/>
            <?php do_action('gdbbx_user_edit_signature_info'); ?>
        </span>
    </div>

    <?php do_action('gdbbx_user_edit_after_signature'); ?>
</fieldset>
