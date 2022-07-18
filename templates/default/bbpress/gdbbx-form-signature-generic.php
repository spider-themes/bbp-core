<div class="<?php echo gdbbx_signature_editor_class(); ?>">
    <label for="signature"><?php _e( "Forum Signature", "bbp-core" ); ?></label>

	<?php

	$signature = gdbbx_signature()->get_signature_for_user();
	gdbbx_render_signature_editor( $signature );

	?>

    <br/>
    <span class="description">
        <?php echo sprintf( __( "Signature length is limited to %s characters.", "bbp-core" ), gdbbx_signature()->get_signature_max_length() ); ?><br/>
        <?php do_action( 'gdbbx_user_edit_signature_info' ); ?>
    </span>
</div>
