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

if ( ! class_exists( 'GADWP_Backend_Widgets' ) ) {

	class GADWP_Backend_Widgets {

		private $gadwp;

		public function __construct() {
			$this->gadwp = GAB();
			if ( GADWP_Tools::check_roles( $this->gadwp->config->options['access_back'] ) && ( 1 == $this->gadwp->config->options['dashboard_widget'] ) ) {
				add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );
			}
		}

		public function add_widget() {
			wp_add_dashboard_widget( 'gadwp-widget', __( "Google Analytics Dashboard", 'google-analytics-board' ), array( $this, 'dashboard_widget' ), $control_callback = null );
		}

		public function dashboard_widget() {
			$projectId = 0;
			
			if ( empty( $this->gadwp->config->options['token'] ) ) {
				echo '<p>' . __( "This plugin needs an authorization:", 'google-analytics-board' ) . '</p><form action="' . menu_page_url( 'gadwp_settings', false ) . '" method="POST">' . get_submit_button( __( "Authorize Plugin", 'google-analytics-board' ), 'secondary' ) . '</form>';
				return;
			}
			
			if ( current_user_can( 'manage_options' ) ) {
				if ( $this->gadwp->config->options['tableid_jail'] ) {
					$projectId = $this->gadwp->config->options['tableid_jail'];
				} else {
					echo '<p>' . __( "An admin should asign a default Google Analytics Profile.", 'google-analytics-board' ) . '</p><form action="' . menu_page_url( 'gadwp_settings', false ) . '" method="POST">' . get_submit_button( __( "Select Domain", 'google-analytics-board' ), 'secondary' ) . '</form>';
					return;
				}
			} else {
				if ( $this->gadwp->config->options['tableid_jail'] ) {
					$projectId = $this->gadwp->config->options['tableid_jail'];
				} else {
					echo '<p>' . __( "An admin should asign a default Google Analytics Profile.", 'google-analytics-board' ) . '</p><form action="' . menu_page_url( 'gadwp_settings', false ) . '" method="POST">' . get_submit_button( __( "Select Domain", 'google-analytics-board' ), 'secondary' ) . '</form>';
					return;
				}
			}
			
			if ( ! ( $projectId ) ) {
				echo '<p>' . __( "Something went wrong while retrieving property data. You need to create and properly configure a Google Analytics account:", 'google-analytics-board' ) . '</p> <form action="https://metricsquery.com/how-to-set-up-google-analytics-on-your-website/" method="POST">' . get_submit_button( __( "Find out more!", 'google-analytics-board' ), 'secondary' ) . '</form>';
				return;
			}
			
			?>
<div id="gadwp-window-1"></div>
<?php
		}
	}
}
