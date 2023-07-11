<?php

namespace admin\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use WP_Query;
use WP_Post;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Forum_posts
 *
 * @package amaCore\Widgets
 */
class Forum_posts extends Widget_Base {

	public function get_name() {
		return 'ama_forum_posts';
	}

	public function get_title() {
		return __( 'BBPC Forum Topics', 'bbp-core' );
	}

	public function get_icon() {
		return 'bbpc_icon_ama_forum_posts';
	}

	public function get_keywords() {
		return [ 'topics', 'replies' ];
	}

	// style dependency
	public function get_style_depends() {
		return [ 'bbpc-el-widgets' ];
	}

	public function get_categories() {
		return [ 'bbp-core' ];
	}

	protected function register_controls() {

		// --- Filter Options
		$this->start_controls_section(
			'filter_opt', [
				'label' => __( 'Filter Options', 'bbp-core' ),
			]
		);

		$this->add_control(
			'ppp', [
				'label'       => esc_html__( 'Show Forum Topics', 'bbp-core' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 5
			]
		);

		$this->add_control(
			'order', [
				'label'   => esc_html__( 'Order', 'bbp-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC'
				],
				'default' => 'ASC'
			]
		);

		$this->end_controls_section();
		// end Document Setting Section
	}

	protected function render() {
		$settings    = $this->get_settings();
		$forum_posts = new WP_Query( array(
			'post_type'      => 'topic',
			'posts_per_page' => ! empty( $settings['ppp'] ) ? $settings['ppp'] : - 1,
			'order'          => $settings['order'] ? $settings['order'] : 'ASC',
		) );
		?>
        <div class="community-posts-wrapper">
			<?php
			while ( $forum_posts->have_posts() ) : $forum_posts->the_post();
				$favoriters     = bbp_get_topic_favoriters();
				$favorite_count = ! empty( $favoriters ) ? $favoriters[0] : '0';
				?>
                <div class="community-post wow fadeInUp" data-wow-delay="0.5s">
                    <div class="post-content">
                        <div class="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ) ?>
                        </div>
                        <div class="entry-content">
                            <h3 class="post-title">
                                <a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a>
                            </h3>

							<?php 
							esc_html_e( 'Last active: ', 'bbp-core' );
							echo bbp_get_forum_last_active_time( get_the_ID() );
							?>

                        </div>
                    </div>
                    <div class="post-meta-wrapper">
                        <ul class="post-meta-info">
                            <li>
                                <a href="<?php bbp_topic_permalink(); ?>">
                                    <svg width="14px" height="14px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <path fill="#444" d="M14 14.2c0 0 0 0 0 0 0-0.6 2-1.8 2-3.1 0-1.5-1.4-2.7-3.1-3.2 0.7-0.8 1.1-1.7 1.1-2.8 0-2.8-2.9-5.1-6.6-5.1-3.5 0-7.4 2.1-7.4 5.1 0 2.1 1.6 3.6 2.3 4.2-0.1 1.2-0.6 1.7-0.6 1.7l-1.2 1h1.5c1.6 0 2.9-0.5 3.7-1.1 0 0.1 0 0.1 0 0.2 0 2 2.2 3.6 5 3.6 0.2 0 0.4 0 0.6 0 0.4 0.5 1.7 1.4 3.4 1.4 0.1-0.1-0.7-0.5-0.7-1.9zM7.4 1c3.1 0 5.6 1.9 5.6 4.1s-2.6 4.1-5.8 4.1c-0.2 0-0.6 0-0.8 0h-0.3l-0.1 0.2c-0.3 0.4-1.5 1.2-3.1 1.5 0.1-0.4 0.1-1 0.1-1.8v-0.3c-1-0.8-2.1-2.2-2.1-3.6 0-2.2 3.2-4.2 6.5-4.2z"></path>
                                    </svg>
									<?php bbp_show_lead_topic() ? bbp_topic_reply_count( get_the_ID() ) : bbp_topic_post_count( get_the_ID() ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php bbp_topic_permalink(); ?>">
                                    <svg fill="#000000" width="15px" height="15px" viewBox="0 0 24 24" id="star" data-name="Line Color" xmlns="http://www.w3.org/2000/svg" class="icon line-color">
                                        <polygon id="primary" points="12 4 9.22 9.27 3 10.11 7.5 14.21 6.44 20 12 17.27 17.56 20 16.5 14.21 21 10.11 14.78 9.27 12 4" style="fill: none; stroke: rgb(0, 0, 0); stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></polygon>
                                    </svg> <?php echo esc_html( $favorite_count ); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
			<?php
			endwhile;
			?>
        </div>
		<?php
	}
}