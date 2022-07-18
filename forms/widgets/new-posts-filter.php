<?php

$_sel_period = array(
	'last_year'      => __( "Last year", "bbp-core" ),
	'last_6months'   => __( "Last 6 months", "bbp-core" ),
	'last_3months'   => __( "Last 3 months", "bbp-core" ),
	'last_month'     => __( "Last month", "bbp-core" ),
	'last_fortnight' => __( "Last two weeks", "bbp-core" ),
	'last_week'      => __( "Last week", "bbp-core" ),
	'last_day'       => __( "Last day", "bbp-core" ),
	'last_hour'      => __( "Last Hour", "bbp-core" )
);
$_sel_scope  = array(
	'topic,reply' => __( "Topics and Replies", "bbp-core" ),
	'topic'       => __( "Topics only", "bbp-core" ),
	'reply'       => __( "Replies only", "bbp-core" )
);
$_sel_date   = array( 'yes' => __( "Yes", "bbp-core" ), 'no' => __( "No", "bbp-core" ) );

?>

<h4><?php _e( "Topics and Reply Filtering", "bbp-core" ); ?></h4>
<table>
    <tbody>
    <tr>
        <td class="cell-left">
            <label for="<?php echo $this->get_field_id( 'period' ); ?>"><?php _e( "Period to get posts", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_period, array(
				'id'       => $this->get_field_id( 'period' ),
				'class'    => 'widefat',
				'name'     => $this->get_field_name( 'period' ),
				'selected' => $instance['period']
			) ); ?>
        </td>
        <td class="cell-right">
            <label for="<?php echo $this->get_field_id( 'scope' ); ?>"><?php _e( "Get new posts from", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_scope, array(
				'id'       => $this->get_field_id( 'scope' ),
				'class'    => 'widefat',
				'name'     => $this->get_field_name( 'scope' ),
				'selected' => $instance['scope']
			) ); ?>
        </td>
    </tr>
    </tbody>
</table>

<h4><?php _e( "Forums", "bbp-core" ); ?></h4>
<table>
    <tbody>
    <tr>
        <td class="cell-singular">
            <label for="<?php echo $this->get_field_id( 'include_forums_ids' ); ?>"><?php _e( "Include forums by forum ID", "bbp-core" ); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'include_forums_ids' ); ?>" name="<?php echo $this->get_field_name( 'include_forums_ids' ); ?>" type="text" value="<?php echo join( ',', $instance['include_forums_ids'] ); ?>"/>
            <em>
				<?php _e( "Comma separated list of forum ID's.", "bbp-core" ); ?>
            </em>
        </td>
    </tr>
    <tr>
        <td class="cell-singular">
            <label for="<?php echo $this->get_field_id( 'exclude_forums_ids' ); ?>"><?php _e( "Exclude forums by forum ID", "bbp-core" ); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'exclude_forums_ids' ); ?>" name="<?php echo $this->get_field_name( 'exclude_forums_ids' ); ?>" type="text" value="<?php echo join( ',', $instance['exclude_forums_ids'] ); ?>"/>
            <em>
				<?php _e( "Comma separated list of forum ID's. This list will be used only if Include forums list is empty.", "bbp-core" ); ?>
            </em>
        </td>
    </tr>
    <tr>
        <td class="cell-singular">
            <label for="<?php echo $this->get_field_id( 'exclude_private' ); ?>"><?php _e( "Check user access rights", "bbp-core" ); ?>:</label>
			<?php d4p_render_select( $_sel_date, array(
				'id'       => $this->get_field_id( 'exclude_private' ),
				'class'    => 'widefat',
				'name'     => $this->get_field_name( 'exclude_private' ),
				'selected' => $instance['exclude_private']
			) ); ?>
            <em>
				<?php _e( "If you use this option, it will work only with widget cache disabled, so make sure to set Cache Period on the Global tab to '0'.", "bbp-core" ); ?>
            </em>
        </td>
    </tr>
    </tbody>
</table>

<h4><?php _e( "Additional", "bbp-core" ); ?></h4>
<table>
    <tbody>
    <tr>
        <td class="cell-singular">
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( "Limit list of topics", "bbp-core" ); ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" min="0" step="1" value="<?php echo $instance['limit']; ?>"/>
        </td>
    </tr>
    </tbody>
</table>
