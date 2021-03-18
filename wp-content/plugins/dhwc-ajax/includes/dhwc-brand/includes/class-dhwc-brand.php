<?php

class DHWC_Brand{
	public function __construct(){
		$this->_includes();
		add_action('init', array($this,'init'),1);
	}
	
	public function init(){
		if(!function_exists('is_plugin_active'))
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require plugin.php to use is_plugin_active() below
		
		if ( !is_plugin_active( 'woocommerce/woocommerce.php' )) {
			add_action('admin_notices', array(&$this,'woocommerce_notice'));
			return ;
		}
		
		$this->_register_taxonomy();
	}
	
	public function woocommerce_notice(){
		$plugin_name = 'DHWC Brand';
		echo '<div class="updated"><p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a></strong> plugin to be installed and activated on your site.', DHWC_BRAND_TEXT_DOMAIN), $plugin_name) . '</p></div>';
	}
	
	public function _includes(){
		
		include_once DHWC_BRAND_DIR.'/includes/functions.php';
		include_once DHWC_BRAND_DIR.'/includes/class-dhwc-brand-query.php';
		
		//Widgets
		include_once DHWC_BRAND_DIR.'/includes/widgets/class-dhwc-brand-widget.php';
		include_once DHWC_BRAND_DIR.'/includes/widgets/class-dhwc-brand-widget-slider.php';
		
		if(!defined('DHWC_AJAX_VERSION'))
			include_once DHWC_BRAND_DIR.'/includes/widgets/class-dhwc-brand-widget-layered-nav.php';
		
		if(is_admin())
			include_once DHWC_BRAND_DIR.'/includes/class-dhwc-brand-admin.php';
		else 
			include_once DHWC_BRAND_DIR.'/includes/class-dhwc-brand-frontend.php';
	}
	
	private function _register_taxonomy(){
		if(taxonomy_exists('product_brand'))
			return;
		
		$permalinks 	= get_option( 'woocommerce_permalinks' );
		$shop_page_id 	= wc_get_page_id('shop');
		$base_slug 		= $shop_page_id > 0 && get_post( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop';
		
		$product_brand_rewrite_slug = empty( $permalinks['brand_base'] ) ? _x( 'product-brand', 'slug', DHWC_BRAND_TEXT_DOMAIN ) : $permalinks['brand_base'];
		register_taxonomy ( 'product_brand',
			array ('product' ),
			array (
				'hierarchical' => true,
				'update_count_callback' => '_wc_term_recount',
				'label' => __ ( 'Product Brands', DHWC_BRAND_TEXT_DOMAIN ),
				'labels' => array (
					'name' => __ ( 'Product Brands', DHWC_BRAND_TEXT_DOMAIN ),
					'singular_name' => __ ( 'Product Brand', DHWC_BRAND_TEXT_DOMAIN ),
					'menu_name' => _x ( 'Brands', 'Admin menu name', DHWC_BRAND_TEXT_DOMAIN ),
					'search_items' => __ ( 'Search Product Brands', DHWC_BRAND_TEXT_DOMAIN ),
					'all_items' => __ ( 'All Product Brands', DHWC_BRAND_TEXT_DOMAIN ),
					'parent_item' => __ ( 'Parent Product Brand', DHWC_BRAND_TEXT_DOMAIN ),
					'parent_item_colon' => __ ( 'Parent Product Brand:', DHWC_BRAND_TEXT_DOMAIN ),
					'edit_item' => __ ( 'Edit Product Brand', DHWC_BRAND_TEXT_DOMAIN ),
					'update_item' => __ ( 'Update Product Brand', DHWC_BRAND_TEXT_DOMAIN ),
					'add_new_item' => __ ( 'Add New Product Brand', DHWC_BRAND_TEXT_DOMAIN ),
					'new_item_name' => __ ( 'New Product Brand Name', DHWC_BRAND_TEXT_DOMAIN )
				),
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'query_var' => true,
				'capabilities' => array (
					'manage_terms' => 'manage_product_terms',
					'edit_terms'   => 'edit_product_terms',
					'delete_terms' => 'delete_product_terms',
					'assign_terms' => 'assign_product_terms'
				),
				'rewrite' => array (
					'slug' => $product_brand_rewrite_slug,
					'with_front' => false,
					'hierarchical' => true
				)
			) 
		);
	}
}
new DHWC_Brand();