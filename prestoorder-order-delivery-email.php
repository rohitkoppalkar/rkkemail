<?php
/**
 * Plugin Name: PrestoOrder Order Delivery Email
 * Plugin URI: http://www.prestoorder.com
 * Description: Plugin for adding a custom WooCommerce email that sends customer an email when an order status is Out for Delivery
 * Author: RKK
 * Author URI: http://www.prestoorder.com
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function add_order_delivery_email( $email_classes ) {
    // include our custom email class
    require_once( 'includes/class-po-order-delivery-email.php' );
    // add the email class to the list of email classes that WooCommerce loads
    $email_classes['PO_Order_Delivery_Email'] = new PO_Order_Delivery_Email();
    return $email_classes;

}
add_filter( 'woocommerce_email_classes', 'add_order_delivery_email' );


function add_delivery_email_actions( $email_actions ) {
    $email_actions[] = 'woocommerce_order_status_delivery';
    return $email_actions;
}
add_filter( 'woocommerce_email_actions', 'add_delivery_email_actions' );