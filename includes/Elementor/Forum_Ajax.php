<?php

namespace admin\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WP_Query;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Forum_Ajax extends Widget_Base {
	public function get_name() {
		return 'ama_ajax_forum';
	}

	public function get_title() {
		return esc_html__( 'BBPC Ajax Forums', 'bbp-core' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_keywords() {
		return [ 'forum', 'ajax' ];
	}

	public function get_categories() {
		return [ 'bbp-core' ];
	}

	public function get_style_depends() {
		return [ 'bbpc-el-widgets' ];
	}
	
	public function get_script_depends() {
		return [ 'bbpc-ajax' ];
	}

	protected function register_controls() {
		/**
		 * Content section
		 */
		$this->start_controls_section(
			'content_sec', [
				'label' => esc_html__( 'Content Section', 'bbp-core' ),
			]
		);

		$this->add_control(
			'ppp', [
				'label'       => esc_html__( 'Show Forums', 'bbp-core' ),
				'description' => esc_html__( 'Show the forums count at the initial view. Default is 9 forums in a row.', 'bbp-core' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'label_block' => true,
				'default'     => 9
			]
		);

		$this->add_control(
			'order', [
				'label'   => esc_html__( 'Order', 'bbp-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC'
				],
				'default' => 'ASC'
			]
		);

		$this->end_controls_section();

		/**
		 * Styling section starts
		 */
		$this->start_controls_section(
			'styling_sec', [
				'label' => esc_html__( 'Title Style', 'bbp-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'forum_title_typography',
				'label'    => esc_html__( 'Forum title typography', 'bbp-core' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .single-forum-post-widget .post-title a',
			]
		);

		$this->add_control(
			'forum_title_color',
			[
				'label'     => esc_html__( 'Forum title color', 'bbp-core' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .single-forum-post-widget .post-title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'forum_title_hover_color',
			[
				'label'     => esc_html__( 'Forum title color', 'bbp-core' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .single-forum-post-widget .post-title a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'forum_meta_typography',
				'label'    => esc_html__( 'Forum meta typography', 'bbp-core' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .single-forum-post-widget .post-info .author,{{WRAPPER}} .single-forum-post-widget .post-info .post-time',
			]
		);

		$this->add_control(
			'forum_meta_color',
			[
				'label'     => esc_html__( 'Forum meta color', 'bbp-core' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .single-forum-post-widget .post-info .author,{{WRAPPER}} .single-forum-post-widget .post-info .post-time' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'parent_forum_typo',
				'label'    => esc_html__( 'Parent forum typography', 'bbp-core' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .post-content .post-category a',
			]
		);

		$this->add_control(
			'parent_forum_color',
			[
				'label'     => esc_html__( 'Parent forum color', 'bbp-core' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .post-content .post-category a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'parent_forum_color_hover',
			[
				'label'     => esc_html__( 'Parent forum hover color', 'bbp-core' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .post-content .post-category a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$topics = new WP_Query( array(
			'post_type'      => 'topic',
			'posts_per_page' => ! empty( $settings['ppp2'] ) ? $settings['ppp2'] : 9,
			'order'          => $settings['order'] ? $settings['order'] : 'DESC',
		) );
		?>

        <div class="forum-post-widget" data_id="<?php echo esc_attr( $this->get_id() ); ?>">
            <div class="post-filter-widget mb-20 wow fadeInUp">
                <div class="single-filter-item">
                    <!-- <i class="bi bi-grid"></i> -->
                    <svg fill="#000000" width="15px" height="15px" viewBox="-2 -2 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin"
                         class="jam jam-grid">
                        <path d='M2 2v4h4V2H2zm0-2h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm12 0h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm0 2v4h4V2h-4zm0 10h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2zm0 2v4h4v-4h-4zM2 12h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2zm0 2v4h4v-4H2z'/>
                    </svg>
                    <a href="#" id="all_filt" data-forum="all" class="data-active"><?php _e( 'All', 'bbp-core' ) ?></a>
                </div>
                <div class="single-filter-item">
                    <!-- <i class="las la-fire"></i> -->
                    <svg fill="#000000" width="15px" height="15px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                        <path d="M615.418 766.446c16.533 22.427 25.578 49.428 25.578 77.826 0 72.765-59.342 131.799-132.588 131.799-73.244 0-132.577-59.032-132.577-131.799 0-27.062 8.196-52.845 23.296-74.639l-33.669-23.327c-19.808 28.591-30.587 62.498-30.587 97.966 0 95.435 77.719 172.759 173.537 172.759 95.82 0 173.548-77.325 173.548-172.759 0-37.227-11.891-72.727-33.568-102.131l-32.969 24.305z"/>
                        <path d="M631.109 793.081c5.649 9.799 18.172 13.164 27.971 7.515s13.164-18.172 7.515-27.971L541.493 555.598c-16.315-28.314-49.85-28.314-66.167.003l-125.1 217.024c-5.649 9.799-2.284 22.322 7.515 27.971s22.322 2.284 27.971-7.515L508.41 580.224l122.698 212.857z"/>
                        <path d="M757.563 484.111c38.881 52.779 60.162 116.401 60.162 183.283 0 171.329-139.484 310.252-311.583 310.252-172.105 0-311.583-138.919-311.583-310.252 0-63.691 19.299-124.465 54.802-175.781l-33.684-23.304c-40.202 58.108-62.078 126.996-62.078 199.085 0 193.991 157.853 351.212 352.543 351.212 194.683 0 352.543-157.224 352.543-351.212 0-75.703-24.124-147.821-68.145-207.577l-32.978 24.294z"/>
                        <path d="M506.141 45.881l286.943 498.337c5.644 9.802 18.165 13.173 27.967 7.529s13.173-18.165 7.529-27.967L539.218 21.242c-16.301-28.324-49.834-28.324-66.152-.006L183.711 523.78c-5.644 9.802-2.273 22.323 7.529 27.967s22.323 2.273 27.967-7.529L506.14 45.881z"/>
                    </svg>
                    <a href="#" id="populer_filt" data-forum="popular"><?php _e( 'Popular', 'bbp-core' ) ?></a>
                </div>
                <div class="single-filter-item">
                    <!-- <i class="bi bi-bookmark-star"></i> -->
                    <svg fill="#000000" width="16px" height="16px" viewBox="0 0 24 24" version="1.2" baseProfile="tiny" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 2h-8c-1.654 0-3 1.346-3 3v14c0 .514.104.946.308 1.285.564.935 1.815 1.008 2.813.008l3.172-3.172c.375-.374 1.039-.374 1.414 0l3.172 3.172c.491.491 1.002.74 1.52.74.797 0 1.601-.629 1.601-2.033v-14c0-1.654-1.346-3-3-3zm-8 2h8c.551 0 1 .449 1 1v9.905l-2.451-2.247c-1.406-1.289-3.693-1.288-5.099 0l-2.45 2.247v-9.905c0-.551.449-1 1-1zm6.121 11.707c-.565-.565-1.318-.876-2.121-.876s-1.556.312-2.121.876l-2.879 2.879v-2.324l3.126-2.866c1.033-.947 2.714-.947 3.747 0l3.127 2.866v2.324l-2.879-2.879z"/>
                    </svg>
                    <a href="#" id="featured_filt" data-forum="featured"><?php _e( 'Featured', 'bbp-core' ) ?></a>
                </div>
                <div class="single-filter-item">
                    <!-- <i class="bi bi-clock-history"></i> -->
                    <svg width="15px" height="15px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-clock-history">
                        <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                        <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                    <a href="#" id="recent_filt" data-forum="recent"><?php _e( 'Recent', 'bbp-core' ) ?></a>
                </div>
                <div class="single-filter-item">
                    <!-- <i class="las la-times-circle"></i> -->
                    <svg fill="#000000" width="15px" height="15px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.71,8.29a1,1,0,0,0-1.42,0L12,10.59,9.71,8.29A1,1,0,0,0,8.29,9.71L10.59,12l-2.3,2.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L12,13.41l2.29,2.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42L13.41,12l2.3-2.29A1,1,0,0,0,15.71,8.29Zm3.36-3.36A10,10,0,1,0,4.93,19.07,10,10,0,1,0,19.07,4.93ZM17.66,17.66A8,8,0,1,1,20,12,7.95,7.95,0,0,1,17.66,17.66Z"/>
                    </svg>
                    <a href="#" id="unsolved_filt" data-forum="unsolved"><?php _e( 'Unsolved', 'bbp-core' ) ?></a>
                </div>
                <div class="single-filter-item">
                    <!-- <i class="las la-check-circle"></i>  -->
                    <svg width="15px" height="15px" viewBox="0 0 1024 1024" class="icon" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#000000" d="M512 896a384 384 0 100-768 384 384 0 000 768zm0 64a448 448 0 110-896 448 448 0 010 896z"/>
                        <path fill="#000000"
                              d="M745.344 361.344a32 32 0 0145.312 45.312l-288 288a32 32 0 01-45.312 0l-160-160a32 32 0 1145.312-45.312L480 626.752l265.344-265.408z"/>
                    </svg>
                    <a href="#" id="solved_filt" data-forum="solved"><?php _e( 'Solved', 'bbp-core' ) ?></a>
                </div>
            </div>

            <div id="aj-post-filter-widget">
				<?php
				$delay = 0.0;
				$i     = 0;
				while ( $topics->have_posts() ) : $topics->the_post();
					$item_id    = get_the_ID();
					$author_id  = get_post_field( 'post_author', $item_id );
					$topic_id   = $topics->posts[ $i ]->ID;
					$vote_count = get_post_meta( $topic_id, "bbpv-votes", true );
					$forum_id   = bbp_get_topic_forum_id();
					?>
                    <div class="single-forum-post-widget wow fadeInUp" data-wow-delay="<?php echo $delay ?>s">
                        <div class="post-content">
                            <div class="post-title">
                                <h6><a href="<?php the_permalink(); ?>"> <?php the_title() ?> </a></h6>
                            </div>
                            <div class="post-info">
                                <div class="author">
                                    <img src="<?php echo BBPC_IMG ?>/forum_tab/user-circle-alt.svg" alt="<?php esc_attr_e( 'User circle alt icon', 'bbpc-core' ); ?>">
									<?php echo get_the_author_meta( 'display_name', $author_id ) ?>
                                </div>

                                <div class="post-time">
                                    <img src="<?php echo BBPC_IMG ?>/forum_tab/time-outline.svg" alt="<?php esc_attr_e( 'Time outline icon', 'bbpc-core' ); ?>">
									<?php echo bbp_forum_last_active_time( get_the_ID() ); ?>
                                </div>
                            </div>

                            <div class="post-category">
                                <a href="<?php echo get_the_permalink( $forum_id ) ?>">
									<?php echo get_the_post_thumbnail( $forum_id ); ?>
									<?php echo bbp_get_topic_forum_title(); ?>
                                </a>
                            </div>
                        </div>
                        <div class="post-reach">
                            <div class="post-view">
                                <img src="<?php echo BBPC_IMG ?>/forum_tab/eye-outline.svg" alt="<?php esc_attr_e( 'Eye outline icon', 'bbpc-core' ); ?>">
								
								<?php 
								bbp_topic_view_count( $topic_id );
								echo '&nbsp;';
								_e( 'Views', 'bbp-core' );
								?>
                            </div>
                            <div class="post-like">
                                <img src="<?php echo BBPC_IMG ?>/forum_tab/thumbs-up-outline.svg" alt="<?php esc_attr_e( 'Thumbs-up outline icon', 'bbpc-core' ); ?>">
								
								<?php 
								if ( $vote_count ) {
									echo $vote_count;
								} else {
									echo "0";
								}
								
								echo '&nbsp;';
								_e( 'Likes', 'bbp-core' );
								?>
                            </div>
                            <div class="post-comment">
                                <img src="<?php echo BBPC_IMG ?>/forum_tab/chatbubbles-outline.svg" alt="<?php esc_attr_e( 'Chat bubbles icon', 'bbpc-core' ); ?>">
								
								<?php 
								bbp_topic_reply_count( $topic_id );
								echo '&nbsp;';
								_e( 'Replies', 'bbp-core' );
								?>
                            </div>
                        </div>
                    </div>

					<?php
					$delay += 0.2;
					if ( $delay > 0.6 ) {
						$delay = 0.0;
					}
					$i ++;
				endwhile;
				unset( $delay );
				unset( $i );
				wp_reset_postdata();
				?>
            </div>
        </div>
		<?php
	}
}