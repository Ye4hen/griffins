/**
 * Author: Yehuda Hassine
 * Author URI: https://metricsquery.com
 * Copyright 2013 by Alin Marcu and forked by Yehuda Hassine
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
 
 * Custom GADWP EndPoint: 

 		- added an action hook to IO -> Abstract -> MakeRequest to enable custom endpoint support:
 
			   public function makeRequest(Deconf_Http_Request $request)
			   {
			
				  	// Add support for GADWP Endpoint
				  	do_action('gadwp_endpoint_support', $request);
				  	
				  	...
				 
				 }
				 
 * Updated the IO -> cacerts.pem file to support Let's Encrypt certificates
 
 * Changed 'Google' provider to 'Deconf', to avoid conflicts on different versions of GAPI PHP Client 	