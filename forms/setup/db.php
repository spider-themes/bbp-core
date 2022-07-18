<h3 style="margin-top: 0;"><?php _e( "Additional database tables", "bbp-core" ); ?></h3>
<?php

require_once( GDBBX_PATH . 'core/admin/install.php' );

$list_db = gdbbx_install_database();

$displayable = array();

foreach ( $list_db as $item ) {
	if ( substr( $item, 0, 7 ) !== 'Changed' ) {
		$displayable[] = $item;
	}
}

if ( ! empty( $displayable ) ) {
	echo '<h4>' . __( "Database Upgrade Notices", "bbp-core" ) . '</h4>';
	echo join( '<br/>', $displayable );
}

echo '<h4>' . __( "Database Tables Check", "bbp-core" ) . '</h4>';
$check = gdbbx_check_database();

$msg = array();
foreach ( $check as $table => $data ) {
	if ( $data['status'] == 'error' ) {
		$_proceed  = false;
		$_error_db = true;
		$msg[]     = '<span class="gdpc-error">[' . __( "ERROR", "bbp-core" ) . '] - <strong>' . $table . '</strong>: ' . $data['msg'] . '</span>';
	} else {
		$msg[] = '<span class="gdpc-ok">[' . __( "OK", "bbp-core" ) . '] - <strong>' . $table . '</strong></span>';
	}
}

echo join( '<br/>', $msg );
