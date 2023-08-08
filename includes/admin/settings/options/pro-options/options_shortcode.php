<?php
// Create a section.
CSF::createSection(
    $prefix,
    [
        'title'  => __( 'Shortcodes', 'bbp-core' ),
        'fields' => [
            
            [
                'id'         => 'bbpc_shortcode',
                'type'       => 'text',
                'title'      => esc_html__( 'Profile URL', 'bbp-core' ),
                'subtitle'   => esc_html__( 'Use this shortcode to display your profile URL', 'bbp-core' ),
                'default'    => '[bbpc_profile_link]',
                'attributes' => array(
                    'readonly' => 'readonly',
                ),
            ]
            
        ],
    ]
);
