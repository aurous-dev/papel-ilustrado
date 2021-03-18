<?php

class DHWC_Ajax_Query{
	
	private static $_chosen_filters;
	
	public static function init(){
		add_filter('woocommerce_product_query_tax_query', array(__CLASS__,'product_query_tax_query'),10,2);
		add_filter('woocommerce_product_query_meta_query', array(__CLASS__,'product_query_meta_query'),10,2);
		add_filter('loop_shop_post_in', array(__CLASS__,'loop_shop_post_in'));
		
		//Is filtered
		add_filter('woocommerce_is_filtered', array(__CLASS__,'is_filtered'));
	}
	
	/**
	 * Returns true when filtering products using DHWC filter widget.
	 *
	 * @return bool
	 */
	public static function is_filtered($is_filtered){
		$stock_filter_query = dhwc_ajax_get_stock_status_filter_query();
		$status_filter_query = dhwc_ajax_get_product_status_filter_query();
		if( count( self::$_chosen_filters ) > 0 || 
			isset($_GET['acf']) || 
			isset($_GET['weight_filter']) || 
			isset($_GET['length_filter']) ||
			isset($_GET['width_filter']) ||
			isset($_GET['height_filter']) ||
			isset($_GET[$stock_filter_query]) ||
			isset($_GET[$status_filter_query])
		){
			return true;
		}
		return $is_filtered;
	}
	
