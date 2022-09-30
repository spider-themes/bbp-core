<?php
if ( ! class_exists( 'bbPress' ) ) {
	return;
}
$depth_one_parents = [];
$depth_two_parents = [];
$fcount            = wp_count_posts( bbp_get_forum_post_type() );
$forum_count       = (int) ( $fcount->publish + $fcount->hidden + $fcount->spam );
$bbpc_opt          = get_option( 'bbp_core_settings' );
$filter_set        = $bbpc_opt['filter_buttons'] ?? [ 'open', 'closed', 'hidden', 'no_reply', 'solved', 'unsolved', 'all', 'trash' ];
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
								<a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Forum.php?bbp_parent_title=" target="_blank" type="button" id="bbpc-forum" class="easydocs-btn easydocs-btn-outline-blue easydocs-btn-sm easydocs-btn-round">
									<span class="dashicons dashicons-plus-alt2"></span>
									<?php esc_html_e( 'Add Forum', 'bbp-core' ); ?>
								</a>
							</div>
						</div>

						<div class="col-lg-5">
							<form action="#" method="POST" class="easydocs-search-form">
							<div class="search-icon">
									<span class="dashicons dashicons-search"></span>
								</div>
								<input type="search" name="keyword" class="form-control" id="bbpc-search" placeholder="<?php esc_attr_e( 'Search for', 'bbp-core' ); ?>" onkeyup="fetch()" />
							</form>
						</div>
						<div class="col-lg-3">
							<div class="navbar-right">
								<ul class="d-flex justify-content-end bbpc-right-nav">
									<li>
										<div class="header-notify-icon bbpc-settings-icon">
												<a href="<?php echo admin_url( 'admin.php?page=bbp-core-settings' ); ?>">
													<img src="<?php echo BBPC_IMG; ?>/admin/admin-settings.svg" alt="<?php esc_html_e( 'Settings Icon', 'bbp-core' ); ?>">
												</a>
										</div>
									</li>
									<li>
										<div class="easydocs-settings">
											<?php if ( current_user_can( 'edit_posts' ) ) : ?>
												<div class="header-notify-icons">
													<select name="bbpc_classic_ui" id="bbpc_classic_ui">
														<option value="<?php echo admin_url( 'admin.php?page=bbp-core' ); ?>"><?php esc_html_e( 'Choose classic UI', 'bbp-core' ); ?></option>
														<option value="<?php echo admin_url( 'edit.php?post_type=forum' ); ?>"><?php esc_html_e( 'Forums', 'bbp-core' ); ?></option>
														<option value="<?php echo admin_url( 'edit.php?post_type=topic' ); ?>"><?php esc_html_e( 'Topics', 'bbp-core' ); ?></option>
														<option value="<?php echo admin_url( 'edit.php?post_type=reply' ); ?>"><?php esc_html_e( 'Replies', 'bbp-core' ); ?></option>
													</select>
												</div>
											<?php endif; ?>
										</div>
									</li>
									<?php
									if ( class_exists( 'BBPCorePro' ) ) :
										do_action( 'bbpcorepro_notification' );
									else :
										?>
										<li class="easydocs-notification bbp-core-pro-notification" title="<?php esc_attr_e( 'Notifications', 'bbp-core' ); ?>">
											<div class="header-notify-icon">
												<img class="notify-icon" src="<?php echo BBPC_IMG; ?>/admin/notification.svg" alt="<?php esc_html_e( 'Notify Icon', 'bbp-core' ); ?>">
												<img class="settings-pro-icon" src="<?php echo BBPC_IMG; ?>/admin/pro-icon.png" alt="<?php esc_html_e( 'Pro Icon', 'bbp-core' ); ?>">
											</div>
										</li>
										<?php
									endif;
									?>
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
					'post_type'      => bbp_get_forum_post_type(),
					'posts_per_page' => -1,
					'post_parent'    => 0,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'post_status'    => 'publish',
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
						'post_type'   => bbp_get_topic_post_type(),
						'orderby'     => 'menu_order',
						'order'       => 'asc',
						'post_status' => [ 'any', 'spam', 'trash' ],
					]
				);
				?>
			<li class="easydocs-navitem <?php echo esc_attr( $is_active ); ?>" data-rel="tab-<?php the_ID(); ?>" data-id="<?php the_ID(); ?>">
				<div class="title">
					<?php
					if ( has_post_thumbnail() ) :
							echo get_the_post_thumbnail( $current_post, 'bbpc_20x20' );
						?>
						<?php else : ?>
					<span class="dashicons dashicons-buddicons-forums"></span>
					<?php endif; ?>
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
					<a href="<?php echo admin_url( 'admin.php' ); ?>/menu/Delete_Forum.php?forum_ID=<?php echo get_the_ID(); ?>" class="link forum-delete" title="<?php esc_attr_e( 'Delete this forum permanently', 'bbp-core' ); ?>">
						<span class="dashicons dashicons-trash"></span>
					</a>
					<?php endif; ?>
				</div>
			</li>
				<?php
				$forum_parent = get_the_ID();
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
			$ids                 = 0;
			$container           = 1;
			if ( is_array( $depth_one_parents ) ) :
				foreach ( $depth_one_parents as $item ) :
					$ids ++;
					$container ++;
					$active = $ids == 1 ? ' tab-active' : '';

					$children = new WP_Query(
						[
							'post_parent'    => $item,
							'post_type'      => bbp_get_topic_post_type(),
							'orderby'        => 'menu_order',
							'order'          => 'asc',
							'posts_per_page' => -1,
							'post_status'    => [ 'any', 'spam', 'trash' ],
						]
					);

					$count_open     = 0;
					$count_closed   = 0;
					$count_hidden   = 0;
					$count_no_reply = 0;
					$count_solved   = 0;
					$count_unsolved = 0;
					$count_trash    = 0;

					while ( $children->have_posts() ) :
						$children->the_post();
						$topic_id = get_the_ID();

						$replies = get_children(
							[
								'post_parent' => $topic_id,
								'post_type'   => bbp_get_reply_post_type(),
								'post_status' => [ 'publish', 'draft', 'pending' ],
							]
						);

						// Count open/closed topics.
						if ( bbp_is_topic_closed( $topic_id ) ) {
							$count_closed++;
						} else {
							$count_open++;
						}

						// Count spam( hidden ) topics.
						if ( bbp_is_topic_spam( $topic_id ) ) {
							$count_hidden++;
						}

						// Replies count.
						if ( 0 == count( $replies ) ) {
							$count_no_reply++;
						}

						// Count solved.
						if ( $GLOBALS['bbp_solved_topic']->is_solved( $topic_id ) ) {
							$count_solved++;
						} else {
							$count_unsolved++;
						}

						// Count trash.
						if ( bbp_is_topic_trash( $topic_id ) ) {
							$count_trash++;
						}

				endwhile;
					wp_reset_postdata();
					?>
		<div class="easydocs-tab <?php echo esc_attr( $active ); ?>" id="tab-<?php echo esc_attr( $item ); ?>">
			<div class="easydocs-filter-container">
				<ul class="single-item-filter">
					<?php if ( in_array( 'open', $filter_set, true ) ) : ?>
						<li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm is-active" cookie-id="open-<?php echo esc_attr( $item ); ?>" data-filter=".open-topics">
							<svg width="15px" height="15px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
								<g>
									<path fill="none" d="M0 0h24v24H0z"/>
									<path d="M16.1 3a5.023 5.023 0 0 0 0 2H4.511l7.55 6.662 5.049-4.52c.426.527.958.966 1.563 1.285l-6.601 5.911L4 7.216V19h16V8.9a5.023 5.023 0 0 0 2 0V20a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h13.1zM21 7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
								</g>
							</svg>
							<?php esc_html_e( 'Open', 'bbp-core' ); ?>
							<span class="filter-count-badge"> <?php echo esc_html( $count_open ); ?> </span>
						</li>
					<?php endif; ?>

					<?php if ( in_array( 'closed', $filter_set, true ) ) : ?>
					<li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" cookie-id="closed-<?php echo esc_attr( $item ); ?>" data-filter=".closed-topics">
							<svg width="15px" height="15px" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/></svg>

							<?php esc_html_e( 'Closed', 'bbp-core' ); ?>
							<span class="filter-count-badge"> <?php echo esc_html( $count_closed ); ?> </span>
					</li>
					<?php endif; ?>

					<?php if ( in_array( 'hidden', $filter_set, true ) ) : ?>
					<li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" cookie-id="hidden-<?php echo esc_attr( $item ); ?>" data-filter=".hidden-topics">
						<svg width="15px" height="15px" viewBox="0 0 24 24" role="img" xmlns="http://www.w3.org/2000/svg" aria-labelledby="notAllowedIconTitle" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" color="#000000"> <title id="notAllowedIconTitle">Not Allowed</title> <circle cx="12" cy="12" r="10"/> <path stroke-linecap="square" d="M5,5 L19,19"/> </svg>

							<?php esc_html_e( 'Hidden', 'bbp-core' ); ?>
							<span class="filter-count-badge"> <?php echo esc_html( $count_hidden ); ?> </span>
					</li>
					<?php endif; ?>

					<?php if ( in_array( 'no_reply', $filter_set, true ) ) : ?>
					<li class="easydocs-btn easydocs-btn-blue-light easydocs-btn-rounded easydocs-btn-sm" cookie-id="no_reply-<?php echo esc_attr( $item ); ?>" data-filter=".no-reply">
						<svg height="15px" width="15px" enable-background="new 0 0 24 24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
						<path d="m21.5 24h-19c-1.379 0-2.5-1.122-2.5-2.5v-9c0-.276.224-.5.5-.5s.5.224.5.5v9c0 .827.673 1.5 1.5 1.5h19c.827 0 1.5-.673 1.5-1.5v-15c0-.827-.673-1.5-1.5-1.5h-4c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h4c1.379 0 2.5 1.122 2.5 2.5v15c0 1.378-1.121 2.5-2.5 2.5z"/>
						<path d="m17.5 16c-.187 0-.361-.104-.447-.276l-.84-1.68c-.94-1.878-2.825-3.044-4.923-3.044h-3.29v3.5c0 .202-.122.385-.309.462-.186.078-.401.035-.545-.108l-7-7c-.195-.195-.195-.512 0-.707l7-7c.144-.144.36-.187.545-.109.187.077.309.26.309.462v3.5h1.5c4.687 0 8.5 3.813 8.5 8.5v3c0 .232-.159.434-.385.487-.039.009-.077.013-.115.013zm-10-6h3.79c2.401 0 4.567 1.293 5.71 3.391v-.891c0-4.136-3.364-7.5-7.5-7.5h-2c-.276 0-.5-.224-.5-.5v-2.793l-5.793 5.793 5.793 5.793v-2.793c0-.276.224-.5.5-.5z"/>
						</svg>
						<?php esc_html_e( 'No Reply', 'bbp-core' ); ?>
						<span class="filter-count-badge"> <?php echo esc_html( $count_no_reply ); ?> </span>
					</li>
					<?php endif; ?>

					<?php if ( in_array( 'solved', $filter_set, true ) ) : ?>
					<li class="easydocs-btn easydocs-btn-green-light easydocs-btn-rounded easydocs-btn-sm" cookie-id="solved-<?php echo esc_attr( $item ); ?>" data-filter=".solved">
						<svg width="12pt" height="12pt" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
						<path d="m369.16 174.77c7.8125 7.8125 7.8125 20.477 0 28.285l-134.17 134.18c-7.8125 7.8086-20.473 7.8086-28.285 0l-63.871-63.875c-7.8125-7.8086-7.8125-20.473 0-28.281 7.8086-7.8125 20.473-7.8125 28.281 0l49.73 49.73 120.03-120.04c7.8125-7.8086 20.477-7.8086 28.285 0zm142.84 81.23c0 141.5-114.52 256-256 256-141.5 0-256-114.52-256-256 0-141.5 114.52-256 256-256 141.5 0 256 114.52 256 256zm-40 0c0-119.39-96.621-216-216-216-119.39 0-216 96.621-216 216 0 119.39 96.621 216 216 216 119.39 0 216-96.621 216-216z"/>
						</svg>
						<?php esc_html_e( 'Solved', 'bbp-core' ); ?>
						<span class="filter-count-badge"> <?php echo esc_html( $count_solved ); ?> </span>
					</li>
					<?php endif; ?>

					<?php if ( in_array( 'unsolved', $filter_set, true ) ) : ?>
					<li class="easydocs-btn easydocs-btn-orange-light easydocs-btn-rounded easydocs-btn-sm" cookie-id="unsolved-<?php echo esc_attr( $item ); ?>" data-filter=".unsolved">
						<svg width="15px" height="15px" viewBox="0 0 358 358" xmlns="http://www.w3.org/2000/svg">
						<path d="m134.34 147.24c-3.478-3.349-7.513-5.933-11.992-7.68-4.652-1.815-9.567-2.638-14.609-2.447-8.8037 0.335-17.221 3.946-23.702 10.169-6.4875 6.23-10.43 14.498-11.1 23.28-0.4301 5.619 0.394 11.089 2.4493 16.26 1.1399 2.868 4.3891 4.268 7.2568 3.128 2.868-1.14 4.2687-4.389 3.1287-7.257-1.4216-3.576-1.9907-7.37-1.6914-11.279 0.9542-12.495 11.533-22.656 24.084-23.133 6.913-0.262 13.457 2.227 18.425 7.01 4.97 4.785 7.707 11.219 7.707 18.117 0 5.188-6.784 13.533-17.706 21.779-4.06 3.066-7.395 7.135-9.646 11.767-2.212 4.554-3.382 9.658-3.382 14.763v5.009c0 3.086 2.502 5.588 5.588 5.588s5.588-2.502 5.588-5.588v-5.009c0-6.968 3.21-13.551 8.587-17.611 10.103-7.628 22.147-19.146 22.147-30.698 0-5.023-1.008-9.889-2.995-14.463-1.918-4.416-4.656-8.354-8.137-11.705z"/>
						<path d="m109.15 260.03c4.629 0 8.382-3.753 8.382-8.382 0-4.63-3.753-8.382-8.382-8.382s-8.382 3.752-8.382 8.382c0 4.629 3.753 8.382 8.382 8.382z"/>
						<path d="m198.56 92.387h-68.058c2.468-3.965 6.59-11.758 6.59-19.558 0-7.4638-2.906-14.48-8.184-19.757-5.277-5.2765-12.293-8.1825-19.756-8.1825s-14.479 2.906-19.756 8.1825c-5.2775 5.2769-8.1838 12.294-8.1838 19.757 0 7.7997 4.122 15.592 6.5902 19.558h-68.058c-10.784 0-19.558 8.7733-19.558 19.558v68.075c0 4.058 2.2024 7.801 5.7477 9.769 3.542 1.966 7.8786 1.856 11.317-0.288 4.9376-3.076 10.048-4.913 13.669-4.913 9.2437 0 16.764 7.521 16.764 16.764 0 9.244-7.5202 16.764-16.764 16.764-3.6229 0-8.7323-1.837-13.668-4.915-3.4386-2.143-7.7749-2.253-11.317-0.288-3.546 1.968-5.7486 5.711-5.7486 9.769v68.078c0 10.784 8.7736 19.558 19.558 19.558h68.058c-2.4681 3.964-6.5902 11.754-6.5902 19.558 0 15.406 12.534 27.94 27.94 27.94 15.406 0 27.94-12.534 27.94-27.94 0-7.804-4.122-15.594-6.59-19.558h68.058c10.784 0 19.558-8.774 19.558-19.558v-68.078c0-4.058-2.203-7.8-5.748-9.769-3.542-1.966-7.879-1.855-11.319 0.289-4.935 3.077-10.044 4.914-13.667 4.914-9.243 0-16.764-7.52-16.764-16.764 0-9.243 7.521-16.764 16.764-16.764 3.622 0 8.732 1.837 13.668 4.913 3.44 2.143 7.776 2.254 11.318 0.288 3.546-1.968 5.748-5.711 5.748-9.769v-68.075c0-10.785-8.774-19.558-19.558-19.558zm8.382 87.616c-3.965-2.468-11.758-6.591-19.558-6.591-7.463 0-14.479 2.906-19.756 8.183-5.278 5.277-8.184 12.293-8.184 19.757 0 15.406 12.534 27.94 27.94 27.94 7.803 0 15.595-4.124 19.558-6.593v68.061c0 4.622-3.76 8.382-8.382 8.382h-68.079c-4.056 0-7.798 2.202-9.766 5.747-1.966 3.542-1.855 7.879 0.288 11.318 3.077 4.935 4.913 10.045 4.913 13.669 0 9.243-7.52 16.764-16.764 16.764-9.2436 0-16.764-7.521-16.764-16.764 0-3.624 1.8364-8.734 4.9125-13.669 2.1439-3.439 2.2542-7.776 0.2881-11.318-1.9676-3.545-5.7097-5.747-9.7661-5.747h-68.078c-4.6219 0-8.382-3.76-8.382-8.382v-68.061c3.9647 2.469 11.756 6.593 19.558 6.593 15.406 0 27.94-12.534 27.94-27.94 0-7.464-2.9065-14.48-8.184-19.757-5.277-5.277-12.293-8.183-19.756-8.183-7.7992 0-15.592 4.122-19.558 6.591v-68.058c0-4.622 3.7601-8.382 8.382-8.382h68.078c4.0563 0 7.7985-2.203 9.766-5.7473 1.9661-3.5422 1.8558-7.8792-0.2876-11.318-3.0765-4.9375-4.913-10.048-4.913-13.669 0-9.2436 7.5202-16.764 16.764-16.764 9.244 0 16.764 7.5203 16.764 16.764 0 3.6217-1.837 8.7316-4.913 13.668-2.144 3.4394-2.254 7.7764-0.288 11.319 1.968 3.5443 5.71 5.7473 9.766 5.7473h68.079c4.622 0 8.382 3.76 8.382 8.382v68.058z"/>
						<path d="m329.88 75.622c-7.8 0-15.592 4.1221-19.558 6.5905v-62.47c0-10.784-8.774-19.558-19.558-19.558h-62.491c-4.056 0-7.798 2.2021-9.766 5.7468-1.966 3.5422-1.856 7.8792 0.288 11.318 3.076 4.9376 4.913 10.048 4.913 13.669 0 9.2437-7.52 16.764-16.764 16.764s-16.764-7.5202-16.764-16.764c0-3.6216 1.837-8.7316 4.913-13.668 2.143-3.4394 2.254-7.7764 0.288-11.319-1.968-3.5448-5.71-5.7469-9.766-5.7469h-62.491c-10.784 0-19.558 8.7736-19.558 19.558v8.3819c0 3.0862 2.502 5.588 5.588 5.588s5.588-2.5018 5.588-5.588v-8.3819c0-4.6219 3.76-8.382 8.382-8.382h62.47c-2.467 3.9649-6.59 11.758-6.59 19.558 0 15.406 12.534 27.94 27.94 27.94s27.94-12.534 27.94-27.94c0-7.7995-4.122-15.592-6.591-19.558h62.471c4.622 0 8.382 3.7601 8.382 8.382v62.49c0 4.0563 2.202 7.7985 5.746 9.766 3.543 1.9661 7.88 1.8557 11.318-0.2876 4.938-3.0765 10.048-4.9132 13.67-4.9132 9.243 0 16.764 7.5203 16.764 16.764 0 9.243-7.521 16.764-16.764 16.764-3.622 0-8.732-1.837-13.669-4.913-3.439-2.144-7.776-2.254-11.318-0.288-3.545 1.967-5.747 5.709-5.747 9.766v62.49c0 4.622-3.76 8.382-8.382 8.382h-55.88c-3.086 0-5.588 2.502-5.588 5.588s2.502 5.588 5.588 5.588h55.88c10.784 0 19.558-8.773 19.558-19.558v-62.47c3.965 2.468 11.758 6.59 19.558 6.59 15.406 0 27.94-12.533 27.94-27.94 0-15.406-12.534-27.939-27.94-27.939z"/>
						</svg>
						<?php esc_html_e( 'Unsolved', 'bbp-core' ); ?>
						<span class="filter-count-badge"> <?php echo esc_html( $count_unsolved ); ?> </span>
					</li>
					<?php endif; ?>

					<?php if ( in_array( 'all', $filter_set, true ) ) : ?>
					<li class="easydocs-btn easydocs-btn-black-light easydocs-btn-rounded easydocs-btn-sm" cookie-id="all-<?php echo esc_attr( $item ); ?>" data-filter="all">
						<svg height="15px" width="15px" enable-background="new 0 0 512 512" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
								<path d="M352,0H64C28.704,0,0,28.704,0,64v192c0,35.296,28.704,64,64,64v80c0,6.24,3.648,11.936,9.312,14.528    C75.456,415.52,77.728,416,80,416c3.744,0,7.456-1.312,10.4-3.872L197.92,320H352c35.296,0,64-28.704,64-64V64    C416,28.704,387.296,0,352,0z M384,256c0,17.632-14.368,32-32,32H192c-3.808,0-7.52,1.344-10.4,3.872L96,365.216V304    c0-8.832-7.168-16-16-16H64c-17.632,0-32-14.368-32-32V64c0-17.632,14.368-32,32-32h288c17.632,0,32,14.368,32,32V256z"/>
								<path d="m469.22 99.744c-8.384-2.88-17.44 1.536-20.352 9.92-2.88 8.352 1.536 17.44 9.92 20.352 12.672 4.352 21.216 16.416 21.216 29.984v192c0 17.632-14.368 32-32 32h-16c-8.832 0-16 7.168-16 16v61.216l-85.6-73.344c-2.88-2.528-6.592-3.872-10.4-3.872h-141.76c-8.832 0-16 7.168-16 16s7.136 16 16 16h135.84l107.52 92.128c2.944 2.56 6.656 3.872 10.4 3.872 2.272 0 4.544-0.48 6.688-1.472 5.664-2.592 9.312-8.288 9.312-14.528v-80c35.296 0 64-28.704 64-64v-192c0-27.2-17.184-51.424-42.784-60.256z"/><path d="m304 96h-192c-8.832 0-16 7.168-16 16s7.168 16 16 16h192c8.832 0 16-7.168 16-16s-7.168-16-16-16z"/><path d="m240 160h-128c-8.832 0-16 7.168-16 16s7.168 16 16 16h128c8.832 0 16-7.168 16-16s-7.168-16-16-16z"/></svg>

						<?php esc_html_e( 'All topics', 'bbp-core' ); ?>
						<span class="filter-count-badge"> <?php echo esc_html( $children->post_count ); ?> </span>
					</li>
					<?php endif; ?>
				</ul>

					<?php if ( in_array( 'trash', $filter_set, true ) ) : ?>
					<li class="easydocs-btn-sm bbpc-trash-filter">
						<a href="<?php echo admin_url( 'edit.php?post_status=trash&post_type=topic' ); ?>">
						<svg width="15px" height="15px" viewBox="0 0 24 24" id="magicoon-Regular" xmlns="http://www.w3.org/2000/svg"><defs></defs><g id="trash-Regular"><path id="trash-Regular-2" data-name="trash-Regular" class="cls-1" d="M21,5.25H17.441A1.251,1.251,0,0,1,16.255,4.4l-.316-.95a1.746,1.746,0,0,0-1.66-1.2H9.721a1.745,1.745,0,0,0-1.66,1.2l-.316.948a1.251,1.251,0,0,1-1.186.855H3a.75.75,0,0,0,0,1.5H4.3l.767,11.5a3.76,3.76,0,0,0,3.742,3.5h6.386a3.76,3.76,0,0,0,3.742-3.5L19.7,6.75H21a.75.75,0,0,0,0-1.5ZM9.483,3.921a.252.252,0,0,1,.238-.171h4.558a.252.252,0,0,1,.238.17l.316.95a2.777,2.777,0,0,0,.161.38H9.006a2.737,2.737,0,0,0,.161-.381ZM17.438,18.15a2.255,2.255,0,0,1-2.245,2.1H8.807a2.255,2.255,0,0,1-2.245-2.1L5.8,6.75h.757a2.783,2.783,0,0,0,.317-.025A.736.736,0,0,0,7,6.75H17a.736.736,0,0,0,.124-.025,2.783,2.783,0,0,0,.317.025H18.2ZM14.75,11v5a.75.75,0,0,1-1.5,0V11a.75.75,0,0,1,1.5,0Zm-4,0v5a.75.75,0,0,1-1.5,0V11a.75.75,0,0,1,1.5,0Z"/></g></svg>
						<?php esc_html_e( 'Trash', 'bbp-core' ); ?>
						<span class="filter-count-badge"> <?php echo esc_html( $count_trash ); ?> </span>
					</a>
					</li>
					<?php endif; ?>
			</div>

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

						while ( $replies->have_posts() ) :
							$replies->the_post();
							$reply_id = get_the_ID();

							if ( bbp_is_reply_pending( $reply_id ) ) {
								$pending_replies[] = $reply_id;
							}

						endwhile;
						wp_reset_postdata();

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
									$pending_replies_count = count( $pending_replies );
									$reply_count           = $replies->found_posts - $pending_replies_count;
									?>
									<div title="<?php echo esc_attr( $reply_count ) . __( ' Publised replies', 'bbp-core' ); ?>">
										<span class="bbpc-reply-count bbpc-published-replies">
											<?php echo esc_html( $reply_count ); ?>
										</span>
									</div>

									<div click-target='<?php echo esc_attr( $current_topic_id ); ?>' title="<?php echo esc_attr( $pending_replies_count ) . __( ' Pending replies', 'bbp-core' ); ?>">
										<span class="bbpc-reply-count bbpc-pending-replies">
											<?php echo esc_html( $pending_replies_count ); ?>
										</span>
									</div>
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

			<a class="button button-info section-doc" id="bbpc-topic" target="_blank" name="submit" href="<?php echo admin_url( 'admin.php' ); ?>/Create_Topic.php?bbp_parentID=<?php echo $item; ?>&is_bbp_section=">
					<?php esc_html_e( 'Add Topic', 'bbp-core' ); ?>
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
				<p class="big-p"> <?php esc_html_e( 'No forum has been found . Perhaps', 'bbp-core' ); ?> </p>
				<p> <br>
					<a href="<?php echo admin_url( 'admin.php' ); ?>/Create_Forum.php?bbp_parent_title=" target="_blank" type="button" id="bbpc-forum" class="button button-primary ezd-btn btn-lg">
						<?php esc_html_e( 'Create Forum', 'bbp-core' ); ?>
					</a>
				</p>
		</div>
	<?php endif; //TODO: Fix open topics not being selected issue. ?>
	</div>
</div>

<script>
	(function ($) {
		$(document).ready(function () {
			let docContainer = document.querySelectorAll('.easydocs-tab');

			var config = {
			controls: {
				scope: 'local',
			},
			animation: {
				enable: false,
			},
			load: {
				filter: '<?php echo esc_js( $bbpc_opt['default_filter'] ?? '.open-topics' ); ?>'
			}
			};

			for (let i = 0; i < docContainer.length; i++) {
			var mixer1 = mixitup(docContainer[i], config);
		}
	});
})(jQuery);
</script>

