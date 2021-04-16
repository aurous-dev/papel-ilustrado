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

// Add multiple productos/variants to cart

function woocommerce_maybe_add_multiple_products_to_cart() {
  // Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
  if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
      return;
  }

  remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

  $product_ids = explode( ',', $_REQUEST['add-to-cart'] );
  $count       = count( $product_ids );
  $number      = 0;

  foreach ( $product_ids as $product_id ) {
      if ( ++$number === $count ) {
          // Ok, final item, let's send it back to woocommerce's add_to_cart_action method for handling.
          $_REQUEST['add-to-cart'] = $product_id;

          return WC_Form_Handler::add_to_cart_action();
      }

      $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
      $was_added_to_cart = false;

      $adding_to_cart    = wc_get_product( $product_id );

      if ( ! $adding_to_cart ) {
          continue;
      }

      if ( $adding_to_cart->is_type( 'simple' ) ) {

          // quantity applies to all products atm
          $quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
          $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

          if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
              wc_add_to_cart_message( array( $product_id => $quantity ), true );
          }

      } else {

          $variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( wp_unslash( $_REQUEST['variation_id'] ) );
          $quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) ); // WPCS: sanitization ok.
          $missing_attributes = array();
          $variations         = array();
          $adding_to_cart     = wc_get_product( $product_id );

          if ( ! $adding_to_cart ) {
            continue;
          }

          // If the $product_id was in fact a variation ID, update the variables.
          if ( $adding_to_cart->is_type( 'variation' ) ) {
            $variation_id   = $product_id;
            $product_id     = $adding_to_cart->get_parent_id();
            $adding_to_cart = wc_get_product( $product_id );

            if ( ! $adding_to_cart ) {
              continue;
            }
          }

          // Gather posted attributes.
          $posted_attributes = array();

          foreach ( $adding_to_cart->get_attributes() as $attribute ) {
            if ( ! $attribute['is_variation'] ) {
              continue;
            }
            $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

            if ( isset( $_REQUEST[ $attribute_key ] ) ) {
              if ( $attribute['is_taxonomy'] ) {
                // Don't use wc_clean as it destroys sanitized characters.
                $value = sanitize_title( wp_unslash( $_REQUEST[ $attribute_key ] ) );
              } else {
                $value = html_entity_decode( wc_clean( wp_unslash( $_REQUEST[ $attribute_key ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // WPCS: sanitization ok.
              }

              $posted_attributes[ $attribute_key ] = $value;
            }
          }

          // If no variation ID is set, attempt to get a variation ID from posted attributes.
          if ( empty( $variation_id ) ) {
            $data_store   = WC_Data_Store::load( 'product' );
            $variation_id = $data_store->find_matching_product_variation( $adding_to_cart, $posted_attributes );
          }

          // Do we have a variation ID?
          if ( empty( $variation_id ) ) {
            throw new Exception( __( 'Please choose product options&hellip;', 'woocommerce' ) );
          }

          // Check the data we have is valid.
          $variation_data = wc_get_product_variation_attributes( $variation_id );

          foreach ( $adding_to_cart->get_attributes() as $attribute ) {
            if ( ! $attribute['is_variation'] ) {
              continue;
            }

            // Get valid value from variation data.
            $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
            $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ]: '';

            /**
             * If the attribute value was posted, check if it's valid.
             *
             * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
             */
            if ( isset( $posted_attributes[ $attribute_key ] ) ) {
              $value = $posted_attributes[ $attribute_key ];

              // Allow if valid or show error.
              if ( $valid_value === $value ) {
                $variations[ $attribute_key ] = $value;
              } elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs() ) ) {
                // If valid values are empty, this is an 'any' variation so get all possible values.
                $variations[ $attribute_key ] = $value;
              } else {
                throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
              }
            } elseif ( '' === $valid_value ) {
              $missing_attributes[] = wc_attribute_label( $attribute['name'] );
            }
          }
          if ( ! empty( $missing_attributes ) ) {
            throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
          }

        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

        if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
          wc_add_to_cart_message( array( $product_id => $quantity ), true );
        }
      }
  }
}
add_action( 'wp_loaded', 'woocommerce_maybe_add_multiple_products_to_cart', 15 );
?>