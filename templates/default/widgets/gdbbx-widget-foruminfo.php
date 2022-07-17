<div class="bbpc-widget-the-info-table">
	<table>
		<tbody>
			<?php

			foreach ( $results as $code => $item ) {
				if ( empty( $item['label'] ) ) {
					echo '<tr class="' . $code . '"><td colspan="2">' . $item['value'] . '</td></tr>';
				} else {
					echo '<tr class="' . $code . '"><th>' . $item['label'] . '</th><td>' . $item['value'] . '</td></tr>';
				}
			}

			?>
		</tbody>
	</table>
</div>
