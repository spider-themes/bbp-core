<?php

global $user_ID;

$post      = get_post( $post_id );
$author_id = $post->post_author;

if ( ( bbpc()->get( 'errors_visible_to_author', 'attachments' ) == 1 && $author_id == $user_ID ) || ( bbpc()->get( 'errors_visible_to_admins', 'attachments' ) == 1 && d4p_is_current_user_admin() ) || ( bbpc()->get( 'errors_visible_to_moderators', 'attachments' ) == 1 && bbpc_is_current_user_bbp_moderator() ) ) {
	$errors = get_post_meta( $post_id, '_bbp_attachment_upload_error' );

	if ( ! empty( $errors ) ) {
		echo '<ul style="list-style: decimal outside; margin-left: 1.5em;">';
		foreach ( $errors as $error ) {
			echo '<li><strong>' . esc_html( $error['file'] ) . '</strong>:<br/>' . __( $error['message'], 'bbp-core' ) . '</li>';
		}
		echo '</ul>';
	} else {
		echo '<p>' . __( 'No upload errors.', 'bbp-core' ) . '</p>';
	}
} else {
	echo '<p>' . __( 'Nothing to show here.', 'bbp-core' ) . '</p>';
}
