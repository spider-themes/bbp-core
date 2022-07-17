<fieldset class="bbp-form bbpc-fieldset-attachments">
	<legend><?php _e( 'Attachments', 'bbp-core' ); ?>:</label></legend>
	<div>
		<?php do_action( 'bbpc_attachments_form_notices' ); ?>

		<div class="bbpc-attachments-form">
			<div class="bbpc-attachments-input">
				<div role="button" class="bbpc-attachment-preview">
					<span aria-hidden="true"><?php _e( 'Select File', 'bbp-core' ); ?></span></div>
				<label>
					<input type="file" size="40" name="bbpc-attachment[]"/>
					<span class="bbpc-accessibility-show-for-sr"><?php _e( 'Select File', 'bbp-core' ); ?></span>
				</label>
				<div class="bbpc-attachment-control"></div>
			</div>
			<a role="button" class="bbpc-attachment-add-file" href="#"><?php _e( 'Add another file', 'bbp-core' ); ?></a>
		</div>
	</div>
</fieldset>
