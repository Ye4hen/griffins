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

if ( ! class_exists( 'GADWP_Frontend_Item_Reports' ) ) {

	final class GADWP_Frontend_Item_Reports {

		private $gadwp;

		public function __construct() {
			$this->gadwp = GAB();
			
			add_action( 'admin_bar_menu', array( $this, 'custom_adminbar_node' ), 999 );
		}

		function custom_adminbar_node( $wp_admin_bar ) {
			if ( GADWP_Tools::check_roles( $this->gadwp->config->options['access_front'] ) && $this->gadwp->config->options['frontend_item_reports'] ) {
				/* @formatter:off */
				$args = array( 	'id' => 'gadwp-1',
								'title' => '<span class="ab-icon"></span><span class="">' . __( "Analytics", 'google-analytics-board' ) . '</span>',
								'href' => '#1',
								);
				/* @formatter:on */
				$wp_admin_bar->add_node( $args );
			}
		}
	}
}
