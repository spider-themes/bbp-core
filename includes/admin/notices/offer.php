<?php
/**
 ** Give Notice
 **/
function bbpc_offer_notice() {
	if ( is_user_logged_in() ) {
		$user_id   = get_current_user_id();
		$dismissed = get_user_meta( $user_id, 'bbpc_offer_dismissed', true );

		if ( '1' === $dismissed ) {
			return; // Don't show the notice if it has been dismissed by this user
		}
	}
	?>
    <div class="bbpc-offer-wrap">
        <div class="bbpc-offer">
            <button class="dismiss-btn">&times;</button>
            <div class="bbpc-col">
                <span class="dashicons dashicons-megaphone"></span>
                <div class="bbpc-col-text">
                    <p><strong><?php esc_html_e('Upgrade to Bbp Core Pro', 'bbp-core'); ?></strong></p>
                    <p><?php esc_html_e('Massive discount', 'bbp-core'); ?></p>
                </div>
            </div>
            <div class="bbpc-col">
                <img src="<?php echo esc_url( BBPC_IMG . 'icon/coupon.svg' ); ?>"
                     alt="<?php echo esc_attr_x( 'Coupon', 'coupon', 'bbp-core' ); ?>" class="coupon-icon">
                <div class="bbpc-col-text">
                    <p><strong><?php esc_html_e('Up to 40% Off', 'bbp-core'); ?></strong></p>
                    <p><?php esc_html_e('This is limited time offer!', 'bbp-core'); ?></p>
                </div>
            </div>
            <div class="bbpc-col">
                <img src="<?php echo esc_url( BBPC_IMG . 'icon/cursor-hand.svg' ); ?>"
                     alt="<?php echo esc_attr_x( 'Coupon', 'coupon', 'bbp-core' ); ?>" class="coupon-icon">
                <div class="bbpc-col-text">
                    <p><strong><?php esc_html_e('Grab the deal', 'bbp-core'); ?></strong></p>
                    <p><?php esc_html_e('Before it expires!', 'bbp-core'); ?></p>
                </div>
            </div>
            <div class="bbpc-col">
                <div class="bbpc-col-box">
                    <label for="coupon"><?php esc_html_e('Coupon Code:', 'bbp-core'); ?></label>
                    <div class="coupon-container">
                        <input type="text" value="DASH40" id="coupon" class="coupon" readonly>
                        <span class="copy-message"><?php esc_html_e('Coupon copied.', 'bbp-core'); ?></span>
                        <button class="copy-btn"><?php esc_html_e('Copy', 'bbp-core'); ?></button>
                    </div>
                </div>
            </div>
            <div class="bbpc-col">
                <a href="https://spider-themes.net/bbp-core/pricing/" class="buy-btn" target="_blank"><?php esc_html_e('Claim Discount', 'bbp-core'); ?></a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyBtn = document.querySelector('.copy-btn');
            const couponInput = document.querySelector('.coupon');
            const copyMessage = document.querySelector('.copy-message');
            const dismissBtn = document.querySelector('.dismiss-btn');
            const offerWrap = document.querySelector('.bbpc-offer-wrap');

            copyBtn.addEventListener('click', function () {
                navigator.clipboard.writeText(couponInput.value).then(() => {
                    copyMessage.style.display = 'inline';
                    setTimeout(() => {
                        copyMessage.style.display = 'none';
                    }, 1000);
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                });
            });

            dismissBtn.addEventListener('click', function () {
                offerWrap.style.display = 'none';

                // Make an AJAX request to save the dismissal for the logged-in user
                fetch('<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=bbpc_dismiss_offer_notice&nonce=<?php echo esc_js( wp_create_nonce( 'bbpc-dismiss-notice' ) ); ?>'
                }).then(response => response.json()).then(data => {
                    if (!data.success) {
                        console.error('Error dismissing the notice:', data.message);
                    }
                }).catch(err => {
                    console.error('Error dismissing the offer:', err);
                });

            });
        });
    </script>
	<?php
}

add_action( 'wp_ajax_bbpc_dismiss_offer_notice', 'bbpc_dismiss_offer_notice' );

function bbpc_dismiss_offer_notice() {
	check_ajax_referer( 'bbpc-dismiss-notice', 'nonce' );

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
		update_user_meta( $user_id, 'bbpc_offer_dismissed', '1' );

		wp_send_json_success( array( 'message' => esc_html__('Notice dismissed for this user.', 'bbp-core') ) );
	} else {
		wp_send_json_error( array( 'message' => esc_html__('User not logged in.', 'bbp-core') ) );
	}

	wp_die();
}
