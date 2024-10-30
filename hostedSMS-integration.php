<?php
/**
 * Plugin Name: HostedSMS.pl - SMS API integration for WooCommerce
 * Description: Wtyczka służy do wysyłania powiadomień SMS o zmianach statusów do klientów sklepu opartego o WooCommerce dla WordPress. Możliwe jest skonfigurowanie powiadomień o zmianach dla 4 rodzajów statusów sklepu WooCommerce.
 * 
 * EN:
 * The plugin is used to send SMS notifications about status changes to customers of a WooCommerce-based store for WordPress. It is possible to configure notifications for changes in 4 types of WooCommerce store statuses.
 * Version:          1.0.1
 * Requires PHP:     7.2
 * Author:           dcs.pl
 * Author URI:       https://dcs.pl/pl
 * Text Domain:      hostedsms-pl-sms-api-integration-for-woocommerce
 * Domain Path:      /languages
 * Requires Plugins: WooCommerce
 * 
 * WC tested up to: 9.2.3
 * 
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * @package WCHSMS_Integration_HostedSMS
 */

if ( ! defined( 'ABSPATH' ) ) 
    exit;

if ( ! class_exists( 'WCHSMS_HostedSMS' ) ) :
    require_once 'vendor/autoload.php';
    class WCHSMS_HostedSMS {
        public function __construct() {
            add_action( 'plugins_loaded', array( $this, 'init' ) );
        }

        public function init() {
            if ( class_exists( 'WC_Integration' ) ) {
                load_plugin_textdomain( 'hostedsms-pl-sms-api-integration-for-woocommerce', false, dirname( plugin_basename( __FILE__ )) . '/languages');
                include_once 'class-wc-hostedSMS-integration.php';
                add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
            } else {
                new Exception('Woocommerce class not loaded');
            }
        }

        public function add_integration( $integrations ) {
            $integrations[] = 'WCHSMS_Integration_HostedSMS';
            return $integrations;
        }
    }
endif;

$WC_HostedSMS = new WCHSMS_HostedSMS( __FILE__ );