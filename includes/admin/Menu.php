<?php
namespace admin;

class Menu {
	function __construct() {
		add_action( 'admin_menu', [ $this, 'bbpc_admin_menu' ] );
	}

	/**
	 * Create Admin menu
	 *
	 * @return void
	 */
	public function bbpc_admin_menu() {
		add_menu_page( __( 'BBP Core', 'bbp-core' ), __( 'BBP Core', 'bbp-core' ), 'manage_options', 'bbp-core', [ $this, 'bbpc_plugin_page' ], 'dashicons-buddicons-bbpress-logo', 20 );
	}

	/**
	 * Plugin page callback function
	 *
	 * @return void
	 */
	public function bbpc_plugin_page() {
		?>
		<div class="bbp-core-main">
			<div class="bbpc-heading">
				<h1 class="wp-heading-inline"><?php esc_html_e( 'BBP Core', 'bbp-core' ); ?></h1>
				<p class="bbp-core-intro">
					<?php esc_html_e( 'Expand bbPress powered forums with useful features.', 'bbp-core' ); ?>
				</p>
			</div>

			<div class="bbpc-dashboard">
			<div class="bbpc-panel">
				<div class="bbpc-logo">
					<img src="<?php echo esc_url( BBPC_ASSETS . '/img/logo.svg' ); ?>" alt="<?php esc_attr_e( 'BBP Core Logo', 'bbp-core' ); ?>">
				</div>
				<ul class="bbpc-group-menu">
					<li><a href="admin.php?page=bbp-core-settings"><?php esc_html_e( 'Settings', 'bbp-core' ); ?></a></li>
				</ul>
			</div>

			<?php if ( class_exists( 'bbPress' ) ) : ?>
			<div class="bbpc-stats">
				<?php
				$forum_posts = wp_count_posts( bbp_get_forum_post_type() );
				$forum_total = $forum_posts->publish + $forum_posts->private + $forum_posts->hidden;

				$topic_posts = wp_count_posts( bbp_get_topic_post_type() );
				$topic_total = $topic_posts->publish + $topic_posts->private + $topic_posts->hidden;

				$reply_posts = wp_count_posts( bbp_get_reply_post_type() );
				$reply_total = $reply_posts->publish + $reply_posts->private + $reply_posts->hidden;

				$topic_tag_count = wp_count_terms( bbp_get_topic_tag_tax_id(), [ 'hide_empty' => true ] );

				if ( current_user_can( 'edit_topic_tags' ) ) {
					$empty_topic_tag_count = wp_count_terms( bbp_get_topic_tag_tax_id() ) - $topic_tag_count;
				}
				?>
				<div class="bbpc-stat-cards">
					<div class="bbpc-stat-card forum-stats">
						<div class="bbpc-card-heading">
							<h2><?php esc_html_e( 'Forum stats', 'bbp-core' ); ?></h2>
						</div>

						<div class="bbpc-card-data">
							<ul class="forum-info">
								<li>
									<i class="dashicons dashicons-buddicons-forums"></i>
									<?php echo esc_html( $forum_total ) . esc_html__( ' Forums', 'bbp-core' ); ?> </i>
								</li>

								<li>
									<i class="dashicons dashicons-buddicons-topics"></i>
									<?php echo esc_html( $topic_total ) . esc_html__( ' Topics', 'bbp-core' ); ?> </i>
								</li>

								<li>
									<i class="dashicons dashicons-buddicons-replies"></i>
									<?php echo esc_html( $reply_total ) . esc_html__( ' Replies', 'bbp-core' ); ?> </i>
								</li>

								<li>
									<i class="dashicons dashicons-tag"></i>
									<?php echo esc_html( $topic_tag_count ) . esc_html__( ' Topic Tags', 'bbp-core' ); ?> </i>
								</li>

								<?php if ( isset( $empty_topic_tag_count ) ) : ?>
									<li>
										<i class="dashicons dashicons-tag"></i>
										<?php echo esc_html( $empty_topic_tag_count ) . esc_html__( ' Empty Topic Tags', 'bbp-core' ); ?> </i>
									</li>
								<?php endif; ?>

							</ul>
						</div>
					</div>
				</div>
			</div>
				<?php
			else :
				$admin = new \admin();
				$admin->admin_notices();
				?>

				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
