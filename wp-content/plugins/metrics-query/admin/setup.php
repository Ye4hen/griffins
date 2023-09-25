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

if ( ! class_exists( 'GADWP_Backend_Setup' ) ) {

	final class GADWP_Backend_Setup {

		private $gadwp;

		public function __construct() {
			$this->gadwp = GAB();

			// Styles & Scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
			// Site Menu
			add_action( 'admin_menu', array( $this, 'site_menu' ) );
			// Network Menu
			add_action( 'network_admin_menu', array( $this, 'network_menu' ) );
			// Settings link
			add_filter( "plugin_action_links_" . plugin_basename( METRICS_QUERY_DIR . 'gadwp.php' ), array( $this, 'settings_link' ) );
			// Updated admin notice
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}

		/**
		 * Add Site Menu
		 */
		public function site_menu() {
			global $wp_version;
			if ( current_user_can( 'manage_options' ) ) {
				include ( METRICS_QUERY_DIR . 'admin/settings.php' );
				add_menu_page( __( "Google Analytics", 'google-analytics-board' ), __( "Metrics Query", 'google-analytics-board' ), 'manage_options', 'gadwp_settings', array( 'GADWP_Settings', 'general_settings' ), version_compare( $wp_version, '3.8.0', '>=' ) ? 'dashicons-chart-area' : METRICS_QUERY_URL . 'admin/images/gadwp-icon.png' );
				add_submenu_page( 'gadwp_settings', __( "General Settings", 'google-analytics-board' ), __( "General Settings", 'google-analytics-board' ), 'manage_options', 'gadwp_settings', array( 'GADWP_Settings', 'general_settings' ) );
				add_submenu_page( 'gadwp_settings', __( "Backend Settings", 'google-analytics-board' ), __( "Backend Settings", 'google-analytics-board' ), 'manage_options', 'gadwp_backend_settings', array( 'GADWP_Settings', 'backend_settings' ) );
				add_submenu_page( 'gadwp_settings', __( "Frontend Settings", 'google-analytics-board' ), __( "Frontend Settings", 'google-analytics-board' ), 'manage_options', 'gadwp_frontend_settings', array( 'GADWP_Settings', 'frontend_settings' ) );
				add_submenu_page( 'gadwp_settings', __( "Tracking Code", 'google-analytics-board' ), __( "Tracking Code", 'google-analytics-board' ), 'manage_options', 'gadwp_tracking_settings', array( 'GADWP_Settings', 'tracking_settings' ) );
				add_submenu_page( 'gadwp_settings', __( "Errors & Debug", 'google-analytics-board' ), __( "Errors & Debug", 'google-analytics-board' ), 'manage_options', 'gadwp_errors_debugging', array( 'GADWP_Settings', 'errors_debugging' ) );
			}
		}

		/**
		 * Add Network Menu
		 */
		public function network_menu() {
			global $wp_version;
			if ( current_user_can( 'manage_network' ) ) {
				include ( METRICS_QUERY_DIR . 'admin/settings.php' );
				add_menu_page( __( "Google Analytics", 'google-analytics-board' ), "Google Analytics", 'manage_network', 'gadwp_settings', array( 'GADWP_Settings', 'general_settings_network' ), version_compare( $wp_version, '3.8.0', '>=' ) ? 'dashicons-chart-area' : METRICS_QUERY_URL . 'admin/images/gadwp-icon.png' );
				add_submenu_page( 'gadwp_settings', __( "General Settings", 'google-analytics-board' ), __( "General Settings", 'google-analytics-board' ), 'manage_network', 'gadwp_settings', array( 'GADWP_Settings', 'general_settings_network' ) );
				add_submenu_page( 'gadwp_settings', __( "Errors & Debug", 'google-analytics-board' ), __( "Errors & Debug", 'google-analytics-board' ), 'manage_network', 'gadwp_errors_debugging', array( 'GADWP_Settings', 'errors_debugging' ) );
			}
		}

		/**
		 * Styles & Scripts conditional loading (based on current URI)
		 *
		 * @param
		 *            $hook
		 */
		public function load_styles_scripts( $hook ) {
			$new_hook = explode( '_page_', $hook );

			if ( isset( $new_hook[1] ) ) {
				$new_hook = '_page_' . $new_hook[1];
			} else {
				$new_hook = $hook;
			}

			/*
			 * GADWP main stylesheet
			 */
			wp_enqueue_style( 'gadwp', METRICS_QUERY_URL . 'admin/css/gadwp.css', null, GADWP_CURRENT_VERSION );

			/*
			 * GADWP UI
			 */

			if ( GADWP_Tools::get_cache( 'gapi_errors' ) ) {
				$ed_bubble = '!';
			} else {
				$ed_bubble = '';
			}

			wp_enqueue_script( 'gadwp-backend-ui', plugins_url( 'js/ui.js', __FILE__ ), array( 'jquery' ), GADWP_CURRENT_VERSION, true );

			/* @formatter:off */
			wp_localize_script( 'gadwp-backend-ui', 'gadwp_ui_data', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'gadwp_dismiss_notices' ),
				'ed_bubble' => $ed_bubble,
			)
			);
			/* @formatter:on */

			if ( $this->gadwp->config->options['switch_profile'] && count( $this->gadwp->config->options['ga_profiles_list'] ) > 1 ) {
				$views = array();
				foreach ( $this->gadwp->config->options['ga_profiles_list'] as $items ) {
					if ( $items[3] ) {
						$views[$items[1]] = esc_js( GADWP_Tools::strip_protocol( $items[3] ) ); // . ' &#8658; ' . $items[0] );
					}
				}
			} else {
				$views = false;
			}

			/*
			 * Main Dashboard Widgets Styles & Scripts
			 */
			$widgets_hooks = array( 'index.php' );

			if ( in_array( $new_hook, $widgets_hooks ) ) {
				if ( GADWP_Tools::check_roles( $this->gadwp->config->options['access_back'] ) && $this->gadwp->config->options['dashboard_widget'] ) {

					if ( $this->gadwp->config->options['ga_target_geomap'] ) {
						$country_codes = GADWP_Tools::get_countrycodes();
						if ( isset( $country_codes[$this->gadwp->config->options['ga_target_geomap']] ) ) {
							$region = $this->gadwp->config->options['ga_target_geomap'];
						} else {
							$region = false;
						}
					} else {
						$region = false;
					}

					wp_enqueue_style( 'gadwp-nprogress', METRICS_QUERY_URL . 'common/nprogress/nprogress.css', null, GADWP_CURRENT_VERSION );

					wp_enqueue_style( 'gadwp-backend-item-reports', METRICS_QUERY_URL . 'admin/css/admin-widgets.css', null, GADWP_CURRENT_VERSION );

					wp_register_style( 'jquery-ui-tooltip-html', METRICS_QUERY_URL . 'common/realtime/jquery.ui.tooltip.html.css' );

					wp_enqueue_style( 'jquery-ui-tooltip-html' );

					wp_register_script( 'jquery-ui-tooltip-html', METRICS_QUERY_URL . 'common/realtime/jquery.ui.tooltip.html.js' );

					wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

					wp_enqueue_script( 'gadwp-nprogress', METRICS_QUERY_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GADWP_CURRENT_VERSION );

					wp_enqueue_script( 'gadwp-backend-dashboard-reports', METRICS_QUERY_URL . 'common/js/reports5.js', array( 'jquery', 'googlecharts', 'gadwp-nprogress', 'jquery-ui-tooltip', 'jquery-ui-core', 'jquery-ui-position', 'jquery-ui-tooltip-html' ), GADWP_CURRENT_VERSION, true );

					/* @formatter:off */

					$datelist = array(
						'realtime' => __( "Real-Time", 'google-analytics-board' ),
						'today' => __( "Today", 'google-analytics-board' ),
						'yesterday' => __( "Yesterday", 'google-analytics-board' ),
						'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 7 ),
						'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 14 ),
						'30daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 30 ),
						'90daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 90 ),
						'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'google-analytics-board' ), __('One', 'google-analytics-board') ),
						'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'google-analytics-board' ), __('Three', 'google-analytics-board') ),
					);


					if ( $this->gadwp->config->options['user_api'] && ! $this->gadwp->config->options['backend_realtime_report'] ) {
						array_shift( $datelist );
					}

					wp_localize_script( 'gadwp-backend-dashboard-reports', 'gadwpItemData', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'security' => wp_create_nonce( 'gadwp_backend_item_reports' ),
						'dateList' => $datelist,
						'reportList' => array(
							'sessions' => __( "Sessions", 'google-analytics-board' ),
							'users' => __( "Users", 'google-analytics-board' ),
							'organicSearches' => __( "Organic", 'google-analytics-board' ),
							'pageviews' => __( "Page Views", 'google-analytics-board' ),
							'visitBounceRate' => __( "Bounce Rate", 'google-analytics-board' ),
							'locations' => __( "Location", 'google-analytics-board' ),
							'contentpages' =>  __( "Pages", 'google-analytics-board' ),
							'referrers' => __( "Referrers", 'google-analytics-board' ),
							'searches' => __( "Searches", 'google-analytics-board' ),
							'trafficdetails' => __( "Traffic", 'google-analytics-board' ),
							'technologydetails' => __( "Technology", 'google-analytics-board' ),
							'404errors' => __( "404 Errors", 'google-analytics-board' ),
						),
						'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'google-analytics-board' ), //0
							__( "Traffic Mediums", 'google-analytics-board' ),
							__( "Visitor Type", 'google-analytics-board' ),
							__( "Search Engines", 'google-analytics-board' ),
							__( "Social Networks", 'google-analytics-board' ),
							__( "Sessions", 'google-analytics-board' ),
							__( "Users", 'google-analytics-board' ),
							__( "Page Views", 'google-analytics-board' ),
							__( "Bounce Rate", 'google-analytics-board' ),
							__( "Organic Search", 'google-analytics-board' ),
							__( "Pages/Session", 'google-analytics-board' ),
							__( "Invalid response", 'google-analytics-board' ),
							__( "No Data", 'google-analytics-board' ),
							__( "This report is unavailable", 'google-analytics-board' ),
							__( "report generated by", 'google-analytics-board' ), //14
							__( "This plugin needs an authorization:", 'google-analytics-board' ) . ' <a href="' . menu_page_url( 'gadwp_settings', false ) . '">' . __( "authorize the plugin", 'google-analytics-board' ) . '</a>.',
							__( "Browser", 'google-analytics-board' ), //16
							__( "Operating System", 'google-analytics-board' ),
							__( "Screen Resolution", 'google-analytics-board' ),
							__( "Mobile Brand", 'google-analytics-board' ),
							__( "REFERRALS", 'google-analytics-board' ), //20
							__( "KEYWORDS", 'google-analytics-board' ),
							__( "SOCIAL", 'google-analytics-board' ),
							__( "CAMPAIGN", 'google-analytics-board' ),
							__( "DIRECT", 'google-analytics-board' ),
							__( "NEW", 'google-analytics-board' ), //25
							__( "Time on Page", 'google-analytics-board' ),
							__( "Page Load Time", 'google-analytics-board' ),
							__( "Session Duration", 'google-analytics-board' ),
						),
						'rtLimitPages' => $this->gadwp->config->options['ga_realtime_pages'],
						'colorVariations' => GADWP_Tools::variations( $this->gadwp->config->options['theme_color'] ),
						'region' => $region,
						'mapsApiKey' => apply_filters( 'gadwp_maps_api_key', $this->gadwp->config->options['maps_api_key'] ),
						'language' => get_bloginfo( 'language' ),
						'viewList' => $views,
						'scope' => 'admin-widgets',
					)

					);
					/* @formatter:on */
				}
			}

			/*
			 * Posts/Pages List Styles & Scripts
			 */
			$contentstats_hooks = array( 'edit.php' );
			if ( in_array( $hook, $contentstats_hooks ) ) {
				if ( GADWP_Tools::check_roles( $this->gadwp->config->options['access_back'] ) && $this->gadwp->config->options['backend_item_reports'] ) {

					if ( $this->gadwp->config->options['ga_target_geomap'] ) {
						$country_codes = GADWP_Tools::get_countrycodes();
						if ( isset( $country_codes[$this->gadwp->config->options['ga_target_geomap']] ) ) {
							$region = $this->gadwp->config->options['ga_target_geomap'];
						} else {
							$region = false;
						}
					} else {
						$region = false;
					}

					wp_enqueue_style( 'gadwp-nprogress', METRICS_QUERY_URL . 'common/nprogress/nprogress.css', null, GADWP_CURRENT_VERSION );

					wp_enqueue_style( 'gadwp-backend-item-reports', METRICS_QUERY_URL . 'admin/css/item-reports.css', null, GADWP_CURRENT_VERSION );

					wp_enqueue_style( "wp-jquery-ui-dialog" );

					wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

					wp_enqueue_script( 'gadwp-nprogress', METRICS_QUERY_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GADWP_CURRENT_VERSION );

					wp_enqueue_script( 'gadwp-backend-item-reports', METRICS_QUERY_URL . 'common/js/reports5.js', array( 'gadwp-nprogress', 'googlecharts', 'jquery', 'jquery-ui-dialog' ), GADWP_CURRENT_VERSION, true );

					/* @formatter:off */
					wp_localize_script( 'gadwp-backend-item-reports', 'gadwpItemData', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'security' => wp_create_nonce( 'gadwp_backend_item_reports' ),
						'dateList' => array(
							'today' => __( "Today", 'google-analytics-board' ),
							'yesterday' => __( "Yesterday", 'google-analytics-board' ),
							'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 7 ),
							'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 14 ),
							'30daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 30 ),
							'90daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-board' ), 90 ),
							'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'google-analytics-board' ), __('One', 'google-analytics-board') ),
							'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'google-analytics-board' ), __('Three', 'google-analytics-board') ),
						),
						'reportList' => array(
							'uniquePageviews' => __( "Unique Views", 'google-analytics-board' ),
							'users' => __( "Users", 'google-analytics-board' ),
							'organicSearches' => __( "Organic", 'google-analytics-board' ),
							'pageviews' => __( "Page Views", 'google-analytics-board' ),
							'visitBounceRate' => __( "Bounce Rate", 'google-analytics-board' ),
							'locations' => __( "Location", 'google-analytics-board' ),
							'referrers' => __( "Referrers", 'google-analytics-board' ),
							'searches' => __( "Searches", 'google-analytics-board' ),
							'trafficdetails' => __( "Traffic", 'google-analytics-board' ),
							'technologydetails' => __( "Technology", 'google-analytics-board' ),
						),
						'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'google-analytics-board' ), //0
							__( "Traffic Mediums", 'google-analytics-board' ),
							__( "Visitor Type", 'google-analytics-board' ),
							__( "Social Networks", 'google-analytics-board' ),
							__( "Search Engines", 'google-analytics-board' ),
							__( "Unique Views", 'google-analytics-board' ),
							__( "Users", 'google-analytics-board' ),
							__( "Page Views", 'google-analytics-board' ),
							__( "Bounce Rate", 'google-analytics-board' ),
							__( "Organic Search", 'google-analytics-board' ),
							__( "Pages/Session", 'google-analytics-board' ),
							__( "Invalid response", 'google-analytics-board' ),
							__( "No Data", 'google-analytics-board' ),
							__( "This report is unavailable", 'google-analytics-board' ),
							__( "report generated by", 'google-analytics-board' ), //14
							__( "This plugin needs an authorization:", 'google-analytics-board' ) . ' <a href="' . menu_page_url( 'gadwp_settings', false ) . '">' . __( "authorize the plugin", 'google-analytics-board' ) . '</a>.',
							__( "Browser", 'google-analytics-board' ), //16
							__( "Operating System", 'google-analytics-board' ),
							__( "Screen Resolution", 'google-analytics-board' ),
							__( "Mobile Brand", 'google-analytics-board' ), //19
							__( "Future Use", 'google-analytics-board' ),
							__( "Future Use", 'google-analytics-board' ),
							__( "Future Use", 'google-analytics-board' ),
							__( "Future Use", 'google-analytics-board' ),
							__( "Future Use", 'google-analytics-board' ),
							__( "Future Use", 'google-analytics-board' ), //25
							__( "Time on Page", 'google-analytics-board' ),
							__( "Page Load Time", 'google-analytics-board' ),
							__( "Exit Rate", 'google-analytics-board' ),
						),
						'colorVariations' => GADWP_Tools::variations( $this->gadwp->config->options['theme_color'] ),
						'region' => $region,
						'mapsApiKey' => apply_filters( 'gadwp_maps_api_key', $this->gadwp->config->options['maps_api_key'] ),
						'language' => get_bloginfo( 'language' ),
						'viewList' => false,
						'scope' => 'admin-item',
						)
					);
					/* @formatter:on */
				}
			}

			/*
			 * Settings Styles & Scripts
			 */
			$settings_hooks = array( '_page_gadwp_settings', '_page_gadwp_backend_settings', '_page_gadwp_frontend_settings', '_page_gadwp_tracking_settings', '_page_gadwp_errors_debugging' );

			if ( in_array( $new_hook, $settings_hooks ) ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker-script-handle', plugins_url( 'js/wp-color-picker-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
				wp_enqueue_script( 'gadwp-settings', plugins_url( 'js/settings.js', __FILE__ ), array( 'jquery' ), GADWP_CURRENT_VERSION, true );
			}
		}

		/**
		 * Add "Settings" link in Plugins List
		 *
		 * @param
		 *            $links
		 * @return array
		 */
		public function settings_link( $links ) {
			$settings_link = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=gadwp_settings' ) ) . '">' . __( "Settings", 'google-analytics-board' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 *  Add an admin notice after a manual or atuomatic update
		 */
		function admin_notice() {
			$currentScreen = get_current_screen();

			if ( ! current_user_can( 'manage_options' ) || strpos( $currentScreen->base, '_gadwp_' ) === false ) {
				return;
			}

			if ( get_option( 'gadwp_got_updated' ) ) :
				?>
<div id="gadwp-notice" class="notice is-dismissible">
	<p><?php echo sprintf( __('Google Analytics Dashboard for WP has been updated to version %s.', 'google-analytics-board' ), GADWP_CURRENT_VERSION).' '.sprintf( __('For details, check out %1$s.', 'google-analytics-board' ), sprintf(' <a href="https://metricsquery.com/?utm_source=gadwp_notice&utm_medium=link&utm_content=release_notice&utm_campaign=gadwp">%s</a>', __('the plugin documentation', 'google-analytics-board') ) ); ?></p>
</div>

			<?php
			endif;
		}
	}
}
