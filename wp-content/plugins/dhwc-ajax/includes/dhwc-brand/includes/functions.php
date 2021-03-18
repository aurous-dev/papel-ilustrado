<?php

function dhwc_brand_get_class($class = '', $band = null){
	$classes = wc_get_product_cat_class($class,$band);
	$classes[] = 'product-brand';
	return array_unique( array_filter( $classes ) );
}

function dhwc_brand_class($class = '', $brand = null){
	echo 'class="' . esc_attr( join( ' ', dhwc_brand_get_class( $class, $brand ) ) ) . '"';
}

function dhwc_brand_recount_product_after_stock_change($product_id){
	if ( 'yes' !== get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		return;
	}

	$product_terms = get_the_terms( $product_id, 'product_brand' );

	if ( $product_terms ) {
		$product_cats = array();

		foreach ( $product_terms as $term ) {
			$product_cats[ $term->term_id ] = $term->parent;
		}

		_wc_term_recount( $product_cats, get_taxonomy( 'product_brand' ), false, false );
	}
}
add_action( 'woocommerce_product_set_stock_status', 'dhwc_brand_recount_product_after_stock_change' );

function dhwc_brand_change_term_counts($taxonomies){
	$taxonomies[] = 'product_brand';
	return $taxonomies;
}
add_filter('woocommerce_change_term_counts', 'dhwc_brand_change_term_counts');
