<?php

class DHWC_Brand_Frontend {
	public function __construct(){
		add_action( 'woocommerce_product_meta_end',array(__CLASS__,'product_brand_meta'));
		
		//Brand list
		add_shortcode('dhwc_brand_list', array($this,'shortcode_output'));
	}
	
	public static function product_brand_meta(){
		global $product;
		$brands = get_the_terms( $product->get_id(), 'product_brand' );
		if(empty($brands))
			return;
		$brand_ids = wp_list_pluck( $brands, 'term_id' );
		echo get_the_term_list( $product->get_id(), 'product_brand','<span class="tagged_as">' . _n( 'Brand:', 'Brands:', count( $brand_ids ), DHWC_BRAND_TEXT_DOMAIN ) . ' ', ', ', '</span>' ); 
	}
	
	/**
	 * List all (or limited) product brands.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode_output($atts){
		if ( isset( $atts['number'] ) ) {
			$atts['limit'] = $atts['number'];
		}

		$atts = shortcode_atts( array(
			'limit'      => '-1',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns'    => '4',
			'hide_empty' => 1,
			'parent'     => '',
			'ids'        => '',
		), $atts, 'dhwc_product_brands' );

		$ids        = array_filter( array_map( 'trim', explode( ',', $atts['ids'] ) ) );
		$hide_empty = ( true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty'] ) ? 1 : 0;

		// Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,
			'child_of'   => $atts['parent'],
		);

		$product_brands = get_terms( 'product_brand', $args );

		if ( '' !== $atts['parent'] ) {
			$product_brands = wp_list_filter( $product_brands, array(
				'parent' => $atts['parent'],
			) );
		}

		if ( $hide_empty ) {
			foreach ( $product_brands as $key => $category ) {
				if ( 0 === $category->count ) {
					unset( $product_brands[ $key ] );
				}
			}
		}

		$atts['limit'] = '-1' === $atts['limit'] ? null : intval( $atts['limit'] );
		if ( $atts['limit'] ) {
			$product_brands = array_slice( $product_brands, 0, $atts['limit'] );
		}

		$columns = absint( $atts['columns'] );

		wc_set_loop_prop( 'columns', $columns );
		wc_set_loop_prop( 'is_shortcode', true );

		ob_start();

		if ( $product_brands ) {
			woocommerce_product_loop_start();

			foreach ( $product_brands as $brand ) {
				wc_get_template( 'content-product_brand.php', array(
					'brand' => $brand,
				),'',DHWC_BRAND_DIR.'/templates/');
			}

			woocommerce_product_loop_end();
		}

		woocommerce_reset_loop();

		return '<div class="dhwc-brand-shortcode woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}
}
new DHWC_Brand_Frontend();