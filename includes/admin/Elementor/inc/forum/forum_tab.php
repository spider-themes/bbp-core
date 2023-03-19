<section class="community-area bg-disable">
    <div class="container">

        <ul class="nav nav-tabs tab-buttons" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active tab-button" id="home-tab" onclick="openTab('tab1')" data-bs-toggle="tab" data-bs-target="#home"
                        type="button" role="tab" aria-controls="home"
                        aria-selected="true">
					<?php _e( 'Show Forums', 'ama-core' ) ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-button" id="profile-tab" onclick="openTab('tab2')" data-bs-toggle="tab" data-bs-target="#profile"
                        type="button" role="tab" aria-controls="profile" aria-selected="false">
					<?php _e( 'Show Topics', 'ama-core' ) ?>
                </button>
            </li>
        </ul>

        <div class="tab-content mt-30 tabs" id="myTabContent">
            <div class="tab-pane fade show tab active" id="tab1" role="tabpanel" aria-labelledby="home-tab">
                <div class="row gy-4 bbpc-community-topic-widget-main-wrapper">
					<?php
					while ( $forums->have_posts() ) : $forums->the_post();
						$item_id   = get_the_ID();
						$author_id = get_post_field( 'post_author', $item_id );
						?>
                        <div class="col-md-6 col-lg-4 bbpc-community-topic-widget-wrapper">
                            <div class="community-topic-widget-box">
								<?php the_post_thumbnail( 'full' ); ?>
                                <div class="box-content">
                                    <a href="<?php the_permalink() ?>"><h5> <?php the_title() ?> </h5></a>
                                    <span>
                                        <?php
                                        bbp_forum_topic_count( $item_id );
										_e( ' Posts', 'ama-core' );
                                        ?>
                                    </span>
                                    <span class="vr-line">|</span>
                                    <span>
                                        <?php
                                        bbp_forum_reply_count( $item_id );
										_e( ' Replies', 'ama-core' );
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
                </div>

                <div class="row">
                    <div class="text-center bbpc-show-more-btn-wrapper">
                        <a href="<?php echo esc_url( $settings['more_url']['url'] ) ?>"
                           class="dbl-arrow-upper show-more-btn show-more-round mt-70">
                            <div class="arrow-cont">
                                <!-- <i class="arrow_carrot-down first"></i> -->
                                <svg width="13px" height="13px" class="first" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" >

                                    <path d="M0 0h48v48H0z" fill="none"/>
                                    <g id="Shopicon">
                                        <g>
                                            <polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585 		"/>
                                        </g>
                                    </g>
                                    </svg>
                                <!-- <i class="arrow_carrot-down second"></i> -->
                                <svg width="13px" height="13px" class="second" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" >

                                    <path d="M0 0h48v48H0z" fill="none"/>
                                    <g id="Shopicon">
                                        <g>
                                            <polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585 		"/>
                                        </g>
                                    </g>
                                    </svg>
                            </div>
							<?php echo $settings['more_txt'] ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade tab" id="tab2" role="tabpanel" aria-labelledby="profile-tab">
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
                                <h6><a href="<?php the_permalink(); ?>"> <?php the_title() ?> </a>
                                </h6>
                            </div>
                            <div class="post-info">
                                <div class="author">
                                    <img src="<?php echo BBPC_IMG ?>/forum_tab/user-circle-alt.svg"
                                         alt="user circle">
									<?php echo get_the_author_meta( 'display_name', $author_id ) ?>
                                </div>

                                <div class="post-time">
                                    <img src="<?php echo BBPC_IMG ?>/forum_tab/time-outline.svg"
                                         alt="time outline">
									<?php echo bbp_forum_last_active_time( get_the_ID() ); ?>
                                </div>

                                <div class="post-category">
                                    <a href="<?php echo get_the_permalink( $forum_id ) ?>">
										<?php echo get_the_post_thumbnail( $forum_id ); ?>
										<?php echo get_the_title( $forum_id ) ?>
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
                                <img src="<?php echo BBPC_IMG ?>/forum_tab/eye-outline.svg" alt="icon">
								<?php bbp_topic_view_count( $topic_id );
								_e( ' Views', 'ama-core' ) ?>
                            </div>
                            <div class="post-like">
                                <img src="<?php echo BBPC_IMG ?>/forum_tab/thumbs-up-outline.svg"
                                     alt="icon">
								<?php if ( $vote_count ) {
									echo $vote_count;
								} else {
									echo "0";
								}
								_e( ' Likes', 'ama-core' ); ?>
                            </div>
                            <div class="post-comment">
                                <img src="<?php echo BBPC_IMG ?>/forum_tab/chatbubbles-outline.svg" alt="icon">
								<?php
								echo bbp_topic_reply_count( $topic_id );
								_e( ' Replies', 'ama-core' ) ?>
                            </div>
                        </div>
                    </div>
					<?php
					$i ++;
				endwhile;
				unset( $i );
				wp_reset_postdata();
				?>

                <div class="row">
                    <div class="text-center bbpc-show-more-btn-wrapper">
                        <a href="<?php echo esc_url( $settings['more_url2']['url'] ) ?>"
                           class="dbl-arrow-upper show-more-btn show-more-round mt-70">
                            <div class="arrow-cont">
                                <!-- <i class="arrow_carrot-down first"></i> -->
                                <svg width="13px" height="13px" class=""first viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" >

                                    <path d="M0 0h48v48H0z" fill="none"/>
                                    <g id="Shopicon">
                                        <g>
                                            <polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585 		"/>
                                        </g>
                                    </g>
                                    </svg>

                                <!-- <i class="arrow_carrot-down second"></i> -->
                                <svg width="13px" height="13px" class="second" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" >

                                    <path d="M0 0h48v48H0z" fill="none"/>
                                    <g id="Shopicon">
                                        <g>
                                            <polygon points="24,29.171 9.414,14.585 6.586,17.413 24,34.827 41.414,17.413 38.586,14.585 		"/>
                                        </g>
                                    </g>
                                    </svg>
                            </div>
							<?php echo $settings['more_txt2'] ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

