<?php

$attachments = bbpc_get_post_attachments( $post_id );

if ( empty( $attachments ) ) {
	echo '<p>' . __( 'No attachments here.', 'bbp-core' ) . '</p>';
} else {
	require_once BBPC_PATH . 'core/functions/admin.php';

	echo '<ul style="list-style: decimal outside; margin-left: 1.5em;">';

	foreach ( $attachments as $attachment ) {
		echo bbpc_admin_render_attachment_for_metabox( $post_id, $attachment->ID );
	}

	echo '</ul>';
}

echo '<hr/>';

echo '<p>' . __( 'You can add more attachments using Media Library.', 'bbp-core' ) . '</p>';

echo '<a class="button-primary bbpc-edit-attachment-attach" data-nonce="' . wp_create_nonce( 'bbpc-att-' . $post_id ) . '" data-post="' . $post_id . '" href="#">' . __( 'Add attachment', 'bbp-core' ) . '</a><br/><br/>';
