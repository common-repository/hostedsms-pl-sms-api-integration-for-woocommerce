<?php
/**
 * HostedSMS Integration
 * 
 * @package  WCHSMS_Integration_HostedSMS
 * @category Integration
 */

if ( ! defined( 'ABSPATH' ) ) 
    exit;

use HostedSms\WebService\HostedSmsWebService;
use HostedSMS\WebService\WebServiceException;

if ( ! class_exists( 'WCHSMS_Integration_HostedSMS' ) ) :
    class WCHSMS_Integration_HostedSMS extends WC_Integration {
        private $login;
        private $password;
        private $user_logged_in;
        public $enabled;
        private $sender;
        private $client;
        private $enabled_agreement;
        private $text_on_agreement;
        private $enabled_on_statuses;
        private $message_on_statuses;
        private $valid_senders;
        private $default_sms_content;

        public function __construct() {
            global $woocommerce;

            $this->id                 = 'hostedsms-integration';
            $this->method_title       = __( 'HostedSMS', 'hostedsms-pl-sms-api-integration-for-woocommerce' );
            $this->method_description = __( 'Ustawienia wtyczki HostedSMS.', 'hostedsms-pl-sms-api-integration-for-woocommerce' );
            $this->default_sms_content = __('Status zamówienia {number} został zmieniony', 'hostedsms-pl-sms-api-integration-for-woocommerce');
            
            $this->init_settings();

            $this->enabled = $this->get_option( 'enabled' );
            $this->login = $this->get_option( 'login' );
            $this->password = $this->get_option('password');
            $this->user_logged_in = $this->get_option('user_logged_in');
            $this->valid_senders = $this->get_option('valid_senders');
            $this->enabled_agreement = $this->get_option('enabled_agreement');
            $this->text_on_agreement = $this->get_option('text_on_agreement');
            $statuses = wc_get_order_statuses();
            if(!$this->is_null_or_empty_string($this->get_option('status1_select')))
            {
                $this->enabled_on_statuses = array(
                    $statuses[$this->get_option('status1_select')] => $this->get_option('enabled_on_status1'),
                    $statuses[$this->get_option('status2_select')] => $this->get_option('enabled_on_status2'),
                    $statuses[$this->get_option('status3_select')] => $this->get_option('enabled_on_status3'),
                    $statuses[$this->get_option('status4_select')] => $this->get_option('enabled_on_status4'),
                );
                $this->message_on_statuses = array(
                    $statuses[$this->get_option('status1_select')] => $this->get_option('message_on_status1'),
                    $statuses[$this->get_option('status2_select')] => $this->get_option('message_on_status2'),
                    $statuses[$this->get_option('status3_select')] => $this->get_option('message_on_status3'),
                    $statuses[$this->get_option('status4_select')] => $this->get_option('message_on_status4'),
                );
            }

            $this->init_hostedSMS_client();

            if(is_countable($this->valid_senders) && count($this->valid_senders) > 0)
            {
                $this->sender = $this->valid_senders[$this->get_option('sender_select')];
            }

            $this->init_form_fields();

            add_action('woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ));
            if($this->is_service_enabled()) {
                add_action('woocommerce_order_status_changed', array( $this,'send_sms_on_status_change'), 10 , 3);
                if($this->enabled_agreement == 'yes')
                {
                    add_action( 'woocommerce_review_order_before_submit' , array( $this, 'sms_agreement_checkout_field'));
                    add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'sms_agreement_checkout_field_update_order_meta'), 10, 1 );
                }
            }
        }

        private function is_service_enabled() {
            return 
                $this->enabled == 'yes' && 
                $this->user_logged_in == true &&
                !$this->is_null_or_empty_string($this->sender);
        }

        private function init_hostedSMS_client() {
            if(!empty($this->login) && !empty($this->password)) 
            {
                $this->client = new HostedSmsWebService($this->login, $this->password);
            }
        }

        public function process_admin_options() {
            parent::process_admin_options();

            $this->set_default_messages_if_empty('message_on_status1');
            $this->set_default_messages_if_empty('message_on_status2');
            $this->set_default_messages_if_empty('message_on_status3');
            $this->set_default_messages_if_empty('message_on_status4');
            
            $valid_senders = array('');
            $user_logged_in = false;
            try {
                $valid_senders = $this->get_valid_senders();
                $user_logged_in = true;
            }
            catch (WebServiceException $e)
            {
                if ($e->getMessage() == 'Invalid Credentials or IP')
                    $this->errors[] = __("Nieprawidłowe dane logowania", 'hostedsms-pl-sms-api-integration-for-woocommerce');
                else
                    $this->errors[] = __("Nie udało się zalogwać", 'hostedsms-pl-sms-api-integration-for-woocommerce');
            }
            
            $this->update_option('user_logged_in', $user_logged_in);
            $this->update_option('valid_senders', $valid_senders);
            $this->display_errors();
            $this->init_form_fields();
        }

        private function set_default_messages_if_empty($key)
        {
            $message = $this->get_option($key);
            if($this->is_null_or_empty_string($message))
                $this->update_option($key, $this->default_sms_content);
        }

        private function is_null_or_empty_string($str){
            return ($str === null || trim($str) === '');
        }

        public function send_sms_on_status_change($order_id, $from_status, $to_status) {
            $order = wc_get_order($order_id);

            $to_status_name = wc_get_order_status_name( $to_status );
            
            if($this->should_send_sms($order_id, $to_status_name)) {
                $message = $this->process_message($this->message_on_statuses[$to_status_name], $order);
                $this->send_sms($this->client, $order->get_billing_phone(), $message);
            }
        }

        private function should_send_sms($order_id, $to_status_name) {
            return
                array_key_exists($to_status_name, $this->enabled_on_statuses) &&
                $this->enabled_on_statuses[$to_status_name] == 'yes' &&
                ($this->enabled_agreement == 'no' || get_post_meta($order_id, 'agreement_field', true) == 1);
        }

        private function send_sms($client, $phone, $message) {
            $transactionId = 'WooCommerce Integration ' . $phone . ' ' . gmdate('Y-m-d H:i:s') . 'GMT';
            try {
                $client->sendSms($phone, $message, $this->sender, $transactionId);
            }
            catch (WebServiceException $e) {
                throw new Exception('HostedSMS error: ' . esc_html($e->getMessage()));
            }
        }
        
        private function process_message($message, $order) {
            $parametrs = array(
                '{number}' => $order->get_order_number(),
                '{phone}' => $order->get_billing_phone(),
                '{customer_first_name}' => $order->get_billing_first_name(),
                '{customer_last_name}' => $order->get_billing_last_name(),
                '{total}' => $this->get_parsed_total($order)
            );
            $message = str_replace(array_keys($parametrs), $parametrs, $message);
            return $message;
        }

        private function get_parsed_total($order) {
            $total = $order->get_total();
            $currency = $order->get_currency();
            return "$total $currency";
        }

        public function get_valid_senders() {
            $client = new HostedSmsWebService($this->get_option( 'login'), $this->get_option( 'password'));
            
            $response = $client->getValidSenders();
            
            return $response->senders;
        }

        public function init_form_fields() {
            $senders = ($this->get_option( 'valid_senders') != null)? $this->get_option( 'valid_senders') : array('');
            $statuses = wc_get_order_statuses();
            $this->form_fields = array(
                'enabled' => array(
                    'type'        => 'checkbox',
                    'label'       => __( 'Wysyłaj notyfikacje SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'default'     => 'no',
                ),
                'login' => array(
                    'title'       => __( 'Login', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'text',
                    'description' => __( 'Login do serwisu HostedSMS.', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'desc_tip'    => true,
                    'default'     => '',
                ),
                'password' => array(
                    'title'       => __( 'Hasło', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'password',
                    'description' => __( 'Hasło do serwisu HostedSMS.', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'desc_tip'    => true,
                    'default'     => '',
                ),
                'sender_select' => array(
                    'title'       => __( 'Nadawca', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'select',
                    'options'     => $senders,
                    'description' => __( 'Aby zobaczyć dostępnych nadawców wypełnij dane logowania i zapisz zmiany.', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'desc_tip'    => true,
                ),
                'enabled_agreement' => array(
                    'type'        => 'checkbox',
                    'title'       => __('Wymagana zgoda na SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce'),
                    'label'       => __( 'Wyświetl okno zgody na otrzymywanie SMS przy składaniu zamówienia', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'default'     => 'no',
                ),
                'text_on_agreement' => array(
                    'title'       => __( 'Treść okna zgody na SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'textarea',
                    'default'     => __('Wyrażam zgodę na otrzymawanie wiadomości SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce')
                ),
                'enabled_on_status1' => array(
                    'type'        => 'checkbox',
                    'title'       => __('Status 1', 'hostedsms-pl-sms-api-integration-for-woocommerce'),
                    'label'       => __( 'Wysyłaj notyfikacje SMS przy zmianie na status:', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'default'     => 'no',
                ),
                'status1_select' => array(
                    'type'        => 'select',
                    'options'     => $statuses,
                ),
                'message_on_status1' => array(
                    'title'       => __( 'Treść wiadomości SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'textarea',
                    'default'     => $this->default_sms_content,
                    'description' => __('Dla konkretnego zamówienia, dostępne są następujące parametry:'
                                        . '<br>{number} - numer zamówienia'
                                        . '<br>{phone} - numer telefonu'
                                        . '<br>{customer_first_name} - imię klienta'
                                        . '<br>{customer_last_name} - nazwisko klienta'
                                        . '<br>{total} - suma zamówienia, np. "4.50 PLN"', 'hostedsms-pl-sms-api-integration-for-woocommerce'),
                ),
                'enabled_on_status2' => array(
                    'type'        => 'checkbox',
                    'title'       => __('Status 2', 'hostedsms-pl-sms-api-integration-for-woocommerce'),
                    'label'       => __( 'Wysyłaj notyfikacje SMS przy zmianie na status:', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'default'     => 'no',
                ),
                'status2_select' => array(
                    'type'        => 'select',
                    'options'     => $statuses,
                ),
                'message_on_status2' => array(
                    'title'       => __( 'Treść wiadomości SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'textarea',
                    'default'     => $this->default_sms_content,
                ),
                'enabled_on_status3' => array(
                    'type'        => 'checkbox',
                    'title'       => __('Status 3', 'hostedsms-pl-sms-api-integration-for-woocommerce'),
                    'label'       => __( 'Wysyłaj notyfikacje SMS przy zmianie na status:', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'default'     => 'no',
                ),
                'status3_select' => array(
                    'type'        => 'select',
                    'options'     => $statuses,
                ),
                'message_on_status3' => array(
                    'title'       => __( 'Treść wiadomości SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'textarea',
                    'default'     => $this->default_sms_content,
                ),
                'enabled_on_status4' => array(
                    'type'        => 'checkbox',
                    'title'       => __('Status 4', 'hostedsms-pl-sms-api-integration-for-woocommerce'),
                    'label'       => __( 'Wysyłaj notyfikacje SMS przy zmianie na status:', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'default'     => 'no',
                ),
                'status4_select' => array(
                    'type'        => 'select',
                    'options'     => $statuses,
                ),
                'message_on_status4' => array(
                    'title'       => __( 'Treść wiadomości SMS', 'hostedsms-pl-sms-api-integration-for-woocommerce' ),
                    'type'        => 'textarea',
                    'default'     => $this->default_sms_content,
                ),
            );
        }
        
        function sms_agreement_checkout_field() {
            echo '<div id="agreement_checkbox_field">';
            woocommerce_form_field( 'agreement_field', array(
                'type'      => 'checkbox',
                'class'     => array('input-checkbox'),
                'label'     => $this->text_on_agreement,
            ),  WC()->checkout->get_value( 'agreement_field' ) );
            echo '</div>';
        }

        function sms_agreement_checkout_field_update_order_meta( $order_id ) {
            $nonce = wp_create_nonce( 'my-nonce' );
            if ( ! wp_verify_nonce( $nonce, 'my-nonce' ) ) {
                 die( 'Security check' ); 
            }
            if ( ! empty( $_POST['agreement_field'] ) )
                update_post_meta( $order_id, 'agreement_field', sanitize_text_field($_POST['agreement_field']) );
        }
    }
endif;