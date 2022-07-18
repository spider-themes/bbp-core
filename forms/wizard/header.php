<div class="d4p-wrap-wizard">
    <div class="d4p-setup-wizard">
        <div class="d4p-wizard-logo">
            <div class="d4p-wizard-badge" style="background-color: #224760;">
                <i class="d4p-icon d4p-plugin-icon-gd-bbpress-toolbox"></i>
            </div>
            <div class="d4p-wizard-title" style="color: #224760;">
                GD bbPress Toolbox Pro
            </div>
        </div>

        <div class="d4p-wizard-panels"><?php

            $step_width = 100 / count(gdbbx_wizard()->panels);
            $past_class = 'd4p-wizard-step-done';
            foreach (gdbbx_wizard()->panels as $w => $obj) {
                if ($w == gdbbx_wizard()->current_panel()) {
                    $past_class = 'd4p-wizard-step-current';
                }

                echo '<div style="width: '.$step_width.'%" class="d4p-wizard-step d4p-wizard-step-'.$w.' '.$past_class.'">'.$obj['label'].'</div>';

                if ($w == gdbbx_wizard()->current_panel()) {
                    $past_class = '';
                }
            }

        ?></div>

        <div class="d4p-wizard-panel">
            <form method="post" action="<?php echo gdbbx_wizard()->get_form_action(); ?>">
                <input type="hidden" name="gdbbx[wizard][_nonce]" value="<?php echo gdbbx_wizard()->get_form_nonce(); ?>" />
                <input type="hidden" name="gdbbx[wizard][_page]" value="<?php echo gdbbx_wizard()->current_panel(); ?>" />
                <input type="hidden" name="gdbbx_handler" value="postback" />
                <input type="hidden" name="option_page" value="gd-bbpress-toolbox-wizard" />
