<?php
/*
Plugin Name: prodservmeta_plugin
Author: Kseniia Loginova
*/

function prod_meta_plugin_add_meta_box()
{

    $screens = array('post', 'page');

    foreach ($screens as $screen) {

        add_meta_box(
            'metaplugin_sectionid',
            'Product meta',
            'plugin_meta_box_callback',
            $screen,
            'side'
        );
    }
}

add_action('add_meta_boxes', 'prod_meta_plugin_add_meta_box');

function plugin_meta_box_callback($post)
{

    $product_name = get_post_meta($post->ID, '_name', true);
    $product_description = get_post_meta($post->ID, '_description', true);
    $product_price = get_post_meta($post->ID, '_price', true);
    $product_currency = get_post_meta($post->ID, '_currency', true);
    wp_nonce_field('prod_meta_plugin_save_meta_box_data', 'prodplugin_meta_box_nonce');
    $image = get_post_meta($post->ID, "_my_image_upload", true);

    echo '<!--Указывается схема Product.-->
	<div itemscope itemtype="http://schema.org/Product">
		<!--В поле name указывается наименование товара.-->
		<label>Name</label>
		<span  itemprop="name"><input type="text" name="name" size="30" value="' . esc_attr($product_name) . '"/></span>
		<!--В поле description дается описание товара.-->
		<label>Description</label>
		<span itemprop="description"><textarea name="description" rows="10" cols="30">' . esc_attr($product_description) . '</textarea></span>
		<!--В поле image указывается ссылка на картинку товара.-->
		<label>Image</label>
		<input itemprop="image" id="image-url" type="text" name="image" value="' . esc_attr($image) . '"/>
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

function prod_plugin_media_lib_uploader_enqueue()
{
    wp_enqueue_media();
    wp_register_script('media-lib-uploader-js', plugins_url('media-lib-uploader.js', __FILE__), array('jquery'));
    wp_enqueue_script('media-lib-uploader-js');
}

add_action('admin_enqueue_scripts', 'prod_plugin_media_lib_uploader_enqueue');


function prod_meta_plugin_save_meta_box_data($post_id)
{

    global $wpdb;

    if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price']) || !isset($_POST['currency']))
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (wp_is_post_revision($postID))
        return;

    check_admin_referer('prod_meta_plugin_save_meta_box_data', 'prodplugin_meta_box_nonce');

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $product_name = sanitize_text_field($_POST['name']);
    update_post_meta($post_id, '_name', $product_name);

    $product_description = sanitize_text_field($_POST['description']);
    update_post_meta($post_id, '_description', $product_description);

    if (!empty($_POST['image'])) {
        $image_url = $_POST['image'];
        $wpdb->insert('images', array('image_url' => $image_url), array('%s'));
    }
    update_post_meta($post_id, "_my_image_upload", $image_url);

    $product_price = sanitize_text_field($_POST['price']);
    update_post_meta($post_id, '_price', $product_price);

    $product_currency = sanitize_text_field($_POST['currency']);
    update_post_meta($post_id, '_currency', $product_currency);
}

add_action('save_post', 'prod_meta_plugin_save_meta_box_data');

require_once(ABSPATH . 'wp-content/plugins/prodservmeta_plugin/pr_meta_widget.php');
add_action('widgets_init', 'pr_meta_load_widget');


function add_short_meta($atts)
{
    global $post;
    extract(shortcode_atts(array(
        'id' => '',
    ), $atts));

    $atts['id'] = empty($atts['id']) ? $post->ID : $atts['id'];
    $product_name = get_post_meta($atts['id'], '_name', true);
    $product_description = get_post_meta($atts['id'], '_description', true);
    $product_price = get_post_meta($atts['id'], '_price', true);
    $product_currency = get_post_meta($atts['id'], '_currency', true);
    $image = get_post_meta($atts['id'], "_my_image_upload", true);

    if (!empty($product_name) && !empty($product_description) && !empty($product_price) && !empty($product_currency) && !empty($image)) {
        $meta = '<div><strong>Product meta</strong>
		    <br>Name: ' . esc_attr($product_name) . '
			<br>Description: ' . esc_attr($product_description) . '
			<br>Image: <img src="' . esc_attr($image) . '"</>
			<br>Price: ' . esc_attr($product_price) . '
			<br>Currency: ' . esc_attr($product_currency) . '
		    </div>';
        return $meta;
    }
}

add_shortcode('add_short_meta', 'add_short_meta');
?>