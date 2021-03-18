<?php
class DHWC_Ajax_Widget extends WC_Widget {
	
	protected function _get_page_url(){
		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_shop() ) {
			$link = get_permalink( wc_get_page_id( 'shop' ) );
		} elseif ( is_product_category() ) {
			$link = get_term_link( get_query_var( 'product_cat' ), 'product_cat' );
		} elseif ( is_product_tag() ) {
			$link = get_term_link( get_query_var( 'product_tag' ), 'product_tag' );
		} else {
			$queried_object = get_queried_object();
			$link = get_term_link( $queried_object->slug, $queried_object->taxonomy );
		}
		return $link;
	}
	
	protected function _get_filtered_product_types(){
		return apply_filters('dhwc_ajax_widget_filtered_product_types_sql', array('product'));
	}
	
	protected function get_current_page_url() {
		
		$link = dhwc_ajax_get_current_page_url();
		
		$link = remove_query_arg('acf',$link);
		
		//Tag
		$tag_filter_query = dhwc_ajax_get_tag_filter_query();
		if ( isset( $_GET[$tag_filter_query] ) ) {
			$link = add_query_arg( $tag_filter_query, wc_clean( wp_unslash( $_GET[$tag_filter_query] ) ), $link );
		}
		
		//Category
		$category_filter_query = dhwc_ajax_get_category_filter_query();
		if ( isset( $_GET[$category_filter_query] ) ) {
			$link = add_query_arg( $category_filter_query, wc_clean( wp_unslash( $_GET[$category_filter_query] ) ), $link );
		}
	
		//Brand
		$brand_filter_query = dhwc_ajax_get_brand_filter_query();
		if ( isset( $_GET[$brand_filter_query] ) ) {
			$link = add_query_arg( $brand_filter_query, wc_clean( wp_unslash( $_GET[$brand_filter_query] ) ) , $link );
		}
		
		/* TODO
		 * Custom Taxonomy Filter
		 */
		$custom_taxonomies = dhwc_ajax_get_product_custom_taxonomies();
		if(!empty($custom_taxonomies)){
			foreach ($custom_taxonomies as $custom_taxonomy=>$custom_taxonomy_label){
				$filter_name  = dhwc_ajax_get_custom_taxonomy_filter_query($custom_taxonomy);
				if ( isset( $_GET[$filter_name] ) ) {
					$link = add_query_arg( $filter_name, wc_clean( wp_unslash( $_GET[$filter_name] ) ) , $link );
				}
			}
		}
		
		//Stock Status
		$stock_status_filter_query = dhwc_ajax_get_stock_status_filter_query();
		if ( isset( $_GET[$stock_status_filter_query] ) ) {
			$link = add_query_arg( $stock_status_filter_query, wc_clean( wp_unslash( $_GET[$stock_status_filter_query] ) ) , $link );
		}
		
		//Product Status
		$product_status_filter_query = dhwc_ajax_get_product_status_filter_query();
		if ( isset( $_GET[$product_status_filter_query] ) ) {
			$link = add_query_arg( $product_status_filter_query, wc_clean( wp_unslash( $_GET[$product_status_filter_query] ) ) , $link );
		}
		
		//Weight Dimensions Filter
		$weight_dimensions_options = dhwc_ajax_get_weight_dimensions_options();
		foreach ($weight_dimensions_options as $key=>$value){
			$filter_name = $key.'_filter';
			if(!empty($_GET[$filter_name])){
				$link = add_query_arg( $filter_name, wc_clean( wp_unslash( $_GET[$filter_name] ) ) , $link );
			}
		}
		
		//Acf
		if ( defined('ACF') && ! empty( $_GET['acf'] ) ) {
			$acf_prefix = dhwc_ajax_get_acf_field_filter_query_prefix();
			$acf_filter_count = 0;
			foreach ( $_GET as $key => $value ) { // WPCS: input var ok, CSRF ok.
				if ( 0 === strpos( $key, $acf_prefix) ) {
					$field_name = str_replace( $acf_prefix, '', $key );
					$field = acf_get_field($field_name);
					
					if ( empty( $field ) ){
						continue;
					}
					
					$acf_filter_count++;
					$link = add_query_arg($key,$value,$link);
				}
			}
			if($acf_filter_count > 0){
				$link = add_query_arg(array('acf'=>1),$link);
			}
		}
	
		//Price Min/Max.
		if ( isset( $_GET['min_price'] ) ) {
			$link = add_query_arg( 'min_price', wc_clean( wp_unslash( $_GET['min_price'] ) ), $link );
		}
	
		if ( isset( $_GET['max_price'] ) ) {
			$link = add_query_arg( 'max_price', wc_clean( wp_unslash( $_GET['max_price'] ) ), $link );
		}
	
		// Order by.
		if ( isset( $_GET['orderby'] ) ) {
			$link = add_query_arg( 'orderby', wc_clean( wp_unslash( $_GET['orderby'] ) ), $link );
		}
	
		/**
		 * Search Arg.
		 * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
		 */
		if ( get_search_query() ) {
			$link = add_query_arg( 's', rawurlencode( wp_specialchars_decode( get_search_query() ) ), $link );
		}
	
		// Post Type Arg.
		if ( isset( $_GET['post_type'] ) ) {
			$link = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $link );
		}
	
		// Min Rating Arg.
		if ( isset( $_GET['rating_filter'] ) ) {
			$link = add_query_arg( 'rating_filter', wc_clean( wp_unslash( $_GET['rating_filter'] ) ), $link );
		}
	
		// All current filters.
		if ( $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes() ) {
			foreach ( $_chosen_attributes as $name => $data ) {
				$filter_name = sanitize_title( str_replace( 'pa_', '', $name ) );
				if ( ! empty( $data['terms'] ) ) {
					$link = add_query_arg( 'filter_' . $filter_name, implode( ',', $data['terms'] ), $link );
				}
				if ( 'or' == $data['query_type'] ) {
					$link = add_query_arg( 'query_type_' . $filter_name, 'or', $link );
				}
			}
		}
	
		return $link;
	}
		
}