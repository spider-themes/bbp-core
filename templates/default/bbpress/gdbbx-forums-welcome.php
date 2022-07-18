<div id="bbp-user-welcome" class="gdbbx-forum-index-block">
    <div class="gdbbx-forums-inner-block">
		<?php $_user_visit = gdbbx_forum_index()->user_visit(); ?>
        <h4><?php

			if ( $_user_visit['timestamp'] > 0 ) {
				_e( "Welcome back", "bbp-core" );
			} else {
				_e( "Welcome", "bbp-core" );
			}

			?></h4>

        <div>
            <p><?php

				if ( $_user_visit['timestamp'] > 0 ) {
					echo sprintf(
						__( "There have been %s and %s since your last visit at %s on %s.", "bbp-core" ),
						sprintf( _nx( "%s new topic", "%s new topics", $_user_visit['topics'], "Forums Welcome Block", "bbp-core" ), $_user_visit['topics'] ),
						sprintf( _nx( "%s new reply", "%s new replies", $_user_visit['replies'], "Forums Welcome Block", "bbp-core" ), $_user_visit['replies'] ),
						$_user_visit['time'],
						$_user_visit['date']
					);
				}

				?></p>

			<?php

			if ( gdbbx_forum_index()->get_welcome( 'links' ) ) {

				?>

                <p><?php echo join( ' &middot; ', gdbbx_forum_index()->user_links() ); ?></p>

				<?php

			}

			?>
        </div>
    </div>
</div>
