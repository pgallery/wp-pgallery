<?php

function pgallery_install(){
    add_option('pgallery_url', 'https://');
    add_option('pgallery_imgcount', '6');
    add_option('pgallery_title_style', '<p><a href="%URL%" target="_blank">%NAME%</a></p>');
    add_option('pgallery_image_style', '<div class="thumb"><a data-fancybox="images" href="%URL%">'
                . '<img src="%THUMB_URL%">'
                . '</a></div>');
}

function pgallery_deactivation() {
    delete_option('pgallery_url');
    delete_option('pgallery_imgcount');
    delete_option('pgallery_title_style');
    delete_option('pgallery_image_style');
}

function pgallery_gallery_shortcode() {
    
    $run = pgallery_execute('gallery');
    
    if($run['result'] == 'Successful' and $run['error'] != true) {
        
        foreach ($run['list'] as $album) {
            
            $result .= '<div>                
                    <p>
                        <a href="' . $album['url'] . '" target="_blank"><img src="' . $album['thumb'] . '"/></a>
                    </p>
                    <p>' . $album['name'] . '</p>
                </div>';
            
        }
        
        return $result;
    }
    
    if($run['message'])
        return '<b>' . $run['message'] . '</b>';
    
    return '<b>Не удалось получить список альбомов</b>';
}

function pgallery_album_shortcode($atts) {

    if(!isset($atts['count']))
        $atts['count'] = get_option('pgallery_imgcount');

    $run = pgallery_execute('album', $atts);
    
    if($run['result'] == 'Successful' and $run['error'] != true) {
        
        $pgallery_title_style = get_option('pgallery_title_style');
        $pgallery_image_style = get_option('pgallery_image_style');
        
        $result = '<div class="thumb-rows">';
        
        $title = str_replace("%URL%", $run['data']['url'], $pgallery_title_style);
        $title = str_replace("%NAME%", $run['data']['name'], $title);

        $result .= $title;
        
        foreach ($run['list'] as $image) {
            
            $img = str_replace("%URL%", $image['url'], $pgallery_image_style);
            $img = str_replace("%THUMB_URL%", $image['thumb'], $img);

            $result .= $img;
        }
        
        $result .= '</div>';
        
        return $result;
        
    }
    
    if($run['message'])
        return '<b>' . $run['message'] . '</b>';
    
    return '<b>Не удалось получить доступ к альбому</b>';
}

function pgallery_image_shortcode($atts){
    
    $run = pgallery_execute('image', $atts);
    
    if($run['result'] == 'Successful' and $run['error'] != true) {
        
        $pgallery_image_style = get_option('pgallery_image_style');
        
        $result = str_replace("%URL%", $run['data']['url'], $pgallery_image_style);
        $result = str_replace("%THUMB_URL%", $run['data']['thumb'], $result);
        
        return $result;
        
    }
    
    if($run['message'])
        return '<b>' . $run['message'] . '</b>';
    
    return '<b>Не удалось получить доступ к изображению</b>';    
}

function pgallery_execute($method, $options = false){

    $pgallery_url = get_option('pgallery_url') . "/api/v1/" . $method;

    if(is_array($options))
        $pgallery_url .= "?" . http_build_query($options);
    
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
        update_option('pgallery_imgcount', $_POST['pgallery_imgcount']);
        update_option('pgallery_title_style', stripcslashes($_POST['pgallery_title_style']));
        update_option('pgallery_image_style', stripcslashes($_POST['pgallery_image_style']));
    }

    $pgallery_url = get_option('pgallery_url');
    $pgallery_imgcount = get_option('pgallery_imgcount');
    $pgallery_title_style = get_option('pgallery_title_style');
    $pgallery_image_style = get_option('pgallery_image_style');

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
            <tr valign="top">
                <th scope="row">Количество изображений:</th>

                <td>
                    <input type="text" name="pgallery_imgcount" size="40" value="<?php echo $pgallery_imgcount; ?>">
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row">Шаблон оформления заголовка:</th>

                <td>
                    <textarea name="pgallery_title_style" cols="40" rows="4"><?php echo stripcslashes($pgallery_title_style); ?></textarea>
                </td>
            </tr>             
            <tr valign="top">
                <th scope="row">Шаблон оформления изображения:</th>

                <td>
                    <textarea name="pgallery_image_style" cols="40" rows="4"><?php echo stripcslashes($pgallery_image_style); ?></textarea>
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


function pgallery_fancybox_js() {

?>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".fancybox").fancybox();
    });
</script>

<?php 

}