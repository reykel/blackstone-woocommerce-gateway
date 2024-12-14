<?php
class bmspay_Blackstone_Payment extends WC_Payment_Gateway_CC {
  function __construct() {
    $this->id = "bmspay_blackstone_payment";
    $this->method_title = __( "Blackstone", 'bmspay-blackstone-payment' );
    $this->method_description = __( "Blackstone Payment Gateway Plug-in for WooCommerce", 'bmspay-blackstone-payment' );
    $this->title = __( "Blackstone", 'bmspay-blackstone-payment' );
    $this->icon = null;
    $this->has_fields = true;
    $this->supports = array( 'default_credit_card_form' );
    $this->init_form_fields();
    $this->init_settings();
    
    foreach ( $this->settings as $setting_key => $value ) {
      $this->$setting_key = $value;
    }
    
    add_action( 'admin_notices', array( $this,  'do_ssl_check' ) );
    
    if ( is_admin() ) {
      add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }
  } 

public function init_form_fields() {
    $this->form_fields = array(
      'enabled'      => array(
        'title'        => __( 'Enable / Disable', 'bmspay-blackstone-payment' ),
        'label'        => __( 'Enable this payment gateway', 'bmspay-blackstone-payment' ),
        'type'         => 'checkbox',
        'default'      => 'no',
      ),
      'title'        => array(
        'title'        => __( 'Title', 'bmspay-blackstone-payment' ),
        'type'         => 'text',
        'desc_tip'     => __( 'Payment title of checkout process.', 'bmspay-blackstone-payment' ),
        'default'      => __( 'Credit card', 'bmspay-blackstone-payment' ),
      ),
      'description'  => array(
        'title'        => __( 'Description', 'bmspay-blackstone-payment' ),
        'type'         => 'textarea',
        'desc_tip'     => __( 'Payment title of checkout process.', 'bmspay-blackstone-payment' ),
        'default'      => __( 'Successfully payment through credit card.', 'bmspay-blackstone-payment' ),
        'css'          => 'max-width:450px;'
      ),
      'api_username' => array(
        'title'        => __( 'Username', 'bmspay-blackstone-payment' ),
        'type'         => 'text',
        'desc_tip'     => __( 'This is the username provided by Blackstone when you signed up for an account.', 'bmspay-blackstone-payment' ),
      ),
      'api_password' => array(
        'title'        => __( 'Password', 'bmspay-blackstone-payment' ),
        'type'         => 'password',
        'desc_tip'     => __( 'This is the password provided by Blackstone when you signed up for an account.', 'bmspay-blackstone-payment' ),
      ),
      'api_mid'      => array(
        'title'        => __( 'MID', 'bmspay-blackstone-payment' ),
        'type'         => 'text',
        'desc_tip'     => __( 'This is the MID code provided by Blackstone when you signed up for an account.', 'bmspay-blackstone-payment' ),
      ),
      'api_cid'      => array(
        'title'        => __( 'CID', 'bmspay-blackstone-payment' ),
        'type'         => 'text',
        'desc_tip'     => __( 'This is the CID code provided by Blackstone when you signed up for an account.', 'bmspay-blackstone-payment' ),
      ),
      'app_type'     => array(
        'title'        => __( 'App Type', 'bmspay-blackstone-payment' ),
        'type'         => 'text',
        'desc_tip'     => __( 'This is the App Type provided by Blackstone when you signed up for an account.', 'bmspay-blackstone-payment' ),
      ),
      'app_key'      => array(
        'title'        => __( 'App Key', 'bmspay-blackstone-payment' ),
        'type'         => 'password',
        'desc_tip'     => __( 'This is the App Key provided by Blackstone when you signed up for an account.', 'bmspay-blackstone-payment' ),
      ),
      'environment'  => array(
        'title'        => __( 'Test Mode', 'bmspay-blackstone-payment' ),
        'label'        => __( 'Enable Test Mode', 'bmspay-blackstone-payment' ),
        'type'         => 'checkbox',
        'description'  => __( 'This is the sandbox of the gateway.', 'bmspay-blackstone-payment' ),
        'default'      => 'no',
      )
    );    
  }

