<?php

function dhwc_ajax_is_request(){
	return isset($_SERVER['HTTP_DHWCAJAX']) && 'yes' === $_SERVER['HTTP_DHWCAJAX'];
}

function dhwc_ajax_get_option($option ,$default = false){
	$value = get_option($option,$default);
	if(empty($value)){
		$value = apply_filters('dhwc_ajax_option_default', $default, $option);
	}
	$value = apply_filters("dhwc_ajax_option_{$option}", $value);
	return apply_filters("dhwc_ajax_option", $value, $option);
}

function dhwc_ajax_get_product_custom_taxonomies($none = false){
	$taxonomies = get_object_taxonomies('product','object');
	$exclude = array('product_brand','product_tag','product_cat');
	$customs = array();
	if($none){
		$customs[''] = '';
	}
	foreach ($taxonomies as $tax_name=>$tax){
		if($tax->public && $tax->rewrite && !in_array($tax_name, $exclude)){
			$customs[$tax_name] = ucwords($tax->label);
		}
	}
	return apply_filters('dhwc_ajax_product_custom_taxonomies', $customs);
}

function dhwc_ajax_get_current_page_url(){
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
	$link = apply_filters('dhwc_ajax_current_page_url', $link);
	return $link;
}

function dhwc_ajax_get_brand_filter_query(){
	return apply_filters('dhwc_ajax_brand_filter_query', 'brand_filter');
}

function dhwc_ajax_get_tag_filter_query(){
	return apply_filters('dhwc_ajax_tag_filter_query', 'tag_filter');
}

function dhwc_ajax_get_category_filter_query(){
	return apply_filters('dhwc_ajax_category_filter_query', 'cat_filter');
}

function dhwc_ajax_get_custom_taxonomy_filter_query($taxonomy){
	return apply_filters('dhwc_ajax_get_custom_taxonomy_filter_query', str_replace('product_', '', $taxonomy).'_filter');
}

function dhwc_ajax_get_stock_status_filter_query(){
	return apply_filters('dhwc_ajax_stock_status_filter_query', 'stock_status_filter');
}

function dhwc_ajax_get_acf_field_filter_query_prefix(){
	return apply_filters('dhwc_ajax_acf_field_filter_query_prefix', 'acf-');
}

function dhwc_ajax_get_acf_field_filter_query($filed_name){
	$prefix = dhwc_ajax_get_acf_field_filter_query_prefix();
	return apply_filters('dhwc_ajax_acf_field_filter_query', $prefix.$filed_name, $filed_name);
}

function dhwc_ajax_get_acf_fields_options(){
	if(function_exists('acf_get_field_groups')){
		$field_groups = acf_get_field_groups();
	}else{
		$field_groups = apply_filters ( 'acf/get_field_groups', array () );
	}
	$allow_types = array('select','checkbox','radio','button_group');
	$custom_fields_options = array();
	foreach ((array) $field_groups as $field_group ) {
		$location = !empty($field_group['location'][0][0]) ? $field_group['location'][0][0] : array();
	
		if (!empty($location) && $location['value'] === 'product') {
			if(function_exists('acf_get_fields')){
				$fields = acf_get_fields($field_group);
				if (! empty ( $fields )) {
					foreach ( $fields as $field ) {
	
						if(!in_array($field['type'], $allow_types)){
							continue;
						}
	
						$custom_fields_options [$field ['name']] = $field ['label'];
					}
				}
	
			}else{
				$fields = apply_filters ( 'acf/field_group/get_fields', array (), $field_group ['id'] );
				if (! empty ( $fields )) {
					foreach ( $fields as $field ) {
	
						if(!in_array($field['type'], $allow_types)){
							continue;
						}
	
						$custom_fields_options [$field ['name']] = $field ['label'];
					}
				}
			}
		}
	}
	return $custom_fields_options;
}

function dhwc_ajax_get_product_status_filter_query(){
	return apply_filters('dhwc_ajax_product_status_filter_query', 'status_filter');
}

function dhwc_ajax_get_product_status_options(){
	return array(
		'onsale'	=> __('On Sale','dhwc-ajax'),
		'featured'  => __('Featured','dhwc-ajax')
	);
}

function dhwc_ajax_get_weight_dimensions_options(){
	return array(
		'weight' => __('Weight','dhwc-ajax'),
		'length' => __('Length','dhwc-ajax'),
		'width'  => __('Width','dhwc-ajax'),
		'height' => __('Height','dhwc-ajax')
	);
}

function dhwc_ajax_get_weight_dimensions_meta_prefix(){
	return apply_filters('dhwc_ajax_weight_dimensions_meta_prefix', '_');	
}
