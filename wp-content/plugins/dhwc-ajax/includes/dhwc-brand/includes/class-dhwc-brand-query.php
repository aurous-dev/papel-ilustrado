<?php

class DHWC_Brand_Query {
	
	private static $_chosen_filters;
	
	public function __construct(){
		add_filter('woocommerce_product_query_tax_query', array(__CLASS__,'product_query_tax_query'),999,2);
	}
	
	public static function product_query_tax_query( $tax_query, $wc_query ){
		foreach (self::get_layered_nav_chosen_filters() as $taxonomy=>$data){
			$tax_query[] = array(
				'taxonomy'         => $taxonomy,
				'field'            => 'slug',
				'terms'            => $data['terms'],
				'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
				'include_children' => false,
			);
		}
		return $tax_query;
	}
	
	public static function get_layered_nav_chosen_filters(){
		if ( ! is_array( self::$_chosen_filters ) ) {
			self::$_chosen_filters = array();
			$filter_key = 'brand_filter';
			if ( ! empty( $_GET[$filter_key] ) ) {
				$filter = ! empty( $_GET[ $filter_key ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_key ] ) ) ) : array();
				self::$_chosen_filters['product_brand']['terms'] 		= array_map( 'sanitize_title', $filter );
				self::$_chosen_filters['product_brand']['query_type']	= 'and';
			}
		}
		return self::$_chosen_filters;
	}
}
new DHWC_Brand_Query();