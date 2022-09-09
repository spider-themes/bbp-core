<?php
function bbpc_post_pagination() {
	if ( is_singular() ) {
		return;
	}
		global $wp_query;

		/** Stop execution if there's only 1 page */
	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

		$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
		$max   = intval( $wp_query->max_num_pages );

		/**    Add current page to the array */
	if ( $paged >= 1 ) {
		$links[] = $paged;
	}

		/**    Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

		echo '<ul class="d-flex align-items-center">' . "\n";

		/**    Previous Post Link */
	if ( get_previous_posts_link() ) {
		printf( '<li>%s</li>' . "\n", get_previous_posts_link( '<i class="fa fa-angle-left" aria-hidden="true"></i>' ) );
	}

		/**    Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : ' ';

		printf( '<li><a %s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) ) {
			echo '<li>&#46;&#46;&#46;</li>';
		}
	}

		/**    Link to current page, plus 2 pages in either direction if necessary */
		sort( $links );
	foreach ( $links as $link ) {
		$class = $paged == $link ? ' class="active"' : ' ';
		printf( '<li><a %s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

		/**    Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) ) {
			echo '<li>&#46;&#46;&#46;</li>' . "\n";
		}

		$class = $paged == $max ? ' class="active"' : '';
		printf( '<li><a %s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

		/**    Next Post Link */
	if ( get_next_posts_link() ) {
		printf( '<li>%s</li>' . "\n", get_next_posts_link( '<i class="fa fa-angle-right" aria-hidden="true"></i>' ) );
	}

		echo '</ul>';
}



	add_action(
		'admin_footer',
		function() {
			if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
				$forum_id = sanitize_text_field( wp_unslash( isset( $_GET['forum_id'] ) ) ? $_GET['forum_id'] : '' );
				if ( $forum_id ) :
					?> 
				<script>
					document.getElementById('parent_id').value = <?php echo esc_js( $forum_id ); ?>;						 
				</script>
					<?php
				endif;
			}
		}
	);
