<?php

/**
 * All Search results
 */
add_action( 'wp_ajax_bbpc_search_data_fetch', 'bbpc_search_data_fetch' );
add_action( 'wp_ajax_nopriv_bbpc_search_data_fetch', 'bbpc_search_data_fetch' );
function bbpc_search_data_fetch() {
	
	$opt                = get_option( 'bbpc_opt' );
	$is_ajax_search_tab = $opt['is_ajax_search_tab'] ?? '';
	global $post;

	if ( class_exists( 'EazyDocs' ) || class_exists( 'bbPress' ) ) :
		
		// All results query
		$all_results = new WP_Query( array(
			'post_type'      => ['post', 'forum', 'topic', 'docs' ],
			's'              => $_POST['keyword'] ?? '',
		) );
		$all_results_count = $all_results->found_posts;
		if ( $all_results_count > 0 ) {
			$all_nsoresult = 'data-result=';
		} else {
			$all_nsoresult = 'data-noresult=';
		}

		// Docs query
		$docs_query = new WP_Query( array(
			'post_type'      => 'docs',
			's'              => $_POST['keyword'] ?? '',
		) );
		$docs_count = $docs_query->found_posts;
		if ( $docs_count > 0 ) {
			$docs_nsoresult = 'data-result=';
		} else {
			$docs_nsoresult = 'data-noresult=';
		}
		
		// Docs query
		$post_query = new WP_Query( array(
			'post_type'      => 'post',
			's'              => $_POST['keyword'] ?? '',
		) );
		$post_count = $post_query->found_posts;
		if ( $post_count > 0 ) {
			$post_nsoresult = 'data-result=';
		} else {
			$post_nsoresult = 'data-noresult=';
		}

		// Forum query
		$forum_query = new WP_Query( array(
			'post_type'      => ['forum', 'topic'],
			's'              => $_POST['keyword'] ?? '',
		) );
		$forum_count = $forum_query->found_posts;
		if ( $forum_count > 0 ) {
			$forum_nsoresult = 'data-result=';
		} else {
			$forum_nsoresult = 'data-noresult=';
		}

		?>
		<div class="searchbar-tabs">
			<button type="button" class="tab-item active all-active" <?php echo esc_attr( $all_nsoresult. 'No-Results-Found' ); ?> onclick="searchAllTab()">
				<?php esc_html_e( 'All', 'bbp-core' ); ?>
			</button>
			<?php 
			if ( class_exists( 'EazyDocs' ) ) : 
				?>
				<button type="button" id="search-docs" <?php echo esc_attr( $docs_nsoresult. 'No-Results-Found' ); ?> class="tab-item" onclick="searchDocTab()">
					<?php esc_html_e( 'Docs', 'bbp-core' ); ?>
				</button>
				<?php 
			endif; 
			?>
			<?php if ( class_exists( 'bbPress' ) ) : ?>
				<button type="button" id="search-forum" <?php echo esc_attr( $forum_nsoresult. 'No-Results-Found' ); ?> class="tab-item" onclick="searchForumTab()">
					<?php esc_html_e( 'Forum', 'bbp-core' ); ?>
				</button>
			<?php endif; ?>
			<button type="button" id="search-blog" <?php echo esc_attr( $post_nsoresult. 'No-Results-Found' ); ?> class="tab-item" onclick="searchBlogTab()">
				<?php esc_html_e( 'Blog', 'bbp-core' ); ?>
			</button>
		</div>
		<?php
	endif;	

	echo '<div class="bbpc-search-results-wrapper">';
	// Blog query
	$posts = new WP_Query( array(
		'post_type'      => 'post',
		's'              => $_POST['keyword'] ?? '',
		'posts_per_page' => 5,
	) );
	if ( $posts->have_posts() ) :
		echo '<div class="search-results-tab post show" id="search-blog-results">';

		echo '<h2 class="bbpc-search-title">' . esc_html__( 'Blog Posts', 'bbp-core' ) . '</h2>';
		while ( $posts->have_posts() ) : $posts->the_post();
			?>
			<div class="search-result-item">
				<a href="<?php the_permalink(); ?>" class="title">
					<?php the_title() ?>
				</a>
			</div>
			<?php
		endwhile;
		wp_reset_postdata();
	echo '</div>';	
	endif;
	
	// Docs query
    $posts = new WP_Query( [
            'post_type'             => 'docs',
            's'     				=> $_POST['keyword'] ?? '',
			'posts_per_page' 		=> 5,
        ]
    );
    if ( $posts->have_posts() ):
		echo '<div class="search-results-tab docs show" id="search-docs-results">';
		echo '<h2 class="bbpc-search-title">' . esc_html__( 'Documentation', 'bbp-core' ) . '</h2>';

        while ( $posts->have_posts() ) : $posts->the_post();
            ?>
            <div class="search-result-item" onclick="document.location='<?php echo get_the_permalink(get_the_ID()); ?>'">
                <a href="<?php echo get_the_permalink(get_the_ID()); ?>" class="title">
                    <?php the_title() ?>
                </a>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
		echo '</div>';
    endif;
	
	// Forums query
	if ( class_exists( 'bbPress' ) ) :

		echo '<div class="search-results-tab forum show" id="search-forum-results">';
		
		$forum = new WP_Query( array(
			'post_type'      => 'forum',
			's'              => $_POST['keyword'] ?? '',
			'posts_per_page' => 5,
		) );

		if ( $forum->have_posts() ) :
			echo '<h2 class="bbpc-search-title">' . esc_html__( 'Forums', 'bbp-core' ) . '</h2>';
			while ( $forum->have_posts() ) : $forum->the_post();
				?>
				<div class="search-result-item forum" onclick="document.location='<?php echo get_the_permalink(get_the_ID()); ?>'">					
					<a href="<?php echo get_the_permalink(get_the_ID()); ?>">
						<?php the_title(); ?>
					</a>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		endif;

		$topics = new WP_Query( array(
			'post_type'      => 'topic',
			's'              => $_POST['keyword'] ?? '',
			'posts_per_page' => 5,
		) );

		if ( $topics->have_posts() ) :
			echo '<h2 class="bbpc-search-title">' . esc_html__( 'Topics', 'bbp-core' ) . '</h2>';
			while ( $topics->have_posts() ) : $topics->the_post();
				?>
				<div class="search-result-item forum" onclick="document.location='<?php echo get_the_permalink(get_the_ID()); ?>'">					
					<a href="<?php echo get_the_permalink(get_the_ID()); ?>">
						<?php the_title(); ?>
					</a>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		endif;
		echo '</div>';		
	endif;

	echo '<h5 class="not-found-text">Not Found Result!</h5>';
	echo '</div>';
	die();
}

