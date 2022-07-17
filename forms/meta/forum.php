<?php

use SpiderDevs\Plugin\BBPC\Basic\Plugin;

function bbpc_select_forum_settings() : array {
	return [
		'default' => __( 'Default', 'bbp-core' ),
		'inherit' => __( 'Inherit', 'bbp-core' ),
		'yes'     => __( 'Yes', 'bbp-core' ),
		'no'      => __( 'No', 'bbp-core' ),
	];
}

function bbpc_select_forum_override() : array {
	return [
		'default' => __( 'Default', 'bbp-core' ),
		'inherit' => __( 'Inherit', 'bbp-core' ),
		'yes'     => __( 'Override', 'bbp-core' ),
	];
}

global $post_ID, $_meta;

$tabs = apply_filters(
	'bbpc_admin_toolbox_meta',
	[
		'attachments' => [
			'label' => __( 'Attachments', 'bbp-core' ),
			'icon'  => 'paperclip',
		],
		'privacy'     => [
			'label' => __( 'Privacy', 'bbp-core' ),
			'icon'  => 'vault',
		],
		'locking'     => [
			'label' => __( 'Forum Lock', 'bbp-core' ),
			'icon'  => 'lock',
		],
		'closing'     => [
			'label' => __( 'Auto Close Topics', 'bbp-core' ),
			'icon'  => 'hidden',
		],
	]
);

if ( ! Plugin::instance()->is_enabled( 'private-topics' ) && ! Plugin::instance()->is_enabled( 'private-replies' ) ) {
	unset( $tabs['privacy'] );
}

if ( ! Plugin::instance()->is_enabled( 'lock-forums' ) ) {
	unset( $tabs['locking'] );
}

if ( ! Plugin::instance()->is_enabled( 'auto-close-topics' ) ) {
	unset( $tabs['closing'] );
}

$_meta = get_post_meta( $post_ID, '_bbpc_settings', true );

if ( ! is_array( $_meta ) ) {
	$_meta = bbpc_default_forum_settings();
} else {
	$_meta = wp_parse_args( $_meta, bbpc_default_forum_settings() );
}

?>
<div class="d4plib-metabox-wrapper">
	<input type="hidden" name="bbpc_forum_settings" value="edit" />

	<ul class="wp-tab-bar">
		<?php

		$active = true;
		foreach ( $tabs as $tab => $obj ) {
			$label = $obj['label'];
			$icon  = $obj['icon'];

			echo '<li class="' . ( $active ? 'wp-tab-active' : '' ) . '"><a href="#bbpc-meta-' . $tab . '">';
			echo '<span aria-hidden="true" aria-labelledby="bbpc-forums-metatab-' . $tab . '" class="dashicons dashicons-' . $icon . '" title="' . $label . '"></span>';
			echo '<span id="bbpc-forums-metatab-' . $tab . '" class="d4plib-metatab-label">' . $label . '</span>';
			echo '</a></li>';

			$active = false;
		}

		?>
	</ul>
	<?php

	$active = true;
	foreach ( $tabs as $tab => $label ) {
		echo '<div id="bbpc-meta-' . $tab . '" class="wp-tab-panel ' . ( $active ? 'tabs-panel-active' : 'tabs-panel-inactive' ) . '">';

		do_action( 'bbpc_admin_toolbox_forums_meta_content_' . $tab, $post_ID );

		echo '</div>';

		$active = false;
	}

	?>
</div>
