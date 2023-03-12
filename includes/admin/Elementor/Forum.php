<?php
	namespace admin\Elementor;

	use Elementor\Controls_Manager;
	use Elementor\Widget_Base;
	use WP_Query;

	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	class Forum extends Widget_Base {
		public function get_name() {
			return 'ama_forum';
		}

		public function get_title() {
			return esc_html__( 'Forum', 'ama-core' );
		}

		public function get_icon() {
			return 'eicon-posts-grid';
		}

		public function get_categories() {
			return [ 'bbp-core' ];
		}

		protected function register_controls() {
			//-------------------------------- Choose Style ------------------------------------- //
			// $this->start_controls_section(
			// 	'style_sec', [
			// 		'label' => esc_html__( 'Choose Style', 'ama-core' ),
			// 	]
			// );

			//TODO: Extend this feature when needed
			// $this->add_control(
			// 	'style',
			// 	[
			// 		'label'   => esc_html__( 'Forum Style', 'ama-core' ),
			// 		'type'    => Controls_Manager::SELECT,
			// 		'options' => [
			// 			'1' => esc_html__( 'Classic', 'ama-core' ),
			// 			'2' => esc_html__( 'Category', 'ama-core' ),
			// 			'3' => esc_html__( 'Threaded', 'ama-core' ),
			// 		],
			// 		'default' => '2',
			// 	]
			// );

			// $this->end_controls_section();

			// --- Forum Filter Options
			$this->start_controls_section(
				'forum_filter', [
					'label' => __( 'Forum Filter Options', 'ama-core' ),
				]
			);

			$this->add_control(
				'ppp',
				[
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

			$this->end_controls_section();
			
			$this->start_controls_section(
			    'forum_styling', [
			        'label' => esc_html__( 'Forum Style', 'ama-core' ),
			        'tab'   => Controls_Manager::TAB_STYLE,
			    ]
			 );
			 
			 $this->add_responsive_control(
			     'section_padding',[
			         'label' => esc_html__( 'Section Padding', 'ama-core' ),
			         'type' => Controls_Manager::DIMENSIONS,
			         'size_units' => [ 'px', '%', 'em' ],
			         'selectors' => [
			             '{{WRAPPER}} .forum-category-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			         ],
			     ]
			 );


			 $this->add_responsive_control(
			     'container_padding',[
			         'label' => esc_html__( 'Container Padding', 'ama-core' ),
			         'type' => Controls_Manager::DIMENSIONS,
			         'size_units' => [ 'px', '%', 'em' ],
			         'selectors' => [
			             '{{WRAPPER}} .forum-category-area .container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			         ],
			     ]
			 );


			$this->add_responsive_control(
				'container_margin',[
					'label' => esc_html__( 'Container Margin', 'ama-core' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .forum-category-area .container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'item_margin',[
					'label' => esc_html__( 'Item Margin', 'ama-core' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .forum-category-area .container .col-custom' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
			    'column_width',
			    [
			        'label' => esc_html__( 'Column Width', 'ama-core' ),
			        'type' => \Elementor\Controls_Manager::SLIDER,
			        'size_units' => [ 'px', '%' ],
			        'range' => [
			            'px' => [
			                'min' => 0,
			                'max' => 1170,
			                'step' => 5,
			            ],
			            '%' => [
			                'min' => 0,
			                'max' => 100,
			            ],
			        ],

			        'selectors' => [
			            '{{WRAPPER}} .forum-category-area .col-custom' => 'width: {{SIZE}}{{UNIT}};',
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

			// include( "inc/forum/forum-{$settings['style']}.php" ); TODO: change it when you properly extend feature
			include( "inc/forum/forum-2.php" );
		}
	}