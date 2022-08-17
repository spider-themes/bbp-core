<?php
if ( ! class_exists( 'bbPress' ) ) {
	return;
}
$depth_one_parents = [];
$depth_two_parents = [];
$forum_count       = (int) wp_count_posts( 'forum' )->publish;
?>
<div class="wrap">
	<div class="body-dark">
		<?php if ( $forum_count > 0 ) : ?>
			<header class="easydocs-header-area">
				<div class="container-fluid">
					<div class="row alignment-center justify-content-between">
						<div class="col-lg-4">
							<div class="navbar-left d-flex alignment-center">
								<div class="easydocs-logo-area">
									<a href="javascript:void(0);">
										<?php esc_html_e( 'Forums', 'bbp-core' ); ?>
									</a>
								</div>

								<a href="<?php echo admin_url( 'post-new.php?post_type=forum' ); ?>" type="button" id="bbpc-forum" class="easydocs-btn easydocs-btn-outline-blue easydocs-btn-sm easydocs-btn-round">
									<span class="dashicons dashicons-plus-alt2"></span>
									<?php esc_html_e( 'Add Forum', 'bbp-core' ); ?>
								</a>

							</div>
						</div>

						<div class="col-lg-5">
							<form action="#" method="POST" class="easydocs-search-form">
								<input type="search" name="keyword" class="form-control" id="bbpc-search" placeholder="<?php esc_attr_e( 'Search for', 'bbp-core' ); ?>" onkeyup="fetch()" />
								<div class="search-icon">
									<span class="dashicons dashicons-search"></span>
								</div>
							</form>
						</div>
						<div class="col-lg-3">
							<div class="navbar-right">
								<ul class="d-flex justify-content-end">
									<li>
										<div class="easydocs-settings">
											<?php if ( current_user_can( 'edit_posts' ) ) : ?>
												<div class="header-notify-icons">
													<select name="bbpc_classic_ui" id="bbpc_classic_ui">
														<option value="<?php echo admin_url( 'admin.php?page=bbp-core' ); ?>"><?php esc_html_e( 'Choose classic UI', 'bbp-core' ); ?></option>
														<option value="<?php echo admin_url( 'edit.php?post_type=forum' ); ?>"><?php esc_html_e( 'Forum', 'bbp-core' ); ?></option>
														<option value="<?php echo admin_url( 'edit.php?post_type=topic' ); ?>"><?php esc_html_e( 'Topic', 'bbp-core' ); ?></option>
														<option value="<?php echo admin_url( 'edit.php?post_type=reply' ); ?>"><?php esc_html_e( 'Reply', 'bbp-core' ); ?></option>
													</select>
												</div>
											<?php endif; ?>

											<div class="header-notify-icon">
												<a href="<?php echo admin_url( 'admin.php?page=bbp-core-settings' ); ?>">
													<img src="<?php echo BBPC_IMG; ?>/admin/admin-settings.svg" alt="<?php esc_html_e( 'Settings Icon', 'bbp-core' ); ?>">
												</a>
											</div>
										</div>
									</li>
										<li class="easydocs-notification pro-notification-alert" title="<?php esc_attr_e( 'Notifications', 'bbp-core' ); ?>">
											<div class="header-notify-icon">
												<img class="notify-icon" src="<?php echo BBPC_IMG; ?>/admin/notification.svg" alt="<?php esc_html_e( 'Notify Icon', 'bbp-core' ); ?>">
												<img class="settings-pro-icon" src="<?php echo BBPC_IMG; ?>/admin/pro-icon.png" alt="<?php esc_html_e( 'Pro Icon', 'bbp-core' ); ?>">
											</div>
										</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</header>
			<main>
				<div class="easydocs-sidebar-menu">
					<div class="tab-container">
			<?php
			$query = new WP_Query(
				[
					'post_type'      => 'forum',
					'posts_per_page' => -1,
					'post_parent'    => 0,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
				]
			);
			$count = $query->found_posts;
			?>

<div class="tab-menu <?php echo $count > 12 ? '' : 'short'; ?>">
	<ul class="easydocs-navbar">
			<?php
			$i = '';
			while ( $query->have_posts() ) :
				$query->the_post();
				$i ++;
				$depth_one_parents[] = get_the_ID();
				$current_post        = get_the_ID();
				$is_active           = $i == 1 ? 'is-active' : '';

				$count_children = get_children(
					[
						'post_parent' => $current_post,
						'post_type'   => 'topic',
						'orderby'     => 'menu_order',
						'order'       => 'asc',
					]
				);
				?>
			<li class="easydocs-navitem  <?php echo esc_attr( $is_active ); ?>" data-rel="tab-<?php the_ID(); ?>" data-id="<?php the_ID(); ?>">
				<div class="title">
					<span class="dashicons dashicons-buddicons-forums"></span>
					<?php the_title(); ?>
				</div>
				<div class="total-page">
					<span>
						<?php echo count( $count_children ) > 0 ? count( $count_children ) : ''; ?>
					</span>
				</div>
				<div class="link">
					<?php if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) : ?>
						<a href="<?php echo get_edit_post_link( get_the_ID() ); ?>" class="link edit" target="_blank" title="<?php esc_attr_e( 'Edit this forum.', 'bbp-core' ); ?>">
							<span class="dashicons dashicons-edit"></span>
						</a>
						<?php
						endif;
					?>

					<a href="<?php the_permalink(); ?>" class="link external-link" target="_blank" data-id="tab-<?php the_ID(); ?>" title="<?php esc_attr_e( 'View this forum in new tab', 'bbp-core' ); ?>">
						<span class="dashicons dashicons-external"></span>
					</a>
					<?php
					if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) :
						?>
					<a href="<?php echo admin_url( 'admin.php' ); ?>/menu/Delete_Post.php?DeleteID=<?php echo get_the_ID(); ?>" class="link delete parent-delete" title="<?php esc_attr_e( 'Delete this forum permanently', 'bbp-core' ); ?>">
						<span class="dashicons dashicons-trash"></span>
					</a>
					<?php endif; ?>
				</div>
			</li>
				<?php
		endwhile;
			wp_reset_postdata();
			?>
	</ul>
