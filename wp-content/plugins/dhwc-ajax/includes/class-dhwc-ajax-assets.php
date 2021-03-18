<?php

class DHWC_Ajax_Assets{
	public static function init(){
		if(!is_admin()){
			add_action('template_redirect', array(__CLASS__,'register_assets'),1);
			add_action( 'wp_enqueue_scripts', array(__CLASS__,'enqueue_styles' ));
			add_action( 'wp_enqueue_scripts', array(__CLASS__,'enqueue_scripts' ));
		}
	}
	
	public static function register_assets(){
		wp_register_script( 'infinite-scroll.pkgd', DHWC_AJAX_URL.'/assets/js/infinite-scroll.pkgd.min.js', array( 'jquery' ), '3.0.6', true );
		
	}
	
	public static function enqueue_scripts(){
		wp_register_script( 'dhwc-ajax', DHWC_AJAX_URL.'/assets/js/script.min.js', array( 'jquery' ), DHWC_AJAX_VERSION, true );
		wp_localize_script('dhwc-ajax', 'dhwc_ajax_params', array(
			'wp_ajax_url'						=> admin_url( 'admin-ajax.php', 'relative' ),
			'ajax_url'							=> add_query_arg( 'dhwc-ajax', '__action__',  home_url( '/', 'relative' ) ),
			'ajax_nonce' 						=> wp_create_nonce( 'dhwc-ajax' ),
			'search_min_keyword' 				=> 1,
			'ajax_add_to_cart'					=> dhwc_ajax_get_option('dhwc_ajax_single_add_to_cart','yes'),
			'single_ajax_added_to_cart_scroll' 	=> apply_filters('dhwc_ajax_single_ajax_added_to_cart_scroll', 'yes'),
			'filter_widget_display'				=> dhwc_ajax_get_option('dhwc_ajax_filter_widget_display','above'),
			'i18n'								=> array(
				'remove_search_filter'=>__('Remove search filter','dhwc-ajax'),
			),
			'elements' => array(
				'product_wrapper' 		=> dhwc_ajax_get_option('dhwc_ajax_elment_product_wrapper','.woocommerce-products-loop-wrapper'),
				'product_list'	  		=> dhwc_ajax_get_option('dhwc_ajax_elment_product_list','ul.products'),
				'product_item'	  		=> dhwc_ajax_get_option('dhwc_ajax_elment_product_item','li.product'),
				'result_count'	  		=> dhwc_ajax_get_option('dhwc_ajax_elment_result_count','.woocommerce-result-count'),
				'pagination_wrapper'	=> dhwc_ajax_get_option('dhwc_ajax_elment_pagination_wrapper','.woocommerce-pagination'),
				'pagination_next'		=> dhwc_ajax_get_option('dhwc_ajax_elment_pagination','.next'),
				'sidebar_filter'	    => dhwc_ajax_get_option('dhwc_ajax_elment_sidebar_filter','.widget-area'),
				'page_title'	        => dhwc_ajax_get_option('dhwc_ajax_elment_page_title','.entry-title'),
				'page_breadcrumb'	    => dhwc_ajax_get_option('dhwc_ajax_elment_page_breadcrumb','.woocommerce-breadcrumb'),
			)
		));
		wp_enqueue_script('dhwc-ajax');
	}
	
	public static function enqueue_styles(){
		wp_enqueue_style('dhwc-ajax',DHWC_AJAX_URL.'/assets/css/style.css',false,DHWC_AJAX_VERSION);
	}
}

DHWC_Ajax_Assets::init();