<div class="bbpc-canned-replies">
	<a role="button" href="#" class="bbpc-canned-replies-show"><?php _e( 'Show Canned Replies List', 'bbp-core' ); ?></a>

	<fieldset class="bbpc-canned-replies-list bbp-form">
		<legend>
			<label><?php _e( 'Canned Replies', 'bbp-core' ); ?>:</label>
			<a role="button" href="#" class="bbpc-canned-replies-hide"><?php _e( 'Hide', 'bbp-core' ); ?></a>
		</legend>

		<div>
			<?php

			$categories = bbpc_canned_replies()->categories();

			if ( empty( $categories ) ) {
				$replies = bbpc_canned_replies()->replies();

				if ( $replies->have_posts() ) {
					echo '<ul>';

					while ( $replies->have_posts() ) {
						$replies->the_post();

						include bbpc_get_template_part( 'bbpc-single-canned-reply.php' );
					}

					echo '</ul>';
				}
			} else {
				$replies = bbpc_canned_replies()->replies( -1 );

				if ( $replies->have_posts() ) {
					echo '<h4 class="bbpc-canned-category">' . __( 'Uncategorized', 'bbp-core' ) . '</h4>';
					echo '<ul>';

					while ( $replies->have_posts() ) {
						$replies->the_post();

						include bbpc_get_template_part( 'bbpc-single-canned-reply.php' );
					}

					echo '</ul>';
				}

				foreach ( $categories as $cat ) {
					$replies = bbpc_canned_replies()->replies( $cat->term_id );

					if ( $replies->have_posts() ) {
						echo '<h4 class="bbpc-canned-category">' . $cat->name . '</h4>';
						echo '<ul>';

						while ( $replies->have_posts() ) {
							$replies->the_post();

							include bbpc_get_template_part( 'bbpc-single-canned-reply.php' );
						}

						echo '</ul>';
					}
				}
			}

			wp_reset_postdata();

			?>
		</div>
	</fieldset>
</div>
