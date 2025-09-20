<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add meta box.
add_action( 'add_meta_boxes', 'bbp_voting_metaboxes' );
function bbp_voting_metaboxes() {
	add_meta_box(
		'bbp_voting',
		'bbPress Voting',
		'bbp_voting_forum_metabox',
		'forum',
		'side',
		'low'
	);
}

// Meta box form.
function bbp_voting_forum_metabox() {
	$post_id = get_the_ID();
	$options = [
		''      => __( 'Default', 'bbp-core' ),
		'true'  => __( 'Enable', 'bbp-core' ),
		'false' => __( 'Disable', 'bbp-core' ),
	];
	?>
	<p class="description">
		<?php esc_html_e( 'Enable or disable voting on topics or replies, only for this forum.', 'bbp-core' ); ?>
	</p>

	<p>
		<strong class="label"><?php esc_html_e( 'Voting on Topics:', 'bbp-core' ); ?></strong>
		<select name="bbp_voting_forum_enable_topics" id="bbp_voting_forum_enable_topics" class="bbp_dropdown">
			<?php
			$selected = get_post_meta( $post_id, 'bbp_voting_forum_enable_topics', true );
			foreach ( $options as $value => $label ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( $selected, $value, false ),
					esc_html( $label )
				);
			}
			?>
		</select>
	</p>

	<p>
		<strong class="label"><?php esc_html_e( 'Voting on Replies:', 'bbp-core' ); ?></strong>
		<select name="bbp_voting_forum_enable_replies" id="bbp_voting_forum_enable_replies" class="bbp_dropdown">
			<?php
			$selected = get_post_meta( $post_id, 'bbp_voting_forum_enable_replies', true );
			foreach ( $options as $value => $label ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( $selected, $value, false ),
					esc_html( $label )
				);
			}
			?>
		</select>
	</p>

	<p class="description">
		<?php esc_html_e( 'Enable or disable sorting based on votes on topics or replies, only for this forum.', 'bbp-core' ); ?>
	</p>

	<p>
		<strong class="label"><?php esc_html_e( 'Sort Topics by Votes:', 'bbp-core' ); ?></strong>
		<select name="sort_bbpress_topics_by_votes_on_forum" id="sort_bbpress_topics_by_votes_on_forum" class="bbp_dropdown">
			<?php
			$selected = get_post_meta( $post_id, 'sort_bbpress_topics_by_votes_on_forum', true );
			foreach ( $options as $value => $label ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( $selected, $value, false ),
					esc_html( $label )
				);
			}
			?>
		</select>
	</p>

	<p>
		<strong class="label"><?php esc_html_e( 'Sort Replies by Votes:', 'bbp-core' ); ?></strong>
		<select name="sort_bbpress_replies_by_votes_on_forum" id="sort_bbpress_replies_by_votes_on_forum" class="bbp_dropdown">
			<?php
			$selected = get_post_meta( $post_id, 'sort_bbpress_replies_by_votes_on_forum', true );
			foreach ( $options as $value => $label ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( $selected, $value, false ),
					esc_html( $label )
				);
			}
			?>
		</select>
	</p>
	<?php
}

// Save meta box data

add_action( 'save_post', 'bbp_voting_save_forum_metabox' );
function bbp_voting_save_forum_metabox( $post_id ) {
	if ( array_key_exists( 'bbp_voting_forum_enable_topics', $_POST ) ) {
		update_post_meta(
			$post_id,
			'bbp_voting_forum_enable_topics',
			$_POST['bbp_voting_forum_enable_topics']
		);
	}
	if ( array_key_exists( 'bbp_voting_forum_enable_replies', $_POST ) ) {
		update_post_meta(
			$post_id,
			'bbp_voting_forum_enable_replies',
			$_POST['bbp_voting_forum_enable_replies']
		);
	}
	if ( array_key_exists( 'sort_bbpress_topics_by_votes_on_forum', $_POST ) ) {
		update_post_meta(
			$post_id,
			'sort_bbpress_topics_by_votes_on_forum',
			$_POST['sort_bbpress_topics_by_votes_on_forum']
		);
	}
	if ( array_key_exists( 'sort_bbpress_replies_by_votes_on_forum', $_POST ) ) {
		update_post_meta(
			$post_id,
			'sort_bbpress_replies_by_votes_on_forum',
			$_POST['sort_bbpress_replies_by_votes_on_forum']
		);
	}
}
