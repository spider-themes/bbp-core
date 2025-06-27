<?php
CSF::createSection(
	$prefix,
	[
		'id'     => 'bbpc_mini_profile',
		'title'  => esc_html__( 'Mini Profile', 'bbp-core' ),
		'icon'   => 'dashicons dashicons-admin-users',
		'fields' => [
			[
				'type'    => 'subheading',
				'content' => esc_html__( 'Mini Profile', 'bbp-core' ),
			],
			
			[
				'id'      => 'bbpc_mini_profile',
				'type'    => 'switcher',
				'default' => false,
				'title'   => esc_html__( 'Show / Hide', 'bbp-core' ),
                'class'   => 'st-pro-notice'
			],
			
			[
				'type'       => 'subheading',
				'content'    => esc_html__( 'Avatar', 'bbp-core' ),
				'dependency' => [ 'bbpc_mini_profile', '==', true, ],
			],

			[
				'id'         => 'bbpc_profile_location',
				'type'       => 'fieldset',
				'title'      => esc_html__( 'Select Location', 'bbp-core' ),
				'subtitle'   => esc_html__( 'Select the menu location to display the mini profile.', 'bbp-core' ),
				'dependency' => [ 'bbpc_mini_profile', '==', true, ],
                'class'      => 'st-pro-notice',
				'fields'     => [
					[
						'id' 		 => 'location_option',
						'type'       => 'select',
						'title'		 => esc_html__( 'Menu Location', 'bbp-core' ),
						'options'    => 'bbpc_get_registered_nav_menus',
						'after'		 => __( 'To insert the mini profile into this location.', 'bbp-core' )						 
					],

					[
						'id' 		 => 'location_selector',
						'type'       => 'text',
						'title'		 => esc_html__( '<b>Or</b> Selector', 'bbp-core' ),
						'after'		 => esc_html__( 'To insert end of this selector. Ex: <code>.parent_selector</code>', 'bbp-core' )
					]
				]
			],

			[
				'id'               => 'bbpc_profile_user_pos',
				'type'             => 'slider',
				'title'            => esc_html__( 'Gap', 'bbp-core' ),
				'dependency'       => [ 'bbpc_mini_profile', '==', true, ],
				'subtitle'         => esc_html__( 'Set the gap between the menu and the Mini Profile\'s avatar image. The gap will be applied to Left side of the Avatar image.', 'bbp-core' ),
				'unit'             => 'px',
				'output'           => '.bbpc-mini-profile',
				'output_mode'      => 'margin-left',
				'output_important' => true,
				'max'              => 200,
                'class'            => 'st-pro-notice'
			],

			[
				'type'       	   => 'subheading',
				'content'    	   => esc_html__( 'Dropdown Profile Box', 'bbp-core' ),
				'dependency' 	   => [ 'bbpc_mini_profile', '==', true, ],
			],

			[
				'id'               => 'bbpc-mini-profile-width',
				'type'             => 'dimensions',
				'title'            => esc_html__( 'Width', 'bbp-core'),
				'height'           => false,
				'units'            => array( 'px' ),
				'output'           => '.bbpc-mini-profile-wrapper',
				'output_mode'      => 'min-width',
				'output_important' => true,
				'dependency'       => [ 'bbpc_mini_profile', '==', true, ],
                'class'            => 'st-pro-notice'
			],

			[
				'id'               => 'bbpc_profile_data_pos',
				'type'             => 'slider',
				'title'            => esc_html__( 'Gap', 'bbp-core' ),
				'subtitle'         => esc_html__( 'Set the gap between the Avatar image and the Mini Profile\'s dropdwon box. This option is helpful to adjust the menu height with the Mini Profile\'s dropdwon box', 'bbp-core' ),
				'dependency'       => [ 'bbpc_mini_profile', '==', true, ],
				'unit'             => 'px',
				'output'           => '.bbpc-mini-profile-wrapper',
				'output_mode'      => 'top',
				'output_important' => true,
				'min'              => 20,
				'max'              => 200,
                'class'            => 'st-pro-notice'
			],

			[
				'id'               => 'bbpc-mini-profile-border',
				'type'             => 'border',
				'title'            => 'Border',
				'output'           => '.bbpc-mini-profile-wrapper',
				'output_mode'      => 'border',
				'output_important' => true,
				'dependency'       => [ 'bbpc_mini_profile', '==', true, ],
                'class'            => 'st-pro-notice'
			],
			
			[
				'id'               => 'bbpc-mini-profile-border-radius',
				'type'             => 'spacing',
				'title'            => esc_html__( 'Border Radius', 'bbp-core' ),
				'output'           => '.bbpc-mini-profile-wrapper',
				'output_mode'      => 'border-radius',
				'units'            => array( 'px' ),
				'output_important' => true,
				'dependency'       => [ 'bbpc_mini_profile', '==', true, ],
                'class'            => 'st-pro-notice'
			],
			
			[
				'type'       => 'subheading',
				'content'    => esc_html__( 'Color Management', 'bbp-core' ),
				'dependency' => [ 'bbpc_mini_profile', '==', true, ],
			],
			
			[
				'id'         => 'bbpc-mini-profile-top',
				'type'       => 'fieldset',
				'dependency' => [ 'bbpc_mini_profile', '==', true, ],
				'title'      => esc_html__( 'Content', 'bbp-core' ),
				'subtitle'   => esc_html__( 'Change the color of the information at the top of the mini profile.', 'bbp-core' ),
				'fields'     => array(
					[
						'id'               => 'bbpc-mini-profile-author',
						'type'             => 'link_color',
						'title'            => esc_html__( 'Author name', 'bbp-core' ),
						'default'          => array(
							'color' => '#5088f7',
							'hover' => '#2067f4',
						),
						'output'           => '.bbpc-mini-profile-head a',
						'output_mode'      => 'color',
						'output_important' => true,
                        'class'            => 'st-pro-notice'
					],
					
					[
						'id'               => 'bbpc-mini-profile-author-role',
						'type'             => 'color',
						'title'            => esc_html__( 'Author Role', 'bbp-core' ),
						'output'           => '.bbpc-mini-profile-head p',
						'output_mode'      => 'color',
						'output_important' => true,
                        'class'            => 'st-pro-notice'
					],
					
					[
						'id'               => 'bbpc-mini-profile-info-color',
						'type'             => 'color',
						'title'            => esc_html__( 'Summery', 'bbp-core' ),
						'output'           => '.bbpc-mini-middle p span',
						'output_mode'      => 'color',
						'output_important' => true,
                        'class'            => 'st-pro-notice'
					],
					
					[
						'id'               => 'bbpc-mini-profile-top-bg',
						'type'             => 'color',
						'title'            => 'Background',
						'output'           => '.bbpc-mini-profile-head, .bbpc-mini-middle',
						'output_mode'      => 'background-color',
						'output_important' => true,
                        'class'            => 'st-pro-notice'
					],
				)
			],
			
			[
				'id'         => 'bbpc-mini-profile-bottom',
				'type'       => 'fieldset',
				'dependency' => [ 'bbpc_mini_profile', '==', true, ],
				'title'      => __( 'Links', 'bbp-core' ),
				'subtitle'   => __( 'Change the color of the information at the bottom of the mini profile.', 'bbp-core' ),
				'fields'     => array(
					[
						'id'               => 'bbpc-mini-profile-link',
						'type'             => 'link_color',
						'title'            => 'Color',
						'default'          => array(
							'color'        => '#384764',
							'hover'        => '#4080FF',
						),
						'output'           => '.bbpc-min-profile-links ul li a',
						'output_mode'      => 'color',
						'output_important' => true,
                        'class'            => 'st-pro-notice'
					],
					
					[
						'id'               => 'bbpc-mini-profile-link-bg',
						'type'             => 'color',
						'title'            => 'Hover Background',
						'output'           => '.bbpc-min-profile-links ul li a:hover',
						'output_mode'      => 'background-color',
						'output_important' => true,
                        'class'            => 'st-pro-notice'
					],
					
					[
						'id'               => 'bbpc-mini-profile-btm-bg',
						'type'             => 'color',
						'title'            => 'Background',
						'output'           => '.bbpc-min-profile-links',
						'output_mode'      => 'background-color',
						'output_important' => true,
                        'class'            => 'st-pro-notice'
					],
				)
			]

		]
	]
);