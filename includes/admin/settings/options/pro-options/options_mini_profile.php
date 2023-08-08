<?php
CSF::createSection(
	$prefix,
	[
		'id'    	=> 'bbpc_mini_profile',
		'title'  => __( 'Mini Profile', 'bbp-core' ),
		'fields' => [	
			[
				'type'    			=> 'subheading',
				'content' 			=> __( 'Mini Profile', 'bbp-core' ),
			], 		
			[
				'id'       			=> 'bbpc_mini_profile',
				'type'     			=> 'switcher',
				'default'  			=> true,
				'title'    			=> __( 'Show / Hide', 'bbp-core' ),
			],		
			[
				'type'    			=> 'subheading',
				'content' 			=> __( 'Avatar', 'bbp-core' ),
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
			], 
			[
				'id'       			=> 'bbpc_profile_location',
				'type'     			=> 'select',
				'title'    			=> __( 'Select Menu', 'bbp-core' ),
				'subtitle'    		=> __( 'Select a menu location to display the user avatar', 'bbp-core' ),
				'options'  			=> 'menus',
				'default'  			=> 'main_menu',
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
			],	
			[
				'id'       			=> 'bbpc_profile_user_pos',
				'type'     			=> 'slider',
				'title'    			=> __( 'Position', 'bbp-core' ),
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
				'subtitle' 			=> __( 'How much space will be left from the menu item.', 'bbp-core' ),
				'units'    			=> array( 'px' ),
				'output'      		=> '.bbpc-mini-profile',
				'output_mode' 		=> 'margin-left',
				'output_important' 	=> true,
				'max' 				=> 200
			],
			
			[
				'type'    			=> 'subheading',
				'content' 			=> __( 'Profile', 'bbp-core' ),
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
			], 

			[
				'id'     => 'bbpc-mini-profile-width',
				'type'   => 'dimensions',
				'title'  => 'Width',
				'height' => false,
				'units'  => array( 'px' ),
				'output'      		=> '.bbpc-mini-profile-wrapper',
				'output_mode' 		=> 'min-width',
				'output_important' 	=> true,
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
			],
			
			[
				'id'       			=> 'bbpc_profile_data_pos',
				'type'     			=> 'slider',
				'title'    			=> __( 'Position', 'bbp-core' ),
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
				'subtitle' 			=> __( 'How far down you want from the menu bar.', 'bbp-core' ),
				'units'    			=> array( 'px' ),
				'output'      		=> '.bbpc-mini-profile-wrapper',
				'output_mode' 		=> 'top',
				'output_important' 	=> true,
				'max' 				=> 200
			],

			[
				'id'       => 'bbpc-mini-profile-border',
				'type'     => 'border',
				'title'    => 'Border',
				'output'      		=> '.bbpc-mini-profile-wrapper',
				'output_mode' 		=> 'border',
				'output_important' 	=> true,
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
			],
			[
				'id'       => 'bbpc-mini-profile-border-radius',
				'type'  	=> 'spacing',
				'title' 	=> 'Border Radius',		
				'output'      		=> '.bbpc-mini-profile-wrapper',
				'output_mode' 		=> 'border-radius',
				'units'  => array( 'px' ),
				'output_important' 	=> true,
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
			],
			[
				'type'    			=> 'subheading',
				'content' 			=> __( 'Color Management', 'bbp-core' ),
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
			], 
			[
				'id'       			=> 'bbpc-mini-profile-top',
				'type'   			=> 'fieldset',
				'dependency' 		=> [ 'bbpc_mini_profile', '==', true, ],
				'title'  			=> __( 'Content', 'bbp-core' ),
				'subtitle'			=> __( 'Change the color of the information at the top of the mini profile.', 'bbp-core' ),
				'fields' => array(
					[
						'id'      => 'bbpc-mini-profile-author',
						'type'    => 'link_color',
						'title'   => 'Author name',
						'default' => array(
						  'color' => '#5088f7',
						  'hover' => '#2067f4',
						),
						'output'      		=> '.bbpc-mini-profile-head a',
						'output_mode' 		=> 'color',
						'output_important' 	=> true,
					],
					[
						'id'     	 		=> 'bbpc-mini-profile-author-role',
						'type'    			=> 'color',
						'title'   			=> 'Author Role',
						'output'      		=> '.bbpc-mini-profile-head p',
						'output_mode' 		=> 'color',
						'output_important' 	=> true,
					],
					[
						'id'     	 		=> 'bbpc-mini-profile-info-color',
						'type'    			=> 'color',
						'title'   			=> 'Summery',
						'output'      		=> '.bbpc-mini-middle p span',
						'output_mode' 		=> 'color',
						'output_important' 	=> true,
					],
					[
						'id'     	 		=> 'bbpc-mini-profile-top-bg',
						'type'    			=> 'color',
						'title'   			=> 'Background',
						'output'      		=> '.bbpc-mini-profile-head, .bbpc-mini-middle',
						'output_mode' 		=> 'background-color',
						'output_important' 	=> true,
					],
				)
			],
			[
				'id'       					=> 'bbpc-mini-profile-bottom',
				'type'   					=> 'fieldset',
				'dependency' 				=> [ 'bbpc_mini_profile', '==', true, ],
				'title'						=> __( 'Links', 'bbp-core' ),
				'subtitle'					=> __( 'Change the color of the information at the bottom of the mini profile.', 'bbp-core' ),
				'fields' => array(
					[
						'id'      			=> 'bbpc-mini-profile-link',
						'type'    			=> 'link_color',
						'title'   			=> 'Color',
						'default' => array(
						  'color' 			=> '#e0e6f0',
						  'hover' 			=> '#4080FF',
						),
						'output'      		=> '.bbpc-min-profile-links ul li a',
						'output_mode' 		=> 'color',
						'output_important' 	=> true,
					],	
					[
						'id'        		=> 'bbpc-mini-profile-link-bg',
						'type'      		=> 'color',
						'title'     		=> 'Hover Background',						
						'output'      		=> '.bbpc-min-profile-links ul li a:hover',
						'output_mode' 		=> 'background-color',
						'output_important' 	=> true,
					],			
					[
						'id'     	 		=> 'bbpc-mini-profile-btm-bg',
						'type'    			=> 'color',
						'title'   			=> 'Background',
						'output'      		=> '.bbpc-min-profile-links',
						'output_mode' 		=> 'background-color',
						'output_important' 	=> true,
					],
				)
			]
			

		]
	]
);