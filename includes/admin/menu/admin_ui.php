<?php
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

						<!-- TODO: Finish Searching properly -->
						<div class="col-lg-5">
							<form action="#" method="POST" class="easydocs-search-form">
								<input type="search" name="keyword" class="form-control" id="easydocs-search" placeholder="<?php esc_attr_e( 'Search for', 'bbp-core' ); ?>" onkeyup="fetch()" />
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
	<ul class="easydocs-navbar sortable">
			<?php
			$i = '';
			while ( $query->have_posts() ) :
				$query->the_post();
				$i ++;
				$depth_one_parents[] = get_the_ID();
				$is_active           = $i == 1 ? 'is-active' : '';
				$doc_counter         = get_pages(
					[
						'child_of'    => get_the_ID(),
						'post_type'   => 'forum',
						'post_status' => [ 'publish', 'draft' ],
					]
				);

				$post_status = get_post_status( get_the_ID() );
				global $post;

				switch ( $post_status ) {
					case 'publish':
						$post_format = 'admin-site-alt3';
						break;

					case 'private':
						$post_format = 'privacy';
						break;

					case 'draft':
						$post_format = 'edit-page';
						break;
				}
				if ( ! empty( $post->post_password ) ) {
					$post_format = 'lock';
				}
				?>
			<li class="easydocs-navitem  <?php echo esc_attr( $is_active ); ?>" data-rel="tab-<?php the_ID(); ?>" data-id="<?php the_ID(); ?>">
				<div class="title">
					<span title="<?php echo esc_attr( $post_status ); ?>" class="dashicons dashicons-<?php echo esc_attr( $post_format ); ?>"></span>
					<?php the_title(); ?>
				</div>
				<div class="total-page">
					<span>
						<?php echo count( $doc_counter ) > 0 ? count( $doc_counter ) : ''; ?>
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

					<a href="<?php the_permalink(); ?>" class="link external-link" target="_blank" data-id="tab-<?php the_ID(); ?>" title="<?php esc_attr_e( 'View this doc item in new tab', 'easydocs' ); ?>">
						<span class="dashicons dashicons-external"></span>
					</a>
					<?php
					if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) :
						?>
					<a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?DeleteID=<?php echo get_the_ID(); ?>" class="link delete parent-delete" title="<?php esc_attr_e( 'Delete this doc permanently', 'bbp-core' ); ?>">
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
						<?php esc_html_e( 'All articles', 'bbp-core' ); ?>
					</li>
					<li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".publish">
						<span class="dashicons dashicons-admin-site-alt3"></span>
						<?php esc_html_e( 'No Reply', 'bbp-core' ); ?>
					</li>

					<li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".private">
						<span class="dashicons dashicons-privacy"></span>
						<?php esc_html_e( 'Solved', 'bbp-core' ); ?>
					</li>
					<li class="easydocs-btn easydocs-btn-orange-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".protected">
						<span class="dashicons dashicons-lock"></span>
						<?php esc_html_e( 'Unsolved', 'bbp-core' ); ?>
					</li>
					<li class="easydocs-btn easydocs-btn-gray-light easydocs-btn-rounded easydocs-btn-sm" data-filter=".draft">
						<span class="dashicons dashicons-edit-page"></span>
						<?php esc_html_e( 'Draft', 'bbp-core' ); ?>
					</li>
				</ul>
			</div>

			<!-- TODO: Notifications, recent topics( ) , recent replies , you will get query from widgets -->
			<ul class="easydocs-accordion sortable accordionjs">
					<?php
							$children = get_children(
								[
									'post_parent' => $item,
									'post_type'   => 'topic',
									'orderby'     => 'menu_order',
									'order'       => 'asc',
									'exclude'     => get_post_thumbnail_id( $item ),
								]
							);

					if ( is_array( $children ) ) :
						foreach ( $children as $child ) :

							$depth_two_parents[] = $child->ID;
							$post_status         = $child->post_status;

							$doc_items = get_children(
								[
									'post_parent' => $child->ID,
									'orderby'     => 'menu_order',
									'post_type'   => 'docs',
									'order'       => 'asc',
									'exclude'     => get_post_thumbnail_id( $child ),
								]
							);

							$child_one = get_children(
								[
									'post_parent' => $child->ID,
									'post_type'   => 'docs',
									'order'       => 'asc',
									'orderby'     => 'menu_order',
									'fields'      => 'ids',
								]
							);

							$depth_two = '';
							foreach ( $doc_items as $doc_item ) {
								$child_depth = get_children(
									[
										'post_parent' => $doc_item->ID,
										'post_type'   => 'docs',
										'fields'      => 'ids',
										'orderby'     => 'menu_order',
										'order'       => 'asc',
									]
								);
								$depth_two   = implode( ',', $child_depth );
							}
							$depth_docs = implode( ',', $child_one ) . ',' . $depth_two . ',' . $child->ID;

							if ( ! empty( $child->post_password ) ) {
								$post_status = 'protected';
							}
							?>
						<li <?php post_class( 'easydocs-accordion-item accordion ez-section-acc-item mix ' . esc_attr( $post_status ) ); ?> data-id="<?php echo esc_attr( $child->ID ); ?>">
							<div class="accordion-title ez-section-title <?php echo count( $doc_items ) > 0 ? 'has-child' : ''; ?>">
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
								<?php if ( count( $doc_items ) > 0 ) : ?>
											<span class="count badge">
												<?php echo count( $doc_items ); ?>
											</span>
										<?php endif; ?>
									</h4>
									<ul class="actions">
								<?php if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) : ?>
										<li>
											<a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?childID=<?php echo $child->ID; ?>&child=" class="child-doc" title="<?php esc_attr_e( 'Add new doc under this doc', 'bbp-core' ); ?>">
												<span class="dashicons dashicons-plus-alt2"></span>
											</a>
										</li>
										<?php endif; ?>
										<li>
											<a href="<?php echo get_permalink( $child ); ?>" target="_blank" title="<?php esc_attr_e( 'View this doc item in new tab', 'easydocs' ); ?>">
												<span class="dashicons dashicons-external"></span>
											</a>
										</li>
											<?php
											if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) :
												?>
											<li class="delete">
												<a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo $depth_docs; ?>" class="section-delete" title="<?php esc_attr_e( 'Delete this doc permanently', 'bbp-core' ); ?>">
													<span class="dashicons dashicons-trash"></span>
												</a>
											</li>
													<?php endif; ?>
									</ul>
								</div>

								<div class="right-content">
									<span class="progress-text">
													<?php
													$positive = (int) get_post_meta( $child->ID, 'positive' );
													$negative = (int) get_post_meta( $child->ID, 'negative', true );

													$positive_title = $positive ? sprintf( _n( '%d Positive vote, ', '%d Positive votes and ', $positive, 'bbp-core' ), number_format_i18n( $positive ) ) : esc_html__( 'No Positive votes, ', 'bbp-core' );
													$negative_title = $negative ? sprintf( _n( '%d Negative vote found.', '%d Negative votes found.', $negative, 'bbp-core' ), number_format_i18n( $negative ) ) : esc_html__( 'No Negative votes.', 'bbp-core' );

													$sum_votes = $positive + $negative;

													if ( $positive || $negative ) {
														echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
													} else {
														esc_html_e( 'No rates', 'bbp-core' );
													}
													?>
									</span>
								</div>
							</div>
							<div class="easydocs-accordion-body nesting-accordion child-docs">
								<div class="nesting-task sortable">

												<?php
												foreach ( $doc_items as $doc_item ) :
													$child_depth = get_children(
														[
															'post_parent' => $doc_item->ID,
															'post_type'   => 'docs',
															'orderby'     => 'menu_order',
															'order'       => 'ASC',
															'exclude'     => get_post_thumbnail_id( $doc_item ),
														]
													);

													$last_section_docs = [];
													if ( is_array( $child_depth ) ) {
														foreach ( $child_depth as $dep3_docs ) {
															$last_section_docs[] = $dep3_docs->ID;
														}
													}
													$last_section_ids = implode( ',', $last_section_docs );

													foreach ( $depth_two_parents as $sec2 ) {
														$parent = $sec2;
													}
													$parent;
													$dep2 = $doc_item->ID;
													?>
										<ul class="accordionjs">
											<li <?php post_class( 'easydocs-accordion-item accordion mix child-one ' . $post_status ); ?> data-id="<?php echo esc_attr( $doc_item->ID ); ?>">
												<div class="accordion-title <?php echo count( $child_depth ) > 0 ? 'has-child' : ''; ?>">
													<?php
													$edit_link = 'javascript:void(0)';
													$target    = '_self';
													if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
														$edit_link = get_edit_post_link( $doc_item );
														$target    = '_blank';
													}
													?>
													<div class="left-content">
														<h4>
															<a href="<?php echo esc_attr( $edit_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="section-last-label">
																<?php echo get_the_title( $doc_item ); ?>
															</a>
															<?php if ( count( $child_depth ) > 0 ) : ?>
																<span class="count badge">
																	<?php echo count( $child_depth ); ?>
																</span>
															<?php endif; ?>
														</h4>
														<ul class="actions">
														<?php
														if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) :
															if ( class_exists( 'EazyDocsPro' ) && eaz_fs()->can_use_premium_code() ) :
																?>
																	<li class="duplicate">
																		<?php do_action( 'eazydocs_child_section_doc_duplicate', $dep2, $parent ); ?>
																	</li>
																		<?php
																	else :
																		?>
																	<li class="duplicate">
																		<a href="javascript:void(0);" class="eazydocs-pro-notice" title="<?php esc_attr_e( 'Duplicate this doc with the child docs.', 'easydocs' ); ?>">
																			<span class="dashicons dashicons-admin-page"></span>
																		</a>
																	</li>
																		<?php
																	endif;
																			endif;
														?>

															<li>
																<a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Post.php?childID=<?php echo $doc_item->ID; ?>&child=" class="child-doc" title="<?php esc_attr_e( 'Add new doc under this doc', 'bbp-core' ); ?>">
																	<span class="dashicons dashicons-plus-alt2"></span>
																</a>
															</li>

															<li>
																<a href="<?php echo get_permalink( $doc_item ); ?>" target="_blank" title="<?php esc_attr_e( 'View this doc item in new tab', 'easydocs' ); ?>">
																	<span class="dashicons dashicons-external"></span>
																</a>
															</li>
															<?php if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) : ?>
																<li class="delete">
																	<a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo esc_attr( $doc_item->ID . ',' . $last_section_ids ); ?>" class="section-delete" title="<?php esc_attr_e( 'Delete this doc permanently', 'bbp-core' ); ?>">
																		<span class="dashicons dashicons-trash"></span>
																	</a>
																</li>
															<?php endif; ?>
														</ul>
													</div>

													<div class="right-content">
														<span class="progress-text">
															<?php
															$positive = (int) get_post_meta( $doc_item->ID, 'positive' );
															$negative = (int) get_post_meta( $doc_item->ID, 'negative', true );

															$positive_title = $positive ? sprintf( _n( '%d Positive vote, ', '%d Positive votes and ', $positive, 'bbp-core' ), number_format_i18n( $positive ) ) : esc_html__( 'No Positive votes, ', 'bbp-core' );
															$negative_title = $negative ? sprintf( _n( '%d Negative vote found.', '%d Negative votes found.', $negative, 'bbp-core' ), number_format_i18n( $negative ) ) : esc_html__( 'No Negative votes.', 'bbp-core' );

															$sum_votes = $positive + $negative;

															if ( $positive || $negative ) {
																echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
															} else {
																esc_html_e( 'No rates', 'bbp-core' );
															}
															?>
														</span>
													</div>

												</div>
												<div class="easydocs-accordion-body nesting-accordion">
													<ul class="nesting-task sortable">
														<?php
														foreach ( $child_depth as $dep3 ) :
															?>
															<li data-id="<?php echo $dep3->ID; ?>" class="child-docs-wrap d-flex justify-content-between">

																<?php
																$edit_link = 'javascript:void(0)';
																$target    = '_self';
																if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
																	$edit_link = get_edit_post_link( $dep3 );
																	$target    = '_blank';
																}
																?>

																<a href="<?php echo esc_attr( $edit_link ); ?>" target="<?php echo esc_attr( $target ); ?>" class="child-last-label">
																   <?php echo $dep3->post_title; ?>
																</a>
																<div class="child-right-content d-flex">

																	<?php
																	if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
																		if ( class_exists( 'EazyDocsPro' ) && eaz_fs()->can_use_premium_code() ) :
																			do_action( 'eazydocs_single_duplicate', $dep3->ID );
																		else :
																			?>
																			<a href="javascript:void(0);" target="_blank" class="eazydocs-pro-notice" title="<?php esc_attr_e( 'Duplicate this doc with the child docs.', 'easydocs' ); ?>">
																				<span class="dashicons dashicons-admin-page"></span>
																			</a>
																			<?php
																		endif;
																	}
																	?>
																	<a href="<?php echo get_permalink( $dep3 ); ?>" target="_blank" class="child-view-link" title="<?php esc_attr_e( 'View this doc item in new tab', 'easydocs' ); ?>">
																		<span class="dashicons dashicons-external"></span>
																	</a>
																	<?php
																	if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) :
																		?>
																	<a href="<?php echo admin_url( 'admin.php' ); ?>/Delete_Post.php?ID=<?php echo $dep3->ID; ?>" class="child-delete" title="<?php esc_attr_e( 'Delete this doc permanently', 'bbp-core' ); ?>">
																		<span class="dashicons dashicons-trash"></span>
																	</a>
																	<?php endif; ?>

																	<span class="progress-text">
																		<?php
																		$positive = (int) get_post_meta( $dep3->ID, 'positive' );
																		$negative = (int) get_post_meta( $dep3->ID, 'negative', true );

																		$positive_title = $positive ? sprintf( _n( '%d Positive vote, ', '%d Positive votes and ', $positive, 'bbp-core' ), number_format_i18n( $positive ) ) : esc_html__( 'No Positive votes, ', 'bbp-core' );
																		$negative_title = $negative ? sprintf( _n( '%d Negative vote found.', '%d Negative votes found.', $negative, 'bbp-core' ), number_format_i18n( $negative ) ) : esc_html__( 'No Negative votes.', 'bbp-core' );

																		$sum_votes = $positive + $negative;

																		if ( $positive || $negative ) {
																			echo "<progress id='file' value='$positive' max='$sum_votes' title='$positive_title$negative_title'> </progress>";
																		} else {
																			esc_html_e( 'No rates', 'bbp-core' );
																		}
																		?>
																	</span>
																</div>
															</li>
															<?php
																	endforeach;
														?>
													</ul>
												</div>
											</li>
										</ul>
													<?php
												endforeach;
												?>
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

						<?php
							$current_theme = get_template();
						if ( $current_theme == 'docy' || $current_theme == 'docly' || class_exists( 'EazyDocsPro' ) ) {
							eazydocs_one_page( $item );

						}
						?>

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

<!-- TODO: Classic ui should be made a dropdown, for forum, topics and replies -->
