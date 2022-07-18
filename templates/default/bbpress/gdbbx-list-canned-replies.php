<div class="gdbbx-canned-replies">
    <a role="button" href="#" class="gdbbx-canned-replies-show"><?php _e( "Show Canned Replies List", "bbp-core" ); ?></a>
    <a role="button" href="#" class="gdbbx-canned-replies-hide"><?php _e( "Hide Canned Replies List", "bbp-core" ); ?></a>

    <fieldset class="bbp-form gdbbx-canned-replies-list">
        <legend>
            <label><?php _e( "Canned Replies", "bbp-core" ); ?>:</label>
        </legend>

		<?php

		$categories = gdbbx_canned_replies()->categories();

		if ( empty( $categories ) ) {
			$replies = gdbbx_canned_replies()->replies();

			if ( $replies->have_posts() ) {
				echo '<ul>';

				while ( $replies->have_posts() ) {
					$replies->the_post();

					include( gdbbx_get_template_part( 'gdbbx-single-canned-reply.php' ) );
				}

				echo '</ul>';
			}
		} else {
			$replies = gdbbx_canned_replies()->replies( - 1 );

			if ( $replies->have_posts() ) {
				echo '<h4 class="gdbbx-canned-category">' . __( "Uncategorized", "bbp-core" ) . '</h4>';
				echo '<ul>';

				while ( $replies->have_posts() ) {
					$replies->the_post();

					include( gdbbx_get_template_part( 'gdbbx-single-canned-reply.php' ) );
				}

				echo '</ul>';
			}

			foreach ( $categories as $cat ) {
				$replies = gdbbx_canned_replies()->replies( $cat->term_id );

				if ( $replies->have_posts() ) {
					echo '<h4 class="gdbbx-canned-category">' . $cat->name . '</h4>';
					echo '<ul>';

					while ( $replies->have_posts() ) {
						$replies->the_post();

						include( gdbbx_get_template_part( 'gdbbx-single-canned-reply.php' ) );
					}

					echo '</ul>';
				}
			}
		}

		wp_reset_postdata();

		?>
    </fieldset>
</div>