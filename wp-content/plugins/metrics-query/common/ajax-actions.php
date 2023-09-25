<?php
/**
 * Author: Yehuda Hassine
 * Author URI: https://metricsquery.com
 * Copyright 2013 by Alin Marcu and forked by Yehuda Hassine
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GADWP_Common_Ajax' ) ) {

	final class GADWP_Common_Ajax {

		private $gadwp;

		public function __construct() {
			$this->gadwp = GAB();

			if ( GADWP_Tools::check_roles( $this->gadwp->config->options['access_back'] ) || GADWP_Tools::check_roles( $this->gadwp->config->options['access_front'] ) ) {
				add_action( 'wp_ajax_gadwp_set_error', array( $this, 'ajax_set_error' ) );
			}
		}

		/**
		 * Ajax handler for storing JavaScript Errors
		 *
		 * @return json|int
		 */
		public function ajax_set_error() {
			if ( ! isset( $_POST['gadwp_security_set_error'] ) || ! ( wp_verify_nonce( $_POST['gadwp_security_set_error'], 'gadwp_backend_item_reports' ) || wp_verify_nonce( $_POST['gadwp_security_set_error'], 'gadwp_frontend_item_reports' ) ) ) {
				wp_die( - 40 );
			}
			$timeout = 24 * 60 * 60;
			GADWP_Tools::set_error( $_POST['response'], $timeout );
			wp_die();
		}
	}
}
