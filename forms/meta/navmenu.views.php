<?php

$views  = [];
$_views = array_keys( bbp_get_views() );

foreach ( $_views as $name ) {
	$view = new stdClass();

	$view->classes   = [];
	$view->type      = $name;
	$view->object_id = $name;
	$view->title     = bbp_get_view_title( $name );
	$view->object    = 'bbx-view';

	$view->menu_item_parent = null;
	$view->url              = null;
	$view->xfn              = null;
	$view->db_id            = null;
	$view->target           = null;
	$view->attr_title       = null;

	$views[ $name ] = $view;
}

$walker = new Walker_Nav_Menu_Checklist( [] );

?>

<div id="bbx-view" class="posttypediv">
	<ul class="taxonomy-tabs add-menu-item-tabs" id="taxonomy-category-tabs">
		<li class="tabs"><?php _e( 'Registered Views', 'bbp-core' ); ?></li>
	</ul>
	<div id="tabs-panel-bbx-view" class="tabs-panel tabs-panel-active">
		<ul id="bbx-view-checklist" class="categorychecklist form-no-clear">
		<?php

			echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $views ), 0, (object) [ 'walker' => $walker ] );

		?>
		</ul>
	</div>
</div>
<p class="button-controls">
	<span class="add-to-menu">
		<input type="submit" class="button-secondary submit-add-to-menu" value="<?php esc_attr_e( 'Add to Menu', 'bbp-core' ); ?>" name="add-bbx-view-menu-item" id="submit-bbx-view" />
		<span class="spinner"></span>
	</span>
</p>