	/**
	 * Return an array post ids when filtering on sale.
	 * 
	 * @param array $post_in
	 */
	public static function loop_shop_post_in($post_in){
		$filter_status_query = dhwc_ajax_get_product_status_filter_query();
		$filter_stock_status = isset( $_GET[$filter_status_query]) ?  $_GET[$filter_status_query] : false;
		if('onsale'===$filter_stock_status){
			return wc_get_product_ids_on_sale();
		}elseif ('featured'===$filter_stock_status){
			return wc_get_featured_product_ids();
		}
		return $post_in;
	}
	/**
	 * Return a meta query for filtering product meta.
	 * 
	 * @param  array $meta_query
	 * @param  WC_Query $wc_query
	 * @return array
	 */
	public static function product_query_meta_query($meta_query, $wc_query){
		$filter_stock_status_query = dhwc_ajax_get_stock_status_filter_query();
		$filter_stock_status = isset( $_GET[$filter_stock_status_query]) ?  $_GET[$filter_stock_status_query] : false;
		if($filter_stock_status && array_key_exists($filter_stock_status,wc_get_product_stock_status_options())){
			$meta_query['stock_status_filter'] = array(
				'key'   => '_stock_status',
				'value' => wc_clean( wp_unslash( $filter_stock_status ) ),
			);
		}
		$weight_dimensions_options = dhwc_ajax_get_weight_dimensions_options();
		foreach ($weight_dimensions_options as $key=>$value){
			$filter_name = $key.'_filter';
			$prefix = dhwc_ajax_get_weight_dimensions_meta_prefix();
			if(!empty($_GET[$filter_name])){
				$filtered =  array_map( 'absint', explode( '-', wp_unslash( $_GET[$filter_name] ) ) );
				
				$min_value = !empty($filtered) ? floatval($filtered[0]) : 0;
				$max_value = !empty($filtered) ? floatval($filtered[1]) : PHP_INT_MAX;
				
				
				$meta_query[$filter_name] = array(
					'key'   => $prefix.$key,
					'value'   => array( $min_value, $max_value ),
					'compare' => 'BETWEEN',
					'type'    => 'DECIMAL(10,0)',
				);
			}
		}
		
		if (defined('ACF') && ! empty( $_GET['acf'] ) ) {
			$acf_prefix = dhwc_ajax_get_acf_field_filter_query_prefix();
			foreach ( $_GET as $key => $value ) { // WPCS: input var ok, CSRF ok.
				if ( 0 === strpos( $key, $acf_prefix) ) {
					
					$field_name = str_replace( $acf_prefix, '', $key );
					$field = acf_get_field($field_name);
					
					if(empty($field)){
						continue;
					}
					
					$fieltered 	= wc_clean( wp_unslash( $value ) );
					
					if ( empty($fieltered ) ){
						continue;
					}
					
					$field_query = array(
						'key'   => $field_name,
						'value' => $fieltered,
					);
					
					if('checkbox' === $field['type'] && ('select' === $field['type'] && $field['multiple'])){
						$field_query['compare']='LIKE';
					}
					
					$meta_query[$key] = $field_query;
				}
			}
		}
		
		return $meta_query;
	}
	/**
	 * Return a tax query for filtering product tax.
	 * 
	 * @param  array $tax_query
	 * @param  WC_Query $wc_query
	 * @return array
	 */
	public static function product_query_tax_query($tax_query, $wc_query){
		foreach (self::get_layered_nav_chosen_filters() as $taxonomy=>$data){
			$include_children = false;
			$operator = 'and' === $data['query_type'] ? 'AND' : 'IN';
			if('product_cat'===$taxonomy){
				$include_children = true;
				$operator = 'IN';
			}
			$tax_query[] = array(
				'taxonomy'         => $taxonomy,
				'field'            => 'slug',
				'terms'            => $data['terms'],
				'operator'         => $operator,
				'include_children' => $include_children,
			);
		}
		return $tax_query;
	}
	/**
	 * Get an array of attributes and terms selected with the layered nav widget.
	 *
	 * @return array
	 */
	public static function get_layered_nav_chosen_filters(){
		if ( ! is_array( self::$_chosen_filters ) ) {
			self::$_chosen_filters = array();
			if ( ! empty( $_GET ) ) {
				$taxonomies     = array(
					'product_cat'		=> dhwc_ajax_get_category_filter_query(),
					'product_brand'		=> dhwc_ajax_get_brand_filter_query(),
					'product_tag'		=> dhwc_ajax_get_tag_filter_query()
				);
				foreach (dhwc_ajax_get_product_custom_taxonomies() as $custom_tax=>$label){
					$taxonomies[$custom_tax] = dhwc_ajax_get_custom_taxonomy_filter_query($custom_tax);
				}
				foreach ($taxonomies as $taxonomy=>$filter_key){
					$filter = ! empty( $_GET[ $filter_key ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_key ] ) ) ) : array();
					if(empty($filter)){
						continue;
					}
					self::$_chosen_filters[$taxonomy]['terms'] 		= array_map( 'sanitize_title', $filter );
					self::$_chosen_filters[$taxonomy]['query_type']	= 'and';
				}
			}
		}
		return apply_filters('dhwc_ajax_query_chosen_filters', self::$_chosen_filters);
	}
	
	/**
	 * Based on DHWC_Ajax_Query::parse_post_in
	 */
	public static function get_product_featured_sql($operators = 'IN'){
		global $wpdb;
		$status_filter_query = dhwc_ajax_get_product_status_filter_query();
		$product_featured = wc_get_featured_product_ids();
		if(!empty($_GET[$status_filter_query]) && !empty($product_featured)){
			return " $wpdb->posts.ID $operators ( " . implode( ',', $product_featured ) . ") ";
		}
		return false;
	}
	
	/**
	 * Based on DHWC_Ajax_Query::parse_post_in
	 */
	public static function get_product_sale_sql($operators = 'IN'){
		global $wpdb;
		$status_filter_query = dhwc_ajax_get_product_status_filter_query();
		$product_sales = wc_get_product_ids_on_sale();
		if(!empty($_GET[$status_filter_query]) && !empty($product_sales)){
			return " $wpdb->posts.ID $operators ( " . implode( ',', $product_sales ) . ") ";
		}
		return false;
	}
	
	public static function get_product_price_range_query(){
		
		global $wpdb;
		
		$query = array('join'=>'','where'=>'');
		
		if ( !isset($wpdb->wc_product_meta_lookup) || ( ! isset( $_GET['max_price'] ) && ! isset( $_GET['min_price'] ) ) ) {
			return $query;
		}
		
		$current_min_price = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
		$current_max_price = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : PHP_INT_MAX; // WPCS: input var ok, CSRF ok.
		
		/**
		 * Adjust if the store taxes are not displayed how they are stored.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates = WC_Tax::get_rates( $tax_class );
		
			if ( $tax_rates ) {
				$current_min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
				$current_max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
			}
		}
		
		$query['join']   = " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		
		$query['where'] .= $wpdb->prepare(
			' AND wc_product_meta_lookup.min_price >= %f AND wc_product_meta_lookup.max_price <= %f ',
			$current_min_price,
			$current_max_price
		);
		
		return apply_filters('dhwc_ajax_product_price_range_query', $query, $current_min_price, $current_max_price);
	}
	
}
DHWC_Ajax_Query::init();