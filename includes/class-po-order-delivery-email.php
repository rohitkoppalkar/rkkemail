<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'PO_Order_Delivery_Email', false ) ) :

    /**
     * A custom Order Delivery Email class
     *
     * @since 0.1
     * @extends \WC_Email
     */
    class PO_Order_Delivery_Email extends WC_Email {


        /**
         * Set email defaults
         *
         * @since 0.1
         */
        public function __construct() {

            $this->id             = 'customer_order_delivery';
            $this->customer_email = true;
            $this->title          = __( 'Out For Delivery', 'woocommerce' );
            $this->description    = __( 'Out For Delivery emails are sent to customers when their orders are marked Out For Delivery.', 'woocommerce' );

            $this->heading        = __( 'Your order is Out For Delivery PO', 'woocommerce' );
            $this->subject        = __( 'Your {site_title} order from {order_date} is Out For Delivery', 'woocommerce' );

            //$this->template_html  = ( 'emails/customer-completed-order.php' );
            //$this->template_plain = ( 'emails/plain/customer-completed-order.php' );

            $this->template_html  = 'emails/customer-delivered-order.php';
            $this->template_plain = 'emails/plain/customer-delivered-order.php';

            // Triggers for this email
            add_action( 'woocommerce_order_status_delivery_notification', array( $this, 'trigger' ), 10, 2 );

            // Call parent constuctor
            parent::__construct();
        }


        /**
         * Determine if the email should actually be sent and setup email merge variables
         *
         * @since 0.1
         * @param int $order_id
         */
        public function trigger( $order_id, $order = false ) {

            if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
                $order = wc_get_order( $order_id );
            }

            if ( is_a( $order, 'WC_Order' ) ) {
                $this->object                  = $order;
                $this->recipient               = $this->object->get_billing_email();

                $this->find['order-date']      = '{order_date}';
                $this->find['order-number']    = '{order_number}';

                $this->replace['order-date']   = wc_format_datetime( $this->object->get_date_created() );
                $this->replace['order-number'] = $this->object->get_order_number();
            }

            if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
                return;
            }

            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }


        /**
         * Get content html.
         *
         * @access public
         * @return string
         */
        public function get_content_html() {
            return wc_get_template_html( $this->template_html, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'			=> $this,
            ) );
        }

        /**
         * Get content plain.
         *
         * @return string
         */
        public function get_content_plain() {
            return wc_get_template_html( $this->template_plain, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true,
                'email'			=> $this,
            ) );
        }


        /**
         * Initialise settings form fields.
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'         => __( 'Enable/Disable', 'woocommerce' ),
                    'type'          => 'checkbox',
                    'label'         => __( 'Enable this email notification', 'woocommerce' ),
                    'default'       => 'yes',
                ),
                'subject' => array(
                    'title'         => __( 'Subject', 'woocommerce' ),
                    'type'          => 'text',
                    /* translators: %s: default subject */
                    'description'   => sprintf( __( 'Defaults to %s', 'woocommerce' ), '<code>' . $this->subject . '</code>' ),
                    'placeholder'   => '',
                    'default'       => '',
                    'desc_tip'      => true,
                ),
                'heading' => array(
                    'title'         => __( 'Email heading', 'woocommerce' ),
                    'type'          => 'text',
                    /* translators: %s: default heading */
                    'description'   => sprintf( __( 'Defaults to %s', 'woocommerce' ), '<code>' . $this->heading . '</code>' ),
                    'placeholder'   => '',
                    'default'       => '',
                    'desc_tip'      => true,
                ),

                'email_type' => array(
                    'title'         => __( 'Email type', 'woocommerce' ),
                    'type'          => 'select',
                    'description'   => __( 'Choose which format of email to send.', 'woocommerce' ),
                    'default'       => 'html',
                    'class'         => 'email_type wc-enhanced-select',
                    'options'       => $this->get_email_type_options(),
                    'desc_tip'      => true,
                ),
            );
        }
    }

endif;