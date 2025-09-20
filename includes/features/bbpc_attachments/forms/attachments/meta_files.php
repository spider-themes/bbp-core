<?php
$attachments = bbpc_get_post_attachments( $post_ID );

if ( empty( $attachments ) ) {
	esc_html_e( 'No attachments here.', 'bbp-core' );
} else {
	echo '<ul style="list-style: decimal outside; margin-left: 1.5em;">';

	foreach ( $attachments as $attachment ) {
		$file     = get_attached_file( $attachment->ID );
		$filename = pathinfo( $file, PATHINFO_BASENAME );

		// Build safe edit link
		$edit_link = add_query_arg(
			array(
				'action'        => 'edit',
				'attachment_id' => (int) $attachment->ID,
			),
			admin_url( 'media.php' )
		);

		echo '<li>' . esc_html( $filename );
		echo ' - <a href="' . esc_url( $edit_link ) . '">' . esc_html__( 'edit', 'bbp-core' ) . '</a>';
		echo '</li>';
	}

	echo '</ul>';
}

if ( bbpc_is_user_admin() || bbpc_is_user_moderator() ) {
	$errors = get_post_meta( $post_ID, '_bbp_attachment_upload_error', true );

	if ( ! empty( $errors ) && is_array( $errors ) ) {
		echo '<h4>' . esc_html__( 'Upload Errors', 'bbp-core' ) . ':</h4>';
		echo '<ul style="list-style: decimal outside; margin-left: 1.5em;">';

		foreach ( $errors as $error ) {
			$file    = isset( $error['file'] ) ? $error['file'] : '';
			$message = isset( $error['message'] ) ? $error['message'] : '';

			echo '<li><strong>' . esc_html( $file ) . '</strong>:<br/>' . esc_html( $message ) . '</li>';
		}

		echo '</ul>';
	}
}