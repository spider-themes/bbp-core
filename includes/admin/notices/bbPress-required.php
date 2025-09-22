<?php
/**
 * BBP Core Installer - No Class Version
 */

if ( ! function_exists( 'bbpcore_admin_notice' ) ) :

    /**
     * Show admin notice for bbPress requirement
     */
    function bbpcore_admin_notice() {
        if ( class_exists( 'bbPress' ) ) {
            return; // bbPress is active, no notice needed
        }

        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $has_installed = get_plugins();
        $button_text   = isset( $has_installed['bbpress/bbpress.php'] )
            ? esc_html__( 'Activate Now!', 'bbp-core' )
            : esc_html__( 'Install Now!', 'bbp-core' );
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php
                printf(
                    '<strong>%1$s</strong> %2$s <strong>%3$s</strong> %4$s',
                    esc_html__( 'BBP Core', 'bbp-core' ),
                    esc_html__( 'requires', 'bbp-core' ),
                    esc_html__( 'bbPress', 'bbp-core' ),
                    esc_html__( 'plugin to be installed and active. Please install or activate it now!', 'bbp-core' )
                );
                ?>
            </p>
            <p>
                <button id="bbp-core-install-core" class="button button-primary">
                    <?php echo esc_html( $button_text ); ?>
                </button>
            </p>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                $('#bbp-core-install-core').on('click', function (e) {
                    e.preventDefault();
                    var self = $(this);
                    self.addClass('install-now updating-message');
                    self.text('<?php echo esc_js( __( 'Installing...', 'bbp-core' ) ); ?>');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'bbpcore_install_core',
                            _wpnonce: '<?php echo esc_js( wp_create_nonce( 'bbpcore_install_core' ) ); ?>',
                        },
                        success: function (response) {
                            if (response.success) {
                                self.text('<?php echo esc_js( __( 'Installed', 'bbp-core' ) ); ?>');
                                window.location.reload();
                            } else {
                                alert(response.data || '<?php echo esc_js( __( 'Something went wrong.', 'bbp-core' ) ); ?>');
                                self.text('<?php echo esc_js( __( 'Failed', 'bbp-core' ) ); ?>');
                            }
                        },
                        error: function (xhr) {
                            alert('AJAX Error: ' + xhr.responseText);
                            self.removeClass('install-now updating-message');
                        },
                        complete: function () {
                            self.attr('disabled', 'disabled');
                            self.removeClass('install-now updating-message');
                        }
                    });
                });
            });
        </script>
        <?php
    }
    add_action( 'admin_notices', 'bbpcore_admin_notice' );

endif;

if ( ! function_exists( 'bbpcore_install_core' ) ) :

    /**
     * Handle AJAX install/activation of bbPress
     */
    function bbpcore_install_core() {
        check_ajax_referer( 'bbpcore_install_core' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You don\'t have permission to install plugins.', 'bbp-core' ) );
        }

        $result = bbpcore_install_plugin( 'bbpress', 'bbpress.php' );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        wp_send_json_success();
    }
    add_action( 'wp_ajax_bbpcore_install_core', 'bbpcore_install_core' );

endif;

if ( ! function_exists( 'bbpcore_install_plugin' ) ) :

    /**
     * Install and activate a plugin
     */
    function bbpcore_install_plugin( $slug, $file ) {
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $plugin_basename = $slug . '/' . $file;

        // Already installed? Just activate
        if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_basename ) ) {
            return activate_plugin( $plugin_basename );
        }

        // Download from WP.org
        $api = plugins_api( 'plugin_information', array(
            'slug'   => $slug,
            'fields' => array( 'sections' => false ),
        ) );

        if ( is_wp_error( $api ) ) {
            return $api;
        }

        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result   = $upgrader->install( $api->download_link );

        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Activate after install
        return activate_plugin( $plugin_basename );
    }

endif;