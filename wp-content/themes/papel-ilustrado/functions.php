<?php

// Add support for thumbnails
add_theme_support('post-thumbnails');

// Add suppport for menus
register_nav_menus(
	array(
		'primary-menu' => __('Primary Menu'),
		'secondary-menu' => __('Secondary Menu')
	)
);
// If Options PAGE
if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title' 	=> 'Opciones Generales',
		'menu_title'	=> 'Opciones Generales',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}
// If Options PAGE

//  Expose ACF on products

$post_types = array_merge(get_post_types(), cptui_get_post_type_slugs());

foreach ($post_types as $type) {
	add_filter(
		'acf/rest_api/' . $type . '/get_fields',
		function ($data, $response) use ($post_types) {
			if ($response instanceof WP_REST_Response) {
				$data = $response->get_data();
			}

			array_walk_recursive($data, 'get_fields_recursive', $post_types);

			return $data;
		},
		10,
		3
	);
}

function get_fields_recursive($item)
{
	if (is_object($item)) {
		$item->acf = array();

		if ($fields = get_fields($item)) {
			$item->acf = $fields;
			array_walk_recursive($item->acf, 'get_fields_recursive');
		}
	}
}


// Expand max variations in bulk
define('WC_MAX_LINKED_VARIATIONS', 100);

// Test adding a number of total items on wishlist

if (defined('YITH_WCWL') && !function_exists('yith_wcwl_get_items_count')) {
	function yith_wcwl_get_items_count()
	{
		ob_start();
?>
	   <?php echo esc_html(yith_wcwl_count_all_products()); ?>
	 <?php
		return ob_get_clean();
	}
	add_shortcode('yith_wcwl_items_count', 'yith_wcwl_get_items_count');
}

if (defined('YITH_WCWL') && !function_exists('yith_wcwl_ajax_update_count')) {
	function yith_wcwl_ajax_update_count()
	{
		wp_send_json(array(
			'count' => yith_wcwl_count_all_products()
		));
	}
	add_action('wp_ajax_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count');
	add_action('wp_ajax_nopriv_yith_wcwl_update_wishlist_count', 'yith_wcwl_ajax_update_count');
}

if (defined('YITH_WCWL') && !function_exists('yith_wcwl_enqueue_custom_script')) {
	function yith_wcwl_enqueue_custom_script()
	{
		wp_add_inline_script(
			'jquery-yith-wcwl',
			"
		   jQuery( function( $ ) {
			 $( document ).on( 'added_to_wishlist removed_from_wishlist', function() {
			   $.get( yith_wcwl_l10n.ajax_url, {
				 action: 'yith_wcwl_update_wishlist_count'
			   }, function( data ) {
				 $('.yith-wcwl-items-count').html( data.count );
			   } );
			 } );
		   } );
		 "
		);
	}
	add_action('wp_enqueue_scripts', 'yith_wcwl_enqueue_custom_script', 20);
}


add_action( 'after_setup_theme', 'setup_woocommerce_support' );

 function setup_woocommerce_support()
{
  add_theme_support('woocommerce');
}
?>