<?php

function pgallery_install(){
    add_option('pgallery_url', 'https://');
}

function pgallery_deactivation() {
    delete_option('pgallery_url');
}

function pgallery_albums_shortcode() {
    
    $run = pgallery_execute('gallery');
    
    if($run['result'] == 'Successful' and $run['error'] != true) {

        foreach ($run['data'] as $album) {
            
            $result .= '<div>                
                    <p>
                        <a href="' . $album['url'] . '" target="_blank"><img src="' . $album['thumb'] . '"/></a>
                    </p>
                    <p>' . $album['name'] . '</p>
                </div>';
            
        }
        
        return $result;
    }
    
    return '<b>Не удалось получить список альбомов</b>';
}

function pgallery_album_shortcode($atts) {
    extract( shortcode_atts( [
        'id' => ''
    ], $atts ) );

    return '<b>Album ID: ' . $id . '</b>';
}

function pgallery_execute($method, $options = false){

    $pgallery_url = get_option('pgallery_url') . "/api/v1/" . $method;

    if(is_array($options))
        $pgallery_url .= $pgallery_url . "?" . http_build_query($options);
    
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $pgallery_url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_TIMEOUT, '60');
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);  
    $result = curl_exec($c);  
    curl_close($c);

    return json_decode($result, true);    
    
}

function pgallery_control_menu() {
  add_submenu_page('options-general.php', 'pGallery Admin Page', 'Настройки pGallery', 'manage_options', 'pgallery', 'pgallery_control_options');
}

function pgallery_control_options() {


if (isset($_POST['submit'])) {  
    if(function_exists('current_user_can') && !current_user_can('manage_options'))
        die ( _e('Hacker?') );

    if (function_exists('check_admin_referer'))
        check_admin_referer('pgallery_form');

    update_option('pgallery_url', $_POST['pgallery_url']);
}

$pgallery_url = get_option('pgallery_url');

?>

    <div class='wrap'>
	<h2><?php _e('Settings'); ?> pGallery</h2>
	<form name="pgallery" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=pgallery&updated=true">
	
        <?php 
	if (function_exists('wp_nonce_field') ) {
	    wp_nonce_field('pgallery_form');
	}
	?>
	
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Адрес фотогалереи pGallery:</th>

                <td>
                    <input type="text" name="pgallery_url" size="40" value="<?php echo $pgallery_url; ?>">
                </td>
            </tr>
        </table>
	
	<p class="submit">
	    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Save Changes') ?>">
	</p>
	</form>
    </div>

<?
}

