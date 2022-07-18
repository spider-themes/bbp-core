<?php

$message = '';
$list    = array();
$counted = count( $thanks_list );

if ( $counted > $this->settings['limit_display'] ) {
	$message = sprintf( __( "Total of %s users thanked author for this post. Here are last %s listed.", "bbp-core" ), $counted, $this->settings['limit_display'] );
} else {
	$message = sprintf( _n( "%s user thanked author for this post.", "%s users thanked author for this post.", $counted, "bbp-core" ), $counted );
}

$thanks_list = array_slice( $thanks_list, 0, $this->settings['limit_display'] );

foreach ( $thanks_list as $user ) {
	$U = gdbbx_say_thanks()->build_user_for_display( $user );

	if ( $U !== false ) {
		$item = '<span class="gdbbx-thanks-user">';
		$item .= isset( $U['avatar'] ) ? $U['avatar'] . ' ' : '';
		$item .= $U['label'];
		$item .= isset( $U['date'] ) ? ' (' . $U['date'] . ')' : '';
		$item .= '</span>';

		$list[] = apply_filters( 'gdbbx_say_thanks_format_user_to_display', $item, $U, $user );
	}
}

?>
<div class="gdbbx-said-thanks">
    <h6><?php echo $message; ?></h6>

    <div class="gdbbx-thanks-list">
		<?php echo join( ', ', $list ); ?>
    </div>
</div>