<?php

class DHWC_Ajax_Widget_Product_Sorting extends DHWC_Ajax_Widget{
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_layered_nav dhwc_ajax_widget_product_sorting';
		$this->widget_description = __( 'Display WooCommerce Product sorting list.', 'dhwc-ajax' );
		$this->widget_id          = 'dhwc_ajax_widget_product_sorting';
		$this->widget_name        = __( 'DHWC Ajax Product Sorting', 'dhwc-ajax' );

		parent::__construct();
	}

	public function update( $new_instance, $old_instance ) {
		$this->init_settings();
		return parent::update( $new_instance, $old_instance );
	}

	public function form( $instance ) {
		$this->init_settings();
		parent::form( $instance );
	}

	public function init_settings() {
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Product Sorting', 'dhwc-ajax' ),
				'label' => __( 'Title', 'dhwc-ajax' )
			),
		);
	}

	public function widget( $args, $instance ) {
		global $wp_query;
		
		if ( 1 != $wp_query->found_posts || woocommerce_products_will_display() ) {

			$this->widget_start( $args, $instance );
			echo '<ul>';
			$orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', dhwc_ajax_get_option( 'woocommerce_default_catalog_orderby' ) );
			$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', dhwc_ajax_get_option( 'woocommerce_default_catalog_orderby' ) );
			$catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
				'menu_order' => __( 'Default sorting', 'dhwc-ajax' ),
				'popularity' => __( 'Sort by popularity', 'dhwc-ajax' ),
				'rating'     => __( 'Sort by average rating', 'dhwc-ajax' ),
				'date'       => __( 'Sort by newness', 'dhwc-ajax' ),
				'price'      => __( 'Sort by price: low to high', 'dhwc-ajax' ),
				'price-desc' => __( 'Sort by price: high to low', 'dhwc-ajax' )
			) );

			if ( !$show_default_orderby ) {
				unset( $catalog_orderby_options['menu_order'] );
			}

			if(!apply_filters('dhwc_ajax_product_sorting_show_default', false)){
				unset( $catalog_orderby_options['menu_order'] );
			}

			if ( dhwc_ajax_get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
				unset( $catalog_orderby_options['rating'] );
			}


			foreach ( $catalog_orderby_options as $id => $name ) {
				if ( $orderby == $id ) {
					$link = remove_query_arg( 'orderby' );
					$link = str_replace( '%2C', ',', $link );
					echo '<li class="chosen"><a href="' . esc_url( $link ) . '">' . esc_attr( $name ) . '</a></li>';
				} else {
					$link = add_query_arg( 'orderby', $id );
					$link = str_replace( '%2C', ',', $link );
					echo '<li><a href="' . esc_url( $link ) . '">' . esc_attr( $name ) . '</a></li>';
				}
			}

			echo '</ul>';

			$this->widget_end( $args );
		}


	}
}
add_action('widgets_init', function(){
	return register_widget("DHWC_Ajax_Widget_Product_Sorting");
});