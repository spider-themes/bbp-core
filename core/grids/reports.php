<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class bbpc_grid_reports extends d4p_grid {
	public $_sanitize_orderby_fields = [ 'a.action_id', 'a.post_id', 'a.logged' ];
	public $_table_class_name        = 'bbpc-grid-report';

	public function __construct( $args = [] ) {
		parent::__construct(
			[
				'singular' => 'report',
				'plural'   => 'reports',
				'ajax'     => false,
			]
		);
	}

	private function _self( $args, $getback = false ) {
		$url = 'admin.php?page=bbp-core-reported-posts&' . $args;

		if ( $getback ) {
			$url .= '&bbpc_handler=getback';
			$url .= '&_wpnonce=' . wp_create_nonce( 'bbp-core-report' );
			$url .= '&_wp_http_referer=' . wp_unslash( $_SERVER['REQUEST_URI'] );
		}

		return self_admin_url( $url );
	}

	protected function extra_tablenav( $which ) {
		if ( $which == 'top' ) {
			$status = [
				''        => __( 'All Reports', 'bbp-core' ),
				'closed'  => __( 'Closed Reports', 'bbp-core' ),
				'waiting' => __( 'Reports Waiting', 'bbp-core' ),
				'deleted' => __( 'Posts Deleted', 'bbp-core' ),
			];

			$reported = [
				''                        => __( 'For Topics And Replies', 'bbp-core' ),
				bbp_get_topic_post_type() => __( 'For Topics Only', 'bbp-core' ),
				bbp_get_reply_post_type() => __( 'For Replies Only', 'bbp-core' ),
			];

			$_sel_type   = isset( $_GET['filter-type'] ) && ! empty( $_GET['filter-type'] ) ? d4p_sanitize_slug( $_GET['filter-type'] ) : '';
			$_sel_status = isset( $_GET['filter-status'] ) && ! empty( $_GET['filter-status'] ) ? d4p_sanitize_slug( $_GET['filter-status'] ) : '';

			echo '<div class="alignleft actions">';
			d4p_render_select(
				$status,
				[
					'selected' => $_sel_status,
					'name'     => 'filter-status',
				]
			);
			d4p_render_select(
				$reported,
				[
					'selected' => $_sel_type,
					'name'     => 'filter-type',
				]
			);
			submit_button( __( 'Filter', 'bbp-core' ), 'button', false, false, [ 'id' => 'bbpc-reports-submit' ] );
			echo '</div>';
		}
	}

	public function single_row( $item ) {
		$classes = $this->get_row_classes( $item );

		$classes[] = 'bbpc-report-status-' . $item->meta->status;

		echo '<tr class="' . join( ' ', $classes ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	public function rows_per_page() {
		$user     = get_current_user_id();
		$per_page = get_user_meta( $user, 'bbpc_rows_reports_per_page', true );

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = 25;
		}

		return $per_page;
	}

	public function get_columns() {
		return [
			'id'       => __( 'ID', 'bbp-core' ),
			'type'     => '',
			'post'     => __( 'Topic / Reply', 'bbp-core' ),
			'reporter' => __( 'Reported by', 'bbp-core' ),
			'report'   => __( 'Report', 'bbp-core' ),
			'status'   => __( 'Status', 'bbp-core' ),
			'date'     => __( 'Reported', 'bbp-core' ),
			'forum'    => __( 'Forum', 'bbp-core' ),
		];
	}

	protected function get_sortable_columns() {
		return [
			'id'   => [ 'a.action_id', false ],
			'post' => [ 'a.post_id', false ],
			'date' => [ 'a.logged', false ],
		];
	}

	public function column_id( $item ) {
		return $item->action_id;
	}

	public function column_date( $item ) {
		return mysql2date( 'Y.m.d', $item->logged ) . '<br/>@ ' . mysql2date( 'H:m:s', $item->logged );
	}

	public function column_post( $item ) {
		$post = $item->post_id;

		$title = '';
		$url   = '';

		if ( bbp_is_reply( $post ) ) {
			$title = bbp_get_reply_title( $post );
			$url   = bbp_get_reply_url( $post );
		} elseif ( bbp_is_topic( $post ) ) {
			$title = bbp_get_topic_title( $post );
			$url   = get_permalink( $post );
		}

		if ( $url == '' ) {
			return '&minus;';
		} else {
			$actions = [
				'visit' => sprintf( '<a href="%s">%s</a>', $url, __( 'Visit', 'bbp-core' ) ),
			];

			return $title . $this->row_actions( $actions );
		}
	}

	public function column_type( $item ) {
		return ucfirst( $item->meta->type );
	}

	public function column_reporter( $item ) {
		$user = get_user_by( 'id', $item->user_id );

		if ( $user === false ) {
			return __( 'User not found', 'bbp-core' );
		} else {
			$actions = [
				'profile' => sprintf( '<a href="%s">%s</a>', bbp_get_user_profile_url( $item->user_id ), __( 'Profile', 'bbp-core' ) ),
			];

			if ( current_user_can( 'edit_users' ) ) {
				$actions['edit'] = sprintf( '<a href="%s">%s</a>', get_edit_user_link( $item->user_id ), __( 'Edit', 'bbp-core' ) );
			}

			return $user->display_name . $this->row_actions( $actions );
		}
	}

	public function column_report( $item ) {
		return $item->meta->content;
	}

	public function column_status( $item ) {
		$actions = [];

		if ( $item->meta->status == 'waiting' ) {
			$actions['close'] = sprintf( '<a href="%s">%s</a>', $this->_self( 'report=' . $item->action_id . '&single-action=close-report', true ), __( 'Close', 'bbp-core' ) );
		}

		return ucfirst( $item->meta->status ) . $this->row_actions( $actions );
	}

	public function column_forum( $item ) {
		if ( $item->post_type == bbp_get_topic_post_type() ) {
			$forum_id = bbp_get_topic_forum_id( $item->post_id );
		} else {
			$forum_id = bbp_get_reply_forum_id( $item->post_id );
		}

		if ( $forum_id == 0 ) {
			return '&minus;';
		} else {
			$actions = [
				'visit'  => sprintf( '<a href="%s">%s</a>', get_permalink( $forum_id ), __( 'Visit', 'bbp-core' ) ),
				'topics' => sprintf( '<a href="edit.php?post_type=topic&bbp_forum_id=%s">%s</a>', $forum_id, __( 'Topics', 'bbp-core' ) ),
			];

			return bbp_get_forum_title( $forum_id ) . $this->row_actions( $actions );
		}
	}

	public function prepare_items() {
		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];

		$per_page = $this->rows_per_page();

		$_sel_type   = isset( $_GET['filter-type'] ) && ! empty( $_GET['filter-type'] ) ? "'" . d4p_sanitize_slug( $_GET['filter-type'] ) . "'" : "'" . bbp_get_topic_post_type() . "', '" . bbp_get_reply_post_type() . "'";
		$_sel_status = isset( $_GET['filter-status'] ) && ! empty( $_GET['filter-status'] ) ? d4p_sanitize_slug( $_GET['filter-status'] ) : '';

		$query_where   = [ "a.action = 'report'" ];
		$query_reports = 'SELECT SQL_CALC_FOUND_ROWS a.*, p.post_date, p.post_author, p.post_type, p.post_title FROM ' . bbpc_db()->actions . ' a LEFT JOIN ' . bbpc_db()->wpdb()->posts . ' p ON p.ID = a.post_id AND p.post_type in (' . $_sel_type . ')';

		if ( ! empty( $_sel_status ) ) {
			if ( $_sel_status == 'closed' || $_sel_status == 'waiting' ) {
				$query_reports .= ' INNER JOIN ' . bbpc_db()->actionmeta . " m ON m.action_id = a.action_id AND m.meta_key = 'status'";
				$query_where[]  = "m.meta_value = '" . $_sel_status . "'";
			} elseif ( $_sel_status == 'deleted' ) {
				$query_where[] = 'p.ID IS NULL';
			}
		}

		if ( ! empty( $query_where ) ) {
			$query_reports .= ' WHERE ' . join( ' AND ', $query_where );
		}

		$orderby = ! empty( $_GET['orderby'] ) ? $this->sanitize_field( 'orderby', $_GET['orderby'], 'p.ID' ) : 'p.ID';
		$order   = ! empty( $_GET['order'] ) ? $this->sanitize_field( 'order', $_GET['order'], 'DESC' ) : 'DESC';

		$query_reports .= " ORDER BY $orderby $order";

		$paged = ! empty( $_GET['paged'] ) ? absint( $_GET['paged'] ) : '';
		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}

		$offset         = intval( ( $paged - 1 ) * $per_page );
		$query_reports .= " LIMIT $offset, $per_page";

		$this->items = bbpc_db()->run_and_index( $query_reports, 'action_id' );

		$total_rows = bbpc_db()->get_found_rows();

		$this->set_pagination_args(
			[
				'total_items' => $total_rows,
				'total_pages' => ceil( $total_rows / $per_page ),
				'per_page'    => $per_page,
			]
		);

		foreach ( array_keys( $this->items ) as $item ) {
			$this->items[ $item ]->meta = new stdClass();
		}

		$ids = bbpc_db()->pluck( $this->items, 'action_id' );

		if ( ! empty( $ids ) ) {
			$query_meta = 'SELECT * FROM ' . bbpc_db()->actionmeta . ' WHERE action_id in (' . join( ', ', $ids ) . ')';
			$metas      = bbpc_db()->run( $query_meta );

			foreach ( $metas as $meta ) {
				$this->items[ $meta->action_id ]->meta->{$meta->meta_key} = $meta->meta_value;
			}
		}

		foreach ( $this->items as &$item ) {
			if ( $item->meta->status == 'waiting' && empty( $item->post_type ) ) {
				$item->meta->status = 'deleted';

				bbpc_db()->report_status( $item->action_id, 'deleted' );
			}
		}
	}
}
