<?php
namespace admin\Blocks;

use WP_Query;

class Register {
	public function __construct() {
		add_action( 'init', [ $this, 'blocks_init' ] );
		add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
	}

	public function register_block_category( $categories, $post ) {
		return array_merge(
			$categories,
			[
				[
					'slug'  => 'bbp-core',
					'title' => __( 'BBP Core', 'bbp-core' ),
				],
			]
		);
	}

	/**
	 * Enqueue assets for the Gutenberg block editor
	 * This ensures the AJAX URL is available for block scripts
	 */
	public function enqueue_editor_assets() {
		$is_pro_active = class_exists( 'BBPCorePro' );
		wp_localize_script( 'wp-edit-post', 'bbpc_editor_config', [
			'ajaxurl'          => admin_url( 'admin-ajax.php' ),
		] );
		wp_localize_script( 'wp-edit-post', 'bbpc_upsell_config', [
			'is_pro_active'    => $is_pro_active,
			'upsell_image_url' => defined( 'BBPC_IMG' ) ? BBPC_IMG . 'upsell-forum-ajax.png' : '',
			'upgrade_url'      => admin_url( 'admin.php?page=bbp-core-pricing' ),
		] );
	}

	public function blocks_init() {


		register_block_type( __DIR__ . '/../../build/forum-posts', [
			'render_callback' => [ $this, 'render_forum_posts' ],
		] );

		register_block_type( __DIR__ . '/../../build/forum-tab', [
			'render_callback' => [ $this, 'render_forum_tab' ],
		] );

		register_block_type( __DIR__ . '/../../build/forums', [
			'render_callback' => [ $this, 'render_forums' ],
		] );

		register_block_type( __DIR__ . '/../../build/search', [
			'render_callback' => [ $this, 'render_search' ],
		] );

		register_block_type( __DIR__ . '/../../build/single-forum', [
			'render_callback' => [ $this, 'render_single_forum' ],
		] );
		
		register_block_type( __DIR__ . '/../../build/forum-ajax', [
			'render_callback' => [ $this, 'render_forum_ajax' ],
		] );
	}



