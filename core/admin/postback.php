<?php

use SpiderDevs\Plugin\BBPC\Tasks\Cleanup;
use SpiderDevs\Plugin\BBPC\Tasks\Recalculations;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class bbpc_admin_postback {
	public function __construct() {
		$page = $_POST['option_page'] ?? false;

		if ( $page !== false ) {
			if ( $page == 'bbp-core-tools' ) {
				$this->tools();
			}

			if ( $page == 'bbp-core-settings' ) {
				$this->settings();
			}

			if ( $page == 'bbp-core-bbcodes' ) {
				$this->bbcodes();
			}

			if ( $page == 'bbp-core-wizard' ) {
				bbpc_wizard()->panel_postback();
			}

			do_action( 'bbpc_admin_postback_handler', $page );
		}
	}

	private function tools() {
		check_admin_referer( 'bbp-core-tools-options' );

		$post   = $_POST['bbpctools'];
		$action = $post['panel'];

		$url = 'admin.php?page=bbp-core-tools&panel=' . $action;

		$message = 'nothing';

		if ( $action == 'remove' ) {
			$remove = isset( $post['remove'] ) ? (array) $post['remove'] : [];

			if ( ! empty( $remove ) ) {
				$message = 'removed';

				if ( isset( $remove['settings'] ) && ! empty( $remove['settings'] ) ) {
					$settings = $remove['settings'];

					if ( isset( $settings['all'] ) && $settings['all'] == 'on' ) {
						bbpc()->remove_all_plugin_settings();
					} else {
						$_remove = [];

						if ( isset( $settings['settings'] ) && $settings['settings'] == 'on' ) {
							$_remove[] = 'settings';
						}

						if ( isset( $settings['features'] ) && $settings['features'] == 'on' ) {
							$_remove[] = 'load';
							$_remove[] = 'features';
						}

						if ( isset( $settings['bbcodes'] ) && $settings['bbcodes'] == 'on' ) {
							$_remove[] = 'bbcodes';
						}

						if ( isset( $settings['online'] ) && $settings['online'] == 'on' ) {
							$_remove[] = 'online';
						}

						if ( isset( $settings['widgets'] ) && $settings['widgets'] == 'on' ) {
							$_remove[] = 'widgets';
						}

						if ( ! empty( $_remove ) ) {
							bbpc()->remove_selected_settings( $_remove );
						}
					}
				}

				if ( isset( $remove['forums'] ) && $remove['forums'] == 'on' ) {
					bbpc()->remove_forums_settings();
				}

				if ( isset( $remove['tracking'] ) && $remove['tracking'] == 'on' ) {
					bbpc()->remove_tracking_settings();
				}

				if ( isset( $remove['signature'] ) && $remove['signature'] == 'on' ) {
					bbpc()->remove_signature_settings();
				}

				if ( isset( $remove['cron'] ) && $remove['cron'] == 'on' ) {
					d4p_remove_cron( 'bbpc_cron_daily_maintenance_job' );
				}

				if ( isset( $remove['drop'] ) && $remove['drop'] == 'on' ) {
					require_once BBPC_PATH . 'core/admin/install.php';

					bbpc_drop_database_tables();

					if ( ! isset( $remove['disable'] ) ) {
						bbpc()->mark_for_update();
					}
				} elseif ( isset( $remove['truncate'] ) && $remove['truncate'] == 'on' ) {
					require_once BBPC_PATH . 'core/admin/install.php';

					bbpc_truncate_database_tables();
				}

				if ( isset( $remove['disable'] ) && $remove['disable'] == 'on' ) {
					deactivate_plugins( 'gd-bbpress-toolbox/gd-bbpress-toolbox.php', false, false );

					wp_redirect( admin_url( 'plugins.php' ) );
					exit;
				}
			}
		} elseif ( $action == 'removeips' ) {
			if ( isset( $post['removeips']['remove'] ) && $post['removeips']['remove'] == 'on' ) {
				$_ips_count = Cleanup::instance()->delete_author_ips_from_postmeta();

				if ( $_ips_count > 0 ) {
					$message = 'ips-removed&ips=' . $_ips_count;
				}
			}
		} elseif ( $action == 'cleanup' ) {
			if ( isset( $post['cleanup']['thanks'] ) && $post['cleanup']['thanks'] == 'on' ) {
				$_thanks_count = Cleanup::instance()->delete_thanks_for_missing_posts();

				if ( $_thanks_count > 0 ) {
					$message = 'cleanup-thanks&thanks=' . $_thanks_count;
				}
			}
		} elseif ( $action == 'recalc' ) {
			if ( isset( $post['recalculate']['subforums_count'] ) && $post['recalculate']['subforums_count'] == 'on' ) {
				Recalculations::instance()->sub_forums_counts();

				$message = 'completed';
			}
		} elseif ( $action == 'close' ) {
			$_topics_closed = 0;

			if ( isset( $post['close']['inactive'] ) && $post['close']['inactive'] == 'on' ) {
				$days = intval( $post['close']['inactivity'] );

				if ( $days > 0 ) {
					$_topics_closed += bbpc_db()->close_inactive_topics( $days );
				}
			}

			if ( isset( $post['close']['old'] ) && $post['close']['old'] == 'on' ) {
				$days = intval( $post['close']['age'] );

				if ( $days > 0 ) {
					$_topics_closed += bbpc_db()->close_old_topics( $days );
				}
			}

			if ( $_topics_closed > 0 ) {
				$message = 'closed&topics=' . $_topics_closed;
			}
		} elseif ( $action == 'import' ) {
			if ( is_uploaded_file( $_FILES['import_file']['tmp_name'] ) ) {
				$data = file_get_contents( $_FILES['import_file']['tmp_name'] );
				$data = json_decode( $data, true );

				if ( ! is_array( $data ) ) {
					$message = 'invalid-import';
				} else {
					$import = $post['import'] ?? [];

					if ( empty( $import ) ) {
						$message = 'nothing-to-import';
					} else {
						$groups = [];

						if ( isset( $import['settings'] ) && $import['settings'] == 'on' ) {
							$groups[] = 'settings';
							$groups[] = 'bbpress';
							$groups[] = 'tools';
						}

						if ( isset( $import['features'] ) && $import['features'] == 'on' ) {
							$groups[] = 'load';
							$groups[] = 'features';
						}

						if ( isset( $import['attachments'] ) && $import['attachments'] == 'on' ) {
							$groups[] = 'attachments';
						}

						if ( isset( $import['online'] ) && $import['online'] == 'on' ) {
							$groups[] = 'online';
						}

						if ( isset( $import['seo'] ) && $import['seo'] == 'on' ) {
							$groups[] = 'seo';
						}

						if ( isset( $import['widgets'] ) && $import['widgets'] == 'on' ) {
							$groups[] = 'widgets';
						}

						if ( isset( $import['buddypress'] ) && $import['buddypress'] == 'on' ) {
							$groups[] = 'buddypress';
						}

						$message = bbpc()->import_from_secure_json( $data, $groups );
					}
				}
			}
		}

		wp_redirect( $url . '&message=' . $message );
		exit;
	}

	private function settings() {
		check_admin_referer( 'bbp-core-settings-options' );

		d4p_includes(
			[
				[
					'name'      => 'walkers',
					'directory' => 'admin',
				],
				[
					'name'      => 'settings',
					'directory' => 'admin',
				],
			],
			BBPC_D4PLIB
		);

		include BBPC_PATH . 'core/admin/internal.php';

		$_panel = bbpc_admin()->panel;
		if ( bbpc_admin()->page == 'features' && ( ! $_panel || $_panel == 'index' ) ) {
			$_panel = 'load';
		}

		$options  = new bbpc_admin_settings();
		$settings = $_panel == 'load' ? $options->features_load() : $options->settings( $_panel );

		$processor       = new d4pSettingsProcess( $settings );
		$processor->base = 'bbpcvalue';

		$data = $processor->process();

		foreach ( $data as $group => $values ) {
			foreach ( $values as $name => $value ) {
				bbpc()->set( $name, $value, $group );

				do_action( 'bbpc_save_settings_value', $group, $name, $value );
			}

			bbpc()->save( $group );
		}

		if ( bbpc_admin()->page == 'views' ) {
			wp_flush_rewrite_rules();
		}

		$url = 'admin.php?page=bbp-core-' . bbpc_admin()->page . '&panel=' . bbpc_admin()->panel;
		wp_redirect( $url . '&message=saved' );
		exit;
	}

	private function bbcodes() {
		check_admin_referer( 'bbp-core-bbcodes-options' );

		$data  = $_POST['bbpc'] ?? [];
		$roles = array_keys( bbpc_get_user_roles() );
		$old   = bbpc()->group_get( 'bbcodes', false );

		foreach ( array_keys( $old ) as $bbcode ) {
			$value = [
				'status'  => false,
				'toolbar' => false,
				'roles'   => [],
			];

			if ( isset( $data['bbcodes'][ $bbcode ] ) ) {
				$input = $data['bbcodes'][ $bbcode ];

				$value['status']  = isset( $input['status'] ) && $input['status'] == 'on';
				$value['toolbar'] = isset( $input['toolbar'] ) && $input['toolbar'] == 'on';
				$value['visitor'] = isset( $input['visitor'] ) && $input['visitor'] == 'on';

				if ( isset( $input['role'] ) && is_array( $input['role'] ) && ! empty( $input['role'] ) ) {
					foreach ( $input['role'] as $role => $val ) {
						if ( $val == 'on' && in_array( $role, $roles ) ) {
							$value['roles'][] = $role;
						}
					}
				}
			}

			bbpc()->set( $bbcode, $value, 'bbcodes' );
			bbpc()->save( 'bbcodes' );
		}

		wp_redirect( 'admin.php?page=bbp-core-bbcodes&message=saved' );
		exit;
	}
}
