<?php

// Define the expected items
$expected_items = [ 'open', 'closed', 'hidden', 'no_reply', 'all', 'trash' ];

// Get the $filter_set, with a default value if it's not set
$filter_set = bbpc_get_opt('filter_buttons') ?? [];

// Check if all expected items are in the $filter_set
$are_all_available = empty(array_diff($expected_items, $filter_set)) ? 'all-available' : '';

?>

<div class="easydocs-filter-container">
	<ul class="single-item-filter <?php echo esc_attr($are_all_available) ?>">
		<?php if ( in_array( 'all', $filter_set, true ) ) : ?>
			<li class="easydocs-btn" cookie-id="all-<?php echo esc_attr( $item ); ?>" data-filter="all">
			<span class="dashicons dashicons-editor-ul"></span>

				<?php esc_html_e( 'All topics', 'bbp-core' ); ?>
					<span class="filter-count-badge"> (<?php echo esc_html( $children->post_count ); ?>) </span>
			</li>
		<?php endif; ?>

		<?php if ( in_array( 'closed', $filter_set, true ) ) : ?>
			<li class="easydocs-btn" cookie-id="closed-<?php echo esc_attr( $item ); ?>" data-filter=".closed-topics">
			<span class="dashicons dashicons-dismiss"></span>
			<?php esc_html_e( 'Closed', 'bbp-core' ); ?>
			<span class="filter-count-badge"> (<?php echo esc_html( $count_closed ); ?>) </span>
			</li>
		<?php endif; ?>

		<?php if ( in_array( 'open', $filter_set, true ) ) : ?>
			<li class="easydocs-btn" cookie-id="open-<?php echo esc_attr( $item ); ?>" data-filter=".open-topics">
			<span class="dashicons dashicons-email"></span>
				<?php esc_html_e( 'Open', 'bbp-core' ); ?>
				<span class="filter-count-badge"> (<?php echo esc_html( $count_open ); ?>) </span>
			</li>
		<?php endif; ?>

		<?php if ( in_array( 'hidden', $filter_set, true ) ) : ?>
		<li class="easydocs-btn" cookie-id="hidden-<?php echo esc_attr( $item ); ?>" data-filter=".hidden-topics">
		<span class="dashicons dashicons-hidden"></span>
            <?php esc_html_e( 'Hidden', 'bbp-core' ); ?>
            <span class="filter-count-badge"> (<?php echo esc_html( $count_hidden ); ?>) </span>
		</li>
		<?php endif; ?>

		<?php if ( in_array( 'no_reply', $filter_set, true ) ) : ?>
		<li class="easydocs-btn" cookie-id="no_reply-<?php echo esc_attr( $item ); ?>" data-filter=".no-reply">
		<span class="dashicons dashicons-backup"></span>
			<?php esc_html_e( 'No Reply', 'bbp-core' ); ?>
			<span class="filter-count-badge"> (<?php echo esc_html( $count_no_reply ); ?>) </span>
		</li>
		<?php endif; ?>

		<?php if ( in_array( 'solved', $filter_set, true ) ) : ?>
		<li class="easydocs-btn" cookie-id="solved-<?php echo esc_attr( $item ); ?>" data-filter=".solved">
            <span class="dashicons dashicons-yes-alt"></span>
			<?php esc_html_e( 'Solved', 'bbp-core' ); ?>
			<span class="filter-count-badge"> (<?php echo esc_html( $count_solved ); ?>) </span>
		</li>
		<?php endif; ?>

		<?php if ( in_array( 'unsolved', $filter_set, true ) ) : ?>
		<li class="easydocs-btn" cookie-id="unsolved-<?php echo esc_attr( $item ); ?>" data-filter=".unsolved">
            <span class="dashicons dashicons-info-outline"></span>
			<?php esc_html_e( 'Unsolved', 'bbp-core' ); ?>
			<span class="filter-count-badge"> (<?php echo esc_html( $count_unsolved ); ?>) </span>
		</li>
		<?php endif; ?>

	</ul>

    <?php if ( in_array( 'trash', $filter_set, true ) ) : ?>
		<div class="easydocs-btn-sm bbpc-trash-filter">
			<a href="<?php echo admin_url( 'edit.php?post_status=trash&post_type=topic' ); ?>">
				<svg width="15px" height="15px" viewBox="0 0 24 24" id="magicoon-Regular" xmlns="http://www.w3.org/2000/svg"><defs></defs><g id="trash-Regular"><path id="trash-Regular-2" data-name="trash-Regular" class="cls-1" d="M21,5.25H17.441A1.251,1.251,0,0,1,16.255,4.4l-.316-.95a1.746,1.746,0,0,0-1.66-1.2H9.721a1.745,1.745,0,0,0-1.66,1.2l-.316.948a1.251,1.251,0,0,1-1.186.855H3a.75.75,0,0,0,0,1.5H4.3l.767,11.5a3.76,3.76,0,0,0,3.742,3.5h6.386a3.76,3.76,0,0,0,3.742-3.5L19.7,6.75H21a.75.75,0,0,0,0-1.5ZM9.483,3.921a.252.252,0,0,1,.238-.171h4.558a.252.252,0,0,1,.238.17l.316.95a2.777,2.777,0,0,0,.161.38H9.006a2.737,2.737,0,0,0,.161-.381ZM17.438,18.15a2.255,2.255,0,0,1-2.245,2.1H8.807a2.255,2.255,0,0,1-2.245-2.1L5.8,6.75h.757a2.783,2.783,0,0,0,.317-.025A.736.736,0,0,0,7,6.75H17a.736.736,0,0,0,.124-.025,2.783,2.783,0,0,0,.317.025H18.2ZM14.75,11v5a.75.75,0,0,1-1.5,0V11a.75.75,0,0,1,1.5,0Zm-4,0v5a.75.75,0,0,1-1.5,0V11a.75.75,0,0,1,1.5,0Z"/></g></svg>
				<?php esc_html_e( 'Trashed', 'bbp-core' ); ?>
				<span class="filter-count-badge"> (<?php echo esc_html( $count_trash ); ?>) </span>
			</a>
		</div>
    <?php endif; ?>
</div>
