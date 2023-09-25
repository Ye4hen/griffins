<?php
/*
 * Plugin Name:     Click to Contact - Float Buttons
 * Version:         1.0.3
 * Text Domain:     ctcflatbuttons
 * Plugin URI:      https://yrokiwp.ru
 * Description:     A simple yet powerful plugin to add floating contact buttons to your site. 13 different social links can be shown as well as phone and email. Nice animation and additional settings will definitely match the design of your site.
 * Author:          dmitrylitvinov
 * Author URI:     https://a.yrokiwp.ru
 *
 *
 * License:           GNU General Public License v3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

define('CTC_VERSION', '1.0.3');
define('CTC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CTC_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

/*------------добавляем стили на самом сайте------------*/
add_action('wp_enqueue_scripts', 'ctcflatbuttons_styles');

function ctcflatbuttons_styles()
{
    wp_register_style('ctcflatbuttons-style', plugin_dir_url(__FILE__) . 'inc/aaist-style.css','',CTC_VERSION);
    wp_register_script('ctcflatbuttons-script', plugin_dir_url(__FILE__) . 'inc/aaist-script.js',array('jquery'), null, true);

    wp_enqueue_style('ctcflatbuttons-style');
    wp_enqueue_script( 'ctcflatbuttons-script' );
}
/*------------добавляем стили на самом сайте------------*/


/*------------Страница админки*------------*/
if (is_admin() || (defined('WP_CLI') && WP_CLI)) {
    require_once(CTC_PLUGIN_DIR . 'inc/admin.php');
    add_action('init', array('CtcFlatButtons', 'init'));
}
/*------------Страница админки------------*/


add_action( 'wp_footer', 'ctcflatbuttons_code', 99 );
function ctcflatbuttons_code(){
    ?>

<style>
    <?php
     $val = get_option( 'ctc_fb_option' );
        if ( !isset($val['color_buttons'])) {  $val['color_buttons'] = '';   }

    ?>
    .speed-dial {  <?php
    if ( isset($val['only_left'])) {
             if($val['only_left']==1){
                echo ' left: 20px;';
             }else{
                  echo 'right: 75px;';
             }
         }else{
        echo 'right: 75px;';
         }
    ?>

    }
    .dials li a,.ctc-bg-share{
        background-color: <?php echo htmlspecialchars($val['color_buttons']); ?> ;
    }
</style>
    <div class="speed-dial">

        <a class="toggle ctc-bg-share"></a>

        <ul class="dials clozed">

            <?php if ( isset($val['whatsapp']) AND $val['whatsapp']!='') { ?>
                <li><a class="ctc-bg-watsapp" target="_blank" href="whatsapp://send?phone=+<?php echo esc_html($val['whatsapp']); ?>">whatsapp</a></li>
            <?php } ?>
            <?php if ( isset($val['pinterest']) AND $val['pinterest']!='') { ?>
                <li><a class="ctc-bg-pinterest" target="_blank" href="<?php echo esc_html($val['pinterest']); ?>">Pinterest</a></li>
            <?php } ?>

            <?php if ( isset($val['insta']) AND $val['insta']!='') { ?>
                <li><a class="ctc-bg-insta" target="_blank" href="<?php echo esc_html($val['insta']); ?>">Instagram</a></li>
            <?php } ?>

            <?php if ( isset($val['linkedIn']) AND $val['linkedIn']!='') { ?>
                <li><a class="ctc-bg-LinkedIn" target="_blank" href="<?php echo esc_html($val['linkedIn']); ?>">linkedIn</a></li>
            <?php } ?>

            <?php if ( isset($val['twitter']) AND $val['twitter']!='') { ?>
                <li><a class="ctc-bg-twitter" target="_blank" href="<?php echo esc_html($val['twitter']); ?>">twitter</a></li>
            <?php } ?>

            <?php if ( isset($val['phone']) AND $val['phone']!='') { ?>
                <li><a class="ctc-bg-phone" target="_blank" href="tel:+<?php echo esc_html($val['phone']); ?>">phone</a></li>
            <?php } ?>

            <?php if ( isset($val['viber']) AND $val['viber']!='') { ?>
                <li><a class="ctc-bg-viber" target="_blank" href="viber://chat?number=%2B<?php echo esc_html($val['viber']); ?>">viber</a></li>
            <?php } ?>

            <?php if ( isset($val['telegram']) AND $val['telegram']!='') { ?>
                <li><a class="ctc-bg-telegram" target="_blank" href="https://t.me/<?php echo esc_html($val['telegram']); ?>">Telegram</a></li>
            <?php } ?>

            <?php if ( isset($val['facebook']) AND $val['facebook']!='') { ?>
                <li><a class="ctc-bg-facebook" target="_blank" href="<?php echo esc_html($val['facebook']); ?>">Facebook</a></li>
            <?php } ?>

            <?php if ( isset($val['vk']) AND $val['vk']!='') { ?>
                <li><a class="ctc-bg-vk" target="_blank" href="<?php echo esc_html($val['vk']); ?>">Vkontakte</a></li>
            <?php } ?>

            <?php if ( isset($val['behance']) AND $val['behance']!='') { ?>
                <li><a class="ctc-bg-behance" target="_blank" href="<?php echo esc_html($val['behance']); ?>">Behance</a></li>
            <?php } ?>

            <?php if ( isset($val['dribbble']) AND $val['dribbble']!='') { ?>
                <li><a class="ctc-bg-dribbble" target="_blank" href="<?php echo esc_html($val['dribbble']); ?>">dribbble</a></li>
            <?php } ?>

            <?php if ( isset($val['skype']) AND $val['skype']!='') { ?>
                <li><a class="ctc-bg-skype" target="_blank" href="skype:<?php echo esc_html($val['skype']); ?>?chat">skype</a></li>
            <?php } ?>

            <?php if ( isset($val['tumblr']) AND $val['tumblr']!='') { ?>
                <li><a class="ctc-bg-tumblr" target="_blank" href="<?php echo esc_html($val['tumblr']); ?>">Tumblr</a></li>
            <?php } ?>

            <?php if ( isset($val['reddit']) AND $val['reddit']!='') { ?>
                <li><a class="ctc-bg-reddit" target="_blank" href="<?php echo esc_html($val['reddit']); ?>">Reddit</a></li>
            <?php } ?>

           <?php if ( isset($val['email']) AND $val['email']!='') { ?>
            <li><a class="ctc-bg-emial" target="_blank" href="mailto:<?php echo esc_html($val['email']); ?>">E-mail</a></li>
        <?php } ?>
        </ul>
    </div>
    <div class="ctc-zad"></div>
    <?php
}