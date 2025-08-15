<fieldset class="bbp-form">
	<legend><?php _e( 'Upload Attachments', 'bbp-core' ); ?></legend>
	<div class="bbp-template-notice">
		<p>
            <?php
            $size = $file_size < 1024 ? $file_size . ' KB' : floor( $file_size / 1024 ) . ' MB';
            printf( __( 'Maximum file size allowed is %s.', 'bbp-core' ), $size );
            ?>
        </p>
	</div>
	<p class="bbp-attachments-form">
		<label for="bbp_topic_tags">
			<?php esc_html_e( 'Attachments', 'bbp-core' ); ?>:
		</label><br/>
        <div class="bbpc_attachments">
            <input type="file" size="40" name="bbpc_attachment[]">
            <a class="bbpc-attachment-addfile" href="#">
                <?php esc_html_e( '+ Add another file', 'bbp-core' ); ?>
            </a>
        </div>
	</p>
</fieldset>
