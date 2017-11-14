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

register_activation_hook( __FILE__, 'pgallery_install');
register_deactivation_hook( __FILE__, 'pgallery_deactivation');

add_action('admin_menu', 'pgallery_control_menu');

if (function_exists ('add_shortcode') ) {

    add_shortcode('PGALLERY-ALBUMS', 'pgallery_albums_shortcode');
    add_shortcode('PGALLERY-ALBUM', 'pgallery_album_shortcode');

}

