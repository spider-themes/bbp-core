<?php

// Brand color option
    
CSF::createSection(
    $prefix,
    [
        'title'  => __( 'Appearance', 'bbp-core' ),
        'fields' => [
            [ 
                'id'          => 'bbpc_brand_color',
                'type'        => 'color',
                'title'       => esc_html__( 'Frontend Brand Color', 'bbp-core' ),
                'default'     => '#078669',
                'output'      => ':root',
                'output_mode' => '--bbpc_brand_color',
            ]
    
        ],
    ]
);