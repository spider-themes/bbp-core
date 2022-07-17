<div class="bbpc-widget-the-info-dl">
	<dl>
		<?php

		foreach ( $instance['stats'] as $stat ) {
			echo '<dt class="bbpc-stat-' . $stat . ' bbpc-stat-item-label">' . $elements[ $stat ] . '</dt>';
			echo '<dd class="bbpc-stat-' . $stat . ' bbpc-stat-item-value">' . $statistics[ $stat ] . '</dd>';
		}

		?>
	</dl>
</div>
