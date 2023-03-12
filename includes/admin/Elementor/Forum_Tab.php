<?php

namespace admin\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WP_Query;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Forum_Tab extends Widget_Base {
	public function get_name() {
		return 'ama_forum_tab';
	}

	public function get_title() {
		return esc_html__( 'Forum Tab', 'ama-core' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'bbp-core' ];
	}

	protected function register_controls() {
		// --- Forum Filter Options
		$this->start_controls_section(
			'forum_filter', [
				'label' => __( 'Froum Filter Options', 'ama-core' ),
			]
		);

		$this->add_control(
			'ppp', [
				'label'       => esc_html__( 'Show Forums', 'ama-core' ),
				'description' => esc_html__( 'Show the forums count at the initial view. Default is 9 forums in a row.', 'ama-core' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
				'default'     => 9
			]
		);

		$this->add_control(
			'order', [
				'label'   => esc_html__( 'Order', 'ama-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC'
				],
				'default' => 'ASC'
			]
		);

		$this->add_control(
			'more_txt', [
				'label'       => esc_html__( 'More button text', 'ama-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'separator'   => 'before',
				'default'     => 'Show more'
			]
		);

		$this->add_control(
			'more_url',
			[
				'label'       => esc_html__( 'More button link', 'ama-core' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'ama-core' ),
				'default'     => [
					'url'         => get_post_type_archive_link( 'forum' ),
					'is_external' => true,
					'nofollow'    => true,
				],
			]
		);


		$this->end_controls_section();

		//-------- Topic Filter Options
		$this->start_controls_section(
			'topic_filter', [
				'label' => __( 'Topic Filter Options', 'ama-core' ),
			]
		);

		$this->add_control(
			'ppp2', [
				'label'       => esc_html__( 'Show Forums', 'ama-core' ),
				'description' => esc_html__( 'Show the forums count at the initial view. Default is 9 forums in a row.', 'ama-core' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
				'default'     => 6
			]
		);

		$this->add_control(
			'order2', [
				'label'   => esc_html__( 'Order', 'ama-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC'
				],
				'default' => 'ASC'
			]
		);

		$this->add_control(
			'more_txt2', [
				'label'       => esc_html__( 'More button text', 'ama-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Show more'
			]
		);

		$this->add_control(
			'more_url2', [
				'label'       => esc_html__( 'More button link', 'ama-core' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'ama-core' ),
				'default'     => [
					'url'         => get_post_type_archive_link( 'topic' ),
					'is_external' => true,
					'nofollow'    => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'forum_tab_styling', [
				'label' => __( 'Section Styles', 'ama-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'padding', [
				'label'      => __( 'Section Padding', 'ama-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .community-area.bg-disable' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'background',
				'label'    => __( 'Section Background', 'ama-core' ),
				'types'    => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}} .community-area.bg-disable',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'forum_tab_style', [
				'label' => __( 'Forum Tab Title', 'ama-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'forum_tab_title',
				'label'    => __( 'Tab Label Typography', 'ama-core' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .community-area .nav-tabs .nav-item button',
			]
		);

		$this->add_control(
			'forum_tab_title_color',
			[
				'label'     => __( 'Tab Label Color', 'ama-core' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .community-area .nav-tabs .nav-item button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'forum_tab_button', [
				'label' => esc_html__( 'Forum Tab Button', 'ama-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tab_btn_margin', [
				'label'      => esc_html__( 'Margin', 'ama-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tab-content .show-more-btn.show-more-round' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tab_btn_padding', [
				'label'      => esc_html__( 'Tab button padding', 'ama-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .tab-content .show-more-btn.show-more-round' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$forums = new WP_Query( array(
			'post_type'      => 'forum',
			'posts_per_page' => ! empty( $settings['ppp'] ) ? $settings['ppp'] : 9,
			'order'          => $settings['order'],
		) );

		$topics = new WP_Query( array(
			'post_type'      => 'topic',
			'posts_per_page' => ! empty( $settings['ppp2'] ) ? $settings['ppp2'] : 9,
			'order'          => $settings['order'],
		) );

		include( "inc/forum/forum_tab.php" );
	}
}