  public function payment_fields() {
      $user_id = get_current_user_id();
      $saved_methods = WC_Payment_Tokens::get_customer_tokens($user_id);

      echo '<div id="saved-payment-methods">';
      
      if ($saved_methods) {
          echo '<p>' . __('Seleccione una tarjeta guardada:', 'your-text-domain') . '</p>'; //'Select a saved card:'
          echo '<ul class="wc-saved-payment-methods">';

          foreach ($saved_methods as $token) {
              $last4 = $token->get_last4();
              $token_id = $token->get_id();

              echo '<li>';
              echo '<input type="radio" id="token-' . esc_attr($token_id) . '" name="wc-saved-payment-token" value="' . esc_attr($token_id) . '" />';
              echo '<label for="token-' . esc_attr($token_id) . '">';
              echo esc_html__('Tarjeta terminada en', 'your-text-domain') . ' ' . esc_html($last4); //'Card ending in'
              echo '</label>';
              echo '</li>';
          }

          echo '</ul>';
      } else {
          echo '<p>' . __('No tiene métodos de pago guardados', 'your-text-domain') . '</p>'; //'You have no saved payment methods.'
      }

      echo '<ul class="wc-saved-payment-methods">';
      echo '<li>';
      echo '<input type="radio" id="token-new" name="wc-saved-payment-token" value="new" checked />';
      echo '<label for="token-new">' . __('Use un nuevo método de pago', 'your-text-domain') . '</label>'; //'Use a new payment method'
      echo '</li>';
      echo '</ul>';
      
      echo '</div>';


      echo '<div id="new-payment-method">';
      echo '<p>' . __('Entre los datos de su tarjeta:', 'your-text-domain') . '</p>'; //'Enter your card details:'
      $this->form();
      echo '</div>';
      ?>
      <script type="text/javascript">
          jQuery(document).ready(function($) {
              $('input[name="wc-saved-payment-token"]').change(function() {
                  if ($(this).val() === 'new') {
                      $('#new-payment-method').show();
                  } else {
                      $('#new-payment-method').hide();
                  }
              });

              if ($('input[name="wc-saved-payment-token"]:checked').val() !== 'new') {
                  $('#new-payment-method').hide();
              }
          });
      </script>
      <?php
  }


