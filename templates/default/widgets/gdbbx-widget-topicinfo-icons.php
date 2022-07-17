<div class="bbpc-widget-the-info-table">
	<table>
		<tbody>
			<?php

			foreach ( $results as $code => $item ) {
				echo '<tr class="' . $code . '"><th><i class="bbpc-icon bbpc-fw bbpc-icon-' . $item['icon'] . '"></i> ' . $item['label'] . '</th><td>' . $item['value'] . '</td></tr>';
			}

			?>
		</tbody>
	</table>
</div>
