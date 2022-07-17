<?php

global $post_ID;

$tabs = apply_filters(
	'bbpc_admin_toolbox_meta',
	[
		'files'  => [
			'label' => __( 'Files', 'bbp-core' ),
			'icon'  => 'media-default',
		],
		'errors' => [
			'label' => __( 'Errors', 'bbp-core' ),
			'icon'  => 'warning',
		],
	]
);

?>
<div class="d4plib-metabox-wrapper">
	<input type="hidden" name="bbpc_topic_attachments" value="edit" />

	<ul class="wp-tab-bar">
		<?php

		$active = true;
		foreach ( $tabs as $tab => $obj ) {
			$label = $obj['label'];
			$icon  = $obj['icon'];

			echo '<li class="' . ( $active ? 'wp-tab-active' : '' ) . '"><a href="#bbpc-meta-' . $tab . '">';
			echo '<span aria-hidden="true" aria-labelledby="bbpc-topic-attachments-metatab-' . $tab . '" class="dashicons dashicons-' . $icon . '" title="' . $label . '"></span>';
			echo '<span id="bbpc-topic-attachments-metatab-' . $tab . '" class="d4plib-metatab-label">' . $label . '</span>';
			echo '</a></li>';

			$active = false;
		}

		?>
	</ul>
	<?php

	$active = true;
	foreach ( $tabs as $tab => $label ) {
		echo '<div id="bbpc-meta-' . $tab . '" class="wp-tab-panel ' . ( $active ? 'tabs-panel-active' : 'tabs-panel-inactive' ) . '">';

		do_action( 'bbpc_admin_toolbox_topic_attachments_meta_content_' . $tab, $post_ID );

		echo '</div>';

		$active = false;
	}

	?>
</div>
<?php
require_once BBPC_PATH . 'forms/dialogs/metabox.php';
