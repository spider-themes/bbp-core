<?php

$_sel_mode = [
	'global'  => __( 'Global search through all forums', 'bbp-core' ),
	'current' => __( 'Search the current forum only', 'bbp-core' ),
];

?>

<h4><?php _e( 'Alternative Title', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-singular">
			<label for="<?php echo $this->get_field_id( 'title_current' ); ?>"><?php _e( "Title for 'Current' search mode", 'bbp-core' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title_current' ); ?>" name="<?php echo $this->get_field_name( 'title_current' ); ?>" type="text" value="<?php echo esc_attr( $instance['title_current'] ); ?>"/>
			<em>
				<?php _e( "This title will be used when using 'Current forum' search mode.", 'bbp-core' ); ?>
			</em>
		</td>
	</tr>
	</tbody>
</table>

<h4><?php _e( 'Search Mode', 'bbp-core' ); ?></h4>
<table>
	<tbody>
	<tr>
		<td class="cell-singular">
			<label for="<?php echo $this->get_field_id( 'search_mode' ); ?>"><?php _e( 'Select mode', 'bbp-core' ); ?>:</label>
			<?php
			d4p_render_select(
				$_sel_mode,
				[
					'id'       => $this->get_field_id( 'search_mode' ),
					'class'    => 'widefat',
					'name'     => $this->get_field_name( 'search_mode' ),
					'selected' => $instance['search_mode'],
				]
			);
			?>
			<em>
				<?php _e( "If the 'Current forum' search mode is active, and the forum can't be identified, search will revert to global mode.", 'bbp-core' ); ?>
			</em>
		</td>
	</tr>
	</tbody>
</table>
