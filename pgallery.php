<?php
/*
    Plugin Name:    pGallery WP
    Plugin URI:     https://pgallery.ru/wordpress/
    Description:    pGallery WordPress Plugin
    Version:        0.0.2 alpha
    Author:         Ruzhentsev Alexandr
    Author URI:     http://pgallery.ru/
    License:        GPLv2
*/

include_once(plugin_dir_path( __FILE__ ) . '/include/wp-functions.php');

if(!extension_loaded('curl'))
    die("Фатальная ошибка: отсутствует расширение PHP curl.");

register_activation_hook( __FILE__, 'pgallery_install');
register_deactivation_hook( __FILE__, 'pgallery_deactivation');

add_action('admin_menu', 'pgallery_control_menu');
add_action('wp_footer','pgallery_fancybox_js');

if (function_exists ('add_shortcode') ) {

    add_shortcode('PGALLERY-GALLERY', 'pgallery_gallery_shortcode');
    add_shortcode('PGALLERY-ALBUM', 'pgallery_album_shortcode');
    add_shortcode('PGALLERY-IMAGE', 'pgallery_image_shortcode');

}

wp_enqueue_script('jquery.fancybox.js', 
        plugins_url( 'fancybox/jquery.fancybox.js', __FILE__ ),
        array('jquery'),
        '3.2.1',
        'in_footer');

wp_enqueue_style('jquery.fancybox.css', 
        plugins_url( 'fancybox/jquery.fancybox.css', __FILE__ ),
        null,
        '3.2.1');

wp_enqueue_style('fancybox.thumb.css', 
        plugins_url( 'fancybox/fancybox.thumb.css', __FILE__ ),
        array('jquery.fancybox.css'),
        '0.3');
