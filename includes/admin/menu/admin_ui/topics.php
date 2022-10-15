<ul class="easydocs-accordion">
	<?php
	while ( $children->have_posts() ) :
		$children->the_post();
		$current_topic_id = get_the_ID();

		$replies = new WP_Query(
			[
				'post_parent' => $current_topic_id,
				'post_type'   => bbp_get_reply_post_type(),
				'post_status' => [ 'publish', 'draft', 'pending' ],
			]
		);

		$pending_replies = [];

		$no_reply    = 0 == $replies->found_posts ? 'no-reply' : '';
		$is_solved   = $GLOBALS['bbp_solved_topic']->is_solved( $current_topic_id ) ? ' solved' : ' unsolved';
		$is_open     = bbp_is_topic_closed( $current_topic_id ) ? ' closed-topics' : ' open-topics';
		$is_hidden   = bbp_is_topic_spam( $current_topic_id ) ? ' hidden-topics' : '';
		$approve_btn = '';

		if ( bbp_is_topic_spam( $current_topic_id ) ) {
			$url         = admin_url( 'admin.php' ) . '/Approve_Topic.php?bbpc_approve_topic_id=' . $current_topic_id;
			$approve_btn = sprintf( '<a class="bbpc-approve-btn" href=%1$s><span class="dashicons dashicons-yes" title="%2$s"></span></a>', $url, __( 'Approve this topic', 'bbp-core' ) );
		}

		$filter_class = $no_reply . $is_solved . $is_open . $is_hidden;

		?>
		<li <?php post_class( 'easydocs-accordion-item accordion ez-section-acc-item mix ' . esc_attr( $filter_class ) ); ?> data-id="<?php echo esc_attr( $current_topic_id ); ?>">
			<div class="accordion-title ez-section-title">
				<?php
				$edit_link = 'javascript:void( 0 )';
				$target    = '_self';
				if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
					$edit_link = get_edit_post_link( $current_topic_id );
					$target    = '_blank';
				}
				?>
				<div class="left-content">
					<h4>
						<a href="<?php echo esc_attr( $edit_link ); ?>" target="<?php echo esc_attr( $target ); ?>">

						<?php the_title(); ?>
						</a>
						<?php
						$allowed_html = [
							'a'    => [
								'href'  => [],
								'class' => [],
							],
							'span' => [
								'class' => [],
								'title' => [],
							],
						];
 
						echo wp_kses( $approve_btn, $allowed_html );

						while ( $replies->have_posts() ) :
							$replies->the_post();
							$reply_id = get_the_ID();

							if ( bbp_is_reply_pending( $reply_id ) ) {
								$pending_replies[] = $reply_id;
							}

						endwhile;
						wp_reset_postdata();


						$pending_replies_count = count( $pending_replies );
						$reply_count           = $replies->found_posts - $pending_replies_count;
						?>
						<div title="<?php echo esc_attr( $reply_count ) . __( ' Publised replies', 'bbp-core' ); ?>">
							<span class="bbpc-reply-count bbpc-published-replies">
								<?php echo esc_html( $reply_count ); ?>
							</span>
						</div>
						<?php if ( $pending_replies_count > 0 ) : ?>
							<div click-target='<?php echo esc_attr( $current_topic_id ); ?>' title="<?php echo esc_attr( $pending_replies_count ) . __( ' Pending replies', 'bbp-core' ); ?>">
								<span class="bbpc-reply-count bbpc-pending-replies">
									<?php echo esc_html( $pending_replies_count ); ?>
								</span>
							</div>
						<?php endif; ?>
					</h4>
					<ul class="actions">
						<li>
							<a href="<?php echo get_permalink( $current_topic_id ); ?>" target="_blank" title="<?php esc_attr_e( 'View this reply in new tab', 'bbp() - core' ); ?>">
								<span class="dashicons dashicons-external"></span>
							</a>
						</li>

					<?php if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) : ?>
						<li class="delete">
							<a href="<?php echo admin_url( 'admin.php' ); ?>/menu/Delete_Topic.php?topic_ID=<?php the_ID(); ?>" class="section-delete" title="<?php esc_attr_e( 'Delete this topic permanently', 'bbp-core' ); ?>">
								<span class="dashicons dashicons-trash"></span>
							</a>
						</li>
					<?php endif; ?>
					</ul>
				</div>
			</div>
			<!-- Accordion children -->
			<div class="easydocs-accordion-body nesting-accordion">
				<?php if ( ! empty( $pending_replies_count ) ) : ?>
					<ul class="bbpc-nested-replies" reply-target=<?php echo esc_attr( $current_topic_id ); ?>>
					<?php
					foreach ( $pending_replies as $p_reply ) {
						setup_postdata( $p_reply );
						?>
						<li reply-id="<?php the_ID(); ?>" class="bbpc-reply-wrap">
							<?php
							echo esc_html( wp_trim_words( get_the_content(), 10, '...' ) );
								echo '  -  ';
								the_author();
							?>
						<?php
						if ( bbp_is_reply_pending( $p_reply ) ) {
							$url         = admin_url( 'admin.php' ) . '/Approve_Topic.php?bbpc_approve_reply_id=' . $p_reply;
							$approve_btn = sprintf( '<a class="bbpc-approve-btn" href=%1$s><span class="dashicons dashicons-yes" title="%2$s"></span></a>', $url, __( 'Approve this topic', 'bbp-core' ) );
						}

							$allowed_html = [
								'a'    => [
									'href'  => [],
									'class' => [],
								],
								'span' => [
									'class' => [],
									'title' => [],
								],

							];

							echo wp_kses( $approve_btn, $allowed_html );
							?>
						</li>
						<?php
					}

					wp_reset_postdata();
					?>
				</ul>
				<?php endif; ?>
			</div>
			<!-- ./Accordion children ends -->
		</li>
        <?php
    endwhile;
    wp_reset_postdata();
	?>
</ul>
