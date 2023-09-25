<?php
/**
 * Class CtcFlatButtons
 *
 * @version 1.0.0
 */
class CtcFlatButtons
{

    public static function init() {
        /* инициализируем меню в админке*/
        add_action( 'admin_menu', array( 'CtcFlatButtons', 'add_admin_menu' ));
        /*Загружаем скрипты и стили*/
        //   add_action( 'admin_enqueue_scripts', array( 'CtcFlatButtons', 'load_scripts' ));
        /*Вывод настроек в меню*/
        add_action( 'admin_init', array( 'CtcFlatButtons', 'plugin_settings' ));

    }

    public static function plugin_settings() {
        register_setting( 'option_group_ctc', 'ctc_fb_option', 'sanitize_callback' );
        $trans1  = __( 'Plugin settings', 'ctcflatbuttons' );
        $trans2  = __( 'Title', 'ctcflatbuttons' );
        $trans_color = __('Color buttons','ctcflatbuttons');
        $trans_only_left = __('Buttons on the left (default on the right)','ctcflatbuttons');

        // параметры: $id, $title, $callback, $page
        add_settings_section( 'section_id', $trans1, '', 'section_ctc_1' );
        // параметры: $id, $title, $callback, $page, $section, $args
         add_settings_field( 'primer_field2', $trans_color, array( 'CtcFlatButtons', 'color_button' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field3', $trans_only_left, array( 'CtcFlatButtons', 'only_left' ), 'section_ctc_1', 'section_id' );

        add_settings_field( 'primer_field0', __('Adding links','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc0' ), 'section_ctc_1', 'section_id' );

        add_settings_field( 'primer_field4', __('Link to emial','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc1' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field5', __('Link to Reddit','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc2' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field6', __('Link to Tumblr','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc3' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field7', __('Login to Skype','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc4' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field8', __('Link to Dribbble','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc5' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field9', __('Link to Behance','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc6' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field10', __('Link to Vkontakte','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc7' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field11', __('Link to Facebook','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc8' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field12', __('Login to Telegram','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc9' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field13', __('Phone number for viber','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc10' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field14', __('Phone number for phone','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc11' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field15', __('Link to Twitter','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc12' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field16', __('Link to LinkedIn','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc13' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field17', __('Link to Instagram','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc14' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field18', __('Link to Pinterest','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc15' ), 'section_ctc_1', 'section_id' );
        add_settings_field( 'primer_field19', __('Phone number for Whatsapp','ctcflatbuttons'), array( 'CtcFlatButtons', 'ctc16' ), 'section_ctc_1', 'section_id' );


    }


    /* инициализируем меню в админке*/
    public static function add_admin_menu() {

        $hello1 = __( 'Click to Contact settings', 'ctcflatbuttons' );
        add_options_page( ' ', $hello1, 'manage_options', 'ctcflatbuttons-plugin-options', array( 'CtcFlatButtons', 'ctc_plugin_options' ) );
    }

    public static function ctc_plugin_options() {
        ?>
        <div class="wrap">

            <h2><?php echo _e( 'Click to Contact - Float Buttons', 'ctcflatbuttons' ), ' V', CTC_VERSION; ?></h2>

            <hr>


            <form action="options.php" method="POST">
                <?php
                settings_fields( 'option_group_ctc' );     // скрытые защитные поля
                do_settings_sections( 'section_ctc_1' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
                submit_button();
                ?>
            </form>

        </div>
        <?php
    }

    /*только на главной*/
    public static function only_left() {
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['only_left'])){ $val = $val['only_left'];}else{ $val= '';}

        ?>
        <label> <input type="checkbox" name="ctc_fb_option[only_left]" value="1" <?php checked( 1, $val ) ?> />
        </label>
        <?php
    }


    /*Цвет кнопки*/
    public static function color_button() {
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['color_buttons'])){ $val = $val['color_buttons'];}else{ $val= '';}
        ?>
        <input type="text" placeholder="#fc6" name="ctc_fb_option[color_buttons]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc0(){  ?>
        <p><?php echo __('If there are no links, then the contact will not be displayed.','ctcflatbuttons');?> </p>
        <?php
    }

    public static function ctc1(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['email'])){ $val = $val['email'];}else{ $val= '';}
        ?>
        <input style="width:250px" type="email" placeholder="mail@domain.com" name="ctc_fb_option[email]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
}
    public static function ctc2(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['reddit'])){ $val = $val['reddit'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://reddit.com/r/User/" name="ctc_fb_option[reddit]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc3(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['tumblr'])){ $val = $val['tumblr'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://tumblr.com" name="ctc_fb_option[tumblr]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc4(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['skype'])){ $val = $val['skype'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="login" name="ctc_fb_option[skype]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc5(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['dribbble'])){ $val = $val['dribbble'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://dribbble.com" name="ctc_fb_option[dribbble]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc6(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['behance'])){ $val = $val['behance'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://behance.net/login" name="ctc_fb_option[behance]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc7(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['vk'])){ $val = $val['vk'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://vk.com/login" name="ctc_fb_option[vk]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }
    public static function ctc8(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['facebook'])){ $val = $val['facebook'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://facebook.com/login" name="ctc_fb_option[facebook]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc9(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['telegram'])){ $val = $val['telegram'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="login" name="ctc_fb_option[telegram]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc10(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['viber'])){ $val = $val['viber'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="tel" placeholder="7988232232" name="ctc_fb_option[viber]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc11(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['phone'])){ $val = $val['phone'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="tel" placeholder="7988232232" name="ctc_fb_option[phone]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public static function ctc12(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['twitter'])){ $val = $val['twitter'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://twitter.com" name="ctc_fb_option[twitter]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }
    public static function ctc13(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['linkedIn'])){ $val = $val['linkedIn'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://linkedIn.com" name="ctc_fb_option[linkedIn]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }
    public static function ctc14(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['insta'])){ $val = $val['insta'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://instagram.com" name="ctc_fb_option[insta]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }
    public static function ctc15(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['pinterest'])){ $val = $val['pinterest'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="text" placeholder="https://pinterest.com" name="ctc_fb_option[pinterest]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }
    public static function ctc16(){
        $val = get_option( 'ctc_fb_option' );
        if(isset( $val['whatsapp'])){ $val = $val['whatsapp'];}else{ $val= '';}
        ?>
        <input style="width:250px"  type="tel" placeholder="7988232232" name="ctc_fb_option[whatsapp]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    ## Очистка данных
    public static function sanitize_callback( $options ) {
        // очищаем
        foreach ( $options as $name => & $val ) {
            $val = strip_tags( $val );
         }

        return $options;
    }

}