</div>
		<div class="easydocs-tab-content">
			<?php
			$child_docs_depth    = [];
			$depth_two_parents   = [];
			$depth_three_parents = [];
			$ids                 = '';
			$container           = 1;
			if ( is_array( $depth_one_parents ) ) :
				foreach ( $depth_one_parents as $item ) :
					$ids ++;
					$container ++;
					$active = $ids == 1 ? ' tab-active' : '';
					?>
		<div class="easydocs-tab<?php echo esc_attr( $active ); ?>" id="tab-<?php echo esc_attr( $item ); ?>">
			<div class="easydocs-filter-container">
				<ul class="single-item-filter">
					<li class="easydocs-btn easydocs-btn-black-light easydocs-btn-rounded easydocs-btn-sm is-active" data-filter="all">
						<span class="dashicons dashicons-media-document"></span>
						<?php esc_html_e( 'All topics', 'bbp-core' ); ?>
					</li>
					<li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".no-reply">
						<span class="dashicons dashicons-admin-site-alt3"></span>
						<?php esc_html_e( 'No Reply', 'bbp-core' ); ?>
					</li>

					<li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".solved">
						<span class="dashicons dashicons-privacy"></span>
						<?php esc_html_e( 'Solved', 'bbp-core' ); ?>
					</li>
					<li class="easydocs-btn easydocs-btn-orange-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".unsolved">
						<span class="dashicons dashicons-lock"></span>
						<?php esc_html_e( 'Unsolved', 'bbp-core' ); ?>
					</li>
				</ul>
			</div>

			<ul class="easydocs-accordion">
					<?php
					$children = get_children(
						[
							'post_parent' => $item,
							'post_type'   => 'topic',
							'orderby'     => 'menu_order',
							'order'       => 'asc',
						]
					);

					if ( is_array( $children ) ) :
						foreach ( $children as $child ) :
							$replies = get_children(
								[
									'post_parent' => $child->ID,
									'post_type'   => 'reply',
									'post_status' => [ 'publish', 'draft' ],
								]
							);

							$no_reply = 0 == count( $replies ) ? 'no-reply' : '';
							$is_solved = $GLOBALS['bbp_solved_topic']->is_solved( $child->ID ) ? ' solved': ' unsolved';
							?>
						<li <?php post_class( 'easydocs-accordion-item accordion ez-section-acc-item mix ' . esc_attr( $no_reply ) . esc_attr( $is_solved ) ); ?> data-id="<?php echo esc_attr( $child->ID ); ?>">
							<div class="accordion-title ez-section-title">
									<?php
									$edit_link = 'javascript:void(0)';
									$target    = '_self';
									if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
										$edit_link = get_edit_post_link( $child );
										$target    = '_blank';
									}
									?>
								<div class="left-content">
									<h4>
										<a href="<?php echo esc_attr( $edit_link ); ?>" target="<?php echo esc_attr( $target ); ?>">
									<?php echo $child->post_title; ?>
										</a>
									<?php if ( count( $replies ) > 0 ) : ?>
											<span class="count badge">
												<?php echo count( $replies ); ?>
											</span>
									<?php endif; ?>
									</h4>
									<ul class="actions">
										<li>
											<a href="<?php echo get_permalink( $child ); ?>" target="_blank" title="<?php esc_attr_e( 'View this reply in new tab', 'bbp-core' ); ?>">
												<span class="dashicons dashicons-external"></span>
											</a>
										</li>
										<?php
											if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) :
											?>
											<li class="delete">
												<a href="<?php echo admin_url( 'admin.php' ); ?>/menu/Delete_Topic.php?topic_ID=<?php echo $child->ID; ?>" class="section-delete" title="<?php esc_attr_e( 'Delete this doc permanently', 'bbp-core' ); ?>">
													<span class="dashicons dashicons-trash"></span>
												</a>
											</li>
											<?php 
										endif; ?>
									</ul>
								</div>
							</div>
						</li>
									<?php
									endforeach;
							endif;
					?>
			</ul>

			<a class="button button-info section-doc" id="bbpc-topic" name="submit" href="<?php echo admin_url( 'post-new.php?post_type=topic' ); ?>">
					<?php esc_html_e( 'Add topics', 'bbp-core' ); ?>
			</a>
			</div>
					<?php
				endforeach;
			endif;
			?>
						</div>
					</div>
				</div>
			</main>
			<?php
		else :
			?>
			<div class="eazydocs-no-content">
				<img src="<?php echo BBPC_IMG; ?>/icon/folder-open.png" alt="<?php esc_attr_e( 'Folder Open', 'bbp-core' ); ?>">
				<p class="big-p"> <?php esc_html_e( 'No docs has been found. Perhaps', 'bbp-core' ); ?> </p>
				<p> <br>
					<a class="button button-primary ezd-btn btn-lg" href="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?new_doc=" id="new-doc">
						<?php esc_html_e( 'Create a Doc', 'bbp-core' ); ?>
					</a>
				</p>

			</div>
			<?php
		endif;
		?>
	</div>
</div>
