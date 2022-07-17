<li>
	<a href="<?php bbp_user_profile_url( $user->ID ); ?>"><?php echo $user->display_name; ?></a>
	<span>
	<?php

	$thanks_count = $user->thanks_count;
	echo sprintf( _n( '%s thanks', '%s thanks', $thanks_count, 'bbp-core' ), $thanks_count );

	?>
	</span>
</li>