/**
 * Blog Search results
 */
add_action( 'wp_ajax_bbpc_search_data_blog', 'bbpc_search_data_blog' );
add_action( 'wp_ajax_nopriv_bbpc_search_data_blog', 'bbpc_search_data_blog' );
function bbpc_search_data_blog() {

	$post_type 		= $_POST['post_type'] ?? '';
	$sec_title   	= '';

	if ( $post_type == 'post' ) {
		$post_name 	= 'blog';
		$sec_title   = 'Blog Posts';
	} else {
		$post_name 	= 'docs';
		$sec_title  = 'Documentation';
	}
	
	// Blog query
	$blog = new WP_Query( array(
		'post_type'      => $post_type,
		's'              => $_POST['keyword'] ?? '',
		'posts_per_page' => 5,
	) );
	echo '<div class="search-results-tab '.$post_name.' show" id="search-'.$post_name.'-results">';
	if ( $blog->have_posts() ) :
		echo '<h2 class="bbpc-search-title">' . esc_html__( $sec_title, 'bbp-core' ) . '</h2>';
		while ( $blog->have_posts() ) : $blog->the_post();
			?>
			<div class="search-result-item blog" onclick="document.location='<?php echo get_the_permalink(get_the_ID()); ?>'">					
				<a href="<?php echo get_the_permalink(get_the_ID()); ?>">
					<?php the_title(); ?>
				</a>
			</div>
			<?php
		endwhile;
		wp_reset_postdata();
	endif;
	echo '</div>';	
	die();
}

/**
 * Forum Search results
 */
add_action( 'wp_ajax_bbpc_search_data_forum', 'bbpc_search_data_forum' );
add_action( 'wp_ajax_nopriv_bbpc_search_data_forum', 'bbpc_search_data_forum' );
function bbpc_search_data_forum() {
	
	// Forums query
	if ( class_exists( 'bbPress' ) ) :

		echo '<div class="search-results-tab show" id="search-forum-results">';
		
		$forum = new WP_Query( array(
			'post_type'      => 'forum',
			's'              => $_POST['keyword'] ?? '',
			'posts_per_page' => 5,
		) );

		if ( $forum->have_posts() ) :
			echo '<h2 class="bbpc-search-title">' . esc_html__( 'Forums', 'bbp-core' ) . '</h2>';
			while ( $forum->have_posts() ) : $forum->the_post();
				?>
				<div class="search-result-item forum" onclick="document.location='<?php echo get_the_permalink(get_the_ID()); ?>'">					
					<a href="<?php echo get_the_permalink(get_the_ID()); ?>">
						<?php the_title(); ?>
					</a>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		endif;

		$topics = new WP_Query( array(
			'post_type'      => 'topic',
			's'              => $_POST['keyword'] ?? '',
			'posts_per_page' => 5,
		) );

		if ( $topics->have_posts() ) :
			echo '<h2 class="bbpc-search-title">' . esc_html__( 'Topics', 'bbp-core' ) . '</h2>';
			while ( $topics->have_posts() ) : $topics->the_post();
				?>
				<div class="search-result-item forum" onclick="document.location='<?php echo get_the_permalink(get_the_ID()); ?>'">					
					<a href="<?php echo get_the_permalink(get_the_ID()); ?>">
						<?php the_title(); ?>
					</a>
				</div>
				<?php
			endwhile;
			wp_reset_postdata();
		endif;
		echo '</div>';
	endif;
	die();
}