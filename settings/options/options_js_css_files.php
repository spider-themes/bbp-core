<?php
CSF::createSection(
	$prefix,
	[
		'title'  => __( 'JS/CSS Files', 'bbp-core' ),
		'fields' => [
			[
				'type'    => 'subheading',
				'content' => __( 'Additional Libraries', 'bbp-core' ),
			],

			[
				'id'       => 'fitvids',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'FitVids', 'bbp-core' ),
				'subtitle' => __( 'Load FitVids library for making YouTube and Vimeo videos responsive. If you already load this library in some other way, disable this option to avoid duplication.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'CSS and JS files loading', 'bbp-core' ),
			],

			[
				'id'       => 'load-bulk-js',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Load Bulk JS', 'bbp-core' ),
				'subtitle' => __( 'Load most of the JS as one single file, instead of loading individual JS components files. Bulk file replaces 3 individual JS files.', 'bbp-core' ),
			],

			[
				'id'       => 'load-bulk-css',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Load Bulk CSS', 'bbp-core' ),
				'subtitle' => __( 'Load most of the CSS as one single file, instead of loading individual CSS components files. Bulk file replaces 5 individual CSS files. RTL support is loaded as additional file.', 'bbp-core' ),
			],
          
			[
				'id'       => 'embedded-icons-font',
				'type'     => 'switcher',
				'default'  => 1,
				'title'    => __( 'Embedded Icons Font', 'bbp-core' ),
				'subtitle' => __( 'Load the font with icons version of the file that has WOFF and WOFF2 fonts embedded into CSS. This will improve the font loading, and eliminate this font as render blocking.', 'bbp-core' ),
			],

			[
				'type'    => 'subheading',
				'content' => __( 'Advanced loading settings', 'bbp-core' ),
			],

			[
				'id'       => 'embedded-icons-font',
				'type'     => 'switcher',
				'title'    => __( 'Always Load', 'bbp-core' ),
				'subtitle' => __( 'If you use short codes to embed forums, and you rely on plugin to add JS and CSS, you also need to enable this option to skip checking for bbPress specific pages. This option is not needed anymore, but if you still have issues with loaded files, enable it.', 'bbp-core' ),
			],

		],
	]
);
