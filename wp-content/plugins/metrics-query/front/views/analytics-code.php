<?php
/**
 * Author: Yehuda Hassine
 * Copyright 2017 Yehuda Hassine
 * Author URI: https://metricsquery.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php if ( 0 == $data['ga_with_gtag'] ):?>
<!-- BEGIN GADWP v<?php echo GADWP_CURRENT_VERSION; ?> Universal Analytics - https://metricsquery.com/ -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','<?php echo $data['tracking_script_path']?>','ga');
<?php echo $data['trackingcode']?>
</script>
<!-- END GADWP Universal Analytics -->
<?php else:?>
<!-- BEGIN GADWP v<?php echo GADWP_CURRENT_VERSION; ?> Global Site Tag - https://metricsquery.com/ -->
<script async src="<?php echo $data['tracking_script_path']?>?id=<?php echo $data['uaid']?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
<?php echo $data['trackingcode']?>

  if (window.performance) {
    var timeSincePageLoad = Math.round(performance.now());
    gtag('event', 'timing_complete', {
      'name': 'load',
      'value': timeSincePageLoad,
      'event_category': 'JS Dependencies'
    });
  }
</script>
<!-- END GADWP Global Site Tag -->
<?php endif;?>