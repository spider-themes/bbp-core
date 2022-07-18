<div id="bbp-forums-statistics" class="gdbbx-forum-index-block">
	<?php

	use Dev4Press\Plugin\GDBBX\Basic\Statistics;

	$users = gdbbx_get_online_users_list( 20, false );

	$all_users = array();
	foreach ( $users as $usr ) {
		$all_users = array_merge( $all_users, $usr );
	}

	if ( gdbbx_forum_index()->get_statistics( 'online' ) ) {
		$online = gdbbx_module_online()->online( false );

		?>

        <div class="gdbbx-forums-inner-block">
            <h4><?php _e( "Who is online", "bbp-core" ); ?></h4>

			<?php if ( gdbbx_forum_index()->get_statistics( 'online_overview' ) ) { ?>

            <div>
                <p>
					<?php echo sprintf( _n( "There is <strong>%s</strong> user online", "There are <strong>%s</strong> users online", $online['counts']['total'], "bbp-core" ), $online['counts']['total'] ); ?> -
					<?php echo sprintf( _n( "<strong>%s</strong> registered", "<strong>%s</strong> registered", $online['counts']['users'], "bbp-core" ), $online['counts']['users'] ); ?>,
					<?php echo sprintf( _n( "<strong>%s</strong> guest", "<strong>%s</strong> guests", $online['counts']['guests'], "bbp-core" ), $online['counts']['guests'] ); ?>.
                </p>

				<?php }
				if ( gdbbx_forum_index()->get_statistics( 'online_top' ) ) {
					$max = gdbbx_module_online()->max();

					?>

                    <p>
						<?php echo sprintf( __( "Most users ever online was <strong>%s</strong> on %s", "bbp-core" ), $max['total']['count'], date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $max['total']['timestamp'] ) ); ?>
                    </p>

				<?php } ?>

                <p>
					<?php echo gdbbx_forum_index()->users_list(); ?>
                </p>

				<?php if ( gdbbx_forum_index()->get_statistics( 'legend' ) ) { ?>

                    <p>
						<?php echo '<label>' . __( "Legend", "bbp-core" ) . ':</label> ' . gdbbx_forum_index()->user_roles_legend(); ?>
                    </p>

				<?php } ?>
            </div>
        </div>

	<?php } ?>

	<?php if ( gdbbx_forum_index()->get_statistics( 'statistics' ) ) {
		$statistics = Statistics::instance()->forums_stats();

		?>

        <div class="gdbbx-forums-inner-block">
            <h4><?php _e( "Forum Statistics", "bbp-core" ); ?></h4>

			<?php if ( gdbbx_forum_index()->get_statistics( 'statistics_totals' ) ) { ?>

            <div>
                <p>
					<?php _e( "Total forums", "bbp-core" ); ?>:
                    <strong><?php echo $statistics['forum_count']; ?></strong> &#8226;
					<?php _e( "Total posts", "bbp-core" ); ?>:
                    <strong><?php echo $statistics['post_count']; ?></strong> &#8226;
					<?php _e( "Total topics", "bbp-core" ); ?>:
                    <strong><?php echo $statistics['topic_count']; ?></strong> &#8226;
					<?php _e( "Total users", "bbp-core" ); ?>:
                    <strong><?php echo $statistics['user_count']; ?></strong>
                </p>

				<?php }
				if ( gdbbx_forum_index()->get_statistics( 'statistics_newest_user' ) ) { ?>

                    <p>
						<?php echo '<label>' . __( "Our newest member is", "bbp-core" ) . '</label> ' . gdbbx_forum_index()->newest_user() . '.'; ?>
                    </p>

				<?php } ?>
            </div>
        </div>

	<?php } ?>
</div>
