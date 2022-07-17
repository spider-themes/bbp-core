<div class="<?php echo bbpc_signature_editor_class(); ?>">
	<label for="signature"><?php _e( 'Forum Signature', 'bbp-core' ); ?></label>

	<?php

	$signature = bbpc_signature()->get_signature_for_user();
	bbpc_render_signature_editor( $signature );

	?>

	<br/>
	<span class="description">
		<?php echo sprintf( __( 'Signature length is limited to %s characters.', 'bbp-core' ), bbpc_signature()->get_signature_max_length() ); ?><br/>
		<?php do_action( 'bbpc_user_edit_signature_info' ); ?>
	</span>
</div>
