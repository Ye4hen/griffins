<?php
/*
 * Plugin Name:     Uploading SVG, WEBP and ICO files
 * Version:         1.2.1
 * Text Domain:     uploading-svgwebp-and-ico-files
 * Plugin URI:      https://yrokiwp.ru
 * Description:     This great plugin allows you to avoid the error about not uploading SVG, Webp and ICO files. Just install and activate it and it will be ready to go.
 * Author:          dmitrylitvinov
 * Author URI:     https://yrokiwp.ru
 *
 *
 * License:           GNU General Public License v3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly



add_filter( 'upload_mimes', 'uswift_upload' );

# Добавляет SVG в список разрешенных для загрузки файлов.
function uswift_upload( $mimes ) {
    if ( current_user_can( 'manage_options' ) ) {
        $mimes['svg']  = 'image/svg+xml';
    }

    return $mimes;
}



 add_filter( 'wp_check_filetype_and_ext', 'uswift_filter_ico_upload', 10, 4 );
function uswift_filter_ico_upload( $types, $file, $filename, $mimes ){
    if ( false !== strpos( $filename, '.ico' ) ) {
        $types['ext'] = 'ico';
        $types['type'] = 'image/ico';
    }

   if ( false !== strpos( $filename, '.webp' ) ) {
        $types['ext'] = 'webp';
        $types['type'] = 'image/webp';
    }


    if( substr( $filename, -4 ) == '.svg' ){
        $types['ext'] = 'svg';
        $types['type'] = 'image/svg+xml';
    }


    return $types;
}


add_filter( 'wp_prepare_attachment_for_js', 'uswift_show_svg_in_media_library' );
# Формирует данные для отображения SVG как изображения в медиабиблиотеке.
function uswift_show_svg_in_media_library( $response ) {

    if ( $response['mime'] === 'image/svg+xml' ) {
        // С выводом названия файла
        $response['image'] = [
            'src' => $response['url'],
        ];
    }
    return $response;
}