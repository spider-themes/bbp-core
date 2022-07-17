<?php

$args = apply_filters(
	'bbpc_admin_dashboard_activity_query_args',
	[
		'post_type'           => [ bbp_get_topic_post_type(), bbp_get_reply_post_type() ],
		'posts_per_page'      => 10,
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	]
);

$loop = new WP_Query( $args );

if ( $loop->have_posts() ) {

	?>

<ul class="bbpc-latest-activity">

	<?php

	$path = bbpc_get_template_part( 'bbpc-dashboard-activity-post.php' );

	while ( $loop->have_posts() ) {
		$loop->the_post();

		?>

	<li><?php include $path; ?></li>

		<?php

	}

	?>

</ul>

	<?php

} else {

	?>

<p><?php _e( 'No posts found.', 'bbp-core' ); ?></p>

	<?php

}
