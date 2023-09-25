<?php
/**
 * Plugin Name: Metrics Query
 * Description: Displays Google Analytics Reports and Real-Time Statistics in your Dashboard. Automatically inserts the tracking code in every page of your website.
 * Author: Yehuda Hassine
 * Version: 1.0.3
 * Requires PHP: 5.6.0
 * Author URI: https://metricsquery.com/
 * Text Domain: google-analytics-board
 * Domain Path: /languages
 */
/**
 * This plugin forked from Google Analytics Dashboard for WP
 * Created by Alin Marcu and forked by Yehuda Hassine
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

// Plugin Version
if ( ! defined( 'GADWP_CURRENT_VERSION' ) ) {
	define( 'GADWP_CURRENT_VERSION', '1.0.1' );
}

if ( ! defined( 'GADWP_ENDPOINT_URL' ) ) {
	define( 'GADWP_ENDPOINT_URL', 'https://metricsquery.com/' );
}


if ( ! class_exists( 'Metrics_Query_Manager' ) ) {

	final class Metrics_Query_Manager {

		private static $instance = null;

		public $config = null;

		public $frontend_actions = null;

		public $common_actions = null;

		public $backend_actions = null;

		public $tracking = null;

		public $frontend_item_reports = null;

		public $backend_setup = null;

		public $frontend_setup = null;

		public $backend_widgets = null;

		public $backend_item_reports = null;

		public $gapi_controller = null;

		/**
		 * Construct forbidden
		 */
		private function __construct() {
			if ( null !== self::$instance ) {
				_doing_it_wrong( __FUNCTION__, __( "This is not allowed, read the documentation!", 'google-analytics-board' ), '4.6' );
			}
		}

		/**
		 * Clone warning
		 */
		private function __clone() {
			_doing_it_wrong( __FUNCTION__, __( "This is not allowed, read the documentation!", 'google-analytics-board' ), '4.6' );
		}

		/**
		 * Wakeup warning
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( "This is not allowed, read the documentation!", 'google-analytics-board' ), '4.6' );
		}

		/**
		 * Creates a single instance for GADWP and makes sure only one instance is present in memory.
		 *
		 * @return Metrics_Query_Manager
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
				self::$instance->setup();
				self::$instance->config = new GADWP_Config();
			}
			return self::$instance;
		}

		/**
		 * Defines constants and loads required resources
		 */
		private function setup() {

			// Plugin Path
			if ( ! defined( 'METRICS_QUERY_DIR' ) ) {
				define( 'METRICS_QUERY_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin URL
			if ( ! defined( 'METRICS_QUERY_URL' ) ) {
				define( 'METRICS_QUERY_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin main File
			if ( ! defined( 'METRICS_QUERY_FILE' ) ) {
				define( 'METRICS_QUERY_FILE', __FILE__ );
			}

			/*
			 * Load Tools class
			 */
			include_once ( METRICS_QUERY_DIR . 'tools/tools.php' );

			/*
			 * Load Config class
			 */
			include_once ( METRICS_QUERY_DIR . 'config.php' );

			/*
			 * Load GAPI Controller class
			 */
			include_once ( METRICS_QUERY_DIR . 'tools/gapi.php' );

			/*
			 * Plugin i18n
			 */
			add_action( 'init', array( self::$instance, 'load_i18n' ) );

			/*
			 * Plugin Init
			 */
			add_action( 'init', array( self::$instance, 'load' ) );

			/*
			 * Include Install
			 */
			include_once ( METRICS_QUERY_DIR . 'install/install.php' );
			register_activation_hook( METRICS_QUERY_FILE, array( 'GADWP_Install', 'google-analytics-dashboard-for-wp 5' ) );

			/*
			 * Include Uninstall
			 */
			include_once ( METRICS_QUERY_DIR . 'install/uninstall.php' );
			register_uninstall_hook( METRICS_QUERY_FILE, array( 'GADWP_Uninstall', 'uninstall' ) );

			/*
			 * Load Frontend Widgets
			 * (needed during ajax)
			 */
			include_once ( METRICS_QUERY_DIR . 'front/widgets.php' );

			/*
			 * Add Frontend Widgets
			 * (needed during ajax)
			 */
			add_action( 'widgets_init', array( self::$instance, 'add_frontend_widget' ) );
		}

		/**
		 * Load i18n
		 */
		public function load_i18n() {
			load_plugin_textdomain( 'google-analytics-board', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Register Frontend Widgets
		 */
		public function add_frontend_widget() {
			register_widget( 'GADWP_Frontend_Widget' );
		}

		/**
		 * Conditional load
		 */
		public function load() {
			if ( is_admin() ) {
				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					if ( GADWP_Tools::check_roles( self::$instance->config->options['access_back'] ) ) {
						/*
						 * Load Backend ajax actions
						 */
						include_once ( METRICS_QUERY_DIR . 'admin/ajax-actions.php' );
						self::$instance->backend_actions = new GADWP_Backend_Ajax();
					}

					/*
					 * Load Frontend ajax actions
					 */
					include_once ( METRICS_QUERY_DIR . 'front/ajax-actions.php' );
					self::$instance->frontend_actions = new GADWP_Frontend_Ajax();

					/*
					 * Load Common ajax actions
					 */
					include_once ( METRICS_QUERY_DIR . 'common/ajax-actions.php' );
					self::$instance->common_actions = new GADWP_Common_Ajax();

					if ( self::$instance->config->options['backend_item_reports'] ) {
						/*
						 * Load Backend Item Reports for Quick Edit
						 */
						include_once ( METRICS_QUERY_DIR . 'admin/item-reports.php' );
						self::$instance->backend_item_reports = new GADWP_Backend_Item_Reports();
					}
				} else if ( GADWP_Tools::check_roles( self::$instance->config->options['access_back'] ) ) {

					/*
					 * Load Backend Setup
					 */
					include_once ( METRICS_QUERY_DIR . 'admin/setup.php' );
					self::$instance->backend_setup = new GADWP_Backend_Setup();

					if ( self::$instance->config->options['dashboard_widget'] ) {
						/*
						 * Load Backend Widget
						 */
						include_once ( METRICS_QUERY_DIR . 'admin/widgets.php' );
						self::$instance->backend_widgets = new GADWP_Backend_Widgets();
					}

					if ( self::$instance->config->options['backend_item_reports'] ) {
						/*
						 * Load Backend Item Reports
						 */
						include_once ( METRICS_QUERY_DIR . 'admin/item-reports.php' );
						self::$instance->backend_item_reports = new GADWP_Backend_Item_Reports();
					}
				}
			} else {
				if ( GADWP_Tools::check_roles( self::$instance->config->options['access_front'] ) ) {
					/*
					 * Load Frontend Setup
					 */
					include_once ( METRICS_QUERY_DIR . 'front/setup.php' );
					self::$instance->frontend_setup = new GADWP_Frontend_Setup();

					if ( self::$instance->config->options['frontend_item_reports'] ) {
						/*
						 * Load Frontend Item Reports
						 */
						include_once ( METRICS_QUERY_DIR . 'front/item-reports.php' );
						self::$instance->frontend_item_reports = new GADWP_Frontend_Item_Reports();
					}
				}

				if ( ! GADWP_Tools::check_roles( self::$instance->config->options['track_exclude'], true ) && 'disabled' != self::$instance->config->options['tracking_type'] ) {
					/*
					 * Load tracking class
					 */
					include_once ( METRICS_QUERY_DIR . 'front/tracking.php' );
					self::$instance->tracking = new GADWP_Tracking();
				}
			}
		}
	}
}

/**
 * Returns a unique instance of GADWP
 */
function GAB() {
	return Metrics_Query_Manager::instance();
}

function mb_check_for_gadwp() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';
    }

	if ( is_plugin_active( 'google-analytics-dashboard-for-wp/gadwp.php' ) ) {

		add_action( 'admin_init', function() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		} );

		add_action( 'admin_notices', function () {
			echo '<div class="error"><p><strong>Metrics Query</strong> plugin is a fork (twin brother) of <u><i>Google Analytics Dashboard For WP</i></u>, you must disable it before you can use this plugin.</p></div>';

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] ); }
		});
	} else {
		GAB();
	}
}
add_action( 'plugins_loaded', 'mb_check_for_gadwp', 20 );
