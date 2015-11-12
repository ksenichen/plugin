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
	/*wp_nonce_field( 'myplugin_save_meta_box_data', 'myplugin_meta_box_nonce' );

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

        $data = get_post_meta($post->ID, '_name', true);
        		
		echo   '<!--Указывается схема Product.-->
		<div itemscope itemtype="http://schema.org/Product">

		<!--В поле name указывается наименование товара.-->
		  <label>Name</label>
		  <span  itemprop="name"><input type="text" name="name" size="30" value="' . esc_attr($data) . '"/></span>

		<!--В поле description дается описание товара.-->
		  <label>Description</label>
		  <span itemprop="description"><textarea name="description" rows="10" cols="30"/></textarea></span>

		<!--В поле image указывается ссылка на картинку товара.-->
		  <label>Image</label>
		  <img name="img"src="http://imageexample.com/iphone6plus.jpg" itemprop="image">

		<!--Указывается схема Offer.-->
		  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer"> 

		<!--В поле price указывается цена товара.-->
		    <label>Price</label>
		    <span itemprop="price"><input type="text" name="price" size="30"/></span>

		<!--В поле priceCurrency указывается валюта.-->
		    <label>Currency</label>
		    <span itemprop="priceCurrency"><input type="text" name="currency" size="30"/></span>
		  </div>
		</div>';

}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function myplugin_save_meta_box_data( $post_id ) {

	if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['currency'])) 
		return; 

	//не происходит ли автосохранение? 
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		return; 

	// не ревизию ли сохраняем? 
	if (wp_is_post_revision($postID)) 
		return; 

	$product_name = sanitize_text_field($_POST['name']);
	update_post_meta($post_id, '_name', $product_name);


}
add_action( 'save_post', 'myplugin_save_meta_box_data' );
?>