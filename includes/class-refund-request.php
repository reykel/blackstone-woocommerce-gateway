<?php

if (!defined('ABSPATH')) {
    exit;
}
/*
    class WC_Refund_Request {
        public function __construct() {
            add_action('admin_enqueue_scripts', [$this, 'remove_refund_button']);
            add_action('admin_init', [$this, 'handle_custom_refund_request']);
        }

        public function remove_refund_button() {
            if (isset($_GET['post']) && get_post_type($_GET['post']) === 'shop_order') {
                add_action('woocommerce_order_item_add_action_buttons', [$this, 'add_custom_button_to_order_page'], 20);
            }
        }

        public function add_custom_button_to_order_page($order) {
            echo '<form method="POST" action="">';
            echo '<input type="hidden" name="custom_refund_order_id" value="' . esc_attr($order->get_id()) . '">';
            echo '<input type="hidden" name="custom_refund_nonce" value="' . wp_create_nonce('custom_refund_nonce') . '">';
            echo '<button type="submit" class="button custom-refund-button">Blackstone Refund</button>';
            echo '</form>';
        }

        public function handle_custom_refund_request() {
            if (isset($_POST['custom_refund_order_id']) && isset($_POST['custom_refund_nonce'])) {
                if (!wp_verify_nonce($_POST['custom_refund_nonce'], 'custom_refund_nonce')) {
                    wc_add_notice('Invalid request.', 'error');
                    return;
                }

                $order_id = intval($_POST['custom_refund_order_id']);
                if (!$order_id) {
                    wc_add_notice('Invalid Order ID.', 'error');
                    return;
                }

                $order = wc_get_order($order_id);
                if (!$order) {
                    wc_add_notice('Order not found.', 'error');
                    return;
                }

                $this->process_refund_request($order);
            }
        }

        private function process_refund_request($order) {
            $payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();
            $blackstone_gateway = $payment_gateways['bmspay_blackstone_payment'];

            $service_reference_number = get_post_meta($order->get_id(), '_bmspay_service_reference_number', true);

            $environment_url = ($blackstone_gateway->environment == 'yes')
                ? 'https://services.bmspay.com/testing/api/Transactions/DoRefund'
                : 'https://services.bmspay.com/api/Transactions/DoRefund';

            $data = array(
                'Amount' => $order->get_total(),
                'TrackData' => '',
                'UserTransactionNumber' => md5(uniqid($service_reference_number . $environment_url, true)),
                'ServiceTransactionNumber' => $service_reference_number,
                'SURI' => '',
                'AppKey' => $blackstone_gateway->environment == 'yes' ? "12345" : $blackstone_gateway->app_key,
                'AppType' => $blackstone_gateway->environment == 'yes' ? "1" : $blackstone_gateway->app_type,
                'mid' => $blackstone_gateway->environment == 'yes' ? "76074" : $blackstone_gateway->api_mid,
                'cid' => $blackstone_gateway->environment == 'yes' ? "260" : $blackstone_gateway->api_cid,
                'UserName' => $blackstone_gateway->environment == 'yes' ? "nicolas" : $blackstone_gateway->api_username,
                'Password' => $blackstone_gateway->environment == 'yes' ? "password1" : $blackstone_gateway->api_password,
                'IpAddress' => $_SERVER['REMOTE_ADDR']
            );

            error_log(print_r($environment_url, true));
            error_log(print_r($data, true));

            $response = wp_remote_post($environment_url, array(
                'method' => 'POST',
                'headers' => array('Content-Type' => 'application/json'),
                'body' => json_encode($data),
            ));

            $response_body = wp_remote_retrieve_body($response);
            $resp = json_decode($response_body, true);

            error_log(print_r($resp, true));

            if ($resp['ResponseCode'] == "200") {

                wc_create_refund(array(
                    'amount' => $order->get_total(),
                    'reason' => 'Refund processed via API.',
                    'order_id' => $order->get_id(),
                    'restock_items'  => true,
                ));

                $order->update_status('refunded');
                $order->add_order_note('Order refunded via API.');
                wp_redirect(admin_url('edit.php?post_type=shop_order'));
                exit; 
            } else {
                wc_add_notice('Refund request failed.', 'error');
            }
        }

    }

*/
/*
    class WC_Refund_Request {
        public function __construct() {
            add_action('admin_enqueue_scripts', [$this, 'remove_refund_button']);
            add_action('admin_init', [$this, 'handle_custom_refund_request']);
        }

        public function remove_refund_button() {
            if (isset($_GET['post']) && get_post_type($_GET['post']) === 'shop_order') {
                add_action('woocommerce_order_item_add_action_buttons', [$this, 'add_custom_button_to_order_page'], 20);
            }
        }

        public function add_custom_button_to_order_page($order) {
            // Check if the order has been refunded
            if ($order->has_status('refunded')) {
                echo '<p>This order has already been refunded.</p>';
                return;
            }

            // Display the custom refund button with a confirmation dialog
            echo '<form method="POST" action="" onsubmit="return confirm(\'Are you sure you want to process this refund?\');">';
            echo '<input type="hidden" name="custom_refund_order_id" value="' . esc_attr($order->get_id()) . '">';
            echo '<input type="hidden" name="custom_refund_nonce" value="' . wp_create_nonce('custom_refund_nonce') . '">';
            echo '<button type="submit" class="button custom-refund-button">Blackstone Refund</button>';
            echo '</form>';
        }

        public function handle_custom_refund_request() {
            if (isset($_POST['custom_refund_order_id']) && isset($_POST['custom_refund_nonce'])) {
                if (!wp_verify_nonce($_POST['custom_refund_nonce'], 'custom_refund_nonce')) {
                    wc_add_notice('Invalid request.', 'error');
                    return;
                }

                $order_id = intval($_POST['custom_refund_order_id']);
                if (!$order_id) {
                    wc_add_notice('Invalid Order ID.', 'error');
                    return;
                }

                $order = wc_get_order($order_id);
                if (!$order) {
                    wc_add_notice('Order not found.', 'error');
                    return;
                }

                $this->process_refund_request($order);
            }
        }
*/
    class WC_Refund_Request {
        public function __construct() {
            add_action('admin_enqueue_scripts', [$this, 'remove_refund_button']);
            add_action('admin_init', [$this, 'handle_custom_refund_request']);
        }

        public function remove_refund_button() {
            if (isset($_GET['post']) && get_post_type($_GET['post']) === 'shop_order') {
                add_action('woocommerce_order_item_add_action_buttons', [$this, 'add_custom_button_to_order_page'], 20);
            }
        }
/*
        public function add_custom_button_to_order_page($order) {
            // Check if the order has been refunded
            if ($order->has_status('refunded')) {
                echo '<p>This order has already been refunded.</p>';
                return;
            }

            // Display the custom refund button
            echo '<form method="POST" action="" class="custom-refund-form">';
            echo '<input type="hidden" name="custom_refund_order_id" value="' . esc_attr($order->get_id()) . '">';
            echo '<input type="hidden" name="custom_refund_nonce" value="' . wp_create_nonce('custom_refund_nonce') . '">';
            echo '<button type="submit" class="button custom-refund-button">Blackstone Reembolso</button>';
            echo '</form>';

            // Add jQuery for the confirmation dialog
            echo '<script type="text/javascript">
                        $(document).ready(function () {
                            $(".custom-refund-form").on("submit", function(e) {
                                if (!confirm("Are you sure you want to process this refund?")) {
                                    e.preventDefault(); // Prevent form submission if the user cancels
                                }
                            });
                        })(jQuery);
            </script>';
        }
*/

function add_custom_button_to_order_page($order) {
    if ($order->has_status('refunded')) {
        echo '<p>This order has already been refunded.</p>';
        return;
    }
    
    echo '<form method="POST" action="" class="custom-refund-form">';
    echo '<input type="hidden" name="custom_refund_order_id" value="' . esc_attr($order->get_id()) . '">';
    echo '<input type="hidden" name="custom_refund_nonce" value="' . wp_create_nonce('custom_refund_nonce') . '">';
    echo '<button type="submit" id="submitbutton" class="button custom-refund-button">Blackstone Reembolso</button>';
    echo '</form>';

    ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            //alert('Custom Refund Button Clicked 1');
            $('#submitbutton').on('click', function(e) {
                //alert('Custom Refund Button Clicked 2');
                if (!confirm("Are you sure you want to process this refund?")) {
                    e.preventDefault(); // Prevent form submission if the user cancels
                }
            });
        });
        </script>
        <?php
}



        public function handle_custom_refund_request() {
            if (isset($_POST['custom_refund_order_id']) && isset($_POST['custom_refund_nonce'])) {
                if (!wp_verify_nonce($_POST['custom_refund_nonce'], 'custom_refund_nonce')) {
                    wc_add_notice('Invalid request.', 'error');
                    return;
                }

                $order_id = intval($_POST['custom_refund_order_id']);
                if (!$order_id) {
                    wc_add_notice('Invalid Order ID.', 'error');
                    return;
                }

                $order = wc_get_order($order_id);
                if (!$order) {
                    wc_add_notice('Order not found.', 'error');
                    return;
                }

                $this->process_refund_request($order);
            }
        }     
           
        private function process_refund_request($order) {
            $payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();
            $blackstone_gateway = $payment_gateways['bmspay_blackstone_payment'];

            $service_reference_number = get_post_meta($order->get_id(), '_bmspay_service_reference_number', true);

            $environment_url = ($blackstone_gateway->environment == 'yes')
                ? 'https://services.bmspay.com/testing/api/Transactions/DoRefund'
                : 'https://services.bmspay.com/api/Transactions/DoRefund';

            $data = array(
                'Amount' => $order->get_total(),
                'TrackData' => '',
                'UserTransactionNumber' => md5(uniqid($service_reference_number . $environment_url, true)),
                'ServiceTransactionNumber' => $service_reference_number,
                'SURI' => '',
                'AppKey' => $blackstone_gateway->environment == 'yes' ? "12345" : $blackstone_gateway->app_key,
                'AppType' => $blackstone_gateway->environment == 'yes' ? "1" : $blackstone_gateway->app_type,
                'mid' => $blackstone_gateway->environment == 'yes' ? "76074" : $blackstone_gateway->api_mid,
                'cid' => $blackstone_gateway->environment == 'yes' ? "260" : $blackstone_gateway->api_cid,
                'UserName' => $blackstone_gateway->environment == 'yes' ? "nicolas" : $blackstone_gateway->api_username,
                'Password' => $blackstone_gateway->environment == 'yes' ? "password1" : $blackstone_gateway->api_password,
                'IpAddress' => $_SERVER['REMOTE_ADDR']
            );

            $response = wp_remote_post($environment_url, array(
                'method' => 'POST',
                'headers' => array('Content-Type' => 'application/json'),
                'body' => json_encode($data),
            ));

            $response_body = wp_remote_retrieve_body($response);
            $resp = json_decode($response_body, true);

            if ($resp['ResponseCode'] == "200") {
                wc_create_refund(array(
                    'amount' => $order->get_total(),
                    'reason' => 'Refund processed via API.',
                    'order_id' => $order->get_id(),
                    'restock_items'  => true,
                ));

                $order->update_status('refunded');
                $order->add_order_note('Order refunded via API.');

                // Redirect to WooCommerce orders page
                wp_redirect(admin_url('edit.php?post_type=shop_order'));
                exit; // Stop further execution to ensure redirect works
            } else {
                wc_add_notice('Refund request failed.', 'error');
            }
        }
    }
