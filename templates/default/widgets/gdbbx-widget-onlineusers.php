<div class="bbpc-online-status">
	<?php $online = bbpc_module_online()->online(); ?>

	<p class="bbpc-online-current">
		<?php echo sprintf( _n( 'There is <strong>%s</strong> user online', 'There are <strong>%s</strong> users online', $online['counts']['total'], 'bbp-core' ), $online['counts']['total'] ); ?> - 
		<?php echo sprintf( _n( '<strong>%s</strong> registered', '<strong>%s</strong> registered', $online['counts']['users'], 'bbp-core' ), $online['counts']['users'] ); ?>, 
		<?php echo sprintf( _n( '<strong>%s</strong> guest', '<strong>%s</strong> guests', $online['counts']['guests'], 'bbp-core' ), $online['counts']['guests'] ); ?>.
	</p>

	<?php

	if ( $instance['show_users_list'] ) {
		$_roles = bbp_get_dynamic_roles();
		$_users = bbpc_get_online_users_list(
			$instance['show_users_limit'],
			$instance['show_users_avatar'],
			$instance['show_users']
		);

		if ( ! empty( $_users ) ) {
			if ( $instance['show_user_roles'] ) {
				foreach ( $_users as $role => $users ) {
					if ( isset( $_roles[ $role ] ) ) {

						?>

						<p>
							<strong><?php echo bbp_translate_user_role( $_roles[ $role ]['name'] ); ?></strong><br/>
							<?php echo join( ', ', $users ); ?>
						</p>

						<?php

					}
				}
			} else {
				$all_users = [];
				foreach ( $_users as $users ) {
					$all_users = array_merge( $all_users, $users );
				}

				?>

				<p class="bbpc-online-users">
					<strong><?php _e( 'Users online', 'bbp-core' ); ?></strong><br/>
					<?php echo join( ', ', $all_users ); ?>
				</p>

				<?php

			}
		}
	}

	?>

	<?php if ( $instance['show_max_users'] ) { ?>
		<?php $max = bbpc_module_online()->max(); ?>

		<p class="bbpc-online-maximum">
			<?php echo sprintf( __( 'Most users ever online was <strong>%1$s</strong> on %2$s.', 'bbp-core' ), $max['total']['count'], date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $max['total']['timestamp'] ) ); ?>
		</p>
	<?php } ?>
</div>
