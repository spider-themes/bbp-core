<div class="d4p-wrap-wizard">
	<div class="d4p-setup-wizard">
		<div class="d4p-wizard-logo">
			<div class="d4p-wizard-badge" style="background-color: #224760;">
				<i class="d4p-icon d4p-plugin-icon-gd-bbpress-toolbox"></i>
			</div>
			<div class="d4p-wizard-title" style="color: #224760;">
				BBP Core
			</div>
		</div>

		<div class="d4p-wizard-panels"><?php

			$step_width = 100 / count( bbpc_wizard()->panels );
			$past_class = 'd4p-wizard-step-done';
		foreach ( bbpc_wizard()->panels as $w => $obj ) {
			if ( $w == bbpc_wizard()->current_panel() ) {
				$past_class = 'd4p-wizard-step-current';
			}

			echo '<div style="width: ' . $step_width . '%" class="d4p-wizard-step d4p-wizard-step-' . $w . ' ' . $past_class . '">' . $obj['label'] . '</div>';

			if ( $w == bbpc_wizard()->current_panel() ) {
				$past_class = '';
			}
		}

		?></div>

		<div class="d4p-wizard-panel">
			<form method="post" action="<?php echo bbpc_wizard()->get_form_action(); ?>">
				<input type="hidden" name="bbpc[wizard][_nonce]" value="<?php echo bbpc_wizard()->get_form_nonce(); ?>" />
				<input type="hidden" name="bbpc[wizard][_page]" value="<?php echo bbpc_wizard()->current_panel(); ?>" />
				<input type="hidden" name="bbpc_handler" value="postback" />
				<input type="hidden" name="option_page" value="bbp-core-wizard" />
