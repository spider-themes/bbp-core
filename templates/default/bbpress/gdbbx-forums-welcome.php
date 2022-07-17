<div id="bbp-user-welcome" class="bbpc-forum-index-block">
	<div class="bbpc-forums-inner-block">
		<?php $_user_visit = bbpc_forum_index()->user_visit(); ?>
		<h4>
		<?php

		if ( $_user_visit['timestamp'] > 0 ) {
			_e( 'Welcome back', 'bbp-core' );
		} else {
			_e( 'Welcome', 'bbp-core' );
		}

		?>
			</h4>

		<div>
			<p>
			<?php

			if ( $_user_visit['timestamp'] > 0 ) {
				echo sprintf(
					__( 'There have been %1$s and %2$s since your last visit at %3$s on %4$s.', 'bbp-core' ),
					sprintf( _nx( '%s new topic', '%s new topics', $_user_visit['topics'], 'Forums Welcome Block', 'bbp-core' ), $_user_visit['topics'] ),
					sprintf( _nx( '%s new reply', '%s new replies', $_user_visit['replies'], 'Forums Welcome Block', 'bbp-core' ), $_user_visit['replies'] ),
					$_user_visit['time'],
					$_user_visit['date']
				);
			}

			?>
				</p>

			<?php

			if ( bbpc_forum_index()->get_welcome( 'links' ) ) {

				?>

				<p><?php echo join( ' &middot; ', bbpc_forum_index()->user_links() ); ?></p>

				<?php

			}

			?>
		</div>
	</div>
</div>
