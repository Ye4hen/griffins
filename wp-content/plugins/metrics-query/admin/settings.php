<?php
/**
 * Author: Yehuda Hassine
 * Author URI: https://metricsquery.com
 * Copyright 2013 by Alin Marcu and forked by Yehuda Hassine
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

final class GADWP_Settings {

    static function clean_options() {
        $clean = array();
        foreach ( $_POST['options'] as $key => $value ) {
            if ( is_array( $value ) ) {
                $clean[$key] = array_map( 'sanitize_text_field', $value );
            } else {
                $clean[$key] = sanitize_text_field( $value );
            }
        }

        return $clean;
    }

	private static function update_options( $who ) {
		$gadwp = GAB();
		$network_settings = false;
		$options = $gadwp->config->options; // Get current options
		if ( isset( $_POST['options']['gadwp_hidden'] ) && isset( $_POST['options'] ) && ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) && 'Reset' != $who ) {
			$new_options = self::clean_options();
			if ( 'tracking' == $who ) {
				$options['ga_anonymize_ip'] = 0;
				$options['ga_optout'] = 0;
				$options['ga_dnt_optout'] = 0;
				$options['ga_event_tracking'] = 0;
				$options['ga_enhanced_links'] = 0;
				$options['ga_event_precision'] = 0;
				$options['ga_remarketing'] = 0;
				$options['ga_event_bouncerate'] = 0;
				$options['ga_crossdomain_tracking'] = 0;
				$options['ga_aff_tracking'] = 0;
				$options['ga_hash_tracking'] = 0;
				$options['ga_formsubmit_tracking'] = 0;
				$options['ga_force_ssl'] = 0;
				$options['ga_pagescrolldepth_tracking'] = 0;
				$options['tm_pagescrolldepth_tracking'] = 0;
				$options['tm_optout'] = 0;
				$options['tm_dnt_optout'] = 0;
				$options['amp_tracking_analytics'] = 0;
				$options['amp_tracking_clientidapi'] = 0;
				$options['amp_tracking_tagmanager'] = 0;
				$options['optimize_pagehiding'] = 0;
				$options['optimize_tracking'] = 0;
				$options['trackingcode_infooter'] = 0;
				$options['trackingevents_infooter'] = 0;
				$options['ga_with_gtag'] = 0;
				if ( isset( $_POST['options']['ga_tracking_code'] ) ) {
					$new_options['ga_tracking_code'] = trim( $new_options['ga_tracking_code'], "\t" );
				}
				if ( empty( $new_options['track_exclude'] ) ) {
					$new_options['track_exclude'] = array();
				}
			} elseif ( 'backend' == $who ) {
				$options['switch_profile'] = 0;
				$options['backend_item_reports'] = 0;
				$options['dashboard_widget'] = 0;
				$options['backend_realtime_report'] = 0;
				if ( empty( $new_options['access_back'] ) ) {
					$new_options['access_back'][] = 'administrator';
				}
			} elseif ( 'frontend' == $who ) {
				$options['frontend_item_reports'] = 0;
				if ( empty( $new_options['access_front'] ) ) {
					$new_options['access_front'][] = 'administrator';
				}
			} elseif ( 'general' == $who ) {
				$options['user_api'] = 0;
				if ( ! is_multisite() ) {
					$options['automatic_updates_minorversion'] = 0;
				}
			} elseif ( 'network' == $who ) {
				$options['user_api'] = 0;
				$options['network_mode'] = 0;
				$options['superadmin_tracking'] = 0;
				$options['automatic_updates_minorversion'] = 0;
				$network_settings = true;
			}
			$options = array_merge( $options, $new_options );
			$gadwp->config->options = $options;
			$gadwp->config->set_plugin_options( $network_settings );
		}
		return $options;
	}

	private static function navigation_tabs( $tabs ) {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			echo "<a class='nav-tab' id='tab-$tab' href='#top#gadwp-$tab'>$name</a>";
		}
		echo '</h2>';
	}

	public static function frontend_settings() {
		$gadwp = GAB();
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options = self::update_options( 'frontend' );
		if ( isset( $_POST['options']['gadwp_hidden'] ) ) {
			$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Settings saved.", 'google-analytics-board' ) . "</p></div>";
			if ( ! ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) ) {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( ! $gadwp->config->options['tableid_jail'] || ! $gadwp->config->options['token'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'google-analytics-board' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_errors_debugging', false ), __( 'Errors & Debug', 'google-analytics-board' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_settings', false ), __( 'authorize the plugin', 'google-analytics-board' ) ) ) );
		}
		?>
<form name="gadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
	<div class="wrap">
	<?php echo "<h2>" . __( "Google Analytics Frontend Settings", 'google-analytics-board' ) . "</h2>"; ?><hr>
	</div>
	<div id="poststuff" class="gadwp">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="settings-wrapper">
					<div class="inside">
					<?php if (isset($message)) {echo $message;} ?>
						<table class="gadwp-settings-options">
							<tr>
								<td colspan="2"><?php echo "<h2>" . __( "Permissions", 'google-analytics-board' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td class="roles gadwp-settings-title">
									<label for="access_front"><?php _e("Show stats to:", 'google-analytics-board' ); ?>
									</label>
								</td>
								<td class="gadwp-settings-roles">
									<table>
										<tr>
										<?php if ( ! isset( $wp_roles ) ) : ?>
											<?php $wp_roles = new WP_Roles(); ?>
										<?php endif; ?>
										<?php $i = 0; ?>
										<?php foreach ( $wp_roles->role_names as $role => $name ) : ?>
											<?php if ( 'subscriber' != $role ) : ?>
												<?php $i++; ?>
												<td>
												<label>
													<input type="checkbox" name="options[access_front][]" value="<?php echo $role; ?>" <?php if ( in_array($role,$options['access_front']) || 'administrator' == $role ) {echo 'checked="checked"';} if ( 'administrator' == $role ) {echo 'disabled="disabled"';}?> /><?php echo $name; ?>
												  </label>
											</td>
											<?php endif; ?>
											<?php if ( 0 == $i % 4 ) : ?>
										 </tr>
										<tr>
											<?php endif; ?>
										<?php endforeach; ?>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<div class="button-primary gadwp-settings-switchoo">
										<input type="checkbox" name="options[frontend_item_reports]" value="1" class="gadwp-settings-switchoo-checkbox" id="frontend_item_reports" <?php checked( $options['frontend_item_reports'], 1 ); ?>>
										<label class="gadwp-settings-switchoo-label" for="frontend_item_reports">
											<div class="gadwp-settings-switchoo-inner"></div>
											<div class="gadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php echo " ".__("enable web page reports on frontend", 'google-analytics-board' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="submit">
									<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'google-analytics-board' ) ?>" />
								</td>
							</tr>
						</table>
						<input type="hidden" name="options[gadwp_hidden]" value="Y">
						<?php wp_nonce_field('gadwp_form','gadwp_security');?>






</form>
<?php
		self::output_sidebar();
	}

	public static function backend_settings() {
		$gadwp = GAB();
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options = self::update_options( 'backend' );
		if ( isset( $_POST['options']['gadwp_hidden'] ) ) {
			$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Settings saved.", 'google-analytics-board' ) . "</p></div>";
			if ( ! ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) ) {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( ! $gadwp->config->options['tableid_jail'] || ! $gadwp->config->options['token'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'google-analytics-board' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_errors_debugging', false ), __( 'Errors & Debug', 'google-analytics-board' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_settings', false ), __( 'authorize the plugin', 'google-analytics-board' ) ) ) );
		}
		?>
<form name="gadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
	<div class="wrap">
			<?php echo "<h2>" . __( "Google Analytics Backend Settings", 'google-analytics-board' ) . "</h2>"; ?><hr>
	</div>
	<div id="poststuff" class="gadwp">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="settings-wrapper">
					<div class="inside">
					<?php if (isset($message)) {echo $message;} ?>
						<table class="gadwp-settings-options">
							<tr>
								<td colspan="2"><?php echo "<h2>" . __( "Permissions", 'google-analytics-board' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td class="roles gadwp-settings-title">
									<label for="access_back"><?php _e("Show stats to:", 'google-analytics-board' ); ?>
									</label>
								</td>
								<td class="gadwp-settings-roles">
									<table>
										<tr>
										<?php if ( ! isset( $wp_roles ) ) : ?>
											<?php $wp_roles = new WP_Roles(); ?>
										<?php endif; ?>
										<?php $i = 0; ?>
										<?php foreach ( $wp_roles->role_names as $role => $name ) : ?>
											<?php if ( 'subscriber' != $role ) : ?>
												<?php $i++; ?>
											<td>
												<label>
													<input type="checkbox" name="options[access_back][]" value="<?php echo $role; ?>" <?php if ( in_array($role,$options['access_back']) || 'administrator' == $role ) {echo 'checked="checked"';} if ( 'administrator' == $role ) {echo 'disabled="disabled"';}?> /> <?php echo $name; ?>
												</label>
											</td>
											<?php endif; ?>
											<?php if ( 0 == $i % 4 ) : ?>
										</tr>
										<tr>
											<?php endif; ?>
										<?php endforeach; ?>






									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<div class="button-primary gadwp-settings-switchoo">
										<input type="checkbox" name="options[switch_profile]" value="1" class="gadwp-settings-switchoo-checkbox" id="switch_profile" <?php checked( $options['switch_profile'], 1 ); ?>>
										<label class="gadwp-settings-switchoo-label" for="switch_profile">
											<div class="gadwp-settings-switchoo-inner"></div>
											<div class="gadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable Switch View functionality", 'google-analytics-board' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<div class="button-primary gadwp-settings-switchoo">
										<input type="checkbox" name="options[backend_item_reports]" value="1" class="gadwp-settings-switchoo-checkbox" id="backend_item_reports" <?php checked( $options['backend_item_reports'], 1 ); ?>>
										<label class="gadwp-settings-switchoo-label" for="backend_item_reports">
											<div class="gadwp-settings-switchoo-inner"></div>
											<div class="gadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable reports on Posts List and Pages List", 'google-analytics-board' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<div class="button-primary gadwp-settings-switchoo">
										<input type="checkbox" name="options[dashboard_widget]" value="1" class="gadwp-settings-switchoo-checkbox" id="dashboard_widget" <?php checked( $options['dashboard_widget'], 1 ); ?>>
										<label class="gadwp-settings-switchoo-label" for="dashboard_widget">
											<div class="gadwp-settings-switchoo-inner"></div>
											<div class="gadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable the main Dashboard Widget", 'google-analytics-board' );?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr><?php echo "<h2>" . __( "Real-Time Settings", 'google-analytics-board' ) . "</h2>"; ?></td>
							</tr>
							<?php if ( $options['user_api'] ) : ?>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<div class="button-primary gadwp-settings-switchoo">
										<input type="checkbox" name="options[backend_realtime_report]" value="1" class="gadwp-settings-switchoo-checkbox" id="backend_realtime_report" <?php checked( $options['backend_realtime_report'], 1 ); ?>>
										<label class="gadwp-settings-switchoo-label" for="backend_realtime_report">
											<div class="gadwp-settings-switchoo-inner"></div>
											<div class="gadwp-settings-switchoo-switch"></div>
										</label>
									</div>
									<div class="switch-desc"><?php _e ( "enable Real-Time report (requires access to Real-Time Reporting API)", 'google-analytics-board' );?></div>
								</td>
							</tr>
							<?php endif; ?>
							<tr>
								<td colspan="2" class="gadwp-settings-title"> <?php _e("Maximum number of pages to display on real-time tab:", 'google-analytics-board'); ?>
									<input type="number" name="options[ga_realtime_pages]" id="ga_realtime_pages" value="<?php echo (int)$options['ga_realtime_pages']; ?>" size="3">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr><?php echo "<h2>" . __( "Location Settings", 'google-analytics-board' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<?php echo __("Target Geo Map to country:", 'google-analytics-board'); ?>
									<input type="text" style="text-align: center;" name="options[ga_target_geomap]" value="<?php echo esc_attr($options['ga_target_geomap']); ?>" size="3">
								</td>
							</tr>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<?php echo __("Maps API Key:", 'google-analytics-board'); ?>
									<input type="text" style="text-align: center;" name="options[maps_api_key]" value="<?php echo esc_attr($options['maps_api_key']); ?>" size="50">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr><?php echo "<h2>" . __( "404 Errors Report", 'google-analytics-board' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="gadwp-settings-title">
									<?php echo __("404 Page Title contains:", 'google-analytics-board'); ?>
									<input type="text" style="text-align: center;" name="options[pagetitle_404]" value="<?php echo esc_attr($options['pagetitle_404']); ?>" size="20">
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="submit">
									<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'google-analytics-board' ) ?>" />
								</td>
							</tr>
						</table>
						<input type="hidden" name="options[gadwp_hidden]" value="Y">
						<?php wp_nonce_field('gadwp_form','gadwp_security'); ?>






</form>
<?php
		self::output_sidebar();
	}

	public static function tracking_settings() {
		$gadwp = GAB();

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options = self::update_options( 'tracking' );
		if ( isset( $_POST['options']['gadwp_hidden'] ) ) {
			$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Settings saved.", 'google-analytics-board' ) . "</p></div>";
			if ( ! ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) ) {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( ! $gadwp->config->options['tableid_jail'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'google-analytics-board' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_errors_debugging', false ), __( 'Errors & Debug', 'google-analytics-board' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_settings', false ), __( 'authorize the plugin', 'google-analytics-board' ) ) ) );
		}
		?>
<form name="gadwp_form" method="post" action="<?php  esc_url($_SERVER['REQUEST_URI']); ?>">
	<div class="wrap">
			<?php echo "<h2>" . __( "Google Analytics Tracking Code", 'google-analytics-board' ) . "</h2>"; ?>
	</div>
	<div id="poststuff" class="gadwp">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="settings-wrapper">
					<div class="inside">
						<?php if ( 'universal' == $options['tracking_type'] ) :?>
						<?php $tabs = array( 'basic' => __( "Basic Settings", 'google-analytics-board' ), 'events' => __( "Events Tracking", 'google-analytics-board' ), 'custom' => __( "Custom Definitions", 'google-analytics-board' ), 'exclude' => __( "Exclude Tracking", 'google-analytics-board' ), 'advanced' => __( "Advanced Settings", 'google-analytics-board' ), 'integration' => __( "Integration", 'google-analytics-board' ) );?>
						<?php elseif ( 'tagmanager' == $options['tracking_type'] ) :?>
						<?php $tabs = array( 'basic' => __( "Basic Settings", 'google-analytics-board' ), 'tmdatalayervars' => __( "DataLayer Variables", 'google-analytics-board' ), 'exclude' => __( "Exclude Tracking", 'google-analytics-board' ), 'tmadvanced' =>  __( "Advanced Settings", 'google-analytics-board' ), 'tmintegration' => __( "Integration", 'google-analytics-board' ) );?>
						<?php else :?>
						<?php $tabs = array( 'basic' => __( "Basic Settings", 'google-analytics-board' ) );?>
						<?php endif; ?>
						<?php self::navigation_tabs( $tabs ); ?>
						<?php if ( isset( $message ) ) : ?>
							<?php echo $message; ?>
						<?php endif; ?>
						<div id="gadwp-basic">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Tracking Settings", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tracking_type"><?php _e("Tracking Type:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="tracking_type" name="options[tracking_type]" onchange="this.form.submit()">
											<option value="universal" <?php selected( $options['tracking_type'], 'universal' ); ?>><?php _e("Analytics", 'google-analytics-board');?></option>
											<option value="tagmanager" <?php selected( $options['tracking_type'], 'tagmanager' ); ?>><?php _e("Tag Manager", 'google-analytics-board');?></option>
											<option value="disabled" <?php selected( $options['tracking_type'], 'disabled' ); ?>><?php _e("Disabled", 'google-analytics-board');?></option>
										</select>
									</td>
								</tr>
								<?php if ( 'universal' == $options['tracking_type'] ) : ?>
								<tr>
									<td class="gadwp-settings-title"></td>
									<td>
										<?php $profile_info = GADWP_Tools::get_selected_profile($gadwp->config->options['ga_profiles_list'], $gadwp->config->options['tableid_jail']); ?>
										<?php echo '<pre>' . __("View Name:", 'google-analytics-board') . "\t" . esc_html($profile_info[0]) . "<br />" . __("Tracking ID:", 'google-analytics-board') . "\t" . esc_html($profile_info[2]) . "<br />" . __("Default URL:", 'google-analytics-board') . "\t" . esc_html($profile_info[3]) . "<br />" . __("Time Zone:", 'google-analytics-board') . "\t" . esc_html($profile_info[5]) . '</pre>';?>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_with_gtag]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_with_gtag" <?php checked( $options['ga_with_gtag'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_with_gtag">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("use global site tag gtag.js (not recommended)", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<?php elseif ( 'tagmanager' == $options['tracking_type'] ) : ?>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tracking_type"><?php _e("Web Container ID:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<input type="text" name="options[web_containerid]" value="<?php echo esc_attr($options['web_containerid']); ?>" size="15">
									</td>
								</tr>
								<?php endif; ?>
								<tr>
									<td class="gadwp-settings-title">
										<label for="trackingcode_infooter"><?php _e("Code Placement:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="trackingcode_infooter" name="options[trackingcode_infooter]">
											<option value="0" <?php selected( $options['trackingcode_infooter'], 0 ); ?>><?php _e("HTML Head", 'google-analytics-board');?></option>
											<option value="1" <?php selected( $options['trackingcode_infooter'], 1 ); ?>><?php _e("HTML Body", 'google-analytics-board');?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-events">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Events Tracking", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_event_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_event_tracking" <?php checked( $options['ga_event_tracking'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_event_tracking">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track downloads, mailto, telephone and outbound links", 'google-analytics-board' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_aff_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_aff_tracking" <?php checked( $options['ga_aff_tracking'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_aff_tracking">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track affiliate links", 'google-analytics-board' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_hash_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_hash_tracking" <?php checked( $options['ga_hash_tracking'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_hash_tracking">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track fragment identifiers, hashmarks (#) in URI links", 'google-analytics-board' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_formsubmit_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_formsubmit_tracking" <?php checked( $options['ga_formsubmit_tracking'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_formsubmit_tracking">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track form submit actions", 'google-analytics-board' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_pagescrolldepth_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_pagescrolldepth_tracking" <?php checked( $options['ga_pagescrolldepth_tracking'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_pagescrolldepth_tracking">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("track page scrolling depth", 'google-analytics-board' ); ?></div>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_event_downloads"><?php _e("Downloads Regex:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_event_downloads" name="options[ga_event_downloads]" value="<?php echo esc_attr($options['ga_event_downloads']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_event_affiliates"><?php _e("Affiliates Regex:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_event_affiliates" name="options[ga_event_affiliates]" value="<?php echo esc_attr($options['ga_event_affiliates']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="trackingevents_infooter"><?php _e("Code Placement:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="trackingevents_infooter" name="options[trackingevents_infooter]">
											<option value="0" <?php selected( $options['trackingevents_infooter'], 0 ); ?>><?php _e("HTML Head", 'google-analytics-board');?></option>
											<option value="1" <?php selected( $options['trackingevents_infooter'], 1 ); ?>><?php _e("HTML Body", 'google-analytics-board');?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-custom">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Custom Dimensions", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_author_dimindex"><?php _e("Authors:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_author_dimindex" name="options[ga_author_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_author_dimindex'], $i ); ?>><?php echo 0 == $i ?'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_pubyear_dimindex"><?php _e("Publication Year:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_pubyear_dimindex" name="options[ga_pubyear_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_pubyear_dimindex'], $i ); ?>><?php echo 0 == $i ?'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_pubyearmonth_dimindex"><?php _e("Publication Month:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_pubyearmonth_dimindex" name="options[ga_pubyearmonth_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_pubyearmonth_dimindex'], $i ); ?>><?php echo 0 == $i ?'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_category_dimindex"><?php _e("Categories:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_category_dimindex" name="options[ga_category_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_category_dimindex'], $i ); ?>><?php echo 0 == $i ? 'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_user_dimindex"><?php _e("User Type:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_user_dimindex" name="options[ga_user_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
											<option value="<?php echo $i;?>" <?php selected( $options['ga_user_dimindex'], $i ); ?>><?php echo 0 == $i ? 'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_tag_dimindex"><?php _e("Tags:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="ga_tag_dimindex" name="options[ga_tag_dimindex]">
										<?php for ($i=0;$i<21;$i++) : ?>
										<option value="<?php echo $i;?>" <?php selected( $options['ga_tag_dimindex'], $i ); ?>><?php echo 0 == $i ? 'Disabled':'dimension '.$i; ?></option>
										<?php endfor; ?>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-tmdatalayervars">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Main Variables", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tm_author_var"><?php _e("Authors:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_author_var" name="options[tm_author_var]">
											<option value="1" <?php selected( $options['tm_author_var'], 1 ); ?>>gadwpAuthor</option>
											<option value="0" <?php selected( $options['tm_author_var'], 0 ); ?>><?php _e( "Disabled", 'google-analytics-board' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tm_pubyear_var"><?php _e("Publication Year:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_pubyear_var" name="options[tm_pubyear_var]">
											<option value="1" <?php selected( $options['tm_pubyear_var'], 1 ); ?>>gadwpPublicationYear</option>
											<option value="0" <?php selected( $options['tm_pubyear_var'], 0 ); ?>><?php _e( "Disabled", 'google-analytics-board' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tm_pubyearmonth_var"><?php _e("Publication Month:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_pubyearmonth_var" name="options[tm_pubyearmonth_var]">
											<option value="1" <?php selected( $options['tm_pubyearmonth_var'], 1 ); ?>>gadwpPublicationYearMonth</option>
											<option value="0" <?php selected( $options['tm_pubyearmonth_var'], 0 ); ?>><?php _e( "Disabled", 'google-analytics-board' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tm_category_var"><?php _e("Categories:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_category_var" name="options[tm_category_var]">
											<option value="1" <?php selected( $options['tm_category_var'], 1 ); ?>>gadwpCategory</option>
											<option value="0" <?php selected( $options['tm_category_var'], 0 ); ?>><?php _e( "Disabled", 'google-analytics-board' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tm_user_var"><?php _e("User Type:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_user_var" name="options[tm_user_var]">
											<option value="1" <?php selected( $options['tm_user_var'], 1 ); ?>>gadwpUser</option>
											<option value="0" <?php selected( $options['tm_user_var'], 0 ); ?>><?php _e( "Disabled", 'google-analytics-board' ); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tm_tag_var"><?php _e("Tags:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="tm_tag_var" name="options[tm_tag_var]">
											<option value="1" <?php selected( $options['tm_tag_var'], 1 ); ?>>gadwpTag</option>
											<option value="0" <?php selected( $options['tm_tag_var'], 0 ); ?>><?php _e( "Disabled", 'google-analytics-board' ); ?></option>
										</select>
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-advanced">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Advanced Tracking", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_speed_samplerate"><?php _e("Speed Sample Rate:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="number" id="ga_speed_samplerate" name="options[ga_speed_samplerate]" value="<?php echo (int)($options['ga_speed_samplerate']); ?>" max="100" min="1">
										%
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_user_samplerate"><?php _e("User Sample Rate:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="number" id="ga_user_samplerate" name="options[ga_user_samplerate]" value="<?php echo (int)($options['ga_user_samplerate']); ?>" max="100" min="1">
										%
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_anonymize_ip]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_anonymize_ip" <?php checked( $options['ga_anonymize_ip'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_anonymize_ip">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("anonymize IPs while tracking", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_optout]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_optout" <?php checked( $options['ga_optout'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_optout">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable support for user opt-out", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_dnt_optout]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_dnt_optout" <?php checked( $options['ga_dnt_optout'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_dnt_optout">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"> <?php _e( 'exclude tracking for users sending Do Not Track header', 'google-analytics-board' ); ?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_remarketing]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_remarketing" <?php checked( $options['ga_remarketing'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_remarketing">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable remarketing, demographics and interests reports", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_event_bouncerate]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_event_bouncerate" <?php checked( $options['ga_event_bouncerate'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_event_bouncerate">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("exclude events from bounce-rate and time on page calculation", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_enhanced_links]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_enhanced_links" <?php checked( $options['ga_enhanced_links'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_enhanced_links">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable enhanced link attribution", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_event_precision]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_event_precision" <?php checked( $options['ga_event_precision'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_event_precision">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("use hitCallback to increase event tracking accuracy", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_force_ssl]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_force_ssl" <?php checked( $options['ga_force_ssl'] || $options['ga_with_gtag'], 1 ); ?>  <?php disabled( $options['ga_with_gtag'], true );?>>
											<label class="gadwp-settings-switchoo-label" for="ga_force_ssl">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Force SSL", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Cross-domain Tracking", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[ga_crossdomain_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="ga_crossdomain_tracking" <?php checked( $options['ga_crossdomain_tracking'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="ga_crossdomain_tracking">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable cross domain tracking", 'google-analytics-board' ); ?></div>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_crossdomain_list"><?php _e("Cross Domains:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_crossdomain_list" name="options[ga_crossdomain_list]" value="<?php echo esc_attr($options['ga_crossdomain_list']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Cookie Customization", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_cookiedomain"><?php _e("Cookie Domain:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_cookiedomain" name="options[ga_cookiedomain]" value="<?php echo esc_attr($options['ga_cookiedomain']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_cookiename"><?php _e("Cookie Name:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_cookiename" name="options[ga_cookiename]" value="<?php echo esc_attr($options['ga_cookiename']); ?>" size="50">
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="ga_cookieexpires"><?php _e("Cookie Expires:", 'google-analytics-board'); ?>
										</label>
									</td>
									<td>
										<input type="text" id="ga_cookieexpires" name="options[ga_cookieexpires]" value="<?php echo esc_attr($options['ga_cookieexpires']); ?>" size="10">
										<?php _e("seconds", 'google-analytics-board' ); ?>
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-integration">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Accelerated Mobile Pages (AMP)", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[amp_tracking_analytics]" value="1" class="gadwp-settings-switchoo-checkbox" id="amp_tracking_analytics" <?php checked( $options['amp_tracking_analytics'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="amp_tracking_analytics">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable tracking for Accelerated Mobile Pages (AMP)", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[amp_tracking_clientidapi]" value="1" class="gadwp-settings-switchoo-checkbox" id="amp_tracking_clientidapi" <?php checked( $options['amp_tracking_clientidapi'] && !$options['ga_with_gtag'], 1 ); ?> <?php disabled( $options['ga_with_gtag'], true );?>>
											<label class="gadwp-settings-switchoo-label" for="amp_tracking_clientidapi">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Google AMP Client Id API", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Ecommerce", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tracking_type"><?php _e("Ecommerce Tracking:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<select id="ecommerce_mode" name="options[ecommerce_mode]" <?php disabled( $options['ga_with_gtag'], true );?>>
											<option value="disabled" <?php selected( $options['ecommerce_mode'], 'disabled' ); ?>><?php _e("Disabled", 'google-analytics-board');?></option>
											<option value="standard" <?php selected( $options['ecommerce_mode'], 'standard' ); ?>><?php _e("Ecommerce Plugin", 'google-analytics-board');?></option>
											<option value="enhanced" <?php selected( $options['ecommerce_mode'], 'enhanced' ); selected( $options['ga_with_gtag'], true );?>><?php _e("Enhanced Ecommerce Plugin", 'google-analytics-board');?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Optimize", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[optimize_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="optimize_tracking" <?php checked( $options['optimize_tracking'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="optimize_tracking">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Optimize tracking", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[optimize_pagehiding]" value="1" class="gadwp-settings-switchoo-checkbox" id="optimize_pagehiding" <?php checked( $options['optimize_pagehiding'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="optimize_pagehiding">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable Page Hiding support", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tracking_type"><?php _e("Container ID:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<input type="text" name="options[optimize_containerid]" value="<?php echo esc_attr($options['optimize_containerid']); ?>" size="15">
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-tmadvanced">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Advanced Tracking", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[tm_optout]" value="1" class="gadwp-settings-switchoo-checkbox" id="tm_optout" <?php checked( $options['tm_optout'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="tm_optout">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable support for user opt-out", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[tm_dnt_optout]" value="1" class="gadwp-settings-switchoo-checkbox" id="tm_dnt_optout" <?php checked( $options['tm_dnt_optout'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="tm_dnt_optout">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"> <?php _e( 'exclude tracking for users sending Do Not Track header', 'google-analytics-board' ); ?></div>
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-tmintegration">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Accelerated Mobile Pages (AMP)", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="gadwp-settings-title">
										<div class="button-primary gadwp-settings-switchoo">
											<input type="checkbox" name="options[amp_tracking_tagmanager]" value="1" class="gadwp-settings-switchoo-checkbox" id="amp_tracking_tagmanager" <?php checked( $options['amp_tracking_tagmanager'], 1 ); ?>>
											<label class="gadwp-settings-switchoo-label" for="amp_tracking_tagmanager">
												<div class="gadwp-settings-switchoo-inner"></div>
												<div class="gadwp-settings-switchoo-switch"></div>
											</label>
										</div>
										<div class="switch-desc"><?php echo " ".__("enable tracking for Accelerated Mobile Pages (AMP)", 'google-analytics-board' );?></div>
									</td>
								</tr>
								<tr>
									<td class="gadwp-settings-title">
										<label for="tracking_type"><?php _e("AMP Container ID:", 'google-analytics-board' ); ?>
										</label>
									</td>
									<td>
										<input type="text" name="options[amp_containerid]" value="<?php echo esc_attr($options['amp_containerid']); ?>" size="15">
									</td>
								</tr>
							</table>
						</div>
						<div id="gadwp-exclude">
							<table class="gadwp-settings-options">
								<tr>
									<td colspan="2"><?php echo "<h2>" . __( "Exclude Tracking", 'google-analytics-board' ) . "</h2>"; ?></td>
								</tr>
								<tr>
									<td class="roles gadwp-settings-title">
										<label for="track_exclude"><?php _e("Exclude tracking for:", 'google-analytics-board' ); ?></label>
									</td>
									<td class="gadwp-settings-roles">
										<table>
											<tr>
										<?php if ( ! isset( $wp_roles ) ) : ?>
											<?php $wp_roles = new WP_Roles(); ?>
										<?php endif; ?>
										<?php $i = 0; ?>
										<?php foreach ( $wp_roles->role_names as $role => $name ) : ?>
											<?php if ( 'subscriber' != $role ) : ?>
												<?php $i++; ?>
											<td>
													<label>
														<input type="checkbox" name="options[track_exclude][]" value="<?php echo $role; ?>" <?php if (in_array($role,$options['track_exclude'])) {echo 'checked="checked"';} ?> /> <?php echo $name; ?>
											</label>
												</td>
											<?php endif; ?>
											<?php if ( 0 == $i % 4 ) : ?>
										 	</tr>
											<tr>
											<?php endif; ?>
										<?php endforeach; ?>
										</table>
									</td>
								</tr>
							</table>
						</div>
						<table class="gadwp-settings-options">
							<tr>
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2" class="submit">
									<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'google-analytics-board' ) ?>" />
								</td>
							</tr>
						</table>
						<input type="hidden" name="options[gadwp_hidden]" value="Y">
						<?php wp_nonce_field('gadwp_form','gadwp_security'); ?>






</form>
<?php
		self::output_sidebar();
	}

	public static function errors_debugging() {

		$gadwp = GAB();

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$anonim = GADWP_Tools::anonymize_options( $gadwp->config->options );

		$options = self::update_options( 'frontend' );
		if ( ! $gadwp->config->options['tableid_jail'] || ! $gadwp->config->options['token'] ) {
			$message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'google-analytics-board' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_errors_debugging', false ), __( 'Errors & Debug', 'google-analytics-board' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_settings', false ), __( 'authorize the plugin', 'google-analytics-board' ) ) ) );
		}
		?>
<div class="wrap">
		<?php echo "<h2>" . __( "Google Analytics Errors & Debugging", 'google-analytics-board' ) . "</h2>"; ?>
</div>
<div id="poststuff" class="gadwp">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content">
			<div class="settings-wrapper">
				<div class="inside">
						<?php if (isset($message)) {echo $message;} ?>
						<?php $tabs = array( 'errors' => __( "Errors & Details", 'google-analytics-board' ), 'config' => __( "Plugin Settings", 'google-analytics-board' ), 'sysinfo' => __( "System", 'google-analytics-board' ) ); ?>
						<?php self::navigation_tabs( $tabs ); ?>
						<div id="gadwp-errors">
						<table class="gadwp-settings-logdata">
							<tr>
								<td>
									<?php echo "<h2>" . __( "Error Details", 'google-analytics-board' ) . "</h2>"; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?php $errors_count = GADWP_Tools::get_cache( 'errors_count' ); ?>
									<pre class="gadwp-settings-logdata"><?php echo '<span>' . __("Count: ", 'google-analytics-board') . '</span>' . (int)$errors_count;?></pre>
									<?php $errors = print_r( GADWP_Tools::get_cache( 'last_error' ), true ) ? esc_html( print_r( GADWP_Tools::get_cache( 'last_error' ), true ) ) : ''; ?>
									<?php $errors = str_replace( 'Deconf_', 'Google_', $errors); ?>
									<pre class="gadwp-settings-logdata"><?php echo '<span>' . __("Last Error: ", 'google-analytics-board') . '</span>' . "\n" . $errors;?></pre>
									<pre class="gadwp-settings-logdata"><?php echo '<span>' . __("GAPI Error: ", 'google-analytics-board') . '</span>'; echo "\n" . esc_html( print_r( GADWP_Tools::get_cache( 'gapi_errors' ), true ) ) ?></pre>
									<br />
									<hr>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo "<h2>" . __( "Sampled Data", 'google-analytics-board' ) . "</h2>"; ?>
								</td>
							</tr>
							<tr>
								<td>
									<?php $sampling = GADWP_TOOLS::get_cache( 'sampleddata' ); ?>
									<?php if ( $sampling ) :?>
									<?php printf( __( "Last Detected on %s.", 'google-analytics-board' ), '<strong>'. $sampling['date'] . '</strong>' );?>
									<br />
									<?php printf( __( "The report was based on %s of sessions.", 'google-analytics-board' ), '<strong>'. $sampling['percent'] . '</strong>' );?>
									<br />
									<?php printf( __( "Sessions ratio: %s.", 'google-analytics-board' ), '<strong>'. $sampling['sessions'] . '</strong>' ); ?>
									<?php else :?>
									<?php _e( "None", 'google-analytics-board' ); ?>
									<?php endif;?>
								</td>
							</tr>
						</table>
					</div>
					<div id="gadwp-config">
						<table class="gadwp-settings-options">
							<tr>
								<td><?php echo "<h2>" . __( "Plugin Configuration", 'google-analytics-board' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td>
									<pre class="gadwp-settings-logdata"><?php echo esc_html(print_r($anonim, true));?></pre>
									<br />
									<hr>
								</td>
							</tr>
						</table>
					</div>
					<div id="gadwp-sysinfo">
						<table class="gadwp-settings-options">
							<tr>
								<td><?php echo "<h2>" . __( "System Information", 'google-analytics-board' ) . "</h2>"; ?></td>
							</tr>
							<tr>
								<td>
									<pre class="gadwp-settings-logdata"><?php echo esc_html(GADWP_Tools::system_info());?></pre>
									<br />
									<hr>
								</td>
							</tr>
						</table>
					</div>
	<?php
		self::output_sidebar();
	}

	public static function general_settings() {
		$gadwp = GAB();

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$options = self::update_options( 'general' );
		printf( '<div id="gapi-warning" class="updated"><p>%1$s <a href="https://metricsquery.com/?utm_source=gadwp_config&utm_medium=link&utm_content=general_screen&utm_campaign=gadwp">%2$s</a></p></div>', __( 'Loading the required libraries. If this results in a blank screen or a fatal error, try this solution:', 'google-analytics-board' ), __( 'Library conflicts between WordPress plugins', 'google-analytics-board' ) );
		if ( null === $gadwp->gapi_controller ) {
			$gadwp->gapi_controller = new GADWP_GAPI_Controller();
		}
		echo '<script type="text/javascript">jQuery("#gapi-warning").hide()</script>';
		if ( isset( $_POST['gadwp_access_code'] ) ) {
			if ( 1 == ! stripos( 'x' . $_POST['gadwp_access_code'], 'UA-', 1 ) && $_POST['gadwp_access_code'] != get_option( 'gadwp_redeemed_code' ) ) {
				try {
					$gadwp_access_code = sanitize_text_field( $_POST['gadwp_access_code'] );
					update_option( 'gadwp_redeemed_code', $gadwp_access_code );
					GADWP_Tools::delete_cache( 'gapi_errors' );
					GADWP_Tools::delete_cache( 'last_error' );
					$gadwp->gapi_controller->client->authenticate( $gadwp_access_code );
					$gadwp->config->options['token'] = $gadwp->gapi_controller->client->getAccessToken();
					$gadwp->config->options['automatic_updates_minorversion'] = 1;
					$gadwp->config->set_plugin_options();
					$options = self::update_options( 'general' );
					$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Plugin authorization succeeded.", 'google-analytics-board' ) . "</p></div>";
					if ( $gadwp->config->options['token'] && $gadwp->gapi_controller->client->getAccessToken() ) {
						$profiles = $gadwp->gapi_controller->refresh_profiles();
						if ( is_array ( $profiles ) && ! empty( $profiles ) ) {
							$gadwp->config->options['ga_profiles_list'] = $profiles;
							if ( ! $gadwp->config->options['tableid_jail'] ) {
								$profile = GADWP_Tools::guess_default_domain( $profiles );
								$gadwp->config->options['tableid_jail'] = $profile;
							}
							$gadwp->config->set_plugin_options();
							$options = self::update_options( 'general' );
						}
					}
				} catch ( Deconf_IO_Exception $e ) {
					$timeout = $gadwp->gapi_controller->get_timeouts( 'midnight' );
					GADWP_Tools::set_error( $e, $timeout );
				} catch ( Deconf_Service_Exception $e ) {
					$timeout = $gadwp->gapi_controller->get_timeouts( 'midnight' );
					GADWP_Tools::set_error( $e, $timeout );
				} catch ( Exception $e ) {
					$timeout = $gadwp->gapi_controller->get_timeouts( 'midnight' );
					GADWP_Tools::set_error( $e, $timeout );
					$gadwp->gapi_controller->reset_token();
				}
			} else {
				if ( 1 == stripos( 'x' . $_POST['gadwp_access_code'], 'UA-', 1 ) ) {
					$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "The access code is <strong>not</strong> your <strong>Tracking ID</strong> (UA-XXXXX-X) <strong>nor</strong> your <strong>email address</strong>!", 'google-analytics-board' ) . ".</p></div>";
				} else {
					$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "You can only use the access code <strong>once</strong>, please generate a <strong>new access</strong> code following the instructions!", 'google-analytics-board' ) . ".</p></div>";
				}
			}
		}
		if ( isset( $_POST['Clear'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {
				GADWP_Tools::clear_cache();
				$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Cleared Cache.", 'google-analytics-board' ) . "</p></div>";
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Reset'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {
				$gadwp->gapi_controller->reset_token();
				GADWP_Tools::clear_cache();
				$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Token Reseted and Revoked.", 'google-analytics-board' ) . "</p></div>";
				$options = self::update_options( 'Reset' );
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Reset_Err'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {

				/* @formatter:on */
				GADWP_Tools::delete_cache( 'last_error' );
				GADWP_Tools::delete_cache( 'gapi_errors' );
				delete_option( 'gadwp_got_updated' );
				$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "All errors reseted.", 'google-analytics-board' ) . "</p></div>";
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['options']['gadwp_hidden'] ) && ! isset( $_POST['Clear'] ) && ! isset( $_POST['Reset'] ) && ! isset( $_POST['Reset_Err'] ) ) {
			$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Settings saved.", 'google-analytics-board' ) . "</p></div>";
			if ( ! ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) ) {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Hide'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {
				$message = "<div class='updated' id='gadwp-action'><p>" . __( "All other domains/properties were removed.", 'google-analytics-board' ) . "</p></div>";
				$lock_profile = GADWP_Tools::get_selected_profile( $gadwp->config->options['ga_profiles_list'], $gadwp->config->options['tableid_jail'] );
				$gadwp->config->options['ga_profiles_list'] = array( $lock_profile );
				$options = self::update_options( 'general' );
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		?>
	<div class="wrap">
	<?php echo "<h2>" . __( "Google Analytics Settings", 'google-analytics-board' ) . "</h2>"; ?>
					<hr>
					</div>
					<div id="poststuff" class="gadwp">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content">
								<div class="settings-wrapper">
									<div class="inside">
										<?php if ( $gadwp->gapi_controller->gapi_errors_handler() || GADWP_Tools::get_cache( 'last_error' ) ) : ?>
													<?php $message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'google-analytics-board' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_errors_debugging', false ), __( 'Errors & Debug', 'google-analytics-board' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_settings', false ), __( 'authorize the plugin', 'google-analytics-board' ) ) ) );?>
										<?php endif;?>
										<?php if ( isset( $_POST['Authorize'] ) ) : ?>
											<?php GADWP_Tools::clear_cache(); ?>
											<?php $gadwp->gapi_controller->token_request(); ?>
											<div class="updated">
												<p><?php _e( "Use the red link (see below) to generate and get your access code! You need to generate a new code each time you authorize!", 'google-analytics-board' )?></p>
											</div>
										<?php else : ?>
										<?php if ( isset( $message ) ) :?>
											<?php echo $message;?>
										<?php endif; ?>
										<form name="gadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
											<input type="hidden" name="options[gadwp_hidden]" value="Y">
											<?php wp_nonce_field('gadwp_form','gadwp_security'); ?>
											<table class="gadwp-settings-options">
												<tr>
													<td colspan="2">
														<?php echo "<h2>" . __( "Plugin Authorization", 'google-analytics-board' ) . "</h2>";?>
													</td>
												</tr>
												<tr>
													<td colspan="2" class="gadwp-settings-info">
														<?php printf(__('You need to create a %1$s and watch this %2$s before proceeding to authorization.', 'google-analytics-board'), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://metricsquery.com/creating-a-google-analytics-account/?utm_source=gadwp_config&utm_medium=link&utm_content=top_tutorial&utm_campaign=gadwp', __("free analytics account", 'google-analytics-board')), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://metricsquery.com/?utm_source=gadwp_config&utm_medium=link&utm_content=top_video&utm_campaign=gadwp', __("video tutorial", 'google-analytics-board')));?>
													</td>
												</tr>
												  <?php if (! $options['token'] || ($options['user_api']  && ! $options['network_mode'])) : ?>
												<tr>
													<td colspan="2" class="gadwp-settings-info">
														<input name="options[user_api]" type="checkbox" id="user_api" value="1" <?php checked( $options['user_api'], 1 ); ?> onchange="this.form.submit()" <?php echo ($options['network_mode'])?'disabled="disabled"':''; ?> /><?php echo " ".__("developer mode (requires advanced API knowledge)", 'google-analytics-board' );?>
													</td>
												</tr>
												  <?php endif; ?>
												  <?php if ($options['user_api']  && ! $options['network_mode']) : ?>
												<tr>
													<td class="gadwp-settings-title">
														<label for="options[client_id]"><?php _e("Client ID:", 'google-analytics-board'); ?></label>
													</td>
													<td>
														<input type="text" name="options[client_id]" value="<?php echo esc_attr($options['client_id']); ?>" size="40" required="required">
													</td>
												</tr>
												<tr>
													<td class="gadwp-settings-title">
														<label for="options[client_secret]"><?php _e("Client Secret:", 'google-analytics-board'); ?></label>
													</td>
													<td>
														<input type="text" name="options[client_secret]" value="<?php echo esc_attr($options['client_secret']); ?>" size="40" required="required">
														<input type="hidden" name="options[gadwp_hidden]" value="Y">
														<?php wp_nonce_field('gadwp_form','gadwp_security'); ?>
													</td>
												</tr>
												  <?php endif; ?>
												  <?php if ( $options['token'] ) : ?>
												<tr>
													<td colspan="2">
														<input type="submit" name="Reset" class="button button-secondary" value="<?php _e( "Clear Authorization", 'google-analytics-board' ); ?>" <?php echo $options['network_mode']?'disabled="disabled"':''; ?> />
														<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'google-analytics-board' ); ?>" />
														<input type="submit" name="Reset_Err" class="button button-secondary" value="<?php _e( "Report & Reset Errors", 'google-analytics-board' ); ?>" />
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<tr>
													<td colspan="2"><?php echo "<h2>" . __( "General Settings", 'google-analytics-board' ) . "</h2>"; ?></td>
												</tr>
												<tr>
													<td class="gadwp-settings-title">
														<label for="tableid_jail"><?php _e("Select View:", 'google-analytics-board' ); ?></label>
													</td>
													<td>
														<select id="tableid_jail" <?php disabled(empty($options['ga_profiles_list']) || 1 == count($options['ga_profiles_list']), true); ?> name="options[tableid_jail]">
															<?php if ( ! empty( $options['ga_profiles_list'] ) ) : ?>
																	<?php foreach ( $options['ga_profiles_list'] as $items ) : ?>
																		<?php if ( $items[3] ) : ?>
																			<option value="<?php echo esc_attr( $items[1] ); ?>" <?php selected( $items[1], $options['tableid_jail'] ); ?> title="<?php _e( "View Name:", 'google-analytics-board' ); ?> <?php echo esc_attr( $items[0] ); ?>">
																				<?php echo esc_html( GADWP_Tools::strip_protocol( $items[3] ) )?> &#8658; <?php echo esc_attr( $items[0] ); ?>
																			</option>
																		<?php endif; ?>
																	<?php endforeach; ?>
															<?php else : ?>
																	<option value=""><?php _e( "Property not found", 'google-analytics-board' ); ?></option>
															<?php endif; ?>
														</select>
														<?php if ( count( $options['ga_profiles_list'] ) > 1 ) : ?>
														&nbsp;<input type="submit" name="Hide" class="button button-secondary" value="<?php _e( "Lock Selection", 'google-analytics-board' ); ?>" />
														<?php endif; ?>
													 </td>
												</tr>
												<?php if ( $options['tableid_jail'] ) :	?>
												<tr>
													<td class="gadwp-settings-title"></td>
													<td>
													<?php $profile_info = GADWP_Tools::get_selected_profile( $gadwp->config->options['ga_profiles_list'], $gadwp->config->options['tableid_jail'] ); ?>
														<pre><?php echo __( "View Name:", 'google-analytics-board' ) . "\t" . esc_html( $profile_info[0] ) . "<br />" . __( "Tracking ID:", 'google-analytics-board' ) . "\t" . esc_html( $profile_info[2] ) . "<br />" . __( "Default URL:", 'google-analytics-board' ) . "\t" . esc_html( $profile_info[3] ) . "<br />" . __( "Time Zone:", 'google-analytics-board' ) . "\t" . esc_html( $profile_info[5] );?></pre>
													</td>
												</tr>
												<?php endif; ?>
												 <tr>
													<td class="gadwp-settings-title">
														<label for="theme_color"><?php _e("Theme Color:", 'google-analytics-board' ); ?></label>
													</td>
													<td>
														<input type="text" id="theme_color" class="theme_color" name="options[theme_color]" value="<?php echo esc_attr($options['theme_color']); ?>" size="10">
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<?php if ( !is_multisite()) :?>
												<tr>
													<td colspan="2"><?php echo "<h2>" . __( "Automatic Updates", 'google-analytics-board' ) . "</h2>"; ?></td>
												</tr>
												<tr>
													<td colspan="2" class="gadwp-settings-title">
														<div class="button-primary gadwp-settings-switchoo">
															<input type="checkbox" name="options[automatic_updates_minorversion]" value="1" class="gadwp-settings-switchoo-checkbox" id="automatic_updates_minorversion" <?php checked( $options['automatic_updates_minorversion'], 1 ); ?>>
															<label class="gadwp-settings-switchoo-label" for="automatic_updates_minorversion">
																<div class="gadwp-settings-switchoo-inner"></div>
																<div class="gadwp-settings-switchoo-switch"></div>
															</label>
														</div>
														<div class="switch-desc"><?php echo " ".__( "automatic updates for minor versions (security and maintenance releases only)", 'google-analytics-board' );?></div>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<?php endif; ?>
												<tr>
													<td colspan="2" class="submit">
														<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'google-analytics-board' ) ?>" />
													</td>
												</tr>
												<?php else : ?>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<input type="submit" name="Authorize" class="button button-secondary" id="authorize" value="<?php _e( "Authorize Plugin", 'google-analytics-board' ); ?>" <?php echo $options['network_mode']?'disabled="disabled"':''; ?> />
														<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'google-analytics-board' ); ?>" />
													</td>
												</tr>
												<tr>
													<td colspan="2">
														<hr>
													</td>
												</tr>
											</table>

										</form>
				<?php self::output_sidebar(); ?>
				<?php return; ?>
			<?php endif; ?>
											</table>

										</form>
										</div>
										</div>
			<?php endif; ?>
			<?php

		self::output_sidebar();
	}

	// Network Settings
	public static function general_settings_network() {
		$gadwp = GAB();

		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}
		$options = self::update_options( 'network' );
		/*
		 * Include GAPI
		 */
		echo '<div id="gapi-warning" class="updated"><p>' . __( 'Loading the required libraries. If this results in a blank screen or a fatal error, try this solution:', 'google-analytics-board' ) . ' <a href="https://metricsquery.com/?utm_source=gadwp_config&utm_medium=link&utm_content=general_screen&utm_campaign=gadwp">Library conflicts between WordPress plugins</a></p></div>';

		if ( null === $gadwp->gapi_controller ) {
			$gadwp->gapi_controller = new GADWP_GAPI_Controller();
		}

		echo '<script type="text/javascript">jQuery("#gapi-warning").hide()</script>';
		if ( isset( $_POST['gadwp_access_code'] ) ) {
			if ( 1 == ! stripos( 'x' . $_POST['gadwp_access_code'], 'UA-', 1 ) && $_POST['gadwp_access_code'] != get_option( 'gadwp_redeemed_code' ) ) {
				try {
					$gadwp_access_code = sanitize_text_field( $_POST['gadwp_access_code'] );
					update_option( 'gadwp_redeemed_code', $gadwp_access_code );
					$gadwp->gapi_controller->client->authenticate( $gadwp_access_code );
					$gadwp->config->options['token'] = $gadwp->gapi_controller->client->getAccessToken();
					$gadwp->config->options['automatic_updates_minorversion'] = 1;
					$gadwp->config->set_plugin_options( true );
					$options = self::update_options( 'network' );
					$message = "<div class='updated' id='gadwp-action'><p>" . __( "Plugin authorization succeeded.", 'google-analytics-board' ) . "</p></div>";
					if ( is_multisite() ) { // Cleanup errors on the entire network
						foreach ( GADWP_Tools::get_sites( array( 'number' => apply_filters( 'gadwp_sites_limit', 100 ) ) ) as $blog ) {
							switch_to_blog( $blog['blog_id'] );
							GADWP_Tools::delete_cache( 'last_error' );
							GADWP_Tools::delete_cache( 'gapi_errors' );
							restore_current_blog();
						}
					} else {
						GADWP_Tools::delete_cache( 'last_error' );
						GADWP_Tools::delete_cache( 'gapi_errors' );
					}
					if ( $gadwp->config->options['token'] && $gadwp->gapi_controller->client->getAccessToken() ) {
						$profiles = $gadwp->gapi_controller->refresh_profiles();
						if ( is_array ( $profiles ) && ! empty( $profiles ) ) {
							$gadwp->config->options['ga_profiles_list'] = $profiles;
							if ( isset( $gadwp->config->options['tableid_jail'] ) && ! $gadwp->config->options['tableid_jail'] ) {
								$profile = GADWP_Tools::guess_default_domain( $profiles );
								$gadwp->config->options['tableid_jail'] = $profile;
							}
							$gadwp->config->set_plugin_options( true );
							$options = self::update_options( 'network' );
						}
					}
				} catch ( Deconf_IO_Exception $e ) {
					$timeout = $gadwp->gapi_controller->get_timeouts( 'midnight' );
					GADWP_Tools::set_error( $e, $timeout );
				} catch ( Deconf_Service_Exception $e ) {
					$timeout = $gadwp->gapi_controller->get_timeouts( 'midnight' );
					GADWP_Tools::set_error( $e, $timeout );
				} catch ( Exception $e ) {
					$timeout = $gadwp->gapi_controller->get_timeouts( 'midnight' );
					GADWP_Tools::set_error( $e, $timeout );
					$gadwp->gapi_controller->reset_token();
				}
			} else {
				if ( 1 == stripos( 'x' . $_POST['gadwp_access_code'], 'UA-', 1 ) ) {
					$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "The access code is <strong>not</strong> your <strong>Tracking ID</strong> (UA-XXXXX-X) <strong>nor</strong> your <strong>email address</strong>!", 'google-analytics-board' ) . ".</p></div>";
				} else {
					$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "You can only use the access code <strong>once</strong>, please generate a <strong>new access code</strong> using the red link", 'google-analytics-board' ) . "!</p></div>";
				}
			}
		}
		if ( isset( $_POST['Refresh'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {
				$gadwp->config->options['ga_profiles_list'] = array();
				$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Properties refreshed.", 'google-analytics-board' ) . "</p></div>";
				$options = self::update_options( 'network' );
				if ( $gadwp->config->options['token'] && $gadwp->gapi_controller->client->getAccessToken() ) {
					if ( ! empty( $gadwp->config->options['ga_profiles_list'] ) ) {
						$profiles = $gadwp->config->options['ga_profiles_list'];
					} else {
						$profiles = $gadwp->gapi_controller->refresh_profiles();
					}
					if ( $profiles ) {
						$gadwp->config->options['ga_profiles_list'] = $profiles;
						if ( isset( $gadwp->config->options['tableid_jail'] ) && ! $gadwp->config->options['tableid_jail'] ) {
							$profile = GADWP_Tools::guess_default_domain( $profiles );
							$gadwp->config->options['tableid_jail'] = $profile;
						}
						$gadwp->config->set_plugin_options( true );
						$options = self::update_options( 'network' );
					}
				}
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Clear'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {
				GADWP_Tools::clear_cache();
				$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Cleared Cache.", 'google-analytics-board' ) . "</p></div>";
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Reset'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {
				$gadwp->gapi_controller->reset_token();
				GADWP_Tools::clear_cache();
				$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Token Reseted and Revoked.", 'google-analytics-board' ) . "</p></div>";
				$options = self::update_options( 'Reset' );
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['options']['gadwp_hidden'] ) && ! isset( $_POST['Clear'] ) && ! isset( $_POST['Reset'] ) && ! isset( $_POST['Refresh'] ) ) {
			$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "Settings saved.", 'google-analytics-board' ) . "</p></div>";
			if ( ! ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) ) {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		if ( isset( $_POST['Hide'] ) ) {
			if ( isset( $_POST['gadwp_security'] ) && wp_verify_nonce( $_POST['gadwp_security'], 'gadwp_form' ) ) {
				$message = "<div class='updated' id='gadwp-autodismiss'><p>" . __( "All other domains/properties were removed.", 'google-analytics-board' ) . "</p></div>";
				$lock_profile = GADWP_Tools::get_selected_profile( $gadwp->config->options['ga_profiles_list'], $gadwp->config->options['tableid_jail'] );
				$gadwp->config->options['ga_profiles_list'] = array( $lock_profile );
				$options = self::update_options( 'network' );
			} else {
				$message = "<div class='error' id='gadwp-autodismiss'><p>" . __( "Cheating Huh?", 'google-analytics-board' ) . "</p></div>";
			}
		}
		?>
<div class="wrap">
											<h2><?php _e( "Google Analytics Settings", 'google-analytics-board' );?></h2>
											<hr>
										</div>
										<div id="poststuff" class="gadwp">
											<div id="post-body" class="metabox-holder columns-2">
												<div id="post-body-content">
													<div class="settings-wrapper">
														<div class="inside">
					<?php if ( $gadwp->gapi_controller->gapi_errors_handler() || GADWP_Tools::get_cache( 'last_error' ) ) : ?>
						<?php $message = sprintf( '<div class="error"><p>%s</p></div>', sprintf( __( 'Something went wrong, check %1$s or %2$s.', 'google-analytics-board' ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_errors_debugging', false ), __( 'Errors & Debug', 'google-analytics-board' ) ), sprintf( '<a href="%1$s">%2$s</a>', menu_page_url( 'gadwp_settings', false ), __( 'authorize the plugin', 'google-analytics-board' ) ) ) );?>
					<?php endif; ?>
					<?php if ( isset( $_POST['Authorize'] ) ) : ?>
						<?php GADWP_Tools::clear_cache();?>
						<?php $gadwp->gapi_controller->token_request();?>
					<div class="updated">
																<p><?php _e( "Use the red link (see below) to generate and get your access code! You need to generate a new code each time you authorize!", 'google-analytics-board' );?></p>
															</div>
					<?php else : ?>
						<?php if ( isset( $message ) ) : ?>
							<?php echo $message; ?>
						<?php endif; ?>
					<form name="gadwp_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
																<input type="hidden" name="options[gadwp_hidden]" value="Y">
						<?php wp_nonce_field('gadwp_form','gadwp_security'); ?>
						<table class="gadwp-settings-options">
																	<tr>
																		<td colspan="2">
								<?php echo "<h2>" . __( "Network Setup", 'google-analytics-board' ) . "</h2>"; ?>
								</td>
																	</tr>
																	<tr>
																		<td colspan="2" class="gadwp-settings-title">
																			<div class="button-primary gadwp-settings-switchoo">
																				<input type="checkbox" name="options[network_mode]" value="1" class="gadwp-settings-switchoo-checkbox" id="network_mode" <?php checked( $options['network_mode'], 1); ?> onchange="this.form.submit()">
																				<label class="gadwp-settings-switchoo-label" for="network_mode">
																					<div class="gadwp-settings-switchoo-inner"></div>
																					<div class="gadwp-settings-switchoo-switch"></div>
																				</label>
																			</div>
																			<div class="switch-desc"><?php echo " ".__("use a single Google Analytics account for the entire network", 'google-analytics-board' );?></div>
																		</td>
																	</tr>
							<?php if ($options['network_mode']) : ?>
							<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2"><?php echo "<h2>" . __( "Plugin Authorization", 'google-analytics-board' ) . "</h2>"; ?></td>
																	</tr>
																	<tr>
																		<td colspan="2" class="gadwp-settings-info">
								<?php printf(__('You need to create a %1$s and watch this %2$s before proceeding to authorization.', 'google-analytics-board'), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://metricsquery.com/creating-a-google-analytics-account/?utm_source=gadwp_config&utm_medium=link&utm_content=top_tutorial&utm_campaign=gadwp', __("free analytics account", 'google-analytics-board')), sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://metricsquery.com/?utm_source=gadwp_config&utm_medium=link&utm_content=top_video&utm_campaign=gadwp', __("video tutorial", 'google-analytics-board')));?>
								</td>
																	</tr>
								<?php if ( ! $options['token'] || $options['user_api'] ) : ?>
								<tr>
																		<td colspan="2" class="gadwp-settings-info">
																			<input name="options[user_api]" type="checkbox" id="user_api" value="1" <?php checked( $options['user_api'], 1 ); ?> onchange="this.form.submit()" /><?php echo " ".__("developer mode (requires advanced API knowledge)", 'google-analytics-board' );?>
								</td>
																	</tr>
								<?php endif; ?>
							<?php if ( $options['user_api'] ) : ?>
							<tr>
																		<td class="gadwp-settings-title">
																			<label for="options[client_id]"><?php _e("Client ID:", 'google-analytics-board'); ?>
									</label>
																		</td>
																		<td>
																			<input type="text" name="options[client_id]" value="<?php echo esc_attr($options['client_id']); ?>" size="40" required="required">
																		</td>
																	</tr>
																	<tr>
																		<td class="gadwp-settings-title">
																			<label for="options[client_secret]"><?php _e("Client Secret:", 'google-analytics-board'); ?>
									</label>
																		</td>
																		<td>
																			<input type="text" name="options[client_secret]" value="<?php echo esc_attr($options['client_secret']); ?>" size="40" required="required">
																			<input type="hidden" name="options[gadwp_hidden]" value="Y">
																			<?php wp_nonce_field('gadwp_form','gadwp_security'); ?>
								</td>
																	</tr>
							<?php endif; ?>
							<?php if ( $options['token'] ) : ?>
							<tr>
																		<td colspan="2">
																			<input type="submit" name="Reset" class="button button-secondary" value="<?php _e( "Clear Authorization", 'google-analytics-board' ); ?>" />
																			<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'google-analytics-board' ); ?>" />
																			<input type="submit" name="Refresh" class="button button-secondary" value="<?php _e( "Refresh Properties", 'google-analytics-board' ); ?>" />
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
								<?php echo "<h2>" . __( "Properties/Views Settings", 'google-analytics-board' ) . "</h2>"; ?>
								</td>
																	</tr>
							<?php if ( isset( $options['network_tableid'] ) ) : ?>
								<?php $options['network_tableid'] = json_decode( json_encode( $options['network_tableid'] ), false ); ?>
							<?php endif; ?>
							<?php foreach ( GADWP_Tools::get_sites( array( 'number' => apply_filters( 'gadwp_sites_limit', 100 ) ) ) as $blog ) : ?>
							<tr>
																		<td class="gadwp-settings-title-s">
																			<label for="network_tableid"><?php echo '<strong>'.$blog['domain'].$blog['path'].'</strong>: ';?></label>
																		</td>
																		<td>
																			<select id="network_tableid" <?php disabled(!empty($options['ga_profiles_list']),false);?> name="options[network_tableid][<?php echo $blog['blog_id'];?>]">
									<?php if ( ! empty( $options['ga_profiles_list'] ) ) : ?>
										<?php foreach ( $options['ga_profiles_list'] as $items ) : ?>
											<?php if ( $items[3] ) : ?>
												<?php $temp_id = $blog['blog_id']; ?>
												<option value="<?php echo esc_attr( $items[1] );?>" <?php selected( $items[1], isset( $options['network_tableid']->$temp_id ) ? $options['network_tableid']->$temp_id : '');?> title="<?php echo __( "View Name:", 'google-analytics-board' ) . ' ' . esc_attr( $items[0] );?>">
													 <?php echo esc_html( GADWP_Tools::strip_protocol( $items[3] ) );?> &#8658; <?php echo esc_attr( $items[0] );?>
												</option>
											<?php endif; ?>
										<?php endforeach; ?>
									<?php else : ?>
												<option value="">
													<?php _e( "Property not found", 'google-analytics-board' );?>
												</option>
									<?php endif; ?>
									</select>
																			<br />
																		</td>
																	</tr>
							<?php endforeach; ?>
							<tr>
																		<td colspan="2">
																			<h2><?php echo _e( "Automatic Updates", 'google-analytics-board' );?></h2>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" class="gadwp-settings-title">
																			<div class="button-primary gadwp-settings-switchoo">
																				<input type="checkbox" name="options[automatic_updates_minorversion]" value="1" class="gadwp-settings-switchoo-checkbox" id="automatic_updates_minorversion" <?php checked( $options['automatic_updates_minorversion'], 1 ); ?>>
																				<label class="gadwp-settings-switchoo-label" for="automatic_updates_minorversion">
																					<div class="gadwp-settings-switchoo-inner"></div>
																					<div class="gadwp-settings-switchoo-switch"></div>
																				</label>
																			</div>
																			<div class="switch-desc"><?php echo " ".__( "automatic updates for minor versions (security and maintenance releases only)", 'google-analytics-board' );?></div>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<hr><?php echo "<h2>" . __( "Exclude Tracking", 'google-analytics-board' ) . "</h2>"; ?></td>
																	</tr>
																	<tr>
																		<td colspan="2" class="gadwp-settings-title">
																			<div class="button-primary gadwp-settings-switchoo">
																				<input type="checkbox" name="options[superadmin_tracking]" value="1" class="gadwp-settings-switchoo-checkbox" id="superadmin_tracking"<?php checked( $options['superadmin_tracking'], 1); ?>">
																				<label class="gadwp-settings-switchoo-label" for="superadmin_tracking">
																					<div class="gadwp-settings-switchoo-inner"></div>
																					<div class="gadwp-settings-switchoo-switch"></div>
																				</label>
																			</div>
																			<div class="switch-desc"><?php echo " ".__("exclude Super Admin tracking for the entire network", 'google-analytics-board' );?></div>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" class="submit">
																			<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Changes', 'google-analytics-board' ) ?>" />
																		</td>
																	</tr>
							<?php else : ?>
							<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2">
																			<input type="submit" name="Authorize" class="button button-secondary" id="authorize" value="<?php _e( "Authorize Plugin", 'google-analytics-board' ); ?>" />
																			<input type="submit" name="Clear" class="button button-secondary" value="<?php _e( "Clear Cache", 'google-analytics-board' ); ?>" />
																		</td>
																	</tr>
							<?php endif; ?>
							<tr>
																		<td colspan="2">
																			<hr>
																		</td>
																	</tr>
																</table>
															</form>
		<?php self::output_sidebar(); ?>
				<?php return; ?>
			<?php endif;?>
						</table>
															</form>
		<?php endif; ?>
		<?php

		self::output_sidebar();
	}

	public static function output_sidebar() {
		global $wp_version;

		$gadwp = GAB();
		?>
				</div>
													</div>
												</div>
												<div id="postbox-container-1" class="postbox-container">
													<div class="meta-box-sortables">

														<div class="postbox">
															<h3>
																<span><?php _e("Stay Updated",'google-analytics-board')?></span>
															</h3>
															<div class="inside">
																<div class="gadwp-desc">
																	<a href="https://twitter.com/Yehuda_Ha" class="twitter-follow-button" data-show-screen-name="false"></a>
																	<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
																</div>
															</div>
														</div>
													
													</div>
												</div>
											</div>
										</div>
<?php
		// Dismiss the admin update notice
		if ( version_compare( $wp_version, '4.2', '<' ) && current_user_can( 'manage_options' ) ) {
			delete_option( 'gadwp_got_updated' );
		}
	}
}
