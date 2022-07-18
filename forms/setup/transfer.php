<h3><?php _e( "Previous Plugin Version", "bbp-core" ); ?></h3>
<?php

$found = false;

$list = array(
	'gd-bbpress-attachments',
	'gd-bbpress-bbpress',
	'gd-bbpress-settings',
	'gd-bbpress-tools',
	'gd-bbpress-widgets'
);

foreach ( $list as $name ) {
	$data = get_option( $name );
	$group = substr( $name, 11 );

	if ( is_array( $data ) && ! empty( $data ) ) {
		$found = true;

		$imported = 0;
		foreach ( $data as $key => $value ) {
			if ( gdbbx()->exists( $key, $group ) ) {
				gdbbx()->set( $key, $value, $group );
				$imported ++;
			}
		}

		gdbbx()->save( $group );

		echo sprintf( __( "Import from <strong>%s</strong> completed, %s records imported.", "bbp-core" ), $name, $imported );
		echo '<br/>';
	}
}

if ( ! $found ) {
	_e( "Older version settings not found.", "bbp-core" );
}
