<?php
/*
Plugin Name: Blackstone Merchant Payment Gateway
Plugin URI: http://www.blackstonemerchant.com/
Description: Blackstone Merchant custom payment gateway integration with Woocommerce.
Author: Adaved Solutions
Author URI: https://adaved.com/
Version: 4.5.3
*/

define('WC_REFUND_PLUGIN_PATH', plugin_dir_path(__FILE__));

include_once WC_REFUND_PLUGIN_PATH . 'includes/class-refund-request.php';

add_action( 'plugins_loaded', 'bmspay_blackstone_init', 0 );

function bmspay_blackstone_init() {
  if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
  include_once( 'blackstone-woocommerce.php' );
  add_filter( 'woocommerce_payment_gateways', 'bmspay_add_blackstone_gateway' );


  function bmspay_add_blackstone_gateway( $methods ) {
    $methods[] = 'bmspay_Blackstone_Payment';
    return $methods;
  }
}

function wc_refund_plugin_init() {
    new WC_Refund_Request();
}

add_action('plugins_loaded', 'wc_refund_plugin_init');
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bmspay_blackstone_action_links' );

function bmspay_blackstone_action_links( $links ) {
  $plugin_links = array(
    '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'bmspay-blackstone-payment' ) . '</a>',
  );
  return array_merge( $plugin_links, $links );
}

