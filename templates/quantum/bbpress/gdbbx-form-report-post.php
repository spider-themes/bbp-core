<div class="bbpc-report-template" style="display: none;">
	<div>
		<fieldset class="bbp-form">
			<legend><?php _e( 'Report This Post', 'bbp-core' ); ?></legend>

			<div class="bbpc-report-form">
				<p>
					<label for="bbpc-report-message"><?php _e( 'Report Message', 'bbp-core' ); ?></label>
					<input id="bbpc-report-message" type="text" value="" />
				</p>
				<p class="description"><?php _e( 'Use this only to report spam, harassment, fighting, or rude content.', 'bbp-core' ); ?></p>
				<button class="gdqnt-button bbpc-report-send"><?php _e( 'Send Report', 'bbp-core' ); ?></button>
				<button class="gdqnt-button gdqnt-button-secondary bbpc-report-cancel"><?php _e( 'Cancel', 'bbp-core' ); ?></button>
			</div>
			<div class="bbpc-report-sending" style="display: none;">
				<?php _e( 'Please wait, sending report.', 'bbp-core' ); ?>
			</div>
			<div class="bbpc-report-sent" style="display: none;">
				<?php _e( 'Thank you, your report has been sent.', 'bbp-core' ); ?>
			</div>
		</fieldset>
	</div>
</div>
