<?php
/*
Plugin Name: my_plugin
Author: Kseniia Loginova
*/

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */



function myplugin_add_meta_box() {

	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'myplugin_sectionid',
			__( 'Product meta', 'myplugin_textdomain' ),
			'myplugin_meta_box_callback',
			$screen,
			'side'
		);
	}
}
add_action( 'add_meta_boxes', 'myplugin_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function myplugin_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	/*

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 
	$value = get_post_meta( $post->ID, '_my_meta_value_key', true );

	echo '<label for="myplugin_new_field">';
	_e( 'Description for this field', 'myplugin_textdomain' );
	echo '</label> ';
	echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="' . esc_attr( $value ) . '" size="25" />';

	$custom         = get_post_custom($post->ID);
    $download_id    = get_post_meta($post->ID, 'document_file_id', true);
 
    echo '<p><label for="document_file">Загрузить файл:</label><br />';
    echo '<input type="file" name="document_file" id="document_file" /></p>';
    echo '</p>';
 
    if(!empty($download_id) && $download_id != '0') {
        echo '<p><a href="' . wp_get_attachment_url($download_id) . '">
            Просмотр файла</a></p>';
        }*/

        $product_name = get_post_meta($post->ID, '_name', true);
        $product_description = get_post_meta($post->ID, '_description', true);
        $product_price = get_post_meta($post->ID, '_price', true);
        $product_currency = get_post_meta($post->ID, '_currency', true);
        wp_nonce_field( 'myplugin_save_meta_box_data', 'myplugin_meta_box_nonce' );
        $image = get_post_meta($post->ID, "_my_image_upload", true);
       
        		
		echo   '<!--Указывается схема Product.-->
		<div itemscope itemtype="http://schema.org/Product">

		<!--В поле name указывается наименование товара.-->
		  <label>Name</label>
		  <span  itemprop="name"><input type="text" name="name" size="30" value="' . esc_attr($product_name) . '"/></span>

		<!--В поле description дается описание товара.-->
		  <label>Description</label>
		  <span itemprop="description"><textarea name="description" rows="10" cols="30">' . esc_textarea($product_description) . '</textarea></span>

		<!--В поле image указывается ссылка на картинку товара.-->
		  <label>Image</label>
		  <input id="image-url" type="text" name="image" value="' . $image . '"/>
  		  <input id="upload-button" type="button" class="button" value="Upload Image" />

		<!--Указывается схема Offer.-->
		  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer"> 

		<!--В поле price указывается цена товара.-->
		    <label>Price</label>
		    <span itemprop="price"><input type="text" name="price" size="30" value="' . esc_attr($product_price) . '"/></span>

		<!--В поле priceCurrency указывается валюта.-->
		    <label>Currency</label>
		    <span itemprop="priceCurrency"><input type="text" name="currency" size="30" value="' . esc_attr($product_currency) . '"/></span>
		  </div>
		</div>';

}

/* Add the media uploader script */
function my_media_lib_uploader_enqueue() {
    wp_enqueue_media();
    wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
    wp_enqueue_script( 'media-lib-uploader-js' );
}
add_action('admin_enqueue_scripts', 'my_media_lib_uploader_enqueue');


/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function myplugin_save_meta_box_data( $post_id ) {

	global $wpdb;

	if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['currency'])) 
		return; 

	//не происходит ли автосохранение? 
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		return; 

	// не ревизию ли сохраняем? 
	if (wp_is_post_revision($postID)) 
		return; 

	check_admin_referer('myplugin_save_meta_box_data', 'myplugin_meta_box_nonce');

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	$product_name = sanitize_text_field($_POST['name']);
	update_post_meta($post_id, '_name', $product_name);

	$product_description = sanitize_text_field($_POST['description']);
	update_post_meta($post_id, '_description', $product_description);
  
	if ( !empty( $_POST['image'] ) ) {
	  $image_url = $_POST['image'];
	  $wpdb->insert( 'images', array( 'image_url' => $image_url ), array( '%s' ) ); 
	}
	update_post_meta($post_id, "_my_image_upload", $image_url);

	$product_price = sanitize_text_field($_POST['price']);
	update_post_meta($post_id, '_price', $product_price);

	$product_currency = sanitize_text_field($_POST['currency']);
	update_post_meta($post_id, '_currency', $product_currency);
}
add_action( 'save_post', 'myplugin_save_meta_box_data' );
?>