	public function render_forum_posts( $attributes, $content ) {
		if ( ! wp_style_is( 'bbpc-el-widgets', 'registered' ) ) {
			if ( defined( 'BBPC_FRONT_ASS' ) ) {
				wp_register_style( 'bbpc-el-widgets', BBPC_FRONT_ASS . 'css/el-widgets.css' );
			}
		}
		if ( ! wp_style_is( 'elegant-icon', 'registered' ) ) {
			wp_register_style( 'elegant-icon', BBPC_VEND . 'elegant-icon/style.css' );
		}
		wp_enqueue_style( 'bbpc-el-widgets' );
		wp_enqueue_style( 'elegant-icon' );

		$ppp       = isset( $attributes['ppp'] ) ? $attributes['ppp'] : 5;
		$order     = isset( $attributes['order'] ) ? $attributes['order'] : 'ASC';
		$show_meta = isset( $attributes['show_meta'] ) ? $attributes['show_meta'] : true;
		
		$title_color = isset( $attributes['title_color'] ) ? $attributes['title_color'] : '';
		$meta_color  = isset( $attributes['meta_color'] ) ? $attributes['meta_color'] : '';
		$bg_color    = isset( $attributes['bg_color'] ) ? $attributes['bg_color'] : '';

		$unique_id = uniqid( 'bbpc-forum-posts-' );

		$forum_posts = new WP_Query( array(
			'post_type'      => 'topic',
			'posts_per_page' => $ppp,
			'order'          => $order,
		) );

		$wrapper_attributes = get_block_wrapper_attributes( [
			'class' => 'community-posts-wrapper',
			'id'    => $unique_id,
		] );

		ob_start();
		?>
		<style>
			<?php if ( $title_color ) : ?>
				#<?php echo esc_attr( $unique_id ); ?> .community-post .post-content .post-title a { color: <?php echo esc_attr( $title_color ); ?>; }
			<?php endif; ?>
			<?php if ( $meta_color ) : ?>
				#<?php echo esc_attr( $unique_id ); ?> .community-post .post-content .entry-content,
				#<?php echo esc_attr( $unique_id ); ?> .community-post .post-meta-wrapper .post-meta-info li a { color: <?php echo esc_attr( $meta_color ); ?>; }
			<?php endif; ?>
			<?php if ( $bg_color ) : ?>
				#<?php echo esc_attr( $unique_id ); ?> .community-post { background-color: <?php echo esc_attr( $bg_color ); ?>; }
			<?php endif; ?>
		</style>
		<div <?php echo $wrapper_attributes; ?>>
			<?php
			while ( $forum_posts->have_posts() ) : $forum_posts->the_post();
				$favoriters     = bbp_get_topic_favoriters();
				$favorite_count = ! empty( $favoriters ) ? $favoriters[0] : '0';
				?>
				<div class="community-post wow fadeInUp" data-wow-delay="0.5s">
					<div class="post-content">
						<div class="author-avatar">
							<?php
							echo wp_kses_post( bbp_get_topic_author_link(
								array(
									'post_id' 	=> get_the_ID(),
									'size' 		=> 40,
									'type' 		=> 'avatar'
								)
							) );
							?>
						</div>
						<div class="entry-content">
							<h3 class="post-title">
								<a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a>
							</h3>

							<?php
							esc_html_e( 'Last active: ', 'bbp-core' );
							echo esc_html( bbp_get_forum_last_active_time( get_the_ID() ) );
							?>

						</div>
					</div>
					<?php if ( $show_meta ) : ?>
					<div class="post-meta-wrapper">
						<ul class="post-meta-info">
							<li>
								<a href="<?php bbp_topic_permalink(); ?>">
									<i class="icon_chat_alt"></i>
									<?php bbp_show_lead_topic() ? bbp_topic_reply_count( get_the_ID() ) : bbp_topic_post_count( get_the_ID() ); ?>
								</a>
							</li>
							<li>
								<a href="<?php bbp_topic_permalink(); ?>">
									<i class="icon_star_alt"></i> <?php echo esc_html( $favorite_count ); ?>
								</a>
							</li>
						</ul>
					</div>
					<?php endif; ?>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_forum_tab( $attributes, $content ) {
		$forum_tab_title = ! empty( $attributes['forum_tab_title'] ) ? $attributes['forum_tab_title'] : __( 'Show Forum', 'bbp-core' );
		$topics_tab_title = ! empty( $attributes['topics_tab_title'] ) ? $attributes['topics_tab_title'] : __( 'Show Topics', 'bbp-core' );
		$ppp = ! empty( $attributes['ppp'] ) ? $attributes['ppp'] : 9;
		$ppp2 = ! empty( $attributes['ppp2'] ) ? $attributes['ppp2'] : 6;
		$order = ! empty( $attributes['order'] ) ? $attributes['order'] : 'ASC';
		$order2 = ! empty( $attributes['order2'] ) ? $attributes['order2'] : 'ASC';
		$settings = $attributes; // Used in template

		// Inline Styles
		if( ! empty( $attributes['forum_tab_title_color'] ) ) {
			$unique_id = isset($attributes['uniqueId']) ? $attributes['uniqueId'] : uniqid(); // We don't have uniqueId attribute yet but good practice
			// We need a unique ID for the style, let's use the one generated for the tab
			// But for now, we will rely on a generated unique ID in the template or wrapper
		}

		$forums = new WP_Query( array(
			'post_type'      => 'forum',
			'posts_per_page' => $ppp,
			'order'          => $order,
		) );

		$topics = new WP_Query( array(
			'post_type'      => 'topic',
			'posts_per_page' => $ppp2,
			'order'          => $order2,
		) );
		
		$unique_id = uniqid(); 
		$this_get_id = $unique_id;

		// Enqueue styles/scripts for forum tab (ensure script is available on frontend)
		if ( ! wp_style_is( 'bbpc-el-widgets', 'registered' ) ) {
			wp_register_style( 'bbpc-el-widgets', BBPC_FRONT_ASS . 'css/el-widgets.css' );
		}
		if ( ! wp_style_is( 'elegant-icon', 'registered' ) ) {
			wp_register_style( 'elegant-icon', BBPC_VEND . 'elegant-icon/style.css' );
		}
		if ( ! wp_script_is( 'bbpc_js', 'registered' ) ) {
			wp_register_script( 'bbpc_js', BBPC_FRONT_ASS . 'js/forumTab.js', array( 'jquery' ), BBPC_VERSION, true );
		}

		wp_enqueue_style( 'bbpc-el-widgets' );
		wp_enqueue_style( 'elegant-icon' );
		wp_enqueue_script( 'bbpc_js' );

		ob_start();
		?>
		<section class="community-area" id="forumTab-<?php echo esc_attr( $this_get_id ); ?>">
			<ul class="nav nav-tabs tab-buttons" role="tablist">
				<li class="nav-item" role="presentation">
				<button class="nav-link active tab-button tab" onclick="forumTab(event, 'forumTab-<?php echo esc_attr( $this_get_id ); ?>', 'forum-<?php echo esc_attr( $this_get_id ); ?>')">
					<?php echo esc_html( $forum_tab_title ); ?>
				</button>
		
				</li>
				<li class="nav-item" role="presentation">
				<button class="nav-link tab-button tab" onclick="forumTab(event, 'forumTab-<?php echo esc_attr( $this_get_id ); ?>', 'topics-<?php echo esc_attr( $this_get_id ); ?>')">
					<?php echo esc_html( $topics_tab_title ); ?>
				</button>
				</li>
			</ul>
			<div id="forum-<?php echo esc_attr( $this_get_id ); ?>" class="tab-content show active">
				<div class="gy-4 bbpc-community-topic-widget-main-wrapper">
					<?php
					while ( $forums->have_posts() ) : $forums->the_post();
						$item_id   = get_the_ID();
						$author_id = get_post_field( 'post_author', $item_id );
						?> 
						<div class="col-md-6 col-lg-4 bbpc-community-topic-widget-wrapper">
							<div class="community-topic-widget-box">
								<?php the_post_thumbnail( 'full' ); ?>
								<div class="box-content">
									<h5>
										<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
									</h5>
									<span><?php bbp_forum_topic_count( $item_id ); esc_html_e( ' Posts', 'bbp-core' ); ?></span>
									<span class="vr-line">|</span>
									<span><?php bbp_forum_reply_count( $item_id ); esc_html_e( ' Replies', 'bbp-core' ); ?></span>
								</div>
							</div>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
		
				<?php if ( isset($settings['is_forum_tab_btn']) && $settings['is_forum_tab_btn'] ) : ?>
					<div class="text-center bbpc-show-more-btn-wrapper">
						<a href="<?php echo esc_url( $settings['more_url'] ?? '#' ); ?>" class="dbl-arrow-upper show-more-btn show-more-round mt-70">
							<div class="arrow-cont">
								<svg width="13px" height="13px" class="first" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585"></polygon> </g> </g></svg>
								<svg width="13px" height="13px" class="second" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585"></polygon> </g> </g></svg>
							</div>
							<?php echo esc_html( $settings['more_txt'] ?? '' ); ?>
						</a>
					</div>
				<?php endif; ?>
		
			</div>
		
			<div id="topics-<?php echo esc_attr( $this_get_id ); ?>" class="tab-content ">
				<?php
				$i = 0;
				while ( $topics->have_posts() ) : $topics->the_post();
					$topic_id   = $topics->posts[ $i ]->ID;
					$vote_count = get_post_meta( $topic_id, "bbpv-votes", true );
					$forum_id   = bbp_get_topic_forum_id();
					?>
					<div class="single-forum-post-widget">
						<div class="post-content">
							<div class="post-title">
								<h6> <a href="<?php the_permalink(); ?>"> <?php the_title() ?> </a> </h6>
							</div>
							<div class="post-info">
								<div class="author">
									<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/user-circle-alt.svg' ) ?>" alt="<?php esc_attr_e( 'User circle', 'bbp-core' );
									?>">
									<?php 
									echo wp_kses_post( bbp_get_topic_author_link( 
										array( 
											'post_id' 	=> $topic_id, 
											'type' 		=> 'name' 
										)
									) );
									?>
								</div>
		
								<div class="post-time">
									<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/time-outline.svg' ) ?>" alt="<?php esc_attr_e( 'Time outline', 'bbp-core' );
									?>">
									<?php bbp_forum_last_active_time( get_the_ID() ); ?>
								</div>
		
								<div class="post-category">
									<a href="<?php echo esc_url( get_the_permalink( $forum_id ) ) ?>">
										<?php echo get_the_post_thumbnail( $forum_id ); ?>
										<?php echo esc_html( get_the_title( $forum_id ) ) ?>
									</a>
								</div>
							</div>
							<?php
							bbp_topic_tag_list( '',
								array(
									'before' => '<div class="post-tags">',
									'after'  => '</div>',
									'sep'    => ''
								)
							);
							?>
						</div>
						<div class="post-reach">
							<div class="post-view">
								<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/eye-outline.svg' ) ?>" alt="<?php esc_attr_e( 'View icon', 'bbp-core' ); ?>">
								<?php bbp_topic_view_count( $topic_id );
								esc_html_e( ' Views', 'bbp-core' ); ?>
							</div>
							<div class="post-like">
								<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/thumbs-up-outline.svg' ) ?>" alt="<?php esc_attr_e( 'Like icon', 'bbp-core' ); ?>">
								<?php
								if ( $vote_count ) {
									echo esc_html( $vote_count );
								} else {
									echo "0";
								}
								esc_html_e( ' Likes', 'bbp-core' ); ?>
							</div>
							<div class="post-comment">
								<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/chatbubbles-outline.svg' ) ?>" alt="<?php esc_attr_e( 'Comment icon', 'bbp-core' ); ?>">
								<?php
								bbp_topic_reply_count( $topic_id );
								esc_html_e( ' Replies', 'bbp-core' )
								?>
							</div>
						</div>
					</div>
					<?php
					$i ++;
				endwhile;
				unset( $i );
				wp_reset_postdata();
				?>
		
				<?php if ( isset($settings['is_topic_tab_btn']) && $settings['is_topic_tab_btn'] ) : ?>
					<div class="row">
						<div class="text-center bbpc-show-more-btn-wrapper">
							<a href="<?php echo esc_url( $settings['more_url2'] ?? '#' ); ?>" class="dbl-arrow-upper show-more-btn show-more-round mt-70">
								<div class="arrow-cont">
									<svg width="13px" height="13px" class="first" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48H0z" fill="none"></path><polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585"></polygon> </g> </g></svg>
									<svg width="13px" height="13px" class="second" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"> <path d="M0 0h48v48H0z" fill="none"></path><polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585"></polygon> </g> </g> </svg>
								</div>
								<?php echo esc_html( $settings['more_txt2'] ?? '' ); ?>
							</a>
						</div>
					</div>
				<?php endif; ?>
		
			</div>
		</section>
		<?php
		// Sanitize color values (allow hex and rgba formats)
		$sanitize_css_color = function( $color ) {
			$color = trim( (string) $color );
			if ( empty( $color ) ) {
				return '';
			}

			// Allow hex colors like #fff or #ffffff
			if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
				return $color;
			}

			// Allow rgb() or rgba() formats
			if ( preg_match( '/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/', $color ) ) {
				return $color;
			}

			// Fallback: empty (reject any other values)
			return '';
		};

		$forum_tab_title_color    = isset( $attributes['forum_tab_title_color'] ) ? $sanitize_css_color( $attributes['forum_tab_title_color'] ) : '';
		$topics_tab_title_color   = isset( $attributes['topics_tab_title_color'] ) ? $sanitize_css_color( $attributes['topics_tab_title_color'] ) : '';
		$forum_tab_content_color  = isset( $attributes['forum_tab_content_color'] ) ? $sanitize_css_color( $attributes['forum_tab_content_color'] ) : '';
		$topics_tab_content_color = isset( $attributes['topics_tab_content_color'] ) ? $sanitize_css_color( $attributes['topics_tab_content_color'] ) : '';

		if ( $forum_tab_title_color || $topics_tab_title_color || $forum_tab_content_color || $topics_tab_content_color ) :
		?>
		<style>
			<?php if ( $forum_tab_title_color ) : ?>
			#forumTab-<?php echo esc_attr( $this_get_id ); ?> .nav-tabs .nav-item:first-child button { color: <?php echo esc_attr( $forum_tab_title_color ); ?>; }
			<?php endif; ?>

			<?php if ( $topics_tab_title_color ) : ?>
			#forumTab-<?php echo esc_attr( $this_get_id ); ?> .nav-tabs .nav-item:nth-child(2) button { color: <?php echo esc_attr( $topics_tab_title_color ); ?>; }
			<?php endif; ?>

			<?php if ( $forum_tab_content_color ) : ?>
			#forum-<?php echo esc_attr( $this_get_id ); ?> .community-topic-widget-box .box-content,
			#forum-<?php echo esc_attr( $this_get_id ); ?> .community-topic-widget-box .box-content a { color: <?php echo esc_attr( $forum_tab_content_color ); ?>; }
			<?php endif; ?>

			<?php if ( $topics_tab_content_color ) : ?>
			#topics-<?php echo esc_attr( $this_get_id ); ?> .single-forum-post-widget .post-content,
			#topics-<?php echo esc_attr( $this_get_id ); ?> .single-forum-post-widget .post-content a { color: <?php echo esc_attr( $topics_tab_content_color ); ?>; }
			<?php endif; ?>
		</style>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	public function render_forums( $attributes, $content ) {
		if ( ! wp_script_is( 'bbpc-ajax', 'registered' ) ) {
			wp_register_script( 'bbpc-ajax', BBPC_FRONT_ASS . 'js/ajax.js', array( 'jquery' ), BBPC_VERSION, true );
		}
		if ( ! wp_style_is( 'bbpc-el-widgets', 'registered' ) ) {
			wp_register_style( 'bbpc-el-widgets', BBPC_FRONT_ASS . 'css/el-widgets.css');
		}

		wp_enqueue_script( 'bbpc-ajax' );
		wp_enqueue_style( 'bbpc-el-widgets' );

		$ppp = ! empty( $attributes['ppp'] ) ? $attributes['ppp'] : 5;
		$ppp2 = ! empty( $attributes['ppp2'] ) ? $attributes['ppp2'] : 10;
		$order = ! empty( $attributes['order'] ) ? $attributes['order'] : 'ASC';
		$more_txt = ! empty( $attributes['more_txt'] ) ? $attributes['more_txt'] : __( 'More Communities', 'bbp-core' );
		$unique_id = uniqid( 'bbpc-forums-' );

		$forums = new WP_Query( array(
			'post_type'      	=> 'forum',
			'posts_per_page' 	=> $ppp,
			'order'          	=> $order,
		) );

		ob_start();
		?>
		<div id="<?php echo esc_attr( $unique_id ); ?>">
			<div class="communities-boxes">
				<?php
				while ( $forums->have_posts() ) : $forums->the_post();
					?>
					<div class="com-box wow fadeInRight" data-wow-delay="0.5s">
						<div class="icon-container">
							<?php the_post_thumbnail( 'full' ); ?>
						</div>
						<div class="com-box-content">
							<h3 class="title">
								<a href="<?php the_permalink(); ?>"> <?php the_title() ?> </a>
							</h3>
							<p class="total-post"> <?php bbp_forum_topic_count( get_the_ID() ); ?> <?php esc_html_e( ' Posts', 'bbp-core' ) ?> </p>
						</div>
						<!-- /.ama-com-box-content -->
					</div>
					<!-- /.ama-com-box -->
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<!-- /.communities-boxes -->
	
			<div class="more-communities" data_id="<?php echo esc_attr( $unique_id );?>">
	
				<a href="#more-category" class="collapse-btn-wrap">
					<?php echo esc_html( $more_txt ); ?>
					 
					<svg fill="#000000" width="16px" height="16px" viewBox="0 0 24 24" id="minus" data-name="Line Color" xmlns="http://www.w3.org/2000/svg" class="icon line-color icon_minus"> <line id="primary" x1="19" y1="12" x2="5" y2="12" style="fill: none;  stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></line> </svg>
					
					<svg fill="#000000" width="16px" height="16px" viewBox="0 0 24 24" id="plus" data-name="Line Color" xmlns="http://www.w3.org/2000/svg" class="icon line-color icon_plus"><path id="secondary" d="M5,12H19M12,5V19" style="fill: none; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path></svg>
				</a>
	
				<div class="collapse-wrap" id="more-category" data_id="<?php echo esc_attr( $unique_id );?>">
					<div class="communities-boxes">
						<?php
						$forums2 = new WP_Query( array(
							'post_type'      => 'forum',
							'posts_per_page' => $ppp2,
							'offset'         => $ppp,
							'order'          => $order,
						) );
						while ( $forums2->have_posts() ) : $forums2->the_post();
							?>
							<div class="com-box">
								<div class="icon-container">
									<?php the_post_thumbnail( 'full' ); ?>
								</div>
								<div class="com-box-content">
									<h3 class="title">
										<a href="<?php the_permalink(); ?>"> <?php the_title() ?> </a>
									</h3>
									<p class="total-post"> <?php bbp_forum_topic_count( get_the_ID() ); ?> <?php esc_html_e( ' Posts', 'bbp-core' ); ?> </p>
								</div>
								<!-- /.ama-com-box-content -->
							</div>
							<!-- /.ama-com-box -->
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</div>
					<!-- /.communities-boxes -->
				</div>
				<!-- /.collapse-wrap -->
			</div>
		</div>
		<!-- /.more-communities -->
		<?php if( ! empty( $attributes['more_text_color'] ) || ! empty( $attributes['title_color'] ) || ! empty( $attributes['content_color'] ) ): ?>
		<style>
			<?php if( ! empty( $attributes['more_text_color'] ) ): ?>
			#<?php echo esc_attr( $unique_id ); ?> .collapse-btn-wrap {
				color: <?php echo esc_attr( $attributes['more_text_color'] ); ?>;
			}
			#<?php echo esc_attr( $unique_id ); ?> .more-communities .collapse-btn-wrap svg path {
				stroke: <?php echo esc_attr( $attributes['more_text_color'] ); ?>;
			}
			#<?php echo esc_attr( $unique_id ); ?> .more-communities .collapse-btn-wrap svg line {
				stroke: <?php echo esc_attr( $attributes['more_text_color'] ); ?>;
			}
			<?php endif; ?>

			<?php if( ! empty( $attributes['title_color'] ) ): ?>
			#<?php echo esc_attr( $unique_id ); ?> .com-box-content .title a {
				color: <?php echo esc_attr( $attributes['title_color'] ); ?>;
			}
			<?php endif; ?>

			<?php if( ! empty( $attributes['content_color'] ) ): ?>
			#<?php echo esc_attr( $unique_id ); ?> .com-box-content .total-post {
				color: <?php echo esc_attr( $attributes['content_color'] ); ?>;
			}
			<?php endif; ?>
		</style>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

	public function render_search( $attributes, $content ) {
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : 'Search for Topics....';
		$submit_btn_type = isset( $attributes['submit_btn_type'] ) ? $attributes['submit_btn_type'] : 'icon';
		$submit_btn_text = isset( $attributes['submit_btn_text'] ) ? $attributes['submit_btn_text'] : __( 'Search', 'bbp-core' );
		$submit_btn_align = isset( $attributes['submit_btn_align'] ) ? $attributes['submit_btn_align'] : 'left';
		$submit_btn_icon = isset( $attributes['submit_btn_icon'] ) ? $attributes['submit_btn_icon'] : [ 'value' => 'fas fa-search', 'library' => 'solid' ];
		$is_keywords = isset( $attributes['is_keywords'] ) ? $attributes['is_keywords'] : true;
		$keywords_label = isset( $attributes['keywords_label'] ) ? $attributes['keywords_label'] : 'Popular:';
		$keywords_align = isset( $attributes['keywords_align'] ) ? $attributes['keywords_align'] : 'center';
		$keywords = isset( $attributes['keywords'] ) ? $attributes['keywords'] : [ [ 'title' => 'Keyword #1' ], [ 'title' => 'Keyword #2' ] ];
		
		$unique_id = uniqid( 'bbpc-search-' );

		$cross_position = $submit_btn_align == 'right' ? 'left' : 'right';

		// Include dependencies
		if ( ! wp_style_is( 'bbpc-el-widgets', 'registered' ) ) {
			wp_register_style( 'bbpc-el-widgets', BBPC_FRONT_ASS . 'css/el-widgets.css' );
		}
		wp_enqueue_style( 'bbpc-el-widgets' );

		wp_enqueue_script( 'bbpc-frontend-js', BBPC_FRONT_ASS . 'js/frontend.js', array( 'jquery' ), BBPC_VERSION, true );

		$wrapper_attributes = get_block_wrapper_attributes( [
			'id'    => $unique_id,
		] );

		ob_start();
		?>
		<div <?php echo $wrapper_attributes; ?>>
			<div class="bbpc-search-overlay"></div>
			
			<form action="<?php echo esc_url( home_url( '/' ) ) ?>" role="search" method="get" class="bbpc_search_form_wrapper">
				<div class="form-group">
					<div class="input-wrapper <?php echo esc_attr( $cross_position ); ?>">
						
						<span class="submit-btn-<?php echo esc_attr( $submit_btn_align ); ?>">
							<button type="submit">			
								<?php
								if ( $submit_btn_type == 'icon' ) {
									// Simple render icon function replacement or manual render
									if( is_array( $submit_btn_icon ) && ! empty( $submit_btn_icon['value'] ) ) {
										echo '<i class="' . esc_attr( $submit_btn_icon['value'] ) . '"></i>';
									} else {
										echo '<i class="fas fa-search"></i>';
									}
								} else {
									echo esc_html( $submit_btn_text );
								}
								?>
							</button>						 
						</span>
	
						<input type='search' id="searchInput" autocomplete="off" name="s" placeholder="<?php echo esc_attr( $placeholder ) ?>">
	
						<!-- Ajax Search Loading Spinner -->
						<div class="spinner">
							<div class="bounce1"></div>
							<div class="bounce2"></div>
							<div class="bounce3"></div>
						</div>
	
						<!-- WPML Language Code -->
						<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
							<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>"/>
						<?php endif; ?>
	
						<input type="hidden" id="hidden_post_type" name="post_type" value="docs"/>
					</div>
				</div>
				<div id="datafetch"></div>
				
				<?php if ( $is_keywords ) : ?>
					<?php if ( class_exists( 'BBPCorePro' ) ) : ?>
						<div class="bbpc-search-keyword">
							<div class="bbpc-keywords-wrapper" style="text-align: <?php echo esc_attr( $keywords_align ); ?>">
								<?php if ( ! empty( $keywords_label ) ) : ?>
									<span class="bbpc-search-keywords-label"><?php echo esc_html( $keywords_label ); ?></span>
								<?php endif; ?>
								<ul>
									<?php foreach ( $keywords as $keyword ) : ?>
										<li><a href="<?php echo esc_url( home_url( '/?s=' . $keyword['title'] . '&post_type=topic' ) ); ?>"> <?php echo esc_html( $keyword['title'] ); ?> </a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php else : ?>
						<?php 
						// Show only in Editor (via ServerSideRender or admin page)
						if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_admin() ) :
							$upsell_img = BBPC_FRONT_ASS . 'img/upsell-search-keywords.png';
							$upgrade_url = admin_url( 'admin.php?page=bbp-core-pricing' );
							?>
							<div class="bbpc-search-keyword-upsell">
								<img src="<?php echo esc_url( $upsell_img ); ?>" alt="<?php esc_attr_e( 'Upgrade to Pro', 'bbp-core' ); ?>">
								<div class="upsell-btn-wrapper bbpc-upgrade-btn">
									<a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" rel="noopener noreferrer">
										<?php esc_html_e( 'Upgrade to Pro', 'bbp-core' ); ?>
                                    </a>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			</form>
		</div>

		<style>
			<?php if( ! empty( $attributes['color_text'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc_search_form_wrapper .input-wrapper input { color: <?php echo esc_attr( $attributes['color_text'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['color_placeholder'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc_search_form_wrapper .input-wrapper input::placeholder { color: <?php echo esc_attr( $attributes['color_placeholder'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['input_bg_color'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc_search_form_wrapper .input-wrapper input { background-color: <?php echo esc_attr( $attributes['input_bg_color'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['color_icon'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc_search_form_wrapper .input-wrapper button { color: <?php echo esc_attr( $attributes['color_icon'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['search_bg'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc_search_form_wrapper .input-wrapper button { background: <?php echo esc_attr( $attributes['search_bg'] ); ?>; }
			<?php endif; ?>

			/* Keywords Styles */
			<?php if( ! empty( $attributes['color_keywords_label'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc-search-keyword span.bbpc-search-keywords-label { color: <?php echo esc_attr( $attributes['color_keywords_label'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['bbpc_color_keywords'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc-search-keyword ul li a { color: <?php echo esc_attr( $attributes['bbpc_color_keywords'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['bbpc_color_keywords_bg'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc-search-keyword ul li a { background: <?php echo esc_attr( $attributes['bbpc_color_keywords_bg'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['bbpc_color_keywords_hover'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc-search-keyword ul li a:hover { color: <?php echo esc_attr( $attributes['bbpc_color_keywords_hover'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['bbpc_color_keywords_bg_hover'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .bbpc-search-keyword ul li a:hover { background: <?php echo esc_attr( $attributes['bbpc_color_keywords_bg_hover'] ); ?>; }
			<?php endif; ?>
		</style>
		<?php
		return ob_get_clean();
	}

	public function render_single_forum( $attributes, $content ) {
		$settings = $attributes;
		$settings['style'] = !empty($attributes['style']) ? $attributes['style'] : '1';
		$settings['ppp'] = !empty($attributes['ppp']) ? $attributes['ppp'] : 3;
		$settings['order'] = !empty($attributes['order']) ? $attributes['order'] : 'ASC';
		$settings['word_length'] = !empty($attributes['word_length']) ? $attributes['word_length'] : 12;
		$settings['read_more'] = !empty($attributes['read_more']) ? $attributes['read_more'] : 'View All';

		$forum_id = !empty($settings[ 'forum_id' ]) ? $settings[ 'forum_id' ] : '';
		
		$cover_image = isset($settings[ 'cover_image' ]) ? $settings[ 'cover_image' ] : '';
		$url = '';
		if ( is_array($cover_image) && isset($cover_image['url']) ) {
			$url = $cover_image['url'];
		} elseif ( is_object($cover_image) && isset($cover_image->url) ) {
			$url = $cover_image->url;
		}

		$post_thumbnail_url = !empty($url) ? $url : get_the_post_thumbnail_url($forum_id);

		$topics = new \WP_Query(array(
			'post_type' => bbp_get_topic_post_type(),
			'order' => !empty($settings['order']) ? $settings['order'] : 'DESC',
			'posts_per_page' => !empty($settings['ppp']) ? $settings['ppp'] : 3,
			'post_parent' => $forum_id,
		));

		$unique_id = uniqid('bbpc-single-forum-');

		if ( ! wp_style_is( 'bbpc-el-widgets', 'registered' ) ) {
			wp_register_style( 'bbpc-el-widgets', BBPC_FRONT_ASS . 'css/el-widgets.css' );
		}
		wp_enqueue_style( 'bbpc-el-widgets' );
		
		ob_start();
		
		echo '<div id="' . esc_attr($unique_id) . '">';

		if ($forum_id) {
			$template_path = __DIR__ . '/../Elementor/inc/single-forum/single-forum-' . $settings['style'] . '.php';
			if ( file_exists( $template_path ) ) {
				include $template_path;
			}
		} else {
			 ?>
			<div class="alert alert-warning" role="alert">
				<?php esc_html_e('Please select a forum.', 'bbp-core'); ?>
			</div>
			<?php
		}
		
		echo '</div>';

		?>
		<style>
			<?php if( ! empty( $attributes['title_color'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .forum-with-topics .topic-table .topic-contents .title h3,
				#<?php echo esc_attr( $unique_id ); ?> .forum-card .card-title h3 { color: <?php echo esc_attr( $attributes['title_color'] ); ?>; }
			<?php endif; ?>
			<?php if( ! empty( $attributes['excerpt_color'] ) ): ?>
				#<?php echo esc_attr( $unique_id ); ?> .forum-with-topics .topic-table .topic-contents .title p,
				#<?php echo esc_attr( $unique_id ); ?> .forum-card .card-body { color: <?php echo esc_attr( $attributes['excerpt_color'] ); ?>; }
			<?php endif; ?>
		</style>
		<?php

		return ob_get_clean();
	}


	public function render_forum_ajax( $attributes, $content ) {
		// Upsell logic for frontend when Pro is not active
		if ( ! class_exists( 'BBPCorePro' ) ) {
			$upsell_img = defined( 'BBPC_IMG' ) ? BBPC_IMG . 'upsell-forum-ajax.png' : '';
			$upgrade_url = admin_url( 'admin.php?page=bbp-core-pricing' );
			
			ob_start();
			?>
			<style>
				.bbpc-upsell-container {
					position: relative;
					display: flex;
					justify-content: center;
					align-items: center;
					min-height: 300px;
					background: #f5f5f5;
					border: 1px dashed #ccc;
					overflow: hidden;
					border-radius: 8px;
					margin: 20px 0;
				}
				.bbpc-upsell-container img {
					max-width: 100%;
					height: auto;
					display: block;
					opacity: 0.5;
				}
				.bbpc-upsell-btn-wrapper {
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					z-index: 10;
				}
				.bbpc-upgrade-btn-frontend {
					background: linear-gradient(90deg, #172473 0%, #0fa2d0 100%) !important;
					border: none !important;
					color: #fff !important;
					padding: 15px 20px !important;
					font-size: 18px !important;
					font-weight: 500 !important;
					border-radius: 4px !important;
					text-decoration: none !important;
					box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
					transition: all 0.3s ease !important;
					display: inline-block !important;
					line-height: 1 !important;
				}
				.bbpc-upgrade-btn-frontend:hover {
					transform: translate(-2px, -2px);
					box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3) !important;
					color: #fff !important;
					background: linear-gradient(80deg, #0fa2d0 0%, #172473 100%) !important;
				}
			</style>
			<div class="bbpc-upsell-container">
				<?php if ( $upsell_img ) : ?>
					<img src="<?php echo esc_url( $upsell_img ); ?>" alt="<?php esc_attr_e( 'Upgrade to Pro', 'bbp-core' ); ?>">
				<?php endif; ?>
				<div class="bbpc-upsell-btn-wrapper">
					<a href="<?php echo esc_url( $upgrade_url ); ?>" class="bbpc-upgrade-btn-frontend">
						<?php esc_html_e( 'Upgrade to Pro', 'bbp-core' ); ?>
					</a>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

		try {
			if ( ! wp_style_is( 'bbpc-el-widgets', 'registered' ) ) {
				if ( defined( 'BBPC_FRONT_ASS' ) ) {
					wp_register_style( 'bbpc-el-widgets', BBPC_FRONT_ASS . 'css/el-widgets.css' );
				}
			}

			wp_enqueue_style( 'bbpc-el-widgets' );

			$ppp2        = isset( $attributes['ppp2'] ) ? $attributes['ppp2'] : 9;
			$order       = isset( $attributes['order'] ) ? $attributes['order'] : 'ASC';
			$filter_btns = isset( $attributes['filter_btns'] ) ? $attributes['filter_btns'] : true;

			// Styling attributes
			$forum_title_color        = isset( $attributes['forum_title_color'] ) ? $attributes['forum_title_color'] : '';
			$forum_title_hover_color  = isset( $attributes['forum_title_hover_color'] ) ? $attributes['forum_title_hover_color'] : '';
			$forum_meta_color         = isset( $attributes['forum_meta_color'] ) ? $attributes['forum_meta_color'] : '';
			$parent_forum_color       = isset( $attributes['parent_forum_color'] ) ? $attributes['parent_forum_color'] : '';
			$parent_forum_color_hover = isset( $attributes['parent_forum_color_hover'] ) ? $attributes['parent_forum_color_hover'] : '';

			// Sanitize color values (allow hex and rgba formats)
			$sanitize_css_color = function( $color ) {
				$color = trim( (string) $color );
				if ( empty( $color ) ) {
					return '';
				}
				if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
					return $color;
				}
				if ( preg_match( '/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+(?:\s*,\s*(0|1|0?\.\d+))?\s*\)$/', $color ) ) {
					return $color;
				}
				return '';
			};

			$forum_title_color        = $sanitize_css_color( $forum_title_color );
			$forum_title_hover_color  = $sanitize_css_color( $forum_title_hover_color );
			$forum_meta_color         = $sanitize_css_color( $forum_meta_color );
			$parent_forum_color       = $sanitize_css_color( $parent_forum_color );
			$parent_forum_color_hover = $sanitize_css_color( $parent_forum_color_hover );

			$unique_id = uniqid( 'bbpc-forum-' );

			// Check constants
			if ( ! defined( 'BBPC_VEND' ) || ! defined( 'BBPC_FRONT_ASS' ) || ! defined( 'BBPC_VERSION' ) ) {
				return 'Constants missing';
			} else {
				// Enqueue styles/scripts
				if ( ! wp_style_is( 'elegant-icon', 'registered' ) ) {
					wp_register_style( 'elegant-icon', BBPC_VEND . '/elegant-icon/style.css' );
				}
				if ( ! wp_script_is( 'bbpc-ajax', 'registered' ) ) {
					// Use Free path
					wp_register_script( 'bbpc-ajax', BBPC_FRONT_ASS . 'js/ajax.js', array( 'jquery' ), BBPC_VERSION, true );
				}

				wp_enqueue_style( 'elegant-icon' );
				wp_enqueue_script( 'bbpc-ajax' );
			}
			
			// Inline Styles
			$style = "<style>";
			if ( $forum_title_color ) {
				$style .= "#{$unique_id} .single-forum-post-widget .post-title a { color: {$forum_title_color}; }";
			}
			if ( $forum_title_hover_color ) {
				$style .= "#{$unique_id} .single-forum-post-widget .post-title a:hover { color: {$forum_title_hover_color}; }";
			}
			if ( $forum_meta_color ) {
				$style .= "#{$unique_id} .single-forum-post-widget .post-info .author, #{$unique_id} .single-forum-post-widget .post-info .post-time { color: {$forum_meta_color}; }";
			}
			if ( $parent_forum_color ) {
				$style .= "#{$unique_id} .post-content .post-category a { color: {$parent_forum_color}; }";
			}
			if ( $parent_forum_color_hover ) {
				$style .= "#{$unique_id} .post-content .post-category a:hover { color: {$parent_forum_color_hover}; }";
			}
			$style .= "</style>";

			$topics = new WP_Query( array(
				'post_type'      => 'topic',
				'posts_per_page' => $ppp2,
				'order'          => $order,
			) );

			$wrapper_attributes = get_block_wrapper_attributes( [
				'class' => 'forum-post-widget',
				'id'    => $unique_id,
			] );

			ob_start();
			echo $style;
			?>

			<div <?php echo $wrapper_attributes; ?> data_id="<?php echo esc_attr( $unique_id ); ?>">

				<?php if ( $filter_btns ) : ?>
					<div class="post-filter-widget mb-20">
						<div class="single-filter-item">
							<a href="#" id="all_filt" data-forum="all" class="data-active">
								<i class="icon_grid-2x2"></i><?php esc_html_e( 'All', 'bbp-core' ) ?>
							</a>
						</div>
						<div class="single-filter-item">
							<a href="#" id="populer_filt" data-forum="popular">
								<i class="icon_easel"></i><?php esc_html_e( 'Popular', 'bbp-core' ) ?>
							</a>
						</div>
						<div class="single-filter-item">
							<a href="#" id="featured_filt" data-forum="featured">
								<i class="icon_ribbon_alt"></i><?php esc_html_e( 'Featured', 'bbp-core' ) ?>
							</a>
						</div>
						<div class="single-filter-item">
							<a href="#" id="recent_filt" data-forum="recent">
								<i class="icon_clock_alt"></i><?php esc_html_e( 'Recent', 'bbp-core' ) ?>
							</a>
						</div>
						<div class="single-filter-item">
							<a href="#" id="unloved_filt" data-forum="unloved">
								<i class="icon_close_alt2"></i><?php esc_html_e( 'Unloved', 'bbp-core' ) ?>
							</a>
						</div>
						<div class="single-filter-item">
							<a href="#" id="loved_filt" data-forum="loved">
								<i class="icon_check_alt2"></i><?php esc_html_e( 'Loved', 'bbp-core' ) ?>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<div id="aj-post-filter-widget">
					<?php
					$i = 0;
					while ( $topics->have_posts() ) : $topics->the_post();
						$topic_id   = $topics->posts[ $i ]->ID;
						$vote_count = get_post_meta( $topic_id, "bbpv-votes", true );
						
						// Safe get forum ID
						$forum_id = function_exists('bbp_get_topic_forum_id') ? bbp_get_topic_forum_id() : 0;
						// Safe get forum title
						$forum_title = '';
						if ( function_exists('bbp_get_topic_forum_title') ) {
							$forum_title = bbp_get_topic_forum_title();
						} elseif ( function_exists('bbp_get_forum_title') ) {
							$forum_title = bbp_get_forum_title( $forum_id );
						}
						?>
						<div class="single-forum-post-widget">
							<div class="post-content">
								<div class="post-title">
									<h6><a href="<?php the_permalink(); ?>"> <?php the_title() ?> </a></h6>
								</div>
								<div class="post-info">
									<div class="author">
										<?php if ( defined('BBPC_IMG') ): ?>
										<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/user-circle-alt.svg' ); ?>" alt="<?php esc_attr_e( 'User circle alt icon', 'bbp-core' ); ?>">
										<?php endif; ?>
										<?php
										if ( function_exists('bbp_get_topic_author_link') ) {
											echo wp_kses_post( bbp_get_topic_author_link(
												array(
													'post_id' => $topic_id,
													'type'    => 'name'
												)
											) );
										}
										?>
									</div>

									<div class="post-time">
										<?php if ( defined('BBPC_IMG') ): ?>
										<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/time-outline.svg' ); ?>"
											alt="<?php esc_attr_e( 'Time outline icon', 'bbp-core' ); ?>">
										<?php endif; ?>
										<?php 
											// Use topic last active time, fallback to forum last active time, checking existence
											if ( function_exists('bbp_topic_last_active_time') ) {
												bbp_topic_last_active_time( get_the_ID() );
											} elseif ( function_exists('bbp_forum_last_active_time') ) {
												bbp_forum_last_active_time( get_the_ID() );
											}
										?>
									</div>
								</div>

								<div class="post-category">
									<a href="<?php echo esc_url( get_the_permalink( $forum_id ) ); ?>">
										<?php echo get_the_post_thumbnail( $forum_id ); ?>
										<?php echo esc_html( $forum_title ); ?>
									</a>
								</div>
							</div>
							<div class="post-reach">
								<div class="post-view">
									<?php if ( defined('BBPC_IMG') ): ?>
									<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/eye-outline.svg' ); ?>" alt="<?php esc_attr_e( 'Eye outline icon', 'bbp-core' ); ?>">
									<?php endif; ?>

									<?php
									if ( function_exists('bbp_topic_view_count') ) {
										bbp_topic_view_count( $topic_id );
									}
									echo '&nbsp;';
									esc_html_e( 'Views', 'bbp-core' );
									?>
								</div>
								<div class="post-like">
									<?php if ( defined('BBPC_IMG') ): ?>
									<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/thumbs-up-outline.svg' ); ?>"
										alt="<?php esc_attr_e( 'Thumbs-up outline icon', 'bbp-core' ); ?>">
									<?php endif; ?>

									<?php
									if ( $vote_count ) {
										echo esc_html( $vote_count );
									} else {
										echo "0";
									}

									echo '&nbsp;';
									esc_html_e( 'Likes', 'bbp-core' );
									?>
								</div>
								<div class="post-comment">
									<?php if ( defined('BBPC_IMG') ): ?>
									<img src="<?php echo esc_url( BBPC_IMG . 'forum_tab/chatbubbles-outline.svg' ); ?>"
										alt="<?php esc_attr_e( 'Chat bubbles icon', 'bbp-core' ); ?>">
									<?php endif; ?>
									<?php
									if ( function_exists('bbp_topic_reply_count') ) {
										bbp_topic_reply_count( $topic_id );
									}
									echo '&nbsp;';
									esc_html_e( 'Replies', 'bbp-core' );
									?>
								</div>
							</div>
						</div>
						<?php
						$i ++;
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<?php
			return ob_get_clean();
		} catch ( \Throwable $e ) {
			return '<div>Error rendering block: ' . esc_html( $e->getMessage() ) . '</div>';
		}
	}
}
