<?php
/**
 * Author: Yehuda Hassine
 * Copyright 2017 Alin Marcu
 * Author URI: https://metricsquery.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<div class="notice notice-large notice-warning" style="text-align: center;">

	<p style="color: firebrick; font-size: 16px;">
		Please read:
	</p>


	<p>
		When you get the Google auth screen for your access code, you will see that the App name is: <strong>GADWP Reloaded</strong>.
	</p>

	<p>
		<strong><u>It's OK</u></strong>
	</p>

	<p>

		This is the first name given to the plugin and to avoid the Google approve process again, for now the name stay on Google side.
	</p>
</div>
<form name="input" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
	<table class="gadwp-settings-options">
		<tr>
			<td colspan="2" class="gadwp-settings-info">
						<?php echo __( "Use this link to get your <strong>one-time-use</strong> access code:", 'google-analytics-board' ) . ' <a href="' . $data['authUrl'] . '" id="gapi-access-code" target="_blank">' . __ ( "Get Access Code", 'google-analytics-board' ) . '</a>.'; ?>
			</td>
		</tr>
		<tr>
			<td class="gadwp-settings-title">
				<label for="gadwp_access_code" title="<?php _e("Use the red link to get your access code! You need to generate a new one each time you authorize!",'google-analytics-board')?>"><?php echo _e( "Access Code:", 'google-analytics-board' ); ?></label>
			</td>
			<td>
				<input type="text" id="gadwp_access_code" name="gadwp_access_code" value="" size="61" autocomplete="off" pattern=".\/.{30,}" required="required" title="<?php _e("Use the red link to get your access code! You need to generate a new one each time you authorize!",'google-analytics-board')?>">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<hr>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" class="button button-secondary" name="gadwp_authorize" value="<?php _e( "Save Access Code", 'google-analytics-board' ); ?>" />
			</td>
		</tr>
	</table>
</form>
