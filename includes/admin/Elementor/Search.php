<?php

namespace admin\Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Search extends Widget_Base {
	public function get_name() {
		return 'ama_search';
	}

	public function get_title() {
		return esc_html__( 'BBP Search', 'ama-core' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_style_depends() {
		return [ 'ama-core-style' ];
	}

	public function get_categories() {
		return [ 'bbp-core' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'search_form_sec', [
				'label' => esc_html__( 'Form', 'ama-core' ),
			]
		);

		$this->add_control(
			'placeholder', [
				'label'       => esc_html__( 'Placeholder', 'ama-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Search for Topics....',
			]
		);

		$this->add_control(
			'submit_btn_icon', [
				'label'   => __( 'Submit Button Icon', 'ama-core' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-search',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'is_keywords', [
				'label'        => esc_html__( 'Keywords', 'ama-core' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'keywords_label',
			[
				'label'       => esc_html__( 'Keywords Label', 'ama-core' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Popular:',
				'condition'   => [
					'is_keywords' => 'yes'
				]
			]
		);

		$keywords = new \Elementor\Repeater();

		$keywords->add_control(
			'title', [
				'label'       => __( 'Title', 'ama-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'keywords',
			[
				'label'         => __( 'Repeater List', 'ama-core' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $keywords->get_controls(),
				'default'       => [
					[
						'title' => __( 'Keyword #1', 'ama-core' ),
					],
					[
						'title' => __( 'Keyword #2', 'ama-core' ),
					],
				],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false,
				'condition'     => [
					'is_keywords' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		/**
		 * Style Keywords
		 * Global
		 */
		$this->start_controls_section(
			'style_form', [
				'label' => esc_html__( 'Form', 'ama-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_placeholder', [
				'label'     => esc_html__( 'Placeholder Color', 'ama-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search_form_wrap .search_field_wrap::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'typography_placeholder',
				'label'    => esc_html__( 'Typography', 'ama-core' ),
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .search_form_wrap .search_field_wrap::placeholder',
			]
		);

		$this->add_control(
			'color_text', [
				'label'     => esc_html__( 'Text Color', 'ama-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search_form_wrap .search_field_wrap' => 'color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'color_icon', [
				'label'     => esc_html__( 'Submit Icon Color', 'ama-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .search_submit_btn > i' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .ama-search-widget',
			]
		);

		$this->add_responsive_control(
			'border-radius', [
				'label'      => __( 'Border Radius', 'ama-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .search_field_wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .search-box input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'input-padding',[
                'label' => esc_html__( 'Input Filed Padding', 'ama-core' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .search_field_wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .search-box input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'search_bg',
			[
				'label'     => __( 'Search Icon Background', 'ama-core' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .search-box i' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_keywords', [
				'label' => esc_html__( 'Keywords', 'ama-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'margin_keywords', [
				'label'       => __( 'Margin', 'ama-core' ),
				'description' => __( 'Margin around the keywords block', 'ama-core' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%', 'em' ],
				'selectors'   => [ '{{WRAPPER}} .header_search_keyword' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'separator'   => 'before',
				'default'     => [
					'unit' => 'px', // The selected CSS Unit. 'px', '%', 'em',
				],
			]
		);

		$this->add_control(
			'color_keywords_label', [
				'label'     => esc_html__( 'Label Color', 'ama-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header_search_keyword .header-search-form__keywords-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'keyword_label_typography',
				'label'    => __( 'Label Typography', 'ama-core' ),
				'scheme'   => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .search_keyword_label',
			]
		);

		$this->add_control(
			'color_keywords', [
				'label'     => esc_html__( 'Keyword Color', 'ama-core' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .header_search_keyword ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_keywords_bg', [
				'label'     => esc_html__( 'Background Color', 'ama-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header_search_keyword ul li a' => 'background: {{VALUE}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'typography_keywords',
				'label'    => esc_html__( 'Typography', 'ama-core' ),
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .header_search_keyword ul li a',
			]
		);

		$this->add_control(
			'keywords_padding', [
				'label'      => __( 'Padding', 'ama-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .header_search_keyword ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'default'    => [
					'unit' => 'px', // The selected CSS Unit. 'px', '%', 'em',
				],
			]
		);

		$this->add_control(
			'border_radius', [
				'label'      => __( 'Border Radius', 'ama-core' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .header_search_keyword ul li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
				'default'    => [
					'unit' => 'px', // The selected CSS Unit. 'px', '%', 'em',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings  = $this->get_settings();
		$title_tag = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h2';

		?>
        <form action="<?php echo esc_url( home_url( '/' ) ) ?>" class="ama-search-widget mx-auto">
            <div class="form-group">
                <div class="search-box">
                    <!-- <i class="icon_search"></i> -->
					<svg width="38px" height="38px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#078669"><path d="M15.25 0a8.25 8.25 0 0 0-6.18 13.72L1 22.88l1.12 1 8.05-9.12A8.251 8.251 0 1 0 15.25.01V0zm0 15a6.75 6.75 0 1 1 0-13.5 6.75 6.75 0 0 1 0 13.5z"/></svg>
                    <input type='search' id="searchInput" autocomplete="off" name="s" placeholder="<?php echo esc_attr( $settings['placeholder'] ) ?>">

                    <!-- Ajax Search Loading Spinner -->
					<?php include( 'inc/hero/search-spinner.php' ); ?>

                    <!-- WPML Language Code -->
					<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
                        <input type="hidden" name="lang" value="<?php echo( ICL_LANGUAGE_CODE ); ?>"/>
					<?php endif; ?>
                </div>
            </div>

			<?php include 'inc/hero/ajax-search-results.php'; ?>
            <div class="ama-search-keyword-wrapper">
	            <?php include 'inc/hero/keywords.php' ?>
            </div>
        </form>

		<?php
	}
}