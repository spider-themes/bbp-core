<?php

$_templates = apply_filters( 'gdbbx-widget-newposts-templates', array(
	'gdbbx-widget-newposts.php' => __( "Default post layout", "bbp-core" ) . ' [' . 'gdbbx-widget-newposts.php' . ']'
) );

?>

<h4><?php _e( "Display Template", "bbp-core" ); ?></h4>
<table>
    <tbody>
    <tr>
        <td class="cell-singular">
            <label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( "Select template", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_templates, array( 'id'       => $this->get_field_id( 'template' ),
			                                             'class'    => 'widefat',
			                                             'name'     => $this->get_field_name( 'template' ),
			                                             'selected' => $instance['template']
			) ); ?>
        </td>
    </tr>
    </tbody>
</table>

<h4><?php _e( "Information to show", "bbp-core" ); ?></h4>
<table>
    <tbody>
    <tr>
        <td class="cell-left">
            <label for="<?php echo $this->get_field_id( 'display_thumbnail' ); ?>"><?php _e( "Show Thumbnail (if available)", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array( 'id'       => $this->get_field_id( 'display_thumbnail' ),
			                                            'class'    => 'widefat',
			                                            'name'     => $this->get_field_name( 'display_thumbnail' ),
			                                            'selected' => $instance['display_thumbnail']
			) ); ?>
        </td>
        <td class="cell-right">
            <label for="<?php echo $this->get_field_id( 'display_forum' ); ?>"><?php _e( "Show forum", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array( 'id'       => $this->get_field_id( 'display_forum' ),
			                                            'class'    => 'widefat',
			                                            'name'     => $this->get_field_name( 'display_forum' ),
			                                            'selected' => $instance['display_forum']
			) ); ?>
        </td>
    </tr>
    <tr>
        <td class="cell-left">
            <label for="<?php echo $this->get_field_id( 'display_date' ); ?>"><?php _e( "Show date", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array( 'id'       => $this->get_field_id( 'display_date' ),
			                                            'class'    => 'widefat',
			                                            'name'     => $this->get_field_name( 'display_date' ),
			                                            'selected' => $instance['display_date']
			) ); ?>
        </td>
        <td class="cell-right">
            <label for="<?php echo $this->get_field_id( 'display_tags' ); ?>"><?php _e( "List tags", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array( 'id'       => $this->get_field_id( 'display_tags' ),
			                                            'class'    => 'widefat',
			                                            'name'     => $this->get_field_name( 'display_tags' ),
			                                            'selected' => $instance['display_tags']
			) ); ?>
        </td>
    </tr>
    <tr>
        <td class="cell-left">
            <label for="<?php echo $this->get_field_id( 'display_author' ); ?>"><?php _e( "Show author", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array( 'id'       => $this->get_field_id( 'display_author' ),
			                                            'class'    => 'widefat',
			                                            'name'     => $this->get_field_name( 'display_author' ),
			                                            'selected' => $instance['display_author']
			) ); ?>
        </td>
        <td class="cell-right">
            <label for="<?php echo $this->get_field_id( 'display_author_avatar' ); ?>"><?php _e( "With avatar image", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array( 'id'       => $this->get_field_id( 'display_author_avatar' ),
			                                            'class'    => 'widefat',
			                                            'name'     => $this->get_field_name( 'display_author_avatar' ),
			                                            'selected' => $instance['display_author_avatar']
			) ); ?>
        </td>
    </tr>
    <tr>
        <td class="cell-left">
            <label for="<?php echo $this->get_field_id( 'display_prefixes' ); ?>"><?php _e( "Show prefixes", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array( 'id'       => $this->get_field_id( 'display_prefixes' ),
			                                            'class'    => 'widefat',
			                                            'name'     => $this->get_field_name( 'display_prefixes' ),
			                                            'selected' => $instance['display_prefixes']
			) ); ?>
        </td>
        <td class="cell-right">
            <label><?php _e( "About prefixes", "bbp-core" ); ?>:</label>
			<?php _e( "Implemented with GD Topic Prefix Pro, and that plugin is required for this.", "bbp-core" ); ?>
        </td>
    </tr>
    </tbody>
</table>
