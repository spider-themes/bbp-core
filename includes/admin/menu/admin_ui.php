<?php
if ( ! class_exists( 'bbPress' ) ) {
	return;
}
$parent_forums     = [];
$fcount            = wp_count_posts( bbp_get_forum_post_type() );
$forum_count       = (int) ( $fcount->publish + $fcount->hidden + $fcount->spam );
$bbpc_opt          = get_option( 'bbp_core_settings' );
$filter_set        = $bbpc_opt['filter_buttons'] ?? [ 'open', 'closed', 'hidden', 'no_reply', 'all', 'trash' ];
?>
<div class="wrap">
<div class="body-dark">
	<?php
	if ( $forum_count > 0 ) :
		include __DIR__ . '/admin_ui/header.php';
		// print_r(__DIR__);
		?>

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

					// Left Sidebar Forums.
					include __DIR__ . '/admin_ui/forums.php';
					?>

					<div class="easydocs-tab-content">
						<?php
						$ids                 = 0;
						$container           = 1;
						if ( is_array( $parent_forums ) ) :
							foreach ( $parent_forums as $item ) :
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

								<!-- Tab filters. -->
								<?php include __DIR__ . '/admin_ui/tab_filters.php'; ?>

								<!-- Children topics. -->
								<?php include __DIR__ . '/admin_ui/topics.php'; ?>

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
		<?php
	endif; //TODO: Fix open topics not being selected issue.
	?>
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