  public function process_payment($order_id) {
     global $woocommerce;
     $customer_order = new WC_Order($order_id);

     if (isset($_POST['wc-saved-payment-token']) && $_POST['wc-saved-payment-token'] !== 'new') {

        $token_id = sanitize_text_field($_POST['wc-saved-payment-token']);
        $token = WC_Payment_Tokens::get($token_id);

          if ($token && $token->get_user_id() === get_current_user_id()) {

            $gateway_id = $token->get_gateway_id();
            $last4 = $token->get_last4();
            $expiry_month = $token->get_expiry_month();
            $expiry_year = $token->get_expiry_year();
            $token_string = $token->get_token();

            $environment_url = ($this->environment == 'yes')
                ? 'https://services.bmspay.com/testing/api/Transactions/SaleWithToken'
                : 'https://services.bmspay.com/api/Transactions/SaleWithToken';

            $payload = array(
                "Token" => $token_string,
                "Amount" => $customer_order->order_total,
                "UserTransactionNumber" => (md5(uniqid($customer_order->get_order_number(), true))), 
                "TransactionType" => 2,

                "NameOnCard"            => $this->environment == 'yes' ? "Test" : $customer_order->get_billing_first_name(),
                "ExpDate"               => $this->environment == 'yes' ? "0827" : ($expiry_month.substr($expiry_year, 2, 2)),
                "AppKey"                => $this->environment == 'yes' ? "12345" : $this->app_key,
                "AppType"               => $this->environment == 'yes' ? "1" : $this->app_type,
                "mid"                   => $this->environment == 'yes' ? "76074" : $this->api_mid,
                "cid"                   => $this->environment == 'yes' ? "260": $this->api_cid,
                "UserName"              => $this->environment == 'yes' ? "nicolas" : $this->api_username,
                "Password"              => $this->environment == 'yes' ? "password1": $this->api_password,

                "x_first_name"           => $customer_order->billing_first_name,
                "x_last_name"            => $customer_order->billing_last_name,
                "x_address"              => $customer_order->billing_address_1,
                "x_city"                 => $customer_order->billing_city,
                "x_state"                => $customer_order->billing_state,
                "x_zip"                  => $customer_order->billing_postcode,
                "x_country"              => $customer_order->billing_country,
                "x_phone"                => $customer_order->billing_phone,
                "x_email"                => $customer_order->billing_email,
                  

                "x_ship_to_first_name"   => $customer_order->shipping_first_name,
                "x_ship_to_last_name"    => $customer_order->shipping_last_name,
                "x_ship_to_company"      => $customer_order->shipping_company,
                "x_ship_to_address"      => $customer_order->shipping_address_1,
                "x_ship_to_city"         => $customer_order->shipping_city,
                "x_ship_to_country"      => $customer_order->shipping_country,
                "x_ship_to_state"        => $customer_order->shipping_state,
                "x_ship_to_zip"          => $customer_order->shipping_postcode,
                  

                "x_cust_id"            => $customer_order->user_id,
                "x_customer_ip"        => $_SERVER['REMOTE_ADDR'],            
            );

            $response = wp_remote_post($environment_url, array(
                'method' => 'POST',
                'body' => http_build_query($payload),
                'timeout' => 45,
                'headers' => array(),
            ));

            if (is_wp_error($response)) {
                wc_add_notice(__('Payment error:', 'bmspay-blackstone-payment') . ' ' . $response->get_error_message(), 'error');
                return;
            }

            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body, true);

            error_log(print_r($payload, true));
            error_log(print_r($response_data, true));
            
            if ( $response_data['ResponseCode'] == "200" ) {
                $customer_order->payment_complete();
                $woocommerce->cart->empty_cart();

                $customer_order->update_meta_data('_bmspay_service_reference_number', $response_data['ServiceReferenceNumber']);
                $customer_order->update_meta_data('_bmspay_transaction_number', $payload['UserTransactionNumber']);
                $customer_order->save();                

                return array(
                  'result'   => 'success',
                  'redirect' => $this->get_return_url( $customer_order ),
                );
            } else {
                wc_add_notice( $r['response_reason_text'], 'error' );
                $customer_order->add_order_note( 'Error: '. $r['response_reason_text'] );
            }
          } else {
              wc_add_notice(__('Invalid payment method selected.', 'your-text-domain'), 'error');
              return;
          }
     } else {

            $environment_url = ($this->environment == 'yes')
                ? 'https://services.bmspay.com/testing/api/Transactions/Sale'
                : 'https://services.bmspay.com/api/Transactions/Sale';


         $payload = array(
             
           "UserName"              => $this->environment == 'yes' ? "nicolas" : $this->api_username,
           "Password"              => $this->environment == 'yes' ? "password1" : $this->api_password,
           "mid"                   => $this->environment == 'yes' ? "76074" : $this->api_mid,
           "cid"                   => $this->environment == 'yes' ? "260" : $this->api_cid,
           "Amount"                => $customer_order->order_total,
           "TransactionType"       => 1,
           "Track2"                => "",
           "ZipCode"               => $this->environment == 'yes' ? "32606" : $customer_order->billing_postcode,
           "ExpDate"               => str_replace( array( '/', ' '), '', $_POST['bmspay_blackstone_payment-card-expiry'] ),
           "CardNumber"            => str_replace( array(' ', '-' ), '', $_POST['bmspay_blackstone_payment-card-number'] ),
           "CVN"                   => $this->environment == 'yes' ? "" : (( isset( $_POST['bmspay_blackstone_payment-card-cvc'] ) ) ? $_POST['bmspay_blackstone_payment-card-cvc'] : ''),
           "NameOnCard"            => $this->environment == 'yes' ? "Test" : $customer_order->billing_first_name,
           "AppKey"                => $this->environment == 'yes' ? "12345" : $this->app_key,
           "AppType"               => $this->environment == 'yes' ? "1" : $this->app_type,
           "UserTransactionNumber" => (md5(uniqid($customer_order->get_order_number(), true))),
           "SaveToken"             => "True",


           "x_first_name"          => $customer_order->billing_first_name,
           "x_last_name"           => $customer_order->billing_last_name,
           "x_address"             => $customer_order->billing_address_1,
           "x_city"                => $customer_order->billing_city,
           "x_state"               => $customer_order->billing_state,
           "x_zip"                 => $customer_order->billing_postcode,
           "x_country"             => $customer_order->billing_country,
           "x_phone"               => $customer_order->billing_phone,
           "x_email"               => $customer_order->billing_email,
             

           "x_ship_to_first_name"  => $customer_order->shipping_first_name,
           "x_ship_to_last_name"   => $customer_order->shipping_last_name,
           "x_ship_to_company"     => $customer_order->shipping_company,
           "x_ship_to_address"     => $customer_order->shipping_address_1,
           "x_ship_to_city"        => $customer_order->shipping_city,
           "x_ship_to_country"     => $customer_order->shipping_country,
           "x_ship_to_state"       => $customer_order->shipping_state,
           "x_ship_to_zip"         => $customer_order->shipping_postcode,
             

           "x_cust_id"             => $customer_order->user_id,
           "x_customer_ip"         => $_SERVER['REMOTE_ADDR'],
         );
       

         $response = wp_remote_post( $environment_url, array(
           'method'               => 'POST',
           'headers'              => array('Content-Type' => 'application/x-www-form-urlencoded'),
           'body'                 => http_build_query( $payload ),
           'timeout'              => 90,
           'sslverify'            => false,
         ) );

         if ( is_wp_error( $response ) ) 
           throw new Exception( __( 'There is issue for connectin payment gateway. Sorry for the inconvenience.', 'bmspay-blackstone-payment' ) );
         if ( empty( $response['body'] ) )
           throw new Exception( __( 'Blackstone\'s Response was not get any data.', 'bmspay-blackstone-payment' ) );
         
         $response_body = wp_remote_retrieve_body( $response );
         $resp = json_decode($response_body, true);
         
         error_log(print_r($environment_url, true));
         error_log(print_r($payload, true));
         error_log(print_r($resp, true));

         if ( $resp['ResponseCode'] == "200" ) {

           $customer_order->add_order_note( __( 'Blackstone complete payment.', 'bmspay-blackstone-payment' ) );
                              
           $customer_order->payment_complete();
           $woocommerce->cart->empty_cart();

           $customer_order->update_meta_data('_bmspay_service_reference_number', $resp['ServiceReferenceNumber']);
           $customer_order->save();


            if (isset($resp['Token'])) {

                $user_id = $customer_order->get_user_id();

                $last4 = sanitize_text_field($resp['LastFour']);
                $card_type = sanitize_text_field($resp['CardType']);
                $expiry_month = substr(sanitize_text_field($payload['ExpDate']), 0, 2);
                $expiry_year = "20" . substr(sanitize_text_field($payload['ExpDate']), 2, 2);

                $existing_tokens = WC_Payment_Tokens::get_tokens(['user_id' => $user_id, 'type' => 'CC']);

                $card_exists = false;

                foreach ($existing_tokens as $existing_token) {
                    if (
                        $existing_token->get_last4() === $last4 &&
                        $existing_token->get_expiry_month() === $expiry_month &&
                        $existing_token->get_expiry_year() === $expiry_year &&
                        $existing_token->get_card_type() === $card_type
                    ) {
                        $card_exists = true;

                        $existing_token->set_token($resp['Token']);
                        $existing_token->save();

                        break;
                    }
                }

                if (!$card_exists) {
                    $token = new WC_Payment_Token_CC();
                    $token->set_token($resp['Token']);
                    $token->set_gateway_id($this->id);
                    $token->set_user_id($user_id);
                    $token->set_last4($last4);
                    $token->set_card_type($card_type);
                    $token->set_expiry_month($expiry_month);
                    $token->set_expiry_year($expiry_year);

                    $token->save();
                }
            }

           return array(
             'result'   => 'success',
             'redirect' => $this->get_return_url( $customer_order ),
           );
         } else {
           wc_add_notice( $r['response_reason_text'], 'error' );
           $customer_order->add_order_note( 'Error: '. $r['response_reason_text'] );
         }
     }
  }

  public function validate_fields() {
    return true;
  }
  public function do_ssl_check() {
    if( $this->enabled == "yes" ) {
      if( get_option( 'woocommerce_force_ssl_checkout' ) == "no" ) {
        echo "<div class=\"error\"><p>". sprintf( __( "<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>" ), $this->method_title, admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) ."</p></div>";  
      }
    }    
  }
}

function bmspay_blackstone_add_payment_gateway($gateways) {
    $gateways[] = 'bmspay_Blackstone_Payment';
    return $gateways;
}

add_filter('woocommerce_payment_gateways', 'bmspay_blackstone_add_payment_gateway');
