<h3><?php IS_PROFILE_PAGE ? _e( 'Your Forum Signature', 'bbp-core' ) : _e( 'User Forum Signature', 'bbp-core' ); ?></h3>
<table class="form-table">
	<tr>
		<th>
			<label for="signature"><?php _e( 'Signature', 'bbp-core' ); ?></label>
		</th>
		<td>
			<div class="<?php echo bbpc_signature_editor_class(); ?>">
				<?php

				$signature = bbpc_signature()->get_signature_for_profile_user();
				bbpc_render_signature_editor( $signature );

				?>
			</div>
		</td>
	</tr>
</table>
