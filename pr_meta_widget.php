<?php

class pr_meta_widget extends WP_Widget
{

    function __construct()
    {

        parent::__construct(
            'pr_meta_widget',
            'Product Meta Widget',
            array('description' => 'Widget for product or service meta',)
        );
    }

    public function widget($args, $instance)
    {

        global $post;
        $product_name = get_post_meta($post->ID, '_name', true);
        $product_description = get_post_meta($post->ID, '_description', true);
        $product_price = get_post_meta($post->ID, '_price', true);
        $product_currency = get_post_meta($post->ID, '_currency', true);
        $image = get_post_meta($post->ID, "_my_image_upload", true);

        if (!empty($product_name) && !empty($product_description) && !empty($product_price) && !empty($product_currency) && !empty($image)) {

            $title = apply_filters('widget_title', $instance['title']);
            echo $args['before_widget'];
            if (!empty($title))
                echo $args['before_title'] . $title . $args['after_title'];
            echo '<p> Name: ' . esc_attr($product_name) . '</p>
					<p>Description: ' . esc_attr($product_description) . '</p>
					<p>Image: <img src="' . esc_attr($image) . '"</></p>
					<p>Price: ' . esc_attr($product_price) . '</p>
					<p>Currency: ' . esc_attr($product_currency) . '</p>';
            echo $args['after_widget'];
        } else return;

    }

    public function form($instance)
    {

        $title = isset($instance['title']) ? $instance['title'] : 'Product or service meta';

        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo 'Title:'; ?></label>
            <input id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>

        <?php
    }

    public function update($new_instance, $old_instance)
    {

        $instance = ['title' => (isset($new_instance['title'])) ? strip_tags($new_instance['title']) : ''];
        return $instance;

    }

}

function pr_meta_load_widget()
{

    register_widget('pr_meta_widget');

}

?>